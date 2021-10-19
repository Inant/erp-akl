@extends('theme.default')

@section('breadcrumb')
<div class="page-breadcrumb">
    <div class="row">
        <div class="col-5 align-self-center">
            <h4 class="page-title">List Material Request</h4>
            <div class="d-flex align-items-center">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item active" aria-current="page">List Material Request</li>
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
            <!-- <div class="text-right">
                <a href="{{ URL::to('material_request/request') }}"><button class="btn btn-success btn-sm mb-2"><i class="fas fa-plus"></i>&nbsp; Create New Request</button></a>
            </div> -->
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">List Material Request</h4>
                    <div class="table-responsive">
                        <table id="pengeluaran_list" class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th class="text-center">Request Number</th>
                                    <th class="text-center">RAB Number</th>
                                    <th class="text-center">No SPK</th>
                                    <th class="text-center">Site Name</th>
                                    <th class="text-center">Kavling</th>
                                    <th class="text-center">Request Type</th>
                                    <th class="text-center">Authorize</th>
                                    <th class="text-center">Action</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>       
</div>

<form method="POST" action="{{ URL::to('pengeluaran_barang') }}" class="form-horizontal">
    @csrf
    <div class="modal fade bs-example-modal-lg" id="modalTransferDetail" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" style="display: none;">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myLargeModalLabel">Material List Material Request</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                </div>
                <div class="modal-body">
                    <h4>Material Request Detail</h4>
                    <div class="table-responsive">
                        <input type="hidden" name="inv_request_id" />
                        <div class="form-group row">
                            <label class="col-sm-3 text-right control-label col-form-label">Kontraktor / Mandor</label>
                            <div class="col-sm-9">
                                <input type="text" required id="mandor" name="mandor" class="form-control text-left">
                            </div>
                        </div>
                        <table id="zero_config1" class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th class="text-center">Material No</th>
                                    <th class="text-center">Material Name</th>
                                    <!-- <th class="text-center">Pilih</th> -->
                                    <th class="text-center">Site Stock</th>
                                    <th class="text-center">Qty Pengajuan</th>
                                    <th class="text-center">Qty Yang Disetujui</th>
                                    <th class="text-center">Satuan</th>
                                </tr>
                            </thead>
                            <tbody>
                                
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" onclick="clickRequest()" class="btn btn-primary waves-effect btn-sm text-left">Submit</button>
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
    <!-- /.modal -->
</form>

<div class="modal fade bs-example-modal-lg" id="modalDetail" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myLargeModalLabel">Material List Material Request</h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <div class="modal-body">
                <h4>Material Request Detail</h4>
                <p id="label-detail"></p>
                <div class="table-responsive">
                <h4>Material Utuh</h4>
                    <table id="zero_config2" class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th class="text-center">Material No</th>
                                <th class="text-center">Material Name</th>
                                <!-- <th class="text-center">Pilih</th> -->
                                <!-- <th class="text-center">Qty Pengajuan</th> -->
                                <th class="text-center">Qty</th>
                                <th class="text-center">Satuan</th>
                            </tr>
                        </thead>
                        <tbody>
                            
                        </tbody>
                    </table>
                    <hr>
                    <h4>Material Tidak Utuh</h4>
                    <table id="zero_config3" class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th class="text-center">Material No</th>
                                <th class="text-center">Material Name</th>
                                <!-- <th class="text-center">Pilih</th> -->
                                <th class="text-center">Kondisi</th>
                                <th class="text-center">Qty</th>
                                <th class="text-center">Satuan</th>
                            </tr>
                        </thead>
                        <tbody>
                            
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<script src="{!! asset('theme/assets/libs/jquery/dist/jquery.min.js') !!}"></script>
<script src="{!! asset('theme/assets/extra-libs/DataTables/datatables.min.js') !!}"></script>
<script>
var url='{{URL::to('/')}}';
var idRequest = null;

// List Stock
var listStockSite = [];

var valid = true;

