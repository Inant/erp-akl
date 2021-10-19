@extends('theme.default')

@section('breadcrumb')
    <div class="page-breadcrumb">
        <div class="row">
            <div class="col-5 align-self-center">
                <h4 class="page-title">List Gaji</h4>
                <div class="d-flex align-items-center">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item active" aria-current="page">Gaji</li>
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
                    <h4 class="card-title">List Setting Gaji</h4>
                    <div class="text-right">
                        <a href="{{ URL::to('salary/add') }}"><button class="btn btn-success btn-sm mb-2"><i class="fas fa-plus"></i>&nbsp; Add Setting Gaji</button></a>
                    </div>
                    <div class="table-responsive">
                        <table id="Jabatan_list" class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th class="text-center">Id</th>
                                    <th class="text-center">Nama</th>
                                    <th class="text-center">Jabatan</th>
                                    <th class="text-center">Lokasi</th>
                                    <th class="text-center">Gaji Pokok</th>
                                    <th class="text-center">Uang Makan</th>
                                    <th class="text-center">Uang Transport</th>
                                    <th class="text-center">Denda Absen</th>
                                    <th class="text-center">Denda Telat</th>
                                    <th class="text-center">Action</th>
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
    $('#Jabatan_list').DataTable( {
        "processing": true,
        "serverSide": true,
        "ajax": "salary",
        "columns": [
            {"data": "id_setting_gaji"},
            {"data": "name"},
            {"data": "position_name"},
            {"data": "site_name"},
            {"data": "gaji_pokok", "render" : function(data, type, row){
                return 'Rp. ' +formatRupiah(row.gaji_pokok);
            }},
            {"data": "uang_makan", "render" : function(data, type, row){
                return 'Rp. ' +formatRupiah(row.uang_makan);
            }},
            {"data": "uang_transport", "render" : function(data, type, row){
                return 'Rp. ' +formatRupiah(row.uang_transport);
            }},
            {"data": "denda", "render" : function(data, type, row){
                return 'Rp. ' +formatRupiah(row.denda);
            }},
            {"data": "denda_telat", "render" : function(data, type, row){
                return 'Rp. ' +formatRupiah(row.denda_telat);
            }},
            {"data": "action"}
        ],
    } );
});
function formatRupiah(angka, prefix)
{
    if (angka == null) {
        return '0';
    }
    var reverse = angka.toString().split('').reverse().join(''),
    ribuan = reverse.match(/\d{1,3}/g);
    ribuan = ribuan.join('.').split('').reverse().join('');
    return ribuan;
}
</script>
@endsection