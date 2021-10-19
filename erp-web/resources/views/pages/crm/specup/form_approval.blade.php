@extends('theme.default')

@section('breadcrumb')
			<div class="page-breadcrumb">
                <div class="row">
                    <div class="col-5 align-self-center">
                        <h4 class="page-title">Spec Up Request Record</h4>
                        <div class="d-flex align-items-center">
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="{{ URL::to('CustomerData') }}">Spec Up Request</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">{{$mode}}</li>
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
            @if ($mode=="approval")
            <form method="POST" action="{{ URL::to('specuprequest/main/approval/'.$rabrequests['id']) }}" class="form-horizontal">
            @else
            <form method="POST" action="{{ URL::to('specuprequest/main/add') }}" class="form-horizontal">
            @endif
            @csrf
                <div class="form-group mb-0 text-right" style="margin-top:10px;">
                    <a href="{{ URL::to('specuprequest') }}"><button type="button" class="btn btn-danger btn-sm mb-2">Cancel</button></a>
                    <button type="submit" class="btn btn-info btn-sm mb-2">Save</button>
                </div>
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Spec Up Request</h4>
                    </div>
                    <hr>
                    <div class="card-body">
                        <!-- <h4 class="card-title">Spec Up Request</h4> -->
                        <div class="form-group row align-items-center mb-0">
                            <label for="rabreqno" class="col-sm-2 text-right control-label col-form-label">Spec Up Request No</label>
                            <div class="col-sm-4 border-left pb-2 pt-2">
                                <input type="text" class="form-control" id="rabreqno" name="rabreqno" value="{{ $trx_no }}">
                                <input type="text" class="form-control" id="rabreqid" name="rabreqid" value="{{ $rabrequests['id'] }}" hidden="true">
                            </div>
                        </div>
                        <div class="form-group row align-items-center mb-0">
                            <label for="nupno" class="col-sm-2 text-right control-label col-form-label">NUP No</label>
                            <div class="col-sm-4 border-left pb-2 pt-2">
                                <select name="nupno" id="nupno"required onchange="setNupRelatedData(this.value);" class="form-control select2 custom-select" style="width: 100%; height:32px;"  >
                                    <option value="">--- Select NUP ---</option>
                                    @if($nups != null)
                                    @foreach($nups as $nup)
                                    <option value="{{ $nup['id'] }}" @if($rabrequests['sale_trx_id']== $nup['id']) selected="selected" @endif  >{{ $nup['no'] }}</option>
                                    @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>
                        <div class="form-group row align-items-center mb-0">
                            <label class="col-sm-2 text-right control-label col-form-label">Site Name</label>
                            <div class="col-sm-4 border-left pb-2 pt-2">
                                <select id="site" name="site" required onchange="getProjectName(this.value);" class="form-control select2 custom-select" style="width: 100%; height:32px;">
                                <option value="">--- Select Site ---</option>    
                                    @if($sites != null)
                                    @foreach($sites as $site)
                                    <option value="{{ $site['id'] }}" @if($rabrequests['project_name'][0]['site_id']== $site['id']) selected="selected" @endif>{{ $site['name'] }}</option>
                                    @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>
                        <div class="form-group row align-items-center mb-0">
                            <label class="col-sm-2 text-right control-label col-form-label">Kavling</label>
                            <div class="col-sm-4 border-left pb-2 pt-2">
                                <select  id="project" name="project" required class="form-control select2 custom-select" style="width: 100%; height:32px;" >
                                    <option value="">--- Select Kavling ---</option>
                                    @if($projects != null)
                                    @foreach($projects as $project)
                                    <option value="{{ $project['id'] }}" @if($rabrequests['project_id']== $project['id']) selected="selected" @endif>{{ $project['name'] }}</option>
                                    @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>
                        <div class="form-group row align-items-center mb-0">
                            <label for="cash_due_date" class="col-sm-2 text-right control-label col-form-label">Estimated Price</label>
                            <div class="col-sm-2 border-left pb-2 pt-2">
                                <input type="text"  class="form-control text-right" id="txt_amount_requested" value="{{ $rabrequests['amount_requested'] }}" onchange="setnumber(this)" onload="setnumber(this)" value = "0"  >
                                <input type="number"  id="amount_requested" name="amount_requested" value="{{ $rabrequests['amount_requested'] }}">
                            </div>
                        </div>
                        <div id="div_specup_list" >
                            <div class="form-group row align-items-center mb-0">
                                <label class="col-sm-2 text-right control-label col-form-label">Spec Up Request Details</label>
                                <label class="col-sm-6 text-right control-label col-form-label"> </label>
                                <button type="button" class="btn btn-info btn-sm mb-2 " onclick="addelRows_AddtionalWork.addRow()">ADD Detail</button>
                            </div>
                            <div class="form-group row align-items-center mb-0">
                                <label class="col-sm-2 text-right control-label col-form-label"> </label>
                                <table id="zero_config1" class="table table-striped table-bordered col-sm-7">
                                    <thead>
                                        <tr>
                                            <th class="text-center">No</th>
                                            <th class="text-center">Spec Up Detail</th>
                                            <th class="text-center">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if($rabrequests['rab_request_ds'] != null)
                                            @for($i = 0; $i < count($rabrequests['rab_request_ds']); $i++)
                                        <tr>
                                            <td class="tbl_id">{{$i+1}}
                                                <input type="text" class="form-control" name="additional_work_id[]" hidden="true" value="{{$rabrequests['rab_request_ds'][$i]['id']}}"/>
                                            </td>
                                            <td><input type="text" class="form-control" name="additional_work[]" value="{{$rabrequests['rab_request_ds'][$i]['additional_work']}}" /></td>
                                            <td><i class="fas fa-trash-alt" onclick="addelRows_AddtionalWork.delRow(this)"></i>
                                            </td>
                                        </tr>
                                            @endfor
                                        @else
                                        <td class="tbl_id">1
                                            <input type="text" class="form-control" name="additional_work_id[]" hidden="true" />
                                        </td>
                                        <td><input type="text" class="form-control" name="additional_work[]"  /></td>
                                        <td><i class="fas fa-trash-alt" onclick="addelRows_AddtionalWork.delRow(this)"></i>
                                        </td>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
            
        </div>
    </div>
                
