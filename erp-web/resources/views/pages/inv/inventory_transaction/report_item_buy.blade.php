@extends('theme.default')

@section('breadcrumb')
			<div class="page-breadcrumb">
                <div class="row">
                    <div class="col-5 align-self-center">
                        <h4 class="page-title">Rincian Pembelian per Barang</h4>
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
                    <h4 class="card-title">Rincian Pembelian per Barang</h4>
                    <!-- <div class="row"> -->
                        <form method="POST" action="{{ URL::to('inventory/report_item_buy') }}">
                            @csrf
                            <div class="row">
                                <div class="col-6">
                                    <h5 class="card-title">Pilih Supplier</h5>
                                    <div class="form-group">
                                        <select class="select2 form-control" multiple="multiple" style="height: 36px; width: 100%;" id="suppl_single" name="suppl_single[]">
                                            <option value="all" {{$all_supplier == true ? 'selected' : ''}}>Semua</option>
                                            @foreach($suppliers as $value)
                                                <?php $same=false; ?>
                                                @foreach($supplier_selected as $v)
                                                    @if($v == $value['id'])
                                                        @php $same=true @endphp
                                                    @endif
                                                @endforeach
                                            <option value="{{$value['id']}}" {{$same == true ? 'selected' : ''}}>{{$value['name']}}</option>
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
                    <form method="POST" action="{{ URL::to('inventory/purchase') }}" class="form-inline" id="form-bill" target="_blank">
                        @csrf
                        <input type="text" hidden name="suppl_single[]" id="suppl_single2[]" class="form-control">
                        <input type="date" name="date" class="form-control" hidden required value="{{$date1}}">
                        <input type="date" name="date2" class="form-control" hidden required value="{{$date2}}">
                        <input name="submitBtn" value="1" hidden>
                        <!-- <button name="submit" value="1" class="btn btn-primary" id="submit">Laporan Penjualan</button> -->
                    </form>
                    @if($data != null)
                    <form method="POST" action="{{ URL::to('inventory/export_item_buy') }}" class="float-right" target="_blank">
                        @csrf
                        <div hidden>
                            <select class="select2 form-control" multiple="multiple" style="height: 36px; width: 100%;" id="suppl_single1" name="suppl_single[]">
                                <option value="all" {{$all_supplier == true ? 'selected' : ''}}>Semua</option>
                                @foreach($suppliers as $value)
                                    <?php $same=false; ?>
                                    @foreach($supplier_selected as $v)
                                        @if($v == $value['id'])
                                            @php $same=true @endphp
                                        @endif
                                    @endforeach
                                <option value="{{$value['id']}}" {{$same == true ? 'selected' : ''}}>{{$value['name']}}</option>
                                @endforeach
                            </select>
                        </div>
                        <input type="date" name="date" hidden class="form-control"  required value="{{$date1}}">
                        <input type="date" name="date2" hidden class="form-control"  required value="{{$date2}}">
                        <div class="form-group">
                            <button class="btn btn-success"><i class="fa fa-file-excel"></i> Export</button>
                        </div>
                    </form>
                    <div class="table-responsive">
                        <table style="border-collapse:collapse; width:100%">
                            <thead>
                                <tr id="table" class="text-primary">
                                    <th class="text-center">Tanggal Faktur</th>
                                    <th class="text-center">No Faktur</th>
                                    <th class="text-center">No Barang</th>
                                    <th class="text-center">Nama Barang</th>
                                    <th class="text-center">Keterangan</th>
                                    <th class="text-center">Kuantitas</th>
                                    <th class="text-center">Unit</th>
                                    <th class="text-center">Harga</th>
                                    <th class="text-center">Jumlah</th>
                                    <th class="text-center">PPN</th>
                                    <th class="text-center">Sub Total</th>
                                    <th class="text-center">Nama Supplier</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php $total_saldo_all=$total_item=$total_value=$total_ppn=0 ?>
                            @foreach($data as $value)
                                <?php
                                $total=($value->amount * $value->base_price);
                                $with_ppn=($value->p_without_ppn != null ? $value->p_without_ppn : ($value->pa_without_ppn != null ? $value->pa_without_ppn : false));
                                $ppn=$with_ppn == false ? $total * 0.1 : 0;
                                $total_item+=$value->amount;
                                $total_value+=$total;
                                $total_ppn+=$ppn;
                                $total_saldo_all+=($total + $ppn);
                                ?>
                                <tr id="table">
                                    <td class="text-center">{{formatDate($value->inv_trx_date)}}</td>
                                    <td class="text-center">{{$value->no_surat_jalan}}</td>
                                    <td class="text-center">{{$value->item_no}}</td>
                                    <td class="text-center">{{$value->item_name}}</td>
                                    <td class="text-center">{{($value->p_notes != null ? $value->p_notes : ($value->pa_notes != null ? $value->pa_notes : ''))}}</td>
                                    <td class="text-right">{{$value->amount}}</td>
                                    <td class="text-center">{{$value->unit_name}}</td>
                                    <td class="text-right">{{formatRupiah($value->base_price)}}</td>
                                    <td class="text-right">{{formatRupiah($value->amount * $value->base_price)}}</td>
                                    <td class="text-right">{{formatRupiah($ppn)}}</td>
                                    <td class="text-right">{{formatRupiah($total + $ppn)}}</td>
                                    <td class="text-center">{{($value->supplier1 != null ? $value->supplier1 : ($value->supplier2 != null ? $value->supplier2 : ''))}}</td>
                                </tr>
                            @endforeach
                                <tr id="table" class="text-primary">
                                    <td colspan="5" class="text-center">Total</td>
                                    <td class="text-right">{{formatRupiah($total_item)}}</td>
                                    <td></td>
                                    <td></td>
                                    <td class="text-right">{{formatRupiah($total_value)}}</td>
                                    <td class="text-right">{{formatRupiah($total_ppn)}}</td>
                                    <td class="text-right">{{formatRupiah($total_saldo_all)}}</td>
                                    <td></td>
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
</script>


@endsection