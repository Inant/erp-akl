@extends('theme.default')

@section('breadcrumb')
<div class="page-breadcrumb">
    <div class="row">
        <div class="col-5 align-self-center">
            <h4 class="page-title">Form Permintaan Material Penunjang</h4>
            <div class="d-flex align-items-center">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ URL::to('material_request/material_support') }}">List Permintaan Material Penunjang</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Form Permintaan Material Penunjang</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
</div>
@endsection

@section('content')
<form method="POST" action="{{ URL::to('material_request/request_material_support') }}" class="form-horizontal">
    @csrf
    <div class="container-fluid">
        <br>
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
                <!-- <div class="text-right"> -->
                    <!-- <a href="{{ URL::to('material_request') }}" class="btn btn-danger btn-sm mb-2">Cancel</a> -->
                    <!-- <button id="btnSubmit" disabled class="btn btn-info btn-sm mb-2">Simpan</button> -->
                <!-- </div> -->
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Request Header</h4>
                        <div class="form-group row">
                            <label class="col-sm-3 text-right control-label col-form-label">No Order Instalasi</label>
                            <div class="col-sm-9">
                                <select name="install_order_id"  onchange="handleRequestWork(this);" class="form-control select2 custom-select" style="width: 100%; height:32px;">
                                    <option value="">--- Select Order No ---</option>
                                    @if($order_list != null)
                                    @foreach($order_list as $value)
                                        
                                        <option value="{{ $value['id'] }}">{{ $value['no'] }}</option>
                                        
                                    @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-3 text-right control-label col-form-label">Product Label</label>
                            <div class="col-sm-9 form-inline" id="div-product-label">
                                
                            </div>
                        </div>
                        <!-- <div class="form-group row">
                            <label class="col-sm-3 text-right control-label col-form-label">RAB Number</label>
                            <div class="col-sm-9">
                                <select id="rab_no" name="rab_no" required class="form-control select2 custom-select" style="width: 100%; height:32px;" onchange="handleRab(this);">
                                    <option value="">--- Select RAB Number ---</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="form-group row">
                            <label class="col-sm-3 text-right control-label col-form-label">Nomor Permintaan</label>
                            <div class="col-sm-9">
                                <select id="request_id" name="request_id" required class="form-control select2 custom-select" style="width: 100%; height:32px;" onchange="handleRequestWork(this);">
                                    <option value="">--- Pilih Nomor Permintaan ---</option>
                                </select>
                            </div>
                        </div> -->
                        
                        <div class="table-responsive">
                            <h4 class="card-title">Tambahkan Material</h4>
                            <button onclick="addProductOrder()" type="button" class="btn btn-info" id="addRow" disabled>tambah</button>
                            <br><br>
                            <table class="table table-bordered table-striped" id="detail-order">
                                <thead>
                                        <tr>
                                            <th class="text-center">Material No</th>
                                            <th class="text-center">Material Name</th>
                                            <th class="text-center">Gudang</th>
                                            <th class="text-center">Stok</th>
                                            <th class="text-center">Volume</th>
                                            <th class="text-center">Satuan</th>
                                            <th class="text-center">Tipe Stok</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                <tbody>
                                    
                                </tbody>
                            </table>
                        </div>
                        <br><br>
                        <div class="form-group">
                            <button id="btnSubmit" disabled class="btn btn-success">Simpan</button>
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

