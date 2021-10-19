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
                                    <li class="breadcrumb-item active" aria-current="page">Edit</li>
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
            <form method="POST" action="{{ URL::to('rab/edit') }}" class="form-horizontal r-separator">
            @csrf
                <!-- <div class="text-right">
                    <a href="{{ URL::to('rab') }}"><button type="button" class="btn btn-danger btn-sm mb-2">Cancel</button></a>
                    <button type="submit" class="btn btn-info btn-sm mb-2">Save</button>
                    <button type="submit" class="btn btn-primary btn-sm mb-2">Submit</button>
                </div> -->

                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Edit Project RAB (Rencana Anggaran Biaya)</h4>
                    </div>
                    <hr>
                    <div class="card-body">
                        <h4 class="card-title">RAB Header</h4>
                        <div class="form-group row align-items-center mb-0">
                            <label class="col-sm-3 text-right control-label col-form-label">Site Name</label>
                            <div class="col-9 border-left pb-2 pt-2">
                                <label class="control-label col-form-label">{{ $rab_header['site_name'] }}</label>
                            </div>
                        </div>
                        <div class="form-group row align-items-center mb-0">
                            <label class="col-sm-3 text-right control-label col-form-label">City</label>
                            <div class="col-9 border-left pb-2 pt-2">
                                <label class="control-label col-form-label">{{ $rab_header['site_location'] }}</label>
                            </div>
                        </div>
                        <div class="form-group row align-items-center mb-0">
                            <label class="col-sm-3 text-right control-label col-form-label">Kavling</label>
                            <div class="col-9 border-left pb-2 pt-2">
                                <label class="control-label col-form-label">{{ $rab_header['project_name'] }}</label>
                            </div>
                        </div>
                        <div class="form-group row align-items-center mb-0">
                            <label class="col-sm-3 text-right control-label col-form-label">RAB Number</label>
                            <div class="col-sm-6 border-left pb-2 pt-2">
                                <input type="hidden" id="rab_id" name="rab_id" value="{{ $id_rab }}">
                                <input type="text" name="rab_no" class="form-control" placeholder="RAB Number" value="{{ $rab_header['rab_no'] }}">
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="card-body">
                        <h4 class="card-title">RAB Detail</h4>
                        <div class="text-right" style="margin-bottom:10px;">
                            <button type="button" onclick="doAddWorkHeader();" data-toggle="modal" data-target="#modalAddWorkHeader" class="btn btn-success waves-effect waves-light btn-sm">Add WorkHeader</button>
                            <button type="button" onclick="doAddWorkDetail();" data-toggle="modal" data-target="#modalAddWorkDetail" class="btn btn-success waves-effect waves-light btn-sm">Add WorkDetail</button>
                            <button type="button" onclick="doAddMaterial();" data-toggle="modal" data-target="#modalAddMaterial" class="btn btn-success waves-effect waves-light btn-sm">Add Material/Resource</button>
                        </div>
                        <div class="table-responsive">
                            <table id="zero_config_no_sort" class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th class="text-center">No</th>
                                        <th class="text-center">Pekerjaan</th>
                                        <th class="text-center">Work Start</th>
                                        <th class="text-center">Length Work</th>
                                        <th class="text-center">Work End</th>
                                        <th class="text-center">Volume</th>
                                        <th class="text-center">Satuan</th>
                                        <th class="text-center">Harga Satuan (Rp.)</th>
                                        <th class="text-center">Total Harga (Rp.)</th>
                                        <th class="text-center">Action</th>
                                    </tr>
                                </thead>                                      
                            </table>
                        </div>
                        <br><br>
                        <div class="row">
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <a href="{{ URL::to('rab') }}"><button type="button" class="btn btn-danger mb-2">Cancel</button></a>
                                    <!-- <button type="submit" class="btn btn-info btn-sm mb-2">Save</button> -->
                                    <button type="submit" class="btn btn-primary mb-2">Submit</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>        
</div>


