@extends('theme.default')

@section('breadcrumb')
<div class="page-breadcrumb">
    <div class="row">
        <div class="col-5 align-self-center">
            <h4 class="page-title">Form Alat Bantu Kerja Request</h4>
            <div class="d-flex align-items-center">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ URL::to('alat_kerja_request') }}">List Alat Bantu Kerja Request</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Form Alat Bantu Kerja Request</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
</div>
@endsection

@section('content')
<form method="POST" action="{{ URL::to('alat_kerja_request/request') }}" class="form-horizontal">
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
                <!--    <a href="{{ URL::to('alat_kerja_request') }}" class="btn btn-danger btn-sm mb-2">Cancel</a>-->
                <!--    <button id="btnSubmit" onclick="handleSubmit();" type="button" disabled data-toggle="modal" data-target="#modalShowDetail" class="btn btn-info btn-sm mb-2">Konfirmasi</button>-->
                <!--</div>-->
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Request</h4>
                        <div>
                            <button type="button" id="addRow" class="btn btn-success btn-sm mb-2"><i class="fas fa-plus"></i>&nbsp; Add New Detail</button>
                            <button type="button" id="delRow" class="btn btn-danger btn-sm mb-2"><i class="ti-trash"></i>&nbsp; Delete Selected Detail</button>
                        </div>    
                        <div class="table-responsive">
                            <table id="requestDetail_addrow" class="table table-striped table-bordered display" style="width:100%">
                                <thead>
                                    <tr>
                                        <th class="text-center">Alat Bantu Kerja No</th>
                                        <th class="text-center">Alat Bantu Kerja Name</th>
                                        <th class="text-center">Qty Pengajuan</th>
                                        <th class="text-center">Satuan</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                        <div class="form-group">
                            <a href="{{ URL::to('alat_kerja_request') }}" class="btn btn-danger mb-2">Cancel</a>
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
                    <h4 class="modal-title" id="modalAddMaterialLabel1">Konfirmasi Alat Bantu Kerja Request</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                    <h5 class="card-title">Permintaan Khusus <span style="color: red; font-size: 12px;">(perlu dilakukan autorisasi)</span></h5>
                    <div class="table-responsive">
                        <table id="dtPermintaanKhusus" class="table table-bordered">
                            <thead>
                                <tr>
                                    <th class="text-center">Alat Bantu Kerja Name</th>
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
});

$('#addRow').on('click', function() {
    t.row.add([
        '<input type="text" id="m_item_no[]" name="m_item_no[]" class="form-control" onchange="handleMaterialNo(this)" />',
        '<input type="hidden" id="m_item_name[]" name="m_item_name[]" /><select class="form-control select2 custom-select" style="width: 100%; height:32px;" id="m_item_id[]" name="m_item_id[]" required onchange="handleMaterial()"></select>',
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
        formMaterial.append('<option value="">-- Select Alat Bantu Kerja --</option>')
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
        formMaterial.append('<option value="">-- Select Alat Bantu Kerja --</option>')
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
    url: "{{ URL::to('alat_kerja_request/get_material') }}", //json get material
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

function handleSubmit() {
    dtPermintaanNormal.clear().draw(false);
    dtPermintaanKhusus.clear().draw(false);
    let countMaterial = $('[id^=m_item_id]').length;
    for(i = 0; i < countMaterial; i++) {
        formMItemName = $('[id^=m_item_name]').eq(i).val();
        formQty = $('[id^=qty_req]').eq(i).val();
        formMUnitName = $('[id^=unit_name]').eq(i).val();

        dtPermintaanKhusus.row.add([
            formMItemName,
            formQty,
            formMUnitName,
            '<input type="text" id="alasan[]" name="alasan[]" required class="form-control">'
        ]).draw(false);
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