var arrMaterial = [];
var warehouse=[];
var listStockSite = [];
$(document).ready(function(){
    $.ajax({
        type: "GET",
        url: "{{ URL::to('inventory/stok_json') }}", //json get site
        dataType : 'json',
        async : false,
        success: function(response){
            listStockSite = response['data'];
        }
    });
    $.ajax({
        type: "GET",
        url: "{{ URL::to('material_request/get_material_without_atk') }}", //json get material
        dataType : 'json',
        async : false,
        success: function(response){
            arrMaterial = response['data'];
        }
    });
    $.ajax({
            type: "GET",
            url: "{{ URL::to('master_warehouse/get_warehouse_by_site/'.$site_id) }}", //json get site
            dataType : 'json',
            async : false,
            success: function(response){
                arrData = response['data'];
                warehouse = arrData;
            }
    });
});
function addProductOrder(){
    var option='<option value="">Pilih Material</option>';
    
    for (var i = 0; i < arrMaterial.length; i++) {
        option+='<option value="'+arrMaterial[i]['id']+'">('+arrMaterial[i]['no']+') '+arrMaterial[i]['name']+'</option>';
    }
    var option_warehouse='<option value="">Pilih Gudang</option>';
    for (var i = 0; i < warehouse.length; i++) {
        option_warehouse+='<option value="'+warehouse[i]['id']+'">'+warehouse[i]['name']+'</option>';
    }
    var tdAdd='<tr>'+
        '<td><input type="" id="m_item_no[]" name="m_item_no[]" readonly class="form-control"/></td>'+
        '<td><select name="m_item_id[]" class="form-control select2 custom-select" onchange="cekItem()">'+option+'</select></td>'+
        '<td><select id="m_warehouse_id[]" required name="m_warehouse_id[]" onchange="cekItemStok()" class="form-control">'+option_warehouse+'</select></td>'+
        '<td><input id="stok[]" name="stok[]" class="form-control text-right" type="text" readonly/></td>'+
        '<td><input id="amount[]" required name="amount[]" onkeyup="cekVolumeItem()" class="form-control text-right" type="number"/></td>'+
        '<td><input type="hidden" id="m_unit_id[]" name="m_unit_id[]" /><input class="form-control" type="" id="m_unit_name[]" name="m_unit_name[]" readonly /></td>'+
        '<td><select name="type_stok[]" onchange="cekTypeStok()" class="form-control" id="type_stok[]"><option value="STK_NORMAL">Stok Normal</option><option value="TRF_STK">Stok Transfer</option></select></td>'+
        '<td><button type="button" class="btn btn-sm btn-danger removeOption"><i class="mdi mdi-delete"></i></button></td>'+
    '</tr>';
    $('#detail-order').find('tbody:last').append(tdAdd);
    // console.log(total_produk);
    $('.custom-select').select2();
}
$("#detail-order").on("click", ".removeOption", function(event) {
    event.preventDefault();
    $(this).closest("tr").remove();
});
function cekItem(){
    var id = $('[name^=m_item_id]');
    var stok = $('[id^=stok]');
    var item_no = $('[id^=m_item_no]');
    var warehouse_id = $('[id^=m_warehouse_id]');
    var unit_id = $('[id^=m_unit_id]');
    var unit_name = $('[id^=m_unit_name]');
    for(var i = 0; i < id.length; i++){
        var m_item_id=id.eq(i).val();
        var m_item_no='', m_unit_id='', m_unit_name='';
        arrMaterial.map((item, obj) => {
            if (item.id == m_item_id){
                m_item_no=item.no;
                m_unit_id=item.m_unit_id;
                m_unit_name=item.m_unit_name;
            }
        });
        item_no.eq(i).val(m_item_no);
        unit_id.eq(i).val(m_unit_id);
        unit_name.eq(i).val(m_unit_name);
    }
    cekItemStok();
}
function cekItemStok(){
    var id = $('[name^=m_item_id]');
    var stok = $('[id^=stok]');
    var item_no = $('[id^=m_item_no]');
    var warehouse_id = $('[id^=m_warehouse_id]');
    for(var i = 0; i < id.length; i++){
        var m_item_id=id.eq(i).val();
        var amount_stok=0;
        var m_warehouse_id=warehouse_id.eq(i).val();
        listStockSite.map((item, obj) => {
            if (item.m_item_id == m_item_id && item.m_warehouse_id == m_warehouse_id){
                amount_stok=item.stok;
            }
        });
        
        stok.eq(i).val(amount_stok);
    }
}

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

// function getRab(project_id){
//     formRabNo = $('[id^=rab_no]');
//     formRabNo.empty();
//     formRabNo.append('<option value="">-- Select RAB Number --</option>');
//     $.ajax({
//         type: "GET",
//         url: "{{ URL::to('rab/get_rab_by_project_id') }}", //json get site
//         dataType : 'json',
//         data:"project_id=" + project_id,
//         success: function(response){
//             arrData = response['data'];
//             for(i = 0; i < arrData.length; i++){
//                 formRabNo.append('<option value="'+arrData[i]['rab_id']+'">'+arrData[i]['rab_no']+'</option>');
//             }
//         }
//     });
//     document.getElementById("btnSubmit").disabled = true;
// }


function handleProjectWork(obj) {  
    document.getElementById("btnSubmit").disabled = true;
}
var project_req_dev=[];
function handleRequestWork(obj) {
    $('#div-product-label').html('');
    var options='';
    $.ajax({
        type: "GET",
        url: "{{ URL::to('material_request/get_label_install_order') }}"+'/'+obj.value, //json get site
        dataType : 'json',
        success: function(response){
            arrData = response['data'];
            for(i = 0; i < arrData.length; i++){
                options+='<div class="custom-control custom-checkbox">'+
                                            // '<input type="checkbox" class="custom-control-input" id="customCheck'+i+'" name="prod_sub_id[]" value="'+arrData[i]['id']+'" checked onclick="return false;">'+
                                            '<input type="checkbox" class="custom-control-input" id="customCheck'+i+'" name="prod_sub_id[]" value="'+arrData[i]['id']+'" checked>'+
                                            '<label class="custom-control-label" for="customCheck'+i+'">'+arrData[i]['no']+'</label>'+
                                        '</div>&nbsp;';
                $('#div-product-label').html(options);
            }
        }
    });
    if(obj.value !== '') {
        document.getElementById("addRow").disabled = false;
    } else {
        document.getElementById("addRow").disabled = true;
    }
    
    if(obj.value != '') {
        document.getElementById("btnSubmit").disabled = false;
    }else{
        document.getElementById("btnSubmit").disabled = true;
    }
}


