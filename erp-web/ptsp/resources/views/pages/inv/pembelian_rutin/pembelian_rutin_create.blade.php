@extends('theme.default')

@section('breadcrumb')
			<div class="page-breadcrumb">
                <div class="row">
                    <div class="col-5 align-self-center">
                        <h4 class="page-title">Pembelian</h4>
                        <div class="d-flex align-items-center">
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="{{ URL::to('pembelian') }}">Pembelian</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">Input Pembelian</li>
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
                        <form method="POST" action="{{ URL::to('pembelian/create') }}" class="form-horizontal">
                        @csrf
                            <div class="card">
                                <div class="card-body">
                                    <h4 class="card-title">Rekomendasi Pembelian</h4>
                                    <div class="table-responsive">
                                        <table id="zero_config" class="table table-striped table-bordered">
                                            <thead>
                                                <tr>
                                                    <th class="text-center"  width="15px"><input id="select_all" type="checkbox" /></th>
                                                    <th class="text-center">Material No</th>
                                                    <th class="text-center">Material Name</th>
                                                    <th class="text-center">Volume</th>
                                                    <th class="text-center">Stok Site</th>
                                                    <th class="text-center">Satuan</th>
                                                    <th class="text-center">Rencana Pakai</th>
                                                    <th class="text-center">Lead Time Ordering</th>
                                                    <th class="text-center">Due Date Ordering</th>
                                                    <th class="text-center">Status</th>
                                                    <th class="text-center">Suggestion Type</th>
                                                </tr>
                                            </thead>
                                        </table>
                                    </div>
                                    <div class="text-right" style="margin-top:10px;">
                                        <button id="btn_add_to_selected" type="button" onclick="addToSelected();" class="btn btn-success btn-sm mb-2">Tambah ke Pembelian</button>
                                    </div>
                                    <h4 class="card-title">Form Pembelian</h4>
                                    <div>
                                        <button type="button" id="addRow" class="btn btn-success btn-sm mb-2"><i class="fas fa-plus"></i>&nbsp; Tambah Pembelian Manual</button>
                                    </div>
                                    <br/>
                                    <div class="form-group row">
                                        <label class="col-sm-3 text-right control-label col-form-label">Supplier</label>
                                        <div class="col-sm-9">
                                            <select id="suppl_single" name="suppl_single" required class="form-control select2 custom-select" style="width: 100%; height:32px;"></select>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-sm-3 text-right control-label col-form-label">Cara Bayar</label>
                                        <div class="col-sm-9">
                                            <select id="cara_bayar_single" name="cara_bayar_single" required class="form-control select2 custom-select" style="width: 100%; height:32px;">
                                                <option value="">-- Select Cara Bayar --</option>
                                                <option value="cash">Cash</option><option value="credit">Credit</option>
                                            </select>
                                        </div>
                                    </div>
                                    <br/>
                                    <div class="table-responsive">
                                        <table id="dt_temp" class="table table-striped table-bordered">
                                            <thead>
                                                <tr>
                                                    <th class="text-center">Material No</th>
                                                    <th class="text-center">Material Name</th>
                                                    <th class="text-center">Volume</th>
                                                    <th class="text-center">Stok Site</th>
                                                    <th class="text-center">Satuan</th>
                                                    <th class="text-center">Best Price/Supplier</th>
                                                    <!-- <th class="text-center">Supplier</th> -->
                                                    <th class="text-center">Harga Supplier</th>
                                                    <!-- <th class="text-center">Cara Bayar</th> -->
                                                    <th class="text-center">Keterangan</th>
                                                </tr>
                                            </thead>
                                        </table>
                                    </div>
                                    <br/>
                                    <div class="text-left">
                                        <a href="{{ URL::to('pembelian') }}"><button class="btn btn-danger mb-2">Cancel</button></a>
                                        <button type="submit" class="btn btn-primary mb-2">Submit</button>
                                    </div>
                                </div>
                            </div>
                            <div id="selected_material">
                            </div>
                        </form>
                    </div>
                </div>
                
</div>

<script src="{!! asset('theme/assets/libs/jquery/dist/jquery.min.js') !!}"></script>
<script src="{!! asset('theme/assets/extra-libs/DataTables/datatables.min.js') !!}"></script>
<script>
var dt_temp = $('#dt_temp').DataTable();
var arrMaterialPembelianRutin = [];
var arrPoCanceled = [];
var selected = [];
var arrSuppl = [];
var tempArrSelected = [];

