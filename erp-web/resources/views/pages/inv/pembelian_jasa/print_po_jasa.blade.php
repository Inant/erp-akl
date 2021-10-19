<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Print Purchase Order</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- <link rel="stylesheet" type="text/css" media="screen" href="main.css"> -->
    <!-- <script src="main.js"></script> -->
</head>
<?php
function penyebut($nilai) {
    $nilai = abs($nilai);
    $huruf = array("", "Satu", "Dua", "Tiga", "Empat", "Lima", "Enam", "Tujuh", "Delapan", "Sembilan", "Sepuluh", "Sebelas");
    $temp = "";
    if ($nilai < 12) {
      $temp = " ". $huruf[$nilai];
    } else if ($nilai <20) {
      $temp = penyebut($nilai - 10). " belas";
    } else if ($nilai < 100) {
      $temp = penyebut($nilai/10)." puluh". penyebut($nilai % 10);
    } else if ($nilai < 200) {
      $temp = " seratus" . penyebut($nilai - 100);
    } else if ($nilai < 1000) {
      $temp = penyebut($nilai/100) . " ratus" . penyebut($nilai % 100);
    } else if ($nilai < 2000) {
      $temp = " seribu" . penyebut($nilai - 1000);
    } else if ($nilai < 1000000) {
      $temp = penyebut($nilai/1000) . " ribu" . penyebut($nilai % 1000);
    } else if ($nilai < 1000000000) {
      $temp = penyebut($nilai/1000000) . " juta" . penyebut($nilai % 1000000);
    } else if ($nilai < 1000000000000) {
      $temp = penyebut($nilai/1000000000) . " milyar" . penyebut(fmod($nilai,1000000000));
    } else if ($nilai < 1000000000000000) {
      $temp = penyebut($nilai/1000000000000) . " trilyun" . penyebut(fmod($nilai,1000000000000));
    }     
    return $temp;
  }
 
  function terbilang($nilai) {
    if($nilai<0) {
      $hasil = "minus ". trim(penyebut($nilai));
    } else {
      $hasil = trim(penyebut($nilai));
    }         
    return $hasil;
  }
  function formatDate($date){
      if ($date != null) {
          return date('d-m-Y', strtotime($date));
      }else{
          return '';
      }
  }
