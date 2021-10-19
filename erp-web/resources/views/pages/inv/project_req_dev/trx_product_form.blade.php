@extends('theme.default')

@section('breadcrumb')
    <div class="page-breadcrumb">
        <div class="row">
            <div class="col-5 align-self-center">
                <h4 class="page-title">Tambah Penyerahan Produk</h4>
                <div class="d-flex align-items-center">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ URL::to('order') }}">Penyerahan Produk</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Tambah</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>
@endsection


@section('content')
<form method="POST" action="{{ URL::to('project_req_dev/save_trx_product') }}" class="form-horizontal">
    @csrf
    <div class="container-fluid">
        <!-- basic table -->
        <br><br>
        <div class="row">
            
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Penyerahan Produk</h4>
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>Customer</label>
                                    <select id="customer_id" name="customer_id" required class="form-control select2 custom-select" style="width: 100%; height:32px;"  onchange="getRequest(this)">
                                        <option value="">--- Pilih Customer ---</option>
                                        @foreach($customer as $value)
                                        <option value="{{$value['id']}}">{{$value['coorporate_name']}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>Nomor Permintaan</label>
                                    <select id="project_req_id" name="project_req_id" required class="form-control select2 custom-select" style="width: 100%; height:32px;" onchange="getLabel(this)">
                                        <option value="">--- Pilih Permintaan ---</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>Due Date</label>
                                    <input type="date" class="form-control" name="due_date" required>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>No Surat Jalan</label>
                                    <input type="" class="form-control" name="no_surat_jalan" required>
                                </div>
                            </div>
                            <!-- <div class="col-sm-4">
                                <div class="form-group">
                                    <label>Biaya Jasa Pemasangan(per produk)</label>
                                    <input type="number" class="form-control" name="installation_fee" required>
                                </div>
                            </div>
                            <div class="col-sm-2">
                            <br><br>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" id="inlineCheckbox1" name="is_out_source" value="1">
                                    <label class="form-check-label" for="inlineCheckbox1">Jasa Out Sourcing</label>
                                </div>
                            </div> -->
                        </div>
                        <br><br>
                        <!-- <div class="table-responsive">
                            <h4 class="card-title">Tambahkan Material</h4>
                            <button onclick="addProductOrder()" type="button" class="btn btn-info">tambah</button>
                            <br><br>
                            <table class="table table-bordered table-striped" id="detail-order">
                                <thead>
                                        <tr>
                                            <th class="text-center">Material No</th>
                                            <th class="text-center">Material Name</th>
                                            <th class="text-center">Gudang</th>
                                            <th class="text-center">Stok</th>
                                            <th class="text-center">Volume</th>
                                            <th class="text-center">Satuan</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                <tbody>
                                    
                                </tbody>
                            </table>
                        </div>
                        <br><br> -->
                        <h4 class="card-title">Pilih Produk</h4>
                        <table class="table table-bordered table-striped" id="detail-label">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Label</th>
                                    <!-- <th>Harga</th> -->
                                    <th><input type="checkbox" name="select-all" id="select-all" /></th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="2"  class="text-center">Total<input type="hidden" id="total_price" name="total_price" value="0"></th>
                                    <!-- <th id="total" class="text-right"></th> -->
                                    <th id="total_prod"></th>
                                </tr>
                            </tfoot>
                        </table>
                        <div class="form-group">
                            <button class="btn btn-success">Simpan</button>
                        </div>
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
var arrMaterial = [];
var warehouse=[];
var listStockSite = [];
$(document).ready(function(){
    $.ajax({
        type: "GET",
        url: "{{ URL::to('inventory/stok_json') }}", //json get site
        dataType : 'json',
        async : false,
        success: function(response){
            listStockSite = response['data'];
        }
    });
    $.ajax({
        type: "GET",
        url: "{{ URL::to('material_request/get_material') }}", //json get material
        dataType : 'json',
        async : false,
        success: function(response){
            arrMaterial = response['data'];
        }
    });
    $.ajax({
            type: "GET",
            url: "{{ URL::to('master_warehouse/get_warehouse_by_site/'.$site_id) }}", //json get site
            dataType : 'json',
            async : false,
            success: function(response){
                arrData = response['data'];
                warehouse = arrData;
            }
    });
    // Listen for click on toggle checkbox
    $('#select-all').click(function(event) {   
        if(this.checked) {
            // Iterate each checkbox
            $('[id^=check_prod_sub_id]').each(function() {
                this.checked = true;        
                cekChecked();                
            });
        } else {
            $('[id^=check_prod_sub_id]').each(function() {
                this.checked = false;                       
                cekChecked();
            });
        }
    });
});

function getRequest(eq){
    $('#detail-label > tbody').empty();
    var id=eq.value;
    formReq = $('[id^=project_req_id]');
    formReq.empty();
    formReq.append('<option value="">-- Pilih Permintaan --</option>');
    $.ajax({
        type: "GET",
        url: "{{ URL::to('project_req_dev/get_prd_by_cust') }}"+'/'+id, //json get site
        dataType : 'json',
        success: function(response){
            arrData = response['data'];
            for(i = 0; i < arrData.length; i++){
                formReq.append('<option value="'+arrData[i]['id']+'">'+arrData[i]['no']+'</option>');
            }
        }
    });
    
}

function getLabel(obj){
    $('#detail-label > tbody').empty();
    $.ajax({
        type: "GET",
        url: "{{ URL::to('project_req_dev/get_inv_product_list/') }}"+'/'+obj.value, //json get site
        dataType : 'json',
        success: function(response){
            arrData=response['data'];
            for(i = 0; i < arrData.length; i++){
                var tdAdd='<tr>'+
                                '<td>'+arrData[i]['item']+' '+arrData[i]['name']+' Series : '+arrData[i]['series']+'</td>'+
                                '<td><input type="hidden" name="label[]" value="'+arrData[i]['no']+'"><input type="hidden" name="inv_request_prod_id[]" value="'+arrData[i]['inv_request_prod_id']+'"><input type="hidden" name="dev_project_label_id[]" value="'+arrData[i]['dev_project_label_id']+'"><input type="hidden" name="product_sub_id[]" value="'+arrData[i]['id']+'">'+arrData[i]['no']+'</td>'+
                                // '<td class="text-right"><input type="hidden" name="price[]" id="price[]" value="'+arrData[i]['price']+'">'+formatRupiah(arrData[i]['price'])+'</td>'+
                                '<td><input type="hidden" name="price[]" id="price[]" value="'+arrData[i]['price']+'"><input type="checkbox" id="check_prod_sub_id[]" name="check_prod_sub_id[]" value="'+arrData[i]['dev_project_label_id']+'" onclick="cekChecked()"></td>'+
                            '</tr>';
                $('#detail-label').find('tbody:last').append(tdAdd);
            }
        }
    });
}
function cekChecked(){
    var cb = $('[id^=check_prod_sub_id]');
    var price = $('[id^=price]');
    var total=0;
    var total_price=0;
    for(i = 0; i < cb.length; i++){
        if(cb.eq(i).prop('checked') === true){
            total++;
            total_price+=parseFloat(price.eq(i).val());
        }
    }
    $('#total_prod').html(total);
    // $('#total').html(formatRupiah(total_price));
    $('#total_price').val(total_price);
    if(total == cb.length){
        $('#select-all').prop('checked', true);
    }else{
        $('#select-all').prop('checked', false);
    }
}
function cekTotal(obj){
    var total=$('#total_use').val();
    if(obj.value > total){
        alert('tidak boleh melebihi dari '+total);
        obj.value=0;
    }
}
function formatRupiah(angka, prefix)
{
    var reverse = angka.toString().split('').reverse().join(''),
    ribuan = reverse.match(/\d{1,3}/g);
    ribuan = ribuan.join('.').split('').reverse().join('');
    return ribuan;
}
function addProductOrder(){
    var option='<option value="">Pilih Material</option>';
    
    for (var i = 0; i < arrMaterial.length; i++) {
        option+='<option value="'+arrMaterial[i]['id']+'">'+arrMaterial[i]['name']+'</option>';
    }
    var option_warehouse='<option value="">Pilih Gudang</option>';
    for (var i = 0; i < warehouse.length; i++) {
        option_warehouse+='<option value="'+warehouse[i]['id']+'">'+warehouse[i]['name']+'</option>';
    }
    var tdAdd='<tr>'+
        '<td><input type="" id="m_item_no[]" name="m_item_no[]" readonly class="form-control"/></td>'+
        '<td><select id="m_item_id[]" name="m_item_id[]" class="form-control" onchange="cekItem()">'+option+'</select></td>'+
        '<td><select id="m_warehouse_id[]" required name="m_warehouse_id[]" onchange="cekItemStok()" class="form-control">'+option_warehouse+'</select></td>'+
        '<td><input id="stok[]" name="stok[]" class="form-control text-right" type="text" readonly/></td>'+
        '<td><input id="amount[]" required name="amount[]" class="form-control text-right" type="number"/></td>'+
        '<td><input type="hidden" id="m_unit_id[]" name="m_unit_id[]" /><input class="form-control" type="" id="m_unit_name[]" name="m_unit_name[]" readonly /></td>'+
        '<td><button type="button" class="btn btn-sm btn-danger removeOption"><i class="mdi mdi-delete"></i></button></td>'+
    '</tr>';
    $('#detail-order').find('tbody:last').append(tdAdd);
    // console.log(total_produk);
}
$("#detail-order").on("click", ".removeOption", function(event) {
    event.preventDefault();
    $(this).closest("tr").remove();
});
function cekItem(){
    var id = $('[id^=m_item_id]');
    var stok = $('[id^=stok]');
    var item_no = $('[id^=m_item_no]');
    var warehouse_id = $('[id^=m_warehouse_id]');
    var unit_id = $('[id^=m_unit_id]');
    var unit_name = $('[id^=m_unit_name]');
    for(var i = 0; i < id.length; i++){
        var m_item_id=id.eq(i).val();
        var m_item_no='', m_unit_id='', m_unit_name='';
        arrMaterial.map((item, obj) => {
            if (item.id == m_item_id){
                m_item_no=item.no;
                m_unit_id=item.m_unit_id;
                m_unit_name=item.m_unit_name;
            }
        });
        item_no.eq(i).val(m_item_no);
        unit_id.eq(i).val(m_unit_id);
        unit_name.eq(i).val(m_unit_name);
    }
    cekItemStok();
}
function cekItemStok(){
    var id = $('[id^=m_item_id]');
    var stok = $('[id^=stok]');
    var item_no = $('[id^=m_item_no]');
    var warehouse_id = $('[id^=m_warehouse_id]');
    for(var i = 0; i < id.length; i++){
        var m_item_id=id.eq(i).val();
        var amount_stok=0;
        var m_warehouse_id=warehouse_id.eq(i).val();
        listStockSite.map((item, obj) => {
            if (item.m_item_id == m_item_id && item.m_warehouse_id == m_warehouse_id){
                amount_stok=item.stok;
            }
        });
        
        stok.eq(i).val(amount_stok);
    }
}

</script>
@endsection