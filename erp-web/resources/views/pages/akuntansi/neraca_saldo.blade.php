@extends('theme.default')

@section('breadcrumb')
            <div class="page-breadcrumb">
                <div class="row">
                    <div class="col-5 align-self-center">
                        <h4 class="page-title">Neraca Saldo</h4>
                        <div class="d-flex align-items-center">
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item active" aria-current="page">Akuntansi</li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
@endsection

@section('content')
@php
    function formatRupiah($num){
        return number_format($num, 0, '.', '.');
    }
    function formatDate($date){
        $date=date_create($date);
        return date_format($date, 'd-m-Y');
    }
    $total_modal=$total_amortisasi=0;
    foreach ($data['parent'] as $k){
        if($k->no_akun != 1 && $k->no_akun != 2 && $k->no_akun != 3){
            foreach($k->detail as $key => $value){
                $total_saldo=0;
                foreach ($value['data'] as $v){
                    $saldo = $v['saldo'];
                    $total_saldo+=(($saldo != null ? $saldo->jumlah_saldo : 0) + $v['detail'][0]->jumlah_kredit) - $v['detail'][0]->jumlah_debit;      
                }
                $total_modal+=$total_saldo;
            }
        }
    }
    foreach ($data['parent'] as $k){
        if($k->no_akun == 1){
            foreach($k->detail as $key => $value){
                $total_saldo=0;
                foreach ($value['data'] as $v){
                    if($v['detail'][0]->id_akun == 49){
                        $saldo = $v['saldo'];
                        $total_saldo+=(($saldo != null ? $saldo->jumlah_saldo : 0) + $v['detail'][0]->jumlah_debit) - $v['detail'][0]->jumlah_kredit;      
                    }
                }
                $total_amortisasi+=$total_saldo;
            }
        }
    }
@endphp
<style>
  .floatLeft { width: 100%;}
  .floatRight {width: 100%;}
  /* .floatLeft { width: 100%; float: left; }
  .floatRight {width: 100%; float: right; } */
    #table th, #table td{
        border:1px solid #7c8186;
        padding : 5px
    }
    .no-border{
        border:1px solid white !important;
        /* padding : 5px */
    }
  </style>
