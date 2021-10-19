<?php

namespace App\Http\Controllers\RAB;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Exception;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Redirect;

class RabController extends Controller
{
    private $base_api_url;
    private $site_id = null;
    // protected $user;
    public function __construct()
    {
        //Authenticate page menu
        $this->middleware(function ($request, $next) {
            Controller::isLogin(auth()->user()['role_id']); 
            $this->site_id = auth()->user()['site_id'];
            return $next($request);
        });

        $this->base_api_url = env('API_URL');

    }

    public function index(Request $request)
    {       
        $rab = null;
        $is_error = false;
        $error_message = '';
        try
        {
            $client = new Client(['base_uri' => $this->base_api_url . 'rab/list']);  
            $response = $client->request('GET', ''); 
            $body = $response->getBody();
            $content =$body->getContents();
            $response_array = json_decode($content,TRUE);
            $rab = $response_array['data'];
        } catch(RequestException $exception) {
            $rab = null;
            $is_error = true;
            $error_message = $exception->getMessage();
        }    

        $data = array(
            'error' => array(
                'is_error' => $is_error,
                'error_message' => $error_message
            ),
            'rab' => $rab
        );
        
        return view('pages.rab.rab.rab_list', $data);
    }

    public function add()
    {
        //basic variable
        $is_error = false;
        $error_message = '';

        //bussiness variable
        $site_location = null;

        //set site location
        try
        {
            $client = new Client(['base_uri' => $this->base_api_url . 'rab/base/MCity']);  
            $response = $client->request('GET', ''); 
            $body = $response->getBody();
            $content =$body->getContents();
            $response_array = json_decode($content,TRUE);

            $site_location = $response_array['data'];
            
        } catch(RequestException $exception) {
            $is_error = true;
            $error_message .= $exception->getMessage();
        }

        $data = array(
            'error' => array(
                'is_error' => $is_error,
                'error_message' => $error_message
            ),
            'site_locations' => $site_location
        );

        return view('pages.rab.rab.rab_form_add', $data);
    }

