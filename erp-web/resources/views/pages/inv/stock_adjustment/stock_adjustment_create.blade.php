@extends('theme.default')

@section('breadcrumb')
<div class="page-breadcrumb">
    <div class="row">
        <div class="col-5 align-self-center">
            <h4 class="page-title">Adjusment Stock</h4>
            <div class="d-flex align-items-center">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ URL::to('inventory/stock_adjustment') }}">Adjusment Stock</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Create Adjusment Stock</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
</div>
@endsection

@section('content')
<form method="POST" action="{{ URL::to('inventory/stock_adjustment_store') }}" class="form-horizontal">
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
                                <label>Gudang</label>
                                <select name="m_warehouse_id" id="m_warehouse_id" class="form-control select2 custom-select" style="width: 100%; height:32px;" required onchange="cekGudang()">
                                    <option value="">--- Pilih Gudang ---</option>
                                    @foreach($gudang as $value)
                                    <option value="{{ $value->id}}" @if($value->id == $m_warehouse_id) selected @endif>{{ $value->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="">Pilih Material</label>
                                <br>
                                <select class="form-control custom-select select2" style="width: 400px; height:32px;" id="item_id"></select>
                                <button type="button" id="addRow" class="btn btn-success btn-sm mb-2"><i class="fas fa-plus"></i>&nbsp; Add New Detail</button>
                            </div>   
                            <div class="form-group col-md-6" hidden>
                                <label>Tanggal Permintaan</label>
                                <input type="date" value="{{date('Y-m-d')}}" name="create_date" required class="form-control">
                            </div>
                            <div class="form-group col-md-6">
                                <label>Catatan</label>
                                <textarea name="notes" id="notes" class="form-control"></textarea>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="">Pilih Lawan</label>
                                <select class="form-control select2" name="akun" style="width: 100%;" required>
                                    @foreach($akun_option as $key => $value)
                                    <option value="{{$key}}">{{$value}}</option>
                                    @endforeach
                                </select>
                            </div> 
                        </div>
                        <div class="table-responsive">
                            <table id="requestDetail_addrow" class="table table-striped table-bordered display" style="width:100%">
                                <thead>
                                    <tr>
                                        <th class="text-center">Material No</th>
                                        <th class="text-center">Material Name</th>
                                        <th class="text-center">Stok Site</th>
                                        <th class="text-center">Stok Seharusnya</th>
                                        <th class="text-center">Harga Item</th>
                                        <th class="text-center">Satuan</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                        <br>
                        <div class="float-right">
                            <label for="">Total :</label>
                            <input type="text" readonly name="total_all" id="total_all" class="form-control" style="height:50px; font-size:28px">
                            <br>
                            <div class="form-group float-right">
                                <button id="btnSubmit" type="submit" data-toggle="modal" data-target="#modalShowDetail" class="btn btn-info mb-2">Submit</button>
                            </div>
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

    var satuan = '', itemName = '', itemNo = '', unitName='', item_prices=0;
    $.each(arrMaterial, function(i, item) {
        if(item_selected == arrMaterial[i]['id']){
            satuan = arrMaterial[i]['m_unit_id'];
            itemName = arrMaterial[i]['name'];
            itemNo = arrMaterial[i]['no'];
            unitName = arrMaterial[i]['m_unit_name'];
            item_prices = parseInt(arrMaterial[i]['item_prices']);
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
            '<td>'+
                '<input readonly id="stok_site[]" name="stok_site[]" type="number" class="form-control text-right" value="'+stok+'" />' +
            '</td>'+
            '<td>'+
                '<input type="number" id="qty[]" name="qty[]" step="any" min="0" class="form-control text-right" placeholder="0" required onkeyup="cekStock()">' +
            '</td>'+
            '<td>'+
                '<input type="" readonly id="item_prices[]" class="form-control text-right" name="item_prices[]" value="'+item_prices+'"/>'+
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
    cekStock()
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
    var is_out_stock=false, total_all=0;
    var qty = $('[id^=qty]');
    var stock = $('[name^=stok_site]');
    var item_prices = $('[name^=item_prices]');
    for(var i = 0; i < qty.length; i++){
        var total_req=qty.eq(i).val();
        var total=stock.eq(i).val();
        var prices=item_prices.eq(i).val();
        var adjusment = total - total_req;
        if (adjusment > 0) {
            total_all=parseInt(total_all) + (parseInt(adjusment) * parseInt(prices));
        }
        else{
            total_all=parseInt(total_all) + (parseInt(adjusment) * parseInt(prices) * -1);
        }
        // if(parseFloat(total_req) > parseFloat(total)){
        //     $(qty.eq(i)).closest("tr").addClass('table-danger');
        //     is_out_stock=true;
        // }else{
        //     $(qty.eq(i)).closest("tr").removeClass('table-danger');
        // }
    }
    
    // if(is_out_stock == true){
    //     $('#btnSubmit').prop('disabled', true)
    // }else{
    //     $('#btnSubmit').prop('disabled', false)
    // }
    $('#total_all').val(formatCurrency(total_all.toString()))
}

</script>
@endsection