// List Stock
var listStockSite = [];

$(document).ready(async function(){
    // Get Stock
    $.ajax({
        type: "GET",
        url: "{{ URL::to('inventory/stok_json') }}", //json get site
        dataType : 'json',
        success: function(response){
            arrData = response['data'];
            listStockSite = arrData;
        }
    });

    // console.log(arrMaterialPembelianRutin);
    t = $('#zero_config').DataTable();
    t.clear().draw(false);
    // Suggest order dari RAB
    await $.ajax({
            type: "GET",
            url: "{{ URL::to('pembelian/material_pembelian_rutin') }}", //json get site
            dataType : 'json',
            success: function(response){
                arrData = response['data'];
                dateNow = response['date_now'];
                arrMaterialPembelianRutin = arrData;
                console.log(response)
                for(i = 0; i < arrData.length; i++){
                    stok = 0;
                    listStockSite.map((item, obj) => {
                        if (item.m_item_id == arrData[i]['m_item_id'])
                            stok = item.stok;
                    });
                    t.row.add([
                        '<input type="checkbox" value="'+arrData[i]['project_worksub_d_id']+'" />',
                        arrData[i]['m_item_no'],
                        arrData[i]['m_item_name'],
                        '<div class="text-right">'+parseFloat(arrData[i]['volume'])+'</div>',
                        '<div class="text-right">'+parseFloat(stok)+'</div>',
                        '<div class="text-center">'+arrData[i]['m_unit_name']+'</div>',
                        '<div class="text-center">'+formatDateID(new Date(arrData[i]['use_date']))+'</div>',
                        '<div class="text-center">'+arrData[i]['late_time']+'</div>',
                        '<div class="text-center">'+formatDateID(new Date(arrData[i]['due_date']))+'</div>',
                        '<div class="text-center">'+arrData[i]['late_stat']+'</div>',
                        'RAB Suggestion'
                    ]).draw(false);
                }
            }
    });

    // Suggest order dari PO Canceled
    await $.ajax({
            type: "GET",
            url: "{{ URL::to('pembelian/po_canceled') }}", //json get site
            dataType : 'json',
            success: function(response){
                arrData = response['data'];
                console.log(arrData)
                arrPoCanceled = arrData;
                for(i = 0; i < arrData.length; i++){
                    stok = 0;
                    listStockSite.map((item, obj) => {
                        if (item.m_item_id == arrData[i]['m_item_id'])
                            stok = item.stok;
                    });
                    t.row.add([
                        '<input type="checkbox" value="po_'+arrData[i]['purchase_d_id']+'" />',
                        arrData[i]['m_items'] !== null ? arrData[i]['m_items']['no'] : '-',
                        arrData[i]['m_item_name'],
                        '<div class="text-right">'+parseFloat(arrData[i]['volume'])+'</div>',
                        '<div class="text-right">'+parseFloat(stok)+'</div>',
                        '<div class="text-center">'+arrData[i]['m_unit_name']+'</div>',
                        '<div class="text-center">-</div>',
                        '<div class="text-center">-</div>',
                        '<div class="text-center">-</div>',
                        '<div class="text-center">-</div>',
                        'PO Canceled'
                    ]).draw(false);
                }
            }
    });

    $.ajax({
        type: "GET",
        url: "{{ URL::to('pembelian/supplier') }}", //json get site
        dataType : 'json',
        success: function(response){
            arrSuppl = response['data'];
            formSuppl = $('#suppl_single');
            formSuppl.empty();
            formSuppl.append('<option value="">-- Select Supplier --</option>');
            for(j = 0; j < arrSuppl.length; j++){
                formSuppl.append('<option value="'+arrSuppl[j]['id']+'">'+arrSuppl[j]['name']+'</option>');
            }
        }
    });
    

    countSuppl = $('[name^=suppl]').length;
    for(i = 0; i < countSuppl; i++) {
        formSuppl = $('[id^=suppl]').eq(i);
        selectedSuppl = $('[id^=suppl]').eq(i).val();
        formSuppl.empty();
        formSuppl.append('<option value="">-- Select Supplier --</option>');
        for(j = 0; j < arrSuppl.length; j++){
            if(selectedSuppl == arrSuppl[j]['id'])
                formSuppl.append('<option selected value="'+arrSuppl[j]['id']+'">'+arrSuppl[j]['name']+'</option>');
            else
                formSuppl.append('<option value="'+arrSuppl[j]['id']+'">'+arrSuppl[j]['name']+'</option>');
        }
    }

    $("#select_all").click(function(){
        $('input:checkbox').not(this).prop('checked', this.checked);
    });
});

