@extends('theme.default')

@section('breadcrumb')
			<div class="page-breadcrumb">
                <div class="row">
                    <div class="col-5 align-self-center">
                        <h4 class="page-title">Role</h4>
                        <div class="d-flex align-items-center">
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item active" aria-current="page">Role</li>
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
                        <div class="card">
                            <div class="card-body">
                                <h4 class="card-title">List Role</h4>
                                <!-- <a href="/user/add"><button class="btn btn-success mb-2"><i class="fas fa-plus"></i>&nbsp; Add new user</button></a> -->
                                <div class="table-responsive">
                                    <table id="zero_config" class="table table-striped table-bordered">
                                        <thead>
                                            <tr>
                                                <th class="text-center" width="15px">No</th>
                                                <th class="text-center">Role Code</th>
                                                <th class="text-center">Role Name</th>
                                                <th class="text-center" width="50px">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @for($i = 0; $i < count($roles); $i++)
                                            <tr>
                                                <td class="text-center">{{ $i+1 }}</td>
                                                <td>{{ $roles[$i]->role_code }}</td>
                                                <td>{{ $roles[$i]->role_name }}</td>
                                                <td class="text-center">
                                                    <a href="{{ URL::to('role/permission/'.$roles[$i]->id) }}" class="btn waves-effect waves-light btn-xs btn-info"><i class="mdi mdi-key-variant"></i></a>
                                                    <a href="{{ URL::to('role/edit/'.$roles[$i]->id) }}" class="btn waves-effect waves-light btn-xs btn-warning"><i class="fas fa-pencil-alt"></i></a>
                                                </td>
                                            </tr>
                                            @endfor
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
</div>

@endsection