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
<body class="page" onload="window.print()">
    <!-- Title -->
    <table class="table">
        <tr>
            <td width="50%"><h3 class="title-print">PURCHASE ORDER (PO)<h3></td>
            <td width="50%" class="text-right"><img src="{!! asset('theme/assets/images/img_01.png') !!}" alt="logo" class="dark-logo" /></td>
        </tr>
    </table>
    
    <!-- Header -->
    <table class="table">
        <tr>
            <td width="50%">Nomer : {{ $purchase['no'] }}</td>
            <td width="50%">Vendor: {{ $purchase['m_suppliers']['name'] }}</td>
        </tr>
        <tr>
            <td width="50%">Tanggal : {{ date('d-m-Y') }}</td>
            <td width="50%"></td>
        </tr>
    </table>

    <!-- Spacer -->
    <br />

    <!-- Content Title -->
    <table class="table">
        <tr>
            <td>Dengan ini kami order pembelian material dengan rincian sebagai berikut :</td>
        </tr>
    </table>

    <!-- Content -->
    <table class="table bordered">
        <tr>
            <th rowspan="2" class="text-center">No</th>
            <th rowspan="2" class="text-center">Nomor Material</th>
            <th rowspan="2" class="text-center">Material</th>
            <th rowspan="2" class="text-center">Qty</th>
            <th rowspan="2" class="text-center">Satuan</th>
            <th colspan="2" class="text-center">Harga (Rp.)</th>
            <th rowspan="2" class="text-center">Keterangan</th>
        </tr>
        <tr>
            <th class="text-center">Satuan @</th>
            <th class="text-center">Total</th>
        </tr>
        @if(count($purchase_d) > 0)
        <?php
        $jumlah=0;
        ?>
            @for($i = 0; $i < count($purchase_d); $i++)
            <tr>
                <td class="text-center">{{ ($i + 1) }}</td>
                <td class="text-center">{{ $purchase_d[$i]['m_items']['no'] }}</td>
                <td>{{ $purchase_d[$i]['m_items']['name'] }}</td>
                <td class="text-right">{{ (float)$purchase_d[$i]['amount'] }}</td>
                <td class="text-center">{{ $purchase_d[$i]['m_units']['name'] }}</td>
                <td class="text-right">{{ $purchase_d[$i]['base_price'] }}</td>
                <td class="text-right">{{ $total=$purchase_d[$i]['base_price'] * $purchase_d[$i]['amount'] }}</td>
                <td></td>
            </tr>
            <?php $jumlah+=$total?>
            @endfor
            <tr>
                <td class="text-center" colspan="5"><b>Grand Total</b></td>
                <td class="text-center" colspan="3">Rp. {{ $jumlah }}</td>
            </tr>
        @endif
    </table>

    <br /><br />

    <!-- Document Signer -->
    <table class="table">
        <tr>
            <td width="50%">PO Oleh,</td>
            <td width="50%">Mengetahui,</td>
        </tr>
        <tr>
            <td height="60px"></td>
        </tr>
        <tr>
            <td width="50%" class="upper-line">Inventory Man</td>
            <td width="50%" class="upper-line">Site Manager</td>
        </tr>
    </table>
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