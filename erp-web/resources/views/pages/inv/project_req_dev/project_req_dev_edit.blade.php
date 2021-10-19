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
<form method="POST" action="{{ URL::to('project_req_dev/update') }}" class="form-horizontal">
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
                        <input type="hidden" value="{{$detail['id']}}" name="id">
                        <div class="form-group row">
                            <label class="col-sm-3 text-right control-label col-form-label">Order No</label>
                            <div class="col-sm-9">
                                <select name="order_id"  onchange="getOrderNo(this.value);" class="form-control custom-select" style="width: 100%; height:32px;" id="order_id">
                                    <option value="">--- Select Order No ---</option>
                                    @if($order_list != null)
                                    @foreach($order_list as $value)
                                        <option value="{{ $value['id'] }}" {{$value['id'] == $detail['order_id'] ? 'selected' : ''}}>{{ $value['order_no'] }}</option>
                                    @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>
                        
                        <div class="form-group row">
                            <label class="col-sm-3 text-right control-label col-form-label">RAB Number</label>
                            <div class="col-sm-9">
                                <select id="rab_no" name="rab_no" required class="form-control custom-select" style="width: 100%; height:32px;">
                                    <option value="">--- Select RAB Number ---</option>
                                    <option value="{{ $rab['id'] }}" {{$rab['id'] == $detail['rab_id'] ? 'selected' : ''}}>{{ $rab['no'] }}</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-3 text-right control-label col-form-label">Total Request</label>
                            <div class="col-sm-9">
                            <input type="" name="total_order" class="form-control" id="total_order" value="{{$detail['total']}}">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-3 text-right control-label col-form-label">Tanggal Permintaan</label>
                            <div class="col-sm-9">
                            <input type="date" name="request_date" required id="estimate_end" class="form-control"  value="{{date('Y-m-d', strtotime($detail['request_date']))}}">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-3 text-right control-label col-form-label">Deadline Pengerjaan</label>
                            <div class="col-sm-9">
                            <input type="date" name="estimate_end" required id="estimate_end" class="form-control"  value="{{date('Y-m-d', strtotime($detail['finish_date']))}}">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-3 text-right control-label col-form-label">Catatan</label>
                            <div class="col-sm-9">
                            <textarea name="note" class="form-control" id="" cols="30" rows="5">{{$detail['note']}}</textarea>
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
    $('#order_id').attr("style", "pointer-events: none;");
    $('#rab_no').attr("style", "pointer-events: none;");
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
</script>
@endsection