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
?>
<body class="page" onload="window.print()">
    <!-- Title -->
    <table style="width:100%; position:relative">
        <tr>
            <td>
            <div style="position: absolute; bottom: 0px;" class="title-print">Permintaan Penawaran Harga</div>
            </td>
            <td class="text-right" style="color:#525659">
                <span style="font-size:18px"><b>PT. Adhiusaha Kencana Lestari </b></span><br>
                Jl. Margomulyo No. 44 <br>
                Pergudangan Suri Mulia Blok FF/2A<br>
                Email : ptprosperity@yahoo.com<br>
            </td>
        </tr>
    </table>
    <br>
    <table>
        <thead>
            <tr>
                <td style="min-width:150px; font-size:16px">Tgl. Permintaan : </td>
                <td style="min-width:150px; font-size:16px">No. Permintaan : </td>
            </tr>
            <tr>
                <td style="min-width:150px; font-size:16px">{{$purchase['delivery_date'] != null ? date("d-m-Y", strtotime($purchase['delivery_date'])): '-'}}</td>
                <td style="min-width:150px; font-size:16px">{{$purchase['no']}}</td>
            </tr>
        </thead>
    </table>
    <!-- Spacer -->
    <br />

    <!-- Content -->
    <table class="table bordered">
        <tr>
            <th class="text-center">No</th>
            <th class="text-center">Nama Barang</th>
            <th class="text-center">Warna</th>
            <th class="text-center">Panjang</th>
            <th class="text-center">Qty</th>
            <th class="text-center">Satuan</th>
            <th class="text-center" style="min-width:180px">Notes</th>
        </tr>
        @if(count($purchase_d) > 0)
        <?php
        $jumlah=0;
        ?>
            @for($i = 0; $i < count($purchase_d); $i++)
            <?php
            $no=$purchase_d[$i]['m_items']['no'];
            $explode=explode('.', $no);
            if ($purchase['with_ppn'] == 1) {
                $total_before_ppn=($purchase_d[$i]['price_before_discount'] / 1.1) * $purchase_d[$i]['amount'];
            }
            ?>
            <tr>
                <td class="text-center">{{ ($i + 1) }}</td>
                <td >{{ !empty($explode[0]) ? $explode[0] : '' }}</td>
                <td >{{ !empty($explode[1]) ? $explode[1] : '' }}</td>
                <td >{{ ($purchase_d[$i]['m_items']['category'] == 'SPARE PART' ? '-' : $purchase_d[$i]['m_items']['amount_unit_child']) }}</td>
                <td class="text-right">{{ (float)$purchase_d[$i]['amount'] }}</td>
                <td class="text-center">{{ $purchase_d[$i]['m_units']['name'] }}</td>
                <td></td>
            </tr>
            @endfor
            @endif
    </table>

    <br /><br />
    <table>
        <tr>
                <!-- <td style="border-bottom:1px solid white; border-right:1px solid white; border-left:2px solid white">Prepared By</td> -->
                <td style="border-bottom:1px solid white; border-left:1px solid white; border-right:2px solid white">Approved By</td>
                <td style="border-bottom:1px solid white; border-left:1px solid white; border-right:2px solid white">Approved By</td>
                <td colspan="4" style="border-bottom:1px solid white; border-left:1px solid white; border-right:2px solid white"></td>
                <td></td>
                <td class="text-center"></td>
                <td class="text-right"></td>
                <td></td>
            </tr>
        <tr>
            <!-- <td style="border-bottom:2px solid white; width:110px; border-right:1px solid white; border-left:2px solid white">@if($purchase['signature_supplier'] != null)<img src="{{ env('API_URL') . $purchase['signature_request'] }}" height="100" width="100" />@endif <br> -->
            <!-- Purchasing : {{$requests != null ? $requests->name : ''}} -->
            <!-- </td> -->
            <td style="border-bottom:2px solid white; width:110px; border-left:1px solid white; border-right:2px solid white">@if($purchase['signature_supplier'] != null)<img src="{{ env('API_URL') . $purchase['signature_supplier'] }}" height='100' width="100" />@endif
            <br>
            Accounting : {{$manager != null ? $manager->name : ''}}
            </td>
            <td style="border-bottom:2px solid white; width:110px; border-left:1px solid white; border-right:2px solid white">@if($purchase['signature_holding'] != null)<img src="{{ env('API_URL') . $purchase['signature_holding'] }}" height='100' width="100" />@endif
            <br>
            Direktur : {{$director != null ? $director->name : ''}}
            </td>
            
            <td colspan="2" style="border-bottom:2px solid white; border-left:2px solid white;"></td>
            <td colspan="2" style="border-bottom:2px solid white; border-left:2px solid white; border-right:2px solid white"></td>
        </tr>
    </table>
    <br><br><br>
    <table>
        <thead>
            <tr>
                <td>Keterangan :</td>
            </tr>
            <tr>
                <td style="width:320px">{{$purchase['notes']}}</td>
            </tr>
        </thead>
    </table>
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
    font-size: 24px;
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