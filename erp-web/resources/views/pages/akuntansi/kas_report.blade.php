@extends('theme.default')

@section('breadcrumb')
<div class="page-breadcrumb">
    <div class="row">
        <div class="col-5 align-self-center">
            <h4 class="page-title">Laporan Buku Kas</h4>
            <div class="d-flex align-items-center">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item active" aria-current="page">Akuntansi</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
</div>
@endsection

@section('content')
@php
function formatRupiah($num){
return number_format($num, 0, '.', '.');
}
function formatDate($date){
$date=date_create($date);
return date_format($date, 'd-m-Y');
}
@endphp
<div class="container-fluid">
    <!-- basic table -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-12">
                            <h4 id="titleJurnal">Laporan Buku Kas</h4>
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <!-- column -->
                        <div class="col-lg-4">
                            <div class="form-group">
                                <select name="" id="warehouse_id" class="form-control select2" onchange="getWarehouse(this.value)" style="width:80%">
                                    <option value="">Pilih Gudang</option>
                                    @foreach($warehouse as $row)
                                    <option value="{{$row->id}}" @if($row->id == $warehouse_id) selected @endif>{{$row->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <h1 class="m-b-0 m-t-30" id="kas_total">Rp. 0</h1>
                            <h6 class="font-light text-muted">Total kas</h6>
                            <h3 class="m-t-30 m-b-0" id="kas_total_masuk">Rp. 0</h3>
                            <h6 class="font-light text-muted">Kas Masuk</h6>
                            <h3 class="m-t-30 m-b-0" id="kas_total_keluar">Rp. 0</h3>
                            <h6 class="font-light text-muted">Kas Keluar</h6>
                            <button id="btn_isi" class="hide btn btn-info m-t-20 p-15 p-l-25 p-r-25 m-b-20" type="button" data-toggle="modal" data-target="#modalFillCash"><i class="mdi mdi-login-variant"></i> Isi Kas</button>
                            <button id="btn_out" class="hide btn btn-danger m-t-20 p-15 p-l-25 p-r-25 m-b-20" type="button" data-toggle="modal" data-target="#modalOutCash"><i class="mdi mdi-logout-variant"></i> Input Pengeluaran Kas</button>
                        </div>
                        <!-- column -->
                        <div class="col-lg-8">
                            <div class="table-responsive">
                                <table class="table table-bordered" id="cash_list">
                                    <thead>
                                        <tr>
                                            <th width="200px">Tanggal</th>
                                            <th>Keterangan</th>
                                            <th>Status</th>
                                            <th>Total</th>
                                            <!-- <th></th> -->
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                        <!-- column -->
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
<div id="modalFillCash" class="modal fade" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel">Isi Kas</h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <form action="{{URL::to('akuntansi/cash_in')}}" method="post" enctype="multipart/form-data">
                <div class="modal-body">
                    @csrf
                    <div class="form-group">
                        <label>Total</label>
                        <input type="text" id="total_cash_in" onkeyup="cashIn(this.value)" name="total" class="form-control">
                    </div>
                    <input type="hidden" id="m_warehouse_id1" name="m_warehouse_id">
                    <div class="form-group">
                        <label>Tanggal Pengisian Kas</label>
                        <input type="date" id="date" name="date" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>No BBK</label>
                        <input type="" id="bbk" name="bbk" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>No BKM</label>
                        <input type="" id="bkm" name="bkm" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="">Akun Pembayaran</label>
                        <select name="account_payment" id="account_payment" class="select2 form-control" style="width:100%" required>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger waves-effect" data-dismiss="modal">Close</button>
                    <button class="btn btn-info waves-effect">Submit</button>
                </div>
            </form>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<div id="modalOutCash" class="modal fade" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel">Input Pengeluaran Kas</h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <form action="{{URL::to('akuntansi/cash_out')}}" method="post" enctype="multipart/form-data">
                <div class="modal-body">
                    @csrf
                    <input type="hidden" value="{{$cash != null ? $cash->amount : 0}}" id="total_cash">
                    <div class="form-group">
                        <label>No BKK</label>
                        <input type="" id="bkk" name="bkk" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Total</label>
                        <input type="text" id="total_cash_out" onkeyup="cashOut(this.value)" name="total" class="form-control">
                    </div>
                    <input type="hidden" id="m_warehouse_id2" name="m_warehouse_id">
                    <div class="form-group">
                        <label>Deskripsi</label>
                        <textarea name="deskripsi" class="form-control" id="deskripsi"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="">Pilih Akun</label>
                        <select name="account_adm" class="select2 form-control" style="width:100%" required>
                            <option value="">--Pilih Akun--</option>
                            @foreach($data['akun_option'] as $key => $value)
                            @if (strpos($value, '7.2') !== false || strpos($value, '7.6') !== false){
                            <option value="{{$key}}">{{$value}}</option>
                            @endif
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger waves-effect" data-dismiss="modal">Close</button>
                    <button class="btn btn-info waves-effect">Submit</button>
                </div>
            </form>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<script src="{!! asset('theme/assets/libs/jquery/dist/jquery.min.js') !!}"></script>
<script src="{!! asset('theme/assets/extra-libs/DataTables/datatables.min.js') !!}"></script>
<script type="text/javascript">
    $(document).ready(function(){
        dt = $('#cash_list').DataTable( {
            "processing": true,
            "serverSide": true,
            "ajax": "{{ URL::to('akuntansi/cash_json') }}",
            "aaSorting": [[ 0, "desc" ]],
            "columns": [
                {"data": "tanggal", "render" : function(data, type, row){
                    return formatTanggal(row.tanggal);
                }},
                {"data": "deskripsi"},
                {"data": "tipe", "render" : function(data, type, row){
                    return row.tipe == 'DEBIT' ? 'In' : (row.id_akun == 29 ? 'In' : 'Out');
                }},
                {"data": "jumlah", "render" : function(data, type, row){
                    return formatCurrency(row.jumlah);
                }, "class" : 'text-right'},
            ],
        } );
        
        $('#account_payment').empty();
        $('#account_payment').append('<option value="">-- Pilih Akun --</option>');
        $.ajax({
            // type: "post",
            url: "{{ URL::to('akuntansi/account_payment') }}",
            dataType: 'json',
            success: function(response) {
                arrData = response;
                for (i = 0; i < arrData.length; i++) {
                    if (arrData[i]['label'] != 24 && arrData[i]['label'] != 101) {
                        $('#account_payment').append('<option value="' + arrData[i]['label'] + '">' + arrData[i]['value'] + '</option>');
                    }
                }
            }
        });
        warehouse_id=$('#warehouse_id').val();
        if(warehouse_id != ''){
            console.log(warehouse_id)
            getWarehouse(warehouse_id)
        }
    });

    function cashIn(val){
        $('#total_cash_in').val(formatNumber(val));
    }
    function cashOut(val){
        var paid=(val).replace(/[^,\d]/g, '').toString();
        var total_cash=$('#total_cash').val();
        if (parseFloat(paid) > parseFloat(total_cash)) {
            $('#total_cash_out').val(0);
            alert('tidak boleh melebihi sisa kas')
        }else{
            $('#total_cash_out').val(formatNumber(val));
        }
    }
    function formatTanggal(date) {
        var temp=date.split('-');
        return temp[2] + '-' + temp[1] + '-' + temp[0];
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
    function getWarehouse(val){
        $('#m_warehouse_id1').val(val)
        $('#m_warehouse_id2').val(val)
        if(val != ''){
            $('#btn_isi').show();
            $('#btn_out').show();
            $.ajax({
            // type: "post",
            url: "{{ URL::to('akuntansi/cek_kas') }}"+'/'+val,
            dataType: 'json',
            success: function(response) {
                arrData = response['data'];
                $('#kas_total').html('Rp. '+(arrData != null ? formatCurrency(parseInt(arrData['amount'])) : 0))
                $('#kas_total_masuk').html('Rp. '+(arrData != null ? formatCurrency(parseInt(arrData['amount_in'])) : 0))
                $('#kas_total_keluar').html('Rp. '+(arrData != null ? formatCurrency(parseInt(arrData['amount_out'])) : 0))
                $('#total_cash').val((arrData != null ? parseInt(arrData['amount']) : 0))
                
            }
        });
        }else{
            $('#btn_isi').hide();
            $('#btn_out').hide();
            $('#kas_total').html('Rp. 0')
            $('#kas_total_masuk').html('Rp. 0')
            $('#kas_total_keluar').html('Rp. 0')
            $('#total_cash').val('')
        }
        
        var data = {
            warehouse_id : val,
        };
        var out = [];

        for (var key in data) {
            out.push(key + '=' + encodeURIComponent(data[key]));
        }
        url_data = out.join('&');
        dt.ajax.url('{{ URL::to('akuntansi/cash_json?') }}' + url_data).load();
    }
</script>
@endsection