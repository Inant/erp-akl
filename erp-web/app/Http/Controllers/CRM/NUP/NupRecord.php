<?php

namespace App\Http\Controllers\CRM\NUP;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Exception;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Redirect;
use Carbon\Carbon;

class NupRecord extends Controller
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
            
        } 

        return $data['transaction_number'];
    }

    public function index(Request $request)
    {       
        //basic variable
        $is_error = false;
        $error_message = '';

        $nup = null;
        //get all project
        try
        {
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . 'crm/nup/list']);  
            $response = $client->request('GET', '', ['headers' => $headers]);  
            $body = $response->getBody();
            $content =$body->getContents();
            $response_array = json_decode($content,TRUE);
            $nup = $response_array['data'];
        } catch(RequestException $exception) {
            $is_error = true;
            $error_message .= $exception->getMessage();
        } 
        $data = array(
            'error' => array(
                'is_error' => $is_error,
                'error_message' => $error_message
            ),
            'title'=>'Penerimaan Uang',
            'current_page' =>'List Penerimaan Uang',
            'current_url'=>'nuprecord',
            'nup' => $nup
        );

        return view('pages.crm.nup.list_main', $data);
    }

    public function addedit($subcat, $mode, $id=null)
    {
        //basic variable
        $is_error = false;
        $error_message = '';

        //bussiness variable
        $customers = null;
        $salespersons = null;
        $sites =null;
        $projects = null;
        $saletrxes = null;
        $trx_no = null;
        //
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

        //get all site
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

            $salespersons = $response_array['data'];
            
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
            $client = new Client(['base_uri' => $this->base_api_url . 'rab/base/Site']);  
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
        $trx_no_bok = $this -> generateTransactionNo('BOK');
        $trx_no_nup = $this -> generateTransactionNo('NUP');
        

        $data = array(
            'error' => array(
                'is_error' => $is_error,
                'error_message' => $error_message
            ),
            'customers' =>$customers,
            'mode' =>$mode,
            'salespersons' => $salespersons,
            'sites' => $sites,
            'projects' => $projects,
            'saletrxes' =>$saletrxes,
            'trx_no_nup' => $trx_no_nup,
            'trx_no_bok' => $trx_no_bok
        );

        return view('pages.crm.nup.form_addedit_'.$subcat, $data);
    }

    public function addeditSubmit(Request $request,$subcat, $mode, $id=null)
    {
        // dd($request);
        $redirectTo='nuprecord/';
        // dd($request->all(), $id);
        $is_error = false;
        $error_message = '';
        try
        { 
            $saleTrx['id'] = $request['saletrxid'];    
            $saleTrx['no'] = $request['saletrxno_nup'];
            $saleTrx['customer_id'] = $request['customer'];
            $saleTrx['m_employee_id'] = $request['salesperson'];
            $saleTrx['follow_history_id'] = $request['followhistoryid'];
            $saleTrx['trx_type'] = $request['trxtype'];
            $saleTrx['payment_method'] = $request['paymentmethod'];
            $saleTrx['total_amount'] = null;
            $saleTrx['base_amount'] = null; 
            $saleTrx['cash_amount'] = $request['cash_amount'];
            $saleTrx['dp_kpr_amount'] = null;
            $saleTrx['dp_inhouse_amount'] = null;
            $saleTrx['nup_planned_date'] = null;
            $saleTrx['spu_planned_date'] = $request['spu_planned_date'];
            $saleTrx['is_validated'] = $request['is_validated'];
            $saleTrx['is_printed'] = $request['is_printed'];
            $saleTrx['project_id'] = $request['project'];

            $saleTrx['bank_account'] = null;
            $saleTrx['additional_amount']=null;
            $saleTrx['ppn_amount'] = null;
            $saleTrx['pbhtb_amount'] = null;
            $saleTrx['address'] = null;

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
            $response = $client->request($requestMethod, '', $body); 
            
        } catch(RequestException $exception) {
            $is_error = true;
            $error_message .= $exception->getMessage();
            throw new Exception($error_message);
        } 
        // if($request['is_use_nup'] == true)
        // {
            try
            { 
                $saleTrx['id'] = $request['saletrxid'];    
                $saleTrx['no'] = $request['saletrxno_bok'];
                $saleTrx['customer_id'] = $request['customer'];
                $saleTrx['m_employee_id'] = $request['salesperson'];
                $saleTrx['follow_history_id'] = $request['followhistoryid'];
                $saleTrx['trx_type'] = 'BOK';
                $saleTrx['payment_method'] = $request['paymentmethod'];
                $saleTrx['total_amount'] = null;
                $saleTrx['base_amount'] = null; 
                $saleTrx['cash_amount'] = $request['cash_amount'];
                $saleTrx['dp_kpr_amount'] = null;
                $saleTrx['dp_inhouse_amount'] = null;
                $saleTrx['nup_planned_date'] = null;
                $saleTrx['spu_planned_date'] = $request['spu_planned_date'];
                $saleTrx['is_validated'] = $request['is_validated'];
                $saleTrx['is_printed'] = $request['is_printed'];
                $saleTrx['project_id'] = $request['project'];

                $saleTrx['bank_account'] = null;
                $saleTrx['additional_amount']=null;
                $saleTrx['ppn_amount'] = null;
                $saleTrx['pbhtb_amount'] = null;
                $saleTrx['address'] = null;

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
                $response = $client->request($requestMethod, '', $body); 
                
            } catch(RequestException $exception) {
                $is_error = true;
                $error_message .= $exception->getMessage();
                throw new Exception($error_message);
            } 
        // }
        
        $notification = array(
            'message' => 'Success insert',
            'alert-type' => 'success'
        );

        return redirect($redirectTo)->with($notification);
    }

    public function getPageContent($category, $id=null)
    {
        $object = null;
        try
        {
            if ($category == 'projectbyid')
            {
                $url_destination = 'rab/base/Project/'.$id;
            }
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . $url_destination]);  
            $response = $client->request('GET', '', ['headers' => $headers]);  
            $body = $response->getBody();
            $content =$body->getContents();
            $object = $content;

        } catch(RequestException $exception) {
            $this->is_error = true;
            $this->error_message = $exception->getMessage();
            throw new Exception($this->error_message);
        }  
        

        return $object;
    }
}