@extends('theme.default')

@section('breadcrumb')
    <div class="page-breadcrumb">
        <div class="row">
            <div class="col-5 align-self-center">
                <h4 class="page-title">Master Product</h4>
                <div class="d-flex align-items-center">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ URL::to('master_product') }}">Master Product</a></li>
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
                    <h4 class="card-title">Create Product</h4>
                    <form method="POST" action="{{ URL::to('master_warehouse/edit/'.$warehouse['id']) }}" class="form-horizontal" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group row">
                            <label class="col-sm-3 text-right control-label col-form-label">Site</label>
                            <div class="col-sm-8">
                                <select id="site_id" name="site_id" required class="form-control select2 custom-select" style="width: 100%; height:32px;" disabled>
                                    <option value="">--- Pilih Site ---</option>
                                    @foreach($site as $value)
                                    <option value="{{$value['id']}}" {{$warehouse['site_id'] == $value['id']  ? 'selected' : ''}}>{{$value['name']}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-3 text-right control-label col-form-label">Nama Gudang</label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="name" value="{{$warehouse['name']}}"/>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-3 text-right control-label col-form-label">Kode Gudang</label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="kode" value="{{$warehouse['code']}}"/>
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


<script src="{!! asset('theme/assets/libs/jquery/dist/jquery.min.js') !!}"></script>
<script>
</script>
@endsection