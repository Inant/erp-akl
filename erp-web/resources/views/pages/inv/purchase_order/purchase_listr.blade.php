@extends('theme.default')

@section('breadcrumb')
			<div class="page-breadcrumb">
                <div class="row">
                    <div class="col-5 align-self-center">
                        <h4 class="page-title">Tagihan Supplier</h4>
                        <div class="d-flex align-items-center">
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item active" aria-current="page">Tagihan</li>
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
        @if($error['is_error'])
        <div class="col-12">
            <div class="alert alert-danger"> <i class="mdi mdi-alert-box"></i> {{ $error['error_message'] }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close"> <span aria-hidden="true">×</span> </button>
            </div>
        </div>
        @endif
        <div class="col-12">
            <div class="text-right">
                <a href="{{ URL::to('inventory/form_bill_supplier') }}"><button class="btn btn-success btn-sm mb-2"><i class="fas fa-plus"></i>&nbsp; Create New</button></a>
            </div>
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Daftar Tagihan Supplier</h4>
                    <form method="POST" action="{{ URL::to('inventory/export_purchase') }}" target="_blank">
                        @csrf
                        <div class="row">
                            <div class="col-6">
                                <h5 class="card-title">Pilih Supplier</h5>
                                <div class="form-group">
                                    <select class="select2 form-control" multiple="multiple" style="height: 36px; width: 100%;" id="m_supplier_id" name="m_supplier_id[]">
                                        <option value="all" {{$all_supplier == true ? 'selected' : ''}}>Semua</option>
                                        @foreach($suppliers as $value)
                                            <?php $same=false; ?>
                                            @foreach($supplier_selected as $v)
                                                @if($v == $value['id'])
                                                    @php $same=true @endphp
                                                @endif
                                            @endforeach
                                        <option value="{{$value['id']}}" {{$same == true ? 'selected' : ''}}>{{$value['name']}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                
                            </div>
                            <div class="col-3">
                                <div class="form-group">
                                    <label for="">Tanggal Awal :</label>
                                    <input type="date" name="date"  id="date" class="form-control" required value="{{$date1}}">
                                </div>
                            </div>
                            <div class="col-3">
                                <div class="form-group">
                                    <label >Tanggal Ahir :</label>
                                    <input type="date" name="date2" id="date2" class="form-control" required value="{{$date2}}">
                                </div>
                            </div>
                            <div class="col-12 form-group">
                                <button class="btn btn-primary" type="button" onclick="updateBillList()"><i class="fa fa-search"></i></button>
                            </div>
                        </div>
                        <div class="form-group text-right">
                            <button type="submit" class="btn btn-success"><i class="fa fa-file-excel"></i> Export</button>
                        </div>
                    </form>
                    <a id="multiple"><button class="btn btn-success text-right"><i class="fa fa-print"></i> Multiple Print</button></a>
                    <br>
                    <br>
                    <div class="table-responsive">
                        <table id="purchase_list" class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th class="text-center">Select</th>
                                    <th class="text-center">No</th>
                                    <th class="text-center">No Invoice</th>
                                    <th class="text-center">No Tagihan</th>
                                    <th class="text-center">Nama Supplier</th>
                                    <th class="text-center">Nomor Surat Jalan</th>
                                    <th class="text-center">Nomor Surat Jalan Jasa</th>
                                    <!-- <th class="text-center">Nomor PO Jasa</th> -->
                                    <!-- <th class="text-center" style="min-width:100px">Tanggal PO</th> -->
                                    <th class="text-center" style="min-width:100px">Tanggal Tagihan</th>
                                    <th class="text-center" style="min-width:100px">Tanggal Jatuh Tempo</th>
                                    <th class="text-center"  style="min-width:100px">Tanggal Bayar</th>
                                    <!-- <th class="text-center">Penerima</th> -->
                                    <th class="text-center">Jumlah Tagihan</th>
                                    <th class="text-center">Status</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>                
</div>


<div class="modal fade" id="modalShowDetail" tabindex="-1" role="dialog" aria-labelledby="modalShowDetailLabel1">
    <div class="modal-dialog  modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="modalAddMaterialLabel1">Detail Tagihan</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table id="dt_detail" class="table table-bordered">
                        <thead>
                            <tr>
                                <th class="text-center">No</th>
                                <th class="text-center">Tanggal Bayar</th>
                                <th class="text-center">Pembayaran dengan</th>
                                <th class="text-center">Ref Code</th>
                                <th class="text-center">Nama Bank</th>
                                <th class="text-center">Nomor Bank</th>
                                <th class="text-center">Atas Nama</th>
                                <th class="text-center">Deskripsi</th>
                                <th class="text-center">Total</th>
                            </tr>
                        </thead>                                      
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <!-- <button type="button" onclick="saveMaterial(this.form);" class="btn btn-primary btn-sm">Save</button> -->
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalShowDetail2" tabindex="-1" role="dialog" aria-labelledby="modalShowDetailLabel1">
    <div class="modal-dialog  modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="modalAddMaterialLabel1">Purchase Order Detail</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table id="po_detail" class="table table-bordered">
                        <thead>
                            <tr>
                                <th class="text-center">No Material</th>
                                <th class="text-center">Nama Material</th>
                                <th class="text-center">Volume</th>
                                <th class="text-center">Satuan</th>
                                <th class="text-center">Harga</th>
                                <th class="text-center">Total</th>
                            </tr>
                        </thead>                                      
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <!-- <button type="button" onclick="saveMaterial(this.form);" class="btn btn-primary btn-sm">Save</button> -->
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalShowDetail3" tabindex="-1" role="dialog" aria-labelledby="modalShowDetailLabel1">
    <div class="modal-dialog  modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="modalAddMaterialLabel1">Purchase Order Detail</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table id="po_asset_detail" class="table table-bordered">
                        <thead>
                            <tr>
                                <th class="text-center">No Material</th>
                                <th class="text-center">Nama Material</th>
                                <th class="text-center">Volume</th>
                                <th class="text-center">Satuan</th>
                                <th class="text-center">Harga</th>
                                <th class="text-center">Total</th>
                            </tr>
                        </thead>                                      
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <!-- <button type="button" onclick="saveMaterial(this.form);" class="btn btn-primary btn-sm">Save</button> -->
            </div>
        </div>
    </div>
</div>

<script src="{!! asset('theme/assets/libs/jquery/dist/jquery.min.js') !!}"></script>
<script src="{!! asset('theme/assets/extra-libs/DataTables/datatables.min.js') !!}"></script>
<script>

dt_detail = $('#dt_detail').DataTable();
po_detail = $('#po_detail').DataTable();
po_asset_detail = $('#po_asset_detail').DataTable();

$(document).ready(function(){

    dt = $('#purchase_list').DataTable( {
        "processing": true,
        "serverSide": true,
        "ajax": "{{ URL::to('inventory/get_payment_list') }}",
        aaSorting: [[6, 0, 'desc']],
        "columns": [
            {"data": "id", "render" : function(data, type, row){
                // return '<a href="" onclick="doShowDetail('+row.id+');" data-no="'+row.no+'" data-toggle="modal" data-target="#modalShowDetail">'+row.no+'</a>';
                return `<input type="checkbox" class="id_payments" onclick="clicked(this)" name="id_payments[]" value="${row.id}">`;
            }},
            {"data": "no", "render" : function(data, type, row){
                return '<a href="" onclick="doShowDetail('+row.id+');" data-no="'+row.no+'" data-toggle="modal" data-target="#modalShowDetail">'+row.no+'</a>';
            }},
            {
                'data' : 'paid_no'
            },
            {"data": "bill_no"},
            {"data": "id",
            "render": function(data, type, row){return row.supplier}},
            {"data": "no_surat_jalan"},
            {"data": "no_surat_jalan_jasa"},
            // {"data": "purchase_service_no",
            // "render": function(data, type, row){return row.purchase_service_no != null ? '<a href="" onclick="doShowDetail3('+row.purchase_service_id+');" data-toggle="modal" data-target="#modalShowDetail3">'+row.purchase_service_no+'</a>' : '-'}},
            // {"data": "id",
            // "render": function(data, type, row){return row.purchase_date != null ? formatDateID(new Date((row.purchase_date).substring(0,10))) : (row.purchase_asset_date != null ? formatDateID(new Date((row.purchase_asset_date).substring(0,10))) : formatDateID(new Date((row.purchase_service_date).substring(0,10))))}},
            {"data": "create_date",
            "render": function(data, type, row){return row.create_date != null ? formatDateID(new Date((row.create_date).substring(0,10))) : '-'}},
            {"data": "paid_for_week",
            "render": function(data, type, row){return row.due_date != null ? formatDateID(new Date((row.due_date).substring(0,10))) : '-'}},
            {"data": "pay_date",
            "render": function(data, type, row){return row.pay_date != '' ? formatDateID(new Date((row.pay_date).substring(0,10))) : '-'}},
            {"data": "amount", "class" : "text-right",
            "render": function(data, type, row){return formatCurrency(row.amount)}},
            {"data": "is_paid",
            "render": function(data, type, row){return row.is_paid == true ? 'Lunas' : "Belum Lunas"}},
            {"data": "id", "render" : function(data, type, row){
                return '<a href="{{URL::to('inventory/purchase/cetak')}}/'+row.id+'" target="_blank" class="btn btn-sm btn-success"><i class="fa fa-print"></i></a>';
            }},
        ],
        "fnRowCallback": function(nRow, aData, iDisplayIndex, iDisplayIndexFull) {
            if (aData.paid_for_week == "1") {
                $('td', nRow).css('background-color', '#e2b2b2');
                $('td', nRow).css('color', '#423f3f');
                $('a', nRow).css('color', '#183177');
                $('.btn', nRow).css('color', 'white');
            }
        }
    } );

    var m_supplier_id=$('[name^=m_supplier_id]').val();

    if(m_supplier_id.length > 0){
        updateBillList();
    }
});
var id_payments = [];
function clicked(e){
    if ($(e).is(":checked") == true) {
        id_payments.push($(e).val());
    }
    else{
        const index = id_payments.indexOf($(e).val());
        if (index > -1) {
            id_payments.splice(index, 1);
        }
    }
    console.log(id_payments)
}
$('#multiple').click(function (e) { 
    $(this).attr({"href":"{{URL::to('inventory/purchase/multiple-cetak')}}"+'?id_payments='+id_payments.toString(), "target":"blank"});
});
function updateBillList() {
    var data = {
        date : $('#date').val(),
        date2 : $('#date2').val(),
        m_supplier_id : $('[name^=m_supplier_id]').val(),
    };
    var out = [];

    for (var key in data) {
        out.push(key + '=' + encodeURIComponent(data[key]));
    }
    url_data = out.join('&');
    dt.ajax.url('{{ URL::to('inventory/get_payment_list?') }}' + url_data).load();
}
function doShowDetail(id){
    dt_detail.clear().draw(false);
    $.ajax({
            type: "GET",
            url: "{{ URL::to('inventory/detail_payment') }}" + "/" + id, //json get site
            dataType : 'json',
            success: function(response){
                arrData = response['payment_supplier_ds'];
                for(i = 0; i < arrData.length; i++){
                    a = i+1;
                    dt_detail.row.add([
                        '<div class="text-left">'+a+'</div>',
                        '<div class="text-left">'+formatDateID(new Date((arrData[i]['pay_date']).substring(0,10)))+'</div>',
                        '<div class="text-center">'+(arrData[i]['wop'] == 'cash' ? 'Tunai' : (arrData[i]['wop'] == 'card' ? 'Kartu' : 'Transfer Bank'))+'</div>',
                        '<div class="text-right">'+arrData[i]['ref_code'] != null ? arrData[i]['ref_code']  : '-'+'</div>',
                        '<div class="text-right">'+arrData[i]['bank_name'] != null ? arrData[i]['bank_name']  : '-'+'</div>',
                        '<div class="text-right">'+arrData[i]['bank_number'] != null ? arrData[i]['bank_number']  : '-'+'</div>',
                        '<div class="text-right">'+arrData[i]['atas_nama'] != null ? arrData[i]['atas_nama']  : '-'+'</div>',
                        '<div class="text-right">'+arrData[i]['description'] != null ? arrData[i]['description']  : '-'+'</div>',
                        '<div class="text-right">'+formatCurrency(arrData[i]['amount'])+'</div>',
                    ]).draw(false);
                }
            }
    });
}
function doShowDetail2(id){
    po_detail.clear().draw(false);
    $.ajax({
            type: "GET",
            url: "{{ URL::to('po_konstruksi/detail') }}" + "/" + id, //json get site
            dataType : 'json',
            success: function(response){
                arrData = response['data'];
                for(i = 0; i < arrData.length; i++){
                    total=(parseFloat(arrData[i]['amount']) * parseFloat(arrData[i]['base_price'])).toFixed(0);
                    po_detail.row.add([
                        '<div class="text-left">'+arrData[i]['m_items']['no']+'</div>',
                        '<div class="text-left">'+arrData[i]['m_items']['name']+'</div>',
                        '<div class="text-right">'+arrData[i]['amount']+'</div>',
                        '<div class="text-center">'+arrData[i]['m_units']['name']+'</div>',
                        '<div class="text-right">'+formatCurrency(arrData[i]['base_price'])+'</div>',
                        '<div class="text-right">'+formatCurrency(total.toString())+'</div>'
                    ]).draw(false);
                }
            }
    });
}
function doShowDetail3(id){
    po_asset_detail.clear().draw(false);
    $.ajax({
            type: "GET",
            url: "{{ URL::to('po_konstruksi/detail_atk') }}" + "/" + id, //json get site
            dataType : 'json',
            success: function(response){
                arrData = response['data'];
                for(i = 0; i < arrData.length; i++){
                    total=(parseFloat(arrData[i]['amount']) * parseFloat(arrData[i]['base_price'])).toFixed(0);
                    po_asset_detail.row.add([
                        '<div class="text-left">'+arrData[i]['m_items']['no']+'</div>',
                        '<div class="text-left">'+arrData[i]['m_items']['name']+'</div>',
                        '<div class="text-right">'+arrData[i]['amount']+'</div>',
                        '<div class="text-center">'+arrData[i]['m_units']['name']+'</div>',
                        '<div class="text-right">'+formatCurrency(arrData[i]['base_price'])+'</div>',
                        '<div class="text-right">'+formatCurrency(total.toString())+'</div>'
                    ]).draw(false);
                }
            }
    });
}
function clickPrint(id) {
    setTimeout(() => {
        window.open("{{ URL::to('po_konstruksi/print') }}" + "/" + id, '_blank')
    }, 500);
}

</script>


@endsection