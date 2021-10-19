@extends('theme.default')

@section('breadcrumb')
    <div class="page-breadcrumb">
        <div class="row">
            <div class="col-5 align-self-center">
                <h4 class="page-title">Otorisasi Material Request</h4>
                <div class="d-flex align-items-center">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item active" aria-current="page">Otorisasi Material Request</li>
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
                    <h4 class="card-title">List Otorisasi Material Request</h4>
                    <div class="table-responsive">
                        <table id="pengambilan_list" class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th class="text-center">Request Number</th>
                                    <th class="text-center">RAB Number</th>
                                    <th class="text-center">SPK Number</th>
                                    <th class="text-center">City</th>
                                    <th class="text-center">Site Name</th>
                                    <th class="text-center">Project</th>
                                    <th class="text-center">Request Type</th>
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

<form method="POST" action="{{ URL::to('auth_pengambilan_barang') }}" class="form-horizontal">
    @csrf
    <div class="modal fade bs-example-modal-lg" id="modalTransferDetail" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" style="display: none;">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myLargeModalLabel">Otorisasi Material Request</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                </div>
                <div class="modal-body">
                    <h4>Material Request Detail</h4>
                    <div class="table-responsive">
                        <input type="hidden" name="inv_request_id" />
                        <table id="zero_config2" class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th class="text-center">Material No</th>
                                    <th class="text-center">Material Name</th>
                                    <th class="text-center">Qty Pengajuan</th>
                                    <th class="text-center">Qty Yang Disetujui</th>
                                    <th class="text-center">Satuan</th>
                                    <th class="text-center">Alasan</th>
                                </tr>
                            </thead>
                            <tbody>
                                
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary waves-effect btn-sm text-left">Submit</button>
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
    <!-- /.modal -->
</form>

<script src="{!! asset('theme/assets/libs/jquery/dist/jquery.min.js') !!}"></script>
<script src="{!! asset('theme/assets/extra-libs/DataTables/datatables.min.js') !!}"></script>
<script>
var uri='{{URL::to('/')}}';
$(document).ready(function() {
    $('#pengambilan_list').DataTable( {
        "processing": true,
        "serverSide": true,
        "ajax": "{{ URL::to('material_request/auth_list') }}",
        "columns": [
            {"data": "no"},
            {"data": "rab_no"},
            {"data": "spk_number"},
            {"data": "site_location"},
            {"data": "site_name"},
            {"data": "project_name"},
            {"data": "req_type", "render": function(data, type, row){return row.req_type == 'REQ_ITEM' ? 'Permintaan Normal' : 'Permintaan Khusus'}},
            {"data": "id", "render": function(data, type, row){
                return row.user_auth == null ? '<button onclick="doShowDetail('+row.id+');" data-toggle="modal" data-target="#modalTransferDetail" class="btn waves-effect waves-light btn-xs btn-warning openModalTransferDetail">Auth</button>' : '';
            }}
        ],
    } );
});

var t2 = $('#zero_config2').DataTable();
// $(document).ready(function(){
//     // console.log(arrMaterialPembelianRutin);
//     t = $('#zero_config').DataTable();
//     t.clear().draw(false);
//     $.ajax({
//             type: "GET",
//             url: "{{ URL::to('material_request/list') }}", //json get site
//             dataType : 'json',
//             success: function(response){
//                 arrData = response['data'];
//                 for(i = 0; i < arrData.length; i++){
//                     // urlReceive = "{{ URL::to('pengambilan_barang/detail') }}" + "/" +arrData[i]['id'];
//                     urlReceive = "#";
//                     if (arrData[i]['req_type'] == 'SPECIAL' && arrData[i]['user_auth'] == null && arrData[i]['rab_id'] != null) {
//                         t.row.add([
//                             '<div class="text-left">'+arrData[i]['no']+'</div>',
//                             '<div class="text-left">'+arrData[i]['rab_no']+'</div>',
//                             '<div class="text-left">'+arrData[i]['site_location']+'</div>',
//                             '<div class="text-center">'+arrData[i]['site_name']+'</div>',
//                             '<div class="text-center">'+arrData[i]['project_name']+'</div>',
//                             '<div class="text-center">'+ (arrData[i]['req_type'] == 'REQ_ITEM' ? 'Permintaan Normal' : 'Permintaan Khusus') +'</div>',
//                             '<div class="text-center"><button onclick="doShowDetail('+arrData[i]['id']+');" data-toggle="modal" data-target="#modalTransferDetail" class="btn waves-effect waves-light btn-xs btn-warning openModalTransferDetail">Auth</button></div>'
//                         ]).draw(false);
//                     }
//                 }
//             }
//     });
// });

function doShowDetail(id){
    $('[name^=inv_request_id]').val(id);
    t2.clear().draw(false);
    $.ajax({
            type: "GET",
            url: "{{ URL::to('material_request/list_detail') }}" + "/" + id, //json get site
            dataType : 'json',
            success: function(response){
                arrData = response['data']['detail'];
                console.log(arrData);
                for(i = 0; i < arrData.length; i++){
                    t2.row.add([
                        '<div class="text-left">'+arrData[i]['m_items']['no']+'</div>',
                        '<input type="hidden" name="inv_request_d_id[]" value="'+arrData[i]['id']+'" /><input type="hidden" name="m_item_id[]" value="'+arrData[i]['m_item_id']+'" /><div class="text-left">'+arrData[i]['m_items']['name']+'</div>',
                        '<div class="text-right">'+parseFloat(arrData[i]['amount'])+'</div>',
                        '<div class="text-right"><input type="number" id="qty[]" name="qty[]" step="any" min="0" class="form-control text-right" placeholder="0" value="'+parseFloat(arrData[i]['amount'])+'" required></div>',
                        '<input type="hidden" name="m_unit_id[]" value="'+arrData[i]['m_unit_id']+'" /><div class="text-center">'+arrData[i]['m_units']['name']+'</div>',
                        '<div class="text-left">'+arrData[i]['notes']+'</div>'
                    ]).draw(false);
                }
            }
    });
}


</script>

@endsection