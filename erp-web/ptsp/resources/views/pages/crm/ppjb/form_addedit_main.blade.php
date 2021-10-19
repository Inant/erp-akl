@extends('theme.default')

@section('breadcrumb')
			<div class="page-breadcrumb">
                <div class="row">
                    <div class="col-5 align-self-center">
                        <h4 class="page-title">PPJB RECORDS</h4>
                        <div class="d-flex align-items-center">
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="{{ URL::to('unittransaction') }}">PPJB RECORDS</a></li>
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
            <form method="POST" action="{{ URL::to('ppjbrecord/main/edit/'.$sale_trxes['id']) }}" class="form-horizontal">
            @else
            <form method="POST" action="{{ URL::to('ppjbrecord/main/add') }}" class="form-horizontal">
            @endif
            @csrf
                <!--<div class="form-group mb-0 text-right" style="margin-top:10px;">-->
                <!--    <a href="{{ URL::to('ppjbrecord') }}"><button type="button" class="btn btn-danger btn-sm mb-2">Cancel</button></a>-->
                <!--    <button type="submit" class="btn btn-info btn-sm mb-2">Save</button>-->
                <!--</div>-->
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">PPJB Record</h4>
                    </div>
                    <hr>
                    <div class="card-body">
                        <h4 class="card-title">Transaction Data</h4>
                        <div class="form-group row align-items-center mb-0">
                            <label for="saletrx_no" class="col-sm-2 text-right control-label col-form-label">PPJB No</label>
                            <div class="col-sm-4 border-left pb-2 pt-2">
                                <input type="text" class="form-control" id="saletrx_no" name="saletrx_no" value="{{  $trx_no }}">
                                <input type="text" class="form-control" id="saletrx_id" name="saletrx_id" hidden="true" value="{{ $sale_trxes['id'] }}">
                                <input type="text" class="form-control" id="trx_type" name="trx_type" hidden="true" value="PPJB">
                            </div>
                            <label for="trx_date" class="col-sm-2 text-right control-label col-form-label">Tanggal Transaksi</label>
                            <div class="col-sm-2 border-left pb-2 pt-2">
                                <input type="date"  class="form-control" id="trx_date" name="trx_date" value="{{ $today_date }}" disabled ="true">
                            </div>
                        </div>
                        <div class="form-group row align-items-center mb-0">
                            <label class="col-sm-2 text-right control-label col-form-label">Customer</label>
                            <div class="col-sm-4 border-left pb-2 pt-2">
                                <!-- <select name="customer" id="customer"class="form-control select2 custom-select" style="width: 100%; height:32px;" onchange="selectedChange(value)"> -->
                                <select name="customer" id="customer"onchange="setCustomerRelatedData(this.value);" class="form-control select2 custom-select" style="width: 100%; height:32px;"  >
                                    <option value="">--- Select Customer ---</option>
                                    @if($customers != null)
                                    @foreach($customers as $customer)
                                    <option value="{{ $customer['id'] }}" @if($sale_trxes['customer_id']== $customer['id']) selected="selected" @endif>{{ $customer['name'] }}</option>
                                    @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>
                        <div class="form-group row align-items-center mb-0">
                            <label for="owner_name" class="col-sm-2 text-right control-label col-form-label">Nama pada sertifikat</label>
                            <div class="col-sm-4 border-left pb-2 pt-2">
                                <input type="textarea"  class="form-control text-right" id="owner_name" name="owner_name" value="">
                            </div>
                        </div>
                        <div class="form-group row align-items-center mb-0">
                            <label for="residence_address" class="col-sm-2 text-right control-label col-form-label">Alamat rumah</label>
                            <div class="col-sm-4 border-left pb-2 pt-2">
                                <input type="textarea"  class="form-control text-right" id="residence_address" name="residence_address"  value="">
                            </div>
                            <label for="residence_zipcode" class="col-sm-0 text-right control-label col-form-label">kodepos  </label>
                            <div class="col-sm-1 pb-2 pt-2">
                                <input type="number"  class="form-control text-right" id="residence_zipcode" name="residence_zipcode"  value="">
                            </div>
                        </div>
                        <div class="form-group row align-items-center mb-0">
                            <label for="residence_rt" class="col-sm-3 text-right control-label col-form-label">RT/RW</label>
                            <div class="col-sm-1 border-left pb-2 pt-2">
                                <input type="number"  class="form-control text-right" id="residence_rt" name="residence_rt"  value="">
                            </div>
                            <label for="residence_rt" class="col-sm-0 text-right control-label col-form-label">/ </label>
                            <div class="col-sm-1  pb-2 pt-2">
                                <input type="number"  class="form-control text-right" id="residence_rw" name="residence_rw"  value="">
                            </div>
                        </div>
                        <div class="form-group row align-items-center mb-0">
                            <label for="residence_kecamatan" class="col-sm-3 text-right control-label col-form-label">Kecamatan</label>
                            <div class="col-sm-3 border-left pb-2 pt-2">
                                <input type="textarea"  class="form-control text-right" id="residence_kecamatan" name="residence_kecamatan"  value="">
                            </div>
                        </div>
                        <div class="form-group row align-items-center mb-0">
                            <label for="residence_kelurahan" class="col-sm-3 text-right control-label col-form-label">Kelurahan</label>
                            <div class="col-sm-3 border-left pb-2 pt-2">
                                <input type="textarea"  class="form-control text-right" id="residence_kelurahan" name="residence_kelurahan"  value="">
                            </div>
                        </div>
                        <div class="form-group row align-items-center mb-0">
                            <label for="residence_city" class="col-sm-3 text-right control-label col-form-label">Kota</label>
                            <div class="col-sm-3 border-left pb-2 pt-2">
                                <input type="textarea"  class="form-control text-right" id="residence_city" name="residence_city"  value="">
                            </div>
                        </div>
                        <br>
                        <div class="form-group row align-items-center mb-0">
                            <label for="legal_address" class="col-sm-2 text-right control-label col-form-label">Alamat surat menyurat</label>
                            <div class="col-sm-4 border-left pb-2 pt-2">
                                <input type="textarea"  class="form-control text-right" id="legal_address" name="legal_address"  value="">
                            </div>
                            <label for="legal_zipcode" class="col-sm-0 text-right control-label col-form-label">kodepos</label>
                            <div class="col-sm-1 pb-2 pt-2">
                                <input type="textarea"  class="form-control text-right" id="legal_zipcode" name="legal_zipcode"  value="">
                            </div>
                        </div>
                        <div class="form-group row align-items-center mb-0">
                            <label for="legal_rt" class="col-sm-3 text-right control-label col-form-label">RT/RW</label>
                            <div class="col-sm-1 border-left pb-2 pt-2">
                                <input type="number"  class="form-control text-right" id="legal_rt" name="legal_rt"  value="">
                            </div>
                            <label for="residence_rt" class="col-sm-0 text-right control-label col-form-label">/ </label>
                            <div class="col-sm-1 pb-2 pt-2">
                                <input type="number"  class="form-control text-right" id="legal_rw" name="legal_rw"  value="">
                            </div>
                        </div>
                        <div class="form-group row align-items-center mb-0">
                            <label for="legal_kecamatan" class="col-sm-3 text-right control-label col-form-label">Kecamatan</label>
                            <div class="col-sm-3 border-left pb-2 pt-2">
                                <input type="textarea"  class="form-control text-right" id="legal_kecamatan" name="legal_kecamatan"  value="">
                            </div>
                        </div>
                        <div class="form-group row align-items-center mb-0">
                            <label for="legal_kelurahan" class="col-sm-3 text-right control-label col-form-label">Kelurahan</label>
                            <div class="col-sm-3 border-left pb-2 pt-2">
                                <input type="textarea"  class="form-control text-right" id="legal_kelurahan" name="legal_kelurahan"  value="">
                            </div>
                        </div>
                        <div class="form-group row align-items-center mb-0">
                            <label for="legal_city" class="col-sm-3 text-right control-label col-form-label">Kota</label>
                            <div class="col-sm-3 border-left pb-2 pt-2">
                                <input type="textarea"  class="form-control text-right" id="legal_city" name="legal_city"  value="">
                            </div>
                        </div>
                        <br>
                        <!--<div class="form-group row align-items-center mb-0">-->
                        <!--    <label for="deal_type" class="col-sm-2 text-right control-label col-form-label">Tipe kesepakatan</label>-->
                        <!--    <div class="col-sm-4 border-left pb-2 pt-2">-->
                        <!--        <input type="textarea"  class="form-control text-right" id="deal_type" name="deal_type"  value="">-->
                        <!--    </div>-->
                        <!--</div>-->
                        <div class="form-group row align-items-center mb-0">
                            <label for="spuno" class="col-sm-2 text-right control-label col-form-label">SPU No</label>
                            <div class="col-sm-4 border-left pb-2 pt-2">
                                <select name="spuno" id="spuno"onchange="setSpuRelatedData(this.value);" class="form-control select2 custom-select" style="width: 100%; height:32px;"  >
                                    <option value="">--- Select SPU  ---</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row align-items-center mb-0">
                            <label class="col-sm-2 text-right control-label col-form-label">Sales Person</label>
                            <div class="col-sm-4 border-left pb-2 pt-2">
                                <select id="sales_person" name="sales_person" class="form-control select2 custom-select" style="width: 100%; height:32px;">
                                    <option value="">--- Select Sales Person ---</option>
                                    @if($sales_persons != null)
                                    @foreach($sales_persons as $sales_person)
                                    <option value="{{ $sales_person['id'] }}" @if($sale_trxes['m_employee_id']== $sales_person['id']) selected="selected" @endif>{{ $sales_person['name'] }}</option>
                                    @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>
                        <div class="form-group row align-items-center mb-0">
                            <label class="col-sm-2 text-right control-label col-form-label">Nama Site</label>
                            <div class="col-sm-4 border-left pb-2 pt-2">
                                <select id="site" name="site" onchange="getProjectName(this.value);" class="form-control select2 custom-select" style="width: 100%; height:32px;">
                                <option value="">--- Select Site ---</option>    
                                    @if($sites != null)
                                    @foreach($sites as $site)
                                    <option value="{{ $site['id'] }}" @if($sale_trxes['site_id']== $site['id']) selected="selected" @endif>{{ $site['name'] }}</option>
                                    @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>
                        <div class="form-group row align-items-center mb-0">
                            <label class="col-sm-2 text-right control-label col-form-label">Kavling</label>
                            <div class="col-sm-4 border-left pb-2 pt-2">
                                <select  id="project" name="project" onchange="setProjectRelatedData(this.value)" class="form-control select2 custom-select" style="width: 100%; height:32px;" >
                                    <option value="">--- Select Kavling ---</option>
                                    @if($projects != null)
                                    @foreach($projects as $project)
                                    <option value="{{ $project['id'] }}" @if($sale_trxes['project_id']== $project['id']) selected="selected" @endif>{{ $project['name'] }}</option>
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
                            <label for="total_discount" class="col-sm-2 text-right control-label col-form-label">Diskon</label>
                            <div class="col-sm-4 border-left pb-2 pt-2">
                                <input type="text"  class="form-control text-right" id="vw_total_discount" value="{{0}}" disabled ="true">
                                <input type="text"  class="form-control text-right" id="txt_total_discount" value="{{0}}" onchange="setnumber(this)" hidden="true">
                                <input type="number" id="total_discount" name="total_discount" value="{{0}}" hidden="true">
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
                                <input type="text"  class="form-control text-right" id="txt_ppn_amount" value="{{$sale_trxes['ppn_amount']}}" onchange="setnumber(this)">
                                <input type="number" id="ppn_amount" name="ppn_amount" value="{{$sale_trxes['ppn_amount']}}" hidden="true">
                            </div>
                        </div>
                        <div class="form-group row align-items-center mb-0">
                            <label for="bphtb_amount" class="col-sm-2 text-right control-label col-form-label">BPHTB</label>
                            <div class="col-sm-4 border-left pb-2 pt-2">
                                <input type="text"  class="form-control text-right" id="txt_bphtb_amount" value="{{$sale_trxes['pbhtb_amount']}}" onchange="setnumber(this)">
                                <input type="number" id="bphtb_amount" name="bphtb_amount" value="{{$sale_trxes['pbhtb_amount']}}" hidden="true">
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
                            <label class="col-sm-2 text-right control-label col-form-label">Tipe Pembayaran</label>
                            <div class="col-sm-4 border-left pb-2 pt-2">
                                <select id="payment_method" name="payment_method" class="form-control select2 custom-select" style="width: 100%; height:32px;"  onchange="selectedPaymentTypeChanged(value)">
                                    <option value="">--- Select Method ---</option>
                                    <option value="CASH" @if($sale_trxes['payment_method']== 'CASH') selected="selected" @endif>CASH</option>
                                    <option value="INHOUSE" @if($sale_trxes['payment_method']== 'INHOUSE') selected="selected" @endif>IN HOUSE CREDIT</option>
                                    <option value="KPR" @if($sale_trxes['payment_method']== 'KPR') selected="selected" @endif>KPR</option>
                                </select>
                            </div>
                        </div>
                        <div id="div_payment_cash" style="display:none">
                            <div class="form-group row align-items-center mb-0">
                                <label for="cash_due_day" class="col-sm-2 text-right control-label col-form-label">Jumlah Cash</label>
                                <div class="col-sm-4 border-left pb-2 pt-2">
                                    <input type="text"  class="form-control text-right" id="txt_cash_amount" value="" onchange="validateCash(this)">
                                    <input type="number"  class="form-control" id="cash_amount" name="cash_amount" value="" min="0"  hidden="true">
                                    <input type="number" class="form-control" id="cash_id" name="cash_id"  hidden="true">
                                </div>
                                <label for="cash_due_day" class="col-sm-2 text-right control-label col-form-label"></label>
                                <div class="border-left pb-2 pt-2">
                                    <label id='lbl_validator_cash' class="text-left control-label col-form-label" style="color:red"></label>
                                </div>
                            </div>
                            <div class="form-group row align-items-center mb-0">
                                <label for="cash_due_date" class="col-sm-2 text-right control-label col-form-label">Tanggal Jatuh Tempo</label>
                                <div class="col-sm-2 border-left pb-2 pt-2">
                                    <input type="date"  class="form-control" id="cash_due_date" name="cash_due_date" >
                                </div>
                            </div>
                        </div>
                        
                        <div id="div_payment_inhouse" style="display:none">
                            <div class="form-group row align-items-center mb-0">
                                <label class="col-sm-2 text-right control-label col-form-label">InHouse Credit</label>
                            </div>
                            <div class="form-group row align-items-center mb-0">
                                <label for="dp_inhouse" class="col-sm-2 text-right control-label col-form-label">Jumlah DP</label>
                                <div class="col-sm-4 border-left pb-2 pt-2">
                                    <input type="text"  class="form-control text-right" id="txt_dp_inhouse" value="" onchange="validateInhouse(this)">
                                    <input type="number"  class="form-control" id="dp_inhouse" name="dp_inhouse" value="" min="0" hidden="true">
                                </div>
                                <label id='lbl_validator_inhouse' class="col-sm-4 text-left control-label col-form-label" style="color:red"></label>
                            </div>
                            <div class="form-group row align-items-center mb-0">
                                <label for="dp_tenor_inhouse" class="col-sm-2 text-right control-label col-form-label">Tenor DP</label>
                                <div class="col-sm-1 border-left pb-2 pt-2">
                                    <input type="text"  class="form-control" id="dp_tenor_inhouse" name="dp_tenor_inhouse" value="1" >
                                </div>
                                <button type="button" class="btn btn-info btn-sm mb-2" style="bold" onclick="adrow_zero_config2.addRow()">+</button>
                            </div>
                            <div class="form-group row align-items-center mb-0">
                                <label class="col-sm-2 text-right control-label col-form-label"> </label>
                                <table id="zero_config2" class="table table-striped table-bordered col-sm-7">
                                    <thead>
                                        <tr>
                                            <th class="text-center">No</th>
                                            <th class="text-center">Tanggal Jatuh Tempo</th>
                                            <th class="text-center">Jumlah</th>
                                            <th class="text-center"> </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td class="tbl_id text-center">1</td>
                                            <td><input type="date" class="dp_inhouse_due_date form-control text-center" name="dp_inhouse_due_date[]"  id="dp_inhouse_due_date[]"  value="" /></td>
                                            <td><input type="text"  class="txt_dp_inhouse_amount form-control text-right" id="txt_dp_inhouse_amount[]" name="txt_dp_inhouse_amount[]" value="0" 
                                                onchange="updateTotal('dp_inhouse','inhouse_amount_validate','zero_config2','dp_inhouse_amount')"   />
                                                <input type="number" class="dp_inhouse_amount form-control text-right" name="dp_inhouse_amount[]"  id="dp_inhouse_amount[]" min="0" hidden="true"/>
                                                <input type="number" class="dp_inhouse_id form-control" id="dp_inhouse_id[]" name="dp_inhouse_id[]" hidden="true"/>
                                            </td>
                                            <td><i class="fas fa-trash-alt" onclick="adrow_zero_config2.delRow(this)"></i>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="form-group row align-items-center mb-0">
                                <label for="cash_due_day" class="col-sm-5 text-right control-label col-form-label">Sub Total</label>
                                <div class="col-sm-4 pb-2 pt-2">
                                    <input type="text"  class="form-control text-right" id="txt_inhouse_amount_validate" value="0" onchange="setnumber(this)">
                                    <input type="number" id="inhouse_amount_validate" name="inhouse_amount_validate" value="0" hidden="true">
                                </div>
                                <label id='lbl_inhouse_amount_validate' class="col-sm-2 text-left control-label col-form-label" style="color:red"></label>
                            </div>

                            <div class="form-group row align-items-center mb-0">
                                <label for="inst_tenor_inhouse" class="col-sm-2 text-right control-label col-form-label">Tenor Angsuran</label>
                                <div class="col-sm-1 border-left pb-2 pt-2">
                                    <input type="text"  class="form-control" id="inst_tenor_inhouse" name="inst_tenor_inhouse" value="1" >
                                </div>
                                <button type="button" class="btn btn-info btn-sm mb-2" style="bold" onclick="adrow_zero_config21.addRow()">+</button>
                            </div>
                            <div class="form-group row align-items-center mb-0">
                                <label class="col-sm-2 text-right control-label col-form-label"> </label>
                                <table id="zero_config21" class="table table-striped table-bordered col-sm-7">
                                    <thead>
                                        <tr>
                                            <th class="text-center">No</th>
                                            <th class="text-center">Tanggal Jatuh Tempo</th>
                                            <th class="text-center">Jumlah</th>
                                            <th class="text-center"> </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td class="tbl_id text-center">1</td>
                                            <td><input type="date" class="inst_inhouse_due_date form-control text-center" name="inst_inhouse_due_date[]"  id="inst_inhouse_due_date[]"  value="{{ $today_date }}" /></td>
                                            <td><input type="text"  class="txt_inst_inhouse_amount form-control text-right" id="txt_inst_inhouse_amount[]" name="txt_inst_inhouse_amount[]" value="0" 
                                                onchange="updateTotalInhouseInst('dp_inhouse','inst_inhouse_amount_validate','zero_config21','inst_inhouse_amount')"  />
                                                <input type="number" class="inst_inhouse_amount form-control text-right" name="inst_inhouse_amount[]"  id="inst_inhouse_amount[]" min="0" hidden="true"/>
                                                <input type="number" class="inst_inhouse_id form-control text-right" name="inst_inhouse_id[]"  id="inst_inhouse_id[]" min="0" hidden="true"/>
                                            </td>
                                            <td><i class="fas fa-trash-alt" onclick="adrow_zero_config21.delRow(this)"></i>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="form-group row align-items-center mb-0">
                                <label for="cash_due_day" class="col-sm-5 text-right control-label col-form-label">Sub Total</label>
                                <div class="col-sm-4 pb-2 pt-2">
                                    <input type="text"  class="form-control text-right" id="txt_inst_inhouse_amount_validate" value="0" onchange="setnumber(this)">
                                    <input type="number" id="inst_inhouse_amount_validate" name="inst_inhouse_amount_validate" value="0" hidden="true">
                                </div>
                                <label id='lbl_inst_inhouse_amount_validate' class="col-sm-2 text-left control-label col-form-label" style="color:red"></label>
                            </div>
                        </div>
                        
                        <div id="div_payment_kpr" style="display:none">
                            <div class="form-group row align-items-center mb-0">
                                <label class="col-sm-2 text-right control-label col-form-label">KPR Credit</label>
                            </div>
                            <div class="form-group row align-items-center mb-0">
                                <label for="dp_kpr" class="col-sm-2 text-right control-label col-form-label">Jumlah DP</label>
                                <div class="col-sm-4 border-left pb-2 pt-2">
                                    <input type="text"  class="form-control text-right" id="txt_dp_kpr" value="" onchange="validatekpr(this)">
                                    <input type="number"  class="form-control" id="dp_kpr" name="dp_kpr" value="" min="0" hidden="true">
                                </div>
                                <label id='lbl_validator_kpr' class="col-sm-4 text-left control-label col-form-label" style="color:red"></label>
                            </div>
                            <div class="form-group row align-items-center mb-0">
                                <label for="dp_tenor_kpr" class="col-sm-2 text-right control-label col-form-label">Tenor DP</label>
                                <div class="col-sm-1 border-left pb-2 pt-2">
                                    <input type="text"  class="form-control" id="dp_tenor_kpr" name="dp_tenor_kpr" value="1" >
                                </div>
                                <button type="button" class="btn btn-info btn-sm mb-2" style="bold" onclick="adrow_zero_config3.addRow()">+</button>
                            </div>
                            <div class="form-group row align-items-center mb-0">
                                <label class="col-sm-2 text-right control-label col-form-label"> </label>
                                <table id="zero_config3" class="table table-striped table-bordered col-sm-7">
                                    <thead>
                                        <tr>
                                            <th class="text-center">No</th>
                                            <th class="text-center">Tanggal Jatuh Tempo</th>
                                            <th class="text-center">Jumlah</th>
                                            <th class="text-center"> </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td class="tbl_id text-center">1</td>
                                            <td><input type="date" class="dp_kpr_due_date form-control text-center" name="dp_kpr_due_date[]"  id="dp_kpr_due_date[]"  value="" /></td>
                                            <td><input type="text"  class="txt_dp_kpr_amount form-control text-right" id="txt_dp_kpr_amount[]" name="txt_dp_kpr_amount[]" value="0" 
                                                onchange="updateTotal('dp_kpr','kpr_amount_validate','zero_config3','dp_kpr_amount')"  />
                                                <input type="number" class="dp_kpr_amount form-control text-right" name="dp_kpr_amount[]"  id="dp_kpr_amount[]" min="0" hidden="true"/>
                                                <input type="number" class="dp_kpr_id form-control" id="dp_kpr_id[]" name="dp_kpr_id[]"  hidden="true"/>
                                            </td>
                                            <td><i class="fas fa-trash-alt" onclick="adrow_zero_config3.delRow(this)"></i>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="form-group row align-items-center mb-0">
                                <label for="cash_due_day" class="col-sm-5 text-right control-label col-form-label">Sub Total</label>
                                <div class="col-sm-4 pb-2 pt-2">
                                    <input type="text"  class="form-control text-right" id="txt_kpr_amount_validate" value="0" onchange="setnumber(this)">
                                    <input type="number" id="kpr_amount_validate" name="kpr_amount_validate" value="0" hidden="true">
                                </div>
                                <label id='lbl_kpr_amount_validate' class="col-sm-2 text-left control-label col-form-label" style="color:red"></label>
                            </div>
                            <div class="form-group row align-items-center mb-0">
                                <label for="inst_kpr" class="col-sm-2 text-right control-label col-form-label">Total jumlah KPR</label>
                                <div class="col-sm-4 border-left pb-2 pt-2">
                                    <input type="text"  class="form-control text-right" id="vw_inst_kpr" value="" disabled="true">
                                    <input type="text"  class="form-control text-right" id="txt_inst_kpr" value="" onchange="validatekpr(this)" hidden="true">
                                    <input type="number" id="inst_kpr" name="inst_kpr" value="" min="0" hidden="true">
                                </div>
                                <label id='lbl_validator_inst_kpr' class="col-sm-4 text-left control-label col-form-label" style="color:red"></label>
                            </div>
                            <div class="form-group row align-items-center mb-0">
                                <label for="inst_tenor_kpr" class="col-sm-2 text-right control-label col-form-label">Tenor KPR</label>
                                <div class="col-sm-1 border-left pb-2 pt-2">
                                    <input type="text"  class="form-control" id="inst_tenor_kpr" name="inst_tenor_kpr" onchange="calculateKPR(this)">
                                </div>
                            </div>
                            <div class="form-group row align-items-center mb-0">
                                <label for="inst_tenor_kpr" class="col-sm-2 text-right control-label col-form-label">Tanggal jatuh tempo</label>
                                <div class="col-sm-1 border-left pb-2 pt-2">
                                    <select name="inst_date_kpr" class="form-control select2 custom-select" style="width: 100%; height:32px;">
                                        @for($i = 1; $i < 31; $i++)
                                        <option value="{{$i}}">{{$i}}</option>
                                        @endfor
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row align-items-center mb-0">
                                <label for="inst_tenor_kpr" class="col-sm-2 text-right control-label col-form-label">Jumlah angsuran perbulan</label>
                                <div class="col-sm-4 border-left pb-2 pt-2">
                                    <input type="text"  class="form-control text-right" id="txt_inst_kpr_amount" value="" onchange="setnumber(this)">
                                    <input type="number" id="inst_kpr_amount" name="inst_kpr_amount" value="" min="0" hidden="true">
                                    <input type="number" id="inst_kpr_id" name="inst_kpr_id" value="" min="0" hidden="true">
                                </div>
                            </div>
                            <div class="form-group row align-items-center mb-0">
                                <label class="col-sm-2 text-right control-label col-form-label">Nama Bank KPR</label>
                                <div class="col-sm-4 border-left pb-2 pt-2">
                                    <select  id="bank_account" name="bank_account" onchange="getKprBankPayment(this.value)" class="form-control select2 custom-select" style="width: 100%; height:32px;" >
                                        <option value="">--- Select BANK ---</option>
                                        @if($kpr_banks != null)
                                        @foreach($kpr_banks as $kpr_bank) 
                                        <option value="{{ $kpr_bank['bank_code'] }}" >{{ $kpr_bank['bank_name'] }}</option>
                                        @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>

                            <div id="div_doclists_kpr" >
                                <div class="form-group row align-items-center mb-0">
                                    <label class="col-sm-2 text-right control-label col-form-label">Pencairan KPR oleh bank</label>
                                </div>
                                <div class="form-group row align-items-center mb-0">
                                    <label class="col-sm-2 text-right control-label col-form-label"> </label>

                                    <table id="zero_config32" class="table table-striped table-bordered col-sm-7">
                                        <thead>
                                            <tr>
                                                <th class="text-center">No</th>
                                                <th class="text-center">Progress</th>
                                                <th class="text-center">persen Pencairan</th>
                                                <th class="text-center">Jumlah Pencairan</th>
                                                <th class="text-center">Due Date</th>
                                            </tr>
                                        </thead>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <br><br>
                        <div class="row">
                            <div class="col-sm-2"></div>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <a href="{{ URL::to('ppjbrecord') }}"><button type="button" class="btn btn-danger mb-2">Cancel</button></a>
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

