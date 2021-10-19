<!DOCTYPE html>
<html>
    
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Print SPU</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- <link rel="stylesheet" type="text/css" media="screen" href="main.css"> -->
    <!-- <script src="main.js"></script> -->
</head>
    <body class="page" onload="window.print()">
    <!-- <body class="page" > -->
    <!-- Title -->
    <table class="table">
        <tr>
            <td width="50%" class="text-center"><img src="{!! asset('theme/assets/images/img_01.png') !!}" alt="logo" class="dark-logo" /></td>
        </tr>
        <tr>
            <td width="50%" class="title-print text-center"><b><u>PERJANJIAN SEMENTARA JUAL BELI</b></u></td>
        </tr>
        <tr>
            <td width="50%" class="title-print text-center"><b><u>{{ $header['no'] }}</b></u></td>
        </tr>
        <tr height="19" style="height:14.25pt">
            <td width="50%" class="text-center">Telah Diterima dari Calon Pembeli</td>
        </tr>
    </table>
    <!-- Content Title -->   
    <table border="0" cellpadding="0" cellspacing="0" width="803" style="border-collapse: collapse;table-layout:fixed;width:603pt">
        <colgroup>
            <col class="xl90" width="34" style="mso-width-source:userset;mso-width-alt:1243; width:26pt">
            <col class="xl90" width="22" style="mso-width-source:userset;mso-width-alt:804; width:17pt">
            <col class="xl67" width="149" style="mso-width-source:userset;mso-width-alt:5449; width:112pt">
            <col class="xl67" width="163" style="mso-width-source:userset;mso-width-alt:5961; width:122pt">
            <col class="xl67" width="31" style="mso-width-source:userset;mso-width-alt:1133; width:23pt">
            <col class="xl67" width="276" style="mso-width-source:userset;mso-width-alt:10093; width:207pt">
            <col class="xl67" width="64" style="width:48pt">
            <col class="xl67" width="64" style="width:48pt">
        </colgroup>
        <tbody>
            
 <tr height="19" style="height:14.25pt">
  <td height="19" class="xl89" style="height:14.25pt"></td>
  <td class="xl89"></td>
  <td class="xl73"></td>
  <td class="xl73"></td>
  <td class="xl73"></td>
  <td class="xl73"></td>
  <td class="xl67"></td>
  <td class="xl67"></td>
 </tr>
 <tr height="19" style="height:14.25pt">
  <td height="19" class="xl90" style="height:14.25pt"></td>
  <td class="xl90"></td>
  <td colspan="2" class="xl87"><span lang="SV" style="line-height:150%;mso-ansi-language:
  SV">Pada tanggal</span></td>
  <td class="xl66">:</td>
  <td class="xl67">{{ $header['spu_date'] }}</td>
  <td class="xl67"></td>
  <td class="xl67"></td>
 </tr>
 <tr height="19" style="height:14.25pt">
  <td height="19" class="xl90" style="height:14.25pt"></td>
  <td class="xl90"></td>
  <td colspan="2" class="xl87"><span lang="SV" style="line-height:150%;mso-ansi-language:
  SV">Nama Pemesan</span></td>
  <td class="xl66">:</td>
  <td class="xl67">{{ $header['customer_name'] }}</td>
  <td class="xl67"></td>
  <td class="xl67"></td>
 </tr>
 <tr height="19" style="height:14.25pt">
  <td height="19" class="xl90" style="height:14.25pt"></td>
  <td class="xl90"></td>
  <td colspan="2" class="xl87"><span lang="SV" style="line-height:150%;mso-ansi-language:
  SV">Tempat / Tanggal Lahir</span></td>
  <td class="xl66">:</td>
  <td class="xl67">{{ $header['birth_place']
  . '/'. $header['birth_date']}}</td>
  <td class="xl67"></td>
 </tr>
 <tr height="19" style="height:14.25pt">
  <td height="19" class="xl90" style="height:14.25pt"></td>
  <td class="xl90"></td>
  <td colspan="2" class="xl87"><span lang="SV" style="line-height:150%;mso-ansi-language:
  SV">Nama dalam Sertifikat / PPJB</span></td>
  <td class="xl66">:</td>
  <td class="xl67">{{ $header['ppjb_name'] }}</td>
  <td class="xl67"></td>
  <td class="xl67"></td>
 </tr>
 <tr height="19" style="height:14.25pt">
  <td height="19" class="xl90" style="height:14.25pt"></td>
  <td class="xl90"></td>
  <td colspan="2" class="xl87" style="text-align:center left"><span lang="SV" style="line-height:150%;mso-ansi-language:
  SV">Alamat lengkap sesuai KTP</span></td>
  <td class="xl66">:</td>
  @if($header['address_spu'] != null)
  <td colspan="6" class="xl94" width="705" style="width:529pt; font-size:14px; padding:5px 0"><span lang="SV">{{$header['address_spu'].', RT '.$header['legal_rt'].' RW '.$header['legal_rw'].', Kelurahan '.$header['legal_kelurahan'].', Kecamatan '.$header['legal_kecamatan'].', Kota '.$header['legal_city'].', Kode Pos '.$header['legal_zipcode']}}</span></td>
  @else
  <td colspan="6" class="xl94" width="705" style="width:529pt; font-size:14px; padding:5px 0"><span lang="SV">{{ $header['address_ktp'].', RT '.$header['residence_rt'].' RW '.$header['residence_rw'].', Kelurahan '.$header['residence_kelurahan'].', Kecamatan '.$header['residence_kecamatan'].', Kota '.$header['residence_city'].', Kode Pos '.$header['residence_zipcode']}}</span></td>
  @endif
  <td class="xl67"></td>
  <td class="xl67"></td>
 </tr>
 <tr height="19" style="height:14.25pt">
  <td height="19" class="xl90" style="height:14.25pt"></td>
  <td class="xl90"></td>
  <td colspan="2" class="xl87" style="text-align:center left"><span lang="EN-US" style="line-height:150%">Alamat
  surat-menyurat</span></td>
  <td class="xl66"><span lang="SV" style="line-height:150%;mso-ansi-language:
  SV">:</span></td>
  @if($header['address_spu'] != null)
  <td colspan="6" class="xl94" width="705" style="width:529pt; font-size:14px; padding:5px 0"><span lang="SV">{{$header['address_spu'].', RT '.$header['legal_rt'].' RW '.$header['legal_rw'].', Kelurahan '.$header['legal_kelurahan'].', Kecamatan '.$header['legal_kecamatan'].', Kota '.$header['legal_city'].', Kode Pos '.$header['legal_zipcode']}}</span></td>
  @else
  <td colspan="6" class="xl94" width="705" style="width:529pt; font-size:14px; padding:5px 0"><span lang="SV">{{ $header['address_ktp'].', RT '.$header['residence_rt'].' RW '.$header['residence_rw'].', Kelurahan '.$header['residence_kelurahan'].', Kecamatan '.$header['residence_kecamatan'].', Kota '.$header['residence_city'].', Kode Pos '.$header['residence_zipcode']}}</span></td>
  @endif
  <td class="xl67"></td>
  <td class="xl67"></td>
 </tr>
 <tr height="19" style="height:14.25pt">
  <td height="19" class="xl90" style="height:14.25pt"></td>
  <td class="xl90"></td>
  <td colspan="2" class="xl87"><span lang="SV" style="line-height:150%;mso-ansi-language:
  SV">Nomor Telp Rumah</span></td>
  <td class="xl66">:</td>
  <td class="xl67">{{ $header['phone_no'] }}</td>
  <td class="xl67"></td>
  <td class="xl67"></td>
 </tr>
 <tr height="19" style="height:14.25pt">
  <td height="19" class="xl90" style="height:14.25pt"></td>
  <td class="xl90"></td>
  <td colspan="2" class="xl87"><span lang="SV" style="line-height:150%;mso-ansi-language:SV">Nomor Handphone</span></td>
  <td class="xl66">:</td>
  <td class="xl67">{{ $header['phone_no'] }}</td>
  <td class="xl67"></td>
  <td class="xl67"></td>
 </tr>
 <tr height="19" style="height:14.25pt">
  <td height="19" class="xl90" style="height:14.25pt"></td>
  <td class="xl90"></td>
  <td colspan="2" class="xl87"><span lang="SV" style="line-height:150%;mso-ansi-language:SV">Nomor KTP / PASPOR / SIM</span></td>
  <td class="xl66">:</td>
  <td class="xl67">{{ $header['id_no'] }}</td>
  <td class="xl67"></td>
  <td class="xl67"></td>
 </tr>
 <tr height="19" style="height:14.25pt">
  <td height="19" class="xl90" style="height:14.25pt"></td>
  <td class="xl90"></td>
  <td class="xl66"></td>
  <td class="xl66"></td>
  <td class="xl66"></td>
  <td class="xl67"></td>
  <td class="xl67"></td>
  <td class="xl67"></td>
 </tr>
 <tr height="21" style="height:15.75pt">
  <td height="21" class="xl90" style="height:15.75pt"></td>
  <td class="xl90"></td>
  <td colspan="4" class="xl79"><span lang="SV" style="mso-ansi-language:SV">Uang
  sebesar<font class="font9"> Rp {{number_format(
  $header['booking_fee'],2,',','.') }}</font></span></td>
  <td class="xl67"></td>
  <td class="xl67"></td>
 </tr>
 <tr height="20" style="height:15.0pt">
  <td height="20" class="xl90" style="height:15.0pt"></td>
  <td class="xl90"></td>
  <td colspan="4" class="xl80"></td>
  <td class="xl67"></td>
  <td class="xl67"></td>
 </tr>
 <tr height="20" style="mso-height-source:userset;height:15.0pt">
  <td colspan="6" height="20" class="xl65" style="height:15.0pt"><span lang="SV" style="line-height:150%;mso-ansi-language:SV">Guna membayar Pesanan Kavling
  untuk Pembelian Rumah</span></td>
  <td class="xl67"></td>
  <td class="xl67"></td>
 </tr>
 <tr height="20" style="mso-height-source:userset;height:15.0pt">
  <td height="20" class="xl91" style="height:15.0pt"></td>
  <td class="xl91"></td>
  <td class="xl65"></td>
  <td class="xl65"></td>
  <td class="xl65"></td>
  <td class="xl65"></td>
  <td class="xl67"></td>
  <td class="xl67"></td>
 </tr>
 <tr height="19" style="height:14.25pt">
  <td height="19" class="xl90" style="height:14.25pt"></td>
  <td class="xl90"></td>
  <td colspan="2" class="xl87"><span lang="SV" style="line-height:150%;mso-ansi-language:
  SV">Nama Perumahan</span></td>
  <td class="xl66">:</td>
  <td class="xl71">{{ $header['site_name'] }}</td>
  <td class="xl67"></td>
  <td class="xl67"></td>
 </tr>
 <tr height="19" style="height:14.25pt">
  <td height="19" class="xl90" style="height:14.25pt"></td>
  <td class="xl90"></td>
  <td colspan="2" class="xl87"><span lang="SV" style="line-height:150%;mso-ansi-language:
  SV">Nomor Kavling</span></td>
  <td class="xl66">:</td>
  <td class="xl71">{{ $header['kavling_no'] }}</td>
  <td class="xl67"></td>
  <td class="xl67"></td>
 </tr>
 <tr height="19" style="height:14.25pt">
  <td height="19" class="xl90" style="height:14.25pt"></td>
  <td class="xl90"></td>
  <td colspan="2" class="xl87"><span lang="SV" style="line-height:150%;mso-ansi-language:
  SV">Type standart ( Lb / Lt )</span></td>
  <td class="xl66">:</td>
  <td class="xl71">{{ $header['base_area'] }}</td>
  <td class="xl67"></td>
  <td class="xl67"></td>
 </tr>
 <tr height="19" style="height:14.25pt">
  <td height="19" class="xl90" style="height:14.25pt"></td>
  <td class="xl90"></td>
  <td colspan="2" class="xl87"><span lang="SV" style="line-height:150%;mso-ansi-language:
  SV">Type Kesepakatan</span></td>
  <td class="xl66">:</td>
  <td class="xl71">{{ $header['base_area'] }}</td>
  <td class="xl67"></td>
  <td class="xl67"></td>
 </tr>
 <tr height="19" style="height:14.25pt">
  <td height="19" class="xl90" style="height:14.25pt"></td>
  <td class="xl90"></td>
  <td class="xl66"></td>
  <td class="xl66"></td>
  <td class="xl67"></td>
  <td class="xl67"></td>
  <td class="xl67"></td>
  <td class="xl67"></td>
 </tr>
 <tr height="20" style="height:15.0pt">
  <td height="20" class="xl90" style="height:15.0pt"></td>
  <td class="xl90"></td>
  <td class="xl66"></td>
  <td class="xl66"></td>
  <td class="xl67"></td>
  <td class="xl67"></td>
  <td class="xl67"></td>
  <td class="xl67"></td>
 </tr>
 <tr height="20" style="height:15.0pt">
  <td height="20" class="xl90" style="height:15.0pt"></td>
  <td class="xl90"></td>
  <td colspan="2" class="xl100" width="312" style="border-right:1.0pt solid black;
  width:234pt"><span lang="SV">1.&nbsp;&nbsp;&nbsp; Harga Property (tanah dan
  bangunan)</span></td>
  <td class="xl83" width="31" style="border-left:none;width:23pt"><span lang="SV">Rp.</span></td>
  <td class="xl98" width="276" style="width:207pt">{{ number_format(
  $header['base_price'] ,2,',','.') }}</td>
  <td class="xl67"></td>
  <td class="xl67"></td>
 </tr>
 <tr height="20" style="height:15.0pt">
  <td height="20" class="xl90" style="height:15.0pt"></td>
  <td class="xl90"></td>
  <td colspan="2" class="xl100" width="312" style="border-right:1.0pt solid black;
  width:234pt"><span lang="SV">2.&nbsp;&nbsp;&nbsp; PPN</span></td>
  <td class="xl83" width="31" style="border-top:none;border-left:none;width:23pt"><span lang="SV">Rp.</span></td>
  <td class="xl98" width="276" style="border-top:none;width:207pt">{{
  number_format( $header['ppn_amount'] ,2,',','.') }}</td>
  <td class="xl67"></td>
  <td class="xl67"></td>
 </tr>
 <tr height="20" style="height:15.0pt">
  <td height="20" class="xl90" style="height:15.0pt"></td>
  <td class="xl90"></td>
  <td colspan="2" class="xl100" width="312" style="border-right:1.0pt solid black;
  width:234pt"><span lang="SV">3.&nbsp;&nbsp;&nbsp; BPHTB</span></td>
  <td class="xl83" width="31" style="border-top:none;border-left:none;width:23pt">Rp.</td>
  <td class="xl98" width="276" style="border-top:none;width:207pt">{{
  number_format( $header['pbhtb_amount'],2,',','.') }}</td>
  <td class="xl67"></td>
  <td class="xl67"></td>
 </tr>
 <tr height="21" style="mso-height-source:userset;height:15.75pt">
  <td height="21" class="xl90" style="height:15.75pt"></td>
  <td class="xl90"></td>
  <td colspan="2" class="xl100" width="312" style="border-right:1.0pt solid black;
  width:234pt"><span lang="SV">4.&nbsp;&nbsp;&nbsp; Biaya pengadaan FASUM</span></td>
  <td class="xl83" width="31" style="border-top:none;border-left:none;width:23pt">Rp.</td>
  <td class="xl98" width="276" style="border-top:none;width:207pt">{{
  number_format( $header['fasum_fee'] ,2,',','.') }}</td>
  <td class="xl67"></td>
  <td class="xl67"></td>
 </tr>
 <tr height="20" style="height:15.0pt">
  <td height="20" class="xl90" style="height:15.0pt"></td>
  <td class="xl90"></td>
  <td colspan="2" class="xl100" width="312" style="border-right:1.0pt solid black;
  width:234pt"><span lang="SV">5.&nbsp;&nbsp;&nbsp; Biaya Notaris</span></td>
  <td class="xl83" width="31" style="border-top:none;border-left:none;width:23pt">Rp.</td>
  <td class="xl98" width="276" style="border-top:none;width:207pt">{{
  number_format( $header['notary_fee'],2,',','.') }}</td>
  <td class="xl67"></td>
  <td class="xl67"></td>
 </tr>
 <tr height="20" style="height:15.0pt">
  <td height="20" class="xl90" style="height:15.0pt"></td>
  <td class="xl90"></td>
  <td colspan="2" class="xl100" width="312" style="border-right:1.0pt solid black;
  width:234pt"><span lang="SV">6.&nbsp;&nbsp;&nbsp; Spec Up</span></td>
  <td class="xl83" width="31" style="border-top:none;border-left:none;width:23pt">Rp.</td>
  <td class="xl98" width="276" style="border-top:none;width:207pt">{{
  number_format( $header['specup_amount'],2,',','.') }}</td>
  <td class="xl67"></td>
  <td class="xl67"></td>
 </tr>
 <tr height="20" style="height:15.0pt">
  <td height="20" class="xl90" style="height:15.0pt"></td>
  <td class="xl90"></td>
  <td colspan="2" class="xl100" width="312" style="border-right:1.0pt solid black;
  width:234pt"><span lang="SV">6.&nbsp;&nbsp;&nbsp; discount</span></td>
  <td class="xl83" width="31" style="border-top:none;border-left:none;width:23pt">Rp.</td>
  <td class="xl98" width="276" style="border-top:none;width:207pt">{{
  number_format( $header['total_discount'],2,',','.') }}</td>
  <td class="xl67"></td>
  <td class="xl67"></td>
 </tr>
 <tr height="19" style="height:14.25pt">
  <td height="19" class="xl90" style="height:14.25pt"></td>
  <td class="xl90"></td>
  <td class="xl66"></td>
  <td class="xl66"></td>
  <td class="xl67"></td>
  <td class="xl67"></td>
  <td class="xl67"></td>
  <td class="xl67"></td>
 </tr>
 <tr height="19" style="height:14.25pt">
  <td height="19" class="xl90" style="height:14.25pt"></td>
  <td class="xl90"></td>
  <td class="xl66"><span lang="SV" style="line-height:150%;mso-ansi-language:SV">Total
  nilai pembayaran</span></td>
  <td class="xl66"></td>
  <td class="xl66">Rp:</td>
  <td class="xl99">{{ number_format( $header['total_price'],2,',','.') }}</td>
  <td class="xl67"></td>
  <td class="xl67"></td>
 </tr>
 <tr height="19" style="height:14.25pt">
  <td height="19" class="xl90" style="height:14.25pt"></td>
  <td class="xl90"></td>
  <td class="xl68"></td>
  <td class="xl68"></td>
  <td class="xl66"></td>
  <td class="xl67"></td>
  <td class="xl67"></td>
  <td class="xl67"></td>
 </tr>
 <tr height="19" style="height:14.25pt">
  <td height="19" class="xl90" style="height:14.25pt"></td>
  <td class="xl90"></td>
  <td class="xl69"></td>
  <td class="xl69"></td>
  <td class="xl67"></td>
  <td class="xl67"></td>
  <td class="xl67"></td>
  <td class="xl67"></td>
 </tr>
 <tr height="19" style="height:14.25pt">
  <td colspan="6" height="19" class="xl81" style="height:14.25pt"><span lang="SV" style="line-height:150%;mso-ansi-language:SV">Catatan :</span></td>
  <td class="xl67"></td>
  <td class="xl67"></td>
 </tr>
 <tr height="38" style="mso-height-source:userset;height:28.5pt">
  <td height="38" class="xl92" style="height:28.5pt">A. </td>
  <td colspan="6" class="xl97" width="705" style="width:529pt"><span lang="EN-US" style="line-height:150%">Apabila ada kurang bayar karena suatu hal apapun
  yang terkait dengan perpajakan (PPN &amp; BPHTB), akan menjadi tanggungan
  Pembeli dan sanggup untuk melunasinya.</span></td>
  <td class="xl67"></td>
 </tr>
 <tr height="19" style="height:14.25pt">
  <td colspan="3" height="19" class="xl81" style="height:14.25pt"><span lang="SV" style="line-height:150%;mso-ansi-language:SV">Catatan :</span></td>
  <td class="xl81"></td>
  <td class="xl86"></td>
  <td class="xl86"></td>
  <td class="xl67"></td>
  <td class="xl67"></td>
 </tr>
 <tr height="36" style="mso-height-source:userset;height:27.0pt">
  <td height="36" class="xl92" style="height:27.0pt">A. </td>
  <td colspan="6" class="xl97" width="705" style="width:529pt"><span lang="EN-US" style="line-height:150%">Apabila ada kurang bayar karena suatu hal apapun
  yang terkait dengan perpajakan (PPN &amp; BPHTB), akan menjadi tanggungan
  Pembeli dan sanggup untuk melunasinya.</span></td>
  <td class="xl67"></td>
 </tr>
 <tr height="52" style="mso-height-source:userset;height:39.0pt">
  <td height="52" class="xl92" style="height:39.0pt">B.</td>
  <td colspan="6" class="xl97" width="705" style="width:529pt"><span lang="SV" style="line-height:150%;mso-ansi-language:SV">Bilamana akan ada peningkatan
  spesifikasi / penambahan bangunan, maka Harga Kesepakatan berikut Penambahan
  akan dicantumkan dalam dokumen Perjanjian Pendahuluan Jual Beli (PPJB) yang
  akan dibuat kemudian.</span></td>
  <td class="xl67"></td>
 </tr>
 <tr height="19" style="mso-height-source:userset;height:14.25pt">
  <td height="19" class="xl92" style="height:14.25pt">C.</td>
  <td colspan="6" class="xl97" width="705" style="width:529pt"><span lang="SV" style="line-height:150%;mso-ansi-language:SV">Lampiran PSJB adalah copy
  identitas KTP / PASPOR / SIM yang masih berlaku.</span></td>
  <td class="xl67"></td>
 </tr>
 <tr height="19" style="height:14.25pt">
  <td height="19" class="xl90" style="height:14.25pt"></td>
  <td class="xl90"></td>
  <td class="xl68"></td>
  <td class="xl68"></td>
  <td class="xl67"></td>
  <td class="xl67"></td>
  <td class="xl67"></td>
  <td class="xl67"></td>
 </tr>
 <tr height="20" style="mso-height-source:userset;height:15.0pt">
  <td colspan="6" height="20" class="xl65" style="height:15.0pt"><span lang="EN-US" style="line-height:150%">Jadwal Pembayaran</span></td>
  <td class="xl67"></td>
  <td class="xl67"></td>
 </tr>
 <tr height="20" style="height:15.0pt">
  <td height="20" class="xl93" style="height:15.0pt"><span lang="EN-US" style="line-height:150%">No.</span></td>
  <td class="xl93"></td>
  <td class="xl70">Tahap Pembayaran</td>
  <td class="xl84">Tanggal Pembayaran</td>
  <td colspan="2" class="xl84">Jumlah Pembayaran</td>
  <td class="xl67"></td>
  <td class="xl67"></td>
 </tr>

 
