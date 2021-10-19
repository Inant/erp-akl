@extends('theme.default')

@section('breadcrumb')
    <div class="page-breadcrumb">
        <div class="row">
            <div class="col-5 align-self-center">
                <h4 class="page-title">Tambah Installasi Order</h4>
                <div class="d-flex align-items-center">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ URL::to('order') }}">Order</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Tambah Installasi</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>
@endsection


@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Tambah Installasi Order</h4>
                    <form method="POST" action="{{ URL::to('order/save_order_install') }}" class="form-horizontal">
                        @csrf
                        <div class="form-group row">
                            <label class="col-sm-3 text-right control-label col-form-label">Order No</label>
                            <div class="col-sm-9">
                                <select name="order_id"  onchange="getDetailOrder(this.value);" class="form-control select2 custom-select" style="width: 100%; height:32px;">
                                    <option value="">--- Select Order No ---</option>
                                    @if($order_list != null)
                                    @foreach($order_list as $value)
                                        @if($value['is_done'] != 1)
                                        <option value="{{ $value['id'] }}">{{ $value['order_no'] }} | {{ $value['spk_number'] }}</option>
                                        @endif
                                    @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-3 text-right control-label col-form-label">No SPK</label>
                            <div class="col-sm-9">
                                <input type="text" name="spk_no" required class="form-control" id="spk_no">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-3 text-right control-label col-form-label">Catatan</label>
                            <div class="col-sm-9">
                                <textarea name="notes" id="notes" required class="form-control"></textarea>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <h4 class="card-title">Detail Order</h4>
                            <table class="table table-bordered table-striped" id="detail-order">
                                <thead>
                                    <tr>
                                        <th>Item</th>
                                        <th>Type Kavling</th>
                                        <th>Deskripsi</th>
                                        <th>Series</th>
                                        <th>Jasa Instalasi</th>
                                        <th>Total Set</th>
                                        <th width="150px">Total</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>    
                                <tbody>
                                    
                                </tbody>
                            </table>
                        </div>
                        <div class="form-group">
                            <button type="submit" id="submit" disabled class="btn btn-primary">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        
        </div>
    </div>
</div>
<!-- modal -->
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
                <input type="hidden" name="order_d_id" id="order_d_id">
                Tambah Pekerjaan
                <button class="btn btn-primary btn-sm float-right" type="button" onclick="addRow()"><i class="mdi mdi-plus"></i></button>
                <br>
                <table id="detail_work" style="width:100%">
                    <tbody>
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger btn-sm waves-effect" data-dismiss="modal">Close</button>
                <button class="btn btn-info btn-sm waves-effect" id="submit">Simpan</button>
            </div>
            </form>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<!-- /.modal -->