function formatDate(jsDate)
{
    let dd = jsDate.getDate();
    let mm = jsDate.getMonth() + 3;
    let y = jsDate.getFullYear();

    return someFormattedDate = y + '-' + ( mm < 10 ? ('0' + mm) : mm ) + '-' + ( dd < 10 ? ('0' + dd) : dd );
}

function setnumber(obj)
{
    var objectName = obj.id.toString().replace("txt_","");
    var numberValue = obj.value.toString().replace(/\B(?=(\d{3})+(?!\d))/g,",");
    $("#txt_"+objectName).val(numberValue);
    $("#vw_"+objectName).val(numberValue);
    $("#"+objectName).val(parseInt(numberValue.replace(/,/g,"")));
}

var Spu_Date = "{{$today_date}}";
$(document).ready(function(){
    let spudate = new Date(Spu_Date);
    let year = parseInt(spudate.getFullYear());
    let month = parseInt(spudate.getMonth());
    let day = parseInt(spudate.getDate())+14;
    let newDate = new Date(year,month,day);

    $("#cash_due_date").val(formatDate(newDate));

    var dateDPInhouse = document.querySelectorAll('#zero_config2 .dp_inhouse_due_date');
    dateDPInhouse[0].value = formatDate(newDate);

    var dateDPKPR = document.querySelectorAll('#zero_config3 .dp_kpr_due_date');
    dateDPKPR[0].value = formatDate(newDate);
});

