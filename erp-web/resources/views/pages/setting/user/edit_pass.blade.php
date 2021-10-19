@extends('theme.default')

@section('breadcrumb')
			<div class="page-breadcrumb">
                <div class="row">
                    <div class="col-5 align-self-center">
                        <h4 class="page-title">Detail User</h4>
                        <div class="d-flex align-items-center">
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="{{ URL::to('user') }}">User</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">Detail</li>
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
                                
                                <form method="POST" action="{{ URL::to('user/edit_pass') }}" >
                                  @csrf
                                  @foreach($users as $key=>$value)
                                  <a href="{{ URL::to('user/edit_pass/'.$value->id) }}" class="btn btn-danger">Edit Password</a>
                                  <div class="form-row">
                                    <div class="col-md-12 mb-3">
                                      <label>Nama</label>
                                      <input type="text" class="form-control" name="nama" required value="{{$value->name}}">
                                      <div class="invalid-tooltip">
                                          Please fill out this field.
                                      </div>
                                    </div>
                                  </div>
                                  <button class="btn btn-primary mt-4" type="submit" disabled id="btn_submit">Submit</button>
                                  <a href="{{ URL::to('menu/payment') }}" class="btn btn-danger mt-4">Batal</a>
                                  @endforeach
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
@endsection