<!-- Modal -->
<div class="modal fade" id="modalAddWorkHeader" role="dialog" aria-labelledby="modalAddWorkHeaderLabel1">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form method="post" name="myForm">
            @csrf
                <div class="modal-header">
                    <h4 class="modal-title" id="modalAddWorkHeaderLabel1">Add WorkHeader</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="work-name-workHeader" class="control-label">Jenis Pekerjaan:</label>
                        <input type="hidden" id="id_rab" name="id_rab" value="{{ $id_rab }}">
                        <input type="hidden" id="project_id" name="project_id" value="{{ $rab_header['project_id'] }}">
                        <input type="text" name="work_name_workHeader" class="form-control" id="work-name-workHeader" required />
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" onclick="saveWorkHeader(this.form);" class="btn btn-primary btn-sm">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="modalAddWorkDetail" role="dialog" aria-labelledby="modalAddWorkDetailLabel1">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form method="post" name="myForm">
                @csrf
                <div class="modal-header">
                    <h4 class="modal-title" id="modalAddWorkDetailLabel1">Add WorkDetail</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="work-name-workDetail" class="control-label">Work Header:</label>
                        <select id="projectwork_name" name="projectwork_name" required required class="form-control select2 custom-select" style="width: 100%; height:32px;">
                            <option value="">--- Select WorkHeader ---</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="work-name-workDetail" class="control-label">Pekerjaan Detail:</label>
                        <input type="text" name="projectworksub_name" required class="form-control" id="projectworksub_name">
                    </div>
                    <div class="form-group">
                        <label for="work-name-workDetail" class="control-label">Volume:</label>
                        <input type="number" name="projectworksub_volume" required class="form-control" id="projectworksub_volume">
                    </div>
                    <div class="form-group">
                        <label for="work-name-workDetail" class="control-label">Satuan:</label>
                        <select id="projectworksub_unit" name="projectworksub_unit" required required class="form-control select2 custom-select" style="width: 100%; height:32px;">
                            <option value="">--- Select Unit ---</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="work-name-workDetail" class="control-label">Harga Satuan:</label>
                        <input type="number" name="projectworksub_price" required class="form-control" id="projectworksub_price">
                    </div>
                    <div class="form-group">
                        <label for="work-name-workDetail" class="control-label">Mulai Pekerjaan:</label>
                        <input type="date" onchange="handleStartDate(this)" name="projectworksub_workstart" min="{{ date('Y-m-d') }}" required class="form-control" id="projectworksub_workstart">
                    </div>
                    <div class="form-group">
                        <label for="work-name-workDetail" class="control-label">Durasi:</label>
                        <input type="number" onkeyup="handleDurasi(this)" name="projectworksub_durasi" required class="form-control" id="projectworksub_durasi">
                    </div>
                    <div class="form-group">
                        <label for="work-name-workDetail" class="control-label">Akhir Pekerjaan:</label>
                        <input type="date" readonly name="projectworksub_workend" min="{{ date('Y-m-d') }}" required class="form-control" id="projectworksub_workend">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" onclick="saveWorkDetail(this.form);" class="btn btn-primary btn-sm">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="modalAddMaterial" role="dialog" aria-labelledby="modalAddMaterialLabel1">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form method="post" name="myForm">
            @csrf
                <div class="modal-header">
                    <h4 class="modal-title" id="modalAddMaterialLabel1">Material / Upah Kerja / Sewa Alat</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label class="control-label">Pekerjaan Detail:</label>
                        <select id="material_worksubname" name="material_worksubname" required required class="form-control select2 custom-select" style="width: 100%; height:32px;">
                            <option value="">--- Select Work Detail ---</option>
                        </select>
                    </div>
                    <!-- <div class="form-group">
                        <label class="control-label">Type:</label>
                        <select id="material_type" name="material_type" required required class="form-control select2 custom-select" onchange="handleType(this.value);" style="width: 100%; height:32px;">
                            <option value="">--- Select Type ---</option>
                            <option value="1">Material Habis Pakai</option>
                            <option value="2">Alat Kerja Habis Pakai</option>
                            <option value="3">Upah Kerja</option>
                            <option value="4">Sewa Alat Kerja</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="control-label">Category:</label>
                        <select id="material_category" name="material_category" required required class="form-control select2 custom-select" onchange="handleCategory(this.value);" style="width: 100%; height:32px;">
                            <option value="">--- Select Category ---</option>
                        </select>
                    </div> -->
                    <div class="form-group">
                        <label class="control-label">Nomor Material:</label>
                        <input type="text" name="m_item_no" required class="form-control" id="m_item_no" onchange="handleMaterialNo(this)">
                    </div>
                    <div class="form-group">
                        <label class="control-label">Material:</label>
                        <select id="material_name" name="material_name" required required class="form-control custom-select" onchange="handleMaterial(this.value);" style="width: 100%; height:32px;">
                            <option value="">--- Select Material ---</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="control-label">Satuan:</label>
                        <input type="hidden" name="material_unit" required class="form-control" readonly id="material_unit">
                        <input type="text" name="material_unit_text" required class="form-control" readonly id="material_unit_text">
                    </div>
                    <div class="form-group">
                        <label class="control-label">Volume:</label>
                        <input type="number" name="material_volume" required class="form-control" id="material_volume">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" onclick="saveMaterial(this.form);" class="btn btn-primary btn-sm">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>
