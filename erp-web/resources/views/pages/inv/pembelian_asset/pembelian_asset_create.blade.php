@extends('theme.default')

@section('breadcrumb')
            <div class="page-breadcrumb">
                <div class="row">
                    <div class="col-5 align-self-center">
                        <h4 class="page-title">Purchase</h4>
                        <div class="d-flex align-items-center">
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="{{ URL::to('pembelian') }}">Purchase Asset</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">Input Purchase</li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
@endsection

@section('content')
<style>
 canvas {
    box-shadow: 0 3px 20px rgba(0, 0, 0, 0.27), 0 0 40px rgba(0, 0, 0, 0.08) inset;
    border-radius: 15px;
 }
</style>
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
                        <form method="POST" action="{{ URL::to('pembelian_asset/create') }}" class="form-horizontal" id="form-po">
                        @csrf
                            <div class="card">
                                <div class="card-body">
                                    <h4 class="card-title">Purchase Asset Form</h4>
                                    <!-- <div>
                                        <button type="button" id="addRow" class="btn btn-success btn-sm mb-2"><i class="fas fa-plus"></i>&nbsp; Add Manual Purchases</button>
                                    </div> -->
                                    <br/>
                                    <div class="form-group row">
                                        <label class="col-sm-3 text-right control-label col-form-label">Nomor SPK</label>
                                        <div class="col-sm-9">
                                            <input id="spk_number" name="spk_number" required class="form-control">
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-sm-3 text-right control-label col-form-label">Supplier</label>
                                        <div class="col-sm-9">
                                            <select id="suppl_single" name="suppl_single" required class="form-control select2 custom-select" style="width: 100%; height:32px;"></select>
                                        </div>
                                    </div>
                                    <!-- <div class="form-group row">
                                        <label class="col-sm-3 text-right control-label col-form-label">Payment Method</label>
                                        <div class="col-sm-9">
                                            <select id="cara_bayar_single" name="cara_bayar_single" required class="form-control select2 custom-select" style="width: 100%; height:32px;">
                                                <option value="">-- Choose Payment Method --</option>
                                                <option value="cash">Cash</option><option value="credit">Credit</option>
                                            </select>
                                        </div>
                                    </div> -->
                                    <div class="form-group row">
                                        <label class="col-sm-3 text-right control-label col-form-label">Tanda Tangan Peminta</label>
                                        <div class="col-sm-9 text-left">
                                        <div class="row">
                                            <div class="col-sm-6">
                                                <input type="text" class="form-control" id="code_request" id="code_request" maxlength="6" placeholder="Kode">
                                            </div>
                                            <div class="col-sm-6">
                                                <button class="btn btn-success" type="button" onclick="saveSignatureRequest()">Input</button>
                                            </div>
                                        </div>
                                        <div id="img_signature"></div>
                                        <input style="display:none" name="signature_request" id="signature_request" class="form-control">
                                        <button type="button" id="btn-signature" data-toggle="modal" data-target="#modalShowSignature1" class="btn btn-success btn-sm">Tanda Tangan</button>
                                        </div>
                                    </div>
                                    <div class="form-group row" hidden>
                                        <label class="col-sm-3 text-right control-label col-form-label">PO dengan AO</label>
                                        <div class="col-sm-9 text-left">
                                        <div class="form-check form-check-inline">
                                            <input class="form-control" style="width:20px;" type="checkbox" name="with_ao" id="inlineCheckbox1" value="1" checked>
                                        </div>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-sm-3 text-right control-label col-form-label">Price Include PPN</label>
                                        <div class="col-sm-9 text-left">
                                        <div class="form-check form-check-inline">
                                            <input class="form-control" style="width:20px;" type="checkbox" name="with_ppn" id="inlineCheckbox1" value="1">
                                        </div>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-sm-3 text-right control-label col-form-label">Harga Item Tanpa PPN</label>
                                        <div class="col-sm-9 text-left">
                                        <div class="form-check form-check-inline">
                                            <input class="form-control" style="width:20px;" type="checkbox" name="without_ppn" id="inlineCheckbox2" value="1">
                                        </div>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-sm-3 text-right control-label col-form-label">Diskon</label>
                                        <div class="col-sm-5">
                                            <input type="text" class="form-control" name="diskon" id="diskon" placeholder="0" onchange="cekDiskonPrice()">
                                        </div>
                                        <div class="col-sm-4">
                                            <select id="discount_type" name="discount_type" required class="form-control select2" style="width: 100%; height:32px;" onchange="cekDiskonPrice()">
                                                <option value="percentage">Persen</option>
                                                <option value="fixed">Tetap</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-sm-3 text-right control-label col-form-label">Tanggal Pengiriman</label>
                                        <div class="col-sm-9">
                                            <input type="date" required class="form-control" name="delivery_date" id="delivery_date">
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-sm-3 text-right control-label col-form-label">Lama Kredit</label>
                                        <div class="col-sm-9 text-left">
                                        <input type="number" class="form-control" id="credit_age" name="credit_age" placeholder="0">
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-sm-3 text-right control-label col-form-label">Catatan</label>
                                        <div class="col-sm-9 text-left">
                                        <textarea name="catatan" id="" class="form-control"></textarea>
                                        </div>
                                    </div>
                                    <br/>
                                    <div class="form-group">
                                        <select class="form-control custom-select select2" style="width: 300px; height:32px;" id="item_id"></select>
                                        <button type="button" id="addRow" class="btn btn-success btn-sm mb-2"><i class="fas fa-plus"></i>&nbsp; Add Manual Purchases</button>
                                    </div>
                                    <!-- <button type="button" id="delRow" class="btn btn-danger btn-sm mb-2"><i class="ti-trash"></i>&nbsp; Delete Selected Detail</button> -->
                                    <div class="table-responsive">
                                        <table id="dt_temp" class="table table-striped table-bordered">
                                            <thead>
                                                <tr>
                                                    <th class="text-center" style="min-width:150px">Material No</th>
                                                    <th class="text-center" style="min-width:300px;">Material Name</th>
                                                    <th class="text-center" style="min-width:100px">Volume</th>
                                                    <th class="text-center" style="min-width:60px">Stok Site</th>
                                                    <th class="text-center" style="min-width:60px">Satuan</th>
                                                    <th class="text-center" style="min-width:100px">Best Price/Supplier/Harga Terbaru</th>
                                                    <!-- <th class="text-center">Supplier</th> -->
                                                    <th class="text-center" style="min-width:150px">Harga Supplier</th>
                                                    <!-- <th class="text-center">Cara Bayar</th> -->
                                                    <!-- <th class="text-center" style="min-width:100px">Keterangan</th> -->
                                                    <th></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <th class="text-center" colspan="7">Total Item</th>
                                                    <th class="text-center"><p id="total_item"></p></th>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                    <br>
                                    <div class="float-right">
                                        <label for="">Ongkos Kirim :</label>
                                        <input type="text" name="delivery_fee" id="delivery_fee" class="form-control" required>
                                        <label for="">Total Bayar :</label>
                                        <input type="text" readonly name="total_bayar" id="total_bayar" class="form-control" style="height:50px; font-size:28px">
                                    </div>
                                    <br><br><br><br><br><br>
                                    <div class="text-left">
                                        <a href="{{ URL::to('pembelian') }}"><button class="btn btn-danger mb-2">Cancel</button></a>
                                        <button type="submit" class="btn btn-primary mb-2">Submit</button>
                                    </div>
                                </div>
                            </div>
                            <div id="selected_material">
                            </div>
                        </form>
                    </div>
                </div>
                
