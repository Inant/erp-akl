@extends('theme.default')

@section('breadcrumb')
    <div class="page-breadcrumb">
        <div class="row">
            <div class="col-5 align-self-center">
                <h4 class="page-title">List Absensi</h4>
                <div class="d-flex align-items-center">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item active" aria-current="page">Absensi</li>
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
                    <h4 class="card-title">List Absensi</h4>
                    <div class="text-right">
                        <a href="{{ URL::to('absensi/import') }}"><button class="btn btn-success btn-sm mb-2"><i class="mdi mdi-import"></i>&nbsp; Import Absensi</button></a>
                        <a href="{{ URL::to('absensi/month') }}"><button class="btn btn-info btn-sm mb-2"><i class="mdi mdi-calendar-clock"></i>&nbsp; Log Absensi Per Bulan</button></a>
                        <a href="{{ URL::to('absensi/import') }}"><button class="btn btn-info btn-sm mb-2"><i class="mdi mdi-coins"></i>&nbsp; Tambah Uang Lembur</button></a>
                    </div>
                    <div class="table-responsive">
                        <table id="absensi_list" class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th>Id</th>
                                    <th>Nama</th>
                                    <th>Tanggal</th>
                                    <th>Jam Datang</th>
                                    <th>Jam Pulang</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>                
</div>

<!-- <script src="{!! asset('theme/assets/libs/jquery/dist/jquery.min.js') !!}"></script> -->
<script type="text/javascript" src="https://code.jquery.com/jquery-3.3.1.js"></script>
<script src="{!! asset('theme/assets/extra-libs/DataTables/datatables.min.js') !!}"></script>
<script type="text/javascript">
$(document).ready(function() {
    $('#absensi_list').DataTable( {
        "processing": true,
        "serverSide": true,
        "ajax": "absensi/json",
        "columns": [
            {"data": "id"},{"data": "name"},{"data": "tanggal"},{"data": "jam_datang"},{"data": "jam_pulang"},{"data": "action"}
        ],
    } );
});
</script>
@endsection