<div class="modal fade" id="modalEditMaterial" role="dialog" aria-labelledby="modalAddMaterialLabel1">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form method="post" name="myForm">
            @csrf
                <div class="modal-header">
                    <h4 class="modal-title" id="modalAddMaterialLabel1">Edit Material / Upah Kerja / Sewa Alat</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label class="control-label">Pekerjaan Detail:</label>
                        <input type="text" required class="form-control" id="material_worksubnames" readonly="">
                        <input type="hidden" required class="form-control" id="material_worksubds_id" readonly="" name="material_worksub_name">
                    </div>
                    <div class="form-group">
                        <label class="control-label">Nomor Material:</label>
                        <input type="text" name="m_item_no" required class="form-control" id="m_item_no" onchange="handleMaterialNos(this)">
                    </div>
                    <div class="form-group">
                        <label class="control-label">Material:</label>
                        <select id="material_names" name="material_names" required required class="form-control custom-select" onchange="handleMaterials(this.value);" style="width: 100%; height:32px;">
                            <option value="">--- Select Material ---</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="control-label">Satuan:</label>
                        <input type="hidden" name="material_units" required class="form-control" readonly id="material_units">
                        <input type="text" name="material_unit_texts" required class="form-control" readonly id="material_unit_texts">
                    </div>
                    <div class="form-group">
                        <label class="control-label">Volume:</label>
                        <input type="number" name="material_volumes" required class="form-control" id="material_volumes">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" onclick="saveEditMaterial(this.form);" class="btn btn-primary btn-sm">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="modalShowMaterial" tabindex="-1" role="dialog" aria-labelledby="modalAddMaterialLabel1">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="modalAddMaterialLabel1">Show Material</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table id="zero_config" class="table table-bordered">
                        <thead>
                            <tr>
                                <th class="text-center">Nama Material</th>
                                <th class="text-center">Volume</th>
                                <th class="text-center">Satuan</th>
                                <th class="text-center">Edit</th>
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

<div class="modal fade" id="modalShowEditWork" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
        <form method="post" name="myForm">
            @csrf
            <div class="modal-header">
                <h4 class="modal-title" id="modalAddMaterialLabel1">Edit Jangka Pekerjaan</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                
                    <input name="projectworksub_id" readonly="" class="form-control" id="id_projectworksub" type="hidden">
                
                <div class="form-group">
                    <label for="work-name-workDetail" class="control-label">Mulai Pekerjaan:</label>
                    <input type="date" onchange="handleStartDate(this)" name="projectworksub_workstarts" min="{{ date('Y-m-d') }}" required class="form-control" id="projectworksub_workstarts">
                </div>
                <div class="form-group">
                    <label for="work-name-workDetail" class="control-label">Durasi:</label>
                    <input type="number" onkeyup="handleDurasi2(this)" name="projectworksub_durasi" required class="form-control" id="projectworksub_durasi">
                </div>
                <div class="form-group">
                    <label for="work-name-workDetail" class="control-label">Akhir Pekerjaan:</label>
                    <input type="date" readonly name="projectworksub_workends" min="{{ date('Y-m-d') }}" required class="form-control" id="projectworksub_workends">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" onclick="editLengthWork(this.form);" class="btn btn-primary btn-sm">Save</button>
            </div>
        </form>
        </div>
    </div>
