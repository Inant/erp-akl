@extends('theme.default')

@section('breadcrumb')
			<div class="page-breadcrumb">
                <div class="row">
                    <div class="col-5 align-self-center">
                        <h4 class="page-title">Project RAB</h4>
                        <div class="d-flex align-items-center">
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item active" aria-current="page">Project RAB</li>
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
                    @if($error['is_error'])
                    <div class="col-12">
                        <div class="alert alert-danger"> <i class="mdi mdi-alert-box"></i> {{ $error['error_message'] }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close"> <span aria-hidden="true">×</span> </button>
                        </div>
                    </div>
                    @endif
                    <div class="col-12">
                        <div class="text-right">
                            <a href="{{ URL::to('rab/add') }}"><button class="btn btn-success btn-sm mb-2"><i class="fas fa-plus"></i>&nbsp; Create New Project RAB</button></a>
                        </div>
                        <div class="card">
                            <div class="card-body">
                                <h4 class="card-title">List Project RAB</h4>
                                <!-- <a href="/rab/add"><button class="btn btn-success mb-2"><i class="fas fa-plus"></i>&nbsp; Create New Project RAB</button></a> -->
                                <div class="table-responsive">
                                    <table id="zero_config" class="table table-striped table-bordered">
                                        <thead>
                                            <tr>
                                                <th class="text-center"  width="15px">No</th>
                                                <th class="text-center">RAB No</th>
                                                <th class="text-center">City</th>
                                                <th class="text-center">Site</th>
                                                <th class="text-center">Kavling</th>
                                                <th class="text-center">Nilai RAB</th>
                                                <th class="text-center">Nilai Kavling</th>
                                                <th class="text-center">Input Data Status</th>
                                                <th class="text-center" width="50px">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        @if($rab != null)
                                            @for($i = 0; $i < count($rab); $i++)
                                            <tr>
                                            
                                                <td class="text-center">{{ $i+1 }}</td>
                                                <td>{{ $rab[$i]['rab_no'] }}</td>
                                                <td>{{ $rab[$i]['site_location'] }}</td>
                                                <td>{{ $rab[$i]['site_name'] }}</td>
                                                <td>{{ $rab[$i]['project_name'] }}</td>
                                                <td class="text-right">{{ number_format($rab[$i]['rab_value'],0,",",".") }}</td>
                                                <td class="text-right">{{ number_format($rab[$i]['project_value'],0,",",".") }}</td>
                                                <td>{{ $rab[$i]['is_final'] ? 'Complete' : 'Incomplete'}}</td>
                                                <td class="text-center">
                                                <a href="{{ URL::to('rab/edit/'.$rab[$i]['rab_id']) }}" class="btn waves-effect waves-light btn-xs btn-warning"><i class="fas fa-pencil-alt"></i></a>
                                                </td>
                                            </tr>
                                            @endfor
                                        @endif
                                        </tbody>
                                        <!-- <tfoot>
                                            <tr>
                                                <th>No</th>
                                                <th>Title</th>
                                                <th>Url</th>
                                                <th>Icon</th>
                                                <th>Is Active</th>
                                                <th>Action</th>
                                            </tr>
                                        </tfoot> -->
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
</div>

@endsection