@extends('theme.default')

@section('breadcrumb')
    <div class="page-breadcrumb">
        <div class="row">
            <div class="col-5 align-self-center">
                <h4 class="page-title">Dashboard</h4>
                <div class="d-flex align-items-center">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item active" aria-current="page"></li>
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
            @for($i = 0; $i < count($data); $i++)
            <div class="card">
                <div class="card-body" style="padding-bottom:0">
                    <h4 style="text-align:center">"{{ $data[$i]->name }}"</h4>
                    @php $date=date('d-m-Y', strtotime($data[$i]->created_at)) @endphp
                    <p style="float:right">{{ $date }}, {{ $data[$i]->username}}</p>
                </div>
            </div>
            @endfor
        </div>
    </div>                
</div>

<script src="{!! asset('theme/assets/libs/jquery/dist/jquery.min.js') !!}"></script>
<script src="{!! asset('theme/assets/extra-libs/DataTables/datatables.min.js') !!}"></script>

@endsection