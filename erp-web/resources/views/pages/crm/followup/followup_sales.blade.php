@extends('theme.default')

@section('breadcrumb')
<div class="page-breadcrumb">
    <div class="row">
        <div class="col-5 align-self-center">
            <h4 class="page-title">Followup Customer</h4>
            <div class="d-flex align-items-center">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ URL::to('followup') }}">Followup</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Followup Customer</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
</div>
@endsection

@section('content')

<div class="container-fluid">
    <!-- basic table -->
    <div class="row">
        <div class="col-12">

            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Daftar Customer Follow Up</h4>
                    @for ($i = 0; $i < count($followup); $i++)
                    <div class="row">
                        <div class="col-md-2"></div>
                        <div class="col-md-4">
                            <p><h4>{{ $followup[$i]['name'] }}, <strong class="text-warning">Followup Ke-{{ $followup[$i]['last_followup_seq'] }}</strong></h4></p>
                        </div>
                        <div class="col-md-6">
                            <button type="button" class="btn btn-primary" data-container="body" title="No Handphone" data-toggle="popover" data-placement="top" data-content="{{ $followup[$i]['phone_no'] }}">
                                <i class="mdi mdi-phone"></i>&nbspCall
                            </button>
                            <button type="button" class="btn btn-info" data-container="body" title="No. WhatsApp" data-toggle="popover" data-placement="top" data-content="{{ $followup[$i]['phone_no'] }}">
                                <i class="mdi mdi-whatsapp"></i>&nbspWA
                            </button>
                        </div>
                    </div>
                    @endfor
                </div>

            </div>
        </div>
    </div>
</div>

<script src="{!! asset('theme/assets/libs/jquery/dist/jquery.min.js') !!}"></script>
<script src="{!! asset('theme/assets/extra-libs/DataTables/datatables.min.js') !!}"></script>

@endsection
