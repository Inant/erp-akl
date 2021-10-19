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
                            <h4 id="title">Jurnal Umum Bulan November 2019</h4>
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
                                <th>Pendapatan dari Penjualan</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                            $sum_pendapatan=0;
                            foreach ($data['pendapatan'] as $key => $value) {
                            @endphp
                            <tr>
                                @php
                                if ($value->id_akun != 90) {
                                    $total=$value->jumlah_kredit-$value->jumlah_debit;
                                    $sum_pendapatan+=$total;
                                @endphp
                                    <td>{{$value->nama_akun}}</td>
                                    <td align="right">Rp. {{formatRupiah($total)}}</td>
                                @php
                                }else if ($value->id_akun == 90){
                                    $total=$value->jumlah_debit-$value->jumlah_kredit;  
                                    $sum_pendapatan-=$total;
                                @endphp
                                    <td>{{$value->nama_akun}}</td>
                                    <td align="right">- Rp. {{formatRupiah($total)}}</td>
                                @php
                                }
                                @endphp
                            </tr>
                            @php
                            }
                            @endphp
                            <tr style="background-color:#ddd">
                                <th>Total Pendapatan dari Penjualan</th>
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
                                <th>Harga Pokok Penjualan</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                            $sum_hpp=0;
                            foreach ($data['beban'] as $key => $value) {
                                if ($value->id_akun == 65 || $value->id_akun == 87) {
                                    $total=$value->jumlah_debit-$value->jumlah_kredit;
                                    $sum_hpp+=$total;
                            @endphp
                            <tr>
                                <td>{{$value->nama_akun}}</td>
                                <td align="right">Rp. {{formatRupiah($total)}}</td>
                            </tr>
                            @php
                                }
                            }
                            
                            foreach ($data['pendapatan'] as $key => $value) {
                                if ($value->id_akun == 45) {
                                    $total=$value->jumlah_kredit-$value->jumlah_debit;
                                    $sum_hpp-=$total;
                            @endphp
                            <tr>
                                <td>{{$value->nama_akun}}</td>
                                <td align="right">- Rp. {{formatRupiah($total)}}</td>
                            </tr>
                            @php
                                }
                            }
                            $bruto=$sum_pendapatan-$sum_hpp;
                            @endphp
                            <tr style="color:red;background-color:#ddd">
                                <th>Total Harga Pokok Penjualan</th>
                                <th class="text-right">Rp. {{formatRupiah($sum_hpp)}}</th>
                            </tr>
                        </tbody>
                        <tbody>
                            <tr>
                                <th></th>
                                <th></th>
                            </tr>
                        </tbody>
                        <tbody style="background-color:#3c8dbc; color:white">
                            <tr style="padding-top:10px">
                                <th>PENDAPATAN KOTOR</th>
                                <th class="text-right">Rp. {{formatRupiah($bruto)}}</th>
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
                                <th>Biaya Operasional</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                            $sum_beban=0;
                            foreach ($data['beban'] as $key => $value) {
                                if ($value->id_akun != 65 && $value->id_akun != 87) {
                                    $total=$value->jumlah_debit-$value->jumlah_kredit;
                                    $sum_beban+=$total;
                            @endphp
                            <tr>
                                <td>{{$value->nama_akun}}</td>
                                <td align="right">Rp. {{formatRupiah($total)}}</td>
                            </tr>
                            @php
                                }
                            }
                            $netto=$bruto-$sum_beban;
                            @endphp
                            <tr style="color:red;background-color:#ddd">
                                <th>Total Pengeluaran Beban</th>
                                <th class="text-right">Rp. {{formatRupiah($sum_beban)}}</th>
                            </tr>
                        </tbody>
                        <tbody>
                            <tr>
                                <th></th>
                                <th></th>
                            </tr>
                        </tbody>
                        <tbody style="background-color:#3c8dbc; color:white">
                            <tr style="padding-top:10px">
                                <th>PENDAPATAN BERSIH</th>
                                <th class="text-right">Rp. {{formatRupiah($netto)}}</th>
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
    console.log(bulan);
    $('#title').html('Laporan Laba Rugi Bulan '+formatBulan(bulan));
    function formatBulan(val){
        var bulan = ['Januari', 'Februari', 'Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
        var getMonth=val[1];
        return bulan[getMonth-1]+' '+val[0];
    }
</script>
@endsection
