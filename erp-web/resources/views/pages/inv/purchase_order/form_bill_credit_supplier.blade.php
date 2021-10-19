@extends('theme.default')

@section('breadcrumb')
    <div class="page-breadcrumb">
        <div class="row">
            <div class="col-5 align-self-center">
                <h4 class="page-title">Form Tagihan Supplier</h4>
                <div class="d-flex align-items-center">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ URL::to('inventory/purchase') }}">Tagihan Supplier</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Form</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>
@endsection


@section('content')
<?php
function formatNumber($val){
    return number_format($val, 0, '.', '.');
}
$total_paid=0;
$ppn=0;
if ($payment_supplier['with_ppn'] == false) {
    $ppn=$payment_supplier['amount'] * (1/10);
}
?>
@foreach($payment_supplier_ds as $value)
<?php 
$total_paid+=$value->amount;
?>
@endforeach
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Form Tagihan Supplier</h4>
                    <table style="width:100%">
                        <thead>
                            <tr>
                                <td>Nomor Tagihan</td>
                                <td>:</td>
                                <td>{{$payment_supplier['no']}}</td>
                                <td>Total Tagihan {{$payment_supplier['with_ppn'] == true ? '(Include PPN)' : ''}}</td>
                                <td>:</td>
                                <td>{{formatNumber($payment_supplier['amount'])}}</td>
                            </tr>
                            <tr>
                                <td>Supplier</td>
                                <td>:</td>
                                <td>{{$m_suppliers['name']}}</td>
                                <td>Biaya Pengiriman</td>
                                <td>:</td>
                                <td>{{formatNumber($payment_supplier['delivery_fee'])}}</td>
                            </tr>
                            <tr>
                                <td></td>
                                <td></td>
                                <td></td>
                                @if($total_paid != 0)
                                <td>Kekurangan Bayar</td>
                                <td>:</td>
                                <td>{{formatNumber($payment_supplier['amount'] - $total_paid)}}</td>
                                @else
                                <td></td>
                                <td></td>
                                <td></td>
                                @endif
                            </tr>
                            @if(count($inv_trxes) != 0)
                            <tr>
                                <td>Nomor Penerimaan</td>
                                <td>:</td>
                                <td>{{$inv_trxes['no']}}</td>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>
                            @endif
                        </thead>
                    </table>

                    @if(count($payment_supplier_ds) != 0)
                    <br>
                    <div class="table-responsive">
                        <h4 class="card-title">Riwayat Pembayaran</h4>
                        <table class="table table-bordered" id="detail-order">
                        <thead>
                                <tr>
                                    <th>Tanggal Bayar</th>
                                    <th>Pembayaran dengan</th>
                                    <th>Ref Code</th>
                                    <th>Nama Bank</th>
                                    <th>Nomor Bank</th>
                                    <th>Atas Nama</th>
                                    <th>Total</th>
                                </tr>
                            </thead>    
                            <tbody>
                                @foreach($payment_supplier_ds as $value)
                                <tr>
                                    <td>{{date("d-m-Y", strtotime($value->pay_date))}}</td>
                                    <td>{{($value->wop == 'cash' ? 'Tunai' : ($value->wop == 'card' ? 'Kartu' : 'Transfer Bank'))}}</td>
                                    <td>{{$value->ref_code != null ? $value->ref_code : '-'}}</td>
                                    <td>{{$value->bank_name != null ? $value->bank_name : '-'}}</td>
                                    <td>{{$value->bank_number != null ? $value->bank_number : '-'}}</td>
                                    <td>{{$value->atas_nama != null ? $value->atas_nama : '-'}}</td>
                                    <td>{{formatNumber($value->amount)}}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @endif
                    <form method="POST" action="{{ URL::to('inventory/save_credit_paid') }}" class="mt-4" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <input type="hidden" name="id" value="{{$payment_supplier['id']}}">
                            <input type="hidden" name="total_tagihan" value="{{round(($payment_supplier['amount'] + $ppn), 0)}}">
                            <!-- <input type="hidden" name="total_ppn" value="{{$ppn}}"> -->
                            <input type="hidden" name="with_ppn" value="{{$payment_supplier['with_ppn']}}">
                            <input type="hidden" name="total_all" value="{{$payment_supplier['amount'] - $total_paid}}">
                            <input type="hidden" name="purchase_id" value="{{$payment_supplier['purchase_id']}}">
                            <input type="hidden" name="purchase_asset_id" value="{{$payment_supplier['purchase_asset_id']}}">
                            <input type="hidden" name="purchase_service_id" value="{{$payment_supplier['purchase_service_id']}}">
                            <input type="hidden" name="inv_trx_id" value="{{$payment_supplier['inv_id']}}">
                            <input type="hidden" name="inv_trx_service_id" value="{{$payment_supplier['inv_trx_service_id']}}">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>Deskripsi</label>
                                    <textarea name="description" id="description" class="form-control"></textarea>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>Tanggal Bayar</label>
                                    <input type="date" required class="form-control" name="pay_date" id="pay_date" value="{{date('Y-m-d')}}">
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="">Total</label>
                                    <input type="text" required name="total" id="total" class="form-control" onkeyup="cekTotal(this.value)">
                                    <input type="hidden" name="paid_more" id="paid_more" value="">
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="">Total PPN</label>
                                    <input type="text" required readonly name="total_ppn" id="total_ppn" class="form-control">
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="">Biaya Pengiriman</label>
                                    <input type="text"  name="delivery_fee" id="delivery_fee" onkeyup="cekOngkir(this.value)" class="form-control">
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="">Total Pembayaran</label>
                                    <input type="text" required readonly name="total_bayar" id="total_bayar" class="form-control">
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                <label>Tipe Pembayaran</label><br>
                                    <select name="wop" id="wop" class="form-control select2" style="width:100%" onchange="cekTipe(this.value)">
                                        <option value="">-- Pilih Tipe Pembayaran --</option>
                                        <option value="cash">Tunai</option>
                                        <!-- <option value="card">Kartu</option> -->
                                        <option value="bank_transfer">Transfer Bank</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="col-sm-6" id="card"  style="display:none">
                                <div class="form-group">
                                    <label for="">Ref Code</label>
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
<script src="{!! asset('theme/assets/libs/jquery/dist/jquery.min.js') !!}"></script>
<script src="{!! asset('theme/assets/libs/sweetalert2/dist/sweetalert2.all.min.js') !!}"></script>
<script>
$(document).ready(function(){
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
$('#inv_id').change(function(){
    var po_id=$('#purchase_id').val();
    var po_asset_id=$('#purchase_asset_id').val();
    var inv_id=$('#inv_id').val();
    if (po_asset_id == null) {
        $.ajax({
            type: "GET",
            url: "{{ URL::to('inventory/get_total_inv/') }}"+'/'+inv_id+'/'+po_id, //json get site
            dataType : 'json',
            success: function(response){
                arrData = response;
                $('#total').val(response)
            }
        });   
    }else{
        $.ajax({
            type: "GET",
            url: "{{ URL::to('inventory/get_total_inv_asset/') }}"+'/'+inv_id+'/'+po_asset_id, //json get site
            dataType : 'json',
            success: function(response){
                arrData = response;
                $('#total').val(response)
            }
        });   
    }
});
function cekTotal(val){
    var total_tagihan=$('[name^=total_tagihan]').val();
    var total_all=$('[name^=total_all]').val();
    var with_ppn='{{$payment_supplier['with_ppn']}}';
    $('#total').val(formatNumber(val));
    var val=(val).replace(/[^,\d]/g, '').toString();
    var ppn=0, paid_more=0;
    if (with_ppn == 1) {
        
    }else if (parseFloat(val) > parseFloat(total_all)) {
        ppn=(total_all*0.1)
    }else{
        ppn=(val*0.1)
    }
    if (parseFloat(val) > parseFloat(total_all)) {
        paid_more=val - total_all;
        
    }
    $('#total_ppn').val(formatNumber(ppn.toString()));
    $('#paid_more').val(paid_more);
    cekTotalBayar();
}
function cekOngkir(val){
    $('#delivery_fee').val(formatNumber(val));
    cekTotalBayar();
}
function cekTotalBayar(){
    var total=($('[id^=total]').val()).replace(/[^,\d]/g, '').toString();
    var total_ppn=($('[id^=total_ppn]').val()).replace(/[^,\d]/g, '').toString();
    var delivery_fee=($('[id^=delivery_fee]').val()).replace(/[^,\d]/g, '').toString();
    var total_all=(parseFloat(total)+parseFloat(total_ppn)+parseFloat((delivery_fee != '' ? delivery_fee : 0))).toFixed(0)
    console.log(total_all)
    $('#total_bayar').val(formatNumber(total_all));
}
</script>

@endsection