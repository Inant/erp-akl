@extends('theme.default')

@section('breadcrumb')
    <div class="page-breadcrumb">
        <div class="row">
            <div class="col-5 align-self-center">
                <h4 class="page-title">Progress Pengerjaan Permintaan</h4>
                <div class="d-flex align-items-center">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item active" aria-current="page">Progress Permintaan Pengerjaan Project</li>
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
                <a href="{{ URL::to('project_req_dev/create') }}"><button class="btn btn-success btn-sm mb-2"><i class="fas fa-plus"></i>&nbsp; Create New</button></a>
            </div>
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Daftar Permintaan Pengerjaan Project</h4>
                    <div class="table-responsive">
                        <table id="order_list" class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th class="text-center">Nomor Permintaan</th>
                                    <th class="text-center">Nomor Order</th>
                                    <th class="text-center">Nomor RAB</th>
                                    <th class="text-center">Customer</th>
                                    <!-- <th class="text-center">Produk</th> -->
                                    <th class="text-center">Total Order</th>
                                    <th class="text-center">Total Permintaan</th>
                                    <th class="text-center">Tanggal Permintaan</th>
                                    <th class="text-center">Tanggal Deadline</th>
                                    <th class="text-center">Status</th>
                                    <th class="text-center" style="min-width: 100px">Action</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" style="display: none;">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="title_detail">Detail Progress</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    </div>
                    <div class="modal-body">
                        <h4></h4>
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped" id="detail_request">
                                <thead>
                                    <tr>
                                        <th class="text-center">Pekerjaan</th>
                                        <th class="text-center">Kavling</th>
                                        <!-- <th class="text-center">Item</th> -->
                                        <th class="text-center">Nomor Permintaan Material</th>
                                        <th class="text-center">Nomor RAB</th>
                                        <th class="text-center">Nomor Permintaan Pengerjaan</th>
                                        <th class="text-center">Created at</th>
                                        <th class="text-center">Status</th>
                                        <td>Action</td>
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
        "ajax": "{{ URL::to('project_req_dev/json') }}",
        "aaSorting": [[ 0, "desc" ]],
        "columns": [
            {"data": "no"},
            {"data": "order_no"},
            {"data": "rab_no"},
            {"data": "customer_name"},
            // {"data": "item", "render": function(data, type, row){return row.item+' '+row.name+' '+row.series}},
            {"data": "total_order"},
            {"data": "total"},
            {"data": "request_date", "class" : 'text-center', "render": function(data, type, row){return formatDate2(row.request_date)}},
            {"data": "finish_date", "class" : 'text-center', "render": function(data, type, row){return formatDate2(row.finish_date)}},
            {"data": "status", "render": function(data, type, row){return row.status == false ? '-' : 'Selesai'}},
            {"data": "id", render : function(data, type, row){ return '<button id="modal_detail" class="btn btn-success btn-sm" data-toggle="modal" data-id="'+row.id+'" data-target=".bs-example-modal-lg" onclick="showDetail(this)"><i class="mdi mdi-eye"></i></button>&nbsp;<a href="{{URL::to('project_req_dev/saveProduct')}}/'+row.id+'" class="btn btn-info btn-sm"><i class="mdi mdi-basket-fill"></i></a>'}}
        ],
    } );
});
function formatTanggal(date) {
  var temp=date.split('-');
  return temp[2] + '-' + temp[1] + '-' + temp[0];
}
function showDetail(el){
    var id=$(el).data('id');
    t = $('#detail_request').DataTable();
    t.clear().draw(false);
    $.ajax({
        url: "{{ URL::to('project_req_dev/detailJson') }}"+'/'+id,
        dataType : 'json',
        success: function(response){
            arrData = response['data'];
            
            var j=0;
            for(i = 0; i < arrData.length; i++){
                console.log(arrData[i]['work_header']);
                j+=1;
                t.row.add([
                    '<div>'+arrData[i]['work_header']+'</div>',
                    '<div>'+arrData[i]['type_kavling']+'</div>',
                    // '<div>'+arrData[i]['item']+'  <br>Series : '+['series']+'<br> Dimensi <br>W : '+arrData[i]['panjang']+'m<sup>2</sup><br>H : '+arrData[i]['lebar']+'m<sup>2</sup>'+'</div>',
                    '<div>'+arrData[i]['no']+'</div>',
                    '<div>'+arrData[i]['rab_no']+'</div>',
                    '<div>'+arrData[i]['req_no']+'</div>',
                    '<div>'+formatTanggal(arrData[i]['created_at'])+'</div>',
                    '<div>'+arrData[i]['is_done'] == null || arrData[i]['is_done'] == false ? 'Belum Selesai' : 'Selesai' +'</div>',
                    '<div><a href="{{URL::to('project_req_dev/report')}}'+'/'+arrData[i]['dp_id']+'" class="btn waves-effect waves-light btn-xs btn-success">Laporan</a></div>',
                    '</div>'
                ]).draw(false);
            }
        }
    });
}
function formatDate2(date){
        if (date == null) {
            return '-';
        }else{

            var myDate = new Date(date);
            var tgl=date.split(/[ -]+/);
            // var output = tgl[2] + "-" +  tgl[1] + "-" + tgl[0] + ' ' + tgl[3];
            var output = tgl[2] + "-" +  tgl[1] + "-" + tgl[0];
            return output;
        }
    }
</script>
@endsection