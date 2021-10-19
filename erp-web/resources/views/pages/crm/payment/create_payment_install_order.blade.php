@extends('theme.default')

@section('breadcrumb')
    <div class="page-breadcrumb">
        <div class="row">
            <div class="col-5 align-self-center">
                <h4 class="page-title">Tambah Pembayaran Pemasangan Produk</h4>
                <div class="d-flex align-items-center">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ URL::to('payment') }}">Pembayaran Pemasangan Produk</a></li>
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
                    <h4 class="card-title">Tambah Pembayaran Pemasangan Produk</h4>
                    <form method="POST" action="{{ URL::to('payment/save_install_order') }}" class="mt-4" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>Nomor Order Instalasi</label>
                                    <select name="install_order_id" onchange="getBillInstall(this)" class="form-control select2 custom-select" style="width: 100%; height:32px;" required>
                                        <option value="">--- Pilih Nomor Order Instalasi ---</option>
                                        @if($order_list != null)
                                        @foreach($order_list as $value)
                                            <option value="{{ $value['id'] }}">{{ $value['no'] }}</option>
                                        @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>
                            <style>
                                table, th, td{
                                    border:1px solid #969696;
                                    padding : 5px
                                }
                            </style>
                            <div class="col-sm-12">
                                <div class="table-responsive form-group">
                                    <table id="detail_work" style="width:100%; border-collapse: collapse; border:1px solid black">
                                        <thead style="border:1px solid black">
                                            <tr style="border:1px solid black">
                                                <th rowspan="2" class="text-center">No</th>
                                                <th rowspan="2" >Item Pekerjaan</th>
                                                <th colspan="2" class="text-center">Kontrak</th>
                                                <th rowspan="2" class="text-center">Total</th>
                                            </tr>
                                            <tr>
                                                <th class="text-center">Qty</th>
                                                <th class="text-center">Biaya</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <th colspan="4" class="text-center">Total</th>
                                                <th class="text-right"><p id="total_paid"></p></th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                            
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>Tanggal</label>
                                    <input type="date"  class="form-control" name="pay_date" id="pay_date" required value="{{date('Y-m-d')}}">
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
                            <div class="col-sm-4">
                                <div class="form-group">
                                <label>Tipe Pembayaran</label><br>
                                    <select name="wop" id="wop" class="form-control select2" style="width:100%" onchange="cekTipe(this.value)" required>
                                        <option value="">-- Pilih Tipe Pembayaran --</option>
                                        <option value="cash">Tunai</option>
                                        <!-- <option value="card">Kartu</option> -->
                                        <option value="bank_transfer">Transfer Bank</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-2">
                            <br><br>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" id="inlineCheckbox1" name="is_out_source" value="1">
                                    <label class="form-check-label" for="inlineCheckbox1">Jasa Out Sourcing</label>
                                </div>
                            </div>
                            <div class="col-sm-6" id="card" style="display:none">
                                <div class="form-group">
                                    <label for="">Ref Code</label>
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
                            <!-- <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="">Akun Beban</label>
                                    <select name="account_beban" id="account_beban" class="select2 form-control" style="width:100%" required>
                                    </select>
                                </div>
                            </div> -->
                            <br>
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
function getBillInstall(eq){
    $('#detail_work > tbody').empty();
    var id=eq.value;
    var total=0;
    $.ajax({
        // type: "post",
        url: "{{ URL::to('payment/getBillInstall') }}"+'/'+id,
        dataType : 'json',
        async : false,
        success: function(response){
            arrData = response['data'];
            var a=1
            for(i = 0; i < arrData.length; i++){
                var tdAdd='<tr>'+
                                '<td class="text-center">'+a+'</td>'+
                                '<td>'+arrData[i]['item']+'</td>'+
                                '<td></td>'+
                                '<td></td>'+
                                '<td></td>'+
                            '</tr>';
                $('#detail_work').find('tbody:last').append(tdAdd);
                for (index = 0; index < arrData[i]['detail'].length; index++) {
                    var tdAdd='<tr>'+
                                    '<td></td>'+
                                    '<td>'+arrData[i]['detail'][index]['worksub_name']+'</td>'+
                                    '<td>'+arrData[i]['detail'][index]['total']+'</td>'+
                                    '<td class="text-right">'+formatCurrency(arrData[i]['detail'][index]['price'])+'</td>'+
                                    '<td class="text-right">'+formatCurrency(parseFloat(arrData[i]['detail'][index]['total'] * arrData[i]['detail'][index]['price']))+'</td>'+
                                '</tr>';
                    $('#detail_work').find('tbody:last').append(tdAdd);
                    total+=parseFloat(arrData[i]['detail'][index]['total'] * arrData[i]['detail'][index]['price']);
                }
                a++;
            }
        }
    });
    $('#total_paid').html(formatCurrency(total));
}
$("#detail_work").on("click", ".removeOption", function(event) {
    event.preventDefault();
    $(this).closest("tr").remove();
});
</script>
@endsection