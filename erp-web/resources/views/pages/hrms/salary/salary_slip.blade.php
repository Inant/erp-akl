@extends('theme.default')

@section('breadcrumb')
      <div class="page-breadcrumb">
                <div class="row">
                    <div class="col-5 align-self-center">
                        <h4 class="page-title">Detail Slip Gaji</h4>
                        <div class="d-flex align-items-center">
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="{{ URL::to('salary') }}">Gaji</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">Slip</li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
@endsection

@section('content')
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
@endphp
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title">
                            </h4>
                            <div class="text-right">
                                <form action="{{URL::to('salary/cetak')}}" method="post" target="_blank">
                                    @csrf
                                        <input type="hidden" value="{{($gaji != null ? $gaji->m_employee_id : '')}}" name="id_pegawai">
                                        @php
                                        if (isset($_POST['bulan'])) {
                                            
                                        @endphp
                                        <input type="hidden" value="{{$_POST['bulan']}}" name="bulan">
                                        <input type="hidden" value="{{$_POST['tahun']}}" name="tahun">
                                        @php
                                        }else{
                                        @endphp
                                        <input type="hidden" value="{{date('m')}}" name="bulan">
                                        <input type="hidden" value="{{date('Y')}}" name="tahun">
                                        @php
                                        }
                                        @endphp
                                        <button class="btn btn-info btn-sm"><i class="fa fa-wpforms"></i>Cetak Slip Gaji</button>
                                    @php
                                    $gaji_pokok=$jumlah_absen > 1 ? ($gaji->gaji_pokok / $total_hari_kerja) * ($jumlah_kehadiran + 1) : $gaji->gaji_pokok;
                                    $kehadiran=$jumlah_kehadiran;
                                    $total_uang_makan=$jumlah_kehadiran * $gaji->uang_makan;
                                    $total_uang_transport=$jumlah_kehadiran * $gaji->uang_transport;
                                    $bonus_kerajinan=$jumlah_denda < 1500000 ? 300000 : 0;
                                    $grand_total=$gaji != null ? (($gaji_pokok + $total_uang_makan + $total_uang_transport + $bonus_disiplin) - $jumlah_denda) : 0;
                                    @endphp
                                </form>
                            </div>
                                
                            <form accept="/salary/slip/{{$id}}" method="post">
                                @csrf
                                    <div class="form-inline">
                                        <label>Pilih Bulan : </label>&nbsp;
                                        <select data-plugin-selectTwo class="form-control select2" name="bulan" id="bulan" required>
                                            <option value="">Pilih Bulan</option>
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
                                        <select class="form-control select2" name="tahun" id="tahun" required>
                                            <option value="">Pilih Tahun</option>
                                            @for ($i = date('Y') - 5; $i <= date('Y'); $i++) { 
                                            <option value="{{$i}}">{{$i}}</option>
                                            @endfor
                                        </select>&nbsp;
                                        <button class="btn btn-primary"  onclick="cekAbsensiDate()"><i class="fa fa-search"></i></button>
                                    </div>
                            </form>
                            <br>
                            <div class="col-sm-4">
                                <div class="row">
                                    <table>
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
                                    <br>
                                </div>
                            </div>
                            <br>
                            <table class="table table-bordered table-striped" id="mytable">
                                <thead>
                                    <tr>
                                        <th>Tipe</th>
                                        <th class="text-right">Fee</th>
                                        <th class="text-center">Kehadiran</th>
                                        <th class="text-right">sub total</th>
                                    </tr>
                                    <tr>
                                        <th>Gaji Pokok</th>
                                        <td class="text-right">Rp. {{formatCurrency($gaji->gaji_pokok)}}</td>
                                        <td colspan="2" class="text-right">@if($gaji->gaji_pokok != $gaji_pokok) (Setelah Potongaan) @endif Rp. {{formatCurrency($gaji_pokok)}}</td>
                                    </tr>
                                   <tr>
                                        <th>Total Uang Makan</th>
                                        <td class="text-right">Rp. {{formatCurrency($gaji->uang_makan)}}</td>
                                        <td class="text-center">{{$kehadiran}}</td>
                                        <td class="text-right">Rp. {{formatCurrency($total_uang_makan)}}</td>
                                    </tr>
                                    <tr>
                                        <th>Total Uang Transport</th>
                                        <td class="text-right">Rp. {{formatCurrency($gaji->uang_transport)}}</td>
                                        <td class="text-center">{{$kehadiran}}</td>
                                        <td class="text-right">Rp. {{formatCurrency($total_uang_transport)}}</td>
                                    </tr>
                                    <tr>
                                        <td>Bonus Disiplin (per Minggu)</td>
                                        <td class="text-right">Rp. {{formatCurrency($gaji_pokok * (2.5/100))}}</td>
                                        <td class="text-center">{{$total_bonus_disiplin}}</td>
                                        <td class="text-right">Rp. {{formatCurrency($bonus_disiplin)}}</td>
                                    </tr>
                                    <tr class="table-danger">
                                        <td>Denda</td>
                                        <td class="text-right"></td>
                                        <td class="text-center"></td>
                                        <td class="text-right">Rp. {{formatCurrency($jumlah_denda)}}</td>
                                    </tr>
                                    <tr>
                                        <th colspan="3" class="text-center">Total</th>
                                        <th class="text-right">Rp. {{formatCurrency($grand_total)}}</th>
                                    </tr>
                                </thead>
                            </table>
                            <br>
                            <div class="row">
                                <div class="col-sm-6">
                                    <h4 class="title" style="color:black">Detail Denda</h4>
                                    <table class="table table-bordered table-striped" id="mytable">
                                        <thead>
                                            <tr>
                                                <th>Tanggal</th>
                                                <th>Alasan</th>
                                                <th class="text-center">Denda</th>
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
                                                <td class="text-right">Rp. {{formatCurrency($value['denda'])}}</td>
                                            </tr>
                                            @php
                                                // }
                                            }
                                            @endphp
                                            <tr>
                                                <th class="text-center" colspan="2">Total Denda</th>
                                                <th class="text-right">Rp. {{formatCurrency($jumlah_denda)}}</th>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            </div>
                    </div>
                </div>
            </div>
        </div>

<script type="text/javascript" src="https://code.jquery.com/jquery-3.3.1.js"></script>
</script>
<script>
    var bulan='{{$bulan}}';
    console.log(bulan);
    var gaji_pokok="{{($gaji != null ? $gaji->gaji_pokok : 0)}}";
    var jumlah_denda="{{$jumlah_denda}}";
    var grand="{{$grand_total}}";
</script>
@endsection



