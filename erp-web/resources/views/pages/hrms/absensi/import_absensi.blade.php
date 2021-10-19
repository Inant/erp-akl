@extends('theme.default')

@section('breadcrumb')
			<div class="page-breadcrumb">
                <div class="row">
                    <div class="col-5 align-self-center">
                        <h4 class="page-title">Import Absensi</h4>
                        <div class="d-flex align-items-center">
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="{{ URL::to('absensi') }}">Absensi</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">Import Absensi</li>
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
                            <form action="{{URL::to('absensi/import')}}" method="post" enctype="multipart/form-data">
                                @csrf
                                <div class="form-row">
                                  <div class="col-12">
                                    <label>File</label>
                                  </div>
                                  <div class="col-md-12 mb-3">
                                    <input type="file" class="form-control-file" name="file">
                                  </div>
                                </div>
                                <button class="btn btn-primary" type="submit">Submit</button>
                            </form>
                            
                        </div>
                    </div>
                </div>
            </div>
        </div>
@endsection
