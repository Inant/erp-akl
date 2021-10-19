@extends('theme.default')

@section('breadcrumb')
			<div class="page-breadcrumb">
                <div class="row">
                    <div class="col-5 align-self-center">
                        <h4 class="page-title">Form Pengajuan Transfer Stok</h4>
                        <div class="d-flex align-items-center">
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="{{ URL::to('transfer_stok') }}">List Request</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">Form Pengajuan</li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
@endsection

@section('content')

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
                        <form method="POST" action="{{ URL::to('transfer_stok/create') }}" class="form-horizontal">
                        @csrf
                            <!--<div class="text-right">-->
                            <!--    <a href="{{ URL::to('transfer_stok') }}" class="btn btn-danger btn-sm mb-2">Cancel</a>-->
                            <!--    <button type="submit" class="btn btn-info btn-sm mb-2">Submit</button>-->
                            <!--</div>-->
                            <div class="card">
                                <div class="card-body">
                                    <h4 class="card-title">Request Header</h4>
                                    <div class="form-group row">
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
                                        <label class="col-sm-3 text-right control-label col-form-label">Site/HO Request</label>
                                        <div class="col-sm-9">
                                            <select id="site_name" name="site_name" required class="form-control select2 custom-select" style="width: 100%; height:32px;">
                                                <option value="">--- Select Site/HO ---</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-sm-3 text-right control-label col-form-label">Due Date Penerimaan</label>
                                        <div class="col-sm-9">
                                            <input type="date" id="due_date" name="due_date" class="form-control" required>
                                        </div>
                                    </div>
                                    <h4 class="card-title">Request Detail</h4>
                                    <div>
                                        <button type="button" id="addRow" class="btn btn-success btn-sm mb-2"><i class="fas fa-plus"></i>&nbsp; Add New Detail</button>
                                        <button type="button" id="delRow" class="btn btn-danger btn-sm mb-2"><i class="ti-trash"></i>&nbsp; Delete Selected Detail</button>
                                    </div>    
                                    <div class="table-responsive">
                                        <table id="requestDetail_addrow" class="table table-striped table-bordered display" style="width:100%">
                                            <thead>
                                                <tr>
                                                    <th class="text-center">Material Name</th>
                                                    <th class="text-center">Qty Pengajuan</th>
                                                    <th class="text-center">Satuan</th>
                                                    <th width="200px" class="text-center">Keterangan</th>
                                                </tr>
                                            </thead>
                                        </table>
                                    </div>
                                    <br><br>

                                    <div class="form-group">
                                        <a href="{{ URL::to('transfer_stok') }}" class="btn btn-danger mb-2">Cancel</a>
                                        <button type="submit" class="btn btn-info mb-2">Submit</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                
</div>

<script src="{!! asset('theme/assets/libs/jquery/dist/jquery.min.js') !!}"></script>
<script src="{!! asset('theme/assets/extra-libs/DataTables/datatables.min.js') !!}"></script>
<script>

var t = $('#requestDetail_addrow').DataTable();
var counter = 1;

var listMaterialRab = [];

$(document).ready(function(){

});

$('#addRow').on('click', function() {
    t.row.add([
        '<select class="form-control select2 custom-select" style="width: 100%; height:32px;" id="m_item_id[]" name="m_item_id[]" required onchange="handleMaterial()"></select>',
        '<input type="number" id="qty[]" name="qty[]" step="any" min="0" class="form-control text-right" placeholder="0" required>',
        '<input type="hidden" id="m_unit_id[]" name="m_unit_id[]" /><select disabled class="form-control select2 custom-select" style="width: 100%; height:32px;" id="m_unit_name[]" name="m_unit_name[]" required></select>',
        '<input type="text" id="keterangan[]" name="keterangan[]" class="form-control">',
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
});



function handleMaterial(value){
    countMaterial = $('[id^=m_item_id]').length;
    let arrSelectedMaterial = [];
    for(i = 0; i < countMaterial; i++){
        if($('[id^=m_item_id]').eq(i).val() !== null && $('[id^=m_item_id]').eq(i).val() !== '')
            arrSelectedMaterial.push($('[id^=m_item_id]').eq(i).val());
    }

    for(i = 0; i < countMaterial; i++){
        formMaterial = $('[id^=m_item_id]').eq(i);
        selectedMaterial = $('[id^=m_item_id]').eq(i).val();
        satuan = '', value = '';
        
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
            }     
        });

        $('[id^=m_unit_id]').eq(i).val(satuan);
        $('[id^=m_unit_name]').eq(i).val(satuan);
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
    formSiteName.append('<option value="">-- Select Site/HO --</option>');
    site_id = {{ $site_id }};
    $.ajax({
        type: "GET",
        url: "{{ URL::to('rab/get_site') }}", //json get site
        dataType : 'json',
        data:"town_id=" + site_location_id,
        success: function(response){
            arrData = response['data'];
            for(i = 0; i < arrData.length; i++){
                if( site_id == arrData[i]['id'] )
                    formSiteName.append('<option disabled value="'+arrData[i]['id']+'">'+arrData[i]['name']+'</option>');
                else
                    formSiteName.append('<option value="'+arrData[i]['id']+'">'+arrData[i]['name']+'</option>');
            }
        }
    });
}
</script>

@endsection