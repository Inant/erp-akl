@extends('theme.default')

@section('breadcrumb')
			<div class="page-breadcrumb">
                <div class="row">
                    <div class="col-5 align-self-center">
                        <h4 class="page-title">NUP RECORDS</h4>
                        <div class="d-flex align-items-center">
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="{{ URL::to('nuprecord') }}">NUP RECORDS</a></li>
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
            @if ($mode=="edit")
            <form method="POST" action="{{ URL::to('nuprecord/main/edit/'.$saletrxes['id']) }}" class="form-horizontal">
            @else
            <form method="POST" action="{{ URL::to('nuprecord/main/add') }}" class="form-horizontal">
            @endif
            @csrf
                <!--<div class="form-group mb-0 text-right" style="margin-top:10px;">-->
                <!--    <a href="{{ URL::to('nuprecord') }}"><button type="button" class="btn btn-danger btn-sm mb-2">Cancel</button></a>-->
                <!--    <button type="submit" class="btn btn-info btn-sm mb-2">Save</button>-->
                <!--</div>-->
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Penerimaan Biaya Booking</h4>
                    </div>
                    <hr>
                    <div class="card-body">
                        <h4 class="card-title">Transaction Data</h4>
                        <div class="form-group row align-items-center mb-0">
                            <label for="saletrxno_bok" class="col-sm-2 text-right control-label col-form-label">No Transaksi</label>
                            <div class="col-sm-4 border-left pb-2 pt-2">
                                <input type="text" required class="form-control" id="saletrxno_bok" name="saletrxno_bok" value="{{ $trx_no_bok }}">
                                <input type="text" class="form-control" id="saletrxid" name="saletrxid" hidden="true" value="{{ $saletrxes['id'] }}">
                                <input type="text" class="form-control" id="followhistoryid" name="followhistoryid" hidden="true" value="{{ $saletrxes['follow_history_id'] }}">
                                <input type="text" class="form-control" id="trxtype" name="trxtype" hidden="true" value="NUP">
                                <input type="text" name="paymentmethod" value="CASH" hidden="true">
                            </div>
                            <label for="saletrxno" class="col-sm-2 text-right control-label col-form-label">Dengan NUP</label>
                            <div class="col-sm-1 border-left pb-2 pt-2">
                                <input type="checkbox" class="form-control text-center" id="is_use_nup" name="is_use_nup" value="true" onclick="checkUseNUP(this)"/>
                            </div>
                        </div>
                        <div id="div_NUP" style="display:none">
                        <div class="form-group row align-items-center mb-0" >
                            <label for="saletrxno_nup" class="col-sm-2 text-right control-label col-form-label">NUP No</label>
                            <div class="col-sm-4 border-left pb-2 pt-2">
                                <input type="text" required class="form-control" id="saletrxno_nup" name="saletrxno_nup" value="{{ $trx_no_nup }}">
                            </div>
                        </div>
                        </div>
                        <div class="form-group row align-items-center mb-0">
                            <label class="col-sm-2 text-right control-label col-form-label">Customer</label>
                            <div class="col-sm-4 border-left pb-2 pt-2">
                                <!-- <select name="customer" id="customer"required class="form-control select2 custom-select" style="width: 100%; height:32px;" onchange="selectedChange(value)"> -->
                                <select name="customer" id="customer"required class="form-control select2 custom-select" style="width: 100%; height:32px;" >
                                    <option value="">--- Select Customer ---</option>
                                    @if($customers != null)
                                    @foreach($customers as $customer)
                                    <option value="{{ $customer['id'] }}" @if($saletrxes['customer_id']== $customer['id']) selected="selected" @endif>{{ $customer['name'] }}</option>
                                    @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>
                        <div class="form-group row align-items-center mb-0">
                            <label class="col-sm-2 text-right control-label col-form-label">Sales Person</label>
                            <div class="col-sm-4 border-left pb-2 pt-2">
                                <select name="salesperson" required class="form-control select2 custom-select" style="width: 100%; height:32px;">
                                    <option value="">--- Select Sales Person ---</option>
                                    @if($salespersons != null)
                                    @foreach($salespersons as $salesperson)
                                    <option value="{{ $salesperson['id'] }}" @if($saletrxes['m_employee_id']== $salesperson['id']) selected="selected" @endif>{{ $salesperson['name'] }}</option>
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
                                    <option value="{{ $site['id'] }}" @if($saletrxes['site_id']== $site['id']) selected="selected" @endif>{{ $site['name'] }}</option>
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
                                    <option value="{{ $project['id'] }}" @if($saletrxes['project_id']== $project['id']) selected="selected" @endif>{{ $project['name'] }}</option>
                                    @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>
                        <div class="form-group row align-items-center mb-0">
                            <label for="saletrxno" class="col-sm-2 text-right control-label col-form-label">Tanggal Rencana SPU</label>
                            <div class="col-sm-2 border-left pb-2 pt-2">
                                <input type="date"  class="form-control" id="spu_planned_date" name="spu_planned_date" value="{{ $saletrxes['spu_planned_date'] }}">
                            </div>
                        </div>
                        @if($saletrxes['payment_method']== 'CASH')
                        <div id="div_payment_cash" @if($saletrxes['payment_method']!= 'CASH') style="display:none" @endif >
                            <div class="form-group row align-items-center mb-0">
                                <label for="cash_due_date" class="col-sm-2 text-right control-label col-form-label">Uang Titipan</label>
                                <div class="col-sm-2 border-left pb-2 pt-2">
                                    <input type="text" name="paymentmethod" value="CASH" hidden="true">
                                    <input type="number" name="tenor_cash" value="1" hidden="true">
                                    <input type="number" name="cash_id" value="{{$saletrxes['cash'][0]['id'] }}" hidden="true">
                                    <input type="date"  class="form-control" id="cash_due_date" name="cash_due_date" value="{{$saletrxes['cash'][0]['due_date']}}"hidden="true">
                                    <input type="text"  class="form-control text-right" id="txt_cash_amount" value="{{$saletrxes['cash'][0]['amount']}}"  value = "0" onchange="setnumber(this)" disabled="true">
                                    <input type="number"  class="form-control" id="cash_amount" name="cash_amount" value="{{$saletrxes['cash'][0]['amount']}}" hidden="true">
                                </div>
                            </div>
                        </div>
                        @else
                        <div id="div_payment_cash" >
                            <div class="form-group row align-items-center mb-0">
                                <label for="cash_due_date" class="col-sm-2 text-right control-label col-form-label">Booking Amount</label>
                                <div class="col-sm-2 border-left pb-2 pt-2">
                                    <input type="text" name="paymentmethod" value="CASH" hidden="true">
                                    <input type="number" name="tenor_cash" value="1" hidden="true">
                                    <input type="date"  class="form-control" id="cash_due_date" name="cash_due_date" value="" hidden="true">
                                    <input type="text"  class="form-control text-right" id="txt_cash_amount" value="" onchange="setnumber(this)" onload="setnumber(this)" value = "0"  >
                                    <input type="number"  id="cash_amount" name="cash_amount" value=""  hidden="true">
                                </div>
                            </div>
                        </div>
                        @endif
                        <br>
                        <div class="row">
                            <div class="col-sm-2"></div>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <a href="{{ URL::to('nuprecord') }}"><button type="button" class="btn btn-danger  mb-2">Cancel</button></a>
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

$(document).ready(function(){
    var nup_amount_obj = $('#txt_cash_amount')[0];
    setnumber(nup_amount_obj);
});

function setnumber(obj)
{
    var objectName = obj.id.toString().replace("txt_","");
    var numberValue = obj.value.toString().replace(/\B(?=(\d{3})+(?!\d))/g,",");
    $("#txt_"+objectName).val(numberValue);
    $("#"+objectName).val(parseInt(numberValue.replace(/,/g,""))).change();
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
        }
    });
}

function checkUseNUP(obj)
{
    var style = document.getElementById('div_NUP').style;
       
    if (obj.checked)
    {
        style.display = "block";
    } else {
        style.display = "none";
    }
}
</script>



@endsection