<div class="container-fluid">
    <!-- basic table -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-12">
                            <h4 class="box-title">Jurnal Umum Bulan November 2019</h4>
                            <form method="POST" action="{{ URL::to('akuntansi/neraca_saldo') }}" class="form-inline float-right">
                              @csrf
                            <div class="form-inline">
                                <!-- <div class="form-group">
                                <select name="" id="" class="form-control select2" style="width:120px"></select>
                                </div>&nbsp; -->
                                <label>Pilih Bulan : </label>&nbsp;
                                <select class="form-control select2" name="bulan" id="bulan" required style="width:120px">
                                    <option value="">--Pilih Bulan--</option>
                                    <option value="01">Januari</option>
                                    <option value="02">Februari</option>
                                    <option value="03">Maret</option>
                                    <option value="04">April</option>
                                    <option value="05">Mei</option>
                                    <option value="06">Juni</option>
                                    <option value="07">Juli</option>
                                    <option value="08">Agustus</option>
                                    <option value="09">September</option>
                                    <option value="10">Oktober</option>
                                    <option value="11">November</option>
                                    <option value="12">Desember</option>
                                </select>
                                &nbsp;
                                <select class="form-control select2" name="tahun" id="tahun" required  style="width:120px">
                                    <option value="">--Pilih Tahun--</option>
                                    @for ($i = date('Y') - 5; $i <= date('Y'); $i++)
                                    <option value="{{$i}}">{{$i}}</option>
                                    @endfor
                                </select>&nbsp;
                                <button class="btn btn-primary"  onclick="cekAbsensiDate()"><i class="fa fa-search"></i></button>
                                &nbsp;&nbsp;
                                <a class="btn btn-success" target="_blank"
                                href="{{ URL::to('akuntansi/export_neraca?date='.$data['date']) }}" >
                                <i class="mdi mdi-file-excel"></i> Export Neraca</a>
                            </div>
                            </form>
                        </div>
                    </div>
                     <br>
                     <div class="table-responsive">
                        <table style="border-collapse:collapse" class="floatLeft">
                            <thead id="table">
                                <tr>
                                    <th rowspan="2" style="vertical-align : middle;text-align:center;">No Akun</th>
                                    <th rowspan="2" style="vertical-align : middle;text-align:center;">Nama Akun</th>
                                    <th rowspan="2" style="vertical-align : middle;text-align:center;">Saldo Awal Bulan Debit</th>
                                    <th rowspan="2" style="vertical-align : middle;text-align:center;">Saldo Awal Bulan Kredit</th>
                                    <th rowspan="2" style="vertical-align : middle;text-align:center;">Perubahan Saldo Debit</th>
                                    <th rowspan="2" style="vertical-align : middle;text-align:center;">Perubahan Saldo Kredit</th>
                                    <!-- <th rowspan="2" style="vertical-align : middle;text-align:center;">Perubahan Saldo</th> -->
                                    <th rowspan="2" colspan="2" class="text-center">Saldo Akhir Debit</th>
                                    <th rowspan="2" colspan="2" class="text-center">Saldo Akhir Kredit</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php 
                                $jumlah_debit=$jumlah_kredit=$total_debit=$total_kredit=$initiate=0;
                                @endphp
                                @foreach ($data['parent'] as $k)
                                    <?php
                                    $temp=array();
                                    if($k->no_akun == 1){
                                    ?>
                                    @foreach($k->detail as $key => $value)
                                    <?php
                                    $jumlah_parent=0;
                                    ?>
                                    <tr id="table" >
                                        <th></th>
                                        <th><b>{{$value['nama']}}</b></th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <!-- <th></th> -->
                                        <th colspan="2" class="text-center"></th>
                                        <th colspan="2" class="text-center"></th>
                                    </tr>
                                        @foreach ($value['data'] as $v)
                                        @if($v['detail'][0]->id_akun != 49)
                                        @php $saldo = $v['saldo'] @endphp
                                        <tr id="table" >
                                            <td>{{$v['detail'][0]->no_akun}}</td>
                                            <td>{{$v['detail'][0]->nama_akun}}</td>
                                            @php $saldo_awal_bulan=($saldo != null ? $saldo->jumlah_saldo : 0) @endphp
                                            @if ($v['detail'][0]->sifat_debit == 1)
                                                <td class="text-right">{{$saldo_awal_bulan > 0 ? 'Rp. '.formatRupiah($saldo_awal_bulan) : 'Rp. 0'}}</td>
                                                <td class="text-right">{{$saldo_awal_bulan < 0 ? 'Rp. '.formatRupiah(abs($saldo_awal_bulan)) : 'Rp. 0'}}</td>
                                            @else
                                                <td class="text-right">{{$saldo_awal_bulan < 0 ? 'Rp. '.formatRupiah(abs($saldo_awal_bulan)) : 'Rp. 0'}}</td>
                                                <td class="text-right">{{$saldo_awal_bulan > 0 ? 'Rp. '.formatRupiah($saldo_awal_bulan) : 'Rp. 0'}}</td>
                                            @endif
                                            <td class="text-right">Rp. {{formatRupiah($v['detail'][0]->jumlah_debit)}}</td>
                                            <td class="text-right">Rp. {{formatRupiah($v['detail'][0]->jumlah_kredit)}}</td>
                                            @php
                                            if ($v['detail'][0]->sifat_debit == 1) {
                                                $a= (($saldo != null ? $saldo->jumlah_saldo : 0) + $v['detail'][0]->jumlah_debit) - $v['detail'][0]->jumlah_kredit;
                                                $jumlah_parent+=$a;
                                                if($a < 0){
                                                    $total_kredit+=abs($a);
                                                }else{
                                                    $total_debit+=$a;
                                                }
                                            @endphp
                                            <!-- <td class="text-right"><a href="" onclick="doShowGL('{{$v['detail'][0]->id_akun}}');" data-toggle="modal" data-target="#modalShowGL">Rp. {{formatRupiah(($v['detail'][0]->jumlah_debit - $v['detail'][0]->jumlah_kredit))}}</a></td> -->
                                                <td class="text-right" colspan="2">{{$a > 0 ? 'Rp. '.formatRupiah($a) : 'Rp. 0'}}</td>
                                                <td class="text-right" colspan="2">{{$a < 0 ? 'Rp. '.formatRupiah(abs($a)) : 'Rp. 0'}}</td>
                                            <!-- <td class="text-right" colspan="2">Rp. {{formatRupiah($a)}}</td> -->
                                            @php
                                            }else{
                                                $b=(($saldo != null ? $saldo->jumlah_saldo : 0) + $v['detail'][0]->jumlah_kredit) - $v['detail'][0]->jumlah_debit;
                                                $jumlah_parent+=($b + ($v['detail'][0]->id_akun == 168 ? $total_modal : 0));
                                                if($b < 0){
                                                    $total_debit+=abs($b);
                                                }else{
                                                    $total_kredit+=$b;
                                                }
                                            @endphp
                                            <!-- <td class="text-right"><a href="" onclick="doShowGL('{{$v['detail'][0]->id_akun}}');" data-toggle="modal" data-target="#modalShowGL">Rp. {{formatRupiah(($v['detail'][0]->jumlah_kredit - $v['detail'][0]->jumlah_debit) + ($v['detail'][0]->id_akun == 168 ? $total_modal : 0))}}</a></td> -->
                                                <td class="text-right" colspan="2">{{$b < 0 ? 'Rp. '.formatRupiah(abs($b)) : 'Rp. 0'}}</td>
                                                <td class="text-right" colspan="2">{{$b > 0 ? 'Rp. '.formatRupiah($b) : 'Rp. 0'}}</td>
                                            <!-- <td class="text-right" colspan="2">Rp. {{formatRupiah($b)}}</td> -->
                                            @php
                                            }
                                            @endphp
                                        </tr>   
                                        @endif
                                        @endforeach
                                        <?php
                                        array_push($temp, $jumlah_parent);
                                        ?>
                                        <tr id="table" >
                                            <th colspan="8" class="text-center"><b>Total {{$value['nama']}}</b></th>
                                            <td class="text-right" colspan="2"><b>Rp. {{formatRupiah(round($jumlah_parent, 1))}}</b></td>
                                        </tr>
                                        @if($value['nama'] == 'Aktiva Tetap')
                                        <tr id="table" >
                                            <th colspan="8" class="text-center"><b>Total Akumulasi Penyusutan</b></th>
                                            <td class="text-right" colspan="2"><b>Rp. {{formatRupiah(round($total_amortisasi, 1))}}</b></td>
                                        </tr>
                                        @endif
                                        <?php $initiate++; ?>
                                    @endforeach
                                    <tr id="table" >
                                        <th colspan="8" class="text-center" style="color:white"><b>-</b></th>
                                        <th colspan="2" class="text-center"><b></b></th>
                                    </tr>
                                    <?php
                                    $sub_total_parent=0;
                                    ?>
                                    @foreach($k->detail as $key => $value)
                                    <?php
                                    $sub_total_parent+=round($temp[$key], 2);
                                    $total=round($temp[$key], 2);
                                    if($value['nama'] == 'Aktiva Tetap'){
                                        $sub_total_parent+=$total_amortisasi;
                                        $total+=$total_amortisasi;
                                    }
                                    ?>
                                    <tr id="table" >
                                        <th colspan="8" class="text-center"><b>Total {{$value['nama']}}</b></th>
                                        <th colspan="2" class="text-right"><b>{{formatRupiah($total)}}</b></th>
                                    </tr>
                                    @endforeach
                                    <tr id="table" >
                                        <th colspan="8" class="text-center"><b>Total {{$k->nama_akun}}</b></th>
                                        <th colspan="2" class="text-right"><b>{{formatRupiah(round($sub_total_parent))}}</b></th>
                                    </tr>
                                    <?php
                                    }
                                    ?>
                                @endforeach
                                <tr id="table">
                                    <th colspan="8" class="text-center">Total Saldo</th>
                                    <th class="text-right" colspan="2">Rp. {{formatRupiah(($total_debit - $total_kredit) + $total_amortisasi)}}</th>
                                </tr>
                            </tbody>
                            <thead id="table">
                                <tr>
                                    <th rowspan="2" colspan="6" style="border-left:1px solid white;border-right:1px solid white;color:white">No Akun</th>
                                </tr>
                            </thead>
                            <thead id="table">
                                <tr>
                                    <th rowspan="2" style="vertical-align : middle;text-align:center;">No Akun</th>
                                    <th rowspan="2" style="vertical-align : middle;text-align:center;">Nama Akun</th>
                                    <th rowspan="2" style="vertical-align : middle;text-align:center;">Saldo Awal Bulan Debit</th>
                                    <th rowspan="2" style="vertical-align : middle;text-align:center;">Saldo Awal Bulan Kredit</th>
                                    <th rowspan="2" style="vertical-align : middle;text-align:center;">Perubahan Saldo Debit</th>
                                    <th rowspan="2" style="vertical-align : middle;text-align:center;">Perubahan Saldo Kredit</th>
                                    <!-- <th rowspan="2" style="vertical-align : middle;text-align:center;">Perubahan Saldo</th> -->
                                    <th rowspan="2" colspan="2" class="text-center">Saldo Akhir Debit</th>
                                    <th rowspan="2" colspan="2" class="text-center">Saldo Akhir Kredit</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php 
                                $jumlah_debit=$jumlah_kredit=$total_debit=$total_kredit=$initiate=0;
                                @endphp
                                @foreach ($data['parent'] as $k)
                                    <?php
                                    $temp=array();
                                    if($k->no_akun == 2 || $k->no_akun == 3){
                                    ?>
                                    @foreach($k->detail as $key => $value)
                                    <?php
                                    $jumlah_parent=0;
                                    ?>
                                    <tr id="table" >
                                    <th></th>
                                        <th><b>{{$value['nama']}}</b></th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <!-- <th></th> -->
                                        <th colspan="2" class="text-center"></th>
                                        <th colspan="2" class="text-center"></th>
                                    </tr>
                                        @foreach ($value['data'] as $v)
                                        @php $saldo = $v['saldo'] @endphp
                                        <tr id="table" >
                                            <td>{{$v['detail'][0]->no_akun}}</td>
                                            <td>{{$v['detail'][0]->nama_akun}}</td>
                                            @php $saldo_awal_bulan=($saldo != null ? $saldo->jumlah_saldo : 0) @endphp
                                            @if ($v['detail'][0]->sifat_debit == 1)
                                                <td class="text-right">{{$saldo_awal_bulan > 0 ? 'Rp. '.formatRupiah($saldo_awal_bulan) : 'Rp. 0'}}</td>
                                                <td class="text-right">{{$saldo_awal_bulan < 0 ? 'Rp. '.formatRupiah(abs($saldo_awal_bulan)) : 'Rp. 0'}}</td>
                                            @else
                                                <td class="text-right">{{$saldo_awal_bulan < 0 ? 'Rp. '.formatRupiah(abs($saldo_awal_bulan)) : 'Rp. 0'}}</td>
                                                <td class="text-right">{{$saldo_awal_bulan > 0 ? 'Rp. '.formatRupiah($saldo_awal_bulan) : 'Rp. 0'}}</td>
                                            @endif
                                            <td class="text-right">Rp. {{formatRupiah($v['detail'][0]->jumlah_debit)}}</td>
                                            <td class="text-right">Rp. {{formatRupiah($v['detail'][0]->jumlah_kredit)}}</td>
                                            @php
                                            if ($v['detail'][0]->sifat_debit == 1) {
                                                $a= (($saldo != null ? $saldo->jumlah_saldo : 0) + $v['detail'][0]->jumlah_debit) - $v['detail'][0]->jumlah_kredit;
                                                $jumlah_parent+=$a;
                                                if($a < 0){
                                                    $total_kredit+=abs($a);
                                                }else{
                                                    $total_debit+=$a;
                                                }
                                            @endphp    
                                            <!-- <td class="text-right"><a href="" onclick="doShowGL('{{$v['detail'][0]->id_akun}}');" data-toggle="modal" data-target="#modalShowGL">Rp. {{formatRupiah($v['detail'][0]->jumlah_debit - $v['detail'][0]->jumlah_kredit)}}</a></td>
                                            <td class="text-right" colspan="2">Rp. {{formatRupiah($a)}}</td> -->
                                                <td class="text-right" colspan="2">{{$a > 0 ? 'Rp. '.formatRupiah($a) : 'Rp. 0'}}</td>
                                                <td class="text-right" colspan="2">{{$a < 0 ? 'Rp. '.formatRupiah(abs($a)) : 'Rp. 0'}}</td>
                                            @php

                                            }else{
                                                $b=((($saldo != null ? $saldo->jumlah_saldo : 0) + $v['detail'][0]->jumlah_kredit) - $v['detail'][0]->jumlah_debit) + ($v['detail'][0]->id_akun == 168 ? $total_modal : 0);
                                                $jumlah_parent+=$b;
                                                if($b < 0){
                                                    $total_debit+=abs($b);
                                                }else{
                                                    $total_kredit+=$b;
                                                }
                                            @endphp
                                            <!-- <td class="text-right"><a href="" onclick="doShowGL('{{$v['detail'][0]->id_akun}}');" data-toggle="modal" data-target="#modalShowGL">Rp. {{formatRupiah(($v['detail'][0]->jumlah_kredit - $v['detail'][0]->jumlah_debit) + ($v['detail'][0]->id_akun == 168 ? $total_modal : 0))}}</a></td>
                                            <td class="text-right"  colspan="2">Rp. {{formatRupiah($b)}}</td> -->
                                                <td class="text-right" colspan="2">{{$b < 0 ? 'Rp. '.formatRupiah(abs($b)) : 'Rp. 0'}}</td>
                                                <td class="text-right" colspan="2">{{$b > 0 ? 'Rp. '.formatRupiah($b) : 'Rp. 0'}}</td>
                                            @php
                                            }
                                            @endphp
                                        </tr>   
                                        @endforeach
                                        <?php
                                        array_push($temp, $jumlah_parent);
                                        ?>
                                        <tr id="table" >
                                            <th colspan="8" class="text-center"><b>Total {{$value['nama']}}</b></th>
                                            <td class="text-right" colspan="2"><b>Rp. {{formatRupiah(round($jumlah_parent, 1))}}</b></td>
                                            <!-- <th colspan="2" class="text-right"><b>{{formatRupiah(round($jumlah_parent, 1))}}</b></th> -->
                                        </tr>
                                        <?php $initiate++; ?>
                                    @endforeach
                                    <tr id="table" >
                                        <th colspan="8" class="text-center" style="color:white"><b>asdfasdf</b></th>
                                        <th colspan="2" class="text-center"><b></b></th>
                                    </tr>
                                    <?php
                                    $sub_total_parent=0;
                                    ?>
                                    @foreach($k->detail as $key => $value)
                                    <?php
                                    $sub_total_parent+=round($temp[$key], 2);
                                    ?>
                                    <tr id="table" >
                                        <th colspan="8" class="text-center"><b>Total {{$value['nama']}}</b></th>
                                        <th colspan="2" class="text-right"><b>{{formatRupiah(round($temp[$key], 2))}}</b></th>
                                    </tr>
                                    @endforeach
                                    <tr id="table" >
                                        <th colspan="8" class="text-center"><b>Total {{$k->nama_akun}}</b></th>
                                        <th colspan="2" class="text-right"><b>{{formatRupiah(round($sub_total_parent))}}</b></th>
                                    </tr>
                                    <?php
                                    }
                                    ?>
                                @endforeach
                                <tr id="table">
                                    <th colspan="8" class="text-center">Total Saldo</th>
                                    <th class="text-right"  colspan="2">Rp. {{formatRupiah($total_kredit - $total_debit)}}</th>
                                </tr>
                            </tbody>
                        </table>

                        <!-- modal -->
                        @php $show=false; @endphp
                        @if($show == true)
                        <table style="border-collapse:collapse"  class="floatLeft">
                            <thead id="table">
                                <tr>
                                    <th rowspan="2" style="vertical-align : middle;text-align:center;">No Akun</th>
                                    <th rowspan="2" style="vertical-align : middle;text-align:center;">Nama Akun</th>
                                    <th rowspan="2" colspan="2" class="text-center">Saldo</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php 
                                $jumlah_debit=$jumlah_kredit=$total_debit=$total_kredit=$initiate=0;
                                @endphp
                                @foreach ($data['parent'] as $k)
                                    <?php
                                    $temp=array();
                                    if($k->no_akun == 2 || $k->no_akun == 3){
                                    ?>
                                    @foreach($k->detail as $key => $value)
                                    <?php
                                    $jumlah_parent=0;
                                    ?>
                                    <tr id="table" >
                                        <th></th>
                                        <th><b>{{$value['nama']}}</b></th>
                                        <th colspan="2" class="text-center"></th>
                                    </tr>
                                        @foreach ($value['data'] as $v)
                                        @php $saldo = $v['saldo'] @endphp
                                        <tr id="table" >
                                            <td>{{$v['detail'][0]->no_akun}}</td>
                                            <td>{{$v['detail'][0]->nama_akun}}</td>
                                            @php
                                            if ($v['detail'][0]->sifat_debit == 1) {
                                                $a= (($saldo != null ? $saldo->jumlah_saldo : 0) + $v['detail'][0]->jumlah_debit) - $v['detail'][0]->jumlah_kredit;
                                                $jumlah_parent+=$a;
                                                if($a < 0){
                                                    $total_kredit+=abs($a);
                                                }else{
                                                    $total_debit+=$a;
                                                }
                                            @endphp    
                                            <td class="text-right" colspan="2">Rp. {{formatRupiah($a)}}</td>
                                            @php

                                            }else{
                                                $b=((($saldo != null ? $saldo->jumlah_saldo : 0) + $v['detail'][0]->jumlah_kredit) - $v['detail'][0]->jumlah_debit) + ($v['detail'][0]->id_akun == 168 ? $total_modal : 0);
                                                $jumlah_parent+=$b;
                                                if($b < 0){
                                                    $total_debit+=abs($b);
                                                }else{
                                                    $total_kredit+=$b;
                                                }
                                            @endphp
                                            <td class="text-right"  colspan="2">Rp. {{formatRupiah($b)}}</td>
                                            @php
                                            }
                                            @endphp
                                        </tr>   
                                        @endforeach
                                        <?php
                                        array_push($temp, $jumlah_parent);
                                        ?>
                                        <tr id="table" >
                                            <th colspan="2" class="text-center"><b>Total {{$value['nama']}}</b></th>
                                            <td class="text-right" colspan="2"><b>Rp. {{formatRupiah(round($jumlah_parent, 1))}}</b></td>
                                            <!-- <th colspan="2" class="text-right"><b>{{formatRupiah(round($jumlah_parent, 1))}}</b></th> -->
                                        </tr>
                                        <?php $initiate++; ?>
                                    @endforeach
                                    <tr id="table" >
                                        <th colspan="2" class="text-center" style="color:white"><b>-</b></th>
                                        <th colspan="2" class="text-center"><b></b></th>
                                    </tr>
                                    <?php
                                    $sub_total_parent=0;
                                    ?>
                                    @foreach($k->detail as $key => $value)
                                    <?php
                                    $sub_total_parent+=round($temp[$key], 2);
                                    ?>
                                    <tr id="table" >
                                        <th colspan="2" class="text-center"><b>Total {{$value['nama']}}</b></th>
                                        <th colspan="2" class="text-right"><b>{{formatRupiah(round($temp[$key], 2))}}</b></th>
                                    </tr>
                                    @endforeach
                                    <tr id="table" >
                                        <th colspan="2" class="text-center"><b>Total {{$k->nama_akun}}</b></th>
                                        <th colspan="2" class="text-right"><b>{{formatRupiah(round($sub_total_parent))}}</b></th>
                                    </tr>
                                    <?php
                                    }
                                    ?>
                                @endforeach
                                <tr id="table">
                                    <th colspan="2" class="text-center">Total Saldo</th>
                                    <th class="text-right"  colspan="2">Rp. {{formatRupiah($total_kredit - $total_debit)}}</th>
                                </tr>
                            </tbody>
                        </table>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
                
