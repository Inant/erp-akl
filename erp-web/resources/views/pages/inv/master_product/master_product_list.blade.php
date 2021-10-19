@extends('theme.default')

@section('breadcrumb')
    <div class="page-breadcrumb">
        <div class="row">
            <div class="col-5 align-self-center">
                <h4 class="page-title">Master Product</h4>
                <div class="d-flex align-items-center">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item active" aria-current="page">Master Product</li>
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
            <div class="text-right">
                <a href="{{ URL::to('master_product/create') }}"><button class="btn btn-success btn-sm mb-2"><i class="fas fa-plus"></i>&nbsp; Create New</button></a>
            </div>
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">List Master Product</h4>
                    <div class="table-responsive">
                        <table id="product_list" class="table table-striped table-bordered" style="width:100%">
                            <thead>
                                <tr>
                                    <th class="text-center" rowspan="2">Id</th>
                                    <th class="text-center" rowspan="2">Item</th>
                                    <th class="text-center" rowspan="2">Type Kavling</th>
                                    <th class="text-center" rowspan="2">Series</th>
                                    <th class="text-center" colspan="2">Dimensi</th>
                                    <!-- <th class="text-center">Lebar</th> -->
                                    <th class="text-center" rowspan="2">Deskripsi</th>
                                    <th class="text-center" rowspan="2">Harga</th>
                                    <th class="text-center" rowspan="2">Jasa Pasang</th>
                                    <th class="text-center" rowspan="2">Satuan</th>
                                    <th class="text-center" rowspan="2">Total Item</th>
                                    <th class="text-center" rowspan="2">Status</th>
                                    <th class="text-center" rowspan="2">Gambar</th>
                                    <th class="text-center" rowspan="2" style="min-width:60px">Action</th>
                                </tr>
                                <tr>
                                    <th class="text-center">Panjang</th>
                                    <th class="text-center">Lebar</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>                
</div>

<script src="{!! asset('theme/assets/libs/jquery/dist/jquery.min.js') !!}"></script>
<script src="{!! asset('theme/assets/extra-libs/DataTables/datatables.min.js') !!}"></script>
<script type="text/javascript">
var uri='{{URL::to('/')}}';
$(document).ready(function() {
    $('#product_list').DataTable( {
        "processing": true,
        "serverSide": true,
        "ajax": "{{ URL::to('master_product/list') }}",
        "columns": [
            {"data": "id"},
            {"data": "item"},
            {"data": "kavling", "render" : function(data, type, row){
                return row.kavling != null ? row.kavling.name : '-';
            }},
            {"data": "series"},
            {"data": "panjang"},
            {"data": "lebar"},
            {"data": "description"},
            {"data": "price", "render": function(data, type, row){return formatRupiah(row.price)}},
            {"data": "installation_fee", "render": function(data, type, row){return formatRupiah(row.installation_fee)}},
            {"data": "m_unit_name"},
            {"data": "amount_set"},
            {"data": "is_active", "render": function(data, type, row){return row.is_active == 0 ? 'non aktif' : 'aktif'}},
            {"data": "image"},
            {"data": "action"}
        ],
    } );
});
</script>
<script>

listSatuan = [];

$(document).ready(async function(){
    await $.ajax({
        type: "GET",
        url: "{{ URL::to('master_satuan/list') }}", //json get site
        dataType : 'json',
        success: function(response){
            arrData = response['data'];
            listSatuan = arrData;
        }
    });

    // console.log(arrProductPembelianRutin);
    t = $('#zero_config1').DataTable();
    t.clear().draw(false);
    $.ajax({
            type: "GET",
            url: "{{ URL::to('master_product/list') }}", //json get site
            dataType : 'json',
            success: function(response){
                arrData = response['data'];
                for(i = 0; i < arrData.length; i++){
                    urlEdit = "{{ URL::to('master_product/edit') }}" + "/" +arrData[i]['id'];
                    urlDelete = "{{ URL::to('master_product/delete') }}" + "/" +arrData[i]['id'];
                    satuan = '';
                    for(j = 0; j < listSatuan.length; j++) {
                        if(listSatuan[j]['id'] == arrData[i]['m_unit_id'])
                            satuan = listSatuan[j]['name'];
                    }
                    
                    t.row.add([
                        '<div class="text-center">'+arrData[i]['']+'</div>',
                        '<div class="text-left">'+arrData[i]['name']+'</div>',
                        '<div class="text-right">'+arrData[i]['late_time']+'</div>',
                        '<div class="text-right">'+satuan+'</div>',
                        '<div class="text-center"><a href="'+urlEdit+'" class="btn waves-effect waves-light btn-xs btn-warning"><i class="fas fa-pencil-alt"></i></a> <a href="'+urlDelete+'" class="btn waves-effect waves-light btn-xs btn-danger" onclick="return confirm("Are you sure to delete item?")"><i class="fas fa-trash-alt"></i></a></div>'
                    ]).draw(false);
                }
            }
    });
});
function formatRupiah(angka, prefix)
{
    if(angka == 'NaN' || angka === null){
        return 0;
    }
    var reverse = angka.toString().split('').reverse().join(''),
    ribuan = reverse.match(/\d{1,3}/g);
    ribuan = ribuan.join('.').split('').reverse().join('');
    return ribuan;
}
</script>

@endsection