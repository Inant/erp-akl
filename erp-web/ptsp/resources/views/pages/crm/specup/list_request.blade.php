@extends('theme.default')

@section('breadcrumb')
			<div class="page-breadcrumb">
                <div class="row">
                    <div class="col-5 align-self-center">
                        <h4 class="page-title">{{$title}}</h4>
                        <div class="d-flex align-items-center">
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item active" aria-current="page">{{$current_page}}</li>
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
        @if($error['is_error'])
        <div class="col-12">
            <div class="alert alert-danger"> <i class="mdi mdi-alert-box"></i> {{ $error['error_message'] }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close"> <span aria-hidden="true">×</span> </button>
            </div>
        </div>
        @endif
        <div class="col-12">
            <div class="text-right">
                <a href="{{ URL::to($current_url.'/main/add') }}"><button class="btn btn-success btn-sm mb-2"><i class="fas fa-plus"></i>&nbsp; Entry New {{$title}}</button></a>
            </div>
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">{{$current_page}}</h4>
                    <hr>
                    <div class="table-responsive">
                        <table id="zero_config" class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th class="text-center"  width="15px">No</th>
                                    <th class="text-center">Request No</th>
                                    <th class="text-center">Site Name</th>
                                    <th class="text-center">Kavling Name</th>
                                    <th class="text-center">Requested Amount</th>
                                    <th class="text-center">Approved Amount</th>
                                    <th class="text-center">Approval Status</th>
                                    <th class="text-center" width="50px">Approval</th>
                                </tr>
                            </thead>
                            <tbody>
                            @if($objects != null)
                                @for($i = 0; $i < count($objects); $i++)
                                <tr>
                                    <td class="text-center">{{ $i+1 }}</td>
                                    <td>{{ $objects[$i]['no'] }}</td>
                                    <td>{{ $objects[$i]['site_name'][0]['name'] }}</td>
                                    <td>{{ $objects[$i]['project_name'][0]['name'] }}</td>
                                    <td class="text-right">{{ number_format($objects[$i]['amount_requested'],2,".",",") }}</td>
                                    <td class="text-right">{{ number_format($objects[$i]['amount'],2,".",",") }}</td>
                                    <td>@if( is_null($objects[$i]['is_approved'])) 
                                        @elseif( $objects[$i]['is_approved'] == true) Approved 
                                        @elseif( $objects[$i]['is_approved'] == false) Rejected 
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if( is_null($objects[$i]['is_approved'])) 
                                        <a href="{{ URL::to($current_url.'/main/approval/'.$objects[$i]['id']) }}" class="btn waves-effect waves-light btn-xs btn-warning"><i class="fas fa-pencil-alt"></i></a>
                                        @endif
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