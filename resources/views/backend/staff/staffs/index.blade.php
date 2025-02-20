@extends('admin.admin_master')
@section('admin')
<section class="content-main">
    <div class="content-header">
        <h3 class="content-title">Staff list <span class="badge rounded-pill alert-success"> {{ count($staffs) }} </span></h3>

        @if (Auth::guard('admin')->user()->role == '1' || in_array('26', json_decode(Auth::guard('admin')->user()->staff->role->permissions)))
            <div>
                <a href="{{ route('staff.create') }}" class="btn btn-primary"><i class="material-icons md-plus"></i>Add New Staff</a>
            </div>
        @endif
    </div>
    </div>
    <div class="card mb-4">
        <div class="card-body">
            <div class="table-responsive-sm">
               <table id="example" class="table table-bordered table-striped" width="100%">
                    <thead>
                        <tr>
                            <th scope="col">Sl</th>
                            <th scope="col">Name</th>
                            <th scope="col">Email</th>
                            <th scope="col">Phone</th>
                            <th scope="col">Role</th>
                            @if (Auth::guard('admin')->user()->role == '1' || in_array('27', json_decode(Auth::guard('admin')->user()->staff->role->permissions)) || in_array('28', json_decode(Auth::guard('admin')->user()->staff->role->permissions)))
                                <th scope="col" class="text-end">Action</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($staffs as $key => $staff)
                        <tr>
                            <td> {{ $key+1}} </td>
                            <td> {{ $staff->user->name ?? 'NULL' }} </td>
                            <td> {{ $staff->user->email ?? 'NULL' }} </td>
                            <td> {{ $staff->user->phone ?? 'NULL' }} </td>
                            <td> {{ $staff->role->name ?? 'NULL' }} </td>
                            @if(Auth::guard('admin')->user()->role != '2')
                                @if (Auth::guard('admin')->user()->role == '1' || in_array('27', json_decode(Auth::guard('admin')->user()->staff->role->permissions)) || in_array('28', json_decode(Auth::guard('admin')->user()->staff->role->permissions)))
                                    <td>
                                        @if (Auth::guard('admin')->user()->role == '1' || in_array('27', json_decode(Auth::guard('admin')->user()->staff->role->permissions)))
                                            <a  class="btn btn-primary btn-icon btn-circle btn-sm btn-xs" href="{{ route('staff.edit',$staff->id) }}">
                                                <i class="fa-solid fa-edit"></i>
                                            </a>
                                        @endif
                                        
                                        @if (Auth::guard('admin')->user()->role == '1' || in_array('28', json_decode(Auth::guard('admin')->user()->staff->role->permissions)))
                                            <a  href="{{ route('staff.destroy', $staff->id) }}" id="delete" class="btn btn-primary btn-icon btn-circle btn-sm btn-xs" data-href="#" >
                                                <i class="fa-solid fa-trash"></i>
                                            </a>
                                        @endif
                                    </td>
                                @endif
                            @endif
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <!-- table-responsive //end -->
        </div>
        <!-- card-body end// -->
    </div>
</section>
@endsection
