@extends('theme.default')

@section('breadcrumb')
<div class="page-breadcrumb">
    <div class="row">
        <div class="col-5 align-self-center">
            <h4 class="page-title">Penawaran</h4>
            <div class="d-flex align-items-center">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item active" aria-current="page">Penawaran</li>
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
            <div class="text-right">
                <a href="{{ URL::to('penjualan_keluar/create') }}"><button class="btn btn-success btn-sm mb-2"><i class="fas fa-plus"></i>&nbsp; Create Penawaran</button></a>
            </div>
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">List Penawaran</h4>
                    <div class="table-responsive">
                        <table id="list_penawaran" class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th class="text-center">Nomor Penawaran</th>
                                    <th class="text-center">Site</th>
                                    <th class="text-center">Tanggal Penawaran</th>
                                    <th class="text-center">Total Penawaran</th>
                                    <th class="text-center">Customer</th>
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
                <h4 class="modal-title" id="myLargeModalLabel">Penawaran Detail</h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <div class="modal-body">
                <h4>Penawaran Detail</h4>
                <div class="table-responsive">
                    <table id="zero_config2" class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th class="text-center">Material No</th>
                                <th class="text-center">Material Name</th>
                                <th class="text-center">Qty Penawaran</th>
                                <th class="text-center">Qty Dikirim</th>
                                <th class="text-center">Harga Satuan</th>
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
    $('#list_penawaran').DataTable( {
        "processing": true,
        "serverSide": true,
        "ajax": "{{ URL::to('penjualan/penawaran/list') }}",
        aaSorting: [[2, 'desc']],
        "columns": [
            {"data": "no"},
            {"data": "sites.name"},
            {"data": "tanggal_penawaran", "class" : "text-center",
            "render": function(data, type, row){return formatDateID(new Date((row.create_date)))}},
            {"data": "total_amount", "class" : "text-right",
            "render": function(data, type, row){return formatCurrency(parseInt(row.total_amount).toString())}},
            {"data": "coorporate_name"},
            {"data": "id",
            "render": function(data, type, row){return '<div class="text-center"><button onclick="doShowDetail('+row.id+');" data-toggle="modal" data-target="#modalTransferDetail" class="btn waves-effect waves-light btn-sm btn-info openModalTransferDetail">Detail</button>&nbsp;'+(row.is_closed == false ? '<a class="btn btn-sm btn-primary text-white" href="{{ URL::to('penjualan_keluar/kirim') }}/'+row.id+'">Kirim</a>&nbsp;<a class="btn btn-sm btn-success text-white" target="_blank" href="{{ URL::to('penjualan_keluar/print_sppjb') }}/'+row.id+'">Print SPPJB</a>' : '<a class="btn btn-sm btn-success text-white" target="_blank" href="{{ URL::to('penjualan_keluar/print_surat_jalan') }}/'+row.id+'">Print Surat Jalan</a>')+'</div>'}},
        ],
    } );
});

function doShowDetail(id){
    t2.clear().draw(false);
    $.ajax({
            type: "GET",
            url: "{{ URL::to('penjualan_keluar/pengajuan_detail') }}" + "/" + id, //json get site
            dataType : 'json',
            success: function(response){
                arrData = response['data'];
                for(i = 0; i < arrData.length; i++){
                    t2.row.add([
                        '<div class="text-left">'+arrData[i]['m_items']['no']+'</div>',
                        '<div class="text-left">'+arrData[i]['m_items']['name']+'</div>',
                        '<div class="text-right">'+parseFloat(arrData[i]['amount'])+'</div>',
                        '<div class="text-right">'+parseFloat(arrData[i]['total_used'])+'</div>',
                        '<div class="text-right">'+formatCurrency(arrData[i]['base_price'])+'</div>',
                        '<div class="text-center">'+arrData[i]['m_units']['name']+'</div>'
                    ]).draw(false);
                }
            }
    });
}


</script>


@endsection