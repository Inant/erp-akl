@extends('theme.default')

@section('breadcrumb')
			<div class="page-breadcrumb">
                <div class="row">
                    <div class="col-5 align-self-center">
                        <h4 class="page-title">Penerimaan Jasa</h4>
                        <div class="d-flex align-items-center">
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item active" aria-current="page">Penerimaan Jasa</li>
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
                                <h4 class="card-title">List Penerimaan Jasa</h4>
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

    $(document).ready(function() {
        // t = $('#po_list').DataTable();
        t = $('#po_list').DataTable( {
            "processing": true,
            "serverSide": true,
            "ajax": "{{ URL::to('penerimaan_service/list') }}",
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
                "render": function(data, type, row){return '<div class="text-center">'+(row.is_closed == false ? '<a href="{{ URL::to('penerimaan_barang/decline') }}'+'/'+row.id+'" onclick="return confirm_click();"><button type="button" class="btn btn-danger waves-effect waves-light btn-sm"><i class="mdi mdi-close"></i></button></a> <a href="{{ URL::to('penerimaan_service/receive') }}'+'/'+row.id+'"><button type="button" class="btn btn-warning waves-effect waves-light btn-sm"><i class="fas fa-pencil-alt"></i></button></a>' : ' <button type="button" onclick="clickPrint('+row.id+');" class="btn btn-info waves-effect waves-light btn-sm" title="print"><i class="fa fa-print"></></button>')+'</div>'}},
            ],
        } );
    });
});
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