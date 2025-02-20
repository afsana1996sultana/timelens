@extends('admin.admin_master')
@section('admin')
<section class="content-main">
    <div class="content-header">
        <h3 class="content-title">Page list <span class="badge rounded-pill alert-success"> {{ count($pages) }} </span></h3>
        @if (Auth::guard('admin')->user()->role == '1' || in_array('50', json_decode(Auth::guard('admin')->user()->staff->role->permissions)))
            <div>
                <a href="{{ route('page.create') }}" class="btn btn-primary"><i class="material-icons md-plus"></i>Page Create</a>
            </div>
        @endif
    </div>
    </div>
    <div class="card mb-4">
        <div class="card-body">
            <div class="table-responsive">
               <table id="example" class="table table-bordered table-striped" width="100%">
                    <thead>
                        <tr>
                            <th scope="col">Sl</th>
                            <th scope="col">Name</th>
                            <th scope="col">Title</th>
                            <th scope="col">Description</th>
                            <th scope="col">Position</th>
                            <th scope="col">Status</th>
                            @if (Auth::guard('admin')->user()->role == '1' || in_array('51', json_decode(Auth::guard('admin')->user()->staff->role->permissions)) || in_array('52', json_decode(Auth::guard('admin')->user()->staff->role->permissions)))
                                <th scope="col" class="text-end">Action</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pages as $key => $page)
                        <tr>
                            <td> {{ $key+1}} </td>
                            <td class="p_name"> {{ $page->name_en ?? 'NULL' }} </td>
                            <td> {{ $page->title ?? 'NULL' }} </td>
                            <td>
                                <?php $des =  strip_tags(html_entity_decode($page->description_en))?>
                                {{ Str::limit($des, $limit = 40, $end = '. . .') }}
                            </td>
                            <td> {{ $page->position ?? 'NULL' }} </td>
                            <td>
                                @if($page->status == 1)
                                  <a href="{{ route('page.in_active',['id'=>$page->id]) }}">
                                    <span class="badge rounded-pill alert-success">Active</span>
                                  </a>
                                @else
                                  <a href="{{ route('page.active',['id'=>$page->id]) }}" > <span class="badge rounded-pill alert-danger">Disable</span></a>
                                @endif
                            </td>
                            @if(Auth::guard('admin')->user()->role != '2')
                                @if (Auth::guard('admin')->user()->role == '1' || in_array('51', json_decode(Auth::guard('admin')->user()->staff->role->permissions)) || in_array('52', json_decode(Auth::guard('admin')->user()->staff->role->permissions)))
                                    <td class="text-end">
                                        <div class="dropdown">
                                            <a href="#" data-bs-toggle="dropdown" class="btn btn-light rounded btn-sm font-sm"> <i class="material-icons md-more_horiz"></i> </a>
                                            <div class="dropdown-menu">
                                                @if(Auth::guard('admin')->user()->role == '1' || in_array('51', json_decode(Auth::guard('admin')->user()->staff->role->permissions)))
                                                    <a class="dropdown-item" href="{{ route('page.edit',$page->id) }}">Edit info</a>
                                                @endif

                                                @if(Auth::guard('admin')->user()->role == '1' || in_array('52', json_decode(Auth::guard('admin')->user()->staff->role->permissions)))
                                                    <a class="dropdown-item text-danger" href="{{ route('page.delete',$page->id) }}" id="delete">Delete</a>
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

@push('footer-script')
    <script>
        $(document).ready(function(){
            $(document).on('click', '.viweBtn', function(){
                var p_name = $(this).closest('tr').find('.p_name').text();
                // alert(p_name);
                $.ajax({
                    type: "POST",
                    url: "",
                    data: {
                        'checking_view': true,
                        'p_name': p_name,
                    },
                    success: function(response){
                        console.log(response);
                    }
                });
            });
        });
    </script>
@endpush
