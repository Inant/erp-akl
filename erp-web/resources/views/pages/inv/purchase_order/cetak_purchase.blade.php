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
    <table class="table bordered">
        <thead>
            <tr>
                <td colspan="3" class="font-in" rowspan="2" style="width:30%">Dibayarkan Kepada : {{$data[0]->supplier}}</td>
                <td colspan="2" class="font-in" rowspan="2" align="center"><h3>Permintaan Pembayaran</h3></td>
                <td colspan="4" class="font-in" style="width:100%">Nomor : {{$data[0]->no}}</td>
            </tr>
            <tr>
                <td colspan="4" class="font-in" style="width:100%">Tanggal : {{formatDate($data[0]->create_date)}}</td>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td style="width:10%"></td>
                <th style="width:10%; border-right:1px solid white" colspan="3" class="in">Nomor Surat Jalan</th>
                <th style="width:60%; border-right:1px solid white" colspan="2" class="in">Nomor PO</th>
                <th style="width:60%; border-right:1px solid white" colspan="2" class="in">Nomor Invoice</th>
            </tr>
            <?php
            $total=0;
            ?>
            @foreach($data as $value)
            <tr>
                <td style="width:10%"></td>
                <td style="width:10%" class="font-in" colspan="3">{{$value->no_surat_jalan != null ? $value->no_surat_jalan : ($value->no_surat_jalan_jasa ? $value->no_surat_jalan_jasa : '-')}}</td>
                <td style="width:60%" colspan="2" class="font-in">{{$value->purchase_no != null ? $value->purchase_no : ($value->purchase_asset_no != null ? $value->purchase_asset_no : ($value->purchase_service_no != null ? $value->purchase_service_no : '-')) }}</td>
                <td style="width:60%" colspan="2" class="font-in">{{$value->paid_no}}</td>
            </tr>
            @endforeach
            <tr>
                <td style="width:10%"></td>
                <th style="width:10%; border-right:1px solid white" class="in" colspan="3">No Tagihan</th>
                <th style="width:60%; border-right:1px solid white" colspan="4" class="in">Nominal Tagihan</th>
            </tr>
            <?php
            $total=0;
            ?>
            @foreach($data as $value)
            <tr>
                <td style="width:10%"></td>
                <td style="width:10%" class="font-in"  colspan="3">{{$value->bill_no}}</td>
                <td align="right" style="width:20%" colspan="4" class="font-in" >Rp. {{formatRupiah($value->amount)}}</td>
            </tr>
            <?php $total+=$value->amount;?>
            @endforeach
            <tr>
                <td style="width:10%" class="font-in">Terbilang</td>
                <th style="width:60%" colspan="6" class="font-in">{{terbilang($total)}} rupiah</th>
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