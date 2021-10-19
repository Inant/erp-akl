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
            <th colspan="6">Laporan Hutang dan Pembayaran Supplier per Tanggal {{formatDate($data['date1'])}} - {{formatDate($data['date2'])}}</th>
        </tr>
        <tr>
            <th colspan="6"></th>
        </tr>
        <tr>
            <th>Tanggal</th>
            <th>No PO</th>
            <th>No Tagihan Supplier</th>
            <th>No Pembayaran</th>
            <th>Supplier</th>
            <th>Keterangan</th>
            <th>Nilai DPP</th>
            <th>PPN</th>
            <th>Debit</th>
            <th>Kredit</th>
            <th>Saldo (asing)</th>
        </tr>
    </thead>
    <tbody>
    @foreach($data['data'] as $value)
        <?php 
        $total_saldo=$total_penambahan=$total_penurunan=0;
        $saldo_awal=$value['perubahan_saldo']->total_kredit - $value['perubahan_saldo']->total_debit;
        ?>
        @if($value['data'] != null || $saldo_awal != 0)
        <tr>
            <td></td>
            <td>{{$value['supplier']->name}}</td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td>{{($value['perubahan_saldo']->total_kredit - $value['perubahan_saldo']->total_debit)}}</td>
        </tr>
        <?php 
        $total_saldo=$value['perubahan_saldo']->total_kredit - $value['perubahan_saldo']->total_debit; 
        ?>
        @foreach($value['data'] as $v)
            @foreach($v['dt'] as $v1)
            <?php 
            $total_saldo=($v1->tipe == 'KREDIT' ? ($total_saldo + $v1->jumlah) : ($total_saldo - $v1->jumlah));
            if ($v1->tipe == 'KREDIT') {
                $total_penambahan+=$v1->jumlah;
            }else{
                $total_penurunan+=$v1->jumlah;
            }
            ?>
            <tr>
                <td>{{date('d-m-Y', strtotime($v['date']))}}</td>
                <td>{{$v1->purchase_no != null ? $v1->purchase_no : ($v1->purchase_asset_no != null ? $v1->purchase_asset_no : '-')}}</td>
                <td>{{$v1->ps_no != null ? $v1->ps_no : '-'}}</td>
                <td>{{$v1->source != null ? $v1->source : '-'}}</td>
                <td>{{$value['supplier']->name}}</td>
                <td>{{$v1->p_notes != null ? $v1->p_notes : ($v1->pa_notes != null ? $v1->p_notes : ($v1->deskripsi != null ? $v1->deskripsi : '-'))}}</td>
                <td>{{($v1->jumlah - $v1->ppn)}}</td>
                <td>{{($v1->ppn)}}</td>
                <td>{{$v1->tipe == 'DEBIT' ? ($v1->jumlah) : 0}}</td>
                <td>{{$v1->tipe == 'KREDIT' ? ($v1->jumlah) : 0}}</td>
                <td>{{($total_saldo)}}</td>
            </tr>
            @endforeach
        @endforeach
        <tr>
            <td colspan="8">Total</td>
            <td>{{($total_penurunan)}}</td>
            <td>{{($total_penambahan)}}</td>
            <td>{{($total_saldo)}}</td>
        </tr>
        @endif
    @endforeach
    </tbody>
</table>