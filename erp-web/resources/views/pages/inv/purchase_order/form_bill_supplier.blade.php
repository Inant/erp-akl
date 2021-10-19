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
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Form Tagihan Supplier</h4>
                    <form method="POST" action="{{ URL::to('inventory/save_bill_supplier') }}" class="mt-4" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col-sm-6" id="sj_item">
                                <div class="form-group">
                                    <label>Nomor Surat Jalan</label>
                                    <select name="no_surat_jalan" id="no_surat_jalan" class="form-control select2 custom-select" style="width: 100%; height:32px;">
                                    </select>
                                    
                                </div>
                            </div>
                            <div class="col-sm-6" id="sj_jasa" style="display:none">
                                <div class="form-group">
                                    <label>Nomor Surat Jalan</label>
                                    <select name="no_surat_jalan_jasa" id="no_surat_jalan_jasa" class="form-control select2 custom-select" style="width: 100%; height:32px;">
                                    </select>
                                    
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <label for="">Pembayaran Untuk : </label><br>
                                <div class="form-check form-check-inline">
                                    <div class="custom-control custom-radio">
                                        <input type="radio" class="custom-control-input" id="customControlValidation3" name="type_po" value="0"  onclick="changePO(this.value)" checked>
                                        <label class="custom-control-label" for="customControlValidation3">Pembayaran Material</label>
                                    </div>
                                </div>
                                <div class="form-check form-check-inline">
                                    <div class="custom-control custom-radio">
                                        <input type="radio" class="custom-control-input" id="customControlValidation4" name="type_po" value="2"  onclick="changePO(this.value)">
                                        <label class="custom-control-label" for="customControlValidation4">Pembayaran Jasa</label>
                                    </div>
                                </div>
                            </div>
                            <!-- <div class="col-sm-6" style="display:none" id="inv_trx">
                                <div class="form-group">
                                    <label>Nomor Penerimaan</label>
                                    <select name="inv_id" id="inv_id" class="form-control select2 custom-select" style="width: 100%; height:32px;">
                                    <option value="">-- Pilih No Penerimaan --</option>
                                    </select>
                                    
                                </div>
                            </div>
                            <div class="col-sm-6" style="display:none" id="inv_trx_service">
                                <div class="form-group">
                                    <label>Nomor Penerimaan</label>
                                    <select name="inv_trx_service_id" id="inv_trx_service_id" class="form-control select2 custom-select" style="width: 100%; height:32px;">
                                    <option value="">-- Pilih No Penerimaan --</option>
                                    </select>
                                    
                                </div>
                            </div> -->
                            <!-- <div class="col-sm-6">
                                <div class="form-group">
                                    <label>Pembayaran Untuk</label>
                                    <select id="trx_type" name="trx_type" class="form-control select2 custom-select" style="width: 100%; height:32px;" required>
                                        <option value="PAY_OTHER">Pembayaran </option>
                                    </select>
                                </div>
                            </div> -->
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="">Nomor Invoice</label>
                                    <input type="" name="paid_no" id="paid_no" required class="form-control">
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="">Nomor Tagihan</label>
                                    <input type="" name="bill_no" id="bill_no" required class="form-control">
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>Deskripsi</label>
                                    <textarea name="description" id="description" class="form-control"></textarea>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="">Tanggal Pembuatan Tagihan</label>
                                    <input type="date" name="date_create" id="date_create" value="{{date('Y-m-d')}}" required class="form-control">
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>Due Date</label>
                                    <input type="date"  class="form-control" name="due_date" id="due_date" required>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="row">
                                    <div class="col-6">
                                        <div class="form-group">
                                            <label for="">Total By System</label>
                                            <input type="text" readonly name="total" id="total" class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="form-group">
                                            <label for="">Total Tagihan Supplier</label>
                                            <input type="number" name="amount_tagihan" id="amount_tagihan" class="form-control" required>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="">Biaya Pengiriman</label>
                                    <input type="number" onkeyup="cekOngkir(this.value)" name="delivery_fee" id="delivery_fee" class="form-control">
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="">Supplier</label>
                                    <input type="" readonly id="supplier_name" class="form-control">
                                </div>
                            </div>
                            <input type="hidden" id="m_supplier_id" name="m_supplier_id">
                            <input type="hidden" id="payment_po" name="payment_po">
                            <input type="hidden" id="total_purchase" name="total_purchase">
                            <br>
                            <div class="col-sm-12">
                                <button type="submit" id="submit" class="btn btn-primary">Simpan</button>
                            </div>
                        </div>
                    </form>
                    <br>
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped" id="detail_item">
                            <thead>
                                <tr>
                                    <th class="text-center"></th>
                                    <th class="text-center">Kode Item</th>
                                    <th class="text-center">Nama Item</th>
                                    <th class="text-center">Total Item</th>
                                    <th class="text-center">Harga</th>
                                    <th class="text-center">PPN</th>
                                    <th class="text-center">Sub Total</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="6">Total</td>
                                    <td class="text-right"><p id="total_all"></p></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        
        </div>
    </div>
