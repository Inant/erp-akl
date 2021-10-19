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
                        <form method="POST" action="{{ URL::to('transfer_stok/create_warehouse') }}" class="form-horizontal">
                        @csrf
                            <!--<div class="text-right">-->
                            <!--    <a href="{{ URL::to('transfer_stok') }}" class="btn btn-danger btn-sm mb-2">Cancel</a>-->
                            <!--    <button type="submit" class="btn btn-info btn-sm mb-2">Submit</button>-->
                            <!--</div>-->
                            <div class="card">
                                <div class="card-body">
                                    <h4 class="card-title">Request Header</h4>
                                    <div class="form-group row">
                                        <label class="col-sm-3 text-right control-label col-form-label">Dari Gudang</label>
                                        <div class="col-sm-9">
                                            <select name="m_warehouse_id" id="m_warehouse_id" onchange="getGudang()" class="form-control select2 custom-select" style="width: 100%; height:32px;" required>
                                                <option value="">--- Pilih Gudang ---</option>
                                                @foreach($gudang as $value)
                                                <option value="{{ $value->id}}">{{ $value->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-sm-3 text-right control-label col-form-label">Ke Gudang</label>
                                        <div class="col-sm-9">
                                            <select name="m_warehouse_id2" id="m_warehouse_id2" class="form-control select2 custom-select" style="width: 100%; height:32px;" required>
                                                <option value="">--- Pilih Gudang ---</option>
                                                @foreach($gudang as $value)
                                                <option value="{{ $value->id}}">{{ $value->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-sm-3 text-right control-label col-form-label">Tanggal Jatuh Tempo Pengiriman</label>
                                        <div class="col-sm-9">
                                            <input type="date" id="due_date_receive" name="due_date_receive" required class="form-control">
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
                                                    <th class="text-center">Material No</th>
                                                    <th class="text-center">Material Name</th>
                                                    <!-- <th class="text-center">Stok</th> -->
                                                    <th class="text-center">Qty Pengajuan</th>
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
var listStockSite = [];
var listStockRestSite = [];

// $(document).ready(async function(){
//     let site_id = {{ $site_id }};
//     // getProjectName(site_id);
//     await $.ajax({
//         type: "GET",
//         url: "{{ URL::to('inventory/stok_json') }}", //json get site
//         dataType : 'json',
//         success: function(response){
//             arrData = response['data'];
//             listStockSite = arrData;
//         }
//     });
// });
var arrMaterial = [];
var arrUnit = [];
$(document).ready(function(){
    // $.ajax({
    //     type: "GET",
    //     url: "{{ URL::to('inventory/get_item_stock') }}", //json get material
    //     dataType : 'json',
    //     async : true,
    //     success: function(response){
    //         arrMaterial = response['data'];
    //     }
    // });
    $.ajax({
        type: "GET",
        async : true,
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


    $.ajax({
        type: "GET",
        url: "{{ URL::to('rab/get_all_m_unit') }}", //json get site
        dataType : 'json',
        async : true,
        success: function(response){
            arrUnit = response['data'];    
        }
    });

    // $.ajax({
    //     type: "GET",
    //     url: "{{ URL::to('inventory/stok_json') }}", //json get site
    //     dataType : 'json',
    //     async : true,
    //     success: function(response){
    //         arrData = response['data'];
    //         listStockSite = arrData;
    //     }
    // });
});
var selectedItem=[];
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
            '<td>'+
                '<input type="number" id="qty[]" name="qty[]" step="any" min="0" class="form-control text-right" placeholder="0" required>' +
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
    // $('.custom-select').select2();
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
    var warehouse_id=$('#m_warehouse_id').val();
    countMaterial = $('[name^=m_item_id]').length;
    let arrSelectedMaterial = [];
    for(i = 0; i < countMaterial; i++){
        if($('[name^=m_item_id]').eq(i).val() !== null && $('[name^=m_item_id]').eq(i).val() !== '')
            arrSelectedMaterial.push($('[name^=m_item_id]').eq(i).val());
    }

    for(i = 0; i < countMaterial; i++){
        formMaterial = $('[name^=m_item_id]').eq(i);
        selectedMaterial = $('[name^=m_item_id]').eq(i).val();
        satuan = '', value = '';
        // stok = 0;
        // listStockSite.map((item, obj) => {
        //     if (item.m_item_id == selectedMaterial && item.m_warehouse_id == warehouse_id && item.type == 'STK_NORMAL'){
        //         stok += parseInt(item.stok);
        //     }
        // });
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
            }     
        });
        // $('[id^=stok]').eq(i).val(stok);
        $('[id^=m_unit_id]').eq(i).val(satuan);
        $('[id^=m_unit_name]').eq(i).val(satuan);
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

function getGudang(){
    // t.clear().draw(false);
    $('#requestDetail_addrow > tbody').empty();
}
// function cekQty(){
//     var qty = $('[id^=qty]');
//     var stok = $('[id^=stok]');
//     for(var i = 0; i < qty.length; i++){
//         var total_req=qty.eq(i).val();
//         var total=stok.eq(i).val();
//         if(parseFloat(total_req) > parseFloat(total)){
//             qty.eq(i).val('');
//             alert('inputan melebihi request yang ada');
//         }
//     }
// }
</script>

@endsection