//DROPDOWN
var custData = [];
function setCustomerRelatedData(cust_id)
{
    formSpuNo = $('[id^=spuno]');
    formSpuNo.empty();
    formSpuNo.append('<option value="">--- Select SPU ---</option>');

    $.ajax({
        type: "GET",
        url: "{{ URL::to('content/spucust') }}" +"/"+ cust_id, //json get site
        dataType : 'json',
        success: function(response){
            arrData = response['data'];
            selected='';
            for(i = 0; i < arrData.length; i++){            
                formSpuNo.append('<option value="'+arrData[i]['id']+'">'+arrData[i]['no']+'</option>');
            }
        }
    });

    $.ajax({
        type: "GET",
        url: "{{ URL::to('content/cust') }}" +"/"+ cust_id, //json get site
        dataType : 'json',
        success: function(response){
            arrData = response['data'];
            $("#owner_name").val(arrData['name']).change();
            $("#residence_address").val(arrData['address']).change();
            $("#residence_zipcode").val(arrData['zipcode']).change();
            $("#residence_rt").val(arrData['rt']).change();
            $("#residence_rw").val(arrData['rw']).change();
            $("#residence_kecamatan").val(arrData['kecamatan']).change();
            $("#residence_kelurahan").val(arrData['kelurahan']).change();
            $("#residence_city").val(arrData['city']).change();
            
            $("#legal_address").val(arrData['address']).change();
            $("#legal_zipcode").val(arrData['zipcode']).change();
            $("#legal_rt").val(arrData['rt']).change();
            $("#legal_rw").val(arrData['rw']).change();
            $("#legal_kecamatan").val(arrData['kecamatan']).change();
            $("#legal_kelurahan").val(arrData['kelurahan']).change();
            $("#legal_city").val(arrData['city']).change();
        }
    });
}