</div>
<!-- End Modal -->

<script src="{!! asset('theme/assets/libs/jquery/dist/jquery.min.js') !!}"></script>
<script>

listMaterial = [];

$(document).ready(function(){
    listProjectWork();
});

function doAddWorkHeader(){
    $('[id^=work-name-workHeader]').val('');
}

function doAddWorkDetail(){
    $('[id^=projectworksub_name]').val('');
    $('[id^=projectworksub_volume]').val('');
    $('[id^=projectworksub_unit]').val('');
    $('[id^=projectworksub_price]').val('');

    formProjectworkName = $('[id^=projectwork_name]');
    formProjectworkName.empty();
    formProjectworkName.append('<option value="">-- Select Work Header --</option>');
    $.ajax({
        type: "GET",
        url: "{{ URL::to('rab/get_project_work_by_rab_id/' . $id_rab) }}", //json get site
        dataType : 'json',
        success: function(response){
            arrData = response['data']['project_works'];
            for(i = 0; i < arrData.length; i++){
                formProjectworkName.append('<option value="'+arrData[i]['id']+'">'+arrData[i]['name']+'</option>');
            }
        }
    });

    formUnit = $('[id^=projectworksub_unit]');
    formUnit.empty();
    formUnit.append('<option value="">-- Select Unit --</option>');
    $.ajax({
        type: "GET",
        url: "{{ URL::to('rab/get_all_m_unit') }}", //json get site
        dataType : 'json',
        success: function(response){
            arrData = response['data'];
            for(i = 0; i < arrData.length; i++){
                formUnit.append('<option value="'+arrData[i]['id']+'">'+arrData[i]['name']+'</option>');
            }
        }
    });

}

function doAddMaterial(){
    $('[id^=material_volume]').val('');
    $('[id^=m_item_no]').val('');

    formWorkSubName = $('[id^=material_worksubname]');
    formWorkSubName.empty();
    formWorkSubName.append('<option value="">-- Select Work Detail --</option>');
    $.ajax({
        type: "GET",
        url: "{{ URL::to('rab/get_project_work_by_rab_id/' . $id_rab) }}", //json get site
        dataType : 'json',
        success: function(response){
            arrData = response['data']['project_works'];
            for(i = 0; i < arrData.length; i++){
                formWorkSubName.append('<option disabled value="">'+arrData[i]['name']+'</option>');
                arrProjectWorksub = arrData[i]['project_worksubs'];
                for(j = 0; j < arrProjectWorksub.length; j++){
                    formWorkSubName.append('<option value="'+arrProjectWorksub[j]['id']+'"> -- '+arrProjectWorksub[j]['name']+'</option>');
                }
            }
        }
    });

    formMaterial = $('[id^=material_name]');
    formMaterial.empty();
    formMaterial.append('<option value="">-- Select Material --</option>');
    $.ajax({
        type: "GET",
        url: "{{ URL::to('material_request/get_material') }}", 
        dataType : 'json',
        success: function(response){
            arrData = response['data'];
            listMaterial = arrData.map((item, obj) => {
                return {
                    id: item.id,
                    name: item.name,
                    m_unit_id: item.m_unit_id,
                    m_unit_name: item.m_unit_name,
                    no: item.no
                }
            });
            for(i = 0; i < arrData.length; i++){
                formMaterial.append('<option value="'+arrData[i]['id']+'">'+arrData[i]['name']+'</option>');
            }
        }
    });
}

function handleMaterial(value){
    unitId = '';
    unitName = '';
    itemNo = '';
    listMaterial.map((item, obj) => {
        if(item.id == value) {
            console.log(item)
            unitId = item.m_unit_id;
            unitName = item.m_unit_name;
            itemNo = item.no;
        }
    });

    $('[id^=material_unit]').val(unitId);
    $('[id^=material_unit_text]').val(unitName);
    $('[id^=m_item_no]').val(itemNo);
}
function handleMaterials(value){
    unitId = '';
    unitName = '';
    itemNo = '';
    listMaterial.map((item, obj) => {
        if(item.id == value) {
            console.log(item)
            unitId = item.m_unit_id;
            unitName = item.m_unit_name;
            itemNo = item.no;
        }
    });

    $('[id^=material_units]').val(unitId);
    $('[id^=material_unit_texts]').val(unitName);
    $('[id^=m_item_no]').val(itemNo);
}

