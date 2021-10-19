@extends('theme.default')

@section('breadcrumb')
    <div class="page-breadcrumb">
        <div class="row">
            <div class="col-5 align-self-center">
                <h4 class="page-title">Tambah Permintaan Pengerjaan Project</h4>
                <div class="d-flex align-items-center">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ URL::to('order') }}">Permintaan Pengerjaan Project</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Tambah</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>
@endsection


@section('content')
<form method="POST" action="{{ URL::to('project_req_dev/save') }}" class="form-horizontal">
    @csrf
    <div class="container-fluid">
        <!-- basic table -->
        <br><br>
        <div class="row">
            @if($error['is_error'])
            <div class="col-12">
                <div class="alert alert-danger"> <i class="mdi mdi-alert-box"></i> {{ $error['error_message'] }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"> <span aria-hidden="true">×</span> </button>
                </div>
            </div>
            @endif
            <br>
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Request Header</h4>
                        
                        <div class="form-group row">
                            <label class="col-sm-3 text-right control-label col-form-label">Order No</label>
                            <div class="col-sm-9">
                                <select name="order_id"  onchange="getOrderNo(this.value);" class="form-control select2 custom-select" style="width: 100%; height:32px;">
                                    <option value="">--- Select Order No ---</option>
                                    @if($order_list != null)
                                    @foreach($order_list as $value)
                                        @if($value['is_done'] != 1)
                                        <option value="{{ $value['id'] }}">{{ $value['order_no'].' | '.$value['spk_number'] }}</option>
                                        @endif
                                    @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>
                        
                        <div class="form-group row">
                            <label class="col-sm-3 text-right control-label col-form-label">RAB Number</label>
                            <div class="col-sm-9">
                                <select id="rab_no" name="rab_no" required class="form-control select2 custom-select" style="width: 100%; height:32px;" onchange="getRab(this)">
                                    <option value="">--- Select RAB Number ---</option>
                                </select>
                            </div>
                        </div>
                        <input type="hidden" id="total_use">
                        <div class="form-group row">
                            <label class="col-sm-3 text-right control-label col-form-label">Total Request</label>
                            <div class="col-sm-9">
                            <input type="" name="total_order" class="form-control" id="total_order" onchange="cekTotal(this)">                         
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-3 text-right control-label col-form-label">Tanggal Permintaan</label>
                            <div class="col-sm-9">
                            <input type="date" name="request_date" required id="estimate_end" class="form-control" value="{{ date('Y-m-d') }}">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-3 text-right control-label col-form-label">Tanggal Perkiraan Pekerjaan Mulai</label>
                            <div class="col-sm-9">
                            <input type="date" name="work_start" required id="work_start" class="form-control" value="{{ date('Y-m-d') }}">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-3 text-right control-label col-form-label">Deadline Pengerjaan</label>
                            <div class="col-sm-9">
                            <input type="date" name="estimate_end" required id="estimate_end" class="form-control">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-3 text-right control-label col-form-label">Catatan</label>
                            <div class="col-sm-9">
                            <textarea name="note" class="form-control" id="" cols="30" rows="5"></textarea>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-3 text-right control-label col-form-label"></label>
                            <div class="col-sm-9">
                            <button class="btn btn-success">Simpan</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>           
    </div>  
    <div style="height:100px"></div>
</form>

<script src="{!! asset('theme/assets/libs/jquery/dist/jquery.min.js') !!}"></script>
<script src="{!! asset('theme/assets/extra-libs/DataTables/datatables.min.js') !!}"></script>
<script>

$(document).ready(function(){
});

function getRab(project_id){
    formRabNo = $('[id^=rab_no]');
    formRabNo.empty();
    formRabNo.append('<option value="">-- Select RAB Number --</option>');
    $.ajax({
        type: "GET",
        url: "{{ URL::to('rab/get_rab_by_project_id') }}", //json get site
        dataType : 'json',
        data:"project_id=" + project_id,
        success: function(response){
            arrData = response['data'];
            for(i = 0; i < arrData.length; i++){
                formRabNo.append('<option value="'+arrData[i]['rab_id']+'">'+arrData[i]['rab_no']+'</option>');
            }
        }
    });
    document.getElementById("btnSubmit").disabled = true;
}

function getOrderNo(order_id){
    formRabNo = $('[id^=rab_no]');
    formRabNo.empty();
    formRabNo.append('<option value="">-- Select RAB Number --</option>');
    $.ajax({
        type: "GET",
        url: "{{ URL::to('rab/get_rab_by_order_id') }}", //json get site
        dataType : 'json',
        data:"order_id=" + order_id,
        success: function(response){
            arrData = response['data'];
            for(i = 0; i < arrData.length; i++){
                formRabNo.append('<option value="'+arrData[i]['id']+'">'+arrData[i]['no']+'</option>');
            }
        }
    });
}
function getRab(obj){
    $.ajax({
        type: "GET",
        url: "{{ URL::to('project_req_dev/getRab/') }}"+'/'+obj.value, //json get site
        dataType : 'json',
        success: function(response){
            arrData=response['data'];
            total_use=parseFloat(arrData['total']) - parseFloat(arrData['use']);
            $('#total_use').val(total_use);
            $('#total_order').val(total_use);
        }
    });
}
function cekTotal(obj){
    var total=$('#total_use').val();
    if(parseFloat(obj.value) > parseFloat(total)){
        alert('tidak boleh melebihi dari '+total);
        obj.value=0;
    }
}
</script>
@endsection