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
    .floatLeft { width: 60%; float: left; padding-bottom:10px}
    .floatRight {width: 40%; float: right; }
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
      $temp = penyebut($nilai - 10). " Belas";
    } else if ($nilai < 100) {
      $temp = penyebut($nilai/10)." Puluh". penyebut($nilai % 10);
    } else if ($nilai < 200) {
      $temp = " Seratus" . penyebut($nilai - 100);
    } else if ($nilai < 1000) {
      $temp = penyebut($nilai/100) . " ratus" . penyebut($nilai % 100);
    } else if ($nilai < 2000) {
      $temp = " Seribu" . penyebut($nilai - 1000);
    } else if ($nilai < 1000000) {
      $temp = penyebut($nilai/1000) . " Ribu" . penyebut($nilai % 1000);
    } else if ($nilai < 1000000000) {
      $temp = penyebut($nilai/1000000) . " Juta" . penyebut($nilai % 1000000);
    } else if ($nilai < 1000000000000) {
      $temp = penyebut($nilai/1000000000) . " Milyar" . penyebut(fmod($nilai,1000000000));
    } else if ($nilai < 1000000000000000) {
      $temp = penyebut($nilai/1000000000000) . " Trilyun" . penyebut(fmod($nilai,1000000000000));
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

  $total_bill=$total_addendum=$total_payment=0;
  if ($cust_bill['end_payment'] == true) {
    foreach ($cust_bill_other as $value) {
        if($value->is_increase == 1){
            $total_addendum+=$value->amount;
        }else{
          $total_addendum-=$value->amount;
        }
    }
  }
  $total_bill=$cust_bill['amount'];
  $total_payment=($total_bill + ($total_bill * 0.1));
?>
<body class="page" onload="window.print()" style="padding:50px; font-size:16px">
  <div style="padding-top:130px">
    <!-- Title -->
    <!-- <div style="float:right">
    <span style="font-size:18px"><b>PT. PROSPERITY ARCHILUM </b></span><br>
    Jl. Margomulyo No. 44 <br>
    Pergudangan Suri Mulia Blok FF/2A<br>
    Email : ptprosperity@yahoo.com<br>
    </div>     -->
    <!-- Header -->
    <table hidden style="width:100%">
    <tr>
        <td style="width:30%"></td>
        <td align="right"><span style="font-size:18px"><b>PT. PROSPERITY ARCHILUM </b></span><br>
    Jl. Margomulyo No. 44, Blok FF/2A Surabaya - Indonesia <br>
    Kawasan Pergudangan Suri Mulia<br>
    Telp.(031) 7 483700 Fax.(031) 7483900<br></td>
    </tr>
    </table>
    <center><b style="font-size:18px;"><i>TAGIHAN</i></b></center>
    <hr style="height:3px; background-color:black"><hr>
    <div class="floatLeft">
        <table style="font-size:12px;">
            <tr>
                <td style="width:70px">Nama</td>
                <td>:</td>
                <td>{{$customer['coorporate_name']}}</td>
            </tr>
            <tr>
                <td style="vertical-align: top;">Alamat</td>
                <td style="vertical-align: top;">:</td>
                <td style="margin-right:15px;">{{$customer['address']}}</td>
            </tr>
            <tr>
                <td>Phone</td>
                <td>:</td>
                <td>{{$customer['phone_no']}}</td>
            </tr>
            <tr>
                <td>NPWP</td>
                <td>:</td>
                <td>{{$customer['npwp']}}</td>
            </tr>
        </table>
    </div>
    <div class="floatRight">
        <table style="font-size:12px;">
            <tr>
                <td>No. Invoice</td>
                <td>:</td>
                <td>{{$cust_bill['invoice_no']}}</td>
            </tr>
            <tr>
                <td>No. Faktur</td>
                <td>:</td>
                <td>{{$cust_bill['bill_no']}}</td>
            </tr>
            <tr>
                <td>Date</td>
                <td>:</td>
                <td>{{date("d-m-Y", strtotime($cust_bill['create_date']))}}</td>
            </tr>
            <tr>
                <td>Due Date Payment</td>
                <td>:</td>
                <td>{{date("d-m-Y", strtotime($cust_bill['due_date']))}}</td>
            </tr>
            <tr>
                <td>SPJB No</td>
                <td>:</td>
                <td>{{$order['spk_number']}}</td>
            </tr>
        </table>
    </div>
    <!-- Spacer -->
    <div style="color:white">-
    </div>
    <!-- Content -->
    <table style="width:100%;">
        <tr>
            <th colspan="5" class="text-center border">DESCRIPTION</th>
            <th class="text-center border" style="width:150px">AMOUNT</th>
        </tr>
        <tr>
            <td colspan="5" class="border" style="padding-bottom:100px">{{$cust_bill['description'].' - '.$cust_bill['notes']}}</td>
            <td class="text-right border" style="padding-bottom:100px">Rp. {{number_format($cust_bill['amount'])}}</td>
        </tr>
        @if($cust_bill['end_payment'] == true)
        @foreach($cust_bill_other as $value)
        <tr>
            <td colspan="5" class="border">{{$value->notes}}</td>
            <td class="text-right border">Rp. {{number_format($value->amount)}}</td>
        </tr>
        @endforeach
        @endif
        <tr>
            <td colspan="3" style="width:50%"></td>
            <td style="width:3%"></td>
            <td class="border">Sub Total</td>
            <td class="text-right border">Rp. {{number_format($cust_bill['amount'])}}</td>
        </tr>
        <tr>
            <td colspan="3">Payment Detail</td>
            <td></td>
            <td class="border">Discount / Advance</td>
            <td class="text-right border"></td>
        </tr>
        <tr>
            <td colspan="3" style="width:50%" class="border-left border-top border-right">Please pay the invoice in <b>FULL AMOUNT</b></td>
            <td></td>
            <td class="border">Total</td>
            <td class="text-right border">Rp. {{number_format($total_bill)}}</td>
        </tr>
        <tr>
            <td colspan="3" class="border-left border-right">(without bank charge) Bank Account : </td>
            <td></td>
            <td class="border">VAT (10%)</td>
            <td class="text-right border">Rp. {{number_format($total_bill * 0.1)}}</td>
        </tr>
        <tr>
            <td class="border-left"><b>Name</b></td>
            <td>:</td>
            <td class="border-right"><b>PT. Prosperity Archilum</b></td>
            <td></td>
            <td><b><i>AMOUNT PAID</i></b></td>
            <td class="text-right border">Rp. {{number_format($total_payment)}}</td>
        </tr>
        <tr>
            <td class="border-left"><b>Danamon</b></td>
            <td>:</td>
            <td class="border-right"><b>660 003 0800</b></td>
            <td></td>
            <td></td>
            <td></td>
        </tr>
        <tr>
            <td class="border-left border-bottom"><b>BCA</b></td>
            <td class="border-bottom">:</td>
            <td class="border-right border-bottom"><b>729 191 9999</b></td>
            <td></td>
            <td class="text-center" colspan="2">Regards</td>
        </tr>
        <tr>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td class="text-center" colspan="2">PT. Prosperity Archilum</td>
        </tr>
        <tr>
            <td colspan="3" style="width:50%;" class="border"><b>Terbilang :</b></td>
            <td></td>
            <td></td>
            <td></td>
        </tr>
        <tr>
            <td colspan="3" style="width:50%; padding-bottom:30px" class="border-left border-bottom border-right">{{terbilang($total_payment)}} rupiah</td>
            <td></td>
            <td colspan="2"></td>
        </tr>
        <tr>
            <td colspan="3" style="width:50%;"></td>
            <td></td>
            <td colspan="2" class="text-center"><ins>Roy Sudarso</ins></td>
        </tr>
        <tr>
            <td colspan="3" style="width:50%;"></td>
            <td></td>
            <td colspan="2" class="text-center"><b>Direktur</b></td>
        </tr>
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