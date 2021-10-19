@extends('theme.default')

@section('breadcrumb')
    <div class="page-breadcrumb">
        <div class="row">
            <div class="col-5 align-self-center">
                <h4 class="page-title">Tambah Hutang Usaha</h4>
                <div class="d-flex align-items-center">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ URL::to('payment') }}">Hutang Usaha</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Tambah</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>
@endsection


@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Tambah Hutang Usaha</h4>
                    <form method="POST" action="{{ URL::to('payment/save_debt') }}" class="mt-4" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>Supplier</label>
                                    <select name="supplier_id" id="supplier_id" class="form-control select2 custom-select" style="width: 100%; height:32px;" required>
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-6" @if(auth()->user()['role_id'] != 1) hidden @endif>
                                <div class="form-group">
                                    <label for="">Tanggal Hutang</label>
                                    <input type="date" name="date" id="date" class="form-control" required value="{{date('Y-m-d')}}">
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="">Tanggal Jatuh Tempo</label>
                                    <input type="date" name="due_date" id="due_date" class="form-control" required>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>Deskripsi</label>
                                    <textarea name="description" id="description" class="form-control" required></textarea>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="">Total</label>
                                    <input type="text" name="total" id="total" class="form-control" onkeyup="formatTotal(this)" required>
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <button type="submit" id="submit" class="btn btn-primary">Simpan</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        
        </div>
    </div>
</div>

<!-- /.modal -->
<!-- <script src="{!! asset('theme/assets/libs/jquery/dist/jquery.min.js') !!}"></script> -->
<script src="{!! asset('theme/assets/libs/sweetalert2/dist/sweetalert2.all.min.js') !!}"></script>
<script>
$(document).ready(function() {
    $.ajax({
        type: "GET",
        url: "{{ URL::to('pembelian/supplier') }}", //json get site
        dataType : 'json',
        success: function(response){
            arrSuppl = response['data'];
            formSuppl = $('#supplier_id');
            formSuppl.empty();
            formSuppl.append('<option value="">-- Select Supplier --</option>');
            for(j = 0; j < arrSuppl.length; j++){
                formSuppl.append('<option value="'+arrSuppl[j]['id']+'">'+arrSuppl[j]['name']+'</option>');
            }
        }
    });

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
    // $('#account_beban').empty();
    // $('#account_beban').append('<option value="">-- Pilih Akun --</option>');
    // $.ajax({
    //     // type: "post",
    //     url: "{{ URL::to('akuntansi/account_adm') }}",
    //     dataType : 'json',
    //     success: function(response){
    //         arrData = response;
    //         for(i = 0; i < arrData.length; i++){
    //             if (arrData[i]['label'] == 111 || arrData[i]['label'] == 112) {
    //                 $('#account_beban').append('<option value="'+arrData[i]['label']+'">'+arrData[i]['value']+'</option>');
    //             }
    //         }
    //     }
    // });
});
function getOrderNo(order_id){
    formReqNo = $('[id^=req_id]');
    formReqNo.empty();
    formReqNo.append('<option value="">-- Pilih Nomor Permintaan --</option>');
    $.ajax({
        type: "GET",
        url: "{{ URL::to('home/get_req_no/') }}"+'/'+order_id, //json get site
        dataType : 'json',
        success: function(response){
            arrData = response['data'];
            for(i = 0; i < arrData.length; i++){
                formReqNo.append('<option value="'+arrData[i]['id']+'">'+arrData[i]['no']+'</option>');
            }
        }
    });
}
function cekTipe(val){
    if (val == 'card') {
        $('#bank').hide()
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
function formatTotal(eq){
    var total=formatNumber(eq.value);
    $('#total').val(total);
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

<script>
    // Replace the <textarea id="editor1"> with a CKEditor
    // instance, using default configuration.
    CKEDITOR.replace( 'editor1' );
</script>
@endsection