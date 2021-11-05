<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customer;
use App\Models\Project;
use App\Models\CustomerFinancial;
use App\Models\FollowupHistory;
use App\Models\SaleTrx;
use App\Models\SaleTrxD;
use App\Models\Site;
use DB;
use Carbon\Carbon;

class Crm extends Controller
{
    private $dateNow;
    private $now;
    public function __construct()
    {
        $this->dateNow = Carbon::now()->toDateString();
        $this->now = Carbon::now();
    }

    public function getUnitTrxList($saletype = null)
    {
        // $objects = DB::select("SELECT tablename FROM sale_trxes as where schemaname = 'public'");
        // join('sale_trxes as s', 's.id', '=', 'sale_trx_ds.sale_trx_id')
        //         ->join ('projects as p', 'p.id','=', 'sale_trx_ds.project_id')
        //         ->join('customers as c', 'c.id','=','s.customer_id')
        //         ->join('m_eployees as e', 'e.id','=','s.m_eployee_id')
        //         ->select(['s.id','s.no','s.sale_type','c.name AS customer_name','e.name AS employee_name'])
        //         ->get();
        if($saletype==null)
        {
            $saletrxes = SaleTrx::All();
        } else{
            $saletrxes = SaleTrx::where('sale_type',$saletype)->get();
        }

        foreach ($saletrxes as $saletrx)
        {
            $saletrx->MEmployee;
            $saletrx->Customer;
            $saletrx->Project;
        }

        return response()->json(['data'=>$objects]);
    }