// function getOrderNo(order_id){
//     $('#detail-order > tbody').empty();
//     formRabNo = $('[id^=rab_no]');
//     formRabNo.empty();
//     formRabNo.append('<option value="">-- Select RAB Number --</option>');
//     $.ajax({
//         type: "GET",
//         url: "{{ URL::to('rab/get_rab_by_order_id') }}", //json get site
//         dataType : 'json',
//         data:"order_id=" + order_id,
//         success: function(response){
//             arrData = response['data'];
//             for(i = 0; i < arrData.length; i++){
//                 formRabNo.append('<option value="'+arrData[i]['id']+'">'+arrData[i]['no']+'</option>');
//             }
//         }
//     });
//     // getOrderProduct(order_id);
//     // getProjectName('');
//     // show_rab($('[id^=project_name]').val());
// }
// function handleRab(obj) {
//     document.getElementById("btnSubmit").disabled = true;
    
//     formRequestWork = $('[id^=request_id]');
//     formRequestWork.empty();
//     formRequestWork.append('<option value="">-- Pilih Nomor Permintaan --</option>');

//     // get project header
//     $.ajax({
//         type: "GET",
//         url: "{{ URL::to('material_request/get-request-work') }}"+'/'+obj.value, //json get site
//         dataType : 'json',
//         success: function(response){
//             arrData = response['data'];
//             for(i = 0; i < arrData.length; i++){
//                 formRequestWork.append('<option value="'+arrData[i]['id']+'">'+arrData[i]['no']+'</option>');
//             }
//         }
//     });
    
// }
function cekVolumeItem(){
    var id = $('[name^=m_item_id]');
    var amount = $('[id^=amount]');
    var stok = $('[id^=stok]');
    var warehouse_id = $('[id^=m_warehouse_id]');
    var type_stok = $('[id^=type_stok]');
    var tempAll=[];
    for(var i = 0; i < id.length; i++){
        var m_item_id=id.eq(i).val();
        var amount_item=amount.eq(i).val();
        var m_warehouse_id=warehouse_id.eq(i).val();
        var type_stk=type_stok.eq(i).val();
        
        if (m_item_id != '' && m_warehouse_id != '' && amount_item != ''){
            var is_there=false;
            var index=0;
            $.each(tempAll, function(j, item){
                if (m_item_id == item.m_item_id && m_warehouse_id == item.m_warehouse_id) {
                    is_there=true;
                    index=j;                    
                }
            })        
            if (is_there == true) {
                tempAll[index]['amount']=parseFloat(tempAll[index]['amount']) + parseFloat(amount_item) 
            }else{
                tempAll.push({'m_item_id' : m_item_id , 'm_warehouse_id' : m_warehouse_id , 'amount' : amount_item})
            }
            var stok_site=0;
            $.each(listStockSite, function(j, item){
                if (m_item_id == item.m_item_id && m_warehouse_id == item.m_warehouse_id && item.type == type_stk) {
                    stok_site=item.stok
                }
            })
            
            $.each(tempAll, function(j, item){
                if (m_item_id == item.m_item_id && m_warehouse_id == item.m_warehouse_id) {
                    console.log(item.amount)
                    if (parseFloat(item.amount) > parseFloat(stok_site)) {
                        amount.eq(i).val('');
                        amount.eq(i).focus();
                        alert('jumlah yang anda input melebihi stok yang ada')
                    }             
                }
            })    
        }
        
    }
    
}
function cekTypeStok(){
    var type_stok = $('[id^=type_stok]');
    var item=$('[name^=m_item_id]');
    var warehouse_id=$('[name^=m_warehouse_id]');
    var stok=$('[name^=stok]');
    for(var i = 0; i < type_stok.length; i++){
        var m_item_id=item.eq(i).val();
        var m_warehouse_id=warehouse_id.eq(i).val();
        var type_stk=type_stok.eq(i).val();
        var amount_stok=0;
        listStockSite.map((item, obj) => {
            
            if (item.m_item_id == m_item_id && item.m_warehouse_id == m_warehouse_id && item.type == type_stk){
                amount_stok=item.stok;
            }
        });
        stok.eq(i).val(amount_stok);
    }
    cekVolumeItem();
}
</script>
@endsection