</div>
<script src="{!! asset('theme/assets/libs/jquery/dist/jquery.min.js') !!}"></script>
<script src="{!! asset('theme/assets/extra-libs/DataTables/datatables.min.js') !!}"></script>
<script>
// var api_url = "{{  env('API_URL') }}";
var addelRows_AddtionalWork = new addRowsTable('zero_config1');
var NUPRelatedData = [];
$(document).ready(function(){
});

function setnumber(obj)
{
    var objectName = obj.id.toString().replace("txt_","");
    var numberValue = obj.value.toString().replace(/\B(?=(\d{3})+(?!\d))/g,",");
    $("#txt_"+objectName).val(numberValue);
    $("#"+objectName).val(parseInt(numberValue.replace(/,/g,""))).change();
}

function setNupRelatedData(nup_id){
    $.ajax({
        type: "GET",
        url: "{{ URL::to('content/nupdata') }}" +"/"+ nup_id, //json get site
        dataType : 'json',
        success: function(response){
            // console.log(response);
            NUPRelatedData = response['data'];
            if(NUPRelatedData['site_id']!=null) $("#site").val(NUPRelatedData['site_id']).change();
        }
    });
}

function getProjectName(site_id)
{
    formProjectName = $('[id^=project]');
    formProjectName.empty();
    formProjectName.append('<option value="">--- Select Kavling ---</option>');
    $.ajax({
        type: "GET",
        url: "{{ URL::to('rab/get_project') }}", //json get site
        dataType : 'json',
        data:"site_id=" + site_id,
        success: function(response){
            arrData = response['data'];
            for(i = 0; i < arrData.length; i++){            
            formProjectName.append('<option value="'+arrData[i]['id']+'">'+arrData[i]['name']+'</option>');
            }
            if(NUPRelatedData['project_id']!=null) 
            {
                $("#project").val(NUPRelatedData['project_id']).change();
            
            }
        }
    });
}

function addRowsTable(id){
  var table = document.getElementById(id);
  var me = this;
  if(document.getElementById(id)){
    var row1 = table.rows[1].outerHTML;

    //adds index-id in cols with class .tbl_id
    function setIds(){
      var tbl_id = document.querySelectorAll('#'+ id +' .tbl_id');
      for(var i=0; i<tbl_id.length; i++) tbl_id[i].innerHTML = i+1;
    }

    //add row after clicked row; receives clicked button in row
    me.addRow = function(btn){
      btn ? btn.parentNode.parentNode.insertAdjacentHTML('afterend', row1): table.insertAdjacentHTML('beforeend',row1);
      setIds();
    }

    //delete clicked row; receives clicked button in row
    me.delRow = function(btn){
      btn.parentNode.parentNode.outerHTML ='';
      setIds();
    }
  }
}
</script>

@endsection