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
            <th>No</th>
            <th>Kode Barang</th>
            <th>Nama Barang</th>
            <th>Jumlah</th>
            <th>Harga</th>
            <th>No PO</th>
            <th>No PO ATK</th>
            <th>No Surat Jalan</th>
            <th>Total</th>
            <th>Tanggal Penerimaan</th>
        </tr>
    </thead>
    <tbody>
    @php $total_all=$n=0 @endphp
    @foreach($data['data'] as $value)
        @php 
        $total=($value->amount * $value->base_price); 
        $total_all+=$total;
        $n++;
        @endphp
        <tr>
            <td>{{$n}}</td>
            <td>{{$value->code_item}}</td>
            <td>{{$value->name}}</td>
            <td>{{($value->amount)}}</td>
            <td>{{($value->base_price)}}</td>
            <td>{{$value->purchase_no != null ? $value->purchase_no : '-'}}</td>
            <td>{{$value->purchase_asset_no != null ? $value->purchase_asset_no : '-'}}</td>
            <td>{{$value->no_surat_jalan != null ? $value->no_surat_jalan : '-'}}</td>
            <td>{{($total)}}</td>
            <td>{{formatDate($value->inv_trx_date)}}</td>
            
        </tr>
    @endforeach
        <tr class="table-info">
            <td colspan="8">Sub Total</td>
            <td>{{($total_all)}}</td>
            <td></td>
        </tr>
    </tbody>
</table>