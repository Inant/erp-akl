@extends('theme.default')

@section('breadcrumb')
			<div class="page-breadcrumb">
                <div class="row">
                    <div class="col-5 align-self-center">
                        <h4 class="page-title">Discount Request Record</h4>
                        <div class="d-flex align-items-center">
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="{{ URL::to('CustomerData') }}">Discount Request</a></li>
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
            <form method="POST" action="{{ URL::to('discountrequest/main/approval/'.$discountrequests['id']) }}" class="form-horizontal">
            @else
            <form method="POST" action="{{ URL::to('discountrequest/main/add') }}" class="form-horizontal">
            @endif
            @csrf
                <!--<div class="form-group mb-0 text-right" style="margin-top:10px;">-->
                <!--    <a href="{{ URL::to('discountrequest') }}"><button type="button" class="btn btn-danger btn-sm mb-2">Cancel</button></a>-->
                <!--    <button type="submit" class="btn btn-info btn-sm mb-2">Save</button>-->
                <!--</div>-->
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Discount Request</h4>
                    </div>
                    <hr>
                    <div class="card-body">
                        <div class="form-group row align-items-center mb-0">
                            <label for="discountreqno" class="col-sm-2 text-right control-label col-form-label">Discount Request No</label>
                            <div class="col-sm-4 border-left pb-2 pt-2">
                                <input type="text" class="form-control" id="discountreqno" name="discountreqno" value="{{ $trx_no }}" @if($mode == 'approval') disabled="true" @endif>
                                <input type="text" class="form-control" id="discountreqid" name="discountreqid" value="{{ $discountrequests['id'] }}" hidden="true">
                            </div>
                        </div>
                        <div class="form-group row align-items-center mb-0">
                            <label for="spuno" class="col-sm-2 text-right control-label col-form-label">SPU No</label>
                            <div class="col-sm-4 border-left pb-2 pt-2">
                                <select name="spuno" id="spuno"required onchange="setSpuRelatedData(this.value);" class="form-control select2 custom-select" style="width: 100%; height:32px;"  @if($mode == 'approval') disabled="true" @endif>
                                    <option value="">--- Select SPU ---</option>
                                    @if($spus != null)
                                    @foreach($spus as $spu)
                                    <option value="{{ $spu['id'] }}" @if($discountrequests['sale_trx_id']== $spu['id']) selected="selected" @endif  >{{ $spu['no'] }}</option>
                                    @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>
                        <div class="form-group row align-items-center mb-0">
                            <label class="col-sm-2 text-right control-label col-form-label">Site Name</label>
                            <div class="col-sm-4 border-left pb-2 pt-2">
                                <select id="site" name="site" required onchange="getProjectName(this.value);" class="form-control select2 custom-select" style="width: 100%; height:32px;" @if($mode == 'approval') disabled="true" @endif>
                                <option value="">--- Select Site ---</option>    
                                    @if($sites != null)
                                    @foreach($sites as $site)
                                    <option value="{{ $site['id'] }}" @if($discountrequests['project_name'][0]['site_id']== $site['id']) selected="selected" @endif>{{ $site['name'] }}</option>
                                    @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>
                        <div class="form-group row align-items-center mb-0">
                            <label class="col-sm-2 text-right control-label col-form-label">Kavling</label>
                            <div class="col-sm-4 border-left pb-2 pt-2">
                                <select  id="project" name="project" required class="form-control select2 custom-select" style="width: 100%; height:32px;" @if($mode == 'approval') disabled="true" @endif>
                                    <option value="">--- Select Kavling ---</option>
                                    @if($projects != null)
                                    @foreach($projects as $project)
                                    <option value="{{ $project['id'] }}" @if($discountrequests['project_id']== $project['id']) selected="selected" @endif>{{ $project['name'] }}</option>
                                    @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>
                        <div class="form-group row align-items-center mb-0">
                            <label class="col-sm-2 text-right control-label col-form-label">Harga Dasar Kalving</label>
                            <div class="col-sm-4 border-left pb-2 pt-2">
                                <input type="text"  class="form-control text-right" id="vw_kavling_price" value="{{0}}" disabled ="true">
                                <input type="text"  class="form-control text-right" id="txt_kavling_price" value="{{0}}" onchange="setnumber(this)" hidden="true">
                                <input type="number"  id="kavling_price" name="kavling_price" hidden="true">
                            </div>
                        </div>
                        <div class="form-group row align-items-center mb-0">
                            <label for="specup_amount" class="col-sm-2 text-right control-label col-form-label">Biaya Spec. Up</label>
                            <div class="col-sm-4 border-left pb-2 pt-2">
                                <input type="text"  class="form-control text-right" id="vw_specup_amount" value="{{0}}" disabled ="true">
                                <input type="text"  class="form-control text-right" id="txt_specup_amount" value="{{0}}" onchange="setnumber(this)" hidden="true">
                                <input type="number" id="specup_amount" name="specup_amount" value="{{0}}" hidden="true">
                            </div>
                        </div>
                        <div class="form-group row align-items-center mb-0">
                            <label for="nup_amount" class="col-sm-2 text-right control-label col-form-label">Biaya Booking</label>
                            <div class="col-sm-4 border-left pb-2 pt-2"> 
                                <input type="text"  class="form-control text-right" id="vw_nup_amount" value="{{0}}" disabled="true">
                                <input type="text"  class="form-control text-right" id="txt_nup_amount" value="{{0}}" onchange="setnumber(this)" hidden="true">
                                <input type="number"  id="nup_amount" name="nup_amount" value="{{0}}" hidden="true">
                            </div>
                        </div>
                        <div class="form-group row align-items-center mb-0">
                            <label for="ppn_amount" class="col-sm-2 text-right control-label col-form-label">PPN</label>
                            <div class="col-sm-4 border-left pb-2 pt-2">
                                <input type="text"  class="form-control text-right" id="vw_ppn_amount" value="{{0}}" disabled ="true" >
                                <input type="text"  class="form-control text-right" id="txt_ppn_amount" value="{{0}}" onchange="setnumber(this)" hidden="true">
                                <input type="number" id="ppn_amount" name="ppn_amount" value="{{0}}" hidden="true">
                            </div>
                        </div>
                        <div class="form-group row align-items-center mb-0">
                            <label for="bphtb_amount" class="col-sm-2 text-right control-label col-form-label">BPHTB</label>
                            <div class="col-sm-4 border-left pb-2 pt-2">
                                <input type="text"  class="form-control text-right" id="vw_bphtb_amount" value="{{0}}" disabled ="true" >
                                <input type="text"  class="form-control text-right" id="txt_bphtb_amount" value="{{0}}" onchange="setnumber(this)" hidden="true">
                                <input type="number" id="bphtb_amount" name="bphtb_amount" value="{{0}}" hidden="true">
                            </div>
                        </div>
                        <div class="form-group row align-items-center mb-0">
                            <label for="fasum_amount" class="col-sm-2 text-right control-label col-form-label">Biaya Fasum</label>
                            <div class="col-sm-4 border-left pb-2 pt-2">
                                <input type="text"  class="form-control text-right" id="vw_fasum_amount" value="{{0}}" disabled ="true" >
                                <input type="text"  class="form-control text-right" id="txt_fasum_amount" value="{{0}}" onchange="setnumber(this)" hidden="true">
                                <input type="number" id="fasum_amount" name="fasum_amount" value="{{0}}" hidden="true">
                            </div>
                        </div>
                        <div class="form-group row align-items-center mb-0">
                            <label for="notary_amount" class="col-sm-2 text-right control-label col-form-label">Biaya Notary</label>
                            <div class="col-sm-4 border-left pb-2 pt-2">
                                <input type="text"  class="form-control text-right" id="vw_notary_amount" value="{{0}}" disabled ="true">
                                <input type="text"  class="form-control text-right" id="txt_notary_amount" value="{{0}}" onchange="setnumber(this)" hidden="true">
                                <input type="number" id="notary_amount" name="notary_amount" value="{{0}}" hidden="true">
                            </div>
                        </div>
                        <div class="form-group row align-items-center mb-0 " >
                            <label for="total_price_amount" class="col-sm-2 text-right control-label col-form-label">Harga Total</label>
                            <div class="col-sm-4 border-left pb-2 pt-2 ">
                                <input type="text"  class="form-control text-right" id="vw_total_price_amount" value="{{0}}" disabled ="true">
                                <input type="text"  class="form-control text-right" id="txt_total_price_amount" value="{{0}}" onchange="setnumber(this)" hidden="true">
                                <input type="number" id="total_price_amount" name="total_price_amount" value="{{0}}" hidden="true">
                            </div>
                        </div>   
                        <div class="form-group row align-items-center mb-0">
                            <label for="cash_due_date" class="col-sm-2 text-right control-label col-form-label">Discount Request Amount</label>
                            <div class="col-sm-4 border-left pb-2 pt-2">
                                <input type="text"  class="form-control text-right" id="txt_amount_requested" value="{{ $discountrequests['amount_requested'] }}" onchange="setnumber(this)" onload="setnumber(this)" @if($mode == 'approval') disabled="true" @endif>
                                <input type="number"  id="amount_requested" name="amount_requested" value="{{ $discountrequests['amount_requested'] }}"  hidden="true">
                            </div>
                        </div>
                        <div id="div_approval" @if($mode != 'approval') style="display:none" @endif>
                            <div class="form-group row align-items-center mb-0">
                                <label for="amount" class="col-sm-2 text-right control-label col-form-label">Approved Price</label>
                                <div class="col-sm-4 border-left pb-2 pt-2">
                                    <input type="text"  class="form-control text-right" id="txt_amount" value="{{ $discountrequests['amount'] }}" onchange="setnumber(this)" onload="setnumber(this)" >
                                    <input type="number"  id="amount" name="amount" value="{{ $discountrequests['amount'] }}"  hidden="true">
                                </div>
                            </div>
                            <div class="form-group row align-items-center mb-0">
                                <label class="col-sm-2 text-right control-label col-form-label">Approval Result</label>
                                <div class="col-sm-4 border-left pb-2 pt-2">
                                    <select  id="is_approved" name="is_approved" required class="form-control select2 custom-select" style="width: 100%; height:32px;">
                                        <option value="1">Approve</option>
                                        <option value="0">Reject</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <br><br>
                        <div class="row">
                            <div class="col-sm-2"></div>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <a href="{{ URL::to('discountrequest') }}"><button type="button" class="btn btn-danger mb-2">Cancel</button></a>
                                    <button type="submit" class="btn btn-info mb-2">Save</button>
                                </div>
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
var SPURelatedData = [];
$(document).ready(function(){
    var amount_requested_obj = $('#txt_amount_requested')[0];
    setnumber(amount_requested_obj);
    if("{{$mode}}" == 'approval')
    {
        $.ajax({
        type: "GET",
        url: "{{ URL::to('content/spudata') }}" +"/"+ "{{$discountrequests['sale_trx_id']}}", //json get site
        dataType : 'json',
        success: function(response){
            // console.log(response);
            arrData = response['data'];
            SPURelatedData = response['data'];
            $("#sales_person").val(arrData['m_employee_id']).change();
            $("#site").val(arrData['site_id']).change();
            // $("#project").val(arrData['project_id']);
            $("#txt_kavling_price").val(arrData['base_amount']).change();
            $("#txt_specup_amount").val(arrData['specup_amount']).change();
            $("#txt_nup_amount").val(arrData['booking_amount']).change();
            $("#txt_ppn_amount").val(arrData['ppn_amount']).change();
            $("#txt_bphtb_amount").val(arrData['pbhtb_amount']).change();
            $("#txt_fasum_amount").val(arrData['fasum_fee']).change();
            $("#txt_notary_amount").val(arrData['notary_fee']).change();
            $("#txt_total_price_amount").val(arrData['total_amount']).change();
        }
    });
    }
});

