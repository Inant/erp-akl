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
@php
    function formatRupiah($num){
        return number_format($num, 0, '.', '.');
    }
    function formatDate($date){
        $date=date_create($date);
        return date_format($date, 'd-m-Y');
    }
    function penyebut($nilai) {
    $nilai = abs($nilai);
    $huruf = array("", "Satu", "Dua", "Tiga", "Empat", "Lima", "Enam", "Tujuh", "Delapan", "Sembilan", "Sepuluh", "Sebelas");
    $temp = "";
    if ($nilai < 12) {
      $temp = " ". $huruf[$nilai];
    } else if ($nilai < 20) {
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
    if($nilai < 0) {
      $hasil = "minus ". trim(penyebut($nilai));
    } else {
      $hasil = trim(penyebut($nilai));
    }         
    return $hasil;
  }
  $title='';
  if(strpos($tipe_label, 'BKK') !== false){
    $title='BUKTI KAS KELUAR';
  }else if(strpos($tipe_label, 'BKM') !== false){
    $title='BUKTI KAS MASUK';
  }else if(strpos($tipe_label, 'BBK') !== false){
    $title='BUKTI BANK KELUAR';
  }else if(strpos($tipe_label, 'BBM') !== false){
    $title='BUKTI BANK MASUK';
  }else{
    $title='';
  }
@endphp
<body class="page" onload="window.print()">
    <table class="table bordered">
        <thead>
            <tr>
                <td colspan="3" class="{{$cash == 'in' ? 'font-in' : 'font-out'}}" rowspan="2" style="width:30%">Dibayarkan Kepada :</td>
                <td colspan="3" class="{{$cash == 'in' ? 'font-in' : 'font-out'}}" rowspan="2" align="center"><h3>{{$title}}</h3></td>
                <td colspan="3" class="{{$cash == 'in' ? 'font-in' : 'font-out'}}" style="width:30%">Nomor : {{$no_label}}</td>
            </tr>
            <tr>
                <td colspan="3" class="{{$cash == 'in' ? 'font-in' : 'font-out'}}" style="width:30%">Tanggal : {{formatDate($data[0]->tanggal)}}</td>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td style="width:10%"></td>
                <th style="width:10%; border-right:1px solid white" class="{{$cash == 'in' ? 'in' : 'out'}}">Perkiraan</th>
                <th style="width:60%; border-right:1px solid white" colspan="5" class="{{$cash == 'in' ? 'in' : 'out'}}">U R A I A N</th>
                <th style="width:20%" colspan="2" class="{{$cash == 'in' ? 'in' : 'out'}}">Jumlah</th>
            </tr>
            <?php
            $total=0;
            ?>
            @foreach($data as $value)
            <tr>
                <td style="width:10%"></td>
                <td style="width:10%" class="{{$cash == 'in' ? 'font-in' : 'font-out'}}">{{$value->no_akun}}</td>
                <td style="width:60%" colspan="5" class="{{$cash == 'in' ? 'font-in' : 'font-out'}}">{{$value->deskripsi}}</td>
                <td align="right" style="width:20%" colspan="2" class="{{$cash == 'in' ? 'font-in' : 'font-out'}}">Rp. {{formatRupiah($value->jumlah)}}</td>
            </tr>
            <?php $total+=$value->jumlah;?>
            @endforeach
            <tr>
                <td style="width:10%" class="{{$cash == 'in' ? 'font-in' : 'font-out'}}">Terbilang</td>
                <th style="width:60%" colspan="5" class="{{$cash == 'in' ? 'font-in' : 'font-out'}}">{{terbilang($total)}} rupiah</th>
                <th style="width:10%" class="{{$cash == 'in' ? 'font-in' : 'font-out'}}">Total</th>
                <th align="right" style="width:20%" colspan="2" class="{{$cash == 'in' ? 'font-in' : 'font-out'}}">Rp. {{formatRupiah($total)}}</th>
            </tr>
            <tr>
                <td colspan="3" style="width:30%; border-bottom:1px solid white" class="{{$cash == 'in' ? 'font-in' : 'font-out'}}">Catatan :</td>
                <th style="width:15%; border-right:1px solid white" class="{{$cash == 'in' ? 'in' : 'out'}}">Pembukuan</th>
                <th style="width:15%; border-right:1px solid white" class="{{$cash == 'in' ? 'in' : 'out'}}">Mengetahui</th>
                <th style="width:15%; border-right:1px solid white" class="{{$cash == 'in' ? 'in' : 'out'}}">Kasir</th>
                <th colspan="3" style="width:15%" class="{{$cash == 'in' ? 'in' : 'out'}}">Penerima</th>
            </tr>
            <tr>
                <td colspan="3" style="width:30%; height:50px"></td>
                <th style="width:15%"></th>
                <th style="width:15%"></th>
                <th style="width:15%"></th>
                <td colspan="3" style="width:15%"></td>
            </tr>
        </tbody>
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
    border: 2px solid {{$cash == 'in' ? '#377142' : '#ac4b6a'}};
}

.bordered th, .bordered td {
    border: 1px solid {{$cash == 'in' ? '#377142' : '#ac4b6a'}};
    padding: 5px;
}
.in{
    background-color : #377142;
    color : white
}
.out{
    background-color : #ac4b6a;
    color : white
}
.font-in{
    color : #377142;
}
.font-out{
    color : #ac4b6a;
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