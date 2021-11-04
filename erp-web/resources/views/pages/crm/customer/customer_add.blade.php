@extends('theme.default')
@section('css-content')
    <link href="{!! asset('public/theme/assets/libs/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css') !!}" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="{!! asset('public/theme/assets/libs/dropzone/dist/min/dropzone.min.css') !!}">
@endsection

@section('breadcrumb')
    <div class="page-breadcrumb">
        <div class="row">
            <div class="col-5 align-self-center">
                <h4 class="page-title">Tambah Customer</h4>
                <div class="d-flex align-items-center">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ URL::to('customer') }}">Customer</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Tambah</li>
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
                        <div class="row">
                            <div class="col-md-1"></div>
                            <div class="col-md-11">
                                <form method="POST" enctype="multipart/form-data" action="{{ URL::to('customer/save') }}">
                                    @csrf
                                    <div class="form-row">
                                        <div class="col-md-8 mb-3">
                                            <label>Nama Perusahaan</label>
                                            <input type="text" class="form-control" name="coorporate_name"
                                                id="coorporate_name" placeholder="ex. PT. Mekar Sari">
                                        </div>
                                    </div>
                                    <div class="form-row">
                                        <div class="col-md-8 mb-3">
                                            <label>Nama Penanggung Jawab</label>
                                            <input type="text" class="form-control" name="name"
                                                placeholder="ex. Haryanto">
                                        </div>
                                    </div>
                                    <div class="form-row">
                                        <div class="col-md-8 mb-3">
                                            <label>Jabatan</label>
                                            <input type="text" class="form-control" name="jabatan" placeholder="">
                                        </div>
                                    </div>
                                    <div class="form-row">
                                        <div class="col-md-8 mb-3">
                                            <label>NPWP</label>
                                            <input type="text" class="form-control" name="npwp" placeholder="">
                                        </div>
                                    </div>
                                    <div class="form-row">
                                        <div class="col-md-8 mb-3">
                                            <label>No. Telp / Fax</label>
                                            <input type="text" class="form-control" name="hp" required
                                                placeholder="ex. 082123xxxxx">
                                        </div>
                                    </div>
                                    <div class="form-row">
                                        <div class="col-md-8 mb-3">
                                            <label>No. Telp / Fax (2)</label>
                                            <input type="text" class="form-control" name="hp2"
                                                placeholder="ex. 082123xxxxx">
                                        </div>
                                    </div>
                                    <div class="form-row">
                                        <div class="col-md-8 mb-3">
                                            <label>Email</label>
                                            <input type="email" class="form-control" name="email">
                                        </div>
                                    </div>

                                    {{-- <div hidden>

                                        <div class="form-row">
                                            <div class="col-md-8 mb-3">
                                                <label>NIK</label>
                                                <input type="number" class="form-control" name="nik"
                                                    placeholder="ex. 3502012xxxxxxxx">
                                            </div>
                                        </div>
                                        <div class="form-row">
                                            <div class="col-md-8 mb-3">
                                                <label>Tempat / Tanggal Lahir</label>
                                                <div class="row">
                                                    <div class="col-md-2">
                                                        <input type="text" class="form-control" name="tempat"
                                                            placeholder="ex. Surabaya">
                                                    </div>
                                                    <div class="col-md-3">
                                                        <input type="date" class="form-control" id="datepicker-autoclose"
                                                            name="tanggal" placeholder="mm/dd/yyyy">
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                        <div class="form-row">
                                            <div class="col-md-6 mb-3">
                                                <label>Agama</label>
                                                <select name="agama" class="form-control" id="bank_name"
                                                    onchange="bank()">
                                                    <option value="Islam">Islam</option>
                                                    <option value="Kristen">Kristen Protestan</option>
                                                    <option value="Katolik">Katolik</option>
                                                    <option value="Hindu">Hindu</option>
                                                    <option value="Budha">Budha</option>
                                                    <option value="Konghucu">Konghucu</option>
                                                </select>

                                            </div>
                                        </div>
                                        <div class="form-row">
                                            <div class="col-md-6 mb-3">
                                                <label>Status</label>
                                                <select name="status" class="form-control" id="bank_name"
                                                    onchange="bank()">
                                                    <option value="Menikah">Menikah</option>
                                                    <option value="Belum Menikah">Belum Menikah</option>
                                                </select>

                                            </div>
                                        </div>
                                        <div class="form-row">
                                            <div class="col-md-6 mb-3">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <label>Foto</label>
                                                        <input type="file" id="photo" name="profil" class="form-control">
                                                        <span class="help-block with-errors"></span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-row">
                                            <div class="col-md-6 mb-3">
                                                <label for="">KTP</label>
                                                <input type="file" id="photo" name="foto_ktp" class="form-control">
                                                <span class="help-block with-errors"></span>
                                            </div>
                                        </div>
                                        <div class="form-row">
                                            <div class="col-md-6 mb-3">
                                                <label>Sales</label>
                                                <select name="sales" required class="form-control" id="bank_name"
                                                    onchange="bank()">
                                                    <option value="" disabled>- Pilih Sales -</option>
                                                    @foreach ($sales as $s)
                                                        <option value="{{ $s->id }}">{{ $s->name }}</option>
                                                    @endforeach
                                                </select>

                                            </div>
                                        </div>
                                    </div> --}}
                                    <div class="form-row">
                                        <div class="col-md-8 mb-3">
                                            <label>Alamat</label>
                                            <textarea class="form-control" id="" name="alamat" cols="10" rows="5"
                                                placeholder="ex. Jl. Mangga 30"></textarea>
                                        </div>
                                    </div>
                                    <div class="form-row">
                                        <div class="col-md-8 mb-3">
                                            <label>Alamat Sesuai NPWP</label>
                                            <textarea class="form-control" id="" name="alamat_npwp" cols="10" rows="5"
                                                placeholder="ex. Jl. Mangga 30"></textarea>
                                        </div>
                                    </div>
                                    <div class="form-row">
                                        <div class="col-md-8 mb-3">
                                            <label>Flag</label>
                                            <br>
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <input type="radio" name="flag" id="customer_proyek" value="Proyek"
                                                        checked><label for="customer_proyek"> &nbsp;Customer Proyek</label>
                                                </div>
                                                <div class="col-md-4">
                                                    <input type="radio" name="flag" id="customer_trading"
                                                        value="Trading"><label for="customer_trading"> &nbsp;Customer
                                                        Trading</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row" hidden>
                                        <div class="col-md-1"></div>
                                        <div class="col-md-6">
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <label>RT / RW</label>
                                                    <div class="row">
                                                        <div class="col-md-2">
                                                            <input type="text" class="form-control" placeholder="RT"
                                                                name="rt">
                                                        </div>
                                                        <div class="col-md-3">
                                                            <input type="text" class="form-control" placeholder="RW"
                                                                name="rw">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <label>Desa / Kelurahan</label>
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <input type="text" class="form-control" name="desa"
                                                                placeholder="ex. Gayungsari">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <label>Kecamatan</label>
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <input type="text" class="form-control" name="kecamatan"
                                                                placeholder="ex. Sumbersari">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <label>Kota / Kabupaten</label>
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <input type="text" class="form-control" name="kabupaten"
                                                                placeholder="ex. Pasuruan">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <button class="btn btn-primary mt-4" type="submit">Submit</button>
                                    <a href="{{ URL::to('customer') }}" class="btn btn-danger mt-4">Batal</a>
                                </form>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('js-content')
    <!-- This Page JS -->
    <script src="{!! asset('public/theme/assets/libs/moment/moment.js') !!}"></script>
    <script src="{!! asset('public/theme/assets/libs/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js') !!}"></script>
    <script src="{!! asset('public/theme/assets/libs/dropzone/dist/min/dropzone.min.js') !!}"></script>
    <script>
        // Date Picker
        jQuery('.mydatepicker, #datepicker, .input-group.date').datepicker();
        jQuery('#datepicker-autoclose').datepicker({
            autoclose: true,
            todayHighlight: true
        });
        jQuery('#date-range').datepicker({
            toggleActive: true
        });
        jQuery('#datepicker-inline').datepicker({
            todayHighlight: true
        });
    </script>
@endsection
