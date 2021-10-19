@extends('theme.default')

@section('breadcrumb')
			<div class="page-breadcrumb">
                <div class="row">
                    <div class="col-5 align-self-center">
                        <h4 class="page-title">Penerimaan ATK</h4>
                        <div class="d-flex align-items-center">
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="{{ URL::to('penerimaan_barang') }}">Penerimaan ATK</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">Receive</li>
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
                        <form method="POST" action="{{ URL::to('penerimaan_barang/receive_atk') }}" class="form-horizontal">
                        @csrf
                            <!-- <div class="text-right">
                                <a href="{{ URL::to('penerimaan_barang') }}"><button type="button" class="btn btn-danger btn-sm mb-2">Cancel</button></a>
                                <a href="{{ URL::to('penerimaan_barang/decline/' . $purchase_asset_id) }}"><button type="button" class="btn btn-warning btn-sm mb-2">Tolak/Retur</button></a>
                                <button type="submit" onclick="clickTerima()" name="submit" value="receive" class="btn btn-primary btn-sm mb-2">Terima</button>
                            </div> -->
                            <div class="card">
                                <div class="card-body">
                                    <h4 class="card-title">Form Input Material Penerimaan ATK</h4>
                                    <br/>
                                        <div class="form-group row">
                                            <label class="col-sm-3 text-right control-label col-form-label">Nama Supplier</label>
                                            <div class="col-sm-8">
                                            <input type="text" class="form-control" readonly value="{{$data->name}}"/>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-sm-3 text-right control-label col-form-label">Nama Ekspedisi/Driver</label>
                                            <div class="col-sm-8">
                                            <input type="text" id="driver" name="driver" class="form-control" required/>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-sm-3 text-right control-label col-form-label">Nomor Surat Jalan</label>
                                            <div class="col-sm-8">
                                            <input type="text" id="no_surat_jalan" name="no_surat_jalan" class="form-control" required/>
                                            </div>
                                            
                                        </div>
                                    <br/>
                                    <div class="table-responsive">
                                        <table id="zero_config" class="table table-striped table-bordered">
                                            <thead>
                                                <tr>
                                                    <th class="text-center">PO Number</th>
                                                    <th class="text-center">Material No</th>
                                                    <th class="text-center">Material Name</th>
                                                    <th class="text-center">Volume</th>
                                                    <!-- <th class="text-center">Receive Volume</th> -->
                                                    <th class="text-center">Satuan</th>
                                                    <!-- <th class="text-center">Storage Location</th> -->
                                                </tr>
                                            </thead>
                                        </table>
                                    </div>
                                    <br><br>
                                    <div class="table-responsive">
                                        <h4 class="card-title">Tambahkan Sebagai Asset</h4>
                                        <button onclick="addProductOrder()" type="button" class="btn btn-info">tambah</button>
                                        <br><br>
                                        <table class="table table-bordered table-striped" id="detail-order">
                                            <thead>
                                                    <tr>
                                                        <th class="text-center">PO Number </th>
                                                        <th class="text-center">Material No</th>
                                                        <th class="text-center">Material Name</th>
                                                        <th class="text-center">Volume</th>
                                                        <th class="text-center">Receive Volume</th>
                                                        <th class="text-center">Satuan</th>
                                                        <th class="text-center">Gudang</th>
                                                        <th class="text-center">Storage Location</th>
                                                        <th class="text-center">Tipe Asset</th>
                                                        <th></th>
                                                    </tr>
                                                </thead>
                                            <tbody>
                                                
                                            </tbody>
                                        </table>
                                    </div>
                                    <br><br>
                                    <div class="table-responsive">
                                        <h4 class="card-title">Tambahkan Sebagai Beban</h4>
                                        <button onclick="addProductCharge()" type="button" class="btn btn-info">tambah</button>
                                        <br><br>
                                        <table class="table table-bordered table-striped" id="detail-charge">
                                            <thead>
                                                    <tr>
                                                        <th class="text-center">PO Number </th>
                                                        <th class="text-center">Material No</th>
                                                        <th class="text-center">Material Name</th>
                                                        <th class="text-center">Volume</th>
                                                        <th class="text-center">Receive Volume</th>
                                                        <th class="text-center">Satuan</th>
                                                        <th class="text-center">Storage Location</th>
                                                        <th></th>
                                                    </tr>
                                                </thead>
                                            <tbody>
                                                
                                            </tbody>
                                        </table>
                                    </div>
                                    <br><br>

                                    <div class="table-responsive">
                                        <h4 class="card-title">Catat Barang Rusak</h4>
                                        <button onclick="addProductCorrupt()" type="button" class="btn btn-info">tambah</button>
                                        <br><br>
                                        <table class="table table-bordered table-striped" id="detail-corrupt">
                                            <thead>
                                                    <tr>
                                                        <th class="text-center">PO Number </th>
                                                        <th class="text-center">Material No</th>
                                                        <th class="text-center">Material Name</th>
                                                        <th class="text-center">Volume</th>
                                                        <th class="text-center">Receive Volume</th>
                                                        <th class="text-center">Satuan</th>
                                                        <th class="text-center">Storage Location</th>
                                                        <th></th>
                                                    </tr>
                                                </thead>
                                            <tbody>
                                                
                                            </tbody>
                                        </table>
                                    </div>
                                    <br><br>
                                    <div class="form-group">
                                        <a href="{{url('/penerimaan_barang')}}"><button type="button" class="btn btn-danger mb-2">Cancel</button></a>
                                        <!-- <a href="http://erp-ptsp.binoemar.com/penerimaan_barang/decline/235"><button type="button" class="btn btn-warning btn-sm mb-2">Tolak/Retur</button></a> -->
                                        <button type="submit" onclick="clickTerima()" name="submit" value="receive" class="btn btn-primary mb-2">Terima</button>
                                    </div>
                                </div>
                                
                            </div>
                        </form>
                    </div>
                </div>

                <!-- <div class="row">
                    <div class="col-12">
                            <div class="card">
                                <div class="card-body">
                                    <h4 class="card-title">Riwayat Penerimaan Barang</h4>
                                    <div class="table-responsive">
                                            <table id="dt_detail" class="table table-striped table-bordered">
                                                <thead>
                                                    <tr>
                                                        <th class="text-center">Transaction Number</th>
                                                        <th class="text-center">PO Number</th>
                                                        <th class="text-center">Material No</th>
                                                        <th class="text-center">Material Name</th>
                                                        <th class="text-center">Volume</th>
                                                        <th class="text-center">Satuan</th>
                                                    </tr>
                                                </thead>
                                            </table>
                                    </div>
                                </div>
                            </div>
                    </div>
                </div> -->
                
