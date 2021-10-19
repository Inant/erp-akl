<!DOCTYPE html>
<html>
    
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Print PPJB</title>
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
            <td width="50%" class="title-print text-center"><b><u>PERJANJIAN PENDAHULUAN JUAL BELI RUMAH</b></u></td>
        </tr>
        <tr>
            <td width="50%" class="title-print text-center"><b><u>{{ $header['no'] }}</b></u><h3></td>
        </tr>
    </table>
    
<table border="0" cellpadding="0" cellspacing="0" width="1096" style="border-collapse:
 collapse;table-layout:fixed;width:822pt;margin-left:260px;">
 <colgroup><col class="xl75" width="39" style="mso-width-source:userset;mso-width-alt:1426;
 width:29pt">
 <col width="64" style="width:48pt">
 <col width="217" style="mso-width-source:userset;mso-width-alt:7936;width:163pt">
 <col width="264" style="mso-width-source:userset;mso-width-alt:9654;width:198pt">
 <col width="63" style="mso-width-source:userset;mso-width-alt:2304;width:47pt">
 <col width="64" span="2" style="width:48pt">
 <col width="65" style="mso-width-source:userset;mso-width-alt:2377;width:49pt">
 <col width="64" span="4" style="width:48pt">
 </colgroup><tbody><tr height="20" style="height:15.0pt">
  <td colspan="4" height="20" class="xl65" width="584" style="height:15.0pt;width:438pt"><span lang="IN" style="line-height:150%">PERJANJIAN PENDAHULUAN JUAL BELI RUMAH</span></td>
  <td class="xl65" width="63" style="width:47pt"></td>
  <td width="64" style="width:48pt"></td>
  <td width="64" style="width:48pt"></td>
  <td width="65" style="width:49pt"></td>
  <td width="64" style="width:48pt"></td>
  <td width="64" style="width:48pt"></td>
  <td width="64" style="width:48pt"></td>
  <td width="64" style="width:48pt"></td>
 </tr>
 <tr height="20" style="height:15.0pt">
  <td height="20" class="xl75" style="height:15.0pt"></td>
  <td colspan="2" style="mso-ignore:colspan"></td>
  <td class="xl66"></td>
  <td class="xl66"></td>
  <td colspan="2" style="mso-ignore:colspan"></td>
  <td></td>
  <td colspan="4" style="mso-ignore:colspan"></td>
 </tr>
 <tr height="37" style="mso-height-source:userset;height:27.75pt">
  <td colspan="4" height="37" class="xl77" width="584" style="height:27.75pt;
  width:438pt"><span lang="IN" style="line-height:150%">Perjanjian Pendahuluan
  Jual Beli Rumah ini dibuat pada tanggal<span style="mso-spacerun:yes">&nbsp;
  </span>{{ date('d-m-Y') }} oleh dan antara :<span style="mso-spacerun:yes">&nbsp;</span></span></td>
  <td class="xl77" width="63" style="width:47pt"></td>
  <td colspan="2" style="mso-ignore:colspan"></td>
  <td></td>
  <td colspan="4" style="mso-ignore:colspan"></td>
 </tr>
 <tr height="53" style="mso-height-source:userset;height:39.75pt">
  <td height="53" class="xl75" style="height:39.75pt">1</td>
  <td colspan="3" class="xl73" width="545" style="width:409pt"><span lang="EN-US"><font class="font6">Sururi Estate </font><font class="font7">berkedudukan di Jalan
  Pamenang, Kediri, dalam hal ini diwakili oleh </font><font class="font6">{{$header['sales_name']}}
  </font><font class="font7">dalam kedudukannya selaku
  {{$header['sales_role']}} untuk selanjutnya disebut “</font><font class="font6">PENJUAL</font><font class="font7">”atau </font><font class="font6">“PIHAK PERTAMA”</font><font class="font7">.</font></span></td>
  <td class="xl73" width="63" style="width:47pt"></td>
  <td colspan="2" style="mso-ignore:colspan"></td>
  <td></td>
  <td colspan="4" style="mso-ignore:colspan"></td>
 </tr>
 <tr height="20" style="height:15.0pt">
  <td height="20" class="xl75" style="height:15.0pt">2</td>
  <td class="xl67"><span lang="EN-US">Nama</span><span lang="EN-US" style="mso-ansi-language:
  EN-US"></span></td>
  <td class="xl67"></td>
  <td class="xl67">: {{$header['customer_name']}}</td>
  <td class="xl67"></td>
  <td colspan="2" style="mso-ignore:colspan"></td>
  <td></td>
  <td colspan="4" style="mso-ignore:colspan"></td>
 </tr>
 <tr height="20" style="height:15.0pt">
  <td height="20" class="xl75" style="height:15.0pt"></td>
  <td class="xl67"><span lang="EN-US" style="mso-ansi-language:EN-US">Umur</span></td>
  <td class="xl67"></td>
  <td class="xl67">: {{$header['customer_age']}}</td>
  <td class="xl67"></td>
  <td colspan="2" style="mso-ignore:colspan"></td>
  <td></td>
  <td colspan="4" style="mso-ignore:colspan"></td>
 </tr>
 <tr height="20" style="height:15.0pt">
  <td height="20" class="xl75" style="height:15.0pt"></td>
  <td class="xl67"><span lang="EN-US" style="line-height:107%;mso-ansi-language:
  EN-US">Alamat</span></td>
  <td class="xl67"></td>
  @if($header['address_spu'] != null)
  <td class="xl67">: {{ $header['address_spu'].', RT '.$header['legal_rt'].' RW '.$header['legal_rw'].', Kelurahan '.$header['legal_kelurahan'] }} <br /> {{ 'Kecamatan '.$header['legal_kecamatan'].', Kota '.$header['legal_city'].', Kode Pos '.$header['legal_zipcode']}}</td>
  @else
  <td class="xl67">: {{ $header['address_ktp'].', RT '.$header['residence_rt'].' RW '.$header['residence_rw'].', Kelurahan '.$header['residence_kelurahan'] }} <br /> {{ 'Kecamatan '.$header['residence_kecamatan'].', Kota '.$header['residence_city'].', Kode Pos '.$header['residence_zipcode']}}</td>
  @endif
  <td class="xl67"></td>
  <td colspan="2" style="mso-ignore:colspan"></td>
  <td></td>
  <td colspan="4" style="mso-ignore:colspan"></td>
 </tr>
 <tr height="25" style="mso-height-source:userset;height:18.75pt">
  <td height="25" class="xl75" style="height:18.75pt"></td>
  <td class="xl67"><span lang="EN-US" style="line-height:107%;mso-ansi-language:
  EN-US">Pekerjaan</span></td>
  <td class="xl67"></td>
  <td class="xl67">: {{$header['description']}}</td>
  <td class="xl67"></td>
  <td colspan="2" style="mso-ignore:colspan"></td>
  <td></td>
  <td colspan="4" style="mso-ignore:colspan"></td>
 </tr>
 <tr height="52" style="mso-height-source:userset;height:39.0pt">
  <td height="52" class="xl75" style="height:39.0pt"></td>
  <td colspan="3" class="xl73" width="545" style="width:409pt"><span lang="EN-US" style="line-height:150%;mso-ansi-language:EN-US">dalam halini bertindak untuk
  diri sendiri dan untuk melakukan tindakan hukum tersebut dalam Perjanjian
  Pendahuluan Jual Beli Rumah ini untuk selanjutnya disebut <font class="font6">“PEMBELI”</font><font class="font7"> atau </font><font class="font6">“PIHAK KEDUA”</font><font class="font7">.</font></span></td>
  <td class="xl73" width="63" style="width:47pt"></td>
  <td colspan="2" style="mso-ignore:colspan"></td>
  <td></td>
  <td colspan="4" style="mso-ignore:colspan"></td>
 </tr>
 <tr height="50" style="mso-height-source:userset;height:37.5pt">
  <td colspan="4" height="50" class="xl73" width="584" style="height:37.5pt;width:438pt"><span lang="IN" style="line-height:150%">Bahwa <font class="font6">PIHAK PERTAMA</font><font class="font7"><span style="mso-spacerun:yes">&nbsp; </span>dan </font><font class="font6">PIHAK KEDUA</font><font class="font7"> telah saling setuju
  untuk membuat, melaksanakan dan mematuhi Perjanjian Pendahuluan Jual Beli
  Rumah ini untuk selanjutnya disebut </font><font class="font6">“PARA PIHAK”</font></span></td>
  <td class="xl73" width="63" style="width:47pt"></td>
  <td colspan="2" style="mso-ignore:colspan"></td>
  <td></td>
  <td colspan="4" style="mso-ignore:colspan"></td>
 </tr>
 <tr height="20" style="height:15.0pt">
  <td colspan="4" height="20" class="xl74" style="height:15.0pt"></td>
  <td class="xl74"></td>
  <td colspan="2" style="mso-ignore:colspan"></td>
  <td></td>
  <td colspan="4" style="mso-ignore:colspan"></td>
 </tr>
 <tr height="20" style="height:15.0pt">
  <td colspan="4" height="20" class="xl66" style="height:15.0pt"><span lang="IN">BAB I</span></td>
  <td class="xl66"></td>
  <td colspan="2" style="mso-ignore:colspan"></td>
  <td></td>
  <td colspan="4" style="mso-ignore:colspan"></td>
 </tr>
 <tr height="20" style="height:15.0pt">
  <td colspan="4" height="20" class="xl66" style="height:15.0pt"><span lang="IN">KETENTUAN
  UMUM</span></td>
  <td class="xl66"></td>
  <td colspan="2" style="mso-ignore:colspan"></td>
  <td></td>
  <td colspan="4" style="mso-ignore:colspan"></td>
 </tr>
 <tr height="20" style="height:15.0pt">
  <td colspan="4" height="20" class="xl66" style="height:15.0pt"><span lang="IN">PASAL
  1</span></td>
  <td class="xl66"></td>
  <td colspan="2" style="mso-ignore:colspan"></td>
  <td></td>
  <td colspan="4" style="mso-ignore:colspan"></td>
 </tr>
 <tr height="20" style="height:15.0pt">
  <td colspan="4" height="20" class="xl74" style="height:15.0pt"></td>
  <td class="xl74"></td>
  <td colspan="2" style="mso-ignore:colspan"></td>
  <td></td>
  <td colspan="4" style="mso-ignore:colspan"></td>
 </tr>
 <tr height="37" style="mso-height-source:userset;height:27.75pt">
  <td height="37" class="xl75" style="height:27.75pt">1</td>
  <td colspan="3" class="xl73" width="545" style="width:409pt"><span lang="IN" style="line-height:150%;mso-fareast-font-family:Arial"><font class="font6">Penjual</font><font class="font7"> adalah subjek hukum yang menyerahkan objek hukum dalam hal ini
  rumah beserta hak kepemilikannya kepada PIHAK KEDUA.</font></span></td>
  <td class="xl73" width="63" style="width:47pt"></td>
  <td colspan="2" style="mso-ignore:colspan"></td>
  <td></td>
  <td colspan="4" style="mso-ignore:colspan"></td>
 </tr>
 <tr height="37" style="mso-height-source:userset;height:27.75pt">
  <td height="37" class="xl75" style="height:27.75pt">2</td>
  <td colspan="3" class="xl73" width="545" style="width:409pt"><span lang="IN" style="line-height:150%;mso-fareast-font-family:Arial"><font class="font6">Pembeli</font><font class="font7"> adalah subjek hukum yang menerima objek hukum dalam hal ini
  rumah beserta hak kepemilikannya dari PIHAK PERTAMA.</font></span></td>
  <td class="xl73" width="63" style="width:47pt"></td>
  <td colspan="2" style="mso-ignore:colspan"></td>
  <td></td>
  <td colspan="4" style="mso-ignore:colspan"></td>
 </tr>
 <tr height="51" style="mso-height-source:userset;height:38.25pt">
  <td height="51" class="xl75" style="height:38.25pt">3</td>
  <td colspan="3" class="xl73" width="545" style="width:409pt"><span lang="IN" style="line-height:150%;mso-fareast-font-family:Arial"><font class="font6">Harga
  jual </font><font class="font7">dalam Perjanjian Pendahuluan<span style="mso-spacerun:yes">&nbsp; </span>Jual Beli Rumah ini adalah harga yang
  terdiri Harga Property (tanah dan bangunan); PPN; BPHTB; Biaya pengadaan
  FASUM; Biaya Notaris; dan Spec Up.</font></span></td>
  <td class="xl73" width="63" style="width:47pt"></td>
  <td colspan="2" style="mso-ignore:colspan"></td>
  <td></td>
  <td colspan="4" style="mso-ignore:colspan"></td>
 </tr>
 <tr height="70" style="mso-height-source:userset;height:52.5pt">
  <td height="70" class="xl75" style="height:52.5pt">4</td>
  <td colspan="3" class="xl73" width="545" style="width:409pt"><span lang="IN" style="line-height:150%;mso-fareast-font-family:Arial"><font class="font6">Objek
  hukum </font><font class="font7">dalam Perjanjian Pendahuluan Jual Beli Rumah
  ini adalah<span style="mso-spacerun:yes">&nbsp; </span>perumahan
  {{$header['site_name']}} nomor kavling {{$header['kavling_no']}} dengan type
  Lb / Lt {{$header['base_area']}} m2, spesifikasi material finishing dan gambar
  standart terlampir, telah disetujui dan ditandatangani antara kedua belah
  pihak pada perjanjian ini.</font></span></td>
  <td class="xl73" width="63" style="width:47pt"></td>
  <td colspan="2" style="mso-ignore:colspan"></td>
  <td></td>
  <td colspan="4" style="mso-ignore:colspan"></td>
 </tr>
 <tr height="22" style="mso-height-source:userset;height:16.5pt">
  <td height="22" class="xl75" style="height:16.5pt">5</td>
  <td colspan="3" class="xl73" width="545" style="width:409pt"><span lang="IN" style="line-height:150%;mso-fareast-font-family:Arial">Letak rumah di
  {{$header['site_address']}}</span></td>
  <td class="xl73" width="63" style="width:47pt"></td>
  <td colspan="2" style="mso-ignore:colspan"></td>
  <td></td>
  <td colspan="4" style="mso-ignore:colspan"></td>
 </tr>
 <tr height="20" style="height:15.0pt">
  <td height="20" class="xl75" style="height:15.0pt">6</td>
  <td colspan="3" class="xl73" width="545" style="width:409pt"><span lang="IN" style="line-height:150%;mso-fareast-font-family:Arial">Luas rumah ±
  {{$header['lb']}} m2 dan berdiri di atas sebidang tanah seluas ±
  {{$header['lt']}} m2.</span></td>
  <td class="xl73" width="63" style="width:47pt"></td>
  <td colspan="2" style="mso-ignore:colspan"></td>
  <td></td>
  <td colspan="4" style="mso-ignore:colspan"></td>
 </tr>
 <tr height="20" style="height:15.0pt">
  <td colspan="4" height="20" class="xl66" style="height:15.0pt"><span lang="IN">BAB II</span></td>
  <td class="xl66"></td>
  <td colspan="2" style="mso-ignore:colspan"></td>
  <td></td>
  <td colspan="4" style="mso-ignore:colspan"></td>
 </tr>
 <tr height="20" style="height:15.0pt">
  <td colspan="4" height="20" class="xl66" style="height:15.0pt"><span lang="IN">HAK
  DAN KEWAJIBAN PARA PIHAK</span></td>
  <td class="xl66"></td>
  <td colspan="2" style="mso-ignore:colspan"></td>
  <td></td>
  <td colspan="4" style="mso-ignore:colspan"></td>
 </tr>
 <tr height="20" style="height:15.0pt">
  <td colspan="4" height="20" class="xl66" style="height:15.0pt"><span lang="IN">PASAL
  2</span></td>
  <td class="xl66"></td>
  <td colspan="2" style="mso-ignore:colspan"></td>
  <td></td>
  <td colspan="4" style="mso-ignore:colspan"></td>
 </tr>
 <tr height="20" style="height:15.0pt">
  <td colspan="4" height="20" class="xl74" style="height:15.0pt"></td>
  <td class="xl74"></td>
  <td colspan="2" style="mso-ignore:colspan"></td>
  <td></td>
  <td colspan="4" style="mso-ignore:colspan"></td>
 </tr>
 <tr height="40" style="mso-height-source:userset;height:30.0pt">
  <td height="40" class="xl75" style="height:30.0pt">1</td>
  <td colspan="3" class="xl73" width="545" style="width:409pt"><span lang="IN" style="line-height:150%;mso-fareast-font-family:Arial">PIHAK PERTAMA berhak
  menerima pembayaran atas objek hukum dalam hal ini rumah beserta hak
  kepemilikannya dari PIHAK KEDUA.</span></td>
  <td class="xl73" width="63" style="width:47pt"></td>
  <td colspan="2" style="mso-ignore:colspan"></td>
  <td></td>
  <td colspan="4" style="mso-ignore:colspan"></td>
 </tr>
 <tr height="53" style="mso-height-source:userset;height:39.75pt">
  <td height="53" class="xl75" style="height:39.75pt">2</td>
  <td colspan="3" class="xl73" width="545" style="width:409pt"><span lang="IN" style="line-height:150%;mso-fareast-font-family:Arial">PIHAK PERTAMA berhak
  menerima pembayaran atas objek hukum dalam hal ini rumah beserta
  kepemilikannya secara tunai atau bertahap sampai lunas dari PIHAK KEDUA.</span></td>
  <td class="xl73" width="63" style="width:47pt"></td>
  <td colspan="2" style="mso-ignore:colspan"></td>
  <td></td>
  <td colspan="4" style="mso-ignore:colspan"></td>
 </tr>
 <tr height="53" style="mso-height-source:userset;height:39.75pt">
  <td height="53" class="xl75" style="height:39.75pt">3</td>
  <td colspan="3" class="xl73" width="545" style="width:409pt"><span lang="IN" style="line-height:150%;mso-fareast-font-family:Arial">PIHAK PERTAMA
  berkewajiban menyerahkan objek hukum dalam hal ini rumah beserta hak
  kepemilikannya setelah dilakukan pembayaran secara lunas oleh PIHAK KEDUA.</span></td>
  <td class="xl73" width="63" style="width:47pt"></td>
  <td colspan="2" style="mso-ignore:colspan"></td>
  <td></td>
  <td colspan="4" style="mso-ignore:colspan"></td>
 </tr>
 <tr height="20" style="height:15.0pt">
  <td colspan="4" height="20" class="xl66" style="height:15.0pt"><span lang="IN" style="line-height:150%">PASAL 3</span></td>
  <td class="xl66"></td>
  <td colspan="2" style="mso-ignore:colspan"></td>
  <td></td>
  <td colspan="4" style="mso-ignore:colspan"></td>
 </tr>
 <tr height="41" style="mso-height-source:userset;height:30.75pt">
  <td height="41" class="xl75" style="height:30.75pt">1</td>
  <td colspan="3" class="xl73" width="545" style="width:409pt"><span lang="IN" style="line-height:150%;mso-fareast-font-family:Arial">PIHAK KEDUA berhak
  menerima objek hukum dalam hal ini rumah beserta hak kepemilikannya setelah
  dilakukan pembayaran secara lunas kepada PIHAK PERTAMA.</span></td>
  <td class="xl73" width="63" style="width:47pt"></td>
  <td colspan="2" style="mso-ignore:colspan"></td>
  <td></td>
  <td colspan="4" style="mso-ignore:colspan"></td>
 </tr>
 <tr height="53" style="mso-height-source:userset;height:39.75pt">
  <td height="53" class="xl75" style="height:39.75pt">2</td>
  <td colspan="3" class="xl73" width="545" style="width:409pt"><span lang="IN" style="line-height:150%;mso-fareast-font-family:Arial">PIHAK KEDUA
  berkewajiban melakukan pembayaran atas objek hukum dalam hal ini rumah
  beserta hak kepemilikannya secara tunai atau bertahap sampai lunas kepada
  PIHAK PERTAMA.</span></td>
  <td class="xl73" width="63" style="width:47pt"></td>
  <td colspan="2" style="mso-ignore:colspan"></td>
  <td></td>
  <td colspan="4" style="mso-ignore:colspan"></td>
 </tr>
 <tr height="20" style="height:15.0pt">
  <td colspan="4" height="20" class="xl76" style="height:15.0pt"></td>
  <td class="xl76"></td>
  <td colspan="2" style="mso-ignore:colspan"></td>
  <td></td>
  <td colspan="4" style="mso-ignore:colspan"></td>
 </tr>
 <tr height="20" style="page-break-before:always;height:15.0pt">
  <td colspan="4" height="20" class="xl66" style="height:15.0pt"><span lang="IN">BAB
  III</span></td>
  <td class="xl66"></td>
  <td colspan="2" style="mso-ignore:colspan"></td>
  <td></td>
  <td colspan="4" style="mso-ignore:colspan"></td>
 </tr>
 <tr height="20" style="height:15.0pt">
  <td colspan="4" height="20" class="xl66" style="height:15.0pt"><span lang="EN-US" style="mso-ansi-language:EN-US">HARGA JUAL</span></td>
  <td class="xl66"></td>
  <td colspan="2" style="mso-ignore:colspan"></td>
  <td></td>
  <td colspan="4" style="mso-ignore:colspan"></td>
 </tr>
 <tr height="20" style="height:15.0pt">
  <td colspan="4" height="20" class="xl66" style="height:15.0pt"><span lang="IN" style="line-height:150%">PASAL 4</span></td>
  <td class="xl66"></td>
  <td colspan="2" style="mso-ignore:colspan"></td>
  <td></td>
  <td colspan="4" style="mso-ignore:colspan"></td>
 </tr>
 <tr height="68" style="mso-height-source:userset;height:51.0pt">
  <td height="68" class="xl75" style="height:51.0pt">1</td>
  <td colspan="3" class="xl73" width="545" style="width:409pt"><span lang="SV">PIHAK
  PERTAMA mengikatkan diri untuk menjual, memindahkan dan mengalihkan kepada
  PIHAK KEDUA dan PIHAK KEDUA membeli, menerima pemindahan serta penyerahan
  dari PIHAK PERTAMA atas tanah dan bangunan tersebut dengan rincian biaya
  sebagai berikut :</span></td>
  <td class="xl73" width="63" style="width:47pt"></td>
  <td colspan="2" style="mso-ignore:colspan"></td>
  <td></td>
  <td colspan="4" style="mso-ignore:colspan"></td>
 </tr>
 <tr height="20" style="height:15.0pt">
  <td height="20" class="xl75" style="height:15.0pt"></td>
  <td colspan="2" class="xl85"><span lang="SV">1.&nbsp;&nbsp;&nbsp; Harga Property
  (tanah dan bangunan)</span></td>
  <td class="xl86">Rp {{ number_format( $header['base_price'] ,2,',','.') }}</td>
  <td></td>
  <td class="xl67"></td>
  <td colspan="2" style="mso-ignore:colspan"></td>
  <td></td>
  <td colspan="3" style="mso-ignore:colspan"></td>
 </tr>
 <tr height="20" style="height:15.0pt">
  <td height="20" class="xl75" style="height:15.0pt"></td>
  <td colspan="2" class="xl85"><span lang="SV">2.&nbsp;&nbsp;&nbsp; PPN</span></td>
  <td class="xl86">Rp {{ number_format( $header['ppn_amount'] ,2,',','.') }}</td>
  <td></td>
  <td class="xl67"></td>
  <td colspan="2" style="mso-ignore:colspan"></td>
  <td></td>
  <td colspan="3" style="mso-ignore:colspan"></td>
 </tr>
 <tr height="20" style="height:15.0pt">
  <td height="20" class="xl75" style="height:15.0pt"></td>
  <td colspan="2" class="xl85"><span lang="SV">3.&nbsp;&nbsp;&nbsp; BPHTB</span></td>
  <td class="xl86" colspan="2" style="mso-ignore:colspan">Rp {{ number_format(
  $header['pbhtb_amount'],2,',','.') }}</td>
  <td class="xl67"></td>
  <td colspan="2" style="mso-ignore:colspan"></td>
  <td></td>
  <td colspan="3" style="mso-ignore:colspan"></td>
 </tr>
 <tr height="20" style="height:15.0pt">
  <td height="20" class="xl75" style="height:15.0pt"></td>
  <td colspan="2" class="xl85"><span lang="SV">4.&nbsp;&nbsp;&nbsp; Biaya pengadaan
  FASUM</span></td>
  <td class="xl86">Rp {{ number_format( $header['fasum_fee'] ,2,',','.') }}</td>
  <td></td>
  <td class="xl67"></td>
  <td colspan="2" style="mso-ignore:colspan"></td>
  <td></td>
  <td colspan="3" style="mso-ignore:colspan"></td>
 </tr>
 <tr height="20" style="height:15.0pt">
  <td height="20" class="xl75" style="height:15.0pt"></td>
  <td colspan="2" class="xl85"><span lang="SV">5.&nbsp;&nbsp;&nbsp; Biaya Notaris</span></td>
  <td class="xl86">Rp {{ number_format( $header['notary_fee'],2,',','.') }}</td>
  <td></td>
  <td class="xl67"></td>
  <td colspan="2" style="mso-ignore:colspan"></td>
  <td></td>
  <td colspan="3" style="mso-ignore:colspan"></td>
 </tr>
 <tr height="20" style="height:15.0pt">
  <td height="20" class="xl75" style="height:15.0pt"></td>
  <td colspan="2" class="xl85"><span lang="SV">6.&nbsp;&nbsp;&nbsp; Spec Up</span></td>
  <td class="xl86">Rp {{ number_format( $header['specup_amount'],2,',','.') }}</td>
  <td></td>
  <td class="xl67"></td>
  <td colspan="2" style="mso-ignore:colspan"></td>
  <td></td>
  <td colspan="3" style="mso-ignore:colspan"></td>
 </tr>
 <tr height="20" style="height:15.0pt">
  <td height="20" class="xl75" style="height:15.0pt"></td>
  <td colspan="2" class="xl85"><span lang="SV">7.&nbsp;&nbsp;&nbsp; Discount</span></td>
  <td class="xl86">Rp {{ number_format( $header['total_discount'],2,',','.') }}</td>
  <td></td>
  <td class="xl67"></td>
  <td colspan="2" style="mso-ignore:colspan"></td>
  <td></td>
  <td colspan="3" style="mso-ignore:colspan"></td>
 </tr>
 <tr height="20" style="height:15.0pt">
  <td height="20" class="xl75" style="height:15.0pt"></td>
  <td colspan="3" class="xl79"><span lang="SV" style="mso-ansi-language:SV">Total
  yang dibayar<font class="font7"> ;</font></span></td>
  <td class="xl68"></td>
  <td colspan="2" style="mso-ignore:colspan"></td>
  <td></td>
  <td colspan="4" style="mso-ignore:colspan"></td>
 </tr>
 <tr height="20" style="height:15.0pt">
  <td height="20" class="xl75" style="height:15.0pt"></td>
  <td></td>
  <td class="xl78"></td>
  <td class="xl78">Rp {{ number_format( $header['total_price'],2,',','.') }}</td>
  <td class="xl78"></td>
  <td class="xl67"></td>
  <td class="xl67"></td>
  <td></td>
  <td colspan="4" style="mso-ignore:colspan"></td>
 </tr>
 <tr height="20" style="height:15.0pt">
  <td height="20" class="xl75" style="height:15.0pt"></td>
  <td colspan="2" style="mso-ignore:colspan"></td>
  <td class="xl67"></td>
  <td class="xl67"></td>
  <td colspan="2" style="mso-ignore:colspan"></td>
  <td></td>
  <td colspan="4" style="mso-ignore:colspan"></td>
 </tr>
 <tr height="49" style="mso-height-source:userset;height:36.75pt">
  <td height="49" class="xl75" style="height:36.75pt">2</td>
  <td colspan="3" class="xl73" width="545" style="width:409pt"><span lang="SV">PIHAK
  KEDUA sanggup melunasi dan menanggung biaya, apabila ada kurang bayar karena
  suatu hal apapun yang terkait dengan perpajakan (PPN &amp; BPHTB) dan Biaya
  Notaris.</span></td>
  <td class="xl67"></td>
  <td colspan="2" style="mso-ignore:colspan"></td>
  <td></td>
  <td colspan="4" style="mso-ignore:colspan"></td>
 </tr>
 <tr height="20" style="height:15.0pt">
  <td colspan="4" height="20" class="xl76" style="height:15.0pt"></td>
  <td class="xl67"></td>
  <td colspan="2" style="mso-ignore:colspan"></td>
  <td></td>
  <td colspan="4" style="mso-ignore:colspan"></td>
 </tr>
 <tr height="20" style="height:15.0pt">
  <td colspan="4" height="20" class="xl66" style="height:15.0pt"><span lang="IN">BAB
  III</span></td>
  <td class="xl66"></td>
  <td colspan="2" style="mso-ignore:colspan"></td>
  <td></td>
  <td colspan="4" style="mso-ignore:colspan"></td>
 </tr>
 <tr height="20" style="height:15.0pt">
  <td colspan="4" height="20" class="xl66" style="height:15.0pt"><span lang="IN">CARA
  PEMBAYARAN<span style="mso-spacerun:yes">&nbsp;</span></span></td>
  <td class="xl66"></td>
  <td colspan="2" style="mso-ignore:colspan"></td>
  <td></td>
  <td colspan="4" style="mso-ignore:colspan"></td>
 </tr>
 <tr height="20" style="height:15.0pt">
  <td colspan="4" height="20" class="xl66" style="height:15.0pt"><span lang="IN">PASAL
  5</span></td>
  <td class="xl66"></td>
  <td colspan="2" style="mso-ignore:colspan"></td>
  <td></td>
  <td colspan="4" style="mso-ignore:colspan"></td>
 </tr>
 <tr height="20" style="height:15.0pt">
  <td colspan="4" height="20" class="xl76" style="height:15.0pt"></td>
  <td class="xl66"></td>
  <td colspan="2" style="mso-ignore:colspan"></td>
  <td></td>
  <td colspan="4" style="mso-ignore:colspan"></td>
 </tr>
 <tr height="52" style="mso-height-source:userset;height:39.0pt">
  <td colspan="4" height="52" class="xl73" width="584" style="height:39.0pt;width:438pt"><span lang="SV" style="line-height:150%;mso-ansi-language:SV">PIHAK KEDUA diwajibkan
  membayar uang tanda jadi kepada PIHAK PERTAMA sebesar Rp. 10.000.000 (Sepuluh
  Juta Rupiah) baik pengambilan pembayarannya secara bertahap maupun secara
  KPR.</span></td>
  <td class="xl67"></td>
  <td colspan="2" style="mso-ignore:colspan"></td>
  <td></td>
  <td colspan="4" style="mso-ignore:colspan"></td>
 </tr>
 <tr height="20" style="height:15.0pt">
  <td colspan="4" height="20" class="xl66" style="height:15.0pt"><span lang="IN" style="line-height:150%">PASAL 6</span></td>
  <td class="xl66"></td>
  <td colspan="2" style="mso-ignore:colspan"></td>
  <td></td>
  <td colspan="4" style="mso-ignore:colspan"></td>
 </tr>
 <tr height="41" style="mso-height-source:userset;height:30.75pt">
  <td height="41" class="xl75" style="height:30.75pt">1</td>
  <td colspan="3" class="xl80" width="545" style="width:409pt"><span lang="IN" style="line-height:150%;mso-fareast-font-family:Arial">Pembayaran objek hukum
  dalam hal ini rumah beserta hak kepemilikannya kepada PIHAK PERTAMA dilakukan
  secara bertahap yaitu</span></td>
  <td class="xl67"></td>
  <td colspan="2" style="mso-ignore:colspan"></td>
  <td></td>
  <td colspan="4" style="mso-ignore:colspan"></td>
 </tr>
 <tr height="20" style="height:15.0pt">
  <td height="20" class="xl75" style="height:15.0pt"></td>
  <td colspan="3" class="xl74"></td>
  <td class="xl67"></td>
  <td colspan="2" style="mso-ignore:colspan"></td>
  <td></td>
  <td colspan="4" style="mso-ignore:colspan"></td>
 </tr>
 @if($detail != null)
    @for($i = 0; $i < count($detail); $i++)
    <tr height="19" style="height:14.25pt">
        <td height="20" class="xl75" style="height:15.0pt"></td>
        @if($detail[$i]['tenor'] == '')
        <td colspan="3" class="xl74"> {{ $i+1 }}. {{ $detail[$i]['tahap_bayar'] }} dilakukan pada tanggal {{ $detail[$i]['due_date'] }} sebesar Rp {{ number_format( $detail[$i]['amount'] ,2,',','.') }} </td>
        @else
        <td colspan="3" class="xl74"> {{ $i+1 }}. {{ $detail[$i]['tahap_bayar'] }} dilakukan setiap tanggal {{ $detail[$i]['due_date'] }} sebesar Rp {{ number_format( $detail[$i]['amount'] ,2,',','.')}} selama {{ $detail[$i]['tenor']}} bulan. </td>
        @endif
        <td class="xl67"></td>
        <td colspan="2" style="mso-ignore:colspan"></td>
        <td></td>
        <td colspan="4" style="mso-ignore:colspan"></td>
    </tr>
    @endfor