</div>
<div class="modal fade" id="modalShowSignature1" tabindex="-1" role="dialog" aria-labelledby="modalShowDetailLabel1">
    <div class="modal-dialog  modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="modalAddMaterialLabel1">Tanda Tangan Digital</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <div id="signature-pad1" class="jay-signature-pad">
                    <div class="jay-signature-pad--body text-center">
                        <canvas id="canvas-signature-pad-1" height=250 width=280></canvas>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger btn-sm" id="clear1">Clear</button>
                <button type="button" class="btn btn-primary btn-sm" id="save-signature1">Simpan</button>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/signature_pad@2.3.2/dist/signature_pad.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/signature_pad/1.5.3/signature_pad.min.js"></script>
<script src="{!! asset('theme/assets/libs/jquery/dist/jquery.min.js') !!}"></script>
<script src="{!! asset('theme/assets/extra-libs/DataTables/datatables.min.js') !!}"></script>
<script>
var wrapper1 = document.getElementById("signature-pad1");
var clearButton1 = document.getElementById("clear1");
var saveSignatureButton1 = document.getElementById("save-signature1");
var canvas1 = wrapper1.querySelector("canvas");
var signaturePad1 = new SignaturePad(canvas1, {
    backgroundColor: 'rgb(255, 255, 255)'
});
clearButton1.addEventListener("click", function (event) {
    signaturePad1.clear();
});
saveSignatureButton1.addEventListener("click", function (event) {
    if (signaturePad1.isEmpty()) {
        alert("Silahkan tanda tangan terlebih dahulu.");
    } else {
        const dataURL = signaturePad1.toDataURL();
        // download(dataURL, "signature.png");
        CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
        $.ajax({
            type:'POST',
            url: "{{ URL::to('pembelian/signature_request/') }}"+'/'+{{$index}}, 
            dataType : 'json',
            data: {
                _token: CSRF_TOKEN,
                file: dataURL
            },
            success:function(data){
                // console.log("success");
                // console.log(data);
                d = new Date();
                arrData=data['data'];
                $('#signature_request').val(arrData['path']);
                $('#img_signature').html('<img src="{{ env('API_URL') }}'+arrData['path']+'?'+d.getTime()+'" height=250 width=300 />');
                $('#modalShowSignature1').modal('hide');
                $('#btn-signature').html('Ganti Tanda Tangan')
            },
            error: function(data){
                console.log("error");
                console.log(data);
            }
        });
    }
});
// var dt_temp = $('#dt_temp').DataTable();
var arrMaterialPembelianRutin = [];
var arrPoCanceled = [];
var selected = [];
var arrSuppl = [];
var tempArrSelected = [];

