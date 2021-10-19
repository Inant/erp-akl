@extends('theme.default')

@section('breadcrumb')
<div class="page-breadcrumb">
    <div class="row">
        <div class="col-5 align-self-center">
            <h4 class="page-title">Customer</h4>
            <div class="d-flex align-items-center">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item active" aria-current="page">Customer</li>
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
                <a href="{{ URL::to('customer/add') }}"><button class="btn btn-success btn-sm mb-2"><i class="fas fa-plus"></i>&nbsp; Add new customer</button></a>
            </div>
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">List Customer</h4>
                    <div class="table-responsive">
                        <table id="zero_config" class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th class="text-center">Customer Name</th>
                                    <th class="text-center">KTP Number</th>
                                    <th class="text-center">Phone</th>
                                    <th class="text-center">Birth Place</th>
                                    <th class="text-center">Birth Date</th>
                                    <th class="text-center">Address</th>
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
            url: "{{ URL::to('customer/list') }}", //json get site
            dataType : 'json',
            success: function(response){
                arrData = response['data'];
                console.log(arrData)
                for(i = 0; i < arrData.length; i++){
                    let address = arrData[i]['address'] !== null ? arrData[i]['address'] : ''
                    address += arrData[i]['kelurahan'] !== null ? (', Kel.' + arrData[i]['kelurahan']) : ''
                    address += arrData[i]['kecamatan'] !== null ? (', Kec.' + arrData[i]['kecamatan']) : ''
                    address += arrData[i]['city'] !== null ? (', Kota' + arrData[i]['city']) : ''
                    t.row.add([
                        '<div class="text-center">'+arrData[i]['name']+'</div>',
                        '<div class="text-center">'+(arrData[i]['id_no'] !== null ? arrData[i]['id_no'] : '-')+'</div>',
                        '<div class="text-left">'+arrData[i]['phone_no']+'</div>',
                        '<div class="text-left">'+(arrData[i]['birth_place'] !== null ? arrData[i]['birth_place'] : '-')+'</div>',
                        '<div class="text-center">'+(arrData[i]['birth_date'] !== null ? arrData[i]['birth_date'] : '')+'</div>',
                        '<div class="text-center">'+address+'</div>',
                        `<div class="text-center"><a href="customer/detail/${arrData[i]['id']}"><button type="button" class="btn btn-info waves-effect waves-light btn-sm">Detail</button></a></div>`
                    ]).draw(false);
                }
            }
    });
});

</script>


@endsection