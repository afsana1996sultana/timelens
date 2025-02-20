<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\OrderStatus;
use App\Models\User;
use App\Models\Address;
use App\Models\District;
use App\Models\Upazilla;
use App\Models\Shipping;
use App\Models\Ordernote;
use Session;
use PDF;
use Illuminate\Support\Carbon;
use Auth;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

     public function index(Request $request)
     {
         $delivery_status = $request->input('delivery_status');
         $payment_status = $request->input('payment_status');
         $note_status = $request->input('note_status');
         $date_range = $request->input('date_range');

         $orders = Order::query();

         if ($delivery_status) {
             $orders->where('delivery_status', $delivery_status);
         }
         if ($payment_status) {
             $orders->where('payment_status', $payment_status);
         }
         if ($note_status) {
             $orders->where('note_status', $note_status);
         }
         if ($date_range) {
             try {
                 $dates = explode(' - ', $date_range);
                 $start_date = \Carbon\Carbon::createFromFormat('Y-m-d h:i A', trim($dates[0]));
                 $end_date = \Carbon\Carbon::createFromFormat('Y-m-d h:i A', trim($dates[1]));
                 $orders->whereBetween('created_at', [$start_date, $end_date]);
             } catch (\Exception $e) {
                 // Handle invalid date range
             }
         }

         $orders = $orders->where('sale_type', 2)->paginate(15);
         $ordernotes = Ordernote::where('status', 1)->get();

         return view('backend.sales.all_orders.index', compact('orders', 'delivery_status', 'payment_status', 'note_status', 'date_range', 'ordernotes'));
     }


     public function Posindex(Request $request)
     {
         $delivery_status = $request->input('delivery_status');
         $payment_status = $request->input('payment_status');
         $note_status = $request->input('note_status');
         $date_range = $request->input('date_range');

         $orders = Order::query();

         if ($delivery_status) {
             $orders->where('delivery_status', $delivery_status);
         }
         if ($payment_status) {
             $orders->where('payment_status', $payment_status);
         }
         if ($note_status) {
             $orders->where('note_status', $note_status);
         }
         if ($date_range) {
             try {
                 $dates = explode(' - ', $date_range);
                 $start_date = \Carbon\Carbon::createFromFormat('Y-m-d h:i A', trim($dates[0]));
                 $end_date = \Carbon\Carbon::createFromFormat('Y-m-d h:i A', trim($dates[1]));
                 $orders->whereBetween('created_at', [$start_date, $end_date]);
             } catch (\Exception $e) {
                 // Handle invalid date range
             }
         }

         $orders = $orders->where('sale_type', 1)->paginate(15);
         $ordernotes = Ordernote::where('status', 1)->get();

         return view('backend.sales.all_orders.pos_sale_index', compact('orders', 'delivery_status', 'payment_status', 'note_status', 'date_range', 'ordernotes'));
     }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $order = Order::findOrFail($id);
        $shippings = Shipping::where('status', 1)->get();
        $ordernotes = Ordernote::where('status', 1)->get();
        return view('backend.sales.all_orders.show', compact('order', 'shippings', 'ordernotes'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->validate($request,[
            'payment_method' => 'required',
        ]);
        $order = Order::findOrFail($id);

        $order->division_id = $request->division_id;
        $order->district_id = $request->district_id;
        $order->upazilla_id = $request->upazilla_id;
        $order->payment_method = $request->payment_method;

        $discount_total = ($order->sub_total - $request->discount);
        $total_ammount = ($discount_total + $request->shipping_charge);

        Order::where('id', $id)->update([
            'shipping_charge'   => $request->shipping_charge,
            'discount'          => $request->discount,
            'grand_total'       => $total_ammount,
        ]);

        $order->save();

        Session::flash('success','Admin Orders Information Updated Successfully');
        return redirect()->back();
    }

    public function assignTo(Request $request)
    {
        $order = Order::findOrFail($request->order_id);  // Find the order by ID
        $order->staff_id = Auth::guard('admin')->user()->id;  // Assign current admin ID to staff_id
        $order->save();  // Save the updated order

        return response()->json(['success'=> 'Assigned To Successfully']);  // Return success message
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $order = Order::findOrFail($id);
        $order->delete();
        $notification = array(
            'message' => 'Order Deleted Successfully.',
            'alert-type' => 'success'
        );
        return redirect()->back()->with($notification);
    }


    /*================= Start update_payment_status Methoed ================*/
    public function update_payment_status(Request $request)
    {
        $order = Order::findOrFail($request->order_id);
        $order->payment_status = $request->status;
        $order->save();

        $order_detail = OrderDetail::where('order_id', $order->id)->get();
        foreach($order_detail as $item){
            $item->payment_status = $request->status;
            $item->save();
        }
        $orderstatus = OrderStatus::create([
            'order_id' => $order->id,
            'title' => 'Payment Status: '.$request->status,
            'comments' => '',
            'created_at' => Carbon::now(),
        ]);
        return response()->json(['success'=> 'Payment status has been updated']);

    }

    /*================= Start update_delivery_status Methoed ================*/
    public function update_delivery_status(Request $request)
    {
        $order = Order::findOrFail($request->order_id);
        $order->delivery_status = $request->status;
        $order->save();

        $order_detail = OrderDetail::where('order_id', $order->id)->get();
        foreach($order_detail as $item){
            $item->delivery_status = $request->status;
            $item->save();
        }

        $orderstatus = OrderStatus::create([
            'order_id' => $order->id,
            'title' => 'Delevery Status: '.$request->status,
            'comments' => '',
            'created_at' => Carbon::now(),
        ]);

        return response()->json(['success'=> 'Delivery status has been updated']);

    }


    /*================= Start update_note_status Methoed ================*/
    public function update_note_status(Request $request)
    {
        $order = Order::findOrFail($request->order_id);
        $order->note_status = $request->status;
        $order->save();

        $order_detail = OrderDetail::where('order_id', $order->id)->get();
        foreach($order_detail as $item){
            $item->note_status = $request->status;
            $item->save();
        }
        return response()->json(['success'=> 'Note status has been updated']);

    }

    /*================= Start admin_user_update Methoed ================*/
    public function admin_user_update(Request $request, $id)
    {
        $user = Order::where('id',$id)->update([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
        ]);

        // dd($user);

        Session::flash('success','Customer Information Updated Successfully');
        return redirect()->back();
    }

    /* ============= Start getdivision Method ============== */
    public function getdivision($division_id){
        $division = District::where('division_id', $division_id)->orderBy('district_name_en','ASC')->get();

        return json_encode($division);
    }
    /* ============= End getdivision Method ============== */

    /* ============= Start getupazilla Method ============== */
    public function getupazilla($district_id){
        $upazilla = Upazilla::where('district_id', $district_id)->orderBy('name_en','ASC')->get();

        return json_encode($upazilla);
    }
    /* ============= End getupazilla Method ============== */

    /* ============= Start invoice_download Method ============== */
    public function invoice_download($id){
        $order = Order::findOrFail($id);
        $pdf = PDF::loadView('backend.invoices.invoice',compact('order'))->setPaper('a4');
        return $pdf->download('invoice.pdf');
    } // end method

    /* ============= End invoice_download Method ============== */
     public function invoice_print_download($id){
        $order = Order::findOrFail($id);
        return view('backend.invoices.invoice_print', compact('order'));
    } // end method

}
