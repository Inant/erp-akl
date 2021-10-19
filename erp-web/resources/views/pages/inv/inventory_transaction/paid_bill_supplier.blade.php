@extends('theme.default')

@section('breadcrumb')
    <div class="page-breadcrumb">
        <div class="row">
            <div class="col-5 align-self-center">
                <h4 class="page-title">Tambah Pembayaran Supplier</h4>
                <div class="d-flex align-items-center">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ URL::to('/') }}">Pembayaran Supplier</a></li>
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
                    <h4 class="card-title">Tambah Pembayaran Supplier</h4>
                    <form method="POST" action="{{ URL::to('inventory/save_paid_supplier') }}" class="mt-4" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col-md-6 col-sm-12"  @if(auth()->user()->role_id != 9) hidden @endif>
                                <div class="form-group">
                                    <label>Nomor BKK/BBK</label>
                                    <input type="text" id="bkk" name="bkk" class="form-control">
                                </div>
                            </div>
                              <div class="col-md-6 col-sm-12"  @if(auth()->user()->role_id != 1) hidden @endif>
                                <div class="form-group">
                                    <label>Nomor BKK/BBK</label>
                                    <input type="text" id="bkk" name="bkk" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-6 col-sm-12">
                                <div class="form-group">
                                    <label>Nama Pemasok</label>
                                    <select id="supplier_id" name="supplier_id" required class="form-control select2 custom-select" style="width: 100%; height:32px;" onchange="getBillSupplier(this.value)">
                                        <option value="">--- Pilih Pemasok ---</option>
                                        @foreach($suppliers as $value)
                                        <option value="{{$value['id']}}">{{$value['name']}}</option>
                                        @endforeach
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
                                                <th>No Tagihan</th>
                                                <th>No Invoice</th>
                                                <!-- <th>Deskripsi</th> -->
                                                <th>Catatan</th>
                                                <!-- <th>Amount</th>
                                                <th>PPN</th> -->
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
                                                <th>No PO</th> -->
                                                <!-- <th>Deskripsi</th> -->
                                                <!-- <th>Catatan</th>
                                                <th>Amount</th>
                                                <th>PPN</th>
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
                            <!-- <div class="col-md-6 col-sm-12">
                                <div class="form-group">
                                    <label>Total Bayar</label>
                                    <input type="text" id="total_bayar" name="total_bayar" class="form-control" readonly>
                                </div>
                            </div> -->
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
function getBillSupplier(val){
    // $('[name^=bill_no]').empty();
    // $('#detail_bill > tbody').empty()
    $('#list_bill > tbody').empty()
    $.ajax({
        url: "{{ URL::to('inventory/get_bill_supplier') }}"+'/'+val,
        dataType : 'json',
        success: function(response){
            arrData = response['data'];
            for (let i = 0; i < arrData.length; i++) {
                // $('[name^=bill_no]').append('<option value="'+arrData[i]['id']+'">'+arrData[i]['no']+'</option>')
                amount=parseFloat(arrData[i]['amount']).toFixed(0);
                total_without_ppn = parseFloat(arrData[i]['total_without_ppn']).toFixed(0);
                // ppn = parseFloat(arrData[i]['ppn']).toFixed(0);
                without_ppn = arrData[i]['without_ppn'];
                data=arrData[i]['detail'];
                paid=parseFloat(arrData[i]['paid']).toFixed(0);
                total=parseFloat(amount) - parseFloat(paid);
                $('#list_bill').find('tbody:last').append(
                    '<tr>'+
                        '<td><input type="checkbox" name="check_id[]" id="check_id[]" value="'+arrData[i]['id']+'" onclick="cekTotalPaid()">'+
                        '<input type="hidden" name="bill_id[]" id="bill_id[]" value="'+arrData[i]['id']+'"></td>'+
                        '<td>'+data['no']+'</td>'+
                        '<td>'+data['bill_no']+'</td>'+
                        '<td>'+data['paid_no']+'<input class="form-control" id="paid_no[]" name="paid_no[]" value="'+data['paid_no']+'"><input type="hidden" id="id[]" name="id[]" value="'+data['id']+'"><input type="hidden" id="without_ppn[]" name="without_ppn[]" value="'+without_ppn+'"></td>'+
                        // '<td>'+(data['purchase_no'] != null ? data['purchase_no'] : (data['purchase_asset_no'] != null ? data['purchase_asset_no'] : data['purchase_service_no']))+'</td>'+
                        '<td>'+(data['p_notes'] != null ? data['p_notes'] : (data['pa_notes'] != null ? data['pa_notes'] : (data['ps_notes'] != null ? data['ps_notes'] : '-')))+'</td>'+
                        '<td>'+formatNumber(total_without_ppn.toString())+'</td>'+
                        // '<td>'+formatNumber(ppn.toString())+'</td>'+
                        // '<td>'+formatNumber(amount.toString())+'</td>'+
                        '<td><input type="hidden" name="amount[]" id="amount[]" value="'+total+'">'+formatNumber(total.toString())+'</td>'+
                    '</tr>'
                );
            }
        }
    });
    // cekTotal()
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

    if(total == check_id.length){
        $('#select-all').prop('checked', true);
    }else{
        $('#select-all').prop('checked', false);
    }
    console.log(total_paid)
    $('#total').val(formatNumber(total_paid.toString()))
    $('#total_all').val(total_paid)
    checkTotal()
}
// function cekTotal(){
//     var bill_no=$('[name^=bill_no]').val()
//     var total=0, subtotal=0;
//     $('#detail_bill > tbody').empty()
//     for (let i = 0; i < bill_no.length; i++) {
//         var id=bill_no[i];
//         $.ajax({
//             url: "{{ URL::to('inventory/get_bill_d_supplier') }}"+'/'+id,
//             dataType : 'json',
//             async : false,
//             success: function(response){
//                 amount = response['total'];
//                 total_without_ppn = parseFloat(response['total_without_ppn']).toFixed(0);
//                 ppn = parseFloat(response['ppn']).toFixed(0);
//                 without_ppn = response['without_ppn'];
//                 total=parseFloat(amount).toFixed(0);
//                 arrData=response['data'];
//                 $('#detail_bill').find('tbody:last').append(
//                     '<tr>'+
//                         '<td>'+arrData['no']+'</td>'+
//                         '<td>'+arrData['paid_no']+'<input class="form-control" id="paid_no[]" name="paid_no[]" value="'+arrData['paid_no']+'"><input type="hidden" id="id[]" name="id[]" value="'+arrData['id']+'"><input id="amount[]" name="amount[]" value="'+total+'" type="hidden"><input type="hidden" id="without_ppn[]" name="without_ppn[]" value="'+without_ppn+'"></td>'+
//                         '<td>'+(arrData['purchase_no'] != null ? arrData['purchase_no'] : (arrData['purchase_asset_no'] != null ? arrData['purchase_asset_no'] : arrData['purchase_service_no']))+'</td>'+
//                         '<td>'+(arrData['p_notes'] != null ? arrData['p_notes'] : (arrData['pa_notes'] != null ? arrData['pa_notes'] : (arrData['ps_notes'] != null ? arrData['ps_notes'] : '-')))+'</td>'+
//                         '<td>'+formatNumber(total_without_ppn.toString())+'</td>'+
//                         '<td>'+formatNumber(ppn.toString())+'</td>'+
//                     '</tr>'
//                 );
//             }
//         });
//         subtotal+=parseFloat(total);
//     }
//     $('#total').val(formatNumber(subtotal.toString()))
//     $('#total_all').val(subtotal)
//     checkTotal()
// }
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
function checkTotal(){
    var id=$('[name^=id]');
    var amount=$('[name^=amount]');
    var without_ppn=$('[name^=without_ppn]');
    var total_without_ppn=0;
    for (let i = 0; i < id.length; i++) {
        var is_with_ppn=without_ppn.eq(i).val();
        var amount_in=amount.eq(i).val();
        if (is_with_ppn == 0) {
            total_without_ppn+=parseFloat(amount_in);
        }
    }
    
    var payment=$('#total').val();
    var paid=(payment).replace(/[^,\d]/g, '').toString();
    var total_paid=paid != '' ? parseFloat(paid) : 0;
    var total_all=$('#total_all').val();
    var sub_total=parseFloat(total_all).toFixed(0);
    
    $('#total').val(formatCurrency(paid));
    var paid_more=0, paid_less=0, ppn=0;
    if (total_paid >= sub_total) {
        paid_more=parseFloat(total_paid) - sub_total;
        ppn=(total_without_ppn * 0.1).toFixed(0);
    }else{
        paid_less=sub_total - parseFloat(total_paid);
        ppn=(total_without_ppn * 0.1).toFixed(0);
    }
    
    $('#paid_more').val(paid_more);
    $('#paid_less').val(paid_less);
    // $('#ppn').val(formatCurrency(ppn))
    // total_bayar=(total_paid+parseFloat(ppn));
    // $('#total_bayar').val(formatCurrency(total_bayar))
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