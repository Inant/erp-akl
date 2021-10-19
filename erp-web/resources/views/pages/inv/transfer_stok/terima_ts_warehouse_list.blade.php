@extends('theme.default')

@section('breadcrumb')
			<div class="page-breadcrumb">
                <div class="row">
                    <div class="col-5 align-self-center">
                        <h4 class="page-title">Penerimaan Transfer Stok Gudang</h4>
                        <div class="d-flex align-items-center">
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item active" aria-current="page">Transfer Stok Gudang</li>
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
                        <div class="card">
                            <div class="card-body">
                                <h4 class="card-title">List Penerimaan Transfer Stok Gudang</h4>
                                <div class="table-responsive">
                                    <table id="transfer_list" class="table table-striped table-bordered">
                                        <thead>
                                            <tr>
                                                <th class="text-center">Nomor Transaksi</th>
                                                <th class="text-center">Site</th>
                                                <th class="text-center">Transfer Dari</th>
                                                <th class="text-center">Transfer Ke</th>
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
                <h4 class="modal-title" id="myLargeModalLabel">Transfer Stok Detail</h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <div class="modal-body">
                <h4>Request Transfer Stok</h4>
                <div class="table-responsive">
                    <table id="zero_config2" class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th class="text-center">Nomor Material</th>
                                <th class="text-center">Nama Material</th>
                                <th class="text-center">Total Transfer</th>
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
var t3 = $('#zero_config3').DataTable();

$(document).ready(function(){
    $('#transfer_list').DataTable( {
        "processing": true,
        "serverSide": true,
        "ajax": "{{ URL::to('transfer_stok/list_terima_ts_warehouse') }}",
        "columns": [
            {"data": "no", "class" : "text-center"},
            {"data": "site_name", "class" : "text-center"},
            {"data": "from", "class" : "text-center"},
            {"data": "to", "class" : "text-center"},
            {"data": "id", "render": function(data, type, row){
                return '<button onclick="doShowDetail('+row.id+');" data-toggle="modal" data-target="#modalTransferDetail" class="btn waves-effect waves-light btn-xs btn-info openModalTransferDetail">Detail</button>&nbsp;<a href="{{URL::to('transfer_stok/form_terima_ts_warehouse')}}/'+row.id+'" '+(row.is_receive == true ? 'hidden' : '')+' class="btn waves-effect waves-light btn-xs btn-primary">Terima</button>'
            }, "class" : "text-center"}
        ],
    } );
});

function doShowDetail(id){
    t2.clear().draw(false);
    $.ajax({
            type: "GET",
            url: "{{ URL::to('transfer_stok/detail_tsw') }}" + "/" + id, //json get site
            dataType : 'json',
            success: function(response){
                arrData = response['data'];
                for(i = 0; i < arrData.length; i++){
                    t2.row.add([
                        '<div class="text-left">'+arrData[i]['material_no']+'</div>',
                        '<div class="text-left">'+arrData[i]['material_name']+'</div>',
                        '<div class="text-center">'+arrData[i]['amount']+'</div>',
                        '<div class="text-left">'+arrData[i]['unit_name']+'</div>'
                    ]).draw(false);
                }
            }
    });
}

</script>


@endsection