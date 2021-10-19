@extends('theme.default')

@section('breadcrumb')
			<div class="page-breadcrumb">
                <div class="row">
                    <div class="col-5 align-self-center">
                        <h4 class="page-title">Purchase Order Jasa</h4>
                        <div class="d-flex align-items-center">
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item active" aria-current="page">Purchase Order Jasa</li>
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
                            <a href="{{ URL::to('pengambilan_barang/request') }}"><button class="btn btn-success btn-sm mb-2"><i class="fas fa-plus"></i>&nbsp; Create New Request</button></a>
                        </div> -->
                        <div class="card">
                            <div class="card-body">
                                <h4 class="card-title">List Purchase Order Jasa</h4>
                                <div class="row">
                                    <div class="col-12">
                                        <form method="POST" action="#" class="form-inline float-right">
                                        @csrf
                                            <label>Cari Rentang Tanggal :</label>&nbsp;
                                            <input type="date" name="date"  id="date" class="form-control" required value="{{date('Y-m-d')}}">&nbsp;
                                            <input type="date" name="date2" id="date2" class="form-control" required value="{{date('Y-m-d')}}">&nbsp;
                                            <button class="btn btn-primary" type="button" onclick="updateBillList()"><i class="fa fa-search"></i></button>&nbsp;
                                            <button class="btn btn-success" type="button" onclick="exportData()"><i class="mdi mdi-file-excel"></i> Export Data</button>&nbsp;
                                        </form>
                                    </div>
                                </div>
                                <br>
                                <div class="table-responsive">
                                    <table id="po_list" class="table table-striped table-bordered">
                                        <thead>
                                            <tr>
                                                <th class="text-center">PO Number</th>
                                                <th class="text-center">Supplier Name</th>
                                                <th class="text-center">PO Value</th>
                                                <th class="text-center">PO Date</th>
                                                <th class="text-center">Way Of Payment</th>
                                                <th class="text-center">PO Status</th>
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


<div class="modal fade" id="modalShowDetail" tabindex="-1" role="dialog" aria-labelledby="modalShowDetailLabel1">
    <div class="modal-dialog  modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="modalAddMaterialLabel1">Purchase Order Detail</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table id="dt_detail" class="table table-bordered">
                        <thead>
                            <tr>
                                <th class="text-center">Nama Jasa</th>
                                <th class="text-center">Volume</th>
                                <th class="text-center">Satuan</th>
                                <th class="text-center">Perkiraan Harga</th>
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

$(document).ready(function(){

    var uri='{{URL::to('/')}}';
    $(document).ready(function() {
        dt = $('#po_list').DataTable( {
            "processing": true,
            "serverSide": true,
            "ajax": "{{ URL::to('pembelian_jasa/service_all') }}",
            aaSorting: [[3, 'desc']],
            "columns": [
                {"data": "no"},
                {"data": "m_suppliers.name"},
                {"data": "base_price", "class" : "text-right",
                "render": function(data, type, row){return formatCurrency(row.base_price)}},
                {"data": "purchase_date",
                "render": function(data, type, row){return formatDateID(new Date((row.purchase_date).substring(0,10)))}},
                {"data": "wop"},
                {"data": "is_closed",
                "render": function(data, type, row){return (row.is_closed ? 'Closed' : 'Open')}},
                {"data": "id",
                "render": function(data, type, row){return '<div class="text-center">'+(row.acc_ao == false ? '<a href="{{URL::to('pembelian_jasa/acc_ao_service_form')}}/'+row.id+'" class="btn btn-success waves-effect waves-light btn-sm"><i class="mdi mdi-pencil"></i></a>' : '<button type="button" onclick="doShowDetail('+row.id+');" data-toggle="modal" data-target="#modalShowDetail" class="btn btn-info waves-effect waves-light btn-sm">Detail</button> <button type="button" onclick="clickPrint('+row.id+');" class="btn btn-warning waves-effect waves-light btn-sm">Print</button>')+'</div>'}},
            ],
        } );
    });
});

function doShowDetail(id){
    dt_detail.clear().draw(false);
    $.ajax({
            type: "GET",
            url: "{{ URL::to('pembelian_jasa/detail_service') }}" + "/" + id, //json get site
            dataType : 'json',
            success: function(response){
                arrData = response['data'];
                for(i = 0; i < arrData.length; i++){
                    dt_detail.row.add([
                        '<div class="text-left">'+arrData[i]['service_name']+'</div>',
                        '<div class="text-right">'+arrData[i]['amount']+'</div>',
                        '<div class="text-center">'+arrData[i]['m_units']['name']+'</div>',
                        '<div class="text-right">'+formatCurrency(arrData[i]['base_price'])+'</div>'
                    ]).draw(false);
                }
            }
    });
}
function updateBillList() {
    var data = {
        date : $('#date').val(),
        date2 : $('#date2').val(),
    };
    var out = [];

    for (var key in data) {
        out.push(key + '=' + encodeURIComponent(data[key]));
    }
    url_data = out.join('&');
    dt.ajax.url('{{ URL::to('pembelian_jasa/service_all') }}?' + url_data).load();
}
function exportData() {
    var date = $('#date').val()
    var date2 = $('#date2').val()
    setTimeout(() => {
        window.open("{{ URL::to('po_konstruksi/export_po_service') }}" + "?date=" + date + "&date2=" + date2, '_blank')
    }, 500);
}
function clickPrint(id) {
    setTimeout(() => {
        window.open("{{ URL::to('po_konstruksi/print_jasa') }}" + "/" + id, '_blank')
    }, 500);
}

</script>


@endsection