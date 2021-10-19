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
                                    <div class="form-group row" hidden>
                                        <label class="col-sm-3 text-right control-label col-form-label">City</label>
                                        <div class="col-sm-9">
                                            <select name="site_location"  onchange="getSiteName(this.value);" class="form-control select2 custom-select" style="width: 100%; height:32px;">
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
                                    <div class="form-group">
                                        <select class="form-control custom-select select2" style="width: 300px; height:32px;" id="item_id"></select>
                                        <button type="button" id="addRow" class="btn btn-success btn-sm mb-2"><i class="fas fa-plus"></i>&nbsp; Add New Detail</button>
                                        <!-- <button type="button" id="delRow" class="btn btn-danger btn-sm mb-2"><i class="ti-trash"></i>&nbsp; Delete Selected Detail</button> -->
                                    </div>      
                                    <div class="table-responsive">
                                        <table id="requestDetail_addrow" class="table table-striped table-bordered display" style="width:100%">
                                            <thead>
                                                <tr>
                                                    <th class="text-center">No Material</th>
                                                    <th class="text-center">Material Name</th>
                                                    <th class="text-center">Qty Pengajuan</th>
                                                    <th class="text-center">Gudang</th>
                                                    <th class="text-center">Satuan</th>
                                                    <th width="200px" class="text-center">Keterangan</th>
                                                    <th></th>
                                                </tr>
                                            </thead>
                                            <tbody></tbody>
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

// var t = $('#requestDetail_addrow').DataTable();
var counter = 1;

