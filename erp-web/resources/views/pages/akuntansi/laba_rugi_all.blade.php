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
                            <h4 id="title">Laba Rugi All</h4>
                        </div>
                    </div>
                    <form method="POST" action="{{ URL::to('akuntansi/laba_rugi_all') }}">
                    @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <input type="date" name="date" class="form-control" required value="{{$date1}}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <input type="date" name="date2" class="form-control" required  value="{{$date2}}">
                            </div>
                            <div class="col-md-12">
                                <button class="btn btn-success" name="submit" value="1">Submit</button>
                            </div>
                        </div>
                    </form>
                     <br>
                    <h4 id="title"></h4>
                    <form method="POST" action="{{ URL::to('akuntansi/export_laba_rugi_all') }}" class="float-right" target="_blank">
                        @csrf
                        <input type="date" name="date" hidden class="form-control"  required value="{{$date1}}">
                        <input type="date" name="date2" hidden class="form-control"  required value="{{$date2}}">
                        <div class="form-group">
                            <button class="btn btn-success"><i class="fa fa-file-excel"></i> Export</button>
                        </div>
                    </form>
                    <div class="table-responsive">
                    <table class="table table-bordered" id="detailKas">
                        @php
                        $sum_pendapatan=$sum_hpp=0;
                            foreach ($data as $key => $value) {
                                $total_pendapatan=$total_hpp=0;
                        @endphp
                            <!-- <tr>
                                <td colspan="2">Proyek Order {{$value->order_no}}</td>
                            </tr>
                            @php
                                foreach ($value->pendapatan as $v){
                            @endphp
                            <tr>
                                @php
                                $total=$v->total_kredit-$v->total_debit;
                                $total_pendapatan+=$total;
                                $sum_pendapatan+=$total;
                                @endphp
                                <td>{{$v->nama_akun}}</td>
                                <td align="right">Rp. {{formatRupiah($total)}}</td>
                            </tr>
                            @php
                                }
                            @endphp
                            @php
                                foreach ($value->hpp as $v){
                            @endphp
                            <tr>
                                @php
                                $total=$v->total_debit-$v->total_kredit;
                                $total_hpp+=$total;
                                $sum_hpp+=$total;
                                @endphp
                                <td>{{$v->nama_akun}}</td>
                                <td align="right">Rp. {{formatRupiah($total)}}</td>
                            </tr>
                            @php
                                }
                            @endphp
                        </tbody>
                        <tbody>
                            <tr style="background-color:#ddd">
                                <th>Gross Profit</th>
                                <th class="text-right">Rp. {{formatRupiah($total_pendapatan - $total_hpp)}}</th>
                            </tr>
                            <tr>
                                <td></td>
                                <td></td>
                            </tr>
                        </tbody> -->
                        @php
                            }
                        @endphp
                        <tbody style="background-color:#3c8dbc; color:white">
                            <tr>
                                <th>Pendapatan</th>
                                <th></th>
                            </tr>
                        </tbody>
                        <tbody>
                            @php
                            $sum_pendapatan=$sum_hpp=0;
                            foreach ($pendapatan as $key => $value) {
                                foreach ($value['data'] as $v){
                                    if ($v['detail'][0]->sifat_debit == 1) {
                                        $total=$v['detail'][0]->jumlah_debit-$v['detail'][0]->jumlah_kredit;
                                    }else{
                                        $total=$v['detail'][0]->jumlah_kredit-$v['detail'][0]->jumlah_debit;
                                    }
                                    $sum_pendapatan+=$total;
                                    if($total > 0){
                            @endphp
                            <tr>
                                <td>{{$v['detail'][0]->nama_akun}}</td>
                                <td align="right">Rp. {{formatRupiah($total)}}</td>
                            </tr>
                            @php
                                    }
                                    $total_hpp=($v['hpp']->total_debit - $v['hpp']->total_kredit);
                                    $sum_hpp+=$total_hpp;
                                    if($total > 0){
                            @endphp
                            <tr>
                                <td>HPP {{$v['detail'][0]->nama_akun}}</td>
                                <td align="right">Rp. {{formatRupiah($total_hpp)}}</td>
                            </tr>
                            @php
                                    }
                                }
                            }
                            @endphp
                            <tr style="background-color:#ddd">
                                <th>Total Pendapatan</th>
                                <th class="text-right">Rp. {{formatRupiah($sum_pendapatan)}}</th>
                            </tr>
                        </tbody>
                        <tbody>
                            <tr style="background-color:#ddd">
                                <th>Gross Profit All</th>
                                <th class="text-right">Rp. {{formatRupiah($sum_pendapatan - $sum_hpp)}}</th>
                            </tr>
                        </tbody>
                        <tbody style="background-color:#3c8dbc; color:white">
                            <tr>
                                <th>Biaya Operasional</th>
                                <th></th>
                            </tr>
                        </tbody>
                        <tbody>
                            @php
                            $sum_biaya_operasional=0;
                            foreach ($biaya_operasional as $key => $value) {
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
                        <tbody style="background-color:#3c8dbc; color:white">
                            <tr>
                                <th>Biaya Administrasi dan Umum</th>
                                <th></th>
                            </tr>
                        </tbody>
                        <tbody>
                            @php
                            $sum_biaya_adm=0;
                            foreach ($biaya_adm as $key => $value) {
                                if($value['id_akun'] != 124){
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
                        </tbody>
                        <tbody style="background-color:#3c8dbc; color:white">
                            <tr>
                                <th>Pendapatan Lain Lain</th>
                                <th></th>
                            </tr>
                        </tbody>
                        <tbody>
                            @php
                            $sum_biaya_lain=0;
                            foreach ($biaya_lain as $key => $value) {
                                foreach ($value['data'] as $v){
                            @endphp
                            <tr>
                                @php
                                if ($v['detail'][0]->sifat_debit == 1) {
                                    $total=$v['detail'][0]->jumlah_debit-$v['detail'][0]->jumlah_kredit;
                                }else{
                                    $total=$v['detail'][0]->jumlah_kredit-$v['detail'][0]->jumlah_debit;
                                }
                                $sum_biaya_lain+=$total;
                                @endphp
                                <td>{{$v['detail'][0]->nama_akun}}</td>
                                <td align="right">Rp. {{formatRupiah($total)}}</td>
                            </tr>
                            @php
                                }
                            }
                            @endphp
                            <tr style="background-color:#ddd">
                                <th>Total Pendapatan Lain Lain</th>
                                <th class="text-right">Rp. {{formatRupiah($sum_biaya_lain)}}</th>
                            </tr>
                        </tbody>
                        <tbody style="background-color:#3c8dbc; color:white">
                            <tr>
                                <th>Hutang Pajak</th>
                                <th></th>
                            </tr>
                        </tbody>
                        <tbody>
                            @php
                            $hutang_ppn=0;
                            foreach ($biaya_adm as $key => $value) {
                                if($value['id_akun'] == 124){
                                foreach ($value['data'] as $v){
                            @endphp
                            <tr>
                                @php
                                if ($v['detail'][0]->sifat_debit == 1) {
                                    $total=$v['detail'][0]->jumlah_debit-$v['detail'][0]->jumlah_kredit;
                                }else{
                                    $total=$v['detail'][0]->jumlah_kredit-$v['detail'][0]->jumlah_debit;
                                }
                                $hutang_ppn+=$total;
                                @endphp
                                <td>{{$v['detail'][0]->nama_akun}}</td>
                                <td align="right">Rp. {{formatRupiah($total)}}</td>
                            </tr>
                            @php
                                }
                                }
                            }
                            $ppn=($saldo_ppn != null ? $saldo_ppn->jumlah_saldo : 0) + $hutang_ppn;
                            @endphp
                            <tr style="background-color:#ddd">
                                <th>Total Hutang PPN</th>
                                <th class="text-right">Rp. {{formatRupiah($ppn)}}</th>
                            </tr>
                        </tbody>
                        <tbody>
                            <tr>
                                <td></td>
                                <td></td>
                            </tr>
                            <tr style="background-color:#ddd">
                                <th>Nett Profit</th>
                                <th class="text-right">Rp. {{formatRupiah($sum_pendapatan - ($sum_hpp + $sum_biaya_operasional + $sum_biaya_adm + $sum_biaya_lain + $ppn))}}</th>
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
    function formatBulan(val){
        var bulan = ['Januari', 'Februari', 'Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
        var getMonth=val[1];
        return bulan[getMonth-1]+' '+val[0];
    }
</script>
@endsection
