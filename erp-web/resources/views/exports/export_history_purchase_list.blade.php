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
            <th colspan="8">Rincian Pembelian per Pemasok per Tanggal {{formatDate($data['date1'])}} - {{formatDate($data['date2'])}}</th>
        </tr>
        <tr>
            <th colspan="8"></th>
        </tr>
        <tr>
            <th>Tanggal</th>
            <th>No PO</th>
            <th class="text-center">No Invoice</th>
            <th>No Surat Jalan</th>
            <th>Keterangan</th>
            <th>Nilai DPP</th>
            <th>Nama Pemasok</th>
            <th>Nomor Rekening</th>
        </tr>
    </thead>
    <tbody>
    <?php $total_saldo_all=0 ?>
    @foreach($data['data'] as $value)
        <?php $total_saldo=$total_penambahan=$total_penurunan=0 ?>
        @if($value['data'] != null)
        <tr>
            <td colspan="5">{{$value['supplier']->name}}</td>
            <td></td>
            <td></td>
            <td></td>
        </tr>
        @foreach($value['data'] as $v)
            @foreach($v['dt'] as $v1)
            <?php 
            $total_saldo+=$v1->total;
            $total_saldo_all+=$v1->total;
            ?>
            <tr>
                <td>{{date('d-m-Y', strtotime($v['date']))}}</td>
                <td>{{$v1->purchase_no != null ? $v1->purchase_no : $v1->purchase_asset_no}}</td>
                <td>{{$v1->paid_no}}</td>
                <td>{{$v1->no_surat_jalan}}</td>
                <td>{{$v1->purchase_notes != null ? $v1->purchase_notes : $v1->purchase_asset_notes}}</td>
                <td>{{($v1->total)}}</td>
                <td>{{$value['supplier']->name}}</td>
                <td>{{$value['supplier']->rekening_number}}</td>
            </tr>
            @endforeach
        @endforeach
        <tr>
            <td>Total Penambahan</td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td>{{($total_saldo)}}</td>
            <td></td>
            <td></td>
        </tr>
        @endif
    @endforeach
        <tr>
            <td colspan="8"></td>
        </tr>
        <tr>
            <td>Total Pembelian</td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td>{{($total_saldo_all)}}</td>
            <td></td>
            <td></td>
        </tr>
    </tbody>
</table>