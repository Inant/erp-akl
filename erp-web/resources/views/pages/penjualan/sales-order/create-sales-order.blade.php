@extends('theme.default')

@section('breadcrumb')
    <div class="page-breadcrumb">
        <div class="row">
            <div class="col-5 align-self-center">
                <h4 class="page-title">Sales Order</h4>
                <div class="d-flex align-items-center">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ URL::to('penjualan/sales-order') }}">Sales Order</a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">Create ales Order</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('content')
    <form method="POST" class="form-horizontal" action="{{ URL::to('penjualan/sales-order/store') }}">
        @csrf
        <div class="container-fluid">
            <!-- basic table -->
            <div class="row">
                <div class="col-12">

                    <!--<div class="text-right">-->
                    <!--    <a href="{{ URL::to('material_request') }}" class="btn btn-danger btn-sm mb-2">Cancel</a>-->
                    <!--    <button id="btnSubmit" type="submit" disabled data-toggle="modal" data-target="#modalShowDetail" class="btn btn-info btn-sm mb-2">Submit</button>-->
                    <!--</div>-->
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title">Sales Order</h4>
                            <input type="hidden" name="penawaran_id" value="{{ $penawaran->id }}">
                            <div class="row">
                                <div class="form-group col-md-6">
                                    <label>Customer</label>
                                    <select id="customer_id" name="customer_id" required
                                        class="form-control select2 custom-select" style="width: 100%; height:32px;"
                                        data-selected_customer="{{ $penawaran->customer_id }}" disabled>
                                    </select>
                                </div>
                                <div class="form-group col-md-6">
                                    <label class="mb-3">Status Alamat Pengiriman</label>
                                    <br>
                                    <input type="checkbox" name="status_alamat" id="status_alamat" value="" checked> <label
                                        for="status_alamat">Gunakan alamat customer</label>
                                </div>
                                <div class="form-group col-md-6">
                                    <label>Alamat Pengiriman</label>
                                    <textarea name="alamat_kirim" id="alamat_kirim" class="form-control" readonly
                                        required>{{ $penawaran->alamat_kirim }}</textarea>
                                </div>
                                <div class="form-group col-md-6">
                                    <label>Tipe Customer</label>
                                    <input type="text" name="tipe_customer" id="tipe_customer"
                                        value="{{ $penawaran->tipe_customer }}" class="form-control" readonly>
                                </div>
                                <div class="form-group col-md-6">
                                    <label>Tanggal</label>
                                    <input type="date"
                                        value="{{ date('Y-m-d', strtotime($penawaran->tanggal_penawaran)) }}"
                                        name="tanggal" required class="form-control">
                                </div>
                                <div class="form-group col-md-6">
                                    <label>Payment Method</label><br>
                                    <select id="payment_method" name="payment_method" required
                                        class="form-control select2 custom-select" style="width: 100%; height:32px;">
                                        <option value="">-- Choose Payment Method --</option>
                                        <option value="cash">Cash</option>
                                        <option value="credit">Credit</option>
                                    </select>
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="">Pilih Material</label>
                                    <br>
                                    <select class="form-control custom-select select2" style="width: 400px; height:32px;"
                                        id="item_id"></select>
                                    <button type="button" id="addRow" class="btn btn-success btn-sm mb-2"><i
                                            class="fas fa-plus"></i>&nbsp; Add New Detail</button>
                                </div>
                            </div>
                            <div class="table-responsive">
                                <table id="detail-sales-order" class="table table-striped table-bordered display"
                                    style="width:100%">
                                    <thead>
                                        <tr>
                                            <th class="text-center">Material No</th>
                                            <th class="text-center">Material Name</th>
                                            <!-- <th class="text-center">Stok Site</th> -->
                                            <th class="text-center">Harga Satuan</th>
                                            <th class="text-center">Quantity</th>
                                            <th class="text-center">Satuan</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($penawaranDetail as $item)
                                            <tr>
                                                <td>
                                                    <input type="hidden" id="m_item_no[]" name="m_item_no[]"
                                                        class="form-control" value="{{ $item->itemNo }}" />
                                                    {{ $item->itemNo }}
                                                </td>
                                                <td>
                                                    <input type="hidden" id="m_item_name[]" name="m_item_name[]"
                                                        value="{{ $item->itemName }}" />
                                                    <input type="hidden" id="m_item_id[]" name="m_item_id[]"
                                                        value="{{ $item->m_item_id }}" />
                                                    {{ $item->itemName }}
                                                </td>
                                                {{-- <td>
                                            <input readonly id="stok_site[]" name="stok_site[]" type="number" class="form-control text-right" value=sto" />
                                            </td> --}}
                                                <td>
                                                    <input type="number" id="price[]" name="price[]" step="any" min="0"
                                                        class="form-control text-right" placeholder="0" required
                                                        onkeyup="cekTotal()" value="{{ $item->base_price }}" />
                                                </td>
                                                <td>
                                                    <input type="hidden" name="temp_qty" id="temp_qty"
                                                        value="{{ $item->amount }}">
                                                    <input type="number" id="qty[]" name="qty[]" step="any" min="0"
                                                        class="form-control text-right" placeholder="0" required
                                                        onkeyup="cekTotal();cekQty()" value="{{ $item->amount }}" />
                                                </td>
                                                <td>
                                                    {{-- <input type="hidden" id="m_unit_id[]" name="m_unit_id[]" value="
                                                satuan
                                                " /> --}}
                                                    <select disabled class="form-control select2"
                                                        style="width: 100%; height:32px;" id="m_unit_name[]"
                                                        name="m_unit_name[]" required>
                                                        <option value=""> {{ $item->unitName }} </option>
                                                    </select>
                                                </td>
                                                <td class="text-center"><button class="btn btn-danger removeOption"><i
                                                            class="mdi mdi-delete"></i></button></td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <br>
                            <div class="row">
                                <div class="col-lg-3 offset-3">
                                    <div class="float-right">
                                        <label for="">Total :</label>
                                        <input type="text" readonly name="total" id="total" class="form-control"
                                            style="height:50px; font-size:28px" value="{{ $penawaran->total }}">
                                    </div>
                                </div>
                                <div class="col-lg-3">
                                    <div class="float-right">
                                        <label for="">Diskon :</label>
                                        <input type="number" name="diskon" id="diskon" class="form-control"
                                            style="height:50px; font-size:28px" value="{{ $penawaran->diskon }}">
                                    </div>
                                </div>
                                <div class="col-lg-3">
                                    <div class="float-right">
                                        <label for="">Grandtotal :</label>
                                        <input type="number" name="grandtotal" id="grandtotal" class="form-control"
                                            style="height:50px; font-size:28px" value="{{ $penawaran->total }}">
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <a href="{{ URL::to('penjualan/sales-order') }}" class="btn btn-danger  mb-2">Cancel</a>
                                <button type="submit" id="btnSubmit" class="btn btn-info mb-2">Submit</button>
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
        var t2 = $('#detail-sales-order').DataTable();
        var counter = 1;

        // List Stock
        var listStockSite = [];
        var arrMaterial = [];
        var item_price = 0;
        var selectedCustomer = '';
        var temp_alamat = `{!! $penawaran->alamat_kirim !!}`;
        $(document).ready(function() {
            selectedCustomer = $('#customer_id').data('selected_customer');
            console.log(selectedCustomer);
            $.ajax({
                type: "GET",
                url: "{{ URL::to('customer/json') }}", //json get material
                dataType: 'json',
                async: false,
                success: function(response) {
                    customer = response['data'];
                    $('#customer_id').append('<option selected value="">Pilih Customer</option>')
                    $.each(customer, function(i, item) {
                        if (customer[i]['id'] == selectedCustomer) {
                            $('#customer_id').append('<option value="' + customer[i]['id'] +
                                '" selected>' +
                                customer[i]['coorporate_name'] + '</option>');
                        } else {
                            $('#customer_id').append('<option value="' + customer[i]['id'] +
                                '">' +
                                customer[i]['coorporate_name'] + '</option>');
                        }
                    });
                }
            });

            // Get Stock
            $.ajax({
                type: "GET",
                url: "{{ URL::to('inventory/stok_json') }}", //json get site
                dataType: 'json',
                success: function(response) {
                    arrData = response['data'];
                    listStockSite = arrData;
                }
            });

            $.ajax({
                type: "GET",
                url: "{{ URL::to('penjualan/sales-order/get-item-by-penawaran') }}/" +
                    {{ $penawaran->id }}, //json get material
                dataType: 'json',
                async: false,
                success: function(response) {
                    arrMaterial = response['data'];
                    $('#item_id').append(
                        '<option selected value="">Pilih Material / Spare Part</option>')
                    $.each(arrMaterial, function(i, item) {
                        $('#item_id').append('<option value="' + arrMaterial[i]['id'] + '">(' +
                            arrMaterial[i]['no'] + ') ' + arrMaterial[i]['name'] +
                            '</option>');
                    });
                }
            });
            var arrUnit = [];
            $.ajax({
                type: "GET",
                url: "{{ URL::to('rab/get_all_m_unit') }}", //json get site
                dataType: 'json',
                success: function(response) {
                    arrUnit = response['data'];
                }
            });
            // cekGudang();

            $('#customer_id').change(function() {
                $.ajax({
                    type: "GET",
                    url: "{{ URL::to('penjualan/penawaran/get-alamat-customer') }}?id=" + $(this)
                        .val(),
                    dataType: "json",
                    success: function(response) {
                        // console.log(response.address)
                        $('#alamat_kirim').val(response.address);
                        temp_alamat = response.address;
                    }
                });
            });

            $('input[type="checkbox"]').click(function() {
                if ($(this).prop("checked") == true) {
                    $('#alamat_kirim').attr('readonly', true);
                    $('#alamat_kirim').val(temp_alamat);
                } else if ($(this).prop("checked") == false) {
                    $('#alamat_kirim').attr('readonly', false);
                    $('#alamat_kirim').val('');
                }
            });
        });
        var selectedItem = [];
        $('#addRow').on('click', function() {
            if ($('#tipe_customer').val() == '') {
                alert('Harap pilih tipe customer terlebih dahulu.');
                $('#tipe_customer').select2('open');
            } else {
                $.ajax({
                    type: "get",
                    url: "{{ URL::to('penjualan/penawaran/get-item-price') }}?item_id=" + $('#item_id')
                        .val() + "&tipe=" + $('#tipe_customer').val(),
                    dataType: "json",
                    success: function(response) {
                        // console.log(response.harga);
                        item_price = parseFloat(response.harga);

                        if (item_price == 'not valid') {
                            alert('Tipe customer tidak valid.');
                        } else {
                            var item_selected = $('#item_id').val();
                            var m_warehouse_id = 2;

                            var satuan = '',
                                itemName = '',
                                itemNo = '',
                                unitName = '';
                            $.each(arrMaterial, function(i, item) {
                                if (item_selected == arrMaterial[i]['id']) {
                                    satuan = arrMaterial[i]['m_unit_id'];
                                    itemName = arrMaterial[i]['name'];
                                    itemNo = arrMaterial[i]['no'];
                                    unitName = arrMaterial[i]['m_unit_name'];
                                }
                            });
                            var is_there = false;
                            $.each(selectedItem, function(i, item) {
                                if (item_selected == item) {
                                    is_there = true;
                                }
                            });

                            stok = 0;
                            listStockSite.map((it, obj) => {
                                if (it.m_item_id == item_selected && it.m_warehouse_id ==
                                    m_warehouse_id && it.type ==
                                    'STK_NORMAL')
                                    stok = parseFloat(it.stok);
                            });
                            // console.log(item_price)
                            if (is_there == false && item_selected != '') {
                                var tdAdd = '<tr>' +
                                    '<td><input type="hidden" id="m_item_no[]" name="m_item_no[]" class="form-control" value="' +
                                    itemNo + '" />' + itemNo + '</td>' +
                                    '<td>' +
                                    '<input type="hidden" id="m_item_name[]" name="m_item_name[]" value="' +
                                    itemName +
                                    '"/><input type="hidden" id="m_item_id[]" name="m_item_id[]" value="' +
                                    item_selected +
                                    '" />' +
                                    itemName +
                                    '</td>' +
                                    // '<td>'+
                                    //     '<input readonly id="stok_site[]" name="stok_site[]" type="number" class="form-control text-right" value="'+stok+'" />' +
                                    // '</td>'+
                                    '<td>' +
                                    '<input type="number" id="price[]" name="price[]" step="any" min="0" class="form-control text-right" placeholder="0" required onkeyup="cekTotal()" value="' +
                                    item_price + '" />' +
                                    '</td>' +
                                    '<td>' +
                                    '<input type="number" id="qty[]" name="qty[]" step="any" min="0" class="form-control text-right" placeholder="0" required onkeyup="cekTotal()">' +
                                    '</td>' +
                                    '<td>' +
                                    '<input type="hidden" id="m_unit_id[]" name="m_unit_id[]" value="' +
                                    satuan +
                                    '"/><select disabled class="form-control select2" style="width: 100%; height:32px;" id="m_unit_name[]" name="m_unit_name[]" required><option value="' +
                                    satuan + '">' + unitName + '</option></select>' +
                                    '</td>' +
                                    '<td class="text-center"><button class="btn btn-danger removeOption"><i class="mdi mdi-delete"></i></button></td>' +
                                    '</tr>';
                                $('#requestDetail_addrow').find('tbody:last').append(tdAdd);
                            }

                            saveSelectedItem()
                        }
                    }
                });
            }
        });

        function cekQty() {
            var qty = $('[id^=qty]');
            var amount = $('[id^=temp_qty]');
            for (var i = 0; i < qty.length; i++) {
                var total_req = qty.eq(i).val();
                var total = amount.eq(i).val();
                if (parseFloat(total_req) > parseFloat(total)) {
                    // qty.eq(i).val('');
                    $('#btnSubmit').prop('disabled', true)
                    alert('Quantity melebihi quantity penawaran.');
                    $('#btnSubmit').preventDefault()
                } else {
                    $('#btnSubmit').prop('disabled', false)
                }
            }
        }

        function saveSelectedItem() {
            selectedItem = [];
            var m_item_id = $('[name^=m_item_id]');
            for (i = 0; i < m_item_id.length; i++) {
                var item = m_item_id.eq(i).val();
                if (item != '') {
                    selectedItem.push(item);
                }
            }
        }

        $("#requestDetail_addrow").on("click", ".removeOption", function(event) {
            event.preventDefault();
            $(this).closest("tr").remove();
            saveSelectedItem()
        });


        function cekGudang() {
            saveSelectedItem()
            var m_warehouse_id = $('#m_warehouse_id').val();
            if (m_warehouse_id == '') {
                $('#addRow').prop('disabled', true)
            } else {
                $('#addRow').prop('disabled', false)
            }
            $('#requestDetail_addrow > tbody').empty();
        }

        function cekStock() {
            var is_out_stock = false;
            var qty = $('[id^=qty]');
            var stock = $('[name^=stok_site]');
            for (var i = 0; i < qty.length; i++) {
                var total_req = qty.eq(i).val();
                var total = stock.eq(i).val();
                if (parseFloat(total_req) > parseFloat(total)) {
                    $(qty.eq(i)).closest("tr").addClass('table-danger');
                    is_out_stock = true;
                } else {
                    $(qty.eq(i)).closest("tr").removeClass('table-danger');
                }
            }
            if (is_out_stock == true) {
                $('#btnSubmit').prop('disabled', true)
            } else {
                $('#btnSubmit').prop('disabled', false)
            }
        }

        function cekTotal() {
            var item = $('[name^=m_item_id');
            var price = $('[name^=price');
            var volume = $('[name^=qty');
            var total = 0,
                total_item = 0,
                total = 0;
            for (var i = 0; i < item.length; i++) {
                var m_item = item.eq(i).val();
                var harga = price.eq(i).val() != '' ? price.eq(i).val() : 0;
                var amount = volume.eq(i).val() != '' ? volume.eq(i).val() : 0;
                if (m_item != '' && harga != '' && amount != '') {
                    total += (parseFloat(amount) * parseFloat(harga));
                }
            }
            $('#total').val(formatCurrency(total.toFixed(0)))
            $('#total_temp').val(total.toFixed(0))
            $('#diskon').val(0)
            $('#grandtotal').val(total)
            $('#grandtotal_temp').val(formatCurrency(total.toFixed(0)))
        }

        $('#diskon').keyup(function(e) {
            var total = parseFloat($('#total_temp').val());
            var diskon = parseFloat($(this).val());
            var grandtotal = total - diskon;
            $('#grandtotal').val(grandtotal);
            $('#grandtotal_temp').val(formatCurrency(grandtotal.toFixed(0)))
        });

    </script>
@endsection
