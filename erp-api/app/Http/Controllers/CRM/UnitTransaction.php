<?php
namespace App\Http\Controllers\CRM;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\CustomerFinancial;
use App\Models\FollowupHistory;
use App\Models\SaleTrx;
use App\Models\SaleTrxD;
use App\Models\SaleTrxDoc;
use App\Models\SaleTrxKprBankPayment;
use App\Models\DiscountRequest;
use App\Models\MDocType;
use App\Models\Project;
use App\Models\RabRequest;

use App\Models\MSequence;
use App\Models\Site;
use DB;

class UnitTransaction extends Controller
{
    public function getKprBankName()
    {
        $objects = DB::select("SELECT DISTINCT bank_code, bank_name FROM m_kpr_bank_payments");

        return response()->json(['data'=>$objects]);
    }

    public function getKprBankPaymentSchemeByName($code)
    {
        $objects = DB::select("SELECT id, bank_name, progress_category, payment_percent FROM m_kpr_bank_payments WHERE lower(bank_code) = '".$code."'");

        return response()->json(['data'=>$objects]);
    }

    public function getProjectRelatedData($id)
    {
        $project = Project::find($id);
        $discount_amount =  DiscountRequest::select('amount') -> where('project_id',$id) -> where('is_approved',true)->first();
        $project['discount_amount'] = json_decode(json_encode($discount_amount),true)['amount'];

        return response()->json(['data'=>$project]);
    }

    public function getSaleTrxById($id)
    {
        $saletrx =  SaleTrx::find($id);
        $projectId = json_decode(json_encode($saletrx),true)['project_id'];
        $site_id =  Project::select('site_id') -> where('id',$projectId) -> first();
        $saletrx['site_id'] = json_decode(json_encode($site_id),true)['site_id'];

        $payments = explode(',','kavling,book,cash,kpr,kpr_dp,kpr_inst,inhouse,inhouse_dp,inhouse_inst');
        $details = SaleTrxD::where('sale_trx_id',$id)->get();
        $details= json_decode(json_encode($details),true);
        foreach($payments as $payment)
        {
            $j=0;
            $detail=null;
            for($i = 0; $i < count($details); $i++)
            {
                if(strtolower($details[$i]['trx_d_code'])==$payment)
                {
                    $detail[$j] = $details[$i];
                    $j++;
                }

            }
            $saletrx[$payment]=$detail;
        }

        $docs = MDocType::all();
        $details = SaleTrxDoc::where('sale_trx_id',$id)->get();
        $details= json_decode(json_encode($details),true);

        $doctype='';
        foreach($docs as $doc)
        {
            if($doctype!=$doc['type'])
            {
                $j=0;
                $detail=null;
                $doctype=$doc['type'];
            }
            for($i = 0; $i < count($details); $i++)
            {
                if(strtolower($details[$i]['m_doc_type_id'])==$doc['id'])
                {
                    $detail[$j] = $details[$i];
                    $j++;
                }
            }
            $saletrx[$doctype]=$detail;
        }
        // dd($saletrx);
        return response()->json(['data'=>$saletrx]);
    }


    public function getCustomerSpu($id)
    {
        $saletrx =  SaleTrx::select('id','no') -> where('customer_id',$id) -> where('trx_type','SPU') ->get();
        return response()->json(['data'=>$saletrx]);
    }
    public function getCustomerNup($id)
    {
        $saletrx =  SaleTrx::select('id','no') -> where('customer_id',$id) -> where('trx_type','NUP') ->get();
        return response()->json(['data'=>$saletrx]);
    }
    public function getCustomerBok($id)
    {
        $saletrx =  SaleTrx::select('id','no') -> where('customer_id',$id) -> where('trx_type','BOK') ->get();
        return response()->json(['data'=>$saletrx]);
    }

