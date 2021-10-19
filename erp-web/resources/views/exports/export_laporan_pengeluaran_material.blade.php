@php
function formatRupiah($num)
{
    return round($num);
}
function formatDate($date)
{
    $date = date_create($date);
    return date_format($date, 'd/m/Y');
}
@endphp
<table border="1">
    <thead>
        <tr>
            <th colspan="8">Laporan Pengeluaran Material per Tanggal {{ formatDate(Request::get('date')) }} -
                {{ formatDate(Request::get('date2')) }}</th>
        </tr>
    </thead>
    <thead>
        <tr>
            <th>No</th>
            <th>Tanggal</th>
            {{-- <th>No Surat Jalan</th> --}}
            <th>No SPK</th>
            <th>No Material</th>
            <th>Nama Material</th>
            <th>QTY</th>
            @if (auth()->user()['role_id'] == 1)
                <th>Harga</th>
                <th>Jumlah</th>
            @endif
        </tr>
    </thead>
    <tbody>
        @php
            $totalQty = 0;
            $totalHarga = 0;
        @endphp
        @foreach ($laporan as $item)
            @php
                $totalQty += formatRupiah($item->amount);
                $totalHarga += formatRupiah($item->amount * $item->base_price);
            @endphp
            <tr>
                <td>{{ $item->no }}</td>
                <td>{{ formatDate($item->inv_trx_date) }}</td>
                {{-- <td>{{$item->no_surat_jalan}}</td> --}}
                <td>{{ $item->spk_number }}</td>
                <td>{{ $item->no_material }}</td>
                <td>{{ $item->nama_material }}</td>
                <td>{{ formatRupiah($item->amount) }}</td>
                @if (auth()->user()['role_id'] == 1)
                    <td>{{ formatRupiah($item->base_price) }}</td>
                    <td>{{ formatRupiah($item->amount * $item->base_price) }}</td>
                @endif
            </tr>
        @endforeach
        <tr id="table" class="table-info">
            <th colspan="5" style="text-align: center">Total</th>
            <th class="text-right">{{ formatRupiah($totalQty) }}</th>
            @if (auth()->user()['role_id'] == 1)
                <th></th>
                <th class="text-right">{{ formatRupiah($totalHarga) }}</th>
            @endif
        </tr>
    </tbody>
</table>
<?php
header('Content-type: application/vnd-ms-excel');
header('Content-Disposition: attachment; filename=laporan_pengeluaran_material.xls');
?>
