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
    .countup, #total_pekerja, #end_work, #next_form, #resume_work, #pause_work, #worker{
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
                                <td class="text-medium">Produk</td>
                                <td class="font-medium">{{$products->item}} {{$products->name}} <br>Series : {{$products->series}}<br> Dimensi <br>W : {{$products->panjang}} m<sup>2</sup><br>H : {{$products->lebar}} m<sup>2</sup></td>
                            </tr>
                            <tr>
                                <td class="text-medium">Work Header</td>
                                <td class="font-medium">
                                    {{$project_worksubs->project_header}}
                                </td>
                            </tr>
                            <tr>
                                <td class="text-medium">Pekerjaan</td>
                                <td class="font-medium">
                                    {{$project_worksubs->name}}
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
                            <input type="hidden" class="form-control" name="project_worksub_id" id="project_worksub_id"  value="{{$project_worksubs->id}}" />
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
                        <div hidden class="countup" id="countup1" style="padding-bottom : 10px">
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
                        
                        <h6 id="work_start"></h6>
                        <h6 id="work_end"></h6>
                    </center>
                    
                    <form id="pause_work" action="javascript:;" accept-charset="utf-8" method="post" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group">
                            <input type="hidden" name="id" id="id1" @if($dev_project_ds != null) value="{{$dev_project_ds->id}}" @endif/>
                            <input type="hidden" name="long_work" id="long_work"/>
                            <input type="hidden" name="status" id="status" value="pause" />
                        </div>
                        <button type="submit" id="submit" class="btn btn-warning btn-block">Pause Pekerjaan</button>
                    </form>

                    <form id="resume_work" action="javascript:;" accept-charset="utf-8" method="post" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group">
                            <input type="hidden" name="id" id="id2" @if($dev_project_ds != null) value="{{$dev_project_ds->id}}" @endif/>
                            <input type="hidden" name="long_work" id="long_work1"/>
                            <input type="hidden" name="status" id="status" value="resume" />
                        </div>
                        <button type="submit" id="submit" class="btn btn-info btn-block">Resume Pekerjaan</button>
                    </form>

                    <!-- <button onclick="resume_work()" id="resume_work" class="btn btn-info btn-block">Resume Pekerjaan</button> -->
                    <form id="end_work" action="javascript:;" accept-charset="utf-8" method="post" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group">
                            <input type="hidden" name="id" id="id3" @if($dev_project_ds != null) value="{{$dev_project_ds->id}}" @endif/>
                            <input type="hidden" name="long_work" id="long_work2"/>
                            <input type="hidden" name="status" id="status" value="done" />
                        </div>
                        <button type="submit" id="submit" class="btn btn-danger btn-block">Stop Pekerjaan</button>
                    </form>
                    <form method="POST" action="{{ URL::to('material_request/get_project') }}" class="mt-4 form-inline" id="next_form">
                      @csrf
                        <input type="hidden" name="inv_id" id="inv_id" value="{{$dev_projects->inv_request_id}}" />
                        <input type="hidden" name="dev_project_id" id="dev_project_id" value="{{$dev_projects->id}}" />
                        <input type="hidden" name="pw_id" id="pw_id" value="{{$dev_projects->project_work_id}}" />
                        <input type="hidden" name="next_id" id="next_id" value="{{$next_id}}" />
                        <input type="hidden" name="status" id="status" value="{{$status}}" />
                        <button type="submit" id="submit" class="btn {{$status == 'last' ? 'btn-danger' : 'btn-success'}} btn-block">{{$status == 'last' ? 'Finish' : 'Next '}} @if($status == 'next') <i class="mdi mdi-arrow-right"></i> {{$next_name}} @endif</button>
                     </form>
                     <br>
                     <div id="worker">
                         <h4>Nama Pekerja</h4>
                         <table id="worker_list" class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th class="text-center">Nama Pekerja</th>
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
<script src="{!! asset('theme/assets/libs/jquery/dist/jquery.min.js') !!}"></script>
<script src="{!! asset('theme/assets/extra-libs/DataTables/datatables.min.js') !!}"></script>
<script>

var long_work = 0;//in seconds

