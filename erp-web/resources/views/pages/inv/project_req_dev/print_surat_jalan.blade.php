<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Print Surat Penerimaan Material</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- <link rel="stylesheet" type="text/css" media="screen" href="main.css"> -->
    <!-- <script src="main.js"></script> -->
</head>
<body class="page" onload="window.print()">
    <!-- Title -->
    <table class="table">
        <tr>
            <td width="50%" class="text-right"><img hidden src="" alt="logo" class="dark-logo" /></td>
            <td width="50%"><h3 class="title-print">INVOICE<h3></td>
        </tr>
    </table>
    <!-- Header -->
    <table class="table">
        <tr>
            <td width="50%">Nomer Order : {{ $data[0]->order_no}}</td>
            <td width="50%">Nomer Permintaan : {{ $data[0]->req_no}}</td>
        </tr>
        <tr>
            <td width="50%">Tanggal : {{ date('d-m-Y') }}</td>
            <td width="50%">Tanggal PO : {{ date('d-m-Y', strtotime($data[0]->request_date)) }}</td>
        </tr>
        <tr>
            <td width="50%">Nomor Surat Jalan : {{ $data[0]->no_surat_jalan}}</td>
            <td width="50%">Customer : {{ $data[0]->coorporate_name}}</td>
        </tr>
        <tr>
            <td width="50%"></td>
            <td width="50%">Due Date : {{ date('d-m-Y', strtotime($data[0]->due_date))}}</td>
        </tr>
    </table>

    <!-- Spacer -->
    <br />

    <!-- Content Title -->
    <table class="table">
        <tr>
            <td>Telah kami terima produk dengan rincian sebagai berikut :</td>
        </tr>
    </table>

    <!-- Content -->
    <table class="table bordered">
        <tr>
            <th class="text-center">No</th>
            <th class="text-center">Nomor Produk</th>
            <th class="text-center">Produk</th>
            <th class="text-center">Harga</th>
        </tr>
        @php 
        $a=$total=0;
        @endphp
       @foreach($data as $key => $value)
       @php 
       $a++;
       $total+=$value->price; 
       @endphp
       <tr>
            <td class="text-center">{{$a}}</td>
            <td class="text-center">{{$value->label}}</td>
            <td class="text-center">{{$value->item}} {{$value->product_name}} {{$value->series}}</td>
            <td class="text-right">{{number_format($value->price, 0, '.', '.')}}</td>
        </tr>
       @endforeach
       <tr>
            <td colspan="2"></td>
            <td class="text-center">Sub Total</td>
            <td class="text-right">{{number_format($total, 0, '.', '.')}}</td>
        </tr>
        @php 
        $ppn=($total * (1/10));
        @endphp
        <tr>
            <td colspan="2"></td>
            <td class="text-center">PPN (10 %)</td>
            <td class="text-right">{{number_format($ppn, 0, '.', '.')}}</td>
        </tr>
        <tr>
            <td colspan="2"></td>
            <th class="text-center">Total</th>
            <td class="text-right">{{number_format(($total + $ppn), 0, '.', '.')}}</td>
        </tr>
    </table>

    <br /><br />

    <!-- Document Signer -->
    <table class="table">
        <tr>
            <td width="50%" class="text-center">Pengirim,</td>
            <td width="50%" class="text-center">Penerima,</td>
        </tr>
        <tr>
            <td height="60px"></td>
        </tr>
        <tr>
            <td width="50%"></td>
            <td width="50%"></td>
        </tr>
        <tr>
            <td width="50%" class="upper-line text-center">Pengirim / Driver</td>
            <td width="50%" class="upper-line text-center">{{ $data[0]->coorporate_name}}</td>
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