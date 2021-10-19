@extends('theme.default')

@section('breadcrumb')
    <div class="page-breadcrumb">
        <div class="row">
            <div class="col-5 align-self-center">
                <h4 class="page-title">Master Material</h4>
                <div class="d-flex align-items-center">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ URL::to('master_material') }}">Master Material</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Create</li>
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
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Create Material</h4>
                    <form method="POST" action="{{ URL::to('master_material/create') }}" class="form-horizontal">
                        @csrf
                        <div class="form-group row">
                            <label class="col-sm-3 text-right control-label col-form-label">No</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" name="no" />
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-3 text-right control-label col-form-label">Name</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" name="name" />
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-3 text-right control-label col-form-label">Tipe</label>
                            <div class="col-sm-9">
                            <select name="category" onchange="cekTipe(this)" id="category" class="form-control select2" style="width:100%">
                                    <option value="">--Pilih Tipe---</option>
                                    <option value="MATERIAL">MATERIAL</option>
                                    <option value="KACA">KACA</option>
                                    <option value="SPARE PART">SPARE PART</option>
                                    <option value="ATK">ATK</option>
                                    <option value="ALAT KERJA">ALAT KERJA</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-3 text-right control-label col-form-label">Lead Time</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" name="lead_time" />
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-3 text-right control-label col-form-label">Satuan</label>
                            <div class="col-sm-9">
                                <select id="satuan" name="m_unit_id" required class="form-control select2 custom-select" style="width: 100%; height:32px;">
                                    <option value="">--- Select Satuan ---</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-3 text-right control-label col-form-label">Satuan Turunan(jika ada)</label>
                            <div class="col-sm-9">
                                <select id="m_unit_id2" name="m_unit_id2" class="form-control select2 custom-select" style="width: 100%; height:32px;">
                                    <option value="">--- Select Satuan ---</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-3 text-right control-label col-form-label">Volume Satuan Turunan</label>
                            <div class="col-sm-9">
                                <input type="number" min="0" class="form-control" name="amount_child" />
                            </div>
                        </div>
                        <div class="form-group row" id="col_item_set" style="display:none">
                            <label class="col-sm-3 text-right control-label col-form-label">Item dari Set</label>
                            <div class="col-sm-9">
                                <select name="item_set" class="form-control select2" id="item_set" style="width: 100%; height:32px;">
                                </select>
                            </div>
                        </div>
                        <div class="form-group row" id="col_amount_set"  style="display:none">
                            <label class="col-sm-3 text-right control-label col-form-label">Total item dari Set</label>
                            <div class="col-sm-9">
                                <input type="number" min="0" class="form-control" name="amount_set" />
                            </div>
                        </div>
                        <div class="text-right">
                            <button class="btn btn-primary mt-4" type="submit">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        
        </div>
    </div>
</div>


<script src="{!! asset('theme/assets/libs/jquery/dist/jquery.min.js') !!}"></script>
<script>

$(document).ready(function(){
    formSatuan = $('[id^=satuan]');
    formSatuan.empty();
    formSatuan.append('<option value="">-- Select Satuan --</option>');
    formSatuan2 = $('[id^=m_unit_id2]');
    formSatuan2.empty();
    formSatuan2.append('<option value="">-- Select Satuan --</option>');
    $.ajax({
        type: "GET",
        url: "{{ URL::to('master_satuan/list') }}", //json get site
        dataType : 'json',
        success: function(response){
            arrData = response['data'];
            for(i = 0; i < arrData.length; i++){
                formSatuan.append('<option value="'+arrData[i]['id']+'">'+arrData[i]['name']+'</option>');
                formSatuan2.append('<option value="'+arrData[i]['id']+'">'+arrData[i]['name']+'</option>');
            }
        }
    });
    formItemSet = $('[id^=item_set]');
    formItemSet.empty();
    formItemSet.append('<option value="">-- Pilih Spare Part --</option>');
    $.ajax({
        url : '{{URL::to("master_material/get_spare_part")}}',
        type : 'GET',
        dataType : 'json',
        async : false,
        success : function(response){
            arrData=response['data'];
            for(i = 0; i < arrData.length; i++){
                formItemSet.append('<option value="'+arrData[i]['id']+'">'+arrData[i]['name']+'</option>');
            }
        }
    })
});
function cekTipe(eq){
    var tipe=eq.value;
    if (tipe == 'SPARE PART') {
        $('#col_item_set').show();
        $('#col_amount_set').show();
    }else{
        $('#col_item_set').hide();
        $('#col_amount_set').hide();
    }
}
</script>
@endsection