    public function addPost(Request $request)
    {
        $data = null;
        $site_id = $request->post('site_name');
        $project_id = $request->post('project_name');
        
        $period_year = date('Y');
        $period_month = date('m');
        $rab_no = $this->generateTransactionNo('RAB', $period_year, $period_month, $site_id);

        try
        {
            $client = new Client(['base_uri' => $this->base_api_url . 'rab/base/Rab']);
            $reqBody = [
                'json' => [
                    'project_id' => $project_id,
                    'no' => $rab_no,
                    'base_price' => 0,
                    'is_final' => false,
                    'stats_code' => ''
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


        $notification = array(
            'message' => 'Success insert Project RAB data',
            'alert-type' => 'success'
        );

        return redirect('rab/edit/'.$data['id'])->with($notification);
    }

    public function edit($id)
    {
        $is_error = false;
        $error_message = '';

        $rab_header = null;
        $rab_detail = null;

        $work_header = array();
        $work_detail = array();

        try
        {
            $client = new Client(['base_uri' => $this->base_api_url . 'rab/get_by_id/'.$id]);
            $response = $client->request('GET', ''); 
            $body = $response->getBody();
            $content =$body->getContents();
            $response_array = json_decode($content,TRUE);

            $rab_header = $response_array['data'][0];
            // $rab_detail = $response_array['data'][0]['rab_detail'];
            // $work_header = $rab_detail['work_header'];
            // $work_detail = $rab_detail['work_detail'];
        } catch(RequestException $exception) {
            $is_error = true;
            $error_message = $exception->getMessage();
            throw new Exception($error_message);
        }   

        $data = array(
            'error' => array(
                'is_error' => $is_error,
                'error_message' => $error_message
            ),
            'id_rab' => $id,
            'rab_header' => $rab_header,
            'work_header' => $work_header,
            'work_detail' => $work_detail
        );

        return view('pages.rab.rab.rab_form_edit', $data);
    }

    public function editPost(Request $request){
        $rab_id = $request->post('rab_id');
        try
        {
            $client = new Client(['base_uri' => $this->base_api_url . 'rab/base/Rab/'.$rab_id]);
            $reqBody = [
                'json' => [
                    'is_final' => true
                   ]
               ]; 
            $response = $client->request('PUT', '', $reqBody); 
            $body = $response->getBody();
            $content = $body->getContents();
        } catch(RequestException $exception) {
        } 


        $notification = array(
            'message' => 'Success Submit Project RAB data',
            'alert-type' => 'success'
        );

        return redirect('rab')->with($notification);
    }


    public function getSiteNameJson(){
        $townId = $_GET['town_id'];
        $response = null;
        try
        {
            $client = new Client(['base_uri' => $this->base_api_url . 'rab/site/get_by_town_id/'.$townId]);  
            $response = $client->request('GET', ''); 
            $body = $response->getBody();
            $content = $body->getContents();
            $response_array = json_decode($content,TRUE);

            $response = $content;         
        } catch(RequestException $exception) {
            
        }    

        return $response;
    }

    public function getProjectNameJson(){
        $siteId = $_GET['site_id'];
        $response = null;
        try
        {
            $client = new Client(['base_uri' => $this->base_api_url . 'rab/project/get_by_site_id/'.$siteId]);  
            $response = $client->request('GET', ''); 
            $body = $response->getBody();
            $content = $body->getContents();
            $response_array = json_decode($content,TRUE);

            $response = $content;         
        } catch(RequestException $exception) {
            
        }    

        return $response;
    }

    public function getListRabByProjectIdJson(){
        // rab/list_by_project_id/

        $projectId = $_GET['project_id'];
        $response = null;
        try
        {
            $client = new Client(['base_uri' => $this->base_api_url . 'rab/list_by_project_id/'.$projectId]);  
            $response = $client->request('GET', ''); 
            $body = $response->getBody();
            $content = $body->getContents();
            $response_array = json_decode($content,TRUE);

            $response = $content;         
        } catch(RequestException $exception) {
            
        }    

        return $response;
    }

    public function getProjectWorkByRabIdJson($rabId){
        $response = null;
        try
        {
            $client = new Client(['base_uri' => $this->base_api_url . 'rab/project_work/get_by_rab_id/'.$rabId]);  
            $response = $client->request('GET', ''); 
            $body = $response->getBody();
            $content = $body->getContents();
            $response_array = json_decode($content,TRUE);

            $response = $content;         
        } catch(RequestException $exception) {
            
        }    

        return $response;
    }

    public function getAllMUnit(){
        $response = null;
        try
        {
            $client = new Client(['base_uri' => $this->base_api_url . 'rab/base/MUnit']);  
            $response = $client->request('GET', ''); 
            $body = $response->getBody();
            $content = $body->getContents();
            $response_array = json_decode($content,TRUE);

            $response = $content;         
        } catch(RequestException $exception) {
            
        }    

        return $response;
    }

    public function getAllMItem(){
        $response = null;
        try
        {
            $client = new Client(['base_uri' => $this->base_api_url . 'rab/base/MItem']);  
            $response = $client->request('GET', ''); 
            $body = $response->getBody();
            $content = $body->getContents();
            $response_array = json_decode($content,TRUE);

            $response = $content;         
        } catch(RequestException $exception) {
            
        }    

        return $response;
    }

    public function getCategory(){
        $type = $_GET['type'];
        $response = null;
        try
        {
            $client = new Client(['base_uri' => $this->base_api_url . 'rab/material_category_by_type/'.$type]);  
            $response = $client->request('GET', ''); 
            $body = $response->getBody();
            $content = $body->getContents();
            $response_array = json_decode($content,TRUE);

            $response = $content;         
        } catch(RequestException $exception) {
            
        }    

        return $response;
    }

    public function getMaterialByCategory() {
        $category = $_GET['category'];
        $response = null;
        try
        {
            $client = new Client(['base_uri' => $this->base_api_url . 'rab/material_by_category']);
            $reqBody = [
                'json' => [
                    'category' => $category
                   ]
               ]; 
            $response = $client->request('POST', '', $reqBody); 
            $body = $response->getBody();
            $content = $body->getContents();
            $response_array = json_decode($content,TRUE);
            
            $response = $content;
        } catch(RequestException $exception) {
            // $is_error = true;
            // $error_message .= $exception->getMessage();
        }   

        return $response;
    }

    public function generateTransactionNo($trasaction_code, $period_year, $period_month, $site_id){
        try
        {
            $client = new Client(['base_uri' => $this->base_api_url . 'master/m_sequence/generate_trx_no']);
            $reqBody = [
                'json' => [
                    'transaction_code' => $trasaction_code,
                    'period_year' => $period_year,
                    'period_month' => $period_month,
                    'site_id' => $site_id
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

    public function saveProjectWork(Request $request){
        try
        {
            $client = new Client(['base_uri' => $this->base_api_url . 'rab/base/ProjectWork']);
            $reqBody = [
                'json' => [
                    'project_id' => $request->project_id,
                    'rab_id' => $request->id_rab,
                    'name' => $request->project_work_name,
                    'base_price' => 0,
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

        $response = array(
            'status' => 'success',
            'msg' => 'success',
        );
        return response()->json($response); 
    }

    public function saveProjectWorkSub(Request $request){
        try
        {
            $client = new Client(['base_uri' => $this->base_api_url . 'rab/base/ProjectWorksub']);
            $reqBody = [
                'json' => [
                    'project_work_id' => $request->projectwork_id,
                    'name' => $request->projectworksub_name,
                    'base_price' => $request->projectworksub_price,
                    'amount' => $request->projectworksub_volume,
                    'm_unit_id' => $request->projectworksub_unit,
                    'work_start' => $request->projectworksub_workstart,
                    'work_end' => $request->projectworksub_workend
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

        $response = array(
            'status' => 'success',
            'msg' => 'success',
        );
        return response()->json($response); 
    }

    public function saveProjectWorkSubD(Request $request){
        try
        {
            $client = new Client(['base_uri' => $this->base_api_url . 'rab/base/ProjectWorksubD']);
            $reqBody = [
                'json' => [
                    'project_worksub_id' => $request->material_worksubname,
                    'm_item_id' => $request->material_name,
                    'amount' => $request->material_volume,
                    'm_unit_id' => $request->material_unit,
                    'base_price' => 0
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

        $response = array(
            'status' => 'success',
            'msg' => 'success',
        );
        return response()->json($response); 
    }
    public function getProjectWorkSubs($id){
        $response = null;
        try
        {
            $client = new Client(['base_uri' => $this->base_api_url . 'rab/base/ProjectWorksub/'.$id]);  
            $response = $client->request('GET', ''); 
            $body = $response->getBody();
            $content = $body->getContents();
            $response_array = json_decode($content,TRUE);

            $response = $content;         
        } catch(RequestException $exception) {
            
        }    

        return $response;
    }
    public function editLengthWorkSub(Request $request){
        $date=date('Y-m-d');
        $today=strtotime($date);
        
        $newday=strtotime($request->projectworksub_workstarts);
        if ($newday <= $today) {
            $id=$request->projectwork_id;
            try
            {
                $client = new Client(['base_uri' => $this->base_api_url . 'rab/base/ProjectWorksub/'. $id]);
                $reqBody = [
                    'json' => [
                        'work_start' => $request->projectworksub_workstarts,
                        'work_end' => $request->projectworksub_workends
                    ]
                ]; 
                $response = $client->request('PUT', '', $reqBody); 
                $body = $response->getBody();
                $content = $body->getContents();
            } catch(RequestException $exception) {
                // $is_error = true;
                // $error_message .= $exception->getMessage();
            } 

            $response = array(
                'status' => 'success',
                'msg' => 'success',
            );
            return response()->json($response); 
        }else{
            $response = array(
                'status' => 'failed',
                'msg' => 'failed',
            );
            return response()->json($response); 
        }
    }
    public function getProjectWorkSubDsById($id){
        $response = null;
        try
        {
            $client = new Client(['base_uri' => $this->base_api_url . 'rab/project_worksubd/'.$id]);  
            $response = $client->request('GET', ''); 
            $body = $response->getBody();
            $content = $body->getContents();
            $response_array = json_decode($content,TRUE);

            $response = $content;         
        } catch(RequestException $exception) {
            
        }    

        return $response;
    }
    public function saveEditProjectWorkSubD(Request $request){
        $id=$request->material_worksub_name;
        try
        {
            $client = new Client(['base_uri' => $this->base_api_url . 'rab/base/ProjectWorksubD/'.$id]);
            $reqBody = [
                'json' => [
                    'm_item_id' => $request->material_names,
                    'amount' => $request->material_volumes,
                    'm_unit_id' => $request->material_units,
                ]
            ]; 
            $response = $client->request('PUT', '', $reqBody); 
            $body = $response->getBody();
            $content = $body->getContents();
        } catch(RequestException $exception) {
            // $is_error = true;
            // $error_message .= $exception->getMessage();
        } 

        $response = array(
            'status' => 'success',
            'msg' => 'success',
        );
        return response()->json($response); 
    }
}

