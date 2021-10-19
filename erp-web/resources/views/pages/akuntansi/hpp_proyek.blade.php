@extends('theme.default')

@section('breadcrumb')
    <div class="page-breadcrumb">
        <div class="row">
            <div class="col-5 align-self-center">
                <h4 class="page-title">HPP Proyek</h4>
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
                    <!-- <form id="reportHpp" action="javascript:;" accept-charset="utf-8" method="post" enctype="multipart/form-data"> -->
                        
                    <h4 class="card-title">HPP Proyek</h4>
                        <div class="row">
                            <!-- <div class="col-sm-4">
                                <label>Nama Proyek</label>
                                    <select id="customer_project_id" name="customer_project_id" required class="form-control select2 custom-select" style="width: 100%; height:32px;" onchange="getOrder(this.value)">
                                        <option value="">--- Pilih Proyek ---</option>
                                    </select>
                            </div> -->
                            <div class="col-sm-4">
                                <label>No SPK</label>
                                <select name="spk_number" id="spk_number"  onchange="getOrderNoBySpk()"  class="form-control select2 custom-select" style="width: 100%; height:32px;">
                                    <option value="">-- Pilih No SPK --</option>
                                    @foreach($spk_list as $value)
                                        @php $val = str_replace("/","|",$value['spk_number']) @endphp
                                        <option value="{{ $val }}">{{ $value['spk_number'] }}</option>
                                    @endforeach
                                    
                                </select>
                            </div>
                            <div class="col-sm-4">
                                <label>Order No</label>
                                <select name="order_id" id="order_id"  class="form-control select2 custom-select" style="width: 100%; height:32px;">
                                </select>
                            </div>
                            <div class="col-sm-2">
                                <label class="text-white">-</label><br>
                                <button class="btn btn-primary" onclick="getReport()"><i class="mdi mdi-eye"></i></button>
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
                    <!-- </form> -->
                </div>
            </div>
        </div>
    </div>                
</div>
<script src="{!! asset('theme/assets/libs/jquery/dist/jquery.min.js') !!}"></script>
<script src="{!! asset('theme/assets/extra-libs/DataTables/datatables.min.js') !!}"></script>
<script>
$(document).ready(function(){
    // $('#customer_project_id').empty();
    // $('#customer_project_id').append('<option value="">-- Pilih Proyek --</option>');
    // $.ajax({
    //     type: "GET",
    //     url: "{{ URL::to('master_kavling/get_project_cust') }}", //json get site
    //     dataType : 'json',
    //     success: function(response){
    //         arrData=response['data'];
    //         for(i = 0; i < arrData.length; i++){
    //             $('#customer_project_id').append('<option value="'+arrData[i]['id']+'">'+arrData[i]['name']+'</option>');
    //         }
    //     }
    // });
    // var id = $('[id^=order_id]').val();
    // getReport(id)
});
function getOrder(id){
    formOrder = $('[id^=order_id]');
    formOrder.empty();
    formOrder.append('<option value="">-- Pilih Nomor Order --</option>');
    $.ajax({
        type: "GET",
        url: "{{ URL::to('order/get_cust_project_order/') }}"+'/'+id, //json get site
        dataType : 'json',
        success: function(response){
            arrData = response['data'];
            for(i = 0; i < arrData.length; i++){
                formOrder.append('<option value="'+arrData[i]['id']+'">'+arrData[i]['order_no']+'</option>');
            }
        }
    });
}
var temp_spk_number=[];
function getOrderNoBySpk(){
    var spk_number=$('[name^=spk_number]');
    var formOrderNo = $('[name^=order_id]');
    // var checkListProd = $('[name^=check_production]');
    var index_id= $('[id^=index]');
    for(i = 0; i < spk_number.length; i++){
        var spk=spk_number.eq(i).val();
        var index=index_id.eq(i).data('index');
        
        if (spk != temp_spk_number[index]) {   
            $.ajax({
                type: "GET",
                url: "{{ URL::to('home/get_order_no/') }}"+'/'+spk, //json get site
                dataType : 'json',
                async : false,
                success: function(response){
                    arrData = response['data'];
                    formOrderNo.eq(i).empty();
                    formOrderNo.eq(i).append('<option value="">-- Pilih Nomor Order --</option>');
                    for(var j = 0; j < arrData.length; j++){
                        formOrderNo.eq(i).append('<option value="'+arrData[j]['id']+'">'+arrData[j]['order_no']+'</option>');
                    }
                }
            });
            // checkListProd.eq(i).val(id);
            temp_spk_number[index]=spk;
        }
    }
}
function getReport(id){
    // $('#detail-order').find('tbody:last').append(tdAdd);
    id=$('[id^=order_id]').val();
    $.ajax({
        type: "GET",
        url: "{{ URL::to('akuntansi/report_hpp_proyek/') }}"+'/'+id, //json get site
        dataType : 'json',
        success: function(response){
            arrData = response['data'];
            $('#detail').html(response['html_content']);
        },error : function(){
            $('#detail').html('');
        }
    });
}
$("form#reportHpp").on("submit", function( event ) {
        var form = $('#reportHpp')[0];
        var data = new FormData(form);
        event.preventDefault();
        console.log( $('form#reportHpp').serialize() );
        $.ajax({
            type: "POST",
            url: "{{ URL::to('akuntansi/report_hpp_proyek') }}", //json get site
            dataType : 'json',
            data: data,
            processData: false,
            contentType: false,
            success: function(response){
                arrData = response['data'];
                $('#detail').html(response['html_content']);
            }
        });
        // $('#myModal').modal('hide');
    });
</script>
@endsection