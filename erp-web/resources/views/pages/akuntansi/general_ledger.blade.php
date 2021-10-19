@extends('theme.default')

@section('breadcrumb')
            <div class="page-breadcrumb">
                <div class="row">
                    <div class="col-5 align-self-center">
                        <h4 class="page-title">General Ledger</h4>
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
<?php
function formatDate($date){
    $tgl=date('d-m-Y', strtotime($date));
    return $tgl;
}
function formatRupiah($val){
    $val=round($val, 0);
    $a=number_format($val, 0, '.', '.');
    return $a;
}
?>
<style>
  .floatLeft { width: 100%;}
  .floatRight {width: 100%;}
  /* .floatLeft { width: 100%; float: left; }
  .floatRight {width: 100%; float: right; } */
    #table th, #table td{
        border:1px solid #7c8186;
        padding : 5px
    }
    .no-border{
        border:1px solid white !important;
        /* padding : 5px */
    }
  </style>
<div class="container-fluid">
    <!-- basic table -->
    <div class="row">
        <div class="col-12">
            <!-- <div class="text-right">
                <a href="{{ URL::to('akuntansi/gl_all') }}"><button class="btn btn-success mb-2"><i class="fas fa-eye"></i>&nbsp; Detail All</button></a>
            </div> -->
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">General Ledger</h4>
                    <form method="POST" action="{{ URL::to('akuntansi/gl') }}">
                    @csrf
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <select class="select2 form-control" multiple="multiple" style="height: 36px; width: 100%;" id="account" name="account[]">
                                        @foreach($account as $value)
                                        <?php $same=false; ?>
                                        @foreach($account_selected as $v)
                                            @if($v == $value->id_akun)
                                                @php $same=true @endphp
                                            @endif
                                        @endforeach
                                        <option value="{{$value->id_akun}}" {{$same == true ? 'selected' : ''}}>{{$value->no_akun.' | '.$value->nama_akun}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <input type="date" name="date" class="form-control" required value="{{$date1}}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <input type="date" name="date2" class="form-control" required  value="{{$date2}}">
                            </div>
                            <div class="col-md-12">
                                <button class="btn btn-success" name="submit" value="1">Submit</button>
                            </div>
                        </div>
                    </form>
                    <br><br>
                    <!-- <div class="table-responsive">
                        <table class="table table-bordered table-striped" id="list_akun">
                            <thead>
                                <tr>
                                    <th>No Akun</th>
                                    <th>Nama Akun</th>
                                    <th>Sifat Debit</th>
                                    <th>Sifat Kredit</th>
                                    <th>Action</th> 
                                </tr>
                            </thead>
                        </table>
                    </div> -->
                    @if($data != null)
                    <form method="POST" action="{{ URL::to('akuntansi/export_gl') }}" class="float-right" target="_blank">
                        @csrf
                        <div hidden>
                            <select class="select2 form-control" multiple="multiple" style="height: 36px; width: 100%;" id="account1" name="account[]">
                                @foreach($account as $value)
                                <?php $same=false; ?>
                                @foreach($account_selected as $v)
                                    @if($v == $value->id_akun)
                                        @php $same=true @endphp
                                    @endif
                                @endforeach
                                <option value="{{$value->id_akun}}" {{$same == true ? 'selected' : ''}}>{{$value->no_akun.' | '.$value->nama_akun}}</option>
                                @endforeach
                            </select>
                            <input type="date" name="date" class="form-control" required value="{{$date1}}">
                            <input type="date" name="date2" class="form-control" required  value="{{$date2}}">
                        </div>
                        <div class="com-md-12">
                            <div class="form-group">
                                <button class="btn btn-success"><i class="fa fa-file-excel"></i> Export</button>
                            </div>
                        </div>
                    </form>
                    <div class="table-responsive">
                            <table style="border-collapse:collapse; width:99%">
                    @foreach($data as $data)
                    @php $total_debit=$total_kredit=0; @endphp
                                <thead>
                                    <tr>
                                        <th height="20px"></th>
                                    </tr>
                                    <tr>
                                        <th colspan="10"><h5 class="panel-title">Detail General Ledger Akun {{$data['akun']->nama_akun}}</h5></th>
                                    </tr>
                                    <tr id="table" class="text-primary">
                                        <th class="text-center" width="100px">Tanggal</th>
                                        <th class="text-center">No Akun</th>
                                        <th class="text-center" width="150px">Nama Akun</th>
                                        <th class="text-center" width="100px">No Sumber</th>
                                        <th class="text-center" width="200px">Deskripsi</th>
                                        <th class="text-center">Debit</th>
                                        <th class="text-center">Kredit</th>
                                        <th class="text-center" width="100px">Customer</th>
                                        <th class="text-center" width="100px">Supplier</th>
                                        <th class="text-center">Total Saldo</th> 
                                    </tr>
                                </thead>
                                <?php $sub_total=($data['saldo_awal']->jumlah_saldo != null ? $data['saldo_awal']->jumlah_saldo : 0) + $data['saldo_before_start_date']?>
                                <tbody>
                                    <tr id="table">
                                        <td class="text-center"></td>
                                        <td class="text-center"></td>
                                        <td class="text-center"></td>
                                        <td class="text-center"></td>
                                        <td class="text-center"></td>
                                        <td class="text-right"></td>
                                        <td class="text-right"></td>
                                        <td class="text-right"></td>
                                        <td class="text-right"></td>
                                        <td class="text-right">{{formatRupiah($sub_total)}}</td>
                                    </tr>
                                </tbody>
                                @foreach($data['data'] as $value)
                                <tbody>
                                    @if(count($value['dt']) == 0)
                                    <tr id="table">
                                        <td class="text-center">{{formatDate($value['date'])}}</td>
                                        <td class="text-center"></td>
                                        <td class="text-center"></td>
                                        <td class="text-center"></td>
                                        <td class="text-center"></td>
                                        <td class="text-right">0</td>
                                        <td class="text-right">0</td>
                                        <td class="text-right"></td>
                                        <td class="text-right"></td>
                                        <td class="text-right">{{formatRupiah($sub_total)}}</td>
                                    </tr>
                                    @else
                                        @foreach($value['dt'] as $k => $v)
                                        <?php
                                        $total=$v->jumlah;
                                        if ($v->tipe == 'DEBIT') {
                                            if ($data['akun']->sifat_debit == 1) {
                                                $sub_total+=$total;
                                            }else{
                                                $sub_total-=$total;
                                            }
                                            $total_debit+=$total;
                                        }else{
                                            if ($data['akun']->sifat_kredit == 1) {
                                                $sub_total+=$total;
                                            }else{
                                                $sub_total-=$total;
                                            }
                                            $total_kredit+=$total;
                                        }
                                        ?>
                                        <tr id="table">
                                            <td class="text-center">{{ $k == 0 ? formatDate($value['date']) : ''}}</td>
                                            <td class="text-center">{{$v->no_akun}}</td>
                                            <td class="text-center">{{$v->nama_akun}}</td>
                                            <td class="text-center">
                                            @if($v->note_no != null)
                                                @if($v->tipe == 'DEBIT')
                                                <a href="{{URL::to('akuntansi/cetak_bukti_kas_masuk').'/'.$v->id_trx_akun_detail}}" target="_blank">{{$v->note_no}}</a>
                                                @else
                                                <a href="{{URL::to('akuntansi/cetak_bukti_kas_keluar').'/'.$v->id_trx_akun_detail}}" target="_blank">{{$v->note_no}}</a>
                                                @endif
                                            @else
                                            {{$v->purchases != null ? $v->purchases->no : ($v->purchase_assets != null ? $v->purchase_assets->no : ($v->orders != null ? $v->orders->order_no : ($v->ts_warehouses != null ? $v->ts_warehouses->no : ($v->debts != null ? $v->debts->no : ($v->install_orders != null ? $v->install_orders->no : ($v->giros != null ? $v->giros->no : ($v->paid_customers != null ? $v->paid_customers->no : ($v->paid_suppliers != null ? $v->paid_suppliers->no : '-'))))))))}}
                                            
                                            @endif
                                            </td>
                                            <td class="text-left">{{$v->deskripsi.' '.$v->code_item}}</td>
                                            <td class="text-right">@if($v->tipe == 'DEBIT') <a href="" onclick="doShowDetail('{{$v->id_trx_akun}}');" data-toggle="modal" data-target="#modalShowDetail">{{formatRupiah($v->jumlah)}}</a> @else 0 @endif</td>
                                            <td class="text-right">@if($v->tipe == 'KREDIT') <a href="" onclick="doShowDetail('{{$v->id_trx_akun}}');" data-toggle="modal" data-target="#modalShowDetail">{{formatRupiah($v->jumlah)}}</a> @else 0 @endif</td>
                                            <td class="text-center">{{$v->customer}}</td>
                                            <td class="text-center">{{$v->supplier}}</td>
                                            <td class="text-right">{{formatRupiah($sub_total)}}</td>
                                        </tr>
                                        @endforeach
                                    @endif
                                </tbody>
                                @endforeach
                                <tbody>
                                    <tr id="table">
                                        <th colspan="5" class="text-center">Total</th>
                                        <th class="text-right">{{formatRupiah($total_debit)}}</th>
                                        <th class="text-right">{{formatRupiah($total_kredit)}}</th>
                                        <th colspan="2" class="text-center"></th>
                                        <th class="text-right">{{formatRupiah($sub_total)}}</th>
                                    </tr>
                                </tbody>
                    @endforeach
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="modalShowDetail" tabindex="-1" role="dialog" aria-labelledby="modalShowDetailLabel1">
    <div class="modal-dialog  modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="modalAddMaterialLabel1">Detail Jurnal</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <table style="width:100%">
                    <thead>
                        <tr>
                            <td>Deskripsi</td>
                            <td>:</td>
                            <td id="deskripsi"></td>
                        </tr>
                        <tr>
                            <td>Tanggal</td>
                            <td>:</td>
                            <td id="date">/td>
                        </tr>
                    </thead>
                </table>
                <br>
                <button class="btn btn-primary" id="btn-po" data-id="1" onclick="doShowPO()" data-toggle="modal" data-target="#modalShowPurchase">PO</button>
                <button class="btn btn-primary" id="btn-po-asset" data-id="1" onclick="doShowPOAsset()" data-toggle="modal" data-target="#modalShowPurchaseAsset">PO Asset</button>
                <button class="btn btn-primary" id="btn-inv" data-id="1" onclick="doShowInv()" data-toggle="modal" data-target="#modalShowInv">Penerimaan</button>
                <button class="btn btn-primary" id="btn-req-dev" data-id="1" onclick="doShowReqDev()" data-toggle="modal" data-target="#modalShowReqDev">Jurnal Permintaan</button>
                <br><br>
                <div class="table-responsive">
                    <table id="dt_detail" class="table table-bordered">
                        <thead>
                            <tr>
                                <th class="text-center">No</th>
                                <th class="text-center">Nama Akun</th>
                                <th class="text-center">Tipe</th>
                                <th class="text-center">Total</th>
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
<div class="modal fade" id="modalShowReqDev" tabindex="-1" role="dialog" aria-labelledby="modalShowDetailLabel1">
    <div class="modal-dialog  modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="modalAddMaterialLabel1">Journal Permintaan Detail</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table id="req_dev_detail" class="table table-bordered">
                        <thead>
                            <tr>
                                <th class="text-center">Tanggal</th>
                                <th class="text-center">Deskripsi</th>
                                <th class="text-center">Akun</th>
                                <th class="text-center">No Akun</th>
                                <th class="text-center">Tipe</th>
                                <th class="text-center">Total</th>
                            </tr>
                        </thead>     
                        <tbody></tbody>                                 
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <!-- <button type="button" onclick="saveMaterial(this.form);" class="btn btn-primary btn-sm">Save</button> -->
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalShowPurchase" tabindex="-1" role="dialog" aria-labelledby="modalShowDetailLabel1">
    <div class="modal-dialog  modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="modalAddMaterialLabel1">Purchase Order Detail</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table id="po_detail" class="table table-bordered">
                        <thead>
                            <tr>
                                <th class="text-center">No Material</th>
                                <th class="text-center">Nama Material</th>
                                <th class="text-center">Volume</th>
                                <th class="text-center">Satuan</th>
                                <th class="text-center">Harga</th>
                                <th class="text-center">Total</th>
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
<div class="modal fade" id="modalShowPurchaseAsset" tabindex="-1" role="dialog" aria-labelledby="modalShowDetailLabel1">
    <div class="modal-dialog  modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="modalAddMaterialLabel1">Purchase Order Detail</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table id="po_asset_detail" class="table table-bordered">
                        <thead>
                            <tr>
                                <th class="text-center">No Material</th>
                                <th class="text-center">Nama Material</th>
                                <th class="text-center">Volume</th>
                                <th class="text-center">Satuan</th>
                                <th class="text-center">Harga</th>
                                <th class="text-center">Total</th>
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
<div class="modal fade" id="modalShowInv" tabindex="-1" role="dialog" aria-labelledby="modalShowDetailLabel1">
    <div class="modal-dialog  modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="modalAddMaterialLabel1">Penerimaan Detail</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table id="inv_detail" class="table table-bordered">
                        <thead>
                            <tr>
                                <th class="text-center">No Material</th>
                                <th class="text-center">Nama Material</th>
                                <th class="text-center">Volume</th>
                                <th class="text-center">Satuan</th>
                                <th class="text-center">Harga</th>
                                <th class="text-center">Total</th>
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
$(document).ready( function(){
var users_table = $('#list_akun').DataTable({
            processing: true,
            serverSide: true,
            ajax: '{{URL::to('akuntansi/gl')}}',
            // columnDefs: [ {
            //     "targets": [4],
            //     "orderable": false,
            //     "searchable": false
            // } ],
            "columns":[
                // {"data":"id_akun"},
                {"data":"no_akun"},
                {"data":"nama_akun"},
                {"data":"debit", "render" : function(data, type, row){ return (row.debit == 0 ? 'Berkurang' : 'Bertambah')}},
                {"data":"kredit", "render" : function(data, type, row){ return (row.kredit == 0 ? 'Berkurang' : 'Bertambah')}},
                {"data":"id_akun", "render" : function(data, type, row){ return '<a href="{{URL::to('akuntansi/detail-gl/')}}/'+row.id_akun+'" class="btn btn-xs btn-primary"><i class="mdi mdi-book-open-page-variant"></i></a>'}},
            ]
        });
});
$(document).ready( function(){

});
dt_detail=$('#dt_detail').DataTable();
po_detail=$('#po_detail').DataTable();
inv_detail=$('#inv_detail').DataTable();
po_asset_detail=$('#po_asset_detail').DataTable();
req_dev_detail=$('#req_dev_detail').DataTable();
function doShowDetail(id){
    dt_detail.clear().draw(false);
    $.ajax({
            type: "GET",
            url: "{{ URL::to('akuntansi/detail-trx-akun') }}" + "/" + id, //json get site
            dataType : 'json',
            success: function(response){
                arrData = response['data'];
                $('#deskripsi').html(arrData[0]['deskripsi'])
                $('#date').html(formatDateID(new Date((arrData[0]['tanggal']).substring(0,10))))
                               
                if (arrData[0]['purchase_id'] == null || arrData[0]['purchase_id'] == 0) {
                    $('#btn-po').hide();
                }else{
                    $('#btn-po').show();
                    $('#btn-po').data('id', arrData[0]['purchase_id'])
                }
                if (arrData[0]['inv_trx_id'] == null || arrData[0]['inv_trx_id'] == 0) {
                    $('#btn-inv').hide();
                }else{
                    $('#btn-inv').show();
                    $('#btn-inv').data('id', arrData[0]['inv_trx_id'])
                }
                if (arrData[0]['purchase_asset_id'] == null || arrData[0]['purchase_asset_id'] == 0) {
                    $('#btn-po-asset').hide();
                }else{
                    $('#btn-po-asset').show();
                    $('#btn-po-asset').data('id', arrData[0]['purchase_asset_id'])
                }
                if (arrData[0]['project_req_development_id'] == null || arrData[0]['project_req_development_id'] == 0) {
                    $('#btn-req-dev').hide();
                }else{
                    $('#btn-req-dev').show();
                    $('#btn-req-dev').data('id', arrData[0]['project_req_development_id'])
                }
                for(i = 0; i < arrData.length; i++){
                    // a = i+1;
                    dt_detail.row.add([
                        '<div class="text-left">'+arrData[i]['no_akun']+'</div>',
                        '<div class="'+(arrData[i]['tipe'] == 'DEBIT' ? 'text-left' : 'text-center')+'">'+arrData[i]['nama_akun']+'</div>',
                        '<div class="text-left">'+arrData[i]['tipe']+'</div>',
                        '<div class="text-right">'+formatCurrency(parseFloat(arrData[i]['jumlah']).toFixed(2))+'</div>',
                    ]).draw(false);
                }
            }
    });
}
function doShowPO(){
    $('#modalShowDetail').modal('toggle');
    po_detail.clear().draw(false);
    var id=$('#btn-po').data('id');
    $.ajax({
            type: "GET",
            url: "{{ URL::to('po_konstruksi/detail') }}" + "/" + id, //json get site
            dataType : 'json',
            success: function(response){
                arrData = response['data'];
                for(i = 0; i < arrData.length; i++){
                    total=(parseFloat(arrData[i]['amount']) * parseFloat(arrData[i]['base_price'])).toFixed(0);
                    po_detail.row.add([
                        '<div class="text-left">'+arrData[i]['m_items']['no']+'</div>',
                        '<div class="text-left">'+arrData[i]['m_items']['name']+'</div>',
                        '<div class="text-right">'+arrData[i]['amount']+'</div>',
                        '<div class="text-center">'+arrData[i]['m_units']['name']+'</div>',
                        '<div class="text-right">'+formatCurrency(parseFloat(arrData[i]['base_price']).toFixed(2))+'</div>',
                        '<div class="text-right">'+formatCurrency(total.toString())+'</div>'
                    ]).draw(false);
                }
            }
    });
}
function doShowPOAsset(){
    $('#modalShowDetail').modal('toggle');
    po_asset_detail.clear().draw(false);
    var id=$('#btn-po-asset').data('id');
    $.ajax({
            type: "GET",
            url: "{{ URL::to('po_konstruksi/detail_atk') }}" + "/" + id, //json get site
            dataType : 'json',
            success: function(response){
                arrData = response['data'];
                for(i = 0; i < arrData.length; i++){
                    total=(parseFloat(arrData[i]['amount']) * parseFloat(arrData[i]['base_price'])).toFixed(0);
                    po_asset_detail.row.add([
                        '<div class="text-left">'+arrData[i]['m_items']['no']+'</div>',
                        '<div class="text-left">'+arrData[i]['m_items']['name']+'</div>',
                        '<div class="text-right">'+arrData[i]['amount']+'</div>',
                        '<div class="text-center">'+arrData[i]['m_units']['name']+'</div>',
                        '<div class="text-right">'+formatCurrency(parseFloat(arrData[i]['base_price']).toFixed(2))+'</div>',
                        '<div class="text-right">'+formatCurrency(total.toString())+'</div>'
                    ]).draw(false);
                }
            }
    });
}
function doShowInv(){
    $('#modalShowDetail').modal('toggle');
    inv_detail.clear().draw(false);
    var id=$('#btn-inv').data('id');
    $.ajax({
            type: "GET",
            url: "{{ URL::to('akuntansi/detail-inv') }}" + "/" + id, //json get site
            dataType : 'json',
            success: function(response){
                arrData = response['data'];
                for(i = 0; i < arrData.length; i++){
                    total=(parseFloat(arrData[i]['amount']) * parseFloat(arrData[i]['base_price'])).toFixed(0);
                    inv_detail.row.add([
                        '<div class="text-left">'+arrData[i]['m_items']['no']+'</div>',
                        '<div class="text-left">'+arrData[i]['m_items']['name']+'</div>',
                        '<div class="text-right">'+arrData[i]['amount']+'</div>',
                        '<div class="text-center">'+arrData[i]['m_units']['name']+'</div>',
                        '<div class="text-right">'+formatCurrency(parseFloat(arrData[i]['base_price']).toFixed(2))+'</div>',
                        '<div class="text-right">'+formatCurrency(total.toString())+'</div>'
                    ]).draw(false);
                }
            }
    });
}
function doShowReqDev(){
    $('#modalShowDetail').modal('toggle');
    po_detail.clear().draw(false);
    var id=$('#btn-req-dev').data('id');
    $('#req_dev_detail > tbody').empty();
    $.ajax({
            type: "GET",
            url: "{{ URL::to('akuntansi/detail-req-dev') }}" + "/" + id, //json get site
            dataType : 'json',
            success: function(response){
                arrData = response['data'];
                for(var i = 0; i < arrData.length; i++){
                    var a=0;
                    for(var j = 0; j < arrData[i]['detail'].length; j++){
                        var td='<tr>'+
                                '<td class="text-center">'+(j == 0 ? formatDate2(arrData[i]['tanggal']) : '')+'</td>'+
                                '<td class="text-center">'+(j == 0 ? arrData[i]['deskripsi'] : '')+'</td>'+
                                '<td class="text-center">'+arrData[i]['detail'][j]['nama_akun']+'</td>'+
                                '<td class="text-center">'+arrData[i]['detail'][j]['no_akun']+'</td>'+
                                '<td class="text-center">'+arrData[i]['detail'][j]['tipe']+'</td>'+
                                '<td class="text-center">'+formatCurrency(parseFloat(arrData[i]['detail'][j]['jumlah']).toFixed(2))+'</td>'+
                            '</tr>';
                        console.log(td)
                        $('#req_dev_detail').find('tbody:last').append(td);
                    }
                }
            }
    });
}
function formatDate2(date){
        if (date == null) {
            return '-';
        }else{

            var myDate = new Date(date);
            var tgl=date.split(/[ -]+/);
            // var output = tgl[2] + "-" +  tgl[1] + "-" + tgl[0] + ' ' + tgl[3];
            var output = tgl[2] + "-" +  tgl[1] + "-" + tgl[0];
            return output;
        }
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
</script>
@endsection