// List Stock
var listStockSite = [];
var recomItem= [];

var arrMaterial = [];
$.ajax({
    type: "GET",
    url: "{{ URL::to('material_request/get_material_atk') }}", //json get material
    dataType : 'json',
    success: function(response){
        arrMaterial = response['data'];
        $('#item_id').append('<option selected value="">Pilih ATK / Alat Kerja</option>')
        $.each(arrMaterial, function(i, item) {
            $('#item_id').append('<option value="'+arrMaterial[i]['id']+'">('+arrMaterial[i]['no']+') '+arrMaterial[i]['name']+'</option>');
        });
    }
});
function getOccurrence(array, value) {
    var count = 0;
    array.forEach((v) => (v === value && count++));
    return count;
}
$('#dt_temp tbody').on('click', 'tr', function() {
    if ($(this).hasClass('selected')) {
        $(this).removeClass('selected');
    } else {
        dt_temp.$('tr.selected').removeClass('selected');
        $(this).addClass('selected');
    }
});

$('#delRow').click(function() {
    dt_temp.row('.selected').remove().draw(false);
    cekDiskonPrice();
});

$(document).ready(async function(){
    // Get Stock
    $.ajax({
        type: "GET",
        url: "{{ URL::to('inventory/stok_json_all') }}", //json get site
        dataType : 'json',
        success: function(response){
            arrData = response['data'];
            listStockSite = arrData;
        }
    });

    // console.log(arrMaterialPembelianRutin);
    t = $('#zero_config').DataTable();
    t.clear().draw(false);
    // Suggest order dari RAB
    
    $.ajax({
        type: "GET",
        url: "{{ URL::to('pembelian/supplier') }}", //json get site
        dataType : 'json',
        success: function(response){
            arrSuppl = response['data'];
            formSuppl = $('#suppl_single');
            formSuppl.empty();
            formSuppl.append('<option value="">-- Select Supplier --</option>');
            for(j = 0; j < arrSuppl.length; j++){
                formSuppl.append('<option value="'+arrSuppl[j]['id']+'">'+arrSuppl[j]['name']+'</option>');
            }
        }
    });
    

    countSuppl = $('[name^=suppl]').length;
    for(i = 0; i < countSuppl; i++) {
        formSuppl = $('[id^=suppl]').eq(i);
        selectedSuppl = $('[id^=suppl]').eq(i).val();
        formSuppl.empty();
        formSuppl.append('<option value="">-- Select Supplier --</option>');
        for(j = 0; j < arrSuppl.length; j++){
            if(selectedSuppl == arrSuppl[j]['id'])
                formSuppl.append('<option selected value="'+arrSuppl[j]['id']+'">'+arrSuppl[j]['name']+'</option>');
            else
                formSuppl.append('<option value="'+arrSuppl[j]['id']+'">'+arrSuppl[j]['name']+'</option>');
        }
    }

    $("#select_all").click(function(){
        $('input:checkbox').not(this).prop('checked', this.checked);
    });
});

