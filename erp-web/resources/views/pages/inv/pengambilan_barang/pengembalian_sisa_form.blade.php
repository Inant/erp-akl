@extends('theme.default')

@section('breadcrumb')
<div class="page-breadcrumb">
    <div class="row">
        <div class="col-5 align-self-center">
            <h4 class="page-title">Form Pengembalian Material</h4>
            <div class="d-flex align-items-center">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ URL::to('material_request') }}">List Pengembalian Material</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Form Pengembalian Material</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
</div>
@endsection

@section('content')
<form method="POST" action="{{ URL::to('material_request/returnadd') }}" class="form-horizontal">
    @csrf
    <div class="container-fluid">
        <!-- basic table -->
        <div class="row">
            @if($error['is_error'])
            <div class="col-12">
                <div class="alert alert-danger"> <i class="mdi mdi-alert-box"></i> {{ $error['error_message'] }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"> <span aria-hidden="true">×</span> </button>
                </div>
            </div>
            @endif
            <div class="col-12">
                <div class="text-right">
                    <a href="{{ URL::to('material_request') }}" class="btn btn-danger btn-sm mb-2">Cancel</a>
                    <button type="submit" class="btn btn-info btn-sm mb-2">Konfirmasi</button>
                </div>
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Pengembalian Material Header</h4>
                        <div class="form-group row">
                            <label class="col-sm-3 text-right control-label col-form-label">Order No</label>
                            <div class="col-sm-9">
                                <select name="order_id"  onchange="getOrderNo(this.value);" class="form-control select2 custom-select" style="width: 100%; height:32px;">
                                    <option value="">--- Select Order No ---</option>
                                    @if($order_list != null)
                                    @foreach($order_list as $value)
                                        @if($value['is_done'] != 1)
                                        <option value="{{ $value['id'] }}">{{ $value['order_no'].' | '.$value['spk_number'] }}</option>
                                        @endif
                                    @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-3 text-right control-label col-form-label">RAB Number</label>
                            <div class="col-sm-9">
                                <select id="rab_no" name="rab_no" required class="form-control select2 custom-select" style="width: 100%; height:32px;" onchange="handleRab(this);">
                                    <option value="">--- Select RAB Number ---</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-3 text-right control-label col-form-label">Inv Request Number</label>
                            <div class="col-sm-9">
                                <select name="inv_request_id" id="inv_request_id"  onchange="doShowDetail(this.value);" class="form-control select2 custom-select" style="width: 100%; height:32px;">
                                    <option value="">--- Select Inv Number ---</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="table-responsive">
                            <table id="requestDetail" class="table table-striped table-bordered display" style="width:100%">
                                <thead>
                                    <tr>
                                        <th class="text-center">Material No</th>
                                        <th class="text-center">Material Name</th>
                                        <th class="text-center">Qty Request</th>
                                        <th class="text-center">Qty Pengembalian Utuh</th>
                                        <th class="text-center">Satuan</th>
                                        <th class="text-center">Gudang</th>
                                        <th class="text-center">Storage</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                        <div class="form-group">
                            <button class="btn btn-primary" type="button" id="add_rest_material" disabled>Tambah Pengembalian Barang Sisa</button>
                        </div>
                       
                        <div class="table-responsive">
                            <table id="restDetail" class="table table-striped table-bordered display" style="width:100%">
                                <thead>
                                    <tr>
                                        <th class="text-center">Material Name</th>
                                        <th class="text-center">Qty Pengembalian Tidak Utuh</th>
                                        <th class="text-center">Satuan</th>
                                        <th class="text-center">Storage</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>           
    </div>
</form>

<script src="{!! asset('theme/assets/libs/jquery/dist/jquery.min.js') !!}"></script>
<script src="{!! asset('theme/assets/extra-libs/DataTables/datatables.min.js') !!}"></script>
<script>

// var t = $('#requestDetail_addrow').DataTable();
// var t2 = $('#requestDetail').DataTable();
var counter = 1;

var listMaterialRab = [];

$(document).ready(function(){
    let site_id = {{ $site_id }};
    // getProjectName(site_id);
});
function getOrderNo(order_id){
    // formProjectName = $('[id^=project_name]');
    // formProjectName.empty();
    // formProjectName.append('<option value="">-- Select Project --</option>');
    $('#requestDetail > tbody').empty();
    $('#restDetail > tbody').empty();
    formRabNo = $('[id^=rab_no]');
    formRabNo.empty();
    formRabNo.append('<option value="">-- Select RAB Number --</option>');
    $('[id^=inv_request_id]').empty();
    $.ajax({
        type: "GET",
        url: "{{ URL::to('rab/get_rab_by_order_id') }}", //json get site
        dataType : 'json',
        data:"order_id=" + order_id,
        success: function(response){
            arrData = response['data'];
            for(i = 0; i < arrData.length; i++){
                formRabNo.append('<option value="'+arrData[i]['id']+'">'+arrData[i]['no']+'</option>');
            }
        }
    });
    $("#add_rest_material").prop('disabled', true);
}
function handleRab(obj) {
    $('#requestDetail > tbody').empty();
    $('#restDetail  > tbody').empty();
    formInvRequest = $('[id^=inv_request_id]');
    formInvRequest.empty();
    formInvRequest.append('<option value="">-- Select Inv Number --</option>');
    $.ajax({
        type: "GET",
        url: "{{ URL::to('material_request/get_inv_req_by_rab') }}"+'/'+obj.value, //json get site
        dataType : 'json',
        success: function(response){
            arrData = response['data'];
            for(i = 0; i < arrData.length; i++){
                if (arrData[i]['req_type'] != 'RET_ITEM') {
                    formInvRequest.append('<option value="'+arrData[i]['id']+'">'+arrData[i]['no']+'</option>');   
                }
            }
        }
    });
}
var material_detail=[];
function doShowDetail(id){
    $('#requestDetail > tbody').empty();
    $.ajax({
            type: "GET",
            // url: "{{ URL::to('material_request/list_detail') }}" + "/" + id, //json get site
            url: "{{ URL::to('material_request/list_sisa') }}" + "/" + id, //json get site
            dataType : 'json',
            async : false,
            success: function(response){
                arrData = response['data'];
                material_detail=arrData;
                for(i = 0; i < arrData.length; i++){
                    // t2.row.add([
                    //     '<div class="text-left"><input type="hidden" id="inv_req_id[]" name="inv_req_d_id[]" value="'+arrData[i]['id']+'"/><input type="hidden" id="m_item_id[]" name="m_item_id[]" value="'+arrData[i]['m_item_id']+'"/><input type="hidden" id="m_unit_id[]" name="m_unit_id[]" value="'+arrData[i]['m_unit_id']+'"/><input type="hidden" id="type_material[]" name="type_material[]" value="'+arrData[i]['type_material']+'"/>'+(arrData[i]['m_items'] != null ? arrData[i]['m_items']['no'] : '-')+'</div>',
                    //     '<div class="text-left">'+(arrData[i]['m_items'] != null ? arrData[i]['m_items']['name'] : '-')+'</div>',
                    //     '<div class="text-right"><input type="hidden" id="amount[]" name="amount[]" value="'+parseFloat(arrData[i]['amount'])+'">'+(arrData[i]['amount'] != null ? parseFloat(arrData[i]['amount']) : '-')+'</div>',
                    //     '<div class="text-right"><input type="number" id="stok[]" name="qty[]" min="0" class="form-control text-right" placeholder="0" required value="0" onkeyup="cekQty()" oninput="this.value=(parseInt(this.value)||0)"></div>',
                    //     '<div class="text-center">'+(arrData[i]['m_units'] != null ? arrData[i]['m_units']['name'] : '-')+'</div>',
                    //     '<div class="text-right"><input type="hidden" class="form-control" id="base_price[]" name="base_price[]" value="'+arrData[i]['base_price']+'"><input type="hidden" class="form-control" id="m_warehouse_id[]" name="m_warehouse_id[]" value="'+arrData[i]['m_warehouse_id']+'">'+arrData[i]['m_warehouses']['name']+'</div>',
                    //     '<div class="text-right"><input type="" class="form-control" id="storage[]" name="storage[]"></div>',
                    // ]).draw(false);
                    
                    var tdAdd='<tr>'+
                            '<td>'+
                            '<div class="text-left"><input type="hidden" id="inv_req_id[]" name="inv_req_d_id[]" value="'+arrData[i]['id']+'"/><input type="hidden" id="m_item_id[]" name="m_item_id[]" value="'+arrData[i]['m_item_id']+'"/><input type="hidden" id="m_unit_id[]" name="m_unit_id[]" value="'+arrData[i]['m_unit_id']+'"/><input type="hidden" id="type_material[]" name="type_material[]" value="'+arrData[i]['type_material']+'"/>'+(arrData[i]['m_items'] != null ? arrData[i]['m_items']['no'] : '-')+'</div>'+
                            '</td>'+
                            '<td>'+
                            '<div class="text-left">'+(arrData[i]['m_items'] != null ? arrData[i]['m_items']['name'] : '-')+'</div>'+
                            '</td>'+
                            '<td>'+
                            '<div class="text-right"><input type="hidden" id="amount[]" name="amount[]" value="'+parseFloat(arrData[i]['amount'])+'">'+(arrData[i]['amount'] != null ? parseFloat(arrData[i]['amount']) : '-')+'</div>'+
                            '</td>'+
                            '<td>'+
                            '<div class="text-right"><input type="number" id="stok[]" name="qty[]" min="0" class="form-control text-right" placeholder="0" required value="0" onkeyup="cekQty()" oninput="this.value=(parseInt(this.value)||0)"></div>'+
                            '</td>'+
                            '<td>'+
                            '<div class="text-center">'+(arrData[i]['m_units'] != null ? arrData[i]['m_units']['name'] : '-')+'</div>'+
                            '</td>'+
                            '<td>'+
                            '<div class="text-right"><input type="hidden" class="form-control" id="base_price[]" name="base_price[]" value="'+arrData[i]['base_price']+'"><input type="hidden" class="form-control" id="m_warehouse_id[]" name="m_warehouse_id[]" value="'+arrData[i]['m_warehouse_id']+'">'+arrData[i]['m_warehouses']['name']+'</div>'+
                            '</td>'+
                            '<td>'+
                            '<div class="text-right"><input type="" class="form-control" id="storage[]" name="storage[]"></div>'+
                            '</td>'+
                        '</tr>';
                    $('#requestDetail').find('tbody:last').append(tdAdd);
                }
            }
    });
    $("#add_rest_material").prop('disabled', false);
}
function cekQty(){
    var inv_req_id = $('[id^=inv_req_id]');
    var qty = $('[id^=amount]');
    var qty_req = $('[id^=stok]');
    for(var i = 0; i < inv_req_id.length; i++){
        var total=qty.eq(i).val();
        var total_req=qty_req.eq(i).val();
        if(parseFloat(total_req) > parseFloat(total)){
            qty_req.eq(i).val('');
            alert('inputan melebihi request yang ada');
        }
    }
}
function cekQtyDec(){
    var inv_req_id = $('[id^=inv_req_id]');
    var qty = $('[id^=amount]');
    var qty_req = $('[id^=stok]');

    var qty_bullet = $('[id^=qty_req_bullet]');
    var qty_dec = $('[id^=qty_req_dec]');
    var inv_req_d_id_rest = $('[id^=inv_req_d_id_rest]');
    var turunan = $('[id^=turunan]');
    
    for(var i = 0; i < inv_req_id.length; i++){
        var total=qty.eq(i).val();
        var total_req=(qty_req.eq(i).val() != '' ? qty_req.eq(i).val() : 0);
        var id=inv_req_id.eq(i).val();
        var qty_request=total_req;
        for(var j = 0; j < inv_req_d_id_rest.length; j++){
            var id_rest=inv_req_d_id_rest.eq(j).val();
            if (id == id_rest) {
                if (qty_bullet.eq(j).val() != '' && qty_dec.eq(j).val() != '') {
                    var total_bullet=qty_bullet.eq(j).val() != '' ? qty_bullet.eq(j).val() : 0;
                    var total_req_dec=qty_dec.eq(j).val() != '' ? qty_dec.eq(j).val() : 0;
                    var total_rest=parseFloat(total_bullet) * parseFloat(total_req_dec);
                    qty_request=parseFloat(qty_request) + parseFloat(total_rest);
                    if(parseFloat(qty_request) > parseFloat(total)){
                        qty_bullet.eq(j).val('');
                        qty_dec.eq(j).val('');
                        turunan.eq(j).val('');
                        alert('inputan melebihi request yang ada');
                    }
                }
            }
        }
    }
}

function cek_rest(){
    var inv_req_d_id_rest = $('[id^=inv_req_d_id_rest]');
    var amount_child = $('[id^=amount_child]');
    var label_child = $('[id^=label_child]');
    var qty_bullet = $('[id^=qty_req_bullet]');
    var qty_dec = $('[id^=qty_req_dec]');
    var child = $('[id^=turunan]');
    var m_item_rest = $('[id^=m_item_rest]');
    var m_unit_rest = $('[id^=m_unit_rest]');
    var m_warehouse_rest = $('[id^=m_warehouse_rest]');
    for(var j = 0; j < inv_req_d_id_rest.length; j++){
        var id_rest=inv_req_d_id_rest.eq(j).val();
        var rest_amount=0;
        var turunan=0;
        var satuan=0;
        var m_item=0;
        var m_unit=0;
        var m_warehouse=0;
        $.each(material_detail, function(j, item) {
            if(material_detail[j]['id'] == id_rest){
                rest_amount=material_detail[j]['m_items']['amount_unit_child'];
                turunan=(material_detail[j]['m_unit_child'] != null ? material_detail[j]['m_unit_child']['name'] : '-');
                satuan=material_detail[j]['m_units']['name'];
                m_item=material_detail[j]['m_item_id'];
                m_unit=material_detail[j]['m_unit_id'];
                m_warehouse=material_detail[j]['m_warehouse_id'];
            }     
        });
        label_child.eq(j).val(rest_amount+' '+turunan+' / '+satuan);
        amount_child.eq(j).val(rest_amount);
        m_item_rest.eq(j).val(m_item);
        m_unit_rest.eq(j).val(m_unit);
        m_warehouse_rest.eq(j).val(m_warehouse);
        qty_bullet.eq(j).val('');
        qty_dec.eq(j).val('');
        child.eq(j).val('');
    }
}
function changeQtyReqDec(){
    var inv_req_d_id_rest = $('[id^=inv_req_d_id_rest]');
    var amount_child = $('[id^=amount_child]');
    var qty_bullet = $('[id^=qty_req_bullet]');
    var qty_dec = $('[id^=qty_req_dec]');
    var turunan = $('[id^=turunan]');
    for(var j = 0; j < inv_req_d_id_rest.length; j++){
        var id_rest=inv_req_d_id_rest.eq(j).val();
        var amount_turunan=turunan.eq(j).val();
        var amount_rest=amount_child.eq(j).val();
        var rest_amount=amount_turunan / amount_rest;
        if (amount_turunan >= amount_rest) {
            turunan.eq(j).val('');
        }else{
            qty_dec.eq(j).val(rest_amount);
        }
    }
}
$("#add_rest_material").click(function(){
    var options='<option value="">-- Pilih Item -- </option>';
    for(var i = 0; i < material_detail.length; i++){
        options+='<option value="'+material_detail[i]['id']+'">'+(arrData[i]['m_items'] != null ? arrData[i]['m_items']['name'] : '-')+'</option>';
    }
    var tdAdd='<tr><td><select name="inv_req_d_id_rest[]" onchange="cek_rest()" id="inv_req_d_id_rest[]" class="form-control">'+options+'</select></td>'+
        '<td><div class="form-group row"><input type="number" id="turunan[]" name="turunan[]" class="form-control col-sm-5" step="any" min="0" placeholder="potongan"  onkeyup="changeQtyReqDec()"><input type="hidden" id="qty_req_dec[]" name="qty_req_dec[]" class="form-control col-sm-5" step="any" min="0" placeholder="desimal"  onkeyup="cekQtyDec()">'+
                                    '<input type="number" id="qty_req_bullet[]" name="qty_req_bullet[]" min="0" class="form-control col-sm-5" placeholder="total" oninput="this.value=(parseInt(this.value)||0)"  onkeyup="cekQtyDec()">'+
                                    '<div></td>'+
        '<td><input type="hidden" id="m_item_rest[]" name="m_item_rest[]" class="form-control col-sm-5"><input type="hidden" id="m_warehouse_rest[]" name="m_warehouse_rest[]" class="form-control col-sm-5"><input type="hidden" id="m_unit_rest[]" name="m_unit_rest[]" class="form-control col-sm-5"><div class="text-right"><input type="" readonly class="form-control" id="label_child[]" name=""><input type="hidden" class="form-control" id="amount_child[]" name=""></div></td>'+
        '<td><div class="text-right"><input type="" class="form-control" id="storage_rest[]" name="storage_rest[]"></div></td>'+
        '<td><button type="button" class="btn btn-sm btn-danger removeOption"><i class="mdi mdi-delete"></i></button></td>'+
        '</tr>';
    $('#restDetail').find('tbody:last').append(tdAdd);
});

$("#restDetail").on("click", ".removeOption", function(event) {
    event.preventDefault();
    $(this).closest("tr").remove();
});
</script>
@endsection