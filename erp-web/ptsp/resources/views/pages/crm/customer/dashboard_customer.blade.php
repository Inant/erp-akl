@extends('theme.default')

@section('breadcrumb')
<div class="page-breadcrumb">
    <div class="row">
        <div class="col-5 align-self-center">
            <h4 class="page-title">Detail Prospect Customer</h4>
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
<style type="text/css">
    .paragraph{
        font-size:32px;
        font-style: bold;
    }
    .target{
        text-align: center;
        font-size:60px;
        font-style: bold;
        color: #fefefe;
    }
    .divide{
        width:20%
    }
    #jumbo{
        border-radius:50%; 
        background-color:#59ca81;
        color: #fefefe;
        height: 230px;
        width: 230px;
    }
    #jumbo p{
        text-align: center;
        margin: 0;
        padding: 0;
        text-transform: uppercase;
        position: relative;
        top: 50%;
        font-size:60px;
        font-style: bold;
        color: #fefefe;
        transform:translateY(-50%);
    }
    .jumbotron{
        padding: 30px 10px;
    }
    .jumbotron p{
        font-style: bold;
        color: #4a4848;
    }
</style>
<div class="container-fluid">
    <!-- basic table -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row" style="text-align:center;">
                        <div class="col-sm-3">
                        </div>
                        <div class="col-sm-3">
                        <a href="" onclick="doShowDetail();" data-toggle="modal" data-target="#modalShowDetail" style="color:#000">
                            <center>
                            <h4 class="card-title">Konsumen Hari ini</h4>
                            <div id="jumbo">
                                <p>{{$cust_today}}</p>
                            </div>
                            <br>
                            <p>Target : 0 Orang (%)</p>
                            </center>
                        </a>
                        </div>
                        <div class="col-sm-3">
                        <a href="" onclick="doShowDetail2();" data-toggle="modal" data-target="#modalShowDetail" style="color:#000">
                        <center>                            
                            <h4 class="card-title">Konsumen Bulan ini</h4>
                            <div id="jumbo">
                                <p>{{$cust_month}}</p>
                            </div>
                            <br>
                            <p>Target : 0 Orang (%)</p>
                        </center>
                        </a>
                        </div>
                        <div class="col-sm-3"></div>
                    </div>
                    <h4 class="card-title">Jumlah Prospect</h4>
                    <div class="row" style="text-align:center; margin:3px">
                        <div class="divide">
                        <a href="" onclick="doShowCount('Low', '{{$count['low']}}');" data-toggle="modal" data-target="#modalShowCntCstr">
                            <div class="jumbotron" style="background-color:#b0e2a5">
                            <p>LOW </p><p class="paragraph">{{$low}}</p>
                            </div>
                        </a>
                        </div>
                        <div class="divide">
                        <a href="" onclick="doShowCount('Medium', '{{$count['medium']}}');" data-toggle="modal" data-target="#modalShowCntCstr">
                            <div class="jumbotron" style="background-color:#e2dda5">
                            <p>MEDIUM </p><p class="paragraph">{{$medium}}</p>
                            </div>
                        </a>
                        </div>
                        <div class="divide">
                        <a href="" onclick="doShowCount('Hot', '{{$count['hot']}}');" data-toggle="modal" data-target="#modalShowCntCstr">
                            <div class="jumbotron" style="background-color:#e1a5a5">
                            <p>HOT </p><p class="paragraph">{{$hot}}</p>
                            </div>
                        </a>
                        </div>
                        <div class="divide">
                        <a href="" onclick="doShowCount('SPU', '{{$count['spu']}}');" data-toggle="modal" data-target="#modalShowCntCstr">
                            <div class="jumbotron" style="background-color:#e1b6a5">
                            <p>SPU </p><p class="paragraph">{{$spu}}</p>
                            </div>
                        </a>
                        </div>
                        <div class="divide">
                        <a href="" onclick="doShowCount('PPJB', '{{$count['ppjb']}}');" data-toggle="modal" data-target="#modalShowCntCstr">
                            <div class="jumbotron" style="background-color:#a6d1e2">
                            <p>PPJB </p><p class="paragraph">{{$ppjb}}</p>
                            </div>
                        </a>
                        </div>
                    </div>
                    <h4 class="card-title">List Prospect Sales</h4>
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th class="text-center">Nama Sales</th>
                                    <th class="text-center">LOW</th>
                                    <th class="text-center">MEDIUM</th>
                                    <th class="text-center">HOT</th>
                                    <th class="text-center">SPU</th>
                                    <th class="text-center">PPJB</th>
                                </tr>
                            </thead>
                            <tbody class="text-center">
                            @for($i=0; $i < count($employee); $i++)
                                <tr>
                                    <td>{{$employee[$i]['nama']}}</td>
                                    @if($employee[$i]['low'] != 0)
                                    <td><a href="" onclick="showProspect('{{$employee[$i]['id']}}', '{{$employee[$i]['nama']}}', 'Low', '{{$employee[$i]['idlow']}}')" data-toggle="modal" data-target="#modalShowProspect">{{$employee[$i]['low']}}</a></td>
                                    @else
                                    <td>{{$employee[$i]['low']}}</td>
                                    @endif

                                    @if($employee[$i]['medium'] != 0)
                                    <td><a href="" onclick="showProspect('{{$employee[$i]['id']}}', '{{$employee[$i]['nama']}}', 'Medium', '{{$employee[$i]['idmedium']}}')" data-toggle="modal" data-target="#modalShowProspect">{{$employee[$i]['medium']}}</a></td>
                                    @else
                                    <td>{{$employee[$i]['medium']}}</td>
                                    @endif

                                    @if($employee[$i]['hot'] != 0)
                                    <td><a href="" onclick="showProspect('{{$employee[$i]['id']}}', '{{$employee[$i]['nama']}}', 'Hot', '{{$employee[$i]['idhot']}}')" data-toggle="modal" data-target="#modalShowProspect">{{$employee[$i]['hot']}}</a></td>
                                    @else
                                    <td>{{$employee[$i]['hot']}}</td>
                                    @endif

                                    @if($employee[$i]['spu'] != 0)
                                    <td><a href="" onclick="showProspect('{{$employee[$i]['id']}}', '{{$employee[$i]['nama']}}', 'Spu', '{{$employee[$i]['idspu']}}')" data-toggle="modal" data-target="#modalShowProspect">{{$employee[$i]['spu']}}</a></td>
                                    @else
                                    <td>{{$employee[$i]['spu']}}</td>
                                    @endif
                                    
                                    @if($employee[$i]['ppjb'] != 0)
                                    <td><a href="" onclick="showProspect('{{$employee[$i]['id']}}', '{{$employee[$i]['nama']}}', 'PPJB', '{{$employee[$i]['idppjb']}}')" data-toggle="modal" data-target="#modalShowProspect">{{$employee[$i]['ppjb']}}</a></td>
                                    @else
                                    <td>{{$employee[$i]['ppjb']}}</td>
                                    @endif
                                </tr>
                            @endfor
                            </tbody>
                        </table>
                    </div>
                    <h4 class="card-title mt-5">List Customer Follow Up</h4>
                    @for ($i = 0; $i < count($followup); $i++)
                    <div class="row">
                        <div class="col-md-2"></div>
                        <div class="col-md-4">
                            <p><h4>{{ $followup[$i]['name'] }}, <strong class="text-warning">Followup Ke-{{ $followup[$i]['last_followup_seq'] }}</strong></h4></p>
                        </div>
                        <div class="col-md-6">
                            <button type="button" class="btn btn-primary" data-container="body" title="No Handphone" data-toggle="popover" data-placement="top" data-content="{{ $followup[$i]['phone_no'] }}">
                                <i class="mdi mdi-phone"></i>&nbspCall
                            </button>
                            <button type="button" class="btn btn-info" data-container="body" title="No. WhatsApp" data-toggle="popover" data-placement="top" data-content="{{ $followup[$i]['phone_no'] }}">
                                <i class="mdi mdi-whatsapp"></i>&nbspWA
                            </button>
                        </div>
                    </div>
                    @endfor
                </div>
            </div>
        </div>
    </div>                
