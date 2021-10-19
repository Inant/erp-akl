@extends('theme.default')

@section('breadcrumb')
			<div class="page-breadcrumb">
                <div class="row">
                    <div class="col-5 align-self-center">
                        <h4 class="page-title">Pengisian Giro</h4>
                        <div class="d-flex align-items-center">
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item active" aria-current="page">Giro</li>
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
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Daftar Pengisian Giro</h4>
                    <div class="table-responsive">
                        <table id="giro_list" class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th class="text-center">No</th>
                                    <th class="text-center">Nomor Tagihan</th>
                                    <th class="text-center"  style="min-width:100px">Tanggal Bayar</th>
                                    <th class="text-center">Jumlah</th>
                                    <th class="text-center">Status</th>
                                    <th></th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>                
</div>
<div class="modal fade bs-example-modal-lg" id="modalBillDetail" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <form action="{{URL::to('payment/save_pengisian_giro')}}" method="post">
            @csrf
            <div class="modal-header">
                <h4 class="modal-title" id="title-modal">Pengisian Giro </h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="giro_id" id="giro_id">
                <div class="form-group">
                    <label for="">Total Pengisian Giro :</label>
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
        "ajax": "{{ URL::to('payment/json_giro_supplier') }}",
        aaSorting: [[0, 'desc']],
        "columns": [
            {"data": "no", "class" : "text-center"},
            {"data": "paid_supplier.no", "class" : "text-center"},
            {"data": "pay_date", "class" : "text-center",
            "render": function(data, type, row){return row.pay_date != null ? formatDateID(new Date((row.pay_date).substring(0,10))) : '-'}},
            {"data": "amount", "class" : "text-right",
            "render": function(data, type, row){return formatCurrency(row.amount)}},
            {"data": "is_divided",
            "render": function(data, type, row){return row.is_divided == false ? 'Belum Dibayarkan' : "Sudah Dibayarkan"}},
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