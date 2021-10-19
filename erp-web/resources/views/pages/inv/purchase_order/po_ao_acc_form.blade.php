@extends('theme.default')

@section('breadcrumb')
<div class="page-breadcrumb">
    <div class="row">
        <div class="col-5 align-self-center">
            <h4 class="page-title">PO Autorisasi</h4>
            <div class="d-flex align-items-center">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ URL::to('po_ao') }}">PO Autorisasi</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Form PO Autorisasi</li>
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
        <div class="col-12">
            <form method="POST" action="{{ URL::to('po_konstruksi/approve') }}" class="form-horizontal r-separator">
                @csrf
                <input type="hidden" name="purchase_id" value="{{ $purchase['id'] }}" />
                <div class="text-right">
                    <a href="{{ URL::to('po_konstruksi/po_ao') }}"><button type="button" class="btn btn-danger btn-sm mb-2">Cancel</button></a>
                    <button type="submit" class="btn btn-primary btn-sm mb-2">Simpan</button>
                </div>
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Form Autorisasi</h4>
                    </div>
                    <hr>
                    <div class="card-body">
                        <h4 class="card-title">Data Purchase Order</h4>
                        <div class="form-group row align-items-center mb-0">
                            <label class="col-sm-3 text-right control-label col-form-label">PO Number</label>
                            <div class="col-9 border-left pb-2 pt-2">
                                <label class="control-label col-form-label">{{ $purchase['no'] }}</label>
                                <input type="hidden" value="{{ $purchase['wop'] }}" name="wop">
                                <input type="hidden" value="{{ $purchase['m_supplier_id'] }}" name="m_supplier_id">
                            </div>
                        </div>
                        <div class="form-group row align-items-center mb-0">
                            <label class="col-sm-3 text-right control-label col-form-label">Nomor SPK</label>
                            <div class="col-9 border-left pb-2 pt-2">
                                <input type="text" class="form-control" name="spk_number" id="spk_number" value="{{ $purchase['spk_number'] }}">
                            </div>
                        </div>
                        <div class="form-group row align-items-center mb-0">
                            <label class="col-sm-3 text-right control-label col-form-label">PO Date</label>
                            <div class="col-9 border-left pb-2 pt-2">
                                <label class="control-label col-form-label">{{ $purchase['purchase_date'] }}</label>
                            </div>
                        </div>
                        <div class="form-group row align-items-center mb-0">
                            <label class="col-sm-3 text-right control-label col-form-label">Supplier</label>
                            <div class="col-9 border-left pb-2 pt-2">
                                <label class="control-label col-form-label">{{ $purchase['m_suppliers']['name'] }}</label>
                            </div>
                        </div>
                        <div class="form-group row mb-0">
                            <label class="col-sm-3 text-right control-label col-form-label">PO Sudah di ACC</label>
                            <div class="col-9 border-left pb-2 pt-2">
                                <input type="checkbox" class="" name="acc" id="acc" value="1" @if ($purchase['signature_holding']=='' || $purchase['signature_supplier']=='' ) disabled @endif>
                            </div>
                        </div>
                        <div class="form-group row align-items-center mb-0">
                            <label class="col-sm-3 text-right control-label col-form-label">Diskon</label>
                            <div class="col-9 border-left pb-2 pt-2">
                                <input type="text" class="form-control" name="diskon" id="diskon" placeholder="0" onchange="cekDiskonPrice()" value="{{ $purchase['discount'] }}">
                            </div>
                        </div>
                        <div class="form-group row align-items-center mb-0">
                            <label class="col-sm-3 text-right control-label col-form-label">Tipe Diskon</label>
                            <div class="col-9 border-left pb-2 pt-2">
                                <select id="discount_type" name="discount_type" required class="form-control select2" style="width: 100%; height:32px;" onchange="cekDiskonPrice()">
                                    <option value="percentage" {{ $purchase['discount_type'] == 'percentage' ? 'selected' : '' }}>Persen</option>
                                    <option value="fixed" {{ $purchase['discount_type'] == 'fixed' ? 'selected' : '' }}>Tetap</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row align-items-center mb-0" {{$role == 6 ? 'hidden' : ''}}>
                            <label class="col-sm-3 text-right control-label col-form-label">Signature Director</label>
                            <div class="col-9 border-left pb-2 pt-2">
                                <div class="row">
                                    <div class="col-sm-6">
                                        <input type="text" class="form-control" id="code_director" id="code_director" maxlength="6" placeholder="Kode">
                                    </div>
                                    <div class="col-sm-6">
                                        <button class="btn btn-success" type="button" onclick="saveSignatureDirector()">Input</button>
                                    </div>
                                </div>
                                
                                <input type="hidden" value="{{$purchase['signature_holding']}}" name="signature_holding">
                                @if ($purchase['signature_holding'] != '')
                                <img src="{{ env('API_URL') . $purchase['signature_holding'] }}" height=250 width=300 />
                                @endif
                                <button type="button" data-toggle="modal" data-target="#modalShowSignature1" class="btn btn-success btn-sm">{{ $purchase['signature_holding'] != '' ? 'Ganti' : 'Tambah' }} Tanda Tangan</button>
                            </div>
                        </div>
                        <div class="form-group row align-items-center mb-0" {{$role == 2 ? 'hidden' : ''}}>
                            <label class="col-sm-3 text-right control-label col-form-label">Signature Accounting</label>
                            <div class="col-9 border-left pb-2 pt-2">
                                <div class="row">
                                    <div class="col-sm-6">
                                        <input type="text" class="form-control" id="code_manager" id="code_manager" maxlength="6" placeholder="Kode">
                                    </div>
                                    <div class="col-sm-6">
                                        <button class="btn btn-success" type="button" onclick="saveSignatureManager()">Input</button>
                                    </div>
                                </div>
                                <input type="hidden" value="{{$purchase['signature_supplier']}}" name="signature_supplier">
                                @if ($purchase['signature_supplier'] != '')
                                <img src="{{ env('API_URL') . $purchase['signature_supplier'] }}" height=250 width=300 />
                                @endif
                                <button type="button" data-toggle="modal" data-target="#modalShowSignature2" class="btn btn-success btn-sm">{{ $purchase['signature_supplier'] != '' ? 'Ganti' : 'Tambah' }} Tanda Tangan</button>
                            </div>
                        </div>
                        <div class="form-group row align-items-center mb-0">
                            <label class="col-sm-3 text-right control-label col-form-label">Catatan</label>
                            <div class="col-9 border-left pb-2 pt-2">
                                <label class="control-label col-form-label">{{ $purchase['notes'] }}</label>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div id="purchase_d_id"></div>
                    <div class="card-body">
                        <h4 class="card-title">List Material</h4>
                        <!-- <button type="button" id="delRow" class="btn btn-danger btn-sm mb-2"><i class="ti-trash"></i>&nbsp; Delete Selected Detail</button> -->
                        <div class="table-responsive">
                            <table id="list_material" class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th class="text-center">Nama Material</th>
                                        <th class="text-center">Volume</th>
                                        <th class="text-center">Satuan</th>
                                        <th class="text-center">Perkiraan Harga</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                        <br>
                        <div class="float-right">
                            <label for="">Total Bayar :</label>
                            <input type="text" readonly name="total_bayar" id="total_bayar" class="form-control">
                        </div>
                    </div>
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

