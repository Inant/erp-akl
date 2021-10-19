<?php

namespace App\Http\Controllers\CRM\Discount;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Exception;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Redirect;
use Carbon\Carbon;

class DiscountRequest extends Controller
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
            $client = new Client(['base_uri' => $this->base_api_url . 'master/m_sequence/generate_trx_no']);
            $reqBody = [
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
        $objects = null;
        //get all project
        try
        {
            $client = new Client(['base_uri' => $this->base_api_url . 'crm/request/discount/all']);  
            $response = $client->request('GET', ''); 
            $body = $response->getBody();
            $content =$body->getContents();
            $response_array = json_decode($content,TRUE);

            $objects = $response_array['data'];
            // dd($objects);
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
            'objects' => $objects,
            'title'=>'Discount Request',
            'current_page' =>'Discount Request List',
            'current_url'=>'discountrequest',
            
        );

        return view('pages.crm.discount.list_request', $data);
    }

    public function addedit($subcat, $mode, $id=null)
    {
        //basic variable
        $is_error = false;
        $error_message = '';

        //bussiness variable
        $saletrxs = null;
        $discountrequests = null;
        $sites =null;
        $projects = null;
        $site_location_id=1;
        $trx_no = null;
        
        //main data
        $nups = null;
        //get all SPU
        try
        {
            $client = new Client(['base_uri' => $this->base_api_url . 'crm/spu/list']);  
            $response = $client->request('GET', ''); 
            $body = $response->getBody();
            $content =$body->getContents();
            $response_array = json_decode($content,TRUE);
            $spus = $response_array['data'];
        } catch(RequestException $exception) {
            $is_error = true;
            $error_message .= $exception->getMessage();
        } 
        //get all site
        try
        {
            $client = new Client(['base_uri' => $this->base_api_url . 'rab/site/get_by_town_id/'. $site_location_id]);  
            $response = $client->request('GET', ''); 
            $body = $response->getBody();
            $content =$body->getContents();
            $response_array = json_decode($content,TRUE);

            $sites = $response_array['data'];
            
        } catch(RequestException $exception) {
            $is_error = true;
            $error_message .= $exception->getMessage();
        } 
        //get all trx edit
        if($mode == "approval")
        {
            try
            {
                //main data
                $client = new Client(['base_uri' => $this->base_api_url . 'crm/request/discount/id/'.$id]);  
                $response = $client->request('GET', ''); 
                $body = $response->getBody();
                $content =$body->getContents();
                $response_array = json_decode($content,TRUE);
    
                $discountrequests = $response_array['data'];
            } catch(RequestException $exception) {
                $this->is_error = true;
                $this->error_message = $exception->getMessage();
                throw new Exception($this->error_message);
            }  

            try
            {
                $client = new Client(['base_uri' => $this->base_api_url . 'rab/kavling/all']);  
                $response = $client->request('GET', ''); 
                $body = $response->getBody();
                $content =$body->getContents();
                $response_array = json_decode($content,TRUE);

                $projects = $response_array['data'];
                
            } catch(RequestException $exception) {
                $is_error = true;
                $error_message .= $exception->getMessage();
            } 
            $trx_no = $discountrequests['no'];
        }
        else{
            $trx_no = $this -> generateTransactionNo('DCS_REQ');
        }

 //       dd($rabrequests);
        $data = array(
            'error' => array(
                'is_error' => $is_error,
                'error_message' => $error_message
            ),
            'mode' => $mode,
            'spus' => $spus,
            'discountrequests' =>$discountrequests,
            'sites' => $sites,
            'projects' => $projects,
            'trx_no' => $trx_no
            // 'saletrxes'=> $saletrxes
        );

        return view('pages.crm.discount.form_request_addedit_'.$subcat, $data);
    }

    public function addeditSubmit(Request $request,$subcat, $mode, $id=null)
    {
        $is_error = false;
        $error_message = '';
        
        $redirectTo='discountrequest/';
        // dd($request);
        
        try
        {
            $discountRequest['id'] = $request['discountreqid'];    

            if($mode=='add')
            {
                $discountRequest['no'] = $request['discountreqno'];    
                $discountRequest['sale_trx_id'] = $request['spuno'];       
                $discountRequest['project_id'] = $request['project'];
                $discountRequest['amount_requested'] = $request['amount_requested'];
            }
            else if($mode == 'approval')
            {
                $discountRequest['amount'] = $request['amount'];
                $discountRequest['is_approved'] = $request['is_approved'];
            }
            // dd((json_encode($discountRequest)));

            $client = new Client(['base_uri' => $this->base_api_url . 'crm/request/discount']);
            $payment_method = '';
            $requestMethod = 'POST';
            $body = [
                'json' =>$discountRequest
            ];    
            
            $response = $client->request($requestMethod, '', $body); 
        } catch(RequestException $exception) {
            $is_error = true;
            $error_message .= $exception->getMessage();
            throw new Exception($error_message);
        } 
        
        $notification = array(
            'message' => 'Success '.$mode,
            'alert-type' => 'success'
        );

        return redirect($redirectTo)->with($notification);
    }

}