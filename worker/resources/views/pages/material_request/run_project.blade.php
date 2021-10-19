@extends('theme.default')

@section('breadcrumb')
<div class="page-breadcrumb">
    <div class="row">
        <div class="col-md-5 align-self-center">
            <h4 class="page-title">Progress Proyek</h4>
            <div class="d-flex align-items-center">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Progress Proyek</li>
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
    <?php if($dev_project_ds == null){?>
    .countup, #total_pekerja, #end_work, #next_form, #resume_work, #pause_work, #worker, #duration{
        display: none
    }
    <?php }else{?>
    #start_work{
        display: none   
    }
    <?php if($dev_project_ds->work_end == null){?>
    #work_start, #work_end, #next_form{
        display: none
    }
        <?php if($dev_project_ds->status == 'pause'){?>
    #pause_work{
        display: none
    }
        <?php }else{?>
    #resume_work{
        display: none
    }
        <?php }?>
    <?php }else{?>
    #end_work, #resume_work, #pause_work{
        display: none
    }
    <?php }}?>
</style>
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <!-- <div class="card-header">Dashboard</div> -->
                <div class="card-body">
                    <!-- <h4 class="card-title">Temp Guide</h4> -->
                    <table class="table no-border mini-table m-t-20">
                        <tbody>
                            <tr>
                                <td class="text-medium">Project</td>
                                <td class="font-medium">
                                    {{$inv_requests->name}}
                                </td>
                            </tr>
                            <tr>
                                <td class="text-medium">No Permintaan</td>
                                <td class="font-medium">
                                    {{$inv_requests->req_no}}
                                </td>
                            </tr>
                            <tr>
                                <td class="text-medium">Work Header</td>
                                <td class="font-medium">
                                    {{$dev_projects->work_header}}
                                </td>
                            </tr>
                            <tr>
                                <td class="text-medium">Pekerjaan</td>
                                <td class="font-medium">
                                    {{$dev_project_ds->notes}}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="card-body">
                    <form id="start_work" action="javascript:;" accept-charset="utf-8" method="post" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group">
                            <input type="hidden" name="dev_project_id" id="dev_project_id" value="{{$dev_projects->id}}" />
                            <input type="hidden" class="form-control" name="work_detail" id="work_detail"  value="{{$dev_project_ds->work_detail}}" />
                            <!-- <input type="text" class="form-control" name="total_worker" id="total_worker" placeholder="Jumlah Pekerja"/> -->
                            <div class="email-repeater form-group">
                                <div data-repeater-list="repeater-group">
                                    <div data-repeater-item class="row m-b-15">
                                        <div class="col-sm-12">
                                            <!-- <input type="" name="worker_name[]" class="form-control" placeholder="Nama Pekerja">
                                        </div>
                                        <div class="col-sm-2">
                                            <button data-repeater-delete="" class="btn btn-danger waves-effect waves-light" type="button"><i class="ti-close"></i>
                                            </button> -->
                                            <div class="input-group">
                                                <input type="" name="worker_name" class="form-control" placeholder="Nama Pekerja" aria-label="" aria-describedby="basic-addon1">&nbsp;
                                                <div class="input-group-append">
                                                    <button data-repeater-delete="" class="btn btn-danger waves-effect waves-light" type="button"><i class="ti-close"></i>
                                                </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <button type="button" data-repeater-create="" class="btn btn-info waves-effect waves-light">Tambah Perkerja
                                </button>
                            </div>
                            <br>
                        </div>
                        <button type="submit" id="submit" class="btn btn-success btn-block">Mulai</button>
                    </form>
                    
                    <center>
                        <h6 id="total_pekerja">{{$dev_project_ds == null ? '' : 'Total Pekerja : '. $dev_project_ds->jumlah_pekerja}}</h6>
                        <div class="countup" id="countup1" style="padding-bottom : 10px; display : none">
                            <h4 class="font-medium">Durasi Kerja</h4>
                            <!--   <span class="timeel years">00</span>
                            <span class="timeel timeRefYears">years</span> -->
                            <div hidden class="badge badge-success font-18 timeel days">00</div>
                            <span hidden class="font-18">:</span>
                            <div class="badge badge-success font-18 timeel hours">00</div>
                            <span class="font-18">:</span>
                            <div class="badge badge-success font-18 timeel minutes">00</div>
                            <span class="font-18">:</span>
                            <div class="badge badge-success font-18 timeel seconds">00</div> 
                        </div>
                        
                        <!-- <h6 id="work_start"></h6>
                        <h6 id="work_end"></h6> -->
                    </center>
                    
                    <form id="pause_work" action="javascript:;" accept-charset="utf-8" method="post" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group">
                            <input type="hidden" name="dev_project_id" id="dev_project_id" value="{{$dev_projects->id}}" />
                            <input type="hidden" name="id" id="id1" @if($dev_project_ds != null) value="{{$dev_project_ds->dev_d_id}}" @endif/>
                            <input type="hidden" name="id_duration" id="id_duration1" @if($dev_project_ds != null) value="{{$idLastDuration}}" @endif/>
                            <!-- <input type="hidden" name="long_work" id="long_work"/> -->
                            <input type="hidden" name="status" id="status" value="pause" />
                            <!-- <input type="hidden" name="pw_id" id="pw_id" value="{{$dev_projects->project_work_id}}" /> -->
                            <input type="hidden" name="work_header" id="work_header" value="{{$dev_projects->work_header}}" />
                        </div>
                        <button type="submit" id="submit" class="btn btn-warning btn-block">Pause Pekerjaan</button>
                    </form>

                    <form id="resume_work" action="javascript:;" accept-charset="utf-8" method="post" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group">
                            <input type="hidden" name="dev_project_id" id="dev_project_id" value="{{$dev_projects->id}}" />
                            <input type="hidden" name="id" id="id2" @if($dev_project_ds != null) value="{{$dev_project_ds->dev_d_id}}" @endif/>
                            <input type="hidden" name="id_duration" id="id_duration2" @if($dev_project_ds != null) value="{{$idLastDuration}}" @endif/>
                            <!-- <input type="hidden" name="long_work" id="long_work1"/> -->
                            <input type="hidden" name="status" id="status" value="resume" />
                            <!-- <input type="hidden" name="pw_id" id="pw_id" value="{{$dev_projects->project_work_id}}" /> -->
                            <input type="hidden" name="work_header" id="work_header" value="{{$dev_projects->work_header}}" />
                        </div>
                        <button type="submit" id="submit" class="btn btn-info btn-block">Resume Pekerjaan</button>
                    </form>

                    <!-- <button onclick="resume_work()" id="resume_work" class="btn btn-info btn-block">Resume Pekerjaan</button> -->
                    <form id="end_work" action="javascript:;" accept-charset="utf-8" method="post" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group">
                            <input type="hidden" name="dev_project_id" id="dev_project_id" value="{{$dev_projects->id}}" />
                            <input type="hidden" name="id" id="id3" @if($dev_project_ds != null) value="{{$dev_project_ds->dev_d_id}}" @endif/>
                            <input type="hidden" name="id_duration" id="id_duration3" @if($dev_project_ds != null) value="{{$idLastDuration}}" @endif/>
                            <!-- <input type="hidden" name="long_work" id="long_work2"/> -->
                            <input type="hidden" name="status" id="status" value="done" />
                            <!-- <input type="hidden" name="pw_id" id="pw_id" value="{{$dev_projects->project_work_id}}" /> -->
                            <input type="hidden" name="work_header" id="work_header" value="{{$dev_projects->work_header}}" />
                        </div>
                        <button type="submit" id="submit" class="btn btn-danger btn-block">Stop Pekerjaan</button>
                    </form>
                    
                     <br>
                     <div id="duration" hidden>
                         <h4>Durasi Kerja</h4>
                         <table id="duration_list" class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th class="text-center">Mulai</th>
                                    <th class="text-center">Istirahat / Selesai</th>
                                </tr>
                            </thead>
                            <tbody>
                                
                            </tbody>
                        </table>
                     </div>
                     <br>
                     <div id="duration">
                         <h4>Produk Label Yang di Kerjakan</h4>
                         <table id="label_list" class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th class="text-center">Label</th>
                                </tr>
                            </thead>
                            <tbody>
                                
                            </tbody>
                        </table>
                     </div>
                     <br>
                     <div id="worker">
                         <h4>Nama Pekerja</h4>
                         <div class="form-group">
                            <button class="btn btn-primary btn-sm" type="button"  data-toggle="modal" data-target="#modalAddWorker" >Tambah Pekerja</button>
                         </div>
                         <table id="worker_list" class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th class="text-center">Nama Pekerja</th>
                                    <th>Catatan</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                
                            </tbody>
                        </table>
                     </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div id="modalAddWorker" class="modal fade" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel">Tambah Pekerja</h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <form id="addWorker" action="javascript:;" accept-charset="utf-8" method="post" enctype="multipart/form-data">
            <div class="modal-body">
                @csrf
                <input type="hidden" name="dev_project_id" id="dev_project_id" value="{{$dev_projects->id}}" />
                <input type="hidden" name="dev_project_d_id" id="dev_project_d_id" @if($dev_project_ds != null) value="{{$dev_project_ds->dev_d_id}}" @endif/>
                <div class="form-group">
                    <label>Nama Pekerja</label>
                    <input type="text" class="form-control" required id="worker_name" name="worker_name">
                </div>
                <div class="form-group">
                    <label>Catatan (Opsional)</label>
                    <textarea name="notes" id="notes" class="form-control"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger waves-effect" data-dismiss="modal">Close</button>
                <button class="btn btn-info waves-effect" id="submit">Submit</button>
            </div>
            </form>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<div id="modalEditWorker" class="modal fade" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel">Tambah Pekerja</h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <form id="editWorker" action="javascript:;" accept-charset="utf-8" method="post" enctype="multipart/form-data">
            <div class="modal-body">
                @csrf
                <input type="hidden" name="dev_project_worker_id" id="dev_project_worker_id" />
                <div class="form-group">
                    <label>Catatan (Opsional)</label>
                    <textarea name="notes" id="notes1" class="form-control"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger waves-effect" data-dismiss="modal">Close</button>
                <button class="btn btn-info waves-effect" id="submit">Submit</button>
            </div>
            </form>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<script src="{!! asset('theme/assets/libs/jquery/dist/jquery.min.js') !!}"></script>
