<?php

namespace App\Http\Controllers\INV;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Carbon\Carbon;
use Yajra\DataTables\Facades\DataTables;
use DB;

class PembelianJasaController extends Controller
{
    private $base_api_url;
    private $site_id = null;
    public function __construct()
    {
        //Authenticate page menu
        $this->middleware(function ($request, $next) {
            Controller::isLogin(auth()->user()['role_id']); 
            $this->site_id = auth()->user()['site_id'];
            $this->user_id = auth()->user()['id'];
            $this->role_id = auth()->user()['role_id'];
            return $next($request);
        });

        $this->base_api_url = env('API_URL');
    }
    
    public function index()
    {
        $rab = null;
        $is_error = false;
        $error_message = '';

        $period_year = date('Y');
        $period_month = date('m');
        $no_po=DB::table('m_sequences')->where('seq_code', 'PO_SVC')->where('period_year', $period_year)->where('period_month', $period_month)->select(DB::raw('MAX(seq_no) as seq_no'))->first();
        $index=$no_po != null ? $no_po->seq_no + 1 : 1;
        $data = array(
            'error' => array(
                'is_error' => $is_error,
                'error_message' => $error_message
            ),
            'index' => $index
        );
        
        return view('pages.inv.pembelian_jasa.pembelian_jasa_create', $data);
    }

    public function createPost(Request $request){
        
        $suppl_single = $request->post('suppl_single');
        $cara_bayar_single = $request->post('cara_bayar_single');
        $signature_request = $request->post('signature_request');

        $service_name = $request->post('service_name');
        $volume = $request->post('volume');
        $m_unit_id = $request->post('m_unit_id');
        $perkiraan_harga_suppl = $request->post('perkiraan_harga_suppl');
        $harga_diskon = $request->post('harga_diskon');
        $diskon = $request->post('diskon');
        $discount_type = $request->post('discount_type');
        $delivery_date = $request->post('delivery_date');
        $delivery_fee = $request->post('delivery_fee');
        $suppl = array();
        $cara_bayar = array();
        
        // set suppl
        for($i = 0; $i < count($service_name); $i++){
            $suppl[$i] = $suppl_single;
            $cara_bayar[$i] = $cara_bayar_single;
        }

        // PO timbul berdasarkan supplier
        // hitung jumlah supplier
        $supplPo = array();
        $wopPo = array();
        for($i = 0; $i < count($suppl); $i++){
            if(!in_array($suppl[$i], $supplPo)){
                array_push($supplPo, $suppl[$i]);
                array_push($wopPo, $cara_bayar[$i]);
            }
        }
        
        $period_year = date('Y');
        $period_month = date('m');
        for($i = 0; $i < count($supplPo); $i++){
            $po_no = $this->generateTransactionNo('PO_SVC', $period_year, $period_month, $this->site_id );
            $sum_perkiraan_harga_suppl = 0;
            for($j = 0; $j < count($perkiraan_harga_suppl); $j++){
                if($suppl[$j] == $supplPo[$i])
                    $sum_perkiraan_harga_suppl += ($harga_diskon[$j]*$volume[$j]);
            }

            try
            {
                $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . 'inv/base/PurchaseService']);
                $reqBody = [
                    'headers' => $headers,
                'json' => [
                        'no' => $po_no,
                        'spk_number' => $request->spk_number,
                        'base_price' => $sum_perkiraan_harga_suppl,
                        'm_supplier_id' => $supplPo[$i],
                        'wop' => $wopPo[$i],
                        'purchase_date' => Carbon::now()->toDateString(),
                        'is_closed' => false,
                        'is_special' => false,
                        'site_id' => $this->site_id,
                        'discount'  => $diskon,
                        'discount_type' => $discount_type,
                        'with_ao'   => 1,
                        'acc_ao'   => 0,
                        'with_ppn'   => $request->input('with_ppn') ? 1 : 0,
                        'is_without_ppn'   => $request->input('without_ppn') ? 1 : 0,
                        'signature_request'   => $signature_request,
                        'notes'   => $request->catatan,
                        'user_id'   => $this->user_id,
                        'delivery_date'   => $delivery_date,
                        'delivery_fee'   => $delivery_fee,
                        'status_payment'   => false
                    ]
                ]; 
                $response = $client->request('POST', '', $reqBody); 
                $body = $response->getBody();
                $content = $body->getContents();
                $response_array = json_decode($content,TRUE);
                $purchase = $response_array['data'];
                // echo json_encode($purchase);
            } catch(RequestException $exception) {
            }
            
