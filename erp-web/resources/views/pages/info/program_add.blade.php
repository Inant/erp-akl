@extends('theme.default')

@section('breadcrumb')
			<div class="page-breadcrumb">
                <div class="row">
                    <div class="col-5 align-self-center">
                        <h4 class="page-title">Tambah Pengumuman</h4>
                        <div class="d-flex align-items-center">
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="{{ URL::to('dashboard/programList') }}">Pengumuman</a></li>
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
                            <form method="POST" action="{{ URL::to('dashboard/program/add_post') }}" >
                              @csrf
                              <div class="form-row">
                                <div class="col-md-12 mb-3">
                                  <label>Nama Pengumuman</label>
                                  <input type="text" class="form-control" name="nama" required>
                                  <div class="invalid-tooltip">
                                      Please fill out this field.
                                  </div>
                                </div>
                              </div>
                              <input type="hidden" class="form-control" name="user_id" required value="{{$id}}">
                              <div class="form-row">
                                <div class="col-12">
                                  <label>Status</label>
                                </div>
                                <div class="col-12">
                                  <select name="status" required class="form-control select2" style="width: 100%;">
                                        <option value="">- Pilih Status -</option>
                                        <option value="Active">Active</option>
                                        <option value="Inactive">Inactive</option>
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
