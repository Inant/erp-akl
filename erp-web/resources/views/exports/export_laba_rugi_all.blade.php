
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
            <th colspan="2"><h2>Laba Rugi All Tanggal {{formatDate($data['date1'])}} Sampai {{formatDate($data['date2'])}}</h2></th>
        </tr>
    </thead>
    @php
    $sum_pendapatan=$sum_hpp=0;
        foreach ($data['data'] as $key => $value) {
            $total_pendapatan=$total_hpp=0;
    @endphp
        
    @php
        }
    @endphp
    <tbody>
        <tr>
            <th>Pendapatan</th>
            <th></th>
        </tr>
    </tbody>
    <tbody>
        @php
        $sum_pendapatan=$sum_hpp=0;
        foreach ($data['pendapatan'] as $key => $value) {
            foreach ($value['data'] as $v){
                if ($v['detail'][0]->sifat_debit == 1) {
                    $total=$v['detail'][0]->jumlah_debit-$v['detail'][0]->jumlah_kredit;
                }else{
                    $total=$v['detail'][0]->jumlah_kredit-$v['detail'][0]->jumlah_debit;
                }
                $sum_pendapatan+=$total;
                if($total > 0){
        @endphp
        <tr>
            <td>{{$v['detail'][0]->nama_akun}}</td>
            <td>{{($total)}}</td>
        </tr>
        @php
                }
                $total_hpp=($v['hpp']->total_debit - $v['hpp']->total_kredit);
                $sum_hpp+=$total_hpp;
                if($total > 0){
        @endphp
        <tr>
            <td>HPP {{$v['detail'][0]->nama_akun}}</td>
            <td>{{($total_hpp)}}</td>
        </tr>
        @php
                }
            }
        }
        @endphp
        <tr>
            <th>Total Pendapatan</th>
            <th>{{($sum_pendapatan)}}</th>
        </tr>
    </tbody>
    <tbody>
        <tr>
            <th>Gross Profit All</th>
            <th>{{($sum_pendapatan - $sum_hpp)}}</th>
        </tr>
    </tbody>
    <tbody>
        <tr>
            <th>Biaya Operasional</th>
            <th></th>
        </tr>
    </tbody>
    <tbody>
        @php
        $sum_biaya_operasional=0;
        foreach ($data['biaya_operasional'] as $key => $value) {
            foreach ($value['data'] as $v){
        @endphp
        <tr>
            @php
            if ($v['detail'][0]->sifat_debit == 1) {
                $total=$v['detail'][0]->jumlah_debit-$v['detail'][0]->jumlah_kredit;
            }else{
                $total=$v['detail'][0]->jumlah_kredit-$v['detail'][0]->jumlah_debit;
            }
            $sum_biaya_operasional+=$total;
            @endphp
            <td>{{$v['detail'][0]->nama_akun}}</td>
            <td>{{($total)}}</td>
        </tr>
        @php
            }
        }
        @endphp
        <tr>
            <th>Total Biaya Operasional</th>
            <th>{{($sum_biaya_operasional)}}</th>
        </tr>
    </tbody>
    <tbody>
        <tr>
            <th></th>
            <th></th>
        </tr>
    </tbody>
    <tbody>
        <tr>
            <th>Biaya Administrasi dan Umum</th>
            <th></th>
        </tr>
    </tbody>
    <tbody>
        @php
        $sum_biaya_adm=0;
        foreach ($data['biaya_adm'] as $key => $value) {
            if($value['id_akun'] != 124){
            foreach ($value['data'] as $v){
        @endphp
        <tr>
            @php
            if ($v['detail'][0]->sifat_debit == 1) {
                $total=$v['detail'][0]->jumlah_debit-$v['detail'][0]->jumlah_kredit;
            }else{
                $total=$v['detail'][0]->jumlah_kredit-$v['detail'][0]->jumlah_debit;
            }
            $sum_biaya_adm+=$total;
            @endphp
            <td>{{$v['detail'][0]->nama_akun}}</td>
            <td>{{($total)}}</td>
        </tr>
        @php
            }
            }
        }
        @endphp
        <tr>
            <th>Total Biaya Administrasi dan Umum</th>
            <th>{{($sum_biaya_adm)}}</th>
        </tr>
    </tbody>
    <tbody>
        <tr>
            <th></th>
            <th></th>
        </tr>
    </tbody>
    <tbody>
        <tr>
            <th>Pendapatan Lain Lain</th>
            <th></th>
        </tr>
    </tbody>
    <tbody>
        @php
        $sum_biaya_lain=0;
        foreach ($data['biaya_lain'] as $key => $value) {
            foreach ($value['data'] as $v){
        @endphp
        <tr>
            @php
            if ($v['detail'][0]->sifat_debit == 1) {
                $total=$v['detail'][0]->jumlah_debit-$v['detail'][0]->jumlah_kredit;
            }else{
                $total=$v['detail'][0]->jumlah_kredit-$v['detail'][0]->jumlah_debit;
            }
            $sum_biaya_lain+=$total;
            @endphp
            <td>{{$v['detail'][0]->nama_akun}}</td>
            <td>{{($total)}}</td>
        </tr>
        @php
            }
        }
        @endphp
        <tr>
            <th>Total Pendapatan Lain Lain</th>
            <th>{{($sum_biaya_lain)}}</th>
        </tr>
    </tbody>
    <tbody>
        <tr>
            <th>Hutang Pajak</th>
            <th></th>
        </tr>
    </tbody>
    <tbody>
        @php
        $hutang_ppn=0;
        foreach ($data['biaya_adm'] as $key => $value) {
            if($value['id_akun'] == 124){
            foreach ($value['data'] as $v){
        @endphp
        <tr>
            @php
            if ($v['detail'][0]->sifat_debit == 1) {
                $total=$v['detail'][0]->jumlah_debit-$v['detail'][0]->jumlah_kredit;
            }else{
                $total=$v['detail'][0]->jumlah_kredit-$v['detail'][0]->jumlah_debit;
            }
            $hutang_ppn+=$total;
            @endphp
            <td>{{$v['detail'][0]->nama_akun}}</td>
            <td>{{($total)}}</td>
        </tr>
        @php
            }
            }
        }
        $ppn=($data['saldo_ppn'] != null ? $data['saldo_ppn']->jumlah_saldo : 0) + $hutang_ppn;
        @endphp
        <tr>
            <th>Total Hutang PPN</th>
            <th>{{($ppn)}}</th>
        </tr>
    </tbody>
    <tbody>
        <tr>
            <td></td>
            <td></td>
        </tr>
        <tr>
            <th>Nett Profit</th>
            <th>{{($sum_pendapatan - ($sum_hpp + $sum_biaya_operasional + $sum_biaya_adm + $sum_biaya_lain + $ppn))}}</th>
        </tr>
    </tbody>
</table>