</div>
<div class="modal fade" id="modalShowGL" tabindex="-1" role="dialog" aria-labelledby="modalShowGLLabel1">
    <div class="modal-dialog  modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="modalAddMaterialLabel1">Detail General Ledger</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <div id="detail_gl"></div>
            </div>
            <div class="modal-footer">
                <!-- <button type="button" onclick="saveMaterial(this.form);" class="btn btn-primary btn-sm">Save</button> -->
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="modalShowDetail" tabindex="-1" role="dialog" aria-labelledby="modalShowDetailLabel1">
    <div class="modal-dialog  modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="modalAddMaterialLabel1">Detail Jurnal</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <table style="width:100% " class="no-border">
                    <thead>
                        <tr>
                            <td>Deskripsi</td>
                            <td>:</td>
                            <td id="deskripsi"></td>
                        </tr>
                        <tr>
                            <td>Tanggal</td>
                            <td>:</td>
                            <td id="date">/td>
                        </tr>
                    </thead>
                </table>
                <!-- <br>
                <button class="btn btn-primary" id="btn-po" data-id="1" onclick="doShowPO()" data-toggle="modal" data-target="#modalShowPurchase">PO</button>
                <button class="btn btn-primary" id="btn-po-asset" data-id="1" onclick="doShowPOAsset()" data-toggle="modal" data-target="#modalShowPurchaseAsset">PO Asset</button>
                <button class="btn btn-primary" id="btn-inv" data-id="1" onclick="doShowInv()" data-toggle="modal" data-target="#modalShowInv">Penerimaan</button>
                <button class="btn btn-primary" id="btn-req-dev" data-id="1" onclick="doShowReqDev()" data-toggle="modal" data-target="#modalShowReqDev">Jurnal Permintaan</button> -->
                <br><br>
                <div class="table-responsive">
                    <table id="dt_detail" class="table table-bordered">
                        <thead>
                            <tr>
                                <th class="text-center">No</th>
                                <th class="text-center">Nama Akun</th>
                                <th class="text-center">Tipe</th>
                                <th class="text-center">Total</th>
                            </tr>
                        </thead>                                      
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <!-- <button type="button" onclick="saveMaterial(this.form);" class="btn btn-primary btn-sm">Save</button> -->
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="modalShowReqDev" tabindex="-1" role="dialog" aria-labelledby="modalShowDetailLabel1">
    <div class="modal-dialog  modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="modalAddMaterialLabel1">Journal Permintaan Detail</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table id="req_dev_detail" class="table table-bordered">
                        <thead>
                            <tr>
                                <th class="text-center">Tanggal</th>
                                <th class="text-center">Deskripsi</th>
                                <th class="text-center">Akun</th>
                                <th class="text-center">No Akun</th>
                                <th class="text-center">Tipe</th>
                                <th class="text-center">Total</th>
                            </tr>
                        </thead>     
                        <tbody></tbody>                                 
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <!-- <button type="button" onclick="saveMaterial(this.form);" class="btn btn-primary btn-sm">Save</button> -->
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalShowPurchase" tabindex="-1" role="dialog" aria-labelledby="modalShowDetailLabel1">
    <div class="modal-dialog  modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="modalAddMaterialLabel1">Purchase Order Detail</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table id="po_detail" class="table table-bordered">
                        <thead>
                            <tr>
                                <th class="text-center">No Material</th>
                                <th class="text-center">Nama Material</th>
                                <th class="text-center">Volume</th>
                                <th class="text-center">Satuan</th>
                                <th class="text-center">Harga</th>
                            </tr>
                        </thead>                                      
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <!-- <button type="button" onclick="saveMaterial(this.form);" class="btn btn-primary btn-sm">Save</button> -->
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="modalShowPurchaseAsset" tabindex="-1" role="dialog" aria-labelledby="modalShowDetailLabel1">
    <div class="modal-dialog  modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="modalAddMaterialLabel1">Purchase Order Detail</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table id="po_asset_detail" class="table table-bordered">
                        <thead>
                            <tr>
                                <th class="text-center">No Material</th>
                                <th class="text-center">Nama Material</th>
                                <th class="text-center">Volume</th>
                                <th class="text-center">Satuan</th>
                                <th class="text-center">Harga</th>
                            </tr>
                        </thead>                                      
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <!-- <button type="button" onclick="saveMaterial(this.form);" class="btn btn-primary btn-sm">Save</button> -->
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="modalShowInv" tabindex="-1" role="dialog" aria-labelledby="modalShowDetailLabel1">
    <div class="modal-dialog  modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="modalAddMaterialLabel1">Penerimaan Detail</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table id="inv_detail" class="table table-bordered">
                        <thead>
                            <tr>
                                <th class="text-center">No Material</th>
                                <th class="text-center">Nama Material</th>
                                <th class="text-center">Volume</th>
                                <th class="text-center">Satuan</th>
                                <th class="text-center">Harga</th>
                            </tr>
                        </thead>                                      
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <!-- <button type="button" onclick="saveMaterial(this.form);" class="btn btn-primary btn-sm">Save</button> -->
            </div>
        </div>
    </div>
