@extends('theme.default')

@section('breadcrumb')
    <div class="page-breadcrumb">
        <div class="row">
            <div class="col-5 align-self-center">
                <h4 class="page-title">Master Suplier</h4>
                <div class="d-flex align-items-center">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ URL::to('master_suplier') }}">Master Suplier</a></li>
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
                    <form method="POST" action="{{ URL::to('master_suplier/create') }}" class="form-horizontal">
                        @csrf
                        <div class="form-group">
                            <label class="control-label col-form-label">Nama Suplier</label>
                            <input type="text" class="form-control" name="nama" required placeholder="Isikan Nama Suplier"/>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-form-label">Nomor Telepon Kantor</label>
                            <input type="number" class="form-control" name="phone" required placeholder="Isikan Nomor Telepon"/>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-form-label">Alamat</label>
                            <input type="text" class="form-control" name="address" required placeholder="Isikan Alamat Suplier"/>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-form-label">Kota</label>
                            <input type="text" class="form-control" name="city" required placeholder="Isikan Kota Suplier"/>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-form-label">Catatan</label>
                            <input type="text" class="form-control" name="note" placeholder="Beri Catatan Suplier"/>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-form-label">Nama Direktur</label>
                            <input type="text" class="form-control" name="director" placeholder="Nama Direktur"/>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-form-label">Nomor Telp Direktur</label>
                            <input type="number" class="form-control" name="director_phone" placeholder="Nomor Telepon Direktur"/>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-form-label">Nama Kontak Person</label>
                            <input type="text" class="form-control" name="person_name" placeholder="Nama Kontak Person"/>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-form-label">Nomor Kontak Person</label>
                            <input type="number" class="form-control" name="person_phone" placeholder="Nomor Telepon Kontak Person"/>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-form-label">Nomor Rekening Supplier</label>
                            <input type="text" class="form-control" name="rekening_number" placeholder="Nomor Rekening Supplier"/>
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
</script>
@endsection