@extends('theme.default')

@section('breadcrumb')
    <div class="page-breadcrumb">
        <div class="row">
            <div class="col-5 align-self-center">
                <h4 class="page-title">Master Product</h4>
                <div class="d-flex align-items-center">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ URL::to('master_product') }}">Master Product</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Create</li>
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
                    <h4 class="card-title">Create Product</h4>
                    <form method="POST" action="{{ URL::to('master_product/create') }}" class="form-horizontal" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group row">
                            <label class="col-sm-3 text-right control-label col-form-label">Customer</label>
                            <div class="col-sm-8">
                                <select id="customer_id" name="customer_id" required class="form-control select2 custom-select" style="width: 100%; height:32px;" onchange="cekKavling(this.value)">
                                    <option value="">--- Pilih Customer ---</option>
                                    @foreach($customer as $value)
                                    <option value="{{$value['id']}}">{{$value['coorporate_name']}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-3 text-right control-label col-form-label">Item</label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="item" />
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-3 text-right control-label col-form-label">Type Kavling</label>
                            <div class="col-sm-8"> 
                                <select id="kavling_id" name="kavling_id" required class="form-control select2" style="width: 100%; height:32px;">
                                </select>   
                                <input type="hidden" class="form-control" name="name" />
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-3 text-right control-label col-form-label">Product Equivalent</label>
                            <div class="col-sm-8"> 
                                <select id="product_equivalent" name="product_equivalent" required class="form-control select2" style="width: 100%; height:32px;">
                                    <option value="">--- Pilih Product Equivalent ---</option>
                                    @foreach($product_equivalent as $value)
                                    <option value="{{ $value->id }}">{{ $value->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-3 text-right control-label col-form-label">Series</label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="series" />
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-3 text-right control-label col-form-label">Dimensi</label>
                            <div class="col-sm-4">
                                <input type="number" class="form-control" name="panjang" placeholder="Panjang" step="any"/>
                            </div>
                            <div class="col-sm-4">
                                <input type="number" class="form-control" name="lebar" placeholder="Lebar" step="any"/>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-3 text-right control-label col-form-label">Deskripsi</label>
                            <div class="col-sm-8">
                                <textarea class="form-control" name="description" ></textarea>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-3 text-right control-label col-form-label">Satuan</label>
                            <div class="col-sm-8">
                                <select id="satuan" name="m_unit_id" required class="form-control select2 custom-select" style="width: 100%; height:32px;">
                                    <option value="">--- Select Satuan ---</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-3 text-right control-label col-form-label">Image Sketch</label>
                            <div class="col-sm-8">
                                <input type="file" class="form-control" name="image"  accept="image/*"/>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-3 text-right control-label col-form-label">Harga</label>
                            <div class="col-sm-8">
                                <input type="number" class="form-control" name="price"/>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-3 text-right control-label col-form-label">Jasa Instalasi</label>
                            <div class="col-sm-8">
                                <input type="number" class="form-control" name="installation_fee"/>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-3 text-right control-label col-form-label">Total Item</label>
                            <div class="col-sm-8">
                                <input type="number" class="form-control" name="set"/>
                            </div>
                        </div>
                        <div class="text-right">
                            <button class="btn btn-primary mt-4" type="submit">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        
        </div>
    </div>
</div>


<script src="{!! asset('theme/assets/libs/jquery/dist/jquery.min.js') !!}"></script>
<script>
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
$(document).ready(function(){
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

});
</script>
@endsection