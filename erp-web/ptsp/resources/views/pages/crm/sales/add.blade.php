@extends('theme.default')

@section('breadcrumb')
			<div class="page-breadcrumb">
                <div class="row">
                    <div class="col-5 align-self-center">
                        <h4 class="page-title">Tambah Sales</h4>
                        <div class="d-flex align-items-center">
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="{{ URL::to('menu/sales') }}">Sales</a></li>
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
                                <form method="POST" action="{{ URL::to('menu/sales/save') }}" >
                                  @csrf
                                  <div class="form-row">
                                    <div class="col-md-8 mb-3">
                                      <label>Nama Sales</label>
                                      <input type="text" class="form-control" name="name"  id="kode">
                                    </div>
                                  </div>
                                  <div class="form-row">
                                    <div class="col-md-6 mb-3">
                                      <label>Role</label>
                                      <select name="role" required class="form-control" id="bank_name" onchange="bank()">
                                            <option value="">- Pilih Role -</option>
                                            <option value="Marketing">Marketing</option>
                                            <option value="Marketing Officer">Marketing Officer</option>
                                      </select>

                                    </div>
                                  </div>
                                  <div class="form-row">
                                    <div class="col-md-6 mb-3">
                                      <label>Position</label>
                                      <select name="position" required class="form-control" id="bank_name" onchange="bank()">
                                            <option value="">- Pilih Position -</option>
                                            <option value="Sales">Sales</option>
                                            <option value="Staff">Staff</option>
                                            <option value="Supervisor">Supervisor</option>
                                      </select>

                                    </div>
                                  </div>
                                  <button class="btn btn-primary mt-4" type="submit">Submit</button>
                                  <a href="{{ URL::to('menu/sales') }}" class="btn btn-danger mt-4">Batal</a>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
@endsection
