@extends('theme.default')

@section('breadcrumb')
<div class="page-breadcrumb">
    <div class="row">
        <div class="col-5 align-self-center">
            <h4 class="page-title">Form Material Request</h4>
            <div class="d-flex align-items-center">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ URL::to('material_request') }}">List Material Request</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Form Material Request</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
</div>
@endsection

@section('content')
<form method="POST" action="{{ URL::to('material_request/re-request') }}" class="form-horizontal">
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
                    <button id="btnSubmit" onclick="handleSubmit();" type="button" disabled data-toggle="modal" data-target="#modalShowDetail" class="btn btn-info btn-sm mb-2">Konfirmasi</button>
                </div>
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Request Header</h4>
                        <!-- <div class="form-group row">
                            <label class="col-sm-3 text-right control-label col-form-label">City</label>
                            <div class="col-sm-9">
                                <select name="site_location" required onchange="getSiteName(this.value);" class="form-control select2 custom-select" style="width: 100%; height:32px;">
                                    <option value="">--- Select City ---</option>
                                    @if($site_locations != null)
                                    @foreach($site_locations as $site_location)
                                    <option value="{{ $site_location['id'] }}">{{ $site_location['city'] }}</option>
                                    @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-3 text-right control-label col-form-label">Site Name</label>
                            <div class="col-sm-9">
                                <select id="site_name" name="site_name" required onchange="getProjectName(this.value);" class="form-control select2 custom-select" style="width: 100%; height:32px;">
                                    <option value="">--- Select Site Name ---</option>
                                </select>
                            </div>
                        </div> -->
                        <div class="form-group row">
                            <label class="col-sm-3 text-right control-label col-form-label">Grup Label</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" id="group_id" onchange="cekInvNumber(this.value)" placeholder="isi salah satu label, ex : J2-SBY-ORD-09-20-012/131/1/1">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-3 text-right control-label col-form-label">Gudang handle</label>
                            <div class="col-sm-9">
                                <select name="m_warehouse_id"  class="form-control select2 custom-select" style="width: 100%; height:32px;">
                                    <option value="">--- Select Warehouse ---</option>
                                    @if($warehouse != null)
                                    @foreach($warehouse as $value)
                                        <option value="{{ $value->id }}">{{ $value->name }}</option>
                                    @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>
                        <!-- <div class="form-group row">
                            <label class="col-sm-3 text-right control-label col-form-label">Order No</label>
                            <div class="col-sm-9">
                                <select name="order_id"  onchange="getOrderNo(this.value);" class="form-control select2 custom-select" style="width: 100%; height:32px;">
                                    <option value="">--- Select Order No ---</option>
                                    @if($order_list != null)
                                    @foreach($order_list as $value)
                                        @if($value['is_done'] != 1)
                                        <option value="{{ $value['id'] }}">{{ $value['order_no'] }}</option>
                                        @endif
                                    @endforeach
                                    @endif
                                </select>
                            </div>
                        </div> -->
                        <!-- <div class="form-group row">
                            <label class="col-sm-3 text-right control-label col-form-label">Project</label>
                            <div class="col-sm-9">
                                <select id="project_name" name="project_name" required class="form-control select2 custom-select" style="width: 100%; height:32px;" onchange="getRab(this.value);">
                                    <option value="">--- Select Kavling ---</option>
                                </select>
                            </div>
                        </div> -->
                        <!-- <div class="form-group row">
                            <label class="col-sm-3 text-right control-label col-form-label">RAB Number</label>
                            <div class="col-sm-9">
                                <select id="rab_no" name="rab_no" required class="form-control select2 custom-select" style="width: 100%; height:32px;" onchange="handleRab(this);">
                                    <option value="">--- Select RAB Number ---</option>
                                </select>
                            </div>
                        </div> -->
                        <div class="form-group row">
                            <label class="col-sm-3 text-right control-label col-form-label">Request Number</label>
                            <div class="col-sm-9">
                                <select id="req_no" name="req_no" required class="form-control select2 custom-select" style="width: 100%; height:32px;" onchange="handleRequest(this);">
                                    <option value="">--- Select Inv Req Number ---</option>
                                </select>
                            </div>
                        </div>
                        <h4 class="card-title">Request Detail</h4>
                            
                        <div class="form-group">
                            <select class="form-control custom-select select2" style="width: 300px; height:32px;" id="item_id"></select>
                            <button type="button" disabled id="addRow" class="btn btn-success btn-sm mb-2"><i class="fas fa-plus"></i>&nbsp; Add New Detail</button>
                            <!-- <button type="button" disabled id="delRow" class="btn btn-danger btn-sm mb-2"><i class="ti-trash"></i>&nbsp; Delete Selected Detail</button> -->
                        </div>
                            
                        <div class="table-responsive">
                            <table id="requestDetail_addrow" class="table table-striped table-bordered display" style="width:100%">
                                <thead>
                                    <tr>
                                        <th class="text-center">Material No</th>
                                        <th class="text-center">Material Name</th>
                                        <th class="text-center">Qty RAP</th>
                                        <th class="text-center">Qty Sisa dari RAP</th>
                                        <th class="text-center">Qty Pengajuan</th>
                                        <th class="text-center" width="150px">Note</th>
                                        <th class="text-center">Satuan</th>
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

    <div class="modal fade" id="modalShowDetail" tabindex="-1" role="dialog" aria-labelledby="modalShowDetailLabel1">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="modalAddMaterialLabel1">Konfirmasi Material Request</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                    <h5 class="card-title">Permintaan Normal</h5>
                    <div class="table-responsive">
                        <table id="dtPermintaanNormal" class="table table-bordered">
                            <thead>
                                <tr>
                                    <th class="text-center">Material Name</th>
                                    <th class="text-center">Qty RAP</th>
                                    <th class="text-center">Qty Sisa dari RAP</th>
                                    <th class="text-center">Qty Pengajuan</th>
                                    <th class="text-center">Note</th>
                                    <th class="text-center">Satuan</th>
                                </tr>
                            </thead>   
                            <tbody></tbody>                                   
                        </table>
                    </div>
                    <br />
                    <h5 class="card-title">Permintaan Khusus <span style="color: red; font-size: 12px;">(perlu dilakukan autorisasi)</span></h5>
                    <div class="table-responsive">
                        <table id="dtPermintaanKhusus" class="table table-bordered">
                            <thead>
                                <tr>
                                    <th class="text-center">Material Name</th>
                                    <th class="text-center">Qty RAP</th>
                                    <th class="text-center">Qty Sisa dari RAP</th>
                                    <th class="text-center">Qty Pengajuan</th>
                                    <th class="text-center">Note</th>
                                    <th class="text-center">Satuan</th>
                                    <th class="text-center">Alasan</th>
                                </tr>
                            </thead>     
                            <tbody></tbody>                                 
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary btn-sm">Submit Request</button>
                </div>
            </div>
        </div>
    </div>
