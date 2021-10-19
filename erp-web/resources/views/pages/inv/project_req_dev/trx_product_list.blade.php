@extends('theme.default')

@section('breadcrumb')
    <div class="page-breadcrumb">
        <div class="row">
            <div class="col-5 align-self-center">
                <h4 class="page-title">Penyerahan Produk</h4>
                <div class="d-flex align-items-center">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item active" aria-current="page">Daftar Penyerahan Produk</li>
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
                <a href="{{ URL::to('project_req_dev/trx_product_create') }}"><button class="btn btn-success btn-sm mb-2"><i class="fas fa-plus"></i>&nbsp; Create New</button></a>
            </div>
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Daftar Penyerahan Produk</h4>
                    <div class="table-responsive">
                        <table id="trx_product_list" class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th class="text-center">Inv Number</th>
                                    <th class="text-center">Nomor Permintaan</th>
                                    <th class="text-center">Order Number</th>
                                    <th class="text-center">Customer</th>
                                    <th class="text-center">Rab Number</th>
                                    <th class="text-center">Produk</th>
                                    <th class="text-center">Status</th>
                                    <!-- <th class="text-center">Total</th> -->
                                    <!-- <th class="text-center">Status Pembayaran</th> -->
                                    <th class="text-center" style="min-width: 100px">Action</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- /.modal -->
    </div>                
</div>
<div class="modal fade bs-example-modal-lg" id="modalAccDetail" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myLargeModalLabel">Detail Produk Jadi</h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <div class="modal-body">
                <h4>List Produk Jadi</h4>
                <p id="label-detail"></p>
                <div class="table-responsive">
                    <table id="zero_config2" class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th class="text-center">Label</th>
                                <th class="text-center">Nama Produk</th>
                            </tr>
                        </thead>
                        <tbody>
                            
                        </tbody>
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
<div class="modal fade bs-example-modal-lg" id="modalPayment" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myLargeModalLabel">Detail Tagihan</h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <div class="modal-body">
                <table class="table table-bordered" id="detail_payment">
                    <thead>
                        <tr>
                            <th colspan="4">Total Tagihan</th>
                            <th id="total_bill" class="text-right"></th>
                        </tr>
                        <tr>
                            <th colspan="4">PPN(10 %)</th>
                            <th id="total_ppn" class="text-right"></th>
                        </tr>
                        <tr>
                            <th colspan="4">Total</th>
                            <th id="total_all" class="text-right"></th>
                        </tr>
                        <tr>
                            <th class="text-center">Tanggal Bayar</th>
                            <th class="text-center">Cara Bayar</th>
                            <th class="text-center">Total</th>
                            <th class="text-center">Nama</th>
                            <th>Catatan</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
                <form class="mt-4" action="{{URL::to('project_req_dev/pay_bill')}}" method="post" id="form_payment">
                    @csrf
                    <input type="hidden" class="form-control" id="inv_id"  name="inv_id" placeholder="">
                    <input type="hidden" class="form-control" id="total_all_payment"  name="total_all_payment" placeholder="">
                    <div class="form-group">
                        <label>Total Pembayaran</label>
                        <input type="number" class="form-control" name="total_payment" placeholder="" onchange="cekTotal(this)">
                    </div>
                    <div class="form-group">
                        <label>Tipe Pembayaran</label>
                        <select name="type_payment" id="" class="form-control">
                            <option value="">Pilih metode Pembayaran</option>
                            <option value="cash">Tunai</option>
                            <option value="credit">Kredit</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Atas Nama</label>
                        <input type="" class="form-control" id="atas_nama" name="atas_nama" placeholder="">
                    </div>
                    <div class="form-group">
                        <label>Catatan</label>
                        <textarea class="form-control" name="catatan" id="" cols="20"></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Submit</button>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger waves-effect btn-sm text-left" data-dismiss="modal">Close</button>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<script src="{!! asset('theme/assets/libs/jquery/dist/jquery.min.js') !!}"></script>
