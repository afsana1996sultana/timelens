@extends('admin.admin_master')
@section('admin')
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<section class="content-main">
    <div class="content-header">
        <h3 class="content-title">Brand list <span class="badge rounded-pill alert-success"> {{ count($brands) }} </span></h3>
        @if (Auth::guard('admin')->user()->role == '1' || in_array('10', json_decode(Auth::guard('admin')->user()->staff->role->permissions)))
            <div>
                <a href="{{ route('brand.add') }}" class="btn btn-primary"><i class="material-icons md-plus"></i>Brand Create</a>
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
                            <th scope="col">Cover Photo</th>
                            <th scope="col">Name (English)</th>
                            <th scope="col">Name (Bangla)</th>
                            <th scope="col">Status</th>
                            @if(Auth::guard('admin')->user()->role != '2')
                                @if (Auth::guard('admin')->user()->role == '1' || in_array('11', json_decode(Auth::guard('admin')->user()->staff->role->permissions)) || in_array('12', json_decode(Auth::guard('admin')->user()->staff->role->permissions)))
                                    <th scope="col" class="text-end">Action</th>
                                @endif
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($brands as $key => $brand)
                        <tr>
                            <td> {{ $key+1}} </td>
                            <td width="15%">
                                <a href="#" class="itemside">
                                    <div class="left">
                                        <img src="{{ asset($brand->brand_image) }}" class="img-sm img-avatar" alt="Userpic" />
                                    </div>
                                </a>
                            </td>
                            <td> {{ $brand->name_en ?? 'NULL' }} </td>
                            <td> {{ $brand->name_bn ?? 'NULL' }} </td>
                            <td>
                                @if($brand->status == 1)
                                  <a @if(Auth::guard('admin')->user()->role != '2') href="{{ route('brand.in_active',['id'=>$brand->id]) }}" @endif>
                                    <span class="badge rounded-pill alert-success">Active</span>
                                  </a>
                                @else
                                  <a @if(Auth::guard('admin')->user()->role != '2') href="{{ route('brand.active',['id'=>$brand->id]) }}" @endif> <span class="badge rounded-pill alert-danger">Disable</span></a>
                                @endif
                            </td>
                            @if(Auth::guard('admin')->user()->role != '2')
                                @if (Auth::guard('admin')->user()->role == '1' || in_array('11', json_decode(Auth::guard('admin')->user()->staff->role->permissions)) || in_array('12', json_decode(Auth::guard('admin')->user()->staff->role->permissions)))
                                    <td class="text-end">
                                        <div class="dropdown">
                                            <a href="#" data-bs-toggle="dropdown" class="btn btn-light rounded btn-sm font-sm"> <i class="material-icons md-more_horiz"></i> </a>
                                            <div class="dropdown-menu">
                                                @if (Auth::guard('admin')->user()->role == '1' || in_array('11', json_decode(Auth::guard('admin')->user()->staff->role->permissions)))
                                                    <a class="dropdown-item" href="{{ route('brand.edit',$brand->id) }}">Edit info</a>
                                                @endif

                                                @if (Auth::guard('admin')->user()->role == '1' || in_array('12', json_decode(Auth::guard('admin')->user()->staff->role->permissions)))
                                                    <a class="dropdown-item text-danger" href="{{ route('brand.delete',$brand->id) }}" id="delete">Delete</a>
                                                @endif

                                            </div>
                                        </div>
                                        <!-- dropdown //end -->
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
