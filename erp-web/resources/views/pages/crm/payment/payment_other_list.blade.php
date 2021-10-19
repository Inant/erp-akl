@extends('theme.default')

@section('breadcrumb')
    <div class="page-breadcrumb">
        <div class="row">
            <div class="col-5 align-self-center">
                <h4 class="page-title">Daftar Pembayaran Beban Produksi</h4>
                <div class="d-flex align-items-center">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item active" aria-current="page">Pembayaran Beban Produksi</li>
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
            <div class="text-right">
                <a href="{{ URL::to('payment/create_cost') }}"><button class="btn btn-success btn-sm mb-2"><i class="fas fa-plus"></i>&nbsp; Create New</button></a>
            </div>
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Pembayaran Beban Produksi</h4>
                    <div class="table-responsive">
                        <table id="order_list" class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th class="text-center">Nomor Pembayaran</th>
                                    <th class="text-center">Nomor Permintaan</th>
                                    <th class="text-center">Nomor Order</th>
                                    <th class="text-center">Total</th>
                                    <th class="text-center">Pembayaran Untuk</th>
                                    <th class="text-center">Deskripsi</th>
                                    <th class="text-center">Tanggal Pembayaran</th>
                                    <!-- <th class="text-center">Action</th> -->
                                </tr>
                            </thead>
                        </table>
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
        // "ajax": "{{ URL::to('payment/list_payment_sdm') }}",
        "ajax": {
            "url": "{{ URL::to('payment/list_payment_sdm') }}",
            "data" : {tipe : 'charge'}
        },
        "aaSorting": [[ 0, "desc" ]],
        "columns": [
            {"data": "paid_no"},
            {"data": "req_no"},
            {"data": "order_no"},
            {"data": "amount", "render" : function(data, type, row){
                return formatRupiah(row.amount);
            }, "class" : 'text-right'},
            {"data": "payment_type", "render" : function(data, type, row){
                return row.payment_type == "PAY_FRAME" ? 'Pembayaran Pemasangan Produk' : 'Pembayaran Produksi';
            }},
            {"data": "description"},
            {"data": "pay_date", "render" : function(data, type, row){
                return formatTanggal(row.pay_date);
            }, 'class' : 'text-center'},
            // {"data": "action"}
        ],
    } );
});
function formatTanggal(date) {
  var temp=date.split('-');
  return temp[2] + '-' + temp[1] + '-' + temp[0];
}
function formatRupiah(angka, prefix)
{
    var reverse = angka.toString().split('').reverse().join(''),
    ribuan = reverse.match(/\d{1,3}/g);
    ribuan = ribuan.join('.').split('').reverse().join('');
    return ribuan;
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
</script>
@endsection