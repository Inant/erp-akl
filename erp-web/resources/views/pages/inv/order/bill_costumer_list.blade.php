@extends('theme.default')

@section('breadcrumb')
    <div class="page-breadcrumb">
        <div class="row">
            <div class="col-5 align-self-center">
                <h4 class="page-title">Daftar Tagihan Order</h4>
                <div class="d-flex align-items-center">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item active" aria-current="page">Daftar Tagihan Order</li>
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
                    <h4 class="card-title">Daftar Tagihan Order</h4>
                    <ul class="nav nav-tabs" role="tablist">
                        <li class="nav-item"> <a class="nav-link active" data-toggle="tab" href="#tab_order" role="tab"><span class="hidden-sm-up"><i class="mdi mdi-solid"></i></span> <span class="hidden-xs-down">Order List</span></a> </li>
                        <li class="nav-item" onclick=""> <a class="nav-link" data-toggle="tab" href="#tab_install_order" role="tab"><span class="hidden-sm-up"><i class="mdi mdi-view-grid"></i></span> <span class="hidden-xs-down">Order Instalasi List</span></a> </li>
                    </ul>
                    <div class="tab-content tabcontent-border">
                        <div class="tab-pane active" id="tab_order" role="tabpanel">
                        <br>
                            <div class="table-responsive">
                                <table id="order_list" class="table table-striped table-bordered">
                                    <thead>
                                        <tr>
                                            <th class="text-center">Nomor Order </th>
                                            <th class="text-center">Nomor SPJB </th>
                                            <th class="text-center">Deskripsi Order</th>
                                            <th class="text-center">Nama Customer</th>
                                            <th class="text-center">Tanggal Order</th>
                                            <th class="text-center">Nilai Kontrak</th>
                                            <th class="text-center">Tanggal Pelunasan</th>
                                            <th class="text-center">Action</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                        <div class="tab-pane" id="tab_install_order" role="tabpanel">
                        <br>
                            <div class="table-responsive">
                                <table id="install_order_list" class="table table-striped table-bordered" style="width:100%">
                                    <thead>
                                        <tr>
                                            <th class="text-center">Nomor </th>
                                            <th class="text-center">Nomor Order</th>
                                            <th class="text-center">Nomor SPJB </th>
                                            <th class="text-center">Nomor SPK</th>
                                            <th class="text-center">Nama Customer</th>
                                            <th class="text-center">Tanggal Order</th>
                                            <th class="text-center">Nilai Kontrak</th>
                                            <th class="text-center">Tanggal Pelunasan</th>
                                            <th class="text-center">Action</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                </div>
            </div>
        </div>
        <div class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" style="display: none;">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="title_detail">Detail Order</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    </div>
                    <div class="modal-body">
                        <h4></h4>
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped" id="detail_order">
                                <thead>
                                    <tr>
                                        <th>Produk</th>
                                        <th>Total</th>
                                        <th>Rab Number</th>
                                        <th>Estimasi Jadi</th>
                                        <th>Status Produksi</th>
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
        <!-- /.modal -->
    </div>                
</div>