function setSpuRelatedData(spu_id)
{
    $.ajax({
        type: "GET",
        url: "{{ URL::to('content/spudata') }}" +"/"+ spu_id, //json get site
        dataType : 'json',
        success: function(response){
            arrData = response['data'];
            custData = response['data'];
            $("#sales_person").val(arrData['m_employee_id']).change();
            $("#site").val(arrData['site_id']).change();
            $("#project").val(arrData['project_id']).change();
            $("#txt_specup_amount").val(arrData['specup_amount']).change();
            custData['project_id']=arrData['project_id'];
            custData['specup_amount']=arrData['amount'];

            //PPJB
            $("#payment_method").val(arrData['payment_method']).change();
            $("#txt_nup_amount").val(arrData['booking_amount']).change();
            $("#owner_name").val(arrData['owner_name']).change();
            $("#owner_name").val(arrData['name']).change();

            $("#residence_address").val(arrData['residence_address']).change();
            $("#residence_zipcode").val(arrData['residence_zipcode']).change();
            $("#residence_rt").val(arrData['residence_rt']).change();
            $("#residence_rw").val(arrData['residence_rw']).change();
            $("#residence_kecamatan").val(arrData['residence_kecamatan']).change();
            $("#residence_kelurahan").val(arrData['residence_kelurahan']).change();
            $("#residence_city").val(arrData['residence_city']).change();
            
            $("#legal_address").val(arrData['legal_address']).change();
            $("#legal_zipcode").val(arrData['legal_zipcode']).change();
            $("#legal_rt").val(arrData['legal_rt']).change();
            $("#legal_rw").val(arrData['legal_rw']).change();
            $("#legal_kecamatan").val(arrData['legal_kecamatan']).change();
            $("#legal_kelurahan").val(arrData['legal_kelurahan']).change();
            $("#legal_city").val(arrData['legal_city']).change();

            $("#deal_type").val(arrData['deal_type']).change();
            
            $("#txt_total_price_amount").val(arrData['total_amount']).change();
            $("#txt_total_discount").val(arrData['total_discount']).change();
            $("#txt_ppn_amount").val(arrData['ppn_amount']).change();
            $("#txt_bphtb_amount").val(arrData['pbhtb_amount']).change();
            $("#txt_fasum_amount").val(arrData['fasum_fee']).change();
            $("#txt_notary_amount").val(arrData['notary_fee']).change();


            // selectedPaymentTypeChanged(arrData['payment_method']);
            if(arrData['payment_method'] == 'CASH')
            {
                $("#txt_cash_amount").val((arrData['cash_amount']).toString().replace(/\B(?=(\d{3})+(?!\d))/g,",")).change();
            }
            else if(arrData['payment_method'] == 'INHOUSE')
            {
                $("#txt_dp_inhouse").val((arrData['dp_inhouse_amount']).toString().replace(/\B(?=(\d{3})+(?!\d))/g,",")).change();
                for(i = 0; i < arrData['inhouse_dp'][0]['tenor']; i++)
                {
                    if (i >0 ) {adrow_zero_config2.addRow();}
                }
                var detail_amounts = document.querySelectorAll('#zero_config2 .txt_dp_inhouse_amount');
                var detail_dates = document.querySelectorAll('#zero_config2 .dp_inhouse_due_date');
                var detail_inst_dates = document.querySelectorAll('#zero_config21 .inst_inhouse_due_date');
                // var detail_ids = document.querySelectorAll('#zero_config2 .dp_inhouse_id');
                for(i = 0; i < arrData['inhouse_dp'][0]['tenor']; i++)
                {
                    detail_amounts[i].value = arrData['inhouse_dp'][i]['amount'];
                    detail_dates[i].value = arrData['inhouse_dp'][i]['due_date'];
                    // detail_ids[i].value = customerRelatedData['inhouse_dp'][i]['id'];

                    let init = detail_dates[i].value;
                    let initDate = new Date(init);
                    let year = parseInt(initDate.getFullYear());
                    let month = parseInt(initDate.getMonth())+1;
                    let day = parseInt(initDate.getDate());
                    let newDate = new Date(year,month,day);
                    detail_inst_dates[0].value = formatDate(newDate);
                    
                }
                updateTotal('dp_inhouse','inhouse_amount_validate','zero_config2','dp_inhouse_amount');
                for(i = 0; i < arrData['inhouse_inst'][0]['tenor']; i++)
                {
                    if (i >0 ) {adrow_zero_config21.addRow();}
                }
                var detail_amounts_inst = document.querySelectorAll('#zero_config21 .txt_inst_inhouse_amount');
                var detail_dates_inst = document.querySelectorAll('#zero_config21 .inst_inhouse_due_date');
                var detail_ids_inst = document.querySelectorAll('#zero_config21 .inst_inhouse_id');
                for(i = 0; i <arrData['inhouse_inst'][0]['tenor']; i++)
                {
                    detail_amounts_inst[i].value = arrData['inhouse_inst'][i]['amount'];
                    detail_dates_inst[i].value = arrData['inhouse_inst'][i]['due_date'];
                    detail_ids_inst[i].value = arrData['inhouse_inst'][i]['id'];
                }
                updateTotalInhouseInst('dp_inhouse','inst_inhouse_amount_validate','zero_config21','inst_inhouse_amount');
            }
            else if(arrData['payment_method'] == 'KPR')
            {
                $("#txt_dp_kpr").val((arrData['dp_kpr_amount']).toString().replace(/\B(?=(\d{3})+(?!\d))/g,",")).change();
                for(i = 0; i < arrData['kpr_dp'][0]['tenor']; i++)
                {
                    if (i >0 ) {adrow_zero_config3.addRow();}
                }
                var detail_amounts = document.querySelectorAll('#zero_config3 .txt_dp_kpr_amount');
                var detail_dates = document.querySelectorAll('#zero_config3 .dp_kpr_due_date');
                for(i = 0; i < arrData['kpr_dp'][0]['tenor']; i++)
                {
                    detail_amounts[i].value = arrData['kpr_dp'][i]['amount'];
                    detail_dates[i].value = arrData['kpr_dp'][i]['due_date'];
                }
                $("#txt_inst_kpr").val((arrData['kpr_inst'][0]['amount'] * arrData['kpr_inst'][0]['tenor']).toString().replace(/\B(?=(\d{3})+(?!\d))/g,",")).change();
                $("#inst_tenor_kpr").val(arrData['kpr_inst'][0]['tenor']).change();
                $("#inst_date_kpr").val(arrData['kpr_inst'][0]['due_day']).change();
                $("#txt_inst_kpr_amount").val((arrData['kpr_inst'][0]['amount']).toString().replace(/\B(?=(\d{3})+(?!\d))/g,",")).change();
                updateTotal('dp_kpr','kpr_amount_validate','zero_config3','dp_kpr_amount');
                validatekpr($("#txt_dp_kpr")[0]);
                calculateKPR();
            }
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
            if(custData['project_id']!=null) 
            {
                $("#project").val(custData['project_id']).change();
            
            }
        }
    });
}

