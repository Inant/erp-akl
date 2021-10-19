@extends('theme.default')

@section('breadcrumb')
			<div class="page-breadcrumb">
                <div class="row">
                    <div class="col-5 align-self-center">
                        <h4 class="page-title">Penerimaan Produk Jadi</h4>
                        <div class="d-flex align-items-center">
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item active" aria-current="page">Penerimaan Produk Jadi</li>
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
            <!-- <div class="text-right">
                <a href="{{ URL::to('inventory/add_acc_prod') }}"><button class="btn btn-success btn-sm mb-2"><i class="fas fa-plus"></i>&nbsp; Create New</button></a>
            </div> -->
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">List Penerimaan Produk Jadi</h4>
                    <div class="table-responsive">
                        <table id="acc_list" class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th class="text-center">Inv Number</th>
                                    <th class="text-center">Order Number</th>
                                    <th class="text-center">Customer</th>
                                    <th class="text-center">Rab Number</th>
                                    <th class="text-center">Detail</th>
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
<div class="modal fade bs-example-modal-lg" id="modalAccDetail" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myLargeModalLabel">Detail Produk Jadi</h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <div class="modal-body">
                <h4>List Produk Jadi</h4>
                <p id="label-detail"></p>
                <div class="table-responsive">
                    <table id="zero_config2" class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th class="text-center">Label</th>
                                <th class="text-center">Nama Produk</th>
                                <th class="text-center">Storage</th>
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
<script src="{!! asset('theme/assets/libs/jquery/dist/jquery.min.js') !!}"></script>
<script src="{!! asset('theme/assets/extra-libs/DataTables/datatables.min.js') !!}"></script>
<script>
var uri='{{URL::to('/')}}';
$(document).ready(function() {
    $('#acc_list').DataTable( {
        "processing": true,
        "serverSide": true,
        "ajax": "{{ URL::to('/inventory/json_acc_product') }}",
        "columns": [
            {"data": "no"},
            {"data": "order_no"},
            {"data": "coorporate_name"},
            {"data": "rab_no"},
            {"data": "name"},
            {"data": "id", "render": function(data, type, row){
                return '<button onclick="doShowDetail('+row.id+');" data-toggle="modal" data-target="#modalAccDetail" class="btn waves-effect waves-light btn-xs btn-info">Detail</button>'
            }}
        ],
    } );
});
function doShowDetail(id){
    t2=$('#zero_config2').DataTable();
    t2.clear().draw(false);
    $.ajax({
            type: "GET",
            url: "{{ URL::to('inventory/json_acc_detail') }}" + "/" + id, //json get site
            dataType : 'json',
            success: function(response){
                arrData = response['data'];
                for(i = 0; i < arrData.length; i++){
                    t2.row.add([
                        '<div class="text-left">'+arrData[i]['prod_no']+'</div>',
                        '<div class="text-left">'+arrData[i]['name']+'</div>',
                        '<div class="text-left">'+arrData[i]['storage_locations']+'</div>'
                    ]).draw(false);
                }
            }
    });
}
</script>


@endsection