</div>
<div class="modal fade" id="modalShowDetail" tabindex="-1" role="dialog" aria-labelledby="modalShowDetailLabel1">
    <div class="modal-dialog  modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="titleSum"></h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table id="dt_detail" class="table table-bordered">
                        <thead>
                            <tr>
                                <th class="text-center">Nama</th>
                                <th class="text-center">Gender</th>
                                <th class="text-center">Alamat</th>
                                <th class="text-center">No Telepon</th>
                                <th class="text-center">Kota</th>
                                <th class="text-center">Nama Sales</th>
                                <th class="text-center">Detail</th>
                            </tr>
                        </thead>                                      
                    </table>
                </div>
            </div>
            <div class="modal-footer">
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="modalShowProspect" tabindex="-1" role="dialog" aria-labelledby="modalShowDetailLabel1">
    <div class="modal-dialog  modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="titleProspect"></h4>
                <p></p>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table id="dt_detail3" class="table table-bordered">
                        <thead>
                            <tr>
                                <th class="text-center">Nama Customer</th>
                                <th class="text-center">Gender</th>
                                <th class="text-center">Alamat</th>
                                <th class="text-center">No Telepon</th>
                                <th class="text-center">Kota</th>
                                <th class="text-center">Detail</th>
                            </tr>
                        </thead>                                      
                    </table>
                </div>
            </div>
            <div class="modal-footer">
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="modalShowProspectMedium" tabindex="-1" role="dialog" aria-labelledby="modalShowDetailLabel1">
    <div class="modal-dialog  modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="titleProspectMedium"></h4>
                <p></p>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table id="dt_detail_medium" class="table table-bordered">
                        <thead>
                            <tr>
                                <th class="text-center">Nama Customer</th>
                                <th class="text-center">Nomor Telepon</th>
                                <th class="text-center">Kota</th>
                                <th class="text-center">Sumber info</th>
                                <th class="text-center">Tanggal Follow Up</th>
                                <th class="text-center">Detail</th>
                            </tr>
                        </thead>                                      
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <!-- <button type="button" onclick="saveMaterial(this.form);" class="btn btn-primary btn-sm">Save</button> -->
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="modalShowCntCstr" tabindex="-1" role="dialog" aria-labelledby="modalShowDetailLabel1">
    <div class="modal-dialog  modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="titleProspectCount"></h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table id="dt_detail4" class="table table-bordered">
                        <thead>
                            <tr>
                                <th class="text-center">Nama Customer</th>
                                <th class="text-center">Gender</th>
                                <th class="text-center">Alamat</th>
                                <th class="text-center">No Telepon</th>
                                <th class="text-center">Kota</th>
                                <th class="text-center">Nama Sales</th>
                                <th class="text-center">Detail</th>
                            </tr>
                        </thead>                                      
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <!-- <button type="button" onclick="saveMaterial(this.form);" class="btn btn-primary btn-sm">Save</button> -->
            </div>
        </div>
    </div>
