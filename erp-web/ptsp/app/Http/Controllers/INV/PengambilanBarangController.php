<?php

namespace App\Http\Controllers\INV;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use App\Http\Controllers\RAB\RabController;
use Carbon\Carbon;
use DB;

class PengambilanBarangController extends Controller
{
    private $base_api_url;
    private $site_id = null;
    private $username = null;
    private $user_name = null;
    public function __construct()
    {
        //Authenticate page menu
        $this->middleware(function ($request, $next) {
            Controller::isLogin(auth()->user()['role_id']); 
            $this->site_id = auth()->user()['site_id'];
            $this->username = auth()->user()['email'];
            $this->user_name = auth()->user()['name'];
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
        
        return view('pages.inv.pengambilan_barang.pengambilan_barang_list', $data);
    }

    public function indexAuthPengambilanBarang() {
        return view('pages.inv.pengambilan_barang.auth_pengambilan_barang_list');
    }

    public function indexPengeluaran()
    {
        $is_error = false;
        $error_message = '';
        $data = array(
            'error' => array(
                'is_error' => $is_error,
                'error_message' => $error_message
            )
        );
        
        return view('pages.inv.pengambilan_barang.pengeluaran_barang_list', $data);
    }

    public function request()
    {
        //basic variable
        $is_error = false;
        $error_message = '';

        //bussiness variable
        $all_sites = null;
        $all_projects = null;
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
            'site_locations' => $site_location, 
            'sites' => $all_sites,
            'projects' => $all_projects,
            'site_id' => $this->site_id
        );

        // return view('pages.rab.rab.rab_form_add', $data);
        
        return view('pages.inv.pengambilan_barang.pengambilan_barang_request', $data);
    }

    public function requestPost(Request $request)
    {
        $rab_id = $request->post('rab_no');

        $m_item_id = $request->post('m_item_id');
        $qty_rab = $request->post('qty_rab');
        $qty_sisa_rab = $request->post('qty_sisa_rab');
        $qty = $request->post('qty');
        $m_unit_id = $request->post('m_unit_id');
        $alasan = $request->post('alasan'); // untuk permintaan khusus

        $permintaan_normal = array();
        $permintaan_khusus = array();
        for($i = 0; $i < count($m_item_id); $i++) {
            if($qty_sisa_rab[$i] >= $qty[$i]) { // request normal
                array_push($permintaan_normal, 
                    array(
                        'm_item_id' => $m_item_id[$i],
                        'qty_rab' => $qty_rab[$i],
                        'qty_sisa_rab' => $qty_sisa_rab[$i],
                        'qty' => $qty[$i],
                        'm_unit_id' => $m_unit_id[$i]
                    ));
            } else { // request khusus
                if($qty_sisa_rab[$i] == 0) {
                    array_push($permintaan_khusus, 
                        array(
                            'm_item_id' => $m_item_id[$i],
                            'qty_rab' => $qty_rab[$i],
                            'qty_sisa_rab' => $qty_sisa_rab[$i],
                            'qty' => $qty[$i],
                            'm_unit_id' => $m_unit_id[$i]
                        ));
                } else {
                    array_push($permintaan_normal, 
                    array(
                        'm_item_id' => $m_item_id[$i],
                        'qty_rab' => $qty_rab[$i],
                        'qty_sisa_rab' => $qty_sisa_rab[$i],
                        'qty' => $qty_sisa_rab[$i],
                        'm_unit_id' => $m_unit_id[$i]
                    ));

                    array_push($permintaan_khusus, 
                        array(
                            'm_item_id' => $m_item_id[$i],
                            'qty_rab' => $qty_rab[$i],
                            'qty_sisa_rab' => $qty_sisa_rab[$i],
                            'qty' => $qty[$i] - $qty_sisa_rab[$i],
                            'm_unit_id' => $m_unit_id[$i]
                        ));
                }
            }
        }

        $period_year = Carbon::now()->year;
        $period_month = Carbon::now()->month;
        $rabcon = new RabController();
        $memo_no = $rabcon->generateTransactionNo('MREQ', $period_year, $period_month, $this->site_id );
        if(count($permintaan_normal) > 0) {
            // //insert ke request
            try
            {
                $client = new Client(['base_uri' => $this->base_api_url . 'inv/base/InvRequest']);
                $reqBody = [
                'json' => [
                        'req_type' => 'REQ_ITEM',
                        'rab_id' => $rab_id,
                        'no' => $memo_no
                    ]
                ];
                $response = $client->request('POST', '', $reqBody); 
                $body = $response->getBody();
                $content = $body->getContents();
                $response_array = json_decode($content,TRUE);
                $inv_req = $response_array['data'];
            } catch(RequestException $exception) {
            }

            for($i = 0; $i < count($permintaan_normal); $i++) {
                //insert detail
                try
                {
                    $client = new Client(['base_uri' => $this->base_api_url . '/inv/base/InvRequestD']);
                    $reqBody = [
                    'json' => [
                            'inv_request_id' => $inv_req['id'],
                            'm_item_id' => $permintaan_normal[$i]['m_item_id'],
                            'amount' => $permintaan_normal[$i]['qty'],
                            'm_unit_id' => $permintaan_normal[$i]['m_unit_id']
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

        // permintaan khusus
        if(count($permintaan_khusus) > 0) {
            //insert ke special
            try
            {
                $client = new Client(['base_uri' => $this->base_api_url . '/inv/base/InvRequest']);
                $reqBody = [
                'json' => [
                        'req_type' => 'SPECIAL',
                        'rab_id' => $rab_id,
                        'no' => $memo_no
                    ]
                ]; 
                $response = $client->request('POST', '', $reqBody); 
                $body = $response->getBody();
                $content = $body->getContents();
                $response_array = json_decode($content,TRUE);
                $inv_spc_req = $response_array['data'];
            } catch(RequestException $exception) {
            }

            for($i = 0; $i < count($permintaan_khusus); $i++) {
                //insert detail
                try
                {
                    $client = new Client(['base_uri' => $this->base_api_url . '/inv/base/InvRequestD']);
                    $reqBody = [
                    'json' => [
                            'inv_request_id' => $inv_spc_req['id'],
                            'm_item_id' => $permintaan_khusus[$i]['m_item_id'],
                            'amount' => $permintaan_khusus[$i]['qty'],
                            'm_unit_id' => $permintaan_khusus[$i]['m_unit_id'],
                            'notes' => $alasan[$i]
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

        $notification = array(
            'message' => 'Success Create New Request',
            'alert-type' => 'success'
        );

        return redirect('material_request')->with($notification);
    }

    public function indexAuthPengambilanBarangPost(Request $request) {
        $inv_request_id = $request->post('inv_request_id');
        $inv_request_d_id = $request->post('inv_request_d_id');
        $m_item_id = $request->post('m_item_id');
        $qty = $request->post('qty');
        $m_unit_id = $request->post('m_unit_id');

        //insert ke special
        try
        {
            $client = new Client(['base_uri' => $this->base_api_url . '/inv/base/InvRequest/' . $inv_request_id ]);
            $reqBody = [
            'json' => [
                    'user_auth' => $this->username
                ]
            ]; 
            $response = $client->request('PUT', '', $reqBody);
        } catch(RequestException $exception) {
        }

        for($i = 0; $i < count($inv_request_d_id); $i++) {
            //insert inv_trx_d
            try
            {
                $client = new Client(['base_uri' => $this->base_api_url . '/inv/base/InvRequestD/' . $inv_request_d_id[$i]]);
                $reqBody = [
                    'json' => [
                        'amount_auth' => $qty[$i]
                        ]
                    ]; 
                    $response = $client->request('PUT', '', $reqBody); 
            } catch(RequestException $exception) {
            }
        }

        $notification = array(
            'message' => 'Success Auth Request',
            'alert-type' => 'success'
        );

        return redirect('auth_pengambilan_barang')->with($notification);
    }

    public function indexPengeluaranPost(Request $request) {
        $inv_request_id = $request->post('inv_request_id');
        $inv_request_d_id = $request->post('inv_request_d_id');
        $m_item_id = $request->post('m_item_id');
        $qty = $request->post('qty');
        $m_unit_id = $request->post('m_unit_id');
        $mandor = $request->post('mandor');
        $stok = $request->post('stok');

        $isSubmit = true;
        for($i = 0; $i < count($inv_request_d_id); $i++) {
            if ($stok[$i] < $qty[$i]) {
                $isSubmit = false;
                break;
            }
        }

        if ($isSubmit) {
            // insert ke special
            try
            {
                $client = new Client(['base_uri' => $this->base_api_url . '/inv/base/InvRequest/' . $inv_request_id ]);
                $reqBody = [
                    'json' => [
                        'contractor' => $mandor
                    ]
                ]; 
                $response = $client->request('PUT', '', $reqBody);
            } catch(RequestException $exception) {
            }

            //insert inv_trx
            $period_year = Carbon::now()->year;
            $period_month = Carbon::now()->month;
            $rabcon = new RabController();
            $inv_no = $rabcon->generateTransactionNo('INV_OUT', $period_year, $period_month, $this->site_id );
            try
            {
                $client = new Client(['base_uri' => $this->base_api_url . '/inv/base/InvTrx']);
                $reqBody = [
                    'json' => [
                        'm_warehouse_id' => 1,
                        'purchase_id' => null,
                        'trx_type' => 'REQ_ITEM',
                        'inv_request_id' => $inv_request_id,
                        'no' => $inv_no,
                        'inv_trx_date' => Carbon::now()->toDateString(),
                        'site_id' => $this->site_id,
                        'is_entry' => false
                        ]
                    ]; 
                    $response = $client->request('POST', '', $reqBody); 
                    $body = $response->getBody();
                    $content = $body->getContents();
                    $response_array = json_decode($content,TRUE);
                    $inv_trx = $response_array['data'];
            } catch(RequestException $exception) {
            }

            for($i = 0; $i < count($inv_request_d_id); $i++) {
                // set nilai material untuk pengeluaran
                try
                {
                    $client = new Client(['base_uri' => $this->base_api_url . '/inv/value_out']);
                    $reqBody = [
                        'json' => [
                            'm_item_id' => $m_item_id[$i],
                            'qty' => $qty[$i]
                            ]
                        ]; 
                        $response = $client->request('POST', '', $reqBody); 
                        $body = $response->getBody();
                        $content = $body->getContents();
                        $response_array = json_decode($content,TRUE);
                        $value = $response_array['data']['value'];
                } catch(RequestException $exception) {
                }

                //insert inv_trx_d
                try
                {
                    $client = new Client(['base_uri' => $this->base_api_url . '/inv/base/InvTrxD']);
                    $reqBody = [
                        'json' => [
                            'inv_trx_id' => $inv_trx['id'],
                            'm_item_id' => $m_item_id[$i],
                            'amount' => $qty[$i],
                            'm_unit_id' => $m_unit_id[$i],
                            'value' => $value
                            ]
                        ]; 
                        $response = $client->request('POST', '', $reqBody); 
                } catch(RequestException $exception) {
                }   
            }

            $notification = array(
                'message' => 'Success Auth Request',
                'alert-type' => 'success'
            );
        } else {
            $notification = array(
                'message' => 'Error, Stock cannot smaller than request',
                'alert-type' => 'error'
            );
        }

        return redirect('pengeluaran_barang')->with($notification);
    }

    public function printPengeluaranBarang($id) {
        $inv_request = null;
        $inv_request_d = null;

        $hitung_h = 0;
        while($inv_request == null && $hitung_h < 10) {
            // Get Header
            try
            {
                $client = new Client(['base_uri' => $this->base_api_url . '/inv/base/InvRequest/' . $id]);  
                $response = $client->request('GET', ''); 
                $body = $response->getBody();
                $content = $body->getContents();
                $response_array = json_decode($content,TRUE);

                $inv_request = $response_array['data'];         
            } catch(RequestException $exception) {
                
            }
            $hitung_h++;
        }

        $inv_trx = null;
        $hitung_inv_trx = 0;
        while($inv_trx == null && $hitung_inv_trx < 100) {
            // Get Header
            try
            {
                $client = new Client(['base_uri' => $this->base_api_url . 'inv/inv_trx/get_by_inv_request_id/' . $id]);  
                $response = $client->request('GET', ''); 
                $body = $response->getBody();
                $content = $body->getContents();
                $response_array = json_decode($content,TRUE);

                $inv_trx = $response_array['data'];         
            } catch(RequestException $exception) {
                
            }
            $hitung_inv_trx++;
        }
        $inv_request['inv_trx'] = $inv_trx;

        // Get RAB
        $rab = null;
        try
        {
            $client = new Client(['base_uri' => $this->base_api_url . '/rab/base/Rab/' . $inv_request['rab_id']]);  
            $response = $client->request('GET', ''); 
            $body = $response->getBody();
            $content = $body->getContents();
            $response_array = json_decode($content,TRUE);

            $rab = $response_array['data'];         
        } catch(RequestException $exception) {
            
        }

        $project = null;
        if ($rab != null) {
            // Get RAB
            try
            {
                $client = new Client(['base_uri' => $this->base_api_url . '/rab/base/Project/' . $rab['project_id']]);  
                $response = $client->request('GET', ''); 
                $body = $response->getBody();
                $content = $body->getContents();
                $response_array = json_decode($content,TRUE);

                $project = $response_array['data'];         
            } catch(RequestException $exception) {
                
            }
        }
        $inv_request['project'] = $project;
        
        // Get detail
        try
        {
            $client = new Client(['base_uri' => $this->base_api_url . '/inv/pengambilan_barang_detail/' . $id]);  
            $response = $client->request('GET', ''); 
            $body = $response->getBody();
            $content = $body->getContents();
            $response_array = json_decode($content,TRUE);

            $inv_request_d = $response_array['data'];         
        } catch(RequestException $exception) {
            
        } 

        $data = array(
            'inv_request' => $inv_request != null ? $inv_request : null,
            'inv_request_d' => $inv_request_d,
            'user_name' => $this->user_name
        );
        return view('pages.inv.pengambilan_barang.print_pengeluaran_material', $data);
    }

    //JSON METHOD
    public function getStokSite($site_id){
        $response = null;
        try
        {
            $client = new Client(['base_uri' => $this->base_api_url . '/inv/stok/'.$site_id]);  
            $response = $client->request('GET', ''); 
            $body = $response->getBody();
            $content = $body->getContents();
            $response_array = json_decode($content,TRUE);

            $response = $response_array['data'];         
        } catch(RequestException $exception) {
            
        }

        return $response;
    }

    public function getAllMaterialJson()
    {
        $response = null;
        try
        {
            $client = new Client(['base_uri' => $this->base_api_url . 'rab/all_material']);  
            $response = $client->request('GET', ''); 
            $body = $response->getBody();
            $content = $body->getContents();
            $response_array = json_decode($content,TRUE);

            $response = $content;         
        } catch(RequestException $exception) {
            
        }    

        return $response;
    }

    public function getAllMaterialRabJson(){
        $rab_id = $_GET['rab_id'];

        $response = null;
        try
        {
            $client = new Client(['base_uri' => $this->base_api_url . 'rab/all_material/'.$rab_id]);  
            $response = $client->request('GET', ''); 
            $body = $response->getBody();
            $content = $body->getContents();
            $response_array = json_decode($content,TRUE);

            $response = $content;        
        } catch(RequestException $exception) {
            
        }    

        return $response;
    }

    public function getListPengambilanBarang(){
        $response = null;
        try
        {
            $client = new Client(['base_uri' => $this->base_api_url . '/inv/pengambilan_barang']);  
            $response = $client->request('GET', ''); 
            $body = $response->getBody();
            $content = $body->getContents();
            $response_array = json_decode($content,TRUE);

            $response = $content;         
        } catch(RequestException $exception) {
            
        }    

        return $response;
    }

    public function getListPengambilanBarangDetail($id){
        $response = null;
        try
        {
            $client = new Client(['base_uri' => $this->base_api_url . '/inv/pengambilan_barang_detail/' . $id]);  
            $response = $client->request('GET', ''); 
            $body = $response->getBody();
            $content = $body->getContents();
            $response_array = json_decode($content,TRUE);

            $response = $content;         
        } catch(RequestException $exception) {
            
        }    

        return $response;
    }

    public function getListPengeluaranBarangJson() {
        $response = null;
        try
        {
            $client = new Client(['base_uri' => $this->base_api_url . '/inv/pengeluaran_barang']);  
            $response = $client->request('GET', ''); 
            $body = $response->getBody();
            $content = $body->getContents();
            $response_array = json_decode($content,TRUE);

            $response = $content;         
        } catch(RequestException $exception) {
            
        }    

        return $response;
    }

}