function addToSelected(){
    $('input:checked').each(function() {
        if (!selected.includes($(this).attr('value')))
            selected.push($(this).attr('value'));
    });

    t = $('#zero_config').DataTable();
    t.clear().draw(false);
    dt_temp.clear().draw(false);

    arrSelected = new Array();
    for(i = 0; i < arrMaterialPembelianRutin.length; i++){
        stok = 0;
        listStockSite.map((item, obj) => {
            if (item.m_item_id == arrMaterialPembelianRutin[i]['m_item_id'])
                stok = item.stok;
        });
        if(selected.includes(arrMaterialPembelianRutin[i]['project_worksub_d_id'].toString())){
            if(!arrSelected.some(item => item.m_item_id === arrMaterialPembelianRutin[i]['m_item_id'])){
                arrSelected.push({
                    'm_item_id' : arrMaterialPembelianRutin[i]['m_item_id'],
                    'm_item_name' : arrMaterialPembelianRutin[i]['m_item_name'],
                    'volume' : arrMaterialPembelianRutin[i]['volume'],
                    'm_unit_id' : arrMaterialPembelianRutin[i]['m_unit_id'],
                    'm_unit_name' : arrMaterialPembelianRutin[i]['m_unit_name'],
                    'supplier_name' : arrMaterialPembelianRutin[i]['supplier_name'] !== null
                                        ? arrMaterialPembelianRutin[i]['supplier_name']
                                        : '-',
                    'best_price' : arrMaterialPembelianRutin[i]['best_price'] !== null
                                        ? formatCurrency(arrMaterialPembelianRutin[i]['best_price'])
                                        : '-',
                    'm_item_no' : arrMaterialPembelianRutin[i]['m_item_no'],
                });
            } else {
                index = arrSelected.findIndex(x => x.m_item_id === arrMaterialPembelianRutin[i]['m_item_id']);
                arrSelected[index]['volume'] = parseFloat(arrSelected[index]['volume']) + parseFloat(arrMaterialPembelianRutin[i]['volume']);
            }
            
            text = document.createElement('div');
            text.innerHTML = '<input type="hidden" name="selected_project_worksub_d_id[]" value="'+arrMaterialPembelianRutin[i]['project_worksub_d_id']+'" />';
            document.getElementById("selected_material").appendChild(text);
        } else {
            t.row.add([
                '<input type="checkbox" value="'+arrMaterialPembelianRutin[i]['project_worksub_d_id']+'" />',
                arrMaterialPembelianRutin[i]['m_item_no'],
                arrMaterialPembelianRutin[i]['m_item_name'],
                '<div class="text-right">'+arrMaterialPembelianRutin[i]['volume']+'</div>',
                '<div class="text-right">'+parseFloat(stok)+'</div>',
                '<div class="text-center">'+arrMaterialPembelianRutin[i]['m_unit_name']+'</div>',
                '<div class="text-center">'+formatDateID(new Date(arrMaterialPembelianRutin[i]['use_date']))+'</div>',
                '<div class="text-center">'+arrMaterialPembelianRutin[i]['late_time']+'</div>',
                '<div class="text-center">'+formatDateID(new Date(arrMaterialPembelianRutin[i]['due_date']))+'</div>',
                '-',
                'RAB Suggestion'
            ]).draw(false);
        }
    }

    for(i = 0; i < arrPoCanceled.length; i++) {
        stok = 0;
        listStockSite.map((item, obj) => {
            if (item.m_item_id == arrPoCanceled[i]['m_item_id'])
                stok = item.stok;
        });
        if(selected.includes('po_'+arrPoCanceled[i]['purchase_d_id'].toString())){
            if(!arrSelected.some(item => item.m_item_id === arrPoCanceled[i]['m_item_id'])){
                arrSelected.push({
                    'm_item_id' : arrPoCanceled[i]['m_item_id'],
                    'm_item_name' : arrPoCanceled[i]['m_item_name'],
                    'volume' : arrPoCanceled[i]['volume'],
                    'm_unit_id' : arrPoCanceled[i]['m_unit_id'],
                    'm_unit_name' : arrPoCanceled[i]['m_unit_name'],
                    'supplier_name' : '-',
                    'best_price' : '-',
                    // 'supplier_name' : arrMaterialPembelianRutin[i]['supplier_name'] !== null
                    //                     ? arrMaterialPembelianRutin[i]['supplier_name']
                    //                     : '-',
                    // 'best_price' : arrMaterialPembelianRutin[i]['best_price'] !== null
                    //                     ? formatCurrency(arrMaterialPembelianRutin[i]['best_price'])
                    //                     : '-'
                    'm_item_no' : arrPoCanceled[i]['m_items'] !== null ? arrPoCanceled[i]['m_items']['no'] : '-'
                });
            } else {
                index = arrSelected.findIndex(x => x.m_item_id === arrPoCanceled[i]['m_item_id']);
                arrSelected[index]['volume'] = parseFloat(arrSelected[index]['volume']) + parseFloat(arrPoCanceled[i]['volume']);
            }
            
            text = document.createElement('div');
            text.innerHTML = '<input type="hidden" name="selected_purchase_d_id[]" value="'+arrPoCanceled[i]['purchase_d_id']+'" />';
            document.getElementById("selected_material").appendChild(text);
        } else {
            t.row.add([
                '<input type="checkbox" value="po_'+arrPoCanceled[i]['purchase_d_id']+'" />',
                arrPoCanceled[i]['m_items'] !== null ? arrPoCanceled[i]['m_items']['no'] : '-',
                arrPoCanceled[i]['m_item_name'],
                '<div class="text-right">'+arrPoCanceled[i]['volume']+'</div>',
                parseFloat(stok),
                '<div class="text-center">'+arrPoCanceled[i]['m_unit_name']+'</div>',
                '<div class="text-center">-</div>',
                '<div class="text-center">-</div>',
                '<div class="text-center">-</div>',
                '-',
                'PO Canceled'
            ]).draw(false);
        }
    }

    for(i = 0; i < arrSelected.length; i++){
        stok = 0;
        listStockSite.map((item, obj) => {
            if (item.m_item_id == arrSelected[i]['m_item_id'])
                stok = item.stok;
        });
        dt_temp.row.add([
                '<div class="text-center">'+arrSelected[i]['m_item_no']+'</div>',
                '<input name="m_item_id[]" type="hidden" value="'+arrSelected[i]['m_item_id']+'" /><div class="text-left">'+arrSelected[i]['m_item_name']+'</div>',
                '<input name="volume[]" type="number" class="form-control text-right" value="'+parseFloat(arrSelected[i]['volume'])+'" />',
                '<div class="text-center">'+stok+'</div>',
                '<input name="m_unit_id[]" type="hidden" value="'+arrSelected[i]['m_unit_id']+'" /><div class="text-center">'+arrSelected[i]['m_unit_name']+'</div>',
                '<div class="text-center">'+arrSelected[i]['best_price']+' / '+ arrSelected[i]['supplier_name']+'</div>',
                // '<select id="suppl[]" name="suppl[]" required onchange="doSuppl();" class="form-control select2 custom-select"></select>',
                '<input id="perkiraan_harga_suppl[]" required onchange="doPerkiraanHarga();" name="perkiraan_harga_suppl[]" class="form-control text-right" />',
                // '<select id="cara_bayar[]" name="cara_bayar[]" required onchange="doCaraBayar();" class="form-control select2 custom-select"><option value="cash">Cash</option><option value="credit">Credit</option></select>',
                '<input id="notes[]" required name="notes[]" class="form-control text-left" type="text" />',
            ]).draw(false);
            
            formSuppl = $('[id^=suppl]');
            formSuppl.empty();
            formSuppl.append('<option value="">-- Select Supplier --</option>');
            for(j = 0; j < arrSuppl.length; j++){
                formSuppl.append('<option value="'+arrSuppl[j]['id']+'">'+arrSuppl[j]['name']+'</option>');
            }
    }
};


