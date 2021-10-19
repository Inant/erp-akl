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
            <div style="position: absolute; bottom: 0px;" class="title-print">Surat Jalan</div>
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
    <table class="table">
        <thead>
            <tr>
                <td>No. Permintaan  </td>
                <td> : </td>
                <td>{{$ts_warehouse['no']}}</td>
                <td>Tanggal Permintaan </td>
                <td> : </td>
                <td>{{date("d-m-Y", strtotime($ts_warehouse['created_at']))}}</td>
            </tr>
            <tr>
                <td>Penerima  </td>
                <td> : </td>
                <td></td>
                <td>Due Date </td>
                <td> : </td>
                <td>{{date("d-m-Y", strtotime($ts_warehouse['due_date_receive']))}}</td>
            </tr>
            <tr>
                <td>Alamat Penerima  </td>
                <td> : </td>
                <td></td>
            </tr>
        </thead>
    </table>
    <!-- Spacer -->
    <br />

    <!-- Content -->
    <table class="table bordered">
        <tr>
            <th class="text-center">No</th>
            <th class="text-center">Item</th>
            <th class="text-center">Deskripsi Barang</th>
            <th class="text-center">Qty</th>
            <th class="text-center">Satuan</th>
        </tr>
        @php $i=1 @endphp
        @foreach($ts_warehouse_d as $value)
        <tr>
            <td class="text-center">{{$i}}</td>
            <td class="text-center">{{$value->m_items->name}}</td>
            <td class="text-center"></td>
            <td class="text-center">{{$value->amount}}</td>
            <td class="text-center">{{$value->m_units->name}}</td>
        </tr>
        @php $i++ @endphp
        @endforeach
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