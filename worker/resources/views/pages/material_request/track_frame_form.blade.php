@extends('theme.default')

@section('breadcrumb')
<div class="page-breadcrumb">
    <div class="row">
        <div class="col-md-5 align-self-center">
            <h4 class="page-title">Form Pasang Produk</h4>
            <div class="d-flex align-items-center">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Library</li>
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
    <?php if($frame_worker == 0){?>
    #table_worker{
        display: none
    }
    <?php }else{?>
    #worker_form{
        display: none
    }
    <?php }?>
    <?php if($frame_material_worker == 0){?>
    #table_material_worker{
        display: none
    }
    <?php }else{?>
    #material_form{
        display: none
    }
    <?php }?>
</style>
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">Dashboard</div>
                <div class="card-body">
                    <table>
                        <thead>
                            <tr>
                                <td>Nomor Permintaan Material</td>
                                <td>:</td>
                                <td>{{$inv_requests->no}}</td>
                            </tr>
                        </thead>
                    </table>
                    <br>
                    <form id="worker_form" action="javascript:;" accept-charset="utf-8" method="post" enctype="multipart/form-data">
                        @csrf
                        <h4 class="card-title">Tambah Pekerja</h4>
                        <input type="hidden" name="dev_id" value="{{$dev_frames->id}}">
                        <div class="email-repeater form-group">
                            <div data-repeater-list="repeater-group">
                                <div data-repeater-item class="row m-b-15">
                                    <div class="col-sm-12">
                                        
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
                            <button type="button" data-repeater-create="" class="btn btn-info waves-effect waves-light"><i class="mdi mdi-plus"></i>
                            </button>
                        </div>
                        <button class="btn btn-success">Simpan</button>
                    </form>
                    <div class="table-responsive" id="table_worker">
                    <br>
                    <h4 class="card-title">Daftar Pekerja</h4>
                        <table class="table table-bordered table-striped" id="worker_list" style="width:100%">
                            <thead>
                                <tr>
                                    <td></td>
                                    <td>Nama Worker</td>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                    <div class="table-responsive" id="table_material">
                    <br>
                        <table class="table table-bordered table-striped" id="material_list">
                            <thead>
                                <tr>
                                    <td>Material</td>
                                    <td>Total</td>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                        <hr>
                    </div>
                    
                    <div class="table-responsive" id="table_material_worker">
                        <h4 class="card-title">Titipan Material pada Pekerja</h4>
                        <table class="table table-bordered table-striped" id="material_worker_list" style="width:100%">
                            <thead>
                                <tr>
                                    <td></td>
                                    <td>Nama Pekerja</td>
                                    <td>Material</td>
                                    <td>Total</td>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                    <form id="material_form" action="javascript:;" accept-charset="utf-8" method="post" enctype="multipart/form-data">
                        @csrf
                        <h4 class="card-title">Titipan Material pada Pekerja</h4>
                        <button class="btn btn-info" onclick="addField()" type="button">Tambah</button>
                        <br><br>
                        <input type="hidden" name="dev_id" value="{{$dev_frames->id}}">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped" id="detail_worker">
                                <thead>
                                    <tr>
                                        <td>Nama Pekerja</td>
                                        <td>Material</td>
                                        <td>Total</td>
                                        <td></td>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                        <button class="btn btn-success">Simpan</button>
                    </form>
                    <hr>
                    <div class="row">
                        <div class="col-12">
                            <!-- <h4 id="titleJurnal">Cari Pekerjaan</h4> -->
                            <form action="{{URL::to('material_request/save_track_frame')}}" accept-charset="utf-8" method="post" enctype="multipart/form-data">
                                @csrf
                                <input type="hidden" name="dev_id" value="{{$dev_frames->id}}">
                                <!-- <label>
                                <input type="checkbox" name="is_out_source" id="is_out_source" value="1"> Out Sourcing
                                </label> -->
                                <br>
                                <div class="form-group">
                                    <input type="hidden" name="inv_id" id="inv_id" value="{{$inv_id}}" />
                                </div>
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Label</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($label as $value)
                                        <tr>
                                            <td>{{$value->item}}, Series : {{$value->series}} <span class="label-info label">{{$value->no}}</span></td>
                                            <td>
                                            <input type="checkbox" style="display: none" name="prod_id[]" id="prod_id[]" value="{{$value->product_sub_id}}">
                                            <button type="button" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#modalWorkSub" onclick="editWork(this)" data-id="{{$value->id}}"  data-product_sub_id="{{$value->product_sub_id}}" data-install_order_id="{{$value->install_order_id}}"><i class="mdi mdi-check"></i></button>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                                <button hidden type="submit" id="submit" class="btn btn-success btn-block">Simpan</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div id="modalWorkSub" class="modal fade" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel">Add Work Sub</h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <form id="addWorksub" action="javascript:;" accept-charset="utf-8" method="post" enctype="multipart/form-data">
            <div class="modal-body">
                @csrf
                <h4>Pekerjaan :</h4>
                <table id="detail_work" style="width:100%">
                    <tbody>
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger btn-sm waves-effect" data-dismiss="modal">Close</button>
                <!-- <button class="btn btn-info btn-sm waves-effect" id="submit">Simpan</button> -->
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
var itemList=[];
var workerList=[];
var inv_request_id={{$inv_id}};
var id={{$dev_frames->id}};
var worker_list=$('#worker_list').DataTable();
var material_worker_list=$('#material_worker_list').DataTable();
$(document).ready(function() {
    worker_list.clear().draw(false);
    $.ajax({
            type: "GET",
            url: "{{ URL::to('material_request/list_track_frame_dt') }}" + "/" + id, //json get site
            dataType : 'json',
            success: function(response){
                arrData2 = response['worker'];
                workerList=arrData2;
                var a=1;
                for(i = 0; i < arrData2.length; i++){
                    worker_list.row.add([
                        '<div class="text-center">'+a+'</div>',
                        '<div class="text-center">'+arrData2[i]['name_worker']+'</div>',
                    ]).draw(false);
                    a++;
                }
            }
    });

    material_worker_list.clear().draw(false);
    $.ajax({
            type: "GET",
            url: "{{ URL::to('material_request/get_item_frame_material') }}" + "/" + id, //json get site
            dataType : 'json',
            success: function(response){
                arrData2 = response['data'];
                workerMaterialList=arrData2;
                var a=1;
                for(i = 0; i < arrData2.length; i++){
                    material_worker_list.row.add([
                        '<div class="text-center">'+a+'</div>',
                        '<div class="text-center">'+arrData2[i]['name_worker']+'</div>',
                        '<div class="text-center">'+arrData2[i]['m_items']['name']+'</div>',
                        '<div class="text-center">'+arrData2[i]['amount']+'</div>',
                    ]).draw(false);
                    a++;
                }
            }
    });
    $("form#worker_form").on("submit", function( event ) {
        var form = $('#worker_form')[0];
        var data = new FormData(form);
        event.preventDefault();
        $.ajax({
            type: "POST",
            url: "{{ URL::to('material_request/save_frame_worker') }}", //json get site
            dataType : 'json',
            data: data,
            async: false,
            processData: false,
            contentType: false,
            success: function(response){
                console.log(response)
                $('#table_worker').show();
                $('#worker_form').hide();
            }
        });
        refreshWorker();
    });
    $("form#material_form").on("submit", function( event ) {
        var form = $('#material_form')[0];
        var data = new FormData(form);
        event.preventDefault();
        // console.log($('#material_form').serialize());
        $.ajax({
            type: "POST",
            url: "{{ URL::to('material_request/save_frame_material_worker') }}", //json get site
            dataType : 'json',
            data: data,
            async: false,
            processData: false,
            contentType: false,
            success: function(response){
                $('#table_material_worker').show();
                $('#material_form').hide();
            }
        });
        refreshMaterialWorker();
    });
    $.ajax({
            type: "GET",
            url: "{{ URL::to('material_request/get_item_frame') }}" + "/" + inv_request_id, //json get site
            dataType : 'json',
            success: function(response){
                arrData = response['data'];
                itemList = arrData;
                for (var i = 0; i < itemList.length; i++) {
                    var tdAdd='<tr>'+
                                    '<td>'+itemList[i]['m_items']['name']+'</td>'+
                                    '<td>'+parseFloat(itemList[i]['amount']).toFixed(0);+'</td>'+
                                '</tr>';
                    $('#material_list').find('tbody:last').append(tdAdd);
                    
                }
            }
    });
} );
function addField(){
    var option_worker='<option value="">--- Pilih Pekerja ---</option>';
    for(i = 0; i < workerList.length; i++){
        option_worker+='<option value="'+workerList[i]['id']+'">'+workerList[i]['name_worker']+'</option>';
    }
    var option_material='<option value="">--- Pilih Material ---</option>';
    for(i = 0; i < itemList.length; i++){
        option_material+='<option value="'+itemList[i]['m_item_id']+'">'+itemList[i]['m_items']['name']+'</option>';
    }
    var tdAdd='<tr>'+
                    '<td>'+
                        '<select id="worker_id[]" name="worker_id[]" required class="form-control custom-select" style="width: 100%; height:32px;">'+
                            option_worker+
                        '</select>'+
                    '</td>'+
                    '<td>'+
                        '<select id="m_item_id[]" name="m_item_id[]" onchange="cekItem()" required class="form-control custom-select" style="width: 100%; height:32px;">'+
                            option_material+
                        '</select>'+
                    '</td>'+
                    '<td>'+
                        '<input type="hidden" name="m_unit_id[]" class="form-control" id="m_unit_id[]"><input type="" name="amount[]" required class="form-control" id="amount[]" onkeyup="cekVolumeItem()">'+
                    '</td>'+
                    '<td class="text-center"><button class="btn btn-danger removeOption"><i class="mdi mdi-delete"></i></button></td>'+
                '</tr>';
    $('#detail_worker').find('tbody:last').append(tdAdd);
}
$("#detail_worker").on("click", ".removeOption", function(event) {
    event.preventDefault();
    $(this).closest("tr").remove();
});