function setProjectRelatedData(project_id)
{
    if (project_id != "") 
    {
        custData['project_id'] = $("#project").val();
        $.ajax({
            type: "GET",
            url: "{{ URL::to('content/projectbyid') }}"+"/"+ custData['project_id'], //json get site
            dataType : 'json',
            success: function(response){
                project = response['data'];
                var pph_percent = 0;
                var pbhtb_percent = 0;
                var fasum_fee = 0;
                var pbhtb_percent = 0;
                var notary_fee = 0;
                if(project!=null)
                {
                    if(custData['project_id'] != "" )$("#txt_kavling_price").val(project['base_price']).change();
                    $.ajax({
                        type: "GET",
                        url: "{{ URL::to('content/pphpercent') }}", //json get site
                        dataType : 'json',
                        success: function(response){
                            pph_percent = response['data'];
                            $("#txt_ppn_amount").val((parseInt(project['base_price'])*(parseInt(pph_percent[0]['gs_value'])/100)).toString().replace(/\B(?=(\d{3})+(?!\d))/g,",")).change();
                            updatePrice();
                        }
                    });
                    $.ajax({
                        type: "GET",
                        url: "{{ URL::to('content/pbhtbpercent') }}", //json get site
                        dataType : 'json',
                        success: function(response){
                            pbhtb_percent = response['data'];
                            $("#txt_bphtb_amount").val((parseInt(project['base_price'])*(parseInt(pbhtb_percent[0]['gs_value'])/100)).toString().replace(/\B(?=(\d{3})+(?!\d))/g,",")).change();
                            updatePrice();
                        }
                    });
                    $.ajax({
                        type: "GET",
                        url: "{{ URL::to('content/fasumfee') }}", //json get site
                        dataType : 'json',
                        success: function(response){
                            fasum_fee = response['data'];
                            $("#txt_fasum_amount").val(((parseInt(fasum_fee[0]['gs_value']))).toString().replace(/\B(?=(\d{3})+(?!\d))/g,",")).change();
                            updatePrice();
                        }
                    });
                    $.ajax({
                        type: "GET",
                        url: "{{ URL::to('content/notaryfee') }}", //json get site
                        dataType : 'json',
                        success: function(response){
                            notary_fee = response['data'];
                            $("#txt_notary_amount").val(((parseInt(notary_fee[0]['gs_value']))).toString().replace(/\B(?=(\d{3})+(?!\d))/g,",")).change();
                            updatePrice();
                        }
                    });
                    
                }
            }
        });
    }
    
}

