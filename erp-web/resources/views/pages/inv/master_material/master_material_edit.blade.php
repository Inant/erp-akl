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
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Create Material</h4>
                    <form method="POST" action="{{ URL::to('master_material/edit/' . $m_items['id']) }}" class="form-horizontal">
                        @csrf
                        <div class="form-group row">
                            <label class="col-sm-3 text-right control-label col-form-label">No</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" name="no" value="{{ $m_items['no'] }}" />
                            </div>
                        </div>
                        
                        <div class="form-group row">
                            <label class="col-sm-3 text-right control-label col-form-label">Name</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" name="name" value="{{ $m_items['name'] }}" />
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-3 text-right control-label col-form-label">Tipe</label>
                            <div class="col-sm-9">
                                <select name="category" onchange="cekTipe(this)" id="category" class="form-control select2" style="width:100%">
                                    <option value="">--Pilih Tipe---</option>
                                    <option value="MATERIAL"  {{$m_items['category'] == 'MATERIAL' ? 'selected' : ''}}>MATERIAL</option>
                                    <option value="KACA" {{$m_items['category'] == 'KACA' ? 'selected' : ''}} >KACA</option>
                                    <option value="SPARE PART" {{$m_items['category'] == 'SPARE PART' ? 'selected' : ''}}>SPARE PART</option>
                                    <option value="ATK" {{$m_items['category'] == 'ATK' ? 'selected' : ''}}>ATK</option>
                                    <option value="ALAT KERJA" {{$m_items['category'] == 'ALAT KERJA' ? 'selected' : ''}}>ALAT KERJA</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-3 text-right control-label col-form-label">Lead Time</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" name="lead_time" value="{{ $m_items['late_time'] }}" />
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
                                <input type="number" min="0" class="form-control" name="amount_child" value="{{ $m_items['amount_unit_child'] }}" />
                            </div>
                        </div>
                        <div class="form-group row" id="col_item_set" @if($m_items['category'] == 'SPARE PART')  @else style="display:none" @endif>
                            <label class="col-sm-3 text-right control-label col-form-label">Item dari Set</label>
                            <div class="col-sm-9">
                                <select name="item_set" class="form-control select2" id="item_set" style="width: 100%; height:32px;">
                                </select>
                            </div>
                        </div>
                        <div class="form-group row"  id="col_amount_set" @if($m_items['category'] == 'SPARE PART')  @else style="display:none" @endif>
                            <label class="col-sm-3 text-right control-label col-form-label">Total item dari Set</label>
                            <div class="col-sm-9">
                                <input type="number" min="0" class="form-control" name="amount_set"  value="{{ $m_items['amount_in_set'] }}" />
                            </div>
                        </div>
                        <!-- <div class="table-responsive">
                            <h4 class="card-title">Item Dalam Set</h4>
                            <div class="form-group">
                                <button class="btn btn-info" type="button" onclick="addItem()">tambah Item</button>
                            </div>
                            <table class="table table-bordered table-striped" id="detail_item">
                                <thead>
                                    <tr>
                                        <th>Item</th>
                                        <th>Total Item dalam Set</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>    
                                <tbody>
                                    
                                </tbody>
                            </table>
                        </div> -->
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
    m_unit_id = "{{ $m_items['m_unit_id'] }}";
    m_unit_id2 = "{{ $m_items['m_unit_child'] }}";
    console.log(m_unit_id)
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
                if (arrData[i]['id'] == m_unit_id){
                    formSatuan.append('<option value="'+arrData[i]['id']+'" selected>'+arrData[i]['name']+'</option>');
                }else{
                    formSatuan.append('<option value="'+arrData[i]['id']+'">'+arrData[i]['name']+'</option>');
                }
                if (arrData[i]['id'] == m_unit_id2){
                    formSatuan2.append('<option value="'+arrData[i]['id']+'" selected>'+arrData[i]['name']+'</option>');
                }else{
                    formSatuan2.append('<option value="'+arrData[i]['id']+'">'+arrData[i]['name']+'</option>');
                }
            }
        }
    });
    var item_id='{{$m_items['id']}}';
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
                if (arrData[i]['id'] != item_id) {
                    formItemSet.append('<option value="'+arrData[i]['id']+'">'+arrData[i]['name']+'</option>');
                }
            }
        }
    })
    item_set = "{{ $m_items['m_group_item_id'] }}";
    formItemSet.val(item_set).change();
});
// function addItem(id){
//     var option_sp='<option value=""> Pilih Item </option>';
//     $.ajax({
//         url : '{{URL::to("master_material/get_spare_part")}}',
//         type : 'GET',
//         dataType : 'json',
//         async : false,
//         success : function(response){
//             arrData=response['data'];
//             for(i = 0; i < arrData.length; i++){
//                 option_sp+='<option value="'+arrData[i]['id']+'">'+arrData[i]['name']+'</option>';
//             }
//         }
//     })
//     var tdAdd='<tr>'+
//                     '<td><select type="" name="item[]" class="form-control" id="inputItem">'+option_sp+'</select></td>'+
//                     '<td><input type="number" name="set[]" class="form-control" value=""></td>'+
//                     '<td><button type="button" class="btn btn-sm btn-danger removeOption"><i class="mdi mdi-delete"></i></button></td>'+
//                 '</tr>';
//     $('#detail_item').find('tbody:last').append(tdAdd);
// }
// $("#detail_item").on("click", ".removeOption", function(event) {
//     event.preventDefault();
//     $(this).closest("tr").remove();
// });
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