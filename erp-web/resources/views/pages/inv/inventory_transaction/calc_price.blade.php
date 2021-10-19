@extends('theme.default')

@section('breadcrumb')
            <div class="page-breadcrumb">
                <div class="row">
                    <div class="col-5 align-self-center">
                        <h4 class="page-title">Calculate Price</h4>
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
<div class="container-fluid">
    <!-- basic table -->
    <div class="row">
        <div class="col-6">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-12">
                            <h4>Calculate Price Rentang Bulan</h4>
                            <form method="POST" action="{{ URL::to('inventory/calcPriceYear') }}" class="form-group">
                              @csrf
                              <div class="row">
                                <div class="col-6">
                                    <div class="form-group">
                                        <label>Pilih Bulan pertama : </label>&nbsp;
                                        <select data-plugin-selectTwo class="form-control select2" name="bulan" id="bulan" required style="width:100%">
                                            <option value="">--Pilih Bulan--</option>
                                            <option value="1">Januari</option>
                                            <option value="2">Februari</option>
                                            <option value="3">Maret</option>
                                            <option value="4">April</option>
                                            <option value="5">Mei</option>
                                            <option value="6">Juni</option>
                                            <option value="7">Juli</option>
                                            <option value="8">Agustus</option>
                                            <option value="9">September</option>
                                            <option value="10">Oktober</option>
                                            <option value="11">November</option>
                                            <option value="12">Desember</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="form-group">
                                        <label>Pilih Bulan kedua : </label>&nbsp;
                                        <select data-plugin-selectTwo class="form-control select2" name="bulan2" id="bulan2" required style="width:100%">
                                            <option value="">--Pilih Bulan--</option>
                                            <option value="1">Januari</option>
                                            <option value="2">Februari</option>
                                            <option value="3">Maret</option>
                                            <option value="4">April</option>
                                            <option value="5">Mei</option>
                                            <option value="6">Juni</option>
                                            <option value="7">Juli</option>
                                            <option value="8">Agustus</option>
                                            <option value="9">September</option>
                                            <option value="10">Oktober</option>
                                            <option value="11">November</option>
                                            <option value="12">Desember</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="form-group">
                                        <label>Pilih Tahun : </label>&nbsp;
                                        <select data-plugin-selectTwo class="form-control select2" name="tahun" id="tahun" required style="width:100%">
                                            <option value="">--Pilih Tahun--</option>
                                            @for ($i = date('Y') - 5; $i <= date('Y'); $i++)
                                            <option value="{{$i}}">{{$i}}</option>
                                            @endfor
                                        </select>
                                    </div>
                                </div>
                              </div>
                              <button class="btn btn-success" type="submit" name="submit" value="submit">Simpan</button>
                             </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-12">
                            <h4>Calculate Price per Bulan</h4>
                            <form method="POST" action="{{ URL::to('inventory/calc_price') }}" class="form-group">
                              @csrf
                              <div class="row">
                                  
                                <div class="col-6">
                                    <div class="form-group">
                                        <label>Pilih Bulan : </label>&nbsp;
                                        <select data-plugin-selectTwo class="form-control select2" name="bulan" id="bulan3" required style="width:100%">
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
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="form-group">
                                        <label>Pilih Tahun : </label>&nbsp;
                                        <select data-plugin-selectTwo class="form-control select2" name="tahun" id="tahun2" required style="width:100%">
                                            <option value="">--Pilih Tahun--</option>
                                            @for ($i = date('Y') - 5; $i <= date('Y'); $i++)
                                            <option value="{{$i}}">{{$i}}</option>
                                            @endfor
                                        </select>
                                    </div>
                                </div>
                              </div>
                                <button class="btn btn-success" type="submit" name="submit" value="submit">Simpan</button>
                             </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
                
</div>
<script src="https://code.jquery.com/jquery-1.10.2.js"></script>
<script type="text/javascript">
</script>
@endsection