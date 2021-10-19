@extends('theme.default')

@section('breadcrumb')
<div class="page-breadcrumb">
    <div class="row">
        <div class="col-5 align-self-center">
            <h4 class="page-title">Site Stock History</h4>
            <div class="d-flex align-items-center">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item active" aria-current="page">Site Stock History</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
</div>
<style>
div.dataTables_wrapper div.dataTables_processing {
  top: 0;
}
</style>
@endsection

@section('content')
@php
    function formatRupiah($num){
        return number_format($num, 0, '.', '.');
    }
    function formatDate($date){
        $date=date_create($date);
        return date_format($date, 'd-m-Y');
    }
@endphp
<div class="container-fluid">
    <!-- basic table -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-12">
                            <h4 class="card-title">List Site Stock History</h4>
                            <form method="POST" action="{{ URL::to('inventory/stock_d') }}" class="form-inline float-right">
                              @csrf
                            <div class="form-inline">
                                <!-- <div class="form-group">
                                <select name="" id="" class="form-control select2" style="width:120px"></select>
                                </div>&nbsp; -->
                                <label>Pilih Tanggal : </label>&nbsp;
                                <input type="date" name="date" id="date" class="form-control" value="{{date('Y-m-d')}}">
                                &nbsp;
                                <input type="date" name="date2" id="date2" class="form-control" value="{{date('Y-m-d')}}">&nbsp;
                                <button class="btn btn-primary" type="button" onclick="updateStockList()"><i class="fa fa-search"></i></button>
                            </div>
                            </form>
                        </div>
                    </div>
                     <br>
                    <div class="table-responsive">
                        <table id="list_stok" class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th class="text-center">Material No</th>
                                    <th class="text-center">Material Name</th>
                                    <!-- <th class="text-center">Nilai Material</th> -->
                                    <th class="text-center">Satuan</th>
                                    <th class="text-center">Stok Awal</th>
                                    <th class="text-center">Harga Awal</th>
                                    <th class="text-center">Jumlah / Nilai Stok Awal</th>
                                    <th class="text-center">Stok Masuk</th>
                                    <th class="text-center">Jumlah / Nilai Masuk</th>
                                    <th class="text-center">Stok Keluar</th>
                                    <th class="text-center">Jumlah / Nilai Keluar</th>
                                    <th class="text-center">Stok Ahir</th>
                                    <th class="text-center">Harga Ahir</th>
                                    <th class="text-center">Nilai Item</th>
                                </tr>
                            </thead>
                            <tfoot>
                                <tr>
                                    <th colspan="5">Total</th>
                                    <th id="nilai_stok_awal"></th>
                                    <th></th>
                                    <th id="nilai_masuk"></th>
                                    <th></th>
                                    <th id="nilai_keluar"></th>
                                    <th colspan="2"></th>
                                    <th id="nilai_ahir"></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
                
</div>

<script src="{!! asset('theme/assets/libs/jquery/dist/jquery.min.js') !!}"></script>
<script src="{!! asset('theme/assets/extra-libs/DataTables/datatables.min.js') !!}"></script>

<script>
$(document).ready(function(){
    dt = $('#list_stok').DataTable( {
        "processing": true,
        "serverSide": true,
        "ajax": "{{ URL::to('inventory/history_stock_json') }}",
        "lengthMenu": [[10, 25, 50, 100, 1000, -1], [10, 25, 50, 100, 1000, "All"]],
        aaSorting: [[0, 'desc']],
        "columns": [
            {"data": "no", "class" : "text-center"},
            {"data": "name", "class" : "text-center"},
            {"data": "unit_name", "class" : "text-center"},
            {"data": "stok_awal", "class" : "text-center"},
            {"data": "price_first", "class" : "text-right",
            "render": function(data, type, row){return formatCurrency(parseFloat(row.price_first).toFixed(0))}},
            {"data": "value_first", "class" : "text-right",
            "render": function(data, type, row){return formatCurrency(parseFloat(row.value_first).toFixed(0))}},
            {"data": "total_material_penerimaan", "class" : "text-center"},
            {"data": "total_penerimaan", "class" : "text-right",
            "render": function(data, type, row){return formatCurrency(parseFloat(row.total_penerimaan).toFixed(0))}},
            {"data": "total_material_pengeluaran", "class" : "text-center"},
            {"data": "total_pengeluaran", "class" : "text-right",
            "render": function(data, type, row){return formatCurrency(parseFloat(row.total_pengeluaran).toFixed(0))}},
            {"data": "stok_last", "class" : "text-center"},
            {"data": "price_last", "class" : "text-right",
            "render": function(data, type, row){return formatCurrency(parseFloat(row.price_last).toFixed(0))}},
            {"data": "value_item", "class" : "text-right",
            "render": function(data, type, row){return formatCurrency(parseFloat(row.value_item).toFixed(0))}},
        ],
        "footerCallback": function ( row, data, start, end, display ) {
                    var api = this.api(), data;
            
                    // Remove the formatting to get integer data for summation
                    var intVal = function ( i ) {
                        return typeof i === 'string' ?
                            i.replace(/[\$,]/g, '')*1 :
                            typeof i === 'number' ?
                                i : 0;
                    };
            
                    // Total over all pages
                    total_stok_awal = api
                        .column( 5 )
                        .data()
                        .reduce( function (a, b) {
                            return intVal(a) + intVal(b);
                        }, 0 );
                    total_masuk = api
                        .column( 7 )
                        .data()
                        .reduce( function (a, b) {
                            return intVal(a) + intVal(b);
                        }, 0 );
                    total_keluar = api
                        .column( 9 )
                        .data()
                        .reduce( function (a, b) {
                            return intVal(a) + intVal(b);
                        }, 0 );
                    total = api
                        .column( 12 )
                        .data()
                        .reduce( function (a, b) {
                            return intVal(a) + intVal(b);
                        }, 0 );
            
                    // Total over this page
                    // pageTotal = api
                    //     .column( 11, { page: 'current'} )
                    //     .data()
                    //     .reduce( function (a, b) {
                    //         return intVal(a) + intVal(b);
                    //     }, 0 );
                    // Update footer
                    $( api.column( 5 ).footer() ).html(formatCurrency(total_stok_awal.toFixed(0)));
                    $( api.column( 7 ).footer() ).html(formatCurrency(total_masuk.toFixed(0)));
                    $( api.column( 9 ).footer() ).html(formatCurrency(total_keluar.toFixed(0)));
                    $( api.column( 12 ).footer() ).html(formatCurrency(total.toFixed(0)));
                }
    } );
    
});
function updateStockList() {
    var data = {
        date : $('#date').val(),
        date2 : $('#date2').val(),
    };
    var out = [];

    for (var key in data) {
        out.push(key + '=' + encodeURIComponent(data[key]));
    }
    url_data = out.join('&');
    dt.ajax.url('{{ URL::to('inventory/history_stock_json?') }}' + url_data).load();
}

function formatBulan(val){
    var bulan = ['Januari', 'Februari', 'Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
    return bulan[val-1]
}

function datediffnow(date) {
    dt1 = new Date(date);
    dt2 = new Date();
    return Math.floor((Date.UTC(dt2.getFullYear(), dt2.getMonth(), dt2.getDate()) - Date.UTC(dt1.getFullYear(), dt1.getMonth(), dt1.getDate()) ) /(1000 * 60 * 60 * 24));
}
</script>

@endsection