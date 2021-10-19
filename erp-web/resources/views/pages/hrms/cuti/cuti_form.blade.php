@extends('theme.default')

@section('breadcrumb')
			<div class="page-breadcrumb">
                <div class="row">
                    <div class="col-5 align-self-center">
                        <h4 class="page-title">Tambah Cuti</h4>
                        <div class="d-flex align-items-center">
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="{{ URL::to('cuti') }}">Cuti</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">Tambah</li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
@endsection

@section('content')
        <div class="container-fluid">
            <div class="row">
                <div class="col-5">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title">
                            </h4>
                            <form method="POST" action="{{ URL::to('cuti/add_post').'/'.$id_pegawai.'/'.$date }}" >
                              @csrf
                              <div class="row">
                                  <div class="col-lg-4">
                                      Nama Pegawai
                                  </div>
                                  <div class="col-lg-6">
                                      : {{$pegawai->name}}
                                  </div>
                              </div>
                              <br>
                              <div class="form-row">
                                <div class="col-md-12 mb-3">
                                  <input type="hidden" class="form-control" name="bulan" value="{{$bulan}}" />
                                  <input type="hidden" class="form-control" name="tahun" value="{{$tahun}}" />
                                  <input type="hidden" class="form-control" name="id_pegawai" id="id_pegawai" placeholder="Id Pegawai"  value="{{$id_pegawai}}" />
                                  <label>Tanggal</label>
                                  <input type="date" class="form-control" name="tanggal" required>
                                </div>
                              </div>
                              <button class="btn btn-primary mt-4" type="submit" id="btn_submit">Submit</button>
                              <a href="{{ URL::to('cuti') }}" class="btn btn-danger mt-4">Batal</a>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-7">
                    <div class="card">
                        <div class="card-body">
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
                                           
                                            <button class="btn btn-primary"  onclick="cekAbsensiDate()"><i class="fa fa-search"></i></button>
                                        </div>
                                    </div>
                                </center>
                            </form>
                          </div>
                          <div class="card-body">
                            <div class="table-responsive">
                              <table class="table">
                                <thead>
                                      <tr>
                                          <th>#</th>
                                          <th>Tanggal Cuti</th>
                                          <th>Action</th>
                                      </tr>
                                  </thead>
                                  <tbody>
                                      @php
                                      $a=0;
                                          foreach ($list_cuti as $key => $value) {
                                              $a++;
                                      @endphp
                                      <tr>
                                          <td>{{$a}}</td>
                                          <td>{{$value->tanggal}}</td>
                                          <td><a href="/cuti/delete_cuti/{{$value->id}}/{{$id_pegawai}}/{{$date}}" class="btn btn-sm btn-danger" title="hapus"><i class="fa fa-trash"></i></a></td>
                                      </tr>
                                      @php
                                          }
                                      @endphp
                                      
                                  </tbody>
                              </table>
                            </div>
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