function handleCategory(value) {
    formMaterial = $('[id^=material_name]');
    formMaterial.empty();
    formMaterial.append('<option value="">-- Select Material --</option>');
    $.ajax({
        type: "GET",
        url: "{{ URL::to('rab/get_all_m_item') }}", 
        dataType : 'json',
        success: function(response){
            arrData = response['data'];
            listMaterial = arrData.map((item, obj) => {
                return {
                    id: item.id,
                    name: item.name,
                    m_unit_id: item.m_unit_id,
                    m_unit_name: item.m_unit_name
                }
            });
            for(i = 0; i < arrData.length; i++){
                formMaterial.append('<option value="'+arrData[i]['id']+'">'+arrData[i]['name']+'</option>');
            }
        }
    });
}

function handleType(value){
    formCategory = $('[id^=material_category]');
    formCategory.empty();
    formCategory.append('<option value="">-- Select Category --</option>');
    $.ajax({
        type: "GET",
        url: "{{ URL::to('rab/get_category') }}", //json get site
        data: "type=" + value,
        dataType : 'json',
        success: function(response){
            arrData = response['data'];
            for(i = 0; i < arrData.length; i++){
                formCategory.append('<option value="'+arrData[i]['category']+'">'+arrData[i]['category']+'</option>');
            }
        }
    });

    handleCategory('');
}

function saveWorkHeader(f){    
    CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
    id_rab = f.id_rab.value;
    project_id = f.project_id.value;
    project_work_name = f.work_name_workHeader.value;

    if(project_work_name != ''){
        $.ajax({
            url: "{{ URL::to('rab/save_project_work') }}",
            type: 'POST',
            data: {_token: CSRF_TOKEN, id_rab: id_rab, project_id: project_id, project_work_name: project_work_name },
            dataType: 'JSON',
            success: function (data) { 
                // $(".writeinfo").append(data.msg); 
                // alert(data.msg);
            }
        });
        $("#modalAddWorkHeader").modal("hide");
        listProjectWork();
    }
}

function saveWorkDetail(f){
    CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
    projectwork_id = f.projectwork_name.value;
    projectworksub_name = f.projectworksub_name.value;
    projectworksub_volume = f.projectworksub_volume.value;
    projectworksub_price = f.projectworksub_price.value; 
    projectworksub_unit = f.projectworksub_unit.value; 
    projectworksub_workstart = f.projectworksub_workstart.value; 
    projectworksub_workend = f.projectworksub_workend.value; 

    if(projectwork_id != '' || projectworksub_name != '' || projectworksub_volume != '' || projectworksub_price != '' 
    || projectworksub_unit != '' || projectworksub_workstart != '' || projectworksub_workend != '' ){
        $.ajax({
            url: "{{ URL::to('rab/save_project_worksub') }}",
            type: 'POST',
            data: {
                _token: CSRF_TOKEN, 
                projectwork_id: projectwork_id, 
                projectworksub_name: projectworksub_name, 
                projectworksub_volume: projectworksub_volume, 
                projectworksub_price: projectworksub_price,
                projectworksub_unit: projectworksub_unit,
                projectworksub_workstart: projectworksub_workstart,
                projectworksub_workend: projectworksub_workend 
            },
            dataType: 'JSON',
            success: function (data) { 
                // $(".writeinfo").append(data.msg); 
                // alert(data.msg);
            }
        });
        $("#modalAddWorkDetail").modal("hide");
        listProjectWork();
    }
}
function editLengthWork(f){
    CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
    projectwork_id = f.projectworksub_id.value;
    projectworksub_workstarts = f.projectworksub_workstarts.value; 
    projectworksub_workends = f.projectworksub_workends.value; 
    if(projectwork_id != '' || projectworksub_workstarts != '' || projectworksub_workends != '' ){
        $.ajax({
            url: "{{ URL::to('rab/edit_length_work_sub') }}",
            type: 'POST',
            data: {
                _token: CSRF_TOKEN, 
                projectwork_id: projectwork_id,
                projectworksub_workstarts: projectworksub_workstarts,
                projectworksub_workends: projectworksub_workends
            },
            dataType: 'JSON',
            success: function (data) { 
                // $(".writeinfo").append(data.msg); 
                // alert(data.msg);
            }
        });
    $("#modalShowEditWork").modal("hide");
        listProjectWork();
    }
}