?>
<body class="page" onload="window.print()">
    <!-- Title -->
    <table style="width:100%; position:relative">
        <tr>
            <td>
            <div style="position: absolute; bottom: 0px;" class="title-print">PURCHASE ORDER</div>
            </td>
            <td class="text-right" style="color:#525659">
                <span style="font-size:18px"><b>PT. PROSPERITY ARCHILUM </b></span><br>
                Jl. Margomulyo No. 44 <br>
                Pergudangan Suri Mulia Blok FF/2A<br>
                Email : ptprosperity@yahoo.com<br>
            </td>
        </tr>
    </table>
    <br>
    <!-- Header -->
    <table class="table" style="border-collapse:collapse">
        <tr>
            <td style="width:100px">Tanggal:</td>
            <td style="width:140px">Syarat Pembayaran :</td>
            <td style="width:140px">Request Delivery :</td>
            <td style="width:80px">Penjual</td>
            <td>:</td>
            <td>
            <b>{{ $purchase['m_suppliers']['name'] }}</b>
            </td>
        </tr>
        <tr>
            <td style="width:100px; position:relative">
            <div style="position: absolute; top: 0px;">{{date('Y-m-d')}}</div>
            </td>
            <td style="width:100px; position:relative">
            <div style="position: absolute; top: 0px;">
            {{($purchase['wop'] != null ? $purchase['wop'] : '')}}
            </div>
            </td>
            <td style="width:100px; position:relative">
            <div style="position: absolute; top: 0px;">
            {{($purchase['delivery_date'] != null ? date("d-m-Y", strtotime($purchase['delivery_date'])) : '')}}
            </div>
            </td>
            <td></td>
            <td></td>
            <td>
            {{ $purchase['m_suppliers']['address'] }} - {{ $purchase['m_suppliers']['city'] }}
            <br>
            CP : {{ $purchase['m_suppliers']['person_phone'] }} ({{ $purchase['m_suppliers']['person_name'] }})
            </td>
        </tr>
        <tr>
            <td>Nomor :</td>
            <td colspan="2">Nomor Rekening : </td>
            <td>Kirim Ke</td>
            <td>:</td>
            <td>Jl. Margomulyo No. 44</td>
        </tr>
        <tr>
            <td style="width:100px; position:relative">
            <div style="position: absolute; top: 0px;">{{$purchase['no']}}</div>
            </td>
            <td style="width:100px; position:relative">
            <div style="position: absolute; top: 0px;">{{ $purchase['m_suppliers']['rekening_number'] }}</div>
            </td>
            <td></td>
            <td></td>
            <td></td>
            <td>
            Pergudangan Suri Mulia Blok FF/2A<br>
            Email : ptprosperity@yahoo.com<br>
            </td>
        </tr>
        <!-- <tr>
            <td width="50%">Nomer : {{ $purchase['no'] }}</td>
            <td width="50%">Vendor: {{ $purchase['m_suppliers']['name'] }}</td>
        </tr>
        <tr>
            <td width="50%">Tanggal : {{ date('d-m-Y') }}</td>
            <td width="50%"></td>
        </tr> -->
    </table>

    <!-- Spacer -->
    <br />

    <!-- Content -->
    <table class="table bordered">
        <tr>
            <th rowspan="2" class="text-center" style="max-width:20px">No</th>
            <th rowspan="2" colspan="5" style="min-width:200px" class="text-center">Deskripsi Jasa</th>
            <th rowspan="2" class="text-center">Qty</th>
            <th rowspan="2" class="text-center">Satuan</th>
            <th colspan="2" class="text-center">Harga</th>
            <th rowspan="2" class="text-center">Pjk</th>
        </tr>
        <tr>
            <th class="text-center" style="width:120px">Satuan @</th>
            <th class="text-center" style="width:120px">Total</th>
        </tr>
        @if(count($purchase_d) > 0)
        <?php
        $jumlah=0;
        ?>
            @for($i = 0; $i < count($purchase_d); $i++)
            <?php
            if ($purchase['with_ppn'] == 1) {
                $total_before_ppn=($purchase_d[$i]['price_before_discount'] / 1.1) * $purchase_d[$i]['amount'];
            }
            ?>
            <tr>
                <td class="text-center" style="max-width:10px">{{ ($i + 1) }}</td>
                <td colspan="5">{{ $purchase_d[$i]['service_name'] }}</td>
                <td class="text-right">{{ (float)$purchase_d[$i]['amount'] }}</td>
                <td class="text-center">{{ $purchase_d[$i]['m_units']['name'] }}</td>
                <td class="text-right">{{ number_format($purchase_d[$i]['price_before_discount']) }} {{$purchase['with_ppn'] == 1 ? '(Include PPN)' : ''}}</td>
                <td class="text-right">{{ number_format($total=$purchase_d[$i]['price_before_discount'] * $purchase_d[$i]['amount']) }}</td>
                <td>{{$purchase['with_ppn'] == 1 ? 'PPN' : ''}}</td>
            </tr>
            <?php 
            $jumlah+=($purchase['with_ppn'] == 1 ? $total : $total);
            ?>
            @endfor
            <?php
            $ppn=$purchase['base_price'] * (1/10);
            if ($purchase['with_ppn'] == 1) {
                $ppn=$purchase['base_price'] - ($purchase['base_price'] / 1.1);
            }
            if ($purchase['is_without_ppn'] == 1) {
                $ppn=0;
            }
            ?>
            <tr>
                <td colspan="8" style="padding-top:15px; padding-left:15px; border-bottom:1px solid white;"><b>Terbilang :</b> {{terbilang(($jumlah + $purchase['delivery_fee'] + ($purchase['with_ppn'] == 1 ? 0 : $ppn)) - ($purchase['discount'] != 0 ? $jumlah - $purchase['base_price'] : 0))}} rupiah</td>
                <td class="text-center"><b>Sub Total</b></td>
                <td class="text-right">Rp. {{ number_format($jumlah) }}</td>
                <td></td>
            </tr>
            @if($purchase['discount'] != 0)
            <tr>
                <td colspan="8" style="border-bottom:1px solid white;"></td>
                <td class="text-center"><b>Diskon {{$purchase['discount_type'] == 'percentage' ? $purchase['discount'].'%' : 'Rp. '.number_format($purchase['discount'], 0, '.', '.')}}</b></td>
                <td class="text-right">Rp. {{number_format(( $diskon = $jumlah - $purchase['base_price']), 0, '.', '.') }}</td>
                <td></td>
            </tr>
            <?php $jumlah -= $diskon ?>
            @endif
            
            <tr>
                <td colspan="8"></td>
                <td class="text-center"><b>Total</b></td>
                <td class="text-right">Rp. {{ number_format($jumlah, 0, '.', '.') }}</td>
                <td></td>
            </tr>
            <tr>
                <td colspan="8" rowspan="5" style="padding-top:15px; padding-left:15px; vertical-align: top; border-bottom:2px solid black;">Keterangan : {{$purchase['notes']}}</td>
                <td class="text-center"><b>PPN</b></td>
                <td class="text-right">Rp. {{ number_format($ppn) }}</td>
                <td>{{($purchase['with_ppn'] == 1 ? 'Include Total' : '')}}</td>
            </tr>
            <tr>
                <!-- <td colspan="8" style="border-bottom:1px solid white"></td> -->
                <td class="text-center"><b>Tot Sub Stlh Pjk</b></td>
                <td class="text-right">Rp. {{ number_format(($jumlah + ($purchase['with_ppn'] == 1 ? 0 : $ppn)), 0, '.', '.') }}</td>
                <td></td>
            </tr>
            <tr>
                <!-- <td colspan="8" style="border-bottom:1px solid white"></td> -->
                <td class="text-center"><b>Ongkos Kirim</b></td>
                <td class="text-right">Rp. {{ number_format($purchase['delivery_fee'], 0, '.', '.') }}</td>
                <td></td>
            </tr>
            <tr>
                <!-- <td colspan="8" style="border-bottom:1px solid white"></td> -->
                <!-- <td style="border-bottom:1px solid white; border-left:1px solid white; border-right:2px solid black"></td> -->
                <td class="text-center"><b>Tot Sub B.Kirim</b></td>
                <td class="text-right">Rp. {{ number_format(($jumlah + $purchase['delivery_fee'] + ($purchase['with_ppn'] == 1 ? 0 : $ppn)), 0, '.', '.') }}</td>
                <td></td>
            </tr>
            <tr>
                <!-- <td colspan="8" style="border-bottom:2px solid black"></td> -->
                <td class="text-center"><b>Grand Total</b></td>
                <td class="text-right">Rp. {{ number_format(($jumlah + $purchase['delivery_fee'] + ($purchase['with_ppn'] == 1 ? 0 : $ppn)), 0, '.', '.') }}</td>
                <td style="border-bottom:2px solid black"></td>
            </tr>
            <tr>
                <td colspan="2" style="border-bottom:2px solid white; border-right:1px solid white; border-left:2px solid white">
                </td>
                <td colspan="2" style="border-bottom:2px solid white; border-left:1px solid white; border-right:2px solid white">
                </td>
                <td colspan="2" style="border-bottom:2px solid white; border-left:1px solid white; border-right:2px solid white">
                </td>
                <td style="border-bottom:2px solid white; border-left:2px solid white; border-top:2px solid black"></td>
                <td style="border-bottom:2px solid white; border-left:2px solid white; border-top:2px solid black"></td>
                <td colspan="2" style="border-bottom:2px solid white; border-left:2px solid white; border-top:2px solid black; border-right:2px solid white"></td>
            </tr>
            
            <!-- <tr>
                <td colspan="3" style="border-bottom:2px solid white; width:110px; border-right:1px solid white; border-left:2px solid white">@if($purchase['signature_supplier'] != null)<img src="{{ env('API_URL') . $purchase['signature_request'] }}" height="100" width="100" />@endif <br>
                Purchasing : {{$requests != null ? $requests->name : ''}}
                </td>
                <td colspan="2" style="border-bottom:2px solid white; border-left:1px solid white; border-right:2px solid white">@if($purchase['signature_supplier'] != null)<img src="{{ env('API_URL') . $purchase['signature_supplier'] }}" height='100' width="100" />@endif
                <br>
                Accounting : {{$manager != null ? $manager->name : ''}}
                </td>
                <td colspan="2" style="border-bottom:2px solid white; border-left:1px solid white; border-right:2px solid white">@if($purchase['signature_holding'] != null)<img src="{{ env('API_URL') . $purchase['signature_holding'] }}" height='100' width="100" />@endif
                <br>
                Direktur : {{$director != null ? $director->name : ''}}
                </td>
                
                <td colspan="2" style="border-bottom:2px solid white; border-left:2px solid white; border-top:2px solid black"></td>
                <td colspan="2" style="border-bottom:2px solid white; border-left:2px solid white; border-top:2px solid black; border-right:2px solid white"></td>
            </tr> -->
        @endif
    </table>
    <table>
        <tr>
            <td colspan="2" style="min-width:120px;" align="center">
            Prepared By,<br>{{formatDate($purchase['purchase_date'])}}</td>
            <td colspan="2" style="min-width:130px" align="center">
            Accepted By,<br>{{formatDate($purchase['acc_manager_date'])}}</td>
            <td colspan="2" style="min-width:130px" align="center">
            Accepted By,<br>{{formatDate($purchase['acc_director_date'])}}</td>
            <td ></td>
            <td ></td>
            <td colspan="2" ></td>
        </tr>
        <tr>
            <td colspan="2">
            </td>
            <td colspan="2">
            </td>
            <td colspan="2">
            </td>
            <td colspan="4"></td>
        </tr>
        
        <tr>
            <td colspan="2"  align="center" style="max-width:110px">@if($purchase['signature_supplier'] != null)<img src="{{ env('API_URL') . $purchase['signature_request'] }}" height="100" width="100" />@endif <br>
            {{$requests != null ? '('.$requests->name.')' : ''}} <br>Purchasing
            </td>
            <td colspan="2"  align="center">@if($purchase['signature_supplier'] != null)<img src="{{ env('API_URL') . $purchase['signature_supplier'] }}" height='100' width="100" />@endif
            <br>
            {{$manager != null ? '( Totok Sugiarto )' : ''}} <br>Accounting
            </td>
            <td colspan="2" align="center">@if($purchase['signature_holding'] != null)<img src="{{ env('API_URL') . $purchase['signature_holding'] }}" height='100' width="100" />@endif
            <br>
             {{$director != null ? '( Roy Sudarso )' : ''}} <br>Direktur
            </td>
            
            <td colspan="2"></td>
            <td colspan="2"></td>
        </tr>
    </table>
    <br /><br />

    <!-- Document Signer -->
</body>
</html>

<style>
body {
    font-size: 14px;
    font-family: "ALIUIV+Calibri";
    color: #000000;
}

.title-print {
    font-size: 20px;
    line-height: 1.117188em;
    font-family: "Arial","sans-serif";
    font-weight: bold;
}

.page {
    padding: 20px;
    margin: 0;
}

.table {
    width: 100%;
}

.table.bordered {
    border-collapse: collapse;
    border: 2px solid black;
}

.bordered th, .bordered td {
    border: 1px solid black;
    padding: 5px;
}

.text-center {
    text-align: center;
}

.text-right {
    text-align: right;
}

.upper-line {
    -webkit-text-decoration-line: overline; /* Safari */
   text-decoration-line: overline; 
}
</style>