            for($j = 0; $j < count($service_name); $j++){
                if($suppl[$j] == $supplPo[$i]){
                    try
                    {
                        $headers = [
                            'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                            'Accept'        => 'application/json',
                        ];
                        $client = new Client(['base_uri' => $this->base_api_url . 'inv/base/PurchaseServiceD']);
                        $reqBody = [
                            'headers' => $headers,
                            'json' => [
                                'purchase_service_id' => $purchase['id'],
                                'service_name' => $service_name[$j],
                                'amount' => $volume[$j],
                                'm_unit_id' => $m_unit_id[$j],
                                'price_before_discount' => $perkiraan_harga_suppl[$j],
                                'base_price' => $harga_diskon[$j]
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
        }
        
        $notification = array(
            'message' => 'Success purchase service',
            'alert-type' => 'success'
        );
        return redirect('pembelian_jasa')->with($notification);
    }
    public function generateTransactionNo($trasaction_code, $period_year, $period_month, $site_id){
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
    public function poServiceIndex()
    {
        $is_error = false;
        $error_message = '';  

        $data = array(
            'error' => array(
                'is_error' => $is_error,
                'error_message' => $error_message
            )
        );
        
        return view('pages.inv.pembelian_jasa.po_service_list', $data);
    }
    public function getPOServiceJson(Request $request){
        $date1=$request->date ? $request->date : date('Y-m-d');
        $date2=$request->date2 ? $request->date2 : date('Y-m-d');
        
        if($this->site_id != null) {
            $datas = DB::table('purchase_services')->where('is_special', false)
                    ->where('site_id', $this->site_id)
                    ->where('purchase_date', '>=', $date1)
                    ->where('purchase_date', '<=', $date2)
                    ->orderBy('id', 'desc')
                    ->get();
        } else {
            $datas = DB::table('purchase_services')->where('is_special', false)
                    ->where('purchase_date', '>=', $date1)
                    ->where('purchase_date', '<=', $date2)
                    ->orderBy('id', 'desc')
                    ->get();
        }

        foreach($datas as $data){
            $data->m_suppliers = DB::table('m_suppliers')->where('id', $data->m_supplier_id)->first();
        }

        $data=DataTables::of($datas)
                                ->make(true);  
                                
        return $data;
    }
    public function getPOServiceDetailJson($id){
        $response = null;
        try
        {
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . '/inv/po/purchase_d_by_purchase_service_id/'.$id]);  
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

        $data = array(
            'purchase' => $purchase,
            'purchase_d' => $purchase_d
        );
        return view('pages.inv.pembelian_jasa.print_pembelian_jasa', $data);
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
        
        return view('pages.inv.pembelian_jasa.po_konstruksi_with_ao_list', $data);
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
    
    public function formAccAOService($id){
        $is_error = false;
        $error_message = '';  

        try
        {
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . '/inv/base/PurchaseService/' . $id]);  
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
        
        return view('pages.inv.pembelian_jasa.po_service_ao_acc_form', $data);
    }

    public function formAccAOServiceSignatureHolding(Request $request, $id){
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
                        'filename' => 'POACCSERVICE-HOLDING_signature_'.$id.'.png'
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
            $client = new Client(['base_uri' => $this->base_api_url . 'inv/base/PurchaseService/'.$id]);
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

    public function formAccAOServiceSignatureSupplier(Request $request, $id){
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
                        'filename' => 'POACCSERVICE-SUPPLIER_signature_'.$id.'.png'
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
            $client = new Client(['base_uri' => $this->base_api_url . 'inv/base/PurchaseService/'.$id]);
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

    public function approvePoAOService(Request $request){
        $purchase_id=$request->input('purchase_id');
        $pd_id=$request->input('pd_id');
        $purchase_d_id=$request->input('purchase_d_id');
        $service_name = $request->post('service_name');
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
            $client = new Client(['base_uri' => $this->base_api_url . 'inv/base/PurchaseService/'.$purchase_id]);
            $reqBody = [
                'headers' => $headers,
                'json' => [
                    'base_price' => $sum_perkiraan_harga_suppl,
                    'discount'  => $diskon,
                    'discount_type' => $discount_type,
                    'acc_ao'   => ($signature_holding != '' && $signature_supplier != '' ? 1 : 0)
                ]
            ]; 
            $response = $client->request('PUT', '', $reqBody); 
            $body = $response->getBody();
            $content = $body->getContents();
            $response_array = json_decode($content,TRUE);
            // echo json_encode($purchase);
        } catch(RequestException $exception) {
        }
        // if ($request->input('wop') == 'cash' && $signature_holding != '' && $signature_supplier != '') {
        //     $period_year = date('Y');
        //     $period_month = date('m');
        //     $bill_no = $this->generateTransactionNo('PAID_SPPL', $period_year, $period_month, $this->site_id );
        //     try
        //     {
        //         $client = new Client(['base_uri' => $this->base_api_url . 'inv/base/PaymentSupplier']);
        //         $reqBody = [
        //             'json' => [
        //                 // 'purchase_id' => 0,
        //                 // 'purchase_asset_id' => 0,
        //                 'purchase_service_id' => $purchase_id,
        //                 // 'inv_id' => 0,
        //                 'amount' => $sum_perkiraan_harga_suppl,
        //                 'due_date' => date('Y-m-d'),
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
            $client = new Client(['base_uri' => $this->base_api_url . 'inv/base/PurchaseServiceD/'.$pd_id[$j]]);
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
        
        return redirect('pembelian_jasa/service');
    }
    public function formSignaturePO(Request $request, $id){
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
                        'filename' => 'PO_SVC_REQUEST_signature_'.$id.'.png'
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
        return $upload_data;
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
            $client = new Client(['base_uri' => $this->base_api_url . '/inv/base/PurchaseService/' . $id]);  
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
            $client = new Client(['base_uri' => $this->base_api_url . '/inv/po/purchase_d_by_purchase_service_id/'.$id]);  
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
        
        return view('pages.inv.pembelian_jasa.print_po_jasa', $data);
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
            $client = new Client(['base_uri' => $this->base_api_url . 'inv/base/PurchaseService/'.$id]);
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
            $client = new Client(['base_uri' => $this->base_api_url . 'inv/base/PurchaseService/'.$id]);
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
}