//TABLE PAYMENTS
var adrow_zero_config1 = new addRowsTable('zero_config1','tenor_cash','cash_amount_validate','cash_due_date');
var adrow_zero_config2 = new addRowsTable('zero_config2','dp_tenor_inhouse','inhouse_amount_validate','dp_inhouse_due_date');
var adrow_zero_config3 = new addRowsTable('zero_config3','dp_tenor_kpr','kpr_amount_validate','dp_kpr_due_date');
var adrow_zero_config21 = new addRowsTable('zero_config21','inst_tenor_inhouse','inst_inhouse_amount_validate','inst_inhouse_due_date');


function addRowsTable(id,counterid,validatorAmount,dateId)
{
  var table = document.getElementById(id);
  var me = this;
  var counter = counterid;
  var validatorAmount = validatorAmount;
  var dateid = dateId;
  if(document.getElementById(id)){
    var row1 = table.rows[1].outerHTML;
    
    //adds index-id in cols with class .tbl_id
    function setIds(counter){
      var tbl_id = document.querySelectorAll('#'+ id +' .tbl_id');
      var date = document.querySelectorAll('#'+id+' .'+dateid);

      for(var i=0; i<tbl_id.length; i++)
        { 
            tbl_id[i].innerHTML = i+1;
            $("#"+counter).val(tbl_id.length).change();
            if ( i==0 && (id =='zero_config2' || id =='zero_config3' ) )
            {
                let spu = $("#trx_date").val();
                let spudate = new Date(spu);
                let year = parseInt(spudate.getFullYear());
                let month = parseInt(spudate.getMonth());
                let day = parseInt(spudate.getDate())+14;
                let newDate = new Date(year,month,day);
                date[i].value = formatDate(newDate);
            }
            else if ( i!=0 && id =='zero_config2') 
            {
                let init = date[i-1].value;
                let initDate = new Date(init);
                let year = parseInt(initDate.getFullYear());
                let month = parseInt(initDate.getMonth())+1;
                let day = parseInt(initDate.getDate());
                let newDate = new Date(year,month,day);
                date[i].value = formatDate(newDate);

                var dateinst = document.querySelectorAll('#zero_config21 .inst_inhouse_due_date');
                let instDate = new Date(year,month+1,day);
                dateinst[0].value = formatDate(instDate);
            }
            else if ( i==0 && id =='zero_config21') 
            {
                var dateinit = document.querySelectorAll('#zero_config2 .dp_inhouse_due_date');
                let init = dateinit[dateinit.length - 1].value;
                let initDate = new Date(init);
                let year = parseInt(initDate.getFullYear());
                let month = parseInt(initDate.getMonth())+1;
                let day = parseInt(initDate.getDate());
                let newDate = new Date(year,month,day);
                date[i].value = formatDate(newDate);
            }
            else{
                let init = date[i-1].value;
                let initDate = new Date(init);
                let year = parseInt(initDate.getFullYear());
                let month = parseInt(initDate.getMonth())+1;
                let day = parseInt(initDate.getDate());
                let newDate = new Date(year,month,day);
                date[i].value = formatDate(newDate);
            }
        }  
    }

    //add row after clicked row; receives clicked button in row
    me.addRow = function(btn){
      btn ? btn.parentNode.parentNode.insertAdjacentHTML('afterend', row1): table.insertAdjacentHTML('beforeend',row1);
      setIds(counter);
    }

    //delete clicked row; receives clicked button in row
    me.delRow = function(btn){
      btn.parentNode.parentNode.outerHTML ='';
      setIds(counter);
      if(id =='zero_config2')
      {
        updateTotal('dp_inhouse','inhouse_amount_validate','zero_config2','dp_inhouse_amount'); 
        // updateTotalInhouseInst('dp_inhouse','inst_inhouse_amount_validate','zero_config21','inst_inhouse_amount');
      }
      else if(id =='zero_config21')
      {
        updateTotalInhouseInst('dp_inhouse','inst_inhouse_amount_validate','zero_config21','inst_inhouse_amount');
      }
      else if(id =='zero_config3')
      {
        updateTotal('dp_kpr','kpr_amount_validate','zero_config3','dp_kpr_amount');
      }
      //$("#"+sumid).val(0).change();
    }
  }
}

