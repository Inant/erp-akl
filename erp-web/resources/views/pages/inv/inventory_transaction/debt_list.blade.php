@extends('theme.default')

@section('breadcrumb')
			<div class="page-breadcrumb">
                <div class="row">
                    <div class="col-5 align-self-center">
                        <h4 class="page-title">Laporan Hutang dan Pembayaran Supplier</h4>
                        <div class="d-flex align-items-center">
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item active" aria-current="page">Tagihan</li>
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
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Laporan Hutang dan Pembayaran Supplier</h4>
                    <!-- <div class="row"> -->
                        <form method="POST" action="{{ URL::to('inventory/debt_list') }}">
                            @csrf
                            <div class="row">
                                <div class="col-6">
                                    <h5 class="card-title">Pilih Supplier</h5>
                                    <div class="form-group">
                                        <select class="select2 form-control" multiple="multiple" style="height: 36px; width: 100%;" id="suppl_single" name="suppl_single[]">
                                            <option value="all" {{$all_supplier == true ? 'selected' : ''}}>Semua</option>
                                            @foreach($suppliers as $value)
                                                <?php $same=false; ?>
                                                @foreach($supplier_selected as $v)
                                                    @if($v == $value['id'])
                                                        @php $same=true @endphp
                                                    @endif
                                                @endforeach
                                            <option value="{{$value['id']}}" {{$same == true ? 'selected' : ''}}>{{$value['name']}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    
                                </div>
                                <div class="col-6">
                                    <div class="form-group">
                                        <label for="">Tanggal Awal :</label>
                                        <input type="date" name="date" class="form-control" required value="{{$date1}}">
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="form-group">
                                        <label >Tanggal Ahir :</label>
                                        <input type="date" name="date2" class="form-control" required value="{{$date2}}">
                                    </div>
                                </div>
                                <div class="col-12 form-group">
                                    <button class="btn btn-primary" name="submit" value="1"><i class="fa fa-search"></i> Cari</button>
                                </div>
                            </div>
                        </form>
                    <!-- </div> -->
                    <br><br>
                    @if($data != null)
                    <form method="POST" action="{{ URL::to('inventory/export_debt_list') }}" class="float-right" target="_blank">
                        @csrf
                        <div hidden>
                            <select class="select2 form-control" multiple="multiple" style="height: 36px; width: 100%;" id="suppl_single1" name="suppl_single[]">
                                <option value="all" {{$all_supplier == true ? 'selected' : ''}}>Semua</option>
                                @foreach($suppliers as $value)
                                    <?php $same=false; ?>
                                    @foreach($supplier_selected as $v)
                                        @if($v == $value['id'])
                                            @php $same=true @endphp
                                        @endif
                                    @endforeach
                                <option value="{{$value['id']}}" {{$same == true ? 'selected' : ''}}>{{$value['name']}}</option>
                                @endforeach
                            </select>
                        </div>
                        <input type="date" name="date" hidden class="form-control"  required value="{{$date1}}">
                        <input type="date" name="date2" hidden class="form-control"  required value="{{$date2}}">
                        <div class="form-group">
                            <button class="btn btn-success"><i class="fa fa-file-excel"></i> Export</button>
                        </div>
                    </form>
                    <div class="table-responsive">
                        <table style="border-collapse:collapse; width:100%">
                            <thead>
                                <tr id="table" class="text-primary">
                                    <th class="text-center">Tanggal</th>
                                    <th class="text-center">No PO</th>
                                    <th class="text-center">No Tagihan Supplier</th>
                                    <th class="text-center">No Pembayaran</th>
                                    <th class="text-center">Supplier</th>
                                    <th class="text-center" width="200px">Keterangan</th>
                                    <th class="text-center">Nilai DPP</th>
                                    <th class="text-center">PPN</th>
                                    <th class="text-center">Debit</th>
                                    <th class="text-center">Kredit</th>
                                    <th class="text-center">Saldo (asing)</th>
                                </tr>
                            </thead>
                            <tbody>
                            @foreach($data as $value)
                                <?php 
                                $total_saldo=$total_penambahan=$total_penurunan=0;
                                $saldo_awal=$value['perubahan_saldo']->total_kredit - $value['perubahan_saldo']->total_debit;
                                ?>
                                @if($value['data'] != null || $saldo_awal != 0)
                                <tr id="table">
                                    <td></td>
                                    <td>{{$value['supplier']->name}}</td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td class="text-right">{{formatRupiah($value['perubahan_saldo']->total_kredit - $value['perubahan_saldo']->total_debit)}}</td>
                                </tr>
                                <?php 
                                $total_saldo=$value['perubahan_saldo']->total_kredit - $value['perubahan_saldo']->total_debit; 
                                // $total_penambahan+=$total_saldo;
                                ?>
                                @foreach($value['data'] as $v)
                                    @foreach($v['dt'] as $v1)
                                    <?php 
                                    $total_saldo=($v1->tipe == 'KREDIT' ? ($total_saldo + $v1->jumlah) : ($total_saldo - $v1->jumlah));
                                    if ($v1->tipe == 'KREDIT') {
                                        $total_penambahan+=$v1->jumlah;
                                    }else{
                                        $total_penurunan+=$v1->jumlah;
                                    }
                                    ?>
                                    <tr id="table">
                                        <td>{{date('d-m-Y', strtotime($v['date']))}}</td>
                                        <td>{{$v1->purchase_no != null ? $v1->purchase_no : ($v1->purchase_asset_no != null ? $v1->purchase_asset_no : '-')}}</td>
                                        <td>{{$v1->ps_no != null ? $v1->ps_no : '-'}}</td>
                                        <td>{{$v1->source != null ? $v1->source : '-'}}</td>
                                        <td>{{$value['supplier']->name}}</td>
                                        <td>{{$v1->p_notes != null ? $v1->p_notes : ($v1->pa_notes != null ? $v1->p_notes : ($v1->deskripsi != null ? $v1->deskripsi : '-'))}}</td>
                                        <td class="text-right">{{formatRupiah($v1->jumlah - $v1->ppn)}}</td>
                                        <td class="text-right">{{formatRupiah($v1->ppn)}}</td>
                                        <td class="text-right">{{$v1->tipe == 'DEBIT' ? formatRupiah($v1->jumlah) : '-'}}</td>
                                        <td class="text-right">{{$v1->tipe == 'KREDIT' ? formatRupiah($v1->jumlah) : '-'}}</td>
                                        <td class="text-right">{{formatRupiah($total_saldo)}}</td>
                                    </tr>
                                    @endforeach
                                @endforeach
                                <tr id="table" class="text-primary">
                                    <td colspan="8" class="text-center">Total</td>
                                    <td class="text-right">{{formatRupiah($total_penurunan)}}</td>
                                    <td class="text-right">{{formatRupiah($total_penambahan)}}</td>
                                    <td class="text-right">{{formatRupiah($total_saldo)}}</td>
                                </tr>
                                @endif
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>                
</div>
<div class="modal fade bs-example-modal-lg" id="modalBillDetail" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <form action="{{URL::to('payment/save_giro_detail')}}" method="post">
            @csrf
            <div class="modal-header">
                <h4 class="modal-title" id="title-modal">Laporan Hutang dan Pembayaran Supplier </h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="giro_id" id="giro_id">
                <div class="form-group">
                    <label for="">Total Laporan Hutang dan Pembayaran Supplier :</label>
                    <input type="" readonly class="form-control" id="total1">
                    <input type="hidden" class="form-control" name="total" id="total">
                </div>
                <div class="form-group pull-right">
                    <button class="btn btn-primary" type="button" onclick="addRow()">add</button>
                </div>
                
                <table id="listDetail" style="width:100%">
                    <thead>
                        <tr>
                            <th style="width:40%">Total</th>
                            <th style="width:50%">Pilih Pencairan Akun</th>
                            <td></td>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
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
var account_payment=[]
$(document).ready(function(){
    // $.ajax({
    //     type: "GET",
    //     url: "{{ URL::to('pembelian/supplier') }}", //json get site
    //     dataType : 'json',
    //     success: function(response){
    //         arrSuppl = response['data'];
    //         formSuppl = $('#suppl_single');
    //         formSuppl.empty();
    //         formSuppl.append('<option value="all">Semua</option>');
    //         for(j = 0; j < arrSuppl.length; j++){
    //             formSuppl.append('<option value="'+arrSuppl[j]['id']+'">'+arrSuppl[j]['name']+'</option>');
    //         }
    //     }
    // });

    $.ajax({
        // type: "post",
        url: "{{ URL::to('akuntansi/account_payment') }}",
        dataType : 'json',
        success: function(response){
            arrData = response;
            account_payment=arrData;
        }
    });
    $('#giro_list').DataTable( {
        "processing": true,
        "serverSide": true,
        "ajax": "{{ URL::to('payment/json_giro') }}",
        aaSorting: [[0, 'desc']],
        "columns": [
            {"data": "no", "class" : "text-center"},
            {"data": "customer_bill.no", "class" : "text-center"},
            {"data": "pay_date", "class" : "text-center",
            "render": function(data, type, row){return row.pay_date != '' ? formatDateID(new Date((row.pay_date).substring(0,10))) : '-'}},
            {"data": "amount", "class" : "text-right",
            "render": function(data, type, row){return formatCurrency(row.amount)}},
            {"data": "is_divided",
            "render": function(data, type, row){return row.is_divided == false ? 'Belum Dicairkan' : "Sudah Dicairkan"}},
            {"data": "id", "class" : "text-center",
            "render": function(data, type, row){return row.is_divided == false ? '<button onclick="doShowDetail(this);" data-toggle="modal" data-no="'+row.no+'" data-id="'+row.id+'" data-amount="'+row.amount+'" data-target="#modalBillDetail" class="btn waves-effect waves-light btn-sm btn-info"><i class="mdi mdi-credit-card-plus"></i></button>' : ''}},
        ],
    } );
});
function doShowDetail(eq){
    var id=$(eq).data('id');
    var amount=$(eq).data('amount');
    $('#giro_id').val(id);
    $('#total1').val(formatCurrency(amount));
    $('#total').val(amount);
    $('#listDetail > tbody').empty();
}
function addRow(){
    var option_account='<option value="">Pilih Akun Pembayaran</option>'
    for(i = 0; i < account_payment.length; i++){
        option_account+='<option value="'+account_payment[i]['label']+'">'+account_payment[i]['value']+'</option>';
    }
    var tdAdd='<tr>'+
        '<td>'+
            '<input id="amount[]" onkeyup="cekTotal()" name="amount[]" class="form-control"/>'+
        '</td>'+
        '<td>'+
            '<select name="account_payment[]" style="width:100%" class="custom-select select2 form-control" required>'+option_account+'</select>' +
        '</td>'+
        '<td class="text-left"><button class="btn btn-danger removeOption"><i class="mdi mdi-delete"></i></button></td>'+
    '</tr>';
    $('#listDetail').find('tbody:last').append(tdAdd);
    $('.custom-select').select2();
}
$("#listDetail").on("click", ".removeOption", function(event) {
    event.preventDefault();
    $(this).closest("tr").remove();
});
function cekTotal(){
    var amount=$('#total').val();
    var total=$('[id^=amount]');
    var total_all=0;
    for(i = 0; i < total.length; i++){
        var qty=total.eq(i).val();
        var paid=(qty).replace(/[^,\d]/g, '').toString();
        total_all+=parseFloat(paid);
        total.eq(i).val(formatCurrency(paid.toString()));
        if (total_all > parseFloat(amount)) {
            total.eq(i).val('');
        }
    }
}
function cekTipe(val){
    if(val == 'bank'){
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
function clickPrint(id) {
    setTimeout(() => {
        window.open("{{ URL::to('po_konstruksi/print') }}" + "/" + id, '_blank')
    }, 500);
}

</script>


@endsection