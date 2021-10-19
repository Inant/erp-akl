<?php

namespace App\Http\Controllers\INV;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Carbon\Carbon;
use DB;
use Yajra\DataTables\Facades\DataTables;
use App\Http\Controllers\RAB\RabController;
use App\Exports\PurchaseExport;
use App\Exports\PurchaseAssetExport;
use App\Exports\PurchaseServiceExport;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class PoController extends Controller
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
            $this->user_id = auth()->user()['id'];
            $this->role_id = auth()->user()['role_id'];
            return $next($request);
        });

        $this->base_api_url = env('API_URL');
    }

    public function poKonstruksiIndex()
    {
        $is_error = false;
        $error_message = '';  

        $data = array(
            'error' => array(
                'is_error' => $is_error,
                'error_message' => $error_message
            )
        );
        
        return view('pages.inv.purchase_order.po_konstruksi_list', $data);
    }

    public function poKhususIndex()
    {
        $is_error = false;
        $error_message = '';  

        $data = array(
            'error' => array(
                'is_error' => $is_error,
                'error_message' => $error_message
            )
        );
        
        return view('pages.inv.purchase_order.po_khusus_list', $data);
    }

    public function poKhususApprovalIndex()
    {
        $is_error = false;
        $error_message = '';  

        $data = array(
            'error' => array(
                'is_error' => $is_error,
                'error_message' => $error_message
            )
        );
        
        return view('pages.inv.purchase_order.po_khusus_approval_list', $data);
    }

    public function poKhususApproval($id)
    {
        $is_error = false;
        $error_message = '';  

        $purchase = $this->getPurchaseById($id);
        $purchase = json_decode($purchase, TRUE);
        $purchase = $purchase['data'];

        $data = array(
            'error' => array(
                'is_error' => $is_error,
                'error_message' => $error_message
            ),
            'purchase' => $purchase
        );

        // echo json_encode($purchase);
        
        return view('pages.inv.purchase_order.po_khusus_approval_detail', $data);
    }

    public function poKhususApprovalApprove($id){
        $is_error = false;
        $error_message = '';  

        $purchase = $this->getPurchaseById($id);
        $purchase = json_decode($purchase, TRUE);
        $purchase = $purchase['data'];

        $data = array(
            'error' => array(
                'is_error' => $is_error,
                'error_message' => $error_message
            ),
            'purchase' => $purchase
        );

        // echo json_encode($purchase);
        
        return view('pages.inv.purchase_order.po_khusus_approval_approve', $data);
    }

    public function poKhususApprovalApprovePost(Request $request)
    {
        $purchase_id = $request->post('purchase_id');
        $apv_decision = $request->post('apv_decision');

        $purchase_approval = $this->getPurchaseApprovalByPurchaseId($purchase_id);
        $purchase_approval = json_decode($purchase_approval, TRUE);
        $purchase_approval = $purchase_approval['data'];

        if($purchase_approval == null)
        {
            try
                    {
                        $headers = [
                            'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                            'Accept'        => 'application/json',
                        ];
                        $client = new Client(['base_uri' => $this->base_api_url . '/inv/base/PurchaseApproval']);
                        $reqBody = [
                            'headers' => $headers,
                            'json' => [
                                'purchase_id' => $purchase_id,
                                'is_apv' => true,
                                'apv_date' => Carbon::now()->toDateString(),
                                'apv_by' => $this->username,
                                'apv_decision' => $apv_decision
                                ]
                            ]; 
                            $response = $client->request('POST', '', $reqBody); 
                            $body = $response->getBody();
                            $content = $body->getContents();
                            $response_array = json_decode($content,TRUE);
                    } catch(RequestException $exception) {
                    }
        }

        $notification = array(
            'message' => 'Purchase Order succesfully approve',
            'alert-type' => 'success'
        );

        return redirect('po_spesial_approval')->with($notification);
    }

    public function poKhususApprovalDecline($id){
        $purchase_approval = $this->getPurchaseApprovalByPurchaseId($id);
        $purchase_approval = json_decode($purchase_approval, TRUE);
        $purchase_approval = $purchase_approval['data'];

        if($purchase_approval == null)
        {
            try
                    {
                        $headers = [
                            'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                            'Accept'        => 'application/json',
                        ];
                        $client = new Client(['base_uri' => $this->base_api_url . '/inv/base/PurchaseApproval']);
                        $reqBody = [
                            'headers' => $headers,
                            'json' => [
                                'purchase_id' => $id,
                                'is_apv' => false,
                                'apv_date' => Carbon::now()->toDateString(),
                                'apv_by' => $this->username
                                ]
                            ]; 
                            $response = $client->request('POST', '', $reqBody); 
                            $body = $response->getBody();
                            $content = $body->getContents();
                            $response_array = json_decode($content,TRUE);
                    } catch(RequestException $exception) {
                    }
        }

        $notification = array(
            'message' => 'Purchase Order succesfully decline',
            'alert-type' => 'success'
        );

        return redirect('po_spesial_approval')->with($notification);
    }

    public function printPO($id) {
        $purchase = null;
        $purchase_d = null;

        // Get Header
        try
        {
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . '/inv/base/Purchase/' . $id]);  
            $response = $client->request('GET', '', ['headers' => $headers]); 
            $body = $response->getBody();
            $content = $body->getContents();
            $response_array = json_decode($content,TRUE);

            $purchase = $response_array['data'];         
        } catch(RequestException $exception) { 
        }

        // Get Sites
        $m_suppliers = null;
        if ($purchase != null) {
            try
            {
                $headers = [
                    'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                    'Accept'        => 'application/json',
                ];
                $client = new Client(['base_uri' => $this->base_api_url . '/inv/base/MSupplier/' . $purchase['m_supplier_id']]);  
                $response = $client->request('GET', '', ['headers' => $headers]); 
                $body = $response->getBody();
                $content = $body->getContents();
                $response_array = json_decode($content,TRUE);

                $m_suppliers = $response_array['data'];         
            } catch(RequestException $exception) {
            }
        }

        $purchase['m_suppliers'] = $m_suppliers;

        try
        {
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . '/inv/po/purchase_d_by_purchase_id/'.$id]);  
            $response = $client->request('GET', '', ['headers' => $headers]); 
            $body = $response->getBody();
            $content = $body->getContents();
            $response_array = json_decode($content,TRUE);

            $purchase_d = $response_array['data'];         
        } catch(RequestException $exception) {
            
        }    
        $request_id=DB::table('users')->where('id', $purchase['user_id'])->first();
        $director_id=DB::table('users')->where('id', $purchase['director_id'])->first();
        $manager_id=DB::table('users')->where('id', $purchase['manager_id'])->first();
        
        $data = array(
            'purchase' => $purchase,
            'purchase_d' => $purchase_d,
            'requests'  => $request_id,
            'director'  => $director_id,
            'manager'  => $manager_id,
        );
        return view('pages.inv.purchase_order.print_purchase_order', $data);
    }

    public function getPOKonstruksiJson(Request $request){
        $date1=$request->date ? $request->date : date('Y-m-d');
        $date2=$request->date2 ? $request->date2 :date('Y-m-d');

        if($this->site_id != null) {
            $datas = DB::table('purchases')->where('is_special', false)
                    ->where('site_id', $this->site_id)
                    ->where('purchase_date', '>=', $date1)
                    ->where('purchase_date', '<=', $date2)
                    ->orderByRaw('acc_director_date desc nulls last')
                    ->get();
        } else {
            $datas = DB::table('purchases')->where('is_special', false)
                    ->orderByRaw('acc_director_date desc nulls last')
                    ->where('purchase_date', '>=', $date1)
                    ->where('purchase_date', '<=', $date2)
                    ->get();
        }

        foreach($datas as $data){
            if(isset($data->m_supplier_id)){
                $data->m_suppliers = DB::table('m_suppliers')->where('id', $data->m_supplier_id)->first();
            }else{
                $data->m_suppliers = null;
            }
        }
        $data=DataTables::of($datas)
                                ->make(true);             

        return $data;
    }

    public function getPOKhususJson(){
        $response = null;
        try
        {
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . '/inv/po/po_khusus']);  
            $response = $client->request('GET', '', ['headers' => $headers]); 
            $body = $response->getBody();
            $content = $body->getContents();
            $response_array = json_decode($content,TRUE);

            $response = $content;         
        } catch(RequestException $exception) {
            
        }    

        return $response;
    }

    public function getPOKhususApprovalJson(){
        $response = null;
        try
        {
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . '/inv/po/po_khusus_approval']);  
            $response = $client->request('GET', '', ['headers' => $headers]); 
            $body = $response->getBody();
            $content = $body->getContents();
            $response_array = json_decode($content,TRUE);

            $response = $content;         
        } catch(RequestException $exception) {
            
        }    

        return $response;
    }

    public function getPOKhususPembelianKhususJson(){
        $response = null;
        try
        {
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . '/inv/po/pembelian_khusus']);  
            $response = $client->request('GET', '', ['headers' => $headers]); 
            $body = $response->getBody();
            $content = $body->getContents();
            $response_array = json_decode($content,TRUE);

            $response = $content;         
        } catch(RequestException $exception) {
            
        }    

        return $response;
    }

    public function getPODetailJson($id){
        $response = null;
        try
        {
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . '/inv/po/purchase_d_by_purchase_id/'.$id]);  
            $response = $client->request('GET', '', ['headers' => $headers]); 
            $body = $response->getBody();
            $content = $body->getContents();
            $response_array = json_decode($content,TRUE);

            $response = $content;         
        } catch(RequestException $exception) {
            
        }    

        return $response;
    }

    public function getPurchaseById($id){
        $response = null;
        try
        {
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . '/inv/purchase/'.$id]);  
            $response = $client->request('GET', '', ['headers' => $headers]); 
            $body = $response->getBody();
            $content = $body->getContents();
            $response_array = json_decode($content,TRUE);

            $response = $content;         
        } catch(RequestException $exception) {
            
        }    

        return $response;
    }

    public function getPurchaseApprovalByPurchaseId($id){
        $response = null;
        try
        {
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . '/inv/purchase_approval/'.$id]);  
            $response = $client->request('GET', '', ['headers' => $headers]); 
            $body = $response->getBody();
            $content = $body->getContents();
            $response_array = json_decode($content,TRUE);

            $response = $content;         
        } catch(RequestException $exception) {
            
        }    

        return $response;
    }

    public function poATKIndex()
    {
        $is_error = false;
        $error_message = '';  

        $data = array(
            'error' => array(
                'is_error' => $is_error,
                'error_message' => $error_message
            )
        );
        
        return view('pages.inv.purchase_order.po_atk_list', $data);
    }
    public function getPOAtkJson(Request $request){
        $date1=$request->date ? $request->date : date('Y-m-d');
        $date2=$request->date2 ? $request->date2 :date('Y-m-d');

        if($this->site_id != null) {
            $datas = DB::table('purchase_assets')->where('is_special', false)
                    ->where('site_id', $this->site_id)
                    ->where('purchase_date', '>=', $date1)
                    ->where('purchase_date', '<=', $date2)
                    ->orderBy('id', 'desc')
                    ->get();
        } else {
            $datas = DB::table('purchase_assets')->where('is_special', false)
                    ->orderBy('id', 'desc')
                    ->where('purchase_date', '>=', $date1)
                    ->where('purchase_date', '<=', $date2)
                    ->get();
        }

        foreach($datas as $data){
            $data->m_suppliers = DB::table('m_suppliers')->where('id', $data->m_supplier_id)->first();
        }
        $data=DataTables::of($datas)
                                ->make(true);  

        return $data;
    }
    public function getPOATKDetailJson($id){
        $response = null;
        try
        {
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . '/inv/po/purchase_d_by_purchase_asset_id/'.$id]);  
            $response = $client->request('GET', '', ['headers' => $headers]); 
            $body = $response->getBody();
            $content = $body->getContents();
            $response_array = json_decode($content,TRUE);

            $response = $content;         
        } catch(RequestException $exception) {
            
        }    

        return $response;
    }
    public function printPOATK($id) {
        $purchase = null;
        $purchase_d = null;

        // Get Header
        try
        {
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . '/inv/base/PurchaseAsset/' . $id]);  
            $response = $client->request('GET', '', ['headers' => $headers]); 
            $body = $response->getBody();
            $content = $body->getContents();
            $response_array = json_decode($content,TRUE);

            $purchase = $response_array['data'];         
        } catch(RequestException $exception) { 
        }

        // Get Sites
        $m_suppliers = null;
        if ($purchase != null) {
            try
            {
                $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . '/inv/base/MSupplier/' . $purchase['m_supplier_id']]);  
                $response = $client->request('GET', '', ['headers' => $headers]); 
                $body = $response->getBody();
                $content = $body->getContents();
                $response_array = json_decode($content,TRUE);

                $m_suppliers = $response_array['data'];         
            } catch(RequestException $exception) {
            }
        }

        $purchase['m_suppliers'] = $m_suppliers;

        try
        {
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . '/inv/po/purchase_d_by_purchase_asset_id/'.$id]);  
            $response = $client->request('GET', '', ['headers' => $headers]); 
            $body = $response->getBody();
            $content = $body->getContents();
            $response_array = json_decode($content,TRUE);

            $purchase_d = $response_array['data'];         
        } catch(RequestException $exception) {
            
        }    

        $request_id=DB::table('users')->where('id', $purchase['user_id'])->first();
        $director_id=DB::table('users')->where('id', $purchase['director_id'])->first();
        $manager_id=DB::table('users')->where('id', $purchase['manager_id'])->first();
        
        $data = array(
            'purchase' => $purchase,
            'purchase_d' => $purchase_d,
            'requests'  => $request_id,
            'director'  => $director_id,
            'manager'  => $manager_id,
        );
        return view('pages.inv.purchase_order.print_purchase_order', $data);
    }
    public function poKonstruksiWithAOIndex()
    {
        $is_error = false;
        $error_message = '';  

        $data = array(
            'error' => array(
                'is_error' => $is_error,
                'error_message' => $error_message
            )
        );
        
        return view('pages.inv.purchase_order.po_konstruksi_with_ao_list', $data);
    }
    public function getPOKonstruksiWithAOJson(){
        $response = null;
        try
        {
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . '/inv/po/po_konstruksi_ao?site_id='.$this->site_id]);  
            $response = $client->request('GET', '', ['headers' => $headers]); 
            $body = $response->getBody();
            $content = $body->getContents();
            $response_array = json_decode($content,TRUE);

            $response = $content;         
            $data=DataTables::of($response_array['data'])
                                    ->make(true);             
        } catch(RequestException $exception) {
            
        }    

        return $data;
    }

    public function formAccAO($id){
        $is_error = false;
        $error_message = '';  

        $purchase = $this->getPurchaseById($id);
        $purchase = json_decode($purchase, TRUE);
        $purchase = $purchase['data'];
        
        $m_suppliers = null;
        if ($purchase != null) {
            try
            {
                $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . '/inv/base/MSupplier/' . $purchase['m_supplier_id']]);  
                $response = $client->request('GET', '', ['headers' => $headers]); 
                $body = $response->getBody();
                $content = $body->getContents();
                $response_array = json_decode($content,TRUE);

                $m_suppliers = $response_array['data'];         
            } catch(RequestException $exception) {
            }
        }

        $purchase['m_suppliers'] = $m_suppliers;
        
        $data = array(
            'error' => array(
                'is_error' => $is_error,
                'error_message' => $error_message
            ),
            'purchase' => $purchase,
            'role'  => $this->role_id
        );

        // echo json_encode($purchase);
        
        return view('pages.inv.purchase_order.po_ao_acc_form', $data);
    }

    public function formAccAOSignatureHolding(Request $request, $id){
        // dd($request->file);
        try
        {
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . 'upload']);
            $reqBody = [
                'headers' => $headers,
                'multipart' => [
                    [
                        'name'     => 'user_id',
                        'contents' => $this->user_id
                    ],
                    [
                        'Content-type' => 'multipart/form-data',
                        'name'     => 'file',
                        'contents' => fopen($request->file, 'r'),
                        'filename' => 'POACC-HOLDING_signature_'.$id.'.png'
                    ]
                ]
            ]; 
            $response = $client->request('POST', '', $reqBody);
            $body = $response->getBody();
            $content = $body->getContents();
            $response_array = json_decode($content,TRUE);
            $upload_data = $response_array;
            // return $response->getBody();
        } catch(RequestException $exception) {
        }

        try
        {
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . 'inv/base/Purchase/'.$id]);
            $reqBody = [
                'headers' => $headers,
                'json' => [
                    'director_id'   => $this->user_id,
                    'acc_director_date' => date('Y-m-d'),
                    'signature_holding' => $upload_data['data']['path']
                ]
            ]; 
            $response = $client->request('PUT', '', $reqBody); 
            $body = $response->getBody();
            $content = $body->getContents();
            $response_array = json_decode($content,TRUE);
            // echo json_encode($purchase);
        } catch(RequestException $exception) {
        }

        return $content;
        
    }

    public function formAccAOSignatureSupplier(Request $request, $id){
        // dd($request->file);
        try
        {
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . 'upload']);
            $reqBody = [
                'headers' => $headers,
                'multipart' => [
                    [
                        'name'     => 'user_id',
                        'contents' => $this->user_id
                    ],
                    [
                        'Content-type' => 'multipart/form-data',
                        'name'     => 'file',
                        'contents' => fopen($request->file, 'r'),
                        'filename' => 'POACC-SUPPLIER_signature_'.$id.'.png'
                    ]
                ]
            ]; 
            $response = $client->request('POST', '', $reqBody);
            $body = $response->getBody();
            $content = $body->getContents();
            $response_array = json_decode($content,TRUE);
            $upload_data = $response_array;
            // echo json_encode($purchase);
        } catch(RequestException $exception) {
        }

        try
        {
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . 'inv/base/Purchase/'.$id]);
            $reqBody = [
                'headers' => $headers,
                'json' => [
                    'manager_id'   => $this->user_id,
                    'acc_manager_date' => date('Y-m-d'),
                    'signature_supplier' => $upload_data['data']['path']
                ]
            ]; 
            $response = $client->request('PUT', '', $reqBody); 
            $body = $response->getBody();
            $content = $body->getContents();
            $response_array = json_decode($content,TRUE);
            // echo json_encode($purchase);
        } catch(RequestException $exception) {
        }

        return $content;
        
    }

    public function approvePoAO(Request $request){
        $purchase_id=$request->input('purchase_id');
        $purchase_d_id=$request->input('purchase_d_id');
        $pd_id=$request->input('pd_id');
        $m_item_id = $request->post('m_item_id');
        $volume = $request->post('volume');
        $perkiraan_harga_suppl = $request->post('perkiraan_harga_suppl');
        $harga_diskon = $request->post('harga_diskon');
        $diskon = $request->post('diskon');
        $discount_type = $request->post('discount_type');
        $signature_holding = $request->post('signature_holding');
        $signature_supplier = $request->post('signature_supplier');
        
        foreach ($purchase_d_id as $value) {
            $is_deleted=true;
            if ($pd_id != null) {
                for ($i=0; $i < count($pd_id); $i++) { 
                    if ($pd_id[$i] == $value) {
                        $is_deleted=false;
                    }
                }
            }
            if ($is_deleted == true) {
                DB::table('purchase_ds')->where('id', $value)->delete();
            }
        }
        $sum_perkiraan_harga_suppl = 0;
        for($j = 0; $j < count($perkiraan_harga_suppl); $j++){
            $sum_perkiraan_harga_suppl += ($harga_diskon[$j]*$volume[$j]);
        }
        
        try
        {
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . 'inv/base/Purchase/'.$purchase_id]);
            $reqBody = [
                'headers' => $headers,
                'json' => [
                    'spk_number'  => $request->spk_number,
                    'base_price' => $sum_perkiraan_harga_suppl,
                    'discount'  => $diskon,
                    'discount_type' => $discount_type,
                    // 'acc_ao'   => ($signature_holding != '' && $signature_supplier != '' ? 1 : 0)
                    // 'status_payment'   => false,
                    'acc_ao'   => ($request->acc ? 1 : 0)
                ]
            ]; 
            $response = $client->request('PUT', '', $reqBody); 
            $body = $response->getBody();
            $content = $body->getContents();
            $response_array = json_decode($content,TRUE);
            // echo json_encode($purchase);
        } catch(RequestException $exception) {
        }
        // if ($request->input('wop') == 'cash' && $request->acc) {
        //     $purchase=DB::table('purchases')->where('id', $purchase_id)->first();
        //     $period_year = date('Y');
        //     $period_month = date('m');
        //     $rabcon = new RabController();
        //     $bill_no = $rabcon->generateTransactionNo('PAID_SPPL', $period_year, $period_month, $this->site_id );
        //     try
        //     {
        //         $headers = [
        //         'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
        //         'Accept'        => 'application/json',
        //     ];
        //     $client = new Client(['base_uri' => $this->base_api_url . 'inv/base/PaymentSupplier']);
        // //         $reqBody = [
        // //             'headers' => $headers,
        //         'json' => [
        //                 'purchase_id' => $purchase_id,
        //                 'purchase_asset_id' => 0,
        //                 // 'inv_id' => 0,
        //                 'amount' => $sum_perkiraan_harga_suppl,
        //                 'due_date' => date('Y-m-d'),
        //                 'delivery_fee' => $purchase->delivery_fee,
        //                 'no'  => $bill_no,
        //                 'is_paid'   => 0,
        //                 'user_id'   => auth()->user()['id'],
        //                 'm_supplier_id' => $request->input('m_supplier_id'),
        //                 'payment_po'   => 'cash',
        //                 'site_id'   => $this->site_id
        //             ]
        //         ]; 
                
        //         $response = $client->request('POST', '', $reqBody); 
        //         $body = $response->getBody();
        //         $content = $body->getContents();
        //         $response_array = json_decode($content,TRUE);
        //         $payment_supplier=$response_array['data'];
        //     } catch(RequestException $exception) {
        //     }
        // }
        if ($pd_id != null) {
            for($j = 0; $j < count($pd_id); $j++){
                try
                {
                    $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . 'inv/base/PurchaseD/'.$pd_id[$j]]);
                    $reqBody = [
                        'headers' => $headers,
                'json' => [
                            'amount' => $volume[$j],
                            'price_before_discount' => $perkiraan_harga_suppl[$j],
                            'base_price' => $harga_diskon[$j]
                        ]
                    ]; 
                    $response = $client->request('PUT', '', $reqBody); 
                    $body = $response->getBody();
                    $content = $body->getContents();
                    $response_array = json_decode($content,TRUE);
                } catch(RequestException $exception) {
                }
            }
        }
        if ($request->acc) {
            for($j = 0; $j < count($m_item_id); $j++){
                $query=DB::table('purchase_ds')
                            ->join('purchases', 'purchases.id', '=', 'purchase_ds.purchase_id')
                            ->where('m_item_id', $m_item_id[$j])
                            ->select('purchases.m_supplier_id', 'purchase_ds.base_price', 'purchases.id')
                            ->limit(3)
                            ->orderBy('purchase_ds.id', 'DESC')
                            ->get();
                $stdClass = json_decode(json_encode($query));
                $numbers = array_column($stdClass, 'base_price');
                $min = array_keys($numbers, min($numbers));
                
                $purchases=DB::table('purchases')->where('id', $stdClass[$min[0]]->id)->first();
                try
                {
                    $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . 'inv/best_price']);
                    $reqBody = [
                        'headers' => $headers,
                'json' => [
                            'm_supplier_id' => $stdClass[$min[0]]->m_supplier_id,
                            'm_item_id' => $m_item_id[$j],
                            'best_price' => ($purchases->with_ppn == true ? ($stdClass[$min[0]]->base_price / 1.1) : $stdClass[$min[0]]->base_price)
                        ]
                    ]; 
                    $response = $client->request('POST', '', $reqBody); 
                    $body = $response->getBody();
                    $content = $body->getContents();
                    $response_array = json_decode($content,TRUE);
                    $data = $response_array['data'];
                } catch(RequestException $exception) {
                }
            }
        }

        return redirect('po_konstruksi/po_ao');
    }
    public function poAssetWithAOIndex()
    {
        $is_error = false;
        $error_message = '';  

        $data = array(
            'error' => array(
                'is_error' => $is_error,
                'error_message' => $error_message
            )
        );
        
        return view('pages.inv.purchase_order.po_asset_with_ao_list', $data);
    }
    public function getPOAssetWithAOJson(){
        $response = null;
        try
        {
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . '/inv/po/po_atk_with_ao?site_id='.$this->site_id]);  
            $response = $client->request('GET', '', ['headers' => $headers]); 
            $body = $response->getBody();
            $content = $body->getContents();
            $response_array = json_decode($content,TRUE);

            $response = $content;         
            $data=DataTables::of($response_array['data'])
                                    ->make(true);             
        } catch(RequestException $exception) {
            
        }    

        return $data;
    }
    public function formAccAOAsset($id){
        $is_error = false;
        $error_message = '';  

        try
        {
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . '/inv/base/PurchaseAsset/' . $id]);  
            $response = $client->request('GET', '', ['headers' => $headers]); 
            $body = $response->getBody();
            $content = $body->getContents();
            $response_array = json_decode($content,TRUE);

            $purchase = $response_array['data'];         
        } catch(RequestException $exception) { 
        }
        
        $m_suppliers = null;
        if ($purchase != null) {
            try
            {
                $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . '/inv/base/MSupplier/' . $purchase['m_supplier_id']]);  
                $response = $client->request('GET', '', ['headers' => $headers]); 
                $body = $response->getBody();
                $content = $body->getContents();
                $response_array = json_decode($content,TRUE);

                $m_suppliers = $response_array['data'];         
            } catch(RequestException $exception) {
            }
        }

        $purchase['m_suppliers'] = $m_suppliers;
        
        $data = array(
            'error' => array(
                'is_error' => $is_error,
                'error_message' => $error_message
            ),
            'purchase' => $purchase,
            'role'  => $this->role_id
        );

        // echo json_encode($purchase);
        
        return view('pages.inv.purchase_order.po_asset_ao_acc_form', $data);
    }

    public function formAccAOAssetSignatureHolding(Request $request, $id){
        // dd($request->file);
        try
        {
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . 'upload']);
            $reqBody = [
                'headers' => $headers,
                'multipart' => [
                    [
                        'name'     => 'user_id',
                        'contents' => $this->user_id
                    ],
                    [
                        'Content-type' => 'multipart/form-data',
                        'name'     => 'file',
                        'contents' => fopen($request->file, 'r'),
                        'filename' => 'POACCASSET-HOLDING_signature_'.$id.'.png'
                    ]
                ]
            ]; 
            $response = $client->request('POST', '', $reqBody);
            $body = $response->getBody();
            $content = $body->getContents();
            $response_array = json_decode($content,TRUE);
            $upload_data = $response_array;
            // echo json_encode($purchase);
        } catch(RequestException $exception) {
        }

        try
        {
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . 'inv/base/PurchaseAsset/'.$id]);
            $reqBody = [
                'headers' => $headers,
                'json' => [
                    'director_id'   => $this->user_id,
                    'acc_director_date' => date('Y-m-d'),
                    'signature_holding' => $upload_data['data']['path']
                ]
            ]; 
            $response = $client->request('PUT', '', $reqBody); 
            $body = $response->getBody();
            $content = $body->getContents();
            $response_array = json_decode($content,TRUE);
            // echo json_encode($purchase);
        } catch(RequestException $exception) {
        }

        return $content;
        
    }

    public function formAccAOAssetSignatureSupplier(Request $request, $id){
        // dd($request->file);
        try
        {
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . 'upload']);
            $reqBody = [
                'headers' => $headers,
                'multipart' => [
                    [
                        'name'     => 'user_id',
                        'contents' => $this->user_id
                    ],
                    [
                        'Content-type' => 'multipart/form-data',
                        'name'     => 'file',
                        'contents' => fopen($request->file, 'r'),
                        'filename' => 'POACCASSET-SUPPLIER_signature_'.$id.'.png'
                    ]
                ]
            ]; 
            $response = $client->request('POST', '', $reqBody);
            $body = $response->getBody();
            $content = $body->getContents();
            $response_array = json_decode($content,TRUE);
            $upload_data = $response_array;
            // echo json_encode($purchase);
        } catch(RequestException $exception) {
        }

        try
        {
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . 'inv/base/PurchaseAsset/'.$id]);
            $reqBody = [
                'headers' => $headers,
                'json' => [
                    'manager_id'   => $this->user_id,
                    'acc_manager_date' => date('Y-m-d'),
                    'signature_supplier' => $upload_data['data']['path']
                ]
            ]; 
            $response = $client->request('PUT', '', $reqBody); 
            $body = $response->getBody();
            $content = $body->getContents();
            $response_array = json_decode($content,TRUE);
            // echo json_encode($purchase);
        } catch(RequestException $exception) {
        }

        return $content;
        
    }

    public function approvePoAOAsset(Request $request){
        $purchase_id=$request->input('purchase_id');
        $pd_id=$request->input('pd_id');
        $purchase_d_id=$request->input('purchase_d_id');
        $m_item_id = $request->post('m_item_id');
        $volume = $request->post('volume');
        $perkiraan_harga_suppl = $request->post('perkiraan_harga_suppl');
        $harga_diskon = $request->post('harga_diskon');
        $diskon = $request->post('diskon');
        $discount_type = $request->post('discount_type');
        $signature_holding = $request->post('signature_holding');
        $signature_supplier = $request->post('signature_supplier');
        foreach ($purchase_d_id as $value) {
            $is_deleted=true;
            if ($pd_id != null) {
                for ($i=0; $i < count($pd_id); $i++) { 
                    if ($pd_id[$i] == $value) {
                        $is_deleted=false;
                    }
                }
            }
            if ($is_deleted == true) {
                DB::table('purchase_asset_ds')->where('id', $value)->delete();
            }
        }
        $sum_perkiraan_harga_suppl = 0;
        for($j = 0; $j < count($perkiraan_harga_suppl); $j++){
            $sum_perkiraan_harga_suppl += ($harga_diskon[$j]*$volume[$j]);
        }
        
        try
        {
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . 'inv/base/PurchaseAsset/'.$purchase_id]);
            $reqBody = [
                'headers' => $headers,
                'json' => [
                    'spk_number'    => $request->spk_number,
                    'base_price' => $sum_perkiraan_harga_suppl,
                    'discount'  => $diskon,
                    'discount_type' => $discount_type,
                    // 'acc_ao'   => ($signature_holding != '' && $signature_supplier != '' ? 1 : 0)
                    // 'status_payment'   => ($request->input('wop') == 'cash' && $request->acc ? true : false),
                    'acc_ao'   => ($request->acc ? 1 : 0)
                ]
            ]; 
            $response = $client->request('PUT', '', $reqBody); 
            $body = $response->getBody();
            $content = $body->getContents();
            $response_array = json_decode($content,TRUE);
            // echo json_encode($purchase);
        } catch(RequestException $exception) {
        }
        // if ($request->input('wop') == 'cash' && $request->acc) {
        //     $purchase=DB::table('purchase_assets')->where('id', $purchase_id)->first();
        //     $period_year = date('Y');
        //     $period_month = date('m');
        //     $rabcon = new RabController();
        //     $bill_no = $rabcon->generateTransactionNo('PAID_SPPL', $period_year, $period_month, $this->site_id );
        //     try
        //     {
        //         $headers = [
        //         'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
        //         'Accept'        => 'application/json',
        //     ];
        //     $client = new Client(['base_uri' => $this->base_api_url . 'inv/base/PaymentSupplier']);
        // //         $reqBody = [
        // //             'headers' => $headers,
        //         'json' => [
        //                 'purchase_id' => 0,
        //                 'purchase_asset_id' => $purchase_id,
        //                 // 'inv_id' => 0,
        //                 'amount' => $sum_perkiraan_harga_suppl,
        //                 'due_date' => date('Y-m-d'),
        //                 'delivery_fee' => $purchase->delivery_fee,
        //                 'no'  => $bill_no,
        //                 'is_paid'   => 0,
        //                 'user_id'   => auth()->user()['id'],
        //                 'm_supplier_id' => $request->input('m_supplier_id'),
        //                 'payment_po'   => 'cash',
        //                 'site_id'   => $this->site_id
        //             ]
        //         ]; 
                
        //         $response = $client->request('POST', '', $reqBody); 
        //         $body = $response->getBody();
        //         $content = $body->getContents();
        //         $response_array = json_decode($content,TRUE);
        //         $payment_supplier=$response_array['data'];
        //     } catch(RequestException $exception) {
        //     }
        // }
        if ($pd_id != null) {
            for($j = 0; $j < count($pd_id); $j++){
                try
                {
                    $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . 'inv/base/PurchaseAssetD/'.$pd_id[$j]]);
                    $reqBody = [
                        'headers' => $headers,
                'json' => [
                            'amount' => $volume[$j],
                            'price_before_discount' => $perkiraan_harga_suppl[$j],
                            'base_price' => $harga_diskon[$j]
                        ]
                    ]; 
                    $response = $client->request('PUT', '', $reqBody); 
                    $body = $response->getBody();
                    $content = $body->getContents();
                    $response_array = json_decode($content,TRUE);
                } catch(RequestException $exception) {
                }
            }
        }
        //update base price
        if ($request->acc) {
            for($j = 0; $j < count($m_item_id); $j++){
                $query=DB::table('purchase_asset_ds')
                            ->join('purchase_assets', 'purchase_assets.id', '=', 'purchase_asset_ds.purchase_asset_id')
                            ->where('m_item_id', $m_item_id[$j])
                            ->select('purchase_assets.m_supplier_id', 'purchase_asset_ds.base_price', 'purchase_assets.id')
                            ->limit(3)
                            ->orderBy('purchase_asset_ds.id', 'DESC')
                            ->get();
                $stdClass = json_decode(json_encode($query));
                $numbers = array_column($stdClass, 'base_price');
                $min = array_keys($numbers, min($numbers));

                $purchases=DB::table('purchase_assets')->where('id', $stdClass[$min[0]]->id)->first();
                try
                {
                    $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . 'inv/best_price']);
                    $reqBody = [
                        'headers' => $headers,
                'json' => [
                            'm_supplier_id' => $stdClass[$min[0]]->m_supplier_id,
                            'm_item_id' => $m_item_id[$j],
                            'best_price' => ($purchases->with_ppn == true ? ($stdClass[$min[0]]->base_price / 1.1) : $stdClass[$min[0]]->base_price)
                        ]
                    ]; 
                    $response = $client->request('POST', '', $reqBody); 
                    $body = $response->getBody();
                    $content = $body->getContents();
                    $response_array = json_decode($content,TRUE);
                    $data = $response_array['data'];
                } catch(RequestException $exception) {
                }
            }
        }
        return redirect('po_konstruksi/po_asset_ao');
    }
    public function printPOBeforeACC($id) {
        $purchase = null;
        $purchase_d = null;

        // Get Header
        try
        {
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . '/inv/base/Purchase/' . $id]);  
            $response = $client->request('GET', '', ['headers' => $headers]); 
            $body = $response->getBody();
            $content = $body->getContents();
            $response_array = json_decode($content,TRUE);

            $purchase = $response_array['data'];         
        } catch(RequestException $exception) { 
        }

        // Get Sites
        $m_suppliers = null;
        if ($purchase != null) {
            try
            {
                $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . '/inv/base/MSupplier/' . $purchase['m_supplier_id']]);  
                $response = $client->request('GET', '', ['headers' => $headers]); 
                $body = $response->getBody();
                $content = $body->getContents();
                $response_array = json_decode($content,TRUE);

                $m_suppliers = $response_array['data'];         
            } catch(RequestException $exception) {
            }
        }

        $purchase['m_suppliers'] = $m_suppliers;

        try
        {
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . '/inv/po/purchase_d_by_purchase_id/'.$id]);  
            $response = $client->request('GET', '', ['headers' => $headers]); 
            $body = $response->getBody();
            $content = $body->getContents();
            $response_array = json_decode($content,TRUE);

            $purchase_d = $response_array['data'];         
        } catch(RequestException $exception) {
            
        }    
        $request_id=DB::table('users')->where('id', $purchase['user_id'])->first();
        $director_id=DB::table('users')->where('id', $purchase['director_id'])->first();
        $manager_id=DB::table('users')->where('id', $purchase['manager_id'])->first();
        
        $data = array(
            'purchase' => $purchase,
            'purchase_d' => $purchase_d,
            'requests'  => $request_id,
            'director'  => $director_id,
            'manager'  => $manager_id,
        );
        // dd($data);
        // if($purchase['m_suppliers']['name'] == 'YKK AP INDONESIA, PT.'){
        //     return view('pages.inv.purchase_order.print_po_acc', $data);    
        // }else{
            return view('pages.inv.purchase_order.print_po_before_acc', $data);
        // }
    }
    public function printPOAssetBeforeACC($id) {
        $purchase = null;
        $purchase_d = null;

        // Get Header
        try
        {
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . '/inv/base/PurchaseAsset/' . $id]);  
            $response = $client->request('GET', '', ['headers' => $headers]); 
            $body = $response->getBody();
            $content = $body->getContents();
            $response_array = json_decode($content,TRUE);

            $purchase = $response_array['data'];         
        } catch(RequestException $exception) { 
        }

        // Get Sites
        $m_suppliers = null;
        if ($purchase != null) {
            try
            {
                $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . '/inv/base/MSupplier/' . $purchase['m_supplier_id']]);  
                $response = $client->request('GET', '', ['headers' => $headers]); 
                $body = $response->getBody();
                $content = $body->getContents();
                $response_array = json_decode($content,TRUE);

                $m_suppliers = $response_array['data'];         
            } catch(RequestException $exception) {
            }
        }

        $purchase['m_suppliers'] = $m_suppliers;

        try
        {
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . '/inv/po/purchase_d_by_purchase_asset_id/'.$id]);  
            $response = $client->request('GET', '', ['headers' => $headers]); 
            $body = $response->getBody();
            $content = $body->getContents();
            $response_array = json_decode($content,TRUE);

            $purchase_d = $response_array['data'];         
        } catch(RequestException $exception) {
            
        }    
        $request_id=DB::table('users')->where('id', $purchase['user_id'])->first();
        $director_id=DB::table('users')->where('id', $purchase['director_id'])->first();
        $manager_id=DB::table('users')->where('id', $purchase['manager_id'])->first();
        
        $data = array(
            'purchase' => $purchase,
            'purchase_d' => $purchase_d,
            'requests'  => $request_id,
            'director'  => $director_id,
            'manager'  => $manager_id,
        );
        // if($purchase['m_suppliers']['name'] == 'YKK AP INDONESIA, PT.'){
        //     return view('pages.inv.purchase_order.print_po_acc', $data);    
        // }else{
            return view('pages.inv.purchase_order.print_po_before_acc', $data);
        // }
    }
    public function saveSignatureDirector(Request $request, $id){
        $code=$request->code;
        $cek=DB::table('users')->where('code_signature', $code)->first();
        $is_there=0;
        if($cek != null){
            $is_there=1;
            try
            {
                $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . 'inv/base/Purchase/'.$id]);
                $reqBody = [
                    'headers' => $headers,
                'json' => [
                        'director_id'   => $cek->id,
                        'acc_director_date' => date('Y-m-d'),
                        'signature_holding' => $cek->signature
                    ]
                ]; 
                $response = $client->request('PUT', '', $reqBody); 
                $body = $response->getBody();
                $content = $body->getContents();
                $response_array = json_decode($content,TRUE);
                // echo json_encode($purchase);
            } catch(RequestException $exception) {
            }
        }
        return $is_there;
    }
    public function saveSignatureManager(Request $request, $id){
        $code=$request->code;
        $cek=DB::table('users')->where('code_signature', $code)->first();
        $is_there=0;
        if($cek != null){
            $is_there=1;
            try
            {
                $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . 'inv/base/Purchase/'.$id]);
                $reqBody = [
                    'headers' => $headers,
                'json' => [
                        'manager_id'   => $cek->id,
                        'acc_manager_date' => date('Y-m-d'),
                        'signature_supplier' => $cek->signature
                    ]
                ]; 
                $response = $client->request('PUT', '', $reqBody); 
                $body = $response->getBody();
                $content = $body->getContents();
                $response_array = json_decode($content,TRUE);
                // echo json_encode($purchase);
            } catch(RequestException $exception) {
            }
        }
        return $is_there;
    }
    public function saveATKSignatureDirector(Request $request, $id){
        $code=$request->code;
        $cek=DB::table('users')->where('code_signature', $code)->first();
        $is_there=0;
        if($cek != null){
            $is_there=1;
            try
            {
                $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . 'inv/base/PurchaseAsset/'.$id]);
                $reqBody = [
                    'headers' => $headers,
                'json' => [
                        'director_id'   => $cek->id,
                        'acc_director_date' => date('Y-m-d'),
                        'signature_holding' => $cek->signature
                    ]
                ]; 
                $response = $client->request('PUT', '', $reqBody); 
                $body = $response->getBody();
                $content = $body->getContents();
                $response_array = json_decode($content,TRUE);
                // echo json_encode($purchase);
            } catch(RequestException $exception) {
            }
        }
        return $is_there;
    }
    public function saveATKSignatureManager(Request $request, $id){
        $code=$request->code;
        $cek=DB::table('users')->where('code_signature', $code)->first();
        $is_there=0;
        if($cek != null){
            $is_there=1;
            try
            {
                $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . 'inv/base/PurchaseAsset/'.$id]);
                $reqBody = [
                    'headers' => $headers,
                'json' => [
                        'manager_id'   => $cek->id,
                        'acc_manager_date' => date('Y-m-d'),
                        'signature_supplier' => $cek->signature
                    ]
                ]; 
                $response = $client->request('PUT', '', $reqBody); 
                $body = $response->getBody();
                $content = $body->getContents();
                $response_array = json_decode($content,TRUE);
                // echo json_encode($purchase);
            } catch(RequestException $exception) {
            }
        }
        return $is_there;
    }
    public function signatureRequest(Request $request){
        $code=$request->code;
        $cek=DB::table('users')->where('code_signature', $code)->first();
        $is_there=0;
        if($cek != null){
            $is_there=1;
        }
        $data=array(
            'status'    => $is_there,
            'data'      => $cek
        );
        return $data;
    }
    public function exportPO(Request $request){
        $date1=$request->date ? $request->date : date('Y-m-d');
        $date2=$request->date2 ? $request->date2 :date('Y-m-d');
        if($this->site_id != null) {
            $datas = DB::table('purchases')->where('is_special', false)
                    ->where('site_id', $this->site_id)
                    ->where('purchase_date', '>=', $date1)
                    ->where('purchase_date', '<=', $date2)
                    ->orderBy('id', 'desc')
                    ->get();
        } else {
            $datas = DB::table('purchases')->where('is_special', false)
                    ->orderBy('id', 'desc')
                    ->where('purchase_date', '>=', $date1)
                    ->where('purchase_date', '<=', $date2)
                    ->get();
        }

        foreach($datas as $data){
            $data->m_suppliers = DB::table('m_suppliers')->where('id', $data->m_supplier_id)->first();
        }
        $data=array(
            'date1'    => $date1,
            'date2'    => $date2,
            'data'      => $datas
        );
        // return $data;
        return Excel::download(new PurchaseExport($data), 'purchase.xlsx');
    }
    public function exportPOAsset(Request $request){
        $date1=$request->date ? $request->date : date('Y-m-d');
        $date2=$request->date2 ? $request->date2 :date('Y-m-d');
        if($this->site_id != null) {
            $datas = DB::table('purchase_assets')->where('is_special', false)
                    ->where('site_id', $this->site_id)
                    ->where('purchase_date', '>=', $date1)
                    ->where('purchase_date', '<=', $date2)
                    ->orderBy('id', 'desc')
                    ->get();
        } else {
            $datas = DB::table('purchase_assets')->where('is_special', false)
                    ->orderBy('id', 'desc')
                    ->where('purchase_date', '>=', $date1)
                    ->where('purchase_date', '<=', $date2)
                    ->get();
        }

        foreach($datas as $data){
            $data->m_suppliers = DB::table('m_suppliers')->where('id', $data->m_supplier_id)->first();
        }
        $data=array(
            'date1'    => $date1,
            'date2'    => $date2,
            'data'      => $datas
        );
        // return $data;
        return Excel::download(new PurchaseAssetExport($data), 'purchase_atk.xlsx');
    }
    public function exportPOService(Request $request){
        $date1=$request->date ? $request->date : date('Y-m-d');
        $date2=$request->date2 ? $request->date2 :date('Y-m-d');
        if($this->site_id != null) {
            $datas = DB::table('purchase_services')->where('is_special', false)
                    ->where('site_id', $this->site_id)
                    ->where('purchase_date', '>=', $date1)
                    ->where('purchase_date', '<=', $date2)
                    ->orderBy('id', 'desc')
                    ->get();
        } else {
            $datas = DB::table('purchase_services')->where('is_special', false)
                    ->orderBy('id', 'desc')
                    ->where('purchase_date', '>=', $date1)
                    ->where('purchase_date', '<=', $date2)
                    ->get();
        }

        foreach($datas as $data){
            $data->m_suppliers = DB::table('m_suppliers')->where('id', $data->m_supplier_id)->first();
        }
        $data=array(
            'date1'    => $date1,
            'date2'    => $date2,
            'data'      => $datas
        );
        // return $data;
        return Excel::download(new PurchaseServiceExport($data), 'purchase_jasa.xlsx');
    }
}
