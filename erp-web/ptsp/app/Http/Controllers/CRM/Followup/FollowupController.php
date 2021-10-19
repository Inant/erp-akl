<?php

namespace App\Http\Controllers\CRM\Followup;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Exception;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Redirect;
use Carbon\Carbon;

class FollowupController extends Controller
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

    public function index() {
        $data = array();
        return view('pages.crm.followup.followup_list', $data);
    }

    public function indexCust($id) {
        $followup = null;
        try
        {
            $client = new Client(['base_uri' => $this->base_api_url . '/crm/followuphistories/cust/'. $id . '/1/']);  
            $response = $client->request('GET', ''); 
            $body = $response->getBody();
            $content = $body->getContents();
            $response_array = json_decode($content,TRUE);
            $followup = $response_array['data'];
        } catch(RequestException $exception) {      
        } 
        $data = array(
            'followup' => $followup
        );
        return view('pages.crm.followup.followup_cust', $data);
    }

    public function indexDetail($id) {
        $followup_detail = null;
        try
        {
            $client = new Client(['base_uri' => $this->base_api_url . '/crm/followuphistories/'. $id ]);  
            $response = $client->request('GET', ''); 
            $body = $response->getBody();
            $content = $body->getContents();
            $response_array = json_decode($content,TRUE);
            $followup_detail = $response_array['data'];
        } catch(RequestException $exception) {      
        } 
        $data = array(
            'followup_detail' => $followup_detail
        );
        return view('pages.crm.followup.followup_detail', $data);
    }

    public function getCustomerFollowupList(){
        $response = null;
        try
        {
            $client = new Client(['base_uri' => $this->base_api_url . '/crm/followuphistories/list/cust/1']);  
            $response = $client->request('GET', ''); 
            $body = $response->getBody();
            $content = $body->getContents();
            $response_array = json_decode($content,TRUE);
                    
            $response = $content;         
        } catch(RequestException $exception) {
                    
        }    
                
        return $response;
    }
    public function followUpList() {
        try
        {
            $client = new Client(['base_uri' => $this->base_api_url . '/crm/followuphistories/list/cust/1']);
            $response = $client->request('GET', '');
            $body = $response->getBody();
            $content = $body->getContents();
            $response_array = json_decode($content,TRUE);
            $followup = $response_array['data'];
        } catch(RequestException $exception) {

        }
        $data = array(
            'followup' => $followup
        );
        return view('pages.crm.followup.followup_sales', $data);
    }
}