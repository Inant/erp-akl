@extends('theme.default')

@section('breadcrumb')
			<div class="page-breadcrumb">
                <div class="row">
                    <div class="col-5 align-self-center">
                        <h4 class="page-title">List Absensi Perbulan</h4>
                        <div class="d-flex align-items-center">
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="{{ URL::to('absensi') }}">Absensi</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">List Absensi Perbulan</li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
@endsection

@section('content')
        @php
        function formatTime($time){
            if ($time != '') {
                $timeTemp=explode(':', $time);
                return $timeTemp[0].':'.$timeTemp[1]. ' WIB';
            }else{
                return '-';
            }
        }
        @endphp
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title">
                            </h4>
                            <form accept="absensi/month" method="post">
                                @csrf
                                <center>
                                    <div class="col-sm-12">
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
                                            <select data-plugin-selectTwo class="form-control select2" name="tahun" id="tahun" required>
                                                <option value="">Pilih Tahun</option>
                                                @for ($i = date('Y') - 5; $i <= date('Y'); $i++) { 
                                                <option value="{{$i}}">{{$i}}</option>
                                                @endfor
                                            </select>&nbsp;
                                            <label>Pilih Pegawai : </label>&nbsp;
                                            <select class="form-control select2" name="id_pegawai" id="id_pegawai" required>
                                                <option value="">Pilih Pegawai</option>
                                                @foreach($pegawai_option as $value)
                                                <option value="{{$value->id}}" {{$value->id == $id_pegawai ? 'selected' : ''}}>{{$value->name}}</option>
                                                @endforeach
                                            </select>
                                            &nbsp;
                                            <button class="btn btn-primary"  onclick="cekAbsensiDate()"><i class="fa fa-search"></i></button>
                                        </div>
                                    </div>
                                </center>
                            </form>
                            <br>
                            @if($id_pegawai != 0)
                            <div class="table-responsive">
                              <table class="table table-bordered table-striped">
                                  <thead>
                                      <tr>
                                              <th>tanggal</th>
                                      @php 
                                      for ($i=0; $i < 10; $i++) { 
                                      @endphp
                                              <th  class="text-center">@php print_r($absensi['data'][$i]['tanggal']) @endphp</th>
                                      @php
                                      }
                                      @endphp
                                          </tr>
                                          <tr>
                                              <th>jam datang</th>
                                      @php 
                                      for ($i=0; $i < 10; $i++) { 
                                      @endphp
                                              <th  class="text-center">{{formatTime($absensi['data'][$i]['jam_datang'])}}</th>
                                      @php
                                      }
                                      @endphp
                                          </tr>
                                          <tr>
                                              <th>jam pulang</th>
                                      @php 
                                      for ($i=0; $i < 10; $i++) { 
                                      @endphp
                                              <th  class="text-center">{{formatTime($absensi['data'][$i]['jam_pulang'])}}</th>
                                      @php
                                      }
                                      @endphp
                                      </tr>
                                      <tr>
                                              <th>Action</th>
                                      @php 
                                      for ($i=0; $i < 10; $i++) { 
                                      @endphp
                                              <th class="text-center"><a href="{{URL::to('absensi/edit/'.$id_pegawai)}}/{{$absensi['data'][$i]['date']}}" class="btn btn-success btn-sm"><i class="mdi mdi-pencil"></i></a></th>
                                      @php
                                      }
                                      @endphp
                                      </tr>
                                  </thead>            
                              </table>

                              <table class="table table-bordered table-striped">
                                  <thead>
                                      <tr>
                                              <th>tanggal</th>
                                      @php 
                                      for ($i=10; $i < 20; $i++) { 
                                      @endphp
                                              <th  class="text-center">@php print_r($absensi['data'][$i]['tanggal']) @endphp</th>
                                      @php
                                      }
                                      @endphp
                                          </tr>
                                          <tr>
                                              <th>jam datang</th>
                                      @php 
                                      for ($i=10; $i < 20; $i++) { 
                                      @endphp
                                              <th  class="text-center">{{formatTime($absensi['data'][$i]['jam_datang'])}}</th>
                                      @php
                                      }
                                      @endphp
                                          </tr>
                                          <tr>
                                              <th>jam pulang</th>
                                      @php 
                                      for ($i=10; $i < 20; $i++) { 
                                      @endphp
                                              <th class="text-center">{{formatTime($absensi['data'][$i]['jam_pulang'])}}</th>
                                      @php
                                      }
                                      @endphp
                                      </tr>
                                      <tr>
                                              <th>Action</th>
                                      @php 
                                      for ($i=10; $i < 20; $i++) { 
                                      @endphp
                                              <th class="text-center"><a href="{{URL::to('absensi/edit/'.$id_pegawai)}}/{{$absensi['data'][$i]['date']}}" class="btn btn-success btn-sm"><i class="mdi mdi-pencil"></i></a></th>
                                      @php
                                      }
                                      @endphp
                                      </tr>
                                  </thead>            
                              </table>
                              <table class="table table-bordered table-striped">
                                  <thead>
                                      <tr>
                                              <th>tanggal</th>
                                      @php 
                                      for ($i=20; $i < $jumlah_hari; $i++) { 
                                      @endphp
                                              <th  class="text-center">@php print_r($absensi['data'][$i]['tanggal']) @endphp</th>
                                      @php
                                      }
                                      @endphp
                                          </tr>
                                          <tr>
                                              <th>jam datang</th>
                                      @php 
                                      for ($i=20; $i < $jumlah_hari; $i++) { 
                                      @endphp
                                              <th  class="text-center">{{formatTime($absensi['data'][$i]['jam_datang'])}}</th>
                                      @php
                                      }
                                      @endphp
                                          </tr>
                                          <tr>
                                              <th>jam pulang</th>
                                      @php 
                                      for ($i=20; $i < $jumlah_hari; $i++) { 
                                      @endphp
                                              <th class="text-center">{{formatTime($absensi['data'][$i]['jam_pulang'])}}</th>
                                      @php
                                      }
                                      @endphp
                                      </tr>
                                      <tr>
                                              <th>Action</th>
                                      @php 
                                      for ($i=20; $i < $jumlah_hari; $i++) { 
                                      @endphp
                                              <th class="text-center"><a href="{{URL::to('absensi/edit/'.$id_pegawai)}}/{{$absensi['data'][$i]['date']}}" class="btn btn-success btn-sm"><i class="mdi mdi-pencil"></i></a></th>
                                      @php
                                      }
                                      @endphp
                                      </tr>
                                  </thead>            
                              </table>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
<script type="text/javascript" src="https://code.jquery.com/jquery-3.3.1.js"></script>
<script>
  var bulan='{{$bulan}}';
  var tahun='{{$tahun}}';
  $(document).ready(function() {
    if (typeof bulan != 'undefined') {
        $('#bulan').val(bulan).change();
        $('#tahun').val(tahun).change();
    }
  });
</script>
@endsection
