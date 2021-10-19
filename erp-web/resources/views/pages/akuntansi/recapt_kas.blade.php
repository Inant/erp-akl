@extends('theme.default')

@section('breadcrumb')
            <div class="page-breadcrumb">
                <div class="row">
                    <div class="col-5 align-self-center">
                        <h4 class="page-title">Laporan Keluar Masuk Kas & Bank</h4>
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
                            <h4 id="titleJurnal">Laporan Keluar Masuk Kas & Bank</h4>
                            <form method="POST" action="{{ URL::to('akuntansi/recapt_kas') }}" class="form-inline float-right">
                              @csrf
                                <label>Cari Rentang Tanggal :</label>&nbsp;
                                <input type="date" name="date" class="form-control" required value="{{$date}}">&nbsp;
                                <input type="date" name="date2" class="form-control" required  value="{{$date2}}">&nbsp;
                                <button class="btn btn-success">cari</button>&nbsp;
                             </form>
                        </div>
                    </div>
                     <br>
                    <div class="table-responsive">
                        <table class="table table-bordered" id="jurnal_list" width="100%">
                            <thead>
                                <tr>
                                    <th width="100px">Tanggal</th>
                                    <th>No Sumber</th>
                                    <th>No Akun</th>
                                    <th>Nama Akun</th>
                                    <th>Status</th>
                                    <th width="200px">Keterangan</th>
                                    <td>Total</td>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($data as $value)
                                <tr>
                                    <td>{{formatDate($value->tanggal)}}</td>
                                    <td><a @if($value->tipe == 'DEBIT') href="{{URL::to('akuntansi/cetak_bukti_kas_keluar').'/'.$value->id_trx_akun_detail}}" @else href="{{URL::to('akuntansi/cetak_bukti_kas_keluar').'/'.$value->id_trx_akun_detail}}" @endif target="_blank">{{$value->no}}</a></td>
                                    <td>{{$value->no_akun}}</td>
                                    <td>{{$value->nama_akun}}</td>
                                    <td>{{$value->tipe == 'DEBIT' ? 'Masuk' : 'Keluar'}}</td>
                                    <td>{{$value->deskripsi}}</td>
                                    <td class="text-right">{{formatRupiah($value->jumlah)}}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
                
</div>
<script src="{!! asset('theme/assets/libs/jquery/dist/jquery.min.js') !!}"></script>
<script src="{!! asset('theme/assets/extra-libs/DataTables/datatables.min.js') !!}"></script>
<script type="text/javascript">
    var bulan='{{$date}}';
    $('#jurnal_list').DataTable({
        'aaSorting' : [0, 'DESC'],
        "lengthMenu": [[10, 25, 50, 100, 1000, -1], [10, 25, 50, 100, 1000, "All"]]
    });
</script>
@endsection