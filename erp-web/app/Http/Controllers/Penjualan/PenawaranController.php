<?php

namespace App\Http\Controllers\Penjualan;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use GuzzleHttp\Client;
use Yajra\DataTables\Facades\DataTables;
use GuzzleHttp\Exception\RequestException;
use App\Http\Controllers\RAB\RabController;
use App\Http\Controllers\Accounting\AkuntanController;
use Carbon\Carbon;
use DB;

class PenawaranController extends Controller
{
    private $base_api_url;
    private $site_id = null;
    private $username = null;
    private $user_id = null;
    private $m_warehouse_id = null;
    public function __construct()
    {
        //Authenticate page menu
        $this->middleware(function ($request, $next) {
            Controller::isLogin(auth()->user()['role_id']);
            $this->site_id = auth()->user()['site_id']; 
            $this->username = auth()->user()['email'];
            $this->user_id = auth()->user()['id'];
            $this->m_warehouse_id = auth()->user()['m_warehouse_id'];
            return $next($request);
        });

        $this->base_api_url = env('API_URL');
    }

    public function index() {
        // return Controller::isLogin(auth()->user()['role_id']);
        return view('pages.penjualan.penawaran.penawaran_list');
    }

    public function listPenawaranJson() {
        $response = null;
        try
        {
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . '/penjualan/penawaran?site_id='.$this->site_id]);  
            $response = $client->request('GET', '', ['headers' => $headers]);  
            $body = $response->getBody();
            $content = $body->getContents();
            $response_array = json_decode($content,TRUE);

            $response = $content;         
        } catch(RequestException $exception) {
            
        }    
        $data=DataTables::of($response_array['data'])
                                ->make(true);             

        return $data;
    }
}