$(document).ready(function(){
    
    $("form#start_work").on("submit", function( event ) {
        var form = $('#start_work')[0];
        var data = new FormData(form);
        event.preventDefault();
        // console.log($('#start_work').serialize());
        var response_data=null;
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
            }
        });
        
        if (response_data != null) {
            $('#total_pekerja').show();
            $('#end_work').show();
            $('#id1').val(response_data['id']);
            $('#id2').val(response_data['id']);
            $('#id3').val(response_data['id']);
            $('#total_pekerja').html('Total Pekerja : ' + response_data['jumlah_pekerja']);
            $('#start_work').hide();
            $('#countup1').show();
            $('#pause_work').show();
            $('#worker').show();
            getListWorker(response_data['id']);
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
            $('#work_start').show();
            $('#work_start').html('Mulai Pekerjaan : '+formatDate(response_data['work_start']));
            $('#work_end').html('Selesai Pekerjaan : '+formatDate(response_data['work_end']));
            $('#work_end').show();
            $('#end_work').hide();
            $('#next_form').show();
            $('#pause_work').hide();
            $('#resume_work').hide();
            pause_work();
        }
    });
    $("form#pause_work").on("submit", function( event ) {
        var form = $('#pause_work')[0];
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
        // console.log(response_data);
        if (response_data != null) {
            pause_work();
            long_work=parseFloat(response_data['long_work']);
            $('#pause_work').hide();
            $('#resume_work').show();
        }
    });

    $("form#resume_work").on("submit", function( event ) {
        var form = $('#resume_work')[0];
        var data = new FormData(form);
        event.preventDefault();
        console.log($('#resume_work').serialize());
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
        // console.log(response_data);
        if (response_data != null) {
            long_work=parseFloat(response_data['long_work']);
            $('#pause_work').show();
            $('#resume_work').hide();
            countUpFromSeconds(long_work, 'countup1');  
        }
    });
});
function formatDate(date) {
  var temp=date.split(/[., \/ -]/);
  
  return temp[2] + '-' + temp[1] + '-' + temp[0] + ' ' + temp[3];
}
// window.setInterval((function(){
//    var start = Date.now();
   
//    return function() {
//         console.log(Math.floor((Date.now()-start)/1000));
//         };
//    }()), 1000);

window.onload = function() {
  // Month Day, Year Hour:Minute:Second, id-of-element-container
  @if($dev_project_ds != null)
    getListWorker('{{$dev_project_ds->id}}');
    @if($dev_project_ds->work_end == null)

        long_work = parseFloat('{{$dev_project_ds->long_work}}');
    
        @if($dev_project_ds->long_work == 0)
            countFrom = new Date('{{$dev_project_ds->work_start}}').getTime();
            var now = new Date(),
                  countFrom = new Date(countFrom),
                  timeDifference = (now - countFrom);

            long_work = Math.floor(timeDifference / 1000);
        @endif

        countUpFromSeconds(long_work, 'countup1'); // ****** Change this line!
        
        $('#long_work1').val(long_work);
        
        @if($dev_project_ds->status == 'pause')

        pause_work();
    
        @endif
    @else
        long_work = parseFloat('{{$dev_project_ds->long_work}}');
    
        @if($dev_project_ds->long_work == 0)
            countFrom = new Date('{{$dev_project_ds->work_start}}').getTime();
            var now = new Date(),
                  countFrom = new Date(countFrom),
                  timeDifference = (now - countFrom);

            long_work = Math.floor(timeDifference / 1000);
        @endif
        $('#long_work').val(long_work);
        countUpFromSeconds(long_work, 'countup1'); // ****** Change this line!
        pause_work();
        // countTime('{{$dev_project_ds->work_start}}', '{{$dev_project_ds->work_end}}', 'countup1')
    @endif
        $('#work_start').html('Mulai Pekerjaan : '+formatDate('{{$dev_project_ds->work_start}}'));
        $('#work_end').html('Selesai Pekerjaan : '+formatDate('{{$dev_project_ds->work_end}}'));
      
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
    // idEl.getElementsByClassName('years')[0].innerHTML = years;
    idEl.getElementsByClassName('days')[0].innerHTML = day;
    idEl.getElementsByClassName('hours')[0].innerHTML = hour.toString().length > 1 ? hour : '0'+hour;
    idEl.getElementsByClassName('minutes')[0].innerHTML = minutes.toString().length > 1 ? minutes : '0'+minutes;
    idEl.getElementsByClassName('seconds')[0].innerHTML = seconds.toString().length > 1 ? seconds : '0'+seconds;
    $('#long_work').val(long_work);
    $('#long_work1').val(long_work);
    $('#long_work2').val(long_work);
    
    clearTimeout(countUpFromSeconds.interval);
    countUpFromSeconds.interval = setTimeout(function(){ countUpFromSeconds(countFrom1, id); }, 1000);
  
}
function resume_work() {
    console.log(long_work);
    
}
function pause_work() {
  clearTimeout(countUpFromSeconds.interval);
}

function getListWorker(id){
    $.ajax({
        type: "GET",
        url: "{{ URL::to('material_request/get_worker_list') }}"+'/'+id, //json get site
        dataType : 'json',
        success: function(response){
            arrData=response['data'];
            for(var i=0; i < arrData.length; i++){
                $('#worker_list tbody').append('<tr><td>'+arrData[i]['name_worker']+'</td></tr>');
            }
        }
    });

    // $('#worker_list').DataTable( {
    //     "processing": true,
    //     "serverSide": true,
    //     "ajax": "{{ url('material_request/get_worker_list') }}"+'/'+id,
    //     "columns": [
    //         {"data": "name_worker"},
    //     ],
    // } );
}

