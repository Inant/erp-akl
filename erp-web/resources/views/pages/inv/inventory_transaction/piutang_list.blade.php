@extends('theme.default')

@section('breadcrumb')
			<div class="page-breadcrumb">
                <div class="row">
                    <div class="col-5 align-self-center">
                        <h4 class="page-title">Rincian Buku Besar Pembantu Piutang</h4>
                        <div class="d-flex align-items-center">
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item active" aria-current="page">Tagihan</li>
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
                    <h4 class="card-title">Daftar Rincian Buku Besar Pembantu Piutang</h4>
                    <!-- <div class="row"> -->
                        <form method="POST" action="{{ URL::to('inventory/piutang_list') }}">
                            @csrf
                            <div class="row">
                                <div class="col-6">
                                    <h5 class="card-title">Pilih Customer</h5>
                                    <div class="form-group">
                                        <select class="form-control select2 custom-select" multiple="multiple" style="height: 36px; width: 100%;" id="customer_id" name="customer_id[]">
                                            <option value="all" {{$all_customer == true ? 'selected' : ''}}>Semua</option>
                                            @foreach($customer as $value)
                                                <?php $same=false; ?>
                                                @if($customer_selected != null)
                                                    @foreach($customer_selected as $v)
                                                        @if($v == $value['id'])
                                                            @php $same=true @endphp
                                                        @endif
                                                    @endforeach
                                                @endif
                                                <option value="{{$value['id']}}" {{$same == true ? 'selected' : ''}}>{{$value['coorporate_name']}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="form-group">
                                        <label for="">Tanggal Awal :</label>
                                        <input type="date" name="date" class="form-control" required value="{{$date1}}">
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="form-group">
                                        <label >Tanggal Ahir :</label>
                                        <input type="date" name="date2" class="form-control" required value="{{$date2}}">
                                    </div>
                                </div>
                                <div class="col-12 form-group">
                                    <button class="btn btn-primary" name="submit" value="1"><i class="fa fa-search"></i> Cari</button>
                                </div>
                            </div>
                        </form>
                    <!-- </div> -->
                    <br><br>
                    @if($data_cust != null)
                    <form method="POST" action="{{ URL::to('inventory/export_piutang_list') }}" class="float-right" target="_blank">
                        @csrf
                        <div hidden>
                            <select class="form-control select2 custom-select" multiple="multiple" style="height: 36px; width: 100%;" id="customer_id1" name="customer_id[]">
                                <option value="all" {{$all_customer == true ? 'selected' : ''}}>Semua</option>
                                @foreach($customer as $value)
                                    <?php $same=false; ?>
                                    @if($customer_selected != null)
                                        @foreach($customer_selected as $v)
                                            @if($v == $value['id'])
                                                @php $same=true @endphp
                                            @endif
                                        @endforeach
                                    @endif
                                    <option value="{{$value['id']}}" {{$same == true ? 'selected' : ''}}>{{$value['coorporate_name']}}</option>
                                @endforeach
                            </select>
                        </div>
                        <input type="date" name="date" hidden class="form-control"  required value="{{$date1}}">
                        <input type="date" name="date2" hidden class="form-control"  required value="{{$date2}}">
                        <div class="form-group">
                            <button class="btn btn-success"><i class="fa fa-file-excel"></i> Export</button>
                        </div>
                    </form>
                    @endif
                    @if($data_cust != null)
                    <div class="table-responsive">
                        <!-- <table id="" class="table table-striped table-bordered"> -->
                        <table style="border-collapse:collapse; width:100%">
                            <thead>
                                <tr id="table" class="text-primary">
                                    <th class="text-center">Tanggal</th>
                                    <th class="text-center">Sumber</th>
                                    <th class="text-center">No Faktur</th>
                                    <th class="text-center">Keterangan</th>
                                    <th class="text-center">Debit</th>
                                    <th class="text-center">Kredit</th>
                                    <th class="text-center">Saldo (asing)</th>
                                </tr>
                            </thead>
                            <tbody>
                            @foreach($data_cust as $value)
                                <?php 
                                $total_saldo=$total_penambahan=$total_penurunan=0;
                                $saldo_awal=$value['perubahan_saldo']->total_kredit - $value['perubahan_saldo']->total_debit;
                                ?>
                                @if($value['data'] != null || $saldo_awal != 0)
                                <tr id="table">
                                    <td></td>
                                    <td class="text-center">{{$value['customer']->coorporate_name}}</td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td class="text-right">{{formatRupiah($value['perubahan_saldo']->total_debit - $value['perubahan_saldo']->total_kredit)}}</td>
                                </tr>
                                <?php 
                                $total_saldo=$value['perubahan_saldo']->total_debit - $value['perubahan_saldo']->total_kredit; 
                                // $total_penambahan+=$total_saldo;
                                ?>
                                @foreach($value['data'] as $v)
                                    @foreach($v['dt'] as $v1)
                                    <?php 
                                    $total_saldo=($v1->tipe == 'DEBIT' ? ($total_saldo + $v1->jumlah) : ($total_saldo - $v1->jumlah));
                                    if ($v1->tipe == 'DEBIT') {
                                        $total_penambahan+=$v1->jumlah;
                                    }else{
                                        $total_penurunan+=$v1->jumlah;
                                    }
                                    ?>
                                    <tr id="table">
                                        <td class="text-center">{{date('d-m-Y', strtotime($v['date']))}}</td>
                                        <td>{{$v1->no_source != null ? $v1->no_source : ''}}</td>
                                        <td class="text-center">{{$v1->bill_no != null ? $v1->bill_no : ($v1->paid_cust_no != null ? $v1->paid_cust_no : '')}}</td>
                                        <td style="width:300px">{{$v1->deskripsi}}</td>
                                        <td class="text-right"> {{$v1->tipe == 'DEBIT' ? formatRupiah($v1->jumlah) : ''}}</td>
                                        <td class="text-right"> @if($v1->tipe == 'KREDIT') <a href="{{URL::to('akuntansi/cetak_bukti_kas_masuk').'/'.$v1->id_source}}" target="_blank" >{{formatRupiah($v1->jumlah)}}</a> @else 0 @endif</td>
                                        <td class="text-right">{{formatRupiah($total_saldo)}}</td>
                                    </tr>
                                    @endforeach
                                @endforeach
                                <tr id="table" class="text-primary">
                                    <td class="text-center" colspan="4">Sub Total</td>
                                    <td class="text-right">{{formatRupiah($total_penambahan)}}</td>
                                    <td class="text-right">{{formatRupiah($total_penurunan)}}</td>
                                    <td class="text-right">{{formatRupiah($total_saldo)}}</td>
                                </tr>
                                @endif
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>                
</div>
<script src="{!! asset('theme/assets/libs/jquery/dist/jquery.min.js') !!}"></script>
<script src="{!! asset('theme/assets/extra-libs/DataTables/datatables.min.js') !!}"></script>


@endsection