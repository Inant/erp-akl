<?php
function formatNumber($val){
    $val=number_format($val, 0, '.', '.');
    return $val;
}
?>
<style>
table, th, td{
    border : 1px solid #536677;
    padding : 5px
}
</style>
<h4>Rekap Kebutuhan Material {{$order->coorporate_name}}</h4>
<table style="width:100%">
    @foreach($rab as $rab)
    <thead>
        <tr>
            <th colspan="6">Kavling {{$rab->name}}</th>
        </tr>
        <tr>
            <th>No</th>
            <th>Profile</th>
            <th>Harga</th>
            <th>Ukuran</th>
            <th>Jumlah</th>
            <th>Total Harga</th>
        </tr>
    </thead>
    <tbody>
    @php $sub_total = 0 @endphp
        @foreach($rab->detail as $key => $value)
        @php $sub_total += $value['base_price'] * $value['amount'] @endphp
        <tr>
            <td>{{$value['m_item_no']}}</td>
            <td>{{$value['m_item_name']}}</td>
            <td class="text-right">{{formatNumber($value['base_price'])}}</td>
            <td>{{$value['amount_unit_child']}}</td>
            <td>{{formatNumber($value['amount'])}}</td>
            <td class="text-right">{{formatNumber($value['base_price'] * $value['amount'])}}</td>
        </tr>
        @endforeach
        <tr>
            <td colspan="5">Sub Total</td>
            <td class="text-right">{{formatNumber($sub_total)}}</td>
        </tr>
    </tbody>
    @endforeach
</table>