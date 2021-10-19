<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Print All Stock</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- <link rel="stylesheet" type="text/css" media="screen" href="main.css"> -->
    <!-- <script src="main.js"></script> -->
</head>
<body class="page" onload="window.print()">
    <!-- Title -->
    <table class="table">
        <tr>
            <td width="50%"><h3 class="title-print">CETAK SEMUA STOK<h3></td>
            <td width="50%" class="text-right"><img src="{!! asset('theme/assets/images/img_01.png') !!}" alt="logo" class="dark-logo" /></td>
        </tr>
    </table>

    <!-- Content -->
    <table class="table bordered">
        <tr>
            <th class="text-center">No</th>
            <th class="text-center">Nomor Material</th>
            <th class="text-center">Material</th>
            <th class="text-center">Satuan</th>
            <th class="text-center">Jumlah Stock Opname</th>
        </tr>
        @if(count($list_stok) > 0)
            @for($i = 0; $i < count($list_stok); $i++) 
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>{{ $list_stok[$i]['m_items']['no'] }}</td>
                    <td>{{ $list_stok[$i]['m_items']['name'] }}</td>
                    <td class="text-center">{{ $list_stok[$i]['m_units']['name'] }}</td>
                    <td></td>
                </tr>
            @endfor
        @endif
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