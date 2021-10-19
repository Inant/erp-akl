@extends('theme.default')

@section('breadcrumb')
    <div class="page-breadcrumb">
        <div class="row">
            <div class="col-5 align-self-center">
                <h4 class="page-title">Form Material Request</h4>
                <div class="d-flex align-items-center">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ URL::to('material_request') }}">Material Request</a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">Form Material Request</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('content')
    @php

    // echo "<pre>";
    // print_r ($listMaterial);
    // echo "</pre>";
    @endphp
    <form method="POST" action="{{ URL::to('pengeluaran_barang') }}" class="form-horizontal">
        @csrf
        <div class="container-fluid">
            <!-- basic table -->
            <div class="row">
                @if ($error['is_error'])
                    <div class="col-12">
                        <div class="alert alert-danger"> <i class="mdi mdi-alert-box"></i> {{ $error['error_message'] }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close"> <span
                                    aria-hidden="true">×</span> </button>
                        </div>
                    </div>
                @endif
                <div class="col-12">
                    <div class="text-right">
                        <a href="{{ URL::to('pengeluaran_barang') }}" class="btn btn-danger btn-sm mb-2">Cancel</a>
                        <button type="submit" id="submit" class="btn btn-info btn-sm mb-2">Konfirmasi</button>
                    </div>
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title">Material Request Header</h4>
                            <div class="form-group row">
                                <label class="col-sm-3 text-right control-label col-form-label">Kontraktor / Mandor</label>
                                <div class="col-sm-9">
                                    <input type="hidden" name="inv_request_id" value="{{ $id }}" />
                                    <input type="text" required id="mandor" name="mandor" class="form-control text-left">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-3 text-right control-label col-form-label">Material </label>
                                <div class="col-sm-9">
                                    <select class="form-control custom-select select2" style="width: 700px; height:32px;"
                                        id="item_id">
                                        <option value="">-- Pilih Material --</option>
                                        @php
                                            $totalProjectReq = $listMaterial['data']['totalProjectReq']->total;
                                        @endphp
                                        @foreach ($listMaterial['data']['detail'] as $detail)
                                            @php
                                                $amount_auth = $detail['amount_auth'] != null ? $detail['amount_auth'] : $detail['amount'];
                                                
                                                $amount_total = $amount_auth * $totalProjectReq - $detail['total_used'];
                                                
                                                $qty_pengeluaran = $amount_auth;
                                                
                                                $amount_total = is_int($amount_total) ? $amount_total : round($amount_total, 1);
                                            @endphp
                                            @if ($detail['m_items']['deleted_at'] == null)
                                                <option data-amount_auth="{{ $amount_auth }}"
                                                    data-amount_total="{{ $amount_total }}"
                                                    data-qty_pengeluaran="{{ $qty_pengeluaran }}"
                                                    data-unit={{ $detail['m_units']['name'] }}
                                                    data-unit_id={{ $detail['m_units']['id'] }}
                                                    data-item_name="{{ $detail['m_items']['name'] }}"
                                                    data-item_no="{{ $detail['m_items']['no'] }}"
                                                    data-detail_id="{{ $detail['id'] }}"
                                                    data-warehouse_id="{{ $detail['m_warehouse_id'] }}"
                                                    value="{{ $detail['m_items']['id'] }}">
                                                    {{ $detail['m_items']['no'] . ' - ' . $detail['m_items']['name'] }}
                                                </option>
                                            @endif
                                        @endforeach
                                    </select>
                                    <button type="button" id="addRow" class="btn btn-success btn-sm"><i
                                            class="fas fa-plus"></i>&nbsp; Add Manual</button>
                                </div>
                            </div>

                            <input type="hidden" name="totalProjectReq" id="totalProjectReq"
                                value="{{ $listMaterial['data']['totalProjectReq']->total }}">
                            <br />
                            <div class="table-responsive">
                                <table id="dt_temp" class="table table-striped table-bordered">
                                    <thead>
                                        <tr>
                                            <th class="text-center">No</th>
                                            <th class="text-center">Material No</th>
                                            <th class="text-center">Material Name</th>
                                            <!-- <th class="text-center">Pilih</th> -->
                                            <th class="text-center">Site Stock</th>
                                            <th class="text-center">Qty Pengajuan</th>
                                            <th class="text-center">Qty Utuh</th>
                                            <th class="text-center">Satuan</th>
                                            <th class="text-center">Gudang</th>
                                            <th class="text-center">Tipe Stok</th>
                                            <th class="text-center">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>

                            {{-- <div class="table-responsive">
                                <table id="requestDetail" class="table table-striped table-bordered display"
                                    style="width:100%">
                                    <thead>
                                        <tr>
                                            <th class="text-center">No</th>
                                            <th class="text-center">Material No</th>
                                            <th class="text-center">Material Name</th>
                                            <!-- <th class="text-center">Pilih</th> -->
                                            <th class="text-center">Site Stock</th>
                                            <th class="text-center">Qty Pengajuan</th>
                                            <th class="text-center">Qty Utuh</th>
                                            <th class="text-center">Satuan</th>
                                            <th class="text-center">Tipe Stok</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div> --}}
                            <div class="form-group">
                                <button class="btn btn-primary" type="button" id="add_rest_material" disabled>Tambah Barang
                                    Sisa</button>
                            </div>
                            <div class="table-responsive">
                                <table id="restDetail" class="table table-striped table-bordered display"
                                    style="width:100%">
                                    <thead>
                                        <tr>
                                            <th class="text-center">Material Name</th>
                                            <th class="text-center">Qty Material Tidak Utuh</th>
                                            <th class="text-center">Satuan</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
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
        // var t = $('#requestDetail_addrow').DataTable();
        // var t2 = $('#requestDetail').DataTable();
        var counter = 1;

        var listMaterialRab = [];
        var idRequest = {{ $id }};
        // List Stock
        var listStockSite = [];
        var listWarehouses = [];
        var listStockRestSite = [];
        var selectedItem = [];
        $(document).ready(async function() {
            let site_id = {{ $site_id }};
            // getProjectName(site_id);
            await $.ajax({
                type: "GET",
                url: "{{ URL::to('inventory/stok_json') }}", //json get site
                dataType: 'json',
                success: function(response) {
                    arrData = response['data'];
                    listStockSite = arrData;
                }
            });

            await $.ajax({
                type: "GET",
                url: "{{ URL::to('inventory/get-warehouse') }}", //json get site
                dataType: 'json',
                success: function(response) {
                    arrData = response['data'];
                    listWarehouses = arrData;
                }
            });

            $.ajax({
                type: "GET",
                url: "{{ URL::to('inventory/stok_rest_json') }}", //json get site
                dataType: 'json',
                success: function(response) {
                    arrData = response['data'];
                    listStockRestSite = arrData;
                }
            });

            // t2.clear().draw(false);
            $('#requestDetail > tbody').empty();

            function isInt(n) {
                if (n % 1 === 0) {
                    return true;
                } else {
                    return false;
                }
            }

            // $.ajax({
            //     type: "GET",
            //     url: "{{ URL::to('material_request/list_detail') }}" + "/" +
            //         idRequest, //json get site
            //     dataType: 'json',
            //     success: function(response) {
            //         var totalProjectReq = parseInt(response['data']['totalProjectReq']['total']);
            //         console.log(totalProjectReq);
            //         arrData = response['data']['detail'];
            //         listMaterialRab = arrData;
            //         var nomor = 1;
            //         for (i = 0; i < arrData.length; i++) {
            //             if (arrData[i]['m_items']['deleted_at'] == null) {
            //                 stok = 0;
            //                 listStockSite.map((item, obj) => {
            //                     if (item.m_item_id == arrData[i]['m_item_id'] && item
            //                         .m_warehouse_id == arrData[i]['m_warehouse_id'] && item
            //                         .type == 'STK_NORMAL') {
            //                         stok += parseInt(item.stok);
            //                     }
            //                 });
            //                 let amount_auth = arrData[i]['amount_auth'] != null ? arrData[i][
            //                     'amount_auth'
            //                 ] : arrData[i]['amount'];
            //                 // var amount_total= (parseFloat(amount_auth) - parseFloat(arrData[i]['total_used']));
            //                 var amount_total = parseFloat(amount_auth) * totalProjectReq -
            //                     parseFloat(arrData[i]['total_used']);
            //                 var qty_pengeluaran = parseFloat(amount_auth)
            //                 // var amount_total= parseFloat(amount_auth) * totalProjectReq;

            //                 amount_total = isInt(amount_total) ? amount_total : amount_total
            //                     .toFixed(1);
            //                 // console.log(amount_total)
            //                 var tdAdd = '<tr>' +
            //                     '<td>' +
            //                     '<div class="text-left">' + nomor + '</div>' +
            //                     '</td>' +
            //                     '<td>' +
            //                     '<div class="text-left">' + arrData[i]['m_items']['id'] + ' -- ' +
            //                     arrData[i]['m_items']['no'] + '</div>' +
            //                     '</td>' +
            //                     '<td>' +
            //                     '<input type="hidden" name="inv_request_d_id[]" value="' + arrData[
            //                         i]['id'] +
            //                     '" /><input type="hidden" name="m_item_id[]" value="' + arrData[i][
            //                         'm_item_id'
            //                     ] + '" /><div class="text-left">' + arrData[i]['m_items']['name'] +
            //                     '</div>' +
            //                     '</td>' +
            //                     '<td>' +
            //                     '<input type="hidden" id="stok[]" name="stok[]" value="' +
            //                     parseFloat(stok) + '" /> <p class="text-right" id="label_stok[]">' +
            //                     parseFloat(stok) + '</p>' +
            //                     '</td>' +
            //                     '<td>' +
            //                     '<div class="text-right"><input type="hidden" id="amount[]" name="amount[]" step="any" min="0" class="form-control text-right" placeholder="0" value="' +
            //                     amount_total + '">' + amount_total + '</div>' +
            //                     '</td>' +
            //                     '<td>' +
            //                     '<div class="text-right"><input type="" id="qty[]" name="qty[]" min="0" class="form-control text-right" value="0" required onkeyup="cekQty()" oninput="this.value=(parseInt(this.value)||0)"></div>' +
            //                     '</td>' +
            //                     '<td>' +
            //                     '<input type="hidden" name="m_unit_id[]" value="' + arrData[i][
            //                         'm_unit_id'
            //                     ] + '" /><input type="hidden" name="m_warehouse_id[]" value="' +
            //                     arrData[i]['m_warehouse_id'] + '" /><div class="text-center">' +
            //                     arrData[i]['m_units']['name'] + '</div>' +
            //                     '</td>' +
            //                     '<td>' +
            //                     '<select name="type_stok[]" onchange="cekTypeStok()" class="form-control" id="type_stok[]"><option value="STK_NORMAL">Stok Normal</option><option value="TRF_STK">Stok Transfer</option></select>' +
            //                     '</td>' +
            //                     '</tr>';
            //                 $('#requestDetail').find('tbody:last').append(tdAdd);

            //                 formInvNo = $('[id^=inv_no]');
            //                 formInvNo.empty();
            //                 formInvNo.append('<option value="">-- Select Inv Number --</option>');
            //                 listStockSite.map((item, obj) => {
            //                     if (item.m_item_id == arrData[i]['m_item_id'])
            //                         formInvNo.append('<option value="' + item
            //                             .purchase_d_id + '">' + item.no + '</option>');
            //                 });
            //                 if (stok < amount_auth) {
            //                     valid = false;
            //                 }
            //                 cekStock();
            //                 nomor++;
            //             }
            //         }
            //     }
            // });

            // Penyesuaian

            var nomor = 0;
            $('#addRow').on('click', function() {
                // countMateri al = $('[name^=m_item_id]').length;
                var item_selected = $('#item_id').val();
                var item_id = $('#item_id').val();
                var selection = $('#item_id').find('option:selected');
                var item_name = selection.data('item_name');
                var item_no = selection.data('item_no');
                var amount_auth = selection.data('amount_auth');
                var amount_total = selection.data('amount_total');
                var qty_pengeluaran = selection.data('qty_pengeluaran');
                var unit = selection.data('unit');
                var unit_id = selection.data('unit_id');
                var detail_id = selection.data('detail_id');
                var warehouse_id = selection.data('warehouse_id');
                stok = 0;
                warehouse = '';
                // var is_there = false;
                // $.each(selectedItem, function(i, item) {
                //     if (item_selected != item) {
                //         is_there = true;
                //     }
                // });
                if (selectedItem.includes(item_id) == false && item_selected != '') {
                    listStockSite.map((item, obj) => {
                        if (item.m_item_id == item_id && item
                            .m_warehouse_id == warehouse_id && item
                            .type == 'STK_NORMAL') {
                            stok += parseInt(item.stok);
                        }
                    });
                    listWarehouses.map((item, obj) => {
                        if (item.id == warehouse_id) {
                            warehouse = item.name
                        }
                    });
                    selectedItem.push(item_id);
                    nomor =selectedItem.indexOf(item_id) + 1;
                    var tdAdd = '<tr>' +
                        '<td>' +
                        '<div class="text-left">' + nomor + '</div>' +
                        '</td>' +
                        '<td>' +
                        '<div class="text-left">' + item_no + '</div>' +
                        '</td>' +
                        '<td>' +
                        '<input type="hidden" name="inv_request_d_id[]" value="' + detail_id +
                        '" /><input type="hidden" name="m_item_id[]" class="item_id" value="' +
                        item_id +
                        '" /><div class="text-left">' + item_name +
                        '</div>' +
                        '</td>' +
                        '<td>' +
                        '<input type="hidden" id="stok[]" name="stok[]" value="' + parseFloat(stok) +
                        '" /> <p class="text-right" id="label_stok[]">' + parseFloat(stok) + '</p>' +
                        '</td>' +
                        '<td>' +
                        '<div class="text-right"><input type="hidden" id="amount[]" name="amount[]" step=".01" min="0" class="form-control text-right" placeholder="0" value="' +
                        amount_total + '">' + amount_total + '</div>' +
                        '</td>' +
                        '<td>' +
                        '<div class="text-right"><input type="number" id="qty[]" step=".01" name="qty[]" min="0" class="form-control text-right" value="0" required onkeyup="cekQty()" ></div>' +
                        '</td>' +
                        '<td>' +
                        '<input type="hidden" name="m_unit_id[]" value="' + unit_id +
                        '" /><input type="hidden" name="m_warehouse_id[]" value="' + warehouse_id +
                        '" /><div class="text-center">' + unit + '</div>' +
                        '</td>' +
                        '<td>' +
                        '<div class="text-center">' + warehouse + '</div>' +
                        '</td>' +
                        '<td>' +
                        '<select name="type_stok[]" onchange="cekTypeStok()" class="form-control" id="type_stok[]"><option value="STK_NORMAL">Stok Normal</option><option value="TRF_STK">Stok Transfer</option></select>' +
                        '</td>' +
                        '<td class="text-center"><button class="btn btn-danger removeOption"><i class="mdi mdi-delete"></i></button></td>' +
                        '</tr>';
                    $('#dt_temp').find('tbody:last').append(tdAdd);
                } else {
                    alert('Item tersebut telah masuk ke dalam daftar.')
                }

                // // if(countMaterial < 5) {
                //     document.getElementById("btn_add_to_selected").disabled = true;

                //     var tdAdd='<tr>'+
                //                 '<td>'+
                //                     '<input type="text" readonly id="m_item_no[]" name="m_item_no[]" class="form-control" onchange="handleMaterialNo(this)" />' +
                //                 '</td>'+
                //                 '<td>'+
                //                     '<select class="form-control custom-select" style="max-width: 400px; height:32px;" name="m_item_id[]" required onchange="handleMaterial()"></select>' +
                //                 '</td>'+
                //                 '<td>'+
                //                     '<input name="volume[]" onchange="cekDiskonPrice();" type="number" class="form-control text-right" step="any" />' +
                //                 '</td>'+
                //                 '<td>'+
                //                     '<input readonly id="stok_site[]" name="stok_site[]" type="number" class="form-control text-right" />' +
                //                 '</td>'+
                //                 '<td>'+
                //                     '<input name="m_unit_id[]" type="hidden" /><input name="m_unit_name[]" class="form-control text-center" type="text" readonly />' +
                //                 '</td>'+
                //                 '<td>'+
                //                     '<div class="text-center"><p id="best_supplier[]"></p></div>' +
                //                 '</td>'+
                //                 '<td>'+
                //                     '<input id="perkiraan_harga_suppl[]" required onchange="doPerkiraanHarga();checkText(this.value);" name="perkiraan_harga_suppl[]" class="form-control text-right" /><input id="harga_diskon[]" required  name="harga_diskon[]" class="form-control text-right" type="hidden"/>' +
                //                     '<input id="notes[]" required name="notes[]" class="form-control text-left" type="hidden" value="-" />' +
                //                 '</td>'+
                //                 '<td class="text-center"><button class="btn btn-danger removeOption"><i class="mdi mdi-delete"></i></button></td>'+
                //             '</tr>';
                //     $('#dt_temp').find('tbody:last').append(tdAdd);
                //     $('.custom-select').select2();
                //     eventSelectedMaterial();
                // }
                // saveSelectedItem();
            });

            $("#add_rest_material").prop('disabled', false);
        });

        $("#dt_temp").on("click", ".removeOption", function(event) {
            event.preventDefault();
            var deletedItem = $(this).closest("tr")
                .find('.item_id')
                .val();
            // console.log(deletedItem);
            var deletedIndex = selectedItem.indexOf(deletedItem);
            if (deletedIndex > -1) {
                selectedItem.splice(deletedIndex, 1);
            }
            $(this).closest("tr").remove();

        });

        function saveSelectedItem() {
            selectedItem = [];
            var m_item_id = $('[name^=m_item_id]');
            for (i = 0; i < m_item_id.length; i++) {
                var item = m_item_id.eq(i).val();
                console.log(item)
                if (item != '') {
                    selectedItem.push(item);
                }
            }
        }

        function cekStock() {
            var is_out_stock = false;
            var qty = $('[id^=qty]');
            var stock = $('[name^=stok]');
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
                $('#submit').prop('disabled', true)
            } else {
                $('#submit').prop('disabled', false)
            }
        }

        function cekQty() {
            var qty = $('[id^=qty]');
            var amount = $('[id^=amount]');
            for (var i = 0; i < qty.length; i++) {
                var total_req = qty.eq(i).val();
                var total = amount.eq(i).val();
                if (parseFloat(total_req) > parseFloat(total)) {
                    qty.eq(i).val('');
                    alert('inputan melebihi request yang ada');
                }
            }
            cekStock()
            cekQtyDec();
        }

        function cekTypeStok() {
            var type_stok = $('[id^=type_stok]');
            var item = $('[name^=m_item_id]');
            var warehouse_id = $('[name^=m_warehouse_id]');
            var stok = $('[name^=stok]');
            var label_stok = $('[id^=label_stok]');
            for (var i = 0; i < type_stok.length; i++) {
                var m_item_id = item.eq(i).val();
                var m_warehouse_id = warehouse_id.eq(i).val();
                var type_stk = type_stok.eq(i).val();
                stock = 0;
                listStockSite.map((item, obj) => {

                    if (item.m_item_id == m_item_id && item.m_warehouse_id == m_warehouse_id && item.type ==
                        type_stk) {
                        stock = item.stok;
                    }
                });
                stok.eq(i).val(stock);
                label_stok.eq(i).html(stock);
            }
            cekQty();
        }

        function cekQtyDec() {
            var qty_bullet = $('[id^=qty_req_bullet]');
            var qty_dec = $('[id^=qty_req_dec]');
            var inv_req_d_id_rest = $('[id^=inv_req_d_id_rest]');
            var m_item_rest_id = $('[id^=m_item_rest_id]');

            var inv_req_d_id = $('[id^=inv_req_d_id]');
            var qty = $('[id^=qty]');
            var amount = $('[id^=amount]');
            for (var j = 0; j < inv_req_d_id.length; j++) {
                var total = qty.eq(j).val();
                var stok = amount.eq(j).val();
                var id = inv_req_d_id.eq(j).val();
                var qty_req = total;
                var sub_total_rest = 0;

                for (var i = 0; i < inv_req_d_id_rest.length; i++) {
                    var id_rest = inv_req_d_id_rest.eq(i).val();
                    if (id == id_rest) {
                        if (qty_bullet.eq(i).val() != '' && qty_dec.eq(i).val() != '') {
                            var total_bullet = qty_bullet.eq(i).val() != '' ? qty_bullet.eq(i).val() : 0;
                            var total_req_dec = qty_dec.eq(i).val() != '' ? qty_dec.eq(i).val() : 0;
                            var m_item_id = m_item_rest_id.eq(i).val();
                            var amount_rest = 0;
                            var stok_rest = 0;
                            var total_rest = parseFloat(total_bullet) * parseFloat(total_req_dec);
                            listStockRestSite.map((item, obj) => {
                                if (item.m_item_id == m_item_id) {
                                    if (total_req_dec == item.amount_rest) {
                                        amount_rest = item.amount_rest;
                                        stok_rest = item.stok;
                                    }
                                }
                            });

                            qty_req = parseFloat(qty_req) + parseFloat(total_rest);
                            sub_total_rest = parseFloat(total_bullet) + parseFloat(sub_total_rest);
                            if (amount_rest == 0) {
                                qty_bullet.eq(i).val('');
                                qty_dec.eq(i).val('');
                                alert('material sisa potongan tidak ditemukan');
                            } else {
                                if (sub_total_rest <= stok_rest) {
                                    if (parseFloat(qty_req) > parseFloat(stok)) {
                                        qty_bullet.eq(i).val('');
                                        // qty_dec.eq(i).val('');
                                        alert('inputan melebihi request yang ada');
                                    }
                                } else {
                                    qty_bullet.eq(i).val('');
                                    // qty_dec.eq(i).val('');
                                    alert('inputan melebihi stok sisa yang ada');
                                }

                            }
                        }
                    }
                }
            }
        }

        function getMItem() {
            var id = $('[id^=inv_req_d_id_rest]');
            var amount_child = $('[id^=amount_child]');
            var label_child = $('[id^=label_child]');
            for (var i = 0; i < id.length; i++) {
                var id_rest = '';
                var rest_amount = 0;
                var turunan = 0;
                var satuan = 0;
                listMaterialRab.map((item, obj) => {
                    if (item.id == id.eq(i).val()) {
                        id_rest = item.m_item_id;
                        rest_amount = item.m_items.amount_unit_child;
                        turunan = (item.m_unit_child != null ? item.m_unit_child.name : '-');
                        satuan = item.m_units.name;
                    }
                });
                label_child.eq(i).val(rest_amount + ' ' + turunan + ' / ' + satuan);
                amount_child.eq(i).val(rest_amount);
                // console.log(id_rest)
                $('[id^=m_item_rest_id]').eq(i).val(id_rest);
            }
        }

        function changeQtyReqDec() {
            var inv_req_d_id_rest = $('[id^=inv_req_d_id_rest]');
            var amount_child = $('[id^=amount_child]');
            var qty_bullet = $('[id^=qty_req_bullet]');
            var qty_dec = $('[id^=qty_req_dec]');
            var turunan = $('[id^=turunan]');
            for (var j = 0; j < inv_req_d_id_rest.length; j++) {
                var id_rest = inv_req_d_id_rest.eq(j).val();
                var amount_turunan = turunan.eq(j).val();
                var amount_rest = amount_child.eq(j).val();
                var rest_amount = amount_turunan / amount_rest;
                if (amount_turunan >= amount_rest) {
                    turunan.eq(j).val('');
                } else {
                    qty_dec.eq(j).val(rest_amount);
                }
            }
        }

        $("#add_rest_material").click(function() {
            var options = '<option value="">-- Pilih Item -- </option>';
            for (var i = 0; i < listMaterialRab.length; i++) {
                options += '<option value="' + listMaterialRab[i]['id'] + '">' + (listMaterialRab[i]['m_items'] !=
                    null ? listMaterialRab[i]['m_items']['name'] : '-') + '</option>';
            }
            var tdAdd =
                '<tr><td><input type="hidden" name="m_item_rest_id[]" id="m_item_rest_id[]" value="0" /><select name="inv_req_d_id_rest[]" id="inv_req_d_id_rest[]" class="form-control" onchange="getMItem()">' +
                options + '</select></td>' +
                '<td><div class="form-group row"><input type="number" id="turunan[]" name="turunan[]" class="form-control col-sm-5" step="any" min="0" placeholder="potongan"  onkeyup="changeQtyReqDec()"><input type="hidden" id="qty_req_dec[]" name="qty_req_dec[]" class="form-control col-sm-5" step="any" min="0" placeholder="desimal" onkeyup="cekQtyDec()">' +
                '<input type="number" id="qty_req_bullet[]" name="qty_req_bullet[]" min="0" class="form-control col-sm-5" placeholder="total" oninput="this.value=(parseInt(this.value)||0)" onkeyup="cekQtyDec()">' +
                '<div></td>' +
                '<td><div class="text-right"><input type="" readonly class="form-control" id="label_child[]" name=""><input type="hidden" class="form-control" id="amount_child[]" name=""></div></td>' +
                '<td><button type="button" class="btn btn-sm btn-danger removeOption"><i class="mdi mdi-delete"></i></button></td>' +
                '</tr>';
            $('#restDetail').find('tbody:last').append(tdAdd);
        });

        $("#restDetail").on("click", ".removeOption", function(event) {
            event.preventDefault();
            $(this).closest("tr").remove();
        });
    </script>
@endsection
