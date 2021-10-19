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
<style>
.isDisabled {
  color: currentColor;
  cursor: not-allowed;
  opacity: 0.5;
  text-decoration: none;
}
</style>
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
                        <!-- <div class="form-group row align-items-center mb-0">
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
                        </div> -->
                        <div class="form-group row align-items-center mb-0">
                            <label class="col-sm-3 text-right control-label col-form-label">Project Name</label>
                            <div class="col-9 border-left pb-2 pt-2">
                                <label class="control-label col-form-label">{{ $rab_header['project_name'] }}</label>
                            </div>
                        </div>
                        <div class="form-group row align-items-center mb-0">
                            <label class="col-sm-3 text-right control-label col-form-label">Type Kavling</label>
                            <div class="col-9 border-left pb-2 pt-2">
                                <label class="control-label col-form-label">{{ $rab_header['type_kavling'] }}</label>
                            </div>
                        </div>
                        <div class="form-group row align-items-center mb-0">
                            <label class="col-sm-3 text-right control-label col-form-label">RAB Number</label>
                            <div class="col-sm-6 border-left pb-2 pt-2">
                                <input type="hidden" id="rab_id" name="rab_id" value="{{ $id_rab }}">
                                <input type="text" readonly name="rab_no" class="form-control" placeholder="RAB Number" value="{{ $rab_header['rab_no'] }}">
                            </div>
                        </div>
                        <div class="form-group row align-items-center mb-0">
                            <label class="col-sm-3 text-right control-label col-form-label">Estimate End Date</label>
                            <div class="col-sm-6 border-left pb-2 pt-2">
                                <input type="date" name="estimate_end" required id="estimate_end" value="{{$rab_header['estimate_end']}}" class="form-control" {{$rab_header['is_final'] == 1 ? 'disabled' : ''}}>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="card-body">
                        <h4 class="card-title">RAB Detail</h4>
                        <div class="text-right" style="margin-bottom:10px;">
                            <h5>Total Anggaran dalam satu produk ({{ $products[0]->amount_set }} Set x {{ $products[0]->totalkavling }} Kavling) : <b>Rp.<span id="total_anggaran"></span></b><h5>
                            <input type="hidden" name="total_rab" id="total_rab">
                        </div>
                        <div class="text-right" style="margin-bottom:10px;">
                            <button type="button" onclick="doAddWorkHeader();" {{$rab_header['is_final'] == 1 ? 'disabled' : ''}} data-toggle="modal" data-target="#modalAddWorkHeader" class="btn btn-success waves-effect waves-light btn-sm">+ Pekerjaan</button>
                            <button type="button" onclick="doAddWorkDetail();" {{$rab_header['is_final'] == 1 ? 'disabled' : ''}} data-toggle="modal" data-target="#modalAddWorkDetail" class="btn btn-success waves-effect waves-light btn-sm">+ Sub Pekerjaan</button>
                            <button type="button" onclick="doAddMaterial();" {{$rab_header['is_final'] == 1 ? 'disabled' : ''}} data-toggle="modal" data-target="#modalAddMaterial" class="btn btn-success waves-effect waves-light btn-sm">+ Material</button>
                            <button type="button" onclick="showAllMaterial();" data-toggle="modal" data-target="#modalShowAllMaterial" class="btn btn-info waves-effect waves-light btn-sm">Lihat Kebutuhan Material</button>
                            <a href="{{URL::to('rab/export_material/'.$id_rab)}}" target="_blank" class="btn btn-success waves-effect waves-light btn-sm">Export Material</a>
                        </div>
                        <div class="table-responsive">
                            <table id="zero_config_no_sort" class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th class="text-center">No</th>
                                        <th class="text-center">Pekerjaan</th>
                                        <th class="text-center">Mulai Kerja</th>
                                        <th class="text-center">Akhir Kerja</th>
                                        <th class="text-center">Volume</th>
                                        <th class="text-center">Satuan</th>
                                        <th class="text-center">Biaya Jasa (Rp.)</th>
                                        <th class="text-center">Harga Material (Rp.)</th>
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
                                    <button type="submit" class="btn btn-primary mb-2" onclick="clicked();" {{$rab_header['is_final'] == 1 ? 'disabled' : ''}}>Submit</button>
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
                    <h4 class="modal-title" id="modalAddWorkHeaderLabel1">Tambah Pekerjaan</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="work-name-workHeader" class="control-label">Jenis Pekerjaan:</label>
                        <input type="hidden" id="id_rab" name="id_rab" value="{{ $id_rab }}">
                        <input type="hidden" id="project_id" name="project_id" value="{{ $rab_header['project_id'] }}">
                        <input type="text" name="work_name_workHeader" class="form-control" id="work-name-workHeader" required />
                    </div>
                    <div class="form-group">
                        <label for="work-name-workHeader" class="control-label">Pilih Produk:</label>
                        <select id="product_id" name="product_id" required class="form-control select2 custom-select" style="width: 100%; height:32px;">
                            <option value="">--- Pilih Produk ---</option>
                            @foreach($products as $value)
                            <option value="{{$value->id}}">{{$value->item.' '.$value->name.' '.$value->series}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Product Equivalent</label>
                        <select id="product_equivalent" name="product_equivalent" required class="form-control select2" style="width: 100%; height:32px;">
                            <option value="">--- Pilih Product Equivalent ---</option>
                            @foreach($product_equivalent as $value)
                            <option value="{{ $value->id }}">{{ $value->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" onclick="saveWorkHeader(this.form);" class="btn btn-primary btn-sm">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>
<div class="modal fade" id="modalEditWorkHeader" role="dialog" aria-labelledby="modalAddWorkHeaderLabel1">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form method="post" name="myForm">
            @csrf
                <div class="modal-header">
                    <h4 class="modal-title" id="modalAddWorkHeaderLabel1">Edit Pekerjaan</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="work-name-workHeader" class="control-label">Jenis Pekerjaan:</label>
                        <input type="hidden" id="id_rab" name="id_rab" value="{{ $id_rab }}">
                        <input type="hidden" id="project_id" name="project_id" value="{{ $rab_header['project_id'] }}">
                        <input type="hidden" id="pw_id" name="pw_id">
                        <input type="text" name="work_name_workHeader" class="form-control" id="work-name-workHeader1" required />
                    </div>
                    <div class="form-group">
                        <label for="work-name-workHeader" class="control-label">Pilih Produk:</label>
                        <select id="product_id1" name="product_id" required class="form-control select2" style="width: 100%; height:32px;">
                            <option value="">--- Pilih Produk ---</option>
                            @foreach($products as $value)
                            <option value="{{$value->id}}">{{$value->item.' '.$value->name.' '.$value->series}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" onclick="editWorkHeader(this.form);" class="btn btn-primary btn-sm">Save</button>
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
                    <h4 class="modal-title" id="modalAddWorkDetailLabel1">Tambah Sub Pekerjaan</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="work-name-workDetail" class="control-label">Pekerjaan:</label>
                        <select id="projectwork_name" name="projectwork_name" required required class="form-control select2 custom-select" style="width: 100%; height:32px;">
                            <option value="">--- Pilih Pekerjaan ---</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="work-name-workDetail" class="control-label">Sub Pekerjaan:</label>
                        <select id="worksub_id" name="worksub_id" onchange="getWorksubD(this.value)" required class="form-control select2" style="width: 100%; height:32px;">
                            <option value="">--- Pilih Sub Pekerjaan ---</option>
                        </select>
                        <input type="hidden" name="projectworksub_name" required class="form-control" id="projectworksub_name">
                    </div>
                    <div class="form-group">
                        <label for="work-name-workDetail" class="control-label">Luas 1:</label>
                        <div class="row">
                            <div class="col-sm-5">
                            <input type="number" name="luas_1_a" required class="form-control" id="luas_1_a" onkeyup="countVolumeSub()">
                            </div>
                            <div class="col-sm-2 text-center">
                            <p style="padding-top:7px">X</p>
                            </div>
                            <div class="col-sm-5">
                            <input type="number" name="luas_1_b" required class="form-control" id="luas_1_b" onkeyup="countVolumeSub()">
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="work-name-workDetail" class="control-label">Luas 2:</label>
                        <div class="row">
                            <div class="col-sm-5">
                            <input type="number" name="luas_2_a" required class="form-control" id="luas_2_a" onkeyup="countVolumeSub()">
                            </div>
                            <div class="col-sm-2 text-center">
                            <p style="padding-top:7px">X</p>
                            </div>
                            <div class="col-sm-5">
                            <input type="number" name="luas_2_b" required class="form-control" id="luas_2_b" onkeyup="countVolumeSub()">
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="work-name-workDetail" class="control-label">Luas 3:</label>
                        <div class="row">
                            <div class="col-sm-5">
                            <input type="number" name="luas_3_a" required class="form-control" id="luas_3_a" onkeyup="countVolumeSub()">
                            </div>
                            <div class="col-sm-2 text-center">
                            <p style="padding-top:7px">X</p>
                            </div>
                            <div class="col-sm-5">
                            <input type="number" name="luas_3_b" required class="form-control" id="luas_3_b" onkeyup="countVolumeSub()">
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="work-name-workDetail" class="control-label">Quantity:</label>
                        <input type="number" name="projectworksub_quantity" required class="form-control" id="projectworksub_quantity" onkeyup="countVolumeSub()">
                    </div>
                    <div class="form-group">
                        <label for="work-name-workDetail" class="control-label">Volume:</label>
                        <input type="number" name="projectworksub_volume" readonly required class="form-control" id="projectworksub_volume">
                    </div>
                    <div class="form-group">
                        <label for="work-name-workDetail" class="control-label">Satuan:</label>
                        <select id="projectworksub_unit" name="projectworksub_unit" required required class="form-control select2 custom-select" style="width: 100%; height:32px;">
                            <option value="">--- Pilih Satuan ---</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="work-name-workDetail" class="control-label">Biaya Jasa:</label>
                        <input type="number" name="projectworksub_price" required class="form-control" id="projectworksub_price">
                    </div>
                    <div class="form-group">
                        <label for="work-name-workDetail" class="control-label">Mulai Pekerjaan:</label>
                        <input type="date" onchange="handleStartDate(this)" name="projectworksub_workstart" min="{{ date('Y-m-d') }}" required class="form-control" id="projectworksub_workstart">
                    </div>
                    <div class="form-group">
                        <label for="work-name-workDetail" class="control-label">Durasi (Harian):</label>
                        <input type="number" onkeyup="handleDurasi(this)" name="projectworksub_durasi" required class="form-control" id="projectworksub_durasi">
                    </div>
                    <div class="form-group">
                        <label for="work-name-workDetail" class="control-label">Akhir Pekerjaan:</label>
                        <input type="date" readonly name="projectworksub_workend" min="{{ date('Y-m-d') }}" required class="form-control" id="projectworksub_workend">
                    </div>
                    <div class="form-group" hidden>
                        <label for="work-name-workDetail" class="control-label">Estimasi Pengerjaan (dalam Menit):</label>
                        <input type="number" name="projectworksub_estimasimenit" required class="form-control" id="projectworksub_estimasimenit">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" onclick="saveWorkDetail(this.form);" class="btn btn-primary btn-sm">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="modalEditPws" role="dialog" aria-labelledby="modalAddWorkDetailLabel1">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form method="post" name="myForm">
                @csrf
                <div class="modal-header">
                    <h4 class="modal-title" id="modalAddWorkDetailLabel1">Edit Sub Pekerjaan</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                    <!-- <div class="form-group">
                        <label for="work-name-workDetail" class="control-label">Work Header:</label>
                        <select id="projectwork_name" name="projectwork_name" required required class="form-control select2 custom-select" style="width: 100%; height:32px;">
                            <option value="">--- Pilih Pekerjaan ---</option>
                        </select>
                    </div> -->
                    <div class="form-group">
                        <label for="work-name-workDetail" class="control-label">Sub Pekerjaan:</label>
                        <input type="hidden" name="projectwork_name1" required class="form-control" id="projectwork_name1">
                        <select id="worksub_id1" name="worksub_id1" onchange="getWorksubD1(this.value)" required class="form-control select2" style="width: 100%; height:32px;">
                            <option value="">--- Pilih Sub Pekerjaan ---</option>
                        </select>

                        <input type="hidden" name="projectworksub_name1" required class="form-control" id="projectworksub_name1">
                    </div>
                    <div class="form-group">
                        <label for="work-name-workDetail" class="control-label">Luas 1:</label>
                        <div class="row">
                            <div class="col-sm-6">
                            <input type="number" name="luas_1_a" required class="form-control" id="luas_1_a_2" onkeyup="countVolumeSub2()">
                            </div>
                            <div class="col-sm-6">
                            <input type="number" name="luas_1_b" required class="form-control" id="luas_1_b_2" onkeyup="countVolumeSub2()">
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="work-name-workDetail" class="control-label">Luas 2:</label>
                        <div class="row">
                            <div class="col-sm-6">
                            <input type="number" name="luas_2_a" required class="form-control" id="luas_2_a_2" onkeyup="countVolumeSub2()">
                            </div>
                            <div class="col-sm-6">
                            <input type="number" name="luas_2_b" required class="form-control" id="luas_2_b_2" onkeyup="countVolumeSub2()">
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="work-name-workDetail" class="control-label">Luas 3:</label>
                        <div class="row">
                            <div class="col-sm-6">
                            <input type="number" name="luas_3_a" required class="form-control" id="luas_3_a_2" onkeyup="countVolumeSub2()">
                            </div>
                            <div class="col-sm-6">
                            <input type="number" name="luas_3_b" required class="form-control" id="luas_3_b_2" onkeyup="countVolumeSub2()">
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="work-name-workDetail" class="control-label">Quantity:</label>
                        <input type="number" name="projectworksub_quantity1" required class="form-control" id="projectworksub_quantity1" onkeyup="countVolumeSub2()">
                    </div>
                    <div class="form-group">
                        <label for="work-name-workDetail" class="control-label">Volume:</label>
                        <input type="number" name="projectworksub_volume1" readonly required class="form-control" id="projectworksub_volume1">
                    </div>
                    <div class="form-group">
                        <label for="work-name-workDetail" class="control-label">Satuan:</label>
                        <select id="projectworksub_unit1" name="projectworksub_unit1" required class="form-control select2 custom-select" style="width: 100%; height:32px;">
                            <option value="">--- Pilih Satuan ---</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="work-name-workDetail" class="control-label">Harga Satuan:</label>
                        <input type="number" name="projectworksub_price1" required class="form-control" id="projectworksub_price1">
                    </div>
                    <div class="form-group">
                        <label for="work-name-workDetail" class="control-label">Mulai Pekerjaan:</label>
                        <input type="date" onchange="handleStartDate2(this)" name="projectworksub_workstart1" min="{{ date('Y-m-d') }}" required class="form-control" id="projectworksub_workstart1">
                    </div>
                    <div class="form-group">
                        <label for="work-name-workDetail" class="control-label">Durasi:</label>
                        <input type="number" onkeyup="handleDurasi1(this)" name="projectworksub_durasi1" required class="form-control" id="projectworksub_durasi1">
                    </div>
                    <div class="form-group">
                        <label for="work-name-workDetail" class="control-label">Akhir Pekerjaan:</label>
                        <input type="date" readonly name="projectworksub_workend1" min="{{ date('Y-m-d') }}" required class="form-control" id="projectworksub_workend1">
                    </div>
                    <div class="form-group" hidden>
                        <label for="work-name-workDetail" class="control-label">Estimasi Pengerjaan (dalam Menit):</label>
                        <input type="number" name="projectworksub_estimasimenit1" required class="form-control" id="projectworksub_estimasimenit1">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" onclick="editWorkDetail(this.form);" class="btn btn-primary btn-sm">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="modalAddMaterial" role="dialog" aria-labelledby="modalAddMaterialLabel1">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <form method="post" name="myForm" id="add_material">
            @csrf
                <div class="modal-header">
                    <h4 class="modal-title" id="modalAddMaterialLabel1">Material</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                                   
                    <div class="form-group">
                        <label class="control-label">Sub Pekerjaan:</label>
                        <select id="material_worksubname" name="material_worksubname" onchange="getMaterialIn(this.value)" required required class="form-control select2 custom-select" style="width: 100%; height:32px;">
                            <option value="">--- Pilih Sub Pekerjaan ---</option>
                        </select>
                    </div>
                    <button class="btn btn-success btn-sm" onclick="addField()" type="button">+ Tambah</button>
                    <button class="btn btn-success btn-sm" onclick="addRekomendasi()" type="button">+ Rekomedasi</button>
                    <button class="btn btn-warning btn-sm" type="button" onclick="importMaterial()">Import Material</button>
                    <input type="file" name="importFile" id="importFile" style="width:200px">
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="tipe_material" id="inlineRadio1" value="kanan">
                        <label class="form-check-label" for="inlineRadio1">Kanan</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="tipe_material" id="inlineRadio2" value="kiri">
                        <label class="form-check-label" for="inlineRadio2">Kiri</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="tipe_material" id="inlineRadio3" value="" checked>
                        <label class="form-check-label" for="inlineRadio3">Tidak Ada</label>
                    </div>
                    <br><br>
                    <p class="card-subtitle">*note : material kiri dan kanan input sesuai kebutuhan keseluruhan kavling</p>
                    <br>
                    <div class="table-responsive">
                        <table class="table table-bordered" id="list_material">
                            <thead>
                                <tr>
                                    <!-- <th style="min-width:250px">Nomor Material</th> -->
                                    <th style="min-width:250px">Material</th>
                                    <th style="min-width:100px">Satuan</th>
                                    <th style="min-width:150px">Satuan Turunan</th>
                                    <th style="min-width:150px">Volume Satuan per Turunan</th>
                                    <th style="min-width:150px">Banyak Item</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                    <br><br>
                    <div>
                        <h4 class="control-label">Material yang sudah diinput:</h4>
                    </div>
                    <div class="table-responsive">
                        <table id="list_material_in" class="table table-bordered">
                            <thead>
                                <tr>
                                    <th class="text-center">No Material</th>
                                    <th class="text-center">Nama Material</th>
                                    <th class="text-center">Posisi</th>
                                    <th class="text-center">Satuan</th>
                                    <th class="text-center">Satuan Turunan</th>
                                    <th class="text-center">Volume (satuan Turunan)</th>
                                    <th class="text-center">Jumlah Item</th>
                                    <th></th>
                                </tr>
                            </thead>                                      
                        </table>
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
                    <!-- <div class="form-group">
                        <label class="control-label">Nomor Material:</label>
                        <input type="text" name="m_item_no" required class="form-control" id="m_item_no" onchange="handleMaterialNo(this)">
                    </div>
                    <div class="form-group">
                        <label class="control-label">Material:</label>
                        <select id="material_name" name="material_name" required required class="form-control custom-select" onchange="handleMaterial(this.value);" style="width: 100%; height:32px;">
                            <option value="">--- Pilih Material ---</option>
                        </select>
                    </div>
                    <div class="form-group row">
                        <div class="col-sm-6">
                            <label class="control-label">Satuan:</label>
                            <input type="hidden" name="material_unit" required class="form-control" readonly id="material_unit">
                            <input type="text" name="material_unit_text" required class="form-control" readonly id="material_unit_text">
                        </div>
                        <div class="col-sm 6">
                        <label class="control-label">Satuan Turunan:</label>
                        <input type="hidden" name="volume_child" required class="form-control" readonly id="volume_child">
                        <input type="text" name="material_unit_text_child" required class="form-control" readonly id="material_unit_text_child">
                        </div>
                    </div>
                    <div class="form-group row"> -->
                        <!-- <div class="col-sm-6">
                            <label class="control-label">Volume per Product:</label>
                            <input type="number" name="volume_per_product" required class="form-control" id="volume_per_product" onkeyup="fillVolume(this.value)" readonly value="1">
                        </div> -->
                        <!-- <div class="col-sm 12">
                            <label class="control-label">Volume per Satuan Turunan:</label>
                            <input type="number" name="volume_per_turunan" required class="form-control" id="volume_per_turunan" onkeyup="fillVolumeTurunan(this.value)" min="0">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label">Banyaknya Item:</label>
                        <input type="number" name="qty" required class="form-control" id="qty" value="1">
                    </div>
                    <input type="hidden" name="price" class="form-control" id="price"> -->
                    <!-- <div class="form-group">
                        <label class="control-label">Volume:</label>
                        <input type="number" name="material_volume" required class="form-control" id="material_volume" readonly>
                    </div> -->
                </div>
                <div class="modal-footer">
                    <button onclick="saveMaterial(this);" class="btn btn-primary btn-sm">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>
<div class="modal fade" id="modalEditMaterial" role="dialog" aria-labelledby="modalAddMaterialLabel1">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form method="post" name="myForm" id="NewMaterialForm">
            @csrf
                <div class="modal-header">
                    <h4 class="modal-title" id="modalAddMaterialLabel1">Edit Material</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label class="control-label">Sub Pekerjaan:</label>
                        <input type="text" required class="form-control" id="material_worksubnames" readonly="">
                        <input type="hidden" required class="form-control" id="material_worksubds_id" readonly="" name="material_worksub_name">
                    </div>
                    <div class="form-group" hidden>
                        <label class="control-label">Nomor Material:</label>
                        <input type="text" name="m_item_no" required class="form-control m_item_no" id="m_item_no" onchange="handleMaterialNos(this)">
                    </div>
                    <div class="form-group">
                        <label class="control-label">Material:</label>
                        <select id="material_names" name="material_names" required class="form-control custom-select2" onchange="handleMaterials(this.value);" style="width: 100%; height:32px;">
                            <option value="">--- Pilih Material ---</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="">Tipe Material : </label>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="tipe_material1" id="kanan1" value="kanan">
                            <label class="form-check-label" for="kanan1">Kanan</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="tipe_material1" id="kiri1" value="kiri">
                            <label class="form-check-label" for="kiri1">Kiri</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="tipe_material1" id="global1" value="" checked>
                            <label class="form-check-label" for="global1">Tidak Ada</label>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-sm-6">
                            <label class="control-label">Satuan:</label>
                            <input type="hidden" name="material_units" required class="form-control" readonly id="material_units">
                            <input type="text" name="material_unit_texts" required class="form-control" readonly id="material_unit_texts">
                        </div>
                        <div class="col-sm 6">
                            <label class="control-label">Satuan Turunan:</label>
                            <input type="hidden" name="volume_childs" required class="form-control" readonly id="volume_childs">
                            <input type="text" name="material_unit_text_childs" required class="form-control" readonly id="material_unit_text_childs">
                        </div>
                    </div>
                    <div class="form-group row">
                        <!-- <div class="col-sm-6">
                            <label class="control-label">Volume per Product:</label>
                            <input type="number" class="form-control" id="volume_per_product1" onkeyup="fillVolume2(this.value)" readonly value="1">
                        </div> -->
                        <div class="col-sm 12">
                            <label class="control-label">Volume per Satuan Turunan:</label>
                            <input type="number" class="form-control" id="volume_per_turunan1" onkeyup="fillVolumeTurunan2(this.value)">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label">Banyaknya Item:</label>
                        <input type="number" name="qty1" required class="form-control" id="qty1" value="1">
                    </div>
                    <input type="hidden" name="price1" class="form-control" id="price1">
                    <!-- <div class="form-group">
                        <label class="control-label">Volume:</label>
                        <input type="number" name="material_volumes" required readonly class="form-control" id="material_volumes">
                    </div> -->
                </div>
                <div class="modal-footer">
                    <button type="button" onclick="saveEditMaterial(this.form);" class="btn btn-primary btn-sm">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="modalShowMaterial" tabindex="-1" role="dialog" aria-labelledby="modalAddMaterialLabel1">
    <div class="modal-dialog modal-xl" role="document">
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
                                <th class="text-center">No Material</th>
                                <th class="text-center">Nama Material</th>
                                <th class="text-center">Posisi</th>
                                <th class="text-center">Satuan</th>
                                <th class="text-center">Satuan Turunan</th>
                                <th class="text-center">Volume (satuan Turunan)</th>
                                <th class="text-center">Jumlah Item</th>
                                <th class="text-center">Harga Satuan (Rp)</th>
                                <th class="text-center">Total Harga (Rp)</th>
                                <th class="text-center" style="min-width:100px">Edit</th>
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

<div class="modal fade" id="modalShowAllMaterial" tabindex="-1" role="dialog" aria-labelledby="modalShowAllMaterial">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="showAllMaterialLabel1">Material Berdasarkan Banyaknya Item</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <div class="text-right" style="margin-bottom:10px;">
                    <button type="button" onclick="calculateMaterial();" {{$rab_header['is_final'] == 1 ? 'disabled' : ''}} class="btn btn-primary waves-effect waves-light btn-sm">Hitung Ulang Kebutuhan Material</button>
                </div>
                <div class="table-responsive">
                    <table id="tblShowMaterial" class="table table-bordered">
                        <thead>
                            <tr>
                                <th class="text-center">No Material</th>
                                <th class="text-center">Nama Material</th>
                                <th class="text-center">Volume Total</th>
                                <th class="text-center">Volume Global</th>
                                <th class="text-center">Volume Kanan</th>
                                <th class="text-center">Volume Kiri</th>
                                <th class="text-center">Harga Satuan (Rp)</th>
                                <th class="text-center">Total Harga (Rp)</th>
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
<!-- End Modal -->

<script src="{!! asset('theme/assets/libs/jquery/dist/jquery.min.js') !!}"></script>
<script>

var listMaterial = [];

$(document).ready(function(){
    listProjectWork();
});
$.ajax({
    type: "GET",
    url: "{{ URL::to('material_request/get_material_without_atk') }}", 
    dataType : 'json',
    async : false,
    success: function(response){
        arrData = response['data'];
        listMaterial = arrData;
    }
});
function addField(){
    var option_material='<option value="">--- Pilih Material ---</option>';
    for(i = 0; i < listMaterial.length; i++){
        option_material+='<option value="'+listMaterial[i]['id']+'">('+listMaterial[i]['no']+') '+listMaterial[i]['name']+'</option>';
    }
    var tdAdd='<tr>'+
                    '<td>'+
                        '<input type="hidden" onchange="handleMaterialNo2();" name="item_no[]" required class="form-control" id="item_no[]" >'+
                        '<select name="material_name2[]" required class="form-control custom-select select-item" onchange="handleMaterial2();" style="width: 100%; height:32px;">'+
                            option_material+
                        '</select>'+
                    '</td>'+
                    '<td>'+
                        '<input type="hidden" name="material_unit2[]" required class="form-control" readonly id="material_unit2[]">'+
                            '<input type="text" name="material_unit_text2[]" required class="form-control" readonly id="material_unit_text2[]">'+
                    '</td>'+
                    '<td>'+
                        '<input type="hidden" name="volume_child2[]" required class="form-control" readonly id="volume_child2[]">'+
                        '<input type="text" name="material_unit_text_child2[]" required class="form-control" readonly id="material_unit_text_child2[]">'+
                    '</td>'+
                    '<td>'+
                        '<input type="number" name="volume_per_turunan2[]" required class="form-control" id="volume_per_turunan2[]" min="0" onkeyup="cekTotalTurunan()" required>'+
                    '</td>'+
                    '<td>'+
                        '<input type="number" name="qty2[]" required step="any" class="form-control" id="qty" value="1" required>'+
                        '<input type="hidden" name="price2[]" class="form-control" id="price2[]">'+
                    '</td>'+
                    '<td class="text-center"><button class="btn btn-danger removeOption"><i class="mdi mdi-delete"></i></button></td>'+
                '</tr>';
    $('#list_material').find('tbody:last').append(tdAdd);
    $('.select-item').select2();
}

function handleMaterial2(){
    var item=$('[name^=material_name2]');
    for (var i = 0; i < item.length; i++) {
        var item_id=item.eq(i).val();
        if (item_id != '') {
            unitId = '';
            unitName = '';
            itemNo = '';
            unitNameChild = '';
            volumeChild = 1;
            price = 0;
            listMaterial.map((item, obj) => {
                if(item.id == item_id) {
                    unitId = item.m_unit_id;
                    unitName = item.m_unit_name;
                    itemNo = item.no;
                    unitNameChild = item.m_unit_childs;
                    volumeChild = item.amount_unit_child;
                    price = (item.best_prices != null ? item.best_prices.best_price : 0);
                }
            });
            $('[id^=material_unit2]').eq(i).val(unitId);
            $('[id^=material_unit_text2]').eq(i).val(unitName);
            $('[id^=item_no]').eq(i).val(itemNo);
            $('[id^=volume_child2]').eq(i).val(volumeChild);
            $('[id^=material_unit_text_child2]').eq(i).val(volumeChild +' '+ unitNameChild +' / '+ unitName);
            $('[id^=volume_per_turunan2]').eq(i).attr({
            "max" : volumeChild
            });
            $('[id^=price2]').eq(i).val(price);
        }else{
            $('[id^=item_no]').eq(i).val('');
            $('[id^=material_unit2]').eq(i).val('');
            $('[id^=material_unit_text2]').eq(i).val('');
            $('[id^=volume_child2]').eq(i).val('');
            $('[id^=material_unit_text_child2]').eq(i).val('');
            $('[id^=volume_per_turunan2]').eq(i).val('');
            $('[id^=price2]').eq(i).val(0);
        }
    }
}
function handleMaterialNo2(){
    var no_item=$('[id^=item_no]');
    for (var i = 0; i < no_item.length; i++) {
        var item_no=no_item.eq(i).val();
        if (item_no != '') {
            unitId = '';
            unitName = '';
            itemId = '';
            unitNameChild = '';
            volumeChild = 1;
            price = 0;
            listMaterial.map((item, obj) => {
                if(item.no == item_no) {
                    unitId = item.m_unit_id;
                    unitName = item.m_unit_name;
                    itemId = item.id;
                    unitNameChild = item.m_unit_childs;
                    volumeChild = item.amount_unit_child;
                    price = (item.best_prices != null ? item.best_prices.best_price : 0);
                }
            });
            $('[id^=material_unit2]').eq(i).val(unitId);
            $('[id^=material_unit_text2]').eq(i).val(unitName);
            $('[name^=material_name2]').eq(i).val(itemId).change();
            $('[id^=material_unit_text_child2]').eq(i).val(volumeChild +' '+ unitNameChild +' / '+ unitName);
            $('[id^=volume_child2]').eq(i).val(volumeChild);
            $('[id^=volume_per_turunan2]').eq(i).attr({
            "max" : volumeChild
            });
            $('[id^=price2]').eq(i).val(price);
        }else{
            $('[name^=material_name2]').eq(i).val('').change();
            $('[id^=material_unit2]').eq(i).val('');
            $('[id^=material_unit_text2]').eq(i).val('');
            $('[id^=volume_child2]').eq(i).val('');
            $('[id^=material_unit_text_child2]').eq(i).val('');
            $('[id^=volume_per_turunan2]').eq(i).val('');
            $('[id^=price2]').eq(i).val(0);
        }
    }
}

function cekTotalTurunan(){
    var no_item=$('[id^=item_no]');
    for (var i = 0; i < no_item.length; i++) {
        var item_no=no_item.eq(i).val();
        var volume_child=$('[id^=volume_child2]').eq(i).val();
        var input_turunan=$('[id^=volume_per_turunan2]').eq(i).val();
        if (item_no != '') {
            if (parseFloat(input_turunan) > parseFloat(volume_child)) {
                $('[id^=volume_per_turunan2]').eq(i).val('');
            }
        }else{
            $('[id^=volume_per_turunan2]').eq(i).val('');
        }
    }
}

$("#list_material").on("click", ".removeOption", function(event) {
    event.preventDefault();
    $(this).closest("tr").remove();
});
function fillVolume(val){
    var total_order=$('#total_order').val();
    var total_child=$('#volume_child').val();
    $('#volume_per_turunan').val(parseFloat(val*total_child));
    // $('#material_volume').val(parseFloat(val*total_order));
}

function fillVolumeTurunan(val){
    var total_order=$('#total_order').val();
    var total_child=$('#volume_child').val();
    var total=Math.ceil(parseFloat(val/total_child));
    // $('#volume_per_product').val(parseFloat(total));
    // $('#material_volume').val(parseFloat(total*total_order));
}

function fillVolume2(val){
    var total_order=$('#total_order').val();
    var total_child=$('#volume_childs').val();
    $('#volume_per_turunan1').val(parseFloat(val*total_child));
    // $('#material_volumes').val(parseFloat(val*total_order));
}

function fillVolumeTurunan2(val){
    var total_order=$('#total_order').val();
    var total_child=$('#volume_childs').val();
    var total=Math.ceil(parseFloat(val/total_child));
    // $('#volume_per_product1').val(parseFloat(total));
    // $('#material_volumes').val(parseFloat(total*total_order));
}

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
    formProjectworkName.append('<option value="">-- Pilih Pekerjaan --</option>');
    $.ajax({
        type: "GET",
        url: "{{ URL::to('rab/get_project_work_by_rab_id/' . $id_rab) }}", //json get site
        dataType : 'json',
        success: function(response){
            arrData = response['data']['project_works'];
            for(i = 0; i < arrData.length; i++){
                formProjectworkName.append('<option value="'+arrData[i]['id']+'">'+arrData[i]['name']+' '+arrData[i]['product']['item']+'</option>');
            }
        }
    });

    formUnit = $('[id^=projectworksub_unit]');
    formUnit.empty();
    formUnit.append('<option value="">-- Pilih Satuan --</option>');
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

    $('#worksub_id').empty();
    $('#worksub_id').append('<option value="">-- Pilih Sub Pekerjaan --</option>');
    $.ajax({
        type: "GET",
        url: "{{ URL::to('rab/get_worksubs') }}", //json get site
        dataType : 'json',
        success: function(response){
            arrData = response['data'];
            for(i = 0; i < arrData.length; i++){
                $('#worksub_id').append('<option value="'+arrData[i]['id']+'">'+arrData[i]['name']+'</option>');
            }
        }
    });
}

function doAddMaterial(){
    // $('[id^=material_volume]').val('');
    $('[id^=m_item_no]').val('');
    list_material_in = $('#list_material_in').DataTable();
    formWorkSubName = $('[id^=material_worksubname]');
    formWorkSubName.empty();
    formWorkSubName.append('<option value="">-- Pilih Sub Pekerjaan --</option>');
    $.ajax({
        type: "GET",
        url: "{{ URL::to('rab/get_project_work_by_rab_id/' . $id_rab) }}", //json get site
        dataType : 'json',
        success: function(response){
            arrData = response['data']['project_works'];
            for(i = 0; i < arrData.length; i++){
                formWorkSubName.append('<option disabled value="">'+arrData[i]['name']+' '+(arrData[i]['product'] != null ? arrData[i]['product']['item'] : '')+'</option>');
                arrProjectWorksub = arrData[i]['project_worksubs'];
                for(j = 0; j < arrProjectWorksub.length; j++){
                    formWorkSubName.append('<option value="'+arrProjectWorksub[j]['id']+'"> -- '+arrProjectWorksub[j]['name']+'</option>');
                }
            }
        }
    });

    formMaterial = $('[id^=material_name]');
    formMaterial.empty();
    formMaterial.append('<option value="">-- Pilih Material --</option>');
    // listMaterial = arrData.map((item, obj) => {              
    //     return {
    //         id: item.id,
    //         name: item.name,
    //         m_unit_id: item.m_unit_id,
    //         m_unit_name: item.m_unit_name,
    //         no: item.no,
    //         m_unit_child: item.m_unit_child,
    //         m_unit_childs: item.m_unit_childs,
    //         amount_unit_child: item.amount_unit_child,
    //     }
    // });
    for(i = 0; i < listMaterial.length; i++){
        formMaterial.append('<option value="'+listMaterial[i]['id']+'">'+listMaterial[i]['name']+'</option>');
    }
    // $.ajax({
    //     type: "GET",
    //     url: "{{ URL::to('material_request/get_material_without_atk') }}", 
    //     dataType : 'json',
    //     success: function(response){
    //         arrData = response['data'];
    //         listMaterial = arrData.map((item, obj) => {              
    //             return {
    //                 id: item.id,
    //                 name: item.name,
    //                 m_unit_id: item.m_unit_id,
    //                 m_unit_name: item.m_unit_name,
    //                 no: item.no,
    //                 m_unit_child: item.m_unit_child,
    //                 m_unit_childs: item.m_unit_childs,
    //                 amount_unit_child: item.amount_unit_child,
    //             }
    //         });
    //         for(i = 0; i < arrData.length; i++){
    //             formMaterial.append('<option value="'+arrData[i]['id']+'">'+arrData[i]['name']+'</option>');
    //         }
    //     }
    // });
    $('select').select2({
                width: '100%'
    });
}

function handleMaterial(value){
    unitId = '';
    unitName = '';
    itemNo = '';
    unitNameChild = '';
    volumeChild = 1;
    price = 0;
    listMaterial.map((item, obj) => {
        if(item.id == value) {

            unitId = item.m_unit_id;
            unitName = item.m_unit_name;
            itemNo = item.no;
            unitNameChild = item.m_unit_childs;
            volumeChild = item.amount_unit_child;
            price = (item.best_prices != null ? item.best_prices.best_price : 0);
        }
    });
    $('[id^=material_unit]').val(unitId);
    $('[id^=material_unit_text]').val(unitName);
    $('[id^=m_item_no]').val(itemNo);
    $('[id^=material_unit_text_child]').val(volumeChild +' '+ unitNameChild +' / '+ unitName);
    $('[id^=volume_child]').val(volumeChild);
    $("[id^=volume_per_turunan]").attr({
       "max" : volumeChild
    });
    $('[id^=price]').val(price);
    // fillVolume(0);
}
function handleMaterials(value){
    unitId = '';
    unitName = '';
    itemNo = '';
    unitNameChild = '';
    volumeChild = 1;
    price = 0;
    listMaterial.map((item, obj) => {
        if(item.id == value) {
            unitId = item.m_unit_id;
            unitName = item.m_unit_name;
            itemNo = item.no;
            unitNameChild = item.m_unit_childs;
            volumeChild = item.amount_unit_child;
            price = (item.best_prices != null ? item.best_prices.best_price : 0);
        }
    });

    $('[id^=material_units]').val(unitId);
    $('[id^=material_unit_texts]').val(unitName);
    $('[id^=m_item_no]').val(itemNo);
    $('[id^=material_unit_text_childs]').val(volumeChild +' '+ unitNameChild +' / '+ unitName);
    $('[id^=volume_childs]').val(volumeChild);
    $('[id^=price1]').val(price);
    // fillVolume2(0);
}

function handleCategory(value) {
    formMaterial = $('[id^=material_name]');
    formMaterial.empty();
    formMaterial.append('<option value="">-- Pilih Material --</option>');
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
                    m_unit_child: item.m_unit_child,
                    m_unit_childs: item.m_unit_childs,
                    amount_unit_child: item.amount_unit_child,
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
    product_id = f.product_id.value;
    product_equivalent = f.product_equivalent.value;

    if(project_work_name != ''){
        $.ajax({
            url: "{{ URL::to('rab/save_project_work') }}",
            type: 'POST',
            data: {_token: CSRF_TOKEN, id_rab: id_rab, project_id: project_id, project_work_name: project_work_name, product_id : product_id, product_equivalent: product_equivalent},
            dataType: 'JSON',
            success: function (data) { 
                $("#modalAddWorkHeader").modal("hide");
                listProjectWork();
            }
        });
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
    projectworksub_estimasimenit = f.projectworksub_estimasimenit.value;
    worksub_id = f.worksub_id.value;
    luas_1_a = f.luas_1_a.value;
    luas_1_b = f.luas_1_b.value;
    luas_2_a = f.luas_2_a.value;
    luas_2_b = f.luas_2_b.value;
    luas_3_a = f.luas_3_a.value;
    luas_3_b = f.luas_3_b.value;
    quantity = f.projectworksub_quantity.value;
    
    if(projectwork_id != '' && projectworksub_name != '' && projectworksub_volume != '' && projectworksub_price != '' 
    && projectworksub_unit != '' && projectworksub_workstart != '' && projectworksub_workend != ''){
        // && projectworksub_workstart != '' && projectworksub_workend != '' && projectworksub_estimasimenit != '' && worksub_id != ''
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
                projectworksub_workend: projectworksub_workend,
                projectworksub_estimasimenit: projectworksub_estimasimenit,
                worksub_id: worksub_id,
                luas_1_a: luas_1_a,
                luas_1_b: luas_1_b,
                luas_2_a: luas_2_a,
                luas_2_b: luas_2_b,
                luas_3_a: luas_3_a,
                luas_3_b: luas_3_b,
                quantity : quantity
            },
            dataType: 'JSON',
            success: function (data) { 
                $("#modalAddWorkDetail").modal("hide");
                listProjectWork();
            }
        });
    }
}
function editWorkDetail(f){
    CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
    projectwork_id1 = f.projectwork_name1.value;
    projectworksub_name1 = f.projectworksub_name1.value;
    projectworksub_volume1 = f.projectworksub_volume1.value;
    projectworksub_price1 = f.projectworksub_price1.value; 
    projectworksub_unit1 = f.projectworksub_unit1.value; 
    projectworksub_workstart1 = f.projectworksub_workstart1.value; 
    projectworksub_workend1 = f.projectworksub_workend1.value; 
    projectworksub_estimasimenit1 = f.projectworksub_estimasimenit1.value;
    worksub_id1 = f.worksub_id1.value;
    luas_1_a = f.luas_1_a.value;
    luas_1_b = f.luas_1_b.value;
    luas_2_a = f.luas_2_a.value;
    luas_2_b = f.luas_2_b.value;
    luas_3_a = f.luas_3_a.value;
    luas_3_b = f.luas_3_b.value;
    quantity = f.projectworksub_quantity1.value;
    
    if(projectwork_id1 != '' && projectworksub_name1 != '' && projectworksub_volume1 != '' && projectworksub_price1 != '' 
    && projectworksub_unit1 != '' && projectworksub_workstart1 != '' && projectworksub_workend1 != ''){
        // && projectworksub_workstart1 != '' && projectworksub_workend1 != '' && worksub_id1 != ''
        $.ajax({
            url: "{{ URL::to('rab/update_project_worksub') }}",
            type: 'POST',
            data: {
                _token: CSRF_TOKEN, 
                projectwork_id: projectwork_id1, 
                projectworksub_name: projectworksub_name1, 
                projectworksub_volume: projectworksub_volume1, 
                projectworksub_price: projectworksub_price1,
                projectworksub_unit: projectworksub_unit1,
                projectworksub_workstart: projectworksub_workstart1,
                projectworksub_workend: projectworksub_workend1,
                projectworksub_estimasimenit: projectworksub_estimasimenit1,
                worksub_id : worksub_id1,
                luas_1_a : luas_1_a,
                luas_1_b : luas_1_b,
                luas_2_a : luas_2_a,
                luas_2_b : luas_2_b,
                luas_3_a : luas_3_a,
                luas_3_b : luas_3_b,
                quantity : quantity
            },
            dataType: 'JSON',
            success: function (data) { 
                $("#modalEditPws").modal("hide");
                listProjectWork();
            }
        });
    }
}
// function editLengthWork(f){
//     CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
//     projectwork_id = f.projectworksub_id.value;
//     projectworksub_workstarts = f.projectworksub_workstarts.value; 
//     projectworksub_workends = f.projectworksub_workends.value; 
//     if(projectwork_id != '' && projectworksub_workstarts != '' && projectworksub_workends != '' ){
//         $.ajax({
//             url: "{{ URL::to('rab/edit_length_work_sub') }}",
//             type: 'POST',
//             data: {
//                 _token: CSRF_TOKEN, 
//                 projectwork_id: projectwork_id,
//                 projectworksub_workstarts: projectworksub_workstarts,
//                 projectworksub_workends: projectworksub_workends
//             },
//             dataType: 'JSON',
//             success: function (data) { 
//                 // $(".writeinfo").append(data.msg); 
//                 // alert(data.msg);
//             }
//         });
//     $("#modalShowEditWork").modal("hide");
//         listProjectWork();
//     }
// }
$("form#add_material").on("submit", function( event ) {
    // var form = $('#add_material')[0];
    // var data = new FormData(form);
    event.preventDefault();
    $.ajax({
        url: "{{ URL::to('rab/save_project_worksub_d') }}",
        type: 'POST',
        data: $('form#add_material').serialize(),
        dataType: 'JSON',
        success: function (response) { 
            console.log(response)
            $("#modalAddMaterial").modal("hide");
            listProjectWork();
            $('#list_material').find('tbody:last').empty();
        }
    });
});
function saveMaterial(f){
    // f.preventDefault();
    CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
    console.log($('#add_material').serialize())
    // material_worksubname = f.material_worksubname.value;
    // material_name = f.material_name.value;
    // // material_volume = f.material_volume.value;
    // material_unit = f.material_unit.value; 
    // volume_child = f.volume_child.value;
    // volume_per_turunan = f.volume_per_turunan.value;
    // qty = f.qty.value;
    // price_material = f.price.value;
    
    // if(material_worksubname != '' && material_name != '' && material_unit != '' && Number(volume_per_turunan) <= Number(volume_child)){
        
    //     $.ajax({
    //         url: "{{ URL::to('rab/save_project_worksub_d') }}",
    //         type: 'POST',
    //         data: {
    //             _token: CSRF_TOKEN, 
    //             material_worksubname: material_worksubname, 
    //             material_name: material_name, 
    //             material_volume: 1, 
    //             material_unit: material_unit,
    //             volume_per_turunan: volume_per_turunan,
    //             qty_item: qty,
    //             price : price_material
    //         },
    //         dataType: 'JSON',
    //         success: function (data) { 
    //             $("#modalAddMaterial").modal("hide");
    //             listProjectWork();
    //         }
    //     });
    // } 
    // else if (Number(volume_per_turunan) > Number(volume_child)) {
    //     console.warn(volume_per_turunan)
    //     alert('Volume per Satuan Turunan tidak boleh melebihi Satuan Turunan');
    // }
}
function saveEditMaterial(f){
    CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
    material_worksub_name = f.material_worksub_name.value;
    material_names = f.material_names.value;
    // material_volumes = f.material_volumes.value;
    material_units = f.material_units.value; 
    volume_childs = f.volume_childs.value;
    volume_per_turunan1 = f.volume_per_turunan1.value;
    qty1 = f.qty1.value;
    price_material1 = f.price1.value;
    tipe_material = f.tipe_material1.value;

    // alert(material_worksub_name+" "+material_names+" "+material_volumes+" "+material_units);
    if(material_worksub_name != '' && material_names != '' && material_units != '' && Number(volume_per_turunan1) <= Number(volume_childs) ){
        $.ajax({
            url: "{{ URL::to('rab/edit_project_worksub_d') }}",
            type: 'POST',
            data: {
                _token: CSRF_TOKEN, 
                material_worksub_name : material_worksub_name,
                material_names: material_names, 
                material_volumes: 1, 
                material_units: material_units,
                volume_per_turunan: volume_per_turunan1,
                qty_item: qty1,
                price : price_material1,
                tipe_material : tipe_material
            },
            dataType: 'JSON',
            success: function (data) { 
                $("#modalEditMaterial").modal("hide");
                listProjectWork();
            }
        });
    } else if (Number(volume_per_turunan1) > Number(volume_childs)) {
        alert('Volume per Satuan Turunan tidak boleh melebihi Satuan Turunan');
    }
}

function listProjectWork(){
    t = $('#zero_config_no_sort').DataTable();
    t.clear().draw(false);
    $.ajax({
            type: "GET",
            url: "{{ URL::to('rab/get_project_work_by_rab_id/' . $id_rab) }}", //json get site
            dataType : 'json',
            async : false,
            success: function(response){
                arrData = response['data']['project_works'];
                let totalAnggaran = 0;
                for(i = 0; i < arrData.length; i++){
                    t.row.add([
                        '<div class="text-center">'+romanize(i + 1)+'</div>',
                        arrData[i]['name']+ ' '+arrData[i]['product']['item'],
                        '',
                        // '',
                        '',
                        '',
                        '',
                        '',
                        '',
                        '',
                        '<button type="button" {{$rab_header['is_final'] == 1 ? 'disabled' : ''}} data-name="'+arrData[i]['name']+'" data-product-id="'+arrData[i]['product_id']+'" data-id="'+arrData[i]['id']+'" title="Edit Work Detail" onclick="doEditWorkHeader(this);" data-toggle="modal" data-target="#modalEditWorkHeader" class="btn btn-success waves-effect waves-light btn-sm"><i class="mdi mdi-pencil"></i></button>'
                    ]).draw(false);

                    arrProjectWorksub = arrData[i]['project_worksubs'];
                    for(j = 0; j < arrProjectWorksub.length; j++){
                        arrProjectWorksubD = arrProjectWorksub[j]['project_worksub_ds'];
                        isMaterial = arrProjectWorksubD.length > 0 ? true : false;
                        biaya_jasa=parseFloat(arrProjectWorksub[j]['amount']) * parseFloat(arrProjectWorksub[j]['base_price']);
                        total_all=biaya_jasa + parseFloat(arrProjectWorksub[j]['total_material']);
                        total_material = parseFloat(arrProjectWorksub[j]['total_material']);
                        t.row.add([
                            '<div class="text-center">'+(j+1)+'</div>',
                            arrProjectWorksub[j]['name'],
                            '<div class="text-center">'+(arrProjectWorksub[j]['work_start'] != null ? formatDateID(new Date((arrProjectWorksub[j]['work_start']).substring(0,10))) : '-')+'</div>',
                            // '<div class="text-center"><button type="button" onclick="doShowLengthWork('+arrProjectWorksub[j]['id']+');" title="Edit Jangka Pekerjaan" {{$rab_header['is_final'] == 1 ? 'disabled' : ''}} data-toggle="modal" data-target="#modalShowEditWork" class="btn btn-success waves-effect waves-light btn-sm">Edit</button></div>',
                            '<div class="text-center">'+(arrProjectWorksub[j]['work_end'] != null ? formatDateID(new Date((arrProjectWorksub[j]['work_end']).substring(0,10))) : '-')+'</div>',
                            '<div class="text-right">'+formatCurrency(arrProjectWorksub[j]['amount'])+'</div>',
                            '<div class="text-center">'+arrProjectWorksub[j]['m_units']['name']+'</div>',
                            '<div class="text-right">'+formatCurrency(biaya_jasa.toFixed(2))+'</div>',
                            '<div class="text-right">'+formatCurrency(total_material.toFixed(2))+'</div>',
                            '<div class="text-right">'+formatCurrency(total_all.toFixed(2))+'</div>',
                            '<div class="text-center"><div class="form-inline"><button type="button" {{$rab_header['is_final'] == 1 ? 'disabled' : ''}} title="Edit Work Detail" onclick="doEditPws('+arrProjectWorksub[j]['id']+');" data-toggle="modal" data-target="#modalEditPws" class="btn btn-success waves-effect waves-light btn-sm"><i class="mdi mdi-pencil"></i></button> &nbsp;'+(isMaterial ? '<button type="button" onclick="doShowMaterial('+arrProjectWorksub[j]['id']+');" data-toggle="modal" data-target="#modalShowMaterial" class="btn btn-info waves-effect waves-light btn-sm">Material</button>' : '') + '</div></div>',
                        ]).draw(false);

                        totalAnggaran += arrProjectWorksub[j]['amount']*arrProjectWorksub[j]['base_price'] + arrProjectWorksub[j]['total_material'];
                    }
                }
                $("#total_rab").val(totalAnggaran.toFixed(2));

                $("#total_anggaran").html(formatCurrency(totalAnggaran.toFixed(2)));
            }
        });
}
function getMaterialIn(project_worksub_id){
    
    // t = $('#zero_config').DataTable();
    list_material_in.clear().draw(false);
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
                                list_material_in.row.add([
                                    (arrProjectWorksubD[k]['m_items'] != null ? arrProjectWorksubD[k]['m_items']['no'] : ''),
                                    (arrProjectWorksubD[k]['m_items'] != null ? arrProjectWorksubD[k]['m_items']['name'] : ''),
                                    (arrProjectWorksubD[k]['tipe_material']),
                                    // '<div class="text-right">'+(arrProjectWorksubD[k]['amount'] != null ? formatCurrency(arrProjectWorksubD[k]['amount']) : '')+'</div>',
                                    (arrProjectWorksubD[k]['m_units'] != null ? arrProjectWorksubD[k]['m_units']['name'] : ''),
                                    (arrProjectWorksubD[k]['unit_child'] != null ? arrProjectWorksubD[k]['m_items']['amount_unit_child'] + ' ' + arrProjectWorksubD[k]['unit_child']['name'] + ' / ' + arrProjectWorksubD[k]['m_units']['name'] : '-'),
                                    '<div class="text-right">'+(arrProjectWorksubD[k]['amount_unit_child'] != null ? formatCurrency(arrProjectWorksubD[k]['amount_unit_child']) : '') + ' ' + (arrProjectWorksubD[k]['unit_child'] != null ? arrProjectWorksubD[k]['unit_child']['name'] : arrProjectWorksubD[k]['m_units']['name']) +'</div>',
                                    (arrProjectWorksubD[k]['qty_item'] != null ? arrProjectWorksubD[k]['qty_item'] : ''),
                                    '<a @if($rab_header['is_final']  != 1) href="{{URL::to('rab/delete_pwsd/'.$id_rab)}}/'+arrProjectWorksubD[k]['id']+'"  @else href="#" @endif class="btn btn-sm btn-danger" onclick="javasciprt: return confirm(\'Are You Sure ?\')"><i class="fa fa-trash"></i></a>'
                                    
                                ]).draw(false);
                                // }
                            }
                        }
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
                                    (arrProjectWorksubD[k]['m_items'] != null ? arrProjectWorksubD[k]['m_items']['no'] : ''),
                                    (arrProjectWorksubD[k]['m_items'] != null ? arrProjectWorksubD[k]['m_items']['name'] : ''),
                                    (arrProjectWorksubD[k]['tipe_material']),
                                    // '<div class="text-right">'+(arrProjectWorksubD[k]['amount'] != null ? formatCurrency(arrProjectWorksubD[k]['amount']) : '')+'</div>',
                                    (arrProjectWorksubD[k]['m_units'] != null ? arrProjectWorksubD[k]['m_units']['name'] : ''),
                                    (arrProjectWorksubD[k]['unit_child'] != null ? arrProjectWorksubD[k]['m_items']['amount_unit_child'] + ' ' + arrProjectWorksubD[k]['unit_child']['name'] + ' / ' + arrProjectWorksubD[k]['m_units']['name'] : '-'),
                                    '<div class="text-right">'+(arrProjectWorksubD[k]['amount_unit_child'] != null ? formatCurrency(arrProjectWorksubD[k]['amount_unit_child']) : '') + ' ' + (arrProjectWorksubD[k]['unit_child'] != null ? arrProjectWorksubD[k]['unit_child']['name'] : arrProjectWorksubD[k]['m_units']['name']) +'</div>',
                                    (arrProjectWorksubD[k]['qty_item'] != null ? arrProjectWorksubD[k]['qty_item'] : ''),
                                    '<div class="text-right">'+(arrProjectWorksubD[k]['best_price'] != null ? formatCurrency(parseFloat(arrProjectWorksubD[k]['best_price']).toFixed(0)) : '-')+'</div>',
                                    '<div class="text-right">'+(arrProjectWorksubD[k]['best_price'] != null ? formatCurrency(parseFloat(arrProjectWorksubD[k]['amount']).toFixed(0) * parseFloat(arrProjectWorksubD[k]['best_price']).toFixed(0)) : '')+'</div>',
                                    '<div class="text-center"><button type="button" {{$rab_header['is_final'] == 1 ? 'disabled' : ''}} onclick="editMaterial('+arrProjectWorksub[j]['id']+','+arrProjectWorksubD[k]['id']+')" data-toggle="modal" data-target="#modalEditMaterial" class="btn btn-info waves-effect waves-light btn-sm">Edit</button> &nbsp;<a @if($rab_header['is_final'] != 1) href="{{URL::to('rab/delete_pwsd/'.$id_rab)}}/'+arrProjectWorksubD[k]['id']+'" @else href="#" @endif class="btn btn-danger btn-sm" onclick="javasciprt: return confirm(\'Are You Sure ?\')"><i class="mdi mdi-delete"></i></a></div>',
                                ]).draw(false);
                                // }
                            }
                        }
                    }
                }
            }
        });
}

