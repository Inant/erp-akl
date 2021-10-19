@extends('theme.default')

@section('breadcrumb')
    <div class="page-breadcrumb">
        <div class="row">
            <div class="col-5 align-self-center">
                <h4 class="page-title">Tambah Order</h4>
                <div class="d-flex align-items-center">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ URL::to('order') }}">Order</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Tambah</li>
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
                    <h4 class="card-title">Tambah Order</h4>
                    <form method="POST" action="{{ URL::to('order/save') }}" class="mt-4" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col-md-5 col-sm-12">
                                <div class="form-group">
                                    <label>Customer</label>
                                    <select id="customer_id" name="customer_id" required class="form-control select2 custom-select" style="width: 100%; height:32px;" onchange="getCustomer(this.value)">
                                        <option value="">--- Pilih Customer ---</option>
                                        @foreach($customer as $value)
                                        <option value="{{$value['id']}}">{{$value['coorporate_name']}}</option>
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
                            <div class="col-md-2 col-sm-12">
                                <div class="form-group">
                                    <label class="text-white">-</label>
                                    <div class="form-inline">
                                    <button class="btn btn-primary" type="button"  data-toggle="modal" data-target="#modalAddProjectCust" ><i class="mdi mdi-plus"></i> Buat Nama Proyek</button>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-5 col-sm-12">
                                <div class="form-group">
                                    <label>No SPJB</label>
                                    <input type="text" id="no_spk" name="no_spk" class="form-control" required>
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label>Deskripsi Order</label>
                                    <!-- <textarea class="form-control" name="order_name" required></textarea> -->
                                    <textarea class="form-control" name="order_name" required id="editor1" rows="3" cols="10">
                                    </textarea>
                                    
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
                            <div class="col-sm-6">
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
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label class="text-white">Import Item</label>
                                    <div class="form-inline">
                                    <!-- <input type="file" name="importFile" id="importFile" class="form-control" style="padding-right:10px"> -->
                                    <button class="btn btn-warning" type="button"  data-toggle="modal" data-target="#modalImportItem" ><i class="mdi mdi-file-excel"></i> Import Item</button>
                                    </div>
                                    
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                        <button class="btn btn-success" type="button" data-toggle="modal" data-target="#modalKavling" title="Tambah Produk">Tambah Kavling</button>
                        <button class="btn btn-primary" type="button" data-toggle="modal" data-target="#modalEditKavling" title="Tambah Produk">Edit Kavling</button>
                        </div>
                        <div class="table-responsive">
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
                                    
                                </tbody>
                            </table>
                        </div>
                        <button type="submit" id="submit" class="btn btn-primary" disabled>Submit</button>
                    </form>
                </div>
            </div>
        
        </div>
    </div>
