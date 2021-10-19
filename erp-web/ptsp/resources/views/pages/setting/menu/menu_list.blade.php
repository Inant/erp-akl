@extends('theme.default')

@section('breadcrumb')
			<div class="page-breadcrumb">
                <div class="row">
                    <div class="col-5 align-self-center">
                        <h4 class="page-title">Menu</h4>
                        <div class="d-flex align-items-center">
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item active" aria-current="page">Menu</li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
@endsection

@section('content')

<div class="container-fluid">
    <!-- basic table -->
                <div class="row">
                    <div class="col-12">
                        <div class="text-right">
                            <a href="{{ URL::to('menu/add') }}"><button class="btn btn-success btn-sm mb-2"><i class="fas fa-plus"></i>&nbsp; Add new menu</button></a>
                        </div>
                        <div class="card">
                            <div class="card-body">
                                <h4 class="card-title">List Menu</h4>
                                <div class="table-responsive">
                                    <table id="zero_config" class="table table-striped table-bordered">
                                        <thead>
                                            <tr>
                                                <th class="text-center"  width="15px">No</th>
                                                <th class="text-center">Title</th>
                                                <th class="text-center">Url</th>
                                                <th class="text-center">Icon</th>
                                                <th class="text-center">Parent Menu</th>
                                                <th class="text-center">Seq No</th>
                                                <th class="text-center">Is Active</th>
                                                <th class="text-center" width="50px">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @for($i = 0; $i < count($menus); $i++)
                                            <tr>
                                                <td class="text-center">{{ $i+1 }}</td>
                                                <td>{{ $menus[$i]->title }}</td>
                                                <td>{{ $menus[$i]->url }}</td>
                                                <td class="text-center"><i class="{{ $menus[$i]->icon }}"></i></td>
                                                <td>{{ $menus[$i]->is_main_menu }}</td>
                                                <td class="text-center">{{ $menus[$i]->seq_no }}</td>
                                                <td>{{ $menus[$i]->is_active == 1 ? 'Active' : 'Inactive' }}</td>
                                                <td class="text-center">
                                                    <a href="{{ URL::to('menu/edit/'.$menus[$i]->id) }}" class="btn waves-effect waves-light btn-xs btn-warning"><i class="fas fa-pencil-alt"></i></a>
                                                    <a href="{{ URL::to('menu/delete/'.$menus[$i]->id) }}" class="btn waves-effect waves-light btn-xs btn-danger" onclick="return confirm('Are you sure to delete item?')"><i class="fas fa-trash-alt"></i></a>
                                                </td>
                                            </tr>
                                            @endfor
                                        </tbody>
                                        <!-- <tfoot>
                                            <tr>
                                                <th>No</th>
                                                <th>Title</th>
                                                <th>Url</th>
                                                <th>Icon</th>
                                                <th>Is Active</th>
                                                <th>Action</th>
                                            </tr>
                                        </tfoot> -->
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
</div>

@endsection