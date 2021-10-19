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
            <th colspan="10">Rincian Pembelian per Barang per Tanggal {{formatDate($data['date1'])}} - {{formatDate($data['date2'])}}</th>
        </tr>
        <tr>
            <th colspan="10"></th>
        </tr>
        <tr>
            <th>Tanggal Faktur</th>
            <th>No Faktur</th>
            <th>No Barang</th>
            <th>Nama Barang</th>
            <th>Keterangan</th>
            <th>Kuantitas</th>
            <th>Unit</th>
            <th>Harga</th>
            <th>Jumlah</th>
            <th>PPN</th>
            <th>Sub Total</th>
            <th>Nama Supplier</th>
        </tr>
    </thead>
    <tbody>
    <?php $total_saldo_all=$total_item=$total_value=$total_ppn=0 ?>
    @foreach($data['data'] as $value)
        <?php
        $total=($value->amount * $value->base_price);
        $with_ppn=($value->p_without_ppn != null ? $value->p_without_ppn : ($value->pa_without_ppn != null ? $value->pa_without_ppn : false));
        $ppn=$with_ppn == false ? $total * 0.1 : 0;
        $total_item+=$value->amount;
        $total_value+=$total;
        $total_ppn+=$ppn;
        $total_saldo_all+=($total + $ppn);
        ?>
        <tr>
            <td>{{formatDate($value->inv_trx_date)}}</td>
            <td>{{$value->no_surat_jalan}}</td>
            <td>{{$value->item_no}}</td>
            <td>{{$value->item_name}}</td>
            <td>{{($value->p_notes != null ? $value->p_notes : ($value->pa_notes != null ? $value->pa_notes : ''))}}</td>
            <td>{{$value->amount}}</td>
            <td>{{$value->unit_name}}</td>
            <td>{{($value->base_price)}}</td>
            <td>{{($value->amount * $value->base_price)}}</td>
            <td>{{($ppn)}}</td>
            <td>{{($total + $ppn)}}</td>
            <td>{{($value->supplier1 != null ? $value->supplier1 : ($value->supplier2 != null ? $value->supplier2 : ''))}}</td>
        </tr>
    @endforeach
        <tr>
            <td colspan="5">Total</td>
            <td>{{($total_item)}}</td>
            <td></td>
            <td></td>
            <td>{{($total_value)}}</td>
            <td>{{($total_ppn)}}</td>
            <td>{{($total_saldo_all)}}</td>
            <td></td>
        </tr>
    </tbody>
</table>