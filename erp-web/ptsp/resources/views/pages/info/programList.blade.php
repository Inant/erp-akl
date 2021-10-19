@extends('theme.default')

@section('breadcrumb')
    <div class="page-breadcrumb">
        <div class="row">
            <div class="col-5 align-self-center">
                <h4 class="page-title">List Pengumuman</h4>
                <div class="d-flex align-items-center">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item active" aria-current="page">Pengumuman</li>
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
                    <h4 class="card-title">List Pengumuman</h4>
                    <div class="text-right">
                        <a href="{{ URL::to('dashboard/program/add') }}"><button class="btn btn-success btn-sm mb-2"><i class="fas fa-plus"></i>&nbsp; Add new Pengumuman</button></a>
                    </div>
                    <div class="table-responsive">
                        <table id="zero_config" class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th class="text-center">Pengumuman</th>
                                    <th class="text-center">Status</th>
                                    <th class="text-center">Pembuat</th>
                                    <th class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @for($i = 0; $i < count($data); $i++)
                                    <tr class="text-center">
                                        <td>{{ $data[$i]->name }}</td>
                                        <td>{{ $data[$i]->status }}</td>
                                        <td>{{ $data[$i]->username }}</td>
                                        <td class="text-center">
                                            <a href="{{ URL::to('dashboard/program/edit/'.$data[$i]->id) }}" class="btn waves-effect waves-light btn-xs btn-warning"><i class="fas fa-pencil-alt"></i></a>
                                            <a href="{{ URL::to('dashboard/program/delete/'.$data[$i]->id) }}" class="btn waves-effect waves-light btn-xs btn-danger" onclick="return confirm('Are you sure to delete this program?')"><i class="fas fa-trash-alt"></i></a>
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

<script src="{!! asset('theme/assets/libs/jquery/dist/jquery.min.js') !!}"></script>
<script src="{!! asset('theme/assets/extra-libs/DataTables/datatables.min.js') !!}"></script>

@endsection