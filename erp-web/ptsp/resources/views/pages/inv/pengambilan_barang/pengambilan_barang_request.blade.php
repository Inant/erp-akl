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
<form method="POST" action="{{ URL::to('material_request/request') }}" class="form-horizontal">
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
                <!--<div class="text-right">-->
                <!--    <a href="{{ URL::to('material_request') }}" class="btn btn-danger btn-sm mb-2">Cancel</a>-->
                <!--    <button id="btnSubmit" onclick="handleSubmit();" type="button" disabled data-toggle="modal" data-target="#modalShowDetail" class="btn btn-info btn-sm mb-2">Konfirmasi</button>-->
                <!--</div>-->
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
                            <label class="col-sm-3 text-right control-label col-form-label">Kavling</label>
                            <div class="col-sm-9">
                                <select id="project_name" name="project_name" required class="form-control select2 custom-select" style="width: 100%; height:32px;" onchange="getRab(this.value);">
                                    <option value="">--- Select Kavling ---</option>
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
                        <h4 class="card-title">Request Detail</h4>
                        <div>
                            <button type="button" disabled id="addRow" class="btn btn-success btn-sm mb-2"><i class="fas fa-plus"></i>&nbsp; Add New Detail</button>
                            <button type="button" disabled id="delRow" class="btn btn-danger btn-sm mb-2"><i class="ti-trash"></i>&nbsp; Delete Selected Detail</button>
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
                                        <th class="text-center">Satuan</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                        <div class="form-group">
                            <a href="{{ URL::to('material_request') }}" class="btn btn-danger mb-2">Cancel</a>
                            <button id="btnSubmit" onclick="handleSubmit();" type="button" disabled data-toggle="modal" data-target="#modalShowDetail" class="btn btn-info mb-2">Konfirmasi</button>
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
                                    <th class="text-center">Satuan</th>
                                </tr>
                            </thead>                                      
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
                                    <th class="text-center">Satuan</th>
                                    <th class="text-center">Alasan</th>
                                </tr>
                            </thead>                                      
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

var t = $('#requestDetail_addrow').DataTable();
var dtPermintaanNormal = $('#dtPermintaanNormal').DataTable();
var dtPermintaanKhusus = $('#dtPermintaanKhusus').DataTable();

var counter = 1;

var listMaterialRab = [];

$(document).ready(function(){
    let site_id = {{ $site_id }};
    getProjectName(site_id);
});

$('#addRow').on('click', function() {
    t.row.add([
        '<input type="text" id="m_item_no[]" name="m_item_no[]" class="form-control" onchange="handleMaterialNo(this)" />',
        '<input type="hidden" id="m_item_name[]" name="m_item_name[]" /><select class="form-control select2 custom-select" style="width: 100%; height:32px;" id="m_item_id[]" name="m_item_id[]" required onchange="handleMaterial()"></select>',
        '<input type="number" readonly id="qty_rab[]" name="qty_rab[]" class="form-control text-right" value="0" required>',
        '<input type="number" readonly id="qty_sisa_rab[]" name="qty_sisa_rab[]" class="form-control text-right" value="0" required>',
        '<input type="number" id="qty_req[]" name="qty[]" step="any" min="0" class="form-control text-right" placeholder="0" required>',
        '<input type="hidden" id="unit_name[]" name="unit_name[]" /><input type="hidden" id="m_unit_id[]" name="m_unit_id[]" /><select disabled class="form-control select2 custom-select" style="width: 100%; height:32px;" id="m_unit_name[]" name="m_unit_name[]" required></select>',
    ]).draw(false);

    countMaterial = $('[id^=m_item_id]').length;
    let arrSelectedMaterial = [];
    for(i = 0; i < countMaterial; i++){
        if($('[id^=m_item_id]').eq(i).val() !== null && $('[id^=m_item_id]').eq(i).val() !== '')
            arrSelectedMaterial.push($('[id^=m_item_id]').eq(i).val());
    }

    for(i = 0; i < countMaterial; i++){
        formMaterial = $('[id^=m_item_id]').eq(i);
        selectedMaterial = $('[id^=m_item_id]').eq(i).val();
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
        });

        formUnit = $('[id^=m_unit_name]').eq(i);
        selectedUnit = $('[id^=m_unit_name]').eq(i).val();
        $('[id^=m_unit_name]').eq(i).empty();
        formUnit.append('<option value="">-- Select Unit --</option>')
        $.each(arrUnit, function(i, item) {
            if(selectedUnit == arrUnit[i]['id'])
                formUnit.append('<option selected value="'+arrUnit[i]['id']+'">'+arrUnit[i]['name']+'</option>');
            else
                formUnit.append('<option value="'+arrUnit[i]['id']+'">'+arrUnit[i]['name']+'</option>');
        });
    }

    document.getElementById("btnSubmit").disabled = false;
});