@if($detail != null)
    @for($i = 0; $i < count($detail); $i++)
    <tr height="19" style="height:14.25pt">
        <td height="19" class="xl93" style="height:14.25pt"><span lang="EN-US" style="line-height:150%"> {{$i+1}} </span></td>
        <td class="xl93"></td>
        <td class="xl69">{{ $detail[$i]['tahap_bayar'] }}<span lang="EN-US" style="line-height:
        150%"></span></td>
        <td class="xl73">{{ $detail[$i]['due_date'] }}</td>
        <td class="xl66">: Rp</td>
        <td class="xl67">{{ number_format( $detail[$i]['amount'] ,2,',','.') }}</td>
        <td class="xl67"></td>
        <td class="xl67"></td>
    </tr>
    @endfor
@endif
 <tr height="19" style="page-break-before:always;height:14.25pt">
  <td height="19" class="xl90" style="height:14.25pt"></td>
  <td class="xl90"></td>
  <td class="xl66"></td>
  <td class="xl67"></td>
  <td class="xl67"></td>
  <td class="xl67"></td>
  <td class="xl67"></td>
  <td class="xl67"></td>
 </tr>
 <tr height="19" style="height:14.25pt">
  <td height="19" class="xl90" style="height:14.25pt"></td>
  <td class="xl90"></td>
  <td colspan="2" class="xl81"><span lang="EN-US" style="line-height:150%">Total
  nilai pembayaran</span><span lang="EN-US" style="line-height:150%"></span></td>
  <td class="xl66">: Rp</td>
  <td class="xl67">{{ number_format( $detail_total ,2,',','.') }}</td>
  <td class="xl67"></td>
  <td class="xl67"></td>
 </tr>
 <tr height="19" style="height:14.25pt">
  <td height="19" class="xl90" style="height:14.25pt"></td>
  <td class="xl90"></td>
  <td class="xl66"></td>
  <td class="xl66"></td>
  <td class="xl67"></td>
  <td class="xl67"></td>
  <td class="xl66"></td>
  <td class="xl67"></td>
 </tr>
 <tr height="21" style="height:15.75pt">
  <td height="21" class="xl90" style="height:15.75pt"></td>
  <td class="xl90"></td>
  <td colspan="2" class="xl88"><span lang="SV" style="mso-ansi-language:SV">Dengan
  Syarat dan Ketentuan :</span></td>
  <td class="xl67"></td>
  <td class="xl67"></td>
  <td class="xl67"></td>
  <td class="xl67"></td>
 </tr>
 <tr height="56" style="mso-height-source:userset;height:42.0pt">
  <td height="56" class="xl95" style="height:42.0pt">1</td>
  <td colspan="6" class="xl94" width="705" style="width:529pt"><span lang="SV">Uang
  tanda jadi atau Booking Fee yang telah dibayarkan, <font class="font15">mengikat
  kavling</font><font class="font7"> yang dipilih oleh Calon Pembeli dan </font><font class="font15">mengikat harga</font><font class="font7"> unit kavling
  tersebut, </font><font class="font15">dengan ketentuan tidak ada
  keterlambatan</font><font class="font7"> dari jadwal pembayaran tersebut di
  atas.</font></span></td>
  <td class="xl67"></td>
 </tr>
 <tr height="40" style="mso-height-source:userset;height:30.0pt">
  <td height="40" class="xl95" style="height:30.0pt">2</td>
  <td colspan="6" class="xl94" width="705" style="width:529pt"><span lang="SV">Apabila
  pernah terjadi keterlambatan, maka harga tersebut di atas tidak mengikat, dan
  bisa berubah sewaktu-waktu sesuai ketentuan harga yang dikeluarkan Developer.</span></td>
  <td class="xl67"></td>
 </tr>
 <tr height="54" style="mso-height-source:userset;height:40.5pt">
  <td height="54" class="xl95" style="height:40.5pt">3</td>
  <td colspan="6" class="xl94" width="705" style="width:529pt"><span lang="SV">Dan
  harga Property dinyatakan mengikat apabila sudah dinyatakan mencapai 30% dari
  Total nilai pembayaran (harga property + PPN + BPHTB + Biaya pengadaan Fasum
  + Bea Notaris + Spec Up) dengan perhitungan harga property terbaru yang
  berlaku.</span></td>
  <td class="xl67"></td>
 </tr>
 <tr height="40" style="mso-height-source:userset;height:30.0pt">
  <td height="40" class="xl95" style="height:30.0pt">4</td>
  <td colspan="6" class="xl94" width="705" style="width:529pt"><span lang="SV">Pembayaran
  1 harus dibayarkan selambat-lambatnya 14 ( empat belas ) hari semenjak
  tanggal pembayaran tanda jadi.</span></td>
  <td class="xl67"></td>
 </tr>
 <tr height="40" style="mso-height-source:userset;height:30.0pt">
  <td height="40" class="xl95" style="height:30.0pt">5</td>
  <td colspan="6" class="xl94" width="705" style="width:529pt"><span lang="SV">Dalam
  jangka waktu selambat-lambatnya 30 ( tiga puluh ) hari sejak tanggal
  pembayaran tanda jadi, pihak calon pembeli setuju dan mufakat untuk :</span></td>
  <td class="xl67"></td>
 </tr>
 <tr height="54" style="mso-height-source:userset;height:40.5pt">
  <td height="54" class="xl95" style="height:40.5pt"></td>
  <td class="xl96">a.</td>
  <td colspan="5" class="xl94" width="683" style="width:512pt"><span lang="SV">Kesepakatan
  gambar kerja bangunan atau gambar perubahan bentuk atau perubahan spesifikasi
  bangunan, yang mana dokumen tersebut akan dilampirkan dalam <font class="font12">BOOKING FEE</font><font class="font7"> yang akan
  ditandatangani.</font></span></td>
  <td class="xl67"></td>
 </tr>
 <tr height="53" style="mso-height-source:userset;height:39.75pt">
  <td height="53" class="xl95" style="height:39.75pt"></td>
  <td class="xl96">b.</td>
  <td colspan="5" class="xl94" width="683" style="width:512pt"><span lang="SV">Apabila
  realisasi pembayaran calon pembeli tidak sesuai dengan jadwal tersebut
  diatas, maka letak kavling dan harga rumah ditentukan menyesuaikan dengan
  kavling yang ada dengan harga dan tata cara pembayaran terbaru yang berlaku.</span></td>
  <td class="xl67"></td>
 </tr>
 <tr height="70" style="mso-height-source:userset;height:52.5pt">
  <td height="70" class="xl95" style="height:52.5pt">6</td>
  <td colspan="6" class="xl94" width="705" style="width:529pt"><span lang="SV">Pembagunan
  fisik rumah akan dimulai selambat-lambatnya 30 (tiga puluh) hari sejak
  diterbitkannya PPJB yang disepakati para pihak dan melihat kesiapan di
  lapangan, dengan ketentuan pembayaran telah mencapai 30% dari harga rumah
  kesepakatan dan pihak bank pemberi kredit telah mensetujui pembiayaan KPR
  calon Pembeli <font class="font12">(bila melalui pembiayaan bank)</font><font class="font7">.<span style="mso-spacerun:yes">&nbsp;</span></font></span></td>
  <td class="xl67"></td>
 </tr>
 <tr height="40" style="mso-height-source:userset;height:30.0pt">
  <td height="40" class="xl95" style="height:30.0pt">7</td>
  <td colspan="6" class="xl94" width="705" style="width:529pt"><span lang="EN-US" style="line-height:150%;mso-fareast-font-family:Arial">Untuk pembelian
  melalui fasilitas pembiayaan bank pemberi kredit (KPR), maka pihak calon
  pembeli setuju dan mufakat untuk :</span></td>
  <td class="xl67"></td>
 </tr>
 <tr height="40" style="mso-height-source:userset;height:30.0pt">
  <td height="40" class="xl95" style="height:30.0pt"></td>
  <td class="xl96">a.</td>
  <td colspan="5" class="xl94" width="683" style="width:512pt"><span lang="EN-US" style="mso-bidi-font-family:Wingdings;mso-fareast-font-family:Wingdings">Melengkapi
  semua persyaratan KPR selambat-lambatnya 14 hari sebelum <font class="font12">BOOKING
  FEE</font><font class="font7"> ditandatangani agar pengurusan KPR dapat
  dijalankan oleh pihak developer.</font></span></td>
  <td class="xl67"></td>
 </tr>
 <tr height="19" style="mso-height-source:userset;height:14.25pt">
  <td height="19" class="xl95" style="height:14.25pt"></td>
  <td class="xl96">b.</td>
  <td colspan="5" class="xl94" width="683" style="width:512pt"><span lang="SV">Menerima
  pengajuan bank pemberi kredit yang menjadi mitra pihak developer.</span></td>
  <td class="xl67"></td>
 </tr>
 <tr height="51" style="mso-height-source:userset;height:38.25pt">
  <td height="51" class="xl95" style="height:38.25pt"></td>
  <td class="xl96">c.</td>
  <td colspan="5" class="xl94" width="683" style="width:512pt"><span lang="SV">Apabila
  bank pemberi kredit menganggap pihak konsumen tidak layak untuk dibiayai /
  terjadi penurunan plafon pinjaman, maka secara otomatis calon pembeli sanggup
  dan bersedia untuk melakukan pembayaran secara bertahap.</span></td>
  <td class="xl67"></td>
 </tr>
 <tr height="54" style="mso-height-source:userset;height:40.5pt">
  <td height="54" class="xl95" style="height:40.5pt">8</td>
  <td colspan="6" class="xl94" width="705" style="width:529pt"><span lang="EN-US" style="line-height:150%;mso-fareast-font-family:Arial">Calon pembeli setuju
  dan sepakat mengundurkan diri dan pesanan kavling menjadi batal dengan
  sendirinya apabila Pembeli cedera janji / wanprestasi atas ketentuan waktu
  pada poin no. 4, no. 5, dan no. 7 di atas dan uang tanda jadi atau booking
  fee <font class="font15">tidak dapat diambil / dikembalikan.</font></span></td>
  <td class="xl67"></td>
 </tr>
 <tr height="35" style="mso-height-source:userset;height:26.25pt">
  <td height="35" class="xl95" style="height:26.25pt">9</td>
  <td colspan="6" class="xl94" width="705" style="width:529pt"><span lang="SV">untuk
  ketentuan-ketentuan lain yang tidak diatur dalam surat Perjanjian Sementara
  Jual Beli ini akan diatur dalam <font class="font12">BOOKING FEE</font><font class="font7"> yang akan ditandatangani bersama.</font></span></td>
  <td class="xl67"></td>
 </tr>
 <tr height="36" style="mso-height-source:userset;height:27.0pt">
  <td colspan="7" height="36" class="xl85" width="739" style="height:27.0pt;width:555pt"><span lang="SV" style="line-height:150%;mso-ansi-language:SV">Surat Perjanjian
  Sementara Jual Beli (PSJB) ini dibuat rangkap 2 dan memiliki kekuatan hukum
  yang sama serta sebagai alat bukti pembayaran yang sah.</span></td>
  <td class="xl67"></td>
 </tr>
 <tr height="19" style="height:14.25pt">
  <td height="19" class="xl90" style="height:14.25pt"></td>
  <td class="xl90"></td>
  <td class="xl72"></td>
  <td class="xl72"></td>
  <td class="xl67"></td>
  <td class="xl67"></td>
  <td class="xl67"></td>
  <td class="xl67"></td>
 </tr>
 <tr height="19" style="height:14.25pt">
  <td height="19" class="xl90" style="height:14.25pt"></td>
  <td class="xl90"></td>
  <td colspan="3" class="xl89"><span lang="EN-US" style="line-height:150%">Tulungagung,</span></td>
  <td class="xl78">....................</td>
  <td class="xl67"></td>
  <td class="xl67"></td>
 </tr>
 <tr height="19" style="height:14.25pt">
  <td height="19" class="xl90" style="height:14.25pt"></td>
  <td class="xl90"></td>
  <td class="xl74"><span lang="SV" style="mso-ansi-language:SV">Menyetujui,</span></td>
  <td class="xl74"></td>
  <td class="xl67"></td>
  <td class="xl67"></td>
  <td class="xl67"></td>
  <td class="xl67"></td>
 </tr>
 <tr height="19" style="height:14.25pt">
  <td height="19" class="xl90" style="height:14.25pt"></td>
  <td class="xl90"></td>
  <td class="xl74"><span lang="SV" style="mso-ansi-language:SV">Calon Konsumen</span></td>
  <td class="xl74"></td>
  <td class="xl67"></td>
  <td class="xl74">Property Advisor,</td>
  <td class="xl67"></td>
  <td class="xl67"></td>
 </tr>
 <tr height="19" style="height:14.25pt">
  <td height="19" class="xl90" style="height:14.25pt"></td>
  <td class="xl90"></td>
  <td class="xl74"></td>
  <td class="xl74"></td>
  <td class="xl67"></td>
  <td class="xl67"></td>
  <td class="xl67"></td>
  <td class="xl67"></td>
 </tr>
 <tr height="19" style="height:14.25pt">
  <td height="19" class="xl90" style="height:14.25pt"></td>
  <td class="xl90"></td>
  <td class="xl75"><span lang="SV"><span style="mso-spacerun:yes">&nbsp;&nbsp;</span></span></td>
  <td class="xl75"></td>
  <td class="xl67"></td>
  <td class="xl67"></td>
  <td class="xl67"></td>
  <td class="xl67"></td>
 </tr>
 <tr height="25" style="height:18.75pt">
  <td height="25" class="xl90" style="height:18.75pt"></td>
  <td class="xl90"></td>
  <td class="xl76"></td>
  <td class="xl76"></td>
  <td class="xl67"></td>
  <td class="xl67"></td>
  <td class="xl67"></td>
  <td class="xl67"></td>
 </tr>
 <tr height="19" style="height:14.25pt">
  <td height="19" class="xl90" style="height:14.25pt"></td>
  <td class="xl90"></td>
  <td class="xl77"></td>
  <td class="xl77"></td>
  <td class="xl67"></td>
  <td class="xl67"></td>
  <td class="xl67"></td>
  <td class="xl67"></td>
 </tr>
 <tr height="19" style="height:14.25pt">
  <td height="19" class="xl90" style="height:14.25pt"></td>
  <td class="xl90"></td>
  <td class="xl78"><span lang="SV" style="line-height:150%;mso-ansi-language:SV">.............................</span></td>
  <td class="xl78"></td>
  <td class="xl67"></td>
  <td class="xl78">....................</td>
  <td class="xl67"></td>
  <td class="xl67"></td>
 </tr>
 <tr height="19" style="height:14.25pt">
  <td height="19" class="xl90" style="height:14.25pt"></td>
  <td class="xl90"></td>
  <td class="xl77"></td>
  <td class="xl77"></td>
  <td class="xl67"></td>
  <td class="xl67"></td>
  <td class="xl67"></td>
  <td class="xl67"></td>
 </tr>
 <tr height="19" style="height:14.25pt">
  <td height="19" class="xl90" style="height:14.25pt"></td>
  <td class="xl90"></td>
  <td class="xl77"></td>
  <td class="xl77"></td>
  <td class="xl67"></td>
  <td class="xl67"></td>
  <td class="xl67"></td>
  <td class="xl67"></td>
 </tr>
 <tr height="19" style="height:14.25pt">
  <td height="19" class="xl90" style="height:14.25pt"></td>
  <td class="xl90"></td>
  <td class="xl74"><span lang="SV" style="line-height:150%;mso-ansi-language:SV">Mengetahui,</span></td>
  <td class="xl74"></td>
  <td class="xl67"></td>
  <td class="xl74">Diterima oleh Bagian Keuangan</td>
  <td class="xl67"></td>
  <td class="xl67"></td>
 </tr>
 <tr height="19" style="height:14.25pt">
  <td height="19" class="xl90" style="height:14.25pt"></td>
  <td class="xl90"></td>
  <td class="xl74"><span lang="SV" style="mso-ansi-language:SV">Site Manager,</span></td>
  <td class="xl74"></td>
  <td class="xl67"></td>
  <td class="xl74">Tanggal : ............................</td>
  <td class="xl67"></td>
  <td class="xl67"></td>
 </tr>
 <tr height="19" style="height:14.25pt">
  <td height="19" class="xl90" style="height:14.25pt"></td>
  <td class="xl90"></td>
  <td class="xl77"></td>
  <td class="xl77"></td>
  <td class="xl67"></td>
  <td class="xl67"></td>
  <td class="xl67"></td>
  <td class="xl67"></td>
 </tr>
 <tr height="19" style="height:14.25pt">
  <td height="19" class="xl90" style="height:14.25pt"></td>
  <td class="xl90"></td>
  <td class="xl74"></td>
  <td class="xl74"></td>
  <td class="xl67"></td>
  <td class="xl67"></td>
  <td class="xl67"></td>
  <td class="xl67"></td>
 </tr>
 <tr height="19" style="height:14.25pt">
  <td height="19" class="xl90" style="height:14.25pt"></td>
  <td class="xl90"></td>
  <td class="xl74"></td>
  <td class="xl74"></td>
  <td class="xl67"></td>
  <td class="xl67"></td>
  <td class="xl67"></td>
  <td class="xl67"></td>
 </tr>
 <tr height="19" style="height:14.25pt">
  <td height="19" class="xl90" style="height:14.25pt"></td>
  <td class="xl90"></td>
  <td class="xl74"></td>
  <td class="xl74"></td>
  <td class="xl67"></td>
  <td class="xl67"></td>
  <td class="xl67"></td>
  <td class="xl67"></td>
 </tr>
 <tr height="19" style="height:14.25pt">
  <td height="19" class="xl90" style="height:14.25pt"></td>
  <td class="xl90"></td>
  <td class="xl78"><span lang="SV" style="mso-ansi-language:SV">........................</span><span lang="SV" style="mso-ansi-language:SV"></span></td>
  <td class="xl78"></td>
  <td class="xl67"></td>
  <td class="xl78">.................................</td>
  <td class="xl67"></td>
  <td class="xl67"></td>
 </tr>
 <!--[if supportMisalignedColumns]-->
 <tr height="0" style="display:none">
  <td width="34" style="width:26pt"></td>
  <td width="22" style="width:17pt"></td>
  <td width="149" style="width:112pt"></td>
  <td width="163" style="width:122pt"></td>
  <td width="31" style="width:23pt"></td>
  <td width="276" style="width:207pt"></td>
  <td width="64" style="width:48pt"></td>
  <td width="64" style="width:48pt"></td>
 </tr>
 <!--[endif]-->