function doPerkiraanHarga(){
    document.getElementById("btn_add_to_selected").disabled = true;
    // alert('test');
}
function doSuppl(){
    document.getElementById("btn_add_to_selected").disabled = true;
    // alert('test');
}
function doCaraBayar(){
    document.getElementById("btn_add_to_selected").disabled = true;
    // alert('test');
}

var dt_temp = $('#dt_temp').DataTable();
$('#addRow').on('click', function() {
    countMaterial = $('[name^=m_item_id]').length;

    if(countMaterial < 5) {
        document.getElementById("btn_add_to_selected").disabled = true;
        
        dt_temp.row.add([
            '<input type="text" id="m_item_no[]" name="m_item_no[]" class="form-control" onchange="handleMaterialNo(this)" />',
            '<select class="form-control select2 custom-select" style="width: 100%; height:32px;" id="m_item_id[]" name="m_item_id[]" required onchange="handleMaterial()"></select>',
            '<input name="volume[]" type="number" class="form-control text-right" step="any" />',
            '<input readonly id="stok_site[]" name="stok_site[]" type="number" class="form-control text-right" />',
            '<input name="m_unit_id[]" type="hidden" /><input name="m_unit_name[]" class="form-control text-center" type="text" readonly />',
            '<div class="text-center"></div>',
            // '<select id="suppl[]" name="suppl[]" required onchange="doSuppl();" class="form-control select2 custom-select"></select>',
            '<input id="perkiraan_harga_suppl[]" required onchange="doPerkiraanHarga();" name="perkiraan_harga_suppl[]" class="form-control text-right" />',
            // '<select id="cara_bayar[]" name="cara_bayar[]" required onchange="doCaraBayar();" class="form-control select2 custom-select"><option value="cash">Cash</option><option value="credit">Credit</option></select>',
            '<input id="notes[]" required name="notes[]" class="form-control text-left" type="text" />',
        ]).draw(false);

        eventSelectedMaterial();
    }
});

