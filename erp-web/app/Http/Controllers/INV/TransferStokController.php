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

class TransferStokController extends Controller
{
    private $base_api_url;
    private $site_id = null;
    private $m_warehouse_id = null;
    public function __construct()
    {
        //Authenticate page menu
        $this->middleware(function ($request, $next) {
            Controller::isLogin(auth()->user()['role_id']); 
            $this->site_id = auth()->user()['site_id']; 
            $this->m_warehouse_id = auth()->user()['m_warehouse_id'];
            $this->user_id = auth()->user()['id']; 
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
        
        return view('pages.inv.transfer_stok.transfer_stok_list', $data);
    }

    public function create()
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
        $gudang = DB::table('m_warehouses')->where('site_id', $this->site_id)->whereNull('deleted_at')->get();
        $data = array(
            'error' => array(
                'is_error' => $is_error,
                'error_message' => $error_message
            ),
            'site_locations' => $site_location, 
            'sites' => $all_sites,
            'projects' => $all_projects,
            'site_id' => $this->site_id,
            'gudang'    => $gudang
        );
        
        return view('pages.inv.transfer_stok.transfer_stok_create', $data);
    }

    public function createPost(Request $request) {
        // single input
        $site_from = $request->post('site_name');
        $site_to = $this->site_id;
        $due_date = $request->post('due_date');

        // array input
        $m_item_id = $request->post('m_item_id');
        $amount = $request->post('qty');
        $m_unit_id = $request->post('m_unit_id');
        $m_warehouse_id = $request->post('m_warehouse_id');
        $notes = $request->post('keterangan');

        //Insert transfer stock header
        $period_year = Carbon::now()->year;
        $period_month = Carbon::now()->month;
        $rabcon = new RabController();
        $trf_no = $rabcon->generateTransactionNo('TRF_REQ', $period_year, $period_month, $this->site_id );
        try
        {
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . 'inv/base/TransferStock']);
            $reqBody = [
            'headers' => $headers,
                'json' => [
                    'site_from' => $site_from,
                    'site_to' => $site_to,
                    'due_date_receive' => $due_date,
                    'no' => $trf_no
                ]
            ];
            $response = $client->request('POST', '', $reqBody); 
            $body = $response->getBody();
            $content = $body->getContents();
            $response_array = json_decode($content,TRUE);
            $transfer_stock = $response_array['data'];
        } catch(RequestException $exception) {
        }

