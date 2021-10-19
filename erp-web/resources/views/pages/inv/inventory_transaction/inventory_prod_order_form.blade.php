@extends('theme.default')

@section('breadcrumb')
<div class="page-breadcrumb">
    <div class="row">
        <div class="col-5 align-self-center">
            <h4 class="page-title">Form Penerimaan Produk Jadi</h4>
            <div class="d-flex align-items-center">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ URL::to('material_request') }}">Penerimaan Produk Jadi</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Form Penerimaan Produk Jadi</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
</div>
@endsection

@section('content')
<form method="POST" action="{{ URL::to('inventory/add_acc_prod') }}" class="mt-5">
    @csrf
    <div class="container-fluid">
        <!-- basic table -->
        <div class="row">
            @if($error['is_error'])
            <div class="col-12">
                <div class="alert alert-danger"> <i class="mdi mdi-alert-box"></i> {{ $error['error_message'] }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"> <span aria-hidden="true">×</span> </button>
                </div>
            </div>
            @endif
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Header</h4>
                 
                        <div class="form-group">
                            <label>Order No</label>
                            <select name="order_id" onchange="getOrderNo(this.value);" class="form-control select2 custom-select" style="width: 100%; height:32px;" required>
                                <option value="">--- Select Order No ---</option>
                                @if($order_list != null)
                                @foreach($order_list as $value)
                                    @if($value['is_done'] != 1)
                                    <option value="{{ $value['id'] }}">{{ $value['order_no'] }}</option>
                                    @endif
                                @endforeach
                                @endif
                            </select>
                        </div>
                       
                        <div class="form-group">
                            <label>RAB Number</label>
                            <select id="rab_no" name="rab_no" required class="form-control select2 custom-select" style="width: 100%; height:32px;" onchange="handleRab(this);">
                                <option value="">--- Select RAB Number ---</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Select Product Label</label>
                            <div id="div-product-label">
                                
                            </div>
                        </div>
                        <table id="requestDetail" class="table table-striped table-bordered display" style="width:100%">
                            <thead>
                                <tr>
                                    <th class="text-center" width="30px"></th>
                                    <th class="text-center">Label</th>
                                    <th class="text-center">Storage</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                        <br>
                        <div class="form-group">
                            <a href="{{ URL::to('inventory/acc_product') }}"><button type="button" class="btn btn-danger mb-2">Cancel</button></a>
                            <button type="submit" name="submit" value="receive" class="btn btn-primary mb-2">Terima</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>           
    </div>
</form>

<script src="{!! asset('theme/assets/libs/jquery/dist/jquery.min.js') !!}"></script>
<script src="{!! asset('theme/assets/extra-libs/DataTables/datatables.min.js') !!}"></script>
<script>
function handleRab(obj) {
    var options='';
    $('#requestDetail tbody').empty();
    $.ajax({
        type: "GET",
        url: "{{ URL::to('inventory/get-product-subs') }}"+'/'+obj.value, //json get site
        dataType : 'json',
        success: function(response){
            arrData = response['data'];
            for(i = 0; i < arrData.length; i++){
                // options+='<div class="custom-control custom-checkbox">'+
                //                         '<input type="checkbox" class="custom-control-input" id="customCheck'+i+'" name="product_sub_id[]" value="'+arrData[i]['id']+'">'+
                //                         '<label class="custom-control-label" for="customCheck'+i+'">'+arrData[i]['no']+'</label>'+
                //                     '</div>';
                options+='<tr><td><div class="custom-control custom-checkbox">'+
                                '<input type="checkbox" class="custom-control-input" id="customCheck'+i+'" name="check_prod_sub_id[]" value="'+arrData[i]['id']+'">'+
                                '<label class="custom-control-label" for="customCheck'+i+'"></label>'+
                            '</div></td>'+
                            '<td><div>'+arrData[i]['no']+'<input type="hidden" class="form-control" name="product_sub_id[]" value="'+arrData[i]['id']+'"></div></td>'+
                            '<td><div><input type="" class="form-control" name="storage[]" placeholder=""></div></td>';
            }
            // $('#div-product-label').html(options);
            $('#requestDetail').append(options);
        },
        error: function (error) {
            $('#requestDetail tbody').empty();
            // $('#div-product-label').html(options);
        }
    });
}

function getOrderNo(order_id){
    // $('#div-product-label').html('');
    $('#requestDetail tbody').empty();
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
                if (arrData[i]['is_final'] == true) {
                    formRabNo.append('<option value="'+arrData[i]['id']+'">'+arrData[i]['no']+'</option>');
                }
            }
        }
    });
}
</script>
@endsection