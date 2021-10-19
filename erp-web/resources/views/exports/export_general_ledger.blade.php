@php
function formatDate($date){
    $tgl=date('d-m-Y', strtotime($date));
    return $tgl;
}
function formatRupiah($val){
    $val=round($val, 0);
    $a=number_format($val, 0, '.', '.');
    return $a;
}
@endphp
<table>
@foreach($data as $data)
@php $total_debit=$total_kredit=0; @endphp
    <thead>
        <tr>
            <th height="20px"></th>
        </tr>
        <tr>
            <th colspan="10"><h5>Detail General Ledger Akun {{$data['akun']->nama_akun}} per Tanggal {{formatDate($data['date1'])}} Sampai {{formatDate($data['date2'])}}</h5></th>
        </tr>
        <tr>
            <th width="100px">Tanggal</th>
            <th>No Akun</th>
            <th width="150px">Nama Akun</th>
            <th width="100px">No Sumber</th>
            <th width="200px">Deskripsi</th>
            <th>Debit</th>
            <th>Kredit</th>
            <th width="100px">Customer</th>
            <th width="100px">Supplier</th>
            <th>Total Saldo</th> 
        </tr>
    </thead>
    <?php $sub_total=($data['saldo_awal']->jumlah_saldo != null ? $data['saldo_awal']->jumlah_saldo : 0) + $data['saldo_before_start_date']?>
    <tbody>
        <tr>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td>{{($sub_total)}}</td>
        </tr>
    </tbody>
    @foreach($data['data'] as $value)
    <tbody>
        @if(count($value['dt']) == 0)
        <tr>
            <td>{{formatDate($value['date'])}}</td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td>0</td>
            <td>0</td>
            <td></td>
            <td></td>
            <td>{{($sub_total)}}</td>
        </tr>
        @else
            @foreach($value['dt'] as $k => $v)
            <?php
            $total=$v->jumlah;
            if ($v->tipe == 'DEBIT') {
                if ($data['akun']->sifat_debit == 1) {
                    $sub_total+=$total;
                }else{
                    $sub_total-=$total;
                }
                $total_debit+=$total;
            }else{
                if ($data['akun']->sifat_kredit == 1) {
                    $sub_total+=$total;
                }else{
                    $sub_total-=$total;
                }
                $total_kredit+=$total;
            }
            ?>
            <tr>
                <td>{{ $k == 0 ? formatDate($value['date']) : ''}}</td>
                <td>{{$v->no_akun}}</td>
                <td>{{$v->nama_akun}}</td>
                <td>
                @if($v->note_no != null)
                    {{$v->note_no}}
                @else
                {{$v->purchases != null ? $v->purchases->no : ($v->purchase_assets != null ? $v->purchase_assets->no : ($v->orders != null ? $v->orders->order_no : ($v->ts_warehouses != null ? $v->ts_warehouses->no : ($v->debts != null ? $v->debts->no : ($v->install_orders != null ? $v->install_orders->no : ($v->giros != null ? $v->giros->no : ''))))))}}
                
                @endif
                </td>
                <td class="text-left">{{$v->deskripsi}}</td>
                <td>@if($v->tipe == 'DEBIT') {{($v->jumlah)}} @else 0 @endif</td>
                <td>@if($v->tipe == 'KREDIT') {{($v->jumlah)}} @else 0 @endif</td>
                <td>{{$v->customer}}</td>
                <td>{{$v->supplier}}</td>
                <td>{{($sub_total)}}</td>
            </tr>
            @endforeach
        @endif
    </tbody>
    @endforeach
    <tbody>
        <tr>
            <th colspan="5">Total</th>
            <th>{{($total_debit)}}</th>
            <th>{{($total_kredit)}}</th>
            <th colspan="2"></th>
            <th>{{($sub_total)}}</th>
        </tr>
    </tbody>
@endforeach
</table>