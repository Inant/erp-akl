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
                            <h4 class="box-title">Neraca Saldo per Tanggal {{formatDate($data['date1'])}} Sampai {{formatDate($data['date2'])}}</h4>
                            <form method="POST" action="{{ URL::to('akuntansi/neraca_saldo') }}" class="form-inline float-right">
                              @csrf
                            <div class="form-inline">
                                <!-- <div class="form-group">
                                <select name="" id="" class="form-control select2" style="width:120px"></select>
                                </div>&nbsp; -->
                                <label>Cari Rentang Tanggal :</label>&nbsp;
                                <input type="date" name="date" class="form-control" required value="{{$data['date1']}}">&nbsp;
                                <input type="date" name="date2" class="form-control" required  value="{{$data['date2']}}">&nbsp;
                                <button class="btn btn-primary"  onclick="cekAbsensiDate()"><i class="fa fa-search"></i></button>
                                &nbsp;&nbsp;
                                <a class="btn btn-success" target="_blank"
                                href="{{ URL::to('akuntansi/export_neraca_saldo?date='.$data['date1'].'&date2='.$data['date2']) }}" >
                                <i class="mdi mdi-file-excel"></i> Export Neraca</a>
                            </div>
                            </form>
                        </div>
                    </div>
                     <br>
                     <div class="table-responsive">
                        <table style="border-collapse:collapse" class="floatLeft table" id="zero-config">
                            <thead id="table">
                                <tr>
                                    <th rowspan="2" style="vertical-align : middle;text-align:center;" width="150px">No Akun</th>
                                    <th rowspan="2" style="vertical-align : middle;text-align:center;" width="250px">Nama Akun</th>
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
                                $except=[152, 153, 154, 43, 44, 45, 46, 47, 48];
                                $saldo_awal_debit=$saldo_awal_kredit=$perubahan_debit=$perubahan_kredit=$perubahan_before=$saldo_awal_debit1=$saldo_awal_kredit1=$perubahan_debit1=$perubahan_kredit1=0;
                                $total_saldo_awal_debit=$total_saldo_awal_kredit=$total_perubahan_debit=$total_perubahan_kredit=$total_all_debit=$total_all_kredit=0;
                                
                                @endphp
                                @foreach($data_akun as $key => $value)
                                    @php 
                                        $saldo_awal_debit1=($value->detail['perubahan_before']->total_debit + $value->detail['saldo_month']->total_debit);
                                        $saldo_awal_kredit1=($value->detail['perubahan_before']->total_kredit + $value->detail['saldo_month']->total_kredit);
                                        $saldo_awal_akun1=($value->sifat_debit == 1 ? ($saldo_awal_debit1 - $saldo_awal_kredit1) : ($saldo_awal_kredit1 - $saldo_awal_debit1));
                                        $perubahan_debit1=$saldo_awal_debit1 + $value->detail['detail_month']->total_debit;
                                        $perubahan_kredit1=$saldo_awal_kredit1 + $value->detail['detail_month']->total_kredit;
                                        $perubahan_akun1=($value->sifat_debit == 1 ? ($perubahan_debit1 - $perubahan_kredit1) : ($perubahan_kredit1 - $perubahan_debit1));


                                        $total_saldo_awal_debit+=($value->sifat_debit == 1 && $saldo_awal_akun1 > 0 ? $saldo_awal_akun1 : ($value->sifat_debit == 0 && $saldo_awal_akun1 < 0 ? abs($saldo_awal_akun1) : 0));
                                        $total_saldo_awal_kredit+=($value->sifat_debit == 0 && $saldo_awal_akun1 > 0 ? $saldo_awal_akun1 : ($value->sifat_debit == 1 && $saldo_awal_akun1 < 0 ? abs($saldo_awal_akun1) : 0));

                                        //$total_saldo_awal_debit+=$saldo_awal_debit1;
                                        //$total_saldo_awal_kredit+=$saldo_awal_kredit1;
                                        $total_perubahan_debit+=$value->detail['detail_month']->total_debit;
                                        $total_perubahan_kredit+=$value->detail['detail_month']->total_kredit;
                                        $total_all_debit+=($value->sifat_debit == 1 && $perubahan_akun1 > 0 ? $perubahan_akun1 : ($value->sifat_debit == 0 && $perubahan_akun1 < 0 ? abs($perubahan_akun1) : 0));;
                                        $total_all_kredit+=($value->sifat_debit == 0 && $perubahan_akun1 > 0 ? $perubahan_akun1 : ($value->sifat_debit == 1 && $perubahan_akun1 < 0 ? abs($perubahan_akun1) : 0));
                                    @endphp
                                <tr id="table" class="table-info">
                                    <td><b>{{$value->no_akun}}</b></td>
                                    <td><b>
                                    {{$value->nama_akun}}
                                    </b></td>
                                    <td class="text-right">@if($value->sifat_debit == 1 && $saldo_awal_akun1 > 0) Rp. {{formatRupiah($saldo_awal_akun1)}} @elseif($value->sifat_debit == 0 && $saldo_awal_akun1 < 0)  Rp. {{formatRupiah(abs($saldo_awal_akun1))}} @else Rp. 0 @endif</td>
                                    <td class="text-right">@if($value->sifat_debit == 0 && $saldo_awal_akun1 > 0) Rp. {{formatRupiah($saldo_awal_akun1)}} @elseif($value->sifat_debit == 1 && $saldo_awal_akun1 < 0)  Rp. {{formatRupiah(abs($saldo_awal_akun1))}} @else Rp. 0 @endif</td>
                                    <td class="text-right">
                                        <a href="" onclick="doShowGL(this);" data-id="{{$value->id_akun}}" data-tipe="DEBIT" data-toggle="modal" data-target="#modalShowGL"> 
                                        Rp. {{formatRupiah($value->detail['detail_month']->total_debit)}}
                                        </a>
                                    </td>
                                    <td class="text-right">
                                        <a href="" onclick="doShowGL(this);" data-id="{{$value->id_akun}}" data-tipe="KREDIT" data-toggle="modal" data-target="#modalShowGL"> 
                                        Rp. {{formatRupiah($value->detail['detail_month']->total_kredit)}}
                                        </a>
                                    </td>
                                    <td colspan="2" class="text-right">@if($value->sifat_debit == 1 && $perubahan_akun1 > 0) Rp. {{formatRupiah($perubahan_akun1)}} @elseif($value->sifat_debit == 0 && $perubahan_akun1 < 0)  Rp. {{formatRupiah(abs($perubahan_akun1))}} @else Rp. 0 @endif</td>
                                    <td colspan="2" class="text-right">@if($value->sifat_debit == 0 && $perubahan_akun1 > 0) Rp. {{formatRupiah($perubahan_akun1)}} @elseif($value->sifat_debit == 1 && $perubahan_akun1 < 0)  Rp. {{formatRupiah(abs($perubahan_akun1))}} @else Rp. 0 @endif</td>
                                </tr>      
                                    @foreach($value->child as $v)
                                        @if($v->turunan1 != 22)
                                        
                                        @if(count($v->child) < 1 || in_array($v->id_akun, [152, 153, 169, 154, 43, 44, 45, 46, 47, 48]))
                                        @php 
                                            $saldo_awal_debit=($v->detail['perubahan_before']->total_debit + $v->detail['saldo_month']->total_debit);
                                            $saldo_awal_kredit=($v->detail['perubahan_before']->total_kredit + $v->detail['saldo_month']->total_kredit);
                                            $saldo_awal_akun=($v->sifat_debit == 1 ? ($saldo_awal_debit - $saldo_awal_kredit) : ($saldo_awal_kredit - $saldo_awal_debit));
                                            $perubahan_debit=$saldo_awal_debit + $v->detail['detail_month']->total_debit;
                                            $perubahan_kredit=$saldo_awal_kredit + $v->detail['detail_month']->total_kredit;
                                            $perubahan_akun=($v->sifat_debit == 1 ? ($perubahan_debit - $perubahan_kredit) : ($perubahan_kredit - $perubahan_debit));
                                        @endphp
                                    <tr id="table">
                                        <td style="padding-left:10px"><b>{{$v->no_akun}}</b></td>
                                        <td><b>{{$v->nama_akun}}</b></td>
                                        <td class="text-right">@if($v->sifat_debit == 1 && $saldo_awal_akun > 0) Rp. {{formatRupiah($saldo_awal_akun)}} @elseif($v->sifat_debit == 0 && $saldo_awal_akun < 0)  Rp. {{formatRupiah(abs($saldo_awal_akun))}} @else Rp. 0 @endif</td>
                                        <td class="text-right">@if($v->sifat_debit == 0 && $saldo_awal_akun > 0) Rp. {{formatRupiah($saldo_awal_akun)}} @elseif($v->sifat_debit == 1 && $saldo_awal_akun < 0)  Rp. {{formatRupiah(abs($saldo_awal_akun))}} @else Rp. 0 @endif</td>
                                        <td class="text-right">
                                            <a href="" onclick="doShowGL(this);" data-id="{{$v->id_akun}}" data-tipe="DEBIT" data-toggle="modal" data-target="#modalShowGL">
                                            Rp. {{formatRupiah($v->detail['detail_month']->total_debit)}}
                                            </a>
                                        </td>
                                        <td class="text-right">
                                            <a href="" onclick="doShowGL(this);" data-id="{{$v->id_akun}}" data-tipe="KREDIT" data-toggle="modal" data-target="#modalShowGL">
                                            Rp. {{formatRupiah($v->detail['detail_month']->total_kredit)}}
                                            </a>
                                        </td>
                                        <td colspan="2" class="text-right">@if($v->sifat_debit == 1 && $perubahan_akun > 0) Rp. {{formatRupiah($perubahan_akun)}} @elseif($v->sifat_debit == 0 && $perubahan_akun < 0)  Rp. {{formatRupiah(abs($perubahan_akun))}} @else Rp. 0 @endif</td>
                                        <td colspan="2" class="text-right">@if($v->sifat_debit == 0 && $perubahan_akun > 0) Rp. {{formatRupiah($perubahan_akun)}} @elseif($v->sifat_debit == 1 && $perubahan_akun < 0)  Rp. {{formatRupiah(abs($perubahan_akun))}} @else Rp. 0 @endif</td>
                                    </tr> 
                                        @endif
                                        @foreach($v->child as $v1)
                                            @if(!in_array($v1->turunan2, $except))
                                            
                                            @if(count($v1->child) < 1 || in_array($v1->id_akun, [50, 51, 52, 53, 54, 179]))
                                            @php 
                                                $saldo_awal_debit=($v1->detail['perubahan_before']->total_debit + $v1->detail['saldo_month']->total_debit);
                                                $saldo_awal_kredit=($v1->detail['perubahan_before']->total_kredit + $v1->detail['saldo_month']->total_kredit);
                                                $saldo_awal_akun=($v1->sifat_debit == 1 ? ($saldo_awal_debit - $saldo_awal_kredit) : ($saldo_awal_kredit - $saldo_awal_debit));
                                                $perubahan_debit=$saldo_awal_debit + $v1->detail['detail_month']->total_debit;
                                                $perubahan_kredit=$saldo_awal_kredit + $v1->detail['detail_month']->total_kredit;
                                                $perubahan_akun=($v1->sifat_debit == 1 ? ($perubahan_debit - $perubahan_kredit) : ($perubahan_kredit - $perubahan_debit));
                                            @endphp
                                        <tr id="table" >
                                            <td style="padding-left:20px"><b>{{$v1->no_akun}}</b></td>
                                            <td><b>{{$v1->nama_akun}}</b></td>
                                            <td class="text-right">@if($v1->sifat_debit == 1 && $saldo_awal_akun > 0) Rp. {{formatRupiah($saldo_awal_akun)}} @elseif($v1->sifat_debit == 0 && $saldo_awal_akun < 0)  Rp. {{formatRupiah(abs($saldo_awal_akun))}} @else Rp. 0 @endif</td>
                                            <td class="text-right">@if($v1->sifat_debit == 0 && $saldo_awal_akun > 0) Rp. {{formatRupiah($saldo_awal_akun)}} @elseif($v1->sifat_debit == 1 && $saldo_awal_akun < 0)  Rp. {{formatRupiah(abs($saldo_awal_akun))}} @else Rp. 0 @endif</td>
                                            <td class="text-right">
                                                <a href="" onclick="doShowGL(this);" data-id="{{$v1->id_akun}}" data-tipe="DEBIT" data-toggle="modal" data-target="#modalShowGL">
                                                Rp. {{formatRupiah($v1->detail['detail_month']->total_debit)}}
                                                </a>
                                            </td>
                                            <td class="text-right">
                                                <a href="" onclick="doShowGL(this);" data-id="{{$v1->id_akun}}" data-tipe="KREDIT" data-toggle="modal" data-target="#modalShowGL">
                                                Rp. {{formatRupiah($v1->detail['detail_month']->total_kredit)}}
                                                </a>
                                            </td>
                                            <td colspan="2" class="text-right">@if($v1->sifat_debit == 1 && $perubahan_akun > 0) Rp. {{formatRupiah($perubahan_akun)}} @elseif($v1->sifat_debit == 0 && $perubahan_akun < 0)  Rp. {{formatRupiah(abs($perubahan_akun))}} @else Rp. 0 @endif</td>
                                            <td colspan="2" class="text-right">@if($v1->sifat_debit == 0 && $perubahan_akun > 0) Rp. {{formatRupiah($perubahan_akun)}} @elseif($v1->sifat_debit == 1 && $perubahan_akun < 0)  Rp. {{formatRupiah(abs($perubahan_akun))}} @else Rp. 0 @endif</td>
                                        </tr>            
                                            @endif                 
                                            @foreach($v1->child as $v2)
                                                @if($v2->turunan2 != 152 && $v2->turunan2 != 49)
                                                @php 
                                                    $saldo_awal_debit=($v2->detail['perubahan_before']->total_debit + $v2->detail['saldo_month']->total_debit);
                                                    $saldo_awal_kredit=($v2->detail['perubahan_before']->total_kredit + $v2->detail['saldo_month']->total_kredit);
                                                    $saldo_awal_akun=($v2->sifat_debit == 1 ? ($saldo_awal_debit - $saldo_awal_kredit) : ($saldo_awal_kredit - $saldo_awal_debit));
                                                    $perubahan_debit=$saldo_awal_debit + $v2->detail['detail_month']->total_debit;
                                                    $perubahan_kredit=$saldo_awal_kredit + $v2->detail['detail_month']->total_kredit;
                                                    $perubahan_akun=($v2->sifat_debit == 1 ? ($perubahan_debit - $perubahan_kredit) : ($perubahan_kredit - $perubahan_debit));
                                                @endphp
                                                <tr id="table" >
                                                    <td style="padding-left:30px"><b>{{$v2->no_akun}}</b></td>
                                                    <td><b>{{$v2->nama_akun}}</b></td>
                                                    <td class="text-right">@if($v2->sifat_debit == 1 && $saldo_awal_akun > 0) Rp. {{formatRupiah($saldo_awal_akun)}} @elseif($v2->sifat_debit == 0 && $saldo_awal_akun < 0)  Rp. {{formatRupiah(abs($saldo_awal_akun))}} @else Rp. 0 @endif</td>
                                                    <td class="text-right">@if($v2->sifat_debit == 0 && $saldo_awal_akun > 0) Rp. {{formatRupiah($saldo_awal_akun)}} @elseif($v2->sifat_debit == 1 && $saldo_awal_akun < 0)  Rp. {{formatRupiah(abs($saldo_awal_akun))}} @else Rp. 0 @endif</td>
                                                    <td class="text-right">
                                                        <a href="" onclick="doShowGL(this);" data-id="{{$v2->id_akun}}" data-tipe="DEBIT" data-toggle="modal" data-target="#modalShowGL">
                                                        Rp. {{formatRupiah($v2->detail['detail_month']->total_debit)}}
                                                        </a>
                                                    </td>
                                                    <td class="text-right">
                                                        <a href="" onclick="doShowGL(this);" data-id="{{$v2->id_akun}}" data-tipe="KREDIT" data-toggle="modal" data-target="#modalShowGL">
                                                        Rp. {{formatRupiah($v2->detail['detail_month']->total_kredit)}}
                                                        </a>
                                                    </td>
                                                    <td colspan="2" class="text-right">@if($v2->sifat_debit == 1 && $perubahan_akun > 0) Rp. {{formatRupiah($perubahan_akun)}} @elseif($v2->sifat_debit == 0 && $perubahan_akun < 0)  Rp. {{formatRupiah(abs($perubahan_akun))}} @else Rp. 0 @endif</td>
                                                    <td colspan="2" class="text-right">@if($v2->sifat_debit == 0 && $perubahan_akun > 0) Rp. {{formatRupiah($perubahan_akun)}} @elseif($v2->sifat_debit == 1 && $perubahan_akun < 0)  Rp. {{formatRupiah(abs($perubahan_akun))}} @else Rp. 0 @endif</td>
                                                </tr>                       
                                                @endif                 
                                            @endforeach   
                                            @endif         
                                        @endforeach
                                        @endif                                  
                                    @endforeach                                  
                                    <!-- <tr id="table" class="table-danger">
                                        <td colspan="2" class="text-center"><b>Total {{$value->nama_akun}}</b></td>
                                        <td class="text-right">@if($value->sifat_debit == 1 && $saldo_awal_akun1 > 0) Rp. {{formatRupiah($saldo_awal_akun1)}} @elseif($value->sifat_debit == 0 && $saldo_awal_akun1 < 0)  Rp. {{formatRupiah(abs($saldo_awal_akun1))}} @else Rp. 0 @endif</td>
                                        <td class="text-right">@if($value->sifat_debit == 0 && $saldo_awal_akun1 > 0) Rp. {{formatRupiah($saldo_awal_akun1)}} @elseif($value->sifat_debit == 1 && $saldo_awal_akun1 < 0)  Rp. {{formatRupiah(abs($saldo_awal_akun1))}} @else Rp. 0 @endif</td>
                                        <td class="text-right">Rp. {{formatRupiah($value->detail['detail_month']->total_debit)}}</td>
                                        <td class="text-right">Rp. {{formatRupiah($value->detail['detail_month']->total_kredit)}}</td>
                                        <td colspan="2"  class="text-right">Rp. {{formatRupiah($perubahan_debit1)}}</td>
                                        <td colspan="2"  class="text-right">Rp. {{formatRupiah($perubahan_kredit1)}}</td>
                                    </tr>       -->
                                @endforeach
                                <tr id="table" class="table-success">
                                    <td colspan="2" class="text-center"><b>Total Saldo</b></td>
                                    <td class="text-right">Rp. {{formatRupiah($total_saldo_awal_debit)}}</td>
                                    <td class="text-right">Rp. {{formatRupiah($total_saldo_awal_kredit)}}</td>
                                    <td class="text-right">Rp. {{formatRupiah($total_perubahan_debit)}}</td>
                                    <td class="text-right">Rp. {{formatRupiah($total_perubahan_kredit)}}</td>
                                    <td colspan="2"  class="text-right">Rp. {{formatRupiah($total_all_debit)}}</td>
                                    <td colspan="2"  class="text-right">Rp. {{formatRupiah($total_all_kredit)}}</td>
                                </tr>
                            </tbody>
                            <tbody>
                                <tr>
                                    <th colspan="10"></th>
                                </tr>
                                <tr id="table" class="table-primary">
                                    <th colspan="10"><b>Ringkasan Saldo</b></th>
                                </tr>
                                @php 
                                $total_saldo_awal_debit=$total_saldo_awal_kredit=$saldo_awal_debit=$saldo_awal_kredit=$perubahan_debit=$perubahan_kredit=$perubahan_before=$saldo_awal_debit1=$saldo_awal_kredit1=$perubahan_debit1=$perubahan_kredit1=$total_perubahan_debit=$total_perubahan_kredit=$total_all_debit=$total_all_kredit=0;
                                @endphp
                                @foreach($data_akun as $key => $value)
                                    @php 
                                        $saldo_awal_debit1=($value->detail['perubahan_before']->total_debit + $value->detail['saldo_month']->total_debit);
                                        $saldo_awal_kredit1=($value->detail['perubahan_before']->total_kredit + $value->detail['saldo_month']->total_kredit);
                                        $saldo_awal_akun1=($value->sifat_debit == 1 ? ($saldo_awal_debit1 - $saldo_awal_kredit1) : ($saldo_awal_kredit1 - $saldo_awal_debit1));
                                        $perubahan_debit1=$saldo_awal_debit1 + $value->detail['detail_month']->total_debit;
                                        $perubahan_kredit1=$saldo_awal_kredit1 + $value->detail['detail_month']->total_kredit;
                                        $perubahan_akun1=($value->sifat_debit == 1 ? ($perubahan_debit1 - $perubahan_kredit1) : ($perubahan_kredit1 - $perubahan_debit1));

                                        $total_saldo_awal_debit+=($value->sifat_debit == 1 && $saldo_awal_akun1 > 0 ? $saldo_awal_akun1 : ($value->sifat_debit == 0 && $saldo_awal_akun1 < 0 ? abs($saldo_awal_akun1) : 0));
                                        $total_saldo_awal_kredit+=($value->sifat_debit == 0 && $saldo_awal_akun1 > 0 ? $saldo_awal_akun1 : ($value->sifat_debit == 1 && $saldo_awal_akun1 < 0 ? abs($saldo_awal_akun1) : 0));

                                        //$total_saldo_awal_debit+=$saldo_awal_debit1;
                                        //$total_saldo_awal_kredit+=$saldo_awal_kredit1;
                                        $total_perubahan_debit+=$value->detail['detail_month']->total_debit;
                                        $total_perubahan_kredit+=$value->detail['detail_month']->total_kredit;

                                        $total_all_debit+=($value->sifat_debit == 1 && $perubahan_akun1 > 0 ? $perubahan_akun1 : ($value->sifat_debit == 0 && $perubahan_akun1 < 0 ? abs($perubahan_akun1) : 0));;
                                        $total_all_kredit+=($value->sifat_debit == 0 && $perubahan_akun1 > 0 ? $perubahan_akun1 : ($value->sifat_debit == 1 && $perubahan_akun1 < 0 ? abs($perubahan_akun1) : 0));
                                    @endphp
                                <tr id="table" class="table-info">
                                    <td><b>{{$value->no_akun}}</b></td>
                                    <td><b>{{$value->nama_akun}}</b></td>
                                    <td class="text-right">@if($value->sifat_debit == 1 && $saldo_awal_akun1 > 0) Rp. {{formatRupiah($saldo_awal_akun1)}} @elseif($value->sifat_debit == 0 && $saldo_awal_akun1 < 0)  Rp. {{formatRupiah(abs($saldo_awal_akun1))}} @else Rp. 0 @endif</td>
                                    <td class="text-right">@if($value->sifat_debit == 0 && $saldo_awal_akun1 > 0) Rp. {{formatRupiah($saldo_awal_akun1)}} @elseif($value->sifat_debit == 1 && $saldo_awal_akun1 < 0)  Rp. {{formatRupiah(abs($saldo_awal_akun1))}} @else Rp. 0 @endif</td>
                                    <td class="text-right">Rp. {{formatRupiah($value->detail['detail_month']->total_debit)}}</td>
                                    <td class="text-right">Rp. {{formatRupiah($value->detail['detail_month']->total_kredit)}}</td>
                                    <td colspan="2" class="text-right">@if($value->sifat_debit == 1 && $perubahan_akun1 > 0) Rp. {{formatRupiah($perubahan_akun1)}} @elseif($value->sifat_debit == 0 && $perubahan_akun1 < 0)  Rp. {{formatRupiah(abs($perubahan_akun1))}} @else Rp. 0 @endif</td>
                                    <td colspan="2" class="text-right">@if($value->sifat_debit == 0 && $perubahan_akun1 > 0) Rp. {{formatRupiah($perubahan_akun1)}} @elseif($value->sifat_debit == 1 && $perubahan_akun1 < 0)  Rp. {{formatRupiah(abs($perubahan_akun1))}} @else Rp. 0 @endif</td>
                                </tr>      
                                @endforeach
                                <tr id="table" class="table-success">
                                    <td colspan="2" class="text-center"><b>Total Saldo</b></td>
                                    <td class="text-right">Rp. {{formatRupiah($total_saldo_awal_debit)}}</td>
                                    <td class="text-right">Rp. {{formatRupiah($total_saldo_awal_kredit)}}</td>
                                    <td class="text-right">Rp. {{formatRupiah($total_perubahan_debit)}}</td>
                                    <td class="text-right">Rp. {{formatRupiah($total_perubahan_kredit)}}</td>
                                    <td colspan="2"  class="text-right">Rp. {{formatRupiah($total_all_debit)}}</td>
                                    <td colspan="2"  class="text-right">Rp. {{formatRupiah($total_all_kredit)}}</td>
                                </tr>
                            </tbody>
                        </table>
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
<script src="{!! asset('theme/assets/libs/jquery/dist/jquery.min.js') !!}"></script>
<script src="{!! asset('theme/assets/extra-libs/DataTables/datatables.min.js') !!}"></script>
<script type="text/javascript">
    function formatBulan(val){
        var bulan = ['Januari', 'Februari', 'Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
        var getMonth=val[1];
        return bulan[getMonth-1]+' '+val[0];
    }
    function doShowGL(eq){
        id=$(eq).data('id')
        tipe=$(eq).data('tipe')
        date='{{$data['date1']}}';
        date2='{{$data['date2']}}';
        $.ajax({
                type: "GET",
                url: "{{ URL::to('akuntansi/show_gl_detail') }}", //json get site
                dataType : 'json',
                data : {
                    id : id,
                    tipe : tipe,
                    date1 : date,
                    date2 : date2
                },
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
                        total=(parseFloat(arrData[i]['amount']) * parseFloat(arrData[i]['base_price'])).toFixed(0);
                        po_detail.row.add([
                            '<div class="text-left">'+arrData[i]['m_items']['no']+'</div>',
                            '<div class="text-left">'+arrData[i]['m_items']['name']+'</div>',
                            '<div class="text-right">'+arrData[i]['amount']+'</div>',
                            '<div class="text-center">'+arrData[i]['m_units']['name']+'</div>',
                            '<div class="text-right">'+formatCurrency(parseFloat(arrData[i]['base_price']).toFixed(2))+'</div>',
                            '<div class="text-right">'+formatCurrency(total.toString())+'</div>'
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
                        total=(parseFloat(arrData[i]['amount']) * parseFloat(arrData[i]['base_price'])).toFixed(0);
                        po_asset_detail.row.add([
                            '<div class="text-left">'+arrData[i]['m_items']['no']+'</div>',
                            '<div class="text-left">'+arrData[i]['m_items']['name']+'</div>',
                            '<div class="text-right">'+arrData[i]['amount']+'</div>',
                            '<div class="text-center">'+arrData[i]['m_units']['name']+'</div>',
                            '<div class="text-right">'+formatCurrency(parseFloat(arrData[i]['base_price']).toFixed(2))+'</div>',
                            '<div class="text-right">'+formatCurrency(total.toString())+'</div>'
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
                        total=(parseFloat(arrData[i]['amount']) * parseFloat(arrData[i]['base_price'])).toFixed(0);
                        inv_detail.row.add([
                            '<div class="text-left">'+arrData[i]['m_items']['no']+'</div>',
                            '<div class="text-left">'+arrData[i]['m_items']['name']+'</div>',
                            '<div class="text-right">'+arrData[i]['amount']+'</div>',
                            '<div class="text-center">'+arrData[i]['m_units']['name']+'</div>',
                            '<div class="text-right">'+formatCurrency(parseFloat(arrData[i]['base_price']).toFixed(2))+'</div>',
                            '<div class="text-right">'+formatCurrency(total.toString())+'</div>'
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

