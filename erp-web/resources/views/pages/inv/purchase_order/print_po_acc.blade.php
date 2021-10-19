<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Print Purchase Order</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- <link rel="stylesheet" type="text/css" media="screen" href="main.css"> -->
    <!-- <script src="main.js"></script> -->
    <style>
    table td, table td * {
        vertical-align: top;
    }
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
    function formatDate($date)
    {
        if($date != null){
            return date('d-m-Y', strtotime($date));
        }
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
            PT. YKK AP INDONESIA<br>
            System Made
            </td>
        </tr>
        <tr>
            <td>
                <center><h4>ACCEPTANCE OF ORDER</h4></center>
            </td>
        </tr>
    </table>
    <table style="width:100%; position:relative; font-size:13px">
        <tr>
            <td style="width:50%">
            </td>
            <td>CUSTOMER</td>
            <td>:</td>
            <td>PT. PROSPERITY ARCHILUM</td>
        </tr>
        <tr>
            <td style="width:50%">
            </td>
            <td>ADDRESS</td>
            <td>:</td>
            <td>
            Jl. Margomulyo No. 44 <br>
            Pergudangan Suri Mulia Blok FF/2A<br>
            Email : ptprosperity@yahoo.com<br>
            </td>
        </tr>
        <tr>
            <td style="width:50%">
            </td>
            <td>A/O NO.</td>
            <td>:</td>
            <td></td>
        </tr>
        <tr>
            <td style="width:50%">
            </td>
            <td>DATE</td>
            <td>:</td>
            <td></td>
        </tr>
        <tr>
            <td style="width:50%">
            </td>
            <td>AO ENTRY DATE</td>
            <td>:</td>
            <td></td>
        </tr>
        <tr>
            <td style="width:50%">
            </td>
            <td>DELIVERY EST. DATE</td>
            <td>:</td>
            <td>{{formatDate($purchase['delivery_date'])}}</td>
        </tr>
        <tr>
            <td style="width:50%">PROFILE :
            </td>
            <td>DELIVERY REQUEST</td>
            <td>:</td>
            <td>{{formatDate($purchase['purchase_date'])}}</td>
        </tr>
        <tr>
            <td style="width:50%">
            <input type="checkbox">
            REGULAR
            </td>
            <td>DESTINATION CODE</td>
            <td>:</td>
            <td></td>
        </tr>
        <tr>
            <td style="width:50%">
            <input type="checkbox">
            PROJECT
            </td>
            <td>DELIVERY TO</td>
            <td>:</td>
            <td>PT. PROSPERITY ARCHILUM</td>
        </tr>
        <tr>
            <td style="width:50%">
            </td>
            <td>SITE ADDRESS</td>
            <td>:</td>
            <td>
            Jl. Margomulyo No. 44 <br>
            Pergudangan Suri Mulia Blok FF/2A<br>
            Email : ptprosperity@yahoo.com<br>
            </td>
        </tr>
        <tr>
            <td style="width:50%">
            <input type="checkbox">
            INDUSTRY P/O NO.
            </td>
            <td></td>
            <td></td>
            <td>
            Surabaya (MS)
            </td>
        </tr>
    </table>

    <!-- Content -->
    <table class="table bordered">
        <tr>
            <th class="text-center" rowspan="2">NO</th>
            <th class="text-center" rowspan="2">ITEM</th>
            <th class="text-center">LENGTH</th>
            <th class="text-center" colspan="2">FINISH</th>
            <th class="text-center" rowspan="2">QTY</th>
            <th class="text-center" style="width:130px">UNIT PRICE</th>
            <th class="text-center" style="width:130px">AMOUNT</th>
        </tr>
        <tr>
            <th class="text-center">(mm)</th>
            <th class="text-center">COLOR</th>
            <th class="text-center">THICKNESS</th>
            <th class="text-center">(IDR)</th>
            <th class="text-center">(IDR)</th>
        </tr>
        @if(count($purchase_d) > 0)
        <?php
        $jumlah=0;
        ?>
            @for($i = 0; $i < count($purchase_d); $i++)
            <?php
            $no=$purchase_d[$i]['m_items']['no'];
            $explode=explode('.', $no);
            $jumlah+=$purchase_d[$i]['amount'];
            ?>
            <tr>
                <td class="text-center">{{ ($i + 1) }}</td>
                <td >{{ !empty($explode[0]) ? $explode[0] : '' }}</td>
                <td >{{ ($purchase_d[$i]['m_items']['category'] == 'SPARE PART' ? '-' : $purchase_d[$i]['m_items']['amount_unit_child']) }}</td>
                <td >{{ !empty($explode[1]) ? $explode[1] : '' }}</td>
                <td class="text-center"></td>
                <td class="text-center">{{ $purchase_d[$i]['amount'] }}</td>
                <td></td>
                <td></td>
            </tr>
            @endfor
            @endif
            <tr>
                <td class="text-center"></td>
                <td ></td>
                <td ></td>
                <td ></td>
                <td class="text-center"></td>
                <td class="text-center">{{ $jumlah }}</td>
                <td></td>
                <td></td>
            </tr>
    </table>
    <br>
    Note : Price not include PPN 10% <br>
            <span style="margin-left:40px">Only for 1(One) delivery destination</span>
    <br /><br />
    <table style="width:100%">
        <tr>
            <td style="width:40%"></td>
            <td style="width:30%">PT. YKK AP INDONESIA</td>
            <td style="width:30%;padding-left:40px">CUSTOMER</td>
        </tr>
        <tr>
            <td>
            <br><br><br><br><br><br>
            </td>
            <td></td>
            <td></td>
        </tr>
        <tr>
            <td></td>
            <td>(&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;)</td>
            <td>(&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;)</td>
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
    border: 1px solid black;
    font-size:13px
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