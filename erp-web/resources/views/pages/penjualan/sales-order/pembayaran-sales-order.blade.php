@extends('theme.default')

@section('breadcrumb')
<div class="page-breadcrumb">
    <div class="row">
        <div class="col-5 align-self-center">
            <h4 class="page-title">Pembayaran Sales Order</h4>
            <div class="d-flex align-items-center">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item active" aria-current="page">Pembayaran Sales Order</li>
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
            {{-- <div class="text-right">
                <a href="{{ URL::to('penjualan/penawaran/create') }}"><button class="btn btn-success btn-sm mb-2"><i class="fas fa-plus"></i>&nbsp; Create Penawaran</button></a>
            </div> --}}
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">List Sales Order</h4>
                    <div class="table-responsive">
                        <table id="list_sales_order" class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th class="text-center">Nomor Sales Order</th>
                                    <th class="text-center">Nomor Penawaran</th>
                                    <th class="text-center">Site</th>
                                    <th class="text-center">Tanggal</th>
                                    <th class="text-center">Total</th>
                                    <th class="text-center">Terbayar</th>
                                    <th class="text-center">Customer</th>
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

<div class="modal fade bs-example-modal-lg" id="modalDetail" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myLargeModalLabel">Penawaran Detail</h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">??</button>
            </div>
            <div class="modal-body">
                <h4>Penawaran Detail</h4>
                <div class="table-responsive">
                    <table id="zero_config2" class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th class="text-center">Material No</th>
                                <th class="text-center">Material Name</th>
                                <th class="text-center">Qty</th>
                                <th class="text-center">Harga Satuan</th>
                                <th class="text-center">Satuan</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger waves-effect btn-sm text-left" data-dismiss="modal">Close</button>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<!-- /.modal -->

<script src="{!! asset('theme/assets/libs/jquery/dist/jquery.min.js') !!}"></script>
<script src="{!! asset('theme/assets/extra-libs/DataTables/datatables.min.js') !!}"></script>
<script>

var t2 = $('#zero_config2').DataTable();
$(document).ready(function(){

    $.ajax({
        type: "get",
        url: "{{ URL::to('penjualan/sales-order/list') }}",
        dataType: "json",
        success: function (response) {
            console.log(response)
        }
    });

    $('#list_sales_order').DataTable( {
        "processing": true,
        "serverSide": true,
        "ajax": "{{ URL::to('penjualan/sales-order/list') }}",
        aaSorting: [[2, 'desc']],
        "columns": [
            {"data": "no"},
            {"data": "no_penawaran"},
            {"data": "sites.name"},
            {"data": "tanggal", "class" : "text-center",
            "render": function(data, type, row){return formatDateID(new Date((row.tanggal)))}},
            {"data": "total_amount", "class" : "text-right",
            "render": function(data, type, row){return formatCurrency(parseInt(row.total_amount).toString())}},
            {"data": "terbayar", "class" : "text-right",
            "render": function(data, type, row){return row.terbayar == null ? 0 : formatCurrency(parseInt(row.terbayar).toString())}},
            {"data": "coorporate_name"},
            {"data": "id",
            "render": function(data, type, row){return '<div class="text-center"><button onclick="doShowDetail('+row.id+');" data-toggle="modal" data-target="#modalDetail" class="btn waves-effect waves-light btn-sm btn-info openModalTransferDetail">Detail</button> <a href="{{URL::to('penjualan/pembayaran-sales-order')}}/'+row.id+'" class="btn btn-sm btn-primary"> Pembayaran </a> </div>'}},
        ],
    } );
});

function doShowDetail(id){
    t2.clear().draw(false);
    $.ajax({
            type: "GET",
            url: "{{ URL::to('penjualan/sales-order-detail') }}" + "/" + id, //json get site
            dataType : 'json',
            success: function(response){
                console.log(response)
                arrData = response['data'];
                for(i = 0; i < arrData.length; i++){
                    console.log(arrData[i]['itemno'])
                    t2.row.add([
                        '<div class="text-left">'+arrData[i]['itemno']+'</div>',
                        '<div class="text-left">'+arrData[i]['itemname']+'</div>',
                        '<div class="text-right">'+parseFloat(arrData[i]['amount'])+'</div>',
                        '<div class="text-right">'+formatCurrency(arrData[i]['base_price'])+'</div>',
                        '<div class="text-center">'+arrData[i]['unitname']+'</div>'
                    ]).draw(false);
                }
            }
    });
}


</script>


@endsection