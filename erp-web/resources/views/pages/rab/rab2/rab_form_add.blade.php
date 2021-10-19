@extends('theme.default')

@section('breadcrumb')
			<div class="page-breadcrumb">
                <div class="row">
                    <div class="col-5 align-self-center">
                        <h4 class="page-title">Project RAB</h4>
                        <div class="d-flex align-items-center">
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="{{ URL::to('rab') }}">Project RAB</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">Add</li>
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
                        <div class="card">
                            <div class="card-body">
                                <h4 class="card-title">Add Project RAB (Rencana Anggaran Biaya)</h4>
                            </div>
                            <hr>
                            <form method="POST" action="{{ URL::to('rab/add') }}" class="form-horizontal">
                            @csrf
                                <div class="card-body">
                                    <h4 class="card-title">RAB Header</h4>
                                    <div class="form-group row" hidden>
                                        <label class="col-sm-3 text-right control-label col-form-label">City</label>
                                        <div class="col-sm-9">
                                            <select name="site_location"  onchange="getSiteName(this.value);" class="form-control select2 custom-select" style="width: 100%; height:32px;">
                                                <option value="">--- Select City---</option>
                                                @if($site_locations != null)
                                                @foreach($site_locations as $site_location)
                                                <option value="{{ $site_location['id'] }}">{{ $site_location['city'] }}</option>
                                                @endforeach
                                                @endif
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-sm-3 text-right control-label col-form-label">Order No</label>
                                        <div class="col-sm-9">
                                            <select name="order_id"  onchange="getOrderNo(this.value);" class="form-control select2 custom-select" style="width: 100%; height:32px;">
                                                <option value="">--- Select Order No ---</option>
                                                @if($order_list != null)
                                                @foreach($order_list as $value)
                                                    @if($value['in_rab'] != 1)
                                                    <option value="{{ $value['id'] }}">{{ $value['order_no'].' | '.$value['spk_number'].' | '.$value['project_name'] }}</option>
                                                    @endif
                                                @endforeach
                                                @endif
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-sm-3 text-right control-label col-form-label">Kavling</label>
                                        <div class="col-sm-9">
                                            <select name="kavling_id" id="kavling_id" class="form-control select2 custom-select" style="width: 100%; height:32px;" required>
                                                <option value="">--- Select Kavling ---</option>
                                            </select>
                                        </div>
                                    </div>
                                    <!-- <div class="form-group row">
                                        <label class="col-sm-3 text-right control-label col-form-label">Product Order</label>
                                        <div class="col-sm-9">
                                            <select name="order_d_id" id="order_d_id" class="form-control select2 custom-select" onchange="getIdProject(this.value);" style="width: 100%; height:32px;">
                                                <option value="">--- Select Product ---</option>
                                            </select>
                                        </div>
                                    </div> -->
                                    <div class="form-group row" hidden>
                                        <label class="col-sm-3 text-right control-label col-form-label">Site Name</label>
                                        <div class="col-sm-9">
                                            <select id="site_name" name="site_name" onchange="getProjectName(this.value);" class="form-control select2 custom-select" style="width: 100%; height:32px;">
                                                <option value="">--- Select Site Name ---</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-sm-3 text-right control-label col-form-label">List Rab (Optional Copy Rab)</label>
                                        <div class="col-sm-9">
                                            <select id="rab_list" name="rab_list" class="form-control select2 custom-select" style="width: 100%; height:32px;">
                                                <option value="">--- Select Rab Number ---</option>
                                            </select>
                                            *isi jika ingin mengopy rab yang ada
                                        </div>
                                    </div>
                                    <div class="form-group row" hidden>
                                        <label class="col-sm-3 text-right control-label col-form-label">Kavling</label>
                                        <div class="col-sm-9">
                                            <input type="text" name="project_name" class="form-control" id="project_name" onchange="show_rab(this.value);">
                                            <!-- <select id="project_name" name="project_name" required class="form-control select2 custom-select" style="width: 100%; height:32px;" onchange="show_rab(this.value);">
                                                <option value="">--- Select Kavling ---</option>
                                            </select> -->
                                        </div>
                                    </div>
                                    <div class="form-group mb-0 text-right" style="margin-top:10px;">
                                        <button type="submit" class="btn btn-info waves-effect waves-light btn-sm">Add Project RAB</button>
                                    </div>
                                    <div id="d_rab_detail" style="display:none;">
                                        <hr/>
                                        <div class="table-responsive">
                                            <table id="zero_config" class="table table-striped table-bordered">
                                                <thead>
                                                    <tr>
                                                        <!-- <th class="text-center">Site Name</th> -->
                                                        <!-- <th class="text-center">City</th> -->
                                                        <th class="text-center">Project Name</th>
                                                        <th class="text-center">Deskripsi</th>
                                                        <th class="text-center">RAB Number</th>
                                                        <th class="text-center">Input Data Status</th>
                                                    </tr>
                                                </thead>
                                            </table>
                                        </div>
                                        <div class="form-group mb-0 text-right" style="margin-top:10px;">
                                            <button type="submit" class="btn btn-info waves-effect waves-light btn-sm">Add Project RAB</button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                
</div>

