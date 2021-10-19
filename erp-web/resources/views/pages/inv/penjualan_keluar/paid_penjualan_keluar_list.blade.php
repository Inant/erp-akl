@extends('theme.default')

@section('breadcrumb')
			<div class="page-breadcrumb">
                <div class="row">
                    <div class="col-5 align-self-center">
                        <h4 class="page-title">Pembayaran Penjualan Material</h4>
                        <div class="d-flex align-items-center">
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item active" aria-current="page">Daftar</li>
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
        
        <div class="col-12">
            <div class="text-right">
                <a href="{{ URL::to('penjualan_keluar/paid_form') }}"><button class="btn btn-success btn-sm mb-2"><i class="fas fa-plus"></i>&nbsp; Create New</button></a>
            </div>
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Pembayaran Penjualan Material</h4>
                    <div class="form-group float-right">
                        <button class="btn btn-primary" onclick="listDetail()" data-toggle="modal" data-target="#modalListDetail">List Detail</button>
                    </div>
                    <div class="table-responsive">
                        <table id="paid_list" class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th class="text-center">No</th>
                                    <th class="text-center">No Penjualan Material</th>
                                    <th class="text-center">Total</th>
                                    <th class="text-center">Customer</th>
                                    <th class="text-center">Tanggal</th>
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
<div class="modal fade bs-example-modal-lg" id="modalDetail" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myLargeModalLabel">Detail Pembayaran</h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <div class="modal-body">
                <table id="listDetail" class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th class="text-center">No Penjualan</th>
                            <th class="text-center">Total</th>
                        </tr>
                    </thead>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger waves-effect btn-sm text-left" data-dismiss="modal">Close</button>
            </div>
            </form>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<div class="modal fade bs-example-modal-lg" id="modalListDetail" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myLargeModalLabel">Detail Pembayaran</h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table id="listDetailAll" class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th class="text-center">No Penjualan</th>
                                <th class="text-center">No Pembayaran</th>
                                <th class="text-center">Tanggal</th>
                                <th class="text-center">Customer</th>
                                <th class="text-center">Total</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger waves-effect btn-sm text-left" data-dismiss="modal">Close</button>
            </div>
            </form>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<script src="{!! asset('theme/assets/libs/jquery/dist/jquery.min.js') !!}"></script>
<script src="{!! asset('theme/assets/extra-libs/DataTables/datatables.min.js') !!}"></script>
<script>
var uri='{{URL::to('/')}}';
$(document).ready(function() {
    $('#paid_list').DataTable( {
        "processing": true,
        "serverSide": true,
        "ajax": "{{ URL::to('/penjualan_keluar/paid_list') }}",
        'aaSorting' : [3, 'desc'],
        "columns": [
            {"data": "no", "class" : "text-center"},
            {"data": "no_sale", "class" : "text-center"},
            {"data": "amount", "class" : "text-center", "render": function(data, type, row){
                var amount=parseFloat(row.amount)
                return formatCurrency(amount.toFixed(2));
            }},
            {"data": "coorporate_name", "class" : "text-center"},
            {"data": "paid_date", "class" : "text-center",
            "render": function(data, type, row){return row.paid_date != null ? formatDateID(new Date((row.paid_date).substring(0,10))) : '-'}},
            {"data": "id", "render": function(data, type, row){
                return '<button onclick="doShowDetail('+row.id+');" data-toggle="modal" data-target="#modalDetail" class="btn waves-effect waves-light btn-xs btn-info">Detail</button>';
            }}
        ],
    } );
    $('#listDetailAll').DataTable( {
        "processing": true,
        "serverSide": true,
        "ajax": "{{ URL::to('penjualan_keluar/paid_all_detail') }}",
        'aaSorting' : [3, 'desc'],
        "columns": [
            {"data": "bill_no", "class" : "text-center"},
            {"data": "paid_no", "class" : "text-center"},
            {"data": "paid_date", "class" : "text-center",
            "render": function(data, type, row){return row.paid_date != null ? formatDateID(new Date((row.paid_date).substring(0,10))) : '-'}},
            {"data": "coorporate_name", "class" : "text-center"},
            {"data": "amount", "class" : "text-right", "render": function(data, type, row){
                var amount=parseFloat(row.amount)
                return formatCurrency(amount.toFixed(2));
            }}
        ],
    } );
});
function doShowDetail(eq){
    t2 = $('#listDetail').DataTable();
    t2.clear().draw(false);
    $.ajax({
        url: "{{ URL::to('penjualan_keluar/paid_list_detail') }}"+'/'+eq,
        dataType : 'json',
        success: function(response){
            arrData = response['data'];
            for(i = 0; i < arrData.length; i++){
                t2.row.add([
                    '<div class="text-left">'+arrData[i]['no']+'</div>',
                    '<div class="text-right">'+formatCurrency(parseInt(arrData[i]['amount']))+'</div>'
                ]).draw(false);
            }
        }
    });
}
function countAmortisasi(total){
    var total_asset=$('#total_asset').val();
    var amortisasi_month=total_asset / parseFloat(total);
    $('#amount').val(formatCurrency(amortisasi_month));
}
</script>


@endsection