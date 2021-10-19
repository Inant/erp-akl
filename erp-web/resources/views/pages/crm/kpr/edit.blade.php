@extends('theme.default')

@section('breadcrumb')
			<div class="page-breadcrumb">
                <div class="row">
                    <div class="col-5 align-self-center">
                        <h4 class="page-title">Edit Simulasi KPR</h4>
                        <div class="d-flex align-items-center">
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="{{ URL::to('menu/sales') }}">Simulasi KPR</a></li>
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
                                @foreach ($data as $d)
                                <form method="GET" action="{{ URL::to('menu/simulasi_kpr/update') }}" >
                                  @csrf
                                  <div class="form-row">
                                    <div class="col-md-2 mb-3">
                                    </div>
                                    <div class="col-md-8 mb-3">
                                      <label>Nama Bank</label>
                                      <input type="text" class="form-control" name="id" hidden value="{{ $d->id }}">
                                      <input type="text" class="form-control" name="bank_id" hidden value="{{ $d->bank_id }}">
                                      <input type="text" class="form-control" name="bank_name" value="{{ $d->bank_name }}" readonly>
                                    </div>
                                  </div>
                                  <div class="form-row">
                                    <div class="col-md-2 mb-3">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label>Link</label>
                                        <input type="text" class="form-control" name="link_url"  value="{{ $d->link_url }}">
                                    </div>
                                  </div>
                                  <div class="form-row">
                                        <div class="col-md-2 mb-3">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                                <button class="btn btn-primary mt-4" type="submit" id="btn_submit">Submit</button>
                                                <a href="{{ URL::to('menu/simulasi_kpr') }}" class="btn btn-danger mt-4">Batal</a>
                                        </div>
                                      </div>
                                  >
                                </form>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
@endsection
