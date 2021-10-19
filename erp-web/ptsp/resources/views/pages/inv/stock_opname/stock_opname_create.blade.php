@extends('theme.default')

@section('breadcrumb')
<div class="page-breadcrumb">
    <div class="row">
        <div class="col-5 align-self-center">
            <h4 class="page-title">Form Stok Opname</h4>
            <div class="d-flex align-items-center">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ URL::to('stok_opname') }}">List Stok Opname</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Form Stok Opname</li>
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
        <div class="col-12">
            <form method="POST" action="{{ URL::to('stok_opname/create') }}" class="form-horizontal">
            @csrf
                <div class="text-right">
                    <a href="{{ URL::to('stok_opname') }}" class="btn btn-danger btn-sm mb-2">Cancel</a>
                    <button type="submit" class="btn btn-info btn-sm mb-2" onclick="return checkform()">Submit</button>
                    <button id="btnMenyerah" type="submit" disabled class="btn btn-warning btn-sm mb-2" onclick="return checkmaterial()">Menyerah</button>
                    <a href="{{ URL::to('stok_opname/print_stok') }}" target="_blank" class="btn btn-primary btn-sm mb-2">Cetak Semua Stok</a>
                </div>
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Input Material Stok Opname</h4>
                        <div>
                            <button type="button" id="addRow" class="btn btn-success btn-sm mb-2"><i class="fas fa-plus"></i>&nbsp; Add New Row</button>
                            <button type="button" id="delRow" class="btn btn-danger btn-sm mb-2"><i class="ti-trash"></i>&nbsp; Delete Selected</button>
                        </div>    
                        <div class="table-responsive">
                            <table id="requestDetail_addrow" class="table table-striped table-bordered display" style="width:100%">
                                <thead>
                                    <tr>
                                        <th class="text-center">Material Number</th>
                                        <th class="text-center">Material Name</th>
                                        <th width="80px" class="text-center">Qty Stock Opname</th>
                                        <th class="text-center">Satuan</th>
                                        <th width="150px" class="text-center">Keterangan</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>    
</div>

<div class="modal fade bs-example-modal-lg" id="modalTransferDetail" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myLargeModalLabel">Kesalahan Stock Opname</h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <div class="modal-body">
                <h4>Stok Opname Material Kurang atau Stok Selisih</h4>
                <div class="table-responsive">
                    <table id="zero_config2" class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th class="text-center">Material No</th>
                                <th class="text-center">Material Name</th>
                                <th class="text-center">Qty Stock Opname</th>
                                <th class="text-center">Satuan</th>
                                <th class="text-center">Keterangan</th>
                            </tr>
                        </thead>
                        <tbody>
                            
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger waves-effect btn-sm text-left" data-dismiss="modal">Close</button>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<!-- /.modal -->

<script src="{!! asset('theme/assets/libs/jquery/dist/jquery.min.js') !!}"></script>
<script src="{!! asset('theme/assets/extra-libs/DataTables/datatables.min.js') !!}"></script>
<script>

var t = $('#requestDetail_addrow').DataTable();
var counter = 1;

var listMaterialRab = [];

var counterSubmit = 0;

// List Stock
var listStockSite = [];
$(document).ready(function(){
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
});

function checkform() {
    countMaterial = $('[id^=m_item_id]').length;
    let listStockInvalid = [];
    for(const index in listStockSite) {
        let isValid = false;
        for(i = 0; i < countMaterial; i++){
            materialId = $('[id^=m_item_id]').eq(i).val();
            qty = $('[id^=qty]').eq(i).val();
            if (listStockSite[index].m_item_id.toString() === materialId.toString()) {
                if(parseFloat(listStockSite[index].stok) === parseFloat(qty))
                    isValid = true;
                else {
                    listStockInvalid.push(listStockSite[index]);
                    listStockInvalid[index].qty_opname = qty;
                }
            }
        }
        listStockSite[index].isValid = isValid;
    }

    let isSubmit = true;
    listStockSite.map((item, obj) => {
        if(!item.isValid)
            isSubmit = false;
    })
    console.log(listStockSite);
    console.log(listStockInvalid);

    if (isSubmit != true) {
        alert('Stok Opname Salah, Silahkan Cek Kembali !!!');

        var t2 = $('#zero_config2').DataTable();
        t2.clear().draw(false);
        for(i = 0; i < listStockInvalid.length; i++) {
            t2.row.add([
                '<div class="text-center">'+listStockInvalid[i]['m_items']['no']+'</div>',
                '<div class="text-left">'+listStockInvalid[i]['m_items']['name']+'</div>',
                '<div class="text-left">'+listStockInvalid[i]['qty_opname']+'</div>',
                '<div class="text-left">'+listStockInvalid[i]['m_units']['name']+'</div>'
            ]).draw(false);
        }

        $('#modalTransferDetail').modal('show');
    } 

    counterSubmit++;
    if (counterSubmit >= 3)
        document.getElementById("btnMenyerah").disabled = false;

    return isSubmit;
}

function checkmaterial() {
    countMaterial = $('[id^=m_item_id]').length;
    let isMenyerah = false;
    if(countMaterial > 0)
        isMenyerah = true;
    else
        alert('Silahkan input material stok opname terlebih dahulu');
    return isMenyerah;
}

$('#addRow').on('click', function() {
    t.row.add([
        '<input type="text" id="m_item_no[]" name="m_item_no[]" class="form-control" onchange="handleMaterialNo(this)" />',
        '<input type="hidden" id="m_item_id[]" name="m_item_id[]" required /><input type="text" id="m_item_name[]" name="m_item_name[]" class="form-control" readonly required>',
        '<input type="number" id="qty[]" name="qty[]" step="any" min="0" class="form-control text-right" placeholder="0" required>',
        '<input type="hidden" id="m_unit_id[]" name="m_unit_id[]" /><input disabled class="form-control" id="m_unit_name[]" name="m_unit_name[]" required>',
        '<input type="text" id="keterangan[]" name="keterangan[]" class="form-control">',
    ]).draw(false);
});

async function handleMaterialNo(obj) {
    countMaterial = $('[id^=m_item_no]').length;
    for(i = 0; i < countMaterial; i++){
        materialNo = $('[id^=m_item_no]').eq(i).val();
        formMaterialId = $('[id^=m_item_id]').eq(i);
        formMaterialName = $('[id^=m_item_name]').eq(i);
        formSatuanId = $('[id^=m_unit_id]').eq(i);
        formSatuanName = $('[id^=m_unit_name]').eq(i);
        id = ''; name = ''; satuan_id = ''; satuan_name = '';
        await $.ajax({
            type: "GET",
            url: "{{ URL::to('stok_opname/material_by_no') }}", //json get site
            dataType : 'json',
            data: {'no' : materialNo},
            success: function(response){
                arrData = response['data'];
                if(arrData.length > 0) {
                    id = arrData[0]['id'];
                    name = arrData[0]['name'];
                    satuan_id = arrData[0]['m_unit_id'];
                    satuan_name = arrData[0]['m_unit_name'];
                } 
            }
        });

        formMaterialId.val(id);
        formMaterialName.val(name);
        formSatuanId.val(satuan_id);
        formSatuanName.val(satuan_name);
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
</script>

@endsection