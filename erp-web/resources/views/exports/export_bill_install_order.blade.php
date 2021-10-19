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
            <th colspan="12">Daftar Tagihan Install Order Customer</th>
        </tr>
        <tr>
            <th colspan="12"></th>
        </tr>
        <tr>
            <th>No</th>
            <th>Nama Customer</th>
            <th>Nomor Order</th>
            <th>Nomor SPJB</th>
            <th>Nomor SPK</th>
            <th>Nomor Tagihan</th>
            <th>Nomor Faktur</th>
            <th>Tanggal Tagihan</th>
            <th>Tanggal Jatuh Tempo</th>
            <th>Total Tagihan</th>
            <th>Alamat Tagihan</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
    @foreach($data['data'] as $value)
        <tr>
            <td>{{$value->no}}</td>
            <td>{{$value->coorporate_name}}</td>
            <td>{{$value->order_no}}</td>
            <td>{{$value->spk_number}}</td>
            <td>{{$value->spk_no}}</td>
            <td>{{$value->invoice_no}}</td>
            <td>{{$value->bill_no}}</td>
            <td>{{$value->create_date != null ? formatDate($value->create_date) : '-'}}</td>
            <td>{{formatDate($value->due_date)}}</td>
            <td>{{round($value->amount, 0)}}</td>
            <td>{{$value->bill_address}}</td>
            <td>{{$value->is_paid == true ? 'Dibayar' : 'Belum Dibayar'}}</td>
        </tr>
    @endforeach
    </tbody>
</table>