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
            <th colspan="10">Stok {{$data['category']}} {{$data['warehouse']}} per Tanggal {{formatDate($data['date1'])}} - {{formatDate($data['date2'])}}</th>
        </tr>
        <tr>
            <th>Gudang</th>
            <th>Material No</th>
            <th>Material Name</th>
            <th>Unit Name</th>
            <th>Stok Awal</th>
            <th>Stok Masuk</th>
            <th>Stok Keluar</th>
            <th>Stok</th>
            <th>Harga</th>
            <th>Nilai Item</th>
        </tr>
    </thead>
    <tbody>
    @php $total_all=$total_item=0 @endphp
    @foreach($data['data'] as $value)
        @php 
        $total=($value->stok * $value->price_last); 
        $total_item+=$value->stok;
        $total_all+=$total;
        @endphp
        <tr>
            <td>{{$value->warehouse}}</td>
            <td>{{$value->no}}</td>
            <td>{{$value->name}}</td>
            <td>{{($value->unit_name)}}</td>
            <td>{{($value->stok_awal)}}</td>
            <td>{{($value->amount_in)}}</td>
            <td>{{($value->amount_out)}}</td>
            <td>{{($value->stok)}}</td>
            <td>{{($value->price_last)}}</td>
            <td>{{$total}}</td>
            
        </tr>
    @endforeach
        <tr class="table-info">
            <td colspan="7">Sub Total</td>
            <td>{{($total_item)}}</td>
            <td></td>
            <td>{{($total_all)}}</td>
        </tr>
    </tbody>
</table>