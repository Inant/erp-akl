@extends('theme.default')

@section('breadcrumb')
            <div class="page-breadcrumb">
                <div class="row">
                    <div class="col-5 align-self-center">
                        <h4 class="page-title">Laba Rugi</h4>
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
                            <h4 id="title">Laba Rugi</h4>
                            <form method="POST" action="{{ URL::to('akuntansi/profit-loss') }}" class="form-inline">
                              @csrf
                            <div class="form-inline">
                                <label>Pilih Bulan : </label>&nbsp;
                                <select class="form-control select2" name="bulan" id="bulan" required style="width:150px">
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
                                <select class="form-control select2" name="tahun" id="tahun" required  style="width:140px">
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
                    <h4 id="title"></h4>
                    <div class="table-responsive">
                    <table class="table table-bordered" id="detailKas">
                        <thead style="background-color:#3c8dbc; color:white">
                            <tr>
                                <th>Pendapatan</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                            $sum_pendapatan=0;
                            foreach ($data['profit'] as $key => $value) {
                                foreach ($value['data'] as $v){
                            @endphp
                            <tr>
                                @php
                                if ($v['detail'][0]->sifat_debit == 1) {
                                    $total=$v['detail'][0]->jumlah_debit-$v['detail'][0]->jumlah_kredit;
                                }else{
                                    $total=$v['detail'][0]->jumlah_kredit-$v['detail'][0]->jumlah_debit;
                                }
                                $sum_pendapatan+=$total;
                                @endphp
                                <td>{{$v['detail'][0]->nama_akun}}</td>
                                <td align="right">Rp. {{formatRupiah($total)}}</td>
                            </tr>
                            @php
                                }
                            }
                            @endphp
                            <tr style="background-color:#ddd">
                                <th>Total Pendapatan</th>
                                <th class="text-right">Rp. {{formatRupiah($sum_pendapatan)}}</th>
                            </tr>
                        </tbody>
                        <tbody>
                            <tr>
                                <th></th>
                                <th></th>
                            </tr>
                        </tbody>
                        <thead style="background-color:#3c8dbc; color:white">
                            <tr>
                                <th>Biaya Produksi</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                            $sum_biaya_produksi=0;
                            foreach ($data['biaya_produksi'] as $key => $value) {
                                foreach ($value['data'] as $v){
                            @endphp
                            <tr>
                                @php
                                if ($v['detail'][0]->sifat_debit == 1) {
                                    $total=$v['detail'][0]->jumlah_debit-$v['detail'][0]->jumlah_kredit;
                                }else{
                                    $total=$v['detail'][0]->jumlah_kredit-$v['detail'][0]->jumlah_debit;
                                }
                                $sum_biaya_produksi+=$total;
                                @endphp
                                <td>{{$v['detail'][0]->nama_akun}}</td>
                                <td align="right">Rp. {{formatRupiah($total)}}</td>
                            </tr>
                            @php
                                }
                            }
                            @endphp
                            <tr style="background-color:#ddd">
                                <th>Total Biaya Produksi</th>
                                <th class="text-right">Rp. {{formatRupiah($sum_biaya_produksi)}}</th>
                            </tr>
                        </tbody>
                        <tbody>
                            <tr>
                                <th></th>
                                <th></th>
                            </tr>
                            <tr style="background-color:#da5353; color:white">
                                <th>Profit Gross</th>
                                <th class="text-right">Rp. {{formatRupiah($sum_pendapatan - $sum_biaya_produksi)}}</th>
                            </tr>
                            <tr>
                                <th></th>
                                <th></th>
                            </tr>
                        </tbody>
                        <thead style="background-color:#3c8dbc; color:white">
                            <tr>
                                <th>Biaya Operasional</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                            $sum_biaya_operasional=0;
                            foreach ($data['biaya_operasional'] as $key => $value) {
                                foreach ($value['data'] as $v){
                            @endphp
                            <tr>
                                @php
                                if ($v['detail'][0]->sifat_debit == 1) {
                                    $total=$v['detail'][0]->jumlah_debit-$v['detail'][0]->jumlah_kredit;
                                }else{
                                    $total=$v['detail'][0]->jumlah_kredit-$v['detail'][0]->jumlah_debit;
                                }
                                $sum_biaya_operasional+=$total;
                                @endphp
                                <td>{{$v['detail'][0]->nama_akun}}</td>
                                <td align="right">Rp. {{formatRupiah($total)}}</td>
                            </tr>
                            @php
                                }
                            }
                            @endphp
                            <tr style="background-color:#ddd">
                                <th>Total Biaya Operasional</th>
                                <th class="text-right">Rp. {{formatRupiah($sum_biaya_operasional)}}</th>
                            </tr>
                        </tbody>
                        <tbody>
                            <tr>
                                <th></th>
                                <th></th>
                            </tr>
                        </tbody>
                        <thead style="background-color:#3c8dbc; color:white">
                            <tr>
                                <th>Biaya Administrasi dan Umum</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                            $sum_biaya_adm=0;
                            foreach ($data['biaya_adm'] as $key => $value) {
                                foreach ($value['data'] as $v){
                            @endphp
                            <tr>
                                @php
                                if ($v['detail'][0]->sifat_debit == 1) {
                                    $total=$v['detail'][0]->jumlah_debit-$v['detail'][0]->jumlah_kredit;
                                }else{
                                    $total=$v['detail'][0]->jumlah_kredit-$v['detail'][0]->jumlah_debit;
                                }
                                $sum_biaya_adm+=$total;
                                @endphp
                                <td>{{$v['detail'][0]->nama_akun}}</td>
                                <td align="right">Rp. {{formatRupiah($total)}}</td>
                            </tr>
                            @php
                                }
                            }
                            @endphp
                            <tr style="background-color:#ddd">
                                <th>Total Biaya Administrasi dan Umum</th>
                                <th class="text-right">Rp. {{formatRupiah($sum_biaya_adm)}}</th>
                            </tr>
                        </tbody>
                        <tbody>
                            <tr>
                                <th></th>
                                <th></th>
                            </tr>
                            <tr style="background-color:#40a553; color:white">
                                <th>Nett Profit</th>
                                <th class="text-right">Rp. {{formatRupiah($sum_pendapatan - ($sum_biaya_produksi + $sum_biaya_operasional + $sum_biaya_adm))}}</th>
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
    var bulan=@php print_r($data['bulan']) @endphp;
    $('#title').html('Laporan Laba Rugi Bulan '+formatBulan(bulan));
    function formatBulan(val){
        var bulan = ['Januari', 'Februari', 'Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
        var getMonth=val[1];
        return bulan[getMonth-1]+' '+val[0];
    }
</script>
@endsection
