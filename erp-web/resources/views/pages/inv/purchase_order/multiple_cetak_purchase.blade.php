<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Cetak Permintaan Pembayaran Suplier</title>
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
  // $title='';
  // if(strpos($tipe_label, 'BKK') !== false){
  //   $title='BUKTI KAS KELUAR';
  // }else if(strpos($tipe_label, 'BKM') !== false){
  //   $title='BUKTI KAS MASUK';
  // }else if(strpos($tipe_label, 'BBK') !== false){
  //   $title='BUKTI BANK KELUAR';
  // }else if(strpos($tipe_label, 'BBM') !== false){
  //   $title='BUKTI BANK MASUK';
  // }else{
  //   $title='';
  // }
@endphp
<body class="page" onload="window.print()">
    <center>
        <h4>Permintaan Pembayaran Suplier</h4>
    </center>
    <table class="table bordered">
        <thead>
            <tr>
                <th>No</th>
                <th>No Sistem</th>
                <th>No Nama Supplier</th>
                <th>No Surat Jalan</th>
                <th>No Tagihan</th>
                <th>No Invoice</th>
                <th>Nominal Tagihan</th>
                <th>Tanggal Jatuh Tempo</th>
            </tr>
        </thead>
        <tbody>
            @php
                $totalTagihan = 0;
            @endphp
            @foreach ($data as $key =>$value)
                @php
                    $totalTagihan += $value->amount;
                @endphp
                <tr>
                    <td>{{$loop->iteration}}</td>
                    <td>{{$value->no}}</td>
                    <td>{{$value->supplier}}</td>
                    <td>{{$value->no_surat_jalan != null ? $value->no_surat_jalan : ($value->no_surat_jalan_jasa ? $value->no_surat_jalan_jasa : '-')}}</td>
                    <td>{{$value->bill_no}}</td>
                    <td>{{$value->paid_no}}</td>
                    <td>Rp{{formatRupiah($value->amount)}}</td>
                    <td>{{formatDate($value->due_date)}}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <th colspan="6" style="text-align:center">Total</th>
                <th colspan="2">Rp{{formatRupiah($totalTagihan)}}</th>
            </tr>
            <tr>
                <td colspan="3" style="width:30%; border-bottom:1px solid white" class="font-in">Catatan :</td>
                <th style="width:15%; border-right:1px solid white" class="in">Pembukuan</th>
                <th style="width:15%; border-right:1px solid white" class="in">Mengetahui</th>
                <th style="width:15%; border-right:1px solid white" class="in">Kasir</th>
                <th colspan="3" style="width:15%" class="in">Penerima</th>
            </tr>
            <tr>
                <td colspan="3" style="width:30%; height:50px"></td>
                <th style="width:15%"></th>
                <th style="width:15%"></th>
                <th style="width:15%"></th>
                <td colspan="3" style="width:15%"></td>
            </tr>
        </tfoot>
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
    border: 2px solid #377142;
}

.bordered th, .bordered td {
    border: 1px solid #377142;
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