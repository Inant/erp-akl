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
                                    <th class="text-center">No</th>
                                    <th class="text-center">PPJB No</th>
                                    <th class="text-center">Customer Name</th>
                                    <th class="text-center">Sales person Name</th>
                                    <th class="text-center">Kavling</th>
                                    <th class="text-center">Transaction Value</th>
                                    <!-- <th class="text-center">Is Validated</th>
                                    <th class="text-center">Is Printed</th> -->
                                    <!-- <th class="text-center">Edit</th> -->
                                    <th class="text-center">Print</th>
                                    <!-- <th class="text-center">Validate</th> -->
                                </tr>
                            </thead>
                            <tbody>
                            @if($ppjb != null)
                                @for($i = 0; $i < count($ppjb); $i++)
                                <tr>
                                    <td class="text-center">{{ $i+1 }}</td>
                                    <td>{{$ppjb[$i]['no'] }}</td>
                                    <td>{{$ppjb[$i]['customer_name']}}</td>
                                    <td>{{$ppjb[$i]['sales_person_name']}}</td>
                                    <td>{{$ppjb[$i]['kavling_name']}}</td>
                                    <td class="text-right">{{number_format($ppjb[$i]['total_amount'],2,".",",")}}</td>
                                    <!-- <td>@if( $ppjb[$i]['is_validated'] ==1) VALIDATED @endif </td>
                                    <td>@if( $ppjb[$i]['is_printed'] ==1) PRINTED @endif </td> -->
                                    <!-- <td class="text-center">
                                        <a href="{{ URL::to($current_url.'/main/edit/'.$ppjb[$i]['id']) }}" class="btn waves-effect waves-light btn-xs btn-warning"><i class="fas fa-pencil-alt"></i></a>
                                    </td> -->
                                    <td class="text-center">
                                    <a href="{{ URL::to($current_url.'/print/'.$ppjb[$i]['id']) }}" target="_blank" class="btn waves-effect waves-light btn-xs btn-info"><i class="fas fa-print"></i></a>
                                    </td>
                                    <!-- <td class="text-center">
                                        <a href="{{ URL::to($current_url.'/finance/edit/'.$ppjb[$i]['id']) }}" class="btn waves-effect waves-light btn-xs btn-warning"><i class="fas fa-pencil-alt"></i></a>
                                    </td> -->
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

<script src="{!! asset('theme/assets/libs/jquery/dist/jquery.min.js') !!}"></script>
<script src="{!! asset('theme/assets/extra-libs/DataTables/datatables.min.js') !!}"></script>
<script>
    
function clickPrint() {
    window.open("{{ URL::to('ppjbrecord/print/1') }}", '_blank');
}
</script>
@endsection