    public function getCustomerDataMain()
    {
        $customers = DB::select("Select A.*, m.name as sales_name
        from customers A left join m_employees m on A.m_employee_id = m.id ");
        
        return response()->json(['data'=>$customers]);
    }

    public function getCustomerDataMainBySalesId($salesid)
    {
        $customers = DB::select("Select A.* 
        from customers A where lower(family_role)='main' and A.m_employee_id=".$salesid);
        
        return response()->json(['data'=>$customers]);
    }

    public function getFinishFollowupHistoryByCustId($id)
    {
        $customer = DB::select(" SELECT a.customer_id, a.m_employee_id ,kav.project_id
        FROM followup_histories a
        LEFT JOIN sale_trxes b ON b.follow_history_id = a.id AND a.customer_id=b.customer_id AND b.trx_type = 'INTERESTS'
        LEFT JOIN sale_trx_ds kav ON kav.sale_trx_id = b.id AND kav.trx_d_code = 'KAVLING' AND kav.seq_no=1
        WHERE a.customer_id = ".$id."
        AND a.followup_status = 'FINISH'
        ORDER BY a.id desc");

        return response()->json(['data'=>$customer]);
    }

    public function validateSPUPayment($spu_id)
    {
        $objects = DB::select("SELECT A.id spu, D.id payment from sp_units A, sale_trxes B, invoices C, payment_receives D
        where A.id = ".$spu_id."
        AND A.sale_trx_id = B.id
        and C.sale_trx_id = B.id
        and D.invoice_id = C.id");
        return response()->json(['data'=>$objects]);
    }

    public function getNUPList()
    {
        $objects = DB::select("SELECT A.*,B.name AS sales_person_name ,C.name AS customer_name
        FROM sale_trxes A
        INNER JOIN m_employees B ON B.id = A.m_employee_id
        INNER JOIN customers C ON C.id = A.customer_id
        WHERE trx_type='NUP'");
        return response()->json(['data'=>$objects]);
    }

    public function getBOKList()
    {
        $objects = DB::select("SELECT A.*,B.name AS sales_person_name ,C.name AS customer_name
        FROM sale_trxes A
        INNER JOIN m_employees B ON B.id = A.m_employee_id
        INNER JOIN customers C ON C.id = A.customer_id
        WHERE trx_type='BOK'");
        return response()->json(['data'=>$objects]);
    }

    public function getSPUList()
    {
        $objects = DB::select("SELECT A.*,B.name AS sales_person_name ,C.name AS customer_name , CONCAT(D.name, ' Type/LB: ',D.area) AS kavling_name
        FROM sale_trxes A
        INNER JOIN m_employees B ON B.id = A.m_employee_id
        INNER JOIN customers C ON C.id = A.customer_id
        INNER JOIN projects D ON D.id = A.project_id
        WHERE trx_type='SPU'");
        return response()->json(['data'=>$objects]);
    }
    public function getPPJBList()
    {
        $objects = DB::select("SELECT A.*,B.name AS sales_person_name ,C.name AS customer_name , CONCAT(D.name, ' Type/LB: ',D.area) AS kavling_name
        FROM sale_trxes A
        INNER JOIN m_employees B ON B.id = A.m_employee_id
        INNER JOIN customers C ON C.id = A.customer_id
        INNER JOIN projects D ON D.id = A.project_id
        WHERE trx_type='PPJB'");
        return response()->json(['data'=>$objects]);
    }

    public function getCustomerData()
    {

        $customers = DB::select("Select A.*
        from customers A where family_role='main'");

        return response()->json(['data'=>$customers]);
    }

    public function getCustomerDataById($id)
    {

        // $customer = DB::select("SELECT A.* FROM customers A WHERE A.id = ".$id."");

        $customer =  Customer::find($id);
        // $customer = DB::select("Select A.*, m.name as sales_name from customers A left join m_employees m on A.m_employee_id = m.id where A.id = $id");
        $customer -> CustomerFinancials;

        $object = DB::select("SELECT id from customers a
        where a.family_id = ".$customer['family_id']."
        AND (a.family_role = 'spouse' OR a.family_role = 'SPOUSE' )");

        if($object!= null)
        {
            $customerSpouse =  Customer::find(json_decode(json_encode($object),true)[0]['id']);
            if($customerSpouse!=null) $customerSpouse -> CustomerFinancials;
            $customer['spouse']=$customerSpouse;
        }
        return response()->json(['data'=> $customer]);
    }

    public function saveCustomerData(Request $request)
    {
        $IsCustomerNew = TRUE;
        $customerFamilyId = 0;
        DB::beginTransaction();

        $object = DB::select("SELECT CASE WHEN (max(id)) IS NULL THEN 0 ELSE (max(id)) END AS lastfamilyid FROM customers");
        $customerFamilyId =  ($object[0]->lastfamilyid)+1;
        foreach($request['customers'] as $requestCust)
        {
            if($requestCust['id']!=0 && $requestCust['id']!=null)
            {
                $customer =  Customer::find($requestCust['id']);
                $IsCustomerNew = FALSE;
            }
            else
            {
                $customer =  new Customer();
            }

            if(isset($requestCust['customer_family_id']))
            {
                $customerFamilyId =  $requestCust['customer_family_id'];
            }
            //else{
            //     $object = DB::select("SELECT CASE WHEN (max(id)) IS NULL THEN 0 ELSE (max(id)) END AS lastfamilyid FROM customers");
            //     $customerFamilyId =  ($object[0]->lastfamilyid)+1;
            // }

            $customer['name'] = $requestCust['name'];
            $customer['birth_place'] = $requestCust['birth_place'];
            $customer['birth_date'] = $requestCust['birth_date'];
            $customer['religion'] = $requestCust['religion'];
            $customer['marital_status'] = $requestCust['marital_status'];
            $customer['address'] = $requestCust['address'];
            $customer['rt'] = $requestCust['rt'];
            $customer['rw'] = $requestCust['rw'];
            $customer['kelurahan'] = $requestCust['kelurahan'];
            $customer['kecamatan'] = $requestCust['kecamatan'];
            $customer['city'] = $requestCust['city'];
            $customer['zipcode'] = $requestCust['zipcode'];
            $customer['notes'] = $requestCust['notes'];
            $customer['profile_picture'] = $requestCust['profile_picture'];
            $customer['id_picture'] = $requestCust['id_picture'];
            $customer['id_no'] = $requestCust['id_no'];
            $customer['family_role'] = $requestCust['family_role'];
            $customer['phone_no'] = $requestCust['phone_no'];
            $customer['m_employee_id'] = $requestCust['m_employee_id'];
            $customer['family_id'] =$customerFamilyId;
            // dd($customer);
            $customer->save();
            if($IsCustomerNew && $requestCust['family_role']=='main')
            {
                $followupHistory = new FollowupHistory();
                $followupHistory['customer_id'] = $customer['id'];
                $followupHistory['m_employee_id'] = $request['sales_id'];
                $followupHistory['followup_schedule'] = $request['followup_schedule'];
                $followupHistory['followup_status'] = 'NEW';
                $followupHistory->save();

                $customerMainId =  $customer['id'];
            }

            if($requestCust['customer_financial']!=null)
            {
                foreach($requestCust['customer_financial'] as $financialData)
                {
                    if($financialData['id']!=null){
                        $customerFinancial =  CustomerFinancial::find($financialData['id']);
                    }
                    else
                    {
                        $customerFinancial =  new CustomerFinancial();
                    }
                    $customerFinancial['customer_id'] = $customer['id'];
                    $customerFinancial['finance_type'] = $financialData['finance_type'];
                    $customerFinancial['description'] = $financialData['description'];
                    $customerFinancial['amount'] = $financialData['amount'];
                    $customerFinancial['frequency'] = $financialData['frequency'];
                    if($financialData['finance_type'] == 'INCOME')
                    {
                        $customerFinancial['state'] = 'D';
                    }else{
                        $customerFinancial['state'] = 'C';
                    }
                    $customerFinancial->save();
                }
            }

        }

        DB::commit();
        return response()->json(
        [
            'data'=> $customer,
            'responseMessage' => 'success'
        ], 201);
    }

    public function getFollowUpHistoriesById($id )
    {
        $followupHistories =  FollowupHistory::find($id);
        $saleTrxes = DB::select("SELECT * FROM sale_trxes WHERE follow_history_id = ?", [$id]);
        if ($followupHistories != null) {
            $followupHistories['customer'] = Customer::find($followupHistories['customer_id']);
            $followupHistories['project'] = Project::find($followupHistories['project_id']);
            if (count($saleTrxes) > 0) {
                $followupHistories['sale_trx'] = $saleTrxes[0];
                $salesTrxDs = DB::select("SELECT * FROM sale_trx_ds WHERE sale_trx_id = ?", [$saleTrxes[0]->id]);
                if (count($salesTrxDs) > 0) {
                    $followupHistories['project'] = Project::find($salesTrxDs[0]->project_id);
                    $followupHistories['site'] = Site::find($followupHistories['project']->site_id);
                }
            }
        }

        return response()->json(['data'=>$followupHistories]);
    }

    public function saveFollowUpCustomer(Request $request, $mode)
    {
        $Mode = $mode;
        DB::beginTransaction();
        //selalu edit karena saat add new cust sudah insert
        $followupHistory =  FollowupHistory::find($request['id']);
        // dd($followupHistory);

        $followupHistory['no'] = $request['no'];
        $followupHistory['customer_id'] = $request['customer_id'];
        $followupHistory['m_employee_id'] = $request['sales_person_id'];
        $followupHistory['project_id'] = $request['project_id'];
        $followupHistory['customer_budget'] = $request['customer_budget'];
        $followupHistory['followup_schedule'] = $request['followup_schedule'];
        $followupHistory['followup_remark'] = $request['followup_remark'];
        $followupHistory['followup_result'] = $request['followup_result'];
        $followupHistory['followup_status'] = $request['followup_status'];
        $followupHistory['prospect_result'] = $request['prospect_result'];
        $followupHistory['notes'] = $request['notes'];
        $followupHistory['manager_notes'] = $request['manager_notes'];
        $followupHistory['supervisor_notes'] = $request['supervisor_notes'];
        $followupHistory['info_source'] = $request['info_source'];

        $followupHistory->save();


        //jika salah satu ada, dicek, untuk history ini udah ada apa belum detailnya
        $object1 = DB::select("SELECT a.*
        FROM sale_trxes a
        WHERE a.customer_id = ".$request['customer_id']."
        AND a.m_employee_id = ".$request['sales_person_id']."
        AND a.follow_history_id = ".$followupHistory['id']."
        ");

        $saleTrxId = 0;
        if($object1==null){
            $saleTrx =  new SaleTrx();
        }else
        {
            $saleTrx =  SaleTrx::find(json_decode(json_encode($object1),true)[0]['id']);
            $saleTrxId = json_decode(json_encode($object1),true)[0]['id'];
        }

        $saleTrx['customer_id'] = $request['customer_id'];
        $saleTrx['m_employee_id'] = $request['sales_person_id'];
        $saleTrx['follow_history_id'] = $followupHistory['id'];
        $saleTrx['trx_type'] = 'INTERESTS';
        $saleTrx['payment_method'] = $request['payment_method'];
        $saleTrx['spu_planned_date'] = $request['spu_planned_date'];
        $saleTrx['nup_planned_date'] = $request['nup_planned_date'];

        $saleTrx->save();

        if($request['kavlings']!=null||$request['payments']!=null)
        {

            foreach($request['kavlings'] as $kavling)
            {

                $object2 = DB::select("SELECT a.*
                FROM sale_trx_ds a
                WHERE a.sale_trx_id = ".$saleTrx['id']."
                AND a.project_id = ".$kavling['id']."
                ");

                if($object2==null){
                    $saleTrxD =  new SaleTrxD();
                }else{
                    $saleTrxD =  SaleTrxD::find(json_decode(json_encode($object2),true)[0]['id']);
                }
                $saleTrxD['sale_trx_id'] = $saleTrx['id'];
                $saleTrxD['trx_d_code'] = 'KAVLING';
                $saleTrxD['seq_no'] = $kavling['seq_no'];
                $saleTrxD['project_id'] = $kavling['id'];
                $saleTrxD->save();
            }

            foreach($request['payments'] as $payment)
            {

                $object2 = DB::select("SELECT a.*
                FROM sale_trx_ds a
                WHERE a.sale_trx_id = ".$saleTrx['id']."
                AND a.trx_d_code = '".strtoupper($payment['method'])."'
                AND a.seq_no = ".$payment['seq_no']."
                ");

                if($object2==null){
                    $saleTrxD =  new SaleTrxD();
                }else{
                    $saleTrxD =  SaleTrxD::find(json_decode(json_encode($object2),true)[0]['id']);
                }
                $saleTrxD['sale_trx_id'] = $saleTrx['id'];
                $saleTrxD['trx_d_code'] = $payment['method'];
                $saleTrxD['tenor'] = $payment['tenor'];
                $saleTrxD['seq_no'] = $payment['seq_no'];
                $saleTrxD['due_day'] = null;
                $saleTrxD['amount'] = $payment['amount'];
                $saleTrxD['due_date'] = $payment['due_date'];
                $saleTrxD->save();
            }
        }

        if($Mode =='submit' )
        {
            $followupHistory = new FollowupHistory();
            $followupHistory['customer_id'] = $request['customer_id'];
            $followupHistory['m_employee_id'] = $request['sales_person_id'];
            $followupHistory['followup_schedule'] = $request['next_followup_schedule'];
            $followupHistory['followup_status'] = 'NEW';
            $followupHistory->save();
        }
        DB::commit();
        return response()->json(
        [
            'data'=> $followupHistory,
            'responseMessage' => 'success'
        ], 201);
    }

    public function getFollowUpHistoriesByCustId($id , $salespersonid)
    {
        $followupHistories = DB::select("SELECT a.id AS followup_history_id, *
        FROM followup_histories a, customers b
        WHERE a.customer_id = ".$id."
        AND b.id = a.customer_id
        AND a.m_employee_id =".$salespersonid." ORDER BY a.created_at ASC");
        return response()->json(['data'=>$followupHistories]);
    }

    // public function getFollowUpHistoriesBySalesId($salesid){
    //     $customers = DB::select("Select c.id, c.name, c.phone_no, c.m_employee_id, (c.address || c.rt || c.rw || c.kelurahan || c.kecamatan || c.city) AS address
    //     from customers c where family_role='main'  order by c.name asc");
    //         // from customers c where family_role='main' and c.m_employee_id=".$salesid. " order by c.name asc");

    //     // $response = array();
    //     foreach($customers as $customer){
    //         $counts = DB::select("select count(*) as hitung from followup_histories where customer_id = ".$customer->id."");
    //         $customer->last_followup_seq = $counts[0]->hitung;
    //         $sales = DB::select("select name from m_employees where id = ".$customer->m_employee_id."");
    //         $customer->sales_name = $sales[0]->name;
    //     }

    //     return response()->json(['data'=>$customers]);
    // }
    public function getFollowUpHistoriesBySalesId($salesid){
        $customers = DB::select("Select c.id, c.name, c.phone_no, c.m_employee_id, (c.address || c.rt || c.rw || c.kelurahan || c.kecamatan || c.city) AS address
            from customers c where family_role='main' and c.m_employee_id=".$salesid. " order by c.name asc");

        // $response = array();
        foreach($customers as $customer){
            $counts = DB::select("select count(*) as hitung from followup_histories where customer_id = ".$customer->id."");
            $customer->last_followup_seq = $counts[0]->hitung;
            $sales = DB::select("select name from m_employees where id = ".$customer->m_employee_id."");
            $customer->sales_name = $sales[0]->name;
        }

        return response()->json(['data'=>$customers]);
    }

    public function countProspectCustByLevel($salespersonid, $prospectlevel)
    {
        if($prospectlevel!='all')
        {
            strtoupper($prospectlevel);
            $objects = DB::select("SELECT DISTINCT COUNT(a.customer_id)
            FROM followup_histories a
            JOIN customers c ON a.customer_id = c.id
            WHERE c.family_role = 'main' AND  a.m_employee_id = ".$salespersonid." AND a.prospect_result = '".$prospectlevel."'");
        }
        else{
            $objects = DB::select("SELECT DISTINCT COUNT(a.customer_id)
            FROM followup_histories a
            JOIN customers c ON a.customer_id = c.id
            WHERE c.family_role = 'main' AND a.m_employee_id = ".$salespersonid." AND (a.prospect_result IS NULL OR a.prospect_result <> 'DEL')");
        }
        return response()->json(['data'=>$objects]);
    }

    public function countCustBySalesPerson($salespersonid, $periode)
    {
        if($periode == 'day')
        {
            $objects = DB::select("SELECT COUNT(a.id)
            FROM customers a
            WHERE
            a.family_role = 'main'
            and a.m_employee_id = ".$salespersonid." 
            AND DATE(a.created_at) = '".$this->dateNow."'");
        } elseif ($periode == 'month')
        {
            $objects = DB::select("SELECT COUNT(a.id)
            FROM customers a
            WHERE
            a.family_role = 'main'
            and a.m_employee_id = ".$salespersonid." 
            AND DATE_PART('MONTH',a.created_at) = ".$this->now->month."
            AND DATE_PART('YEAR',a.created_at) = ".$this->now->year."");
        } elseif ($periode == 'year')
        {
            $objects = DB::select("SELECT COUNT(a.id)
            FROM customers a
            WHERE a.family_role = 'main' 
            and a.m_employee_id = ".$salespersonid." 
            AND DATE_PART('YEAR',a.created_at) = ".$this->now->year."");
        }
        return response()->json(['data'=>$objects]);
    }

    public function countScheduleToday($salespersonid)
    {
        $objects = DB::select("SELECT DISTINCT COUNT(fh.customer_id)
        FROM followup_histories fh
        JOIN customers a ON fh.customer_id = a.id
        WHERE fh.followup_status = 'NEW'
        AND a.family_role = 'main'
        AND DATE(fh.followup_schedule) = '".$this->dateNow."'
        AND fh.m_employee_id = ".$salespersonid."");
        return response()->json(['data'=>$objects]);
    }

    public function listScheduleToday($salespersonid)
    {
        $objects = DB::select("SELECT c.id, c.name, a.id AS followup_history_id, c.phone_no
        FROM followup_histories a
        JOIN customers c ON a.customer_id = c.id
        WHERE followup_status = 'NEW'
        AND c.family_role = 'main'
        AND DATE(followup_schedule) = '".$this->dateNow."'
        AND a.m_employee_id = ".$salespersonid."");
        return response()->json(['data'=>$objects]);
    }

    public function countSPUbySalesPerson($salespersonid)
    {
        $objects = DB::select("SELECT DISTINCT COUNT(a.customer_id)
        FROM followup_histories a
        INNER JOIN sale_trxes b ON a.id=b.followup_history_id
        INNER JOIN spu_records c ON b.id=c.sale_trx_id
        WHERE a.m_employee_id = ".$salespersonid."");
        return response()->json(['data'=>$objects]);
    }

    public function countAJBbySalesPerson($salespersonid)
    {
        $objects = DB::select("SELECT DISTINCT COUNT(a.customer_id)
        FROM followup_histories a
        INNER JOIN sale_trxes b ON a.id=b.followup_history_id
        INNER JOIN ajb_records c ON b.id=c.sale_trx_id
        WHERE a.m_employee_id = ".$salespersonid."");
        return response()->json(['data'=>$objects]);
    }
    
    public function deleteFollowupData($customerId) {
        DB::beginTransaction();
        $customer = DB::select('SELECT * FROM customers WHERE id = ?', [$customerId]);
        $customerFinancial= null;
        $followupHistories = null;
        $saleTrx = null;
        $saleTrxD = null;
        if (count($customer) > 0) {
            $customerFinancial = DB::select('SELECT * FROM customer_financials WHERE customer_id = ?', [$customerId]);
            $followupHistories = DB::select('SELECT * FROM followup_histories WHERE customer_id = ?', [$customerId]);
            $saleTrx = DB::select('SELECT * FROM sale_trxes WHERE customer_id = ?', [$customerId]);
            if (count($saleTrx) > 0) {
                foreach($saleTrx as $data) {
                    // Delete sale trx ds
                    DB::delete('DELETE FROM sale_trx_ds WHERE sale_trx_id = '. $saleTrx[0]->id);
                }
            }
            DB::delete('DELETE FROM sale_trxes WHERE customer_id = '. $customerId);
            DB::delete('DELETE FROM followup_histories WHERE customer_id = '. $customerId);
            DB::delete('DELETE FROM customer_financials WHERE customer_id = '. $customerId);
        }
        DB::delete('DELETE FROM customers WHERE id = '. $customerId);
        DB::commit();

        return response()->json(['error'=>false, 'message'=>'Delete success']);
    }

}
