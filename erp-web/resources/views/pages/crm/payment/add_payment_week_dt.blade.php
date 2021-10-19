@extends('theme.default')

@section('breadcrumb')
    <div class="page-breadcrumb">
        <div class="row">
            <div class="col-5 align-self-center">
                <h4 class="page-title">Tambah Detail Pembayaran Mingguan Produksi</h4>
                <div class="d-flex align-items-center">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ URL::to('payment') }}">Detail Pembayaran Mingguan Produksi</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Tambah</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>
@endsection


@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Tambah Detail Pembayaran Mingguan Produksi</h4>
                    <table style="width:100%">
                        <thead>
                            <tr>
                                <td>Nomor Pembayaran</td>
                                <td>:</td>
                                <td>{{$paid_week['no']}}</td>
                            </tr>
                            <tr>
                                <td>Deskripsi</td>
                                <td>:</td>
                                <td>{{$paid_week['description']}}</td>
                            </tr>
                            <tr>
                                <td>Total Pembayaran</td>
                                <td>:</td>
                                <td>{{number_format($paid_week['amount'])}}</td>
                            </tr>
                            <tr>
                                <td>Tanggal Pembayaran</td>
                                <td>:</td>
                                <td>{{date('d-m-Y', strtotime($paid_week['pay_date']))}}</td>
                            </tr>
                        </thead>
                    </table>
                    <form method="POST" action="{{ URL::to('payment/save_prod_week_dt') }}" class="mt-4" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" value="{{$paid_week['id']}}" name="id">
                        <input type="hidden" value="{{$paid_week['amount']}}" name="total" id="total">
                        <div class="row">
                            <div class="col-12">
                                <div class="table-responsive">
                                    <div class="float-right">
                                        <button onclick="addDetailPayment()" type="button" class="btn btn-info pull-right">tambah</button>
                                    </div>
                                    <br><br>
                                    <table class="table table-bordered table-striped" id="detail-prod-weeks">
                                        <thead>
                                                <tr>
                                                    <th class="text-center">Nomor SPK</th>
                                                    <th class="text-center">Nomor Order</th>
                                                    <th class="text-center">Nomor Permintaan</th>
                                                    <th class="text-center">Jumlah</th>
                                                    <th class="text-center">Biaya Dalam Produksi</th>
                                                    <th class="text-center">Catatan</th>
                                                    <th></th>
                                                </tr>
                                            </thead>
                                        <tbody>
                                            
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            
                            <div class="col-sm-12">
                                <div class="form-group">
                                <br><br>
                                    <button class="btn btn-success">Simpan</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        
        </div>
    </div>
</div>

