<?php
namespace App\Http\Controllers\CRM;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Exception;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Redirect;
use App\Http\Controllers\RAB\RabController;
use App\Http\Controllers\Accounting\AkuntanController;
use Carbon\Carbon;
use DB;
use App\Exports\DebtExport;
use Maatwebsite\Excel\Facades\Excel;

class PaymentController extends Controller
{
    private $base_api_url;
    private $site_id = null;
    private $username = null;
    public function __construct()
    {
        //Authenticate page menu
        $this->middleware(function ($request, $next) {
            Controller::isLogin(auth()->user()['role_id']);
            $this->site_id = auth()->user()['site_id']; 
            $this->username = auth()->user()['email'];
            $this->user_id  = auth()->user()['id'];
            return $next($request);
        });

        $this->base_api_url = env('API_URL');
    }

    public function index()
    {
        $is_error = false;
        $error_message = '';  

        $data = array(
            'error' => array(
                'is_error' => $is_error,
                'error_message' => $error_message
            )
        );
        
        return view('pages.crm.payment.payment_list', $data);
    }
    
    public function createPaymentProject()
    {
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
        $list_bank=DB::table('list_bank')->get();
        $data=array(
            'order_list'     => $order_list,
            'list_bank' => $list_bank
        );
        return view('pages.crm.payment.create_payment_project', $data);
    }
    