</form>

<script src="{!! asset('theme/assets/libs/jquery/dist/jquery.min.js') !!}"></script>
<script src="{!! asset('theme/assets/extra-libs/DataTables/datatables.min.js') !!}"></script>
<script>

// var t = $('#requestDetail_addrow').DataTable();
// var t1 = $('#requestDetail').DataTable();
// var dtPermintaanNormal = $('#dtPermintaanNormal').DataTable();
// var dtPermintaanKhusus = $('#dtPermintaanKhusus').DataTable();

var counter = 1;

var listMaterialRab = [];

$(document).ready(function(){
    let site_id = {{ $site_id }};
    // getProjectName(site_id);
});
let arrSelectedMaterial = [];
var selectedItem=[];
$('#addRow').on('click', function() {
    var item_selected=$('#item_id').val();
    var satuan = '', unitName = '', itemName = '', itemNo = '';
    let qty_rab = 0;
    let qty_sisa_rab = 0;
    $.each(arrMaterial, function(i, item) {
        if(item_selected == arrMaterial[i]['id']){
            satuan = arrMaterial[i]['m_unit_id'];
            unitName = arrMaterial[i]['m_unit_name'];
            itemName = arrMaterial[i]['name'];
            itemNo = arrMaterial[i]['no'];
            // set qty_rab, qty_sisa_rab
            // cek di material rab
            for(j = 0; j < listMaterialRab.length; j++) {
                if(arrMaterial[i]['id'] == listMaterialRab[j]['m_item_id']) {
                    qty_rab = parseFloat(listMaterialRab[j]['amount']);
                    qty_sisa_rab = parseFloat(listMaterialRab[j]['amount']) - parseFloat(listMaterialRab[j]['used_amount']);
                }
            }
        }     
    });
    var is_there=false;
    $.each(selectedItem, function(i, item){
        if (item_selected == item) {
            is_there=true;
        }
    });
    if (is_there == false && item_selected != '') {
        var tdAdd='<tr>'+
                    '<td>'+
                        '<input type="hidden" id="m_item_no[]" name="m_item_no[]" class="form-control" value="'+itemNo+'" readonly/>'+itemNo+
                    '</td>'+
                    '<td>'+
                        '<input type="hidden" id="m_item_name[]" name="m_item_name[]" value="'+itemName+'" /><input type="hidden" id="m_item_id[]" name="m_item_id[]" value="'+item_selected+'" />'+itemName+
                    '</td>'+
                    '<td>'+
                        '<input type="number" readonly id="qty_rab[]" name="qty_rab[]" class="form-control text-right" value="'+qty_rab+'" required>' +
                    '</td>'+
                    '<td>'+
                    '<input type="number" readonly id="qty_sisa_rab[]" name="qty_sisa_rab[]" class="form-control text-right" value="'+qty_sisa_rab+'" required>' +
                    '</td>'+
                    '<td>'+
                        '<input type="number" id="qty_req[]" name="qty[]" step="any" min="0" class="form-control text-right" placeholder="0" value="">' +
                    '</td>'+
                    '<td>'+
                        '<textarea id="detail_note[]" name="detail_note[]" class="form-control text-right"></textarea>' +
                    '</td>'+
                    '<td>'+
                        '<input type="hidden" id="unit_name[]" name="unit_name[]" value="'+unitName+'"/><input type="hidden" id="m_unit_id[]" name="m_unit_id[]" value="'+satuan+'"/><select disabled class="form-control select2 custom-select" style="width: 100%; height:32px;" id="m_unit_name[]" name="m_unit_name[]" required>'+
                        '<option value="'+satuan+'">'+unitName+'</option>'+
                        '</select>' +
                    '</td>'+
                '<td class="text-center"><button class="btn btn-danger removeOption"><i class="mdi mdi-delete"></i></button></td>'+
            '</tr>';
        $('#requestDetail_addrow').find('tbody:last').append(tdAdd);
    }
    // var tdAdd='<tr>'+
    //         '<td>'+
    //             '<input type="text" id="m_item_no[]" name="m_item_no[]" class="form-control" onchange="handleMaterialNo(this)" />'+
    //         '</td>'+
    //         '<td>'+
    //             '<input type="hidden" id="m_item_name[]" name="m_item_name[]" /><select class="form-control select2 custom-select" style="width: 100%; height:32px;" id="m_item_id[]" name="m_item_id[]" required onchange="handleMaterial()"></select>'+
    //         '</td>'+
    //         '<td>'+
    //             '<input type="number" readonly id="qty_rab[]" name="qty_rab[]" class="form-control text-right" value="0" required>'+
    //         '</td>'+
    //         '<td>'+
    //             '<input type="number" readonly id="qty_sisa_rab[]" name="qty_sisa_rab[]" class="form-control text-right" value="0" required>'+
    //         '</td>'+
    //         '<td>'+
    //             '<input type="number" id="qty_req[]" name="qty[]" step="any" min="0" class="form-control text-right" placeholder="0" required>'+
    //         '</td>'+
    //         '<td>'+
    //             '<textarea id="detail_note[]" name="detail_note[]" class="form-control text-right"></textarea>'+
    //         '</td>'+
    //         '<td>'+
    //             '<input type="hidden" id="unit_name[]" name="unit_name[]" /><input type="hidden" id="m_unit_id[]" name="m_unit_id[]" /><select disabled class="form-control select2 custom-select" style="width: 100%; height:32px;" id="m_unit_name[]" name="m_unit_name[]" required></select>'+
    //         '</td>'+
    //         '<td class="text-center"><button class="btn btn-danger removeOption"><i class="mdi mdi-delete"></i></button></td>'+
    //     '</tr>';
    // $('#requestDetail_addrow').find('tbody:last').append(tdAdd);

    // countMaterial = $('[id^=m_item_id]').length;
    // for(i = 0; i < countMaterial; i++){
    //     if($('[id^=m_item_id]').eq(i).val() !== null && $('[id^=m_item_id]').eq(i).val() !== '')
    //         arrSelectedMaterial.push($('[id^=m_item_id]').eq(i).val());
    // }

    // for(i = 0; i < countMaterial; i++){
    //     formMaterial = $('[id^=m_item_id]').eq(i);
    //     selectedMaterial = $('[id^=m_item_id]').eq(i).val();
    //     $('[id^=m_item_id]').eq(i).empty();
    //     formMaterial.append('<option value="">-- Select Material --</option>')
    //     $.each(arrMaterial, function(i, item) {
    //         if(selectedMaterial == arrMaterial[i]['id'])
    //             formMaterial.append('<option selected value="'+arrMaterial[i]['id']+'">'+arrMaterial[i]['name']+'</option>');
    //         else {
    //             if (arrSelectedMaterial.includes(arrMaterial[i]['id'].toString()))
    //                 formMaterial.append('<option disabled value="'+arrMaterial[i]['id']+'">'+arrMaterial[i]['name']+'</option>');
    //             else
    //                 formMaterial.append('<option value="'+arrMaterial[i]['id']+'">'+arrMaterial[i]['name']+'</option>');
    //         }
    //     });

    //     formUnit = $('[id^=m_unit_name]').eq(i);
    //     selectedUnit = $('[id^=m_unit_name]').eq(i).val();
    //     $('[id^=m_unit_name]').eq(i).empty();
    //     formUnit.append('<option value="">-- Select Unit --</option>')
    //     $.each(arrUnit, function(i, item) {
    //         if(selectedUnit == arrUnit[i]['id'])
    //             formUnit.append('<option selected value="'+arrUnit[i]['id']+'">'+arrUnit[i]['name']+'</option>');
    //         else
    //             formUnit.append('<option value="'+arrUnit[i]['id']+'">'+arrUnit[i]['name']+'</option>');
    //     });
    // }

    document.getElementById("btnSubmit").disabled = false;
    saveSelectedItem();
});

