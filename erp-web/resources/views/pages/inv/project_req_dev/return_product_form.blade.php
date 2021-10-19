@extends('theme.default')

@section('breadcrumb')
    <div class="page-breadcrumb">
        <div class="row">
            <div class="col-5 align-self-center">
                <h4 class="page-title">Pengembalian Produk Jadi</h4>
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
<form method="POST" action="{{URL::to('project_req_dev/return_product_label')}}" class="form-horizontal">
    @csrf
    <div class="container-fluid">
        <!-- basic table -->
        <br><br>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Pengembalian Produk Jadi</h4>
                        <div class="form-group row">
                            <label class="col-sm-3 text-right control-label col-form-label">Nomor Penyerahan</label>
                            <div class="col-sm-9">
                                <select name="inv_id" id="inv_id" class="form-control select2" style="width:100%" onchange="getLabel(this)">
                                    <option value="">-- Pilih Inv Number --</option>
                                </select>
                            </div>
                        </div>
                        <table class="table table-bordered table-striped" id="detail-label">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Label</th>
                                    <th><input style="min-width:20px" type="checkbox" name="select-all" id="select-all" class="form-control"></th>
                                    <!-- <th>Kondisi</th>
                                    <th>Penyimpanan</th> -->
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                        <button class="btn btn-success">Simpan</button>
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
    var inv_number=$('#inv_id');
    inv_number.empty();
    inv_number.append('<option value="">-- Pilih Inv Number --</option>');
    $.ajax({
        type: "GET",
        url: "{{ URL::to('project_req_dev/list_transfer') }}", //json get site
        dataType : 'json',
        success: function(response){
            arrData = response['data'];
            for(i = 0; i < arrData.length; i++){
                inv_number.append('<option value="'+arrData[i]['id']+'">'+arrData[i]['no']+'</option>');
            }
        }
    });
    $('#select-all').click(function(event) {   
        if(this.checked) {
            // Iterate each checkbox
            $(':checkbox').each(function() {
                this.checked = true;        
                cekChecked();                
            });
        } else {
            $(':checkbox').each(function() {
                this.checked = false;                       
                cekChecked();
            });
        }
    });
    
});
function getLabel(eq){
    var id=eq.value;
    $.ajax({
        type: "GET",
        url: "{{ URL::to('inventory/json_acc_detail') }}" + "/" + id, //json get site
        dataType : 'json',
        success: function(response){
            arrData = response['data'];
            for(i = 0; i < arrData.length; i++){
                var tdAdd='<tr>'+
                                '<td>'+arrData[i]['item']+' '+arrData[i]['name']+' Series : '+arrData[i]['series']+'</td>'+
                                '<td><input type="hidden" name="label[]" value="'+arrData[i]['prod_no']+'"><input type="hidden" name="id[]" value="'+arrData[i]['id']+'"><input type="hidden" name="product_sub_id[]" value="'+arrData[i]['product_sub_id']+'">'+arrData[i]['prod_no']+'</td>'+
                                '<td>'+
                                '<input type="hidden" name="inv_req_prod_id[]" value="'+arrData[i]['inv_request_prod_id']+'"><input type="hidden" name="dev_project_label_id[]" value="'+arrData[i]['dev_project_label_id']+'">'+
                                '<input style="min-width:20px" type="checkbox" id="check_prod_sub_id[]" class="form-control" name="check_prod_sub_id[]" value="'+arrData[i]['dev_project_label_id']+'" onclick="cekChecked()">'+
                                '</td>'+
                                // '<td><select class="form-control" name="kondisi[]" id="kondisi[]"><option value="bagus">Bagus</option><option value="buruk">Buruk</option></select></td>'+
                                // '<td><input type="" class="form-control" placeholder="" name="storage[]"></td>'+
                            '</tr>';
                $('#detail-label').find('tbody:last').append(tdAdd);
            }
        }
    });
}
function cekChecked(){
    var cb = $('[id^=check_prod_sub_id]');
    var total=0;
    var total_price=0;
    for(i = 0; i < cb.length; i++){
        if(cb.eq(i).prop('checked') === true){
            total++;
        }
    }
    
    if(total == cb.length){
        $('#select-all').prop('checked', true);
    }else{
        $('#select-all').prop('checked', false);
    }
}
</script>
@endsection