@endif
 <tr height="20" style="height:15.0pt">
  <td height="20" class="xl75" style="height:15.0pt"></td>
  <td colspan="3" class="xl74"></td>
  <td class="xl67"></td>
  <td colspan="2" style="mso-ignore:colspan"></td>
  <td></td>
  <td colspan="4" style="mso-ignore:colspan"></td>
 </tr>
 <tr height="107" style="mso-height-source:userset;height:80.25pt">
  <td height="107" class="xl75" style="height:80.25pt">2</td>
  <td colspan="3" class="xl80" width="545" style="width:409pt"><span lang="IN" style="line-height:150%;mso-fareast-font-family:Arial">Untuk tiap-tiap
  pembayaran yang dilakukan PIHAK KEDUA kepada PIHAK PERTAMA, harus dilakukan
  ke alamat PIHAK PERTAMA atau transfer bank ke rekening PIHAK PERTAMA.
  Pembayaran melalui cek atau transfer baru dianggap sah diterima setelah dana
  yang bersangkutan efektif diterima oleh PIHAK PERTAMA dan akan diberikan
  tanda terima berupa kwitansi oleh PIHAK PERTAMA yang merupakan bagian yang
  tidak terpisahkan dari isi perjanjian ini.</span></td>
  <td class="xl67"></td>
  <td colspan="2" style="mso-ignore:colspan"></td>
  <td></td>
  <td colspan="4" style="mso-ignore:colspan"></td>
 </tr>
 <tr height="20" style="height:15.0pt">
  <td colspan="4" height="20" class="xl66" style="height:15.0pt"><span lang="IN" style="line-height:150%">PASAL 7</span></td>
  <td class="xl66"></td>
  <td colspan="2" style="mso-ignore:colspan"></td>
  <td></td>
  <td colspan="4" style="mso-ignore:colspan"></td>
 </tr>
 <tr height="39" style="mso-height-source:userset;height:29.25pt">
  <td colspan="4" height="39" class="xl73" width="584" style="height:29.25pt;
  width:438pt"><span lang="IN" style="line-height:150%">Penyerahan objek hukum
  dalam hal ini rumah beserta hak kepemilikannya dilakukan oleh PIHAK PERTAMA
  kepada PIHAK KEDUA dilakukan setelah pembayaran lunas.</span></td>
  <td class="xl67"></td>
  <td colspan="2" style="mso-ignore:colspan"></td>
  <td></td>
  <td colspan="4" style="mso-ignore:colspan"></td>
 </tr>
 <tr height="20" style="height:15.0pt">
  <td height="20" class="xl75" style="height:15.0pt"></td>
  <td colspan="2" style="mso-ignore:colspan"></td>
  <td class="xl67"></td>
  <td class="xl67"></td>
  <td colspan="2" style="mso-ignore:colspan"></td>
  <td></td>
  <td colspan="4" style="mso-ignore:colspan"></td>
 </tr>
 <tr height="20" style="height:15.0pt">
  <td height="20" class="xl75" style="height:15.0pt"></td>
  <td colspan="2" style="mso-ignore:colspan"></td>
  <td class="xl67"></td>
  <td class="xl67"></td>
  <td colspan="2" style="mso-ignore:colspan"></td>
  <td></td>
  <td colspan="4" style="mso-ignore:colspan"></td>
 </tr>
 <tr height="20" style="height:15.0pt">
  <td colspan="4" height="20" class="xl66" style="height:15.0pt"><span lang="IN">BAB IV</span></td>
  <td class="xl66"></td>
  <td colspan="2" style="mso-ignore:colspan"></td>
  <td></td>
  <td colspan="4" style="mso-ignore:colspan"></td>
 </tr>
 <tr height="20" style="height:15.0pt">
  <td colspan="4" height="20" class="xl66" style="height:15.0pt"><span lang="SV" style="mso-ansi-language:SV">PEMBELIAN DENGAN FASILITAS KREDIT PEMILIKAN
  RUMAH ( KPR )</span></td>
  <td class="xl66"></td>
  <td colspan="2" style="mso-ignore:colspan"></td>
  <td></td>
  <td colspan="4" style="mso-ignore:colspan"></td>
 </tr>
 <tr height="20" style="height:15.0pt">
  <td colspan="4" height="20" class="xl66" style="height:15.0pt"><span lang="IN" style="line-height:150%">PASAL 8</span></td>
  <td class="xl66"></td>
  <td colspan="2" style="mso-ignore:colspan"></td>
  <td></td>
  <td colspan="4" style="mso-ignore:colspan"></td>
 </tr>
 <tr height="155" style="page-break-before:always;mso-height-source:userset;
  height:116.25pt">
  <td height="155" class="xl75" style="height:116.25pt">1</td>
  <td colspan="3" class="xl80" width="545" style="width:409pt"><span lang="EN-US">Apabila
  pelunasan pembayaran dilaksanakan melalui fasilitas KPR, maka PIHAK KEDUA
  bersedia memenuhi segala persyaratan yang diminta oleh pihak Bank pemberi
  kredit selambat-lambatnya 14 hari sejak pembayaran tanda jadi agar pengurusan
  KPR dapat dijalankan oleh Pihak Developer. PIHAK KEDUA juga bersedia memenuhi
  segala biaya-biaya yang diminta oleh pihak Bank pemberi kredit
  selambat-lambatnya 7 hari sejak disetujuinya kredit pemilikan rumah (KPR).
  Bank pemberi kredit adalah Bank yang ditunjuk oleh PIHAK PERTAMA. Apabila
  PIHAK KEDUA tidak dapat memenuhi hal tersebut diatas, maka perjanjian ini
  batal dan untuk selanjutnya PIHAK KEDUA dikenakan denda sesuai dengan Pasal
  10 perjanjian ini.</span></td>
  <td class="xl67"></td>
  <td colspan="2" style="mso-ignore:colspan"></td>
  <td></td>
  <td colspan="4" style="mso-ignore:colspan"></td>
 </tr>
 <tr height="156" style="mso-height-source:userset;height:117.0pt">
  <td height="156" class="xl75" style="height:117.0pt">2</td>
  <td colspan="3" class="xl81" width="545" style="width:409pt"><span lang="EN-US">PIHAK
  KEDUA bersedia melaksanakan pelunasan<span style="mso-spacerun:yes">&nbsp;
  </span>pembayaran rumah dengan melakukan akad kredit dengan pihak bank
  selambat-lambatnya 14 ( empat belas ) hari sejak disetujuinya Kredit
  Pemilikan Rumah ( KPR ) oleh pihak Bank. Jika ternyata PIHAK KEDUA
  membatalkan pembelian dengan menggunakan Fasilitas Kredit Pemilikan Rumah (
  KPR ), atau pihak Bank tidak menyetujui sebagian atau seluruhnya dari Kredit
  Pemilikan Rumah ( KPR ) yang diajukan, maka PIHAK KEDUA sanggup melunasi
  pembayaran secara tunai. Apabila PIHAK KEDUA tidak dapat memenuhi hal
  tersebut diatas, maka perjanjian ini batal dan untuk selanjutnya PIHAK KEDUA
  dikenakan denda sesuai dengan Pasal10 perjanjian ini.</span></td>
  <td class="xl67"></td>
  <td colspan="2" style="mso-ignore:colspan"></td>
  <td></td>
  <td colspan="4" style="mso-ignore:colspan"></td>
 </tr>
 <tr height="20" style="height:15.0pt">
  <td colspan="4" height="20" class="xl76" style="height:15.0pt"></td>
  <td class="xl67"></td>
  <td colspan="2" style="mso-ignore:colspan"></td>
  <td></td>
  <td colspan="4" style="mso-ignore:colspan"></td>
 </tr>
 <tr height="20" style="height:15.0pt">
  <td colspan="4" height="20" class="xl66" style="height:15.0pt"><span lang="IN">BAB V</span></td>
  <td class="xl66"></td>
  <td colspan="2" style="mso-ignore:colspan"></td>
  <td></td>
  <td colspan="4" style="mso-ignore:colspan"></td>
 </tr>
 <tr height="20" style="height:15.0pt">
  <td colspan="4" height="20" class="xl66" style="height:15.0pt"><span lang="SV" style="mso-ansi-language:SV">KETERLAMBATAN DAN PEMBAYARAN DENDA</span></td>
  <td class="xl66"></td>
  <td colspan="2" style="mso-ignore:colspan"></td>
  <td></td>
  <td colspan="4" style="mso-ignore:colspan"></td>
 </tr>
 <tr height="20" style="height:15.0pt">
  <td colspan="4" height="20" class="xl66" style="height:15.0pt"><span lang="IN" style="line-height:150%">PASAL 9</span></td>
  <td class="xl66"></td>
  <td colspan="2" style="mso-ignore:colspan"></td>
  <td></td>
  <td colspan="4" style="mso-ignore:colspan"></td>
 </tr>
 <tr height="35" style="mso-height-source:userset;height:26.25pt">
  <td height="35" class="xl75" style="height:26.25pt">1</td>
  <td colspan="3" class="xl80" width="545" style="width:409pt"><span lang="SV">PIHAK
  KEDUA harus membayar segala pembayaran yang telah disepakati kepada PIHAK
  PERTAMA sesuai dengan jadwal yang telah disepakati.</span></td>
  <td class="xl67"></td>
  <td colspan="2" style="mso-ignore:colspan"></td>
  <td></td>
  <td colspan="4" style="mso-ignore:colspan"></td>
 </tr>
 <tr height="88" style="mso-height-source:userset;height:66.0pt">
  <td height="88" class="xl75" style="height:66.0pt">2</td>
  <td colspan="3" class="xl81" width="545" style="width:409pt"><span lang="SV">Bilamana
  PIHAK KEDUA membayar kewajibannya melebihi batas waktu tersebut diatas, maka
  harga tersebut secara otomatis berubah mengikuti harga terbaru yang berlaku
  dan PIHAK KEDUA bersedia membayar denda 2,5% ( dua koma lima persen )
  perbulan dari nilai pembayaran yang terlambat dan dihitung secara proposional
  harian sejak tanggal jatuh tempo pembayaran dari jadwal yang disepakati
  diatas.</span></td>
  <td class="xl67"></td>
  <td colspan="2" style="mso-ignore:colspan"></td>
  <td></td>
  <td colspan="4" style="mso-ignore:colspan"></td>
 </tr>
 <tr height="120" style="mso-height-source:userset;height:90.0pt">
  <td height="120" class="xl75" style="height:90.0pt">3</td>
  <td colspan="3" class="xl81" width="545" style="width:409pt"><span lang="EN-US">Untuk
  keterlambatan yang berlangsung 2 ( dua ) kali angsuran, maka perjanjian ini
  dianggap batal dan PIHAK KEDUA telah dianggap melepaskan segala hak-haknya
  termasuk pembayaran tanda jadi dan angsuran yang telah dibayarkan kepada
  PIHAK PERTAMA, dan PIHAK PERTAMA berhak mengambil alih segala hak-hak
  tersebut termasuk pembayaran tanda jadi dan angsuran yang telah dibayar oleh
  PIHAK KEDUA. PIHAK PERTAMA juga dapat mengalihkan hak atas tanah dan bangunan
  tersebut kepada PIHAK KETIGA.</span></td>
  <td class="xl67"></td>
  <td colspan="2" style="mso-ignore:colspan"></td>
  <td></td>
  <td colspan="4" style="mso-ignore:colspan"></td>
 </tr>
 <tr height="20" style="height:15.0pt">
  <td colspan="4" height="20" class="xl76" style="height:15.0pt"></td>
  <td class="xl67"></td>
  <td colspan="2" style="mso-ignore:colspan"></td>
  <td></td>
  <td colspan="4" style="mso-ignore:colspan"></td>
 </tr>
 <tr height="20" style="height:15.0pt">
  <td colspan="4" height="20" class="xl66" style="height:15.0pt"><span lang="IN">BAB VI</span></td>
  <td class="xl66"></td>
  <td colspan="2" style="mso-ignore:colspan"></td>
  <td></td>
  <td colspan="4" style="mso-ignore:colspan"></td>
 </tr>
 <tr height="20" style="height:15.0pt">
  <td colspan="4" height="20" class="xl66" style="height:15.0pt"><span lang="SV">PEMBATALAN</span></td>
  <td class="xl66"></td>
  <td colspan="2" style="mso-ignore:colspan"></td>
  <td></td>
  <td colspan="4" style="mso-ignore:colspan"></td>
 </tr>
 <tr height="20" style="height:15.0pt">
  <td colspan="4" height="20" class="xl66" style="height:15.0pt"><span lang="SV" style="mso-ansi-language:SV">PASAL 10</span></td>
  <td class="xl66"></td>
  <td colspan="2" style="mso-ignore:colspan"></td>
  <td></td>
  <td colspan="4" style="mso-ignore:colspan"></td>
 </tr>
 <tr height="20" style="height:15.0pt">
  <td height="20" class="xl75" style="height:15.0pt"></td>
  <td colspan="2" style="mso-ignore:colspan"></td>
  <td class="xl66"></td>
  <td class="xl66"></td>
  <td colspan="2" style="mso-ignore:colspan"></td>
  <td></td>
  <td colspan="4" style="mso-ignore:colspan"></td>
 </tr>
 <tr height="91" style="mso-height-source:userset;height:68.25pt">
  <td colspan="4" height="91" class="xl73" width="584" style="height:68.25pt;
  width:438pt"><span lang="SV" style="line-height:150%;mso-ansi-language:SV">Dalam
  hal terjadi pembatalan jual beli yang dilakukan oleh PIHAK KEDUA, kedua belah
  pihak sepakat untuk mengecualikan ketentuan pasal 1256,<font class="font9">
  1266 dan 1267 Kitab Undang-Undang Hukum Perdata, sehingga hal tersebut
  tidaklah diperlukan suatu keputusan atau ketetapan Pengadilan Negeri, dan
  selanjutnya PIHAK KEDUA setuju untuk membayar biaya administrasi dan denda
  pembatalan kepada PIHAK PERTAMA dengan perincian sebagai berikut :</font></span></td>
  <td class="xl73" width="63" style="width:47pt"></td>
  <td colspan="2" style="mso-ignore:colspan"></td>
  <td></td>
  <td colspan="4" style="mso-ignore:colspan"></td>
 </tr>
 <tr height="69" style="mso-height-source:userset;height:51.75pt">
  <td height="69" class="xl75" style="height:51.75pt">1</td>
  <td colspan="3" class="xl81" width="545" style="width:409pt"><span lang="SV">Setelah
  terjadi PPJB dan belum terjadi proses pembangunan apabila pembeli membatalkan
  pembelian kavling/bangunan dengan alasan apapun, Maka dikenakan denda sebesar
  25% dari total harga jual. Bila uang yang telah disetor kurang dari 25% maka
  uang yang telah dibayar tidak dapat dikembalikan (Hangus).</span></td>
  <td class="xl67"></td>
  <td colspan="2" style="mso-ignore:colspan"></td>
  <td></td>
  <td colspan="4" style="mso-ignore:colspan"></td>
 </tr>
 <tr height="54" style="mso-height-source:userset;height:40.5pt">
  <td height="54" class="xl75" style="height:40.5pt">2</td>
  <td colspan="3" class="xl81" width="545" style="width:409pt"><span lang="SV">Pada
  saat pembangunan rumah sudah berjalan maka PIHAK KEDUA hanya akan menerima
  pengembalian dana, untuk rumah standart sebesar 50% dari uang yang telah
  disetor dan pengembalian 25% dari uang yang telah disetor untuk rumah tidak
  standart.<span style="mso-spacerun:yes">&nbsp;</span></span></td>
  <td class="xl67"></td>
  <td colspan="2" style="mso-ignore:colspan"></td>
  <td></td>
  <td colspan="4" style="mso-ignore:colspan"></td>
 </tr>
 <tr height="35" style="mso-height-source:userset;height:26.25pt">
  <td height="35" class="xl75" style="height:26.25pt">3</td>
  <td colspan="3" class="xl81" width="545" style="width:409pt"><span lang="SV">Setelah
  fisik bangunan rumah sudah selesai, maka semua uang yang sudah terbayar
  dianggap hangus dan menjadi milik PIHAK PERTAMA.</span></td>
  <td class="xl67"></td>
  <td colspan="2" style="mso-ignore:colspan"></td>
  <td></td>
  <td colspan="4" style="mso-ignore:colspan"></td>
 </tr>
 <tr height="68" style="page-break-before:always;mso-height-source:userset;
  height:51.0pt">
  <td height="68" class="xl75" style="height:51.0pt">4</td>
  <td colspan="3" class="xl81" width="545" style="width:409pt"><span lang="SV">Pengembalian
  sisa dana dari PIHAK PERTAMA kepada PIHAK KEDUA dengan mempertimbangkan ayat
  1, 2, dan 3 Pasal 10 diatas akan dibayarkan setelah kavling/ bangunan/ obyek
  yang diperjanjikan sudah terjual kepada PIHAK KETIGA dan PIHAK KETIGA telah
  melakukan penandatanganan AJB di notaris yang ditunjuk</span></td>
  <td class="xl67"></td>
  <td colspan="2" style="mso-ignore:colspan"></td>
  <td></td>
  <td colspan="4" style="mso-ignore:colspan"></td>
 </tr>
 <tr height="52" style="mso-height-source:userset;height:39.0pt">
  <td height="52" class="xl75" style="height:39.0pt">5</td>
  <td colspan="3" class="xl81" width="545" style="width:409pt"><span lang="EN-US">Kedua
  belah pihak sepakat bahwa dalam hal perjanjian ini menjadi batal/berakhir,
  maka kavling/bangunan yang menjadi obyek perjanjian ini tetap merupakan hak
  milik PIHAK PERTAMA sepenuhnya.</span></td>
  <td class="xl67"></td>
  <td colspan="2" style="mso-ignore:colspan"></td>
  <td></td>
  <td colspan="4" style="mso-ignore:colspan"></td>
 </tr>
 <tr height="20" style="height:15.0pt">
  <td height="20" class="xl75" style="height:15.0pt"></td>
  <td colspan="2" style="mso-ignore:colspan"></td>
  <td class="xl66"></td>
  <td class="xl66"></td>
  <td colspan="2" style="mso-ignore:colspan"></td>
  <td></td>
  <td colspan="4" style="mso-ignore:colspan"></td>
 </tr>
 <tr height="20" style="height:15.0pt">
  <td colspan="4" height="20" class="xl66" style="height:15.0pt"><span lang="IN">BAB
  VII</span></td>
  <td class="xl66"></td>
  <td colspan="2" style="mso-ignore:colspan"></td>
  <td></td>
  <td colspan="4" style="mso-ignore:colspan"></td>
 </tr>
 <tr height="20" style="height:15.0pt">
  <td colspan="4" height="20" class="xl66" style="height:15.0pt"><span lang="SV">GAMBAR
  RENCANA RUMAH</span></td>
  <td class="xl66"></td>
  <td colspan="2" style="mso-ignore:colspan"></td>
  <td></td>
  <td colspan="4" style="mso-ignore:colspan"></td>
 </tr>
 <tr height="20" style="height:15.0pt">
  <td colspan="4" height="20" class="xl66" style="height:15.0pt"><span lang="SV" style="mso-ansi-language:SV">PASAL 11</span></td>
  <td class="xl66"></td>
  <td colspan="2" style="mso-ignore:colspan"></td>
  <td></td>
  <td colspan="4" style="mso-ignore:colspan"></td>
 </tr>
 <tr height="20" style="height:15.0pt">
  <td colspan="4" height="20" class="xl76" style="height:15.0pt"></td>
  <td class="xl66"></td>
  <td colspan="2" style="mso-ignore:colspan"></td>
  <td></td>
  <td colspan="4" style="mso-ignore:colspan"></td>
 </tr>
 <tr height="122" style="mso-height-source:userset;height:91.5pt">
  <td height="122" class="xl75" style="height:91.5pt">1</td>
  <td colspan="3" class="xl81" width="545" style="width:409pt"><span lang="EN-US">Gambar
  rencana bangunan sesuai dengan harga dalam pasal perjanjian ini akan
  disiapkan oleh PIHAK PERTAMA atau pihak lain yang disetujui oleh PIHAK
  PERTAMA. Gambar tersebut akan dikonsultasikan kepada PIHAK KEDUA untuk
  kemungkinan adanya pengembangan sesuai keinginan PIHAK KEDUA yang harus
  disetujui dan ditandatangani oleh PIHAK KEDUA selambat-lambatnya 2 ( dua )
  minggu dari tanggal pengikatan kavling. Perubahan gambar untuk tampak depan /
  muka tidak diperkenankan.</span></td>
  <td class="xl67"></td>
  <td colspan="2" style="mso-ignore:colspan"></td>
  <td></td>
  <td colspan="4" style="mso-ignore:colspan"></td>
 </tr>
 <tr height="69" style="mso-height-source:userset;height:51.75pt">
  <td height="69" class="xl75" style="height:51.75pt">2</td>
  <td colspan="3" class="xl81" width="545" style="width:409pt"><span lang="SV">Apabila
  sampai batas waktu yang telah ditentukan diatas PIHAK KEDUA belum menyetujui
  gambar pra rencana tersebut, maka PIHAK KEDUA dianggap menerima/mengikuti
  gambar rencana rumah standart dengan harga sesuai daftar yang berlaku.</span></td>
  <td class="xl67"></td>
  <td colspan="2" style="mso-ignore:colspan"></td>
  <td></td>
  <td colspan="4" style="mso-ignore:colspan"></td>
 </tr>
 <tr height="52" style="mso-height-source:userset;height:39.0pt">
  <td height="52" class="xl75" style="height:39.0pt">3</td>
  <td colspan="3" class="xl81" width="545" style="width:409pt"><span lang="EN-US">Segala
  perubahan dari PIHAK KEDUA hanya dapat diterima oleh PIHAK PERTAMA selama
  proses konsultasi gambar pra rencana dan sebelum perjanjian ini
  ditandatangani.</span></td>
  <td class="xl67"></td>
  <td colspan="2" style="mso-ignore:colspan"></td>
  <td></td>
  <td colspan="4" style="mso-ignore:colspan"></td>
 </tr>
 <tr height="20" style="height:15.0pt">
  <td colspan="4" height="20" class="xl76" style="height:15.0pt"></td>
  <td class="xl66"></td>
  <td colspan="2" style="mso-ignore:colspan"></td>
  <td></td>
  <td colspan="4" style="mso-ignore:colspan"></td>
 </tr>
 <tr height="20" style="height:15.0pt">
  <td colspan="4" height="20" class="xl66" style="height:15.0pt"><span lang="IN">BAB
  VIII</span></td>
  <td class="xl66"></td>
  <td colspan="2" style="mso-ignore:colspan"></td>
  <td></td>
  <td colspan="4" style="mso-ignore:colspan"></td>
 </tr>
 <tr height="20" style="height:15.0pt">
  <td colspan="4" height="20" class="xl66" style="height:15.0pt"><span lang="SV">PEMBANGUNAN</span></td>
  <td class="xl66"></td>
  <td colspan="2" style="mso-ignore:colspan"></td>
  <td></td>
  <td colspan="4" style="mso-ignore:colspan"></td>
 </tr>
 <tr height="20" style="height:15.0pt">
  <td colspan="4" height="20" class="xl66" style="height:15.0pt"><span lang="SV" style="mso-ansi-language:SV">PASAL 12</span></td>
  <td class="xl66"></td>
  <td colspan="2" style="mso-ignore:colspan"></td>
  <td></td>
  <td colspan="4" style="mso-ignore:colspan"></td>
 </tr>
 <tr height="20" style="height:15.0pt">
  <td colspan="4" height="20" class="xl76" style="height:15.0pt"></td>
  <td class="xl68"></td>
  <td colspan="2" style="mso-ignore:colspan"></td>
  <td></td>
  <td colspan="4" style="mso-ignore:colspan"></td>
 </tr>
 <tr height="71" style="mso-height-source:userset;height:53.25pt">
  <td height="71" class="xl75" style="height:53.25pt">1</td>
  <td colspan="3" class="xl77" width="545" style="width:409pt"><span lang="SV">PIHAK
  PERTAMA akan melaksanakan pembangunan fisik rumah dimulai apabila telah
  disepakati oleh para pihak dengan melihat kesiapan dilapangan atau
  selambat-lambatnya 30 (tiga puluh) hari semenjak disetujuinya gambar rencana
  dan ditandatanganinya perjanjian oleh kedua belah pihak,<span style="mso-spacerun:yes">&nbsp;</span></span></td>
  <td class="xl67"></td>
  <td colspan="2" style="mso-ignore:colspan"></td>
  <td></td>
  <td colspan="4" style="mso-ignore:colspan"></td>
 </tr>
 <tr height="55" style="mso-height-source:userset;height:41.25pt">
  <td height="55" class="xl75" style="height:41.25pt"></td>
  <td colspan="3" class="xl81" width="545" style="width:409pt"><span lang="SV">a.<font class="font8">&nbsp;&nbsp; </font><font class="font7">Dan PIHAK KEDUA
  membayar angsuran mencapai nilai 30% ( tiga puluh persen ) dari harga jual
  yang telah disepakati dan setelah disetujuinya Kredit Pemilikan Rumah ( KPR )
  dari bank (bila pembiayaan melalui fasilitas kredit Bank).<span style="mso-spacerun:yes">&nbsp;</span></font></span></td>
  <td class="xl73" width="63" style="width:47pt"></td>
  <td colspan="2" style="mso-ignore:colspan"></td>
  <td></td>
  <td colspan="4" style="mso-ignore:colspan"></td>
 </tr>
 <tr height="34" style="mso-height-source:userset;height:25.5pt">
  <td height="34" class="xl75" style="height:25.5pt"></td>
  <td colspan="3" class="xl81" width="545" style="width:409pt"><span lang="SV">b.<font class="font8">&nbsp;&nbsp; </font><font class="font7">Atau untuk pembelian
  secara Tunai, PIHAK KEDUA telah mencapai pembayaran 60% dari Total nilai yang
  harus dibayar tersebut di atas.</font></span></td>
  <td class="xl67"></td>
  <td colspan="2" style="mso-ignore:colspan"></td>
  <td></td>
  <td colspan="4" style="mso-ignore:colspan"></td>
 </tr>
 <tr height="187" style="mso-height-source:userset;height:140.25pt">
  <td height="187" class="xl75" style="height:140.25pt">2</td>
  <td colspan="3" class="xl81" width="545" style="width:409pt"><span lang="SV">PIHAK
  PERTAMA berkewajiban menyelesaikan pembangunan rumah milik PIHAK KEDUA dalam
  jangka waktu selambat-lambatnya 180 ( seratus delapan puluh ) hari untuk
  bangunan 1 lantai dan 240 ( dua ratus empat puluh ) hari untuk bangunan 2
  lantai dihitung sejak pembayaran dalam pasal 12 ayat 1 terpenuhi. Bila dalam
  jangka waktu yang telah ditentukan PIHAK PERTAMA belum menyelesaikan
  pambangunan rumah tersebut, maka PIHAK KEDUA pada bulan setelah bulan
  penyelesaian rumah yang telah ditentukan dalam Adendum akan mendapat ganti
  rugi atas keterlambatan penyelesaian PIHAK PERTAMA sebesar 0,05% ( nol koma
  nol lima persen ) per hari dari total uang yang telah dibayarkan oleh PIHAK
  KEDUA kepada PIHAK PERTAMA, dengan nilai setinggi-tingginya sebesar 2% ( dua
  persen ) dari total uang yang telah dibayarkan PIHAK KEDUA kepada PIHAK
  PERTAMA.</span></td>
  <td class="xl67"></td>
  <td colspan="2" style="mso-ignore:colspan"></td>
  <td></td>
  <td colspan="4" style="mso-ignore:colspan"></td>
 </tr>
 <tr height="89" style="mso-height-source:userset;height:66.75pt">
  <td height="89" class="xl75" style="height:66.75pt">3</td>
  <td colspan="3" class="xl81" width="545" style="width:409pt"><span lang="SV">Dalam
  hal terjadi keterlambatan masa pembangunan sebagaimana diatur dalam pasal 12
  ayat 2 perjanjian ini, dikecualikan untuk hal-hal yang diluar kemampuan PIHAK
  PERTAMA, seperti sambungan listrik, gas dan air yang sepenuhnya tergantung
  pada ketersediaan jaringan, daya meter, dan meter dari pihak PLN, PDAM, atau
  instansi yang berwenang untuk itu.</span></td>
  <td class="xl67"></td>
  <td colspan="2" style="mso-ignore:colspan"></td>
  <td></td>
  <td colspan="4" style="mso-ignore:colspan"></td>
 </tr>
 <tr height="20" style="height:15.0pt">
  <td colspan="4" height="20" class="xl76" style="height:15.0pt"></td>
  <td class="xl66"></td>
  <td colspan="2" style="mso-ignore:colspan"></td>
  <td></td>
  <td colspan="4" style="mso-ignore:colspan"></td>
 </tr>
 <tr height="20" style="height:15.0pt">
  <td colspan="4" height="20" class="xl66" style="height:15.0pt"><span lang="IN">BAB IX</span></td>
  <td class="xl66"></td>
  <td colspan="2" style="mso-ignore:colspan"></td>
  <td></td>
  <td colspan="4" style="mso-ignore:colspan"></td>
 </tr>
 <tr height="20" style="height:15.0pt">
  <td colspan="4" height="20" class="xl66" style="height:15.0pt"><span lang="SV">PERUBAHAN
  BANGUNAN</span></td>
  <td class="xl66"></td>
  <td colspan="2" style="mso-ignore:colspan"></td>
  <td></td>
  <td colspan="4" style="mso-ignore:colspan"></td>
 </tr>
 <tr height="20" style="height:15.0pt">
  <td colspan="4" height="20" class="xl66" style="height:15.0pt"><span lang="SV" style="mso-ansi-language:SV">PASAL 13</span></td>
  <td class="xl66"></td>
  <td colspan="2" style="mso-ignore:colspan"></td>
  <td></td>
  <td colspan="4" style="mso-ignore:colspan"></td>
 </tr>
 <tr height="20" style="height:15.0pt">
  <td colspan="4" height="20" class="xl76" style="height:15.0pt"></td>
  <td class="xl66"></td>
  <td colspan="2" style="mso-ignore:colspan"></td>
  <td></td>
  <td colspan="4" style="mso-ignore:colspan"></td>
 </tr>
 <tr height="20" style="height:15.0pt">
  <td height="20" class="xl75" style="height:15.0pt"></td>
  <td colspan="2" style="mso-ignore:colspan"></td>
  <td class="xl66"></td>
  <td class="xl66"></td>
  <td colspan="2" style="mso-ignore:colspan"></td>
  <td></td>
  <td colspan="4" style="mso-ignore:colspan"></td>
 </tr>
 <tr height="35" style="mso-height-source:userset;height:26.25pt">
  <td height="35" class="xl75" style="height:26.25pt">1</td>
  <td colspan="3" class="xl81" width="545" style="width:409pt"><span lang="EN-US">PIHAK
  KEDUA tidak diperbolehkan mengajukan dan melakukan perubahan dan penambahan
  bangunan selama dimulainya pelaksanaan pembangunan.</span></td>
  <td class="xl67"></td>
  <td colspan="2" style="mso-ignore:colspan"></td>
  <td></td>
  <td colspan="4" style="mso-ignore:colspan"></td>
 </tr>
 <tr height="138" style="mso-height-source:userset;height:103.5pt">
  <td height="138" class="xl75" style="height:103.5pt">2</td>
  <td colspan="3" class="xl81" width="545" style="width:409pt"><span lang="SV">Setelah
  perjanjian ini ditandatangani, segala bentuk permintaan penambahan/perubahan
  bangunan dapat dilaksanakan sebelum proses pembangunan dimulai dengan batas
  waktu seperti dalam pasal 12 ayat 1. Hal ini dapat disampaikan melalui bagian
  Property Advisor untuk kemudian akan dihitung nilainya oleh bagian estimator.
  PIHAK KEDUA harus membayar lunas segala pekerjaan tambah yang telah
  disepakati antara PIHAK PERTAMA dengan PIHAK KEDUA selambat-lambatnya 1 (
  satu ) minggu dari tanggal kesepakatan pekerjaan tambah tersebut. Pekerjaan
  akan dilaksanakan setelah pembayaran pekerjaan tambah yang disepakati
  dibayarkan lunas oleh PIHAK KEDUA.</span></td>
  <td class="xl67"></td>
  <td colspan="2" style="mso-ignore:colspan"></td>
  <td></td>
  <td colspan="4" style="mso-ignore:colspan"></td>
 </tr>
 <tr height="87" style="mso-height-source:userset;height:65.25pt">
  <td height="87" class="xl75" style="height:65.25pt">3</td>
  <td colspan="3" class="xl81" width="545" style="width:409pt"><span lang="SV">Biaya-biaya
  yang akan timbul dikarenakan perubahan / pekerjaan tambah akan dibuat secara
  tertulis pada lampiran tambahan pekerjaan / adendum perjanjian yang
  disepakati bersama dan merupakan bagian yang tidak terpisahkan dari
  perjanjian ini. Perubahan desain dan / atau penambahan pekerjaan bangunan
  tidak mempengaruhi jadwal pembayaran yang telah ditentukan dalam pasal 5 dan
  6 perjanjian ini.</span></td>
  <td class="xl67"></td>
  <td colspan="2" style="mso-ignore:colspan"></td>
  <td></td>
  <td colspan="4" style="mso-ignore:colspan"></td>
 </tr>
 <tr height="66" style="mso-height-source:userset;height:49.5pt">
  <td height="66" class="xl75" style="height:49.5pt">4</td>
  <td colspan="3" class="xl73" width="545" style="width:409pt"><span lang="SV">PIHAK
  KEDUA tidak diperkenankan memberikan perintah atau order pekerjaan kepada
  staff dan / atau tenaga kerja lapangan secara langsung. Apabila sampai
  terjadi hal yang demikian, maka segala resiko dan tanggung jawab akan
  ditanggung oleh PIHAK KEDUA.</span></td>
  <td class="xl67"></td>
  <td colspan="2" style="mso-ignore:colspan"></td>
  <td></td>
  <td colspan="4" style="mso-ignore:colspan"></td>
 </tr>
 <tr height="47" style="mso-height-source:userset;height:35.25pt">
  <td height="47" class="xl75" style="height:35.25pt"></td>
  <td colspan="3" class="xl73" width="545" style="width:409pt"><span lang="SV" style="line-height:150%;mso-ansi-language:SV">Segala hal-hal yang berhubungan
  dengan pelaksanaan pembangunan harus disampaikan melalui bagian Property
  Advisor yang nantinya akan dilanjutkan ke bagian staff yang bertanggung
  jawab.</span></td>
  <td class="xl67"></td>
  <td colspan="2" style="mso-ignore:colspan"></td>
  <td></td>
  <td colspan="4" style="mso-ignore:colspan"></td>
 </tr>
 <tr height="20" style="height:15.0pt">
  <td colspan="4" height="20" class="xl76" style="height:15.0pt"></td>
  <td class="xl67"></td>
  <td colspan="2" style="mso-ignore:colspan"></td>
  <td></td>
  <td colspan="4" style="mso-ignore:colspan"></td>
 </tr>
 <tr height="20" style="height:15.0pt">
  <td colspan="4" height="20" class="xl66" style="height:15.0pt"><span lang="IN">BAB X</span></td>
  <td class="xl66"></td>
  <td colspan="2" style="mso-ignore:colspan"></td>
  <td></td>
  <td colspan="4" style="mso-ignore:colspan"></td>
 </tr>
 <tr height="20" style="height:15.0pt">
  <td colspan="4" height="20" class="xl66" style="height:15.0pt"><span lang="EN-US" style="mso-ansi-language:EN-US">SERAH TERIMA BANGUNAN</span></td>
  <td class="xl66"></td>
  <td colspan="2" style="mso-ignore:colspan"></td>
  <td></td>
  <td colspan="4" style="mso-ignore:colspan"></td>
 </tr>
 <tr height="20" style="height:15.0pt">
  <td colspan="4" height="20" class="xl66" style="height:15.0pt"><span lang="IN">PASAL
  14</span></td>
  <td class="xl66"></td>
  <td colspan="2" style="mso-ignore:colspan"></td>
  <td></td>
  <td colspan="4" style="mso-ignore:colspan"></td>
 </tr>
 <tr height="20" style="height:15.0pt">
  <td colspan="4" height="20" class="xl76" style="height:15.0pt"></td>
  <td class="xl66"></td>
  <td colspan="2" style="mso-ignore:colspan"></td>
  <td></td>
  <td colspan="4" style="mso-ignore:colspan"></td>
 </tr>
 <tr height="20" style="height:15.0pt">
  <td height="20" class="xl75" style="height:15.0pt">1</td>
  <td colspan="3" class="xl82"><span lang="SV">PIHAK KEDUA setuju dan mufakat :</span></td>
  <td class="xl69"></td>
  <td colspan="2" style="mso-ignore:colspan"></td>
  <td></td>
  <td colspan="4" style="mso-ignore:colspan"></td>
 </tr>
 <tr height="170" style="mso-height-source:userset;height:127.5pt">
  <td height="170" class="xl75" style="height:127.5pt"></td>
  <td colspan="3" class="xl83" width="545" style="width:409pt"><span lang="SV">a.<font class="font10">&nbsp;&nbsp; </font><font class="font9">Apabila pembelian
  dengan fasilitas kredit kepemilikan rumah (KPR) maka PIHAK KEDUA menerima
  pengajuan bank pemberi kredit yang menjadi mitra PIHAK PERTAMA dan bersedia
  melaksanakan akad jual beli di hadapan notaris dalam waktu maksimal 14 hari
  kerja setelah DP lunas dan/atau mendapatkan pemberitahuan persetujuan KPR.
  Dengan mempertimbangkan kesiapan pihak ketiga. Apabila terjadi kerterlambatan
  pelaksanaan AJB dikarenakan PIHAK KEDUA maka PIHAK KEDUA bersedia membayar
  denda 2,5% (dua koma lima persen) perbulan dari nilai KPR dan jika dalam
  waktu 3 bulan belum terjadi AJB maka perjajian ini batal dan untuk
  selanjutnya PIHAK KEDUA dikenakan denda sesuai dengan pasal 10 dalam
  perjanjian ini.</font></span></td>
  <td class="xl69"></td>
  <td colspan="2" style="mso-ignore:colspan"></td>
  <td></td>
  <td colspan="4" style="mso-ignore:colspan"></td>
 </tr>
 <tr height="137" style="mso-height-source:userset;height:102.75pt">
  <td height="137" class="xl75" style="height:102.75pt"></td>
  <td colspan="3" class="xl83" width="545" style="width:409pt"><span lang="SV">b.<font class="font10">&nbsp;&nbsp; </font><font class="font9">Apabila pembelian
  secara cash keras atau cash bertahap, PIHAK KEDUA bersedia melaksanakan akad
  jual beli di hadapan notaris dalam waktu maksimal 14 hari kerja setelah
  pembayaran lunas. Dengan mempertimbangkan kesiapan pihak ketiga. Apabila
  terjadi kerterlambatan pelaksanaan AJB dikarenakan PIHAK KEDUA maka PIHAK
  KEDUA bersedia membayar denda 2,5% (dua koma lima persen) perbulan dari harga
  property dan jika dalam waktu 3 bulan belum terjadi AJB maka perjanjian ini
  batal dan untuk selanjutnya PIHAK KEDUA dikenakan denda sesuai dengan pasal
  10 dalam perjanjian ini.</font></span></td>
  <td class="xl69"></td>
  <td colspan="2" style="mso-ignore:colspan"></td>
  <td></td>
  <td colspan="4" style="mso-ignore:colspan"></td>
 </tr>
 <tr height="84" style="mso-height-source:userset;height:63.0pt">
  <td height="84" class="xl75" style="height:63.0pt">2</td>
  <td colspan="3" class="xl81" width="545" style="width:409pt"><span lang="SV">PIHAK
  KEDUA menerima dan setuju penyerahan bangunan rumah ( serah terima kunci )
  dari PIHAK PERTAMA dilaksanakan, apabila PIHAK KEDUA telah melunasi seluruh
  kewajibannya kepada PIHAK PERTAMA seperti tercantum dalam pasal 5 dan pasal 6
  perjanjian ini. Sebelum diadakan serah terima dari PIHAK PERTAMA kepada PIHAK
  KEDUA, maka PIHAK KEDUA tidak diperkenankan melakukan hal-hal sebagai
  berikut:</span></td>
  <td class="xl67"></td>
  <td colspan="2" style="mso-ignore:colspan"></td>
  <td></td>
  <td colspan="4" style="mso-ignore:colspan"></td>
 </tr>
 <tr height="52" style="mso-height-source:userset;height:39.0pt">
  <td height="52" class="xl75" style="height:39.0pt"></td>
  <td colspan="3" class="xl81" width="545" style="width:409pt"><span lang="SV">a.<font class="font8">&nbsp;&nbsp;&nbsp;&nbsp; </font><font class="font7">PIHAK KEDUA
  tidak diperkenankan untuk melaksanakan pembangunan, mengubah maupun menambah
  bangunan, baik yang dilaksanakan sendiri maupun melalui PIHAK KETIGA.</font></span></td>
  <td class="xl67"></td>
  <td colspan="2" style="mso-ignore:colspan"></td>
  <td></td>
  <td colspan="4" style="mso-ignore:colspan"></td>
 </tr>
 <tr height="37" style="mso-height-source:userset;height:27.75pt">
  <td height="37" class="xl75" style="height:27.75pt"></td>
  <td colspan="3" class="xl77" width="545" style="width:409pt"><span lang="SV">b.<font class="font8">&nbsp;&nbsp;&nbsp;&nbsp; </font><font class="font7">PIHAK KEDUA
  tidak diperkenankan untuk menempati bangunan atau menempatkan PIHAK KETIGA
  dengan alasan apapun dilokasi pembangunan.</font></span></td>
  <td class="xl67"></td>
  <td colspan="2" style="mso-ignore:colspan"></td>
  <td></td>
  <td colspan="4" style="mso-ignore:colspan"></td>
 </tr>
 <tr height="35" style="mso-height-source:userset;height:26.25pt">
  <td height="35" class="xl75" style="height:26.25pt"></td>
  <td colspan="3" class="xl77" width="545" style="width:409pt"><span lang="SV">c.<font class="font8">&nbsp;&nbsp;&nbsp;&nbsp; </font><font class="font7">PIHAK KEDUA
  tidak diperkenankan untuk memasukkan dan/atau menempatkan barang apapun juga
  dilokasi pembangunan.</font></span></td>
  <td class="xl67"></td>
  <td colspan="2" style="mso-ignore:colspan"></td>
  <td></td>
  <td colspan="4" style="mso-ignore:colspan"></td>
 </tr>
 <tr height="136" style="mso-height-source:userset;height:102.0pt">
  <td height="136" class="xl75" style="height:102.0pt">3</td>
  <td colspan="3" class="xl81" width="545" style="width:409pt"><span lang="SV">Penyerahan
  kunci rumah akan dibuatkan dengan Berita Acara Serah Terima rumah tersendiri
  yang merupakan bagian yang tidak terpisahkan dari perjanjian ini. PIHAK
  PERTAMA akan memberikan informasi kepada PIHAK KEDUA bilamana serah terima
  telah dapat dilaksanakan, apabila dalan jangka waktu 10 (sepuluh ) hari kerja
  sejak tanggal pemberitahuan dan atau cetak dokumen BAST PIHAK KEDUA belum
  dapat menandatangani Berita Acara Serah Terima maka dengan lewatnya waktu
  tersebut menjadi bukti bahwa PIHAK KEDUA telah menerima dengan baik
  bangunan/kavling tersebut dan selanjutnya menjadi tanggung jawab PIHAK KEDUA
  sepenuhnya.</span></td>
  <td class="xl67"></td>
  <td colspan="2" style="mso-ignore:colspan"></td>
  <td></td>
  <td colspan="4" style="mso-ignore:colspan"></td>
 </tr>
 <tr height="54" style="mso-height-source:userset;height:40.5pt">
  <td height="54" class="xl75" style="height:40.5pt">4</td>
  <td colspan="3" class="xl81" width="545" style="width:409pt"><span lang="SV">Sejak
  diserahkannya bangunan dari PIHAK PERTAMA kepada PIHAK KEDUA, maka segala
  biaya-biaya yang berkaitan dengan fasilitas pada bangunan/kavling tersebut
  menjadi tanggung jawab PIHAK KEDUA.</span></td>
  <td class="xl67"></td>
  <td colspan="2" style="mso-ignore:colspan"></td>
  <td></td>
  <td colspan="4" style="mso-ignore:colspan"></td>
 </tr>
 <tr height="20" style="height:15.0pt">
  <td colspan="4" height="20" class="xl76" style="height:15.0pt"></td>
  <td class="xl66"></td>
  <td colspan="2" style="mso-ignore:colspan"></td>
  <td></td>
  <td colspan="4" style="mso-ignore:colspan"></td>
 </tr>
 <tr height="20" style="height:15.0pt">
  <td colspan="4" height="20" class="xl66" style="height:15.0pt"><span lang="IN">BAB XI</span></td>
  <td class="xl66"></td>
  <td colspan="2" style="mso-ignore:colspan"></td>
  <td></td>
  <td colspan="4" style="mso-ignore:colspan"></td>
 </tr>
 <tr height="20" style="height:15.0pt">
  <td colspan="4" height="20" class="xl66" style="height:15.0pt"><span lang="IN">JAMINAN</span></td>
  <td class="xl66"></td>
  <td colspan="2" style="mso-ignore:colspan"></td>
  <td></td>
  <td colspan="4" style="mso-ignore:colspan"></td>
 </tr>
 <tr height="20" style="height:15.0pt">
  <td colspan="4" height="20" class="xl66" style="height:15.0pt"><span lang="IN">PASAL
  15</span></td>
  <td class="xl66"></td>
  <td colspan="2" style="mso-ignore:colspan"></td>
  <td></td>
  <td colspan="4" style="mso-ignore:colspan"></td>
 </tr>
 <tr height="20" style="height:15.0pt">
  <td colspan="4" height="20" class="xl76" style="height:15.0pt"></td>
  <td class="xl66"></td>
  <td colspan="2" style="mso-ignore:colspan"></td>
  <td></td>
  <td colspan="4" style="mso-ignore:colspan"></td>
 </tr>
 <tr height="37" style="mso-height-source:userset;height:27.75pt">
  <td height="37" class="xl75" style="height:27.75pt">1</td>
  <td colspan="3" class="xl81" width="545" style="width:409pt"><span lang="IN" style="line-height:150%;mso-fareast-font-family:Arial">PIHAK PERTAMA menjamin
  bahwasanya objek hukum dalam hal ini rumah beserta hak kepemilikannya
  merupakan hak milik dari PIHAK PERTAMA.</span></td>
  <td class="xl67"></td>
  <td colspan="2" style="mso-ignore:colspan"></td>
  <td></td>
  <td colspan="4" style="mso-ignore:colspan"></td>
 </tr>
 <tr height="37" style="mso-height-source:userset;height:27.75pt">
  <td height="37" class="xl75" style="height:27.75pt">2</td>
  <td colspan="3" class="xl81" width="545" style="width:409pt"><span lang="IN" style="line-height:150%;mso-fareast-font-family:Arial">PIHAK PERTAMA menjamin
  bahwasanya objek hukum dalam hal ini rumah dan hak kepemilikannya tidak dalam
  sengketa.</span></td>
  <td class="xl67"></td>
  <td colspan="2" style="mso-ignore:colspan"></td>
  <td></td>
  <td colspan="4" style="mso-ignore:colspan"></td>
 </tr>
 <tr height="37" style="mso-height-source:userset;height:27.75pt">
  <td height="37" class="xl75" style="height:27.75pt">3</td>
  <td colspan="3" class="xl81" width="545" style="width:409pt"><span lang="IN" style="line-height:150%;mso-fareast-font-family:Arial">PIHAK PERTAMA menjamin
  bahwasanya objek hukum dalam hal ini rumah dan hak kepemilikannya belum atau
  tidak dijual kepada Pihak Lain.</span></td>
  <td class="xl67"></td>
  <td colspan="2" style="mso-ignore:colspan"></td>
  <td></td>
  <td colspan="4" style="mso-ignore:colspan"></td>
 </tr>
 <tr height="37" style="mso-height-source:userset;height:27.75pt">
  <td height="37" class="xl75" style="height:27.75pt">4</td>
  <td colspan="3" class="xl81" width="545" style="width:409pt"><span lang="IN" style="line-height:150%;mso-fareast-font-family:Arial">PIHAK PERTAMA menjamin
  bahwasanya objek hukum dalam hal ini rumah dan hak kepemilikannya tidak
  dipergunakan sebagai jaminan hutang dengan cara apapun.</span></td>
  <td class="xl67"></td>
  <td colspan="2" style="mso-ignore:colspan"></td>
  <td></td>
  <td colspan="4" style="mso-ignore:colspan"></td>
 </tr>
 <tr height="85" style="mso-height-source:userset;height:63.75pt">
  <td height="85" class="xl75" style="height:63.75pt">5</td>
  <td colspan="3" class="xl81" width="545" style="width:409pt"><span lang="SV">PIHAK
  PERTAMA akan memberikan jaminan kepada PIHAK KEDUA selama 60 (enam puluh)
  hari apabila terjadi kerusakan yang disebabkan oleh kelalaian PIHAK PERTAMA
  sejak realisasi penyerahan rumah (Berita Acara Serah Terima) sesuai Pasal 14,
  Perjanjian Pendahuluan Jual Beli Rumah ini, kecuali bila terjadi force majeur
  (bencana alam, huru hara, pemogokan, perang).</span></td>
  <td class="xl67"></td>
  <td colspan="2" style="mso-ignore:colspan"></td>
  <td></td>
  <td colspan="4" style="mso-ignore:colspan"></td>
 </tr>
 <tr height="32" style="mso-height-source:userset;height:24.0pt">
  <td height="32" class="xl75" style="height:24.0pt"></td>
  <td colspan="3" class="xl81" width="545" style="width:409pt"><span lang="SV" style="line-height:150%;mso-ansi-language:SV">Bila telah melewati jangka
  waktu dan masa perawatan 60 ( enam puluh ) hari terjadi keluhan / complain,
  maka akan menjadi tanggung jawab PIHAK KEDUA secara penuh.</span></td>
  <td class="xl67"></td>
  <td colspan="2" style="mso-ignore:colspan"></td>
  <td></td>
  <td colspan="4" style="mso-ignore:colspan"></td>
 </tr>
 <tr height="54" style="mso-height-source:userset;height:40.5pt">
  <td height="54" class="xl75" style="height:40.5pt">6</td>
  <td colspan="3" class="xl81" width="545" style="width:409pt"><span lang="IN" style="line-height:150%;mso-fareast-font-family:Arial">PIHAK KEDUA menjamin
  bahwasannya akan melakukan pembayaran atas objek hukum dalam hal ini rumah
  beserta hak kepemilikannya secara tunai atau bertahap sampai lunas kepada
  PIHAK PERTAMA.</span></td>
  <td class="xl67"></td>
  <td colspan="2" style="mso-ignore:colspan"></td>
  <td></td>
  <td colspan="4" style="mso-ignore:colspan"></td>
 </tr>
 <tr height="20" style="height:15.0pt">
  <td colspan="4" height="20" class="xl76" style="height:15.0pt"></td>
  <td class="xl66"></td>
  <td colspan="2" style="mso-ignore:colspan"></td>
  <td></td>
  <td colspan="4" style="mso-ignore:colspan"></td>
 </tr>
 <tr height="20" style="height:15.0pt">
  <td colspan="4" height="20" class="xl76" style="height:15.0pt"></td>
  <td class="xl66"></td>
  <td colspan="2" style="mso-ignore:colspan"></td>
  <td></td>
  <td colspan="4" style="mso-ignore:colspan"></td>
 </tr>
 <tr height="20" style="height:15.0pt">
  <td colspan="4" height="20" class="xl76" style="height:15.0pt"></td>
  <td class="xl66"></td>
  <td colspan="2" style="mso-ignore:colspan"></td>
  <td></td>
  <td colspan="4" style="mso-ignore:colspan"></td>
 </tr>
 <tr height="20" style="height:15.0pt">
  <td colspan="4" height="20" class="xl66" style="height:15.0pt"><span lang="IN" style="line-height:150%">PASAL 16</span></td>
  <td class="xl66"></td>
  <td colspan="2" style="mso-ignore:colspan"></td>
  <td></td>
  <td colspan="4" style="mso-ignore:colspan"></td>
 </tr>
 <tr height="35" style="mso-height-source:userset;height:26.25pt">
  <td colspan="4" height="35" class="xl81" width="584" style="height:26.25pt;
  width:438pt"><span lang="SV" style="line-height:150%;mso-ansi-language:SV">Status
  hak atas tanah sepenuhnya menyesuaikan dengan ketentuan peraturan yang
  berlaku dimana lokasi rumah ini berada dan Badan Pertanahan Nasional ( BPN )
  setempat.</span></td>
  <td class="xl67"></td>
  <td colspan="2" style="mso-ignore:colspan"></td>
  <td></td>
  <td colspan="4" style="mso-ignore:colspan"></td>
 </tr>
 <tr height="20" style="height:15.0pt">
  <td colspan="4" height="20" class="xl76" style="height:15.0pt"></td>
  <td class="xl67"></td>
  <td colspan="2" style="mso-ignore:colspan"></td>
  <td></td>
  <td colspan="4" style="mso-ignore:colspan"></td>
 </tr>
 <tr height="20" style="height:15.0pt">
  <td colspan="4" height="20" class="xl66" style="height:15.0pt"><span lang="IN">BAB V</span></td>
  <td class="xl66"></td>
  <td colspan="2" style="mso-ignore:colspan"></td>
  <td></td>
  <td colspan="4" style="mso-ignore:colspan"></td>
 </tr>
 <tr height="20" style="height:15.0pt">
  <td colspan="4" height="20" class="xl66" style="height:15.0pt"><span lang="IN">WANPRESTASI</span></td>
  <td class="xl66"></td>
  <td colspan="2" style="mso-ignore:colspan"></td>
  <td></td>
  <td colspan="4" style="mso-ignore:colspan"></td>
 </tr>
 <tr height="20" style="height:15.0pt">
  <td colspan="4" height="20" class="xl66" style="height:15.0pt"><span lang="IN">PASAL
  17</span></td>
  <td class="xl66"></td>
  <td colspan="2" style="mso-ignore:colspan"></td>
  <td></td>
  <td colspan="4" style="mso-ignore:colspan"></td>
 </tr>
 <tr height="20" style="height:15.0pt">
  <td colspan="4" height="20" class="xl76" style="height:15.0pt"></td>
  <td class="xl66"></td>
  <td colspan="2" style="mso-ignore:colspan"></td>
  <td></td>
  <td colspan="4" style="mso-ignore:colspan"></td>
 </tr>
 <tr height="50" style="mso-height-source:userset;height:37.5pt">
  <td height="50" class="xl75" style="height:37.5pt">1</td>
  <td colspan="3" class="xl77" width="545" style="width:409pt"><span lang="IN" style="line-height:150%;mso-fareast-font-family:Arial">PIHAK PERTAMA
  dinyatakan Wanprestasi atau lalai atau ingkar janji apabila melanggar
  ketentuan yang tercantum didalam Pasal 12 dalam Perjanjian Pendahuluan Jual
  Beli Rumah ini.</span></td>
  <td class="xl67"></td>
  <td colspan="2" style="mso-ignore:colspan"></td>
  <td></td>
  <td colspan="4" style="mso-ignore:colspan"></td>
 </tr>
 <tr height="53" style="mso-height-source:userset;height:39.75pt">
  <td height="53" class="xl75" style="height:39.75pt">2</td>
  <td colspan="3" class="xl81" width="545" style="width:409pt"><span lang="IN" style="line-height:150%;mso-fareast-font-family:Arial">PIHAK KEDUA dinyatakan
  Wanprestasi atau lalai atau ingkar janji apabila melanggar ketentuan yang
  tercantum didalam Pasal 8 dan Pasal 14 Perjanjian Pendahuluan Jual Beli Rumah
  ini.</span></td>
  <td class="xl67"></td>
  <td colspan="2" style="mso-ignore:colspan"></td>
  <td></td>
  <td colspan="4" style="mso-ignore:colspan"></td>
 </tr>
 <tr height="20" style="height:15.0pt">
  <td colspan="4" height="20" class="xl76" style="height:15.0pt"></td>
  <td class="xl67"></td>
  <td colspan="2" style="mso-ignore:colspan"></td>
  <td></td>
  <td colspan="4" style="mso-ignore:colspan"></td>
 </tr>
 <tr height="20" style="height:15.0pt">
  <td colspan="4" height="20" class="xl66" style="height:15.0pt"><span lang="IN">BAB VI</span></td>
  <td class="xl66"></td>
  <td colspan="2" style="mso-ignore:colspan"></td>
  <td></td>
  <td colspan="4" style="mso-ignore:colspan"></td>
 </tr>
 <tr height="20" style="height:15.0pt">
  <td colspan="4" height="20" class="xl66" style="height:15.0pt"><span lang="IN">SANKSI</span></td>
  <td class="xl66"></td>
  <td colspan="2" style="mso-ignore:colspan"></td>
  <td></td>
  <td colspan="4" style="mso-ignore:colspan"></td>
 </tr>
 <tr height="20" style="height:15.0pt">
  <td colspan="4" height="20" class="xl66" style="height:15.0pt"><span lang="IN">PASAL
  18</span></td>
  <td class="xl66"></td>
  <td colspan="2" style="mso-ignore:colspan"></td>
  <td></td>
  <td colspan="4" style="mso-ignore:colspan"></td>
 </tr>
 <tr height="20" style="height:15.0pt">
  <td colspan="4" height="20" class="xl76" style="height:15.0pt"></td>
  <td class="xl66"></td>
  <td colspan="2" style="mso-ignore:colspan"></td>
  <td></td>
  <td colspan="4" style="mso-ignore:colspan"></td>
 </tr>
 <tr height="34" style="mso-height-source:userset;height:25.5pt">
  <td height="34" class="xl75" style="height:25.5pt">1</td>
  <td colspan="3" class="xl81" width="545" style="width:409pt"><span lang="IN" style="line-height:150%;mso-fareast-font-family:Arial">PIHAK KEDUA akan
  dikenakan Sanksi apabila melanggar ketentuan yang diatur didalam Pasal 8 dan
  Pasal 14 dalam Perjanjian Pendahuluan Jual Beli Rumah ini.</span></td>
  <td class="xl67"></td>
  <td colspan="2" style="mso-ignore:colspan"></td>
  <td></td>
  <td colspan="4" style="mso-ignore:colspan"></td>
 </tr>
 <tr height="37" style="mso-height-source:userset;height:27.75pt">
  <td height="37" class="xl75" style="height:27.75pt">2</td>
  <td colspan="3" class="xl81" width="545" style="width:409pt"><span lang="IN" style="line-height:150%;mso-fareast-font-family:Arial">PIHAK PERTAMA dikenai
  Sanki apabila melanggar ketentuan yang tercantum didalam Pasal 12 dalam
  Perjanjian Pendahuluan Jual Beli Rumah ini.</span></td>
  <td class="xl67"></td>
  <td colspan="2" style="mso-ignore:colspan"></td>
  <td></td>
  <td colspan="4" style="mso-ignore:colspan"></td>
 </tr>
 <tr height="20" style="height:15.0pt">
  <td colspan="4" height="20" class="xl76" style="height:15.0pt"></td>
  <td class="xl67"></td>
  <td colspan="2" style="mso-ignore:colspan"></td>
  <td></td>
  <td colspan="4" style="mso-ignore:colspan"></td>
 </tr>
 <tr height="20" style="height:15.0pt">
  <td colspan="4" height="20" class="xl66" style="height:15.0pt"><span lang="IN">BAB
  VII</span></td>
  <td class="xl66"></td>
  <td colspan="2" style="mso-ignore:colspan"></td>
  <td></td>
  <td colspan="4" style="mso-ignore:colspan"></td>
 </tr>
 <tr height="20" style="height:15.0pt">
  <td colspan="4" height="20" class="xl66" style="height:15.0pt"><span lang="IN">PENYELESAIKAN
  SENGKETA</span></td>
  <td class="xl66"></td>
  <td colspan="2" style="mso-ignore:colspan"></td>
  <td></td>
  <td colspan="4" style="mso-ignore:colspan"></td>
 </tr>
 <tr height="20" style="height:15.0pt">
  <td colspan="4" height="20" class="xl66" style="height:15.0pt"><span lang="IN">PASAL
  19</span></td>
  <td class="xl66"></td>
  <td colspan="2" style="mso-ignore:colspan"></td>
  <td></td>
  <td colspan="4" style="mso-ignore:colspan"></td>
 </tr>
 <tr height="20" style="height:15.0pt">
  <td colspan="4" height="20" class="xl76" style="height:15.0pt"></td>
  <td class="xl66"></td>
  <td colspan="2" style="mso-ignore:colspan"></td>
  <td></td>
  <td colspan="4" style="mso-ignore:colspan"></td>
 </tr>
 <tr height="37" style="mso-height-source:userset;height:27.75pt">
  <td height="37" class="xl84" width="39" style="height:27.75pt;width:29pt">1</td>
  <td colspan="3" class="xl81" width="545" style="width:409pt">Apabila dikemudian
  hari terjadi sengketa atau perselisihan, PARA PIHAK sepakat untuk musyawarah
  mencapai mufakat.</td>
  <td class="xl73" width="63" style="width:47pt"></td>
  <td colspan="2" style="mso-ignore:colspan"></td>
  <td></td>
  <td colspan="4" style="mso-ignore:colspan"></td>
 </tr>
 <tr height="37" style="mso-height-source:userset;height:27.75pt">
  <td height="37" class="xl84" width="39" style="height:27.75pt;width:29pt"><span lang="IN" style="line-height:150%;mso-fareast-font-family:Arial">2</span></td>
  <td colspan="3" class="xl81" width="545" style="width:409pt">Apabila musyawarah
  untuk mufakat tidak tercapai, para pihak sepakat menyelesaikan di kantor
  Kepanitraan Pengadilan Negeri<span style="mso-spacerun:yes">&nbsp;</span></td>
  <td class="xl73" width="63" style="width:47pt"></td>
  <td colspan="2" style="mso-ignore:colspan"></td>
  <td></td>
  <td colspan="4" style="mso-ignore:colspan"></td>
 </tr>
 <tr height="20" style="height:15.0pt">
  <td colspan="4" height="20" class="xl74" style="height:15.0pt"></td>
  <td class="xl74"></td>
  <td colspan="2" style="mso-ignore:colspan"></td>
  <td></td>
  <td colspan="4" style="mso-ignore:colspan"></td>
 </tr>
 <tr height="20" style="height:15.0pt">
  <td colspan="4" height="20" class="xl66" style="height:15.0pt"><span lang="IN">BAB
  VII</span></td>
  <td class="xl66"></td>
  <td colspan="2" style="mso-ignore:colspan"></td>
  <td></td>
  <td colspan="4" style="mso-ignore:colspan"></td>
 </tr>
 <tr height="20" style="height:15.0pt">
  <td colspan="4" height="20" class="xl66" style="height:15.0pt"><span lang="IN">PENYELESAIKAN
  SENGKETA</span></td>
  <td class="xl66"></td>
  <td colspan="2" style="mso-ignore:colspan"></td>
  <td></td>
  <td colspan="4" style="mso-ignore:colspan"></td>
 </tr>
 <tr height="20" style="height:15.0pt">
  <td colspan="4" height="20" class="xl66" style="height:15.0pt"><span lang="IN" style="line-height:150%">PASAL 20</span></td>
  <td class="xl66"></td>
  <td colspan="2" style="mso-ignore:colspan"></td>
  <td></td>
  <td colspan="4" style="mso-ignore:colspan"></td>
 </tr>
 <tr height="90" style="mso-height-source:userset;height:67.5pt">
  <td colspan="4" height="90" class="xl73" width="584" style="height:67.5pt;width:438pt"><span lang="SV">PIHAK PERTAMA dan PIHAK KEDUA menyatakan dengan sungguh-sungguh bahwa
  Perjanjian Pendahuluan tentang Pengikatan Jual Beli ini dibuat dengan tanpa
  adanya paksaan dari pihak manapun, dan merupakan perjanjian terakhir yang
  menghapus perjanjian sebelumnya baik lisan maupun tertulis. Demikian
  perjanjian ini dibuat rangkap 2 dimana masing-masing bermaterai cukup dan
  mempunyai kekuatan hukum yang sama.</span></td>
  <td class="xl73" width="63" style="width:47pt"></td>
  <td colspan="2" style="mso-ignore:colspan"></td>
  <td></td>
  <td colspan="4" style="mso-ignore:colspan"></td>
 </tr>
 <tr height="20" style="height:15.0pt">
  <td height="20" class="xl75" style="height:15.0pt"></td>
  <td colspan="2" style="mso-ignore:colspan"></td>
  <td class="xl67"></td>
  <td class="xl67"></td>
  <td colspan="2" style="mso-ignore:colspan"></td>
  <td></td>
  <td colspan="4" style="mso-ignore:colspan"></td>
 </tr>
 
 <!--[endif]-->