var t2 = $('#zero_config2').DataTable();
var t3 = $('#zero_config3').DataTable();
var uri='{{URL::to('/')}}';
$(document).ready(function(){
    $('#pengeluaran_list').DataTable( {
        "processing": true,
        "serverSide": true,
        "ajax": "{{ URL::to('material_request/acc_list') }}",
        "aaSorting": [[ 6, "desc" ]],
        "columns": [
            {"data": "no"},
            {"data": "rab_no"},
            {"data": "spk_number"},
            {"data": "site_name"},
            {"data": "project_name"},
            {"data": "req_type", "render": function(data, type, row){
                return row.req_type == 'REQ_ITEM' ? 'Permintaan Normal' : 'Permintaan Khusus'}},
            {"data": "user_auth", "render": function(data, type, row){
                return row.user_auth != null ? row.user_auth : '-'}},
            {"data": "id", "render": function(data, type, row){
                return '<button onclick="doShowDetail2('+row.id+');" data-toggle="modal" data-target="#modalDetail" class="btn waves-effect waves-light btn-xs btn-info">Detail</button>'+'&nbsp;<a href="'+uri+'/pengeluaran_barang/form/'+row.id+'" class="btn btn-xs btn-success">pengeluaran</a>';
            }}
        ],
    } );
});

$(document).ready(async function(){
    // Get Stock
    await $.ajax({
        type: "GET",
        url: "{{ URL::to('inventory/stok_json') }}", //json get site
        dataType : 'json',
        success: function(response){
            arrData = response['data'];
            listStockSite = arrData;
        }
    });

    // console.log(arrMaterialPembelianRutin);
    // t = $('#zero_config').DataTable();
    // t.clear().draw(false);
    // $.ajax({
    //         type: "GET",
    //         url: "{{ URL::to('pengeluaran_barang/list') }}", //json get site
    //         dataType : 'json',
    //         success: function(response){
    //             arrData = response['data'];
    //             for(i = 0; i < arrData.length; i++){
    //                 // urlReceive = "{{ URL::to('pengambilan_barang/detail') }}" + "/" +arrData[i]['id'];
    //                 if ((arrData[i]['req_type'] == 'REQ_ITEM' || arrData[i]['user_auth'] != null) && arrData[i]['rab_id'] != null ){
    //                     t.row.add([
    //                         '<div class="text-left">'+arrData[i]['no']+'</div>',
    //                         '<div class="text-left">'+arrData[i]['rab_no']+'</div>',
    //                         '<div class="text-center">'+arrData[i]['site_name']+'</div>',
    //                         '<div class="text-center">'+arrData[i]['project_name']+'</div>',
    //                         '<div class="text-center">'+ (arrData[i]['req_type'] == 'REQ_ITEM' ? 'Permintaan Normal' : 'Permintaan Khusus') +'</div>',
    //                         '<div class="text-center">'+ (arrData[i]['user_auth'] != null ? arrData[i]['user_auth'] : '-') +'</div>',
    //                         '<div class="text-center"><button onclick="doShowDetail('+arrData[i]['id']+');" data-toggle="modal" data-target="#modalTransferDetail" class="btn waves-effect waves-light btn-xs btn-warning openModalTransferDetail">Pengeluaran</button>'+
    //                         '<a href="/pengeluaran_barang/form/'+arrData[i]['id']+'" class="btn btn-xs btn-success">pengeluaran</a></div>'
    //                     ]).draw(false);
    //                 }
    //             }
    //         }
    // });
});

// function doShowDetail(id){
//     idRequest = id;
//     $('[name^=inv_request_id]').val(id);
//     t2.clear().draw(false);
//     $.ajax({
//             type: "GET",
//             url: "{{ URL::to('material_request/list_detail') }}" + "/" + id, //json get site
//             dataType : 'json',
//             success: function(response){
//                 arrData = response['data']['detail'];
//                 for(i = 0; i < arrData.length; i++){
//                     stok = 0;
//                     listStockSite.map((item, obj) => {
//                         if (item.m_item_id == arrData[i]['m_item_id'])
//                             stok += parseInt(item.stok);
//                     });

