@extends('theme.default')

@section('breadcrumb')
            <div class="page-breadcrumb">
                <div class="row">
                    <div class="col-5 align-self-center">
                        <h4 class="page-title">List Akun</h4>
                        <div class="d-flex align-items-center">
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item active" aria-current="page">Akuntansi</li>
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
                                <h4 class="card-title">List Akun</h4>
                                <a href="{{URL::to('akuntansi/createakun')}}"><button class="btn btn-primary mb-2"><i class="fas fa-plus"></i>&nbsp; Add new account</button></a>
                                <a href="{{URL::to('akuntansi/export_account')}}" target="_blank"><button class="btn btn-success mb-2"><i class="mdi mdi-file-excel"></i>&nbsp; Export account</button></a>
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped" id="zero_config">
                                        <thead>
                                            <tr>
                                                <th>Id Akun</th>
                                                <th>No Akun</th>
                                                <th>Nama Akun</th>
                                                <th>Level</th>
                                                <th>Main</th>
                                                <!-- <th>Action</th>  -->
                                            </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($list as $value)
                                            <tr>
                                                <td>{{$value['id_akun']}}</td>
                                                <td>{{$value['no_akun']}}</td>
                                                <td>{{$value['nama_akun']}}</td>
                                                <td>{{$value['level']}}</td>
                                                <td>{{$value['main']}}</td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
</div>

@endsection