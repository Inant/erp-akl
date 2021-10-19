@extends('theme.default')

@section('breadcrumb')
			<div class="page-breadcrumb">
                <div class="row">
                    <div class="col-5 align-self-center">
                        <h4 class="page-title">Tambah Simulasi KPR</h4>
                        <div class="d-flex align-items-center">
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="{{ URL::to('menu/sales') }}">Simulasi KPR</a></li>
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
                                <form method="POST" action="{{ URL::to('menu/gambar/save') }}" enctype="multipart/form-data">
                                  @csrf
                                  <div class="form-row">
                                    <div class="col-md-2 mb-3">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                      <label>Upload Gambar</label>
                                        <input type="file" id="photo" name="photo" class="form-control">
                                        <span class="help-block with-errors"></span>
                                    </div>
                                  </div>
                                  <div class="form-row">
                                        <div class="col-md-2 mb-3">
                                        </div>
                                      <div class="col-md-6">
                                        <button class="btn btn-primary mt-4" type="submit">Submit</button>
                                        <a href="{{ URL::to('menu/gambar') }}" class="btn btn-danger mt-4">Batal</a>
                                      </div>
                                  </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
@endsection
