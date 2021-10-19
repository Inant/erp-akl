<?php

namespace App\Http\Controllers\RAB;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Exception;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Redirect;
use DB;
use App\Exports\MaterialRabExport;

use App\Imports\ExcelDataImport;
use Maatwebsite\Excel\Facades\Excel; 
use PhpOffice\PhpSpreadsheet\Shared\Date;

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
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . 'rab/list']);  
            $response = $client->request('GET', '', ['headers' => $headers]); 
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

    public function json()
    {       
        try
        {
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . 'rab/list?site_id='.$this->site_id]);  
            $response = $client->request('GET', '', ['headers' => $headers]); 
            $body = $response->getBody();
            $content =$body->getContents();
            $response_array = json_decode($content,TRUE);
            // $rab = $response_array['data'];
            $data=DataTables::of($response_array['data'])
                                ->make(true);  
        } catch(RequestException $exception) {
            $rab = null;
            $is_error = true;
            $error_message = $exception->getMessage();
        }    
        
        return $data;
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
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . 'rab/base/MCity']);  
            $response = $client->request('GET', '', ['headers' => $headers]); 
            $body = $response->getBody();
            $content =$body->getContents();
            $response_array = json_decode($content,TRUE);

            $site_location = $response_array['data'];
            
        } catch(RequestException $exception) {
            $is_error = true;
            $error_message .= $exception->getMessage();
        }
        $response = null;
        try
        {
            if ($this->site_id == null) {
                $client1 = new Client(['base_uri' => $this->base_api_url . 'order/option']); 
            }else{
                $client1 = new Client(['base_uri' => $this->base_api_url . 'order/option?site_id='.$this->site_id]); 
            } 
            $response1 = $client1->request('GET', '', ['headers' => $headers]); 
            $body1 = $response1->getBody();
            $content1 = $body1->getContents();
            $response_array1 = json_decode($content1,TRUE);

            $response1 = $content1;  
            $order_list = $response_array1['data'];
        } catch(RequestException $exception) {
            
        }
        foreach ($order_list as $key => $value) {
            if(isset($value['customer_project_id'])){
                $nameProject=DB::table('customer_projects')->where('id', $value['customer_project_id'])->first();
                $order_list[$key]['project_name']=$nameProject != null ? $nameProject->name : '-';
            }else{
                $order_list[$key]['project_name']= '-';
            }
        }
        
        $data = array(
            'error' => array(
                'is_error' => $is_error,
                'error_message' => $error_message
            ),
            'site_locations' => $site_location,
            'order_list' => $order_list
        );
        
        return view('pages.rab.rab.rab_form_add', $data);
    }

    public function addPost(Request $request)
    {
        $data = null;
        $project_id = $request->post('project_name');
        $rab_id=$request->input('rab_list');
        $site_id = $request->post('site_name');
        $order_id = $request->post('order_id');
        $kavling_id = $request->post('kavling_id');
        $period_year = date('Y');
        $period_month = date('m');
        $rab_no = $this->generateTransactionNo('RAB', $period_year, $period_month, 1);
        // DB::table('kavlings')->where('id', $kavling_id)->update(['in_rab' => 1]);
        $projects=DB::table('projects')->where('order_id', $order_id)->first();
        try
        {
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . 'rab/base/Rab']);
            $reqBody = [
                'headers' => $headers,
                'json' => [
                    'project_id' => $projects->id,
                    // 'order_d_id' => $order_d_id,
                    'order_id' => $request->post('order_id'),
                    'kavling_id' => $kavling_id,
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

        // copy rab
        if($rab_id != null){
            $id=DB::table('rabs')->max('id');
            $project=DB::table('project_works')->where(['rab_id'=>$rab_id])->get();
            foreach ($project as $key => $val){
                $insertpw=DB::table('project_works')->insert(
                        [
                            'rab_id'                     => $id,
                            'project_id'                 => $projects->id,
                            'name'                       => $val->name,
                            'base_price'                 => $val->base_price,
                            'created_at'                 => $val->created_at,
                            'updated_at'                 => $val->updated_at,
                            'deleted_at'                 => $val->deleted_at,
                            'product_id'                => $val->product_id
                        ]
                    );
                $projects_worksubs=DB::table('project_worksubs')->where('project_work_id', $val->id)->get();
                foreach($projects_worksubs as $k=>$v){
                    $pw_id=DB::table('project_works')->max('id');
                    $insertpws=DB::table('project_worksubs')->insert(
                                [
                                    'project_work_id'    => $pw_id,
                                    'name'               => $v->name,
                                    'base_price'         => $v->base_price,
                                    'amount'             => $v->amount,
                                    'm_unit_id'          => $v->m_unit_id,
                                    'work_start'         => $v->work_start,
                                    'work_end'           => $v->work_end,
                                    'created_at'         => $v->created_at,
                                    'updated_at'         => $v->updated_at,
                                    'deleted_at'         => $v->deleted_at,
                                ]
                            );
                    $projects_worksub_ds=DB::table('project_worksub_ds')->where('project_worksub_id', $v->id)->get();
                    foreach($projects_worksub_ds as $p){
                        $pwd_id=DB::table('project_worksubs')->max('id');
                        $insertpws=DB::table('project_worksub_ds')->insert(
                            [
                                'project_worksub_id' => $pwd_id,
                                'm_item_id'          => $p->m_item_id,
                                'amount'             => $p->amount,
                                'm_unit_id'          => $p->m_unit_id,
                                'base_price'         => $p->base_price,
                                'buy_date'           => $p->buy_date,
                                'created_at'         => $p->created_at,
                                'updated_at'         => $p->updated_at,
                                'deleted_at'         => $p->deleted_at,
                                'deleted_at'         => $p->deleted_at,
                                'amount_unit_child'         => $p->amount_unit_child,
                                'qty_item'         => $p->qty_item,
                                'notes'         => $p->notes,
                                'tipe_material'         => $p->tipe_material
                            ]
                        );
                    }
                }
            }
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
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . 'rab/get_by_id/'.$id]);
            $response = $client->request('GET', '', ['headers' => $headers]); 
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
        $product=DB::table('order_ds')->select('products.*', 'kavlings.name as type_kavling', 'order_ds.total as totalkavling')->join('products', 'products.id', 'order_ds.product_id')->join('kavlings', 'products.kavling_id', 'kavlings.id')->where('order_id', $rab_header['order_id'])->where('products.kavling_id', $rab_header['kavling_id'])->get();
        
        $product_equivalent = DB::table('m_products')
        ->whereNull('deleted_at')
        ->get();
        
        $data = array(
            'error' => array(
                'is_error' => $is_error,
                'error_message' => $error_message
            ),
            'id_rab' => $id,
            'products'  => $product,
            'rab_header' => $rab_header,
            'work_header' => $work_header,
            'work_detail' => $work_detail,
            'product_equivalent' => $product_equivalent
        );
        
        return view('pages.rab.rab.rab_form_edit', $data);
    }

    public function importMaterialPost(Request $request, $pws_id) 
    {
        // dd($request);

        // validasi
        $this->validate($request, [
            'file' => 'required|mimes:csv,xls,xlsx'
        ]);
 
        // menangkap file excel
        $file = $request->file;
        // membuat nama file unik
        $nama_file = rand().$file->getClientOriginalName();
        
        // upload ke folder file_siswa di dalam folder public
        $file->move('import_excel', $nama_file);
 
        // import data
        $array = Excel::toArray(new ExcelDataImport, public_path('/import_excel/'.$nama_file));
        
        $data = array();

        foreach ($array[0] as $key => $value) {
            if ($key > 0) {
                $m_item_id = $value[0];

                $m_items = DB::table('m_items')
                ->where('m_items.no', $m_item_id)
                ->first();

                if ($m_items != null) {
                    array_push($data, [
                        'm_item_id' => $m_items->id,
                        'volume_per_turunan' => $value[1],
                        'qty_item' => $value[2]
                    ]);
                }

            }
        }
        unlink(public_path('/import_excel/'.$nama_file));

        return response()->json(['data'=>$data]);
    }

    public function editPost(Request $request){
        $rab_id = $request->post('rab_id');

        // hitung material
        $this->calculateAllMaterialByRabId($rab_id);

        $estimate_end = $request->post('estimate_end');
        try
        {
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . 'rab/base/Rab/'.$rab_id]);
            $reqBody = [
                'headers' => $headers,
                'json' => [
                    'base_price'    => $request->input('total_rab'),
                    'is_final' => true,
                    'estimate_end'  => $estimate_end
                   ]
               ]; 
            $response = $client->request('PUT', '', $reqBody); 
            $body = $response->getBody();
            $content = $body->getContents();
        } catch(RequestException $exception) {
        } 

        $get_project=DB::table('project_req_developments')->where('rab_id', $rab_id)->get();
        foreach($get_project as $row){
            try
            {
                $headers = [
                    'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                    'Accept'        => 'application/json',
                ];
                $client1 = new Client(['base_uri' => $this->base_api_url . 'inv/calculate_all_material/' . $row->id]); 
                $response1 = $client1->request('GET', '', ['headers' => $headers]); 
                $body1 = $response1->getBody();
                $content1 = $body1->getContents();
                $response_array1 = json_decode($content1,TRUE);
            } catch(RequestException $exception) {
                
            }
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
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
        ];
        $client = new Client(['base_uri' => $this->base_api_url . 'rab/site/get_by_town_id/'.$townId]);  
            $response = $client->request('GET', '', ['headers' => $headers]); 
            $body = $response->getBody();
            $content = $body->getContents();
            $response_array = json_decode($content,TRUE);

            $response = $content;         
        } catch(RequestException $exception) {
            
        }    

        return $response;
    }

    public function getProjectNameJson(){
        $orderId = $_GET['order_id'];
        $response = null;
        try
        {
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
        ];
        $client = new Client(['base_uri' => $this->base_api_url . 'rab/project/get_by_order_id/'.$orderId]);  
            $response = $client->request('GET', '', ['headers' => $headers]); 
            $body = $response->getBody();
            $content = $body->getContents();
            $response_array = json_decode($content,TRUE);

            $response = $content;         
        } catch(RequestException $exception) {
            
        }    

        return $response;
    }

    public function getOrderProduct(){
        $orderId = $_GET['order_id'];
        $response = null;
        try
        {
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . 'order/order_non_rab/'.$orderId]);  
            $response = $client->request('GET', '', ['headers' => $headers]); 
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
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . 'rab/list_by_project_id/'.$projectId]);  
            $response = $client->request('GET', '', ['headers' => $headers]); 
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
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . 'rab/project_work/get_by_rab_id/'.$rabId]);  
            $response = $client->request('GET', '', ['headers' => $headers]); 
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
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . 'rab/base/MUnit']);  
            $response = $client->request('GET', '', ['headers' => $headers]); 
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
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . 'rab/base/MItem']);  
            $response = $client->request('GET', '', ['headers' => $headers]); 
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
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . 'rab/material_category_by_type/'.$type]);  
            $response = $client->request('GET', '', ['headers' => $headers]); 
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
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . 'rab/material_by_category']);
            $reqBody = [
                'headers' => $headers,
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
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . 'master/m_sequence/generate_trx_no']);
            $reqBody = [
                'headers' => $headers,
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
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . 'rab/base/ProjectWork']);
            $reqBody = [
                'headers' => $headers,
                'json' => [
                    'project_id' => $request->project_id,
                    'rab_id' => $request->id_rab,
                    'name' => $request->project_work_name,
                    'product_id' => $request->product_id,
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

        // Update product equivalent to product
        try
        {
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . 'inv/base/Product/' . $request->product_id]);
            $reqBody = [
                'headers' => $headers,
                'json' => [
                    'm_product_id' => $request->product_equivalent
                ]
            ]; 
            $response = $client->request('PUT', '', $reqBody); 
        } catch(RequestException $exception) {
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
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . 'rab/base/ProjectWorksub']);
            $reqBody = [
                'headers' => $headers,
                'json' => [
                    'project_work_id' => $request->projectwork_id,
                    'worksub_id' => $request->worksub_id,
                    'name' => $request->projectworksub_name,
                    'base_price' => $request->projectworksub_price,
                    'amount' => $request->projectworksub_volume,
                    'm_unit_id' => $request->projectworksub_unit,
                    'work_start' => $request->projectworksub_workstart,
                    'work_end' => $request->projectworksub_workend,
                    'estimation_in_minute' => $request->projectworksub_estimasimenit,
                    'luas_1_a' => $request->luas_1_a,
                    'luas_1_b' => $request->luas_1_b,
                    'luas_2_a' => $request->luas_2_a,
                    'luas_2_b' => $request->luas_2_b,
                    'luas_3_a' => $request->luas_3_a,
                    'luas_3_b' => $request->luas_3_b,
                    'quantity' => $request->quantity,
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

    public function updateProjectWorkSub(Request $request){
        $id=$request->projectwork_id;
        try
            {
                $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
                ];
                $client = new Client(['base_uri' => $this->base_api_url . 'rab/base/ProjectWorksub/'. $id]);
                $reqBody = [
                    'headers' => $headers,
                    'json' => [
                        'name' => $request->projectworksub_name,
                        'base_price' => $request->projectworksub_price,
                        'amount' => $request->projectworksub_volume,
                        'm_unit_id' => $request->projectworksub_unit,
                        'work_start' => $request->projectworksub_workstart,
                        'work_end' => $request->projectworksub_workend,
                        'estimation_in_minute' => $request->projectworksub_estimasimenit,
                        'worksub_id' => $request->worksub_id,
                        'luas_1_a' => $request->luas_1_a,
                        'luas_1_b' => $request->luas_1_b,
                        'luas_2_a' => $request->luas_2_a,
                        'luas_2_b' => $request->luas_2_b,
                        'luas_3_a' => $request->luas_3_a,
                        'luas_3_b' => $request->luas_3_b,
                        'quantity' => $request->quantity,
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

    public function saveProjectWorkSubD(Request $request){
        $material_worksubname=$request->input('material_worksubname');
        $m_item_id=$request->input('material_name2');
        $m_unit_id=$request->input('material_unit2');
        $amount_unit_child=$request->input('volume_per_turunan2');
        $qty_item=$request->input('qty2');
        $base_price=$request->input('price2');
        for ($i=0; $i < count($m_item_id); $i++) { 
            try
            {
                $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
                ];
                $client = new Client(['base_uri' => $this->base_api_url . 'rab/base/ProjectWorksubD']);
                $reqBody = [
                    'headers' => $headers,
                    'json' => [
                        'project_worksub_id' => $material_worksubname,
                        'm_item_id' => $m_item_id[$i],
                        'amount' => 1 * $qty_item[$i],
                        'm_unit_id' => $m_unit_id[$i],
                        'amount_unit_child' => $amount_unit_child[$i],
                        'qty_item' => $qty_item[$i],
                        'base_price'    => $base_price[$i],
                        'tipe_material' => $request->tipe_material
                    ]
                ];
                $response = $client->request('POST', '', $reqBody); 
                $body = $response->getBody();
                $content = $body->getContents();
                $response_array = json_decode($content,TRUE);
                $data = $response_array;
            } catch(RequestException $exception) {
                // $is_error = true;
                // $error_message .= $exception->getMessage();
            }
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
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . 'rab/base/ProjectWorksub/'.$id]);  
            $response = $client->request('GET', '', ['headers' => $headers]); 
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
                $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
                ];
                $client = new Client(['base_uri' => $this->base_api_url . 'rab/base/ProjectWorksub/'. $id]);
                $reqBody = [
                    'headers' => $headers,
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
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . 'rab/project_worksubd/'.$id]);  
            $response = $client->request('GET', '', ['headers' => $headers]); 
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
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . 'rab/base/ProjectWorksubD/'.$id]);
            $reqBody = [
                'headers' => $headers,
                'json' => [
                    'm_item_id' => $request->material_names,
                    'amount' => $request->material_volumes * $request->qty_item,
                    'm_unit_id' => $request->material_units,
                    'amount_unit_child' => $request->volume_per_turunan,
                    'qty_item' => $request->qty_item,
                    'base_price'    => $request->price,
                    'tipe_material' => $request->tipe_material
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

    public function getProjectByIdOrder(){
        $orderId = $_GET['order_id'];
        
        $order_d=DB::table('order_ds')->where('id', $orderId)->first();
        $data['data']=DB::table('projects')->where('product_id', $order_d->product_id)->first();

        return $data;
    }
    public function getRabByIdOrder(){
        $orderId = $_GET['order_id'];
        $data['data']=DB::table('orders')
                    ->select('rabs.*')
                    ->join('rabs', 'rabs.order_id', '=', 'orders.id')
                    ->where('order_id', $orderId)->get();
        return $data;
    }
    public function finalProd($id)
    {
        $is_error = true;
        $error_message = '';
        
        $get_rab=DB::table('rabs')->where('id', $id)->first();
        if ($get_rab->is_final == true) {
            if ($get_rab->is_final_production == false) {
                DB::table('rabs')->where('id', $id)->update(['is_final_production' => true, 'date_final_production' => date('Y-m-d')]);
                // $product_sub=DB::table('product_subs')
                //                 ->join('order_ds', 'order_ds.id', '=', 'product_subs.order_d_id')
                //                 ->select('product_subs.*', 'order_ds.order_id')
                //                 ->where('order_d_id', $get_rab->order_d_id)->get();
                // foreach ($product_sub as $key => $value) {
                //     $data_inv_order=array(
                //         'product_id'    => $value->product_id,
                //         'order_id'    => $value->order_id,
                //         'order_d_id'    => $value->order_d_id,
                //         'project_id'    => $get_rab->project_id,
                //         'rab_id'    => $id,
                //         'product_sub_id'    => $value->id,
                //         'site_id'       => $this->site_id,
                //         'is_entry'    => true
                //     );
                    
                //     try
                //     {
                //         $client = new Client(['base_uri' => $this->base_api_url . 'inv/base/InvOrder']);
                //         $reqBody = [
                //             'headers' => $headers,
                // 'json' => $data_inv_order
                //         ]; 
                //         $response = $client->request('POST', '', $reqBody); 
                //     } catch(RequestException $exception) {
                //     } 
                // }
            }
        }
        // $data = array(
        //     'error' => array(
        //         'is_error' => $is_error,
        //         'error_message' => $error_message
        //     )
        // );
        return redirect('rab');
    }

    public function showAllMaterialGroupByMaterial($id){
        $response = null;
        try
        {
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . 'rab/show_all_mterial_group_by_material/'.$id]);  
            $response = $client->request('GET', '', ['headers' => $headers]); 
            $body = $response->getBody();
            $content = $body->getContents();
            $response_array = json_decode($content,TRUE);

            $response = $content;         
        } catch(RequestException $exception) {
            
        }    

        return $response;
    }

    public function calculateAllMaterialByRabId($id){
        $response = null;
        try
        {
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . 'rab/calculate_all_material/'.$id]);  
            $response = $client->request('GET', '', ['headers' => $headers]); 
            $body = $response->getBody();
            $content = $body->getContents();
            $response_array = json_decode($content,TRUE);

            $response = $content;         
        } catch(RequestException $exception) {
            
        }    

        return $response;
    }

    public function getKavlingByOrder($id){
        $cek=DB::table('rabs')->where('order_id', $id)->pluck('kavling_id');
        $query=DB::table('order_ds as od')
                    ->join('products as p', 'p.id', 'od.product_id')
                    ->join('kavlings as k', 'k.id', 'p.kavling_id')
                    ->groupBy('p.kavling_id')
                    ->where('od.order_id', $id)
                    ->whereNotIn('k.id', $cek)
                    ->select(DB::raw('MAX(k.name) as type_kavling'), DB::raw('MAX(k.id) as id'))
                    ->get();
        $data=array(
            'data'  => $query
        );
        return $data;
    }
    public function deletePwsd($rab_id, $id){
        $query=DB::table('project_worksub_ds')
                    ->where('id', $id)
                    ->delete();
        $notification = array(
            'message' => 'Success delete material',
            'alert-type' => 'success'
        );
        return redirect('rab/edit/'.$rab_id)->with($notification);
    }
    public function getWorksubs(){
        $query=DB::table('worksubs')
                    ->whereNull('deleted_at')
                    ->get();
        $data = array(
            'data' => $query,
        );
        return $data;
    }
    public function getWorksubD($id){
        $query=DB::table('worksubs')
                    ->where('id', $id)
                    ->first();
        $data = array(
            'data' => $query,
        );
        return $data;
    }
    public function editProjectWork(Request $request){
        
        try
        {
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . 'rab/base/ProjectWork/'.$request->pw_id]);
            $reqBody = [
                'headers' => $headers,
                'json' => [
                    'project_id' => $request->project_id,
                    'rab_id' => $request->id_rab,
                    'name' => $request->project_work_name,
                    'product_id' => $request->product_id,
                    'base_price' => 0,
                   ]
               ]; 
            $response = $client->request('PUT', '', $reqBody); 
            $body = $response->getBody();
            $content = $body->getContents();
            $response_array = json_decode($content,TRUE);
            // $data = $response_array['data'];
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

    public function getProductEquivalentByRabId($rab_id) {
        $data = DB::select("SELECT rabs.id, m_product_ds.m_item_id, m_product_ds.formula, products.panjang as h, products.lebar as w, m_product_ds.qty_item FROM rabs
                    JOIN orders ON rabs.order_id = orders.id
                    JOIN order_ds ON orders.id = order_ds.order_id
                    JOIN products ON order_ds.product_id = products.id
                    JOIN m_products ON products.m_product_id = m_products.id
                    JOIN m_product_ds ON m_products.id = m_product_ds.m_product_id
                    WHERE rabs.id =" . $rab_id);
        // $m_equivalents = DB::table('m_equivalents')->get();

        foreach ($data as $key => $value) {
            $qty_item = 0;
            $volume = 1;
            try {
                # code...
                $formulas = explode(';', $value->formula);
                // Dimensi
                $dimensi = strtolower($formulas[0]);
                if ($dimensi == 'h')
                    $dimensi = str_replace('h', (string)$value->h, $dimensi) * 1000;
                else if ($dimensi == 'w')
                    $dimensi = str_replace('w', (string)$value->w, $dimensi) * 1000;
                else if ($dimensi == 'none')
                    $dimensi = 1;

                $operator = strtolower($formulas[1]) == 'none' ? '-' : $formulas[1];

                $equivalent = $formulas[2];
                
                $volume = eval('return '.$dimensi.$operator.$equivalent.';');
            } catch (Exception $e) {

            }
            
            $value->volume_per_turunan = $volume;
            // dd($formula);
        }
        // dd($data);
        $datas=array(
            'data'  => $data
        );
        return $datas;
    }
    public function exportMaterial($id){
        $rab=DB::table('rabs')
                    ->select('rabs.no as rab_no', 'projects.name as project_name', 'kavlings.name as kavling_name', 'kavlings.id as kavling_id', 'rabs.order_id')
                    ->join('projects', 'projects.id', 'rabs.project_id')
                    ->join('kavlings', 'kavlings.id', 'rabs.kavling_id')
                    ->where('rabs.id', $id)
                    ->first();
        $total_kavling=DB::table('products')
                            ->select('order_ds.total')
                            ->join('order_ds', 'order_ds.product_id', 'products.id')
                            ->where('order_ds.order_id', $rab->order_id)
                            ->where('products.kavling_id', $rab->kavling_id)
                            ->first();
        // $this->calculateAllMaterialByRabId($id);
        $response=null;
        try
        {
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . 'rab/show_all_mterial_group_by_material/'.$id]);  
            $response = $client->request('GET', '', ['headers' => $headers]); 
            $body = $response->getBody();
            $content = $body->getContents();
            $response_array = json_decode($content,TRUE);

            $response = $content;         
            $item=$response_array['data'];
        } catch(RequestException $exception) {
            
        }
        $data=array(
            'rab'       => $rab,
            'total'     => $total_kavling,
            'material'  => $item,
        );
        return Excel::download(new MaterialRabExport($data), 'material_rab.xlsx');
    }
}

