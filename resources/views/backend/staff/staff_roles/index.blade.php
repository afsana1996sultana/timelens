@extends('admin.admin_master')
@section('admin')
<section class="content-main">
    <div class="content-header">
        <h3 class="content-title">Role list <span class="badge rounded-pill alert-success"> {{ count($roles) }} </span></h3>

        @if (Auth::guard('admin')->user()->role == '1' || in_array('30', json_decode(Auth::guard('admin')->user()->staff->role->permissions)))
            <div>
                <a href="{{ route('roles.create') }}" class="btn btn-primary"><i class="material-icons md-plus"></i>Add New Role</a>
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
                            @if (Auth::guard('admin')->user()->role == '1' || in_array('31', json_decode(Auth::guard('admin')->user()->staff->role->permissions)) || in_array('32', json_decode(Auth::guard('admin')->user()->staff->role->permissions)))
                                <th scope="col" class="text-end">Action</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($roles as $key => $role)
                        <tr>
                            <td> {{ $key+1}} </td>
                            <td> {{ $role->name ?? 'NULL' }} </td>
                            @if(Auth::guard('admin')->user()->role != '2')
                                @if (Auth::guard('admin')->user()->role == '1' || in_array('31', json_decode(Auth::guard('admin')->user()->staff->role->permissions)) || in_array('32', json_decode(Auth::guard('admin')->user()->staff->role->permissions)))
                                    <td>
                                        @if (Auth::guard('admin')->user()->role == '1' || in_array('31', json_decode(Auth::guard('admin')->user()->staff->role->permissions)))
                                            <a  class="btn btn-primary btn-icon btn-circle btn-sm btn-xs" href="{{ route('roles.edit', $role->id) }}">
                                                <i class="fa-solid fa-edit"></i>
                                            </a>
                                        @endif

                                        @if (Auth::guard('admin')->user()->role == '1' || in_array('32', json_decode(Auth::guard('admin')->user()->staff->role->permissions)))
                                            <a  href="{{route('roles.destroy', $role->id)}}" id="delete" class="btn btn-primary btn-icon btn-circle btn-sm btn-xs" data-href="#" >
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