</div>

<script src="{!! asset('theme/assets/libs/jquery/dist/jquery.min.js') !!}"></script>
<script src="{!! asset('theme/assets/extra-libs/DataTables/datatables.min.js') !!}"></script>
<script>
function doShowDetail(){
    dt_detail = $('#dt_detail').DataTable();
    $('#titleSum').html('List Customer Hari ini');
    dt_detail.clear().draw(false);
    $.ajax({
            type: "GET",
            url: "{{ URL::to('customer/getCustToday') }}", //json get site
            dataType : 'json',
            success: function(response){
                arrData = response['data'];
                for(i = 0; i < arrData.length; i++){
                    a = i+1;
                    dt_detail.row.add([
                        '<div class="text-center">'+arrData[i]['name']+'</div>',
                        '<div class="text-center">'+arrData[i]['gender']+'</div>',
                        '<div class="text-center">'+arrData[i]['address']+'</div>',
                        '<div class="text-center">'+arrData[i]['phone_no']+'</div>',
                        '<div class="text-center">'+arrData[i]['city']+'</div>',
                        '<div class="text-center">'+arrData[i]['nama_sales']+'</div>',
                        '<div class="text-right"><a href="{{ URL::to('customer/detail') }}'+'/'+arrData[i]['id']+'">detail</a></div>'
                    ]).draw(false);
                }
            }
    });
}
function doShowDetail2(){
    dt_detail = $('#dt_detail').DataTable();
    dt_detail.clear().draw(false);
    $('#titleSum').html('List Customer Bulan ini');
    $.ajax({
            type: "GET",
            url: "{{ URL::to('customer/getCustMonth') }}", //json get site
            dataType : 'json',
            success: function(response){
                arrData = response['data'];
                for(i = 0; i < arrData.length; i++){
                    a = i+1;
                    dt_detail.row.add([
                        '<div class="text-center">'+arrData[i]['name']+'</div>',
                        '<div class="text-center">'+arrData[i]['gender']+'</div>',
                        '<div class="text-center">'+arrData[i]['address']+'</div>',
                        '<div class="text-center">'+arrData[i]['phone_no']+'</div>',
                        '<div class="text-center">'+arrData[i]['city']+'</div>',
                        '<div class="text-center">'+arrData[i]['nama_sales']+'</div>',
                        '<div class="text-right"><a class="btn btn-sm btn-primary" href="{{ URL::to('customer/detail') }}'+'/'+arrData[i]['id']+'">detail</a></div>'
                    ]).draw(false);
                }
            }
    });
}
function showProspect(id, nama, pop, nama_cust){
    dt_detail3 = $('#dt_detail3').DataTable();
    dt_detail3.clear().draw(false);
    if (nama_cust==null) {
        nama_cust=0;
    };
    $('#titleProspect').html('List Customer '+pop+" "+nama);
    $.ajax({
            type: "GET",
            url: "{{ URL::to('customer/getFollow') }}" + "/" + id + "/"+ nama_cust, //json get site
            dataType : 'json',
            // data: {id : id, nama : nama},
            success: function(response){
                arrData = response['data'];
                for(i = 0; i < arrData.length; i++){
                    a = i+1;
                    dt_detail3.row.add([
                        
                        '<div class="text-center">'+arrData[i]['name']+'</div>',
                        '<div class="text-center">'+arrData[i]['gender']+'</div>',
                        '<div class="text-center">'+arrData[i]['address']+'</div>',
                        '<div class="text-center">'+arrData[i]['phone_no']+'</div>',
                        '<div class="text-center">'+arrData[i]['city']+'</div>',
                        '<div class="text-right"><a class="btn btn-sm btn-primary" href="{{ URL::to('customer/detail') }}'+'/'+arrData[i]['id']+'">detail</a></div>'
                    ]).draw(false);
                }
            }
    });
}
function doShowCount(nama, nama_cust){
    t = $('#dt_detail4').DataTable();
    t.clear().draw(false);
    $('#titleProspectCount').html('List Customer '+ nama);
    $.ajax({
            type: "GET",
            url: "{{ URL::to('customer/getCountCust') }}" + "/" + nama_cust, //json get site
            dataType : 'json',
            success: function(response){
                arrData = response['data'];
                for(i = 0; i < arrData.length; i++){
                    a = i+1;
                    t.row.add([
                        '<div class="text-center">'+arrData[i]['name']+'</div>',
                        '<div class="text-center">'+arrData[i]['gender']+'</div>',
                        '<div class="text-center">'+arrData[i]['address']+'</div>',
                        '<div class="text-center">'+arrData[i]['phone_no']+'</div>',
                        '<div class="text-center">'+arrData[i]['city']+'</div>',
                        '<div class="text-center">'+arrData[i]['nama_sales']+'</div>',
                        '<div class="text-right"><a class="btn btn-sm btn-primary" href="{{ URL::to('customer/detail') }}'+'/'+arrData[i]['id']+'">detail</a></div>'
                    ]).draw(false);
                }
            }
    });
}
// function showProspectDeal(id, nama, nama_cust){
//     dt_detail4 = $('#dt_detail4').DataTable();
//     dt_detail4.clear().draw(false);
//     $('#titleProspect2').html('List Customer '+nama+" "+nama_cust);
//     $.ajax({
//             type: "GET",
//             url: "{{ URL::to('customer/getDeal') }}" + "/" + id + "/"+ nama, //json get site
//             dataType : 'json',
//             // data: {id : id, nama : nama},
//             success: function(response){
//                 arrData = response['data'];
//                 for(i = 0; i < arrData.length; i++){
//                     a = i+1;
//                     dt_detail4.row.add([
//                         '<div class="text-center">'+arrData[i]['name']+'</div>',
//                         '<div class="text-center">'+arrData[i]['phone_no']+'</div>',
//                         // '<div class="text-center">'+arrData[i]['city']+'</div>',
//                         // '<div class="text-left">'+arrData[i]['owner_name']+'</div>',
//                         '<div class="text-center">'+arrData[i]['address']+'</div>',
//                         '<div class="text-center">'+arrData[i]['payment']+'</div>',
//                         '<div class="text-center">'+arrData[i]['project_name']+'</div>',
//                         '<div class="text-right"><a class="btn btn-sm btn-primary" href="{{ URL::to('customer/detail') }}'+'/'+arrData[i]['customer_id']+'">detail</a></div>'
//                     ]).draw(false);
//                 }
//             }
//     });
// }
</script>


@endsection