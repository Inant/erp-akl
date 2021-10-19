@extends('theme.default')

@section('breadcrumb')
			<div class="page-breadcrumb">
                <div class="row">
                    <div class="col-5 align-self-center">
                        <h4 class="page-title">Pembelian Khusus</h4>
                        <div class="d-flex align-items-center">
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item active" aria-current="page">Pembelian Khusus</li>
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
                                <h4 class="card-title">List Pembelian Khusus</h4>
                                <div class="table-responsive">
                                    <table id="zero_config" class="table table-striped table-bordered">
                                        <thead>
                                            <tr>
                                                <th class="text-center">PO Number</th>
                                                <th class="text-center">Supplier Name</th>
                                                <th class="text-center">Estimate</th>
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

<script src="{!! asset('theme/assets/libs/jquery/dist/jquery.min.js') !!}"></script>
<script src="{!! asset('theme/assets/extra-libs/DataTables/datatables.min.js') !!}"></script>
<script>

dt_detail = $('#dt_detail').DataTable();

$(document).ready(function(){
    // console.log(arrMaterialPembelianRutin);
    t = $('#zero_config').DataTable();
    t.clear().draw(false);
    $.ajax({
            type: "GET",
            url: "{{ URL::to('pembelian_khusus/list/all') }}", //json get site
            dataType : 'json',
            success: function(response){
                arrData = response['data'];
                for(i = 0; i < arrData.length; i++){
                    urlReceive = "{{ URL::to('pembelian_khusus') }}" + "/" +arrData[i]['id'];
                    t.row.add([
                        '<div class="text-center">'+arrData[i]['no']+'</div>',
                        '<div class="text-left">'+(arrData[i]['m_supplier_id'] != null ? arrData[i]['m_suppliers']['name'] : "-")+'</div>',
                        '<div class="text-right">'+(arrData[i]['base_price'] != null ? arrData[i]['base_price'] : "-") +'</div>',
                        '<div class="text-center">'+(arrData[i]['purchase_date']).substring(0,10)+'</div>',
                        '<div class="text-center">'+(arrData[i]['wop'] != null ? arrData[i]['wop'] : "-")+'</div>',
                        '<div class="text-center">'+(arrData[i]['is_closed'] ? 'Closed' : 'Open')+'</div>',
                        '<div class="text-center"><a href="'+urlReceive+'"><button type="button" class="btn btn-warning waves-effect waves-light btn-sm"><i class="fas fa-pencil-alt"></i></button></a></div>'
                    ]).draw(false);
                }
            }
    });
});


</script>

@endsection