</script>

<script type="text/javascript">
    
  //   countFrom = new Date(countFrom).getTime();
  // var now = new Date(),
  //     countFrom = new Date(countFrom),
  //     timeDifference = (now - countFrom);
  // var secondsInADay = 60 * 60 * 1000 * 24,
  //     secondsInAHour = 60 * 60 * 1000;

  // var diffInMilliSeconds = long_work;
  // long_work=diffInMilliSeconds;
  // console.log(long_work);
  //   // calculate days
  //   const day = Math.floor(diffInMilliSeconds / 86400);
  //   diffInMilliSeconds -= day * 86400;
  //   // calculate hours
  //   const hour = Math.floor(diffInMilliSeconds / 3600) % 24;
  //   diffInMilliSeconds -= hour * 3600;

  //   // calculate minutes
  //   const minutes = Math.floor(diffInMilliSeconds / 60) % 60;
  //   diffInMilliSeconds -= minutes * 60;

  // var seconds = diffInMilliSeconds % 60;
  // // console.log(day +' : '+ hour +' : '+ minutes +' : '+ seconds);

  // // days = Math.floor(timeDifference / (secondsInADay) * 1);
  // // years = Math.floor(days / 365);
  // // if (years > 1){ days = days - (years * 365) }
  // // hours = Math.floor((timeDifference % (secondsInADay)) / (secondsInAHour) * 1);
  
  // // mins = Math.floor(((timeDifference % (secondsInADay)) % (secondsInAHour)) / (60 * 1000) * 1);
  // // secs = Math.floor((((timeDifference % (secondsInADay)) % (secondsInAHour)) % (60 * 1000)) / 1000 * 1);

  // // var idEl = document.getElementById(id);
  // // // idEl.getElementsByClassName('years')[0].innerHTML = years;
  // // idEl.getElementsByClassName('days')[0].innerHTML = days;
  // // idEl.getElementsByClassName('hours')[0].innerHTML = hours.toString().length > 1 ? hours : '0'+hours;
  // // idEl.getElementsByClassName('minutes')[0].innerHTML = mins.toString().length > 1 ? mins : '0'+mins;
  // // idEl.getElementsByClassName('seconds')[0].innerHTML = secs.toString().length > 1 ? secs : '0'+secs;
  // var idEl = document.getElementById('countup1');
  // // idEl.getElementsByClassName('years')[0].innerHTML = years;
  // idEl.getElementsByClassName('days')[0].innerHTML = day;
  // idEl.getElementsByClassName('hours')[0].innerHTML = hour.toString().length > 1 ? hour : '0'+hour;
  // idEl.getElementsByClassName('minutes')[0].innerHTML = minutes.toString().length > 1 ? minutes : '0'+minutes;
  // idEl.getElementsByClassName('seconds')[0].innerHTML = seconds.toString().length > 1 ? seconds : '0'+seconds;
  // $('#long_work').val(long_work);

  // countUpFromTime.interval = setTimeout(function(){ countUpFromTime(countFrom, id); }, 1000);


// function countTime(countFrom, countTo, id) {
//   countFrom = new Date(countFrom).getTime();
//   var to = new Date(countTo).getTime();
//   var now = new Date(to),
//       countFrom = new Date(countFrom),
//       timeDifference = (now - countFrom);
    
//   var secondsInADay = 60 * 60 * 1000 * 24,
//       secondsInAHour = 60 * 60 * 1000;
    
//   days = Math.floor(timeDifference / (secondsInADay) * 1);
//   years = Math.floor(days / 365);
//   if (years > 1){ days = days - (years * 365) }
//   hours = Math.floor((timeDifference % (secondsInADay)) / (secondsInAHour) * 1);
//   mins = Math.floor(((timeDifference % (secondsInADay)) % (secondsInAHour)) / (60 * 1000) * 1);
//   secs = Math.floor((((timeDifference % (secondsInADay)) % (secondsInAHour)) % (60 * 1000)) / 1000 * 1);

//   var idEl = document.getElementById(id);
//   // idEl.getElementsByClassName('years')[0].innerHTML = years;
//   idEl.getElementsByClassName('days')[0].innerHTML = days;
//   idEl.getElementsByClassName('hours')[0].innerHTML = hours.toString().length > 1 ? hours : '0'+hours;
//   idEl.getElementsByClassName('minutes')[0].innerHTML = mins.toString().length > 1 ? mins : '0'+mins;
//   idEl.getElementsByClassName('seconds')[0].innerHTML = secs.toString().length > 1 ? secs : '0'+secs;
//   // console.log(mins.toString().length);
//   clearTimeout(countUpFromTime.interval);
// }
</script>
@endsection
