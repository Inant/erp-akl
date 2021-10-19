<center>
    <h4><b>Laba Rugi Sementara Proyek</b></h4>
    <h5><b>Nama Proyek : {{$namaProyek->name}}</b></h5>
</center>
<table border="1">
    <thead>
        <tr  style="background-color: #d7f2ed">
            <th>No Kontrak</th>
            <th>Nama Customer</th>
            <th>Nilai Kontrak</th>
            <th>Pendapatan</th>
            <th>Biaya</th>
            <th>Gross Profit</th>
        </tr>
    </thead>
    <tbody>
        @php
            $totalPenjualan = 0;
            $totalProduksi = 0;
            $totalKontrak = 0;
            $totalProfit = 0;
        @endphp
        @foreach ($data as $key => $order)
            @php
                $sum_pendapatan=0;
                    foreach($order['dp'] as $value){
                    $sum_pendapatan += $value->jumlah;
                }

                $sum_beban=$order['biaya']->total;
            @endphp
            <tr>
                <td>{{$order['spk_number']}}</td>
                <td>{{$order['customer']->coorporate_name}}</td>
                <td>{{round($order['nilaiKontrak'])}}</td>
                <td>{{round($sum_pendapatan)}}</td>
                <td>{{round($sum_beban)}}</td>
                <td>{{round($sum_pendapatan - $sum_beban)}}</td>
            </tr>
            @php
                $totalPenjualan += $sum_pendapatan;
                $totalProduksi += $sum_beban;
                $totalKontrak += $order['nilaiKontrak'];
                $totalProfit += ($sum_pendapatan - $sum_beban);
            @endphp
        @endforeach
        <tr>
            <th colspan="2">Total</th>
            <th>{{round($totalKontrak)}}</th>
            <th>{{round($totalPenjualan)}}</th>
            <th>{{round($totalProduksi)}}</th>
            <th>{{round($totalProfit)}}</th>
        </tr>
    </tbody>
</table>
<script src="https://code.jquery.com/jquery-1.10.2.js"></script>

@php
header("Content-type: application/vnd-ms-excel");
header("Content-Disposition: attachment; filename=laba_rugi_sementara_proyek.xls");
@endphp