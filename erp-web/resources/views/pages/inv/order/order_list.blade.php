@extends('theme.default')

@section('breadcrumb')
    <div class="page-breadcrumb">
        <div class="row">
            <div class="col-5 align-self-center">
                <h4 class="page-title">Order List</h4>
                <div class="d-flex align-items-center">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item active" aria-current="page">Order List</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('content')
<div class="container-fluid pb-0" style="min-height:auto">
    <div class="row">
        <div class="col-12">
            <div class="text-right">
                <a href="{{ URL::to('order/create') }}"><button class="btn btn-success btn-sm mb-2"><i class="fas fa-plus"></i>&nbsp; Create New</button></a>&nbsp;
                <a href="{{ URL::to('order/create_install_order') }}"><button class="btn btn-info btn-sm mb-2"><i class="fas fa-plus"></i>&nbsp; Create Install Order</button></a>
            </div>
        </div>
    </div>
</div>
@include('pages/inv/order/interval_date_form')
@if(isset($_GET['dari']))
<div class="container-fluid">
    <!-- basic table -->
    <div class="row">
        <div class="col-12">
            <div class="text-right">
                <a href="{{ URL::to('order/create') }}"><button class="btn btn-success btn-sm mb-2"><i class="fas fa-plus"></i>&nbsp; Create New</button></a>&nbsp;
                <a href="{{ URL::to('order/create_install_order') }}"><button class="btn btn-info btn-sm mb-2"><i class="fas fa-plus"></i>&nbsp; Create Install Order</button></a>
            </div>
            <div class="card">
                <div class="card-body">
                    <ul class="nav nav-tabs" role="tablist">
                        <li class="nav-item"> <a class="nav-link active" data-toggle="tab" href="#tab_order" role="tab"><span class="hidden-sm-up"><i class="mdi mdi-solid"></i></span> <span class="hidden-xs-down">Order List</span></a> </li>
                        <li class="nav-item" onclick=""> <a class="nav-link" data-toggle="tab" href="#material_tidak_utuh" role="tab"><span class="hidden-sm-up"><i class="mdi mdi-view-grid"></i></span> <span class="hidden-xs-down">Order Instalasi List</span></a> </li>
                    </ul>
                    <div class="tab-content tabcontent-border">
                        <div class="tab-pane active" id="tab_order" role="tabpanel">
                            <br>
                            <a class="btn btn-success float-right" href="{{URL::to('order/export').'?dari='.$_GET['dari'].'&sampai='.$_GET['sampai']}}" target="_blank"><i class="mdi mdi-file-excel"></i> Export</a>
                            <div class="table-responsive">
                            <br>
                                <table id="order_list" class="table table-striped table-bordered">
                                    <thead>
                                        <tr>
                                            <th class="text-center">Order No</th>
                                            <th class="text-center">No SPJB</th>
                                            <th class="text-center">Nama Proyek</th>
                                            <th class="text-center">Order Description</th>
                                            <th class="text-center">Customer Order</th>
                                            <th class="text-center">Order Date</th>
                                            <th class="text-center">Total Kontrak</th>
                                            <th class="text-center">Action</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                        <div class="tab-pane" id="material_tidak_utuh" role="tabpanel">
                            <div class="table-responsive">
                            <br>
                            <a class="btn btn-success float-right" href="{{URL::to('order/export_install').'?dari='.$_GET['dari'].'&sampai='.$_GET['sampai']}}" target="_blank"><i class="mdi mdi-file-excel"></i> Export</a>
                            <div class="table-responsive">
                            <br>
                                <table id="order_install_list" class="table table-striped table-bordered" style="width:100%">
                                    <thead>
                                        <tr>
                                            <th class="text-center">No</th>
                                            <th class="text-center">Order No</th>
                                            <th class="text-center">No SPJB</th>
                                            <th class="text-center">Nama Proyek</th>
                                            <th class="text-center">No SPK</th>
                                            <th class="text-center">Notes</th>
                                            <th class="text-center">Customer Order</th>
                                            <th class="text-center">Order Date</th>
                                            <th class="text-center">Total Kontrak</th>
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
        <div class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" style="display: none;">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="title_detail">Detail Order</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    </div>
                    <div class="modal-body">
                        <h4></h4>
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped" id="detail_order">
                                <thead>
                                    <tr>
                                        <th>Produk</th>
                                        <th>Kavling</th>
                                        <th>Harga</th>
                                        <th>Total Kavling</th>
                                        <th>Rab Number</th>
                                        <th>Estimasi Jadi</th>
                                        <th>Status Produksi</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger waves-effect text-left" data-dismiss="modal">Close</button>
                    </div>
                </div>
                <!-- /.modal-content -->
            </div>
            <!-- /.modal-dialog -->
        </div>
        <div class="modal fade" id="modal_instal_order" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" style="display: none;">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="title_detail_install">Detail Order</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    </div>
                    <div class="modal-body">
                        <h4></h4>
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped" id="detail_order_install">
                                <thead>
                                    <tr>
                                        <th>Produk</th>
                                        <th>Biaya Instalasi(Kontrak)</th>
                                        <th>Total</th>
                                        <th>Pekerjaan</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger waves-effect text-left" data-dismiss="modal">Close</button>
                    </div>
                </div>
                <!-- /.modal-content -->
            </div>
            <!-- /.modal-dialog -->
        </div>
        <div class="modal fade" id="modalDetailWork" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" style="display: none;">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="title_detail_install">Detail Jasa</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    </div>
                    <div class="modal-body">
                        <h4></h4>
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped" id="detail_work">
                                <thead>
                                    <tr>
                                        <th>Pekerjaan</th>
                                        <th>Biaya Jasa</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger waves-effect text-left" data-dismiss="modal">Close</button>
                    </div>
                </div>
                <!-- /.modal-content -->
            </div>
            <!-- /.modal-dialog -->
        </div>
        <div class="modal fade" id="edit_spk" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" style="display: none;">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="title_detail_install">Edit No SPK</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    </div>
                    <form method="POST" action="{{ URL::to('order/edit_spk') }}" class="mt-4" enctype="multipart/form-data">
                        @csrf
                    <div class="modal-body">
                        <input type="text" class="form-control" id="spk_no" name="spk_no">
                        <input type="hidden" name="id" id="install_order_id">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger waves-effect text-left" data-dismiss="modal">Close</button>
                        <button class="btn btn-success waves-effect text-left">Update</button>
                    </div>
                    </form>
                </div>
                <!-- /.modal-content -->
            </div>
            <!-- /.modal-dialog -->
        </div>
        <div class="modal fade" id="edit_spjb" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" style="display: none;">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="title_detail_install">Edit No SPJB</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    </div>
                    <form method="POST" action="{{ URL::to('order/edit_spjb') }}" class="mt-4" enctype="multipart/form-data">
                        @csrf
                    <div class="modal-body">
                        <input type="text" class="form-control" id="spk_number" name="spk_no">
                        <input type="" name="id" id="order_id">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger waves-effect text-left" data-dismiss="modal">Close</button>
                        <button class="btn btn-success waves-effect text-left">Update</button>
                    </div>
                    </form>
                </div>
                <!-- /.modal-content -->
            </div>
            <!-- /.modal-dialog -->
        </div>
        <!-- /.modal -->
    </div>                
