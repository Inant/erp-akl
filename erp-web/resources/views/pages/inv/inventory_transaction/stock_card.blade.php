@extends('theme.default')

@section('breadcrumb')
			<div class="page-breadcrumb">
                <div class="row">
                    <div class="col-5 align-self-center">
                        <h4 class="page-title">Kartu Persediaan</h4>
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
                    <h4 class="card-title">Kartu Persediaan</h4>
                    <!-- <div class="row"> -->
                        <form method="POST" action="{{ URL::to('inventory/stock_card') }}">
                            @csrf
                            <div class="row">
                                <div class="col-6">
                                    <h5 class="card-title">Pilih Material/Spare Part</h5>
                                    <div class="form-group">
                                        <select class="form-control select2 custom-select" style="height: 36px; width: 100%;" id="item" name="item" required>
                                            <option value="">Pilih Item</option>
                                            @foreach($items as $value)
                                                <option value="{{$value->id}}" {{$value->id == $item_selected ? 'selected' : ''}}>({{$value->no}}) {{$value->name}}</option>
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
                    @if($data != null)
                    <form method="POST" action="{{ URL::to('inventory/export_piutang_list') }}" class="float-right" target="_blank">
                        @csrf
                        <div hidden>
                            <select class="form-control" style="height: 36px; width: 100%;" id="item" name="item">
                                @foreach($items as $value)
                                    <option value="{{$value->id}}" {{$value->id == $item_selected ? 'selected' : ''}}>{{$value->no}} | {{$value->name}}</option>
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
                    @if($data != null)
                    <div class="table-responsive">
                        <!-- <table id="" class="table table-striped table-bordered"> -->
                        <table style="border-collapse:collapse; width:100%">
                            <thead>
                                <tr id="table" class="text-primary">
                                    <th class="text-center" rowspan="2">Tanggal</th>
                                    <th class="text-center" colspan="3">Pembelian</th>
                                    <th class="text-center" colspan="3">Harga Pokok Penjualan</th>
                                    <th class="text-center" colspan="3">Persediaan</th>
                                </tr>
                                <tr id="table" class="text-primary">
                                    <th class="text-center">Qty</th>
                                    <th class="text-center">Biaya/Unit</th>
                                    <th class="text-center">Jumlah Biaya</th>
                                    <th class="text-center">Qty</th>
                                    <th class="text-center">Biaya/Unit</th>
                                    <th class="text-center">Jumlah Biaya</th>
                                    <th class="text-center">Qty</th>
                                    <th class="text-center">Biaya/Unit</th>
                                    <th class="text-center">Jumlah Biaya</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr id="table">
                                    <td class="text-center">{{formatDate($data['first_date'])}}</td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td class="text-right">{{formatRupiah($data['stock_first'])}}</td>
                                    <td class="text-right">{{formatRupiah($data['price_first'])}}</td>
                                    <td class="text-right">{{formatRupiah($data['stock_first'] * $data['price_first'])}}</td>
                                </tr>
                            @php 
                                $stok=$data['stock_first'];
                                $price=$data['price_first'];
                                $value_stok=$data['stock_first'] * $data['price_first'];
                            @endphp
                            @foreach($data['stock_range'] as $value)
                                <?php
                                if($value->trx_type == 'RECEIPT'){
                                    $total_all=$value_stok + ($value->amount * $value->base_price);
                                    $stok=($value->amount + $stok);
                                    $price=$total_all / $stok;
                                    $value_stok=($stok * $price);
                                }else{
                                    $total_all=$value_stok - ($value->amount * $price);
                                    $stok=($stok - $value->amount);
                                    $value_stok=($stok * $price);
                                }
                                ?>
                                <tr id="table">
                                    <td class="text-center">{{formatDate($value->inv_trx_date)}}</td>
                                    @if($value->trx_type == 'RECEIPT')
                                    <td class="text-right">{{formatRupiah($value->amount)}}</td>
                                    <td class="text-right">{{formatRupiah($value->base_price)}}</td>
                                    <td class="text-right">{{formatRupiah($value->amount * $value->base_price)}}</td>
                                    <td class="text-right">-</td>
                                    <td class="text-right">-</td>
                                    <td class="text-right">-</td>
                                    @else
                                    <td class="text-right">-</td>
                                    <td class="text-right">-</td>
                                    <td class="text-right">-</td>
                                    <td class="text-right">{{formatRupiah($value->amount)}}</td>
                                    <td class="text-right">{{formatRupiah($price)}}</td>
                                    <td class="text-right">{{formatRupiah($value->amount * $price)}}</td>
                                    @endif
                                    <td class="text-right">{{formatRupiah($stok)}}</td>
                                    <td class="text-right">{{formatRupiah($price)}}</td>
                                    <td class="text-right">{{formatRupiah($stok * $price)}}</td>
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


@endsection