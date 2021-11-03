<?php

namespace App\Http\Controllers\INV;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use App\Http\Controllers\RAB\RabController;
use Carbon\Carbon;
use DB;
use Yajra\DataTables\Facades\DataTables;

class ProjectReqDevController extends Controller
{
    private $base_api_url;
    public function __construct()
    {
        //Authenticate page menu
        $this->middleware(function ($request, $next) {
            Controller::isLogin(auth()->user()['role_id']); 
            $this->site_id = auth()->user()['site_id'];
            $this->username = auth()->user()['email'];
            $this->user_name = auth()->user()['name'];
            $this->user_id = auth()->user()['id'];
            return $next($request);
        });

        $this->base_api_url = env('API_URL');
    }
    
    public function index()
    {
        $rab = null;
        $is_error = false;
        $error_message = '';

        $data = array(
            'error' => array(
                'is_error' => $is_error,
                'error_message' => $error_message
            )
        );
        
        return view('pages.inv.project_req_dev.project_req_dev_list', $data);
    }
    public function json() {
        $query=DB::table('project_req_developments')
                    ->join('rabs', 'rabs.id', '=', 'project_req_developments.rab_id')
                    ->join('kavlings', 'rabs.kavling_id', '=', 'kavlings.id')
                    // ->join('order_ds', 'rabs.order_d_id', '=', 'order_ds.id')
                    // ->join('products', 'order_ds.product_id', '=', 'products.id')
                    ->join('orders', 'orders.id', '=', 'project_req_developments.order_id')
                    ->join('customers', 'orders.customer_id', '=', 'customers.id')
                    ->select('project_req_developments.id', 'project_req_developments.no', 'project_req_developments.status', 'project_req_developments.total', 'project_req_developments.request_date', 'project_req_developments.work_start', 'project_req_developments.finish_date', 'rabs.no AS rab_no', 'orders.spk_number', 'customers.coorporate_name AS customer_name', 'kavlings.amount AS total_kavling', 'kavlings.name as type_kavling', DB::raw('COALESCE((SELECT MAX(order_ds.total) from order_ds join products on products.id=order_ds.product_id where order_ds.order_id=rabs.order_id and products.kavling_id=rabs.kavling_id), 0) as total_order'))
                    ->whereNull('project_req_developments.deleted_at')
                    ->get();
        $data=DataTables::of($query)
                    ->addColumn('action', function ($row) {
                        return '<a hidden href="'.url('/').'/project_req_dev/edit/'.$row->id.'" class="btn btn-success btn-sm"><i class="mdi mdi-pencil"></i></a>'.' 
                        '.'<a href="'.url('/').'/project_req_dev/delete/'.$row->id.'" class="btn btn-danger btn-sm" onclick="javasciprt: return confirm(\'Are You Sure ?\')"><i class="mdi mdi-delete"></i></a>';
                    })
                    ->addColumn('detail', function ($row) {
                        return '<button id="modal_detail" class="btn btn-success btn-sm" data-toggle="modal" data-id="'.$row->id.'" data-target=".bs-example-modal-lg" onclick="showDetail(this)" class="btn btn-success btn-sm"><i class="mdi mdi-eye"></i></button>';
                    })
                    ->rawColumns(['action', 'detail'])
                    ->make(true);        
        
        return $data;
    }
    
    public function create()
    {
        //basic variable
        $is_error = false;
        $error_message = '';

        //bussiness variable
        $site_location = null;

        
        $response = null;
        try
        {
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
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

        $data = array(
            'error' => array(
                'is_error' => $is_error,
                'error_message' => $error_message
            ),
            // 'site_locations' => $site_location,
            'order_list' => $order_list
        );
        return view('pages.inv.project_req_dev.project_req_dev_create', $data);
    }
    public function save(Request $request){
        $rab_id=$request->input('rab_no');
        $order_id=$request->input('order_id');
        $total_order=$request->input('total_order');
        $request_date=$request->input('request_date');
        $work_start=$request->input('work_start');
        $estimate_end=$request->input('estimate_end');
        $note=$request->input('note');

        $period_year = Carbon::now()->year;
        $period_month = Carbon::now()->month;
        $rabcon = new RabController();
        $memo_no = $rabcon->generateTransactionNo('REQ_DEV', $period_year, $period_month, $this->site_id );
        
        try
        {
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
 ];
$client = new Client(['base_uri' => $this->base_api_url . 'rab/base/ProjectReqDevelopment']);
            $reqBody = [
                'headers' => $headers,
                'json' => [
                    'no'    => $memo_no,
                    'rab_id' => $rab_id,
                    'order_id' => $order_id,
                    'total' => $total_order,
                    'request_date' => $request_date,
                    'work_start' => $work_start,
                    'finish_date' => $estimate_end,
                    'note' => $note
                   ]
               ]; 
            $response = $client->request('POST', '', $reqBody); 
            $body = $response->getBody();
            $content = $body->getContents();
            $response_array = json_decode($content,TRUE);
            $data = $response_array['data'];
            $project_req_dev = $response_array['data'];
        } catch(RequestException $exception) {
            // $is_error = true;
            // $error_message .= $exception->getMessage();
        } 

        try
        {
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client1 = new Client(['base_uri' => $this->base_api_url . 'inv/calculate_all_material/' . $project_req_dev['id']]); 
            $response1 = $client1->request('GET', '', ['headers' => $headers]); 
            $body1 = $response1->getBody();
            $content1 = $body1->getContents();
            $response_array1 = json_decode($content1,TRUE);
        } catch(RequestException $exception) {
            
        }

        return redirect('project_req_dev');
    }
    public function delete($id) {
        try
        {
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
 ];
$client = new Client(['base_uri' => $this->base_api_url . 'rab/base/ProjectReqDevelopment/' . $id]);  
            $response = $client->request('DELETE', '', ['headers' => $headers]); 
        } catch(RequestException $exception) {    
        }