</div>

<script src="{!! asset('theme/assets/libs/jquery/dist/jquery.min.js') !!}"></script>
<script src="{!! asset('theme/assets/extra-libs/DataTables/datatables.min.js') !!}"></script>
<script>

dt = $('#dt_detail').DataTable();
var purchase_d=[];
var warehouse=[];
var account_asset=[];
$(document).ready(function(){
    $.ajax({
        // type: "post",
        async : false,
        url: "{{ URL::to('akuntansi/account_asset') }}",
        dataType : 'json',
        success: function(response){
            account_asset = response;
        }
    });
    t = $('#zero_config').DataTable();
    t.clear().draw(false);
    $.ajax({
            type: "GET",
            url: "{{ URL::to('penerimaan_barang/detail_atk/'.$purchase_asset_id) }}", //json get site
            dataType : 'json',
            async : false,
            success: function(response){
                arrData = response['data'];
                for(i = 0; i < arrData.length; i++){
                    // t.row.add([
                    //     '<input type="hidden" id="id[]" name="id[]" value="'+arrData[i]['id']+'" /><input type="hidden" id="purchase_asset_id[]" name="purchase_asset_id[]" value="'+arrData[i]['purchase_asset_id']+'" /><div class="text-left">'+arrData[i]['purchases']['no']+'</div>',
                    //     '<div class="text-left">'+arrData[i]['m_items']['no']+'</div>',
                    //     '<input type="hidden" id="m_item_id[]" name="m_item_id[]" value="'+arrData[i]['m_item_id']+'" /><div class="text-left">'+arrData[i]['m_items']['name']+'</div>',
                    //     '<div class="text-right">'+arrData[i]['amount']+'</div>',
                    //     '<input id="receive_volume[]" required name="receive_volume[]" class="form-control text-right" type="text" value="'+arrData[i]['amount']+'" />',
                    //     '<input type="hidden" id="m_unit_id[]" name="m_unit_id[]" value="'+arrData[i]['m_unit_id']+'" /><div class="text-center">'+arrData[i]['m_units']['name']+'</div>',
                    //     '<input id="notes[]" required name="notes[]" class="form-control text-left" type="text" /><input id="price[]" name="price[]" class="form-control" type="hidden" value="'+arrData[i]['base_price']+'"/>'
                    // ]).draw(false);
                    t.row.add([
                        '<div class="text-left">'+arrData[i]['purchase_assets']['no']+'</div>',
                        '<div class="text-left">'+arrData[i]['m_items']['no']+'</div>',
                        '<div class="text-left">'+arrData[i]['m_items']['name']+'</div>',
                        '<div class="text-right">'+arrData[i]['amount']+'</div>',
                        // '<input class="form-control text-right" type="text" value="'+arrData[i]['amount']+'" />',
                        '<div class="text-center">'+arrData[i]['m_units']['name']+'</div>'
                        
                    ]).draw(false);
                }
                purchase_d = arrData;
            }
    });
    
    dt.clear().draw(false);
    $.ajax({
            type: "GET",
            url: "{{ URL::to('penerimaan_barang/get_inv_by_purchase_asset_id/'.$purchase_asset_id) }}", //json get site
            dataType : 'json',
            success: function(response){
                arrData = response['data'];
                for(x = 0; x < arrData.length; x++){
                    arrInvTrxD = arrData[x]['inv_trx_ds'];
                    // console.log(arrInvTrxD);
                    for(i = 0; i < arrInvTrxD.length; i++){
                        dt.row.add([
                            '<div class="text-left">'+arrData[x]['no']+'</div>',
                            '<div class="text-left">'+arrData[x]['purchase_assets']['no']+'</div>',
                            '<div class="text-left">'+arrInvTrxD[i]['material_no']+'</div>',
                            '<div class="text-left">'+arrInvTrxD[i]['m_items']['name']+'</div>',
                            '<div class="text-right">'+arrInvTrxD[i]['amount']+'</div>',
                            '<div class="text-center">'+arrInvTrxD[i]['m_units']['name']+'</div>',
                        ]).draw(false);
                    }
                }
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
    var option_tipe='<option value="">Pilih Tipe Asset</option>';
    
    for (var i = 0; i < account_asset.length; i++) {
        option_tipe+='<option value="'+account_asset[i]['id']+'">'+account_asset[i]['nama']+'</option>';
    }
    var option='<option value="">Pilih Material</option>';
    
    for (var i = 0; i < purchase_d.length; i++) {
        option+='<option value="'+purchase_d[i]['id']+'">('+purchase_d[i]['m_items']['no']+')'+purchase_d[i]['m_items']['name']+' ('+purchase_d[i]['purchase_assets']['no']+')'+'</option>';
    }
    var option_warehouse='<option value="">Pilih Gudang</option>';
    for (var i = 0; i < warehouse.length; i++) {
        option_warehouse+='<option value="'+warehouse[i]['id']+'">'+warehouse[i]['name']+'</option>';
    }
    var tdAdd='<tr>'+
        '<td><select id="id[]" name="id[]" class="form-control" onchange="cekPurchase()">'+option+'</select><input type="hidden" id="purchase_asset_id[]" name="purchase_asset_id[]" /></td>'+
        '<td><input type="hidden" id="m_item_id[]" name="m_item_id[]"/><input class="form-control" type="" id="m_item_no[]" name="m_item_no[]" readonly/></td>'+
        '<td><input type="" id="m_item_name[]" name="m_item_name[]" readonly class="form-control"/></td>'+
        '<td><input id="amount[]" name="amount[]" class="form-control text-right" type="text" readonly/></td>'+
        '<td><input id="receive_volume[]" required name="receive_volume[]" onkeyup="cekAmount()" class="form-control text-right" type="text"/></td>'+
        '<td><input type="hidden" id="m_unit_id[]" name="m_unit_id[]" /><input class="form-control" type="" id="m_unit_name[]" name="m_unit_name[]" readonly /></td>'+
        '<td><select id="m_warehouse_id[]" required name="m_warehouse_id[]" class="form-control">'+option_warehouse+'</select></td>'+
        '<td><input id="notes[]" required name="notes[]" class="form-control text-left" type="text" /><input id="price[]" name="price[]" class="form-control" type="hidden"/></td>'+
        '<td><select id="tipe_asset[]" required name="tipe_asset[]" class="form-control">'+option_tipe+'</select></td>'+
        '<td><input type="hidden" id="condition[]" name="condition[]" value="1" /><button type="button" class="btn btn-sm btn-danger removeOption"><i class="mdi mdi-delete"></i></button></td>'+
    '</tr>';
    $('#detail-order').find('tbody:last').append(tdAdd);
    // console.log(total_produk);
}
$("#detail-order").on("click", ".removeOption", function(event) {
    event.preventDefault();
    $(this).closest("tr").remove();
    cekAmount();
});

function cekPurchase(){
    var id = $('[id^=id]');
    var purchase_asset_id = $('[id^=purchase_asset_id]');
    var m_item_id = $('[id^=m_item_id]');
    var m_item_no = $('[id^=m_item_no]');
    var m_item_name = $('[id^=m_item_name]');
    var amount = $('[id^=amount]');
    var m_unit_id = $('[id^=m_unit_id]');
    var m_unit_name = $('[id^=m_unit_name]');
    var price = $('[id^=price]');
    for(var i = 0; i < id.length; i++){
        var pd_id=id.eq(i).val();
        if (pd_id == '') {
            purchase_asset_id.eq(i).val('');
            m_item_id.eq(i).val('');
            m_item_no.eq(i).val('');
            m_item_name.eq(i).val('');
            amount.eq(i).val('');
            m_unit_id.eq(i).val('');
            m_unit_name.eq(i).val('');
            price.eq(i).val('');
        }else{
            purchase_d.map((item, obj) => {
                if (item.id == pd_id){
                    purchase_asset_id.eq(i).val(item.purchase_asset_id);
                    m_item_id.eq(i).val(item.m_item_id);
                    m_item_no.eq(i).val(item.m_items.no);
                    m_item_name.eq(i).val(item.m_items.name);
                    amount.eq(i).val(item.amount);
                    m_unit_id.eq(i).val(item.m_units.id);
                    m_unit_name.eq(i).val(item.m_units.name);
                    price.eq(i).val(item.base_price);
                }
            });
        }
    }
}
function clickTerima() {
    driver = document.getElementById("driver");
    receive_volume = document.getElementById("receive_volume[]");
    notes = document.getElementById("notes[]");
    // if (driver.checkValidity() && receive_volume.checkValidity() && receive_volume.checkValidity()) {
    //     setTimeout(() => {
    //         window.open("{{ URL::to('penerimaan_barang/print/' . $purchase_asset_id) }}", '_blank');
    //     }, 500);
    // }
}

var total_purchase=0;
function cekAmount(){
    total_purchase=0;
    var id = $('[id^=id]');
    var amount = $('[id^=receive_volume]');
    var price = $('[id^=price]');
    var temp=[];
    for(var i = 0; i < id.length; i++){
        var pd_id=id.eq(i).val();
        var volume=amount.eq(i).val();
        var harga=price.eq(i).val();
        if (pd_id != '' && volume != '') {
            var is_there=false;
            var index=0;
            $.each(temp, function(j, item){
                    if (item.pd_id == pd_id) {
                        is_there=true;
                        index=j;
                    }
            });
            
            if (is_there == false) {
                temp.push({'pd_id' : pd_id, 'amount' : volume});
                $.each(purchase_d, function(j, item){
                    if (item.id == pd_id) {
                        if (item.amount < volume) {
                            amount.eq(i).val('');
                            alert('inputan melebihi total permintaan')
                        }
                    }
                })
            }else{
                temp[index]['amount']=parseFloat(volume)+parseFloat(temp[index]['amount']);
                $.each(purchase_d, function(j, item){
                    if (item.id == pd_id) {
                        if (item.amount < temp[index]['amount']) {
                            amount.eq(i).val('');
                            alert('inputan melebihi total permintaan')
                        }
                    }
                })
            }
            volume=amount.eq(i).val() != '' ? amount.eq(i).val() : 0;
            total_purchase+=parseFloat(volume) * parseFloat(harga);
        }
    }
    $('#total_bayar').val(total_purchase)
}
function cekTipe(val){
    if (val == 'card') {
        $('#bank').hide()
        $('#bank_no').hide()
        $('#card').show()
        $('#bank_an').hide()
    }else if(val == 'bank_transfer'){
        $('#card').hide()
        $('#bank_no').show()
        $('#bank').show()
        $('#bank_an').show()
    }else{
        $('#bank_no').hide()
        $('#bank').hide()
        $('#card').hide()
        $('#bank_an').hide()
    }
}

function addProductCorrupt(){
    var option='<option value="">Pilih Material</option>';
    
    for (var i = 0; i < purchase_d.length; i++) {
        option+='<option value="'+purchase_d[i]['id']+'">('+purchase_d[i]['m_items']['no']+')'+purchase_d[i]['m_items']['name']+' ('+purchase_d[i]['purchase_assets']['no']+')'+'</option>';
    }
    var option_warehouse='<option value="">Pilih Gudang</option>';
    for (var i = 0; i < warehouse.length; i++) {
        option_warehouse+='<option value="'+warehouse[i]['id']+'">'+warehouse[i]['name']+'</option>';
    }
    var tdAdd='<tr>'+
        '<td><select id="id[]" name="id[]" class="form-control" onchange="cekPurchase()">'+option+'</select><input type="hidden" id="purchase_asset_id[]" name="purchase_asset_id[]" /></td>'+
        '<td><input type="hidden" id="m_item_id[]" name="m_item_id[]"/><input class="form-control" type="" id="m_item_no[]" name="m_item_no[]" readonly/></td>'+
        '<td><input type="" id="m_item_name[]" name="m_item_name[]" readonly class="form-control"/></td>'+
        '<td><input id="amount[]" name="amount[]" class="form-control text-right" type="text" readonly/></td>'+
        '<td><input id="receive_volume[]" required name="receive_volume[]" onkeyup="cekAmount()" class="form-control text-right" type="text"/></td>'+
        '<td><input type="hidden" id="m_unit_id[]" name="m_unit_id[]" /><input class="form-control" type="" id="m_unit_name[]" name="m_unit_name[]" readonly /></td>'+
        '<td><input id="notes[]" required name="notes[]" class="form-control text-left" type="text" /><input id="price[]" name="price[]" class="form-control" type="hidden"/><select id="tipe_asset[]" name="tipe_asset[]" class="form-control hide"></select></td>'+
        '<td><select hidden id="m_warehouse_id[]" name="m_warehouse_id[]" class="form-control">'+option_warehouse+'</select><input type="hidden" id="condition[]" name="condition[]" value="0" /><button type="button" class="btn btn-sm btn-danger removeOption"><i class="mdi mdi-delete"></i></button></td>'+
    '</tr>';
    $('#detail-corrupt').find('tbody:last').append(tdAdd);
    // console.log(total_produk);
}
$("#detail-corrupt").on("click", ".removeOption", function(event) {
    event.preventDefault();
    $(this).closest("tr").remove();
    cekAmount();
});

function addProductCharge(){
    var option='<option value="">Pilih Material</option>';
    
    for (var i = 0; i < purchase_d.length; i++) {
        option+='<option value="'+purchase_d[i]['id']+'">('+purchase_d[i]['m_items']['no']+')'+purchase_d[i]['m_items']['name']+' ('+purchase_d[i]['purchase_assets']['no']+')'+'</option>';
    }
    var option_warehouse='<option value="">Pilih Gudang</option>';
    for (var i = 0; i < warehouse.length; i++) {
        option_warehouse+='<option value="'+warehouse[i]['id']+'">'+warehouse[i]['name']+'</option>';
    }
    var tdAdd='<tr>'+
        '<td><select id="id[]" name="id[]" class="form-control" onchange="cekPurchase()">'+option+'</select><input type="hidden" id="purchase_asset_id[]" name="purchase_asset_id[]" /></td>'+
        '<td><input type="hidden" id="m_item_id[]" name="m_item_id[]"/><input class="form-control" type="" id="m_item_no[]" name="m_item_no[]" readonly/></td>'+
        '<td><input type="" id="m_item_name[]" name="m_item_name[]" readonly class="form-control"/></td>'+
        '<td><input id="amount[]" name="amount[]" class="form-control text-right" type="text" readonly/></td>'+
        '<td><input id="receive_volume[]" required name="receive_volume[]" onkeyup="cekAmount()" class="form-control text-right" type="text"/></td>'+
        '<td><input type="hidden" id="m_unit_id[]" name="m_unit_id[]" /><input class="form-control" type="" id="m_unit_name[]" name="m_unit_name[]" readonly /></td>'+
        '<td><input id="notes[]" required name="notes[]" class="form-control text-left" type="text" /><input id="price[]" name="price[]" class="form-control" type="hidden"/><select id="tipe_asset[]" name="tipe_asset[]" class="form-control hide"></select></td>'+
        '<td><select class="hide" id="m_warehouse_id[]" name="m_warehouse_id[]">'+option_warehouse+'</select><input type="hidden" id="condition[]" name="condition[]" value="2" /><button type="button" class="btn btn-sm btn-danger removeOption"><i class="mdi mdi-delete"></i></button></td>'+
    '</tr>';
    $('#detail-charge').find('tbody:last').append(tdAdd);
    // console.log(total_produk);
}
$("#detail-charge").on("click", ".removeOption", function(event) {
    event.preventDefault();
    $(this).closest("tr").remove();
    cekAmount();
});
</script>


@endsection