</tbody></table>
<table class="table">
    <tr>
        <td width="50%"class="text-center">Pihak Pertama,</td>
        <td width="50%"class="text-center">Pihak Kedua,</td>
    </tr>
    <tr>
        <td height="60px"></td>
    </tr>
    <tr>
        <td width="50%"class="text-center">.........................</td>
        <td width="50%"class="text-center">.........................</td>
    </tr>
    <tr>
        <td width="50%"class="text-center"></td>
        <td width="50%" >Saksi saksi</td>
    </tr>
    <tr>
        <td width="50%"class="text-center">Saksi,</td>
        <td width="50%"class="text-center">Property Advisor,</td>
    </tr>
    <tr>
        <td height="60px"></td>
    </tr>
    <tr>
        <td width="50%"class="text-center">.........................</td>
        <td width="50%"class="text-center">.........................</td>
    </tr>
</table>



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
.font6
	{color:black;
	font-size:10.0pt;
	font-weight:700;
	font-style:normal;
	text-decoration:none;
	font-family:Arial, sans-serif;
	mso-font-charset:0;}
.font7
	{color:black;
	font-size:10.0pt;
	font-weight:400;
	font-style:normal;
	text-decoration:none;
	font-family:Arial, sans-serif;
	mso-font-charset:0;}
