@extends('theme.default')

@section('breadcrumb')
			<div class="page-breadcrumb">
                <div class="row">
                    <div class="col-5 align-self-center">
                        <h4 class="page-title">Pembelian Rutin</h4>
                        <div class="d-flex align-items-center">
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item active" aria-current="page">Pembelian Rutin</li>
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
                    @if($error['is_error'])
                    <div class="col-12">
                        <div class="alert alert-danger"> <i class="mdi mdi-alert-box"></i> {{ $error['error_message'] }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close"> <span aria-hidden="true">×</span> </button>
                        </div>
                    </div>
                    @endif
                    <div class="col-12">
                        <div class="text-right">
                            <a href="{{ URL::to('pembelian/create') }}"><button class="btn btn-success btn-sm mb-2"><i class="fas fa-plus"></i>&nbsp; Input Pembelian Rutin</button></a>
                        </div>
                        <div class="card">
                            <div class="card-body">
                                <h4 class="card-title">List Pembelian Rutin</h4>
                                <div class="table-responsive">
                                    <table id="zero_config" class="table table-striped table-bordered">
                                        <thead>
                                            <tr>
                                                <th class="text-center">PO Number</th>
                                                <th class="text-center">RAB Number</th>
                                                <th class="text-center">Site Location</th>
                                                <th class="text-center">Site Name</th>
                                                <th class="text-center">Project Name</th>
                                                <th class="text-center">Tanggal Pembelian</th>
                                                <th class="text-center">Total Nilai Barang</th>
                                                <th class="text-center">Metode Pembayaran</th>
                                                <th class="text-center">Status Pembelian</th>
                                                <th class="text-center">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td class="text-center">001PO201902000001</td>
                                                <td class="text-center">001RAB201902000001</td>
                                                <td class="text-center">Malang</td>
                                                <td class="text-center">Sururi Estate</td>
                                                <td class="text-center">Pembangunan Rumah Pak Jay</td>
                                                <td class="text-center">09/03/2019</td>
                                                <td class="text-right">20.000.000,00</td>
                                                <td class="text-center">Cash</td>
                                                <th class="text-center">Process</th>
                                                <td class="text-center">
                                                    <a href="{{ URL::to('#') }}" class="btn waves-effect waves-light btn-xs btn-info">Detail</a>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="text-center">001PO201902000002</td>
                                                <td class="text-center">001RAB201902000002</td>
                                                <td class="text-center">Malang</td>
                                                <td class="text-center">Sururi Estate</td>
                                                <td class="text-center">Pembangunan Rumah Pak Jay</td>
                                                <td class="text-center">09/03/2019</td>
                                                <td class="text-right">120.000.000,00</td>
                                                <td class="text-center">Credit</td>
                                                <th class="text-center">Completed</th>
                                                <td class="text-center">
                                                    <a href="{{ URL::to('#') }}" class="btn waves-effect waves-light btn-xs btn-info">Detail</a>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
</div>

@endsection