</div>

<!-- /.modal -->
<script src="{!! asset('theme/assets/libs/jquery/dist/jquery.min.js') !!}"></script>
<script src="{!! asset('theme/assets/libs/sweetalert2/dist/sweetalert2.all.min.js') !!}"></script>
<script>
var delivery_fee=0;
$(document).ready(function(){
    var temp=[]
    $('#no_surat_jalan').select2({
        ajax : {
            url : '{{URL::to('inventory/get_surat_jalan')}}',
            delay: 200,
            dataType : 'json',
            processResults: function (data) {
                temp=data;
                return {
                results:  $.map(data, function (item) {
                    return {
                    text: item.text,
                    id: item.text
                    }
                })
                };
            },
            cache: true
        }
    });
    $('#no_surat_jalan').change(function(){
        var id=$('#no_surat_jalan').val();
        $('#detail_item > tbody').empty();
        $.each(temp, function(i, obj){
            if (obj.text == id) {
                typeItem(obj, 'item')
            }
        })
    });
    $('#no_surat_jalan_jasa').select2({
        ajax : {
            url : '{{URL::to('inventory/get_surat_jalan_jasa')}}',
            delay: 200,
            dataType : 'json',
            processResults: function (data) {
                temp=data;
                return {
                results:  $.map(data, function (item) {
                    return {
                    text: item.text,
                    id: item.text
                    }
                })
                };
            },
            cache: true
        }
    });
    $('#no_surat_jalan_jasa').change(function(){
        var id=$('#no_surat_jalan_jasa').val();
        $.each(temp, function(i, obj){
            if (obj.text == id) {
                typeItem(obj, 'service')
            }
        })
    });
    $('#purchase_id').select2({
        ajax : {
            url : '{{URL::to('inventory/get_po')}}',
            delay: 200,
            dataType : 'json',
            processResults: function (data) {
                temp=data;
                return {
                results:  $.map(data, function (item) {
                    return {
                    text: item.text,
                    id: item.id
                    }
                })
                };
            },
            cache: true
        }
        // placeholder: "Select Product",
        // initSelection: function(element, callback) {                   
        // }
    });
    $('#purchase_id').change(function(){
        var id=$('#purchase_id').val();
        $.each(temp, function(i, obj){
            if (obj.id == id) {
                typePurchase(obj, 'purchase')
            }
        })
    });

    $('#purchase_asset_id').select2({
        ajax : {
            url : '{{URL::to('inventory/get_po_asset')}}',
            delay: 200,
            dataType : 'json',
            processResults: function (data) {
                temp=data;
                return {
                results:  $.map(data, function (item) {
                    return {
                    text: item.text,
                    id: item.id
                    }
                })
                };
            },
            cache: true
        }
        // placeholder: "Select Product",
        // initSelection: function(element, callback) {                   
        // }
    });
    $('#purchase_asset_id').change(function(){
        var id=$('#purchase_asset_id').val();
        $.each(temp, function(i, obj){
            if (obj.id == id) {
                typePurchase(obj, 'purchase_asset')
            }
        })
    });
    $('#purchase_service_id').select2({
        ajax : {
            url : '{{URL::to('inventory/get_po_service')}}',
            delay: 200,
            dataType : 'json',
            processResults: function (data) {
                temp=data;
                return {
                results:  $.map(data, function (item) {
                    return {
                    text: item.text,
                    id: item.id
                    }
                })
                };
            },
            cache: true
        }
        // placeholder: "Select Product",
        // initSelection: function(element, callback) {                   
        // }
    });
    $('#purchase_service_id').change(function(){
        var id=$('#purchase_service_id').val();
        $.each(temp, function(i, obj){
            if (obj.id == id) {
                typePurchaseService(obj)
            }
        })
    });
});
function typeItem(data, type){
    CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');     
    if(type == 'item'){
        $.ajax({
            type: "post",
            url: "{{ URL::to('inventory/get_detail_surat_jalan') }}", //json get site
            data : {_token : CSRF_TOKEN, no : data['text']},
            dataType : 'json',
            async : false,
            success: function(response){
                arrData = response;
            }
        });
    }else{
        $.ajax({
            type: "post",
            url: "{{ URL::to('inventory/get_detail_surat_jalan_jasa') }}"+'?no='+data['text'], //json get site
            data : {_token : CSRF_TOKEN, no : data['text']},
            dataType : 'json',
            async : false,
            success: function(response){
                arrData = response;
            }
        });
    }
    items=arrData['items'];
    total_all=0;
    $('#detail_item > tbody').empty();
    index=0;
    for (var i = 0; i < items.length; i++) {
        index++;
        total=parseFloat(items[i]['amount']) * parseInt(items[i]['base_price']);
        ppn=(items[i]['ppn'] == false ? total * 0.1 : 0);
        total=total + parseInt(ppn);
        total_all+=total;
        var tdAdd='<tr>'+
            '<td>'+index+'</td>'+
            '<td>'+(items[i]['code'] != null ? items[i]['code'] : '-')+'</td>'+
            '<td>'+(items[i]['name'] != null ? items[i]['name'] : items[i]['service_name'])+'</td>'+
            '<td class="text-right">'+parseFloat(items[i]['amount'])+'</td>'+
            '<td class="text-right">'+formatCurrency(parseInt(items[i]['base_price']).toString())+'</td>'+
            '<td class="text-right">'+formatCurrency(parseInt(ppn).toString())+'</td>'+
            '<td class="text-right">'+formatCurrency(parseInt(total).toString())+'</td>'+
        '</tr>';
        $('#detail_item').find('tbody:last').append(tdAdd);
    }
    $('#total_all').html(formatCurrency(parseInt(total_all).toString()));
    $('#total_purchase').val(arrData['total'])
    $('#total').val(formatCurrency(parseFloat(arrData['total']).toFixed(0)))
    $('#delivery_fee').val(arrData['delivery_fee'])
    $('#m_supplier_id').val(arrData['m_supplier_id'])
    $('#supplier_name').val(arrData['supplier']['name'])
    // if (data['wop'] == 'credit') {
    //     $('#notes').hide()
    //     // $('#type_payment').hide()
    //     $('#inv_trx').show()
    //     $('#inv_trx_service').hide()
    //     $('#total_cash').hide()
    //     $('#inv_id').prop('required', true);
    //     $('#inv_trx_service_id').prop('required', false);
    //     if (type == 'purchase') {
    //         getInvData(data['id']);   
    //     }else{
    //         getInvAssetData(data['id'])
    //     }
    // }else{
    //     $('#notes').show()
    //     // $('#type_payment').show()
    //     $('#inv_trx').hide()
    //     $('#inv_trx_service').hide()
    //     $('#total_cash').show()
    //     $('#inv_id').prop('required', false);
    //     $('#inv_trx_service_id').prop('required', false);
    //     $('#account_payment').prop('required', false);
    // }
}
function changePO(val){
    if(val == 0){
        $('#no_surat_jalan').val('');
        $('#no_surat_jalan_jasa').val('');
        $('#sj_item').show();
        $('#sj_jasa').hide();
    }else{
        $('#no_surat_jalan').val('');
        $('#no_surat_jalan_jasa').val('');
        $('#sj_item').hide();
        $('#sj_jasa').show();
    }
}
function typePurchase(data, type){
    delivery_fee=parseFloat((data['delivery_fee'] != null ? data['delivery_fee'] : 0))-parseFloat(data['delivery_fee_used']);
    $('#payment_po').val(data['wop'])
    $('#total_purchase').val(data['base_price'])
    $('#total').val(formatCurrency(data['base_price']))
    $('#delivery_fee').val(delivery_fee)
    $('#m_supplier_id').val(data['m_supplier_id'])
    if (data['wop'] == 'credit') {
        $('#notes').hide()
        // $('#type_payment').hide()
        $('#inv_trx').show()
        $('#inv_trx_service').hide()
        $('#total_cash').hide()
        $('#inv_id').prop('required', true);
        $('#inv_trx_service_id').prop('required', false);
        if (type == 'purchase') {
            getInvData(data['id']);   
        }else{
            getInvAssetData(data['id'])
        }
    }else{
        $('#notes').show()
        // $('#type_payment').show()
        $('#inv_trx').hide()
        $('#inv_trx_service').hide()
        $('#total_cash').show()
        $('#inv_id').prop('required', false);
        $('#inv_trx_service_id').prop('required', false);
        $('#account_payment').prop('required', false);
    }
}
function typePurchaseService(data){
    delivery_fee=parseFloat((data['delivery_fee'] != null ? data['delivery_fee'] : 0))-parseFloat(data['delivery_fee_used']);
    $('#payment_po').val(data['wop'])
    $('#total_purchase').val(data['base_price'])
    $('#total').val(formatCurrency(data['base_price']))
    $('#delivery_fee').val(delivery_fee)
    $('#m_supplier_id').val(data['m_supplier_id'])
    if (data['wop'] == 'credit') {
        // $('#type_payment').hide()
        $('#inv_trx').hide()
        $('#inv_trx_service').show()
        $('#total_cash').hide()
        $('#inv_id').prop('required', false);
        $('#inv_trx_service_id').prop('required', true);
        getInvServiceData(data['id']);   
    }else{
        $('#notes').show()
        // $('#type_payment').show()
        $('#inv_trx').hide()
        $('#inv_trx_service').hide()
        $('#total_cash').show()
        $('#inv_id').prop('required', false);
        $('#inv_trx_service_id').prop('required', false);
    }
}
function getInvData(id){
    $('#inv_id').empty();
    $('#inv_id').append('<option value="">-- Pilih No Penerimaan --</option>');
    $.ajax({
        type: "GET",
        url: "{{ URL::to('inventory/get_inv/') }}"+'/'+id, //json get site
        dataType : 'json',
        success: function(response){
            arrData = response['data'];
            for(i = 0; i < arrData.length; i++){
                $('#inv_id').append('<option value="'+arrData[i]['id']+'">'+arrData[i]['no']+'</option>');
            }
        }
    });
}
function getInvAssetData(id){
    $('#inv_id').empty();
    $('#inv_id').append('<option value="">-- Pilih No Penerimaan --</option>');
    $.ajax({
        type: "GET",
        url: "{{ URL::to('inventory/get_inv_asset/') }}"+'/'+id, //json get site
        dataType : 'json',
        success: function(response){
            arrData = response['data'];
            for(i = 0; i < arrData.length; i++){
                $('#inv_id').append('<option value="'+arrData[i]['id']+'">'+arrData[i]['no']+'</option>');
            }
        }
    });
}
function getInvServiceData(id){
    $('#inv_trx_service_id').empty();
    $('#inv_trx_service_id').append('<option value="">-- Pilih No Penerimaan --</option>');
    $.ajax({
        type: "GET",
        url: "{{ URL::to('inventory/get_inv_service/') }}"+'/'+id, //json get site
        dataType : 'json',
        success: function(response){
            arrData = response['data'];
            for(i = 0; i < arrData.length; i++){
                $('#inv_trx_service_id').append('<option value="'+arrData[i]['id']+'">'+arrData[i]['no']+'</option>');
            }
        }
    });
}
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
                $('#total').val(formatCurrency(response))
            }
        });   
    }else{
        $.ajax({
            type: "GET",
            url: "{{ URL::to('inventory/get_total_inv_asset/') }}"+'/'+inv_id+'/'+po_asset_id, //json get site
            dataType : 'json',
            success: function(response){
                arrData = response;
                $('#total').val(formatCurrency(response))
            }
        });   
    }
});
$('#inv_trx_service_id').change(function(){
    var po_id=$('#purchase_service_id').val();
    var inv_id=$('#inv_trx_service_id').val();
    $.ajax({
        type: "GET",
        url: "{{ URL::to('inventory/get_total_inv_service/') }}"+'/'+inv_id+'/'+po_id, //json get site
        dataType : 'json',
        success: function(response){
            arrData = response;
            $('#total').val(formatCurrency(response))
        }
    });   
});
function cekOngkir(val){
    if (val > delivery_fee) {
        alert('Tidak boleh melebihi '+delivery_fee);
        $('#delivery_fee').val('');
    }
}
</script>

@endsection