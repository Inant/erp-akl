@extends('theme.default')

@section('breadcrumb')
    <div class="page-breadcrumb">
        <div class="row">
            <div class="col-5 align-self-center">
                <h4 class="page-title">Laporan Produksi</h4>
                <div class="d-flex align-items-center">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item active" aria-current="page"></li>
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
                    <div class="col-12">
                    <h4 class="card-title">Laporan Produksi</h4>
                        <div class="row">
                            <div class="col-sm-4">
                                <label>Order No</label>
                                <select name="order_id"  onchange="getReport(this.value);" class="form-control select2 custom-select" style="width: 100%; height:32px;">
                                    <option value="">--- Select Order No ---</option>
                                    @if($order_list != null)
                                    @foreach($order_list as $value)
                                        <option value="{{ $value['id'] }}">{{ $value['order_no'] }}</option>
                                    @endforeach
                                    @endif
                                </select>
                            </div>
                            <!-- <div class="col-sm-4">
                                <label>Nomor Permintaan</label>
                                <select name="req_id" id="req_id" onchange="getReport(this.value)" class="form-control select2 custom-select" style="width: 100%; height:32px;">
                                </select>
                            </div> -->
                        </div>
                        <br><br>
                        <div id="detail"></div>
                        <!-- <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Pekerjaan</th>
                                    </tr>
                                </thead>
                            </table>
                        </div> -->
                    </div>
                </div>
            </div>
        </div>
    </div>                
</div>
<script src="{!! asset('theme/assets/libs/jquery/dist/jquery.min.js') !!}"></script>
<script src="{!! asset('theme/assets/extra-libs/DataTables/datatables.min.js') !!}"></script>
<script>
function getOrderNo(order_id){
    formReqNo = $('[id^=req_id]');
    formReqNo.empty();
    formReqNo.append('<option value="">-- Pilih Nomor Permintaan --</option>');
    $.ajax({
        type: "GET",
        url: "{{ URL::to('home/get_req_no/') }}"+'/'+order_id, //json get site
        dataType : 'json',
        success: function(response){
            arrData = response['data'];
            for(i = 0; i < arrData.length; i++){
                formReqNo.append('<option value="'+arrData[i]['id']+'">'+arrData[i]['no']+'</option>');
            }
        }
    });
    $('#detail').html('');
}
function getReport(id){
    // $('#detail-order').find('tbody:last').append(tdAdd);
    $.ajax({
        type: "GET",
        url: "{{ URL::to('home/get_estimate_result/') }}"+'/'+id, //json get site
        dataType : 'json',
        success: function(response){
            arrData = response['data'];
            $('#detail').html(response['html_content']);
        },error : function(){
            $('#detail').html('');
        }
    });
}
</script>
@endsection