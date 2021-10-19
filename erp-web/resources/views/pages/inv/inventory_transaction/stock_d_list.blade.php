@extends('theme.default')

@section('breadcrumb')
<div class="page-breadcrumb">
    <div class="row">
        <div class="col-5 align-self-center">
            <h4 class="page-title">Site Stock Bulanan</h4>
            <div class="d-flex align-items-center">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item active" aria-current="page">Site Stock Bulanan</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
</div>
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
                            <h4 class="card-title">List Site Stock Bulanan</h4>
                            <form method="POST" action="{{ URL::to('inventory/stock_d') }}" class="form-inline float-right">
                              @csrf
                            <div class="form-inline">
                                <!-- <div class="form-group">
                                <select name="" id="" class="form-control select2" style="width:120px"></select>
                                </div>&nbsp; -->
                                <label>Pilih Bulan : </label>&nbsp;
                                <select class="form-control select2" name="bulan" id="bulan" required style="width:120px">
                                    <option value="">--Pilih Bulan--</option>
                                    <option value="01">Januari</option>
                                    <option value="02">Februari</option>
                                    <option value="03">Maret</option>
                                    <option value="04">April</option>
                                    <option value="05">Mei</option>
                                    <option value="06">Juni</option>
                                    <option value="07">Juli</option>
                                    <option value="08">Agustus</option>
                                    <option value="09">September</option>
                                    <option value="10">Oktober</option>
                                    <option value="11">November</option>
                                    <option value="12">Desember</option>
                                </select>
                                &nbsp;
                                <select class="form-control select2" name="tahun" id="tahun" required  style="width:120px">
                                    <option value="">--Pilih Tahun--</option>
                                    @for ($i = date('Y') - 5; $i <= date('Y'); $i++)
                                    <option value="{{$i}}">{{$i}}</option>
                                    @endfor
                                </select>&nbsp;
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
                                    <!-- <th class="text-center">Inv No</th> -->
                                    <!-- <th class="text-center">Site Name</th> -->
                                    <th class="text-center">Gudang</th>
                                    <th class="text-center">Material No</th>
                                    <th class="text-center">Material Name</th>
                                    <th class="text-center">Nilai Material</th>
                                    <th class="text-center">Harga Satuan</th>
                                    <th class="text-center">Satuan</th>
                                    <th class="text-center">Stock In</th>
                                    <th class="text-center">Stock Out</th>
                                    <th class="text-center">Current Stock</th>
                                    <th class="text-center">Tipe</th>
                                </tr>
                            </thead>
                            <tfoot>
                                <tr>
                                    <th colspan="3">Total</th>
                                    <th class="text-right"></th>
                                    <th colspan="6"></th>
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
var m_warehouse_id='{{$m_warehouse_id}}';
$(document).ready(function(){
    dt = $('#list_stok').DataTable( {
        "processing": true,
        "serverSide": true,
        "ajax": "{{ URL::to('inventory/stock_d_json') }}",
        "lengthMenu": [[10, 25, 50, 100, 1000, -1], [10, 25, 50, 100, 1000, "All"]],
        aaSorting: [[0, 'desc']],
        "columns": [
            {"data": "m_warehouse.name", "class" : "text-center"},
            {"data": "m_items.no", "class" : "text-center"},
            {"data": "m_items.name", "class" : "text-center"},
            {"data": "value", "class" : "text-right",
            "render": function(data, type, row){return formatCurrency(parseFloat(row.value).toFixed(0))}},
            {"data": "last_price", "class" : "text-right",
            "render": function(data, type, row){return formatCurrency(parseFloat(row.last_price).toFixed(0))}},
            {"data": "m_units.name", "class" : "text-center"},
            {"data": "amount_in", "class" : "text-right",
            "render": function(data, type, row){return formatCurrency(row.amount_in)}},
            {"data": "amount_out", "class" : "text-right",
            "render": function(data, type, row){return formatCurrency(row.amount_out)}},
            {"data": "amount", "class" : "text-right",
            "render": function(data, type, row){return formatCurrency(row.amount)}},
            {"data": "type", "class" : "text-right",
            "render": function(data, type, row){return row.type == 'STK_NORMAL' ? 'Normal' : 'Transfer'}},
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
                        .column( 3 )
                        .data()
                        .reduce( function (a, b) {
                            return intVal(a) + intVal(b);
                        }, 0 );
            
                    // Total over this page
                    pageTotal = api
                        .column( 3, { page: 'current'} )
                        .data()
                        .reduce( function (a, b) {
                            return intVal(a) + intVal(b);
                        }, 0 );
                    // Update footer
                    $( api.column( 3 ).footer() ).html(formatCurrency(total.toFixed(0)));
                }
    } );
    
});
function updateStockList() {
    var data = {
        bulan : $('#bulan').val(),
        tahun : $('#tahun').val(),
    };
    var out = [];

    for (var key in data) {
        out.push(key + '=' + encodeURIComponent(data[key]));
    }
    url_data = out.join('&');
    dt.ajax.url('{{ URL::to('inventory/stock_d_json?') }}' + url_data).load();
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