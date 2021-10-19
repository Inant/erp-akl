@extends('theme.default')

@section('breadcrumb')
    <div class="page-breadcrumb">
        <div class="row">
            <div class="col-5 align-self-center">
                <h4 class="page-title">Simpan Produk Jadi</h4>
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
<form method="POST" action="{{URL::to('project_req_dev/save_product_label/'.$id)}}" class="form-horizontal">
    @csrf
    <div class="container-fluid">
        <!-- basic table -->
        <br><br>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Daftar Produk</h4>
                        <input type="hidden" value="{{$data->rab_id}}" name="rab_no">
                        <input type="hidden" value="{{$data->order_id}}" name="order_id">
                        <table class="table table-bordered table-striped" id="detail-label">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Pekerjaan</th>
                                    <th>Label</th>
                                    <th><input style="min-width:20px" type="checkbox" name="select-all" id="select-all" class="form-control"></th>
                                    <th>Kondisi</th>
                                    <th>Penyimpanan</th>
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
    $.ajax({
        type: "GET",
        url: "{{ URL::to('project_req_dev/get_product_label') }}"+'/'+{{$id}}, //json get site
        dataType : 'json',
        success: function(response){
            arrData = response['data'];
            // workData = response['work_id'];
            for(i = 0; i < arrData.length; i++){
                var code='';
                if (arrData[i]['project_name'] == 'Pembuatan Daun Jendela') {
                    code='/A';
                }else if (arrData[i]['project_name'] == 'Pembuatan Daun Pintu') {
                    code='/B';
                }else if (arrData[i]['project_name'] == 'Pembuatan Kusen Pintu'){
                    code='/C';
                }else if (arrData[i]['project_name'] == 'Pembuatan Kusen Jendela Swing/Hung'){
                    code='/D';
                }else if (arrData[i]['project_name'] == 'Pembuatan Jendela Kaca Mati'){
                    code='/E';
                }else if (arrData[i]['project_name'] == 'Pembuatan Kusen Pintu Sliding'){
                    code='/F';
                }else if (arrData[i]['project_name'] == 'Pembuatan Daun Pintu Sliding'){
                    code='/G';
                }
                // if (workData.length > 1) {
                //     for(j = 0; j < workData.length; j++){
                //         if (arrData[j]['project_name'] == 'Pembuatan Daun Jendela') {
                //             code='/A';
                //         }else{
                //             code='/B';
                //         }
                //     }
                // }
                var tdAdd='<tr>'+
                                '<td>'+arrData[i]['item']+' '+arrData[i]['name']+' Series : '+arrData[i]['series']+'</td>'+
                                '<td>'+arrData[i]['project_name']+'</td>'+
                                '<td><input type="hidden" name="label[]" value="'+arrData[i]['label']+code+'"><input type="hidden" name="inv_request_prod_id[]" value="'+arrData[i]['inv_request_prod_id']+'"><input type="hidden" name="id[]" value="'+arrData[i]['id']+'"><input type="hidden" name="product_sub_id[]" value="'+arrData[i]['product_sub_id']+'">'+arrData[i]['label']+code+'</td>'+
                                '<td><input style="min-width:20px" type="checkbox" id="check_prod_sub_id[]" class="form-control" name="check_prod_sub_id[]" value="'+arrData[i]['id']+'" onclick="cekChecked()"></td>'+
                                '<td><select class="form-control" name="kondisi[]" id="kondisi[]"><option value="bagus">Bagus</option><option value="buruk">Buruk</option></select></td>'+
                                '<td><input type="" class="form-control" placeholder="" name="storage[]"></td>'+
                            '</tr>';
                $('#detail-label').find('tbody:last').append(tdAdd);
            }
        }
    });
});
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