var listMaterialRab = [];
var selectedItem=[];
$(document).ready(function(){
    formSiteName = $('[id^=site_name]');
    formSiteName.empty();
    formSiteName.append('<option value="">-- Select Site/HO --</option>');
    site_id = {{ $site_id }};
    $.ajax({
        type: "GET",
        url: "{{ URL::to('rab/get_site') }}", //json get site
        dataType : 'json',
        data:"town_id=1",
        // data:"town_id=1",
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
});

$('#addRow').on('click', function() {
    var item_selected=$('#item_id').val();
    var satuan = '', itemName = '', itemNo = '', unitName='';
    $.each(arrMaterial, function(i, item) {
        if(item_selected == arrMaterial[i]['id']){
            satuan = arrMaterial[i]['m_unit_id'];
            itemName = arrMaterial[i]['name'];
            itemNo = arrMaterial[i]['no'];
            unitName = arrMaterial[i]['m_unit_name'];
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
            '<td>'+itemNo+'</td>'+
            '<td>'+
                '<input type="hidden" id="m_item_id[]" name="m_item_id[]" value="'+item_selected+'" />'+itemName+
            '</td>'+
            // '<td>'+
            //     '<select class="form-control select2 custom-select" style="width: 100%; height:32px;" name="m_item_id[]" required onchange="handleMaterial()"></select>' +
            // '</td>'+
            '<td>'+
                '<input type="number" id="qty[]" name="qty[]" step="any" min="0" class="form-control text-right" placeholder="0" required>' +
            '</td>'+
            '<td>'+
                '<select name="m_warehouse_id[]" id="m_warehouse_id[]" class="form-control" style="width: 100%; height:32px;" required>'+
                    '<option value="">--- Pilih Gudang ---</option>'+
                    '@foreach($gudang as $value)'+
                    '<option value="{{ $value->id}}">{{ $value->name }}</option>'+
                    '@endforeach'+
                '</select>' +
            '</td>'+
            '<td>'+
                '<input type="hidden" id="m_unit_id[]" name="m_unit_id[]" value="'+satuan+'"/><select disabled class="form-control select2" style="width: 100%; height:32px;" id="m_unit_name[]" name="m_unit_name[]" required><option value="'+satuan+'">'+unitName+'</option></select>' +
            '</td>'+
            '<td>'+
                '<input type="text" id="keterangan[]" name="keterangan[]" class="form-control">' +
            '</td>'+
            '<td class="text-center"><button class="btn btn-danger removeOption"><i class="mdi mdi-delete"></i></button></td>'+
        '</tr>';
        $('#requestDetail_addrow').find('tbody:last').append(tdAdd);
    }
    // $('.custom-select').select2();
    // countMaterial = $('[name^=m_item_id]').length;
    // let arrSelectedMaterial = [];
    // for(i = 0; i < countMaterial; i++){
    //     if($('[name^=m_item_id]').eq(i).val() !== null && $('[name^=m_item_id]').eq(i).val() !== '')
    //         arrSelectedMaterial.push($('[name^=m_item_id]').eq(i).val());
    // }

    // for(i = 0; i < countMaterial; i++){
    //     formMaterial = $('[name^=m_item_id]').eq(i);
    //     selectedMaterial = $('[name^=m_item_id]').eq(i).val();
    //     $('[name^=m_item_id]').eq(i).empty();
    //     formMaterial.append('<option value="">-- Select Material --</option>')
    //     $.each(arrMaterial, function(i, item) {
    //         if(selectedMaterial == arrMaterial[i]['id'])
    //             formMaterial.append('<option selected value="'+arrMaterial[i]['id']+'">('+arrMaterial[i]['no']+') '+arrMaterial[i]['name']+'</option>');
    //         else {
    //             if (arrSelectedMaterial.includes(arrMaterial[i]['id'].toString()))
    //                 formMaterial.append('<option disabled value="'+arrMaterial[i]['id']+'">('+arrMaterial[i]['no']+') '+arrMaterial[i]['name']+'</option>');
    //             else
    //                 formMaterial.append('<option value="'+arrMaterial[i]['id']+'">('+arrMaterial[i]['no']+') '+arrMaterial[i]['name']+'</option>');
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
});


function handleMaterial(value){
    var countMaterial = $('[name^=m_item_id]').length;
    let arrSelectedMaterial = [];
    for(var i = 0; i < countMaterial; i++){
        if($('[name^=m_item_id]').eq(i).val() !== null && $('[name^=m_item_id]').eq(i).val() !== ''){
            arrSelectedMaterial.push($('[name^=m_item_id]').eq(i).val());
        }
    }
    // for(var i = 0; i < countMaterial; i++){
    //     formMaterial = $('[name^=m_item_id]').eq(i);
    //     selectedMaterial = $('[name^=m_item_id]').eq(i).val();
    //     var satuan = '', no = '';
    //     // var selectedMaterial = $('[name^=m_item_id]').eq(i).val();
    //     $('[name^=m_item_id]').eq(i).empty();
    //     formMaterial.append('<option value="">-- Select Material --</option>')
    //     $.each(arrMaterial, function(j, item) {
    //         if(selectedMaterial == arrMaterial[i]['id'])
    //             formMaterial.append('<option selected value="'+arrMaterial[j]['id']+'">'+arrMaterial[j]['name']+'</option>');
    //         else {
    //             if (arrSelectedMaterial.includes(arrMaterial[j]['id'].toString()))
    //                 formMaterial.append('<option disabled value="'+arrMaterial[j]['id']+'">'+arrMaterial[j]['name']+'</option>');
    //             else
    //                 formMaterial.append('<option value="'+arrMaterial[j]['id']+'">'+arrMaterial[j]['name']+'</option>');
    //         }
    //         if(selectedMaterial == arrMaterial[j]['id']){
    //             satuan = arrMaterial[j]['m_unit_id'];
    //             no = arrMaterial[j]['no'];
    //         }     
    //     });
    //     if ($('[name^=m_item_id]').eq(i).val() !== '' && $('[id^=m_item_no]').eq(i).val() !== '') {
    //         $('[id^=m_unit_id]').eq(i).val(satuan);
    //         $('[id^=m_unit_name]').eq(i).val(satuan);
    //         $('[id^=m_item_no]').eq(i).val(no);
    //     }else if($('[id^=m_item_no]').eq(i).val() !== ''){
    //         $('[id^=m_unit_id]').eq(i).val(satuan);
    //         $('[id^=m_unit_name]').eq(i).val(satuan);
    //         // $('[id^=m_item_no]').eq(i).val(no);
    //     }else{
    //         $('[id^=m_unit_id]').eq(i).val('');
    //         $('[id^=m_unit_name]').eq(i).val('');
    //         $('[id^=m_item_no]').eq(i).val('');
    //     }
    // }

    for(i = 0; i < countMaterial; i++){
        formMaterial = $('[name^=m_item_id]').eq(i);
        selectedMaterial = $('[name^=m_item_id]').eq(i).val();
        satuan = '', no = '';
        
        $('[name^=m_item_id]').eq(i).empty();
        formMaterial.append('<option value="">-- Select Material --</option>')
        $.each(arrMaterial, function(i, item) {
            if(selectedMaterial == arrMaterial[i]['id'])
                formMaterial.append('<option selected value="'+arrMaterial[i]['id']+'">('+arrMaterial[i]['no']+') '+arrMaterial[i]['name']+'</option>');
            else {
                if (arrSelectedMaterial.includes(arrMaterial[i]['id'].toString()))
                    formMaterial.append('<option disabled value="'+arrMaterial[i]['id']+'">('+arrMaterial[i]['no']+') '+arrMaterial[i]['name']+'</option>');
                else
                    formMaterial.append('<option value="'+arrMaterial[i]['id']+'">('+arrMaterial[i]['no']+') '+arrMaterial[i]['name']+'</option>');
            }

            if(selectedMaterial == arrMaterial[i]['id']){
                satuan = arrMaterial[i]['m_unit_id'];
                no = arrMaterial[i]['no'];
            }     
        });

        $('[id^=m_unit_id]').eq(i).val(satuan);
        $('[id^=m_unit_name]').eq(i).val(satuan);
        // $('[id^=m_item_no]').eq(i).val(no);
    }
}

// $('#requestDetail_addrow tbody').on('click', 'tr', function() {
//     if ($(this).hasClass('selected')) {
//         $(this).removeClass('selected');
//     } else {
//         t.$('tr.selected').removeClass('selected');
//         $(this).addClass('selected');
//     }
// });

// $('#delRow').click(function() {
//     t.row('.selected').remove().draw(false);
// });

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
    formSiteName.append('<option value="">-- Select Site/HO --</option>');
    site_id = {{ $site_id }};
    $.ajax({
        type: "GET",
        url: "{{ URL::to('rab/get_site') }}", //json get site
        dataType : 'json',
        // data:"town_id=" + site_location_id,
        data:"town_id=" + {{$site_id}},
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
// function handleMaterialNo() {
//     var countMaterial = $('[id^=m_item_no]').length;
//     for(var i = 0; i < countMaterial; i++){
//         var materialNo = $('[id^=m_item_no]').eq(i).val();
//         var formMaterialId = $('[name^=m_item_id]').eq(i);
//         console.log(materialNo)
//         // if (materialNo != '') {
//             id = '';
//             $.ajax({
//                 type: "GET",
//                 async : false,
//                 url: "{{ URL::to('stok_opname/material_by_no') }}", //json get site
//                 dataType : 'json',
//                 data: {'no' : materialNo},
//                 success: function(response){
//                     arrData = response['data'];
//                     if(arrData.length > 0) {
//                         id = arrData[0]['id'];
//                     } 
//                 }
//             });
//             formMaterialId.val(id).trigger("change");
//         // }else{
//         //     formMaterialId.val('').change();
//         // }
//     }

//     // handleMaterial();
// }
</script>

@endsection