<script src="{!! asset('theme/assets/extra-libs/DataTables/datatables.min.js') !!}"></script>
<script>

var long_work = 0;//in seconds

$(document).ready(function(){
    $("form#addWorker").on("submit", function( event ) {
        var form = $('#addWorker')[0];
        var data = new FormData(form);
        event.preventDefault();
        // console.log( $('form#addKavling').serialize() );
        var response_data=null;
        $.ajax({
            type: "POST",
            url: "{{ URL::to('material_request/add_worker') }}", //json get site
            dataType : 'json',
            data: data,
            async: false,
            processData: false,
            contentType: false,
            success: function(response){
                response_data=response;
            }
        });
        console.log(response_data['dev_project_d_id'])
        getListWorker(response_data['dev_project_d_id'])
        $('#modalAddWorker').modal('hide');
    });
    $("form#editWorker").on("submit", function( event ) {
        var form = $('#editWorker')[0];
        var data = new FormData(form);
        event.preventDefault();
        // console.log( $('form#addKavling').serialize() );
        var response_data=null;
        $.ajax({
            type: "POST",
            url: "{{ URL::to('material_request/edit_worker') }}", //json get site
            dataType : 'json',
            data: data,
            async: false,
            processData: false,
            contentType: false,
            success: function(response){
                response_data=response['data'];
            }
        });
        console.log(response_data['dev_project_d_id'])
        getListWorker(response_data['dev_project_d_id'])
        $('#modalEditWorker').modal('hide');
    });
    $("form#start_work").on("submit", function( event ) {
        var form = $('#start_work')[0];
        var data = new FormData(form);
        event.preventDefault();
        // console.log($('#start_work').serialize());
        var response_data=null;
        var response_duration=null;
        $.ajax({
            type: "POST",
            url: "{{ URL::to('material_request/save_dev_request_d') }}", //json get site
            dataType : 'json',
            data: data,
            async: false,
            processData: false,
            contentType: false,
            success: function(response){
                response_data=response['data'];
                response_duration=response['duration'];
            }
        });
        
        if (response_data != null) {
            $('#total_pekerja').show();
            $('#end_work').show();
            $('#id1').val(response_data['id']);
            $('#id2').val(response_data['id']);
            $('#id3').val(response_data['id']);
            $('#id_duration1').val(response_duration['id']);
            $('#id_duration2').val(response_duration['id']);
            $('#id_duration3').val(response_duration['id']);
            $('#total_pekerja').html('Total Pekerja : ' + response_data['jumlah_pekerja']);
            $('#start_work').hide();
            // $('#countup1').show();
            $('#pause_work').show();
            $('#worker').show();
            $('#duration').show();
            getListWorker(response_data['id']);
            getListDuration(response_data['id']);
            countUpFromSeconds(parseFloat(response_data['long_work']), 'countup1');      
        }
    });

    $("form#end_work").on("submit", function( event ) {
        var form = $('#end_work')[0];
        var data = new FormData(form);
        event.preventDefault();
        var response_data=null;
        $.ajax({
            type: "POST",
            url: "{{ URL::to('material_request/update_dev_request_d') }}", //json get site
            dataType : 'json',
            data: data,
            async: false,
            processData: false,
            contentType: false,
            success: function(response){
                response_data=response['data'];
            }
        });
        
        if (response_data != null) {
            $('#end_work').hide();
            $('#next_form').show();
            $('#pause_work').hide();
            $('#resume_work').hide();
            getListDuration(response_data['id']);
            countLongWork(response_data['id']);
        }
    });
    $("form#pause_work").on("submit", function( event ) {
        var form = $('#pause_work')[0];
        var data = new FormData(form);
        event.preventDefault();
        console.log($('#pause_work').serialize());
        var response_data=null;
        $.ajax({
            type: "POST",
            url: "{{ URL::to('material_request/update_dev_request_d') }}", //json get site
            dataType : 'json',
            data: data,
            async: false,
            processData: false,
            contentType: false,
            success: function(response){
                response_data=response['data'];
            }
        });

        if (response_data != null) {
            getListDuration(response_data['id']);
            $('#pause_work').hide();
            $('#resume_work').show();
        }
    });

    $("form#resume_work").on("submit", function( event ) {
        var form = $('#resume_work')[0];
        var data = new FormData(form);
        event.preventDefault();
        // console.log($('#resume_work').serialize());
        var response_data=null;
        var last_id=null;
        $.ajax({
            type: "POST",
            url: "{{ URL::to('material_request/update_dev_request_d') }}", //json get site
            dataType : 'json',
            data: data,
            async: false,
            processData: false,
            contentType: false,
            success: function(response){
                response_data=response['data'];
                last_id=response['last_id'];
            }
        });
        if (response_data != null) {
            $('#pause_work').show();
            $('#resume_work').hide();
            $('#id_duration1').val(last_id);
            $('#id_duration2').val(last_id);
            $('#id_duration3').val(last_id);
            getListDuration(response_data['id']);
            countUpFromSeconds(long_work, 'countup1');  
        }
    });
});
function formatDate(date) {
    if(date == null){
        return '-';
    }else{
        var temp=date.split(/[., \/ -]/);
        return temp[2] + '-' + temp[1] + '-' + temp[0] + ' ' + temp[3];
    }
}

