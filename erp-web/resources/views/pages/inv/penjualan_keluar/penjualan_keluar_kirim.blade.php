@extends('theme.default')

@section('breadcrumb')
<div class="page-breadcrumb">
    <div class="row">
        <div class="col-5 align-self-center">
            <h4 class="page-title">Form Penjualan Material</h4>
            <div class="d-flex align-items-center">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ URL::to('material_request') }}">Penjualan Material</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Form Penjualan Material</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
</div>
@endsection

@section('content')
<form method="POST" action="{{ URL::to('penjualan_keluar/save_kirim') }}" class="form-horizontal">
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
                
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Penjualan Material Header</h4>
                        <input type="hidden" name="id" value="{{$id}}">
                        <br>
                        <table style="width:100%">
                            <thead>
                                <tr>
                                    <td>Nomor</td>
                                    <td>:</td>
                                    <td>{{$data->no}}</td>
                                    <td>Customer</td>
                                    <td>:</td>
                                    <td>{{$data->coorporate_name}}</td>
                                </tr>
                                <tr>
                                    <td>Tanggal Permintaan</td>
                                    <td>:</td>
                                    <td>{{date('d-m-Y', strtotime($data->created_at))}}</td>
                                    <td>Gudang</td>
                                    <td>:</td>
                                    <td>{{$data->warehouse_name}}</td>
                                </tr>
                            </thead>
                        </table>
                        <br><br>
                        <div class="table-responsive">
                            <table id="requestDetail" class="table table-striped table-bordered display" style="width:100%">
                                <thead>
                                    <tr>
                                        <th class="text-center">Material No</th>
                                        <th class="text-center">Material Name</th>
                                        <!-- <th class="text-center">Pilih</th> -->
                                        <th class="text-center">Site Stock</th>
                                        <th class="text-center">Qty Pengajuan</th>
                                        <th class="text-center">Qty Utuh</th>
                                        <th class="text-center">Satuan</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                            <!-- <div class="text-right"> -->
                                <a href="{{ URL::to('pengeluaran_barang') }}" class="btn btn-danger mb-2">Cancel</a>
                                <button type="submit" id="submit" class="btn btn-info mb-2">Konfirmasi</button>
                            <!-- </div> -->
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

// var t = $('#requestDetail_addrow').DataTable();
// var t2 = $('#requestDetail').DataTable();
var counter = 1;

var listMaterialRab = [];
var id = {{$id}};
// List Stock
var listStockSite = [];
var listStockRestSite = [];

