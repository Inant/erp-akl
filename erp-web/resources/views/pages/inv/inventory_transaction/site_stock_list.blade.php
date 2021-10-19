@extends('theme.default')

@section('breadcrumb')
<div class="page-breadcrumb">
    <div class="row">
        <div class="col-5 align-self-center">
            <h4 class="page-title">Site Stock</h4>
            <div class="d-flex align-items-center">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item active" aria-current="page">Site Stock</li>
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
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Site Stock</h4>
                    <ul class="nav nav-tabs" role="tablist">
                        <li class="nav-item"> <a class="nav-link active" data-toggle="tab" href="#material_utuh" role="tab"><span class="hidden-sm-up"><i class="mdi mdi-solid"></i></span> <span class="hidden-xs-down">Material Utuh</span></a> </li>
                        <li class="nav-item" onclick="refreshRestMaterial()"> <a class="nav-link" data-toggle="tab" href="#material_tidak_utuh" role="tab"><span class="hidden-sm-up"><i class="mdi mdi-view-grid"></i></span> <span class="hidden-xs-down">Material Tidak Utuh</span></a> </li>
                    </ul>
                    <!-- Tab panes -->
                    <div class="tab-content tabcontent-border">
                        <div class="tab-pane active" id="material_utuh" role="tabpanel">
                            <br>
                            <a hidden href="{{URL::to('inventory/calc_stock')}}"><button class="btn-primary btn">Hitung Ulang Stok</button></a>
                            <!-- <br><br> -->
                            <div class="table-responsive">
                            <a href="{{URL::to('inventory/export_stock')}}" target="_blank"><button class="btn btn-success mb-2"><i class="mdi mdi-file-excel"></i>&nbsp; Export Stok</button></a>
                                <table id="zero_config" class="table table-striped table-bordered">
                                    <thead>
                                        <tr>
                                            <!-- <th class="text-center">Inv No</th> -->
                                            <!-- <th class="text-center">Site Name</th> -->
                                            <th class="text-center">Gudang</th>
                                            <th class="text-center">Material No</th>
                                            <th class="text-center">Material Name</th>
                                            <th class="text-center">Nilai Material</th>
                                            <th class="text-center">Harga Satuan</th>
                                            <th class="text-center">Satuan</th>
                                            <th class="text-center">Stock In</th>
                                            <th class="text-center">Stock Out</th>
                                            <th class="text-center">Current Stock</th>
                                            <th class="text-center">Last Update</th>
                                            <th class="text-center">Umur</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                        <div class="tab-pane" id="material_tidak_utuh" role="tabpanel">
                            <br>
                            <div class="table-responsive">
                                <table id="zero_config2" class="table table-striped table-bordered">
                                    <thead>
                                        <tr>
                                            <!-- <th class="text-center">Inv No</th> -->
                                            <th class="text-center">Site Name</th>
                                            <th class="text-center">Gudang</th>
                                            <th class="text-center">Material No</th>
                                            <th class="text-center">Material Name</th>
                                            <th class="text-center">Kondisi</th>
                                            <th class="text-center">Harga Satuan</th>
                                            <th class="text-center">Satuan</th>
                                            <th class="text-center">Stock In</th>
                                            <th class="text-center">Stock Out</th>
                                            <th class="text-center">Current Stock</th>
                                            <th class="text-center">Last Update</th>
                                            <th class="text-center">Umur</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
                
</div>

<script src="{!! asset('theme/assets/libs/jquery/dist/jquery.min.js') !!}"></script>
<script src="{!! asset('theme/assets/extra-libs/DataTables/datatables.min.js') !!}"></script>

