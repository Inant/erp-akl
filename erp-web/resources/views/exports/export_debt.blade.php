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
            <th colspan="7">Rincian Hutang Usaha</th>
        </tr>
        <tr>
            <th>Nomor</th>
            <th>Supplier</th>
            <th>Deskripsi</th>
            <th>Total</th>
            <th>Tanggal Hutang</th>
            <th>Tanggal Jatuh Tempo</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
    @php $total_all=$n=0 @endphp
    @foreach($data['data'] as $value)
        <tr>
            <td>{{$value->no}}</td>
            <td>{{$value->name}}</td>
            <td>{{($value->notes)}}</td>
            <td>{{($value->amount)}}</td>
            <td>{{formatDate($value->debt_date)}}</td>
            <td>{{formatDate($value->due_date)}}</td>
            <td>{{$value->is_paid == false ? 'Belum Dibayar' : 'Sudah Dibayar'}}</td>
        </tr>
    @endforeach
    </tbody>
</table>