function saveSelectedItem(){
    selectedItem=[];
    var m_item_id = $('[name^=m_item_id]');
    for(i = 0; i < m_item_id.length; i++){
        var item=m_item_id.eq(i).val();
        if (item != '') {
            selectedItem.push(item);
        }
    }
}

$("#requestDetail_addrow").on("click", ".removeOption", function(event) {
    event.preventDefault();
    $(this).closest("tr").remove();
    let countMaterial = $('[id^=m_item_id]').length;
    if (countMaterial == 0)
        document.getElementById("btnSubmit").disabled = true;
});

// let arrSelectedMaterial = [];
function handleMaterial(){
    countMaterial = $('[id^=m_item_id]').length;
    
    for(i = 0; i < countMaterial; i++){
        if($('[id^=m_item_id]').eq(i).val() !== null && $('[id^=m_item_id]').eq(i).val() !== '')
            arrSelectedMaterial.push($('[id^=m_item_id]').eq(i).val());
    }
    
    for(i = 0; i < countMaterial; i++){
        formMaterialNo = $('[id^=m_item_no]').eq(i);
        formMaterial = $('[id^=m_item_id]').eq(i);
        selectedMaterial = $('[id^=m_item_id]').eq(i).val();
        satuan = '', value = '';
        itemName = '', unitName = '';
        itemNo = '';

        formQtyRab = $('[id^=qty_rab]').eq(i);
        formQtySisaRab = $('[id^=qty_sisa_rab]').eq(i);
        
        $('[id^=m_item_id]').eq(i).empty();
        formMaterial.append('<option value="">-- Select Material --</option>')
        $.each(arrMaterial, function(i, item) {
            if(selectedMaterial == arrMaterial[i]['id'])
                formMaterial.append('<option selected value="'+arrMaterial[i]['id']+'">'+arrMaterial[i]['name']+'</option>');
            else {
                if (arrSelectedMaterial.includes(arrMaterial[i]['id'].toString()))
                    formMaterial.append('<option disabled value="'+arrMaterial[i]['id']+'">'+arrMaterial[i]['name']+'</option>');
                else
                    formMaterial.append('<option value="'+arrMaterial[i]['id']+'">'+arrMaterial[i]['name']+'</option>');
            }

            if(selectedMaterial == arrMaterial[i]['id']){
                satuan = arrMaterial[i]['m_unit_id'];
                unitName = arrMaterial[i]['m_unit_name'];
                itemName = arrMaterial[i]['name'];
                itemNo = arrMaterial[i]['no'];
                // set qty_rab, qty_sisa_rab
                // cek di material rab
                let qty_rab = 0;
                let qty_sisa_rab = 0;
                console.log(listMaterialRab);
                for(j = 0; j < listMaterialRab.length; j++) {
                    if(arrMaterial[i]['id'] == listMaterialRab[j]['m_item_id']) {
                        qty_rab = parseFloat(listMaterialRab[j]['amount']);
                        qty_sisa_rab = parseFloat(listMaterialRab[j]['amount']) - parseFloat(listMaterialRab[j]['used_amount']);
                    }
                }
                formQtyRab.val(qty_rab);
                formQtySisaRab.val(qty_sisa_rab);
            }     
        });

        $('[id^=m_unit_id]').eq(i).val(satuan);
        $('[id^=m_unit_name]').eq(i).val(satuan);
        $('[id^=unit_name]').eq(i).val(unitName);
        $('[id^=m_item_name]').eq(i).val(itemName);
        $('[id^=m_item_no]').eq(i).val(itemNo);
    }
}


