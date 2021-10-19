@extends('theme.default')

@section('breadcrumb')
<div class="page-breadcrumb">
    <div class="row">
        <div class="col-5 align-self-center">
            <h4 class="page-title">Create User</h4>
            <div class="d-flex align-items-center">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ URL::to('user') }}">User</a></li>
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
                    <h4 class="card-title">
                    </h4>

                    <form method="post" action="{{ URL::to('user/adduser') }}">
                        @csrf
                        <div class="form-row">
                            <div class="col-md-12 mb-3">
                                <label>Nama</label>
                                <input type="text" class="form-control" name="nama" required placeholder="Isi Nama Lengkap">
                                <div class="invalid-tooltip">
                                    Please fill out this field.
                                </div>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="col-md-12 mb-3">
                                <label>Email</label>
                                <input type="email" class="form-control" name="email" required placeholder="Isikan Email">
                                <div class="invalid-tooltip">
                                    Please fill out this field.
                                </div>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="col-md-12 mb-3">
                                <label>Password</label>
                                <input type="password" class="form-control" minlength="8" name="password" id="password" required placeholder="Isikan Password">
                                <div class="invalid-tooltip">
                                    Please fill out this field.
                                </div>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="col-md-12 mb-3">
                                <label>Konfirmasi Password</label>
                                <input type="password" class="form-control" name="confirm" id="confirm" required placeholder="Konfirmasi Password" onkeyup="cekConfirmPass()">
                                <div class="invalid-tooltip">
                                    Please fill out this field.
                                </div>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="col-md-12 mb-3">
                                <label>Jabatan</label>
                                <select name="jabatan" id="jabatan" class="form-control" required onchange="salesForm()">
                                    <option value="">-- Pilih Jabatan --</option>
                                    @foreach ($roles as $data)
                                    <option value="{{$data->id}}">{{$data->role_name}}</option>
                                    @endforeach
                                </select>
                                <div class="invalid-tooltip">
                                    Please fill out this field.
                                </div>
                            </div>
                        </div>
                        <div id="addsales">

                        </div>
                        <div class="form-row">
                            <div class="col-md-12 mb-3">
                                <label>Site</label>
                                <select name="site" class="form-control" required onchange="getGudang(this)">
                                    <option value="">-- Pilih Site --</option>
                                    @for($i=0; $i < count($site); $i++) <option value="{{$site[$i]['id']}}">{{$site[$i]['name']}}</option>
                                        @endfor
                                </select>
                                <div class="invalid-tooltip">
                                    Please fill out this field.
                                </div>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="col-md-12 mb-3">
                                <label>Gudang</label>
                                <select name="m_warehouse_id" id="m_warehouse_id" class="form-control">
                                    <option value="">-- Pilih Gudang --</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="col-md-12 mb-3">
                                <label>Status</label>
                                <select name="status" class="form-control" required>
                                    <option value="">-- Pilih Status --</option>
                                    <option value="1">Active</option>
                                    <option value="0">Inactive</option>
                                </select>
                                <div class="invalid-tooltip">
                                    Please fill out this field.
                                </div>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="col-md-12 mb-3">
                                <label>Kode TTD</label>
                                <input id="code_signature" class="form-control" maxlength="6" name="code_signature" onchange="cekCode(this.value)">
                                <div class="invalid-tooltip">
                                    Please fill out this field.
                                </div>
                            </div>
                        </div>
                        <button class="btn btn-primary mt-4" type="submit" id="btn_change" disabled>Submit</button>
                        <a href="{{ URL::to('user') }}" class="btn btn-danger mt-4">Batal</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // function cekPass(){
    //     var pass=$('#password').val();
    //     if(pass.length()<10){
    //         $("#textPass").html("password kurang dari 10");
    //     }else{
    //         $("#textPass").html("password");
    //     }

    // }

    function salesForm() {
        var jab = $('#jabatan').val();
        console.log(jab);
        if (jab == 7) {
            console.log("SALESMAN");
            $('#addsales').append('<div id="form-1" class="form-row">' +
                '<div class="col-md-12 mb-3">' +
                '<label>Sales</label>' +
                '<select name="sales" class="form-control" required>' +
                '<option value="">-- Pilih Sales --</option>'
                <?php foreach ($sales as $s) { ?> +
                    '<option value="<?php echo $s->id ?>"><?php echo $s->name ?></option>'
                <?php } ?> +
                '</select>' +
                '<div class="invalid-tooltip">' +
                'Please fill out this field.' +
                '</div>' +
                '</div>' +
                '</div>');
        } else {
            console.log("terhapus");

            $('#form-1').remove();
        }
    }

    function cekConfirmPass() {
        var pass = $('#password').val();
        var confirm = $('#confirm').val();
        if (confirm == pass) {
            $("#btn_change").attr("disabled", false);
        } else {
            $("#btn_change").attr("disabled", true);
        }

    }

    function getGudang(eq) {
        var id = eq.value;
        $('#m_warehouse_id').empty();
        $.ajax({
            type: "GET",
            // url: "{{ URL::to('material_request/list_detail') }}" + "/" + id, //json get site
            url: "{{ URL::to('master_warehouse/get_warehouse_by_site') }}" + "/" + id, //json get site
            dataType: 'json',
            async: false,
            success: function(response) {
                arrData = response['data'];
                var option = '<option value="">-- Pilih Gudang --</option>';
                for (i = 0; i < arrData.length; i++) {
                    option += '<option value="' + arrData[i]['id'] + '">' + arrData[i]['name'] + '</option>';
                }
                $('#m_warehouse_id').append(option);
            }
        });
    }
    function cekCode(val) {
        $.ajax({
            type: "GET",
            url: "{{ URL::to('user/cek_code') }}", //json get site
            dataType: 'json',
            data : {code : val, id: null},
            async: false,
            success: function(response) {
                if (response == 1) {
                    $('#code_signature').val('')
                }
            }
        });
    }
</script>
@endsection