<script>
var m_warehouse_id='{{$m_warehouse_id}}';
$(document).ready(function(){
    t = $('#zero_config').DataTable();
    t.clear().draw(false);
    $.ajax({
        type: "GET",
        url: "{{ URL::to('inventory/stok_json') }}", //json get site
        dataType : 'json',
        success: function(response){
            arrData = response['data'];
            
            for(i = 0; i < arrData.length; i++){
                last_update_in = arrData[i]['last_update_in'] == null ? '1990-01-01' : arrData[i]['last_update_in'].substr(0,10);
                last_update_out = arrData[i]['last_update_out'] == null ? '1990-01-01' : arrData[i]['last_update_out'].substr(0,10);
                last_update = new Date(last_update_in) > new Date(last_update_out) ? last_update_in : last_update_out;
                warehouse_name=arrData[i]['m_warehouse'] != null ? arrData[i]['m_warehouse']['name'] : '';
                if (m_warehouse_id != '') {
                    if (m_warehouse_id == arrData[i]['m_warehouse_id']) {
                        t.row.add([
                            // '<div class="text-left">'+arrData[i]['no']+'</div>',
                            // '<div class="text-left">'+arrData[i]['sites']['name']+'</div>',
                            '<div class="text-left">'+(arrData[i]['m_warehouse'] != null ? arrData[i]['m_warehouse']['name'] : '')+'</div>',
                            '<div class="text-left">'+arrData[i]['m_items']['no']+'</div>',
                            '<div class="text-left">'+arrData[i]['m_items']['name']+'</div>',
                            // '<div class="text-right">'+formatCurrency(arrData[i]['value'])+'</div>',
                            '<div class="text-right">'+formatCurrency(parseFloat(arrData[i]['amount_in'])*parseInt(arrData[i]['last_price']))+'</div>',
                            '<div class="text-right">'+formatCurrency(parseInt(arrData[i]['last_price']))+'</div>',
                            '<div class="text-left">'+arrData[i]['m_units']['name']+'</div>',
                            '<div class="text-center">'+parseFloat(arrData[i]['amount_in'])+'</div>',
                            '<div class="text-center">'+(parseFloat(arrData[i]['amount_out']))+'</div>',
                            // '<div class="text-center">'+(parseFloat(arrData[i]['amount_out']) - parseFloat(arrData[i]['amount_ret']))+'</div>',
                            '<div class="text-center">'+parseFloat(arrData[i]['stok'])+'</div>',
                            '<div class="text-center">'+formatDateID(new Date(last_update))+'</div>',
                            '<div class="text-center">'+datediffnow(last_update)+'</div>',
                        ]).draw(false);
                    }
                }else{
                    t.row.add([
                        // '<div class="text-left">'+arrData[i]['no']+'</div>',
                        // '<div class="text-left">'+arrData[i]['sites']['name']+'</div>',
                        '<div class="text-left">'+(arrData[i]['m_warehouse'] != null ? arrData[i]['m_warehouse']['name'] : '')+'</div>',
                        '<div class="text-left">'+arrData[i]['m_items']['no']+'</div>',
                        '<div class="text-left">'+arrData[i]['m_items']['name']+'</div>',
                        // '<div class="text-right">'+formatCurrency(arrData[i]['value'])+'</div>',
                        '<div class="text-right">'+formatCurrency(parseFloat(arrData[i]['amount_in'])*parseInt(arrData[i]['last_price']))+'</div>',
                        '<div class="text-right">'+formatCurrency(parseInt(arrData[i]['last_price']))+'</div>',
                        '<div class="text-left">'+arrData[i]['m_units']['name']+'</div>',
                        '<div class="text-center">'+parseFloat(arrData[i]['amount_in'])+'</div>',
                        '<div class="text-center">'+(parseFloat(arrData[i]['amount_out']))+'</div>',
                        // '<div class="text-center">'+(parseFloat(arrData[i]['amount_out']) - parseFloat(arrData[i]['amount_ret']))+'</div>',
                        '<div class="text-center">'+parseFloat(arrData[i]['stok'])+'</div>',
                        '<div class="text-center">'+formatDateID(new Date(last_update))+'</div>',
                        '<div class="text-center">'+datediffnow(last_update)+'</div>',
                    ]).draw(false);
                }
            }
        }
    });
});