<script src="{!! asset('theme/assets/libs/jquery/dist/jquery.min.js') !!}"></script>
<script src="{!! asset('theme/assets/libs/sweetalert2/dist/sweetalert2.all.min.js') !!}"></script>
<script>
$("form#addWorksub").on("submit", function( event ) {
    var form = $('#addWorksub')[0];
    var data = new FormData(form);
    event.preventDefault();
    // console.log( $('form#addKavling').serialize() );
    $.ajax({
        type: "POST",
        url: "{{ URL::to('order/save_work_temp') }}", //json get site
        dataType : 'json',
        data: data,
        processData: false,
        contentType: false,
        success: function(response){
            alert(response['message']);
        }
    });
    $('#modalWorkSub').modal('hide');
});
var worksubs=[];
$(document).ready(function(){
    $.ajax({
        type: "GET",
        url: "{{ URL::to('rab/get_worksubs') }}", //json get site
        dataType : 'json',
        success: function(response){
            arrData = response['data'];
            worksubs = arrData;
        }
    });
})
function getDetailOrder(id){
    total_product=0;
    $('#detail-order > tbody').empty();
    $.ajax({
        type: "GET",
        url: "{{ URL::to('order/detail_order') }}"+'/'+id, //json get site
        dataType : 'json',
        success: function(response){
            arrData=response['data'];
            for(i = 0; i < arrData.length; i++){
                addDetailOrder(arrData[i]['product_id'], arrData[i]['id']);
            }
        }
    });
}
var total_produk=0;
function addDetailOrder(id, order_d_id){
    var data=[];
    var detail_product=$('#detail-order');
    $.ajax({
        url : "{{ URL::to('order/get-product') }}"+'/'+id,
        type : 'GET',
        dataType : 'json',
        async : false,
        success : function(response){
            total_produk++;
            var tdAdd='<tr>'+
                            '<td><p id="item">'+response['item']+'</p><input type="" name="item[]" class="form-control" style="display:none" id="inputItem" value="'+response['item']+'"><input type="hidden" name="id[]" value="'+order_d_id+'">'+
                            '</td>'+
                            '<td><input type="hidden" name="id_product[]" value="'+response['id']+'"><p id="">'+response['type_kavling']+'</p><input type="text" style="display:none" id="" class="form-control" value="'+response['name']+'" name="name[]"></td>'+
                            '<td><p id="descriptions">'+response['description']+'</p><textarea name="deskripsi[]" class="form-control" style="display:none" id="inputDesc" onkeyup="changeDesc(this)">'+response['description']+'</textarea></td>'+
                            '<td><p id="series">'+response['series']+'</p><input type="" name="series[]" class="form-control" style="display:none" id="inputSeries" onkeyup="changeSeries(this)" value="'+response['series']+'"></td>'+
                            '<td><p id="fee_installation" hidden>'+(response['installation_fee'])+'</p><input type="number" name="fee_install[]" class="form-control" id="inputFeeInstall" value="'+response['installation_fee']+'"></td>'+
                            '<td><p id="total_set">'+response['amount_set']+'</p><input type="number" name="set[]" class="form-control" style="display:none" id="inputSet" value="'+response['amount_set']+'"></td>'+
                            '<td><input type="number" required class="form-control" placeholder="Total" name="total_produk[]" value="'+response['amount']+'"></td>'+
                            '<td><button type="button" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#modalWorkSub" onclick="editWork(this)" data-id="'+response['id']+'"  data-order_d_id="'+order_d_id+'"><i class="mdi mdi-plus"></i></button>&nbsp;<button type="button" class="btn btn-sm btn-danger removeOption"><i class="mdi mdi-delete"></i></button></td>'+
                        '</tr>';
            $('#detail-order').find('tbody:last').append(tdAdd);
            // $.ajax({
            //     type: "GET",
            //     url: "{{ URL::to('order/get_work_install') }}"+'/'+order_d_id, //json get site
            //     dataType : 'json',
            //     success: function(response){
            //         arrData=response['data'];
            //         for(i = 0; i < arrData.length; i++){
            //             var tdAdd='<tr>'+
            //                             '<td>'+arrData[i]['name']+
            //                             '</td>'+
            //                             '<td>'+arrData[i]['price']+'</td>'+
            //                             '<td></td>'+
            //                             '<td></td>'+
            //                             '<td></td>'+
            //                             '<td></td>'+
            //                             '<td></td>'+
            //                             '<td></td>'+
            //                         '</tr>';
            //             $('#detail-order').find('tbody:last').append(tdAdd);
            //         }
            //     }
            // });
        }
    })
    cekTotalProduk();
    // console.log(total_produk);
}
function editWork(eq){
    $('#detail_work > tbody').empty();
    var order_d_id=$(eq).data('order_d_id');
    $('#order_d_id').val(order_d_id);
    $.ajax({
        type: "GET",
        url: "{{ URL::to('order/get_work_install') }}"+'/'+order_d_id, //json get site
        dataType : 'json',
        success: function(response){
            arrData=response['data'];
            for(i = 0; i < arrData.length; i++){
                addRow(arrData[i]['worksub_id'], arrData[i]['price']);
            }
        }
    });
}
function addRow(id, price){
    var option_worksubs='<option value="">Pilih Pekerjaan</option>';
    
    for (var i = 0; i < worksubs.length; i++) {
        option_worksubs+='<option value="'+worksubs[i]['id']+'"  '+(worksubs[i]['id'] == id ? 'selected' : '')+'>'+worksubs[i]['name']+'</option>';
    }
    var tdAdd='<tr>'+
                    '<td><select name="work_id[]" class="form-control select2 custom-select" style="width: 100%; height:32px;">'+option_worksubs+              
                    '</select></td>'+
                    '<td style="width:40%"><input name="price[]" class="form-control" placeholder="total" value="'+(price == null ? 0 : price)+'"></td>'+
                    '<td></button><button type="button" class="btn btn-sm btn-danger float-right removeOption"><i class="mdi mdi-delete"></i></button></td>'+
                '</tr>';
    $('#detail_work').find('tbody:last').append(tdAdd);
    $('.select2').select2();
}
$("#detail_work").on("click", ".removeOption", function(event) {
    event.preventDefault();
    $(this).closest("tr").remove();
});
function formatNumber(angka, prefix)
{
    var number_string = angka.replace(/[^,\d]/g, '').toString(),
        split = number_string.split(','),
        sisa  = split[0].length % 3,
        rupiah  = split[0].substr(0, sisa),
        ribuan  = split[0].substr(sisa).match(/\d{3}/gi);
        
    if (ribuan) {
        separator = sisa ? '.' : '';
        rupiah += separator + ribuan.join('.');
    }

    rupiah = split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
    return prefix == undefined ? rupiah : (rupiah ? 'Rp. ' + rupiah : '');
}
function cekTotalProduk(){
    if(total_produk < 1){
        $('#submit').attr('disabled', true);
    }else{
        $('#submit').attr('disabled', false);
    }
}
$("#detail-order").on("click", ".removeOption", function(event) {
    event.preventDefault();
    $(this).closest("tr").remove();
    total_produk--;
    cekTotalProduk();
});
</script>
@endsection