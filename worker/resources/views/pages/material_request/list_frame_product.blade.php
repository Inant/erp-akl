@extends('theme.default')

@section('breadcrumb')
<div class="page-breadcrumb">
    <div class="row">
        <div class="col-md-5 align-self-center">
            <h4 class="page-title">Pencatatan Pemasangan</h4>
            <div class="d-flex align-items-center">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Pencatatan Pemasangan</li>
                    </ol>
                </nav>
            </div>
        </div>
        <div class="col-7 align-self-center">
            
        </div>
    </div>
</div>
@endsection

@section('content')
<style>
    @media only screen and (max-width: 600px) {
      table {
        font-size: 14px;
      }
    }
</style>
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">Pencatatan Pemasangan</div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-12">
                            <h4 id="titleJurnal">Cari Permintaan</h4>
                            <form method="POST" action="{{ URL::to('material_request/form_frame') }}" class="mt-4">
                              @csrf
                                <div class="form-group">
                                    <select id="select_product" class="form-control select2" name="inv_id" style="width:100%" required>
                                        <option value="">--- Cari Permintaan ---</option>
                                    </select>
                                </div>                   
                                <button type="submit" class="btn btn-success btn-block">pantau</button>
                             </form>
                        </div>
                    </div>
                    <br>
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
<script>

var t2 = $('#zero_config2').DataTable();
var t3 = $('#zero_config3').DataTable();
var uri='{{URL::to('/')}}';
$(document).ready(function() {
    $('#track_list').DataTable( {
        "processing": true,
        "serverSide": true,
        "ajax": "{{URL::to('material_request/list_track_frame')}}",
        "aaSorting": [[ 0, "desc" ]],
        "columns": [
            {"data": "inv_no", "class" : "text-center"},
            {"data": "io_no", "class" : "text-center"},
            {"data": "date_frame", "render": function(data, type, row){return formatDate(row.date_frame)}, "class" : "text-center"},
            {"data": "name", "class" : "text-center"},
            {"data": "id", "render": function(data, type, row){
                return '<div class="form-inline"><button onclick="doShowDetail2('+row.id+');" data-toggle="modal" data-target="#modalDetail" class="btn waves-effect waves-light btn-xs btn-info">Detail</button>';
            }, "class" : "text-center"}
        ],
    } );
    $('#select_product').select2({
        escapeMarkup: function(markup) {
            return markup;
        },
        ajax : {
            url : '{{ url('material_request/suggest_project_done')}}',
            delay: 250,
            dataType : 'json',
            // processResults: function (data) {
            //     return {
            //     results:  $.map(data, function (item) {
            //         return {
            //             text: '<span class="badge badge-primary">'+item.inv_no+'</span>&nbsp;<span class="badge badge-success">'+item.rab_no+'</span>&nbsp;<span class="badge badge-info">'+item.req_no+'</span> ('+item.name+')',
            //             id: item.inv_id
            //         }
            //     })
            //     };
            // },
            processResults: function (data) {
                return {
                    results: data
                };
            },
            // cache: true
        },
        templateResult: function (data) {
            if (data.loading) return data.text;
            return '<span class="badge badge-primary">'+data.inv_no+'</span>&nbsp;<span class="badge badge-info">'+data.no_order_install+'</span>';
        }
    });  
    
    $('#select_product').change(function(){
        var id=$('#select_product').val();
        // getPws(id);
    });
});
function doShowDetail2(id){
    idRequest = id;
    t2.clear().draw(false);
    t3.clear().draw(false);
    $.ajax({
            type: "GET",
            url: "{{ URL::to('material_request/list_track_frame_dt') }}" + "/" + id, //json get site
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
                for(i = 0; i < arrData2.length; i++){
                    label+=arrData2[i]['name_worker']+', ';
                }
                $('#label-detail').html('Pekerja : '+label.replace(/, +$/, ''));
            }
    });
}
function formatDate(date) {
  var temp=date.split(/[.,\/ -]/);
  return temp[2] + '-' + temp[1] + '-' + temp[0];
}
</script>
@endsection