function showAllMaterial() {
    $("#modalShowAllMaterial").modal("show"); 

    let t = $('#tblShowMaterial').DataTable();
    t.clear().draw(false);
    $.ajax({
            type: "GET",
            url: "{{ URL::to('rab/show_all_mterial_group_by_material/' . $id_rab) }}", //json get site
            dataType : 'json',
            success: function(response){
                arrData = response['data'];
                for(k = 0; k < arrData.length; k++){
                    total_price=Number(arrData[k]['amount']) * Number(arrData[k]['best_price']);
                    t.row.add([
                        (arrData[k]['no'] != null ? arrData[k]['m_item_no'] : ''),
                        (arrData[k]['name'] != null ? arrData[k]['m_item_name'] : ''),
                        '<div class="text-right">'+(arrData[k]['amount'] != null ? formatCurrency(arrData[k]['amount']) + ' ' + arrData[k]['m_unit_name'] : '-')+'</div>',
                        '<div class="text-right">'+(arrData[k]['amount'] != null ? formatCurrency(arrData[k]['amount'] - (arrData[k]['kanan'] + arrData[k]['kiri'])) + ' ' + arrData[k]['m_unit_name'] : '-')+'</div>',
                        '<div class="text-right">'+(arrData[k]['amount'] != null ? formatCurrency(arrData[k]['kanan']) + ' ' + arrData[k]['m_unit_name'] : '-')+'</div>',
                        '<div class="text-right">'+(arrData[k]['kiri'] != null ? formatCurrency(arrData[k]['kiri']) + ' ' + arrData[k]['m_unit_name'] : '-')+'</div>',
                        '<div class="text-right">'+(arrData[k]['best_price'] != null ? formatCurrency(parseFloat(arrData[k]['best_price']).toFixed(0)) : '-')+'</div>',
                        '<div class="text-right">'+(arrData[k]['best_price'] != null ? formatCurrency(total_price.toFixed(2)) : '')+'</div>'
                    ]).draw(false);
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
    $("#modalShowMaterial").modal("toggle"); 
    formMaterial = $('[id^=material_names]');
    formMaterial.empty();
    formMaterial.append('<option value="">-- Pilih Material --</option>');
    $.ajax({
        type: "GET",
        url: "{{ URL::to('material_request/get_material_without_atk') }}", 
        dataType : 'json',
        async : false,
        success: function(response){
            arrData = response['data'];
            listMaterial = arrData.map((item, obj) => {
                return {
                    id: item.id,
                    name: item.name,
                    m_unit_id: item.m_unit_id,
                    m_unit_name: item.m_unit_name,
                    no: item.no,
                    m_unit_child: item.m_unit_child,
                    m_unit_childs: item.m_unit_childs,
                    amount_unit_child: item.amount_unit_child,
                }
            });
            for(i = 0; i < arrData.length; i++){
                formMaterial.append('<option value="'+arrData[i]['id']+'">('+arrData[i]['no']+') '+arrData[i]['name']+'</option>');
            }
        }
    });
    $('.custom-select2').select2();
    $.ajax({
        type: "GET",
        url: "{{ URL::to('rab/get_project_worksub_d_by_id') }}" + '/' + id, //json get site
        dataType : 'json',
        // async : false,
        success: function(response){
            arrData = response['data'];
            // $('[id^=material_volumes]').val(arrData['amount']);
            var idPws=arrData['m_items']['id'];
            $("select#material_names").val(idPws).change();
            $("#material_worksubnames").val(arrData['pws']['name']);
            $("#material_worksubds_id").val(arrData['id']);
            $("[id^=volume_per_turunan1]").val(arrData['amount_unit_child']);
            $("[id^=qty1]").val(arrData['qty_item']);
            $('input[name="tipe_material1"]').val([arrData['tipe_material']]);
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

function handleStartDate2(obj) {
    let workstartdate = new Date(obj.value);
    if(workstartdate.getDay() == 0) {
        alert('Please using workday date');
        obj.value = null;
        let durasi = $("#projectworksub_durasi1").val(null);
        handleDurasi1($("#projectworksub_durasi1"));
    } else {
        handleDurasi1($("#projectworksub_durasi1"));
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
function handleDurasi1(obj) {
    let workstart = $("#projectworksub_workstart1").val();
    let workstartdate = new Date(workstart);
    let duration = parseInt(obj.value);
    if(workstart !== '' && obj.value >= 0) {
        while(duration > 0) {
            workstartdate.setDate( workstartdate.getDate() + 1);
            if(workstartdate.getDay() != 0) duration--;
        }

        $('#projectworksub_workend1').val(formatDate(workstartdate));
    }
        
}
// function handleDurasi2(obj) {
//     let workstart = $("#projectworksub_workstarts").val();
//     let workstartdate = new Date(workstart);
//     let duration = parseInt(obj.value);
//     if(workstart !== '' && obj.value >= 0) {
//         while(duration > 0) {
//             workstartdate.setDate( workstartdate.getDate() + 1);
//             if(workstartdate.getDay() != 0) duration--;
//         }

//         $('#projectworksub_workends').val(formatDate(workstartdate));
//     }
        
// }

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
function doEditPws(id){
    $('#worksub_id1').empty();
    $('#worksub_id1').append('<option value="">-- Pilih Sub Pekerjaan --</option>');
    $.ajax({
        type: "GET",
        url: "{{ URL::to('rab/get_worksubs') }}", //json get site
        dataType : 'json',
        async : true,
        success: function(response){
            arrData = response['data'];
            for(i = 0; i < arrData.length; i++){
                $('#worksub_id1').append('<option value="'+arrData[i]['id']+'">'+arrData[i]['name']+'</option>');
            }
        }
    });
    formUnit1 = $('[id^=projectworksub_unit1]');
    formUnit1.empty();
    formUnit1.append('<option value="">-- Pilih Satuan --</option>');
    $.ajax({
        type: "GET",
        url: "{{ URL::to('rab/get_all_m_unit') }}", //json get site
        dataType : 'json',
        success: function(response){
            arrData = response['data'];
            for(i = 0; i < arrData.length; i++){
                formUnit1.append('<option value="'+arrData[i]['id']+'">'+arrData[i]['name']+'</option>');
            }
        }
    });
    $.ajax({
        type: "GET",
        url: "{{ URL::to('rab/get_work_subs') }}" + '/' + id, //json get site
        dataType : 'json',
        async : true,
        success: function(response){
            arrData = response['data'];
            const base_price = arrData['base_price'].split('.');
            $('#worksub_id1').val(arrData['worksub_id']).change();
            $('#projectwork_name1').val(arrData['id']);
            $('#projectworksub_name1').val(arrData['name']);
            $('#projectworksub_volume1').val(arrData['amount']);
            $('#projectworksub_price1').val(base_price[0]);
            $('#projectworksub_workstart1').val(arrData['work_start']);
            $('#projectworksub_workend1').val(arrData['work_end']);
            $('#projectworksub_unit1').val(arrData['m_unit_id']).change();
            $('#projectworksub_estimasimenit1').val(arrData['estimation_in_minute']);
            $('#luas_1_a_2').val(arrData['luas_1_a']);
            $('#luas_1_b_2').val(arrData['luas_1_b']);
            $('#luas_2_a_2').val(arrData['luas_2_a']);
            $('#luas_2_b_2').val(arrData['luas_2_b']);
            $('#luas_3_a_2').val(arrData['luas_3_a']);
            $('#luas_3_b_2').val(arrData['luas_3_b']);
        }
    });
}

function calculateMaterial() {
    $.ajax({
        type: "GET",
        url: "{{ URL::to('rab/calculate_all_material/' . $id_rab) }}",
        dataType : 'json',
        success: function(response){
            $("#modalShowAllMaterial").modal("hide");
            listProjectWork();
        }
    });
}
function clicked() {
    if (confirm('Apakah yakin akan Submit Rab ini? Ketika anda submit maka RAB sudah tidak dapat dirubah.')) {
        yourformelement.submit();
    } else {
        return false;
    }
}

let showImport = false;
function importMaterial() {
    const importFile = $('#importFile').prop('files');

    if (typeof importFile !== 'undefined') { 
        CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');     
        var form_data = new FormData();                  
        form_data.append('file', importFile[0]);  
        form_data.append('_token', CSRF_TOKEN);              
        $.ajax({
            url: "{{ URL::to('rab/import_material/1') }}", 
            dataType: 'json',  
            cache: false,
            contentType: false,
            processData: false,
            data: form_data,                         
            type: 'post',
            success: function(response){
                response.data.map((item, index) => {
                    var option_material='<option value="">--- Pilih Material ---</option>';
                    for(i = 0; i < listMaterial.length; i++){
                        if (item.m_item_id === listMaterial[i]['id'])
                            option_material+='<option selected value="'+listMaterial[i]['id']+'">('+listMaterial[i]['no']+') '+listMaterial[i]['name']+'</option>';
                        else
                            option_material+='<option value="'+listMaterial[i]['id']+'">('+listMaterial[i]['no']+') '+listMaterial[i]['name']+'</option>';
                    }
                    
                    var tdAdd='<tr>'+
                                    '<td>'+
                                        '<input type="hidden" onchange="handleMaterialNo2();" name="item_no[]" required class="form-control" id="item_no[]">'+
                                        '<select name="material_name2[]" required class="form-control custom-select select-item" onchange="handleMaterial2();" style="width: 100%; height:32px;">'+
                                            option_material+
                                        '</select>'+
                                    '</td>'+
                                    '<td>'+
                                        '<input type="hidden" name="material_unit2[]" required class="form-control" readonly id="material_unit2[]">'+
                                            '<input type="text" name="material_unit_text2[]" required class="form-control" readonly id="material_unit_text2[]">'+
                                    '</td>'+
                                    '<td>'+
                                        '<input type="hidden" name="volume_child2[]" required class="form-control" readonly id="volume_child2[]">'+
                                        '<input type="text" name="material_unit_text_child2[]" required class="form-control" readonly id="material_unit_text_child2[]">'+
                                    '</td>'+
                                    '<td>'+
                                        '<input type="number" name="volume_per_turunan2[]" required class="form-control" id="volume_per_turunan2[]" min="0" onkeyup="cekTotalTurunan()" value="'+item.volume_per_turunan+'" required>'+
                                    '</td>'+
                                    '<td>'+
                                        '<input type="number" name="qty2[]" step="any" required class="form-control" id="qty" value="'+item.qty_item+'" required>'+
                                        '<input type="hidden" name="price2[]" class="form-control" id="price2[]">'+
                                    '</td>'+
                                    '<td class="text-center"><button class="btn btn-danger removeOption"><i class="mdi mdi-delete"></i></button></td>'+
                                '</tr>';
                    $('#list_material').find('tbody:last').append(tdAdd);
                })
                $('.select-item').select2();
                handleMaterial2();
            }
        });

    }

    console.warn(importFile);
}
function getWorksubD(id){
    $.ajax({
        type: "GET",
        url: "{{ URL::to('rab/get_worksub_d') }}"+'/'+id,
        dataType : 'json',
        success: function(response){
            arrData=response['data'];
            $('#projectworksub_name').val(arrData['name']);
            $('#projectworksub_unit').val(arrData['m_unit_id']).change();
            $('#projectworksub_price').val(parseFloat(arrData['price']));
        }
    });
}
function getWorksubD1(id){
    $.ajax({
        type: "GET",
        url: "{{ URL::to('rab/get_worksub_d') }}"+'/'+id,
        dataType : 'json',
        success: function(response){
            arrData=response['data'];
            $('#projectworksub_name1').val(arrData['name']);
            $('#projectworksub_unit1').val(arrData['m_unit_id']).change();
            $('#projectworksub_price1').val(parseFloat(arrData['price']));
        }
    });
}
function doEditWorkHeader(eq){
    var id=$(eq).data('id');
    var product_id=$(eq).data('product-id');
    var name=$(eq).data('name');
    $('#pw_id').val(id);
    $('#work-name-workHeader1').val(name);
    console.log(product_id)
    $('#product_id1').val(product_id).change();

}
function editWorkHeader(f){    
    CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
    id_rab = f.id_rab.value;
    project_id = f.project_id.value;
    pw_id = f.pw_id.value;
    project_work_name = f.work_name_workHeader.value;
    product_id = f.product_id.value;
    console.log(pw_id)
    if(project_work_name != '' && product_id != ''){
        $.ajax({
            url: "{{ URL::to('rab/edit_project_work') }}",
            type: 'POST',
            data: {_token: CSRF_TOKEN, id_rab: id_rab, project_id: project_id, project_work_name: project_work_name, product_id : product_id, pw_id : pw_id},
            dataType: 'JSON',
            success: function (data) { 
                $("#modalEditWorkHeader").modal("hide");
                listProjectWork();
            }
        });
    }
}

function addRekomendasi() {
            
    $.ajax({
        url: "{{ URL::to('rab/get_product_equivalent_recomendation/' . $id_rab) }}", 
        dataType: 'json',                     
        type: 'get',
        success: function(response){
            response.data.map((item, index) => {
                var option_material='<option value="">--- Pilih Material ---</option>';
                for(i = 0; i < listMaterial.length; i++){
                    if (item.m_item_id === listMaterial[i]['id'])
                        option_material+='<option selected value="'+listMaterial[i]['id']+'">('+listMaterial[i]['no']+') '+listMaterial[i]['name']+'</option>';
                    else
                        option_material+='<option value="'+listMaterial[i]['id']+'">('+listMaterial[i]['no']+') '+listMaterial[i]['name']+'</option>';
                }
                
                var tdAdd='<tr>'+
                                '<td>'+
                                    '<input type="hidden" onchange="handleMaterialNo2();" name="item_no[]" required class="form-control" id="item_no[]">'+
                                    '<select id="material_name2[]" name="material_name2[]" required class="form-control custom-select select-item" onchange="handleMaterial2();" style="width: 100%; height:32px;">'+
                                        option_material+
                                    '</select>'+
                                '</td>'+
                                '<td>'+
                                    '<input type="hidden" name="material_unit2[]" required class="form-control" readonly id="material_unit2[]">'+
                                        '<input type="text" name="material_unit_text2[]" required class="form-control" readonly id="material_unit_text2[]">'+
                                '</td>'+
                                '<td>'+
                                    '<input type="hidden" name="volume_child2[]" required class="form-control" readonly id="volume_child2[]">'+
                                    '<input type="text" name="material_unit_text_child2[]" required class="form-control" readonly id="material_unit_text_child2[]">'+
                                '</td>'+
                                '<td>'+
                                    '<input type="number" name="volume_per_turunan2[]" required class="form-control" id="volume_per_turunan2[]" min="0" onkeyup="cekTotalTurunan()" value="'+item.volume_per_turunan+'" required>'+
                                '</td>'+
                                '<td>'+
                                    '<input type="number" name="qty2[]" step="any" required class="form-control" id="qty" value="'+item.qty_item+'" required>'+
                                    '<input type="hidden" name="price2[]" class="form-control" id="price2[]">'+
                                '</td>'+
                                '<td class="text-center"><button class="btn btn-danger removeOption"><i class="mdi mdi-delete"></i></button></td>'+
                            '</tr>';
                $('#list_material').find('tbody:last').append(tdAdd);
            })
            $('.select-item').select2();
            handleMaterial2();
        }
    });
}
function countVolumeSub(){
    var luas_1_a=($('#luas_1_a').val() != '' ? $('#luas_1_a').val() : 0);
    var luas_1_b=($('#luas_1_b').val() != '' ? $('#luas_1_b').val() : 0);
    var luas_2_a=($('#luas_2_a').val() != '' ? $('#luas_2_a').val() : 0);
    var luas_2_b=($('#luas_2_b').val() != '' ? $('#luas_2_b').val() : 0);
    var luas_3_a=($('#luas_3_a').val() != '' ? $('#luas_3_a').val() : 0);
    var luas_3_b=($('#luas_3_b').val() != '' ? $('#luas_3_b').val() : 0);
    var quantity=($('#projectworksub_quantity').val() != '' ? $('#projectworksub_quantity').val() : 0);
    var luas_1=parseFloat(luas_1_a) * parseFloat(luas_1_b);
    var luas_2=parseFloat(luas_2_a) * parseFloat(luas_2_b);
    var luas_3=parseFloat(luas_3_a) * parseFloat(luas_3_b);
    $('#projectworksub_volume').val((luas_1 + luas_2 + luas_3) * quantity);
}
function countVolumeSub2(){
    var luas_1_a=($('#luas_1_a_2').val() != '' ? $('#luas_1_a_2').val() : 0);
    var luas_1_b=($('#luas_1_b_2').val() != '' ? $('#luas_1_b_2').val() : 0);
    var luas_2_a=($('#luas_2_a_2').val() != '' ? $('#luas_2_a_2').val() : 0);
    var luas_2_b=($('#luas_2_b_2').val() != '' ? $('#luas_2_b_2').val() : 0);
    var luas_3_a=($('#luas_3_a_2').val() != '' ? $('#luas_3_a_2').val() : 0);
    var luas_3_b=($('#luas_3_b_2').val() != '' ? $('#luas_3_b_2').val() : 0);
    var quantity=($('#projectworksub_quantity1').val() != '' ? $('#projectworksub_quantity1').val() : 0);
    var luas_1=parseFloat(luas_1_a) * parseFloat(luas_1_b);
    var luas_2=parseFloat(luas_2_a) * parseFloat(luas_2_b);
    var luas_3=parseFloat(luas_3_a) * parseFloat(luas_3_b);
    $('#projectworksub_volume1').val((luas_1 + luas_2 + luas_3) * quantity);
}
</script>

@endsection