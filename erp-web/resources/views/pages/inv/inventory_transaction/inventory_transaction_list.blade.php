@extends('theme.default')

@section('breadcrumb')
<div class="page-breadcrumb">
    <div class="row">
        <div class="col-5 align-self-center">
            <h4 class="page-title">Mutasi Stok</h4>
            <div class="d-flex align-items-center">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item active" aria-current="page">Mutasi Stok</li>
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
                <form method="POST" action="{{ URL::to('inventory') }}" class="form-horizontal">
                @csrf
                    <div class="card-body">
                        <h4 class="card-title">Mutasi Stok</h4>
                        <div class="form-group row">
                            <label class="col-sm-2 text-right control-label col-form-label">Date >=</label>
                            <div class="col-sm-4">
                                <input type="date" name="date_gte" value="{{ $date_gte }}" class="form-control"/>
                            </div>
                            <label class="col-sm-2 text-right control-label col-form-label">Date <=</label>
                            <div class="col-sm-4">
                                <input type="date" name="date_lte" value="{{ $date_lte }}" class="form-control"/>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-2 text-right control-label col-form-label">In/Out</label>
                            <div class="col-sm-4">
                                <select id="is_entry" name="is_entry" value="{{ $is_entry }}" class="form-control select2 custom-select" style="width: 100%; height:32px;">
                                    <option value="all">-- Semua --</option>
                                    <option value="true">In</option>
                                    <option value="false">Out</option>
                                </select>
                            </div>
                            <label class="col-sm-2 text-right control-label col-form-label">Material Name</label>
                            <div class="col-sm-4">
                                <select id="m_item_id" name="m_item_id" value="{{ $m_item_id }}" class="form-control select2 custom-select" style="width: 100%; height:32px;">
                                    <option value="all">-- Semua --</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-2 text-right control-label col-form-label">Gudang</label>
                            <div class="col-sm-4">
                                <select id="m_warehouse_id" name="m_warehouse_id" class="form-control select2 custom-select" style="width: 100%; height:32px;">
                                    <option value="">-- Semua --</option>
                                    @foreach ($warehouse as $item)
                                        <option value="{{$item->id}}">{{$item->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="text-right" style="margin-top:10px;">
                            <button type="submit" class="btn btn-info btn-sm mb-2">Filter</button>
                        </div>
                        
                        @if(is_countable($data) && count($data) > 0)
                        <div class="table-responsive">
                            <table id="zero_config" class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th class="text-center">Material No</th>
                                        <th class="text-center">Material Name</th>
                                        <th class="text-center">Gudang</th>
                                        <th class="text-center">Volume</th>
                                        <th class="text-center">Satuan</th>
                                        <th class="text-center">Date Mutation</th>
                                        <th class="text-center">Value</th>
                                        <th class="text-center">In/Out</th>
                                        <th class="text-center">Kavling</th>
                                        <th class="text-center">Notes</th>
                                        <th class="text-center">Nomor</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($data as $item)
                                    <tr>
                                        <td class="text-center">{{ $item['m_items']['no'] }}</td>
                                        <td class="text-center">{{ $item['m_items']['name'] }}</td>
                                        <td class="text-center">{{ $item['m_warehouse'] != null ? $item['m_warehouse']['name'] : '-' }}</td>                                      
                                        <td class="text-center">{{ (float)$item['amount'] }}</td>
                                        <td class="text-center">{{ $item['m_units']['name'] }}</td>
                                        <td class="text-center">{{ date('d-m-Y', strtotime($item['inv_trx_date'])) }}</td>
                                        <td class="text-right">{{ number_format(((float)$item['value'] * (float)$item['amount']) , 0, ",", ".") }}</td>
                                        <td class="text-center">{{ $item['is_entry'] ? 'In' : 'Out' }}</td>
                                        <td class="text-center">{{ (isset($item['inv_request']['project']) ? $item['inv_request']['project']['name'] : '-') }}</td>
                                        <td class="text-center">{{ $item['transfer_stock_id'] != null ? ($item['is_entry'] ? 'Transfer From ' : 'Transfer To ') . $item['transfer_stok']['sites_from']['name'] : ($item['purchase_asset_id'] != null ? 'Purchase Asset From ' . $item['purchase_asset']['m_suppliers']['name'].' No Po '. $item['purchase_asset']['no'] : ($item['purchase_id'] != null ? 'Purchase From ' . $item['purchase']['m_suppliers']['name'] .' No Po '. $item['purchase']['no'] : (isset($item['inv_request']) ? 'Pengeluaran Material No : '. $item['inv_request']['no'] : ($item['ts_warehouse_id'] ? 'Transfer Stok '.$item['m_warehouse']['name'].' No Permintaan : '. $item['ts_warehouse']['no'] : 'Alat Bantu Kerja')))) }}</td>
                                        <td class="text-center">{{ (isset($item['no_trx']) ? $item['no_trx'] : '-') }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @endif
                    </div>
                </form>
            </div>
        </div>
    </div>
                
</div>

<script src="{!! asset('theme/assets/libs/jquery/dist/jquery.min.js') !!}"></script>
<script src="{!! asset('theme/assets/extra-libs/DataTables/datatables.min.js') !!}"></script>

<script>
var arrMaterial = [];

$(document).ready(function(){
    $.ajax({
        type: "GET",
        url: "{{ URL::to('material_request/get_material') }}", //json get material
        dataType : 'json',
        success: function(response){
            arrMaterial = response['data'];
            $.each(arrMaterial, function(i, item) {
                formMaterial = $('[id^=m_item_id]');
                formMaterial.append('<option value="'+arrMaterial[i]['id']+'">'+arrMaterial[i]['no'] + ' - ' + arrMaterial[i]['name']+'</option>');
            });
        }
    });

});
$.each(arrMaterial, function(i, item) {
    formMaterial = $('[id^=m_item_id]').eq(i);
    formMaterial.append('<option value="'+arrMaterial[i]['id']+'">'+arrMaterial[i]['name']+'</option>');
});
</script>

@endsection