    public function getSpuRelatedData($id)
    {
        $saletrx =  SaleTrx::find($id);
        $projectId = json_decode(json_encode($saletrx),true)['project_id'];

        $site_id =  Project::select('site_id') -> where('id',$projectId) -> first();
        $saletrx['site_id'] = json_decode(json_encode($site_id),true)['site_id'];

        $total_discount =  DiscountRequest::select('amount')
        -> where('sale_trx_id',json_decode(json_encode($saletrx),true)['id'])
        -> where('is_approved',true)
        -> first();
        $saletrx['total_discount'] = json_decode(json_encode($total_discount),true)['amount'];

        $total_discount =  RabRequest::select('amount')
        -> where('sale_trx_id',json_decode(json_encode($saletrx),true)['sale_trx_id'])
        -> where('is_approved',true)
        -> first();
        if ($saletrx['specup_amount'] ==null) $saletrx['specup_amount'] = json_decode(json_encode($total_discount),true)['amount'];

        $payments = explode(',','kavling,book,cash,kpr,kpr_dp,kpr_inst,inhouse,inhouse_dp,inhouse_inst');
        $details = SaleTrxD::where('sale_trx_id',$id)->get();
        $details= json_decode(json_encode($details),true);
        foreach($payments as $payment)
        {
            $j=0;
            $detail=null;
            for($i = 0; $i < count($details); $i++)
            {
                if(strtolower($details[$i]['trx_d_code'])==$payment)
                {
                    $detail[$j] = $details[$i];
                    $j++;
                }

            }
            $saletrx[$payment]=$detail;
        }

        $docs = MDocType::all();
        $details = SaleTrxDoc::where('sale_trx_id',$id)->get();
        $details= json_decode(json_encode($details),true);

        $doctype='';
        foreach($docs as $doc)
        {
            if($doctype!=$doc['type'])
            {
                $j=0;
                $detail=null;
                $doctype=$doc['type'];
            }
            for($i = 0; $i < count($details); $i++)
            {
                if(strtolower($details[$i]['m_doc_type_id'])==$doc['id'])
                {
                    $detail[$j] = $details[$i];
                    $j++;
                }
            }
            $saletrx[$doctype]=$detail;
        }
        // dd($saletrx);
        return response()->json(['data'=>$saletrx]);
    }
    public function getNupRelatedData($id)
    {
        $saletrx =  SaleTrx::find($id);
        $projectId = json_decode(json_encode($saletrx),true)['project_id'];

        $site_id =  Project::select('site_id') -> where('id',$projectId) -> first();
        $saletrx['site_id'] = json_decode(json_encode($site_id),true)['site_id'];

        $specup_amount =  RabRequest::select('amount')
        -> where('sale_trx_id',json_decode(json_encode($saletrx),true)['id'])
        -> where('is_approved',true)
        -> first();
        $saletrx['specup_amount'] = json_decode(json_encode($specup_amount),true)['amount'];
        return response()->json(['data'=>$saletrx]);
    }

    public function getCustomerRelatedData($id)
    {
        $saletrx =  SaleTrx::where('customer_id',$id) -> where('trx_type','SPU')->first();
        $saletrxId = json_decode(json_encode($saletrx),true)['id'];
        $projectId = json_decode(json_encode($saletrx),true)['project_id'];
        $site_id =  Project::select('site_id') -> where('id',$projectId) -> first();
        $saletrx['site_id'] = json_decode(json_encode($site_id),true)['site_id'];

        $nup =  SaleTrx::select('no', 'cash_amount') -> where('customer_id',$id) -> where('trx_type','NUP')->first();
        $saletrx['nup_no'] = json_decode(json_encode($nup),true)['no'];
        $saletrx['nup_amount'] = json_decode(json_encode($nup),true)['cash_amount'];

        $payments = explode(',','kavling,cash,kpr,kpr_dp,kpr_inst,inhouse,inhouse_dp,inhouse_inst');
        $details = SaleTrxD::where('sale_trx_id',$saletrxId)->get();
        $details= json_decode(json_encode($details),true);
        foreach($payments as $payment)
        {
            $j=0;
            $detail=null;
            for($i = 0; $i < count($details); $i++)
            {
                if(strtolower($details[$i]['trx_d_code'])==$payment)
                {
                    $detail[$j] = $details[$i];
                    $j++;
                }

            }
            $saletrx[strtoupper($payment)]=$detail;
        }

        $docs = MDocType::all();
        $details = SaleTrxDoc::where('sale_trx_id',$saletrxId)->get();
        $details= json_decode(json_encode($details),true);

        $doctype='';
        foreach($docs as $doc)
        {
            if($doctype!=$doc['type'])
            {
                $j=0;
                $detail=null;
                $doctype=$doc['type'];
            }
            for($i = 0; $i < count($details); $i++)
            {
                if(strtolower($details[$i]['m_doc_type_id'])==$doc['id'])
                {
                    $detail[$j] = $details[$i];
                    $j++;
                }
            }
            $saletrx[strtoupper($doctype)]=$detail;
        }

        return response()->json(['data'=>$saletrx]);
    }

