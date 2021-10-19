<?php
function formatRupiah($val){
    $val=number_format($val, 0, '.', '.');
    return $val;
}
?>

@php
foreach ($cust_project as $a => $m) {
@endphp
<h4>Order : {{$m->order_no}}</h4>
@php
$total_produksi_order=0;
foreach ($m->order_d as $key => $value) {
    $beban_produksi=0;
@endphp

<h5>Produksi No Permintaan {{$value->no}}</h5>
<br>

<div class="table-responsive">
    <table style="width:100%">
        <!-- <thead>
            <tr>
                <th>Produksi No Permintaan {{$value->no}}</th>
                <td colspan="3"></td>
            </tr>
        </thead> -->
        <?php $sub_total_item=0; ?>
        @foreach($value->inv_request as $k => $v)
        <?php $total_item=0; ?>
        <tbody style="background-color:#3c8dbc; color:white">
            <tr>
                <th>{{($v->req_type != 'RET_ITEM' ? 'Permintaan Material' : 'Pengembalian')}} Nomor {{$v->no}}</th>
                <th class="text-center">@if($k == 0) Total item @endif</th>
                <th class="text-center">@if($k == 0) Harga @endif</th>
                <th class="text-center">@if($k == 0) Sub total @endif</th>
            </tr>
        </tbody>
            @foreach($v->detail as $m)
            <?php 
            $total=$m->amount * $m->base_price; 
            $total_item=($v->req_type != 'RET_ITEM' ? $total_item + $total : $total_item - $total);
            ?>
            <tbody>
                <tr>
                    <td>{{$m->item_name}}</td>
                    <td class="text-center">{{$m->amount}}</td>
                    <td class="text-right">{{formatRupiah($m->base_price)}}</td>
                    <td class="text-right">{{$v->req_type != 'RET_ITEM' ? '' : '- '}}{{formatRupiah($total)}}</td>
                </tr>
            </tbody>
            @endforeach
            <?php $sub_total_item+=$total_item; ?>
            <tbody>
                <tr>
                    <td class="text-center" colspan="3">Total</td>
                    <td class="text-right">{{formatRupiah($sub_total_item)}}</td>
                </tr>
            </tbody>
        @endforeach
            <tbody>
                <tr>
                    <td class="text-center" colspan="3">Sub Total Item</td>
                    <td class="text-right">{{formatRupiah($sub_total_item)}}</td>
                </tr>
            </tbody>
        </table>
</div>
<div class="table-responsive">
    <table style="width:100%">
        <thead style="background-color:#3c8dbc; color:white">
            <tr>
                <th>Biaya Jasa Produksi No Permintaan {{$value->no}}</th>
                <th></th>
            </tr>
        </thead>
        @foreach($value->prd_detail as $v)
        <tbody>
            <tr>
                <?php
                    $total=$v->total_debit - $v->total_kredit;
                    $beban_produksi+=$total;
                ?>
                <td>{{$v->nama_akun}}</td>
                <td class="text-right">{{$total > 0 ? formatRupiah($total) :  '-'.formatRupiah($total)}}</td>
            </tr>
        </tbody>
        @endforeach
        <tr style="background-color:#ddd;">
            <th>Total Jasa</th>
            <th class="text-right">Rp. {{formatRupiah($beban_produksi)}}</th>
        </tr>
        <?php 
            $total_produksi_order+=($sub_total_item + $beban_produksi);
        ?>
        <tr style="background-color:#ddd;">
            <th>Total Produksi</th>
            <th class="text-right">Rp. {{formatRupiah($sub_total_item + $beban_produksi)}}</th>
        </tr>
        <tr>
            <th></th>
            <th></th>
        </tr>
    </table>
</div>
@php
}
@endphp
<br><br>
<div class="table-responsive">
    <table style="width:100%">
        <thead>
            <th>Total HPP Proyek</th>
            <th class="text-right">Rp. {{formatRupiah($total_produksi_order)}}</th>
        </thead>
    </table>
</div>
<hr>
@php
}
@endphp


<style>
    table, th, td{
        padding : 3px;
    }
</style>