$(document).ready(async function(){
    let site_id = {{ $site_id }};
    // getProjectName(site_id);
    await $.ajax({
        type: "GET",
        url: "{{ URL::to('inventory/stok_json') }}", //json get site
        dataType : 'json',
        success: function(response){
            arrData = response['data'];
            listStockSite = arrData;
        }
    });

    // t2.clear().draw(false);
    $('#requestDetail > tbody').empty();
    
    $.ajax({
            type: "GET",
            url: "{{ URL::to('penjualan_keluar/pengajuan_detail') }}" + "/" + id, //json get site
            dataType : 'json',
            success: function(response){
                arrData = response['data'];
                listMaterialRab=arrData;
                for(i = 0; i < arrData.length; i++){
                    stok = 0;
                    listStockSite.map((item, obj) => {
                        if (item.m_item_id == arrData[i]['m_item_id'] && item.m_warehouse_id == arrData[i]['m_warehouse_id'] && item.type == 'STK_NORMAL'){
                            stok += parseInt(item.stok);
                        }
                    });
                    let amount_auth = arrData[i]['amount_auth'] != null ? arrData[i]['amount_auth'] : arrData[i]['amount'];
                    var amount_total=parseFloat(amount_auth) - parseFloat(arrData[i]['total_used']);
                    var tdAdd='<tr>'+
                            '<td>'+
                                '<div class="text-left">'+arrData[i]['m_items']['no']+'</div>' +
                            '</td>'+
                            '<td>'+
                                '<input type="hidden" name="inv_sale_d_id[]" value="'+arrData[i]['id']+'" /><input type="hidden" name="m_item_id[]" value="'+arrData[i]['m_item_id']+'" /><div class="text-left">'+arrData[i]['m_items']['name']+'</div>' +
                            '</td>'+
                            '<td>'+
                                '<input type="hidden" id="stok[]" name="stok[]" value="'+parseFloat(stok)+'" /> <p class="text-right" id="label_stok[]">'+parseFloat(stok)+'</p>' +
                            '</td>'+
                            '<td>'+
                                '<div class="text-right"><input type="hidden" id="amount[]" name="amount[]" step="any" min="0" class="form-control text-right" placeholder="0" value="'+parseFloat(amount_total)+'">'+parseFloat(amount_total)+'</div>' +
                            '</td>'+
                            '<td>'+
                                '<div class="text-right"><input type="" id="qty[]" name="qty[]" min="0" class="form-control text-right" value="'+parseFloat(amount_total)+'" required onkeyup="cekQty()" oninput="this.value=(parseInt(this.value)||0)"></div>' +
                            '</td>'+
                            '<td>'+
                                '<input type="hidden" name="price[]" value="'+arrData[i]['base_price']+'" /><input type="hidden" name="m_unit_id[]" value="'+arrData[i]['m_units']['id']+'" /><input type="hidden" name="m_warehouse_id[]" value="'+arrData[i]['m_warehouse_id']+'" /><div class="text-center">'+arrData[i]['m_units']['name']+'</div>' +
                                '<select hidden name="type_stok[]" onchange="cekTypeStok()" class="form-control" id="type_stok[]"><option value="STK_NORMAL">Stok Normal</option><option value="TRF_STK">Stok Transfer</option></select>' +
                            '</td>'+
                        '</tr>';
                    $('#requestDetail').find('tbody:last').append(tdAdd);

                    formInvNo = $('[id^=inv_no]');
                    formInvNo.empty();
                    formInvNo.append('<option value="">-- Select Inv Number --</option>');
                    listStockSite.map((item, obj) => {
                        if (item.m_item_id == arrData[i]['m_item_id'])
                        formInvNo.append('<option value="'+item.purchase_d_id+'">'+item.no+'</option>');
                    });
                    if(stok < amount_auth){
                        valid = false;
                    }
                    cekStock();
                }
            }
    });
});
function cekStock(){
    var is_out_stock=false;
    var qty = $('[id^=qty]');
    var stock = $('[name^=stok]');
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
        $('#submit').prop('disabled', true)
    }else{
        $('#submit').prop('disabled', false)
    }
}

function cekQty(){
    var qty = $('[id^=qty]');
    var amount = $('[id^=amount]');
    for(var i = 0; i < qty.length; i++){
        var total_req=qty.eq(i).val();
        var total=amount.eq(i).val();
        if(parseFloat(total_req) > parseFloat(total)){
            qty.eq(i).val('');
            alert('inputan melebihi request yang ada');
        }
    }
    cekStock()
}

function cekTypeStok(){
    var type_stok = $('[id^=type_stok]');
    var item=$('[name^=m_item_id]');
    var warehouse_id=$('[name^=m_warehouse_id]');
    var stok=$('[name^=stok]');
    var label_stok=$('[id^=label_stok]');
    for(var i = 0; i < type_stok.length; i++){
        var m_item_id=item.eq(i).val();
        var m_warehouse_id=warehouse_id.eq(i).val();
        var type_stk=type_stok.eq(i).val();
        stock=0;
        listStockSite.map((item, obj) => {
            
            if (item.m_item_id == m_item_id && item.m_warehouse_id == m_warehouse_id && item.type == type_stk){
                stock=item.stok;
            }
        });
        stok.eq(i).val(stock);
        label_stok.eq(i).html(stock);
    }
    cekQty();
}

function getMItem(){
    var id = $('[id^=inv_req_d_id_rest]');
    var amount_child = $('[id^=amount_child]');
    var label_child = $('[id^=label_child]');
    for(var i = 0; i < id.length; i++){
        var id_rest='';
        var rest_amount=0;
        var turunan=0;
        var satuan=0;
        listMaterialRab.map((item, obj) => {
            if (item.id == id.eq(i).val()){
                id_rest=item.m_item_id;
                rest_amount=item.m_items.amount_unit_child;
                turunan=(item.m_unit_child != null ? item.m_unit_child.name : '-');
                satuan=item.m_units.name;
            }
        });
        label_child.eq(i).val(rest_amount+' '+turunan+' / '+satuan);
        amount_child.eq(i).val(rest_amount);
        // console.log(id_rest)
        $('[id^=m_item_rest_id]').eq(i).val(id_rest);
    }
}

</script>
@endsection