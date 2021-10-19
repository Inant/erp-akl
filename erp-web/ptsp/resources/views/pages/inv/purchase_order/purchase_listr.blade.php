@extends('theme.default')

@section('breadcrumb')
			<div class="page-breadcrumb">
                <div class="row">
                    <div class="col-5 align-self-center">
                        <h4 class="page-title">Tagihan Supplier</h4>
                        <div class="d-flex align-items-center">
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item active" aria-current="page">Tagihan</li>
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
                    @if($error['is_error'])
                    <div class="col-12">
                        <div class="alert alert-danger"> <i class="mdi mdi-alert-box"></i> {{ $error['error_message'] }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close"> <span aria-hidden="true">×</span> </button>
                        </div>
                    </div>
                    @endif
                    <div class="col-12">
                        <!-- <div class="text-right">
                            <a href="{{ URL::to('pengambilan_barang/request') }}"><button class="btn btn-success btn-sm mb-2"><i class="fas fa-plus"></i>&nbsp; Create New Request</button></a>
                        </div> -->
                        <div class="card">
                            <div class="card-body">
                                <h4 class="card-title">Daftar Tagihan Supplier</h4>
                                <div class="table-responsive">
                                    <table id="zero_config" class="table table-striped table-bordered">
                                        <thead>
                                            <tr>
                                                <th class="text-center">No</th>
                                                <th class="text-center">Nama Supplier</th>
                                                <th class="text-center">Nomor PO</th>
                                                <th class="text-center">Tanggal PO</th></th>
                                                <th class="text-center">Tanggal Penerimaan</th>
                                                <th class="text-center">Penerima</th>
                                                <th class="text-center">Jumlah Tagihan</th>
                                                <th class="text-center">Button Lunasi</th>
                                            </tr>
                                        </thead>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>                
</div>


<div class="modal fade" id="modalShowDetail" tabindex="-1" role="dialog" aria-labelledby="modalShowDetailLabel1">
    <div class="modal-dialog  modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="modalAddMaterialLabel1">Purchase Order Detail</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table id="dt_detail" class="table table-bordered">
                        <thead>
                            <tr>
                                <th class="text-center">No</th>
                                <th class="text-center">Nama Material</th>
                                <th class="text-center">Volume</th>
                                <th class="text-center">Satuan</th>
                                <th class="text-center">Harga</th>
                            </tr>
                        </thead>                                      
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <!-- <button type="button" onclick="saveMaterial(this.form);" class="btn btn-primary btn-sm">Save</button> -->
            </div>
        </div>
    </div>
</div>

<script src="{!! asset('theme/assets/libs/jquery/dist/jquery.min.js') !!}"></script>
<script src="{!! asset('theme/assets/extra-libs/DataTables/datatables.min.js') !!}"></script>
<script>

dt_detail = $('#dt_detail').DataTable();

$(document).ready(function(){
    // console.log(arrMaterialPembelianRutin);
    t = $('#zero_config').DataTable();
    t.clear().draw(false);
    $.ajax({
            type: "GET",
            url: "{{ URL::to('inventory/purchase/all') }}", //json get site
            dataType : 'json',
            success: function(response){
                arrData = response['data'];
                for(i = 0; i < arrData.length; i++){
                        a = i+1;
                        url = arrData[i]['id'];
                        if(arrData[i]['is_closed'] == false){
                            // b = "<a href='{{ URL::to('inventory/purchase/all') }}http://127.0.0.1:8000/inventory/update/"+url+"' class='btn btn-info btn-sm'><span class='mdi mdi-check'></span> Lunasi</a>";
                            b = "<a href='{{ URL::to('inventory/update') }}/"+url+"' class='btn btn-info btn-sm'><span class='mdi mdi-check'></span> Lunasi</a>";
                        } else {
                            b = "Lunas";
                        }
                        if(arrData[i]['ekspedisi'] == ''){
                            ce = "Tidak ada";
                        } else {
                            ce = arrData[i]['ekspedisi'];
                        }
                        t.row.add([
                            '<div class="text-center">'+a+'</div>',
                            '<div class="text-center">'+arrData[i]['name']+'</div>',
                            '<div class="text-center"><a href="" onclick="doShowDetail('+arrData[i]['id']+');" data-toggle="modal" data-target="#modalShowDetail">'+arrData[i]['no']+'</a></div>',
                            '<div class="text-center">'+formatDateID(new Date((arrData[i]['purchase_date']).substring(0,10)))+'</div>',
                            '<div class="text-center">'+formatDateID(new Date((arrData[i]['updated_at']).substring(0,10)))+'</div>',
                            '<div class="text-center">'+ce+'</div>',
                            '<div class="text-right">'+formatCurrency(arrData[i]['base_price'])+'</div>',
                            '<div class="text-center">'+b+'</div>'
                        ]).draw(false);
                }
            }
    });
});

function doShowDetail(id){
    dt_detail.clear().draw(false);
    $.ajax({
            type: "GET",
            url: "{{ URL::to('inventory/purchase/detail') }}" + "/" + id, //json get site
            dataType : 'json',
            success: function(response){
                arrData = response['data'];
                for(i = 0; i < arrData.length; i++){
                    a = i+1;
                    dt_detail.row.add([
                        '<div class="text-left">'+a+'</div>',
                        '<div class="text-left">'+arrData[i]['name']+'</div>',
                        '<div class="text-right">'+formatCurrency(arrData[i]['amount'])+'</div>',
                        '<div class="text-center">'+arrData[i]['unit']+'</div>',
                        '<div class="text-right">'+formatCurrency(arrData[i]['base_price'])+'</div>'
                    ]).draw(false);
                }
            }
    });
}

function clickPrint(id) {
    setTimeout(() => {
        window.open("{{ URL::to('po_konstruksi/print') }}" + "/" + id, '_blank')
    }, 500);
}

</script>


@endsection