var arrMaterial = [];
$.ajax({
    type: "GET",
    url: "{{ URL::to('material_request/get_material') }}", //json get material
    dataType : 'json',
    success: function(response){
        arrMaterial = response['data'];
        $('#item_id').append('<option selected value="">Pilih Material / Spare Part</option>')
        $.each(arrMaterial, function(i, item) {
            $('#item_id').append('<option value="'+arrMaterial[i]['id']+'">('+arrMaterial[i]['no']+') '+arrMaterial[i]['name']+'</option>');
        });
    }
});

var arrUnit = [];
$.ajax({
    type: "GET",
    url: "{{ URL::to('rab/get_all_m_unit') }}", //json get site
    dataType : 'json',
    success: function(response){
        arrUnit = response['data'];    
    }
});




function getSiteName(site_location_id){
    formSiteName = $('[id^=site_name]');
    formSiteName.empty();
    formSiteName.append('<option value="">-- Select Site Name --</option>');
    $.ajax({
        type: "GET",
        url: "{{ URL::to('rab/get_site') }}", //json get site
        dataType : 'json',
        data:"town_id=" + site_location_id,
        success: function(response){
            arrData = response['data'];
            for(i = 0; i < arrData.length; i++){
                formSiteName.append('<option value="'+arrData[i]['id']+'">'+arrData[i]['name']+'</option>');
            }
        }
    });

    getProjectName('');
}

