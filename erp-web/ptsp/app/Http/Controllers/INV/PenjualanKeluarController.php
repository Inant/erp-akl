<?php

namespace App\Http\Controllers\INV;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use App\Http\Controllers\RAB\RabController;
use Carbon\Carbon;
use DB;
class PenjualanKeluarController extends Controller
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
            return $next($request);
        });

        $this->base_api_url = env('API_URL');
    }

    public function index() {
        return view('pages.inv.penjualan_keluar.penjualan_keluar_list');
    }

    public function create() {
        $data = array(
            'site_id' => $this->site_id
        );

        return view('pages.inv.penjualan_keluar.penjualan_keluar_create', $data);
    }

    public function createPost(Request $request) {
        $m_item_no = $request->post('m_item_no');
        $m_item_id = $request->post('m_item_id');
        $stok_site = $request->post('stok_site');
        $qty = $request->post('qty');
        $price = $request->post('price');
        $m_unit_id = $request->post('m_unit_name');

        $isSubmit = true;
        for($i = 0; $i < count($m_item_id); $i++) {
            if ($stok_site[$i] < $qty[$i]) {
                $isSubmit = false;
                break;
            }
        }

        if ($isSubmit) {
            $period_year = Carbon::now()->year;
            $period_month = Carbon::now()->month;
            $rabcon = new RabController();
            $inv_no = $rabcon->generateTransactionNo('INV_OUT', $period_year, $period_month, $this->site_id );
            $inv_sale_no = $rabcon->generateTransactionNo('INV_SALE', $period_year, $period_month, $this->site_id );
            try
            {
                // ini_set('memory_limit', '-1');
                $client = new Client(['base_uri' => $this->base_api_url . '/inv/base/InvSale']);
                $reqBody = [
                    'json' => [
                        'site_id' => $this->site_id,
                        'no' => $inv_sale_no
                        ]
                    ]; 
                    $response = $client->request('POST', '', $reqBody); 
                    $body = $response->getBody();
                    $content = $body->getContents();
                    $response_array = json_decode($content,TRUE);
                    $inv_sale = $response_array['data'];
            } catch(RequestException $exception) {
            }

            //insert inv_trx
            
            try
            {
                $client = new Client(['base_uri' => $this->base_api_url . '/inv/base/InvTrx']);
                $reqBody = [
                    'json' => [
                        'm_warehouse_id' => 1,
                        'purchase_id' => null,
                        'trx_type' => 'INV_SALE',
                        'inv_request_id' => null,
                        'no' => $inv_no,
                        'inv_trx_date' => Carbon::now()->toDateString(),
                        'site_id' => $this->site_id,
                        'is_entry' => false,
                        // 'inv_sale_id' => $inv_sale['id']
                        ]
                    ]; 
                    $response = $client->request('POST', '', $reqBody); 
                    $body = $response->getBody();
                    $content = $body->getContents();
                    $response_array = json_decode($content,TRUE);
                    $inv_trx = $response_array['data'];
            } catch(RequestException $exception) {
                
            }
            // print_r($inv_trx);
            // exit();
            
            for ($i = 0; $i < count($m_item_id); $i++) {
                try
                {
                    $client = new Client(['base_uri' => $this->base_api_url . '/inv/base/InvSaleD']);
                    $reqBody = [
                        'json' => [
                            'inv_sale_id' => $inv_sale['id'],
                            'm_item_id' => $m_item_id[$i],
                            'amount' => $qty[$i],
                            'base_price' => $price[$i]
                        ]
                    ]; 
                    $response = $client->request('POST', '', $reqBody); 
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
                            'value' => $price[$i] * $qty[$i]
                            ]
                        ]; 
                        $response = $client->request('POST', '', $reqBody); 
                } catch(RequestException $exception) {
                }   
            }

            $notification = array(
                'message' => 'Success Penjualan Keluar',
                'alert-type' => 'success'
            );
        } else {
            $notification = array(
                'message' => 'Error, Stock cannot smaller than request',
                'alert-type' => 'error'
            );
        }

        return redirect('penjualan_keluar')->with($notification);
    }

    public function listPenjualanKeluarJson() {
        $response = null;
        try
        {
            $client = new Client(['base_uri' => $this->base_api_url . '/inv/penjualan_keluar?site_id='.$this->site_id]);  
            $response = $client->request('GET', ''); 
            $body = $response->getBody();
            $content = $body->getContents();
            $response_array = json_decode($content,TRUE);

            $response = $content;         
        } catch(RequestException $exception) {
            
        }    

        return $response;
    }

    public function listPenjualanKeluarDetailJson($id) {
        $response = null;
        try
        {
            $client = new Client(['base_uri' => $this->base_api_url . '/inv/penjualan_keluar_detail/'.$id]);  
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
