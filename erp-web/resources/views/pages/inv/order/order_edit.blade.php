@extends('theme.default')

@section('breadcrumb')
    <div class="page-breadcrumb">
        <div class="row">
            <div class="col-5 align-self-center">
                <h4 class="page-title">Edit Order</h4>
                <div class="d-flex align-items-center">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ URL::to('order') }}">Order</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Edit</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>
@endsection


@section('content')

<style>
.delete{
    background-color:#a57575;
    color:white
}
.checkbox label:after,
.radio label:after {
  content: '';
  display: table;
  clear: both;
}

.checkbox .cr,
.radio .cr {
  position: relative;
  display: inline-block;
  border: 1px solid #a9a9a9;
  border-radius: .25em;
  width: 1.3em;
  height: 1.3em;
  float: left;
  margin-right: .5em;
}

.radio .cr {
  border-radius: 50%;
}

.checkbox .cr .cr-icon,
.radio .cr .cr-icon {
  position: absolute;
  font-size: .8em;
  line-height: 0;
  top: 50%;
  left: 15%;
}

.radio .cr .cr-icon {
  margin-left: 0.04em;
}

.checkbox label input[type="checkbox"],
.radio label input[type="radio"] {
  display: none;
}

.checkbox label input[type="checkbox"]+.cr>.cr-icon,
.radio label input[type="radio"]+.cr>.cr-icon {
  opacity: 0;
}

.checkbox label input[type="checkbox"]:checked+.cr>.cr-icon,
.radio label input[type="radio"]:checked+.cr>.cr-icon {
  opacity: 1;
}

