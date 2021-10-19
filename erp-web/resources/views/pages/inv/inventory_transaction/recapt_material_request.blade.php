@extends('theme.default')

@section('breadcrumb')
    <div class="page-breadcrumb">
        <div class="row">
            <div class="col-5 align-self-center">
                <h4 class="page-title">Rekap Kebutuhan Material</h4>
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
                    <h4 class="card-title">Rekap Kebutuhan Material</h4>
                        <div class="row">
                            <div class="col-md-4 col-sm-12">
                                <label>Order No</label>
                                <select name="order_id"  onchange="getKavling(this.value);" class="form-control select2 custom-select" style="width: 100%; height:32px;">
                                    <option value="">--- Select Order No ---</option>
                                    @if($order_list != null)
                                    @foreach($order_list as $value)
                                        <option value="{{ $value['id'] }}">{{ $value['order_no'] }} | {{ $value['spk_number'] }}</option>
                                    @endforeach
                                    @endif
                                </select>
                            </div>
                            <div class="col-md-4 col-sm-12">
                                <label>Kavling</label>
                                    <select name="kavling_id" id="kavling_id" onchange="getReport(this.value)" class="form-control select2 custom-select" style="width: 100%; height:32px;">
                                </select>
                            </div>
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
function getKavling(id){
    // $('#detail-order').find('tbody:last').append(tdAdd);
    $('#kavling_id').empty();
    $.ajax({
        type: "GET",
        url: "{{ URL::to('inventory/get_kavling/') }}"+'/'+id, //json get site
        dataType : 'json',
        success: function(response){
            arrData = response;
            $('#kavling_id').append('<option value="">Pilih Kavling</option>');
            for (var i = 0; i < arrData.length; i++) {
                $('#kavling_id').append('<option value="'+arrData[i]['id']+'">'+arrData[i]['name']+' | '+arrData[i]['no']+'</option>');
            }
        }
    });
}
function getReport(id){
    // $('#detail-order').find('tbody:last').append(tdAdd);
    $.ajax({
        type: "GET",
        url: "{{ URL::to('inventory/get_material_req/') }}"+'/'+id, //json get site
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