</tbody></table>




</body></html>



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
tr
	{mso-height-source:auto;}
col
	{mso-width-source:auto;}
br
	{mso-data-placement:same-cell;}
.style0
	{mso-number-format:General;
	text-align:general;
	vertical-align:bottom;
	white-space:nowrap;
	mso-rotate:0;
	mso-background-source:auto;
	mso-pattern:auto;
	color:black;
	font-size:11.0pt;
	font-weight:400;
	font-style:normal;
	text-decoration:none;
	font-family:Calibri, sans-serif;
	mso-font-charset:0;
	border:none;
	mso-protection:locked visible;
	mso-style-name:Normal;
	mso-style-id:0;}
.font7
	{color:black;
	font-size:10.0pt;
	font-weight:400;
	font-style:normal;
	text-decoration:none;
	font-family:Arial, sans-serif;
	mso-font-charset:0;}
.font9
	{color:black;
	font-size:12.0pt;
	font-weight:700;
	font-style:normal;
	text-decoration:none;
	font-family:Arial, sans-serif;
	mso-font-charset:0;}
.font12
	{color:black;
	font-size:10.0pt;
	font-weight:400;
	font-style:italic;
	text-decoration:none;
	font-family:Arial, sans-serif;
	mso-font-charset:0;}
.font15
	{color:black;
	font-size:10.0pt;
	font-weight:400;
	font-style:normal;
	text-decoration:underline;
	text-underline-style:single;
	font-family:Arial, sans-serif;
	mso-font-charset:0;}
