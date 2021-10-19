@php
    function formatRupiah($num){
        return number_format($num, 0, '.', '.');
    }
    function formatDate($date){
        $date=date_create($date);
        return date_format($date, 'd/m/Y');
    }
    function datediffnow($date) {
        $now = time(); // or your date as well
        $your_date = strtotime($date);
        $datediff = $now - $your_date;

        return round($datediff / (60 * 60 * 24));
    }
@endphp
<table>
    <thead>
        <tr>
            <th>Gudang</th>
            <th>Material No</th>
            <th>Material Name</th>
            <th>Nilai Material</th>
            <th>Harga Satuan</th>
            <th>Satuan</th>
            <th>Stock In</th>
            <th>Stock Out</th>
            <th>Current Stock</th>
            <th>Last Update</th>
            <th>Umur</th>
        </tr>
    </thead>
    <tbody>
    @foreach($data['data'] as $row)
    <?php
    $last_update_in = $row->last_update_in == null ? '1990-01-01' : $row->last_update_in;
    $last_update_out = $row->last_update_out == null ? '1990-01-01' : $row->last_update_out;
    $last_update = strtotime($last_update_in) > strtotime($last_update_out) ? $last_update_in : $last_update_out;
    ?>
    <tr>
        <td>{{$row->m_warehouse != null ? $row->m_warehouse->name : ''}}</td>
        <td>{{$row->m_items->no}}</td>
        <td>{{$row->m_items->name}}</td>
        <td>{{(round($row->amount_in, 0)*round($row->last_price, 0))}}</td>
        <td>{{(round($row->last_price))}}</td>
        <td>{{$row->m_units->name}}</td>
        <td>{{round($row->amount_in, 0)}}</td>
        <td>{{round($row->amount_out, 0)}}</td>
        <td>{{round($row->stok, 0)}}</td>
        <td>{{formatDate($last_update)}}</td>
        <td>{{datediffnow($last_update)}}</td>
    </tr>
    @endforeach
    </tbody>
</table>