function doPerkiraanHarga(){
    // document.getElementById("btn_add_to_selected").disabled = true;
    cekDiskonPrice();
    // alert('test');
}
function doSuppl(){
    // document.getElementById("btn_add_to_selected").disabled = true;
    // alert('test');
}
function doCaraBayar(){
    // document.getElementById("btn_add_to_selected").disabled = true;
    // alert('test');
}
function cekDiskonPrice(){
    var item=$('[name^=m_item_id');
    var perkiraan_harga_suppl=$('[name^=perkiraan_harga_suppl');
    var volume=$('[name^=volume');
    var diskon_price=$('[name^=harga_diskon');
    var diskon=$('#diskon').val() == '' ? 0 : $('#diskon').val();
    var discount_type=$('#discount_type').val();
    var total=0, total_item=0, total_bayar=0;
    for (var i = 0; i < item.length; i++) {
        var m_item=item.eq(i).val();
        var harga=perkiraan_harga_suppl.eq(i).val();
        var amount=volume.eq(i).val();
        if (m_item != '' && harga != '' && amount != '') {
            total+=(parseFloat(amount)*parseFloat(harga));
            total_item+=parseFloat(amount);
        }
    }
    // var total_diskon=discount_type == 'percentage' ? (parseFloat(total)*(parseFloat(diskon)/100))/parseFloat(total_item) : parseFloat(diskon)/parseFloat(total_item);
    // console.log(discount_type)
    // var total_item=0;
    for (var i = 0; i < item.length; i++) {
        var m_item=item.eq(i).val();
        var harga=perkiraan_harga_suppl.eq(i).val() != '' ? perkiraan_harga_suppl.eq(i).val() : 0;
        var amount=volume.eq(i).val() != '' ? volume.eq(i).val() : 0;
        if (m_item != '' && harga != '' && amount != '') {
            var total_diskon=discount_type == 'percentage' ? parseFloat(harga)*(parseFloat(diskon)/100) : parseFloat(diskon)/parseFloat(total_item);
            var item_price_diskon=parseFloat(harga) - parseFloat(total_diskon);
            diskon_price.eq(i).val(item_price_diskon);
            total_bayar+=(parseFloat(amount)*parseFloat(item_price_diskon));
        }else{
            diskon_price.eq(i).val(harga);
            total_bayar+=(parseFloat(amount)*parseFloat(harga));
        }
        // if(m_item != ''){
        //     total_item++;
        // }
        
    }
    $('#total_item').html(total_item)
    $('#total_bayar').val(formatCurrency(total_bayar.toFixed(0)))
}
var selectedItem=[];
// var dt_temp = $('#dt_temp').DataTable();
$('#addRow').on('click', function() {
    var item_selected=$('#item_id').val();
    stok = 0;
    var unit_id=0, unitName='', itemNo='', itemName='', name='';
    $.each(arrMaterial, function(i, item) {
        if(item_selected == arrMaterial[i]['id']) {
            unit_id = arrMaterial[i]['m_unit_id'];
            unitName = arrMaterial[i]['m_unit_name'];
            itemNo = arrMaterial[i]['no'];
            itemName = arrMaterial[i]['name'];
            name=arrMaterial[i]['best_prices'] != null ? formatCurrency(parseFloat(arrMaterial[i]['best_prices']['best_price']).toFixed(0)) +' / '+arrMaterial[i]['best_prices']['name'] +' / ' + formatCurrency(parseFloat(arrMaterial[i]['item_prices']).toFixed(0)) : '- / - / -';
            listStockSite.map((it, obj) => {
                if (it.m_item_id == arrMaterial[i]['id'])
                    stok = parseFloat(it.stok);
            });
        }
    });
    var is_there=false;
    $.each(selectedItem, function(i, item){
        if (item_selected == item) {
            is_there=true;
        }
    });
    if (is_there == false && item_selected != '') {
        var tdAdd='<tr>'+
            '<td>'+
                '<div class="text-center"><input type="hidden" id="m_item_no[]" name="m_item_no[]" class="form-control" onchange="handleMaterialNo(this)" value="'+itemNo+'" />'+itemNo+'</div>' +
            '</td>'+
            '<td>'+
                '<input name="m_item_id[]" type="hidden" value="'+item_selected+'" /><div class="text-left">'+itemName+'</div>' +
            '</td>'+
            '<td>'+
                '<input name="volume[]" onchange="cekDiskonPrice();" value="" type="number" class="form-control text-right" step="any" />' +
            '</td>'+
            '<td>'+
                '<input readonly id="stok_site[]" name="stok_site[]" type="number" value="'+ stok +'" class="form-control text-right" />' +
            '</td>'+
            '<td>'+
                '<input name="m_unit_id[]" type="hidden" value="'+unit_id+'" /><div class="text-center">'+unitName+'</div>' +
            '</td>'+
            '<td>'+
                '<div class="text-center"><p id="best_supplier[]">'+name+'</p></div>' +
            '</td>'+
            '<td>'+
                '<input id="perkiraan_harga_suppl[]" required onchange="doPerkiraanHarga();checkText(this.value);" name="perkiraan_harga_suppl[]" class="form-control text-right" value="" /><input id="harga_diskon[]" required  name="harga_diskon[]" class="form-control text-right" type="hidden"/>' +
                '<input id="notes[]" required name="notes[]" class="form-control text-left" type="hidden" value="" />' +
            '</td>'+
            '<td class="text-center"><button class="btn btn-danger removeOption"><i class="mdi mdi-delete"></i></button></td>'+
        '</tr>';
        $('#dt_temp').find('tbody:last').append(tdAdd);
    }
    // countMaterial = $('[name^=m_item_id]').length;
    // if(countMaterial < 5) {
        // var tdAdd='<tr>'+
        //             '<td>'+
        //                 '<input type="text" id="m_item_no[]" name="m_item_no[]" class="form-control" readonly onchange="handleMaterialNo(this)" />' +
        //             '</td>'+
        //             '<td>'+
        //                 '<select class="form-control custom-select" style="max-width: 400px; height:32px;" name="m_item_id[]" required onchange="handleMaterial()"></select>' +
        //             '</td>'+
        //             '<td>'+
        //                 '<input name="volume[]" onchange="cekDiskonPrice()"  type="number" class="form-control text-right" step="any" />' +
        //             '</td>'+
        //             '<td>'+
        //                 '<input readonly id="stok_site[]" name="stok_site[]" type="number" class="form-control text-right" />' +
        //             '</td>'+
        //             '<td>'+
        //                 '<input name="m_unit_id[]" type="hidden" /><input name="m_unit_name[]" class="form-control text-center" type="text" readonly />' +
        //             '</td>'+
        //             '<td>'+
        //                 '<div class="text-center"><p id="best_supplier[]"></p></div>' +
        //             '</td>'+
        //             '<td>'+
        //                 '<input id="perkiraan_harga_suppl[]" required onchange="doPerkiraanHarga();checkText(this.value);" name="perkiraan_harga_suppl[]" class="form-control text-right" /><input id="harga_diskon[]" required  name="harga_diskon[]" class="form-control text-right" type="hidden"/>' +
        //                 '<input id="notes[]" required name="notes[]" class="form-control text-left" type="hidden" value="-" />' +
        //             '</td>'+
        //             '<td class="text-center"><button class="btn btn-danger removeOption"><i class="mdi mdi-delete"></i></button></td>'+
        //         '</tr>';
        // $('#dt_temp').find('tbody:last').append(tdAdd);

        // $('.custom-select').select2();
        // eventSelectedMaterial();
    // }
    saveSelectedItem();
});