</div>

<script src="{!! asset('theme/assets/libs/jquery/dist/jquery.min.js') !!}"></script>
<script src="{!! asset('theme/assets/extra-libs/DataTables/datatables.min.js') !!}"></script>
<script type="text/javascript">
var uri='{{URL::to('/')}}';
$(document).ready(function() {
    $('#order_list').DataTable( {
        "processing": true,
        "serverSide": true,
        "ajax": {
            "url" : "{{ URL::to('order/list') }}",
            "type" : "get",
            "data" : {
                "dari" : "{{$_GET['dari']}}",
                "sampai" : "{{$_GET['sampai']}}",
                },
        },
        "aaSorting": [[ 0, "desc" ]],
        "columns": [
            {"data": "order_no"},
            {"data": "spk_number", "render": function(data, type, row){return (row.spk_number != null ? row.spk_number : '-') + '<br><button class="btn btn-xs btn-info" data-toggle="modal" data-target="#edit_spjb" data-id="'+row.id+'" data-spk_number="'+row.spk_number+'" onclick="editSPJB(this)"><i class="mdi mdi-pencil"></i></button>'}},
            {"data" : "project_name"},
            {"data": "order_name"},
            {"data": "customer_coorporate"},
            {"data": "order_date", "render": function(data, type, row){return formatTanggal(row.order_date)}},
            {"data": "total_kontrak", "render": function(data, type, row){return formatCurrency(parseFloat(row.total_kontrak).toFixed(0))}},
            {"data": "action"}
        ],
    } );
    $('#order_install_list').DataTable( {
        "processing": true,
        "serverSide": true,
        "ajax": {
            "url" : "{{ URL::to('order/list_order_install') }}",
            "type" : "get",
            "data" : {
                "dari" : "{{$_GET['dari']}}",
                "sampai" : "{{$_GET['sampai']}}",
                },
        },
        "aaSorting": [[ 0, "desc" ]],
        "columns": [
            {"data": "no"},
            {"data": "order_no"},
            {"data": "spk_number"},
            {"data": "project_name"},
            {"data": "spk_no", "render": function(data, type, row){return (row.spk_no != null ? row.spk_no : '-') + '<br><button class="btn btn-xs btn-info" data-toggle="modal" data-target="#edit_spk" data-id="'+row.id+'" data-spk_no="'+row.spk_no+'" onclick="editSPK(this)"><i class="mdi mdi-pencil"></i></button>'}},
            {"data": "notes"},
            {"data": "customer_coorporate"},
            {"data": "order_date", "render": function(data, type, row){return formatTanggal(row.order_date)}},
            {"data": "total_kontrak", "render": function(data, type, row){return formatCurrency(row.total_kontrak)}},
            {"data": "action"}
        ],
    } );
});
function formatTanggal(date) {
  var temp=date.split('-');
  return temp[2] + '-' + temp[1] + '-' + temp[0];
}
function getDetail(el){
    var id=$(el).data('id');
    var order_no=$(el).data('order_no');
    $('#title_detail').html('Detail Order '+order_no);
    t = $('#detail_order').DataTable();
    t.clear().draw(false);
    $.ajax({
        // type: "post",
        url: "{{ URL::to('order/detail') }}"+'/'+id,
        dataType : 'json',
        success: function(response){
            arrData = response['data'];
            var j=0;
            for(i = 0; i < arrData.length; i++){
                j+=1;
                t.row.add([
                    '<div>'+arrData[i]['amount_set']+' Set '+arrData[i]['item']+' '+arrData[i]['type_kavling']+' Series : '+arrData[i]['series']+' Dimensi W :'+arrData[i]['panjang']+' H : '+arrData[i]['lebar']+'</div>',
                    '<div>'+arrData[i]['type_kavling']+'</div>',
                    '<div>'+formatCurrency(arrData[i]['price'])+'</div>',
                    '<div>'+arrData[i]['total']+'</div>',
                    '<div>'+(arrData[i]['no'] != null ? arrData[i]['no'] : '-') +'</div>',
                    '<div>'+(arrData[i]['estimate_end'] != null ? formatDateID(new Date(arrData[i]['estimate_end'])) : '-') +'</div>',
                    '<div>'+(arrData[i]['is_final'] != null ? (arrData[i]['is_final'] == 0 ? 'In Rab' : (arrData[i]['is_final_production'] != null ? (arrData[i]['is_final_production'] == 0 ? 'Running' : 'Final') : 'In Rab')) : '-') +'</div>',
                    '</div>'
                ]).draw(false);
            }
        }
    });
}
function getDetailInstall(el){
    var id=$(el).data('id');
    var no=$(el).data('no');
    $('#title_detail_install').html('Detail Order Instalasi '+no);
    t = $('#detail_order_install').DataTable();
    t.clear().draw(false);
    $.ajax({
        // type: "post",
        url: "{{ URL::to('order/detail_install') }}"+'/'+id,
        dataType : 'json',
        success: function(response){
            arrData = response['data'];
            var j=0;
            for(i = 0; i < arrData.length; i++){
                j+=1;
                t.row.add([
                    '<div>'+arrData[i]['amount_set']+' Set '+arrData[i]['item']+' '+arrData[i]['type_kavling']+' Series : '+arrData[i]['series']+' Dimensi W :'+arrData[i]['panjang']+' H : '+arrData[i]['lebar']+'</div>',
                    '<div>'+formatCurrency(arrData[i]['installation_fee'])+'</div>',
                    '<div>'+arrData[i]['total']+'</div>',
                    '<div><button class="btn btn-primary btn-sm" data-id="'+arrData[i]['id']+'" data-toggle="modal" data-target="#modalDetailWork" onclick="detailWork(this)"><i class="mdi mdi-eye"></i></button></div>',
                    // '<div>'+(arrData[i]['no'] != null ? arrData[i]['no'] : '-') +'</div>',
                    // '<div>'+(arrData[i]['estimate_end'] != null ? formatDateID(new Date(arrData[i]['estimate_end'])) : '-') +'</div>',
                    // '<div>'+(arrData[i]['is_final'] != null ? (arrData[i]['is_final'] == 0 ? 'In Rab' : (arrData[i]['is_final_production'] != null ? (arrData[i]['is_final_production'] == 0 ? 'Running' : 'Final') : 'In Rab')) : '-') +'</div>',
                    // '</div>'
                ]).draw(false);
            }
        }
    });
}
function detailWork(eq){
    $('#modal_instal_order').modal('hide');
    var id=$(eq).data('id');
    t2 = $('#detail_work').DataTable();
    t2.clear().draw(false);
    $.ajax({
        type: "GET",
        url: "{{ URL::to('order/get_work_install_order') }}"+'/'+id, //json get site
        dataType : 'json',
        success: function(response){
            arrData=response['data'];
            for(i = 0; i < arrData.length; i++){
                t2.row.add([
                    '<div>'+arrData[i]['name']+'</div>',
                    '<div class="text-right">'+formatCurrency(arrData[i]['price_work'])+'</div>',
                ]).draw(false);
            }
        }
    });
}
function editSPK(eq){
    var id=$(eq).data('id');
    var spk_number=$(eq).data('spk_no');
    $('#install_order_id').val(id)
    $('#spk_no').val(spk_number)
}
function editSPJB(eq){
    var id=$(eq).data('id');
    var spk_number=$(eq).data('spk_number');
    $('#order_id').val(id)
    $('#spk_number').val(spk_number)
}
</script>
@endif
@endsection