td
	{mso-style-parent:style0;
	padding-top:1px;
	padding-right:1px;
	padding-left:1px;
	mso-ignore:padding;
	color:black;
	font-size:11.0pt;
	font-weight:400;
	font-style:normal;
	text-decoration:none;
	font-family:Calibri, sans-serif;
	mso-font-charset:0;
	mso-number-format:General;
	text-align:general;
	vertical-align:bottom;
	border:none;
	mso-background-source:auto;
	mso-pattern:auto;
	mso-protection:locked visible;
	white-space:nowrap;
	mso-rotate:0;}
.xl65
	{mso-style-parent:style0;
	font-size:14.0pt;
	font-weight:700;
	font-family:Arial, sans-serif;
	mso-font-charset:0;
	text-align:center;
	vertical-align:middle;}
.xl66
	{mso-style-parent:style0;
	font-size:10.0pt;
	font-family:Arial, sans-serif;
	mso-font-charset:0;
	text-align:justify;
	vertical-align:middle;}
.xl67
	{mso-style-parent:style0;
	font-family:Arial, sans-serif;
	mso-font-charset:0;}
.xl68
	{mso-style-parent:style0;
	font-size:10.0pt;
	font-style:italic;
	font-family:Arial, sans-serif;
	mso-font-charset:0;
	text-align:justify;
	vertical-align:middle;}