function getProjectName(site_id){
    formProjectName = $('[id^=project_name]');
    formProjectName.empty();
    formProjectName.append('<option value="">-- Select Project Name --</option>');
    $.ajax({
        type: "GET",
        url: "{{ URL::to('rab/get_project') }}", //json get site
        dataType : 'json',
        data:"site_id=" + site_id,
        success: function(response){
            arrData = response['data'];
            for(i = 0; i < arrData.length; i++){
                formProjectName.append('<option value="'+arrData[i]['id']+'">'+arrData[i]['name']+'</option>');
            }
        }
    });
}

function getRab(project_id){
    formRabNo = $('[id^=rab_no]');
    formRabNo.empty();
    formRabNo.append('<option value="">-- Select RAB Number --</option>');
    $.ajax({
        type: "GET",
        url: "{{ URL::to('rab/get_rab_by_project_id') }}", //json get site
        dataType : 'json',
        data:"project_id=" + project_id,
        success: function(response){
            arrData = response['data'];
            for(i = 0; i < arrData.length; i++){
                formRabNo.append('<option value="'+arrData[i]['rab_id']+'">'+arrData[i]['rab_no']+'</option>');
            }
        }
    });
}

function handleRab(obj) {
    formReqNo = $('[id^=req_no]');
    formReqNo.empty();
    formReqNo.append('<option value="">-- Select Inv Request Number --</option>');
    $.ajax({
        type: "GET",
        url: "{{ URL::to('material_request/get_inv_req_by_rab') }}"+'/'+obj.value, //json get site
        dataType : 'json',
        success: function(response){
            arrData = response['data'];
            for(i = 0; i < arrData.length; i++){
                formReqNo.append('<option value="'+arrData[i]['id']+'">'+arrData[i]['no']+'</option>');
            }
        }
    });
    
    $('#requestDetail_addrow > tbody').empty();

    // // set listMaterialRab
    // $.ajax({
    //     type: "GET",
    //     url: "{{ URL::to('material_request/material_rab') }}", //json get site
    //     dataType : 'json',
    //     async : false,
    //     data:"rab_id=" + obj.value,
    //     success: function(response){
    //         arrData = response['data'];
    //         listMaterialRab = arrData;
    //     }
    // });
    
}
var inv_request=[];
function handleRequest(obj){
    $('#requestDetail_addrow > tbody').empty();
    if(obj.value !== '') {
        $.ajax({
            type: "GET",
            url: "{{ URL::to('material_request/get_inv_id') }}"+'/'+obj.value, //json get site
            dataType : 'json',
            async : false,
            success: function(response){
                arrData = response['data'];
                inv_request=arrData;
            }
        });
        $.ajax({
            type: "GET",
            url: "{{ URL::to('material_request/project_dev_request') }}"+'/'+inv_request['project_req_development_id'],
            dataType : 'json',
            async : false,
            success: function(response){
                project_req_dev=response['data'];
            }
        });

        $.ajax({
            type: "GET",
            url: "{{ URL::to('material_request/material_rab3') }}", //json get site
            dataType : 'json',
            async : false,
            data:{req_id : inv_request['project_req_development_id']},
            success: function(response){
                arrData = response['data'];
                listMaterialRab = arrData;
            }
        });
        for(var i = 0; i < listMaterialRab.length; i++){
            var qty_rab = Math.ceil(parseFloat(listMaterialRab[i]['amount']) * (parseFloat(listMaterialRab[i]['total_request']) / parseFloat(project_req_dev['amount_kontrak'])));
            listMaterialRab[i]['amount']=qty_rab;
            // listMaterialRab[i]['amount']=qty_rab;
            // var qty_sisa_rab = qty_rab - parseFloat(listMaterialRab[i]['used_amount']);
        }
        document.getElementById("addRow").disabled = false;
        // document.getElementById("delRow").disabled = false;
    } else {
        document.getElementById("addRow").disabled = true;
        // document.getElementById("delRow").disabled = true;
    }

}

