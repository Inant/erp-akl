<?php
function formatRupiah($val){
    $val=number_format($val, 0, '.', '.');
    return $val;
}
?>

<div class="table-responsive">
    <table class="table table-bordered">
        <thead style="background-color:#3c8dbc; color:white">
            <tr>
                <th>Pendapatan dari Penjualan</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @php
            $sum_pendapatan=0;
            foreach ($pendapatan as $key => $value) {
            @endphp
            <tr>
                <?php
                    $total=$value->total_kredit - $value->total_debit;
                    $sum_pendapatan+=$total;
                ?>
                <td>{{$value->nama_akun}}</td>
                <td class="text-right">{{$total > 0 ? formatRupiah($total) :  '-'.formatRupiah($total)}}</td>
            </tr>
            @php
            }
            @endphp
            <tr style="background-color:#ddd">
                <th>Total Pendapatan dari Penjualan</th>
                <th class="text-right">Rp. {{formatRupiah($sum_pendapatan)}}</th>
            </tr>
        </tbody>
        <tbody>
            <tr>
                <th></th>
                <th></th>
            </tr>
        </tbody>
        @php
        $sum_beban=0;
        foreach ($get_project_dev as $key => $value) {
            $beban_produksi=0;
        @endphp
        <thead style="background-color:#3c8dbc; color:white">
            <tr>
                <th>Biaya Produksi No Permintaan {{$value->no}}</th>
                <th></th>
            </tr>
        </thead>
        @foreach($value->prd_detail as $v)
        <tbody>
            <tr>
                <?php
                    $total=$v->total_debit - $v->total_kredit;
                    $beban_produksi+=$total;
                    $sum_beban+=$total;
                ?>
                <td>{{$v->nama_akun}}</td>
                <td class="text-right">{{$total > 0 ? formatRupiah($total) :  '-'.formatRupiah($total)}}</td>
            </tr>
        </tbody>
        @endforeach
        <tr style="background-color:#cc7575; color : white">
            <th>Total Produksi</th>
            <th class="text-right">Rp. {{formatRupiah($beban_produksi)}}</th>
        </tr>
        @php
        }
        @endphp
        <tr>
                <th></th>
                <th></th>
            </tr>
        <tr style="background-color:#ddd">
            <th>Total Beban Produksi</th>
            <th class="text-right">Rp. {{formatRupiah($sum_beban)}}</th>
        </tr>
        <tbody>
            <tr>
                <th></th>
                <th></th>
            </tr>
        </tbody>
        <tr style="background-color:#ddd">
            <th>Gross Profit</th>
            <th class="text-right">Rp. {{formatRupiah($sum_pendapatan - $sum_beban)}}</th>
        </tr>
    </table>
</div>