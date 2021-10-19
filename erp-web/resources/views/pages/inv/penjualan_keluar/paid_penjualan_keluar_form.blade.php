@extends('theme.default')

@section('breadcrumb')
    <div class="page-breadcrumb">
        <div class="row">
            <div class="col-5 align-self-center">
                <h4 class="page-title">Tambah Pembayaran Penjualan Material</h4>
                <div class="d-flex align-items-center">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ URL::to('order') }}">Pembayaran Penjualan Material</a></li>
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
                    <h4 class="card-title">Tambah Pembayaran Penjualan Material</h4>
                    <form method="POST" action="{{ URL::to('penjualan_keluar/save_bill') }}" class="mt-4" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col-md-6 col-sm-12"  @if(auth()->user()->role_id != 1) hidden @endif>
                                <div class="form-group">
                                    <label>Nomor BKM/BBM</label>
                                    <input type="text" id="bkm" name="bkm" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-6 col-sm-12">
                                <div class="form-group">
                                    <label>Customer</label>
                                    <select id="customer_id" name="customer_id" onchange="getBill(this.value)" class="form-control select2 custom-select" style="width: 100%; height:32px;">
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="table-responsive">
                                    <table class="table table-bordered" id="list_bill">
                                        <thead>
                                            <tr>
                                                <th><input type="checkbox" name="select-all" id="select-all" /></th>
                                                <th>No</th>
                                                <th>Tanggal Permintaan</th>
                                                <th>Total</th>
                                                <th>Kekurangan Bayar</th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- <div class="col-md-12 col-sm-12">
                                <div class="form-group">
                                    <label>Pilih Tagihan</label>
                                    <select class="select2 form-control" multiple="multiple" style="height: 36px; width: 100%;" id="bill_no[]" name="bill_no[]" onchange="cekTotal()">
                                        
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="table-responsive">
                                    <table class="table table-bordered" id="detail_bill">
                                        <thead>
                                            <tr>
                                                <th>No SPK</th>
                                                <th>No Tagihan</th>
                                                <th>Deskripsi</th>
                                                <th>Catatan</th>
                                                <th>Amount</th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                </div>
                            </div> -->
                            <div class="col-md-6 col-sm-12">
                                <div class="form-group">
                                    <label>Total Tagihan</label>
                                    <input type="text" id="total" name="total" class="form-control" onkeyup="checkTotal()">
                                    <input type="hidden" id="total_all" name="total_all">
                                    <input type="hidden" id="paid_more" name="paid_more">
                                    <input type="hidden" id="paid_less" name="paid_less">
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>Tipe Pembayaran</label><br>
                                    <select name="wop" id="wop" class="form-control select2" style="width:100%" onchange="cekTipe(this.value)" required>
                                        <option value="">-- Pilih Tipe Pembayaran --</option>
                                        <option value="cash">Tunai</option>
                                        <!-- <option value="giro">Giro</option> -->
                                        <option value="bank_transfer">Transfer Bank</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="">Tanggal Transaksi</label>
                                    <input type="date" name="pay_date" id="pay_date" required class="form-control" value="{{date('Y-m-d')}}">
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="">Catatan</label>
                                    <textarea name="notes" id="notes" class="form-control"></textarea>
                                </div>
                            </div>
                            <div class="col-sm-6" id="card" style="display:none">
                                <div class="form-group">
                                    <label for="">No Giro</label>
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
                            <div class="col-sm-6" id="akun">
                                <div class="form-group">
                                    <label for="">Akun Pembayaran</label>
                                    <select name="account_payment" id="account_payment" class="select2 form-control" style="width:100%" required>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <button type="submit" id="submit" class="btn btn-primary" >Submit</button>
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
});
$.ajax({
    type: "GET",
    url: "{{ URL::to('customer/json') }}", //json get material
    dataType : 'json',
    async : true,
    success: function(response){
        customer = response['data'];
        $('#customer_id').append('<option selected value="">Pilih Customer</option>')
        $.each(customer, function(i, item) {
            $('#customer_id').append('<option value="'+customer[i]['id']+'">'+customer[i]['coorporate_name']+'</option>');
        });
    }
});
function getBill(val){
    // $('[name^=bill_no]').empty();
    $('#list_bill > tbody').empty()
    $.ajax({
        url: "{{ URL::to('penjualan_keluar/get_bill') }}"+'/'+val,
        dataType : 'json',
        success: function(response){
            arrData = response['data'];
            for (let i = 0; i < arrData.length; i++) {
                amount=parseInt(arrData[i]['total']).toString();
                paid=parseFloat(arrData[i]['paid_amount']).toFixed(0);
                less_paid=parseFloat(amount) - parseFloat(paid);

                // $('[name^=bill_no]').append('<option value="'+arrData[i]['id']+'">'+arrData[i]['no']+' | '+arrData[i]['total']+'</option>');
                $('#list_bill').find('tbody:last').append(
                    '<tr>'+
                        '<td><input type="checkbox" name="check_id[]" id="check_id[]" value="'+arrData[i]['id']+'" onclick="cekTotalPaid()">'+
                        '<input type="hidden" name="bill_id[]" id="bill_id[]" value="'+arrData[i]['id']+'"></td>'+
                        '<td>'+arrData[i]['no']+'</td>'+
                        '<td>'+formatDateID(new Date((arrData[i]['create_date'])))+'</td>'+
                        '<td>'+formatNumber(amount)+'</td>'+
                        '<td><input type="hidden" name="amount[]" id="amount[]" value="'+parseFloat(less_paid)+'">'+formatNumber(less_paid.toString())+'</td>'+
                    '</tr>'
                );
            }
        }
    });
}
$('#select-all').click(function(event) {   
    if(this.checked) {
        // Iterate each checkbox
        $('[id^=check_id]').each(function() {
            this.checked = true;        
            cekTotalPaid();                
        });
    } else {
        $('[id^=check_id]').each(function() {
            this.checked = false;                       
            cekTotalPaid();
        });
    }
});
function cekTotalPaid(){
    var check_id=$('[name^=check_id]');
    var amount = $('[name^=amount]');
    var total_paid=0, total=0;
    for(i = 0; i < check_id.length; i++){
        if(check_id.eq(i).prop('checked') === true){
            total++;
            total_paid+=parseFloat(amount.eq(i).val());
        }
    }
    var total_pph=0;
    if(total == check_id.length){
        $('#select-all').prop('checked', true);
    }else{
        $('#select-all').prop('checked', false);
    }
    $('#total').val(formatNumber(total_paid.toString()))
    $('#total_all').val(total_paid)
    checkTotal()
}
function checkTotal(){
    var payment=$('#total').val();
    var paid=(payment).replace(/[^,\d]/g, '').toString();
    var total_paid=paid != '' ? parseFloat(paid) : 0;
    var total_all=$('#total_all').val();
    var sub_total=parseFloat(total_all).toFixed(0);
    
    $('#total').val(formatCurrency(paid));
    var paid_more=0, paid_less=0, ppn=0;
    if (total_paid >= sub_total) {
        paid_more=parseFloat(total_paid) - sub_total;
    }else{
        paid_less=sub_total - parseFloat(total_paid);
    }
    
    $('#paid_more').val(paid_more);
    $('#paid_less').val(paid_less);
}
function cekTipe(val){
    if (val == 'giro') {
        $('#bank').show()
        $('#bank_no').hide()
        $('#card').show()
        $('#bank_an').hide()
        $('#akun').hide()
        $('#account_payment').prop('required', false)
    }else if(val == 'bank_transfer'){
        $('#card').hide()
        $('#bank_no').show()
        $('#bank').show()
        $('#bank_an').show()
        $('#akun').show()
        $('#account_payment').prop('required', true)
    }else{
        $('#bank_no').hide()
        $('#bank').hide()
        $('#card').hide()
        $('#bank_an').hide()
        $('#akun').show()
        $('#account_payment').prop('required', true)
    }
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
</script>
@endsection