<!-- /.modal -->
<!-- <script src="{!! asset('theme/assets/libs/jquery/dist/jquery.min.js') !!}"></script> -->
<script src="{!! asset('theme/assets/libs/sweetalert2/dist/sweetalert2.all.min.js') !!}"></script>
<script>
var account_payment=[];
var account_beban=[];
$(document).ready(function() {
    $.ajax({
        // type: "post",
        url: "{{ URL::to('akuntansi/account_payment') }}",
        dataType : 'json',
        async : false,
        success: function(response){
            arrData = response;
            for(i = 0; i < arrData.length; i++){
                account_payment.push(arrData[i]);
            }
        }
    });
    $.ajax({
        // type: "post",
        url: "{{ URL::to('akuntansi/account_operasional') }}",
        dataType : 'json',
        async : false,
        success: function(response){
            arrData = response;
            for(i = 0; i < arrData.length; i++){
                if (arrData[i]['label'] != 85 && arrData[i]['label'] != 86) {
                    account_beban.push(arrData[i]);
                }
            }
        }
    });
    $.ajax({
        // type: "post",
        url: "{{ URL::to('akuntansi/account_adm') }}",
        dataType : 'json',
        async : false,
        success: function(response){
            arrData = response;
            for(i = 0; i < arrData.length; i++){
                if (arrData[i]['label'] == 111 || arrData[i]['label'] == 112) {
                    account_beban.push(arrData[i]);
                }
            }
        }
    });
});
var temp_spk_number=[];
var temp_order_id=[];
function getOrderNoBySpk(){
    var spk_number=$('[name^=spk_number]');
    var formOrderNo = $('[name^=order_id]');
    // var checkListProd = $('[name^=check_production]');
    var index_id= $('[id^=index]');
    for(i = 0; i < spk_number.length; i++){
        var spk=spk_number.eq(i).val();
        var index=index_id.eq(i).data('index');
        
        if (spk != temp_spk_number[index]) {   
            $.ajax({
                type: "GET",
                url: "{{ URL::to('home/get_order_no/') }}"+'/'+spk, //json get site
                dataType : 'json',
                async : false,
                success: function(response){
                    arrData = response['data'];
                    formOrderNo.eq(i).empty();
                    formOrderNo.eq(i).append('<option value="">-- Pilih Nomor Order --</option>');
                    for(var j = 0; j < arrData.length; j++){
                        formOrderNo.eq(i).append('<option value="'+arrData[j]['id']+'">'+arrData[j]['order_no']+'</option>');
                    }
                }
            });
            // checkListProd.eq(i).val(id);
            temp_spk_number[index]=spk;
        }
    }
}
function getOrderNo(){
    var order_id=$('[name^=order_id]');
    var formReqNo = $('[name^=req_id]');
    var checkListProd = $('[name^=check_production]');
    var index_id= $('[id^=index]');
    for(i = 0; i < order_id.length; i++){
        var id=order_id.eq(i).val();
        var index=index_id.eq(i).data('index');
        
        if (id != temp_order_id[index]) {   
            $.ajax({
                type: "GET",
                url: "{{ URL::to('home/get_req_no/') }}"+'/'+id, //json get site
                dataType : 'json',
                async : false,
                success: function(response){
                    arrData = response['data'];
                    formReqNo.eq(i).empty();
                    formReqNo.eq(i).append('<option value="">-- Pilih Nomor Permintaan --</option>');
                    for(var j = 0; j < arrData.length; j++){
                        formReqNo.eq(i).append('<option value="'+arrData[j]['id']+'">'+arrData[j]['no']+'</option>');
                    }
                }
            });
            // checkListProd.eq(i).val(id);
            temp_order_id[index]=id;
        }
    }
}
function cekTotal(){
    var order_id=$('[name^=order_id]');
    var formReqNo = $('[name^=req_id]');
    var amount = $('[name^=amount]');
    var total=$('#total').val();
    var total_all=0;
    for(i = 0; i < order_id.length; i++){
        var id=order_id.eq(i).val();
        var req_id=formReqNo.eq(i).val();
        if (id != '' && req_id != '') {
            var val_amount=amount.eq(i).val(); 
            amount.eq(i).val(formatNumber(val_amount));
            var temp_amount=val_amount.replace(/[^,\d]/g, '').toString();
            total_all+=parseFloat(temp_amount);
            if (total_all > total) {
                amount.eq(i).val('');
            }
        }
    }
}
function formatTotal(eq){
    var total=formatNumber(eq.value);
    $('#total').val(total);
}
function formatNumber(angka, prefix)
{
    var number_string = angka.replace(/[^,\d]/g, '').toString(),
        split = number_string.split(','),
        sisa  = split[0].length % 3,
        rupiah  = split[0].substr(0, sisa),
        ribuan  = split[0].substr(sisa).match(/\d{3}/gi);
        
    if (ribuan) {
        separator = sisa ? '.' : '';
        rupiah += separator + ribuan.join('.');
    }

    rupiah = split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
    return prefix == undefined ? rupiah : (rupiah ? 'Rp. ' + rupiah : '');
}
var a=0;
function addDetailPayment(){
    temp_order_id.push('');
    var select_spk='<select name="spk_number[]"  onchange="getOrderNoBySpk()" class="form-control select2 custom-select" style="width: 100%; height:32px;" required>'+
                            '<option value="">--- Select SPK Number ---</option>'+
                            '@if($spk_list != null)'+
                            '@foreach($spk_list as $value)'+
                                '@if($value['is_done'] != 1)'+
                                '@php $val = str_replace("/","|",$value['spk_number']) @endphp'+
                                '<option value="{{ $val }}">{{ $value['spk_number'] }}</option>'+
                                '@endif'+
                            '@endforeach'+
                            '@endif'+
                        '</select>';
    
    // var opt_payment='<option value="">-- Pilih Akun -- </option>';
    // for(i = 0; i < account_payment.length; i++){
    //     opt_payment+='<option value="'+account_payment[i]['label']+'">'+account_payment[i]['value']+'</option>';
    // }
    // var opt_beban='<option value="">-- Pilih Akun -- </option>';
    // for(i = 0; i < account_beban.length; i++){
    //     opt_beban+='<option value="'+account_beban[i]['label']+'">'+account_beban[i]['value']+'</option>';
    // }
    var tdAdd='<tr>'+
        '<td>'+select_spk+'</td>'+
        '<td><select name="order_id[]" onchange="getOrderNo()" id="order_id[]" class="form-control select2 custom-select" style="width: 100%; height:32px;" required></select></td>'+
        '<td><select name="req_id[]" id="req_id[]" class="form-control select2 custom-select" style="width: 100%; height:32px;" required></select></td>'+
        '<td><input id="amount[]" required name="amount[]" onkeyup="cekTotal()" class="form-control text-left" type="text" /></td>'+
        // '<td class="text-center"><input style="width:20px" type="checkbox" id="check_production[]" class="form-control" name="check_production[]" value="0"></td>'+
        '<td><select name="check_production[]" id="check_production[]" class="form-control select2 custom-select" style="width: 100%; height:32px;" required><option value="1">ya</option><option value="0">tidak</option></select></td>'+
        // '<td><select name="account_payment[]" id="account_payment[]" class="form-control select2 custom-select" style="width: 100%; height:32px;" required>'+opt_payment+'</select></td>'+
        '<td><input id="notes[]" required name="notes[]" class="form-control text-left" type="text" /></td>'+
        '<td><button type="button" class="btn btn-sm btn-danger removeOption" id="index[]" data-index="'+a+'"><i class="mdi mdi-delete"></i></button></td>'+
    '</tr>';
    $('#detail-prod-weeks').find('tbody:last').append(tdAdd);
    $('.select2').select2({});
    a++;
}
$("#detail-prod-weeks").on("click", ".removeOption", function(event) {
    var index=$(this).data('index');
    event.preventDefault();
    $(this).closest("tr").remove();
    temp_order_id[index]='';
});
</script>
@endsection