@extends('theme.default')

@section('breadcrumb')
			<div class="page-breadcrumb">
                <div class="row">
                    <div class="col-5 align-self-center">
                        <h4 class="page-title">Penerimaan Transfer Stok Gudang</h4>
                        <div class="d-flex align-items-center">
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="{{ URL::to('Penerimaan_ts') }}">Penerimaan Transfer Stok Gudang</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">Form Penerimaan</li>
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
        <form method="POST" action="{{ URL::to('transfer_stok/form_terima_ts_warehouse') }}" class="form-horizontal">
            @csrf
            <input type="hidden" name="ts_warehouse_id" value="{{ $id }}"/>
            <div class="text-right">
                <!-- <a href="{{ URL::to('Penerimaan_ts/tolak/'.$id) }}"><button class="btn btn-danger btn-sm mb-2">Tolak</button></a> -->
                <button type="submit" class="btn btn-primary btn-sm mb-2">Terima</button>
            </div>
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Form Material Penerimaan</h4>
                    <div class="table-responsive">
                        <table id="requestDetail" class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th class="text-center">Material Number</th>
                                    <th class="text-center">Material Name</th>
                                    <th class="text-center">Total yang di Kirim</th>
                                    <th class="text-center">Total yang di Terima</th>
                                    <th class="text-center">Satuan</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>         
</div>

<script src="{!! asset('theme/assets/libs/jquery/dist/jquery.min.js') !!}"></script>
<script src="{!! asset('theme/assets/extra-libs/DataTables/datatables.min.js') !!}"></script>
<script>
    
// List Stock
var listStockSite = [];

$(document).ready(async function(){
    // Get Stock
    await $.ajax({
        type: "GET",
        url: "{{ URL::to('inventory/stok_json') }}", //json get site
        dataType : 'json',
        success: function(response){
            arrData = response['data'];
            listStockSite = arrData;
        }
    });
    var warehouse_id='{{$ts_warehouse['warehouse_from']}}';
    // console.log(warehouse_id);
    // t = $('#zero_config').DataTable();
    // t.clear().draw(false);
    $.ajax({
            type: "GET",
            url: "{{ URL::to('transfer_stok/detail_tsw') }}" + "/" + {{$id}}, //json get site
            dataType : 'json',
            success: function(response){
                arrData = response['data'];
                for(i = 0; i < arrData.length; i++){
                    // t.row.add([
                    //     '<div class="text-center"><input type="hidden" name="m_item_id[]" value="'+arrData[i]['m_item_id']+'">'+arrData[i]['material_no']+'</div>',
                    //     '<div class="text-left">'+arrData[i]['material_name']+'</div>',
                    //     '<div class="text-right">'+arrData[i]['actual_amount']+'</div>',
                    //     '<div class="text-center"><input type="hidden" name="ts_warehouse_d_id[]" value="'+arrData[i]['id']+'"><input type="hidden" id="actual_amount[]" name="actual_amount[]" class="form-control text-right" value="'+arrData[i]['actual_amount']+'"  required><input type="number" id="qty[]" name="qty[]" class="form-control text-right" value="0" onkeyup="cekQty()" required></div>',
                    //     '<div class="text-right"><input type="hidden" name="m_unit_id[]" value="'+arrData[i]['m_unit_id']+'">'+arrData[i]['unit_name']+'</div>'
                    // ]).draw(false);
                    var tdAdd='<tr>'+
                        '<td>'+
                            '<div class="text-center"><input type="hidden" name="m_item_id[]" value="'+arrData[i]['m_item_id']+'">'+arrData[i]['material_no']+'</div>' +
                        '</td>'+
                        '<td>'+
                            '<div class="text-left">'+arrData[i]['material_name']+'</div>' +
                        '</td>'+
                        '<td>'+
                            '<div class="text-right">'+arrData[i]['actual_amount']+'</div>' +
                        '</td>'+
                        '<td>'+
                            '<div class="text-center"><input type="hidden" name="ts_warehouse_d_id[]" value="'+arrData[i]['id']+'"><input type="hidden" id="actual_amount[]" name="actual_amount[]" class="form-control text-right" value="'+arrData[i]['actual_amount']+'"  required><input type="number" id="qty[]" name="qty[]" class="form-control text-right" value="0" onkeyup="cekQty()" required></div>' +
                        '</td>'+
                        '<td>'+
                            '<div class="text-right"><input type="hidden" name="m_unit_id[]" value="'+arrData[i]['m_unit_id']+'">'+arrData[i]['unit_name']+'</div>' +
                        '</td>'+
                    '</tr>';
                    $('#requestDetail').find('tbody:last').append(tdAdd);
                }
            }
    });
});
function cekQty(){
    var qty = $('[id^=qty]');
    var actual_amount = $('[id^=actual_amount]');
    for(var i = 0; i < qty.length; i++){
        var total_req=qty.eq(i).val();
        var total=actual_amount.eq(i).val();
        if(parseFloat(total_req) > parseFloat(total)){
            qty.eq(i).val('');
            alert('inputan melebihi total yang dikirim');
        }
    }
}
</script>


@endsection