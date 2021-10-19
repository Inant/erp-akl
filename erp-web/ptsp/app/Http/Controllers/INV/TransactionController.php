<?php

namespace App\Http\Controllers\INV;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Carbon\Carbon;
use DB;

class TransactionController extends Controller
{
    private $base_api_url;
    private $site_id = null;
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
        $data = array(
            'data' => array(),
            'date_gte' => null,
            'date_lte' => null,
            'm_item_id' => 'all',
            'is_entry' => 'all'
        );
        return view('pages.inv.inventory_transaction.inventory_transaction_list', $data);
    }

    public function siteStockIndex() {
        return view('pages.inv.inventory_transaction.site_stock_list');
    }

    public function indexPost(Request $request) {
        $date_gte = $request->post('date_gte');
        $date_lte = $request->post('date_lte');
        $m_item_id = $request->post('m_item_id');
        $is_entry = $request->post('is_entry');

        $mutasi_stok = null;
        try
        {
            $client = new Client(['base_uri' => $this->base_api_url . 'inv/mutasi_stok']);
            $reqBody = [
                'json' => [
                    'site_id' => $this->site_id,
                    'date_gte' => $date_gte,
                    'date_lte' => $date_lte,
                    'm_item_id' => $m_item_id,
                    'is_entry' => $is_entry
                ]
            ];
            $response = $client->request('POST', '', $reqBody);
            $body = $response->getBody();
            $content = $body->getContents();
            $response_array = json_decode($content,TRUE);
            $mutasi_stok = $response_array['data'];
        } catch(RequestException $exception) {
        }

        $data = array(
            'data' => $mutasi_stok,
            'date_gte' => $date_gte,
            'date_lte' => $date_lte,
            'm_item_id' => $m_item_id,
            'is_entry' => $is_entry
        );
        return view('pages.inv.inventory_transaction.inventory_transaction_list', $data);
    }

    // function json
    public function getStok() {
        $response = null;
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

            $response = $content;
        } catch(RequestException $exception) {

        }

        return $response;
    }
    public function getPurchase()
    {
        $is_error = false;
        $error_message = '';  

        $data = array(
            'error' => array(
                'is_error' => $is_error,
                'error_message' => $error_message
            )
        );

        return view('pages.inv.purchase_order.purchase_listr', $data);
    }
    public function getPurchaseJson()
    {
        $response = null;
        try
        {
            $client = new Client(['base_uri' => $this->base_api_url . '/inv/getPurchase?site_id='.$this->site_id]);  
            $response = $client->request('GET', ''); 
            $body = $response->getBody();
            $content = $body->getContents();
            $response_array = json_decode($content,TRUE);
        
            $response = $content;         
        } catch(RequestException $exception) {
            
        }    
        
        return $response;
    }
    public function getPurchaseDetJson($id)
    {
        $response = null;
        try
        {
            $client = new Client(['base_uri' => $this->base_api_url . '/inv/getPurchase/detail/'.$id]);  
            $response = $client->request('GET', ''); 
            $body = $response->getBody();
            $content = $body->getContents();
            $response_array = json_decode($content,TRUE);

            $response = $content;         
        } catch(RequestException $exception) {
            
        }    

        return $response;
    }
    public function isClosed($id)
    {
        $update=DB::table('purchases')->where('id', $id)->update(
            [
                'is_closed' => true,
            ]
        );
        if($update){
            return redirect('inventory/purchase');
        }else{
            echo "gagal";
        }
    }
}
