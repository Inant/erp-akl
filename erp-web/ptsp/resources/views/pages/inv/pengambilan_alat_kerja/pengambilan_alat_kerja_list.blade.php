@extends('theme.default')

@section('breadcrumb')
    <div class="page-breadcrumb">
        <div class="row">
            <div class="col-5 align-self-center">
                <h4 class="page-title">Alat Bantu Kerja Request</h4>
                <div class="d-flex align-items-center">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item active" aria-current="page">Alat Bantu Kerja Request</li>
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
            <div class="text-right">
                <a href="{{ URL::to('alat_kerja_request/request') }}"><button class="btn btn-success btn-sm mb-2"><i class="fas fa-plus"></i>&nbsp; Create New Request</button></a>
            </div>
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">List Alat Bantu Request</h4>
                    <div class="table-responsive">
                        <table id="zero_config" class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th class="text-center">Request Number</th>
                                    <!-- <th class="text-center">RAB Number</th> -->
                                    <th class="text-center">Site Name</th>
                                    <!-- <th class="text-center">Kavling</th> -->
                                    <th class="text-center">Request Type</th>
                                    <!-- <th class="text-center">Authorize</th> -->
                                    <th class="text-center">Action</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>       
</div>

<div class="modal fade bs-example-modal-lg" id="modalTransferDetail" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myLargeModalLabel">Alat Bantu Kerja Request Detail</h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <div class="modal-body">
                <h4>Material Request Detail</h4>
                <div class="table-responsive">
                    <table id="zero_config2" class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th class="text-center">Material No</th>
                                <th class="text-center">Material Name</th>
                                <th class="text-center">Qty Pengajuan</th>
                                <th class="text-center">Satuan</th>
                            </tr>
                        </thead>
                        <tbody>
                            
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger waves-effect btn-sm text-left" data-dismiss="modal">Close</button>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<!-- /.modal -->

<script src="{!! asset('theme/assets/libs/jquery/dist/jquery.min.js') !!}"></script>
<script src="{!! asset('theme/assets/extra-libs/DataTables/datatables.min.js') !!}"></script>
<script>

var t2 = $('#zero_config2').DataTable();
$(document).ready(function(){
    // console.log(arrMaterialPembelianRutin);
    t = $('#zero_config').DataTable();
    t.clear().draw(false);
    $.ajax({
            type: "GET",
            url: "{{ URL::to('alat_kerja_request/list') }}", //json get site
            dataType : 'json',
            success: function(response){
                arrData = response['data'];
                console.log(arrData);
                for(i = 0; i < arrData.length; i++){
                    if (arrData[i]['rab_id'] == null && arrData[i]['site_id'] != null) {
                        // urlReceive = "{{ URL::to('pengambilan_barang/detail') }}" + "/" +arrData[i]['id'];
                        urlReceive = "#";

                        t.row.add([
                            '<div class="text-left">'+arrData[i]['no']+'</div>',
                            '<div class="text-center">'+arrData[i]['site_name']+'</div>',
                            '<div class="text-center">'+ (arrData[i]['req_type'] == 'REQ_ITEM' ? 'Permintaan Normal' : 'Permintaan Alat Bantu Kerja') +'</div>',
                            // '<div class="text-center">'+ (arrData[i]['user_auth'] != null ? arrData[i]['user_auth'] : '-') +'</div>',
                            '<div class="text-center"><button onclick="doShowDetail('+arrData[i]['id']+');" data-toggle="modal" data-target="#modalTransferDetail" class="btn waves-effect waves-light btn-xs btn-info openModalTransferDetail">Detail</button></div>'
                        ]).draw(false);
                    }
                }
            }
    });
});

function doShowDetail(id){
    t2.clear().draw(false);
    $.ajax({
            type: "GET",
            url: "{{ URL::to('alat_kerja_request/list_detail') }}" + "/" + id, //json get site
            dataType : 'json',
            success: function(response){
                arrData = response['data'];
                for(i = 0; i < arrData.length; i++){
                    t2.row.add([
                        '<div class="text-left">'+arrData[i]['m_items']['no']+'</div>',
                        '<div class="text-left">'+arrData[i]['m_items']['name']+'</div>',
                        '<div class="text-right">'+parseFloat(arrData[i]['amount'])+'</div>',
                        '<div class="text-center">'+arrData[i]['m_units']['name']+'</div>'
                    ]).draw(false);
                }
            }
    });
}


</script>


@endsection