<script src="{!! asset('theme/assets/libs/jquery/dist/jquery.min.js') !!}"></script>
<script src="{!! asset('theme/assets/extra-libs/DataTables/datatables.min.js') !!}"></script>
<script type="text/javascript">
var uri='{{URL::to('/')}}';
$(document).ready(function() {
    $('#order_list').DataTable( {
        "processing": true,
        "serverSide": true,
        "ajax": {
            "url" : "{{ URL::to('order/list_bill_cust') }}",
            "type" : "get",
            "data" : {
                "dari" : "{{$_GET['dari']}}",
                "sampai" : "{{$_GET['sampai']}}",
                },
        },
        "aaSorting": [[ 0, "desc" ]],
        "columns": [
            {"data": "order_no", 'class' : 'text-center'},
            {"data": "spk_number", 'class' : 'text-center'},
            {"data": "order_name"},
            {"data": "customer_coorporate", 'class' : 'text-center'},
            {"data": "order_date", 'class' : 'text-center', "render": function(data, type, row){return formatTanggal(row.order_date)}},
            {"data": "total", 'class' : 'text-center', "render": function(data, type, row){return formatRupiah(parseInt(row.total).toString())}},
            {"data": "paid_off_date", 'class' : 'text-center', "render": function(data, type, row){return row.paid_off_date == null ? '-' : formatDateID(new Date(row.paid_off_date))}},
            {"data": "action", 'class' : 'text-center'}
        ],
    } );
    $('#install_order_list').DataTable( {
        "processing": true,
        "serverSide": true,
        "ajax": {
            "url" : "{{ URL::to('order/list_install_bill_cust') }}",
            "type" : "get",
            "data" : {
                "dari" : "{{$_GET['dari']}}",
                "sampai" : "{{$_GET['sampai']}}",
                },
        },
        "aaSorting": [[ 0, "desc" ]],
        "columns": [
            {"data": "no", 'class' : 'text-center'},
            {"data": "order_no", 'class' : 'text-center'},
            {"data": "spk_number", 'class' : 'text-center'},
            {"data": "spk_no", 'class' : 'text-center'},
            {"data": "customer_coorporate", 'class' : 'text-center'},
            {"data": "order_date", 'class' : 'text-center', "render": function(data, type, row){return formatTanggal(row.order_date)}},
            {"data": "total", 'class' : 'text-center', "render": function(data, type, row){return formatRupiah(parseInt(row.total).toString())}},
            {"data": "paid_off_date", 'class' : 'text-center', "render": function(data, type, row){return row.paid_off_date == null ? '-' : formatDateID(new Date(row.paid_off_date))}},
            {"data": "action", 'class' : 'text-center'}
        ],
    } );
});
function formatTanggal(date) {
    if (date == null) {
        return '-';
    }else{
        var temp=date.split('-');
        return temp[2] + '-' + temp[1] + '-' + temp[0];
    }
}
function getDetail(el){
    var id=$(el).data('id');
    var order_no=$(el).data('order_no');
    $('#title_detail').html('Detail Order '+order_no);
    t = $('#detail_order').DataTable();
    t.clear().draw(false);
    $.ajax({
        // type: "post",
        url: "{{ URL::to('order/detail') }}"+'/'+id,
        dataType : 'json',
        success: function(response){
            arrData = response['data'];
            var j=0;
            for(i = 0; i < arrData.length; i++){
                j+=1;
                t.row.add([
                    '<div>'+arrData[i]['item']+' '+arrData[i]['name']+' Series : '+arrData[i]['series']+' Dimensi W :'+arrData[i]['panjang']+' H : '+arrData[i]['lebar']+'</div>',
                    '<div>'+arrData[i]['total']+'</div>',
                    '<div>'+(arrData[i]['no'] != null ? arrData[i]['no'] : '-') +'</div>',
                    '<div>'+(arrData[i]['estimate_end'] != null ? formatDateID(new Date(arrData[i]['estimate_end'])) : '-') +'</div>',
                    '<div>'+(arrData[i]['is_final'] != null ? (arrData[i]['is_final'] == 0 ? 'In Rab' : (arrData[i]['is_final_production'] != null ? (arrData[i]['is_final_production'] == 0 ? 'Running' : 'Final') : 'In Rab')) : '-') +'</div>',
                    '</div>'
                ]).draw(false);
            }
        }
    });
}
function formatRupiah(angka, prefix)
{
    if(angka == 'NaN' || angka === null){
        return 0;
    }
    var reverse = angka.toString().split('').reverse().join(''),
    ribuan = reverse.match(/\d{1,3}/g);
    ribuan = ribuan.join('.').split('').reverse().join('');
    return ribuan;
}
</script>
@endsection