</div>
<div id="myModal" class="modal fade" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
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
                    <label>Customer</label>
                    <select id="customer_id1" name="customer_id1" required class="form-control select2 custom-select" style="width: 100%; height:32px;" onchange="cekKavling(this.value)">
                        <option value="">--- Pilih Customer ---</option>
                        @foreach($customer as $value)
                        <option value="{{$value['id']}}">{{$value['coorporate_name']}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label>Type Kavling</label>
                    <select id="kavling_id" name="kavling_id" required class="form-control select2" style="width: 100%; height:32px;">
                    </select>
                </div>
                <div class="form-group">
                    <label>Product Equivalent</label>
                    <select id="product_equivalent" name="product_equivalent" class="form-control select2" style="width: 100%; height:32px;">
                        <option value="">--- Pilih Product Equivalent ---</option>
                        @foreach($product_equivalent as $value)
                        <option value="{{ $value->id }}">{{ $value->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label>Item</label>
                    <input type="text" class="form-control" required name="item" />
                </div>
                <div class="form-group">
                    <label>Nama Item</label>
                    <input type="text" class="form-control" name="name" required/>
                </div>
                <div class="form-group">
                    <label>Series</label>
                    <input type="text" class="form-control" required name="series" />
                </div>
                <div class="form-group">
                    <label>Dimensi</label>
                    <div class="row">
                        <div class="col-sm-6">
                            <label>Panjang W(m<sup>1</sup>)</label>
                            <input type="number" class="form-control" name="panjang" step="any"/>
                        </div>
                        <div class="col-sm-6">
                            <label>Tinggi H(m<sup>1</sup>)</label>
                            <input type="number" class="form-control" name="lebar" step="any"/>
                        </div>
                    </div>
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
                    <label>Harga</label>
                    <input type="number" class="form-control" name="price" required/>
                </div>
                <div class="form-group">
                    <label>Jasa Instalasi</label>
                    <input type="number" class="form-control" name="installation_fee"/>
                </div>
                <div class="form-group">
                    <label>Total Item</label>
                    <input type="number" class="form-control" name="set" required/>
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
<div id="modalKavling" class="modal fade" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel">Add Kavling</h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <form id="addKavling" action="javascript:;" accept-charset="utf-8" method="post" enctype="multipart/form-data">
            <div class="modal-body">
                @csrf
                <div class="form-group">
                    <label>Customer</label>
                    <select id="customer_id2" name="customer_id1" required class="form-control select2 custom-select" style="width: 100%; height:32px;">
                        <option value="">--- Pilih Customer ---</option>
                        @foreach($customer as $value)
                        <option value="{{$value['id']}}">{{$value['coorporate_name']}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label>Nama Kavling</label>
                    <input type="text" class="form-control" name="name" required/>
                </div>
                <div class="form-group">
                    <label>Total Kavling</label>
                    <input type="number" class="form-control" name="total" required/>
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
<div id="modalEditKavling" class="modal fade" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel">Edit Kavling</h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <form id="editKavling" action="javascript:;" accept-charset="utf-8" method="post" enctype="multipart/form-data">
            <div class="modal-body">
                @csrf
                <div class="form-group">
                    <label>Customer</label>
                    <select id="customer_id4" name="customer_id1" required class="form-control select2 custom-select" style="width: 100%; height:32px;"  onchange="cekKavling3(this.value)">
                        <option value="">--- Pilih Customer ---</option>
                        @foreach($customer as $value)
                        <option value="{{$value['id']}}">{{$value['coorporate_name']}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label>Nama Kavling</label>
                    <select name="kavling_id" id="kavling_name" onchange="getKavling(this.value)" class="form-control select2" required style="width:100%">
                        <option value="">-- Pilih Kavling --</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Total Kavling</label>
                    <input type="number" class="form-control" name="total_kavling" id="total_kavling" required/>
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
<div id="modalImportItem" class="modal fade" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel">Import Item</h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <form id="addItemImport" action="javascript:;" accept-charset="utf-8" method="post" enctype="multipart/form-data">
            {{-- <form id="addItemImport" action="{{ URL::to('order/import_item_post') }}" accept-charset="utf-8" method="post" enctype="multipart/form-data"> --}}
            <div class="modal-body">
                @csrf
                <div class="form-group">
                    <label>Customer</label>
                    <select id="customer_id3" name="customer_id" required class="form-control select2 custom-select" style="width: 100%; height:32px;"  onchange="cekKavling2(this.value)">
                        <option value="">--- Pilih Customer ---</option>
                        @foreach($customer as $value)
                        <option value="{{$value['id']}}">{{$value['coorporate_name']}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label>Nama Kavling</label>
                    <select name="kavling_id" id="kavling_id2" class="form-control select2" required style="width:100%">
                    </select>
                </div>
                <div class="form-group">
                    <label>Pilih File</label>
                    <input type="file" name="importFile" id="importFile" required class="form-control" accept=".csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel">
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
<div id="modalListItem" class="modal fade" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel">List Item</h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <div class="modal-body">
               <div class="table-responsive">
                    <table class="table table-bordered table-striped" id="item_list">
                        <thead>
                            <tr>
                                <th>Item</th>
                                <th>Harga</th>
                                <th>Kavling</th>
                                <th>Total Set</th>
                                <th></th>
                            </tr>
                        </thead>
                    </table>
               </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger waves-effect" data-dismiss="modal">Close</button>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<div id="modalAddProjectCust" class="modal fade" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel">Tambah Nama Proyek</h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <form id="addProjectCust" action="javascript:;" accept-charset="utf-8" method="post" enctype="multipart/form-data">
            <div class="modal-body">
                @csrf
                <!-- <div class="form-group">
                    <label>Customer</label>
                    <select name="customer_id" required class="form-control select2 custom-select" style="width: 100%; height:32px;">
                        <option value="">--- Pilih Customer ---</option>
                        @foreach($customer as $value)
                        <option value="{{$value['id']}}">{{$value['coorporate_name']}}</option>
                        @endforeach
                    </select>
                </div> -->
                <div class="form-group">
                    <label>Nama Proyek</label>
                    <input type="text" class="form-control" required id="name_project" name="name_project">
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
var total_produk=0;
$(document).ready(function(){
    $('#customer_project_id').empty();
    $('#customer_project_id').append('<option value="">-- Pilih Proyek --</option>');
    $.ajax({
        type: "GET",
        url: "{{ URL::to('master_kavling/get_project_cust') }}", //json get site
        dataType : 'json',
        success: function(response){
            arrData=response['data'];
            for(i = 0; i < arrData.length; i++){
                $('#customer_project_id').append('<option value="'+arrData[i]['id']+'">'+arrData[i]['name']+'</option>');
            }
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
    $("form#addKavling").on("submit", function( event ) {
        var form = $('#addKavling')[0];
        var data = new FormData(form);
        event.preventDefault();
        // console.log( $('form#addKavling').serialize() );
        $.ajax({
            type: "POST",
            url: "{{ URL::to('master_kavling/create_json') }}", //json get site
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
        $('#modalKavling').modal('hide');
    });
    $("form#editKavling").on("submit", function( event ) {
        var form = $('#editKavling')[0];
        var data = new FormData(form);
        event.preventDefault();
        // console.log( $('form#addKavling').serialize() );
        $.ajax({
            type: "POST",
            url: "{{ URL::to('master_kavling/update_json') }}", //json get site
            dataType : 'json',
            data: data,
            processData: false,
            contentType: false,
            success: function(response){
                // $('#product_search').focus();
                $('#editProduct').trigger("reset");
                $('#total_kavling').val('');
                $('#kavling_name').val('').change();
                $('#customer_id4').val('').change();
                alert(response['message']);
            }
        });
        $('#modalEditKavling').modal('hide');
    });
    $("form#addItemImport").on("submit", function( event ) {
        event.preventDefault();
        const importFile = $('#importFile').prop('files');

        if (typeof importFile !== 'undefined') { 
            CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');     
            var form_data = new FormData();                  
            form_data.append('file', importFile[0]);  
            form_data.append('_token', CSRF_TOKEN);   
            form_data.append('customer_id', $('#customer_id3').val());   
            form_data.append('kavling_id', $('#kavling_id2').val());   
            console.log(form_data)
            $.ajax({
                url: "{{ URL::to('order/import_item_post') }}", 
                dataType: 'json',  
                cache: false,
                contentType: false,
                processData: false,
                data: form_data,                         
                async : false,
                type: 'post',
                success: function(response){
                    console.log(response)
                    $('#customer_id').val($('#customer_id3').val()).change();
                    arrData = response['data'];
                    // getCustomer(customer_id); not used
                    for(i = 0; i < arrData.length; i++){
                        addProductOrder(arrData[i])
                    }
                    $('#modalImportItem').modal('hide');
                    // var customer_id=$('#customer_id3').val().change(); not used
                }
            });

        }
    });
    $("form#addProjectCust").on("submit", function( event ) {
        var form = $('#addProjectCust')[0];
        var data = new FormData(form);
        event.preventDefault();
        // console.log( $('form#addKavling').serialize() );
        $.ajax({
            type: "POST",
            url: "{{ URL::to('master_kavling/create_project_json') }}", //json get site
            dataType : 'json',
            data: data,
            processData: false,
            contentType: false,
            success: function(response){
                alert(response['message']);
            }
        });
        $('#modalAddProjectCust').modal('hide');
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
            url:"{{ URL::to('/order/fetch') }}",
            type: 'post',
            dataType: "json",
            data: {
               _token: '{{csrf_token()}}',
               search: request.term,
               customer_id: $('#customer_id').val()
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
    $('#modalListItem').modal('hide');
    var data=[];
    // var detail_product=$('#detail-order');
    $.ajax({
        url : url+'/order/get-product/'+id,
        type : 'GET',
        dataType : 'json',
        async : false,
        success : function(response){
            total_produk++;
            var tdAdd='<tr>'+
            '<td><p id="item">'+response['item']+'</p><input type="" name="item[]" class="form-control" style="display:none" id="inputItem" value="'+response['item']+'"></td>'+
                            '<td><p>'+response['name']+'</p><input type="" name="item[]" class="form-control" style="display:none" id="inputItem" value="'+response['name']+'"></td>'+
                            '<td><input type="hidden" name="id_product[]" value="'+response['id']+'"><p id="">'+response['type_kavling']+'</p><input type="text" style="display:none" id="" class="form-control" value="'+response['name']+'" name="name[]"></td>'+
                            '<td><p id="descriptions">'+response['description']+'</p><textarea name="deskripsi[]" class="form-control" style="display:none" id="inputDesc" onkeyup="changeDesc(this)">'+response['description']+'</textarea></td>'+
                            '<td><p id="series">'+response['series']+'</p><input type="" name="series[]" class="form-control" style="display:none" id="inputSeries" onkeyup="changeSeries(this)" value="'+response['series']+'"></td>'+
                            '<td><p id="panjang">'+response['panjang']+'</p><input type="number" name="panjang[]" class="form-control" style="display:none" id="inputPanjang" value="'+response['panjang']+'"></td>'+
                            '<td><p id="lebar">'+response['lebar']+'</p><input type="number" name="lebar[]" class="form-control" style="display:none" id="inputLebar" value="'+response['lebar']+'"></td>'+
                            // '<td><img src="'+url+'/upload/product/'+response['image']+'" width="60px" onclick="showImage(this.src)"><input type="file" id="file" style="display:none" name="file[]" accept="image/*" class="form-control"></td>'+
                            '<td><p id="price">'+(response['price'] != null ? formatNumber(parseFloat(response['price']).toFixed(0)) : 0)+'</p><input type="number" name="price[]" class="form-control" style="display:none" id="input" value="'+response['price']+'"></td>'+
                            '<td><p id="fee_installation">'+(response['installation_fee'] != null ? formatNumber(response['installation_fee']) : 0)+'</p><input type="number" name="fee_install[]" class="form-control" style="display:none" id="inputFeeInstall" value="'+response['installation_fee']+'"></td>'+
                            '<td><p id="total_set">'+response['amount_set']+'</p><input type="number" name="set[]" class="form-control" style="display:none" id="inputSet" value="'+response['amount_set']+'"></td>'+
                            '<td><input type="number" readonly required class="form-control" placeholder="Total" name="total_produk[]" value="'+response['amount']+'"></td>'+
                            // '<td><input type="checkbox" id="editDesc" class="form-control" onclick="cekChange(this)" name="editProd[]" value="'+response['id']+'"></td>'+
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
// function changeDesc(value){
//     // console.log(value);
//     $(value).closest('tr').find('#description').html(value.value);
// }
// function changeSeries(value){
//     // console.log(value);
//     $(value).closest('tr').find('#series').html(value.value);
// }
function cekChange(el){
    if($(el).is(':checked')){
        $(el).closest('tr').find('#inputDesc').show();
        $(el).closest('tr').find('#inputName').show();
        $(el).closest('tr').find('#inputSeries').show();
        $(el).closest('tr').find('#inputItem').show();
        $(el).closest('tr').find('#inputPanjang').show();
        $(el).closest('tr').find('#inputLebar').show();
        $(el).closest('tr').find('#inputSet').show();
        $(el).closest('tr').find('#inputFeeInstall').show();
        $(el).closest('tr').find('#file').show();
        $(el).closest('tr').find('#descriptions').hide();
        $(el).closest('tr').find('#series').hide();
        $(el).closest('tr').find('#item').hide();
        $(el).closest('tr').find('#panjang').hide();
        $(el).closest('tr').find('#lebar').hide();
        $(el).closest('tr').find('#name').hide();
        $(el).closest('tr').find('#total_set').hide();
        $(el).closest('tr').find('#fee_installation').hide();
    }else{
        $(el).closest('tr').find('#inputDesc').hide();
        $(el).closest('tr').find('#inputName').hide();
        $(el).closest('tr').find('#inputSeries').hide();
        $(el).closest('tr').find('#inputItem').hide();
        $(el).closest('tr').find('#inputPanjang').hide();
        $(el).closest('tr').find('#inputLebar').hide();
        $(el).closest('tr').find('#inputSet').hide();
        $(el).closest('tr').find('#inputFeeInstall').hide();
        $(el).closest('tr').find('#file').hide();
        $(el).closest('tr').find('#descriptions').show();
        $(el).closest('tr').find('#series').show();
        $(el).closest('tr').find('#item').show();
        $(el).closest('tr').find('#panjang').show();
        $(el).closest('tr').find('#lebar').show();
        $(el).closest('tr').find('#name').show();
        $(el).closest('tr').find('#total_set').show();
        $(el).closest('tr').find('#fee_installation').show();
    }
}
function getCustomer(id){
    $('#detail-order > tbody').empty();
}
function cekKavling(val){
    $('#kavling_id').empty();
    $('#kavling_id').append('<option value="">-- Pilih Kavling --</option>');
    $.ajax({
        type: "GET",
        url: "{{ URL::to('master_kavling/get_kavling_by_cust') }}"+'/'+val, //json get site
        dataType : 'json',
        success: function(response){
            arrData=response['data'];
            for(i = 0; i < arrData.length; i++){
                $('#kavling_id').append('<option value="'+arrData[i]['id']+'">'+arrData[i]['name']+'</option>');
            }
        }
    });
}
function cekKavling2(val){
    $('#kavling_id2').empty();
    $('#kavling_id2').append('<option value="">-- Pilih Kavling --</option>');
    $.ajax({
        type: "GET",
        url: "{{ URL::to('master_kavling/get_kavling_by_cust') }}"+'/'+val, //json get site
        dataType : 'json',
        success: function(response){
            arrData=response['data'];
            for(i = 0; i < arrData.length; i++){
                $('#kavling_id2').append('<option value="'+arrData[i]['id']+'">'+arrData[i]['name']+'</option>');
            }
        }
    });
}
function cekKavling3(val){
    $('#kavling_name').empty();
    $('#kavling_name').append('<option value="">-- Pilih Kavling --</option>');
    $.ajax({
        type: "GET",
        url: "{{ URL::to('master_kavling/get_kavling_by_cust') }}"+'/'+val, //json get site
        dataType : 'json',
        success: function(response){
            arrData=response['data'];
            for(i = 0; i < arrData.length; i++){
                $('#kavling_name').append('<option value="'+arrData[i]['id']+'">'+arrData[i]['name']+'</option>');
            }
        }
    });
}
function getKavling(val){
    $.ajax({
        type: "GET",
        url: "{{ URL::to('master_kavling/get_kavling_by_id') }}"+'/'+val, //json get site
        dataType : 'json',
        success: function(response){
            arrData=response['data'];
            $('#total_kavling').val(arrData['amount']);
        }
    });
}
function importItem() {
    const importFile = $('#importFile').prop('files');

    if (typeof importFile !== 'undefined') { 
        CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');     
        var form_data = new FormData();                  
        form_data.append('file', importFile[0]);  
        form_data.append('_token', CSRF_TOKEN);              
        $.ajax({
            url: "{{ URL::to('order/import_item_post') }}", 
            dataType: 'json',  
            cache: false,
            contentType: false,
            processData: false,
            data: form_data,                         
            type: 'post',
            success: function(response){
                // response.data.map((item, index) => {
                //     var option_material='<option value="">--- Pilih Material ---</option>';
                //     for(i = 0; i < listMaterial.length; i++){
                //         if (item.m_item_id === listMaterial[i]['id'])
                //             option_material+='<option selected value="'+listMaterial[i]['id']+'">'+listMaterial[i]['name']+'</option>';
                //         else
                //             option_material+='<option value="'+listMaterial[i]['id']+'">'+listMaterial[i]['name']+'</option>';
                //     }
                    
                //     var tdAdd='<tr>'+
                //                     '<td>'+
                //                         '<input type="text" onchange="handleMaterialNo2();" name="item_no[]" required class="form-control" id="item_no[]">'+
                //                     '</td>'+
                //                     '<td>'+
                //                         '<select id="material_name2[]" name="material_name2[]" required class="form-control custom-select" onchange="handleMaterial2();" style="width: 100%; height:32px;">'+
                //                             option_material+
                //                         '</select>'+
                //                     '</td>'+
                //                     '<td>'+
                //                         '<input type="hidden" name="material_unit2[]" required class="form-control" readonly id="material_unit2[]">'+
                //                             '<input type="text" name="material_unit_text2[]" required class="form-control" readonly id="material_unit_text2[]">'+
                //                     '</td>'+
                //                     '<td>'+
                //                         '<input type="hidden" name="volume_child2[]" required class="form-control" readonly id="volume_child2[]">'+
                //                         '<input type="text" name="material_unit_text_child2[]" required class="form-control" readonly id="material_unit_text_child2[]">'+
                //                     '</td>'+
                //                     '<td>'+
                //                         '<input type="number" name="volume_per_turunan2[]" required class="form-control" id="volume_per_turunan2[]" min="0" onkeyup="cekTotalTurunan()" value="'+item.volume_per_turunan+'" required>'+
                //                     '</td>'+
                //                     '<td>'+
                //                         '<input type="number" name="qty2[]" required class="form-control" id="qty" value="'+item.qty_item+'" required>'+
                //                         '<input type="hidden" name="price2[]" class="form-control" id="price2[]">'+
                //                     '</td>'+
                //                     '<td class="text-center"><button class="btn btn-danger removeOption"><i class="mdi mdi-delete"></i></button></td>'+
                //                 '</tr>';
                //     $('#list_material').find('tbody:last').append(tdAdd);
                // })

                // handleMaterial2();
            }
        });

    }

    console.warn(importFile);
}
function listItem(){
    var id_customer=$('#customer_id').val();
    var t=$('#item_list').DataTable();
    t.clear().draw(false);
    if (id_customer != '') {
        $('#modalListItem').modal('show');
        $.ajax({
            type: "GET",
            url: "{{ URL::to('order/get_item_customer') }}"+'/'+id_customer, //json get site
            dataType : 'json',
            success: function(response){
                arrData=response['data'];
                for(i = 0; i < arrData.length; i++){
                    t.row.add([
                        '<div>'+arrData[i]['item']+' Series : '+arrData[i]['series']+' Dimensi W :'+arrData[i]['panjang']+' H : '+arrData[i]['lebar']+'</div>',
                        '<div>'+formatCurrency(parseFloat(arrData[i]['price']).toFixed(0))+'</div>',
                        '<div>'+arrData[i]['type_kavling'] +'</div>',
                        '<div>'+arrData[i]['amount_set'] +'</div>',
                        '<div><button type="button" class="btn btn-info" onclick="addProductOrder('+arrData[i]['id']+')"><i class="mdi mdi-plus"></i></div>'
                    ]).draw(false);
                }
            }
        });
    }
}
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
</script>
<script>
    // Replace the <textarea id="editor1"> with a CKEditor
    // instance, using default configuration.
    CKEDITOR.replace( 'editor1' );
</script>
@endsection