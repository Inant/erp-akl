@extends('theme.default')

@section('breadcrumb')
<div class="page-breadcrumb">
    <div class="row">
        <div class="col-5 align-self-center">
            <h4 class="page-title">Create Penjualan Material</h4>
            <div class="d-flex align-items-center">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ URL::to('penjualan_keluar') }}">List Penjualan Material</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Create Penjualan Material</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
</div>
@endsection

@section('content')
<form method="POST" action="{{ URL::to('penjualan_keluar/create') }}" class="form-horizontal">
    @csrf
    <div class="container-fluid">
        <!-- basic table -->
        <div class="row">
            <div class="col-12">
                <!--<div class="text-right">-->
                <!--    <a href="{{ URL::to('material_request') }}" class="btn btn-danger btn-sm mb-2">Cancel</a>-->
                <!--    <button id="btnSubmit" type="submit" disabled data-toggle="modal" data-target="#modalShowDetail" class="btn btn-info btn-sm mb-2">Submit</button>-->
                <!--</div>-->
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Input Material</h4>
                        <div class="row">
                            <div class="form-group col-md-6">
                                <label>No Invoice</label>
                                <input type="text" name="invoice" id="invoice" class="form-control" placeholder="No Invoice">
                                {{-- <select id="cara_bayar_single" name="cara_bayar_single" required class="form-control select2 custom-select" style="width: 100%; height:32px;">
                                    <option value="">-- Choose No Faktur --</option>
                                    <option value="cash">Cash</option><option value="credit">Credit</option>
                                </select> --}}
                            </div>
                            <div class="form-group col-md-6">
                                <label>No Faktur</label>
                                <input type="text" name="bill_no" id="bill_no" class="form-control" placeholder="No Faktur">
                                {{-- <select id="cara_bayar_single" name="cara_bayar_single" required class="form-control select2 custom-select" style="width: 100%; height:32px;">
                                    <option value="">-- Choose No Faktur --</option>
                                    <option value="cash">Cash</option><option value="credit">Credit</option>
                                </select> --}}
                            </div>
                            <div class="form-group col-md-6">
                                <label>Ke Gudang</label>
                                <select name="m_warehouse_id" id="m_warehouse_id" class="form-control select2 custom-select" style="width: 100%; height:32px;" required onchange="cekGudang()">
                                    <option value="">--- Pilih Gudang ---</option>
                                    @foreach($gudang as $value)
                                    <option value="{{ $value->id}}" @if($value->id == $m_warehouse_id) selected @endif>{{ $value->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-md-6">
                                <label>Payment Method</label>
                                <select id="cara_bayar_single" name="cara_bayar_single" required class="form-control select2 custom-select" style="width: 100%; height:32px;">
                                    <option value="">-- Choose Payment Method --</option>
                                    <option value="cash">Cash</option><option value="credit">Credit</option>
                                </select>
                            </div>
                            <div class="form-group col-md-6">
                                <label>Customer</label>
                                <select id="customer_id" name="customer_id" required class="form-control select2 custom-select" style="width: 100%; height:32px;">
                                </select>
                            </div>
                            <div class="form-group col-md-6">
                                <label>Harga Item Tanpa PPN</label><br>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" style="width:30px; height:30px" name="without_ppn" type="checkbox" id="inlineCheckbox1" value="1">
                                    <label class="form-check-label" for="inlineCheckbox1">Ya</label>
                                </div>
                            </div>
                            <div class="form-group col-md-6">
                                <label>Tanggal Permintaan</label>
                                <input type="date" value="{{date('Y-m-d')}}" name="create_date" required class="form-control">
                            </div>
                            <div class="form-group col-md-6">
                                <label for="">Pilih Material</label>
                                <br>
                                <select class="form-control custom-select select2" style="width: 400px; height:32px;" id="item_id"></select>
                                <button type="button" id="addRow" class="btn btn-success btn-sm mb-2"><i class="fas fa-plus"></i>&nbsp; Add New Detail</button>
                            </div>    
                        </div>
                        <div class="table-responsive">
                            <table id="requestDetail_addrow" class="table table-striped table-bordered display" style="width:100%">
                                <thead>
                                    <tr>
                                        <th class="text-center">Material No</th>
                                        <th class="text-center">Material Name</th>
                                        <!-- <th class="text-center">Stok Site</th> -->
                                        <th class="text-center">Qty Penjualan</th>
                                        <th class="text-center">Harga Satuan</th>
                                        <th class="text-center">Satuan</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                        <br>
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="float-right">
                                <label for="">Total :</label>
                                <input type="text" readonly name="total_bayar" id="total_bayar" class="form-control" style="height:50px; font-size:28px">
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <a href="{{ URL::to('material_request') }}" class="btn btn-danger  mb-2">Cancel</a>
                            <button id="btnSubmit" type="submit" data-toggle="modal" data-target="#modalShowDetail" class="btn btn-info mb-2">Submit</button>
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

var counter = 1;

// List Stock
var listStockSite = [];
var arrMaterial = [];
$(document).ready(function(){
    $.ajax({
        type: "GET",
        url: "{{ URL::to('customer/json') }}", //json get material
        dataType : 'json',
        async : false,
        success: function(response){
            customer = response['data'];
            $('#customer_id').append('<option selected value="">Pilih Customer</option>')
            $.each(customer, function(i, item) {
                $('#customer_id').append('<option value="'+customer[i]['id']+'">'+customer[i]['coorporate_name']+'</option>');
            });
        }
    });

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
    
    $.ajax({
        type: "GET",
        url: "{{ URL::to('material_request/get_material') }}", //json get material
        dataType : 'json',
        async : false,
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
    cekGudang()
});
var selectedItem=[];
$('#addRow').on('click', function() {
    var item_selected=$('#item_id').val();
    var m_warehouse_id=$('#m_warehouse_id').val();

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

    stok=0;
    listStockSite.map((it, obj) => {
        if (it.m_item_id == item_selected && it.m_warehouse_id == m_warehouse_id && it.type == 'STK_NORMAL')
            stok = parseFloat(it.stok);
    });

    if (is_there == false && item_selected != '') {
        var tdAdd='<tr>'+
            '<td><input type="hidden" id="m_item_no[]" name="m_item_no[]" class="form-control" value="'+itemNo+'" />'+itemNo+'</td>'+
            '<td>'+
                '<input type="hidden" id="m_item_name[]" name="m_item_name[]" value="'+itemName+'"/><input type="hidden" id="m_item_id[]" name="m_item_id[]" value="'+item_selected+'" />'+itemName+
            '</td>'+
            // '<td>'+
            //     '<input readonly id="stok_site[]" name="stok_site[]" type="number" class="form-control text-right" value="'+stok+'" />' +
            // '</td>'+
            '<td>'+
                '<input type="number" id="qty[]" name="qty[]" step="any" min="0" class="form-control text-right" placeholder="0" required onkeyup="">' +
            '</td>'+
            '<td>'+
                '<input type="number" id="price[]" name="price[]" step="any" min="0" class="form-control text-right" placeholder="0" required onkeyup="cekTotal()">' +
            '</td>'+
            '<td>'+
                '<input type="hidden" id="m_unit_id[]" name="m_unit_id[]" value="'+satuan+'"/><select disabled class="form-control select2" style="width: 100%; height:32px;" id="m_unit_name[]" name="m_unit_name[]" required><option value="'+satuan+'">'+unitName+'</option></select>' +
            '</td>'+
            '<td class="text-center"><button class="btn btn-danger removeOption"><i class="mdi mdi-delete"></i></button></td>'+
        '</tr>';
        $('#requestDetail_addrow').find('tbody:last').append(tdAdd);
    }
    
    saveSelectedItem()
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
    saveSelectedItem()
});


function cekGudang(){
    saveSelectedItem()
    var m_warehouse_id=$('#m_warehouse_id').val();
    if (m_warehouse_id == '') {
        $('#addRow').prop('disabled', true)
    }else{
        $('#addRow').prop('disabled', false)
    }
    $('#requestDetail_addrow > tbody').empty();
}
function cekStock(){
    var is_out_stock=false;
    var qty = $('[id^=qty]');
    var stock = $('[name^=stok_site]');
    for(var i = 0; i < qty.length; i++){
        var total_req=qty.eq(i).val();
        var total=stock.eq(i).val();
        if(parseFloat(total_req) > parseFloat(total)){
            $(qty.eq(i)).closest("tr").addClass('table-danger');
            is_out_stock=true;
        }else{
            $(qty.eq(i)).closest("tr").removeClass('table-danger');
        }
    }
    if(is_out_stock == true){
        $('#btnSubmit').prop('disabled', true)
    }else{
        $('#btnSubmit').prop('disabled', false)
    }
}
function cekTotal(){
    var item=$('[name^=m_item_id');
    var price=$('[name^=price');
    var volume=$('[name^=qty');
    var total=0, total_item=0, total_bayar=0;
    for (var i = 0; i < item.length; i++) {
        var m_item=item.eq(i).val();
        var harga=price.eq(i).val() != '' ? price.eq(i).val() : 0;
        var amount=volume.eq(i).val() != '' ? volume.eq(i).val() : 0;
        if (m_item != '' && harga != '' && amount != '') {
            total_bayar+=(parseFloat(amount)*parseFloat(harga));
        }   
    }
    $('#total_bayar').val(formatCurrency(total_bayar.toFixed(0)))
}
</script>
@endsection