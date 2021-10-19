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
            <th colspan="8">Daftar Kontrak Pengadaan</th>
        </tr>
        <tr>
            <th colspan="8"></th>
        </tr>
        <tr>
            <th>Order No</th>
            <th>No SPJB</th>
            <th>Nama Proyek</th>
            <th>Order Deskripsi</th>
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
        <td>{{$row['order_no']}}</td>
        <td>{{$row['spk_number']}}</td>
        <td>{{$row['project_name']}}</td>
        <td>{{strip_tags($row['order_name'])}}</td>
        <td>{{$row['customer_coorporate']}}</td>
        <td>{{formatDate($row['order_date'])}}</td>
        <td>{{$row['total_kontrak']}}</td>
        <td>{{$row['total_kontrak'] * 0.1}}</td>
        <td>{{$row['total_kontrak'] + ($row['total_kontrak'] * 0.1)}}</td>
    </tr>
    @endforeach
    </tbody>
</table>