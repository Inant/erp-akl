@php
    function formatRupiah($num){
        return number_format($num, 0, '.', '.');
    }
    function formatDate($date){
        $date=date_create($date);
        return date_format($date, 'd/m/Y');
    }
@endphp
<table>
    <thead>
        <tr>
            <th colspan="10">Daftar Kontrak Pemasangan</th>
        </tr>
        <tr>
            <th colspan="10"></th>
        </tr>
        <tr>
            <th>No</th>
            <th>Order No</th>
            <th>No SPJB</th>
            <th>Nama Proyek</th>
            <th>No SPK</th>
            <th>Catatan</th>
            <th>Customer</th>
            <th>Tanggal Order</th>
            <th>Total Kontrak</th>
            <th>PPN</th>
            <th>Sub Total</th>
        </tr>
    </thead>
    <tbody>
    @foreach($data['data'] as $row)
    <tr>
        <td>{{$row['no']}}</td>
        <td>{{$row['order_no']}}</td>
        <td>{{$row['spk_number']}}</td>
        <td>{{$row['project_name']}}</td>
        <td>{{$row['spk_no']}}</td>
        <td>{{$row['notes']}}</td>
        <td>{{$row['customer_coorporate']}}</td>
        <td>{{formatDate($row['order_date'])}}</td>
        <td>{{$row['total_kontrak']}}</td>
        <td>{{$row['total_kontrak'] * 0.1}}</td>
        <td>{{$row['total_kontrak'] + ($row['total_kontrak'] * 0.1)}}</td>
    </tr>
    @endforeach
    </tbody>
</table>