.xl69
	{mso-style-parent:style0;
	font-size:10.0pt;
	font-weight:700;
	font-family:Arial, sans-serif;
	mso-font-charset:0;
	text-align:justify;
	vertical-align:middle;}
.xl70
	{mso-style-parent:style0;
	font-weight:700;
	font-family:Arial, sans-serif;
	mso-font-charset:0;
	text-align:justify;
	vertical-align:middle;}
.xl71
	{mso-style-parent:style0;
	font-size:10.0pt;
	font-family:Arial, sans-serif;
	mso-font-charset:0;}
.xl72
	{mso-style-parent:style0;
	font-size:6.0pt;
	font-family:Arial, sans-serif;
	mso-font-charset:0;
	text-align:justify;
	vertical-align:middle;}
.xl73
	{mso-style-parent:style0;
	font-size:10.0pt;
	font-family:Arial, sans-serif;
	mso-font-charset:0;
	text-align:center;
	vertical-align:middle;}
.xl74
	{mso-style-parent:style0;
	font-size:10.0pt;
	font-family:Arial, sans-serif;
	mso-font-charset:0;
	vertical-align:middle;}
.xl75
	{mso-style-parent:style0;
	color:gray;
	font-size:9.0pt;
	font-style:italic;
	font-family:Arial, sans-serif;
	mso-font-charset:0;
	vertical-align:middle;}
