<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <title>Slip Gaji</title>
  <style>
	.header img {
	  float: left;
	  width: 130px;
	  /*height: 100px;*/
      margin-top: -15px;
	}

	.header h2 {
	  position: relative;
	  top: 25px;
	  left: 20px;
	  font-size: 20px;
	}
    #table {
    border-collapse: collapse;
    width: 100%;
    }

    #table_th{
    padding-bottom: 5px;
    padding-top: 5px;
    /*text-align: left;*/
    border-bottom: 1px solid #000;
    }
    #tbl_padding{
    padding-top: 8px;
    }
</style>
</head>

<body onload="window.print()">
@php
function formatCurrency($val){
    return number_format($val, 0, '.', '.');
}
function formatTanggal($val){
    $date=date('d-m-Y', strtotime($val));
    return $date;
}
function getMonth($month){
    $month=explode('-', $month);
    $bulan = ["Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];
    return $bulan[$month[1]-1].' '.$month[0];
}

$gaji_pokok=$jumlah_absen > 1 ? ($gaji->gaji_pokok / $total_hari_kerja) * ($jumlah_kehadiran + 1) : $gaji->gaji_pokok;
$kehadiran=$jumlah_kehadiran;
$total_uang_makan=$jumlah_kehadiran * $gaji->uang_makan;
$total_uang_transport=$jumlah_kehadiran * $gaji->uang_transport;
$bonus_kerajinan=$jumlah_denda < 1500000 ? 300000 : 0;
$grand_total=$gaji != null ? (($gaji_pokok + $total_uang_makan + $total_uang_transport + $bonus_disiplin) - $jumlah_denda) : 0;
@endphp

<table id="table">
    <tr>
        <td>Bulan</td>
        <td>&nbsp;&nbsp;:&nbsp;&nbsp;</td>
        <td>{{getMonth($bulan)}}</td>
    </tr>
    <tr>
        <td>Nama Pegawai</td>
        <td> &nbsp;&nbsp;: &nbsp;&nbsp;</td>
        <td>{{$gaji != null ? $gaji->name : 0}}</td>
    </tr>
</table>
<table id="table">
    <thead>
        <tr>
            <td id="table_th">Tipe</td>
            <th id="table_th" align="right">Fee</td>
            <th id="table_th" align="center">Kehadiran</td>
            <th id="table_th" align="right">sub total</td>
        </tr>
        <tr>
            <td id="table_th">Gaji Pokok</td>
            <td id="table_th" align="right">Rp. {{formatCurrency($gaji->gaji_pokok)}}</td>
            <td id="table_th" colspan="2" align="right">@if($gaji->gaji_pokok != $gaji_pokok) (Setelah Potongaan) @endif Rp. {{formatCurrency($gaji_pokok)}}</td>
        </tr>
        <tr>
            <td id="table_th">Total Uang Makan</td>
            <td id="table_th" align="right">Rp. {{formatCurrency($gaji->uang_makan)}}</td>
            <td id="table_th" align="center">{{$kehadiran}}</td>
            <td id="table_th" align="right">Rp. {{formatCurrency($total_uang_makan)}}</td>
        </tr>
        <tr>
            <td id="table_th">Total Uang Transport</td>
            <td id="table_th" align="right">Rp. {{formatCurrency($gaji->uang_transport)}}</td>
            <td id="table_th" align="center">{{$kehadiran}}</td>
            <td id="table_th" align="right">Rp. {{formatCurrency($total_uang_transport)}}</td>
        </tr>
        <tr>
            <td id="table_th">Bonus Disiplin (per Minggu)</td>
            <td id="table_th" align="right">Rp. {{formatCurrency($gaji_pokok * (2.5/100))}}</td>
            <td id="table_th" align="center">{{$total_bonus_disiplin}}</td>
            <td id="table_th" align="right">Rp. {{formatCurrency($bonus_disiplin)}}</td>
        </tr>
        <tr class="table-danger">
            <td id="table_th">Denda</td>
            <td id="table_th" align="right"></td>
            <td id="table_th" align="center"></td>
            <td id="table_th" align="right">Rp. {{formatCurrency($jumlah_denda)}}</td>
        </tr>
        <tr>
            <th id="table_th" colspan="3" align="center">Total</td>
            <th id="table_th" align="right">Rp. {{formatCurrency($grand_total)}}</td>
        </tr>
    </thead>
</table>
<h4 class="title" style="color:black">Detail Denda</h4>
<table border="1px solid black" style="border-collapse:collapse" width="100%">
    <thead>
        <tr>
            <td>Tanggal</td>
            <td>Alasan</td>
            <th align="center">Denda</td>
        </tr>
    </thead>
    <tbody>
        @php
        foreach ($detail as $key => $value) {
            // if ($value['denda'] > 0) {
        @endphp
        <tr>
            <td>{{formatTanggal($value['tanggal'])}}</td>
            <td>{{$value['alasan_denda']}}</td>
            <td align="right">Rp. {{formatCurrency($value['denda'])}}</td>
        </tr>
        @php
            // }
        }
        @endphp
        <tr>
            <th align="center" colspan="2">Total Denda</td>
            <th align="right">Rp. {{formatCurrency($jumlah_denda)}}</td>
        </tr>
    </tbody>
</table>
<script type="text/javascript" src="https://code.jquery.com/jquery-3.3.1.js"></script>



