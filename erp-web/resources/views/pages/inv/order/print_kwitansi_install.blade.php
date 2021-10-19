<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Print Tagihan Customer</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- <link rel="stylesheet" type="text/css" media="screen" href="main.css"> -->
    <!-- <script src="main.js"></script> -->
    <style>
    .floatLeft { width: 50%; float: left; padding-bottom:10px}
    .floatRight {width: 50%; float: right; }
    </style>
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
  function formatBulan($val){
        $date=explode('-', $val);
        $bulan = ['Januari', 'Februari', 'Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
        $getMonth=(int)$date[1];
        return $date[0].' '. $bulan[$getMonth-1].' '.$date[2];
    }
  $total_addendum=$total_payment=0;
  $total_payment=($cust_bill['amount'] + ($cust_bill['amount'] * (1/10))) + $total_addendum;
?>
<body class="page" onload="window.print()"  style="padding:30px; border:1px solid black; margin:10px">
  <div">
    <!-- Title -->
    <!-- <div style="float:right">
    <span style="font-size:18px"><b>PT. PROSPERITY ARCHILUM </b></span><br>
    Jl. Margomulyo No. 44 <br>
    Pergudangan Suri Mulia Blok FF/2A<br>
    Email : ptprosperity@yahoo.com<br>
    </div>     -->
    <!-- Header -->
    <center><b style="font-size:26px;"><ins>K W I T A N S I</ins></b></center>
    <br>
    <table>
        <thead>
            <tr>
                <th style="width:50px" align="left">No. :</th>
                <th></th>
                <th>{{$cust_bill['bill_no']}}</th>
            </tr>
        </thead>
    </table>
    <br>
    <table style="width:100%;">
        <thead>
            <tr>
                <td>Telah terima dari</td>
                <td>:</td>
                <td style="padding-bottom:10px; font-size:16px; height:10px; vertical-align:top"><b>{{$customer['coorporate_name']}}</b></td>
            </tr>
            <tr>
                <td style="padding-bottom:10px">Banyaknya uang </td>
                <td style="padding-bottom:10px">:</td>
                <td style="padding-bottom:10px; font-size:16px">{{terbilang($cust_bill_d->amount)}} rupiah</td>
            </tr>
            <tr>
                <td style="padding-bottom:10px; width:110px; vertical-align:top">Untuk Pembayaran</td>
                <td style="padding-bottom:10px; vertical-align:top">:</td>
                <td style="height:120px; font-size:16px; vertical-align:top">{{$cust_bill['description'].' - '.$cust_bill['notes']}}</td>
            </tr>
        </thead>
    </table>
    <!-- Spacer -->
    <!-- <table style="width:100%">
    <tr>
        <td style="width:30%"></td>
        <td><span style="font-size:18px"><b>PT. PROSPERITY ARCHILUM </b></span><br>
    Jl. Margomulyo No. 44, Blok FF/2A Surabaya - Indonesia <br>
    Kawasan Pergudangan Suri Mulia<br>
    Telp.(031) 7 483700 Fax.(031) 7483900<br></td>
    </tr>
    </table> -->

    <table style="width:100%">
        <thead>
            <tr>
                <td colspan="3"><button style="font-size:12px; background-color:white; border-color: black; border:1px solid black; border-radius:5px"><p style="font-size:10px; font-style:italic; margin-left:40px">NB. Mohon Pembayaran ditransfer ke rekening atas nama PT. Prosperity Archilum <br> Danamon: 660.003.0800 / BCA :729 191 9999</p></button></td>
            </tr>
            <tr>
                <td style="width:40%" colspan="2"></td>
                <td style="width:30%"></td>
                <td style="width:30%" align="center"> Surabaya, {{formatBulan(date("d-m-Y", strtotime($cust_bill['create_date'])))}}</td>
            </tr>
            <tr>
                <td style="height:25px"></td>
            </tr>
            <tr>
                <td style="width:40%; padding-bottom:5px" colspan="2"><button style="font-size:12px; color:white; background-color:#4a4949; border-color: #4a4949; border:1px solid #4a4949; border-radius:5px"><b>Pembayaran dianggap lunas<br>setelah kami uangkan.</b></button></td>
                <td style="width:30%"></td>
                <td style="width:30%"></td>
            </tr>
            <tr>
                <td style="width:30%;padding:10px 0 10px 0; border-top:1px solid black; border-bottom:1px solid black"><i><b>Terbilang Rp.</b></i></td>
                <td style="width:10%;padding:10px 0 10px 0; border-top:1px solid black; border-bottom:1px solid black" align="right"><b>{{number_format($cust_bill_d->amount, 0, '.', '.')}}</b></td>
                <td style="width:30%;"></td>
                <td style="width:30%" align="center"></td>
            </tr>
            <tr>
                <td style="width:30%" colspan="2"></td>
                <td style="width:30%"></td>
                <td style="width:40%" align="center">&nbsp;&nbsp;&nbsp;<ins><b>( ROY SUDARSO )</b></ins> &nbsp;&nbsp;&nbsp;</td>
            </tr>
        </thead>
    </table>   
    <style>
    table {
    border-collapse: collapse;
    }
    .border{
        padding : 3px;
        border: 1px solid black;
    }
    .border-left{
        padding : 3px;
        border-left: 1px solid black;
    }
    .border-top{
        padding : 3px;
        border-top: 1px solid black;
    }
    .border-right{
        padding : 3px;
        border-right: 1px solid black;
    }
    .border-bottom{
        padding : 3px;
        border-bottom: 1px solid black;
    }
    </style>
    </div>
    </body>
</html>

<style>
body {
    font-size: 14px;
    font-family: "ALIUIV+Calibri";
    /* color: #000000; */
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