.xl76
	{mso-style-parent:style0;
	color:gray;
	font-size:14.0pt;
	font-style:italic;
	font-family:Arial, sans-serif;
	mso-font-charset:0;
	vertical-align:middle;}
.xl77
	{mso-style-parent:style0;
	font-size:9.0pt;
	font-family:Arial, sans-serif;
	mso-font-charset:0;
	vertical-align:middle;}
.xl78
	{mso-style-parent:style0;
	font-size:10.0pt;
	font-weight:700;
	font-family:Arial, sans-serif;
	mso-font-charset:0;
	vertical-align:middle;}
.xl79
	{mso-style-parent:style0;
	font-size:12.0pt;
	font-family:Arial, sans-serif;
	mso-font-charset:0;
	text-align:center;
	vertical-align:middle;}
.xl80
	{mso-style-parent:style0;
	font-size:12.0pt;
	font-style:italic;
	font-family:Arial, sans-serif;
	mso-font-charset:0;
	text-align:center;
	vertical-align:middle;}
.xl81
	{mso-style-parent:style0;
	font-size:10.0pt;
	font-weight:700;
	font-family:Arial, sans-serif;
	mso-font-charset:0;
	text-align:left;
	vertical-align:middle;}
.xl82
	{mso-style-parent:style0;
	font-size:14.0pt;
	font-weight:700;
	text-decoration:underline;
	text-underline-style:single;
	font-family:Arial, sans-serif;
	mso-font-charset:0;
	text-align:center;
	vertical-align:middle;}
