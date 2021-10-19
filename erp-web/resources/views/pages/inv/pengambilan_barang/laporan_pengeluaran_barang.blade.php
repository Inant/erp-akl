@extends('theme.default')

@section('breadcrumb')
    <div class="page-breadcrumb">
        <div class="row">
            <div class="col-5 align-self-center">
                <h4 class="page-title">Laporan Pengeluaran Material</h4>
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
    function formatRupiah($num)
    {
        return number_format($num, 0, '.', '.');
    }
    function formatDate($date)
    {
        $date = date_create($date);
        return date_format($date, 'd-m-Y');
    }
    @endphp
    <style>
        .floatLeft {
            width: 100%;
        }

        .floatRight {
            width: 100%;
        }

        /* .floatLeft { width: 100%; float: left; }
              .floatRight {width: 100%; float: right; } */
        #table th,
        #table td {
            border: 1px solid #7c8186;
            padding: 5px
        }

        .no-border {
            border: 1px solid white !important;
            /* padding : 5px */
        }

    </style>
    <div class="container-fluid">
        <!-- basic table -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Laporan Pengeluaran Material</h4>
                        <!-- <div class="row"> -->
                        <form method="POST" action="{{ URL::to('pengeluaran_barang/laporan-pengeluaran-material') }}">
                            @csrf
                            <div class="row">
                                <div class="col-6">
                                    <div class="form-group">
                                        <label for="">Tanggal Awal :</label>
                                        <input type="date" name="date" class="form-control" required
                                            value="{{ $date }}">
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="form-group">
                                        <label>Tanggal Ahir :</label>
                                        <input type="date" name="date2" class="form-control" required
                                            value="{{ $date2 }}">
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="form-group">
                                        <label>No SPK</label>
                                        <select class="form-control select2 custom-select"
                                            style="height: 36px; width: 100%;" id="no_spk" name="no_spk">
                                            <option value="">Semua</option>
                                            @foreach ($allSpkNumber as $value)
                                                <option value="{{ $value->spk_number }}"
                                                    {{ $no_spk == $value->spk_number ? 'selected' : '' }}>
                                                    {{ $value->spk_number }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="form-group">
                                        <label>Pilih Gudang</label>
                                        <select class="form-control select2 custom-select"
                                            style="height: 36px; width: 100%;" id="warehouse_id" name="warehouse_id">
                                            <option value="all">Semua</option>
                                            @foreach ($warehouse as $value)
                                                <option value="{{ $value->id }}"
                                                    {{ $warehouse_id == $value->id ? 'selected' : '' }}>
                                                    {{ $value->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-12 form-group">
                                    <button class="btn btn-primary" name="submit" value="1"><i class="fa fa-search"></i>
                                        Cari</button>
                                </div>
                            </div>
                        </form>
                        <!-- </div> -->
                        <br><br>
                        @if (Request::get('date') != null && Request::get('date2') != null)
                            
                            <a href="{{ url('pengeluaran_barang/export-laporan-pengeluaran-material')}}?date={{$date}}&date2={{$date2}}&no_spk={{$no_spk}}&warehouse_id={{$warehouse_id}}" class="float-right mb-3"  target="_blank">
                                <button class="btn btn-success"><i class="fa fa-file-excel"></i> Export</button>
                            </a>
                            <div class="table-responsive">
                                <!-- <table id="" class="table table-striped table-bordered"> -->
                                <table style="border-collapse:collapse; width:100%" id="table">
                                    <thead>
                                        <tr id="table" class="text-primary">
                                            <th class="text-center">No</th>
                                            <th class="text-center">Tanggal</th>
                                            {{-- <th class="text-center">No Surat Jalan</th> --}}
                                            <th class="text-center">No SPK</th>
                                            <th class="text-center">No Material</th>
                                            <th class="text-center">Nama Material</th>
                                            <th class="text-center">QTY</th>
                                            @if (auth()->user()['role_id'] == 1)
                                                <th class="text-center">Harga</th>
                                                <th class="text-center">Jumlah</th>
                                            @endif
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $totalQty = 0;
                                            $totalHarga = 0;
                                        @endphp
                                        @foreach ($laporan as $item)
                                            @php
                                                $totalQty += round($item->amount);
                                                $totalHarga += round($item->amount * $item->base_price);
                                            @endphp
                                            <tr>
                                                <td>{{$item->no}}</td>
                                                <td>{{formatDate($item->inv_trx_date)}}</td>
                                                {{-- <td>{{$item->no_surat_jalan}}</td> --}}
                                                <td>{{$item->spk_number}}</td>
                                                <td>{{$item->no_material}}</td>
                                                <td>{{$item->nama_material}}</td>
                                                <td>{{ $item->amount }}</td>
                                                @if (auth()->user()['role_id'] == 1)
                                                    <td>{{formatRupiah($item->base_price)}}</td>
                                                    <td>{{formatRupiah($item->amount * $item->base_price)}}</td>
                                                @endif
                                            </tr>
                                        @endforeach
                                        <tr id="table" class="table-info">
                                            <td colspan="5" class="text-center">Total</td>
                                            <td class="text-right">{{ $totalQty }}</td>
                                            @if (auth()->user()['role_id'] == 1)
                                                <td></td>
                                                <td class="text-right">{{ formatRupiah($totalHarga) }}</td>
                                            @endif
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
        // function toTempProfitLoss(id){
        //     $('#order_id').val(id);
        //     var form = document.getElementById("form-temp");
        //     form.submit();
        // }
        // 
    </script>

@endsection
