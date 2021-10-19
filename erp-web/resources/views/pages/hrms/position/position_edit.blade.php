@extends('theme.default')

@section('breadcrumb')
      <div class="page-breadcrumb">
                <div class="row">
                    <div class="col-5 align-self-center">
                        <h4 class="page-title">Edit Jabatan</h4>
                        <div class="d-flex align-items-center">
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="{{ URL::to('position') }}">Jabatan</a></li>
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
                            <form method="POST" action="{{ URL::to('position/edit_post') }}" >
                              @csrf
                              <div class="form-row">
                                <div class="col-md-12 mb-3">
                                  <label>Nama Jabatan</label>
                                  <input type="hidden" name="id" value="{{$data->id}}">
                                  <input type="text" class="form-control" name="name" required value="{{$data->name}}">
                                  <div class="invalid-tooltip">
                                      Please fill out this field.
                                  </div>
                                </div>
                              </div>
                              <button class="btn btn-primary mt-4" type="submit" id="btn_submit">Submit</button>
                              <a href="{{ URL::to('position') }}" class="btn btn-danger mt-4">Batal</a>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
@endsection
