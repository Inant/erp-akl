@extends('theme.default')

@section('breadcrumb')
    <div class="page-breadcrumb">
        <div class="row">
            <div class="col-5 align-self-center">
                <h4 class="page-title">Tambah Tagihan Pengadaan/Pemasangan</h4>
                <div class="d-flex align-items-center">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ URL::to('payment') }}">Tagihan Pengadaan/Pemasangan</a></li>
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
                    <h4 class="card-title">Tambah Tagihan Pengadaan/Pemasangan</h4>
                    <form method="POST" action="{{ URL::to('payment/save_bill_vendor') }}" class="mt-4" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>Nomor Order</label>
                                    <select name="order_id" id="order_id" class="form-control select2 custom-select" style="width: 100%; height:32px;" onchange="checkedOrder(this.value)">
                                        <option value="">--- Pilih Nomor Order ---</option>
                                        @if($order != null)
                                        @foreach($order as $value)
                                            <option value="{{ $value['id'] }}">{{ $value['order_no'] .' ('. $value['spk_number'].')' }}</option>
                                        @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>Nomor Order Instalasi</label>
                                    <select name="install_order_id" id="install_order_id" class="form-control select2 custom-select" style="width: 100%; height:32px;" onchange="checkedInstallOrder(this.value)">
                                        <option value="">--- Pilih Nomor Order Instalasi ---</option>
                                        @if($install_order != null)
                                        @foreach($install_order as $value)
                                            <option value="{{ $value['id'] }}">{{ $value['no']  .' ('. $value['spk_number'].')' }}</option>
                                        @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>Supplier</label>
                                    <select class="select2 form-control" style="height: 36px; width: 100%;" id="suppl_single" name="suppl_single" required>
                                        @foreach($suppliers as $value)
                                        <option value="{{$value['id']}}">{{$value['name']}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>No Tagihan</label>
                                    <input type="text"  class="form-control" name="bill_no" required>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>Tanggal</label>
                                    <input type="date"  class="form-control" name="date_create" id="date_create" required value="{{date('Y-m-d')}}">
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>Tanggal Jatuh Tempo</label>
                                    <input type="date"  class="form-control" name="due_date" id="due_date" required value="{{date('Y-m-d')}}">
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>Deskripsi</label>
                                    <textarea name="description" id="description" class="form-control" required></textarea>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="">DPP</label>
                                    <input type="text" name="total" id="total" class="form-control"  onkeyup="cekTotal(this)" required>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="">PPN</label>
                                    <input readonly type="" name="ppn_bill" id="ppn_bill" class="form-control">
                                </div>
                            </div>
                            <div class="col-sm-5">
                                <div class="form-group">
                                    <label for="">PPH</label>
                                    <input readonly type="" name="pph_bill" id="pph_bill" class="form-control">
                                </div>
                            </div>
                            <div class="col-sm-1">
                                <label for="">With PPH</label><br>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" id="with_pph" name="with_pph" checked value="1" onclick="cekPPH()">
                                    <!-- <label class="form-check-label" for="inlineCheckbox1">With PPH</label> -->
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="">Total Tagihan</label>
                                    <input readonly type="" name="bill_amount" id="bill_amount" class="form-control">
                                </div>
                            </div>
                            <br>
                            <div class="col-sm-12">
                                <button type="submit" id="submit" class="btn btn-primary">Simpan</button>
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
$(document).ready(function() {
    $('#account_payment').empty();
    $('#account_payment').append('<option value="">-- Pilih Akun --</option>');
    $.ajax({
        // type: "post",
        url: "{{ URL::to('akuntansi/account_payment') }}",
        dataType : 'json',
        success: function(response){
            arrData = response;
            for(i = 0; i < arrData.length; i++){
                $('#account_payment').append('<option value="'+arrData[i]['label']+'">'+arrData[i]['value']+'</option>');
            }
        }
    });
    // $('#account_beban').empty();
    // $('#account_beban').append('<option value="">-- Pilih Akun --</option>');
    // $.ajax({
    //     // type: "post",
    //     url: "{{ URL::to('akuntansi/account_adm') }}",
    //     dataType : 'json',
    //     success: function(response){
    //         arrData = response;
    //         for(i = 0; i < arrData.length; i++){
    //             if (arrData[i]['label'] == 111 || arrData[i]['label'] == 112) {
    //                 $('#account_beban').append('<option value="'+arrData[i]['label']+'">'+arrData[i]['value']+'</option>');
    //             }
    //         }
    //     }
    // });
});
function getOrderNo(order_id){
    formReqNo = $('[id^=req_id]');
    formReqNo.empty();
    formReqNo.append('<option value="">-- Pilih Nomor Permintaan --</option>');
    $.ajax({
        type: "GET",
        url: "{{ URL::to('home/get_req_no/') }}"+'/'+order_id, //json get site
        dataType : 'json',
        success: function(response){
            arrData = response['data'];
            for(i = 0; i < arrData.length; i++){
                formReqNo.append('<option value="'+arrData[i]['id']+'">'+arrData[i]['no']+'</option>');
            }
        }
    });
}
function cekTipe(val){
    if (val == 'card') {
        $('#bank').hide()
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
function cekTotal(eq){
    var paid=(eq.value).replace(/[^,\d]/g, '').toString();
    var total_paid=paid != '' ? parseFloat(paid) : 0;
    // var total_tagihan=$('#total_tagihan').val();
    // var total_all=$('#total_all').val();
    var sub_total=parseFloat($('#sub_total').val()).toFixed(0);
    // var countPersen=parseFloat(total_tagihan) * (parseFloat(persen)/ 100);

    if (total_paid > parseFloat(sub_total)) {
        $('#total').val(0);
        $('#ppn_bill').val(0);
        $('#pph_bill').val(0);
        $('#bill_amount').val(0);
    }else{
        $('#total').val(formatNumber(paid));
        cekPPH()
    }
}
function cekPPH(){
    paid=($('#total').val()).replace(/[^,\d]/g, '').toString();
    ppn=(parseFloat(paid)*0.1).toFixed(0);
    $('#ppn_bill').val(formatNumber(ppn.toString()))
    if ($('#with_pph').is(':checked')) {
        pph=(parseFloat(paid)*0.02).toFixed(0);
        $('#pph_bill').val(formatNumber(pph))
    }else{
        pph=0;
        $('#pph_bill').val(0)
    }
    bill_amount=parseFloat(paid) + (parseFloat(ppn) - parseFloat(pph));
    $('#bill_amount').val(formatNumber(bill_amount.toString()))
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
$("#detail_work").on("click", ".removeOption", function(event) {
    event.preventDefault();
    $(this).closest("tr").remove();
});
function checkedOrder(val){
    if(val != ''){
        $('#install_order_id').val('').trigger('change');
    }
}
function checkedInstallOrder(val){
    if(val != ''){
        $('#order_id').val('').trigger('change');
    }
}
</script>
@endsection