function setnumber(obj)
{
    var objectName = obj.id.toString().replace("txt_","");
    var numberValue = obj.value.toString().replace(/\B(?=(\d{3})+(?!\d))/g,",");
    $("#txt_"+objectName).val(numberValue);
    $("#vw_"+objectName).val(numberValue);
    $("#"+objectName).val(parseInt(numberValue.replace(/,/g,""))).change();
}

function setSpuRelatedData(spu_id){
    $.ajax({
        type: "GET",
        url: "{{ URL::to('content/spudata') }}" +"/"+ spu_id, //json get site
        dataType : 'json',
        success: function(response){
            // console.log(response);
            arrData = response['data'];
            SPURelatedData = response['data'];
            $("#sales_person").val(arrData['m_employee_id']).change();
            $("#site").val(arrData['site_id']).change();
            // $("#project").val(arrData['project_id']);
            $("#txt_kavling_price").val(arrData['base_amount']).change();
            $("#txt_specup_amount").val(arrData['specup_amount']).change();
            $("#txt_nup_amount").val(arrData['booking_amount']).change();
            $("#txt_ppn_amount").val(arrData['ppn_amount']).change();
            $("#txt_bphtb_amount").val(arrData['pbhtb_amount']).change();
            $("#txt_fasum_amount").val(arrData['fasum_fee']).change();
            $("#txt_notary_amount").val(arrData['notary_fee']).change();
            $("#txt_total_price_amount").val(arrData['total_amount']).change();
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
            if(SPURelatedData['project_id']!=null) 
            {
                $("#project").val(SPURelatedData['project_id']).change();
            
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