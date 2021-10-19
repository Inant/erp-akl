@extends('theme.default')

@section('breadcrumb')
			<div class="page-breadcrumb">
                <div class="row">
                    <div class="col-5 align-self-center">
                        <h4 class="page-title">Laporan Penjualan</h4>
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
                    <h4 class="card-title">Laporan Penjualan</h4>
                    <!-- <div class="row"> -->
                        <form method="POST" action="{{ URL::to('inventory/sell_customer') }}">
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
                    <form method="POST" action="{{ URL::to('inventory/export_sell_customer') }}" class="float-right" target="_blank">
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
                    <form method="POST" action="{{ URL::to('inventory/piutang_all') }}" class="form-inline" id="form-piutang" target="_blank">
                        @csrf
                        <input type="text" hidden name="customer_id[]" id="customer_id1[]" class="form-control">
                        <input type="date" name="date" class="form-control" hidden required value="{{$date1}}">
                        <input type="date" name="date2" class="form-control" hidden required value="{{date('Y-m-d')}}">
                        <input name="submitBtn" value="1" hidden>
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
                                    <th class="text-center">Sumber</th>
                                    
                                    <th class="text-center">No Faktur</th>
                                    <th class="text-center">No Invoice</th>
                                    <th class="text-center" width="300px">Keterangan</th>
                                    <th class="text-center">No SPK</th>
                                    <th class="text-center">No SPK Ins</th>
                                    <th class="text-center">DPP</th>
                                    <th class="text-center">PPN</th>
                                    <th class="text-center">PPH</th>
                                    <th class="text-center">Nilai</th>
                                </tr>
                            </thead>
                            <tbody>
                            @php $sub_total=$index=$dpp=$ppn=$pph=0 @endphp
                            @foreach($data_cust as $value)
                                @php 
                                $sub_total+=$value->jumlah; 
                                $index++; 
                                $dpp+=($value->dpp != null ? $value->dpp->jumlah : 0);
                                $ppn+=($value->ppn != null ? $value->ppn->jumlah : 0);
                                $pph+=($value->pph != null ? $value->pph->jumlah : 0);
                                @endphp
                                <tr id="table">
                                    <td>{{$index}}</td>
                                    <td>{{formatDate($value->tanggal)}}</td>
                                    <td><a href="#" data-id="{{$value->customer_id}}" onclick="getReport(this)" data-toggle="tooltip" data-placement="bottom" title="" data-original-title="Lihat Laporan Piutang dan Pembayaran Customer">{{$value->coorporate_name}}</a></td>
                                    <td>{{ $value->bill_no != null ? $value->bill_no : '-'}}</td>
                                    <td>{{$value->invoice_no}}</td> <!-- disini editnya -->
                                    <td>{{$value->deskripsi}}</td>
                                    <td>{{$value->spk_number != null ? $value->spk_number : '-'}}</td>
                                     <td>{{$value->spk_number_ins != null ? $value->spk_number_ins : '-'}}</td>
                                    <td class="text-right">{{$value->dpp != null ? formatRupiah($value->dpp->jumlah) : 0}}</td>
                                    <td class="text-right">{{$value->ppn != null ? formatRupiah(($value->ppn->jumlah)) : 0}}</td>
                                    <td class="text-right">{{$value->pph != null ? formatRupiah(($value->pph->jumlah)) : 0}}</td>
                                    <td class="text-right">{{formatRupiah($value->jumlah)}}</td>
                                </tr>
                            @endforeach
                                <tr id="table" class="table-info">
                                    <td colspan="8" class="text-center">Sub Total</td>
                                    <td class="text-right">{{formatRupiah($dpp)}}</td>
                                    <td class="text-right">{{formatRupiah($ppn)}}</td>
                                    <td class="text-right">{{formatRupiah($pph)}}</td>
                                    <td class="text-right">{{formatRupiah($sub_total)}}</td>
                                </tr>
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
function getReport(eq){
    id=$(eq).data('id')
    $('[id^=customer_id1]').val(id);
    $('#form-piutang').submit();
}
</script>

@endsection