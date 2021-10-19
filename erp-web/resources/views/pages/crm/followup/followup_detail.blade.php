@extends('theme.default')

@section('breadcrumb')
<div class="page-breadcrumb">
    <div class="row">
        <div class="col-5 align-self-center">
            <h4 class="page-title">Followup Customer Detail</h4>
            <div class="d-flex align-items-center">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ URL::to('followup') }}">Followup</a></li>
                        <li class="breadcrumb-item"><a href="{{ URL::to('followup/cust/') . '/' . $followup_detail['customer_id'] }}">Followup Customer</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Followup Customer Detail</li>
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
            <div class="text-right">
                <a href="{{ URL::to('followup/cust/') . '/' . $followup_detail['customer_id'] }}"><button class="btn btn-warning btn-sm mb-2">Kembali</button></a>
            </div>
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Main Data Customer</h4>
                    @if ($followup_detail != null)
                    <div class="form-group row align-items-center mb-0">
                        <label for="customer_name" class="col-sm-2 text-right control-label col-form-label">Customer Name</label>
                        <div class="col-sm-4 border-left pb-2 pt-2">
                            {{ $followup_detail['customer']['name'] }}
                        </div>
                        <label for="ktp_no" class="col-sm-2 text-right control-label col-form-label">KTP Number</label>
                        <div class="col-sm-4 border-left pb-2 pt-2">
                            {{ $followup_detail['customer']['id_no'] != null ? $followup_detail['customer']['id_no'] : '-' }}
                        </div>
                    </div>
                    <div class="form-group row align-items-center mb-0">
                        <label for="phone" class="col-sm-2 text-right control-label col-form-label">Phone Number</label>
                        <div class="col-sm-4 border-left pb-2 pt-2">
                            {{ $followup_detail['customer']['phone_no'] != null ? $followup_detail['customer']['phone_no'] : '-' }}
                        </div>
                        <label for="customer_name" class="col-sm-2 text-right control-label col-form-label">Address</label>
                        <div class="col-sm-4 border-left pb-2 pt-2">
                            {{ ($followup_detail['customer']['address'] != null ? $followup_detail['customer']['address'] : '-') . ', Kel.' . ($followup_detail['customer']['kelurahan'] != null ? $followup_detail['customer']['kelurahan'] : '-') . ', Kec.' . ($followup_detail['customer']['kecamatan'] != null ? $followup_detail['customer']['kecamatan'] : '-') . ', Kota ' . ($followup_detail['customer']['city'] != null ? $followup_detail['customer']['city'] : '-') }}
                        </div>
                    </div>
                    @endif
                </div>
                <div class="card-body">
                    <h4 class="card-title">Followup Detail</h4>
                    @if ($followup_detail != null)
                    <div class="form-group row align-items-center mb-0">
                        <label for="customer_name" class="col-sm-2 text-right control-label col-form-label">Followup Schedule</label>
                        <div class="col-sm-4 border-left pb-2 pt-2">
                            {{ $followup_detail['followup_schedule'] != null ? $followup_detail['followup_schedule'] : '-' }}
                        </div>
                        <label for="ktp_no" class="col-sm-2 text-right control-label col-form-label">Followup Result</label>
                        <div class="col-sm-4 border-left pb-2 pt-2">
                            {{ $followup_detail['followup_result'] != null ? $followup_detail['followup_result'] : '-' }}
                        </div>
                    </div>
                    <div class="form-group row align-items-center mb-0">
                        <label for="phone" class="col-sm-2 text-right control-label col-form-label">Kavling</label>
                        <div class="col-sm-4 border-left pb-2 pt-2">
                            {{ $followup_detail['project'] != null ? $followup_detail['project']['name'] : '-' }}
                        </div>
                        <label for="customer_name" class="col-sm-2 text-right control-label col-form-label">Budget Buy</label>
                        <div class="col-sm-4 border-left pb-2 pt-2">
                            {{ ($followup_detail['customer_budget'] != null ? $followup_detail['customer_budget'] : '-') }}
                        </div>
                    </div>
                    <div class="form-group row align-items-center mb-0">
                        <label for="phone" class="col-sm-2 text-right control-label col-form-label">Source Info</label>
                        <div class="col-sm-4 border-left pb-2 pt-2">
                            {{ $followup_detail['info_source'] != null ? $followup_detail['info_source'] : '-' }}
                        </div>
                        <label for="customer_name" class="col-sm-2 text-right control-label col-form-label">Notes</label>
                        <div class="col-sm-4 border-left pb-2 pt-2">
                            {{ ($followup_detail['notes'] != null ? $followup_detail['notes'] : '-') }}
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>                
</div>

<script src="{!! asset('theme/assets/libs/jquery/dist/jquery.min.js') !!}"></script>
<script src="{!! asset('theme/assets/extra-libs/DataTables/datatables.min.js') !!}"></script>

@endsection