.xl83
	{mso-style-parent:style0;
	font-family:Arial, sans-serif;
	mso-font-charset:0;
	vertical-align:middle;
	border-top:1.0pt solid black;
	border-right:none;
	border-bottom:1.0pt solid black;
	border-left:1.0pt solid black;
	white-space:normal;}
.xl84
	{mso-style-parent:style0;
	font-weight:700;
	font-family:Arial, sans-serif;
	mso-font-charset:0;
	text-align:center;
	vertical-align:middle;}
.xl85
	{mso-style-parent:style0;
	font-size:10.0pt;
	font-family:Arial, sans-serif;
	mso-font-charset:0;
	text-align:left;
	vertical-align:top;
	white-space:normal;}
.xl86
	{mso-style-parent:style0;
	font-family:Arial, sans-serif;
	mso-font-charset:0;
	text-align:left;}
.xl87
	{mso-style-parent:style0;
	font-size:10.0pt;
	font-family:Arial, sans-serif;
	mso-font-charset:0;
	text-align:left;
	vertical-align:top;}
.xl88
	{mso-style-parent:style0;
	font-size:12.0pt;
	font-weight:700;
	font-family:Arial, sans-serif;
	mso-font-charset:0;
	text-align:left;
	vertical-align:middle;}
.xl89
	{mso-style-parent:style0;
	font-size:10.0pt;
	font-family:Arial, sans-serif;
	mso-font-charset:0;
	text-align:right;
	vertical-align:middle;}
