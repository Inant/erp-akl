<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\View;
use Exception;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Redirect;
use Carbon\Carbon;
use Yajra\DataTables\Facades\DataTables;
use DB;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    
    public function __construct()
    {
        $this->middleware('auth');
        //Authenticate page menu
        // $this->middleware(function ($request, $next) {
            $this->user_id = auth()->user()['id'];
            // $this->site_id = auth()->user()['site_id'];
            // $this->user_name = auth()->user()['name'];
            
        //     return $next($request);
        // });
        $this->base_api_url = env('API_URL');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
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
                    
            $response = $content;
            $customer=$response_array['data'];

        } catch(RequestException $exception) {
                    
        }    

        $data=array(
            'customer'  => $customer
        );
        return view('home', $data);
    }
    public function listMaterialRequest(){

            // left join dev_projects dp on ir.id = dp.inv_request_id
        $warehouse_id=auth()->user()['m_warehouse_id'];
        $datas = DB::select("
            select ir.*, r.no as rab_no, work_header, p.name as project_name, prq.no AS req_no, k.name as type_kavling, prq.total, dp.id as dp_id, dp.created_at as created_at
            from dev_projects dp
            join inv_requests ir on dp.inv_request_id=ir.id
            join projects p on dp.project_id = p.id
            join rabs r on ir.rab_id = r.id
            join kavlings k on k.id = r.kavling_id
            join project_req_developments prq on ir.project_req_development_id = prq.id
            where ir.req_type != 'RET_ITEM' and ir.req_type != 'REQ_ITEM_SP' and ir.inv_request_id is null ".($warehouse_id != null ? "and ir.id IN (select inv_request_id from inv_trxes join inv_trx_ds on inv_trxes.id=inv_trx_ds.inv_trx_id where inv_trxes.trx_type='REQ_ITEM' and inv_trx_ds.m_warehouse_id=".$warehouse_id." group by inv_requests.id)" : '')." ");

        foreach ($datas as $key => $value) {
            $dev_projects=DB::table('dev_projects')->select('is_done')->where('inv_request_id', $value->id)->first();
            $value->is_done=$dev_projects != null ? $dev_projects->is_done : false;
        }
        $data=DataTables::of($datas)
                            ->make(true);   
        
        return $data;
    }
    // public function listMaterialRequest(){

    //         // left join dev_projects dp on ir.id = dp.inv_request_id
    //     $warehouse_id=auth()->user()['m_warehouse_id'];
    //     $datas = DB::select("
    //         select ir.*, r.no as rab_no, pw.name AS work_header, p.name as project_name, pw.id AS project_work_id, prq.no AS req_no, k.name as type_kavling, pr.item, pr.name as pr_name, pr.series, pr.panjang, pr.lebar, prq.total, dp.id as dp_id
    //         from dev_projects dp
    //         join inv_requests ir on dp.inv_request_id=ir.id
    //         join project_works pw on dp.project_work_id = pw.id
    //         join projects p on dp.project_id = p.id
    //         join rabs r on ir.rab_id = r.id
    //         join kavlings k on k.id = r.kavling_id
    //         join products pr on pr.id = dp.product_id
    //         join project_req_developments prq on ir.project_req_development_id = prq.id
    //         where ir.req_type != 'RET_ITEM' and ir.req_type != 'REQ_ITEM_SP' and ir.inv_request_id is null ".($warehouse_id != null ? "and ir.id IN (select inv_request_id from inv_trxes join inv_trx_ds on inv_trxes.id=inv_trx_ds.inv_trx_id where inv_trxes.trx_type='REQ_ITEM' and inv_trx_ds.m_warehouse_id=".$warehouse_id." group by inv_requests.id)" : '')." ");

    //     foreach ($datas as $key => $value) {
    //         $dev_projects=DB::table('dev_projects')->select('is_done')->where('inv_request_id', $value->id)->first();
    //         $value->is_done=$dev_projects != null ? $dev_projects->is_done : false;
    //     }
    //     $data=DataTables::of($datas)
    //                         ->make(true);   
        
    //     return $data;
    // }
    public function getProject(Request $request){

        $inv_request_id=$request->input('inv_id');

        // $pws_id=$request->input('pws_id');
        $inv_requests=DB::table('inv_requests')
                            ->join('rabs', 'rabs.id', 'inv_requests.rab_id')
                            ->join('projects', 'projects.id', 'rabs.project_id')
                            ->join('project_req_developments', 'project_req_developments.id', 'inv_requests.project_req_development_id')
                            ->select('inv_requests.*', 'rabs.project_id', 'projects.name', 'project_req_developments.no as req_no')
                            ->where('inv_requests.id', $inv_request_id)->first();
        
        // $project_work_id=$request->input('pw_id');
        // $product_id=$request->input('product_id');
        $work_header=$request->input('work_header');
        $work_detail=$request->input('work_detail');
        $project_req_development_id=$inv_requests->project_req_development_id;
        
        // $dev_projects=DB::table('dev_projects')->where('project_work_id', $project_work_id)->first();
        $dev_projects=DB::table('dev_projects')->where('inv_request_id', $inv_request_id)->where('work_header', $work_header)->first();

        if ($dev_projects == null) {
            
            // $get_detail=DB::table('project_works')
            //                     ->select('project_works.*')
            //                     ->where('project_works.id', $project_work_id)
            //                     ->first();
        
            // $get_project_worksubs=DB::table('project_worksubs')->where('project_work_id', $get_detail->id)->first();
            
            try
            {
                $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . 'inv/base/DevProject']);
                $reqBody = [
                    'headers' => $headers,
                'json' => [
                        'project_id' => $inv_requests->project_id,
                        'rab_id' => $inv_requests->rab_id,
                        // 'project_work_id' => $project_work_id,
                        'inv_request_id' => $inv_request_id,
                        // 'pws_running' => $get_project_worksubs->id,
                        'is_done' => 0,
                        // 'product_id' => $product_id,
                        'project_req_development_id' => $project_req_development_id,
                        'work_header'   => $work_header
                    ]
                ]; 
                
                $response = $client->request('POST', '', $reqBody); 
                $body = $response->getBody();
                $content = $body->getContents();
                $response_array = json_decode($content,TRUE);
                $dev_projects=$response_array['data'];
            } catch(RequestException $exception) {
            }
            $dev_projects=DB::table('dev_projects')->where('inv_request_id', $inv_request_id)->where('work_header', $work_header)->first();
        }
        
        // $project_worksubs=DB::table('project_worksubs')
        //                         ->select('project_worksubs.*', 'project_works.name AS project_header')
        //                         ->join('project_works', 'project_works.id', '=', 'project_worksubs.project_work_id')
        //                         ->where('project_worksubs.id', $pws_id)->first();

        
        $dev_project_ds=DB::table('dev_project_ds')
                            ->where('dev_project_id', $dev_projects->id)
                            // ->where('project_worksub_id', $pws_id)
                            ->where('work_detail', $work_detail)
                            ->get();
        $product_sub=$this->getProdByInv($inv_request_id, $dev_projects->id, $project_req_development_id, $work_detail);
        
        $data = array(
            'dev_projects' => $dev_projects,
            'product_sub'   =>$product_sub,
            // 'project_worksubs' => $project_worksubs,
            'inv_requests' => $inv_requests,
            'dev_project_ds'    => $dev_project_ds,
            'work_detail'       => $work_detail,
            // 'products'       => DB::table('products')->select('products.*', 'kavlings.name as type_kavling')->join('kavlings', 'kavlings.id', 'products.kavling_id')->where('products.id', $product_id)->first()
        );

        return view('pages.material_request.create_work', $data);
    }
    private function getProdByInv($id, $dev_id, $req_id, $work_detail){
        $dev_d_id=DB::table('dev_project_ds')
                            ->where('dev_project_id', $dev_id)
                            ->where('work_detail', $work_detail)
                            ->pluck('id');
        $cek=DB::table('dev_project_labels')->where('dev_project_id', $dev_id)->whereIn('dev_project_d_id', $dev_d_id)->pluck('product_sub_id');
        $product_sub=DB::table('inv_request_prod_ds as ird')
                            ->join('product_subs as ps', 'ps.id', 'ird.product_sub_id')
                            ->join('products as p', 'ps.product_id', 'p.id')
                            ->select('ps.no', 'p.*', 'ps.id as pd_id')
                            ->where('ird.inv_request_id', $id)
                            ->whereNotIn('ps.id', $cek)
                            ->get();
        return $product_sub;
    }
    public function getListAccPengambilanBarangDetail($id){
        $detailAcc=DB::table('inv_trxes')
                        ->join('inv_trx_ds', 'inv_trx_ds.inv_trx_id', '=', 'inv_trxes.id')
                        ->where('inv_trxes.inv_request_id', $id)
                        ->where('inv_trxes.trx_type', 'REQ_ITEM')
                        ->select('inv_trx_ds.*')
                        ->get();
        foreach ($detailAcc as $key => $value) {
            $value->m_items=DB::table('m_items')->where('id', $value->m_item_id)->first();
            $value->m_units=DB::table('m_units')->where('id', $value->m_unit_id)->first();
        }

        $detailRestAcc=DB::table('inv_trxes')
                        ->join('inv_trx_rest_ds as d', 'd.inv_trx_id', '=', 'inv_trxes.id')
                        ->where('inv_trxes.inv_request_id', $id)
                        ->select(DB::raw('MAX(d.amount) as amount'), DB::raw('COUNT(d.amount) as total'), DB::raw('MAX(d.m_item_id) as m_item_id'), DB::raw('MAX(d.m_unit_id) as m_unit_id'))
                        ->groupBy(['d.m_item_id', 'd.amount'])
                        ->get();
        foreach ($detailRestAcc as $key => $value) {
            $value->m_items=DB::table('m_items')->where('id', $value->m_item_id)->first();
            $value->m_units=DB::table('m_units')->where('id', $value->m_unit_id)->first();
        }

        $getInvReq=DB::table('inv_requests')->where('id', $id)->first();
        $id_refered_prod_sub=$getInvReq->id;
        if ($getInvReq->inv_request_id != null) {
            $getInvReqId=DB::table('inv_requests')->where('id', $getInvReq->inv_request_id)->first();
            $id_refered_prod_sub=$getInvReqId->id;
        }
        $inv_prod_sub=DB::table('inv_request_prod_ds')
                            ->select('product_subs.*')
                            ->where('inv_request_id', $id_refered_prod_sub)
                            ->join('product_subs', 'product_subs.id', '=', 'inv_request_prod_ds.product_sub_id')
                            ->get();
        $data['data']=array(
            'detail'    => $detailAcc,
            'detail_rest'    => $detailRestAcc,
            'prod_sub'  => $inv_prod_sub
        );
        return $data;
    }
    public function suggestPW(Request $request){
        $warehouse_id=auth()->user()['m_warehouse_id'];
        if($request->has('q')){
            $key=$request->q;
            $query=DB::table('project_works')
                        ->select('project_works.*', 'projects.name AS project_name', 'rabs.no AS rab_no', 'inv_requests.no AS inv_no', 'inv_requests.id AS id', DB::raw('CONCAT(project_req_developments.no, \' / \',  inv_requests.no,  \' / (\', projects.name,   \')\') AS text'), 'inv_requests.req_type')
                        ->join('projects', 'projects.id', '=', 'project_works.project_id')
                        ->join('inv_requests', 'inv_requests.project_work_id', '=', 'project_works.id')
                        // ->join('inv_trxes', 'inv_requests.id', '=', 'inv_trxes.inv_request_id')
                        ->join('project_req_developments', 'project_req_developments.id', '=', 'inv_requests.project_req_development_id')
                        ->join('rabs', 'rabs.id', '=', 'project_works.rab_id')
                        ->where('project_works.name', 'ilike', '%'.$key.'%')
                        ->orWhere('projects.name', 'ilike', '%'.$key.'%')
                        ->orWhere('rabs.no', 'ilike', '%'.$key.'%')
                        ->orWhere('inv_requests.no', 'ilike', '%'.$key.'%')
                        ->orWhere('project_req_developments.no', 'like', '%'.$key.'%');
            $data=$query->limit(15)->get();

            foreach ($data as $key => $value) {
                $getDevProject=DB::table('dev_projects')->where('inv_request_id', $value->id)->first();
                if ($value->req_type != 'REQ_ITEM') {
                    unset($data[$key]);
                }else{
                    if ($warehouse_id != null) {
                        $cek=DB::select("select * from inv_trxes join inv_trx_ds on inv_trxes.id=inv_trx_ds.inv_trx_id where inv_trx_ds.m_warehouse_id=".$warehouse_id." and inv_trxes.inv_request_id=".$value->id."");
                        if (count($cek) > 0) {
                            if ($getDevProject != null) {
                                if ($getDevProject->is_done == true) {
                                    unset($data[$key]);
                                }
                            }
                        }else{
                            unset($data[$key]);
                        }
                    }else{
                        if ($getDevProject != null) {
                            if ($getDevProject->is_done == true) {
                                unset($data[$key]);
                            }
                        }
                    }
                }
            }
            // exit;
            // $data1=array();
            // foreach ($data as $key => $value) {
            //     $data1[]=array('id' => $value->inv_id, 'text' => $value->name);
            // }
            // echo json_encode($data1);
            return $data;
        }
    }
    public function runProjects($id){
        $detail=DB::table('dev_project_ds')
                            ->join('dev_projects', 'dev_projects.id', '=', 'dev_project_ds.dev_project_id')
                            ->where('dev_project_ds.id', $id)
                            ->select('dev_projects.*', 'dev_project_ds.work_detail', 'dev_project_ds.jumlah_pekerja', 'dev_project_ds.work_end', 'dev_project_ds.status', 'dev_project_ds.id as dev_d_id', 'dev_project_ds.notes')
                            ->first();
        $inv_request_id=$detail->inv_request_id;
        // $pws_id=$detail->project_worksub_id;
        $inv_requests=DB::table('inv_requests')
                            ->join('rabs', 'rabs.id', 'inv_requests.rab_id')
                            ->join('projects', 'projects.id', 'rabs.project_id')
                            ->join('project_req_developments', 'project_req_developments.id', 'inv_requests.project_req_development_id')
                            ->select('inv_requests.*', 'rabs.project_id', 'projects.name', 'project_req_developments.no as req_no')
                            ->where('inv_requests.id', $inv_request_id)->first();
        
        $project_work_id=$detail->project_work_id;
        $product_id=$detail->product_id;
        $project_req_development_id=$inv_requests->project_req_development_id;
        
        $dev_projects=DB::table('dev_projects')->where('id', $detail->id)->first();

        $getLastPw=DB::table('project_worksubs')
                            ->where('project_work_id', $dev_projects->project_work_id)
                            // ->where('project_worksubs.name', 'not ilike', '%pasang%')
                            ->max('id');
        $getAllPw=DB::table('project_worksubs')
                            ->where('project_work_id', $dev_projects->project_work_id)
                            // ->where('project_worksubs.name', 'not ilike', '%pasang%')
                            ->get();
        
        $is_next=false;
        $next_id=0;
        // $status='next';
        // if ($getLastPw == $pws_id) {
        //     $next_id=$getLastPw;
        //     $status='last';
        // }else{
        //     foreach ($getAllPw as $key => $value) {
        //         if ($value->id == $pws_id) {
        //             $is_next=true;
        //         }else{
        //             if ($is_next == true) {
        //                 $next_id=$value->id;
        //                 $status='next';
        //             }
        //         }
        //     }
        // }
        
        // $project_worksubs=DB::table('project_worksubs')
        //                         ->select('project_worksubs.*', 'project_works.name AS project_header')
        //                         ->join('project_works', 'project_works.id', '=', 'project_worksubs.project_work_id')
        //                         ->where('project_worksubs.id', $pws_id)->first();
        // $next_name=DB::table('project_worksubs')->where('id', $next_id)->first();
        $getLastDurationId=0;
        $getLastDurationId=DB::table('dev_project_d_durations')
                                ->where('dev_project_d_id', $id)
                                ->max('id');
        
        $data = array(
            'dev_projects' => $dev_projects,
            // 'project_worksubs' => $project_worksubs,
            'dev_project_ds' => $detail,
            'idLastDuration' => $getLastDurationId,
            'inv_requests' => $inv_requests,
            // 'products'       => DB::table('products')->select('products.*', 'kavlings.name as type_kavling')->join('kavlings', 'kavlings.id', 'products.kavling_id')->where('products.id', $product_id)->first(),
            // 'status'        => $status
        );
        // dd($data);
        return view('pages.material_request.run_project', $data);
    }
    public function saveDevRequestD(Request $request) {
        
        $dev_project_id = $request->post('dev_project_id');
        $project_worksub_id = $request->post('project_worksub_id');
        $total_worker = 0;
        $dev_project_ds=null;
        foreach ($request->input('repeater-group') as $key => $value) {
            if ($value['worker_name'] != null) {
                $total_worker++;
            }
        }
        try
        {
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . 'inv/base/DevProjectD']);
            $reqBody = [
                'headers' => $headers,
                'json' => [
                    'dev_project_id' => $dev_project_id,
                    'project_worksub_id' => $project_worksub_id,
                    'jumlah_pekerja' => $total_worker,
                    'work_start' => date('Y-m-d H:i:s'),
                    'work_end' => null,
                    'user_id'   => auth()->user()['id'],
                    'long_work' => 0
                ]
            ]; 
            
            $response = $client->request('POST', '', $reqBody); 
            $body = $response->getBody();
            $content = $body->getContents();
            $response_array = json_decode($content,TRUE);
            $dev_project_ds=$response_array['data'];
        } catch(RequestException $exception) {
        }

        try
        {
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . 'inv/base/DevProjectDDuration']);
            $reqBody = [
                'headers' => $headers,
                'json' => [
                    'dev_project_id' => $dev_project_id,
                    'dev_project_d_id' => $dev_project_ds['id'],
                    'work_start' => date('Y-m-d H:i:s'),
                    'work_end' => null,
                ]
            ]; 
            
            $response = $client->request('POST', '', $reqBody); 
            $body = $response->getBody();
            $content = $body->getContents();
            $response_array = json_decode($content,TRUE);
            $dev_project_duration=$response_array['data'];
        } catch(RequestException $exception) {
        }

        foreach ($request->input('repeater-group') as $key => $value) {        
            if ($value['worker_name'] != null) {
                try
                {
                    $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . 'inv/base/DevProjectWorker']);
                    $reqBody = [
                        'headers' => $headers,
                'json' => [
                            'dev_project_id' => $dev_project_id,
                            'dev_project_d_id' => $dev_project_ds['id'],
                            'project_worksub_id' => $project_worksub_id,
                            'name_worker' => $value['worker_name'],
                        ]
                    ]; 
                    
                    $response = $client->request('POST', '', $reqBody); 
                    $body = $response->getBody();
                    $content = $body->getContents();
                    $response_array = json_decode($content,TRUE);
                    
                } catch(RequestException $exception) {
                }
            }
        }
        
        if ($dev_project_ds != null) {
            $notification = array(
                'data'      => $dev_project_ds,
                'duration'      => $dev_project_duration,
                'message' => 'Success',
                'alert-type' => 'success'
            );
        }else{
            $notification = array(
                'data'      => null,
                'duration'      => null,
                'message' => 'error',
                'alert-type' => 'error'
            );
        }

        return $notification;
    }
    public function updateDevRequestD(Request $request) {
        $id = $request->post('id');
        $dev_project_id = $request->post('dev_project_id');
        // $pw_id = $request->post('pw_id');
        $work_header = $request->post('work_header');
        $id_duration = $request->post('id_duration');
        
        $dev_project_ds=null;
        $data=null;
        if ($request->input('status') != 'done') {
            $data=array(
                // 'long_work' => $request->input('long_work'),
                'status'    => $request->input('status')
            );
            if ($request->input('status') == 'pause') {
                try
                {
                    $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . 'inv/base/DevProjectDDuration/' . $id_duration]);
                    $reqBody = [
                        'headers' => $headers,
                'json' => array('work_end' => date('Y-m-d H:i:s'))
                    ]; 
                    
                    $response = $client->request('PUT', '', $reqBody); 
                    $body = $response->getBody();
                    $content = $body->getContents();
                    $response_array = json_decode($content,TRUE);
                    // $dev_project_ds=$response_array['data'];
                } catch(RequestException $exception) {
                }
            }else{
                try
                {
                    $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . 'inv/base/DevProjectDDuration']);
                    $reqBody = [
                        'headers' => $headers,
                'json' => [
                            'dev_project_id' => $request->input('dev_project_id'),
                            'dev_project_d_id' => $id,
                            'work_start' => date('Y-m-d H:i:s'),
                            'work_end' => null,
                        ]
                    ]; 
                    
                    $response = $client->request('POST', '', $reqBody); 
                    $body = $response->getBody();
                    $content = $body->getContents();
                    $response_array = json_decode($content,TRUE);
                    $dev_project_duration=$response_array['data'];
                } catch(RequestException $exception) {
                }
            }
        }else{
            $data=array(
                'work_end' => date('Y-m-d H:i:s'),
                // 'long_work' => $request->input('long_work'),
                'status'    => $request->input('status'),
                'is_done'   => 1
            );
            try
            {
                $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . 'inv/base/DevProjectDDuration/' . $id_duration]);
                $reqBody = [
                    'headers' => $headers,
                'json' => array('work_end' => date('Y-m-d H:i:s'))
                ]; 
                
                $response = $client->request('PUT', '', $reqBody); 
                $body = $response->getBody();
                $content = $body->getContents();
                $response_array = json_decode($content,TRUE);
                // $dev_project_ds=$response_array['data'];
            } catch(RequestException $exception) {
            }
        }
        try
        {
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . 'inv/base/DevProjectD/' . $id]);
            $reqBody = [
                'headers' => $headers,
                'json' => $data
            ]; 
            
            $response = $client->request('PUT', '', $reqBody); 
            $body = $response->getBody();
            $content = $body->getContents();
            $response_array = json_decode($content,TRUE);
            // $dev_project_ds=$response_array['data'];
        } catch(RequestException $exception) {
        }

        try
        {
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . 'inv/base/DevProjectD/' . $id]);    
            $response = $client->request('GET', '', ['headers' => $headers]); 
            $body = $response->getBody();
            $content = $body->getContents();
            $response_array = json_decode($content,TRUE);
            $dev_project_ds=$response_array['data'];
        } catch(RequestException $exception) {
        }
        
        $getLastDurationId=DB::table('dev_project_d_durations')
                                    ->where('dev_project_d_id', $id)
                                    ->max('id');

        // $query=DB::table('project_worksubs')->where('project_worksubs.name', 'not ilike', '%pasang%')->where('project_work_id', $pw_id)->pluck('id');
        // $count_dev_project=DB::table('dev_project_ds')->where('dev_project_id', $dev_project_id)->select(DB::raw('COUNT(id) AS total'))->whereIn('project_worksub_id', $query)->where('is_done', 1)->first();//menghitung jumlah project_worksub yang ada
        
        // if (count($query) == $count_dev_project->total) {//jika project_worksub sudah diselesaikan semua
        //     $update_dev_project=DB::table('dev_projects')->where('id', $dev_project_id)->update(['is_done' => 1]);//maka pekerjaan itu status diupdate selesai
        //     $dev_projects=DB::table('dev_projects')->where('id', $dev_project_id)->first();
        //     $project_works=DB::table('rabs')->select('project_works.*')->where('project_works.name', 'not ilike', '%pasang%')->join('project_works', 'rabs.id', '=', 'project_works.rab_id')->where('rabs.id', $dev_projects->rab_id)->pluck('id');
        //     $dev_projects_count=DB::table('dev_projects')->select(DB::raw('COUNT(id) AS total'))->whereIn('project_work_id', $project_works)->where('is_done', 1)->first();//menghitung jumlah project_work yang selesai
            
        //     // if ($dev_projects_count->total == count($project_works)) {//jika semua project_work selesai semua, maka status rab itu diupdate menjadi selesai
        //     //     $update_request=DB::table('project_req_developments')->where('id', $dev_projects->project_req_development_id)->update(['status' => 1, 'work_end' => date('Y-m-d H:i:s')]);
        //     // }
        // }
        
        if ($dev_project_ds != null) {
            $notification = array(
                'data'      => $dev_project_ds,
                'last_id'      => $getLastDurationId,
                'message' => 'Success',
                'alert-type' => 'success'
            );
        }else{
            $notification = array(
                'data'      => null,
                'last_id'      => $getLastDurationId,
                'message' => 'error',
                'alert-type' => 'error'
            );
        }

        return $notification;
    }
    public function getWorkerList($id){

            // left join dev_projects dp on ir.id = dp.inv_request_id
        $datas = DB::table('dev_project_workers')->where('dev_project_d_id', $id)->get();

        $data=DataTables::of($datas)
                            ->make(true);   
        
        return $data;
    }
    public function getDurationList($id){

        // left join dev_projects dp on ir.id = dp.inv_request_id
        $datas = DB::table('dev_project_d_durations')
                        ->where('dev_project_d_id', $id)
                        ->orderBy('created_at', 'ASC')
                        ->get();

        $data=DataTables::of($datas)
                            ->make(true);   
        
        return $data;
    }
    public function getReportWorker($id){

        $datas = DB::table('dev_projects')
                    ->where('dev_projects.id', $id)
                    ->select('dev_projects.*', 'work_header', 'kavlings.name as type_kavling', 'prd.total', 'prd.no as prd_no')
                    // ->join('project_works', 'project_works.id', '=', 'dev_projects.project_work_id')
                    // ->join('products', 'dev_projects.product_id', '=', 'products.id')
                    ->join('rabs', 'dev_projects.rab_id', '=', 'rabs.id')
                    ->join('kavlings', 'kavlings.id', '=', 'rabs.kavling_id')
                    ->join('project_req_developments as prd', 'prd.id', '=', 'dev_projects.project_req_development_id')
                    ->get();
                    // dd($datas);
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
        
        return view('pages.material_request.report_project', $data);
    }

    public function getProjectWorkSub($id){
        $datas['data'] = DB::table('project_worksubs')
                    ->where('project_work_id', $id)
                    // ->where('project_worksubs.name', 'not ilike', '%pasang%')
                    ->select('project_worksubs.*')
                    ->get();
        return $datas;
    }
    public function listFrameProduct()
    {
        return view('pages.material_request.list_frame_product');
    }
    // public function suggestPWDone(Request $request){
    //     $warehouse_id=auth()->user()['m_warehouse_id'];
    //     if($request->has('q')){
    //         $key=$request->q;
    //         $query=DB::table('project_req_developments')
    //                     ->select('project_req_developments.id as prd_id', 'project_req_developments.no AS req_no', 'products.name', 'products.series', 'products.item', 'products.panjang', 'products.lebar', 'inv_requests.no as inv_no', 'inv_requests.req_type as req_type', 'inv_requests.id as id', DB::raw('CONCAT(project_req_developments.no, \' / \',  inv_requests.no,  \' / (\', products.item, \' \', products.name, \' \', products.series, \')\') as text'))
    //                     ->join('rabs', 'project_req_developments.rab_id', 'rabs.id')
    //                     ->join('order_ds', 'rabs.order_d_id', 'order_ds.id')
    //                     ->join('products', 'products.id', 'order_ds.product_id')
    //                     ->join('inv_requests', 'inv_requests.project_req_development_id', '=', 'project_req_developments.id')
    //                     ->join('inv_trxes', 'inv_requests.id', '=', 'inv_trxes.inv_request_id')
    //                     ->where('project_req_developments.no', 'like', '%'.$key.'%')
    //                     ->orWhere('inv_requests.no', 'like', '%'.$key.'%')
    //                     ->orWhere('products.name', 'ilike', '%'.$key.'%');
    //         $data=$query->limit(15)->get();

    //         foreach ($data as $key => $value) {
    //             if ($value->req_type != 'REQ_ITEM_SP') {
    //                 unset($data[$key]);
    //             }
    //         }
           
    //         return $data;
    //     }
    // }
    public function suggestPWDone(Request $request){
        $warehouse_id=auth()->user()['m_warehouse_id'];
        if($request->has('q')){
            $key=$request->q;
            $query=DB::table('inv_requests')
                        ->select('inv_requests.no as inv_no', 'inv_requests.req_type as req_type', 'inv_requests.id as id', 'io.no as no_order_install', DB::raw('CONCAT(io.no, \' / \',  inv_requests.no) as text'))
                        ->join('install_orders as io', 'io.id', 'inv_requests.install_order_id')
                        ->join('inv_trxes', 'inv_requests.id', '=', 'inv_trxes.inv_request_id')
                        ->orWhere('inv_requests.no', 'like', '%'.$key.'%')
                        ->orWhere('io.no', 'ilike', '%'.$key.'%');
            $data=$query->limit(15)->get();

            foreach ($data as $key => $value) {
                if ($value->req_type != 'REQ_ITEM_SP') {
                    unset($data[$key]);
                }
            }
           
            return $data;
        }
    }
    public function trackFrameForm(Request $request) {
        $inv_request_id=$request->input('inv_id');
        $inv_requests=DB::table('inv_requests as ir')
                        ->select('ird.m_item_id', 'ird.m_unit_id', 'ird.amount', 'ir.*')
                        ->join('inv_request_ds as ird', 'ir.id', 'ird.inv_request_id')
                        ->where('ir.id', $inv_request_id)
                        ->first();
        
        $dev_frames=DB::table('dev_project_frames')->where('inv_request_id', $inv_request_id)->first();

        if ($dev_frames == null) {
            try
            {
                $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . 'inv/base/DevProjectFrame']);
                $reqBody = [
                    'headers' => $headers,
                'json' => [
                        'date_frame'    => date('Y-m-d'),
                        // 'project_req_development_id'    => $inv_requests->project_req_development_id,
                        'inv_request_id'    => $inv_request_id,
                        'user_id'   => auth()->user()['id'],
                    ]
                ]; 
                
                $response = $client->request('POST', '', $reqBody); 
                $body = $response->getBody();
                $content = $body->getContents();
                $response_array = json_decode($content,TRUE);
                $dev_project_frame=$response_array['data'];
            } catch(RequestException $exception) {
            }
            $dev_frames=DB::table('dev_project_frames')->where('inv_request_id', $inv_request_id)->first();
        }
        
        $product_sub=DB::table('dev_project_frames')
                        ->where('inv_request_id', $inv_request_id)
                        ->join('dev_project_frame_ds', 'dev_project_frame_ds.dev_project_frame_id', 'dev_project_frames.id')
                        ->pluck('product_sub_id');
        $query=DB::table('inv_requests')
                    ->select('product_sub_id', 'product_subs.no', 'products.id', 'inv_requests.install_order_id', 'products.item', 'products.name', 'products.series', 'kavlings.name as type_kavling')
                    ->join('inv_request_prod_installs as irpd', 'irpd.inv_request_id', 'inv_requests.id')
                    ->join('product_subs', 'product_subs.id', 'irpd.product_sub_id')
                    ->join('products', 'products.id', 'product_subs.product_id')
                    ->join('kavlings', 'products.kavling_id', 'kavlings.id')
                    ->where('inv_requests.id', $inv_request_id)
                    ->whereNotIn('irpd.product_sub_id', $product_sub)
                    ->get();

        $frame_worker=DB::table('dev_project_frame_workers')->where('dev_project_frame_id', $dev_frames->id)->count();
        $frame_material_worker=DB::table('dev_project_frame_material_workers')->where('dev_project_frame_id', $dev_frames->id)->count();

        $data = array(
            'dev_frames' => $dev_frames,
            'label' => $query,
            'inv_requests'  => $inv_requests,
            'inv_id'    => $inv_request_id,
            'frame_worker'      => $frame_worker,
            'frame_material_worker' => $frame_material_worker,
        );
        // dd($data);
        // exit;
        // return $data;
        return view('pages.material_request.track_frame_form', $data);
    }
    public function getItemFrame($id){
        $inv_requests=DB::table('inv_requests as ir')
                        ->select('ird.m_item_id', DB::raw('MAX(ird.m_unit_id) as m_unit_id'), DB::raw('MAX(ird.amount) as amount'))
                        ->join('inv_request_ds as ird', 'ir.id', 'ird.inv_request_id')
                        ->where('ir.id', $id)
                        ->groupBy('m_item_id')
                        ->get();
        foreach ($inv_requests as $key => $value) {
            $value->m_items=DB::table('m_items')->where('id', $value->m_item_id)->first();
            $value->m_units=DB::table('m_units')->where('id', $value->m_unit_id)->first();
        }
        $data=array(
            'data'  => $inv_requests
        );
        return $data;
    }
    public function saveTrackFrame(Request $request){
        $product_sub=$request->input('prod_id');
        $dev_id=$request->input('dev_id');
        foreach ($product_sub as $key => $value) {
            try
            {
                $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . 'inv/base/DevProjectFrameD']);
                $reqBody = [
                    'headers' => $headers,
                'json' => [
                        'dev_project_frame_id'    => $dev_id,
                        'product_sub_id' => $value
                    ]
                ]; 
                
                $response = $client->request('POST', '', $reqBody); 
                $body = $response->getBody();
                $content = $body->getContents();
                $response_array = json_decode($content,TRUE);
            } catch(RequestException $exception) {
            }
        }
        return redirect('material_request/list_frame');
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
        $dev_project_frame_workers=DB::table('dev_project_frame_workers')
                    ->where('dev_project_frame_id', $id)
                    ->get();
        $data=array(
            'dt'    => $dev_project_frame_ds,
            'worker'    => $dev_project_frame_workers
        );
        return $data;
    }
    public function saveFrameWorker(Request $request){
        $id=$request->input('dev_id');
        foreach ($request->input('repeater-group') as $key => $value) {        
            if ($value['worker_name'] != null) {
                try
                {
                    $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . 'inv/base/DevProjectFrameWorker']);
                    $reqBody = [
                        'headers' => $headers,
                'json' => [
                            'dev_project_frame_id'    => $id,
                            'name_worker' => $value['worker_name'],
                        ]
                    ]; 
                    
                    $response = $client->request('POST', '', $reqBody); 
                    $body = $response->getBody();
                    $content = $body->getContents();
                    $response_array = json_decode($content,TRUE);
                    
                } catch(RequestException $exception) {
                }
            }
        }
        return true;
    }
    public function saveFrameMaterialWorker(Request $request){
        $id=$request->input('dev_id');
        $worker_id=$request->input('worker_id');
        $m_item_id=$request->input('m_item_id');
        $m_unit_id=$request->input('m_unit_id');
        $amount=$request->input('amount');
        
        for ($i=0; $i < count($worker_id); $i++) {        
            try
            {
                $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . 'inv/base/DevProjectFrameMaterialWorker']);
                $reqBody = [
                    'headers' => $headers,
                'json' => [
                        'dev_project_frame_id'    => $id,
                        'dev_project_frame_worker_id'    => $worker_id[$i],
                        'm_item_id'    => $m_item_id[$i],
                        'm_unit_id'    => $m_unit_id[$i],
                        'amount'    => $amount[$i],
                    ]
                ]; 
                
                $response = $client->request('POST', '', $reqBody); 
                $body = $response->getBody();
                $content = $body->getContents();
                $response_array = json_decode($content,TRUE);
                
            } catch(RequestException $exception) {
            }
        }
        return true;
    }
    public function getTrackFrameMaterialDetail($id){
        $dev_project_frame_ds=DB::table('dev_project_frame_workers')
                    ->join('dev_project_frame_material_workers', 'dev_project_frame_workers.id', 'dev_project_frame_material_workers.dev_project_frame_worker_id')
                    ->where('dev_project_frame_workers.dev_project_frame_id', $id)
                    ->get();
        foreach ($dev_project_frame_ds as $key => $value) {
            $value->m_items=DB::table('m_items')->where('id', $value->m_item_id)->first();
            $value->m_units=DB::table('m_units')->where('id', $value->m_unit_id)->first();
        }
        $data=array(
            'data'    => $dev_project_frame_ds,
        );
        return $data;
    }

    public function suggestProject($id){
        $warehouse_id=auth()->user()['m_warehouse_id'];
        $cek_request=DB::table('orders')
                        ->join('project_req_developments as prd', 'prd.order_id', 'orders.id')
                        ->where('customer_id', $id)
                        ->pluck('prd.id');
        
        $query=DB::table('inv_requests')
                    ->join('rabs', 'rabs.id', '=', 'inv_requests.rab_id')
                    ->join('project_req_developments', 'project_req_developments.id', '=', 'inv_requests.project_req_development_id')            
                    ->join('projects', 'projects.id', '=', 'rabs.project_id')
                    ->join('kavlings', 'kavlings.id', 'rabs.kavling_id')
                    ->select('rabs.no AS rab_no', 'inv_requests.no AS inv_no', 'inv_requests.id AS id', DB::raw('CONCAT(project_req_developments.no, \' / \',  inv_requests.no,  \' / (\', projects.name,   \')\') AS text'), 'inv_requests.req_type', 'kavlings.name as type_kavling', 'project_req_developments.total', 'project_req_developments.no as req_no')
                    ->whereNull('inv_requests.inv_request_id')
                    ->where('inv_requests.req_type', 'REQ_ITEM')                
                    ->whereIn('project_req_developments.id', $cek_request);
        $list=$query->get();
        foreach ($list as $key => $value) {
            $getDevProject=DB::table('dev_projects')->where('inv_request_id', $value->id)->first();
            if ($value->req_type != 'REQ_ITEM') {
                unset($list[$key]);
            }else{
                if ($warehouse_id != null) {
                    $cek=DB::select("select * from inv_trxes join inv_trx_ds on inv_trxes.id=inv_trx_ds.inv_trx_id where inv_trx_ds.m_warehouse_id=".$warehouse_id." and inv_trxes.inv_request_id=".$value->id."");
                    if (count($cek) > 0) {
                        if ($getDevProject != null) {
                            if ($getDevProject->is_done == true) {
                                unset($list[$key]);
                            }
                        }
                    }else{
                        unset($list[$key]);
                    }
                }else{
                    if ($getDevProject != null) {
                        if ($getDevProject->is_done == true) {
                            unset($list[$key]);
                        }
                    }
                }
            }
        }
        $data=array(
            'data'  => $list
        );
        return $data;
    }
    public function suggestProjectWorks($id){
        $query=DB::table('inv_requests')
                    ->join('rabs', 'rabs.id', '=', 'inv_requests.rab_id')
                    ->join('project_works', 'project_works.rab_id', '=', 'rabs.id')
                    ->join('products', 'project_works.product_id', '=', 'products.id')
                    ->select('project_works.*', 'products.item', 'products.series', 'products.id as product_id')
                    // ->where('project_works.name', 'not ilike', '%pasang%')
                    ->where('inv_requests.id', $id);
        $list=$query->get();
        $data=array(
            'data'  => $list
        );
        return $data;
    }
    public function saveWorkD(Request $request) {
        $dev_project_id = $request->post('dev_project_id');
        // $project_worksub_id = $request->post('project_worksub_id');
        $work_detail = $request->post('work_detail');
        $worker = $request->post('worker');
        $product_sub_id = $request->post('product_sub_id');
        $total_worker = count($worker);
        $dev_project_ds=null;
        try
        {
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . 'inv/base/DevProjectD']);
            $reqBody = [
                'headers' => $headers,
                'json' => [
                    'dev_project_id' => $dev_project_id,
                    // 'project_worksub_id' => $project_worksub_id,
                    'jumlah_pekerja' => $total_worker,
                    'notes' => $request->input('notes'),
                    'work_start' => date('Y-m-d H:i:s'),
                    'work_end' => null,
                    'user_id'   => auth()->user()['id'],
                    'work_detail'   => $work_detail,
                    'long_work' => 0
                ]
            ]; 
            
            $response = $client->request('POST', '', $reqBody); 
            $body = $response->getBody();
            $content = $body->getContents();
            $response_array = json_decode($content,TRUE);
            $dev_project_ds=$response_array['data'];
        } catch(RequestException $exception) {
        }

        try
        {
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . 'inv/base/DevProjectDDuration']);
            $reqBody = [
                'headers' => $headers,
                'json' => [
                    'dev_project_id' => $dev_project_id,
                    'dev_project_d_id' => $dev_project_ds['id'],
                    'work_start' => date('Y-m-d H:i:s'),
                    'work_end' => null,
                ]
            ]; 
            
            $response = $client->request('POST', '', $reqBody); 
            $body = $response->getBody();
            $content = $body->getContents();
            $response_array = json_decode($content,TRUE);
            $dev_project_duration=$response_array['data'];
        } catch(RequestException $exception) {
        }

        foreach ($worker as $key => $value) {   
            try
            {
                $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . 'inv/base/DevProjectWorker']);
                $reqBody = [
                    'headers' => $headers,
                'json' => [
                        'dev_project_id' => $dev_project_id,
                        'dev_project_d_id' => $dev_project_ds['id'],
                        // 'project_worksub_id' => $project_worksub_id,
                        'name_worker' => $value,
                    ]
                ]; 
                
                $response = $client->request('POST', '', $reqBody); 
                $body = $response->getBody();
                $content = $body->getContents();
                $response_array = json_decode($content,TRUE);
                
            } catch(RequestException $exception) {
            }
        }
        foreach ($product_sub_id as $key => $value) {   
            try
            {
                $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . 'inv/base/DevProjectLabel']);
                $reqBody = [
                    'headers' => $headers,
                'json' => [
                        'dev_project_id' => $dev_project_id,
                        'dev_project_d_id' => $dev_project_ds['id'],
                        'product_sub_id' => $value,
                    ]
                ]; 
                
                $response = $client->request('POST', '', $reqBody); 
                $body = $response->getBody();
                $content = $body->getContents();
                $response_array = json_decode($content,TRUE);
                
            } catch(RequestException $exception) {
            }
        }
        return redirect('material_request/run_project/'.$dev_project_ds['id']);
    }
    public function getLabel($id){
        // left join dev_projects dp on ir.id = dp.inv_request_id
        $datas = DB::table('dev_project_labels')
                        ->join('product_subs', 'product_subs.id', 'dev_project_labels.product_sub_id')
                        ->select('product_subs.*')
                        ->where('dev_project_d_id', $id)
                        ->get();

        $data=DataTables::of($datas)
                            ->make(true);   
        
        return $data;
    }
    public function closeWork($id){
        try
        {
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . 'inv/base/DevProject/'.$id]);
            $reqBody = [
                'headers' => $headers,
                'json' => [
                    'is_done' => 1,
                ]
            ]; 
            
            $response = $client->request('PUT', '', $reqBody); 
            $body = $response->getBody();
            $content = $body->getContents();
            $response_array = json_decode($content,TRUE);
        } catch(RequestException $exception) {
        }
        return redirect('home');
    }
    public function getWorkSub(Request $request){
        $dev_frame_id=$request->dev_frame_id;
        $install_order_id=$request->install_order_id;
        $id=$request->id;
        $product_sub_id=$request->product_sub_id;
        $query=DB::table('install_worksubs')
                        ->join('worksubs', 'worksubs.id', 'install_worksubs.worksub_id')
                        ->where('product_id', $id)
                        ->where('install_order_id', $install_order_id)
                        ->select('install_worksubs.*', 'worksubs.name', DB::raw('COALESCE((select COUNT(id) from dev_project_frame_worksubs where product_id='.$id.' and product_sub_id='.$product_sub_id.' and dev_project_frame_id='.$dev_frame_id.' and worksub_id=worksubs.id), 0) as cek'))
                        ->get();
        $data=array(
            'data'  => $query
        );
        return $data;
    }
    public function saveWorksub(Request $request){
        $dev_frame_id=$request->dev_frame_id;
        $product_sub_id=$request->product_sub_id;
        $product_id=$request->product_id;
        $worksub_id=$request->worksub_id;
        $status=$request->status;
        $cek=DB::table('dev_project_frame_worksubs')
                    ->where('product_id', $product_id)
                    ->where('product_sub_id', $product_sub_id)
                    ->where('worksub_id', $worksub_id)
                    ->where('dev_project_frame_id', $dev_frame_id)
                    ->first();
        if ($cek == null) {
            try
            {
                $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . 'inv/base/DevProjectFrameWorksub']);
                $reqBody = [
                    'headers' => $headers,
                'json' => [
                        'dev_project_frame_id' => $dev_frame_id,
                        'product_id' => $product_id,
                        'product_sub_id' => $product_sub_id,
                        'worksub_id' => $worksub_id
                    ]
                ]; 
                $response = $client->request('POST', '', $reqBody);
                $body = $response->getBody();
                $content = $body->getContents();
                $response_array = json_decode($content,TRUE); 
            } catch(RequestException $exception) {
            }
        }else{
            DB::table('dev_project_frame_worksubs')->where('id', $cek->id)->delete();
        }
    }
    public function getDetailProgress($id){
        $data=DB::table('dev_project_ds')->where('dev_project_id', $id)->groupBy('work_detail')->select('work_detail')->get();
        return $data;
    }
    public function addWorker(Request $request){
        try
        {
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . 'inv/base/DevProjectWorker']);
            $reqBody = [
                'headers' => $headers,
                'json' => [
                    'dev_project_id' => $request->dev_project_id,
                    'dev_project_d_id' => $request->dev_project_d_id,
                    'name_worker' => $request->worker_name,
                    'notes' => $request->notes,
                ]
            ]; 
            
            $response = $client->request('POST', '', $reqBody); 
            $body = $response->getBody();
            $content = $body->getContents();
            $response_array = json_decode($content,TRUE);
            
        } catch(RequestException $exception) {
        }
        return $response_array['data'];
    }
    public function editWorker(Request $request){
        
        try
        {
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . 'inv/base/DevProjectWorker/'.$request->dev_project_worker_id]);
            $reqBody = [
                'headers' => $headers,
                'json' => [
                    'notes' => $request->notes,
                ]
            ]; 
            
            $response = $client->request('PUT', '', $reqBody); 
            $body = $response->getBody();
            $content = $body->getContents();
            $response_array = json_decode($content,TRUE);
            
        } catch(RequestException $exception) {
        }
        $dt=DB::table('dev_project_workers')->where('id', $request->dev_project_worker_id)->first();
        $data=array(
            'data'=> $dt
        );
        return $data;
    }
}