function selectedPaymentTypeChanged(paymentType)
{
    
    if(paymentType == 'CASH')
    {
        var style = document.getElementById('div_payment_cash').style;
        style.display = "block";
        var style = document.getElementById('div_payment_inhouse').style;
        style.display = "none";
        var style = document.getElementById('div_payment_kpr').style;
        style.display = "none";
    }
    else if(paymentType == 'INHOUSE')
    {
        var style = document.getElementById('div_payment_cash').style;
        style.display = "none";
        var style = document.getElementById('div_payment_inhouse').style;
        style.display = "block";
        var style = document.getElementById('div_payment_kpr').style;
        style.display = "none";
    }
    else if(paymentType == 'KPR')
    {
        var style = document.getElementById('div_payment_cash').style;
        style.display = "none";
        var style = document.getElementById('div_payment_inhouse').style;
        style.display = "none";
        var style = document.getElementById('div_payment_kpr').style;
        style.display = "block";
    }
    else if(paymentType == '')
    {
        var style = document.getElementById('div_payment_cash').style;
        style.display = "none";
        var style = document.getElementById('div_payment_inhouse').style;
        style.display = "none";
        var style = document.getElementById('div_payment_kpr').style;
        style.display = "none";
    }
};

var t32 = $('#zero_config32').DataTable();
function getKprBankPayment(bankcode){
    $.ajax({
        type: "GET",
        url: "{{ URL::to('content/kprbank') }}"+"/"+bankcode.toLowerCase(), //json get site
        dataType : 'json',
        success: function(response){
            bank_payments = response['data'];
            if(bank_payments!=null)
            {
                var totalKpr = document.getElementById('inst_kpr');
                t32.clear().draw();
                for(i = 0; i < bank_payments.length; i++){
                    var cairKpr = parseInt(parseInt(bank_payments[i]['payment_percent']) * parseInt(totalKpr.value) / 100)
                    var cairKpr_txt = cairKpr.toString().replace(/\B(?=(\d{3})+(?!\d))/g,",");
                    t32.row.add([
                        '<td class="tbl_id text-center">'+(parseInt(i)+1)+'<input type="text"  class="kpr_bank_id form-control text-center" id="kpr_bank_id[]" name="kpr_bank_id[]"  value="'+bank_payments[i]['id']+'" hidden="true"/></td>'
                        +'<input type="text"  class="trx_kpr_bank_id form-control text-center" id="trx_kpr_bank_id[]" name="trx_kpr_bank_id[]" hidden="true"/></td>'
                        ,'<td><input type="text"  class="kpr_bank_progress form-control text-center" id="kpr_bank_progress[]" name="kpr_bank_progress[]"  value="'+bank_payments[i]['progress_category']+'" disabled="true"/></td>'
                        ,'<td><input type="text"  class="kpr_bank_percent form-control text-center" id="kpr_bank_percent[]" name="kpr_bank_percent[]" value="'+bank_payments[i]['payment_percent']+'"  disabled="true"/></td>'
                        ,'<td><input type="text"  class="txt_kpr_bank_amount form-control text-right" id="txt_kpr_bank_amount[]" name="txt_kpr_bank_amount[]" value="'+cairKpr_txt+'"  disabled="true"/> '
                        +'<input type="text"  class="kpr_bank_amount form-control text-center" id="kpr_bank_amount[]" name="kpr_bank_amount[]" value="'+cairKpr+'"  hidden="true"/> </td>'
                        ,'<td><input type="date" class="kpr_bank_plan_date form-control text-center" name="kpr_bank_plan_date[]"  id="kpr_bank_plan_date[]" /></td>'
                    ]).draw(false);
                }
            }
        }
    });
}