window.onload = function() {
  // Month Day, Year Hour:Minute:Second, id-of-element-container
  @if($dev_project_ds != null)
    getListWorker('{{$dev_project_ds->dev_d_id}}');
    getListDuration('{{$dev_project_ds->dev_d_id}}');
    getListLabel('{{$dev_project_ds->dev_d_id}}');
    @if($dev_project_ds->status == 'done')

    countLongWork('{{$dev_project_ds->dev_d_id}}');

    @endif

  @endif
};

var start = new Date();
function countUpFromSeconds(countFrom1, id) {

    var diffInMilliSeconds = countFrom1++;
    long_work=diffInMilliSeconds;
    

    const day = Math.floor(diffInMilliSeconds / 86400);
    diffInMilliSeconds -= day * 86400;
    // calculate hours
    const hour = Math.floor(diffInMilliSeconds / 3600) % 24;
    diffInMilliSeconds -= hour * 3600;

    // calculate minutes
    const minutes = Math.floor(diffInMilliSeconds / 60) % 60;
    diffInMilliSeconds -= minutes * 60;

    var seconds = diffInMilliSeconds % 60;

    var idEl = document.getElementById(id);
    idEl.getElementsByClassName('days')[0].innerHTML = day;
    idEl.getElementsByClassName('hours')[0].innerHTML = hour.toString().length > 1 ? hour : '0'+hour;
    idEl.getElementsByClassName('minutes')[0].innerHTML = minutes.toString().length > 1 ? minutes : '0'+minutes;
    idEl.getElementsByClassName('seconds')[0].innerHTML = seconds.toString().length > 1 ? seconds : '0'+seconds;  
}

