<?php

namespace App\Http\Controllers\CRM\PPJB;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Exception;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Redirect;
use Carbon\Carbon;

class PpjbRecord extends Controller
{
    private $base_api_url;
    private $site_id = null;
    private $user_name = null;
    public function __construct()
    {
        //Authenticate page menu
        $this->middleware(function ($request, $next) {
            Controller::isLogin(auth()->user()['role_id']); 
            $this->site_id = auth()->user()['site_id'];
            $this->user_name = auth()->user()['name'];
            
            return $next($request);
        });

        $this->base_api_url = env('API_URL');
    }
    
    public function generateTransactionNo($trasaction_code){
        
        try
        {
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . 'master/m_sequence/generate_trx_no']);
            $reqBody = [
                'headers' => $headers,
                'json' => [
                    'transaction_code' => $trasaction_code,
                    'period_year' => Carbon::now()->year,
                    'period_month' => Carbon::now()->month,
                    'site_id' => $this->site_id
                   ]
               ]; 
            $response = $client->request('POST', '', $reqBody); 
            $body = $response->getBody();
            $content = $body->getContents();
            $response_array = json_decode($content,TRUE);
            $data = $response_array['data'];
        } catch(RequestException $exception) {
            // $is_error = true;
            // $error_message .= $exception->getMessage();
        } 

        return $data['transaction_number'];
    }

    public function index(Request $request)
    {       
        //basic variable
        $is_error = false;
        $error_message = '';

        $ppjb = null;
        //get all project
        try
        {
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . 'crm/ppjb/list']);  
            $response = $client->request('GET', '', ['headers' => $headers]);  
            $body = $response->getBody();
            $content =$body->getContents();
            $response_array = json_decode($content,TRUE);
            $ppjb = $response_array['data'];
        } catch(RequestException $exception) {
            $is_error = true;
            $error_message .= $exception->getMessage();
        } 

        //get all project

        $data = array(
            'error' => array(
                'is_error' => $is_error,
                'error_message' => $error_message
            ),
            'title'=>'PPJB Record',
            'current_page' =>'PPJB Record List',
            'current_url'=>'ppjbrecord',
            'ppjb' => $ppjb
        );

        return view('pages.crm.ppjb.list_main', $data);
    }

    public function addedit($subcat, $mode, $id=null)
    {
        //basic variable
        $is_error = false;
        $error_message = '';

        //bussiness variable
        $customers = null;
        $sales_persons = null;
        $sites =null;
        $projects = null;
        $sale_trxes = null;
        $doc_lists = null;
        
        $site_location_id=1;
        $trx_no = null;

        try
        {
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . 'crm/customerdatamain']);  
            $response = $client->request('GET', '', ['headers' => $headers]);  
            $body = $response->getBody();
            $content =$body->getContents();
            $response_array = json_decode($content,TRUE);

            $customers = $response_array['data'];
            
        } catch(RequestException $exception) {
            $is_error = true;
            $error_message .= $exception->getMessage();
        } 

        try
        {
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . 'crm/doclist/type/doc_kpr']);  
            $response = $client->request('GET', '', ['headers' => $headers]);  
            $body = $response->getBody();
            $content =$body->getContents();
            $response_array = json_decode($content,TRUE);

            $doc_lists['KPR'] = $response_array['data'];
            
        } catch(RequestException $exception) {
            $is_error = true;
            $error_message .= $exception->getMessage();
        } 

        try
        {
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . 'crm/doclist/type/doc_ajb']);  
            $response = $client->request('GET', '', ['headers' => $headers]);  
            $body = $response->getBody();
            $content =$body->getContents();
            $response_array = json_decode($content,TRUE);

            $doc_lists['AJB'] = $response_array['data'];
            
        } catch(RequestException $exception) {
            $is_error = true;
            $error_message .= $exception->getMessage();
        } 

        //get all sales person
        try
        {
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . 'rab/base/MEmployee']);  
            $response = $client->request('GET', '', ['headers' => $headers]);  
            $body = $response->getBody();
            $content =$body->getContents();
            $response_array = json_decode($content,TRUE);

            $sales_persons = $response_array['data'];
            
        } catch(RequestException $exception) {
            $is_error = true;
            $error_message .= $exception->getMessage();
        } 

        //get all site
        try
        {
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . 'rab/site/get_by_town_id/'. $site_location_id]);  
            $response = $client->request('GET', '', ['headers' => $headers]);  
            $body = $response->getBody();
            $content =$body->getContents();
            $response_array = json_decode($content,TRUE);

            $sites = $response_array['data'];
            
        } catch(RequestException $exception) {
            $is_error = true;
            $error_message .= $exception->getMessage();
        } 

        //get all project
        try
        {
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . 'rab/kavling/all']);  
            $response = $client->request('GET', '', ['headers' => $headers]);  
            $body = $response->getBody();
            $content =$body->getContents();
            $response_array = json_decode($content,TRUE);

            $projects = $response_array['data'];
            
        } catch(RequestException $exception) {
            $is_error = true;
            $error_message .= $exception->getMessage();
        } 

        $kpr_banks=null;
        try
        {
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . 'crm/kprbank/name/all']);  
            $response = $client->request('GET', '', ['headers' => $headers]);  
            $body = $response->getBody();
            $content =$body->getContents();
            $response_array = json_decode($content,TRUE);

            $kpr_banks = $response_array['data'];
            
        } catch(RequestException $exception) {
            $is_error = true;
            $error_message .= $exception->getMessage();
        }

        //get all trx edit
        if($mode == "edit")
        {
            try
            {
                //main data
                $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . 'crm/unittrx/'.$id]);  
                $response = $client->request('GET', '', ['headers' => $headers]);  
                $body = $response->getBody();
                $content =$body->getContents();
                $response_array = json_decode($content,TRUE);
    
                $sale_trxes = $response_array['data'];

            } catch(RequestException $exception) {
                $this->is_error = true;
                $this->error_message = $exception->getMessage();
                throw new Exception($this->error_message);
            }  
            $trx_no = $sale_trxes['no'];
        }
        else{
            $trx_no = $this -> generateTransactionNo('PPJB');
        }

        
        $data = array(
            'error' => array(
                'is_error' => $is_error,
                'error_message' => $error_message
            ),
            'customers' =>$customers,
            'mode' =>$mode,
            'sales_persons' => $sales_persons,
            'sites' => $sites,
            'projects' => $projects,
            'sale_trxes' =>$sale_trxes,
            'doc_lists' => $doc_lists,
            'today_date' => date('Y-m-d'),
            'trx_no' => $trx_no,
            'kpr_banks' => $kpr_banks
        );

        return view('pages.crm.ppjb.form_addedit_'.$subcat, $data);
    }

    public function addeditSubmit(Request $request,$subcat, $mode, $id=null)
    {
        $is_error = false;
        $error_message = '';
        
        // dd($request);
        $redirectTo='ppjbrecord/';
        // dd($request->all(), $id);
        
        try
        {
            if($subcat == "validate")
            {
                $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . 'crm/unittrx/validate/'.$request['saletrxid']]);
                $payment_method = '';
                $requestMethod = 'POST';
                $body = [
                    'headers' => $headers,
                'json' =>null
                ];  
            }
            else
            {  
                $saleTrx['id'] = $request['saletrx_id'];    
                $saleTrx['no'] = $request['saletrx_no'];
                $saleTrx['customer_id'] = $request['customer'];
                $saleTrx['m_employee_id'] = $request['sales_person'];
                $saleTrx['follow_history_id'] = $request['followhistoryid'];
                $saleTrx['trx_type'] = 'PPJB';
                $saleTrx['payment_method'] = $request['payment_method'];
                $saleTrx['total_amount'] = $request['total_price_amount'];
                $saleTrx['total_discount'] = $request['total_discount'];
                $saleTrx['base_amount'] = $request['kavling_price']; 
                $saleTrx['cash_amount'] = $request['cash_amount'];
                $saleTrx['dp_kpr_amount'] = $request['dp_kpr'];
                $saleTrx['dp_inhouse_amount'] = $request['dp_inhouse'];
                $saleTrx['nup_planned_date'] = null;
                $saleTrx['ppjb_planned_date'] = $request['ppjb_planned_date'];
                $saleTrx['is_validated'] = $request['is_validated'];
                $saleTrx['is_printed'] = $request['is_printed'];
                $saleTrx['project_id'] = $request['project'];
                $saleTrx['bank_account'] = $request['bank_account'];
                $saleTrx['additional_amount']=$request['additional_amount'];
                $saleTrx['ppn_amount'] = $request['ppn_amount'];
                $saleTrx['pbhtb_amount'] = $request['bphtb_amount'];
                $saleTrx['address'] = $request['address'];
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
                $saleTrx['fasum_fee'] = $request['fasum_amount'];
                $saleTrx['notary_fee'] = $request['notary_amount'];
                $saleTrx['booking_amount'] = $request['nup_amount'];
                $saleTrx['sale_trx_id'] = $request['spuno'];

                // $i = 0;
                // $saleTrx['book'][$i]['id']=$request['booking_id'];
                // $saleTrx['book'][$i]['trx_d_code'] = 'book';
                // $saleTrx['book'][$i]['seq_no'] = 1;
                // $saleTrx['book'][$i]['tenor'] = 1;
                // $saleTrx['book'][$i]['due_day'] = null;
                // $saleTrx['book'][$i]['due_date'] = $request['book_due_date'];
                // $saleTrx['book'][$i]['amount'] = $request['booking_amount'];
                // $saleTrx['book'][$i]['project_id'] = $request['project'];
            
                if($saleTrx['payment_method']=='CASH')
                {
                    $i = 0;
                    // for($i = 0; $i < $request['tenor_cash']; $i++)
                    // {
                        $saleTrx['cash'][$i]['id']=$request['cash_id'];
                        $saleTrx['cash'][$i]['trx_d_code'] = 'cash';
                        $saleTrx['cash'][$i]['seq_no'] = $i+1;
                        $saleTrx['cash'][$i]['tenor'] = $request['tenor_cash'];
                        $saleTrx['cash'][$i]['due_day'] = $request['cash_due_day'];
                        $saleTrx['cash'][$i]['due_date'] = $request['cash_due_date'];
                        $saleTrx['cash'][$i]['amount'] = $request['cash_amount'];
                        $saleTrx['cash'][$i]['project_id'] = $request['project'];
                    // }
                }
                if($saleTrx['payment_method']=='INHOUSE')
                {
                    for($i = 0; $i < $request['dp_tenor_inhouse']; $i++)
                    {
                        $saleTrx['inhouse_dp'][$i]['id']=$request['dp_inhouse_id'][$i];
                        $saleTrx['inhouse_dp'][$i]['trx_d_code'] = 'inhouse_dp';
                        $saleTrx['inhouse_dp'][$i]['seq_no'] = $i+1;
                        $saleTrx['inhouse_dp'][$i]['tenor'] = $request['dp_tenor_inhouse'];
                        $saleTrx['inhouse_dp'][$i]['due_day'] = null;
                        $saleTrx['inhouse_dp'][$i]['due_date'] = $request['dp_inhouse_due_date'][$i];
                        $saleTrx['inhouse_dp'][$i]['amount'] = $request['dp_inhouse_amount'][$i];
                        $saleTrx['inhouse_dp'][$i]['project_id'] = $request['project'];
                    }
                    for($i = 0; $i < $request['inst_tenor_inhouse']; $i++)
                    {
                        $saleTrx['inhouse_inst'][$i]['id']=$request['inst_inhouse_id'][$i];
                        $saleTrx['inhouse_inst'][$i]['trx_d_code'] = 'inhouse_inst';
                        $saleTrx['inhouse_inst'][$i]['seq_no'] = $i+1;
                        $saleTrx['inhouse_inst'][$i]['tenor'] = $request['inst_tenor_inhouse'];
                        $saleTrx['inhouse_inst'][$i]['due_day'] = $request['inhouse_due_day'];
                        $saleTrx['inhouse_inst'][$i]['due_date'] = $request['inst_inhouse_due_date'][$i];
                        $saleTrx['inhouse_inst'][$i]['amount'] = $request['inst_inhouse_amount'][$i];
                        $saleTrx['inhouse_inst'][$i]['project_id'] = $request['project'];
                    }
                }
                if($saleTrx['payment_method']=='KPR')
                {
                    for($i = 0; $i < $request['dp_tenor_kpr']; $i++)
                    {
                        $saleTrx['kpr_dp'][$i]['id']=$request['dp_kpr_id'][$i];
                        $saleTrx['kpr_dp'][$i]['trx_d_code'] = 'kpr_dp';
                        $saleTrx['kpr_dp'][$i]['seq_no'] = $i+1;
                        $saleTrx['kpr_dp'][$i]['tenor'] = $request['dp_tenor_kpr'];
                        $saleTrx['kpr_dp'][$i]['due_day'] = null;
                        $saleTrx['kpr_dp'][$i]['due_date'] = $request['dp_kpr_due_date'][$i];
                        $saleTrx['kpr_dp'][$i]['amount'] = $request['dp_kpr_amount'][$i];
                        $saleTrx['kpr_dp'][$i]['project_id'] = $request['project'];
                    }

                    // for($i = 0; $i < $request['inst_tenor_kpr']; $i++)
                    // {
                        $saleTrx['kpr_inst'][$i]['id']=$request['inst_kpr_id'];
                        $saleTrx['kpr_inst'][$i]['trx_d_code'] = 'kpr_inst';
                        $saleTrx['kpr_inst'][$i]['seq_no'] = 1;
                        $saleTrx['kpr_inst'][$i]['tenor'] = $request['inst_tenor_kpr'];
                        $saleTrx['kpr_inst'][$i]['due_day'] = $request['inst_date_kpr'];
                        $saleTrx['kpr_inst'][$i]['due_date'] = null;
                        $saleTrx['kpr_inst'][$i]['amount'] = $request['inst_kpr_amount'];
                        $saleTrx['kpr_inst'][$i]['project_id'] = $request['project'];
                    // }
                    for($i = 0; $i < count($request['kpr_bank_id']); $i++)
                    {
                        $saleTrx['kpr_bank_payments'][$i]['id']=$request['trx_kpr_bank_id'][$i];
                        $saleTrx['kpr_bank_payments'][$i]['m_kpr_bank_payment_id'] = $request['kpr_bank_id'][$i];
                        $saleTrx['kpr_bank_payments'][$i]['payment_amount'] = $request['kpr_bank_amount'][$i];
                        $saleTrx['kpr_bank_payments'][$i]['plan_at'] = $request['kpr_bank_plan_date'][$i];
                        
                    }
                }
                // dd(json_encode($saleTrx));

                $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . 'crm/unittrx']);
                $payment_method = '';
                $requestMethod = 'POST';
                $body = [
                    'headers' => $headers,
                'json' =>$saleTrx
                ];    
            }
            $response = $client->request($requestMethod, '', $body); 
            
        } catch(RequestException $exception) {
            $is_error = true;
            $error_message .= $exception->getMessage();
            throw new Exception($error_message);
        } 
        
        $notification = array(
            'message' => 'Success insert',
            'alert-type' => 'success'
        );

        return redirect($redirectTo)->with($notification);
    }

    public function print($id) 
    {
        try
        {
            //main data
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . 'crm/ppjb/print/header/'.$id]);  
            $response = $client->request('GET', '', ['headers' => $headers]);  
            $body = $response->getBody();
            $content =$body->getContents();
            $response_array = json_decode($content,TRUE);

            $header = $response_array['data'][0];

        } catch(RequestException $exception) {
            $this->is_error = true;
            $this->error_message = $exception->getMessage();
            throw new Exception($this->error_message);
        }

        try
        {
            //main data
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . 'crm/ppjb/print/detail/'.$id]);  
            $response = $client->request('GET', '', ['headers' => $headers]);  
            $body = $response->getBody();
            $content =$body->getContents();
            $response_array = json_decode($content,TRUE);

            $detail = $response_array['data'];
            $detailtotal=0;
            if($detail != null)
            {
                for($i = 0; $i < count($detail); $i++)
                {
                    $detailtotal = $detailtotal + $detail[$i]['amount'];
                }
            }

        } catch(RequestException $exception) {
            $this->is_error = true;
            $this->error_message = $exception->getMessage();
            throw new Exception($this->error_message);
        }  
        $data = array(
            'header' => $header,
            'detail' => $detail,
            'detail_total' => $detailtotal
        );
        return view('pages.crm.ppjb.print_ppjb', $data);
    }
}