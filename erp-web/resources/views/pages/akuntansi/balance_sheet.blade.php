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
<div class="container-fluid">
    <!-- basic table -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-12">
                            <h4 class="box-title">Jurnal Umum Bulan November 2019</h4>
                            <form method="POST" action="{{ URL::to('akuntansi/neraca') }}" class="form-inline float-right">
                              @csrf
                            <div class="form-inline">
                                <!-- <div class="form-group">
                                <select name="" id="" class="form-control select2" style="width:120px"></select>
                                </div>&nbsp; -->
                                <label>Pilih Bulan : </label>&nbsp;
                                <select data-plugin-selectTwo class="form-control select2" name="bulan" id="bulan" required style="width:120px">
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
                                <select data-plugin-selectTwo class="form-control select2" name="tahun" id="tahun" required  style="width:120px">
                                    <option value="">--Pilih Tahun--</option>
                                    @for ($i = date('Y') - 5; $i <= date('Y'); $i++)
                                    <option value="{{$i}}">{{$i}}</option>
                                    @endfor
                                </select>&nbsp;
                                <button class="btn btn-primary"  onclick="cekAbsensiDate()"><i class="fa fa-search"></i></button>
                            </div>
                            </form>
                        </div>
                    </div>
                     <br>
                     <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead id="table">
                                <tr>
                                    <th rowspan="2" style="vertical-align : middle;text-align:center;">No Akun</th>
                                    <th rowspan="2" style="vertical-align : middle;text-align:center;">Nama Akun</th>
                                    <th colspan="2" class="text-center">Saldo</th>
                                </tr>
                                <tr>
                                    <th>Debit</th>
                                    <th>Kredit</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php 
                                $jumlah_debit=$jumlah_kredit=$total_debit=$total_kredit=0;
                                @endphp
                                @foreach ($data['saldo'] as $key => $value)
                                <tr id="table">
                                    <th></th>
                                    <th><b>{{$value['nama']}}</b></th>
                                    <th class="text-right"></th>
                                    <th class="text-right"></th>
                                </tr>
                                    @foreach ($value['data'] as $v)
                                    @php $saldo = $v['saldo'] @endphp
                                    <tr id="table">
                                        <td>{{$v['detail'][0]->no_akun}}</td>
                                        <td>{{$v['detail'][0]->nama_akun}}</td>
                                        @php
                                        if ($value['id_parent'] == 3 || $value['id_parent'] == 7) {
                                            $a= (($saldo != null ? $saldo->jumlah_saldo : 0) + $v['detail'][0]->jumlah_debit) - $v['detail'][0]->jumlah_kredit;
                                            if($a < 0){
                                                $total_kredit+=abs($a);
                                            }else{
                                                $total_debit+=$a;
                                            }
                                            if ($a < 0) {
                                                $a=abs($a);
                                                @endphp
                                        <td class="text-right">-</td>
                                        <td class="text-right">Rp. {{formatRupiah($a)}}</td>
                                        @php
                                            }else{
                                        @endphp
                                        <td class="text-right">Rp. {{formatRupiah($a)}}</td>
                                        <td class="text-right">-</td>
                                        @php
                                            }
                                        }else{
                                            $b=(($saldo != null ? $saldo->jumlah_saldo : 0) + $v['detail'][0]->jumlah_kredit) - $v['detail'][0]->jumlah_debit;
                                            if($b < 0){
                                                $total_debit+=abs($b);
                                            }else{
                                                $total_kredit+=$b;
                                            }
                                            if ($b < 0) {
                                                $b=abs($b);
                                            @endphp
                                        <td class="text-right">Rp. {{formatRupiah($b)}}</td>    
                                        <td class="text-right">-</td>
                                        @php
                                            }else{
                                        @endphp
                                        <td class="text-right">-</td>
                                        <td class="text-right">Rp. {{formatRupiah($b)}}</td>
                                        @php
                                            }
                                        }
                                        @endphp
                                    </tr>   
                                    @endforeach
                                @endforeach
                                <tr id="table">
                                    <th colspan="2" class="text-center">Total Saldo</th>
                                    <th class="text-right">Rp. {{formatRupiah($total_debit)}}</th>
                                    <th class="text-right">Rp. {{formatRupiah($total_kredit)}}</th>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
                
</div>
<script src="https://code.jquery.com/jquery-1.10.2.js"></script>
<script type="text/javascript">
    var bulan=@php print_r($bulan) @endphp;
    console.log(bulan);
    $('.box-title').html('Neraca Saldo Bulan '+formatBulan(bulan));
    function formatBulan(val){
        var bulan = ['Januari', 'Februari', 'Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
        var getMonth=val[1];
        return bulan[getMonth-1]+' '+val[0];
    }
</script>
@endsection