function handleSubmit() {
    // dtPermintaanNormal.clear().draw(false);
    // dtPermintaanKhusus.clear().draw(false);
    $('#dtPermintaanNormal > tbody').empty();
    $('#dtPermintaanKhusus > tbody').empty();
    let countMaterial = $('[id^=m_item_id]').length;
    for(i = 0; i < countMaterial; i++) {
        formMItemName = $('[id^=m_item_name]').eq(i).val();
        formQtyRab = parseFloat($('[id^=qty_rab]').eq(i).val());
        formQtySisaRab = parseFloat($('[id^=qty_sisa_rab]').eq(i).val());
        formQty = parseFloat(($('[id^=qty_req]').eq(i).val() ? $('[id^=qty_req]').eq(i).val() : 0));
        formNote = $('[id^=detail_note]').eq(i).val();
        formMUnitName = $('[id^=unit_name]').eq(i).val();

        // normal
        if (formQty != 0 && formQty != '') {
            if (formQtySisaRab >= formQty) {
                // dtPermintaanNormal.row.add([
                //     formMItemName,
                //     formQtyRab,
                //     formQtySisaRab,
                //     formQty,
                //     formNote,
                //     formMUnitName
                // ]).draw(false);
                var tdAdd='<tr>'+
                    '<td>'+formMItemName+'</td>'+
                    '<td>'+formQtyRab +'</td>'+
                    '<td>'+formQtySisaRab +'</td>'+
                    '<td>'+formQty +'</td>'+
                    '<td>'+formNote +'</td>'+
                    '<td>'+formMUnitName+'</td>'+
                '</tr>';
                $('#dtPermintaanNormal').find('tbody:last').append(tdAdd);
            } else { 
                if (formQtySisaRab == 0) {
                    // khusus
                    // dtPermintaanKhusus.row.add([
                    //     formMItemName,
                    //     formQtyRab,
                    //     formQtySisaRab,
                    //     formQty,
                    //     formNote,
                    //     formMUnitName,
                    //     '<input type="text" id="alasan[]" name="alasan[]" required class="form-control">'
                    // ]).draw(false);
                    var tdAdd='<tr>'+
                        '<td>'+formMItemName+'</td>'+
                        '<td>'+formQtyRab +'</td>'+
                        '<td>'+formQtySisaRab +'</td>'+
                        '<td>'+formQty +'</td>'+
                        '<td>'+formNote +'</td>'+
                        '<td>'+formMUnitName+'</td>'+
                        '<td><input type="text" id="alasan[]" name="alasan[]" required class="form-control"></td>'+
                    '</tr>';
                    $('#dtPermintaanKhusus').find('tbody:last').append(tdAdd);
                } else {
                    // permintaan normal
                    // dtPermintaanNormal.row.add([
                    //     formMItemName,
                    //     formQtyRab,
                    //     formQtySisaRab,
                    //     formQtySisaRab,
                    //     formNote,
                    //     formMUnitName
                    // ]).draw(false);
                    var tdAdd='<tr>'+
                        '<td>'+formMItemName+'</td>'+
                        '<td>'+formQtyRab +'</td>'+
                        '<td>'+formQtySisaRab +'</td>'+
                        '<td>'+formQty +'</td>'+
                        '<td>'+formNote +'</td>'+
                        '<td>'+formMUnitName+'</td>'+
                    '</tr>';
                    $('#dtPermintaanNormal').find('tbody:last').append(tdAdd);
                    // permintaan khusus
                    // dtPermintaanKhusus.row.add([
                    //     formMItemName,
                    //     formQtyRab,
                    //     formQtySisaRab,
                    //     formQty - formQtySisaRab,
                    //     formNote,
                    //     formMUnitName,
                    //     '<input type="text" id="alasan[]" name="alasan[]" required class="form-control">'
                    // ]).draw(false);
                    var tdAdd='<tr>'+
                        '<td>'+formMItemName+'</td>'+
                        '<td>'+formQtyRab +'</td>'+
                        '<td>'+formQtySisaRab +'</td>'+
                        '<td>'+formQty +'</td>'+
                        '<td>'+formNote +'</td>'+
                        '<td>'+formMUnitName+'</td>'+
                        '<td><input type="text" id="alasan[]" name="alasan[]" required class="form-control"></td>'+
                    '</tr>';
                    $('#dtPermintaanKhusus').find('tbody:last').append(tdAdd);
                }
            }
        }
    }
}

