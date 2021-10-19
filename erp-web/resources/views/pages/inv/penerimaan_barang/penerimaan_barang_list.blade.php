@extends('theme.default')

@section('breadcrumb')
			<div class="page-breadcrumb">
                <div class="row">
                    <div class="col-5 align-self-center">
                        <h4 class="page-title">Penerimaan Barang</h4>
                        <div class="d-flex align-items-center">
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item active" aria-current="page">Penerimaan Barang</li>
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
                                <h4 class="card-title">List Penerimaan Barang</h4>
                                <div class="col-sm-4"> 
                                    <select name="m_item_id" id="m_item_id" class="form-control col-sm-4 select2" onchange="cekWithMaterial(this)" style="width:100%">
                                    </select>
                                </div>
                                <br>
                                <div class="table-responsive">
                                    <table id="po_list" class="table table-striped table-bordered">
                                        <thead>
                                            <tr>
                                                <th class="text-center">PO Number</th>
                                                <th class="text-center">Supplier Name</th>
                                                <th class="text-center">Site Name</th>
                                                <th class="text-center">PO Date</th>
                                                <th class="text-center">Status</th>
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

$(document).ready(function(){
    var arrMaterial = [];
    var formMaterial = $('[id^=m_item_id]');
    $.ajax({
        type: "GET",
        url: "{{ URL::to('material_request/get_material') }}", //json get material
        dataType : 'json',
        success: function(response){
            arrMaterial = response['data'];
            formMaterial.append('<option value="">--Cari dari Material--</option>');
            $.each(arrMaterial, function(i) {
                // console.log(item);
                formMaterial.append('<option value="'+arrMaterial[i]['id']+'">'+arrMaterial[i]['name']+'</option>');
            });
        }
    });

    $(document).ready(function() {
        // t = $('#po_list').DataTable();
        t = $('#po_list').DataTable( {
            "processing": true,
            "serverSide": true,
            "ajax": "{{ URL::to('penerimaan_barang/list') }}",
            aaSorting: [[3, 'desc']],
            "columns": [
                {"data": "no"},
                {"data": "m_suppliers.name"},
                {"data": "sites.name"},
                {"data": "purchase_date",
                "render": function(data, type, row){return formatDateID(new Date((row.purchase_date).substring(0,10)))}},
                {"data": "is_closed",
                "render": function(data, type, row){return (row.is_closed ? 'Closed' : 'Open')}},
                {"data": "id",
                "render": function(data, type, row){return '<div class="text-center">'+(row.is_closed == false ? '<a href="{{ URL::to('penerimaan_barang/decline') }}'+'/'+row.id+'" onclick="return confirm_click();"><button type="button" class="btn btn-danger waves-effect waves-light btn-sm"><i class="mdi mdi-close"></i></button></a> <a href="{{ URL::to('penerimaan_barang/receive') }}'+'/'+row.id+'"><button type="button" class="btn btn-warning waves-effect waves-light btn-sm"><i class="fas fa-pencil-alt"></i></button></a>' : ' <button type="button" onclick="clickPrint('+row.id+');" class="btn btn-info waves-effect waves-light btn-sm" title="print"><i class="fa fa-print"></></button>')+'</div>'}},
            ],
        } );
    });
    // console.log(arrMaterialPembelianRutin);
    // t = $('#zero_config').DataTable();
    // t.clear().draw(false);
    // $.ajax({
    //         type: "GET",
    //         url: "{{ URL::to('penerimaan_barang/list') }}", //json get site
    //         dataType : 'json',
    //         success: function(response){
    //             arrData = response['data'];
    //             console.log(response);
    //             for(i = 0; i < arrData.length; i++){
    //                 urlReceive = "{{ URL::to('penerimaan_barang/receive') }}" + "/" +arrData[i]['id'];
    //                 urlDecline = "{{ URL::to('penerimaan_barang/decline') }}" + "/" +arrData[i]['id'];
    //                 // var tanggal=(arrData[i]['is_closed'] ? arrData[i]['updated_at'] : arrData[i]['purchase_date']);
    //                 t.row.add([
    //                     '<div class="text-left">'+arrData[i]['no']+'</div>',
    //                     '<div class="text-left">'+arrData[i]['m_suppliers']['name']+'</div>',
    //                     '<div class="text-left">'+arrData[i]['sites']['name']+'</div>',
    //                     '<div class="text-center">'+formatDateID(new Date((arrData[i]['purchase_date']).substr(0,10)))+'</div>',
    //                     '<div class="text-center">'+(arrData[i]['is_closed'] ? 'Closed' : 'Open')+'</div>',
    //                     '<div class="text-center">'+(arrData[i]['is_closed'] == false ? '<a href="'+urlDecline+'" onclick="return confirm_click();"><button type="button" class="btn btn-danger waves-effect waves-light btn-sm"><i class="mdi mdi-close"></i></button></a> <a href="'+urlReceive+'"><button type="button" class="btn btn-warning waves-effect waves-light btn-sm"><i class="fas fa-pencil-alt"></i></button></a>' : ' <button type="button" onclick="clickPrint('+arrData[i]['id']+');" class="btn btn-info waves-effect waves-light btn-sm" title="print"><i class="fa fa-print"></></button>')+'</div>'
    //                 ]).draw(false);
    //             }
    //         }
    // });
});
function cekWithMaterial(eq){
    $('#po_list').DataTable().clear().destroy();
    $('#po_list').DataTable( {
            "processing": true,
            "serverSide": true,
            "type": "GET",
            "ajax": "{{ URL::to('penerimaan_barang/cek_po_by_material?m_item_id=') }}"+eq.value, //json get site
            aaSorting: [[0, 'desc']],
            "columns": [
                {"data": "no"},
                {"data": "m_suppliers.name"},
                {"data": "sites.name"},
                {"data": "purchase_date",
                "render": function(data, type, row){return formatDateID(new Date((row.purchase_date).substring(0,10)))}},
                {"data": "is_closed",
                "render": function(data, type, row){return (row.is_closed ? 'Closed' : 'Open')}},
                {"data": "id",
                "render": function(data, type, row){return '<div class="text-center">'+(row.is_closed == false ? '<a href="{{ URL::to('penerimaan_barang/decline') }}'+'/'+row.id+'" onclick="return confirm_click();"><button type="button" class="btn btn-danger waves-effect waves-light btn-sm"><i class="mdi mdi-close"></i></button></a> <a href="{{ URL::to('penerimaan_barang/receive') }}'+'/'+row.id+'"><button type="button" class="btn btn-warning waves-effect waves-light btn-sm"><i class="fas fa-pencil-alt"></i></button></a>' : ' <button type="button" onclick="clickPrint('+row.id+');" class="btn btn-info waves-effect waves-light btn-sm" title="print"><i class="fa fa-print"></></button>')+'</div>'}},
            ],
        } );
    // $.ajax({
    //         type: "GET",
    //         url: "{{ URL::to('penerimaan_barang/cek_po_by_material') }}", //json get site
    //         dataType : 'json',
    //         data : {m_item_id : eq.value},
    //         success: function(response){
    //             arrData = response['data'];
    //             console.log(response);
    //             for(i = 0; i < arrData.length; i++){
    //                 urlReceive = "{{ URL::to('penerimaan_barang/receive') }}" + "/" +arrData[i]['id'];
    //                 urlDecline = "{{ URL::to('penerimaan_barang/decline') }}" + "/" +arrData[i]['id'];
    //                 // var tanggal=(arrData[i]['is_closed'] ? arrData[i]['updated_at'] : arrData[i]['purchase_date']);
    //                 t.row.add([
    //                     '<div class="text-left">'+arrData[i]['no']+'</div>',
    //                     '<div class="text-left">'+arrData[i]['m_suppliers']['name']+'</div>',
    //                     '<div class="text-left">'+arrData[i]['sites']['name']+'</div>',
    //                     '<div class="text-center">'+formatDateID(new Date((arrData[i]['purchase_date']).substr(0,10)))+'</div>',
    //                     '<div class="text-center">'+(arrData[i]['is_closed'] ? 'Closed' : 'Open')+'</div>',
    //                     '<div class="text-center">'+(arrData[i]['is_closed'] == false ? '<a href="'+urlDecline+'" onclick="return confirm_click();"><button type="button" class="btn btn-danger waves-effect waves-light btn-sm"><i class="mdi mdi-close"></i></button></a> <a href="'+urlReceive+'"><button type="button" class="btn btn-warning waves-effect waves-light btn-sm"><i class="fas fa-pencil-alt"></i></button></a>' : ' <button type="button" onclick="clickPrint('+arrData[i]['id']+');" class="btn btn-info waves-effect waves-light btn-sm" title="print"><i class="fa fa-print"></></button>')+'</div>'
    //                 ]).draw(false);
    //             }
    //         }
    // });
}
function confirm_click(){
    return confirm("PO yakin dibatalkan ?");
}

function clickPrint(id) {
    setTimeout(() => {
        window.open("{{ URL::to('penerimaan_barang/print') }}" + "/" + id, '_blank')
    }, 500);
}

</script>


@endsection