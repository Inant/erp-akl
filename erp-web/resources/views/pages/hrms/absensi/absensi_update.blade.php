@extends('theme.default')

@section('breadcrumb')
			<div class="page-breadcrumb">
                <div class="row">
                    <div class="col-5 align-self-center">
                        <h4 class="page-title">Edit Absensi</h4>
                        <div class="d-flex align-items-center">
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="{{ URL::to('absensi') }}">Absensi</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">Edit</li>
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
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title">
                            </h4>
                            <form method="POST" action="{{ URL::to('absensi/update') }}" >
                              @csrf
                              <input type="hidden" class="form-control" name="id_absensi" id="id_absensi" value="{{$detail->id_absensi}}" />
                              <input type="hidden" class="form-control" name="id_pegawai" id="id_pegawai" value="{{$detail->m_employee_id}}" />
                              <div class="form-row">
                                <div class="col-md-12 mb-3">
                                  <label>Nama Pegawai</label>
                                  <input type="text" class="form-control" name="nama" required value="{{$detail->name}}">
                                </div>
                              </div>
                              <div class="form-row">
                                <div class="col-md-12 mb-3">
                                  <label>Tanggal</label>
                                  <input type="date" class="form-control" name="tanggal" id="tanggal"  value="{{$detail->tanggal}}" readonly/>
                                </div>
                              </div>
                              <div class="form-row">
                                <div class="col-md-12 mb-3">
                                  <label>Jam Datang</label>
                                  <input type="time" class="form-control" name="jam_datang" id="jam_datang"  value="{{$detail->jam_datang}}" required>
                                </div>
                              </div>
                              <div class="form-row">
                                <div class="col-md-12 mb-3">
                                  <label>Jam Pulang</label>
                                  <input type="time" class="form-control" name="jam_pulang" id="jam_pulang"  value="{{$detail->jam_pulang}}" required/>
                                </div>
                              </div>
                              <div class="form-row">
                                <div class="col-12">
                                  <label>Keterangan</label>
                                </div>
                                <div class="col-md-12 mb-3">
                                  <input type="text" class="form-control" name="ket" id="ket"  value="{{$detail->keterangan}}" />
                                </div>
                              </div>
                              <button class="btn btn-primary mt-4" type="submit" id="btn_submit">Submit</button>
                              <a href="{{ URL::to('absensi') }}" class="btn btn-danger mt-4">Batal</a>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
@endsection
