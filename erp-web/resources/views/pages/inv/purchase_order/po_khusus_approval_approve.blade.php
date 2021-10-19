@extends('theme.default')

@section('breadcrumb')
			<div class="page-breadcrumb">
                <div class="row">
                    <div class="col-5 align-self-center">
                        <h4 class="page-title">PO Khusus Approval</h4>
                        <div class="d-flex align-items-center">
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="{{ URL::to('po_spesial_approval') }}">PO Khusus Approval</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">Form Approve</li>
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
                        <form method="POST" action="{{ URL::to('po_spesial_approval/approve') }}" class="form-horizontal r-separator">
                        @csrf
                            <input type="hidden" name="purchase_id" value="{{ $purchase['id'] }}" />
                            <div class="text-right">
                                <a href="{{ URL::to('po_spesial_approval') }}"><button type="button" class="btn btn-danger btn-sm mb-2">Cancel</button></a>
                                <button type="submit" class="btn btn-primary btn-sm mb-2">Approve</button>
                            </div>
                            <div class="card">
                                <div class="card-body">
                                    <h4 class="card-title">Form Approval</h4>
                                </div>
                                <hr>
                                <div class="card-body">
                                    <h4 class="card-title">Data Purchase Order</h4>
                                    <div class="form-group row align-items-center mb-0">
                                        <label class="col-sm-3 text-right control-label col-form-label">PO Number</label>
                                        <div class="col-9 border-left pb-2 pt-2">
                                            <label class="control-label col-form-label">{{ $purchase['no'] }}</label>
                                        </div>
                                    </div>
                                    <div class="form-group row align-items-center mb-0">
                                        <label class="col-sm-3 text-right control-label col-form-label">PO Date</label>
                                        <div class="col-9 border-left pb-2 pt-2">
                                            <label class="control-label col-form-label">{{ $purchase['purchase_date'] }}</label>
                                        </div>
                                    </div>
                                    <div class="form-group row align-items-center mb-0">
                                        <label class="col-sm-3 text-right control-label col-form-label">Decision</label>
                                        <div class="col-9 border-left pb-2 pt-2">
                                            <select id="apv_decision" name="apv_decision" required class="form-control select2 custom-select" style="width: 100%; height:32px;">
                                                <option value="">--- Select Decision ---</option>
                                                <option value="BUY">Pembelian Khusus</option>
                                                <option value="TRANSFER">Transfer Stok from HO</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <hr>
                                <div class="card-body">
                                    <h4 class="card-title">List Material</h4>
                                    <div class="table-responsive">
                                        <table id="zero_config" class="table table-bordered">
                                            <thead>
                                                <tr>
                                                    <th class="text-center">Nama Material</th>
                                                    <th class="text-center">Volume</th>
                                                    <th class="text-center">Satuan</th>
                                                    <th class="text-center">Perkiraan Harga</th>
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


<script src="{!! asset('theme/assets/libs/jquery/dist/jquery.min.js') !!}"></script>
<script src="{!! asset('theme/assets/extra-libs/DataTables/datatables.min.js') !!}"></script>
<script>

$(document).ready(function(){
    // console.log(arrMaterialPembelianRutin);
    t = $('#zero_config').DataTable();
    t.clear().draw(false);
    $.ajax({
            type: "GET",
            url: "{{ URL::to('po_konstruksi/detail/' . $purchase['id']) }}", //json get site
            dataType : 'json',
            success: function(response){
                arrData = response['data'];
                for(i = 0; i < arrData.length; i++){
                    t.row.add([
                        '<div class="text-left">'+arrData[i]['m_items']['name']+'</div>',
                        '<div class="text-right">'+arrData[i]['amount']+'</div>',
                        '<div class="text-center">'+arrData[i]['m_units']['name']+'</div>',
                        '<div class="text-right">'+(arrData[i]['base_price'] != null ? arrData[i]['base_price'] : "-")+'</div>'
                    ]).draw(false);
                }
            }
    });
});

</script>

@endsection