.xl90
	{mso-style-parent:style0;
	font-family:Arial, sans-serif;
	mso-font-charset:0;
	text-align:right;}
.xl91
	{mso-style-parent:style0;
	font-size:14.0pt;
	font-weight:700;
	font-family:Arial, sans-serif;
	mso-font-charset:0;
	text-align:right;
	vertical-align:middle;}
.xl92
	{mso-style-parent:style0;
	font-size:10.0pt;
	font-style:italic;
	font-family:Arial, sans-serif;
	mso-font-charset:0;
	text-align:right;
	vertical-align:top;
	white-space:normal;}
.xl93
	{mso-style-parent:style0;
	font-size:10.0pt;
	font-weight:700;
	font-family:Arial, sans-serif;
	mso-font-charset:0;
	text-align:right;
	vertical-align:middle;}
.xl94
	{mso-style-parent:style0;
	font-size:10.0pt;
	font-family:Arial, sans-serif;
	mso-font-charset:0;
	vertical-align:top;
	white-space:normal;}
.xl95
	{mso-style-parent:style0;
	font-size:10.0pt;
	font-family:Arial, sans-serif;
	mso-font-charset:0;
	text-align:right;
	vertical-align:top;}
.xl96
	{mso-style-parent:style0;
	font-size:10.0pt;
	font-family:Arial, sans-serif;
	mso-font-charset:0;
	vertical-align:top;}
.xl97
	{mso-style-parent:style0;
	font-size:10.0pt;
	font-style:italic;
	font-family:Arial, sans-serif;
	mso-font-charset:0;
	text-align:left;
	vertical-align:top;
	white-space:normal;}
.xl98
	{mso-style-parent:style0;
	font-size:8.0pt;
	font-family:Arial, sans-serif;
	mso-font-charset:0;
	vertical-align:middle;
	border-top:1.0pt solid black;
	border-right:1.0pt solid black;
	border-bottom:1.0pt solid black;
	border-left:none;
	white-space:normal;}
.xl99
	{mso-style-parent:style0;
	font-size:8.0pt;
	font-family:Arial, sans-serif;
	mso-font-charset:0;}
.xl100
	{mso-style-parent:style0;
	font-size:10.0pt;
	font-family:Arial, sans-serif;
	mso-font-charset:0;
	text-align:left;
	vertical-align:middle;
	border-top:1.0pt solid black;
	border-right:none;
	border-bottom:1.0pt solid black;
	border-left:1.0pt solid black;
	white-space:normal;}
.xl101
	{mso-style-parent:style0;
	font-size:10.0pt;
	font-family:Arial, sans-serif;
	mso-font-charset:0;
	text-align:left;
	vertical-align:middle;
	border-top:1.0pt solid black;
	border-right:1.0pt solid black;
	border-bottom:1.0pt solid black;
	border-left:none;
	white-space:normal;}

</style>