
<?php
function formatDate($date){
    $tgl=date('d-m-Y', strtotime($date));
    return $tgl;
}
function formatRupiah($val){
    $a=number_format($val, 0, '.', '.');
    return $a;
}
$total_debit=$total_kredit=0;
?>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="form-group">
                    <button onclick="submitGL()" class="btn btn-primary" style="margin-top:5px">Buku Besar</button>
                    @if($id == 151)
                    <button onclick="submitPiutang()" class="btn btn-primary" style="margin-top:5px">Laporan Penjualan</button>
                    <button onclick="submitPiutangAll()" class="btn btn-primary" style="margin-top:5px">Laporan Piutang dan Pembayaran Customer</button>
                    @endif
                    @if($id == 147)
                    <button onclick="submitPOHistory()" class="btn btn-primary" style="margin-top:5px">Laporan Pembelian per Supplier</button>
                    <button onclick="submitDebtSupplier()" class="btn btn-primary" style="margin-top:5px">Laporan Hutang Supplier</button>
                    <button onclick="submitDebtList()" class="btn btn-primary" style="margin-top:5px">Laporan Hutang dan Pembayaran Supplier</button>
                    @endif
                </div>
                <form method="POST" action="{{ URL::to('inventory/sell_customer') }}" class="form-inline" id="form-piutang" target="_blank">
                    @csrf
                    <input type="text" hidden name="customer_id[]" class="form-control" value="all">
                    <input type="date" name="date" class="form-control" hidden required value="{{$date1}}">
                    <input type="date" name="date2" class="form-control" hidden required value="{{$date2}}">
                    <input name="submitBtn" value="1" hidden>
                    <!-- <button name="submit" value="1" class="btn btn-primary" id="submit">Laporan Penjualan</button> -->
                </form>
                <form method="POST" action="{{ URL::to('inventory/piutang_all') }}" class="form-inline" id="form-piutang-all" target="_blank">
                    @csrf
                    <input type="text" hidden name="customer_id[]" class="form-control" value="all">
                    <input type="date" name="date" class="form-control" hidden required value="{{$date1}}">
                    <input type="date" name="date2" class="form-control" hidden required value="{{$date2}}">
                    <input name="submitBtn" value="1" hidden>
                </form>
                <form method="POST" action="{{ URL::to('inventory/debt_supplier') }}" class="form-inline" id="form-debt" target="_blank">
                    @csrf
                    <input type="text" hidden name="suppl_single[]" class="form-control" value="all">
                    <input type="date" name="date" class="form-control" hidden required value="{{$date1}}">
                    <input type="date" name="date2" class="form-control" hidden required value="{{$date2}}">
                    <input name="submitBtn" value="1" hidden>
                    <!-- <button name="submit" value="1" class="btn btn-primary" id="submit">Laporan Penjualan</button> -->
                </form>
                <form method="POST" action="{{ URL::to('inventory/po_history') }}" class="form-inline" id="form-po-history" target="_blank">
                    @csrf
                    <input type="text" hidden name="suppl_single[]" class="form-control" value="all">
                    <input type="date" name="date" class="form-control" hidden required value="{{$date1}}">
                    <input type="date" name="date2" class="form-control" hidden required value="{{$date2}}">
                    <input name="submitBtn" value="1" hidden>
                </form>
                <form method="POST" action="{{ URL::to('inventory/debt_list') }}" class="form-inline" id="form-debt-list" target="_blank">
                    @csrf
                    <input type="text" hidden name="suppl_single[]" class="form-control" value="all">
                    <input type="date" name="date" class="form-control" hidden required value="{{$date1}}">
                    <input type="date" name="date2" class="form-control" hidden required value="{{$date2}}">
                    <input name="submitBtn" value="1" hidden>
                </form>
                <form method="POST" action="{{ URL::to('akuntansi/gl') }}" class="form-inline" id="form-gl" target="_blank">
                    @csrf
                    <input type="text" hidden name="account[]" class="form-control" value="{{$id}}">
                    <input type="date" name="date" class="form-control" hidden required value="{{$date1}}">
                    <input type="date" name="date2" class="form-control" hidden required value="{{$date2}}">
                    <input name="submitBtn" value="1" hidden>
                </form>
                <div class="table-responsive">
                    <table class="table table-bordered" id="">
                        <thead>
                            <tr>
                                <th class="text-center">Tanggal</th>
                                <th class="text-center">No Akun</th>
                                <th class="text-center">Nama Akun</th>
                                <th class="text-center" style="min-width:200px">No</th>
                                <th class="text-center">Deskripsi</th>
                                <!-- <th class="text-center">Debit</th> -->
                                <th class="text-center">Jumlah</th>
                                <th class="text-center">Customer</th>
                                <th class="text-center">Supplier</th>
                                <th class="text-center">Total Saldo</th> 
                            </tr>
                        </thead>
                        <?php 
                        //$sub_total=($saldo_awal->jumlah_saldo != null ? $saldo_awal->jumlah_saldo : 0) + $saldo_before_start_date
                        $sub_total=0;
                        ?>
                        <!-- <tbody>
                            <tr>
                                <td class="text-center"></td>
                                <td class="text-center"></td>
                                <td class="text-center"></td>
                                <td class="text-center"></td>
                                <td class="text-center"></td> -->
                                <!-- <td class="text-right"></td> -->
                                <!-- <td class="text-right"></td>
                                <td class="text-right"></td>
                                <td class="text-right"></td>
                                <td class="text-right">{{formatRupiah($sub_total)}}</td>
                            </tr>
                        </tbody> -->
                        @foreach($data as $value)
                        <tbody>
                            @if(count($value['dt']) == 0)
                            <tr>
                                <td class="text-center">{{formatDate($value['date'])}}</td>
                                <td class="text-center"></td>
                                <td class="text-center"></td>
                                <td class="text-center"></td>
                                <td class="text-center"></td>
                                <!-- <td class="text-right">0</td> -->
                                <td class="text-right">0</td>
                                <td class="text-right"></td>
                                <td class="text-right"></td>
                                <td class="text-right">{{formatRupiah($sub_total)}}</td>
                            </tr>
                            @else
                                @foreach($value['dt'] as $k => $v)
                                <?php
                                $total=round($v->jumlah, 0);
                                // if ($v->tipe == 'DEBIT') {
                                //     if ($akun->sifat_debit == 1) {
                                //         $sub_total+=$total;
                                //     }else{
                                //         $sub_total-=$total;
                                //     }
                                //     $total_debit+=$total;
                                // }else{
                                //     if ($akun->sifat_kredit == 1) {
                                //         $sub_total+=$total;
                                //     }else{
                                //         $sub_total-=$total;
                                //     }
                                //     $total_kredit+=$total;
                                // }
                                $sub_total+=$total;
                                ?>
                                <tr>
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
                                    {{$v->purchases != null ? $v->purchases->no : ($v->purchase_assets != null ? $v->purchase_assets->no : ($v->orders != null ? $v->orders->order_no : ($v->ts_warehouses != null ? $v->ts_warehouses->no : ($v->debts != null ? $v->debts->no : ($v->install_orders != null ? $v->install_orders->no : ($v->giros != null ? $v->giros->no : ''))))))}}
                                    
                                    @endif
                                    </td>
                                    <td class="text-left">{{$v->deskripsi}}</td>
                                    <!-- <td class="text-right">@if($v->tipe == 'DEBIT') <a href="" onclick="doShowDetail('{{$v->id_trx_akun}}');" data-toggle="modal" data-target="#modalShowDetail">{{formatRupiah($v->jumlah)}}</a> @else 0 @endif</td>
                                    <td class="text-right">@if($v->tipe == 'KREDIT') <a href="" onclick="doShowDetail('{{$v->id_trx_akun}}');" data-toggle="modal" data-target="#modalShowDetail">{{formatRupiah($v->jumlah)}}</a> @else 0 @endif</td> -->
                                    <td class="text-right"><a href="" onclick="doShowDetail('{{$v->id_trx_akun}}');" data-toggle="modal" data-target="#modalShowDetail">{{formatRupiah($v->jumlah)}}</a></td>
                                    <td class="text-center">{{$v->customer}}</td>
                                    <td class="text-center">{{$v->supplier}}</td>
                                    <td class="text-right">{{formatRupiah($sub_total)}}</td>
                                </tr>
                                @endforeach
                            @endif
                        </tbody>
                        @endforeach
                        <tfoot>
                            <tr>
                                <th colspan="5" class="text-center">Total</th>
                                <!-- <th class="text-right">{{formatRupiah($total_debit)}}</th>
                                <th class="text-right">{{formatRupiah($total_kredit)}}</th> -->
                                <th></th>
                                <th colspan="2" class="text-center"></th>
                                <th class="text-right">{{formatRupiah($sub_total)}}</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
$(document).ready( function(){

});
function submitPiutang(){
    var form = document.getElementById("form-piutang");
    form.submit();
}
function submitPiutangAll(){
    var form = document.getElementById("form-piutang-all");
    form.submit();
}
function submitPOHistory(){
    var form = document.getElementById("form-po-history");
    form.submit();
}
function submitDebtSupplier(){
    var form = document.getElementById("form-debt");
    form.submit();
}
function submitDebtList(){
    var form = document.getElementById("form-debt-list");
    form.submit();
}
function submitGL(){
    var form = document.getElementById("form-gl");
    form.submit();
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