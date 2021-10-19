<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Print Bukti Pengeluaran Material</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- <link rel="stylesheet" type="text/css" media="screen" href="main.css"> -->
    <!-- <script src="main.js"></script> -->
</head>
<body class="page" onload="window.print()">
    <!-- Title -->
    <table class="table">
        <tr>
            <td width="50%"><h3 class="title-print">BUKTI PENGELUARAN MATERIAL<h3></td>
            <td width="50%" class="text-right"><img src="{!! asset('theme/assets/images/img_01.png') !!}" alt="logo" class="dark-logo" /></td>
        </tr>
    </table>
    
    <!-- Header -->
    <table class="table">
        <tr>
            <td width="50%">Nomer : {{ $inv_request['inv_trx'] != null ? $inv_request['inv_trx'][0]['no'] : '' }}</td>
            <td width="50%">Nomer Request : {{ $inv_request['no'] }}</td>
        </tr>
        <tr>
            <td width="50%">Tanggal : {{ date('d-m-Y') }}</td>
            <td width="50%">Tanggal Request : {{ date('d-m-Y', strtotime($inv_request['created_at'])) }}</td>
        </tr>
        <tr>
            <td width="50%">Untuk Kavling : {{ $inv_request['project'] != null ? $inv_request['project']['name'] : '' }} </td>
            <td width="50%"></td>
        </tr>
        <tr>
            <td width="50%">Kontraktor / Mandor : {{ $inv_request['contractor'] }}</td>
            <td width="50%"></td>
        </tr>
    </table>

    <!-- Spacer -->
    <br />

    <!-- Content Title -->
    <table class="table">
        <tr>
            <td>Telah kami serahkan material / barang dengan rincian sebagai berikut :</td>
        </tr>
    </table>

    <!-- Content -->
    <table class="table bordered">
        <tr>
            <th class="text-center">No</th>
            <th class="text-center">Nomor Material</th>
            <th class="text-center">Material</th>
            <th class="text-center">Qty</th>
            <th class="text-center">Satuan</th>
            <th class="text-center">Keterangan</th>
        </tr>
        @if(count($inv_request_d) > 0)
            @for($i = 0; $i < count($inv_request_d); $i++)
            <tr>
                <td class="text-center">{{ ($i + 1) }}</td>
                <td class="text-center">{{ $inv_request_d[$i]['m_items']['no'] }}</td>
                <td>{{ $inv_request_d[$i]['m_items']['name'] }}</td>
                <td class="text-right">{{ (float)$inv_request_d[$i]['amount'] }}</td>
                <td class="text-center">{{ $inv_request_d[$i]['m_units']['name'] }}</td>
                <td>{{ $inv_request_d[$i]['notes'] }}</td>
            </tr>
            @endfor
        @endif
    </table>

    <br /><br />

    <!-- Document Signer -->
    <table class="table">
        <tr>
            <td width="50%">Diserahkan oleh,</td>
            <td width="50%">Penerima,</td>
        </tr>
        <tr>
            <td height="60px"></td>
        </tr>
        <tr>
            <td width="50%">{{ $user_name != null ? $user_name : '' }}</td>
            <td width="50%">{{ $inv_request['contractor'] }}</td>
        </tr>
        <tr>
            <td width="50%" class="upper-line">Inventory Man</td>
            <td width="50%" class="upper-line">Kontraktor / Mandor</td>
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