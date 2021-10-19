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
            <th colspan="10">Rincian Stok Masuk No PO/No Surat Jalan {{$data['no']}} per Tanggal {{formatDate($data['date1'])}} - {{formatDate($data['date2'])}}</th>
        </tr>
        <tr>
            <th>TGL FP</th>
            <th>No Surat Jalan / Faktur</th>
            <th>Supplier</th>
            <th>Jenis Barang</th>
            <th>QTY</th>
            <th>SAT</th>
            <th>Harga Satuan</th>
            <th>Jumlah</th>
            <th>Total</th>
            <th>Tanggal Penerimaan</th>
        </tr>
    </thead>
    <tbody>
    @php $total_all=$n=0 @endphp
    @foreach($data['data'] as $row)
        <?php 
        $without_ppn=($row->p_without_ppn != null ? $row->p_without_ppn : ($row->pa_without_ppn != null ? $row->pa_without_ppn : false));
        $index=$jumlah=$jumlah_ppn=0;
        ?>
        @foreach($row->item as $value)
        <?php 
        $index++;
        $total=($value->base_price * $value->amount);
        $ppn=($without_ppn == false ? ($total * 0.1) : 0);
        $jumlah+=$total;
        $jumlah_ppn+=$ppn;
        ?>
        <tr>
            <td>@if($index == 1) {{formatDate($row->inv_trx_date)}} @endif</td>
            <td>@if($index == 1) {{$row->no_surat_jalan}} @endif</td>
            <td>@if($index == 1) {{($row->supplier1 != null ? $row->supplier1 : $row->supplier2)}} @endif</td>
            <td>{{($value->item_name)}}</td>
            <td>{{($value->amount)}}</td>
            <td>{{($value->unit_name)}}</td>
            <td>{{$value->base_price}}</td>
            <td>{{$total}}</td>
            <td></td>
            <td></td>
            <td></td>
        </tr>
        @endforeach
        <tr>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td>{{$jumlah}}</td>
            <td>{{$jumlah}}</td>
            <td>{{$jumlah_ppn}}</td>
            <td>{{$jumlah + $jumlah_ppn}}</td>
        </tr>
    @endforeach
    </tbody>
</table>