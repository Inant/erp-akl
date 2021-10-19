@extends('theme.default')

@section('breadcrumb')
			<div class="page-breadcrumb">
                <div class="row">
                    <div class="col-5 align-self-center">
                        <h4 class="page-title">Tambah Pegawai</h4>
                        <div class="d-flex align-items-center">
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="{{ URL::to('employee') }}">Pegawai</a></li>
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
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title">
                            </h4>
                            <form method="POST" action="{{ URL::to('employee/add_post') }}" >
                              @csrf
                              <div class="form-row">
                                <div class="col-md-12 mb-3">
                                  <label>Nama Pegawai</label>
                                  <input type="text" class="form-control" name="nama" required>
                                  <div class="invalid-tooltip">
                                      Please fill out this field.
                                  </div>
                                </div>
                              </div>
                              <div class="form-row">
                                <div class="col-md-12 mb-3">
                                  <label>No Telp</label>
                                  <input type="text" class="form-control" name="telp" required>
                                </div>
                              </div>
                              <div class="form-row">
                                <div class="col-md-12 mb-3">
                                  <label>Email</label>
                                  <input type="email" class="form-control" name="email" required>
                                </div>
                              </div>
                              <div class="form-row">
                                <div class="col-md-12 mb-3">
                                  <label>Alamat</label>
                                  <textarea class="form-control" name="alamat" required></textarea>
                                </div>
                              </div>
                              <div class="form-row">
                                <div class="col-12">
                                  <label>Jabatan</label>
                                </div>
                                <div class="col-md-12 mb-3">
                                  <select name="position_id" required class="form-control select2" style="width: 100%;">
                                        <option value="">- Pilih Jabatan -</option>
                                        @foreach($jabatan as $value)
                                        <option value="{{$value->id}}">{{$value->name}}</option>
                                        @endforeach
                                  </select>
                                </div>
                              </div>
                              <div class="form-row">
                                <div class="col-12">
                                  <label>Lokasi</label>
                                </div>
                                <div class="col-md-12 mb-3">
                                  <select name="site_id" required class="form-control select2" style="width: 100%;">
                                        <option value="">- Pilih Lokasi -</option>
                                        @foreach($sites as $value)
                                        <option value="{{$value->id}}">{{$value->name}}</option>
                                        @endforeach
                                  </select>
                                </div>
                              </div>
                              <button class="btn btn-primary mt-4" type="submit" id="btn_submit">Submit</button>
                              <a href="{{ URL::to('dashboard/programList') }}" class="btn btn-danger mt-4">Batal</a>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
@endsection
