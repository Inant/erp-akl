@extends('theme.default')

@section('breadcrumb')
    <div class="page-breadcrumb">
        <div class="row">
            <div class="col-5 align-self-center">
                <h4 class="page-title">Master Kavling</h4>
                <div class="d-flex align-items-center">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item active" aria-current="page">Master Kavling</li>
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
            <!-- <div class="text-right">
                <a href="{{ URL::to('master_kavling/create') }}"><button class="btn btn-success btn-sm mb-2"><i class="fas fa-plus"></i>&nbsp; Create New Kavling</button></a>
            </div> -->
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">List Kavling</h4>
                    <div class="table-responsive">
                        <table id="zero_config" class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th class="text-center">Kavling</th>
                                    <th class="text-center">Customer</th>
                                    <!-- <th class="text-center">Site</th> -->
                                    <th class="text-center">Total</th>
                                    <!-- <th class="text-center">Action</th> -->
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

$(document).ready(function(){
    // console.log(arrMaterialPembelianRutin);
    t = $('#zero_config').DataTable();
    t.clear().draw(false);
    $.ajax({
        type: "GET",
        url: "{{ URL::to('master_kavling/list') }}", //json get site
        dataType : 'json',
        success: function(response){
            arrData = response['data'];
            for(i = 0; i < arrData.length; i++){
                urlEdit = "{{ URL::to('master_kavling/edit') }}" + "/" +arrData[i]['id'];
                urlDelete = "{{ URL::to('master_kavling/delete') }}" + "/" +arrData[i]['id'];
                t.row.add([
                    '<div class="text-center">'+arrData[i]['name']+'</div>',
                    '<div class="text-left">'+arrData[i]['customer']+'</div>',
                    '<div class="text-left">'+arrData[i]['amount']+'</div>',
                    // '<div class="text-right">'+formatCurrency(arrData[i]['base_price'])+'</div>',
                    // '<div class="text-center"><a href="'+urlEdit+'" class="btn waves-effect waves-light btn-xs btn-warning"><i class="fas fa-pencil-alt"></i></a> <a href="'+urlDelete+'" class="btn waves-effect waves-light btn-xs btn-danger" onclick="return confirm("Are you sure to delete item?")"><i class="fas fa-trash-alt"></i></a></div>'
                    // '<div class="text-center"><a href="'+urlEdit+'" class="btn waves-effect waves-light btn-xs btn-warning"><i class="fas fa-pencil-alt"></i></a>'
                ]).draw(false);
            }
        }
    });
});

</script>


@endsection