async function handleMaterialNo(obj) {
    countMaterial = $('[id^=m_item_no]').length;
    for(i = 0; i < countMaterial; i++){
        materialNo = $('[id^=m_item_no]').eq(i).val();
        formMaterialId = $('[id^=m_item_id]').eq(i);
        id = '';
        await $.ajax({
            type: "GET",
            url: "{{ URL::to('stok_opname/material_by_no') }}", //json get site
            dataType : 'json',
            data: {'no' : materialNo},
            success: function(response){
                arrData = response['data'];
                if(arrData.length > 0) {
                    id = arrData[0]['id'];
                } 
            }
        });

        formMaterialId.val(id);
    }

    handleMaterial();
}
function getOrderNo(order_id){
    $('#div-product-label').html('');
    $('#requestDetail_addrow > tbody').empty();
    // formProjectName = $('[id^=project_name]');
    // formProjectName.empty();
    // formProjectName.append('<option value="">-- Select Project --</option>');
    formRabNo = $('[id^=rab_no]');
    formRabNo.empty();
    formRabNo.append('<option value="">-- Select RAB Number --</option>');
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
    // getOrderProduct(order_id);
    // getProjectName('');
    // show_rab($('[id^=project_name]').val());
}
function cekInvNumber(val){
    formReqNo = $('[id^=req_no]');
    formReqNo.empty();
    formReqNo.append('<option value="">-- Select Inv Request Number --</option>');
    $.ajax({
        type: "GET",
        url: "{{ URL::to('material_request/get_inv_by_label') }}", //json get site
        dataType : 'json',
        data : {no : val},
        success: function(response){
            arrData = response['data'];
            for(i = 0; i < arrData.length; i++){
                formReqNo.append('<option value="'+arrData[i]['id']+'">'+arrData[i]['no']+'</option>');
            }
        }
    });
}
</script>
@endsection