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
            <th colspan="6">Laporan Penjualan per Tanggal {{formatDate($data['date1'])}} - {{formatDate($data['date2'])}}</th>
        </tr>
    </thead>
    <thead>
        <tr class="text-primary">
            <th></th>
            <th class="text-center">Tanggal</th>
            <th class="text-center">Sumber</th>
            <th class="text-center">No Invoice</th>
            <th class="text-center">No Faktur</th>
            <th class="text-center" width="300px">Keterangan</th>
            <th class="text-center">No SPK</th>
            <th class="text-center">DPP</th>
            <th class="text-center">PPN</th>
            <th class="text-center">PPH</th>
            <th class="text-center">Nilai</th>
        </tr>
    </thead>
    <tbody>
    @php $sub_total=$index=$dpp=$ppn=$pph=0 @endphp
    @foreach($data['data_cust'] as $value)
        @php 
        $sub_total+=$value->jumlah; 
        $index++; 
        $dpp+=($value->dpp != null ? $value->dpp->jumlah : 0);
        $ppn+=($value->ppn != null ? $value->ppn->jumlah : 0);
        $pph+=($value->pph != null ? $value->pph->jumlah : 0);
        @endphp
        <tr>
            <td>{{$index}}</td>
            <td>{{formatDate($value->tanggal)}}</td>
            <td>{{$value->coorporate_name}}</td>
            <td>{{$value->invoice_no}}</td>
            <td>{{$value->bill_no != null ? $value->bill_no : '-'}}</td>
            <td>{{$value->deskripsi}}</td>
            <td>{{$value->spk_number != null ? $value->spk_number : $value->spk_number_ins}}</td>
            {{-- <td>{{ $value->spk_number_ins != null ? $value->spk_number_ins : '-'}}</td> --}}
            <td>{{$value->dpp != null ? ($value->dpp->jumlah) : 0}}</td>
            <td>{{$value->ppn != null ? (($value->ppn->jumlah)) : 0}}</td>
            <td>{{$value->pph != null ? (($value->pph->jumlah)) : 0}}</td>
            <td>{{($value->jumlah)}}</td>
        </tr>
    @endforeach
        <tr class="table-info">
            <td colspan="7" class="text-center">Sub Total</td>
            <td>{{($dpp)}}</td>
            <td>{{($ppn)}}</td>
            <td>{{($pph)}}</td>
            <td>{{($sub_total)}}</td>
        </tr>
    </tbody>
</table>