.font8
	{color:black;
	font-size:7.0pt;
	font-weight:400;
	font-style:normal;
	text-decoration:none;
	font-family:"Times New Roman", serif;
	mso-font-charset:0;}
.font9
	{color:black;
	font-size:10.0pt;
	font-weight:400;
	font-style:normal;
	text-decoration:none;
	font-family:Arial, sans-serif;
	mso-font-charset:0;}
.font10
	{color:black;
	font-size:7.0pt;
	font-weight:400;
	font-style:normal;
	text-decoration:none;
	font-family:"Times New Roman", serif;
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
	font-size:10.0pt;
	font-weight:700;
	text-decoration:underline;
	text-underline-style:single;
	font-family:Arial, sans-serif;
	mso-font-charset:0;
	text-align:center;
	vertical-align:middle;}
.xl66
	{mso-style-parent:style0;
	font-size:10.0pt;
	font-weight:700;
	font-family:Arial, sans-serif;
	mso-font-charset:0;
	text-align:center;
	vertical-align:middle;}
.xl67
	{mso-style-parent:style0;
	font-size:10.0pt;
	font-family:Arial, sans-serif;
	mso-font-charset:0;
	text-align:justify;
	vertical-align:middle;}
.xl68
	{mso-style-parent:style0;
	font-size:10.0pt;
	font-weight:700;
	font-family:Arial, sans-serif;
	mso-font-charset:0;
	text-align:justify;
	vertical-align:middle;}