function cekItem(){
    var id = $('[id^=m_item_id]');
    var unit_id = $('[id^=m_unit_id]');
    for(var i = 0; i < id.length; i++){
        var m_item_id=id.eq(i).val();
        if (m_item_id != ''){
            $.each(itemList, function(j, item){
                if (m_item_id == item.m_item_id) {
                    unit_id.eq(i).val(item.m_unit_id);
                }
            })
        }else{
            unit_id.eq(i).val('');
        }
    }
}

function cekVolumeItem(){
    var id = $('[id^=m_item_id]');
    var amount = $('[id^=amount]');
    var tempAll=[];
    for(var i = 0; i < id.length; i++){
        var m_item_id=id.eq(i).val();
        var amount_item=amount.eq(i).val();
        
        if (m_item_id != '' && amount_item != ''){
            var is_there=false;
            var index=0;
            $.each(tempAll, function(j, item){
                if (m_item_id == item.m_item_id) {
                    is_there=true;
                    index=j;                    
                }
            })        
            if (is_there == true) {
                tempAll[index]['amount']=parseFloat(tempAll[index]['amount']) + parseFloat(amount_item) 
            }else{
                tempAll.push({'m_item_id' : m_item_id , 'amount' : amount_item})
            }
            var amount_material=0;
            $.each(itemList, function(j, item){
                if (m_item_id == item.m_item_id) {
                    amount_material=item.amount
                }
            })
            
            $.each(tempAll, function(j, item){
                if (m_item_id == item.m_item_id) {
                    if (parseFloat(item.amount) > parseFloat(amount_material)) {
                        amount.eq(i).val('');
                        amount.eq(i).focus();
                        alert('jumlah yang anda input melebihi total yang ada')
                    }             
                }
            })    
        }
        
    }
    
}
function refreshWorker(){
    worker_list.clear().draw(false);
    $.ajax({
            type: "GET",
            url: "{{ URL::to('material_request/list_track_frame_dt') }}" + "/" + id, //json get site
            dataType : 'json',
            success: function(response){
                arrData2 = response['worker'];
                workerList=arrData2;
                var a=1;
                for(i = 0; i < arrData2.length; i++){
                    worker_list.row.add([
                        '<div class="text-center">'+a+'</div>',
                        '<div class="text-center">'+arrData2[i]['name_worker']+'</div>',
                    ]).draw(false);
                    a++;
                }
            }
    });
}
function refreshMaterialWorker(){
    material_worker_list.clear().draw(false);
    $.ajax({
            type: "GET",
            url: "{{ URL::to('material_request/get_item_frame_material') }}" + "/" + id, //json get site
            dataType : 'json',
            success: function(response){
                arrData2 = response['data'];
                workerMaterialList=arrData2;
                var a=1;
                for(i = 0; i < arrData2.length; i++){
                    material_worker_list.row.add([
                        '<div class="text-center">'+a+'</div>',
                        '<div class="text-center">'+arrData2[i]['name_worker']+'</div>',
                        '<div class="text-center">'+arrData2[i]['m_items']['name']+'</div>',
                        '<div class="text-center">'+arrData2[i]['amount']+'</div>',
                    ]).draw(false);
                    a++;
                }
            }
    });
}
function editWork(eq){
    $('#detail_work > tbody').empty();
    var id=$(eq).data('id');
    var product_sub_id=$(eq).data('product_sub_id');
    var install_order_id=$(eq).data('install_order_id');
    $.ajax({
        type: "POST",
        url: "{{ URL::to('material_request/get_worksub') }}", //json get site
        dataType : 'json',
        data : {
            '_token' : '{{csrf_token()}}',
            'id'  : id,
            'install_order_id'  : install_order_id,
            'product_sub_id'    : product_sub_id,
            'dev_frame_id'      : {{$dev_frames->id}}
        },
        success: function(response){
            arrData=response['data'];
            for(i = 0; i < arrData.length; i++){
                var tdAdd='<tr>'+
                    '<td>'+arrData[i]['name']+'</td>'+
                    '<td><input type="checkbox" name="work_id[]" id="work_id[]" data-dev_frame_id="{{$dev_frames->id}}" data-worksub_id="'+arrData[i]['worksub_id']+'" data-product_sub_id="'+product_sub_id+'" data-product_id="'+arrData[i]['product_id']+'" value="1" '+(arrData[i]['cek'] == 1 ? 'checked' : '')+' onclick="saveWorksub(this)"></td>'+
                '</tr>';
                $('#detail_work').find('tbody:last').append(tdAdd);
            }
        }
    });
}
function saveWorksub(eq){
    var product_sub_id=$(eq).data('product_sub_id');
    var product_id=$(eq).data('product_id');
    var worksub_id=$(eq).data('worksub_id');
    var dev_frame_id=$(eq).data('dev_frame_id');
    var status=0;
    if($(eq).prop('checked') == true){
        status=1;
    }else{
        status=0;
    }
    $.ajax({
        type: "POST",
        url: "{{ URL::to('material_request/save_worksub') }}", //json get site
        data : {
            '_token' : '{{csrf_token()}}',
            'dev_frame_id' : dev_frame_id,
            'product_sub_id'    : product_sub_id,
            'product_id'    : product_id,
            'worksub_id'    : worksub_id,
            'status'    : status
        },
        dataType : 'json',
        success: function(response){
            arrData=response['data'];
            
        }
    });
}
</script>
@endsection
