@php
    function formatRupiah($num){
        return number_format($num, 0, '', '');
        //return $num;
    }
    function formatDate($date){
        $date=date_create($date);
        return date_format($date, 'd/m/Y');
    }
    function formatBulan($val){
        $bulan = ['Januari', 'Februari', 'Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
        $getMonth=(int)$val;
        return $bulan[$getMonth-1];
    }
    
@endphp
<center>
    <h3><b>PT Prosperity Archilum</b></h3>
    <h4><b>Neraca Saldo</b></h4>
    <h5><b>per Tanggal {{formatDate($data['date1'])}} Sampai {{formatDate($data['date2'])}}</b></h5>
</center>
<table border="1">
    <thead>
        <tr  style="background-color: #d7f2ed">
            <th>No Akun</th>
            <th>Nama Akun</th>
            <th>Saldo Awal Bulan Debit</th>
            <th>Saldo Awal Bulan Kredit</th>
            <th>Perubahan Saldo Debit</th>
            <th>Perubahan Saldo Kredit</th>
            <th colspan="2">Saldo Akhir Debit</th>
            <th colspan="2">Saldo Akhir Kredit</th>
        </tr>
    </thead>
    <tbody>
        @php 
        $except=[152, 153, 154, 43, 44, 45, 46, 47, 48];
        $saldo_awal_debit=$saldo_awal_kredit=$perubahan_debit=$perubahan_kredit=$perubahan_before=$saldo_awal_debit1=$saldo_awal_kredit1=$perubahan_debit1=$perubahan_kredit1=0;
        $total_saldo_awal_debit=$total_saldo_awal_kredit=$total_perubahan_debit=$total_perubahan_kredit=$total_all_debit=$total_all_kredit=0;
        @endphp
        @foreach($data['data'] as $key => $value)
            @php 
                $saldo_awal_debit1=($value->detail['perubahan_before']->total_debit + $value->detail['saldo_month']->total_debit);
                $saldo_awal_kredit1=($value->detail['perubahan_before']->total_kredit + $value->detail['saldo_month']->total_kredit);
                $saldo_awal_akun1=($value->sifat_debit == 1 ? ($saldo_awal_debit1 - $saldo_awal_kredit1) : ($saldo_awal_kredit1 - $saldo_awal_debit1));
                $perubahan_debit1=$saldo_awal_debit1 + $value->detail['detail_month']->total_debit;
                $perubahan_kredit1=$saldo_awal_kredit1 + $value->detail['detail_month']->total_kredit;
                $perubahan_akun1=($value->sifat_debit == 1 ? ($perubahan_debit1 - $perubahan_kredit1) : ($perubahan_kredit1 - $perubahan_debit1));


                $total_saldo_awal_debit+=($value->sifat_debit == 1 && $saldo_awal_akun1 > 0 ? $saldo_awal_akun1 : ($value->sifat_debit == 0 && $saldo_awal_akun1 < 0 ? abs($saldo_awal_akun1) : 0));
                $total_saldo_awal_kredit+=($value->sifat_debit == 0 && $saldo_awal_akun1 > 0 ? $saldo_awal_akun1 : ($value->sifat_debit == 1 && $saldo_awal_akun1 < 0 ? abs($saldo_awal_akun1) : 0));

                //$total_saldo_awal_debit+=$saldo_awal_debit1;
                //$total_saldo_awal_kredit+=$saldo_awal_kredit1;
                $total_perubahan_debit+=$value->detail['detail_month']->total_debit;
                $total_perubahan_kredit+=$value->detail['detail_month']->total_kredit;
                $total_all_debit+=($value->sifat_debit == 1 && $perubahan_akun1 > 0 ? $perubahan_akun1 : ($value->sifat_debit == 0 && $perubahan_akun1 < 0 ? abs($perubahan_akun1) : 0));;
                $total_all_kredit+=($value->sifat_debit == 0 && $perubahan_akun1 > 0 ? $perubahan_akun1 : ($value->sifat_debit == 1 && $perubahan_akun1 < 0 ? abs($perubahan_akun1) : 0));
            @endphp
        <tr style="background-color: #d7f2ed">
            <td><b>{{$value->no_akun}}</b></td>
            <td><b>
            {{$value->nama_akun}}
            </b></td>
            <td>@if($value->sifat_debit == 1 && $saldo_awal_akun1 > 0) {{formatRupiah($saldo_awal_akun1)}} @elseif($value->sifat_debit == 0 && $saldo_awal_akun1 < 0){{formatRupiah(abs($saldo_awal_akun1))}} @else 0 @endif</td>
            <td>@if($value->sifat_debit == 0 && $saldo_awal_akun1 > 0) {{formatRupiah($saldo_awal_akun1)}} @elseif($value->sifat_debit == 1 && $saldo_awal_akun1 < 0){{formatRupiah(abs($saldo_awal_akun1))}} @else 0 @endif</td>
            <td>{{formatRupiah($value->detail['detail_month']->total_debit)}}</td>
            <td>{{formatRupiah($value->detail['detail_month']->total_kredit)}}</a>
            </td>
            <td colspan="2">@if($value->sifat_debit == 1 && $perubahan_akun1 > 0) {{formatRupiah($perubahan_akun1)}} @elseif($value->sifat_debit == 0 && $perubahan_akun1 < 0){{formatRupiah(abs($perubahan_akun1))}} @else 0 @endif</td>
            <td colspan="2">@if($value->sifat_debit == 0 && $perubahan_akun1 > 0) {{formatRupiah($perubahan_akun1)}} @elseif($value->sifat_debit == 1 && $perubahan_akun1 < 0){{formatRupiah(abs($perubahan_akun1))}} @else 0 @endif</td>
        </tr>      
            @foreach($value->child as $v)
                @if($v->turunan1 != 22)
                
                @if(count($v->child) < 1 || in_array($v->id_akun, [152, 153, 169, 154, 43, 44, 45, 46, 47, 48]))
                @php 
                    $saldo_awal_debit=($v->detail['perubahan_before']->total_debit + $v->detail['saldo_month']->total_debit);
                    $saldo_awal_kredit=($v->detail['perubahan_before']->total_kredit + $v->detail['saldo_month']->total_kredit);
                    $saldo_awal_akun=($v->sifat_debit == 1 ? ($saldo_awal_debit - $saldo_awal_kredit) : ($saldo_awal_kredit - $saldo_awal_debit));
                    $perubahan_debit=$saldo_awal_debit + $v->detail['detail_month']->total_debit;
                    $perubahan_kredit=$saldo_awal_kredit + $v->detail['detail_month']->total_kredit;
                    $perubahan_akun=($v->sifat_debit == 1 ? ($perubahan_debit - $perubahan_kredit) : ($perubahan_kredit - $perubahan_debit));
                @endphp
            <tr>
                <td style="padding-left:10px"><b>{{$v->no_akun}}</b></td>
                <td><b>{{$v->nama_akun}}</b></td>
                <td>@if($v->sifat_debit == 1 && $saldo_awal_akun > 0) {{formatRupiah($saldo_awal_akun)}} @elseif($v->sifat_debit == 0 && $saldo_awal_akun < 0){{formatRupiah(abs($saldo_awal_akun))}} @else 0 @endif</td>
                <td>@if($v->sifat_debit == 0 && $saldo_awal_akun > 0) {{formatRupiah($saldo_awal_akun)}} @elseif($v->sifat_debit == 1 && $saldo_awal_akun < 0){{formatRupiah(abs($saldo_awal_akun))}} @else 0 @endif</td>
                <td>{{formatRupiah($v->detail['detail_month']->total_debit)}}</td>
                <td>{{formatRupiah($v->detail['detail_month']->total_kredit)}}</td>
                <td colspan="2">@if($v->sifat_debit == 1 && $perubahan_akun > 0) {{formatRupiah($perubahan_akun)}} @elseif($v->sifat_debit == 0 && $perubahan_akun < 0){{formatRupiah(abs($perubahan_akun))}} @else 0 @endif</td>
                <td colspan="2">@if($v->sifat_debit == 0 && $perubahan_akun > 0) {{formatRupiah($perubahan_akun)}} @elseif($v->sifat_debit == 1 && $perubahan_akun < 0){{formatRupiah(abs($perubahan_akun))}} @else 0 @endif</td>
            </tr> 
                @endif
                @foreach($v->child as $v1)
                    @if(!in_array($v1->turunan2, $except))
                    
                    @if(count($v1->child) < 1 || in_array($v1->id_akun, [50, 51, 52, 53, 54, 179]))
                    @php 
                        $saldo_awal_debit=($v1->detail['perubahan_before']->total_debit + $v1->detail['saldo_month']->total_debit);
                        $saldo_awal_kredit=($v1->detail['perubahan_before']->total_kredit + $v1->detail['saldo_month']->total_kredit);
                        $saldo_awal_akun=($v1->sifat_debit == 1 ? ($saldo_awal_debit - $saldo_awal_kredit) : ($saldo_awal_kredit - $saldo_awal_debit));
                        $perubahan_debit=$saldo_awal_debit + $v1->detail['detail_month']->total_debit;
                        $perubahan_kredit=$saldo_awal_kredit + $v1->detail['detail_month']->total_kredit;
                        $perubahan_akun=($v1->sifat_debit == 1 ? ($perubahan_debit - $perubahan_kredit) : ($perubahan_kredit - $perubahan_debit));
                    @endphp
                <tr >
                    <td style="padding-left:20px"><b>{{$v1->no_akun}}</b></td>
                    <td><b>{{$v1->nama_akun}}</b></td>
                    <td>@if($v1->sifat_debit == 1 && $saldo_awal_akun > 0) {{formatRupiah($saldo_awal_akun)}} @elseif($v1->sifat_debit == 0 && $saldo_awal_akun < 0){{formatRupiah(abs($saldo_awal_akun))}} @else 0 @endif</td>
                    <td>@if($v1->sifat_debit == 0 && $saldo_awal_akun > 0) {{formatRupiah($saldo_awal_akun)}} @elseif($v1->sifat_debit == 1 && $saldo_awal_akun < 0){{formatRupiah(abs($saldo_awal_akun))}} @else 0 @endif</td>
                    <td>{{formatRupiah($v1->detail['detail_month']->total_debit)}}</td>
                    <td>{{formatRupiah($v1->detail['detail_month']->total_kredit)}}</a>
                    </td>
                    <td colspan="2">@if($v1->sifat_debit == 1 && $perubahan_akun > 0) {{formatRupiah($perubahan_akun)}} @elseif($v1->sifat_debit == 0 && $perubahan_akun < 0){{formatRupiah(abs($perubahan_akun))}} @else 0 @endif</td>
                    <td colspan="2">@if($v1->sifat_debit == 0 && $perubahan_akun > 0) {{formatRupiah($perubahan_akun)}} @elseif($v1->sifat_debit == 1 && $perubahan_akun < 0){{formatRupiah(abs($perubahan_akun))}} @else 0 @endif</td>
                </tr>            
                    @endif                 
                    @foreach($v1->child as $v2)
                        @if($v2->turunan2 != 152 && $v2->turunan2 != 49)
                        @php 
                            $saldo_awal_debit=($v2->detail['perubahan_before']->total_debit + $v2->detail['saldo_month']->total_debit);
                            $saldo_awal_kredit=($v2->detail['perubahan_before']->total_kredit + $v2->detail['saldo_month']->total_kredit);
                            $saldo_awal_akun=($v2->sifat_debit == 1 ? ($saldo_awal_debit - $saldo_awal_kredit) : ($saldo_awal_kredit - $saldo_awal_debit));
                            $perubahan_debit=$saldo_awal_debit + $v2->detail['detail_month']->total_debit;
                            $perubahan_kredit=$saldo_awal_kredit + $v2->detail['detail_month']->total_kredit;
                            $perubahan_akun=($v2->sifat_debit == 1 ? ($perubahan_debit - $perubahan_kredit) : ($perubahan_kredit - $perubahan_debit));
                        @endphp
                        <tr >
                            <td style="padding-left:30px"><b>{{$v2->no_akun}}</b></td>
                            <td><b>{{$v2->nama_akun}}</b></td>
                            <td>@if($v2->sifat_debit == 1 && $saldo_awal_akun > 0) {{formatRupiah($saldo_awal_akun)}} @elseif($v2->sifat_debit == 0 && $saldo_awal_akun < 0){{formatRupiah(abs($saldo_awal_akun))}} @else 0 @endif</td>
                            <td>@if($v2->sifat_debit == 0 && $saldo_awal_akun > 0) {{formatRupiah($saldo_awal_akun)}} @elseif($v2->sifat_debit == 1 && $saldo_awal_akun < 0){{formatRupiah(abs($saldo_awal_akun))}} @else 0 @endif</td>
                            <td>{{formatRupiah($v2->detail['detail_month']->total_debit)}}
                            </td>
                            <td>{{formatRupiah($v2->detail['detail_month']->total_kredit)}}</td>
                            <td colspan="2">@if($v2->sifat_debit == 1 && $perubahan_akun > 0) {{formatRupiah($perubahan_akun)}} @elseif($v2->sifat_debit == 0 && $perubahan_akun < 0){{formatRupiah(abs($perubahan_akun))}} @else 0 @endif</td>
                            <td colspan="2">@if($v2->sifat_debit == 0 && $perubahan_akun > 0) {{formatRupiah($perubahan_akun)}} @elseif($v2->sifat_debit == 1 && $perubahan_akun < 0){{formatRupiah(abs($perubahan_akun))}} @else 0 @endif</td>
                        </tr>                       
                        @endif                 
                    @endforeach   
                    @endif         
                @endforeach
                @endif                                  
            @endforeach                                  
            
        @endforeach
        <tr style="background-color: #d7f2ed">
            <td colspan="2"><b>Total Saldo</b></td>
            <td>Rp. {{formatRupiah($total_saldo_awal_debit)}}</td>
            <td>Rp. {{formatRupiah($total_saldo_awal_kredit)}}</td>
            <td>Rp. {{formatRupiah($total_perubahan_debit)}}</td>
            <td>Rp. {{formatRupiah($total_perubahan_kredit)}}</td>
            <td colspan="2" >Rp. {{formatRupiah($total_all_debit)}}</td>
            <td colspan="2" >Rp. {{formatRupiah($total_all_kredit)}}</td>
        </tr>
    </tbody>
    <tbody>
        <tr style="background-color: #e3dffc">
            <th colspan="10" rowspan="2"><b>Ringkasan Saldo</b></th>
        </tr>
        <tr></tr>
        @php 
        $total_saldo_awal_debit=$total_saldo_awal_kredit=$saldo_awal_debit=$saldo_awal_kredit=$perubahan_debit=$perubahan_kredit=$perubahan_before=$saldo_awal_debit1=$saldo_awal_kredit1=$perubahan_debit1=$perubahan_kredit1=$total_perubahan_debit=$total_perubahan_kredit=$total_all_debit=$total_all_kredit=0;
        @endphp
        @foreach($data['data'] as $key => $value)
            @php 
                $saldo_awal_debit1=($value->detail['perubahan_before']->total_debit + $value->detail['saldo_month']->total_debit);
                $saldo_awal_kredit1=($value->detail['perubahan_before']->total_kredit + $value->detail['saldo_month']->total_kredit);
                $saldo_awal_akun1=($value->sifat_debit == 1 ? ($saldo_awal_debit1 - $saldo_awal_kredit1) : ($saldo_awal_kredit1 - $saldo_awal_debit1));
                $perubahan_debit1=$saldo_awal_debit1 + $value->detail['detail_month']->total_debit;
                $perubahan_kredit1=$saldo_awal_kredit1 + $value->detail['detail_month']->total_kredit;
                $perubahan_akun1=($value->sifat_debit == 1 ? ($perubahan_debit1 - $perubahan_kredit1) : ($perubahan_kredit1 - $perubahan_debit1));

                $total_saldo_awal_debit+=($value->sifat_debit == 1 && $saldo_awal_akun1 > 0 ? $saldo_awal_akun1 : ($value->sifat_debit == 0 && $saldo_awal_akun1 < 0 ? abs($saldo_awal_akun1) : 0));
                $total_saldo_awal_kredit+=($value->sifat_debit == 0 && $saldo_awal_akun1 > 0 ? $saldo_awal_akun1 : ($value->sifat_debit == 1 && $saldo_awal_akun1 < 0 ? abs($saldo_awal_akun1) : 0));

                //$total_saldo_awal_debit+=$saldo_awal_debit1;
                //$total_saldo_awal_kredit+=$saldo_awal_kredit1;
                $total_perubahan_debit+=$value->detail['detail_month']->total_debit;
                $total_perubahan_kredit+=$value->detail['detail_month']->total_kredit;

                $total_all_debit+=($value->sifat_debit == 1 && $perubahan_akun1 > 0 ? $perubahan_akun1 : ($value->sifat_debit == 0 && $perubahan_akun1 < 0 ? abs($perubahan_akun1) : 0));;
                $total_all_kredit+=($value->sifat_debit == 0 && $perubahan_akun1 > 0 ? $perubahan_akun1 : ($value->sifat_debit == 1 && $perubahan_akun1 < 0 ? abs($perubahan_akun1) : 0));
            @endphp
        <tr style="background-color: #d7f2ed">
            <td><b>{{$value->no_akun}}</b></td>
            <td><b>{{$value->nama_akun}}</b></td>
            <td>@if($value->sifat_debit == 1 && $saldo_awal_akun1 > 0) {{formatRupiah($saldo_awal_akun1)}} @elseif($value->sifat_debit == 0 && $saldo_awal_akun1 < 0){{formatRupiah(abs($saldo_awal_akun1))}} @else 0 @endif</td>
            <td>@if($value->sifat_debit == 0 && $saldo_awal_akun1 > 0) {{formatRupiah($saldo_awal_akun1)}} @elseif($value->sifat_debit == 1 && $saldo_awal_akun1 < 0){{formatRupiah(abs($saldo_awal_akun1))}} @else 0 @endif</td>
            <td>{{formatRupiah($value->detail['detail_month']->total_debit)}}</td>
            <td>{{formatRupiah($value->detail['detail_month']->total_kredit)}}</td>
            <td colspan="2">@if($value->sifat_debit == 1 && $perubahan_akun1 > 0) {{formatRupiah($perubahan_akun1)}} @elseif($value->sifat_debit == 0 && $perubahan_akun1 < 0){{formatRupiah(abs($perubahan_akun1))}} @else 0 @endif</td>
            <td colspan="2">@if($value->sifat_debit == 0 && $perubahan_akun1 > 0) {{formatRupiah($perubahan_akun1)}} @elseif($value->sifat_debit == 1 && $perubahan_akun1 < 0){{formatRupiah(abs($perubahan_akun1))}} @else 0 @endif</td>
        </tr>      
        @endforeach
        <tr style="background-color: #d7f2ed">
            <td colspan="2"><b>Total Saldo</b></td>
            <td>Rp. {{formatRupiah($total_saldo_awal_debit)}}</td>
            <td>Rp. {{formatRupiah($total_saldo_awal_kredit)}}</td>
            <td>Rp. {{formatRupiah($total_perubahan_debit)}}</td>
            <td>Rp. {{formatRupiah($total_perubahan_kredit)}}</td>
            <td colspan="2" >Rp. {{formatRupiah($total_all_debit)}}</td>
            <td colspan="2" >Rp. {{formatRupiah($total_all_kredit)}}</td>
        </tr>
    </tbody>
</table>
<script src="https://code.jquery.com/jquery-1.10.2.js"></script>

@php
header("Content-type: application/vnd-ms-excel");
header("Content-Disposition: attachment; filename=neraca_saldo.xls");
@endphp