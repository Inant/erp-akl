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
            <th colspan="5">Pembayaran Tagihan Supplier</th>
        </tr>
        <tr>
            <th colspan="5"></th>
        </tr>
    </thead>
    <thead>
        <tr>
            <th class="text-center">No</th>
            <th class="text-center">Invoice</th>
            <th class="text-center">Total</th>
            <th class="text-center">Supplier</th>
            <th class="text-center">Tanggal</th>
        </tr>
    </thead>
    <tbody>
    @foreach($data['data'] as $value)
        <tr>
            <td>{{$value->no}}</td>
            <td>{{$value->dt}}</td>
            <td>{{$value->amount}}</td>
            <td>{{$value->name}}</td>
            <td>{{formatDate($value->paid_date)}}</td>
        </tr>
    @endforeach
    </tbody>
</table>