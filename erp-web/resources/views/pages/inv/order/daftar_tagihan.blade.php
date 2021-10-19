@extends('theme.default')

@section('breadcrumb')
    <div class="page-breadcrumb">
        <div class="row">
            <div class="col-5 align-self-center">
                <h4 class="page-title">Tagihan Customer</h4>
                <div class="d-flex align-items-center">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item active" aria-current="page">Daftar Tagihan Customer</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('content')
@include('pages/inv/order/interval_date_form')
@if(isset($_GET['dari']))
<div class="container-fluid">
    <!-- basic table -->
    <div class="row">
        <div class="col-12">
            <div class="text-right">
                <a href="{{ URL::to('order/bill')."?dari=$_GET[dari]&sampai=$_GET[sampai]" }}"><button class="btn btn-success btn-sm mb-2"><i class="fas fa-plus"></i>&nbsp; Buat Tagihan</button></a>
            </div>
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Daftar Tagihan Customer</h4>
                    <ul class="nav nav-tabs" role="tablist">
                        <li class="nav-item"> <a class="nav-link active" data-toggle="tab" href="#tab_order" role="tab"><span class="hidden-sm-up"><i class="mdi mdi-solid"></i></span> <span class="hidden-xs-down">Tagihan Order</span></a> </li>
                        <li class="nav-item" onclick=""> <a class="nav-link" data-toggle="tab" href="#tab_install_order" role="tab"><span class="hidden-sm-up"><i class="mdi mdi-view-grid"></i></span> <span class="hidden-xs-down">Tagihan Order Instalasi</span></a> </li>
                    </ul>
                    <div class="tab-content tabcontent-border">
                        <div class="tab-pane active" id="tab_order" role="tabpanel">
                            <br>
                            <div class="form-group float-right">
                                <a target="_blank" href="{{URL::to('order/export_bill_order')}}" class="btn btn-success"><i class="fa fa-file-excel"></i> Export</a>
                            </div>
                            <div class="table-responsive">
                                <table id="bill_list" class="table table-striped table-bordered">
                                    <thead>
                                        <tr>
                                            <th class="text-center">No</th>
                                            <th class="text-center">Nama Customer</th>
                                            <th class="text-center">Nomor Order</th>
                                            <th class="text-center">Nomor SPJB</th>
                                            <th class="text-center">Nomor Tagihan</th>
                                            <th class="text-center">Nomor Faktur</th>
                                            <th class="text-center">Deskripsi</th>
                                            <th class="text-center">Tanggal Jatuh Tempo</th>
                                            <th class="text-center">Total Tagihan</th>
                                            <th class="text-center">Alamat Tagihan</th>
                                            <th class="text-center">Status</th>
                                            <th class="text-center" style="min-width:70px"></th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                        <div class="tab-pane" id="tab_install_order" role="tabpanel">
                            <br>
                            <div class="form-group float-right">
                                <a target="_blank" href="{{URL::to('order/export_bill_install_order')}}" class="btn btn-success"><i class="fa fa-file-excel"></i> Export</a>
                            </div>
                            <div class="table-responsive">
                                <table id="bill_install_list" class="table table-striped table-bordered" style="width:100%">
                                    <thead>
                                        <tr>
                                            <th class="text-center">No</th>
                                            <th class="text-center">Nama Customer</th>
                                            <th class="text-center">Nomor Order</th>
                                            <th class="text-center">Nomor SPJB</th>
                                            <th class="text-center">Nomor SPK</th>
                                            <th class="text-center">Nomor Tagihan</th>
                                            <th class="text-center">Nomor Faktur</th>
                                            <th class="text-center">Tanggal Jatuh Tempo</th>
                                            <th class="text-center">Total Tagihan</th>
                                            <th class="text-center">Alamat Tagihan</th>
                                            <th class="text-center">Status</th>
                                            <th class="text-center" style="min-width:70px"></th>
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
<div class="modal fade bs-example-modal-lg" id="modalBillDetail" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <form action="{{URL::to('order/save_bill_detail')}}" method="post">
            @csrf
            <div class="modal-header">
                <h4 class="modal-title" id="title-modal">Pembayaran Tagihan </h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <div class="modal-body">
                <h4>Daftar Tagihan</h4>
                <table>
                    <tr>
                        <td>Total Tagihan</td>
                        <td id="bill_amount"></td>
                    </tr>
                    <!-- <tr>
                        <td>PPN(10%)</td>
                        <td id="bill_ppn"></td>
                    </tr> -->
                    <tr>
                        <td>Total Bayar</td>
                        <td id="bill_all"></td>
                    </tr>
                </table>
                <br>
                <div class="table-responsive">
                    <table class="table table-bordered" id="detail_payment">
                        <thead>
                            <tr>
                                <th class="text-center">BBM/BKM</th>
                                <th class="text-center">Tipe Pembayaran</th>
                                <th class="text-center">Bank</th>
                                <th class="text-center">Nomor Bank</th>
                                <th class="text-center">Atas Nama</th>
                                <!-- <th class="text-center">Ref Code</th> -->
                                <th class="text-center">Total</th>
                                <th class="text-center">Pay Date</th>
                                <th></th>
                            </tr>
                        </thead>
                    </table>
                </div>
                <p id="label-detail"></p>
                <input type="hidden" name="order_id" id="order_id" value="">
                <input type="hidden" id="bill_id" name="bill_id">
                <input type="hidden" id="amount_bill" name="amount_bill">
                <input type="hidden" id="total_min" name="total_min">
                <input type="hidden" id="total_awal" name="total_awal">
                <input type="hidden" id="total_ppn" name="total_ppn">
                <div class="row" hidden>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label for="">Total</label>
                            <input type="" onkeyup="checkTotalBill(this)" name="total_bill" id="total_bill" class="form-control" style="100%">
                            <input type="hidden" readonly name="paid_more" id="paid_more" class="form-control">
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label>Tipe Pembayaran</label><br>
                            <select name="wop" id="wop" class="form-control select2" style="width:100%" onchange="cekTipe(this.value)" required>
                                <option value="">-- Pilih Tipe Pembayaran --</option>
                                <option value="cash">Tunai</option>
                                <option value="giro">Giro</option>
                                <option value="bank_transfer">Transfer Bank</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-sm-6" id="card" style="display:none">
                        <div class="form-group">
                            <label for="">Nomor Giro</label>
                            <input type="" name="ref_code" id="ref_code" class="form-control" style="100%">
                        </div>
                    </div>
                    <div class="col-sm-6"  id="bank" style="display:none">
                        <div class="form-group">
                            <label>Bank</label><br>
                            <select name="id_bank" id="id_bank" class="form-control select2" style="width:100%">
                                <option value="">-- Pilih Bank --</option>
                                @foreach($list_bank as $value)
                                <option value="{{$value->id_bank}}">{{$value->bank_name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-sm-6" id="bank_no" style="display:none">
                        <div class="form-group">
                            <label for="">Nomor Rekening</label>
                            <input type="" name="bank_number" id="bank_number" class="form-control">
                        </div>
                    </div>
                    <div class="col-sm-6" id="bank_an" style="display:none">
                        <div class="form-group">
                            <label for="">Atas Nama</label>
                            <input type="" name="atas_nama" id="atas_nama" class="form-control">
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label for="">Akun Pembayaran</label>
                            <select name="account_payment" id="account_payment" class="select2 form-control" style="width:100%" required>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger waves-effect btn-sm text-left" data-dismiss="modal">Close</button>
                <button class="btn btn-success waves-effect btn-sm text-left" id="submit_bill">Simpan</button>
            </div>
            </form>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<div class="modal fade bs-example-modal-lg" id="modalBillDetail2" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <form action="{{URL::to('order/save_bill_install_detail')}}" method="post">
            @csrf
            <div class="modal-header">
                <h4 class="modal-title" id="title-modal">Pembayaran Tagihan </h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <div class="modal-body">
                <h4>Daftar Tagihan</h4>
                <table>
                    <tr>
                        <td>Total Tagihan</td>
                        <td id="bill_amount2"></td>
                    </tr>
                    <!-- <tr>
                        <td>PPN(10%)</td>
                        <td id="bill_ppn2"></td>
                    </tr>
                    <tr>
                        <td>PPH 22(2,5%)</td>
                        <td id="bill_pph2"></td>
                    </tr> -->
                    <tr>
                        <td>Total Bayar</td>
                        <td id="bill_all2"></td>
                    </tr>
                </table>
                <br>
                <div class="table-responsive">
                    <table class="table table-bordered" id="detail_payment2">
                        <thead>
                            <tr>
                                <th class="text-center">Tipe Pembayaran</th>
                                <th class="text-center">Bank</th>
                                <th class="text-center">Nomor Bank</th>
                                <th class="text-center">Atas Nama</th>
                                <!-- <th class="text-center">Ref Code</th> -->
                                <th class="text-center">Total</th>
                                <th class="text-center">Pay Date</th>
                                <th></th>
                            </tr>
                        </thead>
                    </table>
                </div>
                <p id="label-detail"></p>
                <input type="hidden" name="install_order_id" id="install_order_id">
                <input type="hidden" id="bill_id2" name="bill_id">
                <input type="hidden" id="amount_bill2" name="amount_bill">
                <input type="hidden" id="total_min2" name="total_min">
                <input type="hidden" id="total_awal2" name="total_awal">
                <input type="hidden" id="total_ppn2" name="total_ppn">
                <input type="hidden" id="total_pph2" name="total_pph">
                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label for="">Total</label>
                            <input type="" onkeyup="checkTotalBill2(this)" name="total_bill" id="total_bill2" class="form-control" style="100%">
                            <input type="hidden" readonly name="paid_more" id="paid_more2" class="form-control">
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label>Tipe Pembayaran</label><br>
                            <select name="wop" id="wop2" class="form-control select2" style="width:100%" onchange="cekTipe2(this.value)" required>
                                <option value="">-- Pilih Tipe Pembayaran --</option>
                                <option value="cash">Tunai</option>
                                <option value="giro">Giro</option>
                                <option value="bank_transfer">Transfer Bank</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-sm-6" id="card2" style="display:none">
                        <div class="form-group">
                            <label for="">Nomor Giro</label>
                            <input type="" name="ref_code" id="ref_code2" class="form-control" style="100%">
                        </div>
                    </div>
                    <div class="col-sm-6"  id="bank2" style="display:none">
                        <div class="form-group">
                            <label>Bank</label><br>
                            <select name="id_bank" id="id_bank2" class="form-control select2" style="width:100%">
                                <option value="">-- Pilih Bank --</option>
                                @foreach($list_bank as $value)
                                <option value="{{$value->id_bank}}">{{$value->bank_name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-sm-6" id="bank_no2" style="display:none">
                        <div class="form-group">
                            <label for="">Nomor Rekening</label>
                            <input type="" name="bank_number" id="bank_number2" class="form-control">
                        </div>
                    </div>
                    <div class="col-sm-6" id="bank_an2" style="display:none">
                        <div class="form-group">
                            <label for="">Atas Nama</label>
                            <input type="" name="atas_nama" id="atas_nama2" class="form-control">
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label for="">Akun Pembayaran</label>
                            <select name="account_payment" id="account_payment2" class="select2 form-control" style="width:100%" required>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger waves-effect btn-sm text-left" data-dismiss="modal">Close</button>
                <button class="btn btn-success waves-effect btn-sm text-left" id="submit_bill2">Simpan</button>
            </div>
            </form>
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
    $('#account_payment').empty();
    $('#account_payment2').empty();
    $('#account_payment').append('<option value="">-- Pilih Akun --</option>');
    $('#account_payment2').append('<option value="">-- Pilih Akun --</option>');
    $.ajax({
        // type: "post",
        url: "{{ URL::to('akuntansi/account_payment') }}",
        dataType : 'json',
        success: function(response){
            arrData = response;
            for(i = 0; i < arrData.length; i++){
                $('#account_payment').append('<option value="'+arrData[i]['label']+'">'+arrData[i]['value']+'</option>');
                $('#account_payment2').append('<option value="'+arrData[i]['label']+'">'+arrData[i]['value']+'</option>');
            }
        }
    });
    $('#bill_list').DataTable( {
        "processing": true,
        "serverSide": true,
        "ajax": {
            "url" : "{{ URL::to('order/get_bill') }}",
            "type" : "get",
            "data" : {
                "dari" : "{{$_GET['dari']}}",
                "sampai" : "{{$_GET['sampai']}}",
                },
        },
        // aaSorting: [[3, 0, 'desc']],
        "columns": [
            {"data": "no", "class" : "text-center"},
            {"data": "coorporate_name", "class" : "text-center"},
            {"data": "order_no", "class" : "text-center"},
            {"data": "spk_number", "class" : "text-center"},
            {"data": "invoice_no", "class" : "text-center"},
            {"data": "bill_no", "class" : "text-center"},
            {"data": "description", "class" : "text-center"},
            {"data": "due_date",
            "render": function(data, type, row){return row.due_date != null ? formatDateID(new Date((row.due_date).substring(0,10))) : '-'}},
            {"data": "amount", "class" : "text-right",
            "render": function(data, type, row){
                var amount=parseInt(row.amount).toString();
                return formatCurrency(amount);
            }},
            {"data": "bill_address", "class" : "text-center"},
            {"data": "is_paid", "class" : "text-center",
            "render": function(data, type, row){return row.is_paid == true ? 'Dibayar' : 'Belum Dibayar'}},
            {"data": "action", "class" : "text-center"},
        ]
    } );
    $('#bill_install_list').DataTable( {
        "processing": true,
        "serverSide": true,
        "ajax": {
            "url" : "{{ URL::to('order/get_bill_install') }}",
            "type" : "get",
            "data" : {
                "dari" : "{{$_GET['dari']}}",
                "sampai" : "{{$_GET['sampai']}}",
                },
        },
        // aaSorting: [[3, 0, 'desc']],
        "columns": [
            {"data": "no", "class" : "text-center"},
            {"data": "coorporate_name", "class" : "text-center"},
            {"data": "order_no", "class" : "text-center"},
            {"data": "spk_number", "class" : "text-center"},
            {"data": "spk_no", "class" : "text-center"},
            {"data": "invoice_no", "class" : "text-center"},
            {"data": "bill_no", "class" : "text-center"},
            {"data": "due_date",
            "render": function(data, type, row){return row.due_date != null ? formatDateID(new Date((row.due_date).substring(0,10))) : '-'}},
            {"data": "amount", "class" : "text-right",
            "render": function(data, type, row){return formatCurrency(parseInt(row.amount).toString())}},
            {"data": "bill_address", "class" : "text-center"},
            {"data": "is_paid", "class" : "text-center",
            "render": function(data, type, row){return row.is_paid == true ? 'Dibayar' : 'Belum Dibayar'}},
            {"data": "action", "class" : "text-center"},
        ]
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
function doShowDetail(eq){
    var order_id=$(eq).data('order_id');
    var total_addendum=$(eq).data('total_adendum');
    var no=$(eq).data('no');
    var id=$(eq).data('id');
    var amount=$(eq).data('amount');
    var end_payment=$(eq).data('end_payment');
    // var total_addendum=$('#total_addendum').val();
    t = $('#detail_payment').DataTable();
    t.clear().draw(false);
    var total_min=0;
    $.ajax({
            type: "GET",
            url: "{{ URL::to('order/detail_customer_bill') }}"+'/'+id, //json get site
            dataType : 'json',
            async : false,
            success: function(response){
                arrData = response['data'];
                for(i = 0; i < arrData.length; i++){
                    total_min+=parseFloat(arrData[i]['amount']);
                    t.row.add([
                        '<div class="text-left">'+arrData[i]['number']+'</div>',
                        '<div class="text-left">'+arrData[i]['wop']+'</div>',
                        '<div class="text-left">'+arrData[i]['bank_name'] != null ? arrData[i]['bank_name'] : '-'+'</div>',
                        '<div class="text-center">'+arrData[i]['bank_number'] != null ? arrData[i]['bank_number'] : '-'+'</div>',
                        '<div class="text-center">'+arrData[i]['atas_nama'] != null ? arrData[i]['atas_nama'] : '-'+'</div>',
                        '<div class="text-center">'+formatCurrency(arrData[i]['amount'])+'</div>',
                        '<div class="text-center">'+formatDateID(new Date((arrData[i]['pay_date']).substring(0,10)))+'</div>',
                        '<div class="text-center"><a href="{{URL::to('order/print_kwitansi/')}}/'+arrData[i]['id']+'" class="btn btn-success btn-sm" target="_blank"><i class="mdi mdi-printer"></i></a></div>'
                    ]).draw(false);
                }
            }
    });
    amount=(end_payment == 1 ? parseFloat(amount) + parseFloat(total_addendum) : parseFloat(amount)).toFixed(0);
    // var ppn=(parseFloat(amount) * (1/10)).toFixed(0);
    var ppn=0;
    $('#title-modal').html('Pembayaran Tagihan '+no);
    $('#bill_id').val(id);
    $('#order_id').val(order_id);
    $('#total_ppn').val(ppn);
    $('#total_awal').val(amount);
    $('#amount_bill').val(parseFloat(amount) + parseFloat(ppn));
    $('#bill_amount').html(': '+formatCurrency(parseFloat(amount)));
    $('#bill_ppn').html(': '+formatCurrency(parseFloat(ppn)));
    $('#bill_all').html(': '+formatCurrency(parseFloat(amount) + parseFloat(ppn)));
    $('#total_min').val((parseFloat(amount) + parseFloat(ppn)) - parseFloat(total_min))
}
function checkTotalBill(eq){
    var paid=(eq.value).replace(/[^,\d]/g, '').toString();
    var total_paid=paid != '' ? parseFloat(paid) : 0;
    var sub_total=$('#total_min').val();
    $('#total_bill').val(formatCurrency(paid));
    var paid_more=0;
    if (total_paid >= parseFloat(sub_total)) {
        paid_more=parseFloat(total_paid) - parseFloat(sub_total);
    }
    $('#paid_more').val(paid_more);
}
function cekTipe(val){
    if (val == 'giro') {
        $('#bank').show()
        $('#bank_no').hide()
        $('#card').show()
        $('#bank_an').hide()
    }else if(val == 'bank_transfer'){
        $('#card').hide()
        $('#bank_no').show()
        $('#bank').show()
        $('#bank_an').show()
    }else{
        $('#bank_no').hide()
        $('#bank').hide()
        $('#card').hide()
        $('#bank_an').hide()
    }
}
function doShowDetail2(eq){
    var install_order_id=$(eq).data('install_order_id');
    var total_addendum=0;
    var no=$(eq).data('no');
    var id=$(eq).data('id');
    var amount=$(eq).data('amount');
    var end_payment=$(eq).data('end_payment');
    t = $('#detail_payment2').DataTable();
    t.clear().draw(false);
    var total_min=0;
    $.ajax({
            type: "GET",
            url: "{{ URL::to('order/detail_customer_bill') }}"+'/'+id, //json get site
            dataType : 'json',
            async : false,
            success: function(response){
                arrData = response['data'];
                for(i = 0; i < arrData.length; i++){
                    total_min+=parseFloat(arrData[i]['amount']);
                    t.row.add([
                        '<div class="text-left">'+arrData[i]['wop']+'</div>',
                        '<div class="text-left">'+arrData[i]['bank_name'] != null ? arrData[i]['bank_name'] : '-'+'</div>',
                        '<div class="text-center">'+arrData[i]['bank_number'] != null ? arrData[i]['bank_number'] : '-'+'</div>',
                        '<div class="text-center">'+arrData[i]['atas_nama'] != null ? arrData[i]['atas_nama'] : '-'+'</div>',
                        '<div class="text-center">'+formatCurrency(arrData[i]['amount'])+'</div>',
                        '<div class="text-center">'+formatDateID(new Date((arrData[i]['pay_date']).substring(0,10)))+'</div>',
                        '<div class="text-center"><a href="{{URL::to('order/print_kwitansi_install/')}}/'+arrData[i]['id']+'" class="btn btn-success btn-sm" target="_blank"><i class="mdi mdi-printer"></i></a></div>'
                    ]).draw(false);
                }
            }
    });
    amount=(end_payment == 1 ? parseFloat(amount) + parseFloat(total_addendum) : parseFloat(amount)).toFixed(0);
    // var ppn=(parseFloat(amount) * (1/10)).toFixed(0);
    // var pph=(parseFloat(amount) * (2.5/100)).toFixed(0);
    var ppn=0;
    var pph=0;
    $('#title-modal2').html('Pembayaran Tagihan '+no);
    $('#bill_id2').val(id);
    $('#total_ppn2').val(ppn);
    $('#total_pph2').val(pph);
    $('#total_awal2').val(amount);
    $('#install_order_id').val(install_order_id);
    $('#amount_bill2').val(parseFloat(amount) + parseFloat(ppn) + parseFloat(pph));
    $('#bill_amount2').html(': '+formatCurrency(parseFloat(amount)));
    $('#bill_ppn2').html(': '+formatCurrency(parseFloat(ppn)));
    $('#bill_pph2').html(': '+formatRupiah(parseFloat(pph)));
    $('#bill_all2').html(': '+formatCurrency(parseFloat(amount) + parseFloat(ppn) + parseFloat(pph)));
    $('#total_min2').val((parseFloat(amount) + parseFloat(ppn) + parseFloat(pph)) - parseFloat(total_min))
}
function checkTotalBill2(eq){
    var paid=(eq.value).replace(/[^,\d]/g, '').toString();
    var total_paid=paid != '' ? parseFloat(paid) : 0;
    var sub_total=$('#total_min2').val();
    $('#total_bill2').val(formatCurrency(paid));
    var paid_more=0;
    if (total_paid >= parseFloat(sub_total)) {
        paid_more=parseFloat(total_paid) - parseFloat(sub_total);
    }
    $('#paid_more2').val(paid_more);
}
function cekTipe2(val){
    if (val == 'giro') {
        $('#bank2').show()
        $('#bank_no2').hide()
        $('#card2').show()
        $('#bank_an2').hide()
    }else if(val == 'bank_transfer'){
        $('#card2').hide()
        $('#bank_no2').show()
        $('#bank2').show()
        $('#bank_an2').show()
    }else{
        $('#bank_no2').hide()
        $('#bank2').hide()
        $('#card2').hide()
        $('#bank_an2').hide()
    }
}
function printKwitansi(eq){
    var id=$(eq).data('id')

    setTimeout(() => {
        window.open("{{ URL::to('po_konstruksi/print') }}" + "/" + id, '_blank')
    }, 500);
}
</script>
@endif
@endsection