@extends('theme.default')

@section('breadcrumb')
<div class="page-breadcrumb">
    <div class="row">
        <div class="col-5 align-self-center">
            <h4 class="page-title">Customer Detail</h4>
            <div class="d-flex align-items-center">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ URL::to('customer') }}">Customer</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Customer Detail</li>
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
                <a href="{{ URL::to('customer') }}"><button class="btn btn-warning btn-sm mb-2">Kembali</button></a>
            </div>
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Main Data Customer</h4>
                    @if ($customer != null)
                    <div class="form-group row align-items-center mb-0">
                        <label for="customer_name" class="col-sm-2 text-right control-label col-form-label">Customer Name</label>
                        <div class="col-sm-4 border-left pb-2 pt-2">
                            {{ $customer['name'] }}
                        </div>
                        <label for="ktp_no" class="col-sm-2 text-right control-label col-form-label">KTP Number</label>
                        <div class="col-sm-4 border-left pb-2 pt-2">
                            {{ $customer['id_no'] != null ? $customer['id_no'] : '-' }}
                        </div>
                    </div>
                    <div class="form-group row align-items-center mb-0">
                        <label for="customer_name" class="col-sm-2 text-right control-label col-form-label">Address</label>
                        <div class="col-sm-4 border-left pb-2 pt-2">
                            {{ ($customer['address'] != null ? $customer['address'] : '-') . ', Kel.' . ($customer['kelurahan'] != null ? $customer['kelurahan'] : '-') . ', Kec.' . ($customer['kecamatan'] != null ? $customer['kecamatan'] : '-') . ', Kota ' . ($customer['city'] != null ? $customer['city'] : '-') }}
                        </div>
                    </div>
                    <div class="form-group row align-items-center mb-0">
                        <label for="customer_name" class="col-sm-2 text-right control-label col-form-label">Sales Name</label>
                        <div class="col-sm-4 border-left pb-2 pt-2">
                            @php
                                echo $sales;
                            @endphp
                        </div>
                    </div>
                    <div class="form-group row align-items-center mb-0">
                        <label for="phone" class="col-sm-2 text-right control-label col-form-label">Phone Number</label>
                        <div class="col-sm-4 border-left pb-2 pt-2">
                            {{ $customer['phone_no'] != null ? $customer['phone_no'] : '-' }}
                        </div>
                        <label for="customer_name" class="col-sm-2 text-right control-label col-form-label">Birth Place & Birth Date</label>
                        <div class="col-sm-4 border-left pb-2 pt-2">
                            {{ ($customer['birth_place'] != null ? $customer['birth_place'] : '-') . ', ' . ($customer['birth_date'] != null ? $customer['birth_date'] : '-') }}
                        </div>
                    </div>
                    <div class="form-group row align-items-center mb-0">
                        <label for="religion" class="col-sm-2 text-right control-label col-form-label">Religion</label>
                        <div class="col-sm-4 border-left pb-2 pt-2">
                            {{ $customer['religion'] != null ? $customer['religion'] : '-' }}
                        </div>
                        <label for="marital_status" class="col-sm-2 text-right control-label col-form-label">Marital Status</label>
                        <div class="col-sm-4 border-left pb-2 pt-2">
                            {{ $customer['marital_status'] != null ? $customer['marital_status'] : '-' }}
                        </div>
                    </div>
                    <div class="form-group row align-items-center mb-0">
                        <label for="religion" class="col-sm-2 text-right control-label col-form-label">Profil Picture</label>
                        <div class="col-sm-4 border-left pb-2 pt-2">
                            @if ($customer['profile_picture'] != '')
                            <!--<img style="max-width:100%;" src="{!! asset('theme/assets/images/customer') !!}{{ '/'.$customer['profile_picture'] }}">-->
                            <img style="max-width:100%;" src="{!! asset('public/upload/profil') !!}{{ '/'.$customer['profile_picture'] }}">
                            @else
                            <img src="{!! asset('theme/assets/images/image-default.png') !!}">
                            @endif
                        </div>
                        <label for="marital_status" class="col-sm-2 text-right control-label col-form-label">ID Picture (KTP)</label>
                        <div class="col-sm-4 border-left pb-2 pt-2">
                            @if ($customer['id_picture'] != '')
                            <img style="max-width:100%;" src="{!! asset('public/upload/ktp') !!}{{ '/'.$customer['id_picture'] }}">
                            @else
                            <img src="{!! asset('theme/assets/images/image-default.png') !!}">
                            @endif
                        </div>
                    </div>
                    @endif
                </div>
                <!-- SPOUSE -->
                <div class="card-body">
                    <h4 class="card-title">Spouse Data</h4>
                    @if (isset($customer['spouse']['name']))
                    <div class="form-group row align-items-center mb-0">
                        <label for="customer_name" class="col-sm-2 text-right control-label col-form-label">Customer Name</label>
                        <div class="col-sm-4 border-left pb-2 pt-2">
                            {{ $customer['spouse']['name'] }}
                        </div>
                        <label for="ktp_no" class="col-sm-2 text-right control-label col-form-label">KTP Number</label>
                        <div class="col-sm-4 border-left pb-2 pt-2">
                            {{ $customer['spouse']['id_no'] != null ? $customer['spouse']['id_no'] : '-' }}
                        </div>
                    </div>
                    <div class="form-group row align-items-center mb-0">
                        <label for="customer_name" class="col-sm-2 text-right control-label col-form-label">Address</label>
                        <div class="col-sm-4 border-left pb-2 pt-2">
                            {{ ($customer['spouse']['address'] != null ? $customer['spouse']['address'] : '-') . ', Kel.' . ($customer['spouse']['kelurahan'] != null ? $customer['spouse']['kelurahan'] : '-') . ', Kec.' . ($customer['spouse']['kecamatan'] != null ? $customer['spouse']['kecamatan'] : '-') . 'Kota ' . ($customer['spouse']['city'] != null ? $customer['spouse']['city'] : '-') }}
                        </div>
                    </div>
                    <div class="form-group row align-items-center mb-0">
                        <label for="phone" class="col-sm-2 text-right control-label col-form-label">Phone Number</label>
                        <div class="col-sm-4 border-left pb-2 pt-2">
                            {{ $customer['spouse']['phone_no'] != null ? $customer['spouse']['phone_no'] : '-' }}
                        </div>
                        <label for="customer_name" class="col-sm-2 text-right control-label col-form-label">Birth Place & Birth Date</label>
                        <div class="col-sm-4 border-left pb-2 pt-2">
                            {{ ($customer['spouse']['birth_place'] != null ? $customer['spouse']['birth_place'] : '-') . ', ' . ($customer['spouse']['birth_date'] != null ? $customer['spouse']['birth_date'] : '-') }}
                        </div>
                    </div>
                    <div class="form-group row align-items-center mb-0">
                        <label for="religion" class="col-sm-2 text-right control-label col-form-label">Religion</label>
                        <div class="col-sm-4 border-left pb-2 pt-2">
                            {{ $customer['spouse']['religion'] != null ? $customer['spouse']['religion'] : '-' }}
                        </div>
                        <label for="marital_status" class="col-sm-2 text-right control-label col-form-label">Marital Status</label>
                        <div class="col-sm-4 border-left pb-2 pt-2">
                            {{ $customer['spouse']['marital_status'] != null ? $customer['spouse']['marital_status'] : '-' }}
                        </div>
                    </div>
                    @endif
                </div>
                <!-- FINANCIAL DATA -->
                <div class="card-body">
                    <h4 class="card-title">Customer Financial Data</h4>
                    @for ($i = 0; $i < count($customer['customer_financials']); $i++)
                    <div class="form-group row align-items-center mb-0">
                        <label for="customer_name" class="col-sm-2 text-right control-label col-form-label">Financial Type</label>
                        <div class="col-sm-4 border-left pb-2 pt-2">
                            {{ $customer['customer_financials'][$i]['finance_type'] }}
                        </div>
                    </div>
                    <div class="form-group row align-items-center mb-0">
                        <label for="phone" class="col-sm-2 text-right control-label col-form-label">Description</label>
                        <div class="col-sm-4 border-left pb-2 pt-2">
                            {{ $customer['customer_financials'][$i]['description'] }}
                        </div>
                    </div>
                    <div class="form-group row align-items-center mb-0">
                        <label for="religion" class="col-sm-2 text-right control-label col-form-label">Amount</label>
                        <div class="col-sm-4 border-left pb-2 pt-2">
                            {{ number_format((int)$customer['customer_financials'][$i]['amount'], 0, ',', '.')}}
                        </div>
                    </div>
                    <div class="form-group row align-items-center mb-0">
                        <label for="religion" class="col-sm-2 text-right control-label col-form-label">Frequency</label>
                        <div class="col-sm-4 border-left pb-2 pt-2">
                            {{ $customer['customer_financials'][$i]['frequency'] }}
                        </div>
                    </div>
                    <div class="form-group row align-items-center mb-0">
                        <label for="religion" class="col-sm-2 text-right control-label col-form-label">Debit/Credit</label>
                        <div class="col-sm-4 border-left pb-2 pt-2">
                            {{ $customer['customer_financials'][$i]['state'] == 'D' ? 'Debit' : 'Credit' }}
                        </div>
                    </div>
                    <hr/>
                    @endfor
                </div>
                 <!-- FINANCIAL DATA SPOUSE-->
                 <div class="card-body">
                    <h4 class="card-title">Spouse Financial Data</h4>
                    @if (isset($customer['spouse']['customer_financials']))
                    @for ($i = 0; $i < count($customer['spouse']['customer_financials']); $i++)
                    <div class="form-group row align-items-center mb-0">
                        <label for="customer_name" class="col-sm-2 text-right control-label col-form-label">Financial Type</label>
                        <div class="col-sm-4 border-left pb-2 pt-2">
                            {{ $customer['spouse']['customer_financials'][$i]['finance_type'] }}
                        </div>
                    </div>
                    <div class="form-group row align-items-center mb-0">
                        <label for="phone" class="col-sm-2 text-right control-label col-form-label">Description</label>
                        <div class="col-sm-4 border-left pb-2 pt-2">
                            {{ $customer['spouse']['customer_financials'][$i]['description'] }}
                        </div>
                    </div>
                    <div class="form-group row align-items-center mb-0">
                        <label for="religion" class="col-sm-2 text-right control-label col-form-label">Amount</label>
                        <div class="col-sm-4 border-left pb-2 pt-2">
                            {{ number_format((int)$customer['spouse']['customer_financials'][$i]['amount'], 0, ',', '.')}}
                        </div>
                    </div>
                    <div class="form-group row align-items-center mb-0">
                        <label for="religion" class="col-sm-2 text-right control-label col-form-label">Frequency</label>
                        <div class="col-sm-4 border-left pb-2 pt-2">
                            {{ $customer['spouse']['customer_financials'][$i]['frequency'] }}
                        </div>
                    </div>
                    <div class="form-group row align-items-center mb-0">
                        <label for="religion" class="col-sm-2 text-right control-label col-form-label">Debit/Credit</label>
                        <div class="col-sm-4 border-left pb-2 pt-2">
                            {{ $customer['spouse']['customer_financials'][$i]['state'] == 'D' ? 'Debit' : 'Credit' }}
                        </div>
                    </div>
                    <hr/>
                    @endfor
                    @endif
                </div>
                <!-- List FollowUp-->
                 <div class="card-body">
                    <h4 class="card-title">List Followup Customer</h4>
                    @if ($followup != null)
                    <div class="table-responsive">
                        <table id="zero_config" class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th class="text-center">Followup Sequence</th>
                                    <th class="text-center">Followup Date</th>
                                    <th class="text-center">Prospect</th>
                                    <th class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @for ($i = 0; $i < count($followup); $i++)
                                <tr>
                                    <td class="text-left">Followup ke-{{ $i + 1 }}</td>
                                    <td class="text-left">{{ $followup[$i]['followup_schedule'] }}</td>
                                    <td class="text-left">{{ $followup[$i]['prospect_result'] != null ? $followup[$i]['prospect_result'] : '-' }}</td>
                                    <td class="text-center"><a href="{{ URL::to('followup/detail/'.$followup[$i]['followup_history_id']) }}"><button type="button" class="btn btn-info waves-effect waves-light btn-sm">Detail</button></a></td>
                                </tr>
                                @endfor
                            </tbody>
                        </table>
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