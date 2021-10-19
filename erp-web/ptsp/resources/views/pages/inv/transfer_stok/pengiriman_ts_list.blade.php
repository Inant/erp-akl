@extends('theme.default')

@section('breadcrumb')
			<div class="page-breadcrumb">
                <div class="row">
                    <div class="col-5 align-self-center">
                        <h4 class="page-title">Pengiriman Transfer Stok</h4>
                        <div class="d-flex align-items-center">
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item active" aria-current="page">Pengiriman Transfer Stok</li>
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
                <!-- <a href="{{ URL::to('transfer_stok/create') }}"><button class="btn btn-success btn-sm mb-2"><i class="fas fa-plus"></i>&nbsp; New Transfer Stok</button></a> -->
            </div>
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">List Transfer Stok</h4>
                    <div class="table-responsive">
                        <table id="zero_config" class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th class="text-center">Transaction Number</th>
                                    <th class="text-center">Transfer From</th>
                                    <th class="text-center">Transfer To</th>
                                    <th class="text-center">Request Date</th>
                                    <th class="text-center">Transfer Status</th>
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
                                <th class="text-center">Material No</th>
                                <th class="text-center">Material Name</th>
                                <th class="text-center">Volume Pengajuan</th>
                                <th class="text-center">Volume Transfer</th>
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
var site_id = null;

$(document).ready(function(){
    site_id = {{ $site_id }}
    t = $('#zero_config').DataTable();
    t.clear().draw(false);
    $.ajax({
            type: "GET",
            url: "{{ URL::to('transfer_stok/list') }}", //json get site
            dataType : 'json',
            success: function(response){
                arrData = response['data'];
                for(i = 0; i < arrData.length; i++){
                    // if(arrData[i]['site_from'] == site_id) {
                        let status = null;
                        if(arrData[i]['is_sent'] == null && arrData[i]['is_receive'] == null)
                            status = 'Pengajuan';
                        else if (arrData[i]['is_sent'] == true && arrData[i]['is_receive'] == null)
                            status = 'Pengiriman';
                        else if (arrData[i]['is_sent'] == true && arrData[i]['is_receive'] == true)
                            status = 'Diterima';
                        else 
                            status = 'Gagal';
                        
                        if(status === 'Pengajuan' ) {
                            t.row.add([
                                '<div class="text-left">'+arrData[i]['no']+'</div>',
                                '<div class="text-left">'+arrData[i]['site_from_name']+'</div>',
                                '<div class="text-left">'+arrData[i]['site_to_name']+'</div>',
                                '<div class="text-center">'+formatDateID(new Date(arrData[i]['created_at'].substr(0,10)))+'</div>',
                                '<div class="text-center">'+status+'</div>',
                                '<div class="text-center"><a href="{{ URL::to('pengiriman_ts/kirim') }}/'+arrData[i]['id']+'" class="btn waves-effect waves-light btn-xs btn-warning">Kirim</a> <button onclick="doShowDetail('+arrData[i]['id']+');" data-toggle="modal" data-target="#modalTransferDetail" class="btn waves-effect waves-light btn-xs btn-info openModalTransferDetail">Detail</button></div>'
                            ]).draw(false);
                        }
                    // }
                }
            }
    });
});

function doShowDetail(id){
    t2.clear().draw(false);
    $.ajax({
            type: "GET",
            url: "{{ URL::to('transfer_stok/list_detail') }}" + "/" + id, //json get site
            dataType : 'json',
            success: function(response){
                arrData = response['data'];
                for(i = 0; i < arrData.length; i++){
                    t2.row.add([
                        '<div class="text-left">'+arrData[i]['m_items']['no']+'</div>',
                        '<div class="text-left">'+arrData[i]['m_items']['name']+'</div>',
                        '<div class="text-right">'+arrData[i]['amount']+'</div>',
                        '<div class="text-right">'+(arrData[i]['actual_amount'] != null ? arrData[i]['actual_amount'] : 0) +'</div>',
                        '<div class="text-left">'+arrData[i]['m_units']['name']+'</div>'
                    ]).draw(false);
                }
            }
    });
}

</script>


@endsection