        for($i = 0; $i < count($m_item_id); $i++) {
            // transfer stock detail
            try
            {
                $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . 'inv/base/TransferStockD']);
                $reqBody = [
                'headers' => $headers,
                'json' => [
                        'transfer_stock_id' => $transfer_stock['id'],
                        'm_item_id' => $m_item_id[$i],
                        'amount' => $amount[$i],
                        'm_unit_id' => $m_unit_id[$i],
                        'm_warehouse_id' => $m_warehouse_id[$i],
                        'notes' => $notes[$i] != '' ? $notes[$i] : null
                    ]
                ];
                $response = $client->request('POST', '', $reqBody); 
            } catch(RequestException $exception) {
            }
        }

        $notification = array(
            'message' => 'Success Create New Transfer Stock Request',
            'alert-type' => 'success'
        );

        return redirect('transfer_stok')->with($notification);
    }


    public function indexPengiriman()
    {
        $is_error = false;
        $error_message = '';  

        $data = array(
            'error' => array(
                'is_error' => $is_error,
                'error_message' => $error_message
            ),
            'site_id' => $this->site_id
        );
        
        return view('pages.inv.transfer_stok.pengiriman_ts_list', $data);
    }

    public function kirimPengiriman($id) {
        $is_error = false;
        $error_message = '';  

        $data = array(
            'error' => array(
                'is_error' => $is_error,
                'error_message' => $error_message
            ),
            'id' => $id
        );
        
        return view('pages.inv.transfer_stok.pengiriman_ts_kirim', $data);
    }

    public function kirimPengirimanPost(Request $request) {
        $transfer_stock_id = $request->post('transfer_stock_id');
        $transfer_stock_d_id = $request->post('transfer_stock_d_id');
        $actual_amount = $request->post('actual_amount');
        $m_item_id = $request->post('m_item_id');
        $m_unit_id = $request->post('m_unit_id');

        // Update transfer stock status
        try
        {
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . 'inv/base/TransferStock/'.$transfer_stock_id]);
            $reqBody = [
                'headers' => $headers,
                'json' => [
                    'is_sent' => true
                ]
            ]; 
            $response = $client->request('PUT', '', $reqBody); 
        } catch(RequestException $exception) {
        }

        $period_year = Carbon::now()->year;
        $period_month = Carbon::now()->month;
        $rabcon = new RabController();
        $inv_no = $rabcon->generateTransactionNo('TRF_SEND', $period_year, $period_month, $this->site_id );
        try
        {
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . 'inv/base/InvTrx']);
            $reqBody = [
                'headers' => $headers,
                'json' => [
                    'm_warehouse_id' => 1,
                    'purchase_id' => null,
                    'trx_type' => 'TRF_STK',
                    'inv_request_id' => null,
                    'no' => $inv_no,
                    'inv_trx_date' => Carbon::now()->toDateString(),
                    'site_id' => $this->site_id,
                    'is_entry' => false,
                    'transfer_stock_id' => $transfer_stock_id
                    ]
                ]; 
                $response = $client->request('POST', '', $reqBody); 
                $body = $response->getBody();
                $content = $body->getContents();
                $response_array = json_decode($content,TRUE);
                $inv_trx = $response_array['data'];
        } catch(RequestException $exception) {
        }

        for ($i = 0; $i < count($transfer_stock_d_id); $i++) {
            // Update transfer stock status detail
            try
            {
                $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . 'inv/base/TransferStockD/'.$transfer_stock_d_id[$i]]);
                $reqBody = [
                    'headers' => $headers,
                'json' => [
                        'actual_amount' => $actual_amount[$i]
                    ]
                ]; 
                $response = $client->request('PUT', '', $reqBody); 
            } catch(RequestException $exception) {
            }

            try
            {
                $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . 'inv/base/InvTrxD']);
                $reqBody = [
                    'headers' => $headers,
                'json' => [
                        'inv_trx_id' => $inv_trx['id'],
                        'm_item_id' => $m_item_id[$i],
                        'amount' => $actual_amount[$i],
                        'm_unit_id' => $m_unit_id[$i]
                        ]
                    ]; 
                    $response = $client->request('POST', '', $reqBody); 
                    $body = $response->getBody();
                    $content = $body->getContents();
                    $response_array = json_decode($content,TRUE);
            } catch(RequestException $exception) {
            }
            $cek_stok=DB::table('stocks')
                            // ->where('m_warehouse_id', $m_warehouse_id[$i])
                            ->where('site_id', $this->site_id)
                            ->where('m_item_id', $m_item_id[$i])
                            ->where('m_unit_id', $m_unit_id[$i])
                            ->where('type', 'TRF_STK')
                            ->first();
                if ($cek_stok == null) {
                    try
                    {
                        $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . '/inv/base/Stock']);
                        $reqBody = [
                            'headers' => $headers,
                'json' => [
                                'site_id' => $this->site_id,
                                'm_item_id' => $m_item_id[$i],
                                'amount' => $actual_amount[$i],
                                'amount_out' => $actual_amount[$i],
                                'm_unit_id' => $m_unit_id[$i],
                                // 'm_warehouse_id' => $m_warehouse_id[$i],
                                'type'  => 'TRF_STK'
                                ]
                            ]; 
                            $response = $client->request('POST', '', $reqBody); 
                            $body = $response->getBody();
                            $content = $body->getContents();
                            $response_array = json_decode($content,TRUE);
                    } catch(RequestException $exception) {
                    }
                }else{
                    try
                    {
                        $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . '/inv/base/Stock/'.$cek_stok->id]);
                        $reqBody = [
                            'headers' => $headers,
                'json' => [
                                'amount' => $cek_stok->amount - $actual_amount[$i],
                                'amount_out' => $cek_stok->amount_out + $actual_amount[$i]
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

        $notification = array(
            'message' => 'Success Create Pengiriman Transfer Stok',
            'alert-type' => 'success'
        );

        return redirect('pengiriman_ts')->with($notification);
    }

    public function indexPenerimaan()
    {
        $is_error = false;
        $error_message = '';  

        $data = array(
            'error' => array(
                'is_error' => $is_error,
                'error_message' => $error_message
            ),
            'site_id' => $this->site_id
        );
        
        return view('pages.inv.transfer_stok.penerimaan_ts_list', $data);
    }

    public function terimaPenerimaan($id) {
        $is_error = false;
        $error_message = '';  

        $data = array(
            'error' => array(
                'is_error' => $is_error,
                'error_message' => $error_message
            ),
            'id' => $id
        );
        
        return view('pages.inv.transfer_stok.penerimaan_ts_terima', $data);
    }

    public function terimaPenerimaanPost(Request $request) {
        $transfer_stock_id = $request->post('transfer_stock_id');
        $transfer_stock_d_id = $request->post('transfer_stock_d_id');
        $actual_amount = $request->post('actual_amount');
        $m_item_id = $request->post('m_item_id');
        $m_unit_id = $request->post('m_unit_id');

        // Update transfer stock status
        try
        {
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . 'inv/base/TransferStock/'.$transfer_stock_id]);
            $reqBody = [
                'headers' => $headers,
                'json' => [
                    'is_receive' => true
                ]
            ]; 
            $response = $client->request('PUT', '', $reqBody); 
        } catch(RequestException $exception) {
        }

        $period_year = Carbon::now()->year;
        $period_month = Carbon::now()->month;
        $rabcon = new RabController();
        $inv_no = $rabcon->generateTransactionNo('TRF_RCV', $period_year, $period_month, $this->site_id );
        try
        {
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . 'inv/base/InvTrx']);
            $reqBody = [
                'headers' => $headers,
                'json' => [
                    'm_warehouse_id' => 1,
                    'purchase_id' => null,
                    'trx_type' => 'TRF_STK',
                    'inv_request_id' => null,
                    'no' => $inv_no,
                    'inv_trx_date' => Carbon::now()->toDateString(),
                    'site_id' => $this->site_id,
                    'is_entry' => true,
                    'transfer_stock_id' => $transfer_stock_id
                    ]
                ]; 
                $response = $client->request('POST', '', $reqBody); 
                $body = $response->getBody();
                $content = $body->getContents();
                $response_array = json_decode($content,TRUE);
                $inv_trx = $response_array['data'];
        } catch(RequestException $exception) {
        }

        for ($i = 0; $i < count($transfer_stock_d_id); $i++) {
            // Update transfer stock status detail
            try
            {
                $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . 'inv/base/TransferStockD/'.$transfer_stock_d_id[$i]]);
                $reqBody = [
                    'headers' => $headers,
                'json' => [
                        'actual_amount' => $actual_amount[$i]
                    ]
                ]; 
                $response = $client->request('PUT', '', $reqBody); 
            } catch(RequestException $exception) {
            }
            $trf_stk_d=DB::table('transfer_stock_ds')->where('id', $transfer_stock_d_id[$i])->first();
            try
            {
                $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . 'inv/base/InvTrxD']);
                $reqBody = [
                    'headers' => $headers,
                'json' => [
                        'inv_trx_id' => $inv_trx['id'],
                        'm_item_id' => $m_item_id[$i],
                        'amount' => $actual_amount[$i],
                        'm_unit_id' => $m_unit_id[$i],
                        'm_warehouse_id'    => $trf_stk_d->m_warehouse_id,
                        'type_material' => 'TRF_STK'
                        ]
                    ]; 
                    $response = $client->request('POST', '', $reqBody); 
                    $body = $response->getBody();
                    $content = $body->getContents();
                    $response_array = json_decode($content,TRUE);
            } catch(RequestException $exception) {
            }
            $cek_stok=DB::table('stocks')
                        ->where('m_warehouse_id', $trf_stk_d->m_warehouse_id)
                        ->where('site_id', $this->site_id)
                        ->where('m_item_id', $m_item_id[$i])
                        ->where('m_unit_id', $m_unit_id[$i])
                        ->where('type', 'TRF_STK')
                        ->first();
            if ($cek_stok == null) {
                try
                {
                    $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . '/inv/base/Stock']);
                    $reqBody = [
                        'headers' => $headers,
                'json' => [
                            'site_id' => $this->site_id,
                            'm_item_id' => $m_item_id[$i],
                            'amount' => $actual_amount[$i],
                            'amount_in' => $actual_amount[$i],
                            'amount_out' => 0,
                            'm_unit_id' => $m_unit_id[$i],
                            'm_warehouse_id' => $trf_stk_d->m_warehouse_id,
                            'type'  => 'TRF_STK'
                            ]
                        ]; 
                        $response = $client->request('POST', '', $reqBody); 
                        $body = $response->getBody();
                        $content = $body->getContents();
                        $response_array = json_decode($content,TRUE);
                } catch(RequestException $exception) {
                }
            }else{
                try
                {
                    $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . '/inv/base/Stock/'.$cek_stok->id]);
                    $reqBody = [
                        'headers' => $headers,
                'json' => [
                            'amount' => $cek_stok->amount + $actual_amount[$i],
                            'amount_in' => $cek_stok->amount_in + $actual_amount[$i]
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

        $notification = array(
            'message' => 'Success Create Penerimaan Transfer Stok',
            'alert-type' => 'success'
        );

        return redirect('penerimaan_ts')->with($notification);
    }

    public function printPenerimaan($id) {
        $transfer_stock = null;
        $transfer_stock_d = null;

        // Get Header
        try
        {
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . '/inv/base/TransferStock/' . $id]);  
            $response = $client->request('GET', '', ['headers' => $headers]);  
            $body = $response->getBody();
            $content = $body->getContents();
            $response_array = json_decode($content,TRUE);

            $transfer_stock = $response_array['data'];         
        } catch(RequestException $exception) { 
        }

        // Get Sites
        $sites = null;
        if ($transfer_stock != null) {
            try
            {
                $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . '/inv/base/Site/' . $transfer_stock['site_from']]);  
                $response = $client->request('GET', '', ['headers' => $headers]);  
                $body = $response->getBody();
                $content = $body->getContents();
                $response_array = json_decode($content,TRUE);

                $sites = $response_array['data'];         
            } catch(RequestException $exception) {
            }
        }

        $transfer_stock['sites'] = $sites;

        try
        {
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . 'inv/transfer_stok_detail/' . $id]);  
            $response = $client->request('GET', '', ['headers' => $headers]);  
            $body = $response->getBody();
            $content = $body->getContents();
            $response_array = json_decode($content,TRUE);

            $transfer_stock_d = $response_array['data'];
        } catch(RequestException $exception) {
            
        } 

        $data = array(
            'transfer_stock' => $transfer_stock,
            'transfer_stock_d' => $transfer_stock_d
        );

        return view('pages.inv.transfer_stok.print_penerimaan_ts', $data);
    }

    // public function tolakPengiriman($id) {
    //     // Update transfer stock status
    //     try
    //     {
    //         $headers = [
    //             'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
    //             'Accept'        => 'application/json',
    //         ];
    //         $client = new Client(['base_uri' => $this->base_api_url . 'inv/base/TransferStock/'.$id]);
    // //         $reqBody = [
    // //             'headers' => $headers,
    //             'json' => [
    //                 'is_sent' => false,
    //                 'is_receive' => false
    //             ]
    //         ]; 
    //         $response = $client->request('PUT', '', $reqBody); 
    //     } catch(RequestException $exception) {
    //     }

    //     $notification = array(
    //         'message' => 'Success Tolak Pengiriman Transfer Stok',
    //         'alert-type' => 'success'
    //     );

    //     return redirect('pengiriman_ts')->with($notification);
    // }


    // JSOn FUNCTION
    public function listTransferStokJson() {
        $response = null;
        try
        {
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . 'inv/transfer_stok']);  
            $response = $client->request('GET', '', ['headers' => $headers]);  
            $body = $response->getBody();
            $content = $body->getContents();
            $response_array = json_decode($content,TRUE);

            $response = $content;         
        } catch(RequestException $exception) {
            
        }    
        return $response;
    }

    public function listTransferStokDetailJson($id) {
        $response = null;
        try
        {
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . 'inv/transfer_stok_detail/' . $id]);  
            $response = $client->request('GET', '', ['headers' => $headers]);  
            $body = $response->getBody();
            $content = $body->getContents();
            $response_array = json_decode($content,TRUE);

            $response = $content;         
        } catch(RequestException $exception) {
            
        }    

        return $response;
    }
    public function indexWarehouse()
    {
        $is_error = false;
        $error_message = '';  

        $data = array(
            'error' => array(
                'is_error' => $is_error,
                'error_message' => $error_message
            )
        );
        
        return view('pages.inv.transfer_stok.transfer_stok_warehouse_list', $data);
    }
    public function createTSWarehouse()
    {
        //basic variable
        $is_error = false;
        $error_message = '';

        //bussiness variable
        $gudang = DB::table('m_warehouses')->where('site_id', $this->site_id)->whereNull('deleted_at')->get();


        $data = array(
            'error' => array(
                'is_error' => $is_error,
                'error_message' => $error_message
            ),
            'gudang' => $gudang, 
            'site_id' => $this->site_id
        );
        
        return view('pages.inv.transfer_stok.transfer_stok_warehouse_create', $data);
    }
    public function saveTSWarehouse(Request $request) {
        // single input
        $m_warehouse_id = $request->post('m_warehouse_id');
        $m_warehouse_id2 = $request->post('m_warehouse_id2');
        $due_date_receive = $request->post('due_date_receive');
        // array input
        $m_item_id = $request->post('m_item_id');
        $amount = $request->post('qty');
        $m_unit_id = $request->post('m_unit_id');
        $notes = $request->post('keterangan');
        
        //Insert transfer stock header
        $period_year = Carbon::now()->year;
        $period_month = Carbon::now()->month;
        $rabcon = new RabController();
        $trf_stk_no = $rabcon->generateTransactionNo('TRF_WRH', $period_year, $period_month, $this->site_id );
        //stok keluar
        
        try
        {
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . 'inv/base/TsWarehouse']);
            $reqBody = [
                'headers' => $headers,
                'json' => [
                    'warehouse_from' => $m_warehouse_id,
                    'warehouse_to' => $m_warehouse_id2,
                    'no' => $trf_stk_no,
                    'site_id' => $this->site_id,
                    'due_date_receive'  => $due_date_receive
                    ]
                ]; 
                
                $response = $client->request('POST', '', $reqBody); 
                $body = $response->getBody();
                $content = $body->getContents();
                $response_array = json_decode($content,TRUE);
                $ts_warehouse = $response_array['data'];
        } catch(RequestException $exception) {
        }
        
        for($i = 0; $i < count($m_item_id); $i++) {
            // transfer stock detail
            try
            {
                $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . 'inv/base/TsWarehouseD']);
                $reqBody = [
                    'headers' => $headers,
                'json' => [
                        'ts_warehouse_id' => $ts_warehouse['id'],
                        'm_item_id' => $m_item_id[$i],
                        'amount' => $amount[$i],
                        'm_unit_id' => $m_unit_id[$i],
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
            'message' => 'Success Create New Transfer Stock Request',
            'alert-type' => 'success'
        );

        return redirect('transfer_stok/warehouse')->with($notification);
    }
    public function listTransferStokWarehouseJson() {
        $response = null;
        $query=DB::table('ts_warehouses as tw')
                        ->join('m_warehouses as mw1', 'mw1.id', '=', 'tw.warehouse_to')
                        ->join('m_warehouses as mw2', 'mw2.id', '=', 'tw.warehouse_from')
                        ->join('sites as s', 's.id', '=', 'tw.site_id')
                        ->select('s.name as site_name', 'mw1.name as to', 'mw2.name as from', 'tw.*');
        if ($this->site_id != null) {
            $query->where('tw.site_id', $this->site_id);
        }
        $query->get();
        $data=DataTables::of($query)
                        ->make(true); 
        return $data;
    }
    public function detailTSWarehouse($id) {
        $data['data']=DB::table('ts_warehouse_ds as tsd')
                        ->join('m_items as mi', 'mi.id', '=', 'tsd.m_item_id')
                        ->join('m_units as mu', 'mu.id', '=', 'tsd.m_unit_id')
                        ->where('tsd.ts_warehouse_id', $id)
                        ->select('tsd.*', 'mi.no as material_no', 'mi.name as material_name', 'mu.name as unit_name')
                        ->get();
        return $data;
    }
    private function journalTransfer($data){
        $id_akun=($data['type'] == 'material' ?  ($data['m_warehouse_id'] == 2 ? 141 : 142) : ($data['m_warehouse_id'] == 2 ? 143 : 144));
        $lawan_akun=($data['type'] == 'material' ?  ($data['m_warehouse_id2'] == 2 ? 141 : 142) : ($data['m_warehouse_id2'] == 2 ? 143 : 144));
        $data_trx=array(
            'deskripsi'     => $data['deskripsi'],
            'location_id'     => $data['location_id'],
            'tanggal'       => $data['tgl'],
            'inv_trx_id'   => $data['inv_trx_id'],
            'ts_warehouse_id'   => $data['ts_warehouse_id'],
            'user_id'       => $data['user_id'],
        );
        $insert=DB::table('tbl_trx_akuntansi')->insert($data_trx);
        if ($insert) {
            $id_last=DB::table('tbl_trx_akuntansi')->max('id_trx_akun');
            $lawan=array(
                'id_trx_akun'   => $id_last,
                'id_akun'       => $lawan_akun,
                'jumlah'        => $data['total'],
                'tipe'          => "DEBIT",
                'keterangan'    => 'lawan',
            );
            DB::table('tbl_trx_akuntansi_detail')->insert($lawan);
            $akun=array(
                'id_trx_akun'   => $id_last,
                'id_akun'       => $id_akun,
                'jumlah'        => $data['total'],
                'tipe'          => "KREDIT",
                'keterangan'    => 'akun',
            );
            DB::table('tbl_trx_akuntansi_detail')->insert($akun);
        }
    }
    public function indexPengirimanTSWarehouse()
    {
        $is_error = false;
        $error_message = '';  

        $data = array(
            'error' => array(
                'is_error' => $is_error,
                'error_message' => $error_message
            ),
            'site_id' => $this->site_id
        );
        
        return view('pages.inv.transfer_stok.kirim_ts_warehouse_list', $data);
    }
    public function kirimPengirimanTSWarehouse($id) {
        $is_error = false;
        $error_message = '';  
        try
        {
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . 'inv/base/TsWarehouse/'.$id]);
            $response = $client->request('GET', '', ['headers' => $headers]);  
            $body = $response->getBody();
            $content = $body->getContents();
            $response_array = json_decode($content,TRUE);
            $ts_warehouse = $response_array['data'];
        } catch(RequestException $exception) {
        }
        $data = array(
            'error' => array(
                'is_error' => $is_error,
                'error_message' => $error_message
            ),
            'ts_warehouse'  => $ts_warehouse,
            'id' => $id
        );
        return view('pages.inv.transfer_stok.form_pengiriman_ts_warehouse', $data);
    }

    public function kirimPengirimanTSWarehousePost(Request $request) {
        $ts_warehouse_id = $request->post('ts_warehouse_id');
        $ts_warehouse_d_id = $request->post('ts_warehouse_d_id');
        $qty = $request->post('qty');
        $m_item_id = $request->post('m_item_id');
        $m_unit_id = $request->post('m_unit_id');
        try
        {
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . 'inv/base/TsWarehouse/'.$ts_warehouse_id]);
            $reqBody = [
                'headers' => $headers,
                'json' => [
                    'is_sent' => 1,
                    ]
                ]; 
                $response = $client->request('PUT', '', $reqBody); 
        } catch(RequestException $exception) {
        }

        try
        {
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . 'inv/base/TsWarehouse/'.$ts_warehouse_id]);
            $response = $client->request('GET', '', ['headers' => $headers]);  
            $body = $response->getBody();
            $content = $body->getContents();
            $response_array = json_decode($content,TRUE);
            $ts_warehouse=$response_array['data'];
        } catch(RequestException $exception) {
        }
        // Update transfer stock status
        for($i = 0; $i < count($ts_warehouse_d_id); $i++) {
            // transfer stock detail
            try
            {
                $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . 'inv/base/TsWarehouseD/'.$ts_warehouse_d_id[$i]]);
                $reqBody = [
                    'headers' => $headers,
                'json' => [
                        'actual_amount' => $qty[$i],
                        ]
                    ]; 
                    $response = $client->request('PUT', '', $reqBody); 
                    $body = $response->getBody();
                    $content = $body->getContents();
                    $response_array = json_decode($content,TRUE);
            } catch(RequestException $exception) {
            }
        }
        
        $notification = array(
            'message' => 'Success Create Pengiriman Transfer Stok',
            'alert-type' => 'success'
        );

        return redirect('transfer_stok/kirim_ts_warehouse')->with($notification);
    }
    public function pengirimanTSWarehouseJson(){
        $response = null;
        $query=DB::table('ts_warehouses as tw')
                        ->join('m_warehouses as mw1', 'mw1.id', '=', 'tw.warehouse_to')
                        ->join('m_warehouses as mw2', 'mw2.id', '=', 'tw.warehouse_from')
                        ->join('sites as s', 's.id', '=', 'tw.site_id')
                        // ->where('is_sent', false)
                        ->select('s.name as site_name', 'mw1.name as to', 'mw2.name as from', 'tw.*');
        if ($this->site_id != null) {
            $query->where('tw.site_id', $this->site_id);
        }
        if ($this->m_warehouse_id != null) {
            $query->where('tw.warehouse_from', $this->m_warehouse_id);
        }
        $query->get();
        $data=DataTables::of($query)
                        ->make(true); 
        return $data;
    }
    public function indexPenerimaanTSWarehouse()
    {
        $is_error = false;
        $error_message = '';  

        $data = array(
            'error' => array(
                'is_error' => $is_error,
                'error_message' => $error_message
            ),
            'site_id' => $this->site_id
        );
        
        return view('pages.inv.transfer_stok.terima_ts_warehouse_list', $data);
    }
    public function terimaPenerimaanTSWarehouse($id) {
        $is_error = false;
        $error_message = '';  
        try
        {
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . 'inv/base/TsWarehouse/'.$id]);
            $response = $client->request('GET', '', ['headers' => $headers]);  
            $body = $response->getBody();
            $content = $body->getContents();
            $response_array = json_decode($content,TRUE);
            $ts_warehouse = $response_array['data'];
        } catch(RequestException $exception) {
        }
        $data = array(
            'error' => array(
                'is_error' => $is_error,
                'error_message' => $error_message
            ),
            'ts_warehouse'  => $ts_warehouse,
            'id' => $id
        );
        
        return view('pages.inv.transfer_stok.form_penerimaan_ts_warehouse', $data);
    }

    public function terimaPenerimaanTSWarehousePost(Request $request) {
        $ts_warehouse_id = $request->post('ts_warehouse_id');
        $ts_warehouse_d_id = $request->post('ts_warehouse_d_id');
        $qty = $request->post('qty');
        $m_item_id = $request->post('m_item_id');
        $m_unit_id = $request->post('m_unit_id');
        try
        {
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . 'inv/base/TsWarehouse/'.$ts_warehouse_id]);
            $reqBody = [
                'headers' => $headers,
                'json' => [
                    'is_receive' => 1,
                    ]
                ]; 
                $response = $client->request('PUT', '', $reqBody); 
        } catch(RequestException $exception) {
        }

        try
        {
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . 'inv/base/TsWarehouse/'.$ts_warehouse_id]);
            $response = $client->request('GET', '', ['headers' => $headers]);  
            $body = $response->getBody();
            $content = $body->getContents();
            $response_array = json_decode($content,TRUE);
            $ts_warehouse=$response_array['data'];
        } catch(RequestException $exception) {
        }
        $period_year = Carbon::now()->year;
        $period_month = Carbon::now()->month;
        $rabcon = new RabController();
        $trf_no = $rabcon->generateTransactionNo('TRF_RCV', $period_year, $period_month, $this->site_id );
        //stok keluar
        
        try
        {
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . 'inv/base/InvTrx']);
            $reqBody = [
                'headers' => $headers,
                'json' => [
                    'm_warehouse_id' => 1,
                    'purchase_id' => null,
                    'trx_type' => 'TRF_WRH',
                    'inv_request_id' => null,
                    'no' => $trf_no,
                    'inv_trx_date' => Carbon::now()->toDateString(),
                    'site_id' => $this->site_id,
                    'is_entry' => false,
                    'ts_warehouse_id'   => $ts_warehouse['id']
                    ]
                ]; 
                
                $response = $client->request('POST', '', $reqBody); 
                $body = $response->getBody();
                $content = $body->getContents();
                $response_array = json_decode($content,TRUE);
                $inv_trx = $response_array['data'];
        } catch(RequestException $exception) {
        }
        
        for($i = 0; $i < count($m_item_id); $i++) {
            if ($qty[$i] > 0) {
                // transfer stock detail
                $get_save_price=DB::table('m_item_prices')->where(['m_item_id' => $m_item_id[$i], 'm_unit_id' => $m_unit_id[$i], 'site_id' => $this->site_id])->first();
                try
                {
                    $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . 'inv/base/InvTrxD']);
                    $reqBody = [
                        'headers' => $headers,
                'json' => [
                            'inv_trx_id' => $inv_trx['id'],
                            'm_item_id' => $m_item_id[$i],
                            'amount' => $qty[$i],
                            'm_unit_id' => $m_unit_id[$i],
                            'm_warehouse_id' => $ts_warehouse['warehouse_from'],
                            'base_price'    => $get_save_price->price
                            ]
                        ]; 
                        $response = $client->request('POST', '', $reqBody); 
                        $body = $response->getBody();
                        $content = $body->getContents();
                        $response_array = json_decode($content,TRUE);
                } catch(RequestException $exception) {
                }
                
                // stok out
                $cek_stok=DB::table('stocks')
                            ->where('m_warehouse_id', $ts_warehouse['warehouse_from'])
                            ->where('site_id', $this->site_id)
                            ->where('m_item_id', $m_item_id[$i])
                            ->where('m_unit_id', $m_unit_id[$i])
                            ->where('type', 'STK_NORMAL')
                            ->first();
                if ($cek_stok == null) {
                    try
                    {
                        $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . '/inv/base/Stock']);
                        $reqBody = [
                            'headers' => $headers,
                'json' => [
                                'site_id' => $this->site_id,
                                'm_item_id' => $m_item_id[$i],
                                'amount' => $qty[$i],
                                'amount_in' => 0,
                                'amount_out' => $qty[$i],
                                'm_unit_id' => $m_unit_id[$i],
                                'm_warehouse_id' => $ts_warehouse['warehouse_from'],
                                'type'  => 'STK_NORMAL'
                                ]
                            ]; 
                            $response = $client->request('POST', '', $reqBody); 
                            $body = $response->getBody();
                            $content = $body->getContents();
                            $response_array = json_decode($content,TRUE);
                    } catch(RequestException $exception) {
                    }
                }else{
                    try
                    {
                        $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . '/inv/base/Stock/'.$cek_stok->id]);
                        $reqBody = [
                            'headers' => $headers,
                'json' => [
                                'amount' => $cek_stok->amount - $qty[$i],
                                'amount_out' => $cek_stok->amount_out + $qty[$i]
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
        }
        
        $period_year = Carbon::now()->year;
        $period_month = Carbon::now()->month;
        $rabcon = new RabController();
        $trf_no = $rabcon->generateTransactionNo('TRF_RCV', $period_year, $period_month, $this->site_id );
        //stok masuk
        
        try
        {
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . 'inv/base/InvTrx']);
            $reqBody = [
                'headers' => $headers,
                'json' => [
                    'm_warehouse_id' => 1,
                    'purchase_id' => null,
                    'trx_type' => 'TRF_WRH',
                    'inv_request_id' => null,
                    'no' => $trf_no,
                    'inv_trx_date' => Carbon::now()->toDateString(),
                    'site_id' => $this->site_id,
                    'is_entry' => true,
                    'ts_warehouse_id'   => $ts_warehouse['id']
                    ]
                ]; 
                
                $response = $client->request('POST', '', $reqBody); 
                $body = $response->getBody();
                $content = $body->getContents();
                $response_array = json_decode($content,TRUE);
                $inv_trx = $response_array['data'];
        } catch(RequestException $exception) {
        }
        
        for($i = 0; $i < count($m_item_id); $i++) {
            if ($qty[$i] > 0) {
                    // transfer stock detail
                $get_save_price=DB::table('m_item_prices')->where(['m_item_id' => $m_item_id[$i], 'm_unit_id' => $m_unit_id[$i], 'site_id' => $this->site_id])->first();
                try
                {
                    $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . 'inv/base/InvTrxD']);
                    $reqBody = [
                        'headers' => $headers,
                'json' => [
                            'inv_trx_id' => $inv_trx['id'],
                            'm_item_id' => $m_item_id[$i],
                            'amount' => $qty[$i],
                            'm_unit_id' => $m_unit_id[$i],
                            'm_warehouse_id' => $ts_warehouse['warehouse_to'],
                            'base_price'    => $get_save_price->price
                            ]
                        ]; 
                        $response = $client->request('POST', '', $reqBody); 
                        $body = $response->getBody();
                        $content = $body->getContents();
                        $response_array = json_decode($content,TRUE);
                } catch(RequestException $exception) {
                }
                
                // stok in
                $cek_stok=DB::table('stocks')
                            ->where('m_warehouse_id', $ts_warehouse['warehouse_to'])
                            ->where('site_id', $this->site_id)
                            ->where('m_item_id', $m_item_id[$i])
                            ->where('m_unit_id', $m_unit_id[$i])
                            ->where('type', 'STK_NORMAL')
                            ->first();
                if ($cek_stok == null) {
                    try
                    {
                        $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . '/inv/base/Stock']);
                        $reqBody = [
                            'headers' => $headers,
                'json' => [
                                'site_id' => $this->site_id,
                                'm_item_id' => $m_item_id[$i],
                                'amount' => $qty[$i],
                                'amount_in' => $qty[$i],
                                'amount_out' => 0,
                                'm_unit_id' => $m_unit_id[$i],
                                'm_warehouse_id' => $ts_warehouse['warehouse_to'],
                                'type'  => 'STK_NORMAL'
                                ]
                            ]; 
                            $response = $client->request('POST', '', $reqBody); 
                            $body = $response->getBody();
                            $content = $body->getContents();
                            $response_array = json_decode($content,TRUE);
                    } catch(RequestException $exception) {
                    }
                }else{
                    try
                    {
                        $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . '/inv/base/Stock/'.$cek_stok->id]);
                        $reqBody = [
                            'headers' => $headers,
                'json' => [
                                'amount' => $cek_stok->amount + $qty[$i],
                                'amount_in' => $cek_stok->amount_in + $qty[$i]
                                ]
                            ]; 
                            $response = $client->request('PUT', '', $reqBody); 
                            $body = $response->getBody();
                            $content = $body->getContents();
                            $response_array = json_decode($content,TRUE);
                    } catch(RequestException $exception) {
                    }
                }

                $get_save_price=DB::table('m_item_prices')->where(['m_item_id' => $m_item_id[$i], 'm_unit_id' => $m_unit_id[$i], 'site_id' => $this->site_id])->first();
                $item=DB::table('m_items')->where('id', $m_item_id[$i])->first();
                if ($item->category == 'MATERIAL') {
                    $input_jurnal=array(
                        'inv_trx_id' => $inv_trx['id'],
                        'total' => ($qty[$i] * $get_save_price->price),
                        'user_id'   => $this->user_id,
                        'deskripsi'     => 'Transfer Stok Gudang Material dari No '.$trf_no,
                        'tgl'       => date('Y-m-d'),
                        'type'      => 'material',
                        'm_warehouse_id' => $ts_warehouse['warehouse_from'],//stok keluar
                        'm_warehouse_id2' => $ts_warehouse['warehouse_to'],//stok masuk
                        'ts_warehouse_id' => $ts_warehouse['id'],
                        'location_id'   => $this->site_id
                    );
                    $this->journalTransfer($input_jurnal);
                }else{
                    $input_jurnal=array(
                        'inv_trx_id' => $inv_trx['id'],
                        'total' => ($qty[$i] * $get_save_price->price),
                        'user_id'   => $this->user_id,
                        'deskripsi'     => 'Transfer Stok Gudang Spare Part dari No '.$trf_no,
                        'tgl'       => date('Y-m-d'),
                        'type'      => 'spare part',
                        'm_warehouse_id' => $ts_warehouse['warehouse_from'],
                        'm_warehouse_id2' => $ts_warehouse['warehouse_to'],
                        'ts_warehouse_id' => $ts_warehouse['id'],
                        'location_id'   => $this->site_id
                    );
                    $this->journalTransfer($input_jurnal);
                }
            }
        }

        $notification = array(
            'message' => 'Success Create Penerimaan Transfer Stok Gudang',
            'alert-type' => 'success'
        );

        return redirect('transfer_stok/terima_ts_warehouse')->with($notification);
    }
    public function penerimaTSWarehouseJson(){
        $response = null;
        $query=DB::table('ts_warehouses as tw')
                        ->join('m_warehouses as mw1', 'mw1.id', '=', 'tw.warehouse_to')
                        ->join('m_warehouses as mw2', 'mw2.id', '=', 'tw.warehouse_from')
                        ->join('sites as s', 's.id', '=', 'tw.site_id')
                        ->where('is_sent', true)
                        ->select('s.name as site_name', 'mw1.name as to', 'mw2.name as from', 'tw.*');
        if ($this->site_id != null) {
            $query->where('tw.site_id', $this->site_id);
        }
        if ($this->m_warehouse_id != null) {
            $query->where('tw.warehouse_to', $this->m_warehouse_id);
        }
        $query->get();
        $data=DataTables::of($query)
                        ->make(true); 
        return $data;
    }
    public function suratJalanTSWarehouse($id){
        try
        {
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . 'inv/base/TsWarehouse/'.$id]);
            $response = $client->request('GET', '', ['headers' => $headers]);  
            $body = $response->getBody();
            $content = $body->getContents();
            $response_array = json_decode($content,TRUE);
            $ts_warehouse=$response_array['data'];
        } catch(RequestException $exception) {

        }
        
        $query=DB::table('ts_warehouse_ds')->where('ts_warehouse_id', $id)->get();
        foreach ($query as $key => $value) {
            $value->m_items=DB::table('m_items')->where('id', $value->m_item_id)->first();
            $value->m_units=DB::table('m_units')->where('id', $value->m_unit_id)->first();
        }
        $data=array(
            'ts_warehouse'  => $ts_warehouse,
            'ts_warehouse_d'    => $query
        );
        return view('pages.inv.transfer_stok.print_surat_jalan_ts_warehouse', $data);
    }
}
