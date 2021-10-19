@extends('theme.default')

@section('breadcrumb')
<div class="page-breadcrumb">
    <div class="row">
        <div class="col-5 align-self-center">
            <h4 class="page-title">Stok Periodik</h4>
            <div class="d-flex align-items-center">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item active" aria-current="page">Stok Periodik</li>
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
                            <h4 class="card-title">List Stok Periodik</h4>
                            <form method="POST" action="{{ URL::to('inventory/stock_d') }}" class="form-inline float-right">
                              @csrf
                            <div class="form-inline">
                                <!-- <div class="form-group">
                                <select name="" id="" class="form-control select2" style="width:120px"></select>
                                </div>&nbsp; -->
                                <label>Pilih Jenis : </label>&nbsp;
                                <select name="category" id="category" class="form-control select2" style="width:120px">
                                <option value="all">All</option><option value="MATERIAL">MATERIAL</option><option value="SPARE PART">SPARE PART</option><option value="KACA">KACA</option></select>
                                </select>
                                &nbsp;
                                <label>Pilih Gudang : </label>&nbsp;
                                <select name="warehouse_id" id="warehouse_id" class="form-control select2" style="width:120px">
                                    <option value="">Pilih Gudang</option>
                                    @foreach($warehouse as $row)
                                    <option value="{{$row->id}}">{{$row->name}}</option>
                                    @endforeach
                                </select>
                                &nbsp;
                                <label>Pilih Tanggal : </label>&nbsp;
                                <input type="date" name="date" id="date" class="form-control" value="{{date('Y-m-d')}}">
                                &nbsp;
                                <input type="date" name="date2" id="date2" class="form-control" value="{{date('Y-m-d')}}">&nbsp;
                                <button class="btn btn-primary" type="button" onclick="updateStockList()"><i class="fa fa-search"></i></button>
                                &nbsp;
                                <button class="btn btn-success" type="button" onclick="exportData()"><i class="mdi mdi-file-excel"></i> Export</button>
                            </div>
                            </form>
                        </div>
                    </div>
                     <br>
                    <div class="table-responsive">
                        <table id="list_stok" class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th class="text-center">Gudang</th>
                                    <th class="text-center">Material No</th>
                                    <th class="text-center">Material Name</th>
                                    <th class="text-center">Unit Name</th>
                                    <th class="text-center">Stok Awal</th>
                                    <th class="text-center">Stok Masuk</th>
                                    <th class="text-center">Stok Keluar</th>
                                    <th class="text-center">Stok</th>
                                    <th class="text-center">Harga</th>
                                    <th class="text-center">Nilai Item</th>
                                </tr>
                            </thead>
                            <tfoot>
                                <tr>
                                    <th colspan="7">Total</th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
                
</div>
<div class="modal fade" id="stok_in" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="title_detail_install">Detail Stok Masuk</h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped" id="detail_stok_in">
                        <thead>
                            <tr>
                                <th class="text-center">Tanggal</th>
                                <th class="text-center">Qty</th>
                                <th class="text-center">Nilai</th>
                                <th class="text-center" width="200px">Keterangan</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger waves-effect text-left" data-dismiss="modal">Close</button>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<div class="modal fade" id="stok_out" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="title_detail_install">Detail Stok Keluar</h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped" id="detail_stok_out">
                        <thead>
                            <tr>
                                <th class="text-center">Tanggal</th>
                                <th class="text-center">Qty</th>
                                <th class="text-center">Nilai</th>
                                <th class="text-center" width="200px">Keterangan</th>
                            </tr>

                        </thead>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger waves-effect text-left" data-dismiss="modal">Close</button>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<script src="{!! asset('theme/assets/libs/jquery/dist/jquery.min.js') !!}"></script>
<script src="{!! asset('theme/assets/extra-libs/DataTables/datatables.min.js') !!}"></script>