function updatePrice()
{
    kavling_price = $('#kavling_price')[0];
    if((kavling_price.value)!="") { kavling_price = parseInt(kavling_price.value) }else{ kavling_price = 0 } ;
    booking_amount = $('#nup_amount')[0];
    if((booking_amount.value)!="") { booking_amount = parseInt(booking_amount.value) }else{ booking_amount = 0 } ;
    discount_amount = $('#total_discount')[0];
    if((discount_amount.value)!="") { discount_amount = parseInt(discount_amount.value) }else{ discount_amount = 0 } ;
    ppn_amount = $('#ppn_amount')[0];
    if((ppn_amount.value)!="") { ppn_amount = parseInt(ppn_amount.value) }else{ ppn_amount = 0 } ;
    bphtb_amount = $('#bphtb_amount')[0];
    if((bphtb_amount.value)!="") { bphtb_amount = parseInt(bphtb_amount.value) }else{ bphtb_amount = 0 } ;
    fasumamount = $('#fasum_amount')[0];
    if((fasumamount.value)!="") { fasumamount = parseInt(fasumamount.value) }else{ fasumamount = 0 } ;
    notaryamount = $('#notary_amount')[0];
    if((notaryamount.value)!="") { notaryamount = parseInt(notaryamount.value) }else{ notaryamount = 0 } ;
    specup_amount = $('#specup_amount')[0];
    if((specup_amount.value)!="") { specup_amount = parseInt(specup_amount.value) }else{ specup_amount = 0 } ;
    
    os_amount =   kavling_price - discount_amount - booking_amount + ppn_amount + bphtb_amount + fasumamount + notaryamount + specup_amount;
    $("#txt_total_price_amount").val(os_amount.toString().replace(/\B(?=(\d{3})+(?!\d))/g,",")).change();
}

function updateTotal(minvalue,totalid,tableid,classname)
{
    var validatorAmount = document.getElementById(totalid);
    var txtvalidatorAmount = document.getElementById('txt_'+totalid);
    var lblvalidatorAmount = document.getElementById('lbl_'+totalid);
    
    var total_minvalue = parseInt(document.getElementById(minvalue).value);
    
    var table = document.getElementById(tableid);
    var details = document.querySelectorAll('#'+tableid+' .'+classname);
    var txtdetails = document.querySelectorAll('#'+tableid+' .txt_'+classname);
    var total = 0;
    for(var i=0; i<details.length; i++) 
    {
        var numberValue = (txtdetails[i].value!=""?txtdetails[i].value:0).toString().replace(/\B(?=(\d{3})+(?!\d))/g,",");
        txtdetails[i].value = numberValue;
        details[i].value = parseInt(numberValue.replace(/,/g,""));
        total = parseInt(total) + parseInt(details[i].value!=""?details[i].value:0);
    }
    validatorAmount.value = total;
    txtvalidatorAmount.value = total.toString().replace(/\B(?=(\d{3})+(?!\d))/g,",");
    if(parseInt(total) != parseInt(total_minvalue))
    {
        lblvalidatorAmount.innerHTML = '* Total pembayaran harus : '+total_minvalue.toString().replace(/\B(?=(\d{3})+(?!\d))/g,","); 
    }else{
        lblvalidatorAmount.innerHTML = '';
    }

}

//CASH
function validateCash(input)
{
    setnumber(input);
    os_amount = $('#total_price_amount')[0];
    var validator = document.getElementById('lbl_validator_cash');
    var invalue =parseInt(input.value.toString().replace(/,/g,""));
    if (invalue < parseInt(os_amount.value.toString().replace(/,/g,"")))
    { 
        validator.innerHTML = '* Nilai pembayaran minimal : '+os_amount.value.toString().replace(/\B(?=(\d{3})+(?!\d))/g,","); 
        input.min=os_amount;        
    }
    else validator.innerHTML = '';
}

//INHOUSE
function validateInhouse(input)
{
    setnumber(input);
    os_amount = $('#total_price_amount')[0];
    os_amount = (parseInt(os_amount.value) * 0.5);
    var invalue =parseInt(input.value.toString().replace(/,/g,""));
    var validator = document.getElementById('lbl_validator_inhouse');
    var validatorAmountDP = document.getElementById('inhouse_amount_validate');
    var validatorAmountInst = document.getElementById('inst_inhouse_amount_validate');
    validatorAmountDP.min = parseInt(input.value);
    validatorAmountInst.min =  parseInt(os_amount) - parseInt(input.value);
    if (invalue < os_amount  )
    { 
        validator.innerHTML = '*DP minimal :'+os_amount.toString().replace(/\B(?=(\d{3})+(?!\d))/g,","); 
        input.min=os_amount;        
    }
    else validator.innerHTML = '';
}

function updateTotalInhouseInst(minvalue,totalid,tableid,classname)
{
    os_amount = $('#total_price_amount')[0];
    var validatorAmount = document.getElementById(totalid);
    var txtvalidatorAmount = document.getElementById('txt_'+totalid);
    var lblvalidatorAmount = document.getElementById('lbl_'+totalid);

    os_amount = $('#total_price_amount')[0];
    dp_amount = document.getElementById(minvalue);
    var total_minvalue = parseInt(os_amount.value) - parseInt(dp_amount.value);
    
    var table = document.getElementById(tableid);
    var details = document.querySelectorAll('#'+tableid+' .'+classname);
    var txtdetails = document.querySelectorAll('#'+tableid+' .txt_'+classname);
    var total = 0;
    for(var i=0; i<details.length; i++) 
    {
        var numberValue = (txtdetails[i].value!=""?txtdetails[i].value:0).toString().replace(/\B(?=(\d{3})+(?!\d))/g,",");
        txtdetails[i].value = numberValue;
        details[i].value = parseInt(numberValue.replace(/,/g,""));
        total = parseInt(total) + parseInt(details[i].value!=""?details[i].value:0);
    }
    validatorAmount.value = total;
    txtvalidatorAmount.value = total.toString().replace(/\B(?=(\d{3})+(?!\d))/g,",");
    if(parseInt(total) != parseInt(total_minvalue))
    {
        lblvalidatorAmount.innerHTML = '* Total pembayaran harus : '+total_minvalue.toString().replace(/\B(?=(\d{3})+(?!\d))/g,","); 
    }else{
        lblvalidatorAmount.innerHTML = '';
    }
}


//KPR
function validatekpr(input)
{
    setnumber(input);
    os_amount = $('#total_price_amount')[0];
    dpkpr = $('#dp_kpr')[0];
    instkpr = $('#txt_inst_kpr')[0];
    
    var validator = document.getElementById('lbl_validator_kpr');
    var validatorInst = document.getElementById('lbl_validator_inst_kpr');
    var validatorAmountDP = document.getElementById('kpr_amount_validate');
    
    var objectName = input.id.toString().replace("txt_","");
    var numvalue = document.getElementById(objectName);
    
    instkpr.value= parseInt(os_amount.value) - parseInt(dpkpr.value);
    setnumber(instkpr);
}

function calculateKPR()
{
    var totalKpr = document.getElementById('inst_kpr');
    var tenorKpr = document.getElementById('inst_tenor_kpr');
    var instKpr = document.getElementById('txt_inst_kpr_amount');
    var instPerTenor = parseInt(totalKpr.value)/parseInt(tenorKpr.value);
    $("#txt_inst_kpr_amount").val((Number(Math.round(instPerTenor+'e2') + 'e-2')).toString().replace(/\B(?=(\d{3})+(?!\d))/g,",")).change();
}

//DOCUMENTS
function checkDueDate(obj)
{
    var objectName = obj.id.toString().replace("is_checked","due_date");
    if (!obj.checked)
    {
        document.getElementById(objectName).required= true;
        document.getElementById(objectName).disabled = false;
    } else {
        document.getElementById(objectName).disabled = true;
    }
}
</script>



@endsection