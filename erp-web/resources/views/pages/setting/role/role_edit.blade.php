@extends('theme.default')

@section('breadcrumb')
			<div class="page-breadcrumb">
                <div class="row">
                    <div class="col-5 align-self-center">
                        <h4 class="page-title">Detail Role</h4>
                        <div class="d-flex align-items-center">
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="{{ URL::to('user') }}">Role</a></li>
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
                    
                    <form method="post" action="{{ URL::to('role/editrole') }}">
                        @csrf
                        <input type="hidden" name="id" required value="{{$roles->id}}">
                        <div class="form-group row">
                            
                            <label class="col-sm-3 text-right control-label col-form-label">Nama</label>
                            <div class="col-sm-6 pb-2 pt-2">
                                <input type="text" class="form-control" name="nama" required value="{{$roles->role_name}}">
                                <div class="invalid-tooltip">
                                    Please fill out this field.
                                </div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-3 text-right control-label col-form-label">Code</label>
                            <div class="col-sm-6 pb-2 pt-2">
                                <input type="text" class="form-control" name="code" required value="{{$roles->role_code}}">
                                <div class="invalid-tooltip">
                                    Please fill out this field.
                                </div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-sm-3"></div>
                            <div class="col-sm-6">
                                <button class="btn btn-primary mt-4" type="submit">Submit</button>
                                <a href="{{ URL::to('role') }}" class="btn btn-danger mt-4">Batal</a>
                            </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
            
<script>
</script>
@endsection