function handleMaterial(){
    countMaterial = $('[id^=m_item_id]').length;
    let arrSelectedMaterial = [];
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

$('#requestDetail_addrow tbody').on('click', 'tr', function() {
    if ($(this).hasClass('selected')) {
        $(this).removeClass('selected');
    } else {
        t.$('tr.selected').removeClass('selected');
        $(this).addClass('selected');
    }
});

$('#delRow').click(function() {
    t.row('.selected').remove().draw(false);

    let countMaterial = $('[id^=m_item_id]').length;
    if (countMaterial == 0)
        document.getElementById("btnSubmit").disabled = true;
});

var arrMaterial = [];
$.ajax({
    type: "GET",
    url: "{{ URL::to('material_request/get_material') }}", //json get material
    dataType : 'json',
    success: function(response){
        arrMaterial = response['data'];
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
    if(obj.value !== '') {
        document.getElementById("addRow").disabled = false;
        document.getElementById("delRow").disabled = false;
    } else {
        document.getElementById("addRow").disabled = true;
        document.getElementById("delRow").disabled = true;
    }

    t.clear().draw(false);

    // set listMaterialRab
    $.ajax({
        type: "GET",
        url: "{{ URL::to('material_request/material_rab') }}", //json get site
        dataType : 'json',
        data:"rab_id=" + obj.value,
        success: function(response){
            arrData = response['data'];
            listMaterialRab = arrData;
        }
    });
}

function handleSubmit() {
    dtPermintaanNormal.clear().draw(false);
    dtPermintaanKhusus.clear().draw(false);
    let countMaterial = $('[id^=m_item_id]').length;
    for(i = 0; i < countMaterial; i++) {
        formMItemName = $('[id^=m_item_name]').eq(i).val();
        formQtyRab = parseFloat($('[id^=qty_rab]').eq(i).val());
        formQtySisaRab = parseFloat($('[id^=qty_sisa_rab]').eq(i).val());
        formQty = parseFloat($('[id^=qty_req]').eq(i).val());
        formMUnitName = $('[id^=unit_name]').eq(i).val();

        // normal
        if (formQtySisaRab >= formQty) {
            dtPermintaanNormal.row.add([
                formMItemName,
                formQtyRab,
                formQtySisaRab,
                formQty,
                formMUnitName
            ]).draw(false);
        } else { 
            if (formQtySisaRab == 0) {
                // khusus
                dtPermintaanKhusus.row.add([
                    formMItemName,
                    formQtyRab,
                    formQtySisaRab,
                    formQty,
                    formMUnitName,
                    '<input type="text" id="alasan[]" name="alasan[]" required class="form-control">'
                ]).draw(false);
            } else {
                // permintaan normal
                dtPermintaanNormal.row.add([
                    formMItemName,
                    formQtyRab,
                    formQtySisaRab,
                    formQtySisaRab,
                    formMUnitName
                ]).draw(false);
                // permintaan khusus
                dtPermintaanKhusus.row.add([
                    formMItemName,
                    formQtyRab,
                    formQtySisaRab,
                    formQty - formQtySisaRab,
                    formMUnitName,
                    '<input type="text" id="alasan[]" name="alasan[]" required class="form-control">'
                ]).draw(false);
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
</script>
@endsection