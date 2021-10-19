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
<div class="table-responsive">
    <h4>RAB {{$order->coorporate_name}} Kavling {{$rab[0]->kavling_name}}</h4>
    <table style="width:100%">
        <thead>
            <tr>
                <th rowspan="2">No</th>
                <th rowspan="2" colspan="2">Item Pekerjaan</th>
                <th rowspan="2">Profil</th>
                <th rowspan="2">Volume</th>
                <th rowspan="2">Satuan</th>
                <th colspan="2">Ukuran</th>
                <th colspan="5">Harga Satuan Pekerjaan Pengadaan & Pemasangan</th>
                <th rowspan="2">Kavling</th>
                <th rowspan="2">Jumlah</th>
            </tr>
            <tr>
                <th>W(mm)</th>
                <th>H(mm)</th>
                <th>Aluminium</th>
                <th>Kaca</th>
                <th>Parts</th>
                <th>Upah Kerja</th>
                <th>Jumlah</th>
            </tr>
        </thead>
        <tbody>
            @php $sub_total=$material=$kaca=$parts=$upah=$total_kavling=0 @endphp
            @foreach($rab as $key => $value)
            @php 
            $material += $value->material;
            $kaca += $value->kaca;
            $parts += $value->spare_part;
            $upah += $value->jasa;
            $total_kavling += $value->total;
            $sub_total += $value->jumlah;
            @endphp
            <tr>
                <td></td>
                <td>{{$value->item}}</td>
                <td>{{$value->name}}</td>
                <td>{{$value->series}}</td>
                <td>{{$value->total}}</td>
                <td>{{$value->unit_name}}</td>
                <td>{{$value->panjang}}</td>
                <td>{{$value->lebar}}</td>
                <td class="text-right">{{formatNumber($value->material)}}</td>
                <td class="text-right">{{formatNumber($value->kaca)}}</td>
                <td class="text-right">{{formatNumber($value->spare_part)}}</td>
                <td class="text-right">{{formatNumber($value->jasa)}}</td>
                <td class="text-right">{{formatNumber($value->jumlah)}}</td>
                <td></td>
                <td class="text-right">{{formatNumber($value->jumlah)}}</td>
            </tr>
            @endforeach
            <tr>
                <td colspan="4">Sub Total</td>
                <td>{{$total_kavling}}</td>
                <td colspan="3">Sub Total</td>
                <td class="text-right">{{formatNumber($material)}}</td>
                <td class="text-right">{{formatNumber($kaca)}}</td>
                <td class="text-right">{{formatNumber($parts)}}</td>
                <td class="text-right">{{formatNumber($upah)}}</td>
                <td class="text-right">{{formatNumber($sub_total)}}</td>
                <td></td>
                <td class="text-right">{{formatNumber($sub_total)}}</td>
            </tr>
            <tr>
                <td colspan="12">Nilai Kontrak</td>
                <td class="text-right">{{formatNumber($amount_contract)}}</td>
                <td></td>
                <td class="text-right">{{formatNumber($amount_contract)}}</td>
            </tr>
            <tr>
                <td colspan="12">Perkiraan Laba</td>
                <td class="text-right">{{formatNumber($amount_contract - $sub_total)}}</td>
                <td></td>
                <td class="text-right">{{formatNumber($amount_contract - $sub_total)}}</td>
            </tr>
            @php $persen_laba=(($amount_contract - $sub_total) / $amount_contract) * 100 @endphp
            <tr>
                <td colspan="12"></td>
                <td class="text-right">{{round($persen_laba, 2)}} %</td>
                <td></td>
                <td class="text-right">{{round($persen_laba, 2)}} %</td>
            </tr>
        </tbody>
    </table>
</div>