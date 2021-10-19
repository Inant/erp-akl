@extends('theme.default')

@section('breadcrumb')
			<div class="page-breadcrumb">
                <div class="row">
                    <div class="col-5 align-self-center">
                        <h4 class="page-title">{{$title}}</h4>
                        <!-- <div class="d-flex align-items-center">
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item active" aria-current="page">{{$current_page}}</li>
                                </ol>
                            </nav>
                        </div> -->
                    </div>
                </div>
            </div>
@endsection

@section('content')

<div class="container-fluid">
                <div class="row">
                    @if($error['is_error'])
                    <div class="col-12">
                        <div class="alert alert-danger"> <i class="mdi mdi-alert-box"></i> {{ $error['error_message'] }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close"> <span aria-hidden="true">×</span> </button>
                        </div>
                    </div>
                    @endif
                    <div class="col-12">
                        <div class="text-right">
                            <a href="{{ URL::to($header_action_url1) }}"><button class="btn btn-success btn-sm mb-2"><i class="fas fa-plus"></i>&nbsp; Entry New Customer</button></a>
                        </div>
                        <div class="card">
                            <div class="card-body">
                                <h4 class="card-title">List Project customer</h4>
                                <div class="table-responsive">
                                    <table id="zero_config" class="table table-striped table-bordered">
                                        <thead>
                                            <tr>
                                                <th class="text-center" width="15px">No</th>
                                                <th class="text-center">customer Name</th>
                                                <th class="text-center">Proespect Level</th>
                                                <th class="text-center">Address</th>
                                                <th class="text-center">Kelurahan</th>
                                                <th class="text-center">Kecamatan</th>
                                                <th class="text-center">City</th>
                                                <th class="text-center">Job</th>
                                                <th class="text-center" width="50px">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        @if($customer != null)
                                            @for($i = 0; $i < count($customer); $i++)
                                            <tr>
                                                <td class="text-center">{{ $i+1 }}</td>
                                                <td>{{ $customer[$i]['name'] }}</td>
                                                <td>{{ $customer[$i]['prospect_level'] }}</td>
                                                <td>{{ $customer[$i]['address'] }}</td>
                                                <td>{{ $customer[$i]['kelurahan'] }}</td>
                                                <td>{{ $customer[$i]['kecamatan'] }}</td>
                                                <td>{{ $customer[$i]['city'] }}</td>
                                                <td>{{ $customer[$i]['job'] }}</td>
                                                <td class="text-center">
                                                    <a href="{{ URL::to($row_action_url1.$customer[$i]['id']) }}" class="btn waves-effect waves-light btn-xs btn-warning"><i class="fas fa-pencil-alt"></i></a>
                                                </td>
                                            </tr>
                                            @endfor
                                        @endif
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
</div>

@endsection