@php
function formatRupiah($num)
{
    return number_format($num, 0);
}
function formatDate($date)
{
    $date = date_create($date);
    return date_format($date, 'd/m/Y');
}
@endphp
<table>
    <thead>
        <tr>
            <th>Nama Pemasok</th>
            <th>Jumlah Tagihan</th>
            {{-- <th>Belum</th> --}}
            <th>1-30</th>
            <th>30-60</th>
            <th>60-90</th>
            <th>90-120</th>
            <th>> 120</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($data['data'] as $value)
            @if ($value->detail->total_in_one_months > 0 || $value->detail->total_in_two_months > 0 || $value->detail->total_in_three_months || $value->detail->total_in_four_months || $value->detail->total_in_five_months > 0)
                @php
                    $totalTagihan = $value->detail->total_in_one_months + $value->detail->total_in_two_months + $value->detail->total_in_three_months + $value->detail->total_in_four_months + $value->detail->total_in_five_months;
                @endphp
                <tr>
                    <td>{{ $value->name }}</td>
                    <td>{{$totalTagihan}}</td>
                    {{-- <td></td> --}}
                    <td>{{ $value->detail->total_in_one_months }}</td>
                    <td>{{ $value->detail->total_in_two_months }}</td>
                    <td>{{ $value->detail->total_in_three_months }}</td>
                    <td>{{ $value->detail->total_in_four_months }}</td>
                    <td>{{ $value->detail->total_in_five_months }}</td>
                </tr>
            @endif
        @endforeach
    </tbody>
</table>
