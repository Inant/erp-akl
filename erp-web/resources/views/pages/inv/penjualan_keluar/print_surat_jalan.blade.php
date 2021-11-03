<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Print Surat Jalan</title>
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
    <table width="100%">
        <tr>
            <td>
                PT. Adhiusaha Kencana Lestari <br>
                Jl. Margomulyo No. 44 <br>
                Pergudangan Suri Mulia Blok FF/2A<br>
                Email : ptprosperity@yahoo.com<br>
            </td>
            <td>
                <h2 style="float:right;">Surat Jalan</h2>        
            </td>
        </tr>
    </table>
    <hr><hr>
    <table width="100%">
        <tr>
            <td>Nama</td>
            <td>:</td>
            <td width="300px">
                <?=$data->coorporate_name?>
            </td>
            <td>Invoice no.</td>
            <td>:</td>
            <td>
                <?=$data->inv_no?><br>
            </td>
        </tr>
        <tr>
            <td>No Telp</td>
            <td>:</td>
            <td width="300px">
                <?=$data->phone_no?>
            </td>
            <td>Tanggal</td>
            <td>:</td>
            <td>
                <?=date('d-m-Y', strtotime($data->inv_trx_date))?><br>
            </td>
        </tr>
        <tr>
            <td>Alamat</td>
            <td>:</td>
            <td width="300px">
                <?=$data->address?>
            </td>
            <td>Ekspedisi</td>
            <td>:</td>
            <td>
                
            </td>
        </tr>
    </table>
    <br>
    <!-- Content -->
    <table class="table bordered">
        <tr>
            <th class="text-center">Kode Barang</th>
            <th class="text-center">Deskripsi Barang</th>
            <th class="text-center">Qty</th>
            <th class="text-center">Satuan</th>
            <th class="text-center">Terkirim</th>
        </tr>
        <?php $total_item=$total_qty=0; ?>
        @foreach($detail as $key => $value)
        <?php 
        $total_item++;
        $total_qty+=$value->amount; 
        ?>
        <tr>
            <td class="text-center" @if((count($detail) - 1) != $key ) style="border-bottom:1px solid white" @endif>{{ $value->item_no }}</td>
            <td class="text-center" @if((count($detail) - 1) != $key ) style="border-bottom:1px solid white" @endif>{{ $value->item_name }}</td>
            <td class="text-center" @if((count($detail) - 1) != $key ) style="border-bottom:1px solid white" @endif>{{ $value->amount }}</td>
            <td class="text-center">{{ $value->unit_name }}</td>
            <td></td>
        </tr>
        @endforeach
        <tr>
            <td></td>
            <td></td>
            <td colspan="3">
            PERHATIAN : <br>
            1. Surat Jalan ini merupakan bukti resmi penerimaan barang <br>
            2. Surat Jalan ini bukan bukti penjualan <br>
            3. Surat Jalan ini akan dilengkapi invoice sebagai bukti penjualan
            </td>
        </tr>
        <tr>
            <td style="border-bottom:1px solid white; border-right:1px solid white; border-left:1px solid white"><span style="float:right">Total Item : <?=$total_item?></span></td>
            <td style="border-bottom:1px solid white; border-right:1px solid white; border-left:1px solid white"></td>
            <td style="border-bottom:1px solid white; border-right:1px solid white; border-left:1px solid white">Total Kuantitas : <span style="float:right"><?=$total_qty?> </span></td>
            <td colspan="2" style="border-bottom:1px solid white; border-right:1px solid white; border-left:1px solid white"></td>
        </tr>
    </table>
    <i>BARANG SUDAH DITERIMA DALAM KEADAAN BAIK DAN CUKUP oleh :</i><br>
    <i>(Tanda tangan dan cap stempel perusahaan)</i>
    <br>
    <br>
    <table width="100%">
        <tr>
            <td width="30%" align="center">
                Penerima/Pembeli
            </td>
            <td width="30%" align="center">
                Bagian Pengiriman,
            </td>
            <td width="30%" align="center">
                Petugas Gudang,
            </td>
        </tr>
        <tr>
            <td height="80px"></td>
            <td></td>
            <td></td>
        </tr>
        <tr>
            <td align="center"><?=$data->coorporate_name?> <br><hr style="width:80%;"></td>
            <td align="center"><span style="color:white">-</span><hr style="width:80%;"></td>
            <td align="center"><span style="color:white">-</span><hr style="width:80%;"></td>
        </tr>
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
table td,
    table td * {
        vertical-align: top;
        position:relative;
    }
.title-print {
    font-size: 20px;
    line-height: 1.117188em;
    font-family: "Arial","sans-serif";
    font-weight: bold;
}

.page {
    padding: 10px 20px;
    margin: 0;
}

.table {
    width: 100%;
}

.table.bordered {
    border-collapse: collapse;
    border: 1px solid black;
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