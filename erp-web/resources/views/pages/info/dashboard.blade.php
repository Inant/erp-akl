@extends('theme.default')

@section('breadcrumb')
    <div class="page-breadcrumb">
        <div class="row">
            <div class="col-5 align-self-center">
                <h4 class="page-title">Dashboard</h4>
                <div class="d-flex align-items-center">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="#">Home</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Dashboard</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('content')
<div class="container-fluid">
    @if(auth()->user()['role_id'] == 6)
    <div class="row">
        <div class="col-sm-12 col-lg-12">
            <h4>Welcome {{auth()->user()['name']}}</h4>
        </div>
    </div>
    @else
    <div class="row">
        <!-- column -->
        <div class="col-sm-12 col-lg-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Internal memo</h4>
                    <div class="row">
                        <div class="col-sm-12 col-lg-6">
                            <div class="card-body" style="background-color:#eef5f9">
                                <h4 class="card-title">Memo Anda</h4>
                                <div id="my_memo">
                                    <table style="width:100%" id="table-memo">
                                        <tbody>
                                            <tr>
                                                <th>Memo Anda Kosong</th>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <br>
                                <a href="#" id="btn-memo" onclick="openMemo()">Buat Memo</a>
                                <form id="form-memo" style="display:none" action="javascript:;"  accept-charset="utf-8" method="post" enctype="multipart/form-data">
                                    @csrf
                                    <div class="form-group">
                                        <input type="hidden" name="user_id" id="user_id" value="{{$user_id}}" />
                                        <input type="text" name="title" id="title" required placeholder="Judul" class="form-control">
                                    </div>
                                    <div class="form-group">
                                        <textarea name="notes" id="notes" placeholder="Catatan" required class="form-control"></textarea>
                                    </div>
                                    <div class="form-group">
                                        <label>Deadline</label>
                                        <input type="date" name="date_end" id="date_end" required class="form-control">
                                    </div>
                                    <div class="form-group">
                                        <select name="to" id="to" class="form-control select2" required style="width:100%">
                                            <option value="">Pilih Tujuan Memo</option>
                                            @foreach($user_list as $value)
                                            <option value="{{$value->id}}">{{$value->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <button type="submit" id="submit" class="btn btn-success btn-sm">Buat</button>
                                    <button type="button" onclick="closeMemo()" class="btn btn-danger btn-sm">Tutup</button>
                                </form>
                            </div>    
                        </div>
                        <div class="col-sm-12 col-lg-6">
                            <div class="card-body" style="background-color:#eef5f9">
                                <h4 class="card-title">Memo Untuk Anda</h4>
                                <div id="my_memo">
                                    <table style="width:100%" id="memo_todo">
                                        <tbody>
                                            <tr>
                                                <th>Memo Anda Kosong</th>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>    
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-12 col-lg-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Daftar Proyek Berjalan</h4>
                    <div class="table-responsive">
                        <table id="worker_list" class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th class="text-center">Nama Proyek</th>
                                    <th class="text-center">Customer</th>
                                    <th class="text-center">Tempat Produksi</th>
                                    <th class="text-center">Pengerjaan Kavling</th>
                                    <th class="text-center">Total</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-12 col-lg-6">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Daftar Hutang Supplier Jatuh Tempo</h4>
                    <div class="table-responsive">
                        <table id="purchase_list" class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <!-- <th class="text-center">No</th> -->
                                    <th class="text-center">Nama Supplier</th>
                                    <!-- <th class="text-center">Nomor PO</th> -->
                                    <!-- <th class="text-center">Nomor PO Asset</th> -->
                                    <th class="text-center" style="min-width:100px">Tanggal Jatuh Tempo</th>
                                    <th class="text-center">Jumlah Tagihan</th>
                                    <th class="text-center">Button Lunasi</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <!-- column -->
        <div class="col-sm-12 col-lg-6">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Daftar Piutang Supplier Jatuh Tempo</h4>
                    <div class="table-responsive">
                        <table id="piutang_list" class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <!-- <th class="text-center">No</th> -->
                                    <th class="text-center">Nama Supplier</th>
                                    <!-- <th class="text-center">Nomor PO</th> -->
                                    <!-- <th class="text-center">Nomor PO Asset</th> -->
                                    <th class="text-center" style="min-width:100px">Tanggal Jatuh Tempo</th>
                                    <th class="text-center">Jumlah Tagihan</th>
                                    <th class="text-center">Button Lunasi</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-12 col-lg-6">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">PO Open</h4>
                    <div class="table-responsive">
                        <table id="po_list" class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th class="text-center">Nama Supplier</th>
                                    <th class="text-center">Nomor PO</th>
                                    <th class="text-center" style="min-width:100px">Tanggal PO</th>
                                    <th class="text-center"></th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-12 col-lg-6">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">PO Asset Open</h4>
                    <div class="table-responsive">
                        <table id="po_asset_list" class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th class="text-center">Nama Supplier</th>
                                    <th class="text-center">Nomor PO</th>
                                    <th class="text-center" style="min-width:100px">Tanggal PO</th>
                                    <th class="text-center"></th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-12 col-lg-6">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Tagihan Belum Dibayar</h4>
                    <div class="table-responsive">
                        <table id="bill_list" class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th class="text-center">No</th>
                                    <th class="text-center">Nama Customer</th>
                                    <th class="text-center">Nomor Order</th>
                                    <th class="text-center">Nomor Tagihan</th>
                                    <th class="text-center">Tanggal Jatuh Tempo</th>
                                    <th class="text-center">Total Tagihan</th>
                                    <th class="text-center"></th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-12 col-lg-6">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Tagihan Instalasi Belum Dibayar</h4>
                    <div class="table-responsive">
                        <table id="bill_install_list" class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th class="text-center">No</th>
                                    <th class="text-center">Nama Customer</th>
                                    <th class="text-center">Nomor Order</th>
                                    <th class="text-center">Nomor Tagihan</th>
                                    <th class="text-center">Tanggal Jatuh Tempo</th>
                                    <th class="text-center">Total Tagihan</th>
                                    <th class="text-center"></th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div> 
    @endif    
</div>
<div class="modal fade bs-example-modal-lg" id="modalBillDetail" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <form action="{{URL::to('order/save_bill_detail')}}" method="post">
            @csrf
            <div class="modal-header">
                <h4 class="modal-title" id="title-modal">Pembayaran Tagihan </h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <div class="modal-body">
                <h4>Daftar Tagihan</h4>
                <table>
                    <tr>
                        <td>Total Tagihan</td>
                        <td id="bill_amount"></td>
                    </tr>
                    <!-- <tr>
                        <td>PPN(10%)</td>
                        <td id="bill_ppn"></td>
                    </tr> -->
                    <tr>
                        <td>Total Bayar</td>
                        <td id="bill_all"></td>
                    </tr>
                </table>
                <br>
                <div class="table-responsive">
                    <table class="table table-bordered" id="detail_payment">
                        <thead>
                            <tr>
                                <th class="text-center">Tipe Pembayaran</th>
                                <th class="text-center">Bank</th>
                                <th class="text-center">Nomor Bank</th>
                                <th class="text-center">Atas Nama</th>
                                <!-- <th class="text-center">Ref Code</th> -->
                                <th class="text-center">Total</th>
                                <th class="text-center">Pay Date</th>
                                <th></th>
                            </tr>
                        </thead>
                    </table>
                </div>
                <p id="label-detail"></p>
                <input type="hidden" name="order_id" id="order_id" value="">
                <input type="hidden" id="bill_id" name="bill_id">
                <input type="hidden" id="amount_bill" name="amount_bill">
                <input type="hidden" id="total_min" name="total_min">
                <input type="hidden" id="total_awal" name="total_awal">
                <input type="hidden" id="total_ppn" name="total_ppn">
                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label for="">Total</label>
                            <input type="" onkeyup="checkTotalBill(this)" name="total_bill" id="total_bill" class="form-control" style="100%">
                            <input type="hidden" readonly name="paid_more" id="paid_more" class="form-control">
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label>Tipe Pembayaran</label><br>
                            <select name="wop" id="wop" class="form-control select2" style="width:100%" onchange="cekTipe(this.value)" required>
                                <option value="">-- Pilih Tipe Pembayaran --</option>
                                <option value="cash">Tunai</option>
                                <option value="giro">Giro</option>
                                <option value="bank_transfer">Transfer Bank</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-sm-6" id="card" style="display:none">
                        <div class="form-group">
                            <label for="">Nomor Giro</label>
                            <input type="" name="ref_code" id="ref_code" class="form-control" style="100%">
                        </div>
                    </div>
                    <div class="col-sm-6"  id="bank" style="display:none">
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
                <button type="button" class="btn btn-danger waves-effect btn-sm text-left" data-dismiss="modal">Close</button>
                <button class="btn btn-success waves-effect btn-sm text-left" id="submit_bill" disabled>Simpan</button>
            </div>
            </form>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<div class="modal fade bs-example-modal-lg" id="modalBillDetail2" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <form action="{{URL::to('order/save_bill_install_detail')}}" method="post">
            @csrf
            <div class="modal-header">
                <h4 class="modal-title" id="title-modal">Pembayaran Tagihan </h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <div class="modal-body">
                <h4>Daftar Tagihan</h4>
                <table>
                    <tr>
                        <td>Total Tagihan</td>
                        <td id="bill_amount2"></td>
                    </tr>
                    <!-- <tr>
                        <td>PPN(10%)</td>
                        <td id="bill_ppn2"></td>
                    </tr>
                    <tr>
                        <td>PPH 22(2,5%)</td>
                        <td id="bill_pph2"></td>
                    </tr> -->
                    <tr>
                        <td>Total Bayar</td>
                        <td id="bill_all2"></td>
                    </tr>
                </table>
                <br>
                <div class="table-responsive">
                    <table class="table table-bordered" id="detail_payment2">
                        <thead>
                            <tr>
                                <th class="text-center">Tipe Pembayaran</th>
                                <th class="text-center">Bank</th>
                                <th class="text-center">Nomor Bank</th>
                                <th class="text-center">Atas Nama</th>
                                <!-- <th class="text-center">Ref Code</th> -->
                                <th class="text-center">Total</th>
                                <th class="text-center">Pay Date</th>
                                <th></th>
                            </tr>
                        </thead>
                    </table>
                </div>
                <p id="label-detail"></p>
                <input type="hidden" name="install_order_id" id="install_order_id">
                <input type="hidden" id="bill_id2" name="bill_id">
                <input type="hidden" id="amount_bill2" name="amount_bill">
                <input type="hidden" id="total_min2" name="total_min">
                <input type="hidden" id="total_awal2" name="total_awal">
                <input type="hidden" id="total_ppn2" name="total_ppn">
                <input type="hidden" id="total_pph2" name="total_pph">
                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label for="">Total</label>
                            <input type="" onkeyup="checkTotalBill2(this)" name="total_bill" id="total_bill2" class="form-control" style="100%">
                            <input type="hidden" readonly name="paid_more" id="paid_more2" class="form-control">
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label>Tipe Pembayaran</label><br>
                            <select name="wop" id="wop2" class="form-control select2" style="width:100%" onchange="cekTipe2(this.value)" required>
                                <option value="">-- Pilih Tipe Pembayaran --</option>
                                <option value="cash">Tunai</option>
                                <option value="giro">Giro</option>
                                <option value="bank_transfer">Transfer Bank</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-sm-6" id="card2" style="display:none">
                        <div class="form-group">
                            <label for="">Nomor Giro</label>
                            <input type="" name="ref_code" id="ref_code2" class="form-control" style="100%">
                        </div>
                    </div>
                    <div class="col-sm-6"  id="bank2" style="display:none">
                        <div class="form-group">
                            <label>Bank</label><br>
                            <select name="id_bank" id="id_bank2" class="form-control select2" style="width:100%">
                                <option value="">-- Pilih Bank --</option>
                                @foreach($list_bank as $value)
                                <option value="{{$value->id_bank}}">{{$value->bank_name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-sm-6" id="bank_no2" style="display:none">
                        <div class="form-group">
                            <label for="">Nomor Rekening</label>
                            <input type="" name="bank_number" id="bank_number2" class="form-control">
                        </div>
                    </div>
                    <div class="col-sm-6" id="bank_an2" style="display:none">
                        <div class="form-group">
                            <label for="">Atas Nama</label>
                            <input type="" name="atas_nama" id="atas_nama2" class="form-control">
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label for="">Akun Pembayaran</label>
                            <select name="account_payment" id="account_payment2" class="select2 form-control" style="width:100%" required>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger waves-effect btn-sm text-left" data-dismiss="modal">Close</button>
                <button class="btn btn-success waves-effect btn-sm text-left" id="submit_bill2" disabled>Simpan</button>
            </div>
            </form>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<div class="modal fade" id="modalShowDetail" role="dialog" aria-labelledby="modalShowDetailLabel1">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="detail_title"></h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <form id="form-detail" action="javascript:;"  accept-charset="utf-8" method="post" enctype="multipart/form-data">
            @csrf
            <div class="modal-body">
                <!-- <p id="from_detail"></p> -->
                <input type="hidden" id="detail_id" name="id">
                <label>Deskripsi :</label>
                <p id="detail_deskripsi"></p>
                <label>Deadline :</label>
                <p id="deadline_detail"></p>
                <label>Status :</label>
                <select name="edit_status" id="detail_status" class="form-control">
                    <option value="0">Dibuat</option>
                    <option value="1">Dikerjakan</option>
                    <option value="2">Selesai</option>
                    <option value="3">Revisi</option>
                    <option value="4">Terkonfirmasi</option>
                </select>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal">Tutup</button>
                <button class="btn btn-primary btn-sm">Save</button>
            </div>
            </form>
        </div>
    </div>
</div>
<div class="modal fade" id="modalEditDetail" role="dialog" aria-labelledby="modalShowDetailLabel1">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="edit_title"></h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <form id="form-edit" action="javascript:;"  accept-charset="utf-8" method="post" enctype="multipart/form-data">
            @csrf
            <div class="modal-body">
                <input type="hidden" id="edit_id" name="id">
                <p id="from_edit"></p>
                <label>Deskripsi :</label>
                <p id="deskripsi_edit"></p>
                <label>Deadline :</label>
                <p id="deadline_edit"></p>
                <label>Status :</label>
                <select name="edit_status" id="edit_status" class="form-control">
                    <option value="0">Dibuat</option>
                    <option value="1">Dikerjakan</option>
                    <option value="2">Selesai</option>
                    <option value="3">Revisi</option>
                    <option value="4">Terkonfirmasi</option>
                </select>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger btn-sm" data-dismiss="modal">Tutup</button>
                <button class="btn btn-primary btn-sm">Save</button>
            </div>
            </form>
        </div>
    </div>
</div>
<script src="{!! asset('theme/assets/libs/jquery/dist/jquery.min.js') !!}"></script>
<script src="{!! asset('theme/assets/extra-libs/DataTables/datatables.min.js') !!}"></script>
<script>
$(document).ready(function(){
    $('#account_payment').empty();
    $('#account_payment2').empty();
    $('#account_payment').append('<option value="">-- Pilih Akun --</option>');
    $('#account_payment2').append('<option value="">-- Pilih Akun --</option>');
    $.ajax({
        // type: "post",
        url: "{{ URL::to('akuntansi/account_payment') }}",
        dataType : 'json',
        success: function(response){
            arrData = response;
            for(i = 0; i < arrData.length; i++){
                $('#account_payment').append('<option value="'+arrData[i]['label']+'">'+arrData[i]['value']+'</option>');
                $('#account_payment2').append('<option value="'+arrData[i]['label']+'">'+arrData[i]['value']+'</option>');
            }
        }
    });
    $.ajax({
        type: "GET",
        url: "{{ URL::to('home/get-my-memo-to') }}", //json get site
        dataType : 'json',
        async: false,
        success: function(response){
            if (response['data'].length != 0) {
                $('#table-memo > tbody').empty();
                $.each(response['data'], function(i, item){
                    $('#table-memo tbody').append('<tr>'+
                                '<td><div class="card-body" style="background-color:white">'+item.title+
                                '<button class="btn btn-danger btn-xs float-right" onclick="deleteMemo('+item.id+');"><i class="mdi mdi-delete"></i></button>'+'<button class="btn btn-info btn-xs float-right" style="margin-right:5px" data-toggle="modal" data-target="#modalShowDetail" onclick="lihatMemo('+item.id+');"><i class="mdi mdi-eye"></i></button>'+'<br>untuk : '+item.name+
                                '</div><br></td></tr>');
                })
            }
        }
    });
    $.ajax({
        type: "GET",
        url: "{{ URL::to('home/get-my-memo') }}", //json get site
        dataType : 'json',
        async: false,
        success: function(response){
            if (response['data'].length != 0) {
                $('#memo_todo > tbody').empty();
                $.each(response['data'], function(i, item){
                    $('#memo_todo tbody').append('<tr>'+
                                '<td><div class="card-body" style="background-color:white">'+item.title+
                                '<button onclick="editMemo('+item.id+');" class="btn btn-info btn-xs float-right" style="margin-right:5px" data-toggle="modal" data-target="#modalEditDetail"><i class="mdi mdi-eye"></i></button>'+'<br>dari : '+item.name+
                                '</div><br></td></tr>');
                })
            }
        }
    });
    $('#worker_list').DataTable( {
        "processing": true,
        "serverSide": true,
        "ajax": "{{ URL::to('home/get_project_walk') }}",
        aaSorting: [[3, 0, 'desc']],
        "columns": [
            {"data": "project_name", "class" : "text-center"},
            {"data": "customer_name", "class" : "text-center"},
            {"data": "warehouse_name", "class" : "text-center"},
            {"data": "nama_kavling", "class" : "text-center"},
            {"data": "total_kavling", "class" : "text-center"},
        ]
    } );
    $('#bill_list').DataTable( {
        "processing": true,
        "serverSide": true,
        "ajax": "{{ URL::to('home/get_bill_open') }}",
        // aaSorting: [[3, 0, 'desc']],
        "columns": [
            {"data": "no", "class" : "text-center"},
            {"data": "coorporate_name", "class" : "text-center"},
            {"data": "order_no", "class" : "text-center"},
            {"data": "bill_no", "class" : "text-center"},
            {"data": "due_date",
            "render": function(data, type, row){return row.due_date != null ? formatDateID(new Date((row.due_date).substring(0,10))) : '-'}},
            {"data": "amount", "class" : "text-right",
            "render": function(data, type, row){return formatCurrency(row.amount)}},
            {"data": "action", "class" : "text-center"},
        ]
    } );
    $('#bill_install_list').DataTable( {
        "processing": true,
        "serverSide": true,
        "ajax": "{{ URL::to('home/get_bill_install_open') }}",
        // aaSorting: [[3, 0, 'desc']],
        "columns": [
            {"data": "no", "class" : "text-center"},
            {"data": "coorporate_name", "class" : "text-center"},
            {"data": "order_no", "class" : "text-center"},
            {"data": "bill_no", "class" : "text-center"},
            {"data": "due_date",
            "render": function(data, type, row){return row.due_date != null ? formatDateID(new Date((row.due_date).substring(0,10))) : '-'}},
            {"data": "amount", "class" : "text-right",
            "render": function(data, type, row){return formatCurrency(row.amount)}},
            {"data": "action", "class" : "text-center"},
        ]
    } );
    $('#purchase_list').DataTable( {
        "processing": true,
        "serverSide": true,
        "ajax": {
            url : "{{ URL::to('home/hutang_due_date') }}",
            data : {type : 'credit'}
        },
        aaSorting: [[3, 0, 'desc']],
        "columns": [
            {"data": "id",
            "render": function(data, type, row){return row.supplier}},
            {"data": "paid_for_week",
            "render": function(data, type, row){return row.due_date != null ? formatDateID(new Date((row.due_date).substring(0,10))) : '-'}},
            {"data": "amount", "class" : "text-right",
            "render": function(data, type, row){return formatCurrency(row.amount)}},
            {"data": "is_paid",
            "render": function(data, type, row){return row.is_paid == true ? 'Lunas' : "<a href='{{ URL::to('inventory/paid_credit') }}/"+row.id+"' class='btn btn-info btn-sm'><span class='mdi mdi-check'></span> Lunasi</a>"}},
        ]
    } );
    $('#piutang_list').DataTable( {
        "processing": true,
        "serverSide": true,
        "ajax": {
            url : "{{ URL::to('home/hutang_due_date') }}",
            data : {type : 'cash'}
        },
        aaSorting: [[3, 0, 'desc']],
        "columns": [
            {"data": "id",
            "render": function(data, type, row){return row.supplier}},
            {"data": "paid_for_week",
            "render": function(data, type, row){return row.due_date != null ? formatDateID(new Date((row.due_date).substring(0,10))) : '-'}},
            {"data": "amount", "class" : "text-right",
            "render": function(data, type, row){return formatCurrency(row.amount)}},
            {"data": "is_paid",
            "render": function(data, type, row){return row.is_paid == true ? 'Lunas' : "<a href='{{ URL::to('inventory/paid_credit') }}/"+row.id+"' class='btn btn-info btn-sm'><span class='mdi mdi-check'></span> Lunasi</a>"}},
        ]
    } );

    $('#po_list').DataTable( {
        "processing": true,
        "serverSide": true,
        "ajax": {
            url : "{{ URL::to('home/po_open') }}",
            data : {type : 'cash'}
        },
        aaSorting: [[3, 0, 'desc']],
        "columns": [
            {"data": "id",
            "render": function(data, type, row){return row.supplier}},
            {"data": "no"},
            {"data": "purchase_date",
            "render": function(data, type, row){return row.purchase_date != null ? formatDateID(new Date((row.purchase_date).substring(0,10))) : '-'}},
            {"data": "id",
                "render": function(data, type, row){return '<div class="text-center">'+(row.is_closed == false ? '<a href="{{ URL::to('penerimaan_barang/decline') }}'+'/'+row.id+'" onclick="return confirm_click();"><button type="button" class="btn btn-danger waves-effect waves-light btn-sm"><i class="mdi mdi-close"></i></button></a> <a href="{{ URL::to('penerimaan_barang/receive') }}'+'/'+row.id+'"><button type="button" class="btn btn-warning waves-effect waves-light btn-sm"><i class="fas fa-pencil-alt"></i></button></a>' : ' <button type="button" onclick="clickPrint('+row.id+');" class="btn btn-info waves-effect waves-light btn-sm" title="print"><i class="fa fa-print"></></button>')+'</div>'}},
        ]
    } );
    $('#po_asset_list').DataTable( {
        "processing": true,
        "serverSide": true,
        "ajax": {
            url : "{{ URL::to('home/po_asset_open') }}",
            data : {type : 'cash'}
        },
        aaSorting: [[3, 0, 'desc']],
        "columns": [
            {"data": "id",
            "render": function(data, type, row){return row.supplier}},
            {"data": "no"},
            {"data": "purchase_date",
            "render": function(data, type, row){return row.purchase_date != null ? formatDateID(new Date((row.purchase_date).substring(0,10))) : '-'}},
            {"data": "id",
                "render": function(data, type, row){return '<div class="text-center">'+(row.is_closed == false ? '<a href="{{ URL::to('penerimaan_barang/decline_atk') }}'+'/'+row.id+'" onclick="return confirm_click();"><button type="button" class="btn btn-danger waves-effect waves-light btn-sm"><i class="mdi mdi-close"></i></button></a> <a href="{{ URL::to('penerimaan_barang/receive_atk') }}'+'/'+row.id+'"><button type="button" class="btn btn-warning waves-effect waves-light btn-sm"><i class="fas fa-pencil-alt"></i></button></a>' : ' <button type="button" onclick="clickPrint('+row.id+');" class="btn btn-info waves-effect waves-light btn-sm" title="print"><i class="fa fa-print"></></button>')+'</div>'}},
        ]
    } );
    $("form#form-memo").on("submit", function( event ) {
        // var form = $('#form-memo')[0];
        // var data = new FormData(form);
        event.preventDefault();
        $.ajax({
            type: "POST",
            url: "{{ URL::to('home/create-memo') }}", //json get site
            dataType : 'json',
            data: $('#form-memo').serialize(),
            async: false,
            success: function(response){
                if (response['responseMessage'] == 'success') {
                    closeMemo();
                    loadMemo();
                }
            }
        });
    });
    $("form#form-detail").on("submit", function( event ) {
        event.preventDefault();
        $.ajax({
            type: "POST",
            url: "{{ URL::to('home/edit-memo') }}", //json get site
            dataType : 'json',
            data: $('#form-detail').serialize(),
            async: false,
            success: function(response){
                $('#modalShowDetail').modal('toggle');
                if (response['responseMessage'] == 'success') {

                }
            }
        });
    });
    $("form#form-edit").on("submit", function( event ) {
        // var form = $('#form-memo')[0];
        // var data = new FormData(form);
        event.preventDefault();
        $.ajax({
            type: "POST",
            url: "{{ URL::to('home/edit-memo') }}", //json get site
            dataType : 'json',
            data: $('#form-edit').serialize(),
            async: false,
            success: function(response){
                $('#modalEditDetail').modal('toggle');
                if (response['responseMessage'] == 'success') {
                }
            }
        });
    });
})    
function loadMemo(){
    $.ajax({
        type: "GET",
        url: "{{ URL::to('home/get-my-memo-to') }}", //json get site
        dataType : 'json',
        async: true,
        success: function(response){
            $('#table-memo > tbody').empty();
            if (response['data'].length != 0) {
                $.each(response['data'], function(i, item){
                    $('#table-memo tbody').append('<tr>'+
                                '<td><div class="card-body" style="background-color:white">'+item.title+
                                '<button class="btn btn-danger btn-xs float-right" onclick="deleteMemo('+item.id+');"><i class="mdi mdi-delete"></i></button>'+'<button class="btn btn-info btn-xs float-right" style="margin-right:5px" data-toggle="modal" data-target="#modalShowDetail" onclick="lihatMemo('+item.id+');"><i class="mdi mdi-eye"></i></button>'+'<br>untuk : '+item.name+
                                '</div><br></td></tr>');
                })
            }else{
                $('#table-memo tbody').append('<tr>'+
                                        '<th>Memo Anda Kosong</th>'+
                                    '</tr>');
            }
        }
    });
}
function openMemo(){
    $('#btn-memo').hide();
    $('#form-memo').show();
}
function closeMemo(){
    $('#btn-memo').show();
    $('#form-memo').hide();
}
function deleteMemo(id){
    var a=confirm_click();
    if (a) {
        $.ajax({
            type: "GET",
            url: "{{ URL::to('home/delete_memo') }}"+'/'+id, //json get site
            dataType : 'json',
            async: false,
            success: function(response){
                
            }
        });
        loadMemo();   
    }
}
function lihatMemo(id){
    $.ajax({
        type: "GET",
        url: "{{ URL::to('home/detail_memo') }}"+'/'+id, //json get site
        dataType : 'json',
        async: false,
        success: function(response){
            arrData=response['data'];
            $('#detail_title').html(arrData['title']);
            // $('#from_detail').html('dari : '+arrData['name']);
            $('#detail_deskripsi').html(arrData['notes']);
            $('#deadline_detail').html(formatDateID(new Date((arrData['date_end']).substring(0,10))));
            $('#detail_status').val(arrData['status']);
            $('#detail_id').val(arrData['id']);
        }
    });
}
function editMemo(id){
    $.ajax({
        type: "GET",
        url: "{{ URL::to('home/detail_memo') }}"+'/'+id, //json get site
        dataType : 'json',
        async: false,
        success: function(response){
            arrData=response['data'];
            $('#edit_title').html(arrData['title']);
            $('#from_edit').html('dari : '+arrData['name']);
            $('#deskripsi_edit').html(arrData['notes']);
            $('#deadline_edit').html(formatDateID(new Date((arrData['date_end']).substring(0,10))));
            $('#detail_edit').val(arrData['status']);
            $('#edit_id').val(arrData['id']);
        }
    });
}
function confirm_click(){
    return !confirm("Memo yakin dihapus ?") ? false : true;
}
function doShowDetail(eq){
    console.log($(eq).data('order_id'));
    var order_id=$(eq).data('order_id');
    var total_addendum=$(eq).data('total_adendum');
    var no=$(eq).data('no');
    var id=$(eq).data('id');
    var amount=$(eq).data('amount');
    var end_payment=$(eq).data('end_payment');
    // var total_addendum=$('#total_addendum').val();
    t = $('#detail_payment').DataTable();
    t.clear().draw(false);
    var total_min=0;
    $.ajax({
            type: "GET",
            url: "{{ URL::to('order/detail_customer_bill') }}"+'/'+id, //json get site
            dataType : 'json',
            async : false,
            success: function(response){
                arrData = response['data'];
                for(i = 0; i < arrData.length; i++){
                    total_min+=parseFloat(arrData[i]['amount']);
                    t.row.add([
                        '<div class="text-left">'+arrData[i]['wop']+'</div>',
                        '<div class="text-left">'+arrData[i]['bank_name'] != null ? arrData[i]['bank_name'] : '-'+'</div>',
                        '<div class="text-center">'+arrData[i]['bank_number'] != null ? arrData[i]['bank_number'] : '-'+'</div>',
                        '<div class="text-center">'+arrData[i]['atas_nama'] != null ? arrData[i]['atas_nama'] : '-'+'</div>',
                        '<div class="text-center">'+formatCurrency(arrData[i]['amount'])+'</div>',
                        '<div class="text-center">'+formatDateID(new Date((arrData[i]['pay_date']).substring(0,10)))+'</div>',
                        '<div class="text-center"><a href="{{URL::to('order/print_kwitansi/')}}/'+arrData[i]['id']+'" class="btn btn-success btn-sm" target="_blank"><i class="mdi mdi-printer"></i></a></div>'
                    ]).draw(false);
                }
            }
    });
    amount=(end_payment == 1 ? parseFloat(amount) + parseFloat(total_addendum) : parseFloat(amount)).toFixed(0);
    // var ppn=(parseFloat(amount) * (1/10)).toFixed(0);
    var ppn=0;
    $('#title-modal').html('Pembayaran Tagihan '+no);
    $('#bill_id').val(id);
    $('#order_id').val(order_id);
    $('#total_ppn').val(ppn);
    $('#total_awal').val(amount);
    $('#amount_bill').val(parseFloat(amount) + parseFloat(ppn));
    $('#bill_amount').html(': '+formatCurrency(parseFloat(amount)));
    $('#bill_ppn').html(': '+formatCurrency(parseFloat(ppn)));
    $('#bill_all').html(': '+formatCurrency(parseFloat(amount) + parseFloat(ppn)));
    $('#total_min').val((parseFloat(amount) + parseFloat(ppn)) - parseFloat(total_min))
}
function checkTotalBill(eq){
    var paid=(eq.value).replace(/[^,\d]/g, '').toString();
    var total_paid=paid != '' ? parseFloat(paid) : 0;
    var sub_total=$('#total_min').val();
    $('#total_bill').val(formatCurrency(paid));
    var paid_more=0;
    if (total_paid >= parseFloat(sub_total)) {
        paid_more=parseFloat(total_paid) - parseFloat(sub_total);
    }
    $('#paid_more').val(paid_more);
}
function cekTipe(val){
    if (val == 'giro') {
        $('#bank').show()
        $('#bank_no').hide()
        $('#card').show()
        $('#bank_an').hide()
    }else if(val == 'bank_transfer'){
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
function doShowDetail2(eq){
    var install_order_id=$(eq).data('install_order_id');
    console.log(install_order_id)
    var total_addendum=0;
    var no=$(eq).data('no');
    var id=$(eq).data('id');
    var amount=$(eq).data('amount');
    var end_payment=$(eq).data('end_payment');
    t = $('#detail_payment2').DataTable();
    t.clear().draw(false);
    var total_min=0;
    $.ajax({
            type: "GET",
            url: "{{ URL::to('order/detail_customer_bill') }}"+'/'+id, //json get site
            dataType : 'json',
            async : false,
            success: function(response){
                arrData = response['data'];
                for(i = 0; i < arrData.length; i++){
                    total_min+=parseFloat(arrData[i]['amount']);
                    t.row.add([
                        '<div class="text-left">'+arrData[i]['wop']+'</div>',
                        '<div class="text-left">'+arrData[i]['bank_name'] != null ? arrData[i]['bank_name'] : '-'+'</div>',
                        '<div class="text-center">'+arrData[i]['bank_number'] != null ? arrData[i]['bank_number'] : '-'+'</div>',
                        '<div class="text-center">'+arrData[i]['atas_nama'] != null ? arrData[i]['atas_nama'] : '-'+'</div>',
                        '<div class="text-center">'+formatCurrency(arrData[i]['amount'])+'</div>',
                        '<div class="text-center">'+formatDateID(new Date((arrData[i]['pay_date']).substring(0,10)))+'</div>',
                        '<div class="text-center"><a href="{{URL::to('order/print_kwitansi_install/')}}/'+arrData[i]['id']+'" class="btn btn-success btn-sm" target="_blank"><i class="mdi mdi-printer"></i></a></div>'
                    ]).draw(false);
                }
            }
    });
    amount=(end_payment == 1 ? parseFloat(amount) + parseFloat(total_addendum) : parseFloat(amount)).toFixed(0);
    // var ppn=(parseFloat(amount) * (1/10)).toFixed(0);
    // var pph=(parseFloat(amount) * (2.5/100)).toFixed(0);
    var ppn=0;
    var pph=0;
    $('#title-modal2').html('Pembayaran Tagihan '+no);
    $('#bill_id2').val(id);
    $('#total_ppn2').val(ppn);
    $('#total_pph2').val(pph);
    $('#total_awal2').val(amount);
    $('#install_order_id').val(install_order_id);
    $('#amount_bill2').val(parseFloat(amount) + parseFloat(ppn) + parseFloat(pph));
    $('#bill_amount2').html(': '+formatCurrency(parseFloat(amount)));
    $('#bill_ppn2').html(': '+formatCurrency(parseFloat(ppn)));
    $('#bill_pph2').html(': '+formatCurrency(parseFloat(pph)));
    $('#bill_all2').html(': '+formatCurrency(parseFloat(amount) + parseFloat(ppn) + parseFloat(pph)));
    $('#total_min2').val((parseFloat(amount) + parseFloat(ppn) + parseFloat(pph)) - parseFloat(total_min))
}
function checkTotalBill2(eq){
    var paid=(eq.value).replace(/[^,\d]/g, '').toString();
    var total_paid=paid != '' ? parseFloat(paid) : 0;
    var sub_total=$('#total_min2').val();
    $('#total_bill2').val(formatCurrency(paid));
    var paid_more=0;
    if (total_paid >= parseFloat(sub_total)) {
        paid_more=parseFloat(total_paid) - parseFloat(sub_total);
    }
    $('#paid_more2').val(paid_more);
}
function cekTipe2(val){
    if (val == 'giro') {
        $('#bank2').show()
        $('#bank_no2').hide()
        $('#card2').show()
        $('#bank_an2').hide()
    }else if(val == 'bank_transfer'){
        $('#card2').hide()
        $('#bank_no2').show()
        $('#bank2').show()
        $('#bank_an2').show()
    }else{
        $('#bank_no2').hide()
        $('#bank2').hide()
        $('#card2').hide()
        $('#bank_an2').hide()
    }
}
</script>

@endsection