function saveMaterial(f){
    CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
    material_worksubname = f.material_worksubname.value;
    material_name = f.material_name.value;
    material_volume = f.material_volume.value;
    material_unit = f.material_unit.value; 
    if(material_worksubname != '' || material_name != '' || material_volume != '' || material_unit != '' ){
        $.ajax({
            url: "{{ URL::to('rab/save_project_worksub_d') }}",
            type: 'POST',
            data: {
                _token: CSRF_TOKEN, 
                material_worksubname: material_worksubname, 
                material_name: material_name, 
                material_volume: material_volume, 
                material_unit: material_unit
            },
            dataType: 'JSON',
            success: function (data) { 
                // $(".writeinfo").append(data.msg); 
                // alert(data.msg);
            }
        });
        
        $("#modalAddMaterial").modal("hide");
        listProjectWork();
    }
}
function saveEditMaterial(f){
    CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
    material_worksub_name = f.material_worksub_name.value;
    material_names = f.material_names.value;
    material_volumes = f.material_volumes.value;
    material_units = f.material_units.value; 
    // alert(material_worksub_name+" "+material_names+" "+material_volumes+" "+material_units);
    if(material_worksub_name != '' || material_names != '' || material_volumes != '' || material_units != '' ){
        $.ajax({
            url: "{{ URL::to('rab/edit_project_worksub_d') }}",
            type: 'POST',
            data: {
                _token: CSRF_TOKEN, 
                material_worksub_name : material_worksub_name,
                material_names: material_names, 
                material_volumes: material_volumes, 
                material_units: material_units
            },
            dataType: 'JSON',
            success: function (data) { 
                // $(".writeinfo").append(data.msg); 
                // alert(data.msg);
            }
        });
        
        $("#modalEditMaterial").modal("hide");
        listProjectWork();
    }
}

function listProjectWork(){
    t = $('#zero_config_no_sort').DataTable();
    t.clear().draw(false);
    $.ajax({
            type: "GET",
            url: "{{ URL::to('rab/get_project_work_by_rab_id/' . $id_rab) }}", //json get site
            dataType : 'json',
            success: function(response){
                arrData = response['data']['project_works'];
                for(i = 0; i < arrData.length; i++){
                    t.row.add([
                        '<div class="text-center">'+romanize(i + 1)+'</div>',
                        arrData[i]['name'],
                        '',
                        '',
                        '',
                        '',
                        '',
                        '',
                        '',
                        '',
                    ]).draw(false);

                    arrProjectWorksub = arrData[i]['project_worksubs'];
                    for(j = 0; j < arrProjectWorksub.length; j++){
                        arrProjectWorksubD = arrProjectWorksub[j]['project_worksub_ds'];
                        isMaterial = arrProjectWorksubD.length > 0 ? true : false;

                        t.row.add([
                            '<div class="text-center">'+(j+1)+'</div>',
                            arrProjectWorksub[j]['name'],
                            '<div class="text-center">'+formatDateID(new Date((arrProjectWorksub[j]['work_start']).substring(0,10)))+'</div>',
                            '<div class="text-center"><button type="button" onclick="doShowLengthWork('+arrProjectWorksub[j]['id']+');" title="Edit Jangka Pekerjaan" data-toggle="modal" data-target="#modalShowEditWork" class="btn btn-success waves-effect waves-light btn-sm">Edit</button></div>',
                            '<div class="text-center">'+formatDateID(new Date((arrProjectWorksub[j]['work_end']).substring(0,10)))+'</div>',
                            '<div class="text-right">'+arrProjectWorksub[j]['amount']+'</div>',
                            '<div class="text-center">'+arrProjectWorksub[j]['m_units']['name']+'</div>',
                            '<div class="text-right">'+formatCurrency(arrProjectWorksub[j]['base_price'])+'</div>',
                            '<div class="text-right">'+formatCurrency(arrProjectWorksub[j]['amount']*arrProjectWorksub[j]['base_price'])+'</div>',
                            '<div class="text-center">'+(isMaterial ? '<button type="button" onclick="doShowMaterial('+arrProjectWorksub[j]['id']+');" data-toggle="modal" data-target="#modalShowMaterial" class="btn btn-info waves-effect waves-light btn-sm">Materials/Resources</button>' : '') + '</div>',
                        ]).draw(false);
                    }
                }
            }
        });
}

