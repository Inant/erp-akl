@extends('theme.default')

@section('breadcrumb')
    <div class="page-breadcrumb">
        <div class="row">
            <div class="col-5 align-self-center">
                <h4 class="page-title">Master Harga Jual</h4>
                <div class="d-flex align-items-center">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ URL::to('master_harga_jual/index') }}">Master Harga Jual</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Create</li>
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
                    <h4 class="card-title">Create Master Harga Jual</h4>
                    <form method="POST" action="{{ URL::to('master_harga_jual/') }}" class="form-horizontal">
                        @csrf
                        <div class="form-group row">
                            <label class="col-sm-3 text-right control-label col-form-label">Nomor Material</label>
                            <div class="col-sm-9">
                                <select name="material" id="material" class="form-control select2" style="width:100%">
                                    <option value="">--Pilih Nomor Material---</option>
                                    @foreach ($mItem as $item)
                                        <option value="{{ old('material', $item->id) }}">{{ $item->no }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-3 text-right control-label col-form-label">Retail</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" name="retail" value="{{ old('retail') }}" />
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-3 text-right control-label col-form-label">Grosir</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" name="grosir" value="{{ old('grosir') }}" />
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-3 text-right control-label col-form-label">Distributor</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" name="distributor" value="{{ old('distributor') }}" />
                            </div>
                        </div>
                        <div class="text-right">
                            <button class="btn btn-primary mt-4" type="submit">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        
        </div>
    </div>
</div>

@endsection