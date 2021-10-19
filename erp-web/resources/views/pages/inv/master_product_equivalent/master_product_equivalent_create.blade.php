@extends('theme.default')

@section('breadcrumb')
    <div class="page-breadcrumb">
        <div class="row">
            <div class="col-5 align-self-center">
                <h4 class="page-title">Master Product Equivalent</h4>
                <div class="d-flex align-items-center">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ URL::to('master_product_equivalent') }}">Master Product Equivalent</a></li>
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
                    <h4 class="card-title">Create Product Equivalent</h4>
                    <form method="POST" action="{{ URL::to('master_product_equivalent/create') }}" class="form-horizontal" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group row">
                            <label class="col-sm-3 text-right control-label col-form-label">Code</label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="code" required />
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-3 text-right control-label col-form-label">Name</label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="name" required />
                            </div>
                        </div>

                        <br />
                        <h4 class="card-title">Product Equivalent - Kebutuhan Material</h4>
                        <div style="margin-top:10px;">
                            <button id="btn_add_to_selected" type="button" onclick="addToSelected();" class="btn btn-success btn-sm mb-2">Tambah Material</button>
                            <!-- <button type="button" onclick="addEquivalent();" class="btn btn-warning btn-sm mb-2">Tambah Equivalent</button>
                            <button type="button" onclick="editEquivalent();" class="btn btn-warning btn-sm mb-2">Edit Equivalent</button> -->
                        </div>
                        <div class="table-responsive">
                            <table id="dt_temp" class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th class="text-center">Material Name</th>
                                        <th class="text-center">Dimensi</th>
                                        <th class="text-center">Operator</th>
                                        <th class="text-center">Equivalent</th>
                                        <th class="text-center">Qty Item</th>
                                        <th class="text-center">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tobdy>
                            </table>
                        </div>
                        <br/>
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
var arrMaterial = [];
$.ajax({
    type: "GET",
    url: "{{ URL::to('material_request/get_material_without_atk') }}", //json get material
    dataType : 'json',
    async : false,
    success: function(response){
        arrMaterial = response['data'];
    }
});

var arrEquivalent = [];
$.ajax({
    type: "GET",
    url: "{{ URL::to('master_product_equivalent/m_equivalent') }}", //json
    dataType : 'json',
    async : false,
    success: function(response){
        arrEquivalent = response['data'];
    }
});

function addToSelected() {
    var tdAdd='<tr>'+
                    '<td>'+
                        '<select name="m_item_id[]" class="form-control select2" style="width: 100%;"></select>' +
                    '</td>'+
                    '<td>'+
                        '<select name="dimensi[]" class="form-control select2" style="width: 100%;"><option value="None">None</option><option value="H">H</option><option value="W">W</option></select>' +
                    '</td>'+
                    '<td>'+
                        '<select name="operator[]" class="form-control select2" style="width: 100%;"><option value="None">None</option><option value="-">-</option><option value="+">+</option></select>' +
                    '</td>'+
                    '<td>'+
                        '<input name="equivalent[]" type="number" class="form-control text-left" min="0" required />' +
                    '</td>'+
                    '<td>'+
                        '<input name="qty_item[]" type="number" class="form-control text-left" min="0" required />' +
                    '</td>'+
                    '<td class="text-center"><button class="btn btn-danger removeOption"><i class="mdi mdi-delete"></i></button></td>'+
                '</tr>';
    $('#dt_temp').find('tbody:last').append(tdAdd);
    $('.select2').select2();
    eventSelectedMaterial();
}

function eventSelectedMaterial() {
    countMaterial = $('[name^=m_item_id]').length;
    let arrSelectedMaterial = [];
    for(i = 0; i < countMaterial; i++){
        if($('[name^=m_item_id]').eq(i).val() !== null && $('[name^=m_item_id]').eq(i).val() !== '')
            arrSelectedMaterial.push($('[name^=m_item_id]').eq(i).val());
    }
    // console.log(arrMaterial);
    for(i = 0; i < countMaterial; i++){
        formMaterial = $('[name^=m_item_id]').eq(i);
        selectedMaterial = $('[name^=m_item_id]').eq(i).val();
        $('[name^=m_item_id]').eq(i).empty();
        formMaterial.append('<option value="">-- Select Material --</option>');
        $.each(arrMaterial, function(i, item) {
            if(selectedMaterial == arrMaterial[i]['id']) {
                formMaterial.append('<option selected value="'+arrMaterial[i]['id']+'">('+arrMaterial[i]['no']+') '+arrMaterial[i]['name']+'</option>');
            } else {
                formMaterial.append('<option value="'+arrMaterial[i]['id']+'">('+arrMaterial[i]['no']+') '+arrMaterial[i]['name']+'</option>');
            }
        });
    }
}

$("#dt_temp").on("click", ".removeOption", function(event) {
    event.preventDefault();
    $(this).closest("tr").remove();
});
</script>
@endsection