.checkbox label input[type="checkbox"]:disabled+.cr,
.radio label input[type="radio"]:disabled+.cr {
  opacity: .5;
}
</style>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Edit Order</h4>
                    <form method="POST" action="{{ URL::to('order/update') }}" class="mt-4" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="dari" value="{{$_GET['dari']}}">
                        <input type="hidden" name="sampai" value="{{$_GET['sampai']}}">
                        <div class="row">
                        <div class="col-sm-6">
                                <div class="form-group">
                                    <label>Customer</label>
                                    <input type="hidden" name="order_id" value="{{$order['id']}}">
                                    <select id="customer_id" name="customer_id" required class="form-control select2 custom-select" style="width: 100%; height:32px;">
                                        <option value="">--- Pilih Customer ---</option>
                                        @foreach($customer as $value)
                                        <option value="{{$value['id']}}" {{ $order['customer_id'] == $value['id'] ? 'selected' : '' }}>{{$value['coorporate_name']}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-5 col-sm-12">
                                <div class="form-group">
                                    <label>Nama Proyek</label>
                                    <select id="customer_project_id" name="customer_project_id" required class="form-control select2 custom-select" style="width: 100%; height:32px;">
                                        <option value="">--- Pilih Proyek ---</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label>Deskripsi Order</label>
                                    <textarea class="form-control" id="editor1" rows="3" cols="10" name="order_name" required>{{$order['order_name']}}</textarea>
                                </div>
                            </div>
                            <div class="col-sm-6" hidden>
                                <div class="form-group">
                                    <label>Pilih Produk</label>
                                    <select id="select_product" class="form-control select2 custom-select" style="width: 100%; height:32px;" onchange="addProduct(this.value)">
                                        <option value="">--- Pilih Produk ---</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-6" hidden>
                                <div class="form-group">
                                    <label>Pilih Produk</label>
                                    <div class="input-group">
                                    
                                    <input type="text" id='product_search' class="form-control" placeholder="Cari Produk" aria-label="" aria-describedby="basic-addon1">
                                    <div class="input-group-append">
                                        <button class="btn btn-success" type="button" data-toggle="modal" data-target="#myModal" title="Tambah Produk"><i class="mdi mdi-plus-circle"></i></button>
                                    </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <br>
                        <div class="table-responsive" id="detailOrder">
                            <h4 class="card-title">Detail Order</h4>
                            <div class="form-group">
                                <button class="btn btn-info" type="button" onclick="listItem()">List Item</button>
                            </div>
                            <table class="table table-bordered table-striped" id="detail-order">
                                <thead>
                                    <tr>
                                        <th>Item</th>
                                        <th></th>
                                        <th>Type Kavling</th>
                                        <th>Deskripsi</th>
                                        <th>Series</th>
                                        <th colspan="2">Dimensi (W(m<sup>1</sup>) x H(m<sup>1</sup>))</th>
                                        <th>Harga</th>
                                        <th>Jasa Instalasi</th>
                                        <th>Total Item</th>
                                        <th width="150px">Total Kavling</th>
                                        <!-- <th>Edit Sebagai Produk Baru</th> -->
                                        <th>Action</th>
                                    </tr>
                                </thead>    
                                <tbody>
                                    @foreach ($order_d as $detailItem)
                                        <tr>
                                            <td>{{$detailItem['item']}}</td>
                                            <td>{{$detailItem['name']}}</td>
                                            <td>{{$detailItem['type_kavling']}}</td>
                                            <td>{{$detailItem['description']}}</td>
                                            <td>{{$detailItem['series']}}</td>
                                            <td>{{$detailItem['panjang']}}</td>
                                            <td>{{$detailItem['lebar']}}</td>
                                            <td>{{number_format($detailItem['price'], 0, ',', '.')}}</td>
                                            <td>{{number_format($detailItem['installation_fee'], 0, ',', '.')}}</td>
                                            <td>{{number_format($detailItem['total'], 0, ',', '.')}}</td>
                                            <td>{{number_format($detailItem['amount_set'], 0, ',', '.')}}</td>
                                            <td><button type="button" class="editDetailOrder btn btn-sm btn-success" data-param="{{$detailItem['id_detail']}}|{{$detailItem['name']}}|{{$detailItem['description']}}|{{$detailItem['item']}}|{{$detailItem['type_kavling']}}|{{$detailItem['panjang']}}|{{$detailItem['lebar']}}|{{$detailItem['price']}}|{{$detailItem['total']}}" ><i class="mdi mdi-pencil"></i></button></td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <button type="submit" id="submit" class="btn btn-primary">Submit</button>
                    </form>
                </div>
            </div>
        
        </div>
    </div>
</div>
<div id="myModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel">Add Product</h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <form id="addProduct" action="javascript:;" accept-charset="utf-8" method="post" enctype="multipart/form-data">
            <div class="modal-body">
                @csrf
                <div class="form-group">
                    <label>Nama</label>
                    <input type="text" class="form-control" name="name" required/>
                </div>
                <div class="form-group">
                    <label>Deskripsi</label>
                    <textarea class="form-control" name="description" required></textarea>
                </div>
                <div class="form-group">
                    <label>Satuan</label>
                    <select id="satuan" name="m_unit_id" required class="form-control select2 custom-select" style="width: 100%; height:32px;">
                        <option value="">--- Select Satuan ---</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Image Sketch</label>
                    <input type="file" class="form-control" name="image"  accept="image/*"/>
                </div>
                <div class="form-group">
                    <label>Price</label>
                    <input type="number" class="form-control" name="price"/>
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
<!-- /.modal -->
    
<div id="modalEdit" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel">Edit Product</h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <form id="editProduct" action="{{url('order/updateProduct')}}" method="post">
            <div class="modal-body">
                @csrf

                <input type="hidden" name="dari" value="{{$_GET['dari']}}">
                <input type="hidden" name="sampai" value="{{$_GET['sampai']}}">
                <input type="hidden" name="order_id" value="{{$order['id']}}">
                <input type="hidden" name="id_detail" id="id_detail_">
                <div class="form-group">
                    <label>Nama</label>
                    <input type="text" class="form-control" id="nama_" disabled/>
                </div>
                <div class="form-group">
                    <label>Deskripsi</label>
                    <textarea class="form-control" id="deskripsi_" disabled></textarea>
                </div>
                <div class="form-group">
                    <label>Item</label>
                    <input type="text" class="form-control" id="item_" disabled/>
                </div>
                <div class="form-group">
                    <label>Type Kavling</label>
                    <input type="text" class="form-control" id="type_kavling_" disabled/>
                </div>
                <div class="form-group">
                    <label>Dimensi</label>
                    <div class="row">
                        <div class="col-sm-6">
                            <label>Panjang W(m<sup>1</sup>)</label>
                            <input type="number" class="form-control" id="panjang_" disabled/>
                        </div>
                        <div class="col-sm-6">
                            <label>Tinggi H(m<sup>1</sup>)</label>
                            <input type="number" class="form-control" id="tinggi_" disabled/>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label>Harga</label>
                    <input type="number" class="form-control" name="price" id="price_"/>
                </div>
                <div class="form-group">
                    <label>Total Item</label>
                    <input type="number" class="form-control" name="total" id="total_"/>
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
<!-- /.modal -->
<!-- <script src="{!! asset('theme/assets/libs/jquery/dist/jquery.min.js') !!}"></script> -->
<script src="{!! asset('theme/assets/libs/sweetalert2/dist/sweetalert2.all.min.js') !!}"></script>
<script>
var url='{{URL::to('/')}}';
var total_produk={{count($order_d)}};
$(document).ready(function(){
    $('#customer_project_id').empty();
    $('#customer_project_id').append('<option value="">-- Pilih Proyek --</option>');
    var selectedProject = "{{$order['customer_project_id']}}";
    $.ajax({
        type: "GET",
        url: "{{ URL::to('master_kavling/get_project_cust') }}", //json get site
        dataType : 'json',
        success: function(response){
            arrData=response['data'];
            for(i = 0; i < arrData.length; i++){
                var selected = selectedProject==arrData[i]['id'] ? 'selected' : '';
                $('#customer_project_id').append('<option value="'+arrData[i]['id']+'" '+selected+'>'+arrData[i]['name']+'</option>');
            }
            console.log(selectedProject);
        }
    });
    formSatuan = $('[id^=satuan]');
    formSatuan.empty();
    formSatuan.append('<option value="">-- Select Satuan --</option>');
    $.ajax({
        type: "GET",
        url: "{{ URL::to('master_satuan/list') }}", //json get site
        dataType : 'json',
        success: function(response){
            arrData = response['data'];
            for(i = 0; i < arrData.length; i++){
                formSatuan.append('<option value="'+arrData[i]['id']+'">'+arrData[i]['name']+'</option>');
            }
        }
    });
    $(".editDetailOrder").click(function(){
        $("#modalEdit").modal('show')
        var data = $(this).data('param')
        data = data.split('|')
        $("#id_detail_").val(data[0])
        $("#nama_").val(data[1])
        $("#deskripsi_").val(data[2])
        $("#item_").val(data[3])
        $("#type_kavling_").val(data[4])
        $("#panjang_").val(data[5])
        $("#tinggi_").val(data[6])
        $("#price_").val(data[7])
        $("#total_").val(data[8])
    })
    $("form#addProduct").on("submit", function( event ) {
        var form = $('#addProduct')[0];
        var data = new FormData(form);
        event.preventDefault();
        // console.log( $('form#addProduct').serialize() );
        $.ajax({
            type: "POST",
            url: "{{ URL::to('master_product/create_json') }}", //json get site
            dataType : 'json',
            data: data,
            processData: false,
            contentType: false,
            success: function(response){
                $('#product_search').focus();
                $('#addProduct').trigger("reset");
                formSatuan.val('').change();
                alert(response['message']);
            }
        });
        $('#myModal').modal('hide');
    });
    // $('#select_product').select2({
    //     ajax : {
    //         url : '/order/suggest_product',
    //         delay: 200,
    //         dataType : 'json',
    //         processResults: function (data) {
    //             return {
    //             results:  $.map(data, function (item) {
    //                 return {
    //                 text: item.name,
    //                 id: item.id
    //                 }
    //             })
    //             };
    //         },
    //         cache: true
    //     }
    //     // placeholder: "Select Product",
    //     // initSelection: function(element, callback) {                   
    //     // }
    // });

      $( "#product_search" ).autocomplete({
        source: function( request, response ) {
          // Fetch data
          $.ajax({
            url:"{{ URL::to('order/fetch') }}",
            type: 'post',
            dataType: "json",
            data: {
               _token: '{{csrf_token()}}',
               search: request.term
            },
            success: function( data ) {
               response( data );
            }
          });
        },
        select: function (event, ui) {
           // Set selection
           $('#product_search').val(''); 
           addProductOrder(ui.item.value);
           $('#product_search').focus();
           return false;
        },
        minLength: 1
      });
    //   .data('ui-autocomplete')._renderItem = function(ul, item){
    //     return $("<li class='ui-autocomplete-row'></li>")
    //         .data("item.autocomplete", item)
    //         .append(item.label)
    //         .appendTo(ul);
    //     };
});
function addProduct(value){
    addProductOrder(value);
    $('#select_product').focus();
}
var wrapper_product_detail = $("#detail-order"); 
function addProductOrder(id){
    var data=[];
    var detail_product=$('#detail-order');
    $.ajax({
        url : '{{ URL::to('order/get-product') }}'+id,
        type : 'GET',
        dataType : 'json',
        async : false,
        success : function(response){
            total_produk++;
            var tdAdd='<tr>'+
                            '<td><input type="hidden" name="order_d_id[]" value="0"><input type="hidden" name="id_product[]" value="'+response['id']+'"><p id="name">'+response['name']+'</p><input type="text" style="display:none" id="inputName" class="form-control" value="'+response['name']+'" name="name[]"></td>'+
                            '<td><p id="descriptions">'+response['description']+'</p><textarea name="deskripsi[]" class="form-control" style="display:none" id="inputDesc" onkeyup="changeDesc(this)">'+response['description']+'</textarea></td>'+
                            '<td><input type="checkbox" id="editDesc" class="form-control" onclick="cekChange(this)" name="editProd[]" value="'+response['id']+'"></td>'+
                            '<td><img src="/upload/product/'+response['image']+'" width="60px" onclick="showImage(this.src)"><input type="file" id="file" style="display:none" name="file[]" accept="image/*" class="form-control"></td>'+
                            '<td><input type="number" required class="form-control" placeholder="Total" name="total_produk[]"></td>'+
                            '<td><button type="button" class="btn btn-sm btn-danger removeOption"><i class="mdi mdi-delete"></i></button></td>'+
                        '</tr>';
            $('#detail-order').find('tbody:last').append(tdAdd);
        }
    })
    cekTotalProduk();
    // console.log(total_produk);
}
$("#detail-order").on("click", ".removeOption", function(event) {
    event.preventDefault();
    $(this).closest("tr").remove();
    total_produk--;
    cekTotalProduk();
});
function showImage(src){
    swal({   
        imageUrl: src,
        showConfirmButton: false
    });
}
function cekTotalProduk(){
    if(total_produk < 1){
        $('#submit').attr('disabled', true);
    }else{
        $('#submit').attr('disabled', false);
    }
}
function changeDesc(value){
    // console.log(value);
    $(value).closest('tr').find('#description').html(value.value);
}
function cekChange(el){
    if($(el).is(':checked')){
        $(el).closest('tr').find('#inputDesc').show();
        $(el).closest('tr').find('#inputName').show();
        $(el).closest('tr').find('#file').show();
        $(el).closest('tr').find('#descriptions').hide();
        $(el).closest('tr').find('#name').hide();
    }else{
        $(el).closest('tr').find('#inputDesc').hide();
        $(el).closest('tr').find('#inputName').hide();
        $(el).closest('tr').find('#file').hide();
        $(el).closest('tr').find('#descriptions').show();
        $(el).closest('tr').find('#name').show();
    }
}
function deleteRow(el){
    if($(el).is(':checked')){
        $(el).closest('tr').addClass("delete");
    }else{
        $(el).closest('tr').removeClass("delete");
    }
}
CKEDITOR.replace( 'editor1' );
</script>


@endsection