@extends('theme.default')

@section('breadcrumb')
<div class="page-breadcrumb">
    <div class="row">
        <div class="col-5 align-self-center">
            <h4 class="page-title">Hapus Pengeluaran Material</h4>
            <div class="d-flex align-items-center">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item active" aria-current="page">Hapus Pengeluaran Material</li>
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
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">List Pengeluaran Material</h4>
                    <div class="table-responsive">
                        <table id="pengeluaran_list" class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th class="text-center">Tanggal</th>
                                    <th class="text-center">No Pengeluaran</th>
                                    <th class="text-center">Request Number</th>
                                    <th class="text-center">RAB Number</th>
                                    <th class="text-center">SPK Number</th>
                                    <th class="text-center">Project Name</th>
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
function formatDate(date) {
  var temp=date.split(/[.,\/ -]/);
  return temp[2] + '-' + temp[1] + '-' + temp[0];
}
function confirm_click(){
    return !confirm("Memo yakin dihapus ?") ? false : true;
}
$(document).ready(function(){
    $('#pengeluaran_list').DataTable( {
        "processing": true,
        "serverSide": true,
        "ajax": "{{ URL::to('pengeluaran_barang/list_pengeluaran_material') }}",
        "aaSorting": [[ 0, "desc" ]],
        "columns": [
            {"data": "inv_trx_date"},
            {"data": "no_trx"},
            {"data": "no_req"},
            {"data": "no_rab"},
            {"data": "spk_number"},
            {"data": "project_name"},
            {"data": "user_auth", "render": function(data, type, row){
                return row.user_auth != null ? row.user_auth : '-'}},
            {"data": "inv_trx_id", "render": function(data, type, row){
                return '<a href="'+uri+'/pengeluaran_barang/hapus/'+row.inv_trx_id+'" onclick="return confirm_click();" class="btn btn-xs btn-danger" class="confirmation">Hapus</a>';
            }}
        ],
    } );
});

var elems = document.getElementsByClassName('confirmation');
var confirmIt = function (e) {
    if (!confirm('Are you sure?')) e.preventDefault();
};
for (var i = 0, l = elems.length; i < l; i++) {
    elems[i].addEventListener('click', confirmIt, false);
}

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
                var label='';
                for(i = 0; i < arrData3.length; i++){
                    label+=arrData3[i]['no']+', ';
                }
                $('#label-detail').html('Untuk Pengerjaan Produk : '+label.replace(/, +$/, ''));

                for(i = 0; i < arrData.length; i++){
                    let amount_auth = arrData[i]['amount_auth'] != null ? arrData[i]['amount_auth'] : arrData[i]['amount'];
                    t2.row.add([
                        '<div class="text-left">'+arrData[i]['m_items']['no']+'</div>',
                        '<div class="text-left">'+arrData[i]['m_items']['name']+'</div>',
                        '<div class="text-right">'+parseFloat(arrData[i]['amount'])+'</div>',
                        '<div class="text-center">'+arrData[i]['m_units']['name']+'</div>'
                    ]).draw(false);
                }
                for(i = 0; i < arrData2.length; i++){
                    t3.row.add([
                        '<div class="text-left">'+arrData2[i]['m_items']['no']+'</div>',
                        '<div class="text-left">'+arrData2[i]['m_items']['name']+'</div>',
                        '<div class="text-right">'+parseFloat(arrData2[i]['amount'])+'</div>',
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