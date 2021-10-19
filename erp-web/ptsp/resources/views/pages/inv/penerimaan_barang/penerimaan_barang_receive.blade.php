@extends('theme.default')

@section('breadcrumb')
			<div class="page-breadcrumb">
                <div class="row">
                    <div class="col-5 align-self-center">
                        <h4 class="page-title">Penerimaan Barang</h4>
                        <div class="d-flex align-items-center">
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="{{ URL::to('penerimaan_barang') }}">Penerimaan Barang</a></li>
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
                        <form method="POST" action="{{ URL::to('penerimaan_barang/receive') }}" class="form-horizontal">
                        @csrf
                            <!--<div class="text-right">-->
                            <!--    <a href="{{ URL::to('penerimaan_barang') }}"><button type="button" class="btn btn-danger btn-sm mb-2">Cancel</button></a>-->
                                <!-- <a href="{{ URL::to('penerimaan_barang/decline/' . $purchase_id) }}"><button type="button" class="btn btn-warning btn-sm mb-2">Tolak/Retur</button></a> -->
                            <!--    <button type="submit" onclick="clickTerima()" name="submit" value="receive" class="btn btn-primary btn-sm mb-2">Terima</button>-->
                            <!--</div>-->
                            <div class="card">
                                <div class="card-body">
                                    <h4 class="card-title">Form Input Material Penerimaan Barang</h4>
                                    <br/>
                                    <div class="form-group row">
                                        <label class="col-sm-3 text-right control-label col-form-label">Nama Ekspedisi/Driver</label>
                                        <div class="col-sm-9">
                                            <input type="text" id="driver" name="driver" class="form-control" required/>
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
                                                    <th class="text-center">Receive Volume</th>
                                                    <th class="text-center">Satuan</th>
                                                    <th class="text-center">Storage Location</th>
                                                </tr>
                                            </thead>
                                        </table>
                                    </div>
                                    <br><br>

                                  <div class="form-group">
                                    <a href="http://erp-ptsp.binoemar.com/penerimaan_barang"><button type="button" class="btn btn-danger mb-2">Cancel</button></a>
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

$(document).ready(function(){
    t = $('#zero_config').DataTable();
    t.clear().draw(false);
    $.ajax({
            type: "GET",
            url: "{{ URL::to('penerimaan_barang/detail/'.$purchase_id) }}", //json get site
            dataType : 'json',
            success: function(response){
                arrData = response['data'];
                for(i = 0; i < arrData.length; i++){
                    t.row.add([
                        '<input type="hidden" id="purchase_id[]" name="purchase_id[]" value="'+arrData[i]['purchase_id']+'" /><div class="text-left">'+arrData[i]['purchases']['no']+'</div>',
                        '<div class="text-left">'+arrData[i]['m_items']['no']+'</div>',
                        '<input type="hidden" id="m_item_id[]" name="m_item_id[]" value="'+arrData[i]['m_item_id']+'" /><div class="text-left">'+arrData[i]['m_items']['name']+'</div>',
                        '<div class="text-right">'+arrData[i]['amount']+'</div>',
                        '<input id="receive_volume[]" required name="receive_volume[]" class="form-control text-right" type="text" readonly value="'+arrData[i]['amount']+'" />',
                        '<input type="hidden" id="m_unit_id[]" name="m_unit_id[]" value="'+arrData[i]['m_unit_id']+'" /><div class="text-center">'+arrData[i]['m_units']['name']+'</div>',
                        '<input id="notes[]" required name="notes[]" class="form-control text-left" type="text" />'
                    ]).draw(false);
                }
            }
    });

    dt.clear().draw(false);
    $.ajax({
            type: "GET",
            url: "{{ URL::to('penerimaan_barang/get_inv_by_purchase_id/'.$purchase_id) }}", //json get site
            dataType : 'json',
            success: function(response){
                arrData = response['data'];
                for(x = 0; x < arrData.length; x++){
                    arrInvTrxD = arrData[x]['inv_trx_ds'];
                    // console.log(arrInvTrxD);
                    for(i = 0; i < arrInvTrxD.length; i++){
                        dt.row.add([
                            '<div class="text-left">'+arrData[x]['no']+'</div>',
                            '<div class="text-left">'+arrData[x]['purchases']['no']+'</div>',
                            '<div class="text-left">'+arrInvTrxD[i]['material_no']+'</div>',
                            '<div class="text-left">'+arrInvTrxD[i]['m_items']['name']+'</div>',
                            '<div class="text-right">'+arrInvTrxD[i]['amount']+'</div>',
                            '<div class="text-center">'+arrInvTrxD[i]['m_units']['name']+'</div>',
                        ]).draw(false);
                    }
                }
            }
    });
});

function clickTerima() {
    driver = document.getElementById("driver");
    receive_volume = document.getElementById("receive_volume[]");
    notes = document.getElementById("notes[]");
    if (driver.checkValidity() && receive_volume.checkValidity() && receive_volume.checkValidity()) {
        setTimeout(() => {
            window.open("{{ URL::to('penerimaan_barang/print/' . $purchase_id) }}", '_blank');
        }, 500);
    }
}


</script>


@endsection