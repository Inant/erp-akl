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
            <th colspan="6">Rincian Tagihan Hutang per Tanggal {{formatDate($data['date1'])}} - {{formatDate($data['date2'])}}</th>
        </tr>
        <tr>
            <th colspan="6"></th>
        </tr>
        <tr>
            <th></th>
            <th>Tanggal</th>
            <th>No Surat Jalan</th>
            <th>Supplier</th>
            <th>Keterangan</th>
            <th>Debit</th>
            <th>Kredit</th>
            <th>Saldo(Asing)</th>
        </tr>
    </thead>
    <tbody>
    @php $sub_total=$total_debit=$total_kredit=0 @endphp
        <tr>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td>{{($data['saldo_awal'])}}</td>
        </tr>
    @php 
    $sub_total+=$data['saldo_awal'] ;
    $n=0;
    @endphp
    @foreach($data['data'] as $value)
        @php 
        $sub_total=($value->tipe == 'DEBIT' ? ($sub_total - $value->jumlah) : ($sub_total + $value->jumlah)); 
        $total_debit+=($value->tipe == 'DEBIT' ? $value->jumlah : 0);
        $total_kredit+=($value->tipe == 'KREDIT' ? $value->jumlah : 0);
        $n++;
        @endphp
        <tr>
            <td>{{$n}}</td>
            <td>{{formatDate($value->tanggal)}}</td>
            <td>{{$value->no_surat_jalan != null ? $value->no_surat_jalan : '-'}}</td>
            <td>{{$value->name}}</td>
            <td>{{$value->deskripsi}}</td>
            <td>{{$value->tipe == 'DEBIT' ? ($value->jumlah) : '-'}}</td>
            <td>{{$value->tipe == 'KREDIT' ? ($value->jumlah) : '-'}}</td>
            <td>{{($sub_total)}}</td>
        </tr>
    @endforeach
        <tr>
            <td colspan="5">Sub Total</td>
            <td>{{($total_debit)}}</td>
            <td>{{($total_kredit)}}</td>
            <td>{{($sub_total)}}</td>
        </tr>
    </tbody>
</table>