.xl69
	{mso-style-parent:style0;
	color:black;
	font-size:10.0pt;
	font-family:Arial, sans-serif;
	mso-font-charset:0;
	text-align:justify;
	vertical-align:middle;}
.xl70
	{mso-style-parent:style0;
	font-size:10.0pt;
	font-family:Arial, sans-serif;
	mso-font-charset:0;
	text-align:center;
	vertical-align:middle;}
.xl71
	{mso-style-parent:style0;
	vertical-align:middle;}
.xl72
	{mso-style-parent:style0;
	font-size:10.0pt;
	font-family:Arial, sans-serif;
	mso-font-charset:0;
	vertical-align:middle;}
.xl73
	{mso-style-parent:style0;
	font-size:10.0pt;
	font-family:Arial, sans-serif;
	mso-font-charset:0;
	vertical-align:middle;
	white-space:normal;}
.xl74
	{mso-style-parent:style0;
	text-align:left;}
.xl75
	{mso-style-parent:style0;
	text-align:right;
	vertical-align:top;}
.xl76
	{mso-style-parent:style0;
	text-align:center;
	vertical-align:top;}
.xl77
	{mso-style-parent:style0;
	font-size:10.0pt;
	font-family:Arial, sans-serif;
	mso-font-charset:0;
	text-align:left;
	vertical-align:middle;
	white-space:normal;}