        $notification = array(
            'message' => 'Success delete material',
            'alert-type' => 'success'
        );

        return redirect('project_req_dev')->with($notification);
    }
    public function edit($id)
    {
        //basic variable
        $is_error = false;
        $error_message = '';

        //bussiness variable
        try
        {
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
 ];
$client = new Client(['base_uri' => $this->base_api_url . 'rab/base/ProjectReqDevelopment/' . $id]);  
            $response = $client->request('GET', '', ['headers' => $headers]); 
            $body = $response->getBody();
            $content = $body->getContents();
            $response_array = json_decode($content,TRUE);

            $response = $content;  
            $detail = $response_array['data'];
        } catch(RequestException $exception) {    
        }
        
        $response = null;
        try
        {
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
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

        try
        {
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
 ];
$client = new Client(['base_uri' => $this->base_api_url . 'rab/base/Rab/'.$detail['rab_id']]);
            $response = $client->request('GET', '', ['headers' => $headers]); 
            $body = $response->getBody();
            $content = $body->getContents();
            $response_array = json_decode($content,TRUE);

            $response = $content;  
            $rab = $response_array['data'];
        } catch(RequestException $exception) {
            
        }

        $data = array(
            'error' => array(
                'is_error' => $is_error,
                'error_message' => $error_message
            ),
            'detail' => $detail,
            'order_list' => $order_list,
            'rab'   => $rab
        );
        // print_r($detail);
        // exit;
        return view('pages.inv.project_req_dev.project_req_dev_edit', $data);
    }
    public function update(Request $request){
        $id=$request->input('id');
        $rab_id=$request->input('rab_no');
        $order_id=$request->input('order_id');
        $total_order=$request->input('total_order');
        $request_date=$request->input('request_date');
        $estimate_end=$request->input('estimate_end');
        $note=$request->input('note');
        $period_year = Carbon::now()->year;
        $period_month = Carbon::now()->month;
        $rabcon = new RabController();
        $memo_no = $rabcon->generateTransactionNo('REQ_DEV', $period_year, $period_month, $this->site_id );
        
        try
        {
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
 ];
$client = new Client(['base_uri' => $this->base_api_url . 'rab/base/ProjectReqDevelopment/'.$id]);
            $reqBody = [
                'headers' => $headers,
                'json' => [
                    'no'    => $memo_no,
                    'rab_id' => $rab_id,
                    'order_id' => $order_id,
                    'total' => $total_order,
                    'request_date' => $request_date,
                    'finish_date' => $estimate_end,
                    'note' => $note
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
        return redirect('project_req_dev');
    }
    public function getRab($id){
        // $query=DB::table('rabs')
        //                 ->where('rabs.id', $id)
        //                 ->join('order_ds', 'order_ds.order_id', '=', 'rabs.order_id')
        //                 ->select(DB::raw('MAX(total) as total'))
        //                 ->first();
        $query=DB::table('rabs')
                        ->where('rabs.id', $id)
                        ->join('kavlings', 'kavlings.id', '=', 'rabs.kavling_id')
                        ->select('amount')
                        ->first();
        $count_use=DB::table('rabs')
                    ->where('project_req_developments.rab_id', $id)
                    ->whereNull('project_req_developments.deleted_at')
                    ->join('project_req_developments', 'project_req_developments.rab_id', '=', 'rabs.id')
                    ->select(DB::raw('COALESCE(SUM(total), 0) AS total'))
                    ->first();
        $data['data']=array('total' => $query->amount, 'use' => $count_use->total);
        return $data;
    }
    public function progressList(){
        $is_error = false;
        $error_message = '';

        $data = array(
            'error' => array(
                'is_error' => $is_error,
                'error_message' => $error_message
            )
        );
        
        return view('pages.inv.project_req_dev.progress_req_dev', $data);
    }
    public function detailJson($id){
            // left join dev_projects dp on ir.id = dp.inv_request_id
        $datas = DB::select("
        select ir.*, r.no as rab_no, work_header, p.name as project_name, prq.no AS req_no, k.name as type_kavling, prq.total, dp.id as dp_id
        from dev_projects dp
        join inv_requests ir on dp.inv_request_id=ir.id
        join projects p on dp.project_id = p.id
        join rabs r on ir.rab_id = r.id
        join kavlings k on k.id = r.kavling_id
        join project_req_developments prq on ir.project_req_development_id = prq.id
        where ir.req_type != 'RET_ITEM' AND ir.project_req_development_id = ".$id."
        ");

        foreach ($datas as $key => $value) {
            // $value->products=DB::table('products')->where('id', $value->product_id)->first();
            $dev_projects=DB::table('dev_projects')->select('is_done')->where('inv_request_id', $value->id)->first();
            $value->is_done=$dev_projects != null ? $dev_projects->is_done : false;
        }
        // $data=DataTables::of($datas)
        //                     ->make(true);   
        $data['data']=$datas;
        return $data;
    }
    public function report($id){

        $datas = DB::table('dev_projects')
                    ->where('dev_projects.id', $id)
                    ->select('dev_projects.*', 'work_header', 'kavlings.name as type_kavling', 'prd.total')
                    // ->join('project_works', 'project_works.id', '=', 'dev_projects.project_work_id')
                    // ->join('products', 'dev_projects.product_id', '=', 'products.id')
                    ->join('rabs', 'dev_projects.rab_id', '=', 'rabs.id')
                    ->join('kavlings', 'kavlings.id', '=', 'rabs.kavling_id')
                    ->join('project_req_developments as prd', 'prd.id', '=', 'dev_projects.project_req_development_id')
                    ->get();
        foreach ($datas as $key => $value) {
            $dev_project_ds=DB::table('dev_project_ds')
                                ->select('dev_project_ds.*', 'users.name')
                                // ->where('project_worksubs.name', 'not ilike', '%pasang%')
                                ->where('dev_project_id', $value->id)
                                // ->leftJoin('dev_project_ds', 'project_worksubs.id', '=', 'dev_project_ds.project_worksub_id')
                                ->leftJoin('users', 'users.id', '=', 'dev_project_ds.user_id')
                                ->get();
            $value->dev_project_ds=$dev_project_ds;
            foreach ($value->dev_project_ds as $k) {
                $dev_project_workers=DB::table('dev_project_workers')->where('dev_project_d_id', $k->id)->get();
                $dev_project_labels=DB::table('dev_project_labels')
                                        ->select('product_subs.no')
                                        ->where('dev_project_d_id', $k->id)
                                        ->join('product_subs', 'product_subs.id', 'dev_project_labels.product_sub_id')
                                        ->get();
                $dev_project_durations=DB::table('dev_project_d_durations')->where('dev_project_d_id', $k->id)->get();
                $k->worker=$dev_project_workers;
                $k->label=$dev_project_labels;
                $k->durations=$dev_project_durations;
                $long_work=0;
                foreach ($dev_project_durations as $duration) {
                    $diff = strtotime(($duration->work_end != null ? $duration->work_end : date('Y-m-d H:i:s'))) - strtotime($duration->work_start);
                    $long_work+=$diff;
                }
                $k->long_work=$long_work;
            }
        }
        
        $data=array(
            'data'  => $datas
        );
        // return $data;
        return view('pages.inv.project_req_dev.report_req_dev', $data);
    }
    public function saveProduct($id){

        $query=DB::table('project_req_developments')->where('id', $id)->first();
        $data = array(
            'data'  => $query,
            'id'    => $id
        );
        return view('pages.inv.project_req_dev.save_prod_req_dev', $data);
    }
    public function getProductLabel($id){
        $cek_label=DB::table('inv_orders')
                        ->where('project_req_development_id', $id)
                        ->join('inv_order_ds', 'inv_order_ds.inv_order_id', '=', 'inv_orders.id')
                        ->pluck('inv_order_ds.dev_project_label_id');
        $label_done=DB::table('inv_requests')
                        ->where('dev_projects.project_req_development_id', $id)
                        ->join('dev_projects', 'inv_requests.id', '=', 'dev_projects.inv_request_id')
                        ->join('dev_project_ds', 'dev_project_ds.dev_project_id', '=', 'dev_projects.id')
                        ->join('dev_project_labels', 'dev_project_ds.id', '=', 'dev_project_labels.dev_project_d_id')
                        ->join('product_subs', 'product_subs.id', '=', 'dev_project_labels.product_sub_id')
                        ->join('products', 'product_subs.product_id', '=', 'products.id')
                        ->select('dev_project_labels.*', 'product_subs.no as label', 'products.name', 'products.item', 'products.series', 'products.panjang AS panjang', 'products.lebar AS lebar', 'dev_projects.work_header as project_name', DB::raw('COALESCE((SELECT id FROM inv_request_prod_ds WHERE inv_request_id = inv_requests.id and product_sub_id=product_subs.id),0) AS inv_request_prod_id'))
                        ->where('dev_project_ds.is_done', true)
                        ->whereNotIn('dev_project_labels.id', $cek_label)
                        ->whereIn('dev_project_ds.work_detail', ['Merakit Daun Jendela ke Kusen', 'Packing Pintu', 'Pasang Glasbit', 'Packing', "Pasang Lockset dan Handle"])
                        ->get();
        $data = array(
            'data'  => $label_done,
        );
        return $data;
    }
    public function saveProductLabel(Request $request, $id){
        $product_sub_id=$request->input('product_sub_id');
        $inv_request_prod_id=$request->input('inv_request_prod_id');
        $work_id=$request->input('id');
        $product_label=$request->input('label');
        $check_prod_sub_id=$request->input('check_prod_sub_id');
        $storage_locations=$request->input('storage');
        $kondisi=$request->input('kondisi');
        
        $period_year = date('Y');
        $period_month = date('m');

        $get_rab=DB::table('rabs')->where('id', $request->input('rab_no'))->first();
        $inv_order_no = $this->generateTransactionNo('INV_ORD', $period_year, $period_month, $this->site_id );
        
        try
        {
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . 'inv/base/InvOrder']);
            $reqBody = [
                'headers' => $headers,
                'json' => [
                    'no'          => $inv_order_no,
                    'order_id'    => $request->input('order_id'),
                    'project_id'    => $get_rab->project_id,
                    'rab_id'    => $request->input('rab_no'),
                    'site_id'       => $this->site_id,
                    'project_req_development_id' => $id,
                    'is_entry'    => true,
                    'type'          => 'RECEIPT'
                ]
            ]; 
            
            $response = $client->request('POST', '', $reqBody); 
            $body = $response->getBody();
            $content = $body->getContents();
            $response_array = json_decode($content,TRUE);
            $inv_order=$response_array['data'];
        } catch(RequestException $exception) {
        } 


        foreach ($work_id as $key => $value) {
            $cek=in_array($value, $check_prod_sub_id);
            if ($cek == true) {
                $product_sub=DB::table('product_subs')->where('id', $product_sub_id[$key])->first();
                try
                {
                    $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
 ];
$client = new Client(['base_uri' => $this->base_api_url . 'inv/base/InvOrderD']);
                    $reqBody = [
                        'headers' => $headers,
                'json' => [
                            'inv_order_id'    => $inv_order['id'],
                            'product_sub_id'    => $product_sub_id[$key],
                            'inv_request_prod_id'    => $inv_request_prod_id[$key],
                            'dev_project_label_id'    => $value,
                            'no'    => $product_label[$key],
                            'order_d_id'    => $product_sub->order_d_id,
                            'storage_locations' => $storage_locations[$key],
                            'condition' => $kondisi[$key], 
                            'is_entry'  => 1
                        ]
                    ]; 
                    $response = $client->request('POST', '', $reqBody);
                } catch(RequestException $exception) {
                }
            }
        }
        return redirect('inventory/acc_product');
    }
    private function generateTransactionNo($trasaction_code, $period_year, $period_month, $site_id){
        try
        {
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
 ];
$client = new Client(['base_uri' => $this->base_api_url . '/master/m_sequence/generate_trx_no']);
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
    public function transactionProductList(){
        
        return view('pages.inv.project_req_dev.trx_product_list');
    }
    public function createTransactionProduct(){
        $customer = null;
        try
        {
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
 ];
$client = new Client(['base_uri' => $this->base_api_url . '/crm/customerdatamain']);  
            $response = $client->request('GET', '', ['headers' => $headers]); 
            $body = $response->getBody();
            $content = $body->getContents();
            $response_array = json_decode($content,TRUE);
            $customer = $response_array['data'];
        } catch(RequestException $exception) {      
        }
        
        $data = array(
            'customer' => $customer,
            'site_id'   => $this->site_id
        );
        return view('pages.inv.project_req_dev.trx_product_form', $data);
    }
    public function getRequestByCustId($id){
        $query=DB::table('orders')
                    ->where('customer_id', $id)
                    ->join('project_req_developments as prd', 'prd.order_id', '=', 'orders.id')
                    ->select('prd.id', 'prd.no')
                    ->get();
        $data=array(
            'data' => $query
        );
        return $data;
    }
    public function getInvOrders($id){
        $val_entry=DB::table('inv_orders')
                    ->where('project_req_development_id', $id)
                    ->where('iod.is_entry', 0)
                    ->join('inv_order_ds as iod', 'iod.inv_order_id', '=', 'inv_orders.id')
                    ->join('product_subs as ps', 'iod.product_sub_id', '=', 'ps.id')
                    ->pluck('iod.dev_project_label_id');
        $query=DB::table('inv_orders')
                    ->where('project_req_development_id', $id)
                    ->where('iod.is_entry', 1)
                    ->whereNotIn('iod.dev_project_label_id', $val_entry)
                    ->join('inv_order_ds as iod', 'iod.inv_order_id', '=', 'inv_orders.id')
                    ->join('product_subs as ps', 'iod.product_sub_id', '=', 'ps.id')
                    ->join('products as p', 'p.id', '=', 'ps.product_id')
                    ->select('p.name', 'p.item', 'p.series', 'iod.no', 'ps.id', 'iod.condition', 'p.price', 'iod.inv_request_prod_id', 'iod.dev_project_label_id')
                    ->get();
        $product_sub=DB::table('inv_orders')
                    ->where('project_req_development_id', $id)
                    ->where('iod.is_entry', 0)
                    ->join('inv_order_ds as iod', 'iod.inv_order_id', '=', 'inv_orders.id')
                    ->join('product_subs as ps', 'iod.product_sub_id', '=', 'ps.id')
                    ->pluck('iod.product_sub_id');
        $data=array(
            'data' => $query,
            'prod_sub'  => $product_sub
        );
        return $data;
    }
    public function saveTrxProduct(Request $request){
        $dev_project_label_id=$request->input('dev_project_label_id');
        $work_id=$request->input('inv_request_prod_id');
        $product_sub_id=$request->input('product_sub_id');
        $product_label=$request->input('label');
        $check_prod_sub_id=$request->input('check_prod_sub_id');
        $due_date=$request->input('due_date');
        $project_req_id=$request->input('project_req_id');
        $total_price=$request->input('total_price');
        $no_surat_jalan=$request->input('no_surat_jalan');
        $m_item_id = $request->post('m_item_id');
        $m_unit_id = $request->post('m_unit_id');
        $amount = $request->post('amount');
        $m_warehouse_id = $request->post('m_warehouse_id');
        $label = $request->post('label');
        
        $project_req_dev=DB::table('project_req_developments')->where('id', $project_req_id)->first();
        
        $period_year = date('Y');
        $period_month = date('m');

        $get_rab=DB::table('rabs')->where('id', $project_req_dev->rab_id)->first();
        $inv_order_no = $this->generateTransactionNo('INV_ORD', $period_year, $period_month, $this->site_id );
        
        try
        {
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . 'inv/base/InvOrder']);
            $reqBody = [
                'headers' => $headers,
                'json' => [
                    'no'          => $inv_order_no,
                    'order_id'    => $project_req_dev->order_id,
                    'project_id'    => $get_rab->project_id,
                    'rab_id'    => $project_req_dev->rab_id,
                    'site_id'       => $this->site_id,
                    'project_req_development_id' => $project_req_id,
                    'is_entry'    => false,
                    'type'          => 'TRX_PRODUCT',
                    // 'amount'        => $total_price,
                    'due_date'      => $due_date,
                    'no_surat_jalan'      => $no_surat_jalan,
                ]
            ]; 
            $response = $client->request('POST', '', $reqBody); 
            $body = $response->getBody();
            $content = $body->getContents();
            $response_array = json_decode($content,TRUE);
            $inv_order=$response_array['data'];
        } catch(RequestException $exception) {
        } 

        foreach ($dev_project_label_id as $key => $value) {
            $cek=in_array($value, $check_prod_sub_id);
            if ($cek == true) {
                $product_sub=DB::table('product_subs')->where('id', $product_sub_id[$key])->first();
                try
                {
                    $headers = [
                        'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                        'Accept'        => 'application/json',
                    ];
                    $client = new Client(['base_uri' => $this->base_api_url . 'inv/base/InvOrderD']);
                    $reqBody = [
                        'headers' => $headers,
                        'json' => [
                            'inv_order_id'    => $inv_order['id'],
                            'product_sub_id'    => $product_sub_id[$key],
                            'order_d_id'    => $product_sub->order_d_id,
                            'inv_request_prod_id'    => $work_id[$key],
                            'dev_project_label_id'  => $value,
                            'no'    => $product_label[$key],
                            // 'storage_locations' => $storage_locations[$key],
                            // 'condition' => $kondisi[$key], 
                            'is_entry'  => 0
                        ]
                    ]; 
                    $response = $client->request('POST', '', $reqBody);
                } catch(RequestException $exception) {
                }
            }
        }
        // if (count($check_prod_sub_id) == count($product_sub_id)) {
        //     $query=DB::table('tbl_trx_akuntansi')
        //                 ->join('tbl_trx_akuntansi_detail', 'tbl_trx_akuntansi_detail.id_trx_akun', 'tbl_trx_akuntansi.id_trx_akun')
        //                 ->select(DB::raw("SUM(CASE WHEN tipe = 'DEBIT' THEN tbl_trx_akuntansi_detail.jumlah ELSE 0 END) - SUM(CASE WHEN tipe = 'KREDIT' THEN tbl_trx_akuntansi_detail.jumlah ELSE 0 END) as total"))
        //                 ->where('project_req_development_id', $project_req_id)
        //                 ->where('tbl_trx_akuntansi_detail.id_akun', 81)
        //                 ->whereNotNull('inv_request_id')
        //                 ->first();
        //     $input_data=array(
        //         'project_req_development_id'    => $project_req_id,
        //         'total'     => $query->total,
        //         'deskripsi' => 'Jurnal Balik Biaya Produksi No Permintaan '.$project_req_dev->no,
        //         'user_id'   => $this->user_id,
        //         'tgl'       => date('Y-m-d'),
        //         'location_id'   => $this->site_id
        //     );
        //     // $this->journalPenyerahan($input_data);
        // }
        
        return redirect('project_req_dev/trx_product');
    }
    public function jsonAccProduct() {
        $query=DB::table('inv_orders')
                    ->join('project_req_developments', 'project_req_developments.id', '=', 'inv_orders.project_req_development_id')
                    ->join('rabs', 'rabs.id', '=', 'inv_orders.rab_id')
                    ->join('projects', 'projects.id', '=', 'inv_orders.project_id')
                    ->join('orders', 'orders.id', '=', 'inv_orders.order_id')
                    ->join('order_ds', 'orders.id', '=', 'order_ds.order_id')
                    ->join('products', 'order_ds.product_id', '=', 'products.id')
                    ->join('customers', 'orders.customer_id', '=', 'customers.id')
                    ->whereIn('inv_orders.type', ['TRX_PRODUCT', 'RESEND'])
                    ->select('inv_orders.*', 'rabs.no as rab_no', 'products.item', 'products.name as product_name', 'products.series', 'products.panjang', 'products.lebar', 'orders.order_no', 'customers.coorporate_name', 'projects.name', 'project_req_developments.no AS req_no')
                    ->get();
        $data=DataTables::of($query)
                    ->make(true); 
        return $data; 
    }
    public function printTrxProduct($id) {
        $query=DB::table('inv_order_ds')
                    ->join('inv_orders as io', 'io.id', '=', 'inv_order_ds.inv_order_id')
                    ->join('project_req_developments as prd', 'prd.id', '=', 'io.project_req_development_id')
                    ->join('product_subs', 'product_subs.id', '=', 'inv_order_ds.product_sub_id')
                    ->join('products', 'products.id', '=', 'product_subs.product_id')
                    ->join('orders', 'io.order_id', '=', 'orders.id')
                    ->join('customers AS c', 'c.id', '=', 'orders.customer_id')
                    ->select('inv_order_ds.*', 'product_subs.*', 'inv_order_ds.no AS label', 'products.name AS product_name', 'products.price', 'products.item', 'products.series', 'products.panjang', 'products.lebar', 'prd.no AS req_no', 'prd.id AS req_id', 'io.order_id', 'c.name', 'c.coorporate_name', 'orders.order_no', 'prd.request_date', 'io.no_surat_jalan', 'io.due_date')
                    ->where('inv_order_id', $id)
                    ->get();
        $data=array(
            'data'  => $query
        );
        // return $data;

        return view('pages.inv.project_req_dev.print_surat_jalan', $data);
    }
    public function getPaymentDetail($id) {
        $query=DB::table('inv_orders as io')
                    ->join('payments as p', 'io.id', '=', 'p.inv_order_id')
                    ->select('p.*')
                    ->where('p.inv_order_id', $id)
                    ->get();
        $data=array(
            'detail'  => $query, 
            'data'  => DB::table('inv_orders')->where('id', $id)->first()
        );
        return $data;
    }
    public function billPayment(Request $request){
        
        $inv_id=$request->input('inv_id');
        $total_all_payment=$request->input('total_all_payment');
        $total_payment=$request->input('total_payment');
        $type_payment=$request->input('type_payment');
        $atas_nama=$request->input('atas_nama');

        try
        {
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . 'inv/base/Payment']);
            $reqBody = [
                'headers' => $headers,
                'json' => [
                    'inv_order_id'    => $inv_id,
                    'amount'        => $total_payment,
                    'wop'           => $type_payment,
                    'name'          => $atas_nama
                ]
            ]; 
            $response = $client->request('POST', '', $reqBody); 
            $body = $response->getBody();
            $content = $body->getContents();
            $response_array = json_decode($content,TRUE);
        } catch(RequestException $exception) {
        } 
        if($total_payment == $total_all_payment){
            DB::table('inv_orders')->where('id', $inv_id)->update(['payment_status' => 1]);
        }
        return redirect('project_req_dev/trx_product');
    }
    public function listFrameProduct()
    {
        return view('pages.inv.project_req_dev.list_frame_product');
    }
    public function listTrackFrame(){
        $query=DB::table('dev_project_frames')
                    ->select('dev_project_frames.*', 'users.name', 'ir.no as inv_no', 'io.no as io_no')
                    ->join('inv_requests as ir', 'dev_project_frames.inv_request_id', 'ir.id')
                    ->join('install_orders as io', 'io.id', 'ir.install_order_id')
                    ->join('users', 'users.id', 'dev_project_frames.user_id')
                    ->get();

        $data=DataTables::of($query)
                    ->make(true); 
        return $data;
    }
    public function getTrackFrameDetail($id){
        $dev_project_frame_ds=DB::table('dev_project_frame_ds')
                    ->join('product_subs', 'product_subs.id', 'dev_project_frame_ds.product_sub_id')
                    ->join('products', 'products.id', 'product_subs.product_id')
                    ->join('kavlings', 'products.kavling_id', 'kavlings.id')
                    ->where('dev_project_frame_ds.dev_project_frame_id', $id)
                    ->select('product_subs.no', 'products.item', 'products.name', 'products.series', 'kavlings.name as type_kavling')
                    ->get();
        $dev_project_frame_workers=DB::table('dev_project_frame_workers as dvw')
                    ->where('dvw.dev_project_frame_id', $id)
                    ->join('dev_project_frame_material_workers as dvm', 'dvw.id', 'dvm.dev_project_frame_worker_id')
                    ->join('m_items as mi', 'mi.id', 'dvm.m_item_id')
                    ->join('m_units as mu', 'mu.id', 'dvm.m_unit_id')
                    ->select('dvw.*', 'mi.name as item_name', 'mu.name as unit_name', 'dvm.amount')
                    ->get();
        $data=array(
            'dt'    => $dev_project_frame_ds,
            'worker'    => $dev_project_frame_workers
        );
        return $data;
    }
    private function journalPenyerahan($data){
        $project_req_developments=DB::table('project_req_developments')->where('id', $data['project_req_development_id'])->first();
        // $account_project=DB::table('account_projects')->where('order_id', $project_req_developments->order_id)->first();
        $data_trx=array(
            'deskripsi'     => $data['deskripsi'],
            'location_id'     => $data['location_id'],
            'tanggal'       => $data['tgl'],
            'user_id'       => $data['user_id'],
            'project_req_development_id'   => $data['project_req_development_id']
        );
        $insert=DB::table('tbl_trx_akuntansi')->insert($data_trx);
        if ($insert) {
            $id_last=DB::table('tbl_trx_akuntansi')->max('id_trx_akun');
            $akun=array(
                'id_trx_akun'   => $id_last,
                'id_akun'       => 81,
                'jumlah'        => $data['total'],
                'tipe'          => "KREDIT",
                'keterangan'    => 'akun',
            );
            DB::table('tbl_trx_akuntansi_detail')->insert($akun);
            $lawan=array(
                'id_trx_akun'   => $id_last,
                'id_akun'       => 148,
                'jumlah'        => $data['total'],
                'tipe'          => "DEBIT",
                'keterangan'    => 'lawan',
            );
            DB::table('tbl_trx_akuntansi_detail')->insert($lawan);
        }
    }
    public function transferProductJson(){
        $query=DB::table('inv_orders')->where('type', 'TRX_PRODUCT')->get();
        $data=array(
            'data'  => $query
        );
        return $data;
    }
    public function returnProductList(){
        return view('pages.inv.project_req_dev.return_product_list');
    }
    public function returnProductForm(){
        return view('pages.inv.project_req_dev.return_product_form');
    }
    public function returnProductLabel(Request $request){
        $dev_project_label_id=$request->input('dev_project_label_id');
        $inv_id=$request->input('inv_id');
        $product_sub_id=$request->input('product_sub_id');
        $work_id=$request->input('inv_req_prod_id');
        $product_label=$request->input('label');
        $check_prod_sub_id=$request->input('check_prod_sub_id');
        // $storage_locations=$request->input('storage');
        // $kondisi=$request->input('kondisi');
        $inv=DB::table('inv_orders')->where('id', $inv_id)->first();
        $period_year = date('Y');
        $period_month = date('m');
        
        $get_rab=DB::table('rabs')->where('id', $inv->rab_id)->first();
        $inv_order_no = $this->generateTransactionNo('INV_ORD', $period_year, $period_month, $this->site_id );
        
        try
        {
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
 ];
$client = new Client(['base_uri' => $this->base_api_url . 'inv/base/InvOrder']);
            $reqBody = [
                'headers' => $headers,
                'json' => [
                    'no'          => $inv_order_no,
                    'order_id'    => $inv->order_id,
                    'project_id'    => $get_rab->project_id,
                    'rab_id'    => $inv->rab_id,
                    'site_id'       => $this->site_id,
                    'project_req_development_id' => $inv->project_req_development_id,
                    'is_entry'    => true,
                    'type'          => 'RETURN'
                ]
            ]; 
            
            $response = $client->request('POST', '', $reqBody); 
            $body = $response->getBody();
            $content = $body->getContents();
            $response_array = json_decode($content,TRUE);
            $inv_order=$response_array['data'];
        } catch(RequestException $exception) {
        } 


        foreach ($dev_project_label_id as $key => $value) {
            $cek=in_array($value, $check_prod_sub_id);
            if ($cek == true) {
                $product_sub=DB::table('product_subs')->where('id', $product_sub_id[$key])->first();
                try
                {
                    $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
 ];
$client = new Client(['base_uri' => $this->base_api_url . 'inv/base/InvOrderD']);
                    $reqBody = [
                        'headers' => $headers,
                'json' => [
                            'inv_order_id'    => $inv_order['id'],
                            'product_sub_id'    => $product_sub_id[$key],
                            'dev_project_label_id'  => $value,
                            'inv_request_prod_id'    => $work_id[$key],
                            'no'    => $product_label[$key],
                            'order_d_id'    => $product_sub->order_d_id,
                            'storage_locations' => '-',
                            'condition' => 'buruk', 
                            'is_entry'  => 1
                        ]
                    ]; 
                    $response = $client->request('POST', '', $reqBody);
                } catch(RequestException $exception) {
                }
            }
        }
        return redirect('project_req_dev/return_product');
    }
    public function jsonReturnProduct() {
        $query=DB::table('inv_orders')
                    ->join('rabs', 'rabs.id', '=', 'inv_orders.rab_id')
                    ->join('projects', 'projects.id', '=', 'inv_orders.project_id')
                    ->join('orders', 'orders.id', '=', 'inv_orders.order_id')
                    ->join('customers', 'orders.customer_id', '=', 'customers.id')
                    ->where('inv_orders.type', 'RETURN')
                    ->select('inv_orders.*', 'rabs.no as rab_no', 'orders.order_no', 'customers.coorporate_name', 'projects.name')
                    ->get();
        $data=DataTables::of($query)
                    ->make(true); 
        return $data;
    }
    public function resend($id) {
        $inv_order=DB::table('inv_orders')->where('id', $id)->first();
        $period_year = date('Y');
        $period_month = date('m');
        $inv_order_no = $this->generateTransactionNo('INV_ORD', $period_year, $period_month, $this->site_id );
        try
        {
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
 ];
$client = new Client(['base_uri' => $this->base_api_url . 'inv/base/InvOrder']);
            $reqBody = [
                'headers' => $headers,
                'json' => [
                    'no'          => $inv_order_no,
                    'order_id'    => $inv_order->order_id,
                    'project_id'    => $inv_order->project_id,
                    'rab_id'    => $inv_order->rab_id,
                    'site_id'       => $this->site_id,
                    'project_req_development_id' => $inv_order->project_req_development_id,
                    'is_entry'    => false,
                    'type'          => 'RESEND'
                ]
            ]; 
            
            $response = $client->request('POST', '', $reqBody); 
            $body = $response->getBody();
            $content = $body->getContents();
            $response_array = json_decode($content,TRUE);
            $inv_order=$response_array['data'];
        } catch(RequestException $exception) {
        } 
        $inv_order_ds=DB::table('inv_order_ds')->where('inv_order_id', $id)->get();
        foreach ($inv_order_ds as $key => $value) {
            try
            {
                $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
 ];
$client = new Client(['base_uri' => $this->base_api_url . 'inv/base/InvOrderD']);
                $reqBody = [
                    'headers' => $headers,
                'json' => [
                        'inv_order_id'    => $inv_order['id'],
                        'product_sub_id'    => $value->product_sub_id,
                        'inv_request_prod_id'    => $value->inv_request_prod_id,
                        'dev_project_label_id'  => $value->dev_project_label_id,
                        'no'    => $value->no,
                        'order_d_id'    => $value->order_d_id,
                        'storage_locations' => '-',
                        'condition' => '', 
                        'is_entry'  => 1
                    ]
                ]; 
                $response = $client->request('POST', '', $reqBody);
            } catch(RequestException $exception) {
            }
        }
        return redirect('project_req_dev/trx_product');
    }
}