<script>
$(document).ready(function(){
    show_rab();
})
function show_rab(){
    formListRab = $('[id^=rab_list]');
    formListRab.empty();
    formListRab.append('<option value="">-- Select Rab Number --</option>');
    $.ajax({
        type: "GET",
        url: "{{ URL::to('rab/json') }}", //json get site
        dataType : 'json',
        // data:"project_id=" + value,
        success: function(response){
            arrayData=response['data'];
            for(i = 0; i < arrayData.length; i++){
                formListRab.append('<option value="'+arrayData[i]['rab_id']+'">'+arrayData[i]['rab_no']+'</option>');
            }
        }
    });

    // d_rab_detail = document.getElementById("d_rab_detail");
    // if(value != ""){
    //     d_rab_detail.style.display = "block";


    //     t = $('#zero_config').DataTable();
    //     t.clear().draw(false);
    //     $.ajax({
    //         type: "GET",
    //         url: "{{ URL::to('rab/get_rab_by_project_id') }}", //json get site
    //         dataType : 'json',
    //         data:"project_id=" + value,
    //         success: function(response){
    //             arrData = response['data'];
    //             for(i = 0; i < arrData.length; i++){
    //                 // console.log(arrData);
    //                 t.row.add([
    //                     // arrData[i]['site_name'],
    //                     // arrData[i]['site_location'],
    //                     arrData[i]['project_name'],
    //                     'Pengerjaan Produk '+arrData[i]['product_name']+' (total:'+arrData[i]['total_order']+')',
    //                     arrData[i]['rab_no'],
    //                     arrData[i]['status']
    //                 ]).draw(false);
    //             }
    //         }
    //     });
    // }
    // else
    //     d_rab_detail.style.display = "none";
}

function getOrderNo(order_id){
    formProjectName = $('[id^=project_name]');
    formProjectName.empty();
    formProjectName.append('<option value="">-- Select Project --</option>');
    $.ajax({
        type: "GET",
        url: "{{ URL::to('rab/get_project_by_order_id') }}", //json get site
        dataType : 'json',
        data:"order_id=" + order_id,
        success: function(response){
            // arrData = response['data'];
            $('[id^=project_name]').val(response['data']['id']);
            // show_rab(response['data']['id']);
            // for(i = 0; i < arrData.length; i++){
            //     formProjectName.append('<option value="'+arrData[i]['id']+'">'+arrData[i]['name']+'</option>');
            // }
        }
    });
    // formProjectName = $('[id^=project_name]');
    // formProjectName.empty();
    // formProjectName.append('<option value="">-- Select Project --</option>');
    // $.ajax({
    //     type: "GET",
    //     url: "{{ URL::to('rab/get_project_by_order_id') }}", //json get site
    //     dataType : 'json',
    //     data:"order_id=" + order_id,
    //     success: function(response){
    //         // arrData = response['data'];
    //         $('[id^=project_name]').val(response['data']['id']);
    //         // show_rab(response['data']['id']);
    //         // for(i = 0; i < arrData.length; i++){
    //         //     formProjectName.append('<option value="'+arrData[i]['id']+'">'+arrData[i]['name']+'</option>');
    //         // }
    //     }
    // });
    getOrderProduct(order_id);
    // getProjectName('');
    // show_rab($('[id^=project_name]').val());
}
function getOrderProduct(order_id){
    formKavling = $('[id^=kavling_id]');
    formKavling.empty();
    formKavling.append('<option value="">-- Select Kavling --</option>');
    $.ajax({
        type: "GET",
        url: "{{ URL::to('rab/get_kavling_by_order') }}"+'/'+order_id, //json get site
        dataType : 'json',
        success: function(response){
            arrData = response['data'];
            for(i = 0; i < arrData.length; i++){
                formKavling.append('<option value="'+arrData[i]['id']+'">'+arrData[i]['type_kavling']+'</option>');
            }
        }
    });

    // getProjectName('');
    // show_rab($('[id^=project_name]').val());
}
function getIdProject(order_id){
    console.log(order_id);
    $.ajax({
        type: "GET",
        url: "{{ URL::to('rab/get_project_by_order_id') }}", //json get site
        dataType : 'json',
        data:"order_id=" + order_id,
        success: function(response){
            // arrData = response['data'];
            $('[id^=project_name]').val(response['data']['id']);
            show_rab(response['data']['id']);
        }
    });
}
// function getSiteName(site_location_id){
//     formSiteName = $('[id^=site_name]');
//     formSiteName.empty();
//     formSiteName.append('<option value="">-- Select Site Name --</option>');
//     $.ajax({
//         type: "GET",
//         url: "{{ URL::to('rab/get_site') }}", //json get site
//         dataType : 'json',
//         data:"town_id=" + site_location_id,
//         success: function(response){
//             arrData = response['data'];
//             for(i = 0; i < arrData.length; i++){
//                 formSiteName.append('<option value="'+arrData[i]['id']+'">'+arrData[i]['name']+'</option>');
//             }
//         }
//     });

//     getProjectName('');
//     show_rab($('[id^=project_name]').val());
// }

// function getProjectName(site_id){
//     formProjectName = $('[id^=project_name]');
//     formProjectName.empty();
//     formProjectName.append('<option value="">-- Select Kavling --</option>');
//     $.ajax({
//         type: "GET",
//         url: "{{ URL::to('rab/get_project') }}", //json get site
//         dataType : 'json',
//         data:"site_id=" + site_id,
//         success: function(response){
//             arrData = response['data'];
//             for(i = 0; i < arrData.length; i++){
//                 formProjectName.append('<option value="'+arrData[i]['id']+'">'+arrData[i]['name']+'</option>');
//             }
//         }
//     });

//     show_rab($('[id^=project_name]').val());
// }
</script>

@endsection