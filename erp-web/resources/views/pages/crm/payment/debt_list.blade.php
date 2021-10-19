@extends('theme.default')

@section('breadcrumb')
    <div class="page-breadcrumb">
        <div class="row">
            <div class="col-5 align-self-center">
                <h4 class="page-title">Daftar Hutang Usaha</h4>
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
                <a href="{{ URL::to('payment/create_debt') }}"><button class="btn btn-success btn-sm mb-2"><i class="fas fa-plus"></i>&nbsp; Create New</button></a>
            </div>
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Daftar Hutang Usaha</h4>
                    <form method="POST" action="" class="form-inline float-right" style="padding-bottom:10px">
                        @csrf
                        <div class="form-inline">
                            <label>Pilih Status : </label>&nbsp;
                            <select name="status" id="status" class="form-control select2" style="width:120px">
                            <option value="all">All</option>
                            <option value="1">Sudah Dibayar</option>
                            <option value="0">Belum Dibayar</option>
                            </select>
                            &nbsp;
                            <button class="btn btn-primary" type="button" onclick="updateDebtList()"><i class="fa fa-search"></i></button>
                            &nbsp;
                            <button class="btn btn-success" type="button" onclick="exportData()"><i class="mdi mdi-file-excel"></i> Export</button>
                        </div>
                    </form>
                    <div class="table-responsive">
                        <table id="order_list" class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th class="text-center">Nomor</th>
                                    <th class="text-center">Supplier</th>
                                    <th class="text-center">Deskripsi</th>
                                    <th class="text-center">Total</th>
                                    <th class="text-center">Tanggal Hutang</th>
                                    <th class="text-center">Tanggal Jatuh Tempo</th>
                                    <th class="text-center">Status</th>
                                    <th class="text-center">Action</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" style="display: none;">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="title_detail">Pembayaran Hutang</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    </div>
                    <form action="{{URL::to('payment/save_paid_debt')}}" method="post">
                    @csrf
                    <div class="modal-body">
                        <input type="hidden" name="debt_id" id="debt_id">
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group" @if(auth()->user()->role_id != 1) hidden @endif>
                                    <label for="">No BBK : </label>
                                    <input type="" class="form-control" name="bbk" id="bbk">
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="">Total Hutang :</label>
                                    <input type="" readonly class="form-control" id="total1">
                                    <input type="hidden" class="form-control" name="total" id="total">
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="">Total Yang Dibayar :</label>
                                    <input type="" class="form-control" id="total_paid" name="total_paid" onkeyup="formatTotal(this.value)">
                                    <input type="hidden" id="paid_more" name="paid_more">
                                    <input type="hidden" id="paid_less" name="paid_less">
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>Tipe Pembayaran</label><br>
                                    <select name="wop" id="wop" class="form-control select2" style="width:100%" onchange="cekTipe(this.value)" required>
                                        <option value="">-- Pilih Tipe Pembayaran --</option>
                                        <option value="cash">Tunai</option>
                                        <option value="bank_transfer">Transfer Bank</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="">Tanggal Transaksi</label>
                                    <input type="date" name="pay_date" id="pay_date" required class="form-control" value="{{date('Y-m-d')}}">
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="">Catatan</label>
                                    <textarea name="notes" id="notes" class="form-control"></textarea>
                                </div>
                            </div>
                            <div class="col-sm-6" id="bank" style="display:none">
                                <div class="form-group">
                                    <label>Bank</label><br>
                                    <select name="id_bank" id="id_bank" class="form-control select2" style="width:100%">
                                        <option value="">-- Pilih Bank --</option>
                                        @foreach($list_bank as $value)
                                        <option value="{{$value->id_bank}}">{{$value->bank_name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-6" id="bank_no" style="display:none">
                                <div class="form-group">
                                    <label for="">Nomor Rekening</label>
                                    <input type="" name="bank_number" id="bank_number" class="form-control">
                                </div>
                            </div>
                            <div class="col-sm-6" id="bank_an" style="display:none">
                                <div class="form-group">
                                    <label for="">Atas Nama</label>
                                    <input type="" name="atas_nama" id="atas_nama" class="form-control">
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="">Akun Pembayaran</label>
                                    <select name="account_payment" id="account_payment" class="select2 form-control" style="width:100%" required>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger waves-effect text-left" data-dismiss="modal">Close</button>
                        <button class="btn btn-success waves-effect text-left">Simpan</button>
                    </div>
                    </form>
                </div>
                <!-- /.modal-content -->
            </div>
            <!-- /.modal-dialog -->
        </div>
        <!-- /.modal -->
    </div>                
</div>

<script src="{!! asset('theme/assets/libs/jquery/dist/jquery.min.js') !!}"></script>
<script src="{!! asset('theme/assets/extra-libs/DataTables/datatables.min.js') !!}"></script>
<script type="text/javascript">
var uri='{{URL::to('/')}}';
$(document).ready(function() {
    $('#account_payment').empty();
    $('#account_payment').append('<option value="">-- Pilih Akun --</option>');
    $.ajax({
        // type: "post",
        url: "{{ URL::to('akuntansi/account_payment') }}",
        dataType : 'json',
        success: function(response){
            arrData = response;
            for(i = 0; i < arrData.length; i++){
                $('#account_payment').append('<option value="'+arrData[i]['label']+'">'+arrData[i]['value']+'</option>');
            }
        }
    });
    dt = $('#order_list').DataTable( {
        "processing": true,
        "serverSide": true,
        "ajax": "{{ URL::to('payment/list_debt') }}",
        "aaSorting": [[ 0, "desc" ]],
        "columns": [
            {"data": "no"},
            {"data": "name"},
            {"data": "notes"},
            {"data": "amount", "render" : function(data, type, row){
                return formatRupiah(parseFloat(row.amount));
            }, "class" : 'text-right'},
            {"data": "debt_date", "render" : function(data, type, row){
                return formatTanggal(row.debt_date);
            }, 'class' : 'text-center'},
            {"data": "due_date", "render" : function(data, type, row){
                return row.due_date != null ? formatTanggal(row.due_date) : '-';
            }, 'class' : 'text-center'},
            {"data": "action"},
            {"data": "total_paid", "render" : function(data, type, row){
                return (row.total_paid == 0 ? '<a href="{{ URL::to('payment/delete_debt') }}/'+row.id+'" class="btn btn-danger btn-sm">hapus</a>' : '');
            }, 'class' : 'text-center'}
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