function saveSelectedItem(){
    selectedItem=[];
    var m_item_id = $('[name^=m_item_id]');
    for(i = 0; i < m_item_id.length; i++){
        var item=m_item_id.eq(i).val();
        if (item != '') {
            selectedItem.push(item);
        }
    }
}

$("#dt_temp").on("click", ".removeOption", function(event) {
    event.preventDefault();
    $(this).closest("tr").remove();
    cekDiskonPrice()
});
function checkText(v){
    // console.log(v);
    if (v.split('.').length > 2) {
        alert('format harga salah');
    }
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
        formUnit = $('[name^=m_unit_id]').eq(i);
        formUnitName = $('[name^=m_unit_name]').eq(i);
        formMaterialNo = $('[name^=m_item_no]').eq(i);
        formStokSite = $('[name^=stok_site]').eq(i);
        itemNo = '';
        stok = 0;
        var name='';
        $.each(arrMaterial, function(i, item) {
            if(selectedMaterial == arrMaterial[i]['id']) {
                formMaterial.append('<option selected value="'+arrMaterial[i]['id']+'">('+arrMaterial[i]['no']+') '+arrMaterial[i]['name']+'</option>');
                formUnit.val(arrMaterial[i]['m_unit_id']);
                formUnitName.val(arrMaterial[i]['m_unit_name']);
                itemNo = arrMaterial[i]['no'];
                name=arrMaterial[i]['best_prices'] != null ? formatCurrency(parseFloat(arrMaterial[i]['best_prices']['best_price']).toFixed(0)) +' / '+arrMaterial[i]['best_prices']['name'] +' / ' + formatCurrency(parseFloat(arrMaterial[i]['item_prices']).toFixed(0)) : '- / -';
                listStockSite.map((it, obj) => {
                    if (it.m_item_id == arrMaterial[i]['id'])
                        stok = parseFloat(it.stok);
                });
            } else {
                if (arrSelectedMaterial.includes(arrMaterial[i]['id'].toString()))
                    formMaterial.append('<option disabled value="'+arrMaterial[i]['id']+'">('+arrMaterial[i]['no']+') '+arrMaterial[i]['name']+'</option>');
                else 
                    formMaterial.append('<option value="'+arrMaterial[i]['id']+'">('+arrMaterial[i]['no']+') '+arrMaterial[i]['name']+'</option>');
            }
        });
        
        formMaterialNo.val(itemNo);
        formStokSite.val(stok);
        // var index=i;
        $('[id^=best_supplier]').eq(i).text(name);
       
        // $.ajax({
        //     type: "GET",
        //     url: "{{ URL::to('pembelian/best_prices') }}"+'/'+selectedMaterial, //json get material
        //     dataType : 'json',
        //     async : false,
        //     success: function(response){
        //         arrData = response['data'];
        //         name=arrData != null ? arrData['name'] : '';
        //         // console.log(i);
        //     }
        // });
        
    }
    
    // countSuppl = $('[name^=suppl]').length;
    // for(i = 0; i < countSuppl; i++) {
    //     formSuppl = $('[id^=suppl]').eq(i);
    //     selectedSuppl = $('[id^=suppl]').eq(i).val();
    //     formSuppl.empty();
    //     formSuppl.append('<option value="">-- Select Supplier --</option>');
    //     for(j = 0; j < arrSuppl.length; j++){
    //         if(selectedSuppl == arrSuppl[j]['id'])
    //             formSuppl.append('<option selected value="'+arrSuppl[j]['id']+'">'+arrSuppl[j]['name']+'</option>');
    //         else
    //             formSuppl.append('<option value="'+arrSuppl[j]['id']+'">'+arrSuppl[j]['name']+'</option>');
    //     }
    // }
}