function doShowMaterial(project_worksub_id){
    // alert(project_worksub_id);
    $("#modalShowMaterial").modal("show"); 

    t = $('#zero_config').DataTable();
    t.clear().draw(false);
    $.ajax({
            type: "GET",
            url: "{{ URL::to('rab/get_project_work_by_rab_id/' . $id_rab) }}", //json get site
            dataType : 'json',
            success: function(response){
                arrData = response['data']['project_works'];
                for(i = 0; i < arrData.length; i++){
                    arrProjectWorksub = arrData[i]['project_worksubs'];
                    for(j = 0; j < arrProjectWorksub.length; j++){
                        if(project_worksub_id == arrProjectWorksub[j]['id']){
                            arrProjectWorksubD = arrProjectWorksub[j]['project_worksub_ds'];
                            for(k = 0; k < arrProjectWorksubD.length; k++){
                                t.row.add([
                                    (arrProjectWorksubD[k]['m_items'] != null ? arrProjectWorksubD[k]['m_items']['name'] : ''),
                                    (arrProjectWorksubD[k]['amount'] != null ? arrProjectWorksubD[k]['amount'] : ''),
                                    (arrProjectWorksubD[k]['m_units'] != null ? arrProjectWorksubD[k]['m_units']['name'] : ''),
                                    '<div class="text-center"><button type="button" onclick="editMaterial('+arrProjectWorksub[j]['id']+','+arrProjectWorksubD[k]['id']+')" data-toggle="modal" data-target="#modalEditMaterial" class="btn btn-info waves-effect waves-light btn-sm">Edit</button></div>',
                                ]).draw(false);
                                // }
                            }
                        }
                    }
                }
            }
        });
}
function doShowLengthWork(project_worksub_id){
    $("#modalShowEditWork").modal("show"); 
    $.ajax({
        type: "GET",
        url: "{{ URL::to('rab/get_work_subs') }}" + '/' + project_worksub_id, //json get site
        dataType : 'json',
        success: function(response){
            arrData = response['data'];
            $('#id_projectworksub').val(arrData['id']);
            $('#projectworksub_workstarts').val(arrData['work_start']);
            $('#projectworksub_workends').val(arrData['work_end']);
        }
    });
}
function editMaterial(project_worksub_id, id){
    $("#modalShowMaterial").modal("hide"); 
    formMaterial = $('[id^=material_names]');
    formMaterial.empty();
    formMaterial.append('<option value="">-- Select Material --</option>');
    $.ajax({
        type: "GET",
        url: "{{ URL::to('material_request/get_material') }}", 
        dataType : 'json',
        success: function(response){
            arrData = response['data'];
            listMaterial = arrData.map((item, obj) => {
                return {
                    id: item.id,
                    name: item.name,
                    m_unit_id: item.m_unit_id,
                    m_unit_name: item.m_unit_name,
                    no: item.no
                }
            });
            for(i = 0; i < arrData.length; i++){
                formMaterial.append('<option value="'+arrData[i]['id']+'">'+arrData[i]['name']+'</option>');
            }
        }
    });
    $.ajax({
        type: "GET",
        url: "{{ URL::to('rab/get_project_worksub_d_by_id') }}" + '/' + id, //json get site
        dataType : 'json',
        success: function(response){
            arrData = response['data'];
            $('[id^=material_volumes]').val(arrData['amount']);
            var idPws=arrData['m_items']['id'];
            $("select#material_names").val(idPws).change();
            $("#material_worksubnames").val(arrData['pws']['name']);
            $("#material_worksubds_id").val(arrData['id']);
        }
    });
}
function romanize (num) {
    if (isNaN(num))
        return NaN;
    var digits = String(+num).split(""),
        key = ["","C","CC","CCC","CD","D","DC","DCC","DCCC","CM",
               "","X","XX","XXX","XL","L","LX","LXX","LXXX","XC",
               "","I","II","III","IV","V","VI","VII","VIII","IX"],
        roman = "",
        i = 3;
    while (i--)
        roman = (key[+digits.pop() + (i * 10)] || "") + roman;
    return Array(+digits.join("") + 1).join("M") + roman;
}