function eventSelectedMaterial() {
    countMaterial = $('[name^=m_item_id]').length;
    let arrSelectedMaterial = [];
    for(i = 0; i < countMaterial; i++){
        if($('[name^=m_item_id]').eq(i).val() !== null && $('[name^=m_item_id]').eq(i).val() !== '')
            arrSelectedMaterial.push($('[name^=m_item_id]').eq(i).val());
    }
    // console.log(arrSelectedMaterial);
    for(i = 0; i < countMaterial; i++){
        formMaterial = $('[name^=m_item_id]').eq(i);
        selectedMaterial = $('[name^=m_item_id]').eq(i).val();
        $('[name^=m_item_id]').eq(i).empty();
        formMaterial.append('<option value="">-- Select Material --</option>');
        formUnit = $('[name^=m_unit_id]').eq(i);
        formUnitName = $('[name^=m_unit_name]').eq(i);
        formMaterialNo = $('[name^=m_item_no]').eq(i);
        formStokSite = $('[name^=stok_site]').eq(i);
        itemNo = '';
        stok = 0;
        $.each(arrMaterial, function(i, item) {
            if(selectedMaterial == arrMaterial[i]['id']) {
                formMaterial.append('<option selected value="'+arrMaterial[i]['id']+'">'+arrMaterial[i]['name']+'</option>');
                formUnit.val(arrMaterial[i]['m_unit_id']);
                formUnitName.val(arrMaterial[i]['m_unit_name']);
                itemNo = arrMaterial[i]['no'];
                listStockSite.map((it, obj) => {
                    if (it.m_item_id == arrMaterial[i]['id'])
                        stok = parseFloat(it.stok);
                });
            } else {
                if (arrSelectedMaterial.includes(arrMaterial[i]['id'].toString()))
                    formMaterial.append('<option disabled value="'+arrMaterial[i]['id']+'">'+arrMaterial[i]['name']+'</option>');
                else 
                    formMaterial.append('<option value="'+arrMaterial[i]['id']+'">'+arrMaterial[i]['name']+'</option>');
            }
        });
        formMaterialNo.val(itemNo);
        formStokSite.val(stok);
    }

    // countSuppl = $('[name^=suppl]').length;
    // for(i = 0; i < countSuppl; i++) {
    //     formSuppl = $('[id^=suppl]').eq(i);
    //     selectedSuppl = $('[id^=suppl]').eq(i).val();
    //     formSuppl.empty();
    //     formSuppl.append('<option value="">-- Select Supplier --</option>');
    //     for(j = 0; j < arrSuppl.length; j++){
    //         if(selectedSuppl == arrSuppl[j]['id'])
    //             formSuppl.append('<option selected value="'+arrSuppl[j]['id']+'">'+arrSuppl[j]['name']+'</option>');
    //         else
    //             formSuppl.append('<option value="'+arrSuppl[j]['id']+'">'+arrSuppl[j]['name']+'</option>');
    //     }
    // }
}

var arrMaterial = [];
$.ajax({
    type: "GET",
    url: "{{ URL::to('material_request/get_material') }}", //json get material
    dataType : 'json',
    success: function(response){
        arrMaterial = response['data'];
    }
});

function handleMaterial(){
    eventSelectedMaterial();
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