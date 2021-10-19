@extends('theme.default')

@section('breadcrumb')
			<div class="page-breadcrumb">
                <div class="row">
                    <div class="col-5 align-self-center">
                        <h4 class="page-title">Permintaan Material Penunjang</h4>
                        <div class="d-flex align-items-center">
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item active" aria-current="page">Permintaan Material Penunjang</li>
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
                <a href="{{ URL::to('material_request/request_material_support') }}"><button class="btn btn-success btn-sm mb-2"><i class="fas fa-plus"></i>&nbsp; Create New Request</button></a>
            </div>
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">List Permintaan Material Penunjang</h4>
                    <div class="table-responsive">
                        <table id="pengambilan_list" class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th class="text-center">Request Number</th>
                                    <th class="text-center">RAB Number</th>
                                    <th class="text-center">Site Name</th>
                                    <th class="text-center">Project</th>
                                    <th class="text-center">Request Type</th>
                                    <th class="text-center">Authorize</th>
                                    <th class="text-center">Created at</th>
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
                <h4 class="modal-title" id="myLargeModalLabel">Material Request Detail</h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <div class="modal-body">
                <h4>Material Request Detail</h4>
                <p id="label-detail"></p>
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
var uri='{{URL::to('/')}}';
$(document).ready(function() {
    $('#pengambilan_list').DataTable( {
        "processing": true,
        "serverSide": true,
        "ajax": "{{ URL::to('material_request/list_sp') }}",
        "columns": [
            {"data": "no"},
            {"data": "rab_no"},
            {"data": "site_name"},
            {"data": "project_name"},
            {"data": "req_type", "render": function(data, type, row){return row.req_type == 'REQ_ITEM_SP' ? 'Permintaan Material Penunjang' : 'Permintaan Khusus'}},
            {"data": "user_auth", "render": function(data, type, row){return row.user_auth != null ? row.user_auth : '-'}},
            {"data": "created_at", "render": function(data, type, row){return formatDateID(new Date((row.created_at).substring(0,10)))}},
            {"data": "id", "render": function(data, type, row){
                return '<button onclick="doShowDetail('+row.id+');" data-toggle="modal" data-target="#modalTransferDetail" class="btn waves-effect waves-light btn-xs btn-info openModalTransferDetail">Detail</button>'
            }}
        ],
    } );
});


var t2 = $('#zero_config2').DataTable();
$(document).ready(function(){
    // console.log(arrMaterialPembelianRutin);
    t = $('#zero_config').DataTable();
    t.clear().draw(false);
    $.ajax({
            type: "GET",
            url: "{{ URL::to('material_request/list') }}", //json get site
            dataType : 'json',
            success: function(response){
                arrData = response['data'];
                for(i = 0; i < arrData.length; i++){
                    if (arrData[i]['rab_id'] != null) {
                        // urlReceive = "{{ URL::to('pengambilan_barang/detail') }}" + "/" +arrData[i]['id'];
                        urlReceive = "#";

                        t.row.add([
                            '<div class="text-left">'+arrData[i]['no']+'</div>',
                            '<div class="text-left">'+arrData[i]['rab_no']+'</div>',
                            '<div class="text-center">'+arrData[i]['site_name']+'</div>',
                            '<div class="text-center">'+arrData[i]['project_name']+'</div>',
                            '<div class="text-center">'+ (arrData[i]['req_type'] == 'REQ_ITEM' ? 'Permintaan Normal' : 'Permintaan Khusus') +'</div>',
                            '<div class="text-center">'+ (arrData[i]['user_auth'] != null ? arrData[i]['user_auth'] : '-') +'</div>',
                            '<div class="text-center">'+formatDateID(new Date((arrData[i]['created_at']).substring(0,10)))+'</div>',
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
            url: "{{ URL::to('material_request/list_detail') }}" + "/" + id, //json get site
            dataType : 'json',
            success: function(response){
                arrData = response['data']['detail'];
                for(i = 0; i < arrData.length; i++){
                    t2.row.add([
                        '<div class="text-left">'+(arrData[i]['m_items'] != null ? arrData[i]['m_items']['no'] : '-')+'</div>',
                        '<div class="text-left">'+(arrData[i]['m_items'] != null ? arrData[i]['m_items']['name'] : '-')+'</div>',
                        '<div class="text-right">'+(arrData[i]['amount'] != null ? parseFloat(arrData[i]['amount']) : '-')+'</div>',
                        '<div class="text-center">'+(arrData[i]['m_units'] != null ? arrData[i]['m_units']['name'] : '-')+'</div>'
                    ]).draw(false);
                }
            }
    });
}


</script>


@endsection