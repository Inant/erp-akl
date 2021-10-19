@extends('theme.default')

@section('breadcrumb')
			<div class="page-breadcrumb">
                <div class="row">
                    <div class="col-5 align-self-center">
                        <h4 class="page-title">Pembelian Khusus</h4>
                        <div class="d-flex align-items-center">
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="{{ URL::to('pembelian_khusus') }}">Pembelian Khusus</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">Form Pembelian</li>
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
                        <form method="POST" action="{{ URL::to('pembelian_khusus') }}" class="form-horizontal r-separator">
                        @csrf
                            <input type="hidden" name="purchase_id" value="{{ $purchase['id'] }}" />
                            <div class="text-right">
                                <a href="{{ URL::to('pembelian_khusus') }}"><button type="button" class="btn btn-danger btn-sm mb-2">Cancel</button></a>
                                <button type="submit" class="btn btn-primary btn-sm mb-2">Submit</button>
                            </div>
                            <div class="card">
                                <div class="card-body">
                                    <h4 class="card-title">Form Pembelian Khusus</h4>
                                </div>
                                <hr>
                                <div class="card-body">
                                    <h4 class="card-title">Data Pembelian</h4>
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
                                        <label class="col-sm-3 text-right control-label col-form-label">Supplier</label>
                                        <div class="col-9 border-left pb-2 pt-2">
                                            <select id="suppl" name="suppl" required class="form-control select2 custom-select" style="width: 100%; height:32px;"></select>
                                        </div>
                                    </div>
                                    <div class="form-group row align-items-center mb-0">
                                        <label class="col-sm-3 text-right control-label col-form-label">Way of Payment</label>
                                        <div class="col-9 border-left pb-2 pt-2">
                                            <select id="cara_bayar" name="cara_bayar" required class="form-control select2 custom-select" style="width: 100%; height:32px;">
                                                <option value="cash">Cash</option>
                                                <option value="credit">Credit</option>
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
                                                    <th class="text-center">Perkiraan Harga Supplier</th>
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

arrSuppl = [];
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
                        '<input type="hidden" name="purchase_d_id[]" value="'+arrData[i]['id']+'" /><div class="text-left">'+arrData[i]['m_items']['name']+'</div>',
                        '<input type="hidden" name="amount[]" value="'+arrData[i]['amount']+'" /><div class="text-right">'+arrData[i]['amount']+'</div>',
                        '<div class="text-center">'+arrData[i]['m_units']['name']+'</div>',
                        '<input id="perkiraan_harga_suppl[]" required name="perkiraan_harga_suppl[]" class="form-control text-right" type="number" />'
                    ]).draw(false);
                }
            }
    });

    formSuppl = $('[id^=suppl]');
    formSuppl.empty();
    formSuppl.append('<option value="">-- Select Supplier --</option>');
    $.ajax({
        type: "GET",
        url: "{{ URL::to('pembelian_rutin/supplier') }}", //json get site
        dataType : 'json',
        success: function(response){
            arrSuppl = response['data']; 
            // console.log(arrSuppl);
            for(j = 0; j < arrSuppl.length; j++){
                formSuppl.append('<option value="'+arrSuppl[j]['id']+'">'+arrSuppl[j]['name']+'</option>');
            }
        }
    });

});

</script>

@endsection