</div>
<script src="{!! asset('theme/assets/libs/jquery/dist/jquery.min.js') !!}"></script>
<script src="{!! asset('theme/assets/extra-libs/DataTables/datatables.min.js') !!}"></script>
<script type="text/javascript">
    var bulan=@php print_r($bulan) @endphp;
    $('.box-title').html('Neraca Saldo Tanggal 1 - {{$jumlah_hari}} Per '+formatBulan(bulan));
    function formatBulan(val){
        var bulan = ['Januari', 'Februari', 'Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
        var getMonth=val[1];
        return bulan[getMonth-1]+' '+val[0];
    }
    function doShowGL(id){
        var date='{{$data['date']}}';
        $.ajax({
                type: "GET",
                url: "{{ URL::to('akuntansi/show_gl_detail') }}" + "/" + id+ "/" + date, //json get site
                dataType : 'json',
                success: function(response){
                    $('#detail_gl').html(response['html_content']);
                }
        });
    }
    dt_detail=$('#dt_detail').DataTable();
    po_detail=$('#po_detail').DataTable();
    inv_detail=$('#inv_detail').DataTable();
    po_asset_detail=$('#po_asset_detail').DataTable();
    req_dev_detail=$('#req_dev_detail').DataTable();
    function doShowDetail(id){
        $('#modalShowGL').modal('toggle');
        dt_detail.clear().draw(false);
        $.ajax({
                type: "GET",
                url: "{{ URL::to('akuntansi/detail-trx-akun') }}" + "/" + id, //json get site
                dataType : 'json',
                success: function(response){
                    arrData = response['data'];
                    $('#deskripsi').html(arrData[0]['deskripsi'])
                    $('#date').html(formatDateID(new Date((arrData[0]['tanggal']).substring(0,10))))
                                
                    if (arrData[0]['purchase_id'] == null || arrData[0]['purchase_id'] == 0) {
                        $('#btn-po').hide();
                    }else{
                        $('#btn-po').show();
                        $('#btn-po').data('id', arrData[0]['purchase_id'])
                    }
                    if (arrData[0]['inv_trx_id'] == null || arrData[0]['inv_trx_id'] == 0) {
                        $('#btn-inv').hide();
                    }else{
                        $('#btn-inv').show();
                        $('#btn-inv').data('id', arrData[0]['inv_trx_id'])
                    }
                    if (arrData[0]['purchase_asset_id'] == null || arrData[0]['purchase_asset_id'] == 0) {
                        $('#btn-po-asset').hide();
                    }else{
                        $('#btn-po-asset').show();
                        $('#btn-po-asset').data('id', arrData[0]['purchase_asset_id'])
                    }
                    if (arrData[0]['project_req_development_id'] == null || arrData[0]['project_req_development_id'] == 0) {
                        $('#btn-req-dev').hide();
                    }else{
                        $('#btn-req-dev').show();
                        $('#btn-req-dev').data('id', arrData[0]['project_req_development_id'])
                    }
                    for(i = 0; i < arrData.length; i++){
                        // a = i+1;
                        dt_detail.row.add([
                            '<div class="text-left">'+arrData[i]['no_akun']+'</div>',
                            '<div class="'+(arrData[i]['tipe'] == 'DEBIT' ? 'text-left' : 'text-center')+'">'+arrData[i]['nama_akun']+'</div>',
                            '<div class="text-left">'+arrData[i]['tipe']+'</div>',
                            '<div class="text-right">'+formatCurrency(parseFloat(arrData[i]['jumlah']).toFixed(2))+'</div>',
                        ]).draw(false);
                    }
                }
        });
    }
    function doShowPO(){
        $('#modalShowDetail').modal('toggle');
        po_detail.clear().draw(false);
        var id=$('#btn-po').data('id');
        $.ajax({
                type: "GET",
                url: "{{ URL::to('po_konstruksi/detail') }}" + "/" + id, //json get site
                dataType : 'json',
                success: function(response){
                    arrData = response['data'];
                    for(i = 0; i < arrData.length; i++){
                        po_detail.row.add([
                            '<div class="text-left">'+arrData[i]['m_items']['no']+'</div>',
                            '<div class="text-left">'+arrData[i]['m_items']['name']+'</div>',
                            '<div class="text-right">'+arrData[i]['amount']+'</div>',
                            '<div class="text-center">'+arrData[i]['m_units']['name']+'</div>',
                            '<div class="text-right">'+formatCurrency(parseFloat(arrData[i]['base_price']).toFixed(2))+'</div>'
                        ]).draw(false);
                    }
                }
        });
    }
    function doShowPOAsset(){
        $('#modalShowDetail').modal('toggle');
        po_asset_detail.clear().draw(false);
        var id=$('#btn-po-asset').data('id');
        $.ajax({
                type: "GET",
                url: "{{ URL::to('po_konstruksi/detail_atk') }}" + "/" + id, //json get site
                dataType : 'json',
                success: function(response){
                    arrData = response['data'];
                    for(i = 0; i < arrData.length; i++){
                        po_asset_detail.row.add([
                            '<div class="text-left">'+arrData[i]['m_items']['no']+'</div>',
                            '<div class="text-left">'+arrData[i]['m_items']['name']+'</div>',
                            '<div class="text-right">'+arrData[i]['amount']+'</div>',
                            '<div class="text-center">'+arrData[i]['m_units']['name']+'</div>',
                            '<div class="text-right">'+formatCurrency(parseFloat(arrData[i]['base_price']).toFixed(2))+'</div>'
                        ]).draw(false);
                    }
                }
        });
    }
    function doShowInv(){
        $('#modalShowDetail').modal('toggle');
        inv_detail.clear().draw(false);
        var id=$('#btn-inv').data('id');
        $.ajax({
                type: "GET",
                url: "{{ URL::to('akuntansi/detail-inv') }}" + "/" + id, //json get site
                dataType : 'json',
                success: function(response){
                    arrData = response['data'];
                    for(i = 0; i < arrData.length; i++){
                        inv_detail.row.add([
                            '<div class="text-left">'+arrData[i]['m_items']['no']+'</div>',
                            '<div class="text-left">'+arrData[i]['m_items']['name']+'</div>',
                            '<div class="text-right">'+arrData[i]['amount']+'</div>',
                            '<div class="text-center">'+arrData[i]['m_units']['name']+'</div>',
                            '<div class="text-right">'+formatCurrency(parseFloat(arrData[i]['base_price']).toFixed(2))+'</div>'
                        ]).draw(false);
                    }
                }
        });
    }
    function doShowReqDev(){
        $('#modalShowDetail').modal('toggle');
        po_detail.clear().draw(false);
        var id=$('#btn-req-dev').data('id');
        $('#req_dev_detail > tbody').empty();
        $.ajax({
                type: "GET",
                url: "{{ URL::to('akuntansi/detail-req-dev') }}" + "/" + id, //json get site
                dataType : 'json',
                success: function(response){
                    arrData = response['data'];
                    for(var i = 0; i < arrData.length; i++){
                        var a=0;
                        for(var j = 0; j < arrData[i]['detail'].length; j++){
                            var td='<tr>'+
                                    '<td class="text-center">'+(j == 0 ? formatDate2(arrData[i]['tanggal']) : '')+'</td>'+
                                    '<td class="text-center">'+(j == 0 ? arrData[i]['deskripsi'] : '')+'</td>'+
                                    '<td class="text-center">'+arrData[i]['detail'][j]['nama_akun']+'</td>'+
                                    '<td class="text-center">'+arrData[i]['detail'][j]['no_akun']+'</td>'+
                                    '<td class="text-center">'+arrData[i]['detail'][j]['tipe']+'</td>'+
                                    '<td class="text-center">'+formatCurrency(parseFloat(arrData[i]['detail'][j]['jumlah']).toFixed(2))+'</td>'+
                                '</tr>';
                            console.log(td)
                            $('#req_dev_detail').find('tbody:last').append(td);
                        }
                    }
                }
        });
    }
</script>
@endsection