<div class="modal fade" id="modalShowSignature2" tabindex="-1" role="dialog" aria-labelledby="modalShowDetailLabel1">
    <div class="modal-dialog  modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="modalAddMaterialLabel1">Tanda Tangan Digital</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <div id="signature-pad2" class="jay-signature-pad">
                    <div class="jay-signature-pad--body text-center">
                        <canvas id="canvas-signature-pad-2" height=250 width=280></canvas>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger btn-sm" id="clear2">Clear</button>
                <button type="button" class="btn btn-primary btn-sm" id="save-signature2">Simpan</button>
            </div>
        </div>
    </div>
</div>

<script src="{!! asset('theme/assets/libs/jquery/dist/jquery.min.js') !!}"></script>
<!-- <script src="{!! asset('theme/assets/extra-libs/DataTables/datatables.min.js') !!}"></script> -->
<script src="https://cdn.jsdelivr.net/npm/signature_pad@2.3.2/dist/signature_pad.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/signature_pad/1.5.3/signature_pad.min.js"></script>
<script>
    // t = $('#zero_config').DataTable();

    // $('#zero_config tbody').on('click', 'tr', function() {
    //     if ($(this).hasClass('selected')) {
    //         $(this).removeClass('selected');
    //     } else {
    //         t.$('tr.selected').removeClass('selected');
    //         $(this).addClass('selected');
    //     }
    // });

    $('#delRow').click(function() {
        t.row('.selected').remove().draw(false);
        cekDiskonPrice();
    });

    $(document).ready(function() {
        // console.log(arrMaterialPembelianRutin);
        // t.clear().draw(false);
        $.ajax({
            type: "GET",
            url: "{{ URL::to('po_konstruksi/detail/' . $purchase['id']) }}", //json get site
            dataType: 'json',
            async: false,
            success: function(response) {
                arrData = response['data'];
                var pd_id = '';
                var table = null;
                for (i = 0; i < arrData.length; i++) {
                    console.log('askdjhkasjh')
                    // t.row.add([
                    //     '<div class="text-left"><input name="pd_id[]" type="hidden" value="'+arrData[i]['id']+'" /><input name="m_item_id[]" type="hidden" value="'+arrData[i]['m_item_id']+'" />'+arrData[i]['m_items']['name']+'</div>',
                    //     '<div class="text-right"><input name="volume[]" type="" onchange="cekDiskonPrice()" class="form-control text-right" value="'+parseFloat(arrData[i]['amount'])+'" /></div>',
                    //     '<div class="text-center">'+arrData[i]['m_units']['name']+'</div>',
                    //     '<div class="text-right"><input type="text" name="perkiraan_harga_suppl[]" class="form-control" onchange="cekDiskonPrice()" value="'+arrData[i]['price_before_discount']+'"><input type="hidden" class="form-control" name="harga_diskon[]" value="'+arrData[i]['base_price']+'"></div>'
                    // ]).draw(false);
                    var tdAdd = '<tr>' +
                        '<td>' +
                        '<input name="pd_id[]" type="hidden" value="' + arrData[i]['id'] + '" /><input name="m_item_id[]" type="hidden" value="' + arrData[i]['m_item_id'] + '" />' + arrData[i]['m_items']['name'] + '' +
                        '</td>' +
                        '<td>' +
                        '<input name="volume[]" type="" onchange="cekDiskonPrice()" class="form-control text-right" value="' + parseFloat(arrData[i]['amount']) + '" />' +
                        '</td>' +
                        '<td>' +
                        '<div class="text-center">' + arrData[i]['m_units']['name'] + '</div>' +
                        '</td>' +
                        '<td>' +
                        '<div class="text-right"><input type="text" name="perkiraan_harga_suppl[]" class="form-control" onchange="cekDiskonPrice()" value="' + arrData[i]['price_before_discount'] + '"><input type="hidden" class="form-control" name="harga_diskon[]" value="' + arrData[i]['base_price'] + '"></div>' +
                        '</td>' +
                        '<td class="text-center"><button class="btn btn-danger removeOption"><i class="mdi mdi-delete"></i></button></td>' +
                        '</tr>';
                    $('#list_material').find('tbody:last').append(tdAdd);
                    pd_id += '<input type="hidden" value="' + arrData[i]['id'] + '" name="purchase_d_id[]">';
                }

                $('#purchase_d_id').html(pd_id);
            }
        });
        cekDiskonPrice();
    });
    $("#list_material").on("click", ".removeOption", function(event) {
        event.preventDefault();
        $(this).closest("tr").remove();
        cekDiskonPrice();
    });
    var wrapper1 = document.getElementById("signature-pad1");
    var clearButton1 = document.getElementById("clear1");
    var saveSignatureButton1 = document.getElementById("save-signature1");
    var canvas1 = wrapper1.querySelector("canvas");
    var signaturePad1 = new SignaturePad(canvas1, {
        backgroundColor: 'rgb(255, 255, 255)'
    });

    var wrapper2 = document.getElementById("signature-pad2");
    var clearButton2 = document.getElementById("clear2");
    var saveSignatureButton2 = document.getElementById("save-signature2");
    var canvas2 = wrapper2.querySelector("canvas");
    var signaturePad2 = new SignaturePad(canvas2, {
        backgroundColor: 'rgb(255, 255, 255)'
    });

    clearButton1.addEventListener("click", function(event) {
        signaturePad1.clear();
    });
    saveSignatureButton1.addEventListener("click", function(event) {
        if (signaturePad1.isEmpty()) {
            alert("Silahkan tanda tangan terlebih dahulu.");
        } else {
            const dataURL = signaturePad1.toDataURL();
            // download(dataURL, "signature.png");
            CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
            $.ajax({
                type: 'POST',
                url: "{{ URL::to('po_konstruksi/acc_ao_form/signature_holding/' . $purchase['id']) }}",
                dataType: 'json',
                data: {
                    _token: CSRF_TOKEN,
                    file: dataURL
                },
                success: function(data) {
                    console.log("success");
                    console.log(data);

                    $('#modalShowSignature1').modal('hide');

                    location.reload();

                    // purchaseById();
                },
                error: function(data) {
                    console.log("error");
                    console.log(data);
                }
            });
        }
    });

    clearButton2.addEventListener("click", function(event) {
        signaturePad2.clear();
    });
    saveSignatureButton2.addEventListener("click", function(event) {
        if (signaturePad2.isEmpty()) {
            alert("Silahkan tanda tangan terlebih dahulu.");
        } else {
            const dataURL = signaturePad2.toDataURL();
            // download(dataURL, "signature.png");
            CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
            $.ajax({
                type: 'POST',
                url: "{{ URL::to('po_konstruksi/acc_ao_form/signature_supplier/' . $purchase['id']) }}",
                dataType: 'json',
                data: {
                    _token: CSRF_TOKEN,
                    file: dataURL
                },
                success: function(data) {
                    console.log("success");
                    console.log(data);

                    $('#modalShowSignature2').modal('hide');

                    location.reload();

                    // purchaseById();
                },
                error: function(data) {
                    console.log("error");
                    console.log(data);
                }
            });
        }
    });

    function cekDiskonPrice() {
        var item = $('[name^=m_item_id');
        var perkiraan_harga_suppl = $('[name^=perkiraan_harga_suppl');
        var volume = $('[name^=volume');
        var diskon_price = $('[name^=harga_diskon');
        var diskon = $('#diskon').val() == '' ? 0 : $('#diskon').val();
        var discount_type = $('#discount_type').val();
        var total = 0,
            total_item = 0,
            total_bayar = 0;
        for (var i = 0; i < item.length; i++) {
            var m_item = item.eq(i).val();
            var harga = perkiraan_harga_suppl.eq(i).val();
            var amount = volume.eq(i).val();
            if (m_item != '' && harga != '' && amount != '') {
                total += (parseFloat(amount) * parseFloat(harga));
                total_item += parseFloat(amount);
            }
        }
        // var total_diskon = discount_type == 'percentage' ? (parseFloat(total) * (parseFloat(diskon) / 100)) / parseFloat(total_item) : parseFloat(diskon) / parseFloat(total_item);

        for (var i = 0; i < item.length; i++) {
            var m_item = item.eq(i).val();
            var harga = perkiraan_harga_suppl.eq(i).val() != '' ? perkiraan_harga_suppl.eq(i).val() : 0;
            var amount = volume.eq(i).val() != '' ? volume.eq(i).val() : 0;
            if (m_item != '' && harga != '' && amount != '') {
                var total_diskon=discount_type == 'percentage' ? parseFloat(harga)*(parseFloat(diskon)/100) : parseFloat(diskon)/parseFloat(total_item);
                var item_price_diskon = parseFloat(harga) - parseFloat(total_diskon);
                diskon_price.eq(i).val(item_price_diskon);
                total_bayar += (parseFloat(amount) * parseFloat(item_price_diskon));
            } else {
                diskon_price.eq(i).val(harga);
                total_bayar += (parseFloat(amount) * parseFloat(harga));
            }

        }
        $('#total_bayar').val(total_bayar)
    }

    function purchaseById() {
        $.ajax({
            type: "GET",
            url: "{{ URL::to('po_konstruksi/purchase_by_id/' . $purchase['id']) }}", //json get site
            dataType: 'json',
            success: function(response) {
                arrData = response['data'];
            }
        });
    }
    function saveSignatureDirector(){
        var code=$('#code_director').val();
        CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
        $.ajax({
            type: "POST",
            url: "{{ URL::to('po_konstruksi/saveSignatureDirector/' . $purchase['id']) }}", //json get site
            dataType: 'json',
            data: {
                _token: CSRF_TOKEN,
                code : code
            },
            success: function(response) {
                if (response == 1) {
                    location.reload();
                }else{
                    alert('Kode Tidak Ditemukan')
                }
            }
        });
    }
    function saveSignatureManager(){
        var code=$('#code_manager').val();
        CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
        $.ajax({
            type: "POST",
            url: "{{ URL::to('po_konstruksi/saveSignatureManager/' . $purchase['id']) }}", //json get site
            dataType: 'json',
            data: {
                _token: CSRF_TOKEN,
                code : code
            },
            success: function(response) {
                if (response == 1) {
                    location.reload();
                }else{
                    alert('Kode Tidak Ditemukan')
                }
            }
        });
    }
</script>

@endsection