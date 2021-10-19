<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Print Label</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- <link rel="stylesheet" type="text/css" media="screen" href="main.css"> -->
    <!-- <script src="main.js"></script> -->
</head>
<?php
$total=count($data)/16;
?>
<body class="page" onload="window.print()">
    <!-- Title -->
    @php $n=0; $m=16; @endphp
    @for($b=0; $b < ceil($total) ; $b++)
    <table style="width:100%; height:100%">
        @for($i=$n; $i < $m; $i=$i+2)
        @php 
        $a=$i; 
        @endphp
        <tr style="height:145px;">
            <th style="font-size:35px">{{!empty($data[$a]) ? $data[$a]->no : ''}}</th>
            <th style="font-size:35px">{{!empty($data[$a+1]) ? $data[$a+1]->no : ''}}</th>
        </tr>
        @endfor
        @php 
        $n+=16; 
        $m+=16; 
        @endphp
    </table>
    @if((ceil($total)-1) != $b)
    <br><br><br><br><br>
    @endif
    @endfor
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