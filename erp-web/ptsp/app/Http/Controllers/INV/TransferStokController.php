<?php

namespace App\Http\Controllers\INV;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use App\Http\Controllers\RAB\RabController;
use Carbon\Carbon;

class TransferStokController extends Controller
{
    private $base_api_url;
    private $site_id = null;
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
        $notes = $request->post('keterangan');

        //Insert transfer stock header
        $period_year = Carbon::now()->year;
        $period_month = Carbon::now()->month;
        $rabcon = new RabController();
        $trf_no = $rabcon->generateTransactionNo('TRF_REQ', $period_year, $period_month, $this->site_id );
        try
        {
            $client = new Client(['base_uri' => $this->base_api_url . 'inv/base/TransferStock']);
            $reqBody = [
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
                $client = new Client(['base_uri' => $this->base_api_url . 'inv/base/TransferStockD']);
                $reqBody = [
                'json' => [
                        'transfer_stock_id' => $transfer_stock['id'],
                        'm_item_id' => $m_item_id[$i],
                        'amount' => $amount[$i],
                        'm_unit_id' => $m_unit_id[$i],
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
            $client = new Client(['base_uri' => $this->base_api_url . 'inv/base/TransferStock/'.$transfer_stock_id]);
            $reqBody = [
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
            $client = new Client(['base_uri' => $this->base_api_url . 'inv/base/InvTrx']);
            $reqBody = [
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
                $client = new Client(['base_uri' => $this->base_api_url . 'inv/base/TransferStockD/'.$transfer_stock_d_id[$i]]);
                $reqBody = [
                    'json' => [
                        'actual_amount' => $actual_amount[$i]
                    ]
                ]; 
                $response = $client->request('PUT', '', $reqBody); 
            } catch(RequestException $exception) {
            }

            try
            {
                $client = new Client(['base_uri' => $this->base_api_url . 'inv/base/InvTrxD']);
                $reqBody = [
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
            $client = new Client(['base_uri' => $this->base_api_url . 'inv/base/TransferStock/'.$transfer_stock_id]);
            $reqBody = [
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
            $client = new Client(['base_uri' => $this->base_api_url . 'inv/base/InvTrx']);
            $reqBody = [
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
                $client = new Client(['base_uri' => $this->base_api_url . 'inv/base/TransferStockD/'.$transfer_stock_d_id[$i]]);
                $reqBody = [
                    'json' => [
                        'actual_amount' => $actual_amount[$i]
                    ]
                ]; 
                $response = $client->request('PUT', '', $reqBody); 
            } catch(RequestException $exception) {
            }

            try
            {
                $client = new Client(['base_uri' => $this->base_api_url . 'inv/base/InvTrxD']);
                $reqBody = [
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
            $client = new Client(['base_uri' => $this->base_api_url . '/inv/base/TransferStock/' . $id]);  
            $response = $client->request('GET', ''); 
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
                $client = new Client(['base_uri' => $this->base_api_url . '/inv/base/Site/' . $transfer_stock['site_from']]);  
                $response = $client->request('GET', ''); 
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
            $client = new Client(['base_uri' => $this->base_api_url . 'inv/transfer_stok_detail/' . $id]);  
            $response = $client->request('GET', ''); 
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
    //         $client = new Client(['base_uri' => $this->base_api_url . 'inv/base/TransferStock/'.$id]);
    //         $reqBody = [
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
            $client = new Client(['base_uri' => $this->base_api_url . 'inv/transfer_stok']);  
            $response = $client->request('GET', ''); 
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
            $client = new Client(['base_uri' => $this->base_api_url . 'inv/transfer_stok_detail/' . $id]);  
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
