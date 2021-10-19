@extends('theme.default')

@section('breadcrumb')
    <div class="page-breadcrumb">
        <div class="row">
            <div class="col-5 align-self-center">
                <h4 class="page-title">Pemasangan Produk</h4>
                <div class="d-flex align-items-center">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item active" aria-current="page">Daftar Pemasangan Produk</li>
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
                    <h4 class="card-title">Daftar Pemasangan Produk</h4>
                    <div class="table-responsive">
                        <table id="track_list" class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th class="text-center">Nomor Permintaan Pengerjaan</th>
                                    <th class="text-center" width="200px">Project</th>
                                    <th class="text-center">Tanggal Pemasangan</th>
                                    <th class="text-center">Pengawas</th>
                                    <th class="text-center" width="200px">Action</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- /.modal -->
    </div>                
</div>
<div class="modal fade bs-example-modal-lg" id="modalDetail" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myLargeModalLabel">Detail Pemasangan</h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <div class="modal-body">
                <p id="label-detail"></p>
                <table id="zero_config3" class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th class="text-center">No</th>
                            <th class="text-center">Pekerja</th>
                            <th class="text-center">Material</th>
                            <th class="text-center">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        
                    </tbody>
                </table>
                <br>
                <div class="table-responsive">
                <h4>Produk yang Dipasang</h4>
                    <table id="zero_config2" class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th class="text-center"></th>
                                <th class="text-center">Label</th>
                            </tr>
                        </thead>
                        <tbody>
                            
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<script src="{!! asset('theme/assets/libs/jquery/dist/jquery.min.js') !!}"></script>
<script src="{!! asset('theme/assets/extra-libs/DataTables/datatables.min.js') !!}"></script>
<script type="text/javascript">
var uri='{{URL::to('/')}}';
var t2 = $('#zero_config2').DataTable();
var t3 = $('#zero_config3').DataTable();
$(document).ready(function() {
    $('#track_list').DataTable( {
        "processing": true,
        "serverSide": true,
        "ajax": "{{URL::to('project_req_dev/list_track_frame')}}",
        "aaSorting": [[ 0, "desc" ]],
        "columns": [
            {"data": "inv_no", "class" : "text-center"},
            {"data": "io_no", "class" : "text-center"},
            {"data": "date_frame", "render": function(data, type, row){return formatTanggal(row.date_frame)}, "class" : "text-center"},
            {"data": "name", "class" : "text-center"},
            {"data": "id", "render": function(data, type, row){
                return '<div class="form-inline"><button onclick="doShowDetail2('+row.id+');" data-toggle="modal" data-target="#modalDetail" class="btn waves-effect waves-light btn-xs btn-info">Detail</button>';
            }, "class" : "text-center"}
        ],
    } );
});
function doShowDetail2(id){
    idRequest = id;
    t2.clear().draw(false);
    t3.clear().draw(false);
    $.ajax({
            type: "GET",
            url: "{{ URL::to('project_req_dev/list_track_frame_dt') }}" + "/" + id, //json get site
            dataType : 'json',
            success: function(response){
                arrData = response['dt'];
                arrData2 = response['worker'];
                var a=1, label='';
                for(i = 0; i < arrData.length; i++){
                    t2.row.add([
                        '<div class="text-center">'+a+'</div>',
                        '<div class="text-center">'+arrData[i]['item']+', Series : '+arrData[i]['series']+' <span class="label-info label">'+arrData[i]['no']+'</span></div>',
                    ]).draw(false);
                    a++;
                }
                a=1;
                for(i = 0; i < arrData2.length; i++){
                    t3.row.add([
                        '<div class="text-center">'+a+'</div>',
                        '<div class="text-center">'+arrData2[i]['name_worker']+'</div>',
                        '<div class="text-center">'+arrData2[i]['item_name']+'</div>',
                        '<div class="text-center">'+arrData2[i]['amount']+'</div>',
                    ]).draw(false);
                    a++;
                }
                // $('#label-detail').html('Pekerja : '+label.replace(/, +$/, ''));
            }
    });
}
function formatTanggal(date) {
  var temp=date.split('-');
  return temp[2] + '-' + temp[1] + '-' + temp[0];
}
</script>
@endsection