<script src="{!! asset('theme/assets/extra-libs/DataTables/datatables.min.js') !!}"></script>
<script type="text/javascript">
var uri='{{URL::to('/')}}';
$(document).ready(function() {
    $('#trx_product_list').DataTable( {
        "processing": true,
        "serverSide": true,
        "ajax": "{{ URL::to('project_req_dev/json_trx_product') }}",
        "aaSorting": [[ 0, "desc" ]],
        "columns": [
            {"data": "no"},
            {"data": "req_no"},
            {"data": "order_no"},
            {"data": "coorporate_name"},
            {"data": "rab_no"},
            {"data": "name", "class" : 'text-center', "render": function(data, type, row){return row.item+' '+row.product_name+' '+row.series}},
            {"data": "type", "class" : 'text-center', "render": function(data, type, row){return row.type == 'TRX_PRODUCT' ? 'PENGIRIMAN' : 'PENGIRIMAN ULANG'}},
            // {"data": "amount", "render": function(data, type, row){
            //     return 'Rp. '+formatRupiah(row.amount);
            // }},
            // {"data": "payment_status", "render": function(data, type, row){
            //     return row.payment_status == false ? 'Belum Lunas' : 'Lunas';
            // }},
            {"data": "id", "render": function(data, type, row){
                return '<button onclick="doShowDetail('+row.id+');" data-toggle="modal" data-target="#modalAccDetail" class="btn waves-effect waves-light btn-sm btn-info" title="detail"><i class="mdi mdi-eye"></i></button>&nbsp;<a href="{{URL::to('project_req_dev/print_surat_jalan')}}/'+row.id+'" class="btn btn-success btn-sm" title="print surat jalan" target="_blank"><i class="mdi mdi-printer"></i></a>&nbsp;<button hidden onclick="doShowDetailPayment(this);" data-toggle="modal" data-target="#modalPayment" data-id="'+row.id+'" class="btn waves-effect waves-light btn-sm btn-primary" title="Tagihan"><i class="mdi mdi-cash-multiple"></i></button>'
            }}
        ],
    } );
});
function cekTotal(eq){
    var total=eq.value;
    var total_all=$('#total_all_payment').val();
    if(total > total_all){
        $(eq).val('');
        alert('inputan melebihi total yang harus dibayar');
    }
}
function doShowDetailPayment(eq){
    var id=$(eq).data('id');
    $('#detail_payment > tbody').empty();
    $.ajax({
            type: "GET",
            url: "{{ URL::to('project_req_dev/get_payment') }}" + "/" + id, //json get site
            dataType : 'json',
            success: function(response){
                arrData = response['data'];
                arrDetail = response['detail'];
                $('#inv_id').val(id);
                $('#total_bill').html(formatRupiah(arrData['amount']));
                var total=0;
                var amount=arrData['amount'] == 'NaN' || arrData['amount'] == null ? 0 : arrData['amount'];
                for(i = 0; i < arrDetail.length; i++){
                    total+=parseFloat(arrDetail[i]['amount']);
                    var tdAdd='<tr>'+
                                '<td><div class="text-left">'+arrDetail[i]['created_at']+'</div></td>'+
                                '<td><div class="text-left">'+arrDetail[i]['wop']+'</div></td>'+
                                '<td><div class="text-right">'+formatRupiah(arrDetail[i]['amount'])+'</div></td>'+
                                '<td><div class="text-left">'+arrDetail[i]['name']+'</div></td>'+
                                '<td><div class="text-left">'+(arrDetail[i]['note'] != null ? arrDetail[i]['note'] : '-')+'</div></td>'+
                            '</tr>';
                    $('#detail_payment').find('tbody:last').append(tdAdd);
                }
                var ppn=(parseFloat(amount) * (1/10)).toFixed(2);
                var total_all_payment=(parseFloat(amount) + parseFloat(ppn)) - parseFloat(total);
                $('#total_all_payment').val(total_all_payment);
                $('#total_ppn').html(formatRupiah(ppn));
                var total_all=(parseFloat(amount) + parseFloat(ppn));
                $('#total_all').html(formatRupiah(parseFloat(total_all).toFixed(2)));
                if (total_all_payment == 0) {
                    $('#form_payment').hide();
                }else{
                    $('#form_payment').show();
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
function doShowDetail(id){
    t2=$('#zero_config2').DataTable();
    t2.clear().draw(false);
    $.ajax({
            type: "GET",
            url: "{{ URL::to('inventory/json_acc_detail') }}" + "/" + id, //json get site
            dataType : 'json',
            success: function(response){
                arrData = response['data'];
                for(i = 0; i < arrData.length; i++){
                    t2.row.add([
                        '<div class="text-left">'+arrData[i]['prod_no']+'</div>',
                        '<div class="text-left">'+arrData[i]['item']+' '+arrData[i]['name']+' '+arrData[i]['series']+'</div>'
                    ]).draw(false);
                }
            }
    });
}
function formatTanggal(date) {
  var temp=date.split('-');
  return temp[2] + '-' + temp[1] + '-' + temp[0];
}

function formatDate2(date){
        if (date == null) {
            return '-';
        }else{

            var myDate = new Date(date);
            var tgl=date.split(/[ -]+/);
            // var output = tgl[2] + "-" +  tgl[1] + "-" + tgl[0] + ' ' + tgl[3];
            var output = tgl[2] + "-" +  tgl[1] + "-" + tgl[0];
            return output;
        }
    }
</script>
@endsection