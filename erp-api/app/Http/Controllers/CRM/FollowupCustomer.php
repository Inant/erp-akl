<?php
namespace App\Http\Controllers\CRM;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\CustomerFinancial;
use App\Models\FollowupHistory;
use App\Models\SaleTrx;
use App\Models\SaleTrxD;
use DB;

class FollowupCustomer extends Controller
{
    public function getLastFollowUpHistoyResultByCustomerId($id)
    {
        $customer = DB::select(" SELECT a.customer_id, a.m_employee_id ,kav.project_id, proj.site_id, c.cash_amount nup_amount , c.no nup_no
        FROM followup_histories a 
        LEFT JOIN sale_trxes b ON b.follow_history_id = a.id AND a.customer_id=b.customer_id AND b.trx_type = 'INTERESTS'
        LEFT JOIN sale_trx_ds kav ON kav.sale_trx_id = b.id AND kav.trx_d_code = 'KAVLING' AND kav.seq_no=1
        LEFT JOIN projects proj ON kav.project_id = proj.id
        LEFT JOIN sale_trxes c ON a.customer_id=c.customer_id AND c.trx_type = 'NUP'
        WHERE a.customer_id = ".$id."
        AND a.followup_status = 'FINISH'
        ORDER BY a.id desc");

        return response()->json(['data'=>$customer]);
    }
    public function countFollowUp()
    {
        $response=Customer::where('family_role', 'main')->get();
        $low=$medium=$hot=$spu=$ppjb=0;
        $data=[];
        foreach ($response as $key => $value) {
            $id=$value->id;
            $sql=DB::select("select * from followup_histories where id IN (select max(id) from followup_histories where customer_id=".$id.")");
            $sql_spu=DB::select("select * from sale_trxes where id IN (select max(id) from sale_trxes where customer_id=".$id.") and trx_type IN (select trx_type from sale_trxes where trx_type='SPU' OR trx_type='PPJB')");
            $type=$temp_deal=0;
            $id_employee=0;
             if (count($sql_spu) != 0) {
                $id_employee_deal=$sql_spu[0]->m_employee_id;
                $type=$sql_spu[0]->trx_type;
                if ($type == 'SPU') {
                    $spu+=1;
                }else{
                    $ppjb+=1;
                }
            }else if (count($sql) != 0) {
                $id_employee=$sql[0]->m_employee_id;
                if($sql[0]->prospect_result == NULL){
                    $type='LOW';
                    $low+=1;
                }else if($sql[0]->prospect_result == 'MEDIUM'){
                    $type=$sql[0]->prospect_result;
                    $medium+=1;
                }else{
                    $type=$sql[0]->prospect_result;
                    $hot+=1;
                }
            }else {
            }
            echo $id." : ";
            echo $id_employee_deal;
            echo $type;
            echo "<br>";
            // echo $temp;
            // echo "<br>";
            if ($id_employee != 0) {
                $data['data'][$key]=array('prospect' =>$type, 'id'=>$id, 'id_employee'=>$id_employee);
            }else{
                $data['data'][$key]=array('prospect' =>$type, 'id'=>$id, 'id_employee'=>$id_employee_deal);
            }
        }
        
        return response()->json($data);
    }
}
