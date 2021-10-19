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
            <th colspan="6">Rincian Buku Besar Pembantu Piutang per Tanggal {{formatDate($data['date1'])}} - {{formatDate($data['date2'])}}</th>
        </tr>
    </thead>
    <thead>
        <tr>
            <th colspan="6"></th>
        </tr>
        <tr>
            <th colspan="6">Customer</th>
        </tr>
        <tr>
            <th>Tanggal</th>
            <th>Sumber</th>
            <th>No Faktur</th>
            <th>Keterangan</th>
            <th>Debit</th>
            <th>Kredit</th>
            <th>Saldo (asing)</th>
        </tr>
    </thead>
    <tbody>
    @foreach($data['data_cust'] as $value)
        <?php $total_saldo=$total_penambahan=$total_penurunan=0 ?>
        @if($value['data'] != null)
        <tr>
            <td></td>
            <td>{{$value['customer']->coorporate_name}}</td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td>{{($value['perubahan_saldo']->total_debit - $value['perubahan_saldo']->total_kredit)}}</td>
        </tr>
        <?php 
        $total_saldo=$value['perubahan_saldo']->total_debit - $value['perubahan_saldo']->total_kredit; 
        // $total_penambahan+=$total_saldo;
        ?>
        @foreach($value['data'] as $v)
            @foreach($v['dt'] as $v1)
            <?php 
            $total_saldo=($v1->tipe == 'DEBIT' ? ($total_saldo + $v1->jumlah) : ($total_saldo - $v1->jumlah));
            if ($v1->tipe == 'DEBIT') {
                $total_penambahan+=$v1->jumlah;
            }else{
                $total_penurunan+=$v1->jumlah;
            }
            ?>
            <tr>
                <td>{{date('d-m-Y', strtotime($v['date']))}}</td>
                <td>{{$v1->no_source != null ? $v1->no_source : ''}}</td>
                <td>{{$v1->bill_no != null ? $v1->bill_no : ($v1->paid_cust_no != null ? $v1->paid_cust_no : '')}}</td>
                <td>{{$v1->deskripsi}}</td>
                <td>{{$v1->tipe == 'DEBIT' ? ($v1->jumlah) : '0'}}</td>
                <td>{{$v1->tipe == 'KREDIT' ? ($v1->jumlah) : '0'}}</td>
                <td>{{($total_saldo)}}</td>
            </tr>
            @endforeach
        @endforeach
        <tr>
            <td colspan="4">Sub Total</td>
            <td>{{($total_penambahan)}}</td>
            <td>{{($total_penurunan)}}</td>
            <td>{{($total_saldo)}}</td>
        </tr>
        @endif
    @endforeach
    </tbody>
</table>