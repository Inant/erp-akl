@extends('theme.default')

@section('breadcrumb')
			<div class="page-breadcrumb">
                <div class="row">
                    <div class="col-5 align-self-center">
                        <h4 class="page-title">Daftar Asset</h4>
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
            <!-- <div class="text-right">
                <a href="{{ URL::to('inventory/add_acc_prod') }}"><button class="btn btn-success btn-sm mb-2"><i class="fas fa-plus"></i>&nbsp; Create New</button></a>
            </div> -->
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Daftar Asset</h4>
                    <div class="table-responsive">
                        <table id="acc_list" class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th class="text-center">No</th>
                                    <th class="text-center">Nama </th>
                                    <th class="text-center">Unit</th>
                                    <th class="text-center">Total</th>
                                    <th class="text-center">Total Amortisasi per Bulan</th>
                                    <!-- <th class="text-center">Persen Amortisasi</th> -->
                                    <th class="text-center">Lama Amortisasi</th>
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
<div class="modal fade bs-example-modal-lg" id="modalAccDetail" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myLargeModalLabel">Input Amortisasi</h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <form method="POST" action="{{ URL::to('inventory/add_amortisasi') }}">
                @csrf
            <div class="modal-body">
                <h4>Form Amortisasi</h4>

                    <input type="hidden" id="id" name="id">
                    <div class="form-group">
                        <label id="aseet_total">Total Asset : </label>
                        <input type="hidden" class="form-control" name="total" id="total_asset">
                    </div>
                    <div class="form-group">
                        <label>Lama Amortisasi</label>
                        <input type="text" class="form-control" name="total_bulan" id="total_bulan" onkeyup="countAmortisasi(this.value)">
                    </div>
                    <div class="form-group">
                        <label>Lama Amortisasi (yang di simpan)</label>
                        <input type="text" class="form-control" name="total_bulan_save" required>
                    </div>
                    <div class="form-group">
                        <label>Jumlah Amortisasi</label>
                        <input type="text" readonly class="form-control" name="amount" id="amount">
                    </div>
                    <!-- <div class="form-group">
                        <label>Lama Amortisasi</label>
                        <input type="month"  class="form-control" name="date">
                    </div> -->
                
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger waves-effect btn-sm text-left" data-dismiss="modal">Close</button>
                <button class="btn btn-success waves-effect btn-sm text-left">Simpan</button>
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
    $('#acc_list').DataTable( {
        "processing": true,
        "serverSide": true,
        "ajax": "{{ URL::to('/inventory/list_material_asset') }}",
        "columns": [
            {"data": "no", "class" : "text-center"},
            {"data": "name", "class" : "text-center"},
            {"data": "unit_name", "class" : "text-center"},
            // {"data": "amount", "class" : "text-center", "class" : "text-center", "render": function(data, type, row){
            //     return parseFloat(row.amount);
            // }},
            {"data": "base_price", "class" : "text-center", "render": function(data, type, row){
                var asset=parseFloat(row.amount) * parseFloat(row.base_price)
                return formatCurrency(asset.toFixed(2));
            }},
            {"data": "amount_amortisasi", "class" : "text-center", "render": function(data, type, row){
                return row.amount_amortisasi != null ? formatCurrency(row.amount_amortisasi) : '';
            }},
            {"data": "end_date_amortisasi", "class" : "text-center"},
            {"data": "id", "render": function(data, type, row){
                return '<button onclick="doShowDetail(this);" data-toggle="modal" data-price="'+row.base_price+'" data-amount="'+row.amount+'" data-id="'+row.id+'" data-target="#modalAccDetail" class="btn waves-effect waves-light btn-xs btn-info">Input Amortisasi</button>'
            }}
        ],
    } );
});
function doShowDetail(eq){
    $('#amount').val('');
    $('#total_bulan').val('');
    var id=$(eq).data('id');
    var amount=$(eq).data('amount');
    var price=$(eq).data('price');
    var total_asset=parseFloat(amount)*parseFloat(price);
    $('#total_asset').val(total_asset.toFixed(2));
    $('#aseet_total').html('Total Asset : '+formatCurrency(total_asset.toFixed(2)));
    $('#id').val(id);
}
function countAmortisasi(total){
    var total_asset=$('#total_asset').val();
    var amortisasi_month=total_asset / parseFloat(total);
    $('#amount').val(formatCurrency(amortisasi_month));
}
</script>


@endsection