    public function savePaymentProject(Request $request){
        $period_year = date('Y');
        $period_month = date('m');
        $rabcon = new RabController();
        $bill_no = $rabcon->generateTransactionNo($request->input('trx_type'), $period_year, $period_month, $this->site_id );
        $payment=array();
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
                    'project_req_development_id' => $request->input('req_id'),
                    'order_id' => $request->input('order_id'),
                    'payment_type' => $request->input('trx_type'),
                    'wop' => $request->input('wop'),
                    'bank_number' => $request->input('bank_number'),
                    'atas_nama' => $request->input('atas_nama'),
                    'ref_code' => $request->input('ref_code'),
                    'id_bank' => $request->input('id_bank'),
                    'amount' => $this->currency($request->input('total')),
                    'description' => $request->input('description'),
                    'pay_date' => $request->input('pay_date'),
                    'no'  => $bill_no,
                    'is_out_source' => $request->input('is_out_source'),
                    'is_production' => $request->input('trx_type') == 'PAY_PROD' ? 1 : 0,
                    'site_id'   => $this->site_id
                ]
            ]; 
            
            $response = $client->request('POST', '', $reqBody); 
            $body = $response->getBody();
            $content = $body->getContents();
            $response_array = json_decode($content,TRUE);
            $payment=$response_array['data'];
        } catch(RequestException $exception) {
        }
        $project_dev=DB::table('project_req_developments')->where('id', $request->input('req_id'))->first();
        $account_project=DB::table('account_projects')->where('order_id', $request->input('order_id'))->first();
        $input_jurnal=array(
            'payment_id'    => $payment['id'],
            'payment_per_week_id'   => null,
            'payment_per_week_d_id'    => null,
            'payment_cost_other_id'     => null,
            'project_req_development_id' => $request->input('req_id'),
            'order_id' => $request->input('order_id'),
            'total' => $this->currency($request->input('total')),
            'user_id'   => $this->user_id,
            'akun'      => $request->input('account_payment'),
            'lawan'      => $account_project->cost_service_id,
            'deskripsi'     => 'Pembayaran SDM '.($request->input('is_out_source') ? 'Out Sourcing' : '').' '.($request->input('trx_type') == 'PAY_PROD' ? 'Produksi' : '').' dari No Permintaan '.$project_dev->no,
            'tgl'       => date('Y-m-d'),
            'location_id'   => $this->site_id
        );
        $this->journalPayment($input_jurnal);

        return redirect('/payment')->with('notification');
    }
    public function GetSDMPaymentJson(Request $request) {
        $tipe=$request->has('tipe');
        $query=DB::table('payments')
                    ->join('orders as o', 'o.id', 'payments.order_id')
                    ->join('project_req_developments as prd', 'prd.id', 'payments.project_req_development_id')
                    ->select('o.order_no', 'payments.*', 'prd.no as req_no', 'payments.no as paid_no')
                    ->whereNull('payments.deleted_at');
        if ($tipe == 'charge') {
            $query->where('payment_type', 'PAY_CHARGE');
        }else{
            $query->whereIn('payment_type', ['PAY_PROD', 'PAY_FRAME']);
        }
        $query->get();
        $data=DataTables::of($query)
                                ->addColumn('action', function ($row) {
                                    return '<button type="button" id="modal_detail" class="btn btn-success btn-sm" data-toggle="modal" data-order_no="'.$row->paid_no.'" data-id="'.$row->id.'" data-target=".bs-example-modal-lg" onclick="getDetail(this)"><i class="mdi mdi-eye"></i></button>'.'
                                    
                                    '.'<a hidden href="'.url('/payment/delete/'.$row->id).'" class="btn btn-danger btn-sm" onclick="javasciprt: return confirm(\'Are You Sure ?\')"><i class="mdi mdi-delete"></i></a>';
                                    // '.'<a href="/order/edit/'.$row['id'].'" class="btn btn-info btn-sm"><i class="mdi mdi-pencil"></i></a>'.'
                                })
                                ->rawColumns(['order_name', 'action'])
                                ->make(true);          
    
        return $data;
    }
    public function indexPaymentOther()
    {
        $is_error = false;
        $error_message = '';  

        $data = array(
            'error' => array(
                'is_error' => $is_error,
                'error_message' => $error_message
            )
        );
        
        return view('pages.crm.payment.payment_other_list', $data);
    }
    public function createPaymentCost()
    {
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
        $list_bank=DB::table('list_bank')->get();
        $data=array(
            'order_list'     => $order_list,
            'list_bank' => $list_bank
        );
        return view('pages.crm.payment.create_payment_cost', $data);
    }
    public function savePaymentCost(Request $request){
        $period_year = date('Y');
        $period_month = date('m');
        $rabcon = new RabController();
        $bill_no = $rabcon->generateTransactionNo('PAY_CHARGE', $period_year, $period_month, $this->site_id );
        $payment=array();
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
                    'project_req_development_id' => $request->input('req_id'),
                    'order_id' => $request->input('order_id'),
                    'payment_type' => 'PAY_CHARGE',
                    'wop' => $request->input('wop'),
                    'bank_number' => $request->input('bank_number'),
                    'atas_nama' => $request->input('atas_nama'),
                    'ref_code' => $request->input('ref_code'),
                    'id_bank' => $request->input('id_bank'),
                    'amount' => $this->currency($request->input('total')),
                    'description' => $request->input('description'),
                    'pay_date' => $request->input('pay_date'),
                    'no'  => $bill_no,
                    'is_production' => $request->input('is_production'),
                    'site_id'   => $this->site_id
                ]
            ]; 
            
            $response = $client->request('POST', '', $reqBody); 
            $body = $response->getBody();
            $content = $body->getContents();
            $response_array = json_decode($content,TRUE);
            $payment=$response_array['data'];
        } catch(RequestException $exception) {
        }
        
        $project_dev=DB::table('project_req_developments')->where('id', $request->input('req_id'))->first();
        $account_project=DB::table('account_projects')->where('order_id', $request->input('order_id'))->first();
        $input_jurnal=array(
            'payment_id'    => $payment['id'],
            'payment_per_week_id'   => null,
            'payment_per_week_d_id'    => null,
            'payment_cost_other_id'     => null,
            'project_req_development_id' => $request->input('req_id'),
            'order_id' => $request->input('order_id'),
            'total' => $this->currency($request->input('total')),
            'user_id'   => $this->user_id,
            'akun'      => $request->input('account_payment'),
            'lawan'      => $account_project->cost_service_id,
            'deskripsi'     => 'Pembayaran Biaya '.($request->input('is_production') ? 'Produksi ' : '').' dari No Permintaan '.$project_dev->no,
            'tgl'       => date('Y-m-d'),
            'location_id'   => $this->site_id
        );
        $this->journalPayment($input_jurnal);

        return redirect('/payment/cost')->with('notification');
    }
    public function indexPaymentWeek()
    {
        $is_error = false;
        $error_message = '';  

        $data = array(
            'error' => array(
                'is_error' => $is_error,
                'error_message' => $error_message
            )
        );
        
        return view('pages.crm.payment.payment_week_list', $data);
    }
    public function createPaymentWeek()
    {
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
        $list_bank=DB::table('list_bank')->get();
        $data=array(
            'order_list'     => $order_list,
            'list_bank' => $list_bank
        );
        return view('pages.crm.payment.create_payment_week', $data);
    }
    public function savePaymentWeek(Request $request){
        $period_year = date('Y');
        $period_month = date('m');
        $rabcon = new RabController();
        $bill_no = $rabcon->generateTransactionNo('PAY_WEEK', $period_year, $period_month, $this->site_id );
        $payment=array();
        try
        {
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . 'inv/base/PaymentPerWeek']);
            $reqBody = [
                'headers' => $headers,
                'json' => [
                    'wop' => $request->input('wop'),
                    'bank_number' => $request->input('bank_number'),
                    'atas_nama' => $request->input('atas_nama'),
                    'ref_code' => $request->input('ref_code'),
                    'id_bank' => $request->input('id_bank'),
                    'amount' => $this->currency($request->input('total')),
                    'description' => $request->input('description'),
                    'pay_date' => $request->input('pay_date'),
                    'no'  => $bill_no,
                    'site_id'   => $this->site_id
                ]
            ]; 
            
            $response = $client->request('POST', '', $reqBody); 
            $body = $response->getBody();
            $content = $body->getContents();
            $response_array = json_decode($content,TRUE);
            $payment=$response_array['data'];
        } catch(RequestException $exception) {
        }

        $input_jurnal=array(
            'payment_id'    => null,
            'payment_per_week_id'   => $payment['id'],
            'payment_per_week_d_id'    => null,
            'payment_cost_other_id'     => null,
            'project_req_development_id' => 0,
            'order_id' => 0,
            'total' => $this->currency($request->input('total')),
            'user_id'   => $this->user_id,
            'akun'      => $request->input('account_payment'),
            'lawan'      => 149,
            'deskripsi'     => 'Pembayaran Biaya Produksi Mingguan',
            'tgl'       => date('Y-m-d'),
            'location_id'   => $this->site_id
        );
        $this->journalPayment($input_jurnal);

        return redirect('/payment/prod_weeks')->with('notification');
    }
    public function GetPaymentWeekJson(Request $request) {
        $query=DB::table('payment_per_weeks')
                    ->select('payment_per_weeks.*')
                    ->whereNull('payment_per_weeks.deleted_at');
        $query->get();
        $data=DataTables::of($query)
                                ->addColumn('action', function ($row) {
                                    return '<button type="button" id="modal_detail" class="btn btn-success btn-sm" data-toggle="modal" data-no="'.$row->no.'" data-id="'.$row->id.'" data-target=".bs-example-modal-lg" onclick="getDetail(this)"><i class="mdi mdi-eye"></i></button>'.'
                                    
                                    '.'<a href="'.url('/payment/add_prod_week_dt/'.$row->id).'" class="btn btn-info btn-sm"><i class="mdi mdi-plus"></i></a>';
                                    // '.'<a href="/order/edit/'.$row['id'].'" class="btn btn-info btn-sm"><i class="mdi mdi-pencil"></i></a>'.'
                                })
                                ->rawColumns(['order_name', 'action'])
                                ->make(true);          
    
        return $data;
    }
    public function createPaymentWeekDT($id)
    {
        try
        {
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            if ($this->site_id == null) {
                $client1 = new Client(['base_uri' => $this->base_api_url . 'order/spk_option']); 
            }else{
                $client1 = new Client(['base_uri' => $this->base_api_url . 'order/spk_option?site_id='.$this->site_id]); 
            }
            $response1 = $client1->request('GET', '', ['headers' => $headers]); 
            $body1 = $response1->getBody();
            $content1 = $body1->getContents();
            $response_array1 = json_decode($content1,TRUE);

            $response1 = $content1;  
            $spk_list = $response_array1['data'];
        } catch(RequestException $exception) {
            
        }
        try
        {
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . 'inv/base/PaymentPerWeek/'.$id]);
            $response = $client->request('GET', '', ['headers' => $headers]); 
            $body = $response->getBody();
            $content = $body->getContents();
            $response_array = json_decode($content,TRUE);
            $paid_week=$response_array['data'];
        } 
        catch(RequestException $exception) {
        }
        $data=array(
            'spk_list'     => $spk_list,
            'paid_week' => $paid_week
        );
        // return $spk_list;
        return view('pages.crm.payment.add_payment_week_dt', $data);
    }
    public function savePaymentWeekDT(Request $request){
        $id=$request->input('id');
        $order_id=$request->input('order_id');
        $req_id=$request->input('req_id');
        $amount=$request->input('amount');
        $check_production=$request->input('check_production');
        $notes=$request->input('notes');
        $account_beban=$request->input('account_beban');
        
        for ($i=0; $i < count($order_id); $i++) { 
            $payment=array();
            try
            {
                $headers = [
                    'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                    'Accept'        => 'application/json',
                ];
                $client = new Client(['base_uri' => $this->base_api_url . 'inv/base/PaymentPerWeekD']);
                $reqBody = [
                    'headers' => $headers,
                    'json' => [
                        'order_id' => $order_id[$i],
                        'project_req_development_id' => $req_id[$i],
                        'amount' => $this->currency($amount[$i]),
                        'note' => $notes[$i],
                        'payment_per_week_id' => $id,
                        'is_production'  => $check_production[$i],
                    ]
                ]; 
                
                $response = $client->request('POST', '', $reqBody); 
                $body = $response->getBody();
                $content = $body->getContents();
                $response_array = json_decode($content,TRUE);
                $payment=$response_array['data'];
            } catch(RequestException $exception) {
            }   
            $project_dev=DB::table('project_req_developments')->where('id', $req_id[$i])->first();
            $account_project=DB::table('account_projects')->where('order_id', $order_id[$i])->first();
            $input_jurnal=array(
                'payment_id'    => null,
                'payment_per_week_id'   => null,
                'payment_per_week_d_id'    => $payment['id'],
                'payment_cost_other_id'     => null,
                'project_req_development_id' => $req_id[$i],
                'order_id' => $order_id[$i],
                'total' => $this->currency($amount[$i]),
                'user_id'   => $this->user_id,
                'akun'      => 149,
                'lawan'      => $account_project->cost_service_id,
                'deskripsi'     => 'Pembayaran '.$notes[$i].' '.($check_production[$i] == 1 ? 'Produksi' : '').' dari No Permintaan '.$project_dev->no,
                'tgl'       => date('Y-m-d'),
                'location_id'   => $this->site_id
            );
            $this->journalPayment($input_jurnal);
        }
        return redirect('/payment/prod_weeks')->with('notification');
    }
    public function getDetailPaidPerWeek($id){
        $query=DB::table('payment_per_week_ds')
                    ->where('payment_per_week_id', $id)
                    ->join('orders', 'orders.id', 'payment_per_week_ds.order_id')
                    ->join('project_req_developments', 'project_req_developments.id', 'payment_per_week_ds.project_req_development_id')
                    ->select('project_req_developments.no as req_no', 'orders.order_no', 'payment_per_week_ds.*')
                    ->get();

        $data=array(
            'data'  => $query
        );
        return $data;
    }
    public function indexCostOther()
    {
        $is_error = false;
        $error_message = '';  

        $data = array(
            'error' => array(
                'is_error' => $is_error,
                'error_message' => $error_message
            )
        );
        
        return view('pages.crm.payment.payment_cost_other_list', $data);
    }
    public function addPaidCostOther()
    {
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
        $list_bank=DB::table('list_bank')->get();
        $data=array(
            'order_list'     => $order_list,
            'list_bank' => $list_bank
        );
        return view('pages.crm.payment.paid_cost_other_form', $data);
    }
    public function savePaidCostOther(Request $request){
        $period_year = date('Y');
        $period_month = date('m');
        $rabcon = new RabController();
        $bill_no = $rabcon->generateTransactionNo('PAY_COST', $period_year, $period_month, $this->site_id );
        $payment=array();
        try
        {
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . 'inv/base/PaymentCostOther']);
            $reqBody = [
                'headers' => $headers,
                'json' => [
                    'wop' => $request->input('wop'),
                    'bank_number' => $request->input('bank_number'),
                    'atas_nama' => $request->input('atas_nama'),
                    'ref_code' => $request->input('ref_code'),
                    'id_bank' => $request->input('id_bank'),
                    'amount' => $this->currency($request->input('total')),
                    'description' => $request->input('description'),
                    'pay_date' => $request->input('pay_date'),
                    'no'  => $bill_no,
                    'site_id'   => $this->site_id
                ]
            ]; 
            
            $response = $client->request('POST', '', $reqBody); 
            $body = $response->getBody();
            $content = $body->getContents();
            $response_array = json_decode($content,TRUE);
            $payment=$response_array['data'];
        } catch(RequestException $exception) {
        }
        $input_jurnal=array(
            'payment_id'    => null,
            'payment_per_week_id'   => null,
            'payment_per_week_d_id'    => null,
            'payment_cost_other_id'     => $payment['id'],
            'project_req_development_id' => 0,
            'order_id' => 0,
            'total' => $this->currency($request->input('total')),
            'user_id'   => $this->user_id,
            'akun'      => $request->input('account_payment'),
            'lawan'      => $request->input('account_beban'),
            'deskripsi'     => $request->input('description'),
            'tgl'       => date('Y-m-d'),
            'location_id'   => $this->site_id
        );
        $this->journalPayment($input_jurnal);
        return redirect('/payment/cost_other')->with('notification');
    }
    public function GetPaidCostOther(Request $request) {
        try
        {
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . 'inv/base/PaymentCostOther']);
            $response = $client->request('GET', '', ['headers' => $headers]); 
            $body = $response->getBody();
            $content = $body->getContents();
            $response_array = json_decode($content,TRUE);
        } catch(RequestException $exception) {
        }
        $data=DataTables::of($response_array['data'])
                                ->addColumn('action', function ($row) {
                                    return '<button type="button" id="modal_detail" class="btn btn-success btn-sm" data-toggle="modal" data-no="'.$row['no'].'" data-id="'.$row['id'].'" data-target=".bs-example-modal-lg" onclick="getDetail(this)"><i class="mdi mdi-eye"></i></button>&nbsp;<a href="'.url('payment/delete_cost_other/'.$row['id']).'" class="btn btn-danger btn-sm" onclick="javasciprt: return confirm(\'Are You Sure ?\')"><i class="mdi mdi-delete"></i></a>';
                                })
                                ->rawColumns(['action'])
                                ->make(true);          
    
        return $data;
    }
    private function currency($val){
        $data=explode('.', $val);
        $new=implode('', $data);
        return $new;
    }
    public function journalPayment($data){
        $data_trx=array(
            'deskripsi'     => $data['deskripsi'],
            'location_id'     => $data['location_id'],
            'tanggal'       => $data['tgl'],
            'user_id'       => $data['user_id'],
            'order_id'   => $data['order_id'],
            'project_req_development_id'   => $data['project_req_development_id'],
            'payment_id'    => $data['payment_id'],
            'payment_per_week_id'   => $data['payment_per_week_id'],
            'payment_per_week_d_id'    => $data['payment_per_week_d_id'],
            'payment_cost_other_id'     => $data['payment_cost_other_id'],
        );
        $insert=DB::table('tbl_trx_akuntansi')->insert($data_trx);
        if ($insert) {
            $id_last=DB::table('tbl_trx_akuntansi')->max('id_trx_akun');
            $lawan=array(
                'id_trx_akun'   => $id_last,
                'id_akun'       => $data['lawan'],
                'jumlah'        => $data['total'],
                'tipe'          => "DEBIT",
                'keterangan'    => 'lawan',
            );
            DB::table('tbl_trx_akuntansi_detail')->insert($lawan);
            if($data['lawan'] == 24 || $data['lawan'] == 101){
                $acccon = new AkuntanController();
                $warehouse_id=$data['lawan'] == 24 ? 2 : 3;
                $acccon->countSaldoCash($this->site_id, $warehouse_id, "in", $data['total']);
            }

            $acccon = new AkuntanController();
            $no=$acccon->createNo($data['akun'], "KREDIT");
            $akun=array(
                'id_trx_akun'   => $id_last,
                'id_akun'       => $data['akun'],
                'jumlah'        => $data['total'],
                'tipe'          => "KREDIT",
                'keterangan'    => 'akun',
                'no'            => $no
            );
            DB::table('tbl_trx_akuntansi_detail')->insert($akun);
            if($data['akun'] == 24 || $data['akun'] == 101){
                $acccon = new AkuntanController();
                $warehouse_id=$data['akun'] == 24 ? 2 : 3;
                $acccon->countSaldoCash($this->site_id, $warehouse_id, "out", $data['total']);
            }
        }
    }
    public function instalOrderList()
    {
        $is_error = false;
        $error_message = '';  

        $data = array(
            'error' => array(
                'is_error' => $is_error,
                'error_message' => $error_message
            )
        );
        
        return view('pages.crm.payment.payment_install_order_list', $data);
    }

    public function createPaymentInstallOrder()
    {
        try
        {
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            if ($this->site_id == null) {
                $client1 = new Client(['base_uri' => $this->base_api_url . 'order/option_install_order']); 
            }else{
                $client1 = new Client(['base_uri' => $this->base_api_url . 'order/option_install_order?site_id='.$this->site_id]); 
            } 
            $response1 = $client1->request('GET', '', ['headers' => $headers]); 
            $body1 = $response1->getBody();
            $content1 = $body1->getContents();
            $response_array1 = json_decode($content1,TRUE);

            $response1 = $content1;  
            $order_list = $response_array1['data'];
        } catch(RequestException $exception) {
            
        }
        $list_bank=DB::table('list_bank')->get();
        $data=array(
            'order_list'     => $order_list,
            'list_bank' => $list_bank
        );
        return view('pages.crm.payment.create_payment_install_order', $data);
    }
    public function saveInstallOrder(Request $request){
        $period_year = date('Y');
        $period_month = date('m');
        $rabcon = new RabController();
        $bill_no = $rabcon->generateTransactionNo('PAID_IO', $period_year, $period_month, $this->site_id );
        $payment=array();
        try
        {
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . 'inv/base/PaymentOrderInstall']);
            $reqBody = [
                'headers' => $headers,
                'json' => [
                    'install_order_id' => $request->input('install_order_id'),
                    'payment_type' => 'PAID_IO',
                    'wop' => $request->input('wop'),
                    'bank_number' => $request->input('bank_number'),
                    'atas_nama' => $request->input('atas_nama'),
                    'ref_code' => $request->input('ref_code'),
                    'id_bank' => $request->input('id_bank'),
                    'amount' => $this->currency($request->input('total')),
                    'description' => $request->input('description'),
                    'pay_date' => $request->input('pay_date'),
                    'no'  => $bill_no,
                    'is_out_source' => $request->input('is_out_source'),
                    'site_id'   => $this->site_id
                ]
            ]; 
            
            $response = $client->request('POST', '', $reqBody); 
            $body = $response->getBody();
            $content = $body->getContents();
            $response_array = json_decode($content,TRUE);
            $payment=$response_array['data'];
        } catch(RequestException $exception) {
        }
        $install_order=DB::table('install_orders')->where('id', $request->input('install_order_id'))->first();
        $input_jurnal=array(
            'payment_order_install_id'  => $payment['id'],
            'install_order_id' => $request->input('install_order_id'),
            'total' => $this->currency($request->input('total')),
            'user_id'   => $this->user_id,
            'akun'      => $request->input('account_payment'),
            'lawan'      => 169,
            'deskripsi'     => 'Pembayaran SDM '.($request->input('is_out_source') ? 'Out Sourcing' : '').'  dari No Order Instalasi '.$install_order->no,
            'tgl'       => date('Y-m-d'),
            'location_id'   => $this->site_id
        );
        $this->journalPaymentInstallOrder($input_jurnal);

        return redirect('/payment/install_order')->with('notification');
    }
    private function journalPaymentInstallOrder($data){
        $data_trx=array(
            'deskripsi'     => $data['deskripsi'],
            'location_id'     => $data['location_id'],
            'tanggal'       => $data['tgl'],
            'user_id'       => $data['user_id'],
            'payment_order_install_id'       => $data['payment_order_install_id'],
            'install_order_id' => $data['install_order_id']
        );
        $insert=DB::table('tbl_trx_akuntansi')->insert($data_trx);
        if ($insert) {
            $id_last=DB::table('tbl_trx_akuntansi')->max('id_trx_akun');
            $lawan=array(
                'id_trx_akun'   => $id_last,
                'id_akun'       => $data['lawan'],
                'jumlah'        => $data['total'],
                'tipe'          => "DEBIT",
                'keterangan'    => 'lawan',
            );
            DB::table('tbl_trx_akuntansi_detail')->insert($lawan);
            $acccon = new AkuntanController();
            $no=$acccon->createNo($data['akun'], "KREDIT");
            $akun=array(
                'id_trx_akun'   => $id_last,
                'id_akun'       => $data['akun'],
                'jumlah'        => $data['total'],
                'tipe'          => "KREDIT",
                'keterangan'    => 'akun',
                'no'            => $no
            );
            DB::table('tbl_trx_akuntansi_detail')->insert($akun);
            if($data['akun'] == 24 || $data['akun'] == 101){
                $acccon = new AkuntanController();
                $warehouse_id=$data['akun'] == 24 ? 2 : 3;
                $acccon->countSaldoCash($this->site_id, $warehouse_id, "out", $data['total']);
            }
        }
    }
    public function GetInstallOrderPaymentJson(Request $request) {
        $tipe=$request->has('tipe');
        $query=DB::table('payment_order_installs as pi')
                    ->join('install_orders as io', 'io.id', 'pi.install_order_id')
                    ->select('pi.*', 'io.no as io_no', 'pi.no as paid_no')
                    ->whereNull('pi.deleted_at');
        if ($this->site_id != null) {
            $query->where('pi.site_id', $this->site_id);
        }
        $query->get();
        $data=DataTables::of($query)
                                ->addColumn('action', function ($row) {
                                    return '<button type="button" id="modal_detail" class="btn btn-success btn-sm" data-toggle="modal" data-order_no="'.$row->paid_no.'" data-id="'.$row->id.'" data-target=".bs-example-modal-lg" onclick="getDetail(this)"><i class="mdi mdi-eye"></i></button>'.'
                                    
                                    '.'<a hidden href="'.url('/payment/delete/'.$row->id).'" class="btn btn-danger btn-sm" onclick="javasciprt: return confirm(\'Are You Sure ?\')"><i class="mdi mdi-delete"></i></a>';
                                    // '.'<a href="/order/edit/'.$row['id'].'" class="btn btn-info btn-sm"><i class="mdi mdi-pencil"></i></a>'.'
                                })
                                ->rawColumns(['order_name', 'action'])
                                ->make(true);          
    
        return $data;
    }
    public function debtList()
    {
        $is_error = false;
        $error_message = '';  

        $data = array(
            'error' => array(
                'is_error' => $is_error,
                'error_message' => $error_message
            ),
            'list_bank' =>DB::table('list_bank')->get()
        );
        
        return view('pages.crm.payment.debt_list', $data);
    }
    public function createDebt()
    {
        try
        {
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . 'inv/base/MSupplier']);
            
            $response = $client->request('GET', '', ['headers' => $headers]); 
            $body = $response->getBody();
            $content = $body->getContents();
            $response_array = json_decode($content,TRUE);
        } catch(RequestException $exception) {
        }
        $data=array(
            'supplier'  => $response_array['data']
        );
        return view('pages.crm.payment.create_debt', $data);
    }
    public function saveDebt(Request $request){
        $period_year = date('Y');
        $period_month = date('m');
        $rabcon = new RabController();
        $bill_no = $rabcon->generateTransactionNo('DEBT', $period_year, $period_month, $this->site_id );
        $debts=null;
        try
        {
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . 'inv/base/Debt']);
            $reqBody = [
                'headers' => $headers,
                'json' => [
                    'm_supplier_id' => $request->input('supplier_id'),
                    'amount' => $this->currency($request->input('total')),
                    'notes' => $request->input('description'),
                    'debt_date' => $request->input('date'),
                    'due_date' => $request->input('due_date'),
                    'no'  => $bill_no,
                    'site_id'   => $this->site_id
                ]
            ]; 
            
            $response = $client->request('POST', '', $reqBody); 
            $body = $response->getBody();
            $content = $body->getContents();
            $response_array = json_decode($content,TRUE);
            $debts=$response_array['data'];
        } catch(RequestException $exception) {
        }
        $input_jurnal=array(
            'total' => $this->currency($request->input('total')),
            'user_id'   => $this->user_id,
            'akun'      => 55,
            'lawan'      => 20,
            'deskripsi'     => $request->input('description'),
            'tgl'       => $request->input('date'),
            'location_id'   => $this->site_id,
            'm_supplier_id' => $request->input('supplier_id'),
            'debt_id'   => $debts['id']
        );
        $this->journalDebt($input_jurnal);

        return redirect('/payment/debt')->with('notification');
    }
    private function journalDebt($data){
        $data_trx=array(
            'deskripsi'     => $data['deskripsi'],
            'location_id'     => $data['location_id'],
            'tanggal'       => $data['tgl'],
            'user_id'       => $data['user_id'],
            'm_supplier_id' => $data['m_supplier_id'],
            'debt_id'       => $data['debt_id']
        );
        $insert=DB::table('tbl_trx_akuntansi')->insert($data_trx);
        if ($insert) {
            $id_last=DB::table('tbl_trx_akuntansi')->max('id_trx_akun');
            $lawan=array(
                'id_trx_akun'   => $id_last,
                'id_akun'       => $data['lawan'],
                'jumlah'        => $data['total'],
                'tipe'          => "DEBIT",
                'keterangan'    => 'akun',
            );
            DB::table('tbl_trx_akuntansi_detail')->insert($lawan);
            $akun=array(
                'id_trx_akun'   => $id_last,
                'id_akun'       => $data['akun'],
                'jumlah'        => $data['total'],
                'tipe'          => "KREDIT",
                'keterangan'    => 'lawan',
            );
            DB::table('tbl_trx_akuntansi_detail')->insert($akun);
        }
    }
    public function GetDebtJson(Request $request) {
        $status=$request->status;
        $query=DB::table('debts')
                    ->join('m_suppliers as ms', 'ms.id', 'debts.m_supplier_id')
                    ->select('debts.*', 'ms.name', DB::raw('coalesce((select count(debt_id) from debt_ds where debt_id=debts.id), 0) as total_paid'))
                    ->where('site_id', $this->site_id)
                    ->whereNull('debts.deleted_at');

        if($status != null && $status != 'all'){
            $query->where('is_paid', $status);
        }
                    // ->where('debts.site_id', $this->site_id)
        $query=$query->get();
        
        $data=DataTables::of($query)
                                ->addColumn('action', function ($row) {
                                    // return $row->is_paid == false ? '<button type="button" id="modal_detail" class="btn btn-success btn-sm" data-toggle="modal" data-order_no="'.$row->no.'" data-id="'.$row->id.'" data-amount="'.$row->amount.'" data-target=".bs-example-modal-lg" onclick="getDetail(this)"><i class="mdi mdi-credit-card-plus"></i></button>' : 'Sudah Dibayar';
                                    return $row->is_paid == false ? 'Belum Dibayar' : 'Sudah Dibayar';
                                    
                                })
                                // ->rawColumns(['order_name', 'action'])
                                ->make(true);          
    
        return $data;
    }
    public function getBillInstall($id){
        $product=DB::table('install_order_ds')
                        ->join('products', 'products.id', 'install_order_ds.product_id')
                        ->select('products.*')
                        ->where('install_order_ds.install_order_id', $id)
                        ->get();
        foreach ($product as $key => $value) {
            $value->detail=DB::table('inv_requests as ir')
                    ->select('dpfw.product_id', DB::raw('COUNT(dpfw.worksub_id) as total'), DB::raw('COALESCE((SELECT price_work from install_worksubs where product_id=dpfw.product_id and install_order_id='.$id.' and worksub_id=dpfw.worksub_id), 0) as price'), DB::raw('MAX(dpfw.worksub_id) as worksub_id'), DB::raw('MAX(worksubs.name) as worksub_name'))
                    ->join('dev_project_frames as dpf', 'ir.id', 'dpf.inv_request_id')
                    ->join('dev_project_frame_worksubs as dpfw', 'dpf.id', 'dpfw.dev_project_frame_id')
                    ->join('worksubs', 'worksubs.id', 'dpfw.worksub_id')
                    ->where('dpfw.product_id', $value->id)
                    ->where('ir.install_order_id', $id)
                    ->groupBy('dpfw.product_id', 'dpfw.worksub_id')
                    ->get();
        }
        $data=array(
            'data'  => $product
        );
        return $data;
    }
    public function paidDebt($id){
        $debts=DB::table('debts')
                        ->where('id', $id)
                        ->first();
        dd($debts);
    }
    public function giroList(){
        $is_error = false;
        $error_message = '';  

        $data = array(
            'error' => array(
                'is_error' => $is_error,
                'error_message' => $error_message
            ),
            'list_bank' =>DB::table('list_bank')->get()
        );
        return view('pages.inv.inventory_transaction.giro_list', $data);
    }
    public function giroListSupplier(){
        $is_error = false;
        $error_message = '';  

        $data = array(
            'error' => array(
                'is_error' => $is_error,
                'error_message' => $error_message
            ),
            'list_bank' =>DB::table('list_bank')->get()
        );
        return view('pages.inv.inventory_transaction.giro_supplier_list', $data);
    }
    public function jsonGiro(){
        $giro=null;
        try
        {
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            if ($this->site_id == null) {
                $client1 = new Client(['base_uri' => $this->base_api_url . 'crm/giro']); 
            }else{
                $client1 = new Client(['base_uri' => $this->base_api_url . 'crm/giro?site_id='.$this->site_id]); 
            }
            $response1 = $client1->request('GET', '', ['headers' => $headers]); 
            $body1 = $response1->getBody();
            $content1 = $body1->getContents();
            $response_array1 = json_decode($content1,TRUE);
            $response1 = $content1;  
            $giro = $response_array1['data'];
        } catch(RequestException $exception) {
            
        }
        $data=DataTables::of($giro)
                                // ->addColumn('action', function ($row) {
                                //     return '<button type="button" id="modal_detail" class="btn btn-success btn-sm" data-toggle="modal" data-order_no="'.$row->paid_no.'" data-id="'.$row->id.'" data-target=".bs-example-modal-lg" onclick="getDetail(this)"><i class="mdi mdi-eye"></i></button>'.'
                                    
                                //     '.'<a hidden href="'.url('/payment/delete/'.$row->id).'" class="btn btn-danger btn-sm" onclick="javasciprt: return confirm(\'Are You Sure ?\')"><i class="mdi mdi-delete"></i></a>';
                                //     // '.'<a href="/order/edit/'.$row['id'].'" class="btn btn-info btn-sm"><i class="mdi mdi-pencil"></i></a>'.'
                                // })
                                // ->rawColumns(['order_name', 'action'])
                                ->make(true);          
        return $data;
    }
    public function jsonGiroSupplier(){
        $giro=null;
        try
        {
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            if ($this->site_id == null) {
                $client1 = new Client(['base_uri' => $this->base_api_url . 'crm/giro_supplier']); 
            }else{
                $client1 = new Client(['base_uri' => $this->base_api_url . 'crm/giro_supplier?site_id='.$this->site_id]); 
            }
            $response1 = $client1->request('GET', '', ['headers' => $headers]); 
            $body1 = $response1->getBody();
            $content1 = $body1->getContents();
            $response_array1 = json_decode($content1,TRUE);
            $response1 = $content1;  
            $giro = $response_array1['data'];
        } catch(RequestException $exception) {
            
        }
        $data=DataTables::of($giro)
                                // ->addColumn('action', function ($row) {
                                //     return '<button type="button" id="modal_detail" class="btn btn-success btn-sm" data-toggle="modal" data-order_no="'.$row->paid_no.'" data-id="'.$row->id.'" data-target=".bs-example-modal-lg" onclick="getDetail(this)"><i class="mdi mdi-eye"></i></button>'.'
                                    
                                //     '.'<a hidden href="'.url('/payment/delete/'.$row->id).'" class="btn btn-danger btn-sm" onclick="javasciprt: return confirm(\'Are You Sure ?\')"><i class="mdi mdi-delete"></i></a>';
                                //     // '.'<a href="/order/edit/'.$row['id'].'" class="btn btn-info btn-sm"><i class="mdi mdi-pencil"></i></a>'.'
                                // })
                                // ->rawColumns(['order_name', 'action'])
                                ->make(true);          
        return $data;
    }
    public function saveGiroDetail(Request $request){
        $total=$request->total;
        $id=$request->giro_id;
        $amount=$request->amount;
        $account_payment=$request->account_payment;
        $giro=DB::table('giros')->where('id', $id)->first();
        $data_temp=array();
        $paid_less=$total;
        foreach ($amount as $key => $value) {
            if ($this->currency($value) > 0) {
                $paid_less-=$this->currency($value);
                try
                {
                    $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . 'inv/base/GiroD']);
                    $reqBody = [
                        'headers' => $headers,
                'json' => [
                            'giro_id' => $id,
                            'amount' => $this->currency($value),
                            'akun_id' => $account_payment[$key],
                        ]
                    ]; 
                    $response = $client->request('POST', '', $reqBody); 

                } catch(RequestException $exception) {
                }
                $data_temp[]=array(
                    'total' => $this->currency($value),
                    'akun' => $account_payment[$key],
                );
            }
        }
        
        $data=array(
            'giro_id'   => $id,
            'total' => $total,
            'akun' => $data_temp,
            'paid_less' => $paid_less,
            'location_id'   => $giro->site_id,
            'deskripsi'     => 'Pencairan Giro No '.$giro->no
        );
        $this->journalPencairanGiro($data);

        try
        {
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . 'inv/base/Giro/'.$id]);
            $reqBody = [
                'headers' => $headers,
                'json' => [
                    'is_divided' => true,
                ]
            ]; 
            $response = $client->request('PUT', '', $reqBody); 
        } catch(RequestException $exception) {
        }
        return redirect('payment/giro');
    }
    private function journalPencairanGiro($data){
        $data_trx=array(
            'deskripsi'     => $data['deskripsi'],
            'location_id'     => $data['location_id'],
            'tanggal'       => date('Y-m-d'),
            'user_id'       => $this->user_id,
            'giro_id'   => $data['giro_id'],
        );
        $insert=DB::table('tbl_trx_akuntansi')->insert($data_trx);
        if ($insert) {
            $id_last=DB::table('tbl_trx_akuntansi')->max('id_trx_akun');
            foreach ($data['akun'] as $key => $value) {
                $acccon = new AkuntanController();
                $no=$acccon->createNo($value['akun'], "DEBIT");
                $akun=array(
                    'id_trx_akun'   => $id_last,
                    'id_akun'       => $value['akun'],
                    'jumlah'        => $value['total'],
                    'tipe'          => "DEBIT",
                    'keterangan'    => 'akun',
                    'no'            => $no
                );
                DB::table('tbl_trx_akuntansi_detail')->insert($akun);
                if($value['akun'] == 24 || $value['akun'] == 101){
                    $acccon = new AkuntanController();
                    $warehouse_id=$value['akun'] == 24 ? 2 : 3;
                    $acccon->countSaldoCash($this->site_id, $warehouse_id, "in", $value['total']);
                }
            }
            if ($data['paid_less'] > 0) {
                $akun=array(
                    'id_trx_akun'   => $id_last,
                    'id_akun'       => 165,
                    'jumlah'        => $data['paid_less'],
                    'tipe'          => "DEBIT",
                    'keterangan'    => 'akun',
                );
                DB::table('tbl_trx_akuntansi_detail')->insert($akun);
            }
            $lawan=array(
                'id_trx_akun'   => $id_last,
                'id_akun'       => 36,
                'jumlah'        => $data['total'],
                'tipe'          => "KREDIT",
                'keterangan'    => 'lawan',
            );
            DB::table('tbl_trx_akuntansi_detail')->insert($lawan);
        }
    }
    public function savePengisianGiro(Request $request){
        $total=$request->total;
        $id=$request->giro_id;
        $amount=$request->amount;
        $account_payment=$request->account_payment;
        $giro=DB::table('giros')->where('id', $id)->first();
        $data_temp=array();
        $paid_less=$total;
        foreach ($amount as $key => $value) {
            if ($this->currency($value) > 0) {
                $paid_less-=$this->currency($value);
                try
                {
                    $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . 'inv/base/GiroD']);
                    $reqBody = [
                        'headers' => $headers,
                'json' => [
                            'giro_id' => $id,
                            'amount' => $this->currency($value),
                            'akun_id' => $account_payment[$key],
                        ]
                    ]; 
                    $response = $client->request('POST', '', $reqBody); 

                } catch(RequestException $exception) {
                }
                $data_temp[]=array(
                    'total' => $this->currency($value),
                    'akun' => $account_payment[$key],
                );
            }
        }
        
        $data=array(
            'giro_id'   => $id,
            'total' => $total,
            'akun' => $data_temp,
            'paid_less' => $paid_less,
            'location_id'   => $giro->site_id,
            'deskripsi'     => 'Pengisian Giro No '.$giro->no
        );
        $this->journalPengisianGiro($data);
        
        try
        {
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . 'inv/base/Giro/'.$id]);
            $reqBody = [
                'headers' => $headers,
                'json' => [
                    'is_divided' => true,
                ]
            ]; 
            $response = $client->request('PUT', '', $reqBody); 
        } catch(RequestException $exception) {
        }
        return redirect('payment/giro_fill');
    }
    private function journalPengisianGiro($data){
        $data_trx=array(
            'deskripsi'     => $data['deskripsi'],
            'location_id'     => $data['location_id'],
            'tanggal'       => date('Y-m-d'),
            'user_id'       => $this->user_id,
            'giro_id'   => $data['giro_id'],
        );
        $insert=DB::table('tbl_trx_akuntansi')->insert($data_trx);
        if ($insert) {
            $id_last=DB::table('tbl_trx_akuntansi')->max('id_trx_akun');
            foreach ($data['akun'] as $key => $value) {
                $acccon = new AkuntanController();
                $no=$acccon->createNo($value['akun'], "KREDIT");
                $akun=array(
                    'id_trx_akun'   => $id_last,
                    'id_akun'       => $value['akun'],
                    'jumlah'        => $value['total'],
                    'tipe'          => "KREDIT",
                    'keterangan'    => 'akun',
                    'no'            => $no
                );
                DB::table('tbl_trx_akuntansi_detail')->insert($akun);
                if($value['akun'] == 24 || $value['akun'] == 101){
                    $acccon = new AkuntanController();
                    $warehouse_id=$value['akun'] == 24 ? 2 : 3;
                    $acccon->countSaldoCash($this->site_id, $warehouse_id, "out", $value['total']);
                }
            }
            if ($data['paid_less'] > 0) {
                $akun=array(
                    'id_trx_akun'   => $id_last,
                    'id_akun'       => 166,
                    'jumlah'        => $data['paid_less'],
                    'tipe'          => "KREDIT",
                    'keterangan'    => 'akun',
                );
                DB::table('tbl_trx_akuntansi_detail')->insert($akun);
            }
            $lawan=array(
                'id_trx_akun'   => $id_last,
                'id_akun'       => 36,
                'jumlah'        => $data['total'],
                'tipe'          => "DEBIT",
                'keterangan'    => 'lawan',
            );
            DB::table('tbl_trx_akuntansi_detail')->insert($lawan);
        }
    }
    public function savePaidDebt(Request $request){
        $total=$request->total;
        $id=$request->debt_id;
        $total_paid=$this->currency($request->total_paid);
        $account_payment=$request->account_payment;
        $paid_more=$request->paid_more;
        $paid_less=$request->paid_less;
        $debt=DB::table('debts')->where('id', $id)->first();
        $data_temp=array();
        try
        {
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . 'inv/base/DebtD']);
            $reqBody = [
                'headers' => $headers,
                'json' => [
                    'debt_id' => $id,
                    'akun_id' => $account_payment,
                    'amount' => $total_paid,
                    'notes' => $request->notes,
                    'id_bank' => $request->id_bank,
                    'bank_number' => $request->bank_number,
                    'atas_nama' => $request->atas_nama,
                    'pay_date' => $request->pay_date,
                    'wop' => $request->wop,
                ]
            ]; 
            $response = $client->request('POST', '', $reqBody); 

        } catch(RequestException $exception) {
        }
        
        $input_jurnal=array(
            'total' => $total,
            'total_paid' => $total_paid,
            'user_id'   => $this->user_id,
            'akun'      => $account_payment,
            'bbk'       => $request->bbk,
            'lawan'      => 55,
            'deskripsi'     => 'Pembayaran Hutang Supplier no '.$debt->no,
            'tgl'       => $request->pay_date,
            'paid_more' => $paid_more,
            'paid_less' => $paid_less,
            'location_id'   => $this->site_id,
            'm_supplier_id' => $debt->m_supplier_id,
            'debt_id'   => $id
        );
        $this->journalPaidDebt($input_jurnal);
        
        try
        {
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . 'inv/base/Debt/'.$id]);
            $reqBody = [
                'headers' => $headers,
                'json' => [
                    'is_paid' => true,
                ]
            ]; 
            $response = $client->request('PUT', '', $reqBody); 
        } catch(RequestException $exception) {
        }
        return redirect('payment/debt');
    }
    private function journalPaidDebt($data){
        $data_trx=array(
            'deskripsi'     => $data['deskripsi'],
            'location_id'     => $data['location_id'],
            'tanggal'       => $data['tgl'],
            'user_id'       => $data['user_id'],
            'm_supplier_id' => $data['m_supplier_id'],
            'debt_id'       => $data['debt_id']
        );
        $insert=DB::table('tbl_trx_akuntansi')->insert($data_trx);
        if ($insert) {
            $id_last=DB::table('tbl_trx_akuntansi')->max('id_trx_akun');
            $lawan=array(
                'id_trx_akun'   => $id_last,
                'id_akun'       => $data['lawan'],
                'jumlah'        => $data['total'],
                'tipe'          => "DEBIT",
                'keterangan'    => 'akun',
            );
            DB::table('tbl_trx_akuntansi_detail')->insert($lawan);
            if ($data['paid_more'] > 0) {
                $akun=array(
                    'id_trx_akun'   => $id_last,
                    'id_akun'       => 163,
                    'jumlah'        => $data['paid_more'],
                    'tipe'          => "DEBIT",
                    'keterangan'    => 'akun',
                );
                DB::table('tbl_trx_akuntansi_detail')->insert($akun);
            }
            $no=$data['bbk'];
            if ($no == '') {
                $acccon = new AkuntanController();
                $no=$acccon->createNo($data['akun'], "KREDIT");
            }
            $akun=array(
                'id_trx_akun'   => $id_last,
                'id_akun'       => $data['akun'],
                'jumlah'        => $data['total_paid'],
                'tipe'          => "KREDIT",
                'keterangan'    => 'lawan',
                'no'            => $no
            );
            DB::table('tbl_trx_akuntansi_detail')->insert($akun);
            if($data['akun'] == 24 || $data['akun'] == 101){
                $acccon = new AkuntanController();
                $warehouse_id=$data['akun'] == 24 ? 2 : 3;
                $acccon->countSaldoCash($this->site_id, $warehouse_id, "out", $data['total_paid']);
            }
            if ($data['paid_less'] > 0) {
                $akun=array(
                    'id_trx_akun'   => $id_last,
                    'id_akun'       => 166,
                    'jumlah'        => $data['paid_less'],
                    'tipe'          => "KREDIT",
                    'keterangan'    => 'lawan',
                );
                DB::table('tbl_trx_akuntansi_detail')->insert($akun);
            }
        }
    }
    public function paidDebtList(){
        return view('pages.crm.payment.payment_debt');
    }
    public function formPaidDebt(){
        $suppliers = null;
        try
        {
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . '/crm/base/MSupplier']);  
            $response = $client->request('GET', '', ['headers' => $headers]); 
            $body = $response->getBody();
            $content = $body->getContents();
            $response_array = json_decode($content,TRUE);
            $suppliers = $response_array['data'];
        } catch(RequestException $exception) {      
        }
        $data=array(
            'suppliers'  => $suppliers,
            'list_bank' => DB::table('list_bank')->get()
        );
        return view('pages.crm.payment.form_paid_debt', $data);
    }
    public function getNoDebtJson($id){
        $query=DB::table('debts')->where('m_supplier_id', $id)->where('is_paid', false)->get();
        foreach ($query as $key => $value) {
            $detail=$this->getDebtDetailJson($value->id);
            $value->paid=$detail['paid'];
        }
        $data=array(
            'data'  => $query
        );
        return $data;
    }
    public function getDebtDetailJson($id){
        $query=DB::table('debts')
                        ->where('id', $id)
                        ->first();
        $total=$query->amount;
        $paid=DB::table('debt_ds')
                        ->where('debt_id', $id)
                        ->select(DB::raw('COALESCE(SUM(amount),0) as amount'))
                        ->first();
        $data=array(
            'data'  => $query,
            'total' => $total,
            'paid'  => $paid->amount
        );
        return $data;
    }
    public function saveMultiplePaidDebt(Request $request) {
        $bill_id=$request->check_id;
        $debt_id=$request->debt_id;
        $amount_bill=$request->amount;
        
        $total=$this->currency($request->total);
        $paid_more=$this->currency($request->paid_more);
        $paid_less=$this->currency($request->paid_less);
        $total_all=$request->total_all;
        $pay_date=$request->pay_date;
        $paid_no=$request->paid_no;
        $notes=$request->notes;
        $account_payment=$request->account_payment;

        $period_year = date('Y');
        $period_month = date('m');
        $rabcon = new RabController();
        $bill_no = $rabcon->generateTransactionNo('PAID_DEBT', $period_year, $period_month, $this->site_id );
        $paid_sppl=null;
        try
        {
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . 'inv/base/PaidDebt']);
            $reqBody = [
                'headers' => $headers,
                'json' => [
                    'm_supplier_id' => $request->input('supplier_id'),
                    'no' => $bill_no,
                    'notes' => $notes,
                    'wop' => $request->input('wop'),
                    'bank_number' => $request->input('bank_number'),
                    'ref_code' => $request->input('ref_code'),
                    'id_bank' => $request->input('id_bank'),
                    'amount' => $total,
                    'paid_date' => $pay_date,
                    'site_id' => $this->site_id,
                ]
            ]; 
            $response = $client->request('POST', '', $reqBody); 
            $body = $response->getBody();
            $content = $body->getContents();
            $response_array = json_decode($content,TRUE);
            $paid_sppl=$response_array['data'];
        } catch(RequestException $exception) {
        }
        
        $temp_total=$total;
        $temp_amount=array();
        $temp_no_bill='';
        foreach ($bill_id as $key => $value) {
            // $bill_detail=$this->getDebtDetailJson($value);
            // $amount=$bill_detail['total'];
            foreach ($debt_id as $k => $v) {
                if ($value == $v) {
                    $amount=$amount_bill[$k];
                }
            }
            $cek_amount=$amount;
            $saving_amount=($key == (count($bill_id)-1) ? $temp_total : $amount);
            if ($temp_total >= $amount) {
                $temp_total-=$amount;//untuk mengurangi sisa total yang dibayar dengan total tagihan
            }else{
                $amount=$temp_total;//total tagihan di ganti dengan sisa yang dibayar jika sisa kurang dari total tagihan
                $temp_total=0;
            }
            $get_bill=DB::table('debts')->where('id', $value)->first();
            // DB::table('debts')->where('id', $value)->update(array('paid_no' => $paid_no[$key]));//update no tagihan
            $temp_no_bill.=$get_bill->no.', ';
            $temp_amount[]=array('amount' => (($cek_amount - $amount) <= 10000 ? $cek_amount : $amount));
            $supplier_d=null;
            try
            {
                $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . 'inv/base/DebtD']); 
                $reqBody = [
                    'headers' => $headers,
                'json' => [
                        'debt_id' => $value,
                        'akun_id' => $account_payment,
                        'amount' => $saving_amount,
                        'notes' => $request->notes,
                        'id_bank' => $request->id_bank,
                        'bank_number' => $request->bank_number,
                        'atas_nama' => $request->atas_nama,
                        'pay_date' => $request->pay_date,
                        'wop' => $request->wop,
                    ]
                ]; 

                $response = $client->request('POST', '', $reqBody); 
                $body = $response->getBody();
                $content = $body->getContents();
                $response_array = json_decode($content,TRUE);
                $debt_d=$response_array['data'];
            } catch(RequestException $exception) {
            }

            try
            {
                $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . 'inv/base/PaidDebtD']);
                $reqBody = [
                    'headers' => $headers,
                'json' => [
                        'paid_debt_id'  => $paid_sppl['id'],
                        'debt_id' => $value,
                        'debt_d_id' => $debt_d['id'],
                        'amount' => $saving_amount,
                    ]
                ]; 
                $response = $client->request('POST', '', $reqBody); 
                $body = $response->getBody();
                $content = $body->getContents();
                $response_array = json_decode($content,TRUE);
            } catch(RequestException $exception) {
            }
            if (($cek_amount - $amount) <= 10000) {
                DB::table('debts')->where('id', $value)->update(array('is_paid' => true));
            }
        }
        if (($total_all - $total) > 10000) {
            // $total_all=$total;
            $paid_more=0;
            $paid_less=0;
        }
        $input_jurnal=array(
            'paid_debt_id'  => $paid_sppl['id'],
            'm_supplier_id' => $request->input('supplier_id'),
            'total' => $temp_amount,
            'total_all' => $total,
            'paid_more' => $paid_more,
            'paid_less' => $paid_less,
            'user_id'   => $this->user_id,
            'akun'      => $request->account_payment,
            'deskripsi'     => 'Pembayaran Hutang No '.$paid_sppl['no'].' dari Tagihan Hutang No '.rtrim($temp_no_bill, ', '),
            'tgl'       => $request->pay_date,
            'no_bkk'    => $request->bkk,
            'location_id'   => $this->site_id
        );
        $this->journalPaidSupplier($input_jurnal);
        return redirect('payment/paid_debt');
    }
    private function journalPaidSupplier($data){
        $data_trx=array(
            'deskripsi'     => $data['deskripsi'],
            'location_id'     => $data['location_id'],
            'tanggal'       => $data['tgl'],
            'user_id'       => $data['user_id'],
            'paid_debt_id'  => $data['paid_debt_id'],
            'm_supplier_id'  => $data['m_supplier_id'],
        );
        $insert=DB::table('tbl_trx_akuntansi')->insert($data_trx);
        if ($insert) {
            $id_last=DB::table('tbl_trx_akuntansi')->max('id_trx_akun');

            foreach ($data['total'] as $key => $value) {
                $lawan=array(
                    'id_trx_akun'   => $id_last,
                    'id_akun'       => 55,
                    'jumlah'        => $value['amount'],
                    'tipe'          => "DEBIT",
                    'keterangan'    => 'akun',
                );
                DB::table('tbl_trx_akuntansi_detail')->insert($lawan);
            }
            $no=$data['no_bkk'];
            if ($no == '') {
                $acccon = new AkuntanController();
                $no=$acccon->createNo($data['akun'], "KREDIT");
            }
            $akun=array(
                'id_trx_akun'   => $id_last,
                'id_akun'       => $data['akun'],
                'jumlah'        => $data['total_all'],
                'tipe'          => "KREDIT",
                'keterangan'    => 'lawan',
                'no'            => $no
            );
            DB::table('tbl_trx_akuntansi_detail')->insert($akun);
            if($data['akun'] == 24 || $data['akun'] == 101){
                $acccon = new AkuntanController();
                $warehouse_id=$data['akun'] == 24 ? 2 : 3;
                $acccon->countSaldoCash($this->site_id, $warehouse_id, "out", $data['total_all']);
            }
            if ($data['paid_less'] > 0) {
                $paid_less=array(
                    'id_trx_akun'   => $id_last,
                    'id_akun'       => 166,
                    'jumlah'        => $data['paid_less'],
                    'tipe'          => "KREDIT",
                    'keterangan'    => 'akun',
                );
                DB::table('tbl_trx_akuntansi_detail')->insert($paid_less);
            }
            if ($data['paid_more'] > 0) {
                $paid_more=array(
                    'id_trx_akun'   => $id_last,
                    'id_akun'       => 163,
                    'jumlah'        => $data['paid_more'],
                    'tipe'          => "DEBIT",
                    'keterangan'    => 'lawan',
                );
                DB::table('tbl_trx_akuntansi_detail')->insert($paid_more);
            }
        }
    }
    public function getPaidDebtJson(){
        $query=DB::table('paid_debts')
                    ->select('paid_debts.*', 'm_suppliers.name')        
                    ->join('m_suppliers', 'm_suppliers.id', 'paid_debts.m_supplier_id')
                    ->where('site_id', $this->site_id)
                    ->get();
        $data=DataTables::of($query)->make(true);

        return $data;
    }
    public function getPaidDebtDetailJson($id){
        $query=DB::table('paid_debt_ds')
                    ->select('paid_debt_ds.*', 'debts.no')        
                    ->join('debts', 'debts.id', 'paid_debt_ds.debt_id')
                    ->where('paid_debt_id', $id)
                    ->get();
        $data=array(
            'data'  => $query
        );

        return $data;
    }
    public function deleteDebt($id)
    {
        $debt=DB::table('tbl_trx_akuntansi')->where('debt_id', $id)->first();
        if($debt != null){
            DB::table('tbl_trx_akuntansi')->where('debt_id', $id)->delete();
            DB::table('debts')->where('id', $id)->delete();
        }
        return redirect('payment/debt');
    }
    public function exportDebt(Request $request) {
        $status=$request->status;
        $query=DB::table('debts')
                    ->join('m_suppliers as ms', 'ms.id', 'debts.m_supplier_id')
                    ->select('debts.*', 'ms.name', DB::raw('coalesce((select count(debt_id) from debt_ds where debt_id=debts.id), 0) as total_paid'))
                    ->where('site_id', $this->site_id)
                    ->whereNull('debts.deleted_at')
                    ->orderby('debt_date');

        if($status != null && $status != 'all'){
            $query->where('is_paid', $status);
        }
        
        $query=$query->get();
        $data=array(
            'data'  => $query
        );
        return Excel::download(new DebtExport($data), 'hutang usaha.xlsx');
    }
    public function listDetailAll(){
        $query=DB::table('paid_debt_ds as pds')
                ->join('paid_debts as pd', 'pd.id', 'pds.paid_debt_id')
                ->join('debts as d', 'd.id', 'pds.debt_id')
                ->join('m_suppliers as ms', 'ms.id', 'pd.m_supplier_id')
                ->select('pd.no as paid_no', 'd.no as debt_no', 'ms.name', 'pd.paid_date', 'pds.amount')
                ->get();
        $data=DataTables::of($query)
                    ->make(true);   
        return $data;       
    }
    public function billVendor()
    {
        $is_error = false;
        $error_message = '';  

        $data = array(
            'error' => array(
                'is_error' => $is_error,
                'error_message' => $error_message
            ),
        );
        
        return view('pages.crm.payment.bill_vendor_list', $data);
    }
    public function createBillVendor()
    {
        $suppliers = null;
        try
        {
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . '/crm/base/MSupplier']);  
            $response = $client->request('GET', '', ['headers' => $headers]);  
            $body = $response->getBody();
            $content = $body->getContents();
            $response_array = json_decode($content,TRUE);
            $suppliers = $response_array['data'];
        } catch(RequestException $exception) {      
        }

        try
        {
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            if ($this->site_id == null) {
                $client1 = new Client(['base_uri' => $this->base_api_url . 'order/option_install_order']); 
            }else{
                $client1 = new Client(['base_uri' => $this->base_api_url . 'order/option_install_order?site_id='.$this->site_id]); 
            } 
            $response1 = $client1->request('GET', '', ['headers' => $headers]); 
            $body1 = $response1->getBody();
            $content1 = $body1->getContents();
            $response_array1 = json_decode($content1,TRUE);

            $response1 = $content1;  
            $install_order = $response_array1['data'];
        } catch(RequestException $exception) {
            
        }

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
        foreach($install_order as $key => $row){
            $order=DB::table('orders')->where('id', $row['order_id'])->first();
            $install_order[$key]['spk_number']=$order != null ? $order->spk_number : '';
        }
        $data=array(
            'order' => $order_list,
            'install_order' => $install_order,
            'suppliers' => $suppliers
        );
        return view('pages.crm.payment.create_bill_vendor', $data);
    }
    public function saveBillVendor(Request $request){
        $total=$this->currency($request->input('total'));
        $sub_total=$request->input('sub_total');
        $period_year = date('Y');
        $period_month = date('m');
        $rabcon = new RabController();
        $bill_no = $rabcon->generateTransactionNo('BILL', $period_year, $period_month, $this->site_id );
        $bill_cust=array();
        try
        {
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . 'inv/base/BillVendor']);
            $reqBody = [
                'headers' => $headers,
                'json' => [
                    'order_id' => $request->input('order_id'),
                    'install_order_id' => $request->input('install_order_id'),
                    'm_supplier_id' => $request->input('suppl_single'),
                    'bill_no' => $request->input('bill_no'),
                    'amount' => $this->currency($request->input('total')),
                    'due_date' => $request->input('due_date'),
                    'create_date'   => $request->date_create,
                    'no'  => $bill_no,
                    'notes' => $request->input('description'),
                    'with_pph' => $request->input('with_pph') ? 1 : 0,
                ]
            ]; 
            
            $response = $client->request('POST', '', $reqBody); 
            $body = $response->getBody();
            $content = $body->getContents();
            $response_array = json_decode($content,TRUE);
            $bill_cust=$response_array['data'];
        } catch(RequestException $exception) {
        }
        $order_id=$request->order_id;
        $install_order=DB::table('install_orders')->where('id', $request->input('install_order_id'))->first();
        if($install_order != null){
            $order_id=$install_order->order_id;
        }
        
        $account_project=DB::table('account_projects')->where('order_id', $order_id)->first();
        
        $input_jurnal=array(
            'bill_vendor_id'   => $bill_cust['id'],
            'total' => $this->currency($request->input('total')),
            'ppn' => $this->currency($request->input('ppn_bill')),
            'pph' => $this->currency($request->input('pph_bill')),
            'user_id'   => $this->user_id,
            'm_supplier_id' => $request->input('suppl_single'),
            'deskripsi'     => 'Pembuatan Tagihan Pengadaan / Pemasangan No '.$bill_no,
            'tgl'       => $request->date_create,
            'lawan'      => $account_project != null ? $account_project->cost_service_id : 108,
            'akun'      => 147,
            'location_id'   => $this->site_id
        );
        $this->journalBillVendor($input_jurnal);
        
        return redirect('payment/bill_vendor');
    }
    private function journalBillVendor($data){
        $data_trx=array(
            'deskripsi'     => $data['deskripsi'],
            'location_id'     => $data['location_id'],
            'tanggal'       => $data['tgl'],
            'user_id'       => $data['user_id'],
            'm_supplier_id'   => $data['m_supplier_id'],
            'bill_vendor_id'   => $data['bill_vendor_id'],
        );
        $insert=DB::table('tbl_trx_akuntansi')->insert($data_trx);
        if ($insert) {
            $id_last=DB::table('tbl_trx_akuntansi')->max('id_trx_akun');
            $akun=array(
                'id_trx_akun'   => $id_last,
                'id_akun'       => $data['lawan'],
                'jumlah'        => $data['total'],
                'tipe'          => "DEBIT",
                'keterangan'    => 'akun',
            );
            DB::table('tbl_trx_akuntansi_detail')->insert($akun);
            
            if ($data['ppn'] != 0) {
                $ppn=array(
                    'id_trx_akun'   => $id_last,
                    'id_akun'       => 133,
                    'jumlah'        => $data['ppn'],
                    'tipe'          => "DEBIT",
                    'keterangan'    => 'akun',
                );
                DB::table('tbl_trx_akuntansi_detail')->insert($ppn);
            }
            if ($data['pph'] != 0) {
                $ppn=array(
                    'id_trx_akun'   => $id_last,
                    'id_akun'       => 135,
                    'jumlah'        => $data['pph'],
                    'tipe'          => "KREDIT",
                    'keterangan'    => 'akun',
                );
                DB::table('tbl_trx_akuntansi_detail')->insert($ppn);
            }
            $lawan=array(
                'id_trx_akun'   => $id_last,
                'id_akun'       => $data['akun'],
                'jumlah'        => ($data['total'] + $data['ppn']) - $data['pph'],
                'tipe'          => "KREDIT",
                'keterangan'    => 'lawan',
            );
            DB::table('tbl_trx_akuntansi_detail')->insert($lawan);
            
        }
    }
    public function billVendorJson()
    {
        $query=DB::table('bill_vendors as bv')
                    ->join('m_suppliers as ms', 'ms.id', 'bv.m_supplier_id')
                    ->leftJoin('orders as o', 'o.id', 'bv.order_id')
                    ->leftJoin('install_orders as io', 'io.id', 'bv.install_order_id')
                    ->select('bv.*', 'ms.name as supplier_name', 'o.order_no', 'io.no as install_order_no', 'io.order_id as order_id_io', 'o.spk_number')
                    ->get();
        foreach($query as $row){
            if($row->order_id_io != null){
                $order=DB::table('orders')->where('id', $row->order_id_io)->first();
                $row->spk_number=$order->spk_number;
            }
            $pph=($row->with_pph == true ? $row->amount * 0.020 : 0);
            $row->amount=($row->amount + (($row->amount * 0.1) - $pph));
        }
        $data=DataTables::of($query)
                    ->make(true);          

        return $data;
    }
    public function paidBillVendor(){
        return view('pages.crm.payment.payment_bill_vendor');
    }
    public function formPaidBillVendor(){
        $suppliers = null;
        try
        {
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . '/crm/base/MSupplier']);  
            $response = $client->request('GET', '', ['headers' => $headers]); 
            $body = $response->getBody();
            $content = $body->getContents();
            $response_array = json_decode($content,TRUE);
            $suppliers = $response_array['data'];
        } catch(RequestException $exception) {      
        }
        $data=array(
            'suppliers'  => $suppliers,
            'list_bank' => DB::table('list_bank')->get()
        );
        return view('pages.crm.payment.form_paid_bill_vendor', $data);
    }
    public function getNoBillVendorJson($id){
        $query=DB::table('bill_vendors')->where('m_supplier_id', $id)->where('is_paid', false)->get();
        foreach ($query as $key => $value) {
            $detail=$this->getBillVendorDetailJson($value->id);
            $value->paid=$detail['paid'];
            $pph=($value->with_pph == true ? $value->amount * 0.020 : 0);
            $value->amount=($value->amount + (($value->amount * 0.1) - $pph));
        }
        $data=array(
            'data'  => $query
        );
        return $data;
    }
    public function getBillVendorDetailJson($id){
        $query=DB::table('bill_vendors')
                        ->where('id', $id)
                        ->first();
        $total=$query->amount;
        $paid=DB::table('paid_bill_vendor_ds')
                        ->where('bill_vendor_id', $id)
                        ->select(DB::raw('COALESCE(SUM(amount),0) as amount'))
                        ->first();
        $data=array(
            'data'  => $query,
            'total' => $total,
            'paid'  => $paid->amount
        );
        return $data;
    }
    public function saveMultiplePaidBillVendor(Request $request) {
        
        $bill_id=$request->check_id;
        $bill_vendor_id=$request->bill_vendor_id;
        $amount_bill=$request->amount;
        
        $total=$this->currency($request->total);
        $paid_more=$this->currency($request->paid_more);
        $paid_less=$this->currency($request->paid_less);
        $total_all=$request->total_all;
        $pay_date=$request->pay_date;
        $paid_no=$request->paid_no;
        $notes=$request->notes;
        $account_payment=$request->account_payment;

        $period_year = date('Y');
        $period_month = date('m');
        $rabcon = new RabController();
        $bill_no = $rabcon->generateTransactionNo('PAID_BILL', $period_year, $period_month, $this->site_id );
        $paid_sppl=null;
        try
        {
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . 'inv/base/PaidBillVendor']);
            $reqBody = [
                'headers' => $headers,
                'json' => [
                    'm_supplier_id' => $request->input('supplier_id'),
                    'no' => $bill_no,
                    'notes' => $notes,
                    'wop' => $request->input('wop'),
                    'bank_number' => $request->input('bank_number'),
                    'ref_code' => $request->input('ref_code'),
                    'id_bank' => $request->input('id_bank'),
                    'amount' => $total,
                    'paid_date' => $pay_date,
                    'site_id' => $this->site_id,
                ]
            ]; 
            $response = $client->request('POST', '', $reqBody); 
            $body = $response->getBody();
            $content = $body->getContents();
            $response_array = json_decode($content,TRUE);
            $paid_sppl=$response_array['data'];
        } catch(RequestException $exception) {
        }
        
        $temp_total=$total;
        $temp_amount=array();
        $temp_no_bill='';
        foreach ($bill_id as $key => $value) {
            // $bill_detail=$this->getDebtDetailJson($value);
            // $amount=$bill_detail['total'];
            foreach ($bill_vendor_id as $k => $v) {
                if ($value == $v) {
                    $amount=$amount_bill[$k];
                }
            }
            $cek_amount=$amount;
            $saving_amount=($key == (count($bill_id)-1) ? $temp_total : $amount);
            if ($temp_total >= $amount) {
                $temp_total-=$amount;//untuk mengurangi sisa total yang dibayar dengan total tagihan
            }else{
                $amount=$temp_total;//total tagihan di ganti dengan sisa yang dibayar jika sisa kurang dari total tagihan
                $temp_total=0;
            }
            $get_bill=DB::table('bill_vendors')->where('id', $value)->first();
            // DB::table('debts')->where('id', $value)->update(array('paid_no' => $paid_no[$key]));//update no tagihan
            $temp_no_bill.=$get_bill->no.', ';
            $temp_amount[]=array('amount' => (($cek_amount - $amount) <= 10000 ? $cek_amount : $amount));
            $supplier_d=null;

            try
            {
                $headers = [
                    'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                    'Accept'        => 'application/json',
                ];
                $client = new Client(['base_uri' => $this->base_api_url . 'inv/base/PaidBillVendorD']);
                $reqBody = [
                    'headers' => $headers,
                    'json' => [
                        'paid_bill_vendor_id'  => $paid_sppl['id'],
                        'bill_vendor_id' => $value,
                        'amount' => $saving_amount,
                    ]
                ]; 
                $response = $client->request('POST', '', $reqBody); 
                $body = $response->getBody();
                $content = $body->getContents();
                $response_array = json_decode($content,TRUE);
            } catch(RequestException $exception) {
            }
            if (($cek_amount - $amount) <= 10000) {
                DB::table('bill_vendors')->where('id', $value)->update(array('is_paid' => true));
            }
        }
        if (($total_all - $total) > 10000) {
            // $total_all=$total;
            $paid_more=0;
            $paid_less=0;
        }
        $input_jurnal=array(
            'paid_bill_vendor_id'  => $paid_sppl['id'],
            'm_supplier_id' => $request->input('supplier_id'),
            'total' => $temp_amount,
            'total_all' => $total,
            'paid_more' => $paid_more,
            'paid_less' => $paid_less,
            'user_id'   => $this->user_id,
            'akun'      => $request->account_payment,
            'deskripsi'     => 'Pembayaran Tagihan Pengadaan/Pemasangan No '.$paid_sppl['no'].' dari Tagihan No '.rtrim($temp_no_bill, ', '),
            'tgl'       => $request->pay_date,
            'no_bkk'    => $request->bkk,
            'location_id'   => $this->site_id
        );
        $this->journalPaidBillVendor($input_jurnal);
        return redirect('payment/paid_bill_vendor');
    }
    private function journalPaidBillVendor($data){
        $data_trx=array(
            'deskripsi'     => $data['deskripsi'],
            'location_id'     => $data['location_id'],
            'tanggal'       => $data['tgl'],
            'user_id'       => $data['user_id'],
            'paid_bill_vendor_id'  => $data['paid_bill_vendor_id'],
            'm_supplier_id'  => $data['m_supplier_id'],
        );
        $insert=DB::table('tbl_trx_akuntansi')->insert($data_trx);
        if ($insert) {
            $id_last=DB::table('tbl_trx_akuntansi')->max('id_trx_akun');

            foreach ($data['total'] as $key => $value) {
                $lawan=array(
                    'id_trx_akun'   => $id_last,
                    'id_akun'       => 147,
                    'jumlah'        => $value['amount'],
                    'tipe'          => "DEBIT",
                    'keterangan'    => 'akun',
                );
                DB::table('tbl_trx_akuntansi_detail')->insert($lawan);
            }
            $no=$data['no_bkk'];
            if ($no == '') {
                $acccon = new AkuntanController();
                $no=$acccon->createNo($data['akun'], "KREDIT");
            }
            $akun=array(
                'id_trx_akun'   => $id_last,
                'id_akun'       => $data['akun'],
                'jumlah'        => $data['total_all'],
                'tipe'          => "KREDIT",
                'keterangan'    => 'lawan',
                'no'            => $no
            );
            DB::table('tbl_trx_akuntansi_detail')->insert($akun);
            if($data['akun'] == 24 || $data['akun'] == 101){
                $acccon = new AkuntanController();
                $warehouse_id=$data['akun'] == 24 ? 2 : 3;
                $acccon->countSaldoCash($this->site_id, $warehouse_id, "out", $data['total_all']);
            }
            if ($data['paid_less'] > 0) {
                $paid_less=array(
                    'id_trx_akun'   => $id_last,
                    'id_akun'       => 166,
                    'jumlah'        => $data['paid_less'],
                    'tipe'          => "KREDIT",
                    'keterangan'    => 'akun',
                );
                DB::table('tbl_trx_akuntansi_detail')->insert($paid_less);
            }
            if ($data['paid_more'] > 0) {
                $paid_more=array(
                    'id_trx_akun'   => $id_last,
                    'id_akun'       => 163,
                    'jumlah'        => $data['paid_more'],
                    'tipe'          => "DEBIT",
                    'keterangan'    => 'lawan',
                );
                DB::table('tbl_trx_akuntansi_detail')->insert($paid_more);
            }
        }
    }
    public function getPaidBillVendorJson(){
        $query=DB::table('paid_bill_vendors')
                    ->select('paid_bill_vendors.*', 'm_suppliers.name')        
                    ->join('m_suppliers', 'm_suppliers.id', 'paid_bill_vendors.m_supplier_id')
                    ->where('site_id', $this->site_id)
                    ->get();
        $data=DataTables::of($query)->make(true);

        return $data;
    }
    public function getPaidBillVendorDetailJson($id){
        $query=DB::table('paid_bill_vendor_ds')
                    ->select('paid_bill_vendor_ds.*', 'bill_vendors.no')        
                    ->join('bill_vendors', 'bill_vendors.id', 'paid_bill_vendor_ds.bill_vendor_id')
                    ->where('paid_bill_vendor_id', $id)
                    ->get();
        $data=array(
            'data'  => $query
        );

        return $data;
    }
    public function listPaidBillVendorDetailAll(){
        $query=DB::table('paid_bill_vendor_ds as pds')
                ->join('paid_bill_vendors as pd', 'pd.id', 'pds.paid_bill_vendor_id')
                ->join('bill_vendors as d', 'd.id', 'pds.bill_vendor_id')
                ->join('m_suppliers as ms', 'ms.id', 'pd.m_supplier_id')
                ->select('pd.no as paid_no', 'd.no as bill_vendor_no', 'ms.name', 'pd.paid_date', 'pds.amount')
                ->get();
        $data=DataTables::of($query)
                    ->make(true);   
        return $data;       
    }
}
