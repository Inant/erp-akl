@extends('theme.default')

@section('breadcrumb')
<div class="page-breadcrumb">
    <div class="row">
        <div class="col-5 align-self-center">
            <h4 class="page-title">Followup</h4>
            <div class="d-flex align-items-center">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item active" aria-current="page">Followup</li>
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
                    <h4 class="card-title">List Followup Customer</h4>
                    <div class="table-responsive">
                        <table id="zero_config" class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th class="text-center">Customer Name</th>
                                    <th class="text-center">Phone</th>
                                    <th class="text-center">Address</th>
                                    <th class="text-center">Sales</th>
                                    <th class="text-center">Followup Ke-</th>
                                    <th class="text-center">Action</th>
                                </tr>
                            </thead>
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

dt_detail = $('#dt_detail').DataTable();

$(document).ready(function(){
    t = $('#zero_config').DataTable();
    t.clear().draw(false);
    $.ajax({
            type: "GET",
            url: "{{ URL::to('followup/list') }}", //json get site
            dataType : 'json',
            success: function(response){
                arrData = response['data'];
                console.log(arrData)
                for(i = 0; i < arrData.length; i++){
                    t.row.add([
                        '<div class="text-left">'+arrData[i]['name']+'</div>',
                        '<div class="text-center">'+arrData[i]['phone_no']+'</div>',
                        '<div class="text-left">'+(arrData[i]['address'] !== null ? arrData[i]['address'] : '-')+'</div>',
                        '<div class="text-center">'+arrData[i]['sales_name']+'</div>',
                        '<div class="text-center">'+arrData[i]['last_followup_seq']+'</div>',
                        `<div class="text-center"><a href="followup/cust/${arrData[i]['id']}"><button type="button" class="btn btn-info waves-effect waves-light btn-sm">Detail</button></a></div>`
                    ]).draw(false);
                }
            }
    });
});

</script>


@endsection