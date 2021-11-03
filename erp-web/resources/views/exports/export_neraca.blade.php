@php

    function formatRupiah($num){
        //return number_format($num, 0, '.', '.');
        return $num;
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
    // $total_modal=$total_amortisasi=0;
    // foreach ($data['parent'] as $k){
    //     if($k->no_akun != 1 && $k->no_akun != 2 && $k->no_akun != 3){
    //         foreach($k->detail as $key => $value){
    //             $total_saldo=0;
    //             foreach ($value['data'] as $v){
    //                 $saldo = $v['saldo'];
    //                 $total_saldo+=(($saldo != null ? $saldo->jumlah_saldo : 0) + $v['detail'][0]->jumlah_kredit) - $v['detail'][0]->jumlah_debit;      
    //             }
    //             $total_modal+=$total_saldo;
    //         }
    //     }
    // }
    // foreach ($data['parent'] as $k){
    //     if($k->no_akun == 1){
    //         foreach($k->detail as $key => $value){
    //             $total_saldo=0;
    //             foreach ($value['data'] as $v){
    //                 if($v['detail'][0]->id_akun == 49){
    //                     $saldo = $v['saldo'];
    //                     $total_saldo+=(($saldo != null ? $saldo->jumlah_saldo : 0) + $v['detail'][0]->jumlah_debit) - $v['detail'][0]->jumlah_kredit;      
    //                 }
    //             }
    //             $total_amortisasi+=$total_saldo;
    //         }
    //     }
    // }

    $total_modal=$total_amortisasi=0;
    foreach ($data['parent'] as $k){
        if($k->no_akun != 1 && $k->no_akun != 2 && $k->no_akun != 3){
            foreach($k->detail as $key => $value){
                $total_saldo=0;
                foreach ($value['data'] as $v){
                    $saldo = $v['saldo_month'];
                    $total_saldo+=(($saldo != null ? $saldo->total_kredit : 0) + $v['detail'][0]->jumlah_kredit) - (($saldo != null ? $saldo->total_debit : 0) + $v['detail'][0]->jumlah_debit);      
                }
                $total_modal+=$total_saldo;
            }
        }
    }
    foreach ($data['parent'] as $k){
        if($k->no_akun == 1){
            foreach($k->detail as $key => $value){
                $total_saldo=0;
                foreach ($value['data'] as $v){
                    if($v['detail'][0]->id_akun == 49){
                        $saldo = $v['saldo'];
                        $total_saldo+=(($saldo != null ? $saldo->jumlah_saldo : 0) + $v['detail'][0]->jumlah_debit) - $v['detail'][0]->jumlah_kredit;      
                    }
                }
                $total_amortisasi+=$total_saldo;
            }
        }
    }
@endphp
    <table>
            <tr>
                <th colspan="4">PT. Adhiusaha Kencana Lestari</th>
            </tr>
            <tr>
                <th colspan="4">Neraca</th>
            </tr>
            <tr>
                <th colspan="4">Tanggal {{formatDate($data['date1'])}} Sampai {{formatDate($data['date2'])}}</th>
            </tr>
            <tr>
                <th colspan="4"></th>
            </tr>
            @php 
            $jumlah_debit=$jumlah_kredit=$total_debit=$total_kredit=$initiate=0;
            @endphp
            @foreach ($data['parent'] as $k)
                <?php
                $temp=array();
                if($k->no_akun == 1){
                ?>
                @foreach($k->detail as $key => $value)
                <?php
                $jumlah_parent=0;
                ?>
                <tr>
                    <th colspan="2"><b>{{$value['nama']}}</b></th>
                    <th colspan="2"></th>
                </tr>
                    @foreach ($value['data'] as $v)
                    @if($v['detail'][0]->id_akun != 49)
                    @php $saldo = $v['saldo'] @endphp
                    <tr>
                        
                        <th colspan="2">{{$v['detail'][0]->nama_akun}}</th>
                        @php
                        if ($v['detail'][0]->sifat_debit == 1) {
                            $a= (($saldo != null ? $saldo->jumlah_saldo : 0) + $v['detail'][0]->jumlah_debit) - $v['detail'][0]->jumlah_kredit;
                            $jumlah_parent+=$a;
                            if($a < 0){
                                $total_kredit+=abs($a);
                            }else{
                                $total_debit+=$a;
                            }
                        @endphp
                        @if($key % 2 == 0)
                        <th ></th>
                        <th >{{formatRupiah($a)}}</th>
                        @else
                        <th>{{formatRupiah($a)}}</th>
                        <th></th>
                        @endif
                        @php
                        }else{
                            $b=(($saldo != null ? $saldo->jumlah_saldo : 0) + $v['detail'][0]->jumlah_kredit) - $v['detail'][0]->jumlah_debit;
                            $jumlah_parent+=($b + ($v['detail'][0]->id_akun == 168 ? $total_modal : 0));
                            if($b < 0){
                                $total_debit+=abs($b);
                            }else{
                                $total_kredit+=$b;
                            }
                        @endphp
                        @if($key % 2 == 0)
                        <th></th>
                        <th >{{formatRupiah($b)}}</th>
                        @else
                        <th >{{formatRupiah($b)}}</th>
                        <th ></th>
                        @endif
                        @php
                        }
                        @endphp
                    </tr>   
                    @endif
                    @endforeach
                    <?php
                    array_push($temp, $jumlah_parent);
                    ?>
                    <tr>
                        <th colspan="2"><b>Total {{$value['nama']}}</b></th>
                        @if($key % 2 == 0)
                        <th></th>
                        <th style="font-weight: bold;">{{formatRupiah(round($jumlah_parent, 1))}}</th>
                        @else
                        <th style="font-weight: bold;">{{formatRupiah(round($jumlah_parent, 1))}}</th>
                        <th></th>
                        @endif
                    </tr>
                    @if($value['nama'] == 'Aktiva Tetap')
                    <tr>
                        <th colspan="2"><b>Total Akumulasi Penyusutan</b></th>
                        @if($key % 2 == 0)
                        <th></th>
                        <th style="font-weight: bold;">{{formatRupiah(round($total_amortisasi, 1))}}</th>
                        @else
                        <th style="font-weight: bold;">{{formatRupiah(round($total_amortisasi, 1))}}</th>
                        <th></th>
                        @endif
                    </tr>
                    <!-- <tr>
                        <th colspan="2"><b>Nilai Buku Aktiva Tetap</b></th>
                        @if($key % 2 == 0)
                        <th></th>
                        <th style="font-weight: bold;">{{formatRupiah(round($jumlah_parent + $total_amortisasi, 1))}}</th>
                        @else
                        <th style="font-weight: bold;">{{formatRupiah(round($jumlah_parent + $total_amortisasi, 1))}}</th>
                        <th></th>
                        @endif
                    </tr> -->
                    @endif
                    <?php $initiate++; ?>
                @endforeach
                <tr>
                    <th colspan="2" style="color:white"><b>-</b></th>
                    <th colspan="2"><b></b></th>
                </tr>
                <?php
                $sub_total_parent=0;
                ?>
                @foreach($k->detail as $key => $value)
                <?php
                $sub_total_parent+=round($temp[$key], 2);
                $total=round($temp[$key], 2);
                if($value['nama'] == 'Aktiva Tetap'){
                    $sub_total_parent+=$total_amortisasi;
                    $total+=$total_amortisasi;
                }
                ?>
                @if($value['nama'] == 'Aktiva Tetap')
                <tr>
                    <th colspan="2"><b>Total {{$value['nama']}}</b></th>
                    <th></th>
                    <th style="font-weight: bold;">{{formatRupiah($total)}}</th>
                </tr>
                @endif
                @endforeach
                <!-- <tr>
                    <th colspan="2"><b>Total {{$k->nama_akun}}</b></th>
                    <th></th>
                    <th style="font-weight: bold;">{{formatRupiah(round($sub_total_parent))}}</th>
                </tr> -->
                <?php
                }
                ?>
            @endforeach
            <tr id="table">
                <th colspan="2">Total Aktiva</th>
                <th></th>
                <th style="font-weight: bold;">{{formatRupiah(($total_debit - $total_kredit) + $total_amortisasi)}}</th>
            </tr>
            <tr>
            <th colspan="4"></th>
            </tr>
            <!-- <tr>
            <th colspan="4"><b><i>KEWAJIBAN DAN MODAL</i></b></th>
            </tr> -->
            @php 
            $jumlah_debit=$jumlah_kredit=$total_debit=$total_kredit=$initiate=0;
            @endphp
            @foreach ($data['parent'] as $k)
                <?php
                $temp=array();
                if($k->no_akun == 2 || $k->no_akun == 3){
                ?>
                @foreach($k->detail as $key => $value)
                <?php
                $jumlah_parent=0;
                ?>
                <tr>
                    <th colspan="2"><b>{{$value['nama'] == 'Hutang Lancar' ? 'Kewajiban dan Modal' : $value['nama']}}</b></th>
                    <th colspan="2"></th>
                </tr>
                    @foreach ($value['data'] as $v)
                    @php $saldo = $v['saldo'] @endphp
                    <tr>
                        
                        <th colspan="2">{{$v['detail'][0]->nama_akun}}</th>
                        @php
                        if ($v['detail'][0]->sifat_debit == 1) {
                            $a= (($saldo != null ? $saldo->jumlah_saldo : 0) + $v['detail'][0]->jumlah_debit) - $v['detail'][0]->jumlah_kredit;
                            $jumlah_parent+=$a;
                            if($a < 0){
                                $total_kredit+=abs($a);
                            }else{
                                $total_debit+=$a;
                            }
                        @endphp    
                        <th></th>
                        <th>{{formatRupiah($a)}}</th>
                        @php

                        }else{
                            if($v['detail'][0]->no_akun == '3.4.0.0'){
                                $v['detail'][0]->jumlah_debit += $data['hppProduksi'];
                                echo "asdf";
                            }
                            $b=((($saldo != null ? $saldo->jumlah_saldo : 0) + $v['detail'][0]->jumlah_kredit) - $v['detail'][0]->jumlah_debit) + ($v['detail'][0]->id_akun == 168 ? $total_modal : 0);
                            $jumlah_parent+=$b;
                            if($b < 0){
                                $total_debit+=abs($b);
                            }else{
                                $total_kredit+=$b;
                            }
                        @endphp
                        <th></th>
                        <th>{{formatRupiah($b)}}</th>
                        @php
                        }
                        @endphp
                    </tr>   
                    @endforeach
                    <?php
                    array_push($temp, $jumlah_parent);
                    ?>
                    @if($k->no_akun == 2)
                    <tr>
                        <th colspan="2"><b>Total {{$value['nama']}}</b></th>
                        <th></th>
                        <th style="font-weight: bold;">{{formatRupiah(round($jumlah_parent, 1))}}</th>
                        <!-- <th colspan="2"><b>{{formatRupiah(round($jumlah_parent, 1))}}</b></th> -->
                    </tr>
                    @endif
                    <?php $initiate++; ?>
                @endforeach
                <tr>
                    <th colspan="2" style="color:white"><b>-</b></th>
                    <th colspan="2"><b></b></th>
                </tr>
                <?php
                $sub_total_parent=0;
                ?>
                @foreach($k->detail as $key => $value)
                <?php
                $sub_total_parent+=round($temp[$key], 2);
                ?>
                <!-- <tr>
                    <th colspan="2"><b>Total {{$value['nama']}}</b></th>
                    <th></th>
                    <th style="font-weight: bold;">{{formatRupiah(round($temp[$key], 2))}}</th>
                </tr> -->
                @endforeach
                @if($k->nama_akun != 'Hutang')
                <tr>
                    <th colspan="2"><b>Total {{$k->nama_akun}}</b></th>
                    <th></th>
                    <th style="font-weight: bold;">{{formatRupiah(round($sub_total_parent))}}</th>
                </tr>
                @endif
                <?php
                }
                ?>
            @endforeach
            <tr>
                <th colspan="2">Total Pasiva</th>
                <th></th>
                <th align="right" style="font-weight: bold;">{{formatRupiah($total_kredit - $total_debit)}}</th>
            </tr>
    </table>
<script src="https://code.jquery.com/jquery-1.10.2.js"></script>