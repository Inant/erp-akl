@extends('theme.default')

@section('breadcrumb')
    <div class="page-breadcrumb">
        <div class="row">
            <div class="col-5 align-self-center">
                <h4 class="page-title">Daftar Tagihan Pengadaan/Pemasangan</h4>
                <div class="d-flex align-items-center">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item active" aria-current="page">Hutang</li>
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
                <a href="{{ URL::to('payment/create_bill_vendor') }}"><button class="btn btn-success btn-sm mb-2"><i class="fas fa-plus"></i>&nbsp; Create New</button></a>
            </div>
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Daftar Tagihan Pengadaan/Pemasangan</h4>
                    
                    <div class="table-responsive">
                        <table id="bill_vendor_list" class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th class="text-center">Nomor</th>
                                    <th class="text-center">Nomor Tagihan</th>
                                    <th class="text-center">Nomor SPK</th>
                                    <th class="text-center">Supplier</th>
                                    <th class="text-center">Deskripsi</th>
                                    <th class="text-center">Total</th>
                                    <th class="text-center">Tanggal Hutang</th>
                                    <th class="text-center">Tanggal Jatuh Tempo</th>
                                    <th class="text-center">Status</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <!-- /.modal -->
    </div>                
</div>

<script src="{!! asset('theme/assets/libs/jquery/dist/jquery.min.js') !!}"></script>
<script src="{!! asset('theme/assets/extra-libs/DataTables/datatables.min.js') !!}"></script>
<script type="text/javascript">
var uri='{{URL::to('/')}}';
$(document).ready(function() {
    dt = $('#bill_vendor_list').DataTable( {
        "processing": true,
        "serverSide": true,
        "ajax": "{{ URL::to('payment/bill_vendor_json') }}",
        "aaSorting": [[ 0, "desc" ]],
        "columns": [
            {"data": "no"},
            {"data": "bill_no"},
            {"data": "spk_number"},
            {"data": "supplier_name"},
            {"data": "notes"},
            {"data": "amount", "render" : function(data, type, row){
                return formatRupiah(parseFloat(row.amount));
            }, "class" : 'text-right'},
            {"data": "create_date", "render" : function(data, type, row){
                return formatTanggal(row.create_date);
            }, 'class' : 'text-center'},
            {"data": "due_date", "render" : function(data, type, row){
                return row.due_date != null ? formatTanggal(row.due_date) : '-';
            }, 'class' : 'text-center'},
            {"data": "is_paid", "render" : function(data, type, row){
                return row.is_paid == true ? 'Dibayar' : 'Belum Dibayar';
            }, 'class' : 'text-center'},
        ],
    } );
});
function formatTanggal(date) {
  var temp=date.split('-');
  return temp[2] + '-' + temp[1] + '-' + temp[0];
}
function formatRupiah(angka, prefix)
{
    var reverse = angka.toString().split('').reverse().join(''),
    ribuan = reverse.match(/\d{1,3}/g);
    ribuan = ribuan.join('.').split('').reverse().join('');
    return ribuan;
}
function formatNumber(angka, prefix)
{
    var number_string = angka.replace(/[^,\d]/g, '').toString(),
        split = number_string.split(','),
        sisa  = split[0].length % 3,
        rupiah  = split[0].substr(0, sisa),
        ribuan  = split[0].substr(sisa).match(/\d{3}/gi);
        
    if (ribuan) {
        separator = sisa ? '.' : '';
        rupiah += separator + ribuan.join('.');
    }

    rupiah = split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
    return prefix == undefined ? rupiah : (rupiah ? 'Rp. ' + rupiah : '');
}
function getDetail(eq){
    var id=$(eq).data('id');
    var amount=parseFloat($(eq).data('amount')).toFixed(0);
    $('#debt_id').val(id);
    $('#total1').val(formatCurrency(amount));
    $('#total').val(amount);
}
function formatTotal(val){
    $('#total_paid').val(formatNumber(val));
    var total=$('#total').val();
    var subtotal=parseFloat(total).toFixed(0);
    var paid=val.replace(/[^,\d]/g, '').toString();
    var paid_more=0, paid_less=0;
    if (parseFloat(paid) > parseFloat(subtotal)) {
        paid_more=paid-subtotal;
    }else{
        paid_less=subtotal-paid;
    }
    $('#paid_more').val(paid_more)
    $('#paid_less').val(paid_less)
    
}
function cekTipe(val){
    if(val == 'bank_transfer'){
        $('#card').hide()
        $('#bank_no').show()
        $('#bank').show()
        $('#bank_an').show()
    }else{
        $('#bank_no').hide()
        $('#bank').hide()
        $('#card').hide()
        $('#bank_an').hide()
    }
}
function updateDebtList() {
    var data = {
        status : $('#status').val(),
    };
    var out = [];

    for (var key in data) {
        out.push(key + '=' + encodeURIComponent(data[key]));
    }
    url_data = out.join('&');
    dt.ajax.url('{{ URL::to('payment/list_debt?') }}' + url_data).load();
}
function exportData(){
    status = $('#status').val()

    var form = $('<form id="export_form" action="{{ URL::to('payment/export_debt') }}" method="post" target="_blank" hidden>' +
    '<input type="text" name="_token" value="{{ csrf_token() }}" />' +
    '<input type="text" name="status" value="' + status + '" />' +
    '</form>');
    $('body').append(form);
    form.submit();
    $('#export_form').remove();
}
</script>
@endsection