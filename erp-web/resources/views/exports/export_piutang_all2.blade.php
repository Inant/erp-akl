@php
    function formatRupiah($num){
        return number_format($num, 0, '.', '.');
    }
    function formatDate($date){
        $date=date_create($date);
        return date_format($date, 'd/m/Y');
    }
@endphp
<table border="1">
    <thead>
        <tr>
            <th colspan="11">Laporan Piutang dan Pembayaran Customer per Tanggal {{formatDate($date1)}} - {{formatDate($date2)}}</th>
        </tr>
    </thead>
    <thead>
        <tr>
            <th></th>
            <th>Tanggal</th>
            <th>Customer</th>
            <th>No Faktur</th>
            <th>No Faktur /No Penerimaan</th>
            <th>Keterangan</th>
            <th>No Tagihan</th>
            <th>No Tagihan Dibayar</th>
            <th>Debit</th>
            <th>Kredit</th>
            <th>Saldo(Asing)</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($data_cust as $customer)
            @php
            $total_debit=$total_kredit=$n=0;
            $sub_total=round($customer['saldoAwal']->total_debit - $customer['saldoAwal']->total_kredit);
            @endphp
            <tr>
                <td></td>
                <td></td>
                <td>{{$customer['customer']->coorporate_name}}</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td>{{$sub_total}}</td>
            </tr>
            
            @foreach($customer['data'] as $key => $value)
                @php 
                $sub_total=($value->tipe == 'DEBIT' ? round($sub_total+$value->jumlah) : round($sub_total-$value->jumlah));
                $total_debit+=($value->tipe == 'DEBIT' ? $value->jumlah : 0);
                $total_kredit+=($value->tipe == 'KREDIT' ? $value->jumlah : 0);
                $n++;
                
                // cek apakah terdapat kurang / lebih bayar
                $getKurangLebihBayar = \DB::table('plusminusbill')->where('id_trx_akun', $value->id_trx_akun);
                $cekKurangLebihBayar = $getKurangLebihBayar->count();
                if ($cekKurangLebihBayar > 0 ) {
                    $nominalKurangLebih = $getKurangLebihBayar->select('nominal')->first()->nominal;
                }
                else{
                    $nominalKurangLebih = 0;
                }

                //jika lebih maka total kredit nya nambah
                if ($nominalKurangLebih > 0) {
                    $total_kredit += $nominalKurangLebih;
                }
                // jika kurang maka total debit nya nambah
                elseif($nominalKurangLebih < 0){
                    $total_debit += ($nominalKurangLebih * -1);
                }

                @endphp

                @if($value->source != null && strpos($value->deskripsi, ','))
                    @php
                        $billNo = explode(',', $value->deskripsi);
                        $billNo[0] = explode('No Tagihan', $billNo[0])[1];
                        $billNo = array_map('trim', $billNo);
                        
                        $detailBill = \DB::table('customer_bills')
                                            ->join('tbl_trx_akuntansi', 'tbl_trx_akuntansi.customer_bill_id', '=', 'customer_bills.id')
                                            ->join('tbl_trx_akuntansi_detail', 'tbl_trx_akuntansi_detail.id_trx_akun', '=', 'tbl_trx_akuntansi.id_trx_akun')
                                            ->select('customer_bills.id', 'customer_bills.no', 'tbl_trx_akuntansi_detail.jumlah')
                                            ->whereIn('customer_bills.no', $billNo)
                                            ->where('tbl_trx_akuntansi_detail.id_akun', 151)
                                            ->get()
                                            ->toArray();
                    @endphp
                @endif
                <!-- kondisi split sby bill -->
                @if(isset($detailBill) && count($detailBill) > 0 && $value->source != null && strpos($value->deskripsi, ','))
                    <tr>
                        <td rowspan="{{$cekKurangLebihBayar > 0 ? count($detailBill) + 1 : count($detailBill)}}"> {!!$n!!}</td>
                        <td rowspan="{{$cekKurangLebihBayar > 0 ? count($detailBill) + 1 : count($detailBill)}}">{!!formatDate($value->tanggal)!!}</td>
                        <td rowspan="{{$cekKurangLebihBayar > 0 ? count($detailBill) + 1 : count($detailBill)}}">{!!$value->coorporate_name!!}</td>
                        <td rowspan="{{$cekKurangLebihBayar > 0 ? count($detailBill) + 1 : count($detailBill)}}">{!!$value->customer_bill_no != null ? $value->customer_bill_no : ($value->order_no != null ? $value->order_no : ($value->install_order_no != null ? $value->install_order_no : '-'))!!}</td>
                        <td rowspan="{{$cekKurangLebihBayar > 0 ? count($detailBill) + 1 : count($detailBill)}}">
                        @if($value->source != null){{$value->source}}@else - @endif
                        </td>
                        <td>Pembayaran Customer dari No Tagihan {{$detailBill[0]->no}}</td>
                        <td>-</td>
                        <td>{{$detailBill[0]->no}}</td>
                        <td>-</td>
                        <td>{{round($detailBill[0]->jumlah)}}</td>
                        <td rowspan="{{$cekKurangLebihBayar > 0 ? count($detailBill) + 1 : count($detailBill)}}">{{$sub_total}}</td>
                        @php
                            $totalBill = 0;
                            $nomorBill = '';

                            $totalBill += round($detailBill[0]->jumlah);
                            $nomorBill .= $detailBill[0]->no . ', ';
                            array_shift($detailBill)
                        @endphp
                        @foreach ($detailBill as $item)
                        <tr>
                            <td>Pembayaran Customer dari No Tagihan {{$item->no}}</td>
                            <td>-</td>
                            <td>{{$item->no}}</td>
                            <td>-</td>
                            <td>{{round($item->jumlah)}}</td>
                        </tr>
                        @endforeach
                        @if ($cekKurangLebihBayar > 0 && $nominalKurangLebih > 0)
                        <tr>
                            <td>Lebih Bayar {{$value->source}}</td>
                            <td>{{$value->no}}</td>
                            @php
                                $noTagDibayar = '-';
                                if ($value->source != null && $value->tipe == 'KREDIT' && strpos($value->deskripsi, 'No Tagihan')) {
                                    $noTagDibayar = explode('No Tagihan', $value->deskripsi)[1];
                                }
                            @endphp
                        <td>{{$noTagDibayar}}</td>
                            <td>-</td>
                            <td>{{round($nominalKurangLebih)}}</td>
                        </tr>
                        @elseif($cekKurangLebihBayar > 0 && $nominalKurangLebih < 0)
                        <tr>
                            <td>Kurang Bayar {{$value->source}}</td>
                            <td>{{$value->no}}</td>
                            @php
                                $noTagDibayar = '-';
                                if ($value->source != null && $value->tipe == 'KREDIT' && strpos($value->deskripsi, 'No Tagihan')) {
                                    $noTagDibayar = explode('No Tagihan', $value->deskripsi)[1];
                                }
                            @endphp
                            <td>{{$noTagDibayar}}</td>
                            <td>{{round($nominalKurangLebih)}}</td>
                            <td>-</td>
                        </tr>
                        @endif
                    </tr>    
                @else
                    <!--jika terdapat kurang lebih bayar-->
                    @if ($cekKurangLebihBayar > 0)
                    <tr>
                        <td rowspan="2"> {!!$n!!}</td>
                        <td rowspan="2">{!!formatDate($value->tanggal)!!}</td>
                        <td rowspan="2">{!!$value->coorporate_name!!}</td>
                        <td rowspan="2">{!!$value->customer_bill_no != null ? $value->customer_bill_no : ($value->order_no != null ? $value->order_no : ($value->install_order_no != null ? $value->install_order_no : '-'))!!}</td>
                        <td rowspan="2">
                        @if($value->source != null){{$value->source}}@else - @endif
                        </td>
                        <td>{!!$value->deskripsi!!}</td>
                        <td>{{$value->no}}</td>
                        @php
                            $noTagDibayar = '-';
                            if ($value->source != null && $value->tipe == 'KREDIT' && strpos($value->deskripsi, 'No Tagihan')) {
                                $noTagDibayar = explode('No Tagihan', $value->deskripsi)[1];
                            }
                        @endphp
                        <td>{{$noTagDibayar}}</td>
                        <td>{{$value->tipe == 'DEBIT' ? round($value->jumlah) : '-'}}</td>
                        <td>{{$value->tipe == 'KREDIT' ? round($value->jumlah) : '-'}}</td>
                        <td rowspan="2">{{$sub_total}}</td>
                        <!-- jika terdapat kurang lebih bayar -->
                        @if ($cekKurangLebihBayar > 0 && $nominalKurangLebih > 0)
                        <tr>
                            <td>Lebih Bayar {{$value->source}}</td>
                            <td>{{$value->no}}</td>
                            @php
                                $noTagDibayar = '-';
                                if ($value->source != null && $value->tipe == 'KREDIT' && strpos($value->deskripsi, 'No Tagihan')) {
                                    $noTagDibayar = explode('No Tagihan', $value->deskripsi)[1];
                                }
                            @endphp
                        <td>{{$noTagDibayar}}</td>
                            <td>-</td>
                            <td>{{round($nominalKurangLebih)}}</td>
                        </tr>
                        @elseif($cekKurangLebihBayar > 0 && $nominalKurangLebih < 0)
                        <tr>
                            <td>Kurang Bayar {{$value->source}}</td>
                            <td>{{$value->no}}</td>
                            @php
                                $noTagDibayar = '-';
                                if ($value->source != null && $value->tipe == 'KREDIT' && strpos($value->deskripsi, 'No Tagihan')) {
                                    $noTagDibayar = explode('No Tagihan', $value->deskripsi)[1];
                                }
                            @endphp
                            <td>{{$noTagDibayar}}</td>
                            <td>{{round($nominalKurangLebih * -1)}}</td>
                            <td>-</td>
                        </tr>
                        @endif
                    </tr>
                    <!-- jika tidak terdapat kurang lebih bayar -->
                    @else
                    <tr>
                        <td> {!!$n!!}</td>
                        <td>{!!formatDate($value->tanggal)!!}</td>
                        <td>{!!$value->coorporate_name!!}</td>
                        <td>{!!$value->customer_bill_no != null ? $value->customer_bill_no : ($value->order_no != null ? $value->order_no : ($value->install_order_no != null ? $value->install_order_no : '-'))!!}</td>
                        <td>
                        @if($value->source != null){{$value->source}}@else - @endif
                        </td>
                        <td>{!!$value->deskripsi!!}</td>
                        <td>{{$value->no}}</td>
                        @php
                            $noTagDibayar = '-';
                            if ($value->source != null && $value->tipe == 'KREDIT' && strpos($value->deskripsi, 'No Tagihan')) {
                                $noTagDibayar = explode('No Tagihan', $value->deskripsi)[1];
                            }
                        @endphp
                        <td>{{$noTagDibayar}}</td>
                        <td>{{$value->tipe == 'DEBIT' ? round($value->jumlah) : '-'}}</td>
                        <td>{{$value->tipe == 'KREDIT' ? round($value->jumlah) : '-'}}</td>
                        <td>{{$sub_total}}</td>
                    </tr>
                    @endif
                @endif
            @endforeach
            <tr>
                <td colspan="8" style="text-align: center">Total</td>
                <td>{{round($total_debit)}}</td>
                <td>{{round($total_kredit)}}</td>
                <td>{{round($sub_total)}}</td>
            </tr>
        @endforeach
    </tbody>
</table>
<?php
header("Content-type: application/vnd-ms-excel");
header("Content-Disposition: attachment; filename=laporan_piutang_pembayaran_customer2.xls");
?>