function formatNum(obj) {
  if (obj) {  // object exist
    var val = obj.value
    if (!parseFloat(val) || val.match(/[^\d]$/)) {  // invalid character input
      if (val.length>0) {  // delete invalid char
        obj.value = val.substring(0, val.length-1)
      }
    }
    else {  // valid char input for the key stroke
      if (val.match(/\./)) {  // already added "."
        var idx = val.indexOf(".")
        var front = val.substring(0, idx)  // before "."
        var back = val.substring(idx+1, val.length)  // after "."
        front += back.charAt(0)  // move "." back 1 char
        if (parseInt(front)==0) { front = front.replace(/^0/, "") }  // delete leading "0"
        else { front = front.replace(/^0+/, "") }
        back = back.substring(1, back.length)
        obj.value = front + "." + back
      }
      else {
        obj.value = "0.0"+val
      }
    }
  }
}

function handleStartDate(obj) {
    let workstartdate = new Date(obj.value);
    if(workstartdate.getDay() == 0) {
        alert('Please using workday date');
        obj.value = null;
        let durasi = $("#projectworksub_durasi").val(null);
        handleDurasi($("#projectworksub_durasi"));
    } else {
        handleDurasi($("#projectworksub_durasi"));
    }
}

function handleDurasi(obj) {
    let workstart = $("#projectworksub_workstart").val();
    let workstartdate = new Date(workstart);
    let duration = parseInt(obj.value);
    if(workstart !== '' && obj.value >= 0) {
        while(duration > 0) {
            workstartdate.setDate( workstartdate.getDate() + 1);
            if(workstartdate.getDay() != 0) duration--;
        }

        $('#projectworksub_workend').val(formatDate(workstartdate));
    }
        
}
function handleDurasi2(obj) {
    let workstart = $("#projectworksub_workstarts").val();
    let workstartdate = new Date(workstart);
    let duration = parseInt(obj.value);
    if(workstart !== '' && obj.value >= 0) {
        while(duration > 0) {
            workstartdate.setDate( workstartdate.getDate() + 1);
            if(workstartdate.getDay() != 0) duration--;
        }

        $('#projectworksub_workends').val(formatDate(workstartdate));
    }
        
}

async function handleMaterialNo(obj) {
    materialNo = $('[id^=m_item_no]').val();
    formMaterialId = $('[id^=material_name]');
    id = '';
    await $.ajax({
        type: "GET",
        url: "{{ URL::to('stok_opname/material_by_no') }}", //json get site
        dataType : 'json',
        data: {'no' : materialNo},
        success: function(response){
            arrData = response['data'];
            if(arrData.length > 0) {
                id = arrData[0]['id'];
            } 
        }
    });

    formMaterialId.val(id);


    handleMaterial();
}
async function handleMaterialNos(obj) {
    materialNo = $('[id^=m_item_no]').val();
    formMaterialId = $('[id^=material_names]');
    id = '';
    await $.ajax({
        type: "GET",
        url: "{{ URL::to('stok_opname/material_by_no') }}", //json get site
        dataType : 'json',
        data: {'no' : materialNo},
        success: function(response){
            arrData = response['data'];
            if(arrData.length > 0) {
                id = arrData[0]['id'];
            } 
        }
    });

    formMaterialId.val(id);


    handleMaterials();
}

</script>

@endsection