//                     let amount_auth = arrData[i]['amount_auth'] != null ? arrData[i]['amount_auth'] : arrData[i]['amount'];
//                     t2.row.add([
//                         '<div class="text-left">'+arrData[i]['m_items']['no']+'</div>',
//                         '<input type="hidden" name="inv_request_d_id[]" value="'+arrData[i]['id']+'" /><input type="hidden" name="m_item_id[]" value="'+arrData[i]['m_item_id']+'" /><div class="text-left">'+arrData[i]['m_items']['name']+'</div>',
//                         // '<div class="text-right"><select id="inv_no" name="inv_no" required class="form-control select2 custom-select" style="width: 100%; height:32px;">'+
//                         //             +'<option value="">--- Select Inv Number ---</option>'+
//                         //         +'</select></div>',
//                         '<input type="hidden" id="stok[]" name="stok[]" value="'+parseFloat(stok)+'" /> <div class="text-right">'+parseFloat(stok)+'</div>',
//                         '<div class="text-right">'+parseFloat(arrData[i]['amount'])+'</div>',
//                         '<div class="text-right"><input type="hidden" id="qty[]" name="qty[]" step="any" min="0" class="form-control text-right" placeholder="0" value="'+parseFloat(amount_auth)+'">'+parseFloat(amount_auth)+'</div>',
//                         '<input type="hidden" name="m_unit_id[]" value="'+arrData[i]['m_unit_id']+'" /><div class="text-center">'+arrData[i]['m_units']['name']+'</div>'
//                     ]).draw(false);
//                     formInvNo = $('[id^=inv_no]');
//                     formInvNo.empty();
//                     formInvNo.append('<option value="">-- Select Inv Number --</option>');
//                     listStockSite.map((item, obj) => {
//                         if (item.m_item_id == arrData[i]['m_item_id'])
//                         formInvNo.append('<option value="'+item.purchase_d_id+'">'+item.no+'</option>');
//                     });
//                     if(stok < amount_auth)
//                         valid = false;
//                 }
//             }
//     });
// }

function isInt(n) {
    if (n % 1 === 0){
        return true;
    }
    else{
        return false;
    }
}

function doShowDetail2(id){
    idRequest = id;
    t2.clear().draw(false);
    t3.clear().draw(false);
    $.ajax({
            type: "GET",
            url: "{{ URL::to('material_request/list_detail_acc') }}" + "/" + id, //json get site
            dataType : 'json',
            success: function(response){
                arrData = response['data']['detail'];
                arrData2 = response['data']['detail_rest'];
                arrData3 = response['data']['prod_sub'];
                // var totalProjectReq = parseInt(response['data']['totalProjectReq']['total']);
                var label='';
                for(i = 0; i < arrData3.length; i++){
                    label+=arrData3[i]['no']+', ';
                }
                $('#label-detail').html('Untuk Pengerjaan Produk : '+label.replace(/, +$/, ''));

                for(i = 0; i < arrData.length; i++){
                    // let amount_auth = arrData[i]['amount_auth'] != null ? arrData[i]['amount_auth'] : arrData[i]['amount'];
                    // let amount = parseFloat(arrData[i]['amount']) * totalProjectReq;
                    // amount = isInt(amount) ? amount : amount.toFixed(0);
                    t2.row.add([
                        '<div class="text-left">'+arrData[i]['m_items']['no']+'</div>',
                        '<div class="text-left">'+arrData[i]['m_items']['name']+'</div>',
                        '<div class="text-right">'+arrData[i]['amount']+'</div>',
                        '<div class="text-center">'+arrData[i]['m_units']['name']+'</div>'
                    ]).draw(false);
                }
                for(i = 0; i < arrData2.length; i++){
                    // let amount = parseFloat(arrData2[i]['amount']) * totalProjectReq;
                    // amount = isInt(amount) ? amount : amount.toFixed(0);
                    t3.row.add([
                        '<div class="text-left">'+arrData2[i]['m_items']['no']+'</div>',
                        '<div class="text-left">'+arrData2[i]['m_items']['name']+'</div>',
                        '<div class="text-right">'+arrData2[i]['m_items']+'</div>',
                        '<div class="text-right">'+parseFloat(arrData2[i]['total'])+'</div>',
                        '<div class="text-center">'+arrData2[i]['m_units']['name']+'</div>'
                    ]).draw(false);
                }
            }
    });
}

function clickRequest() {
    mandor = document.getElementById("mandor");
    if (mandor.checkValidity()) {
        setTimeout(() => {
            window.open("{{ URL::to('pengeluaran_barang/print') }}" + "/" + idRequest, '_blank')
        }, 500);
    }
}


</script>


@endsection