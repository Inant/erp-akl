@extends('theme.default')

@section('breadcrumb')
			<div class="page-breadcrumb">
                <div class="row">
                    <div class="col-5 align-self-center">
                        <h4 class="page-title">Laporan Piutang dan Pembayaran Customer</h4>
                        <div class="d-flex align-items-center">
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item active" aria-current="page">Home</li>
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
                    <h4 class="card-title">Laporan Piutang dan Pembayaran Customer</h4>
                    <!-- <div class="row"> -->
                        <form method="POST" action="{{ URL::to('inventory/piutang_all2') }}">
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
                    <form method="POST" action="{{ URL::to('inventory/export_piutang_all2') }}" class="float-right" target="_blank">
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
                    <form method="POST" action="{{ URL::to('akuntansi/temp_profit_loss') }}" class="form-inline" id="form-temp" target="_blank">
                        @csrf
                        <input type="text" hidden name="order_id" class="form-control" id="order_id">
                        <!-- <button name="submit" value="1" class="btn btn-primary" id="submit">Laporan Penjualan</button> -->
                    </form>
                    @endif
                    @if($data_cust != null)
                    <div class="table-responsive">
                        <!-- <table id="" class="table table-striped table-bordered"> -->
                        <table style="border-collapse:collapse; width:100%">
                            <thead>
                                <tr id="table" class="text-primary">
                                    <th></th>
                                    <th class="text-center">Tanggal</th>
                                    <th class="text-center">Customer</th>
                                    <th class="text-center">No Faktur</th>
                                    <th class="text-center">No Faktur /No Penerimaan</th>
                                    <th class="text-center" width="300px">Keterangan</th>
                                    <th class="text-center">No Tagihan</th>
                                    <th class="text-center">No Tagihan Dibayar</th>
                                    <th class="text-center">Debit</th>
                                    <th class="text-center">Kredit</th>
                                    <th class="text-center">Saldo(Asing)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($data_cust as $customer)
                                    @php
                                    $total_debit=$total_kredit=$n=0;
                                    $sub_total=$customer['saldoAwal']->total_debit - $customer['saldoAwal']->total_kredit;
                                    
                                    @endphp
                                    <tr id="table">
                                        <td></td>
                                        <td></td>
                                        <td>{{$customer['customer']->coorporate_name}}</td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td class="text-right"></td>
                                        <td class="text-right"></td>
                                        <td></td>
                                        <td></td>
                                        <td class="text-right">{{formatRupiah($sub_total)}}</td>
                                    </tr>
                                    
                                    @foreach($customer['data'] as $key => $value)
                                        @php 
                                        $sub_total=($value->tipe == 'DEBIT' ? ($sub_total+$value->jumlah) : ($sub_total-$value->jumlah));
                                        $total_debit+=($value->tipe == 'DEBIT' ? $value->jumlah : 0);
                                        $total_kredit+=($value->tipe == 'KREDIT' ? $value->jumlah : 0);
                                        $n++;
                                        
                                        // cek apakah terdapat kurang / lebih bayar
                                        $getKurangLebihBayar = \DB::table('plusminusbill')->where('id_trx_akun', $value->id_trx_akun);
                                        $cekKurangLebihBayar = $getKurangLebihBayar->count();
                                        if ($cekKurangLebihBayar > 0 ) {
                                            $nominalKurangLebih = $getKurangLebihBayar->select('nominal')->first()->nominal;
                                        }
                                        else{
                                            $nominalKurangLebih = 0;
                                        }

                                        //jika lebih maka total kredit nya nambah
                                        if ($nominalKurangLebih > 0) {
                                            $total_kredit += $nominalKurangLebih;
                                        }
                                        // jika kurang maka total debit nya nambah
                                        elseif($nominalKurangLebih < 0){
                                            $total_debit += ($nominalKurangLebih * -1);
                                        }

                                        @endphp

                                        @if($value->source != null && strpos($value->deskripsi, ','))
                                            @php
                                                $billNo = explode(',', $value->deskripsi);
                                                $billNo[0] = explode('No Tagihan', $billNo[0])[1];
                                                $billNo = array_map('trim', $billNo);
                                                
                                                $detailBill = \DB::table('customer_bills')
                                                                    ->join('tbl_trx_akuntansi', 'tbl_trx_akuntansi.customer_bill_id', '=', 'customer_bills.id')
                                                                    ->join('tbl_trx_akuntansi_detail', 'tbl_trx_akuntansi_detail.id_trx_akun', '=', 'tbl_trx_akuntansi.id_trx_akun')
                                                                    ->select('customer_bills.id', 'customer_bills.no', 'tbl_trx_akuntansi_detail.jumlah')
                                                                    ->whereIn('customer_bills.no', $billNo)
                                                                    ->where('tbl_trx_akuntansi_detail.id_akun', 151)
                                                                    ->get()
                                                                    ->toArray();
                                            @endphp
                                        @endif
                                        <!-- kondisi split sby bill -->
                                        @if(isset($detailBill) && count($detailBill) > 0 && $value->source != null && strpos($value->deskripsi, ','))
                                            <tr id="table">
                                                <td rowspan="{{$cekKurangLebihBayar > 0 ? count($detailBill) + 1 : count($detailBill)}}"> {!!$n!!}</td>
                                                <td rowspan="{{$cekKurangLebihBayar > 0 ? count($detailBill) + 1 : count($detailBill)}}">{!!formatDate($value->tanggal)!!}</td>
                                                <td rowspan="{{$cekKurangLebihBayar > 0 ? count($detailBill) + 1 : count($detailBill)}}">{!!$value->coorporate_name!!}</td>
                                                <td rowspan="{{$cekKurangLebihBayar > 0 ? count($detailBill) + 1 : count($detailBill)}}">{!!$value->customer_bill_no != null ? $value->customer_bill_no : ($value->order_no != null ? $value->order_no : ($value->install_order_no != null ? $value->install_order_no : '-'))!!}</td>
                                                <td rowspan="{{$cekKurangLebihBayar > 0 ? count($detailBill) + 1 : count($detailBill)}}">
                                                @if($value->source != null){{$value->source}}@else - 
                                                @endif
                                                @if($value->order_id != null)
                                                @foreach($value->order_id as $v)
                                                    @if($v != null)
                                                    <button onclick="toTempProfitLoss('{{$v->id}}')" class="btn btn-sm btn-primary" style="margin-top:5px"  data-toggle="tooltip" data-placement="bottom" title="" data-original-title="Lihat Laba Rugi Sementara SPK Order : {{$v->spk_number}}"><i class="fa fa-eye"></i></button>
                                                    @endif
                                                @endforeach
                                                @endif
                                                </td>
                                                <td>Pembayaran Customer dari No Tagihan {{$detailBill[0]->no}}</td>
                                                <td>-</td>
                                                <td>{{$detailBill[0]->no}}</td>
                                                <td class="text-right">-</td>
                                                <td class="text-right">{{number_format($detailBill[0]->jumlah, 0, ',', '.')}}</td>
                                                <td class="text-right" rowspan="{{$cekKurangLebihBayar > 0 ? count($detailBill) + 1 : count($detailBill)}}">{{formatRupiah($sub_total)}}</td>
                                                @php
                                                    $totalBill = 0;
                                                    $nomorBill = '';

                                                    $totalBill += $detailBill[0]->jumlah;
                                                    $nomorBill .= $detailBill[0]->no . ', ';
                                                    array_shift($detailBill)
                                                @endphp
                                                @foreach ($detailBill as $item)
                                                <tr id="table">
                                                    <td>Pembayaran Customer dari No Tagihan {{$item->no}}</td>
                                                    <td>-</td>
                                                    <td>{{$item->no}}</td>
                                                    <td class="text-right">-</td>
                                                    <td class="text-right">{{number_format($item->jumlah, 0, ',', '.')}}</td>
                                                </tr>
                                                @endforeach
                                                @if ($cekKurangLebihBayar > 0 && $nominalKurangLebih > 0)
                                                <tr id="table">
                                                    <td>Lebih Bayar {{$value->source}}</td>
                                                    <td>{{$value->no}}</td>
                                                    @php
                                                        $noTagDibayar = '-';
                                                        if ($value->source != null && $value->tipe == 'KREDIT' && strpos($value->deskripsi, 'No Tagihan')) {
                                                            $noTagDibayar = explode('No Tagihan', $value->deskripsi)[1];
                                                        }
                                                    @endphp
                                                <td>{{$noTagDibayar}}</td>
                                                    <td class="text-right">-</td>
                                                    <td class="text-right">{{number_format($nominalKurangLebih, 0, ',', '.')}}</td>
                                                </tr>
                                                @elseif($cekKurangLebihBayar > 0 && $nominalKurangLebih < 0)
                                                <tr id="table">
                                                    <td>Kurang Bayar {{$value->source}}</td>
                                                    <td>{{$value->no}}</td>
                                                    @php
                                                        $noTagDibayar = '-';
                                                        if ($value->source != null && $value->tipe == 'KREDIT' && strpos($value->deskripsi, 'No Tagihan')) {
                                                            $noTagDibayar = explode('No Tagihan', $value->deskripsi)[1];
                                                        }
                                                    @endphp
                                                    <td>{{$noTagDibayar}}</td>
                                                    <td class="text-right">{{number_format($nominalKurangLebih, 0, ',', '.')}}</td>
                                                    <td class="text-right">-</td>
                                                </tr>
                                                @endif
                                            </tr>    
                                        @else
                                            <!--jika terdapat kurang lebih bayar-->
                                            @if ($cekKurangLebihBayar > 0)
                                            <tr id="table">
                                                <td rowspan="2"> {!!$n!!}</td>
                                                <td rowspan="2">{!!formatDate($value->tanggal)!!}</td>
                                                <td rowspan="2">{!!$value->coorporate_name!!}</td>
                                                <td rowspan="2">{!!$value->customer_bill_no != null ? $value->customer_bill_no : ($value->order_no != null ? $value->order_no : ($value->install_order_no != null ? $value->install_order_no : '-'))!!}</td>
                                                <td rowspan="2">
                                                @if($value->source != null){{$value->source}}@else - 
                                                @endif
                                                @if($value->order_id != null)
                                                @foreach($value->order_id as $v)
                                                    @if($v != null)
                                                    <button onclick="toTempProfitLoss('{{$v->id}}')" class="btn btn-sm btn-primary" style="margin-top:5px"  data-toggle="tooltip" data-placement="bottom" title="" data-original-title="Lihat Laba Rugi Sementara SPK Order : {{$v->spk_number}}"><i class="fa fa-eye"></i></button>
                                                    @endif
                                                @endforeach
                                                @endif
                                                </td>
                                                <td>{!!$value->deskripsi!!}</td>
                                                <td>{{$value->no}}</td>
                                                @php
                                                    $noTagDibayar = '-';
                                                    if ($value->source != null && $value->tipe == 'KREDIT' && strpos($value->deskripsi, 'No Tagihan')) {
                                                        $noTagDibayar = explode('No Tagihan', $value->deskripsi)[1];
                                                    }
                                                @endphp
                                                <td>{{$noTagDibayar}}</td>
                                                <td class="text-right">{{$value->tipe == 'DEBIT' ? formatRupiah($value->jumlah) : '-'}}</td>
                                                <td class="text-right">{{$value->tipe == 'KREDIT' ? formatRupiah($value->jumlah) : '-'}}</td>
                                                <td rowspan="2" class="text-right">{{formatRupiah($sub_total)}}</td>
                                                <!-- jika terdapat kurang lebih bayar -->
                                                @if ($cekKurangLebihBayar > 0 && $nominalKurangLebih > 0)
                                                <tr id="table">
                                                    <td>Lebih Bayar {{$value->source}}</td>
                                                    <td>{{$value->no}}</td>
                                                    @php
                                                        $noTagDibayar = '-';
                                                        if ($value->source != null && $value->tipe == 'KREDIT' && strpos($value->deskripsi, 'No Tagihan')) {
                                                            $noTagDibayar = explode('No Tagihan', $value->deskripsi)[1];
                                                        }
                                                    @endphp
                                                <td>{{$noTagDibayar}}</td>
                                                    <td class="text-right">-</td>
                                                    <td class="text-right">{{number_format($nominalKurangLebih, 0, ',', '.')}}</td>
                                                </tr>
                                                @elseif($cekKurangLebihBayar > 0 && $nominalKurangLebih < 0)
                                                <tr id="table">
                                                    <td>Kurang Bayar {{$value->source}}</td>
                                                    <td>{{$value->no}}</td>
                                                    @php
                                                        $noTagDibayar = '-';
                                                        if ($value->source != null && $value->tipe == 'KREDIT' && strpos($value->deskripsi, 'No Tagihan')) {
                                                            $noTagDibayar = explode('No Tagihan', $value->deskripsi)[1];
                                                        }
                                                    @endphp
                                                    <td>{{$noTagDibayar}}</td>
                                                    <td class="text-right">{{number_format($nominalKurangLebih * -1, 0, ',', '.')}}</td>
                                                    <td class="text-right">-</td>
                                                </tr>
                                                @endif
                                            </tr>
                                            <!-- jika tidak terdapat kurang lebih bayar -->
                                            @else
                                            <tr id="table">
                                                <td> {!!$n!!}</td>
                                                <td>{!!formatDate($value->tanggal)!!}</td>
                                                <td>{!!$value->coorporate_name!!}</td>
                                                <td>{!!$value->customer_bill_no != null ? $value->customer_bill_no : ($value->order_no != null ? $value->order_no : ($value->install_order_no != null ? $value->install_order_no : '-'))!!}</td>
                                                <td>
                                                @if($value->source != null){{$value->source}}@else - 
                                                @endif
                                                @if($value->order_id != null)
                                                @foreach($value->order_id as $v)
                                                    @if($v != null)
                                                    <button onclick="toTempProfitLoss('{{$v->id}}')" class="btn btn-sm btn-primary" style="margin-top:5px"  data-toggle="tooltip" data-placement="bottom" title="" data-original-title="Lihat Laba Rugi Sementara SPK Order : {{$v->spk_number}}"><i class="fa fa-eye"></i></button>
                                                    @endif
                                                @endforeach
                                                @endif
                                                </td>
                                                <td>{!!$value->deskripsi!!}</td>
                                                <td>{{$value->no}}</td>
                                                @php
                                                    $noTagDibayar = '-';
                                                    if ($value->source != null && $value->tipe == 'KREDIT' && strpos($value->deskripsi, 'No Tagihan')) {
                                                        $noTagDibayar = explode('No Tagihan', $value->deskripsi)[1];
                                                    }
                                                @endphp
                                                <td>{{$noTagDibayar}}</td>
                                                <td class="text-right">{{$value->tipe == 'DEBIT' ? formatRupiah($value->jumlah) : '-'}}</td>
                                                <td class="text-right">{{$value->tipe == 'KREDIT' ? formatRupiah($value->jumlah) : '-'}}</td>
                                                <td class="text-right">{{formatRupiah($sub_total)}}</td>
                                                
                                            </tr>
                                            @endif
                                        @endif
                                        
                                    @endforeach
                                    <tr id="table" class="table-info">
                                        <td colspan="8" class="text-center">Total</td>
                                        <td class="text-right">{{formatRupiah($total_debit)}}</td>
                                        <td class="text-right">{{formatRupiah($total_kredit)}}</td>
                                        <td class="text-right">{{formatRupiah($sub_total)}}</td>
                                    </tr>
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
<script>
function toTempProfitLoss(id){
    $('#order_id').val(id);
    var form = document.getElementById("form-temp");
    form.submit();
}
</script>

@endsection