function countLongWork(id){
    $.ajax({
        type: "GET",
        url: "{{ URL::to('material_request/duration_list') }}"+'/'+id, //json get site
        dataType : 'json',
        async : false,
        success: function(response){
            arrData=response['data'];
            for(var i=0; i < arrData.length; i++){
                countFrom = new Date(arrData[i]['work_start']).getTime();
                var now = new Date(arrData[i]['work_end']).getTime(),
                    
                    timeDifference = (now - countFrom);

                in_seconds= Math.floor(timeDifference / 1000);
                long_work+=in_seconds;
            }

            // $('#countup1').show();
            countUpFromSeconds(long_work, 'countup1');
        }
    });
}
function getListWorker(id){
    $('#worker_list > tbody').empty();
    $.ajax({
        type: "GET",
        url: "{{ URL::to('material_request/get_worker_list') }}"+'/'+id, //json get site
        dataType : 'json',
        success: function(response){
            arrData=response['data'];
            for(var i=0; i < arrData.length; i++){
                tdAdd='<tr>'+
                        '<td>'+arrData[i]['name_worker']+'</td>'+
                        '<td>'+(arrData[i]['notes'] != null ? arrData[i]['notes'] : '')+'</td>'+
                        '<td><button data-id="'+arrData[i]['id']+'" data-notes="'+arrData[i]['notes']+'" data-toggle="modal" data-target="#modalEditWorker" onclick="editWorker(this)" class="btn btn-sm btn-info">Edit Catatan</button></td>'+
                        '</tr>';
                $('#worker_list tbody').append(tdAdd);
            }
        }
    });
}
function editWorker(eq){
    var id=$(eq).data('id')
    var notes=$(eq).data('notes')
    $('#dev_project_worker_id').val(id)
    $('#notes1').val((notes != null ? notes : ''))
}
function getListDuration(id){
    var count_rest = 0;//in seconds
    $('#duration_list tbody').empty();
    $.ajax({
        type: "GET",
        async : false,
        url: "{{ URL::to('material_request/duration_list') }}"+'/'+id, //json get site
        dataType : 'json',
        success: function(response){
            arrData=response['data'];
            for(var i=0; i < arrData.length; i++){
                count_rest++;
                $('#duration_list tbody').append('<tr><td>'+formatDate(arrData[i]['work_start'])+'</td><td>'+formatDate(arrData[i]['work_end'])+'</td></tr>');
            }
        }
    });

    // if(count_rest > 3){
    //     $('#pause_work').hide();
    //     $('#resume_work').hide();
    // }
}
function getListLabel(id){
    $('#label_list tbody').empty();
    $.ajax({
        type: "GET",
        async : false,
        url: "{{ URL::to('material_request/get_label') }}"+'/'+id, //json get site
        dataType : 'json',
        success: function(response){
            arrData=response['data'];
            for(var i=0; i < arrData.length; i++){
                $('#label_list tbody').append('<tr><td>'+arrData[i]['no']+'</td></tr>');
            }
        }
    });
}

</script>
@endsection
