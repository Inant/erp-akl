@extends('theme.default')

@section('breadcrumb')
    <div class="page-breadcrumb">
        <div class="row">
            <div class="col-5 align-self-center">
                <h4 class="page-title">Master Kavling</h4>
                <div class="d-flex align-items-center">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ URL::to('master_kavling') }}">Master Kavling</a></li>
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
                    <!-- <h4 class="card-title">Create Kavling</h4> -->
                    <form method="POST" action="{{ URL::to('master_kavling/create') }}" class="form-horizontal">
                        @csrf
                        <div class="form-group">
                            <label class="control-label col-form-label">Nama Kavling</label>
                            <input type="text" class="form-control" name="code" required placeholder="Isikan Nama Kavling"/>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-form-label">Area</label>
                            <input type="text" class="form-control" name="area" required placeholder="Isikan Area Kavling"/>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-form-label">Base Price</label>
                            <input type="number" class="form-control" name="price" required placeholder="Isikan Harga Kavling"/>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-form-label">City</label>
                            <select class="form-control" name="city" onchange="getSiteName(this.value);">
                                <option>-- Pilih Kota --</option>
                                @for($i=0; $i < count($kota); $i++)
                                <option value="{{$kota[$i]['id']}}">{{$kota[$i]['city']}}</option>
                                @endfor
                            </select>
                        </div>
                        <div class="form-group row">
                            <div class="col-sm-1">
                            <label class="control-label col-form-label">Site</label>
                            </div>
                            <div class="col-sm-12">
                            <select id="site_name" class="form-control select2 custom-select" name="site" required style="width: 100%;">
                                <option value="">-- Pilih Site --</option>
                                <!-- @for($i=0; $i < count($site); $i++)
                                <option value="{{$site[$i]['id']}}">{{$site[$i]['name']}}</option>
                                @endfor -->
                            </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-form-label">Status</label>
                            <select class="form-control" name="status" required>
                                <option>-- Pilih Status --</option>
                                <option value="Available">Available</option>
                                <option value="Inavailable">Inavailable</option>
                            </select>
                        </div>
                        <div class="">
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

$(document).ready(function(){
    formSatuan = $('[id^=satuan]');
    formSatuan.empty();
    formSatuan.append('<option value="">-- Select Satuan --</option>');
    $.ajax({
        type: "GET",
        url: "{{ URL::to('master_satuan/list') }}", //json get site
        dataType : 'json',
        success: function(response){
            arrData = response['data'];
            for(i = 0; i < arrData.length; i++){
                formSatuan.append('<option value="'+arrData[i]['id']+'">'+arrData[i]['name']+'</option>');
            }
        }
    });

});
function getSiteName(site_location_id){
    formSiteName = $('[id^=site_name]');
    formSiteName.empty();
    formSiteName.append('<option value="">-- Select Site Name --</option>');
    $.ajax({
        type: "GET",
        url: "{{ URL::to('rab/get_site') }}", //json get site
        dataType : 'json',
        data:"town_id=" + site_location_id,
        success: function(response){
            arrData = response['data'];
            for(i = 0; i < arrData.length; i++){
                formSiteName.append('<option value="'+arrData[i]['id']+'">'+arrData[i]['name']+'</option>');
            }
        }
    });
}
</script>
@endsection