    public function getMDocListList($doctype)
    {
        $objects = MDocType::where('type',$doctype)->get();

        return response()->json(['data'=>$objects]);
    }

    public function validateSaleTrx($id)
    {
        DB::beginTransaction();
        $saleTrx =  SaleTrx::find($id);
        $saleTrx['is_validated'] = true;
        $saleTrx ->save();
        return response()->json(['data'=>$saleTrx]);
        DB::commit();
        return response()->json(
        [
            'data'=> $saleTrx,
            'responseMessage' => 'success'
        ], 201);
    }

    public function saveSaleTrx(Request $request)
    {
        DB::beginTransaction();
        //selalu edit karena saat add new cust sudah insert
        if($request['id']==null){
            $saleTrx =  new saleTrx();
        }else{
            $saleTrx =  saleTrx::find($request['id']);
        }

        $saleTrx['no'] = $request['no'];
        $saleTrx['customer_id'] = $request['customer_id'];
        $saleTrx['m_employee_id'] = $request['m_employee_id'];
        $saleTrx['follow_history_id'] = $request['follow_history_id'];
        $saleTrx['trx_type'] = $request['trx_type'];
        $saleTrx['payment_method'] = $request['payment_method'];
        $saleTrx['total_amount'] = $request['total_amount'];
        $saleTrx['total_discount'] = $request['total_discount'];
        $saleTrx['base_amount'] = $request['base_amount'];
        $saleTrx['cash_amount'] = $request['cash_amount'];
        $saleTrx['dp_kpr_amount'] =$request['dp_kpr_amount'];
        $saleTrx['dp_inhouse_amount'] = $request['dp_inhouse_amount'];
        $saleTrx['nup_planned_date'] = $request['nup_planned_date'];
        $saleTrx['spu_planned_date'] = $request['spu_planned_date'];
        $saleTrx['is_validated'] = $request['is_validated'];
        $saleTrx['is_printed'] = $request['is_printed'];
        $saleTrx['project_id'] = $request['project_id'];
        $saleTrx['bank_account'] = $request['bank_account'];
        $saleTrx['additional_amount']=$request['additional_amount'];
        $saleTrx['ppn_amount']=$request['ppn_amount'];
        $saleTrx['pbhtb_amount']=$request['pbhtb_amount'];
        $saleTrx['address']=$request['address'];
        $saleTrx['owner_name']=$request['owner_name'];

        $saleTrx['residence_address']=$request['residence_address'];
        $saleTrx['residence_rt']=$request['residence_rt'];
        $saleTrx['residence_rw']=$request['residence_rw'];
        $saleTrx['residence_kelurahan']=$request['residence_kelurahan'];
        $saleTrx['residence_kecamatan']=$request['residence_kecamatan'];
        $saleTrx['residence_city']=$request['residence_city'];
        $saleTrx['residence_zipcode']=$request['residence_zipcode'];

        $saleTrx['legal_address']=$request['legal_address'];
        $saleTrx['legal_rt']=$request['legal_rt'];
        $saleTrx['legal_rw']=$request['legal_rw'];
        $saleTrx['legal_kelurahan']=$request['legal_kelurahan'];
        $saleTrx['legal_kecamatan']=$request['legal_kecamatan'];
        $saleTrx['legal_city']=$request['legal_city'];
        $saleTrx['legal_zipcode']=$request['legal_zipcode'];

        $saleTrx['deal_type']=$request['deal_type'];
        $saleTrx['specup_amount'] = $request['specup_amount'];
        $saleTrx['fasum_fee'] = $request['fasum_fee'];
        $saleTrx['notary_fee'] = $request['notary_fee'];
        $saleTrx['sale_trx_id'] = $request['sale_trx_id'];
        $saleTrx['booking_amount'] = $request['booking_amount'];
        $saleTrx->save();

        if ( strtoupper($request['trx_type']) == 'PPJB')
        {
            $kavling =  Project::find($request['project_id']);
            $kavling['sale_status'] ='SOLD';
            $kavling->save();
        }

        $id_project=$request['project_id'];
        $updateProject=DB::table('projects')->where('id', $id_project)->update(['sale_status' => 'Inavailable']);

        $trx_types = explode(',','deposit,kavling,book,cash,kpr,kpr_dp,kpr_inst,inhouse,inhouse_dp,inhouse_inst');
        $doc_types = explode(',','doc_kpr,doc_ajb');

        foreach ($trx_types as $trx_type)
        {
            if($request[$trx_type]!=null)
            {
                foreach($request[$trx_type] as $reqSaleTrxD)
                {
                    if($reqSaleTrxD['id']==null){
                        $saleTrxD =  new SaleTrxD();
                    }else{
                        $saleTrxD =  SaleTrxD::find($reqSaleTrxD['id']);
                    }
                    $saleTrxD['sale_trx_id'] = $saleTrx['id'];
                    $saleTrxD['trx_d_code'] = $trx_type;
                    $saleTrxD['seq_no'] = $reqSaleTrxD['seq_no'];
                    $saleTrxD['tenor'] = $reqSaleTrxD['tenor'];
                    $saleTrxD['due_day'] = $reqSaleTrxD['due_day'];
                    $saleTrxD['due_date'] = $reqSaleTrxD['due_date'];
                    $saleTrxD['amount'] = $reqSaleTrxD['amount'];
                    $saleTrxD['project_id'] = $reqSaleTrxD['project_id'];
                    $saleTrxD->save();
                }
            }
        }

        foreach($doc_types as $doc_type)
        {
            if($request[$doc_type]!=null)
            {
                foreach($request[$doc_type] as $reqSaleTrxDoc)
                {
                    if($reqSaleTrxDoc['id']==null){
                        $SaleTrxDoc =  new SaleTrxDoc();
                    }else{
                        $SaleTrxDoc =  SaleTrxDoc::find($reqSaleTrxDoc['id']);
                    }
                    $SaleTrxDoc['sale_trx_id'] = $saleTrx['id'];
                    $SaleTrxDoc['m_doc_type_id'] = $reqSaleTrxDoc['doc_type_id'];
                    $SaleTrxDoc['due_date'] = $reqSaleTrxDoc['due_date'];
                    $SaleTrxDoc['is_checked'] = $reqSaleTrxDoc['is_checked'];
                    $SaleTrxDoc->save();
                }
            }
        }
        if($request['kpr_bank_payments']!=null)
            {
                foreach($request['kpr_bank_payments'] as $kpr_bank_payment)
                {
                    if($kpr_bank_payment['id']==null){
                        $SaleTrxKprBankPayment =  new SaleTrxKprBankPayment();
                    }else{
                        $SaleTrxKprBankPayment =  SaleTrxKprBankPayment::find($kpr_bank_payment['id']);
                    }
                    $SaleTrxKprBankPayment['sale_trx_id'] = $saleTrx['id'];
                    $SaleTrxKprBankPayment['m_kpr_bank_payment_id'] = $kpr_bank_payment['m_kpr_bank_payment_id'];
                    $SaleTrxKprBankPayment['plan_at'] = $kpr_bank_payment['plan_at'];
                    $SaleTrxKprBankPayment['payment_amount'] = $kpr_bank_payment['payment_amount'];
                    $SaleTrxKprBankPayment->save();
                }
            }

        // if($request['trx_type'] != 'NUP')
        // {
        //     $trxcode = str_replace('/','',$request['no']);
        //     $transaction_code = substr($trxcode,3,3);
        //     $seq_no = (int)(substr($trxcode,6,3));
        //     $period_year = '20'.substr($trxcode,9,2);;
        //     $period_month = '01';

        //     $site = Site::select('id') -> where('code',substr($trxcode,0,3)) -> first();
        //     $site_id = $site['id'];
        //     // dd($trxcode);
        //     $m_sequence = MSequence::where('seq_code', $transaction_code)
        //         ->where('period_year', $period_year)
        //         ->where('period_month', $period_month)
        //         ->where('site_id', $site_id)
        //         ->get();
        //     // dd($m_sequence);
        //     $object=null;
        //     //Update m_sequences
        //     if((int)$m_sequence[0]['seq_no']<=$seq_no)
        //     {
        //         $object = MSequence::findOrFail($m_sequence[0]['id']);
        //         $object->update(['seq_no' => (int)$m_sequence[0]['seq_no']+1]);
        //     }
        // }
        DB::commit();
        return response()->json(
        [
            'data'=> $saleTrx,
            'responseMessage' => 'success'
        ], 201);
    }

