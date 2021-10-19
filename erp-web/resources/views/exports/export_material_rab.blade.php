<table>
    <thead>
        <tr>
            <th colspan="5">Rekap Kebutuhan RAB No {{$data['rab']->rab_no}}</th>
        </tr>
        <tr>
            <th colspan="5">{{$data['rab']->project_name}} Kavling : {{$data['rab']->kavling_name}}</th>
        </tr>
        <tr>
            <th colspan="5">Material Global</th>
        </tr>
        <tr>
            <th>No</th>
            <th>Profile</th>
            <th>Ukuran</th>
            <th>Harga</th>
            <th>Jumlah</th>
        </tr>
    </thead>
    <tbody>
        @foreach($data['material'] as $key => $value)
        @php $total=$value['amount'] - ($value['kanan'] + $value['kiri']) @endphp
        @if($value['category'] == 'MATERIAL' && $total != 0)
        <tr>
            <td>{{$value['m_item_no']}}</td>
            <td>{{$value['m_item_name']}}</td>
            <td>{{$value['amount_unit_child']}}</td>
            <td>{{$value['best_price']}}</td>
            <td>{{$total}}</td>
        </tr>
        @endif
        @endforeach
        <tr>
            <th colspan="5"></th>
        </tr>
    </tbody>
    <tbody>
        <tr>
            <th colspan="5">SPARE PART Global</th>
        </tr>
        @foreach($data['material'] as $key => $value)
        @php $total=$value['amount'] - ($value['kanan'] + $value['kiri']) @endphp
        @if($value['category'] == 'SPARE PART' && $total != 0)
        <tr>
            <td>{{$value['m_item_no']}}</td>
            <td>{{$value['m_item_name']}}</td>
            <td>{{$value['amount_unit_child']}}</td>
            <td>{{$value['best_price']}}</td>
            <td>{{$total}}</td>
        </tr>
        @endif
        @endforeach
        <tr>
            <th colspan="5"></th>
        </tr>
    </tbody>
    <tbody>
        <tr>
            <th colspan="5">KACA Global</th>
        </tr>
        @foreach($data['material'] as $key => $value)
        @php $total=$value['amount'] - ($value['kanan'] + $value['kiri']) @endphp
        @if($value['category'] == 'KACA' && $total != 0)
        <tr>
            <td>{{$value['m_item_no']}}</td>
            <td>{{$value['m_item_name']}}</td>
            <td>{{$value['amount_unit_child']}}</td>
            <td>{{$value['best_price']}}</td>
            <td>{{$total}}</td>
        </tr>
        @endif
        @endforeach
        <tr>
            <th colspan="5"></th>
        </tr>
    </tbody>
    <tbody>
        <tr>
            <th colspan="5">SPARE PART KANAN</th>
        </tr>
        @foreach($data['material'] as $key => $value)
        @if($value['kanan'] != 0)
        <tr>
            <td>{{$value['m_item_no']}}</td>
            <td>{{$value['m_item_name']}}</td>
            <td>{{$value['amount_unit_child']}}</td>
            <td>{{$value['best_price']}}</td>
            <td>{{$value['kanan']}}</td>
        </tr>
        @endif
        @endforeach
        <tr>
            <th colspan="5"></th>
        </tr>
    </tbody>
    <tbody>
        <tr>
            <th colspan="5">SPARE PART KANAN</th>
        </tr>
        @foreach($data['material'] as $key => $value)
        @if($value['kiri'] != 0)
        <tr>
            <td>{{$value['m_item_no']}}</td>
            <td>{{$value['m_item_name']}}</td>
            <td>{{$value['amount_unit_child']}}</td>
            <td>{{$value['best_price']}}</td>
            <td>{{$value['kiri']}}</td>
        </tr>
        @endif
        @endforeach
    </tbody>
</table>