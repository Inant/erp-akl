@extends('theme.default')

@section('breadcrumb')
			<div class="page-breadcrumb">
                <div class="row">
                    <div class="col-5 align-self-center">
                        <h4 class="page-title">Pengiriman Transfer Stok</h4>
                        <div class="d-flex align-items-center">
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="{{ URL::to('pengiriman_ts') }}">Pengiriman Transfer Stok</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">Form Pengiriman</li>
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
        <form method="POST" action="{{ URL::to('pengiriman_ts/kirim') }}" class="form-horizontal">
            @csrf
            <input type="hidden" name="transfer_stock_id" value="{{ $id }}"/>
            <div class="text-right">
                <a href="{{ URL::to('pengiriman_ts/tolak/'.$id) }}"><button class="btn btn-danger btn-sm mb-2">Tolak</button></a>
                <button type="submit" class="btn btn-warning btn-sm mb-2">Kirim</button>
            </div>
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Form Material Pengiriman</h4>
                    <div class="table-responsive">
                        <table id="zero_config" class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th class="text-center">Material Number</th>
                                    <th class="text-center">Material Name</th>
                                    <th class="text-center">Stok</th>
                                    <th class="text-center">Volume Pengajuan</th>
                                    <th class="text-center">Volume Kirim</th>
                                    <th class="text-center">Satuan</th>
                                </tr>
                            </thead>
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

    t = $('#zero_config').DataTable();
    t.clear().draw(false);
    $.ajax({
            type: "GET",
            url: "{{ URL::to('transfer_stok/list_detail/'.$id) }}", //json get site
            dataType : 'json',
            success: function(response){
                arrData = response['data'];
                console.log(arrData);
                for(i = 0; i < arrData.length; i++){
                    stok = 0;
                    listStockSite.map((item, obj) => {
                        if (item.m_item_id == arrData[i]['m_item_id'])
                            stok = item.stok;
                    });

                    t.row.add([
                        '<div class="text-center"><input type="hidden" name="m_item_id[]" value="'+arrData[i]['m_items']['id']+'">'+arrData[i]['m_items']['no']+'</div>',
                        '<div class="text-left">'+arrData[i]['m_items']['name']+'</div>',
                        '<div class="text-right">' + stok + '</div>',
                        '<div class="text-right">'+arrData[i]['amount']+'</div>',
                        '<div class="text-center"><input type="hidden" name="transfer_stock_d_id[]" value="'+arrData[i]['id']+'"><input type="number" id="actual_amount[]" name="actual_amount[]" step="any" min="0" max="'+arrData[i]['amount']+'" class="form-control text-right" value="'+arrData[i]['amount']+'"  required></div>',
                        '<div class="text-right"><input type="hidden" name="m_unit_id[]" value="'+arrData[i]['m_units']['id']+'">'+arrData[i]['m_units']['name']+'</div>'
                    ]).draw(false);
                }
            }
    });
});
</script>


@endsection