.xl78
	{mso-style-parent:style0;
	font-size:8.0pt;
	font-family:Arial, sans-serif;
	mso-font-charset:0;}
.xl79
	{mso-style-parent:style0;
	font-size:10.0pt;
	font-weight:700;
	font-family:Arial, sans-serif;
	mso-font-charset:0;
	text-align:left;
	vertical-align:middle;}
.xl80
	{mso-style-parent:style0;
	font-size:10.0pt;
	font-family:Arial, sans-serif;
	mso-font-charset:0;
	vertical-align:top;
	white-space:normal;}
.xl81
	{mso-style-parent:style0;
	font-size:10.0pt;
	font-family:Arial, sans-serif;
	mso-font-charset:0;
	text-align:left;
	vertical-align:top;
	white-space:normal;}
.xl82
	{mso-style-parent:style0;
	color:black;
	font-size:10.0pt;
	font-family:Arial, sans-serif;
	mso-font-charset:0;
	text-align:left;
	vertical-align:middle;}
.xl83
	{mso-style-parent:style0;
	color:black;
	font-size:10.0pt;
	font-family:Arial, sans-serif;
	mso-font-charset:0;
	text-align:left;
	vertical-align:top;
	white-space:normal;}
.xl84
	{mso-style-parent:style0;
	font-size:10.0pt;
	font-family:Arial, sans-serif;
	mso-font-charset:0;
	text-align:right;
	vertical-align:top;
	white-space:normal;}
.xl85
	{mso-style-parent:style0;
	text-align:left;}
.xl86
	{mso-style-parent:style0;
	font-size:8.0pt;}
</style>