<script>
$(document).ready(function(){
    t = $('#detail_stok_in').DataTable();
    t2 = $('#detail_stok_out').DataTable();
    dt = $('#list_stok').DataTable( {
        "processing": true,
        "serverSide": true,
        "ajax": "{{ URL::to('inventory/stock_periodic_json') }}",
        "lengthMenu": [[10, 25, 50, 100, 1000, -1], [10, 25, 50, 100, 1000, "All"]],
        aaSorting: [[0, 'desc']],
        "columns": [
            {"data": "warehouse", "class" : "text-center"},
            {"data": "no", "class" : "text-center"},
            {"data": "name", "class" : "text-center"},
            {"data": "unit_name", "class" : "text-center"},
            {"data": "stok_awal", "class" : "text-right",
            "render": function(data, type, row){return formatCurrency(parseFloat(row.stok_awal).toFixed(0))}},
            {"data": "amount_in", "class" : "text-right",
            "render": function(data, type, row){
                return '<a href="" data-target="#stok_in" data-id="'+row.m_item_id+'" data-m_warehouse_id="'+row.m_warehouse_id+'" data-toggle="modal" onclick="getStokIn(this)">'+formatCurrency(parseFloat(row.amount_in).toFixed(0))+'</a>';
            }},
            {"data": "amount_out", "class" : "text-right",
            "render": function(data, type, row){
                return '<a href="" data-target="#stok_out" data-id="'+row.m_item_id+'" data-m_warehouse_id="'+row.m_warehouse_id+'" data-toggle="modal" onclick="getStokOut(this)">'+formatCurrency(parseFloat(row.amount_out).toFixed(0))+'</a>';
            }},
            {"data": "stok", "class" : "text-right",
            "render": function(data, type, row){return formatCurrency(parseFloat(row.stok).toFixed(0))}},
            {"data": "price_last", "class" : "text-right",
            "render": function(data, type, row){return formatCurrency(parseFloat(row.price_last).toFixed(0))}},
            {"data": "total", "class" : "text-right",
            "render": function(data, type, row){return formatCurrency(parseFloat(row.total).toFixed(0))}},
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
                    total = api
                        .column( 7 )
                        .data()
                        .reduce( function (a, b) {
                            return intVal(a) + intVal(b);
                        }, 0 );
                    jumlah = api
                        .column( 9 )
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
                    $( api.column( 7 ).footer() ).html(formatCurrency(total.toFixed(0)));
                    $( api.column( 9 ).footer() ).html(formatCurrency(jumlah.toFixed(0)));
                }
    } );
    
});
function updateStockList() {
    var data = {
        date : $('#date').val(),
        date2 : $('#date2').val(),
        warehouse_id : $('#warehouse_id').val(),
        category : $('#category').val(),
    };
    var out = [];

    for (var key in data) {
        out.push(key + '=' + encodeURIComponent(data[key]));
    }
    url_data = out.join('&');
    dt.ajax.url('{{ URL::to('inventory/stock_periodic_json?') }}' + url_data).load();
}
function exportData(){
    date = $('#date').val()
    date2 = $('#date2').val()
    warehouse_id = $('#warehouse_id').val()
    category = $('#category').val()

    var form = $('<form id="export_form" action="{{ URL::to('inventory/export_stock_periodic') }}" method="post" target="_blank" hidden>' +
    '<input type="text" name="_token" value="{{ csrf_token() }}" />' +
    '<input type="text" name="date" value="' + date + '" />' +
    '<input type="text" name="date2" value="' + date2 + '" />' +
    '<input type="text" name="warehouse_id" value="' + warehouse_id + '" />' +
    '<input type="text" name="category" value="' + category + '" />' +
    '</form>');
    $('body').append(form);
    form.submit();
    $('#export_form').remove();
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
function getStokIn(eq) {
    var id=$(eq).data('id');
    var warehouse_id=$(eq).data('m_warehouse_id');
    var data = {
        date : $('#date').val(),
        date2 : $('#date2').val(),
        id  : $(eq).data('id'),
        warehouse_id    : $(eq).data('m_warehouse_id')
    };
    var out = [];

    for (var key in data) {
        out.push(key + '=' + encodeURIComponent(data[key]));
    }
    url_data = out.join('&');
    // t = $('#detail_payment2').DataTable();
    t.clear().draw(false);
    var total_min=0;
    $.ajax({
            type: "GET",
            url: "{{ URL::to('inventory/stok_in') }}"+'?'+url_data, //json get site
            dataType : 'json',
            async : false,
            success: function(response){
                arrData = response['data'];
                for(i = 0; i < arrData.length; i++){
                    total=parseInt(arrData[i]['amount'] * arrData[i]['base_price']);
                    notes=arrData[i]['purchase'] != null ? 'Purchase Order dari No : '+arrData[i]['purchase']['no'] : (arrData[i]['purchase_asset'] != null ? 'Purchase Order ATK dari No : '+arrData[i]['purchase_asset']['no'] : (arrData[i]['ts_warehouse'] != null ? 'Transfer dari '+arrData[i]['ts_warehouse']['warehouse']+' dari No Permintaan : '+arrData[i]['ts_warehouse']['no'] : ''))
                    t.row.add([
                        '<div class="text-center">'+formatDateID(new Date((arrData[i]['inv_trx_date']).substring(0,10)))+'</div>',
                        '<div class="text-right">'+arrData[i]['amount']+'</div>',
                        '<div class="text-right">'+formatCurrency(total)+'</div>',
                        '<div class="text-center">'+notes+'</div>',
                    ]).draw(false);
                }
            }
    });
}
function getStokOut(eq) {
    var id=$(eq).data('id');
    var warehouse_id=$(eq).data('m_warehouse_id');
    var data = {
        date : $('#date').val(),
        date2 : $('#date2').val(),
        id  : $(eq).data('id'),
        warehouse_id    : $(eq).data('m_warehouse_id')
    };
    var out = [];

    for (var key in data) {
        out.push(key + '=' + encodeURIComponent(data[key]));
    }
    url_data = out.join('&');
    t2.clear().draw(false);
    var total_min=0;
    $.ajax({
            type: "GET",
            url: "{{ URL::to('inventory/stok_out') }}"+'?'+url_data, //json get site
            dataType : 'json',
            async : false,
            success: function(response){
                arrData = response['data'];
                console.log(arrData)
                for(i = 0; i < arrData.length; i++){
                    total=parseInt(arrData[i]['amount'] * arrData[i]['base_price']);
                    notes=arrData[i]['inv_request'] != null ? 'Pengeluaran Material dari No : '+arrData[i]['inv_request']['no'] : (arrData[i]['inv_sale'] != null ? 'Penjualan Material dari No : '+arrData[i]['inv_sale']['no'] : '')
                    t2.row.add([
                        '<div class="text-center">'+formatDateID(new Date((arrData[i]['inv_trx_date']).substring(0,10)))+'</div>',
                        '<div class="text-right">'+arrData[i]['amount']+'</div>',
                        '<div class="text-right">'+formatCurrency(total)+'</div>',
                        '<div class="text-center">'+notes+'</div>',
                    ]).draw(false);
                }
            }
    });
}
</script>

@endsection