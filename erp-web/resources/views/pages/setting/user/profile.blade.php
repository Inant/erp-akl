@extends('theme.default')

@section('breadcrumb')
<div class="page-breadcrumb">
    <div class="row">
        <div class="col-5 align-self-center">
            <h4 class="page-title">Profil User</h4>
            <div class="d-flex align-items-center">
                <nav aria-label="breadcrumb">
                    <!-- <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ URL::to('user') }}">User</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Detail</li>
                    </ol> -->
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

                    <form method="post" action="{{ URL::to('user/editprofile') }}" enctype="multipart/form-data">
                        @csrf
                        <button class="btn btn-primary" type="button" data-toggle="modal" data-target="#passwordModal">Edit Password</button>
                        <br><br>
                        <input type="hidden" name="id" required value="{{$value[0]->id}}">
                        <div class="form-row">
                            <div class="col-md-12 mb-3">
                                <label>Nama</label>
                                <input type="text" class="form-control" name="nama" required value="{{$value[0]->name}}">
                                <div class="invalid-tooltip">
                                    Please fill out this field.
                                </div>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="col-md-12 mb-3">
                                <label>Email</label>
                                <input type="email" class="form-control" name="email" required value="{{$value[0]->email}}">
                                <div class="invalid-tooltip">
                                    Please fill out this field.
                                </div>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="col-md-12 mb-3">
                                <label>Tanda Tangan</label>
                                <div class="row">
                                    <div class="col-sm-6">
                                        <input type="file" name="signature" id="signature" class="form-control">
                                    </div>
                                    <div class="col-sm-6">
                                        <img src="{{env('API_URL').$value[0]->signature}}" alt="" width="200">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="col-md-12 mb-3">
                                <label>Kode TTD</label>
                                <input id="code_signature" class="form-control" maxlength="6" name="code_signature" onchange="cekCode(this.value)" value="{{$value[0]->code_signature}}">
                                <div class="invalid-tooltip">
                                    Please fill out this field.
                                </div>
                            </div>
                        </div>
                        <button class="btn btn-primary mt-4" type="submit">Submit</button>
                        <a href="{{ URL::to('user') }}" class="btn btn-danger mt-4">Batal</a>

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="passwordModal" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form method="post" action="{{ URL::to('user/edit_profil_password') }}">
                @csrf
                <div class="modal-header">
                    <h4 class="modal-title" id="modalAddWorkHeaderLabel1">Ganti Password</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <input type="hidden" value="{{$id}}" name="id" class="form-control" required />
                        <label class="control-label">Password:</label>
                        <input type="password" name="password" class="form-control" id="password" required autofocus />
                        <p id="textPass" style="color:red"></p>
                        <label class="control-label">Confirm Password:</label>
                        <input type="password" name="confirm" class="form-control" id="confirm" required onkeyup="cekConfirmPass()" />
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" id="btn_change" disabled class="btn btn-primary btn-sm">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
    function cekConfirmPass() {
        var pass = $('#password').val();
        var confirm = $('#confirm').val();
        if (confirm == pass) {
            $("#btn_change").attr("disabled", false);
        } else {
            $("#btn_change").attr("disabled", true);
        }

    }

    function cekCode(val) {
        $.ajax({
            type: "GET",
            url: "{{ URL::to('user/cek_code') }}", //json get site
            dataType: 'json',
            data : {code : val, id : null},
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