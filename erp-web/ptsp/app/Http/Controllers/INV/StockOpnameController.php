<?php

namespace App\Http\Controllers\INV;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use App\Http\Controllers\RAB\RabController;
use Carbon\Carbon;

class StockOpnameController extends Controller
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
        return view('pages.inv.stock_opname.stock_opname_list');
    }

    public function create()
    {
        return view('pages.inv.stock_opname.stock_opname_create');
    }

    public function createPost(Request $request) {
        $m_item_id = $request->post('m_item_id');
        $m_item_no = $request->post('m_item_no');
        $qty = $request->post('qty');
        $m_unit_id = $request->post('m_unit_id');
        $notes = $request->post('keterangan');

        //Insert transfer stock header
        $date_now = Carbon::now()->toDateString();
        $period_year = Carbon::now()->year;
        $period_month = Carbon::now()->month;
        $rabcon = new RabController();
        $sto_no = $rabcon->generateTransactionNo('STO', $period_year, $period_month, $this->site_id );
        try
        {
            $client = new Client(['base_uri' => $this->base_api_url . 'inv/base/StockOpname']);
            $reqBody = [
            'json' => [
                    'no' => $sto_no,
                    'site_id' => $this->site_id,
                    'date' => $date_now,
                    'is_closed' => false
                ]
            ];
            $response = $client->request('POST', '', $reqBody); 
            $body = $response->getBody();
            $content = $body->getContents();
            $response_array = json_decode($content,TRUE);
            $stock_opname = $response_array['data'];
        } catch(RequestException $exception) {
        }

        for($i = 0; $i < count($m_item_no); $i++) {
            try
            {
                $client = new Client(['base_uri' => $this->base_api_url . 'inv/base/StockOpnameD']);
                $reqBody = [
                'json' => [
                        'stock_opname_id' => $stock_opname['id'],
                        'm_item_id' => $m_item_id[$i],
                        'amount' => $this->getStokByMaterial($m_item_id[$i]),
                        'real_amount' => $qty[$i],
                        'notes' => $notes[$i]
                    ]
                ];
                $response = $client->request('POST', '', $reqBody); 
            } catch(RequestException $exception) {
            }
        }

        $notification = array(
            'message' => 'Success Create New Stock Opname',
            'alert-type' => 'success'
        );

        return redirect('stok_opname')->with($notification);
    }

    public function printAllStock() {
        try
        {
            if($this->site_id != null)
                $client = new Client(['base_uri' => $this->base_api_url . '/inv/stok/' . $this->site_id]);  
            else
                $client = new Client(['base_uri' => $this->base_api_url . '/inv/stok']);  
            $response = $client->request('GET', ''); 
            $body = $response->getBody();
            $content = $body->getContents();
            $response_array = json_decode($content,TRUE);

            $response = $response_array['data'];         
        } catch(RequestException $exception) {
            
        }

        $data = array(
            'list_stok' => $response
        );

        return view('pages.inv.stock_opname.stock_opname_print_stock', $data);
    }
    

    // JSOn FUNCTION
    public function materialByNoJson() {
        $no = $_GET['no'];
        $response = null;
        try
        {
            $client = new Client(['base_uri' => $this->base_api_url . 'rab/material_by_no']);
            $reqBody = [
                'json' => [
                    'no' => $no
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

    public function listStokOpnameJson() {
        $response = null;
        try
        {
            $client = new Client(['base_uri' => $this->base_api_url . 'inv/stok_opname']);  
            $response = $client->request('GET', ''); 
            $body = $response->getBody();
            $content = $body->getContents();
            $response_array = json_decode($content,TRUE);

            $response = $content;         
        } catch(RequestException $exception) {
            
        }    

        return $response;
    }

    public function listStokOpnameDetailJson($id) {
        $response = null;
        try
        {
            $client = new Client(['base_uri' => $this->base_api_url . 'inv/stok_opname_detail/' . $id]);  
            $response = $client->request('GET', ''); 
            $body = $response->getBody();
            $content = $body->getContents();
            $response_array = json_decode($content,TRUE);

            $response = $content;         
        } catch(RequestException $exception) {
            
        }    

        return $response;
    }

    // Other method
    private function getStokByMaterial($m_item_id) {
        try
        {
            $client = new Client(['base_uri' => $this->base_api_url . 'inv/stok/'.$this->site_id.'?m_item_id='.$m_item_id]);
            $response = $client->request('GET', ''); 
            $body = $response->getBody();
            $content = $body->getContents();
            $response_array = json_decode($content,TRUE);
        } catch(RequestException $exception) {
        }

        if(count($response_array['data']) > 0)
            return (float)$response_array['data'][0]['stok'];
        else
            return 0;
    }

}