function handleMaterial(){
    eventSelectedMaterial();
}

async function handleMaterialNo(obj) {
    countMaterial = $('[id^=m_item_no]').length;
    for(i = 0; i < countMaterial; i++){
        materialNo = $('[id^=m_item_no]').eq(i).val();
        formMaterialId = $('[name^=m_item_id]').eq(i);
        id = '';
        await $.ajax({
            type: "GET",
            url: "{{ URL::to('stok_opname/material_by_no') }}", //json get site
            dataType : 'json',
            data: {'no' : materialNo},
            success: function(response){
                arrData = response['data'];
                if(arrData.length > 0) {
                    id = arrData[0]['id'];
                } 
            }
        });

        formMaterialId.val(id);
    }

    handleMaterial();
}
$('#form-po').on('submit', function(event){
    var sr=$('#signature_request').val();
    if (sr == '') {
        event.preventDefault();
        alert('Harap Isi Tanda Tangan Dahulu')
    }
});
function saveSignatureRequest(){
    var code=$('#code_request').val();
    CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
    $.ajax({
        type: "POST",
        url: "{{ URL::to('po_konstruksi/signatureRequest') }}", //json get site
        dataType: 'json',
        data: {
            _token: CSRF_TOKEN,
            code : code
        },
        success: function(response) {
            arrData=response;
            d = new Date();
            if (response['status'] == 1) {
                $('#signature_request').val(arrData['data']['signature']);
                $('#img_signature').html('<img src="{{ env('API_URL') }}'+arrData['data']['signature']+'?'+d.getTime()+'" height=250 width=300 />');
            }else{
                alert('Kode tidak ditemukan')
            }
        }
    });
}
</script>

@endsection