function refreshRestMaterial(){
    t2 = $('#zero_config2').DataTable();
    t2.clear().draw(false);
    $.ajax({
        type: "GET",
        url: "{{ URL::to('inventory/stok_rest_json') }}", //json get site
        dataType : 'json',
        success: function(response){
            arrData = response['data'];
            
            for(i = 0; i < arrData.length; i++){
                last_update_in = arrData[i]['last_update_in'] == null ? '1990-01-01' : arrData[i]['last_update_in'].substr(0,10);
                last_update_out = arrData[i]['last_update_out'] == null ? '1990-01-01' : arrData[i]['last_update_out'].substr(0,10);
                last_update = new Date(last_update_in) > new Date(last_update_out) ? last_update_in : last_update_out;
                if (m_warehouse_id != '') {
                    if (m_warehouse_id == arrData[i]['m_warehouse_id']) {
                        t2.row.add([
                            // '<div class="text-left">'+arrData[i]['no']+'</div>',
                            '<div class="text-left">'+arrData[i]['sites']['name']+'</div>',
                            '<div class="text-left">'+arrData[i]['m_warehouse']['name']+'</div>',
                            '<div class="text-left">'+arrData[i]['m_items']['no']+'</div>',
                            '<div class="text-left">'+arrData[i]['m_items']['name']+'</div>',
                            '<div class="text-right">'+arrData[i]['amount_rest']+'</div>',
                            '<div class="text-right">'+formatCurrency(parseInt(arrData[i]['last_price']))+'</div>',
                            '<div class="text-left">'+arrData[i]['m_units']['name']+'</div>',
                            '<div class="text-center">'+parseFloat(arrData[i]['amount_in'])+'</div>',
                            '<div class="text-center">'+parseFloat(arrData[i]['amount_out'])+'</div>',
                            '<div class="text-center">'+parseFloat(arrData[i]['stok'])+'</div>',
                            '<div class="text-center">'+formatDateID(new Date(last_update))+'</div>',
                            '<div class="text-center">'+datediffnow(last_update)+'</div>',
                        ]).draw(false);
                    }
                }else{
                    t2.row.add([
                        // '<div class="text-left">'+arrData[i]['no']+'</div>',
                        '<div class="text-left">'+arrData[i]['sites']['name']+'</div>',
                        '<div class="text-left">'+arrData[i]['m_warehouse']['name']+'</div>',
                        '<div class="text-left">'+arrData[i]['m_items']['no']+'</div>',
                        '<div class="text-left">'+arrData[i]['m_items']['name']+'</div>',
                        '<div class="text-right">'+arrData[i]['amount_rest']+'</div>',
                        '<div class="text-right">'+formatCurrency(parseInt(arrData[i]['last_price']))+'</div>',
                        '<div class="text-left">'+arrData[i]['m_units']['name']+'</div>',
                        '<div class="text-center">'+parseFloat(arrData[i]['amount_in'])+'</div>',
                        '<div class="text-center">'+parseFloat(arrData[i]['amount_out'])+'</div>',
                        '<div class="text-center">'+parseFloat(arrData[i]['stok'])+'</div>',
                        '<div class="text-center">'+formatDateID(new Date(last_update))+'</div>',
                        '<div class="text-center">'+datediffnow(last_update)+'</div>',
                    ]).draw(false);
                }
            }
        }
    });
}

function datediffnow(date) {
    dt1 = new Date(date);
    dt2 = new Date();
    return Math.floor((Date.UTC(dt2.getFullYear(), dt2.getMonth(), dt2.getDate()) - Date.UTC(dt1.getFullYear(), dt1.getMonth(), dt1.getDate()) ) /(1000 * 60 * 60 * 24));
}
</script>

@endsection