    public function getSpuPrintDataHeader($id)
    {
        $objects = DB::select("select
        h.no
        , h.payment_method
        , to_char(h.created_at, 'dd/mm/yyyy') spu_date
        , c.name customer_name
        , c.birth_place
        , to_char(c.birth_date, 'dd/mm/yyyy') birth_date
        , c.name ppjb_name
        , h.residence_address address_ktp
        , h.legal_address address_spu
        , c.phone_no
        , c.id_no
        , h.booking_amount booking_fee

        , p.name kavling_no
        , s.name site_name
        , p.area base_area
        , p.base_price base_price
        , h.ppn_amount
        , h.pbhtb_amount

        , cast(h.specup_amount as numeric(18,2)) specup_amount
        , cast(h.total_discount as numeric(18,2)) total_discount
        , cast(h.fasum_fee as numeric(18,2)) fasum_fee
        , cast(h.notary_fee as numeric(18,2)) notary_fee
        , cast(h.total_amount as numeric(18,2)) total_price
        ,h.residence_rt
        ,h.residence_rt
        ,h.residence_rw
        ,h.residence_kelurahan
        ,h.residence_kecamatan
        ,h.residence_city
        ,h.residence_zipcode

        ,h.legal_rt
        ,h.legal_rw
        ,h.legal_kelurahan
        ,h.legal_kecamatan
        ,h.legal_city
        ,h.legal_zipcode
        ,cf.description as pekerjaan
        from sale_trxes as h
        left join sale_trx_ds as bk on h.id = bk.sale_trx_id and bk.trx_d_code = 'book'
        left join customers c on c.id = h.customer_id
        left join customer_financials cf on cf.customer_id = c.id and lower(cf.finance_type) = 'income'
        left join projects p on p.id = h.project_id
        left join sites s on s.id = p.site_id
        left join rab_requests r on r.project_id = h.project_id
        left join general_settings fsm on fsm.gs_code = 'fasumfee'
        left join general_settings ntr on ntr.gs_code = 'notaryfee'
        where h.id = '".$id."'");

        return response()->json(['data'=>$objects]);
    }

    public function getSpuPrintDataDetail($id)
    {
        $objects = DB::select("select distinct
        'Pembayaran Booking'tahap_bayar
        ,to_char(n.created_at, 'dd/mm/yyyy') due_date
        ,h.booking_amount amount
        --,h.*
        from sale_trxes h
        left join sale_trxes n on n.customer_id= h.customer_id AND h.sale_trx_id= n.id
        where h.id = '".$id."'
        --and trx_d_code not in ('kpr_inst','inhouse_inst')

        union all
        select distinct
        case
        when trx_d_code = 'book' then 'Uang tanda jadi'
        when trx_d_code in ('kpr_dp','inhouse_dp') then 'Pembayaran dp ke-'||cast(seq_no as varchar(2))
        when trx_d_code = 'cash' then 'Pembayaran cash'
        end tahap_bayar
        ,to_char(d.due_date, 'dd/mm/yyyy') due_date
        ,amount
        from sale_trx_ds d
        left join sale_trxes h on d.sale_trx_id= h.id
        where d.sale_trx_id = '".$id."'
        and trx_d_code not in ('kpr_inst','inhouse_inst')

        union all
        select distinct
        case
        when trx_d_code = 'inhouse_inst' then 'In house'
        when trx_d_code = 'kpr_inst' then 'Kpr'
        end
        ,to_char(d.due_date, 'dd/mm/yyyy') due_date
        ,case
        when trx_d_code = 'inhouse_dp' then d.amount
        when trx_d_code = 'kpr_dp' then d.amount
        when trx_d_code = 'kpr_inst' then d.amount *d.tenor
        when trx_d_code = 'inhouse_inst' then d.amount
        end
        from sale_trx_ds d
        left join sale_trxes h on d.sale_trx_id= h.id
        where d.sale_trx_id = '".$id."'
        and trx_d_code not in ('book','cash','inhouse_dp','kpr_dp')");

        return response()->json(['data'=>$objects]);
    }

    public function getPpjbPrintDataHeader($id)
    {
        $objects = DB::select("
        select
        h.no
        ,h.payment_method
        , c.name customer_name
        , CAST(date_part('year', now()) AS INT) - CAST(date_part('year', c.birth_date) AS INT) customer_age
        , h.residence_address address_ktp
        , h.legal_address address_spu
        , cf.description
        , m.name sales_name
        , m.role sales_role

        , p.name kavling_no
        , s.name site_name
        , s.name site_address
        , p.area base_area
        , split_part(p.area,'/',1) lb
        , split_part(p.area,'/',2) lt
        , p.base_price base_price
        , h.ppn_amount
        , h.pbhtb_amount
        , cast(h.specup_amount as numeric(18,2)) specup_amount
        , cast(h.total_discount as numeric(18,2)) total_discount
        , cast(h.fasum_fee as numeric(18,2)) fasum_fee
        , cast(h.notary_fee as numeric(18,2)) notary_fee
        , cast(h.total_amount as numeric(18,2)) total_price

        ,h.residence_rt
        ,h.residence_rt
        ,h.residence_rw
        ,h.residence_kelurahan
        ,h.residence_kecamatan
        ,h.residence_city
        ,h.residence_zipcode

        ,h.legal_rt
        ,h.legal_rw
        ,h.legal_kelurahan
        ,h.legal_kecamatan
        ,h.legal_city
        ,h.legal_zipcode
        --,cf.description as pekerjaan
        from sale_trxes as h
        LEFT JOIN m_employees AS m ON h.m_employee_id = m.id
        left join customers c on c.id = h.customer_id
        left join customer_financials cf on cf.customer_id = c.id and lower(cf.finance_type) = 'income'
        left join projects p on p.id = h.project_id
        left join sites s on s.id = p.site_id
        where h.id = '".$id."'");

        return response()->json(['data'=>$objects]);
    }

    public function getPpjbPrintDataDetail($id)
    {
        $objects = DB::select("
        select distinct
        'Pembayaran Booking'tahap_bayar
        ,to_char(m.created_at, 'dd/mm/yyyy') due_date
        ,n.booking_amount amount
        ,'' tenor
        from sale_trxes h
        left join sale_trxes n on n.customer_id= h.customer_id AND h.sale_trx_id= n.id
        left join sale_trxes m on m.customer_id= n.customer_id AND n.sale_trx_id= m.id
        where h.id = '".$id."'


        union all
        select distinct
        case
        when trx_d_code = 'book' then 'Uang tanda jadi'
        when trx_d_code in ('kpr_dp','inhouse_dp') then 'Pembayaran dp ke-'||cast(seq_no as varchar(2))
        when trx_d_code = 'cash' then 'Pembayaran cash'
        end tahap_bayar
        ,to_char(d.due_date, 'dd/mm/yyyy') due_date
        ,amount
        ,''
        from sale_trx_ds d
        left join sale_trxes h on d.sale_trx_id= h.id
        where d.sale_trx_id = '".$id."'
        and trx_d_code not in ('kpr_inst','inhouse_inst')

        union all
        select distinct
        case
        when trx_d_code = 'inhouse_inst' then 'Pembayaran angsuran In house ke-'||cast(seq_no as varchar(2))
        when trx_d_code = 'kpr_inst' then 'Kpr'
        end
        ,case
        when trx_d_code = 'inhouse_inst' then to_char(d.due_date, 'dd/mm/yyyy')
        when trx_d_code = 'kpr_inst' then cast(d.due_day as varchar(2))
        end due_date
        ,case
        when trx_d_code = 'inhouse_dp' then d.amount
        when trx_d_code = 'kpr_dp' then d.amount
        when trx_d_code = 'kpr_inst' then d.amount
        when trx_d_code = 'inhouse_inst' then d.amount
        end
        ,case
        when trx_d_code = 'inhouse_inst' then ''
        when trx_d_code = 'kpr_inst' then cast(d.tenor as varchar(3))
        end
        from sale_trx_ds d
        left join sale_trxes h on d.sale_trx_id= h.id
        where d.sale_trx_id = '".$id."'
        and trx_d_code not in ('book','cash','inhouse_dp','kpr_dp')");

        return response()->json(['data'=>$objects]);
    }
}
