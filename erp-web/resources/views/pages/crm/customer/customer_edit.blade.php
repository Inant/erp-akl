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
                                <form method="POST" enctype="multipart/form-data"
                                    action="{{ URL::to('customer/update/' . $customer['id']) }}">
                                    @csrf
                                    <div class="form-row">
                                        <div class="col-md-8 mb-3">
                                            <label>Nama Perusahaan</label>
                                            <input type="text" class="form-control" name="coorporate_name"
                                                value="{{ $customer['coorporate_name'] }}" id="coorporate_name"
                                                placeholder="ex. PT. Mekar Sari">
                                        </div>
                                    </div>
                                    <div class="form-row">
                                        <div class="col-md-8 mb-3">
                                            <label>Nama Penanggung Jawab</label>
                                            <input type="text" class="form-control" name="name"
                                                value="{{ $customer['name'] }}" placeholder="ex. Haryanto">
                                        </div>
                                    </div>
                                    <div class="form-row">
                                        <div class="col-md-8 mb-3">
                                            <label>Jabatan</label>
                                            <input type="text" class="form-control" name="jabatan"
                                                value="{{ $customer['jabatan'] }}" placeholder="">
                                        </div>
                                    </div>
                                    <div class="form-row">
                                        <div class="col-md-8 mb-3">
                                            <label>NPWP</label>
                                            <input type="text" class="form-control" name="npwp"
                                                value="{{ $customer['npwp'] }}" placeholder="">
                                        </div>
                                    </div>
                                    <div class="form-row">
                                        <div class="col-md-8 mb-3">
                                            <label>No. Telp / Fax</label>
                                            <input type="text" class="form-control" name="hp"
                                                value="{{ $customer['phone_no'] }}" required
                                                placeholder="ex. 082123xxxxx">
                                        </div>
                                    </div>
                                    <div class="form-row">
                                        <div class="col-md-8 mb-3">
                                            <label>No. Telp / Fax (2)</label>
                                            <input type="text" class="form-control" name="hp2"
                                                value="{{ $customer['phone_no2'] }}" placeholder="ex. 082123xxxxx">
                                        </div>
                                    </div>
                                    <div class="form-row">
                                        <div class="col-md-8 mb-3">
                                            <label>Email</label>
                                            <input type="email" class="form-control" value="{{ $customer['email'] }}"
                                                name="email">
                                        </div>
                                    </div>
                                    <div class="form-row">
                                        <div class="col-md-8 mb-3">
                                            <label>Alamat</label>
                                            <textarea class="form-control" id="" name="alamat" cols="10" rows="5"
                                                placeholder="ex. Jl. Mangga 30">{{ $customer['address'] }}</textarea>
                                        </div>
                                    </div>
                                    <div class="form-row">
                                        <div class="col-md-8 mb-3">
                                            <label>Alamat Sesuai NPWP</label>
                                            <textarea class="form-control" id="" name="alamat_npwp" cols="10" rows="5"
                                                placeholder="ex. Jl. Mangga 30">{{ $customer['npwp_address'] }}</textarea>
                                        </div>
                                    </div>
                                    <div class="form-row">
                                        <div class="col-md-8 mb-3">
                                            <label>Flag</label>
                                            <br>
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <input type="radio" name="flag" id="customer_proyek" value="Proyek"
                                                        {{$customer['flag'] == 'Proyek' ? 'checked' : ''}}><label for="customer_proyek"> &nbsp;Customer Proyek</label>
                                                </div>
                                                <div class="col-md-4">
                                                    <input type="radio" name="flag" id="customer_trading"
                                                        value="Trading" {{$customer['flag'] == 'Trading' ? 'checked' : ''}}><label for="customer_trading"> &nbsp;Customer
                                                        Trading</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-row">
                                        <div class="col-md-8 mb-3">
                                            <label>Plafond Piutang</label>
                                            <input type="number" name="plafond_piutang" id="plafond_piutang" class="form-control" value={{$customer['plafond_piutang']}}>
                                        </div>
                                    </div>
                                    <div class="form-row">
                                        <div class="col-md-8 mb-3">
                                            <label>Jumlah Tagihan Maksimal</label>
                                            <input type="number" name="jumlah_tagihan_maksimal" id="jumlah_tagihan_maksimal" class="form-control" value="{{$customer['jumlah_tagihan_maksimal']}}">
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
