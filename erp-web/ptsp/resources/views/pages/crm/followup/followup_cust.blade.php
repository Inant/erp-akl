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
            <div class="text-right">
                <a href="{{ URL::to('followup') }}"><button class="btn btn-warning btn-sm mb-2">Kembali</button></a>
            </div>
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Main Data Customer</h4>
                    @if ($followup != null)
                    <div class="form-group row align-items-center mb-0">
                        <label for="customer_name" class="col-sm-2 text-right control-label col-form-label">Customer Name</label>
                        <div class="col-sm-4 border-left pb-2 pt-2">
                            {{ $followup[0]['name'] }}
                        </div>
                        <label for="ktp_no" class="col-sm-2 text-right control-label col-form-label">KTP Number</label>
                        <div class="col-sm-4 border-left pb-2 pt-2">
                            {{ $followup[0]['id_no'] != null ? $followup[0]['id_no'] : '-' }}
                        </div>
                    </div>
                    <div class="form-group row align-items-center mb-0">
                        <label for="phone" class="col-sm-2 text-right control-label col-form-label">Phone Number</label>
                        <div class="col-sm-4 border-left pb-2 pt-2">
                            {{ $followup[0]['phone_no'] != null ? $followup[0]['phone_no'] : '-' }}
                        </div>
                        <label for="customer_name" class="col-sm-2 text-right control-label col-form-label">Address</label>
                        <div class="col-sm-4 border-left pb-2 pt-2">
                            {{ ($followup[0]['address'] != null ? $followup[0]['address'] : '-') . ', Kel.' . ($followup[0]['kelurahan'] != null ? $followup[0]['kelurahan'] : '-') . ', Kec.' . ($followup[0]['kecamatan'] != null ? $followup[0]['kecamatan'] : '-') . ', Kota ' . ($followup[0]['city'] != null ? $followup[0]['city'] : '-') }}
                        </div>
                    </div>
                    @endif
                </div>
                <div class="card-body">
                    @if ($followup != null)
                    <h4 class="card-title">List Followup Customer</h4>
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