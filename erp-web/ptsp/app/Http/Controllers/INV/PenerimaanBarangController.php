<?php

namespace App\Http\Controllers\INV;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\RAB\RabController;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Carbon\Carbon;
use DB;

class PenerimaanBarangController extends Controller
{
    private $base_api_url;
    private $site_id = null;
    private $user_name = null;
    public function __construct()
    {
        //Authenticate page menu
        $this->middleware(function ($request, $next) {
            Controller::isLogin(auth()->user()['role_id']); 
            $this->site_id = auth()->user()['site_id'];
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
            
        return view('pages.inv.penerimaan_barang.penerimaan_barang_list', $data);
    }
        
    public function receive($id)
    {
        $is_error = false;
        $error_message = '';  
            
        $data = array(
                'error' => array(
                    'is_error' => $is_error,
                    'error_message' => $error_message
                ),
                'purchase_id' => $id
        );
            
        return view('pages.inv.penerimaan_barang.penerimaan_barang_receive', $data);
    }
        
    public function receivePost(Request $request)
    {
        $submit = $request->submit; //receive || decline
        $purchase_id = $request->post('purchase_id');
        $m_item_id = $request->post('m_item_id');
        $m_unit_id = $request->post('m_unit_id');
        $receive_volume = $request->post('receive_volume');
        $notes = $request->post('notes');
        $driver = $request->post('driver');
        
        DB::beginTransaction();
        try
        {
            //insert inv_trx
            $period_year = Carbon::now()->year;
            $period_month = Carbon::now()->month;
            $rabcon = new RabController();
            $inv_no = $rabcon->generateTransactionNo('INV_RCV', $period_year, $period_month, $this->site_id );
            try
            {
                $client = new Client(['base_uri' => $this->base_api_url . 'inv/base/InvTrx']);
                $reqBody = [
                    'json' => [
                        'm_warehouse_id' => 1,
                        'purchase_id' => $purchase_id[0],
                        'trx_type' => 'RECEIPT',
                        'inv_request_id' => null,
                        'no' => $inv_no,
                        'inv_trx_date' => Carbon::now()->toDateString(),
                        'site_id' => $this->site_id,
                        'is_entry' => true
                        ]
                    ]; 
                    $response = $client->request('POST', '', $reqBody); 
                    $body = $response->getBody();
                    $content = $body->getContents();
                    $response_array = json_decode($content,TRUE);
                    $inv_trx = $response_array['data'];

                    
            } catch(RequestException $exception) {
            }

            //insert inv_trx_d
            for($i = 0; $i < count($m_item_id); $i++){
                if($receive_volume[$i] > 0) {
                    try
                    {
                        $client = new Client(['base_uri' => $this->base_api_url . '/inv/base/InvTrxD']);
                        $reqBody = [
                            'json' => [
                                'inv_trx_id' => $inv_trx['id'],
                                'm_item_id' => $m_item_id[$i],
                                'amount' => $receive_volume[$i],
                                'm_unit_id' => $m_unit_id[$i],
                                'notes' => $notes[$i]
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

            if($this->checkIsClosedPo($purchase_id[0])){
                try
                {
                    $client = new Client(['base_uri' => $this->base_api_url . '/inv/base/Purchase/'.$purchase_id[0]]);
                    $reqBody = [
                        'json' => [
                            'is_closed' => true,
                            'is_receive' => true,
                            'ekspedisi' => $driver
                            ]
                        ]; 
                        $response = $client->request('PUT', '', $reqBody); 
                        $body = $response->getBody();
                        $content = $body->getContents();
                        $response_array = json_decode($content,TRUE);
                } catch(RequestException $exception) {
                }
            }
                
            DB::commit();
        }
        catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }


        $notification = array(
            'message' => 'Success receipt material',
            'alert-type' => 'success'
        );

        return redirect('penerimaan_barang')->with($notification);
    }

    public function decline($id) {
        try
        {
            $client = new Client(['base_uri' => $this->base_api_url . '/inv/base/Purchase/'.$id]);
            $reqBody = [
                'json' => [
                    'is_closed' => true,
                    'is_receive' => false
                    ]
                ]; 
                $response = $client->request('PUT', '', $reqBody); 
                $body = $response->getBody();
                $content = $body->getContents();
                $response_array = json_decode($content,TRUE);
        } catch(RequestException $exception) {
        }

        $notification = array(
            'message' => 'Success decline purchase order',
            'alert-type' => 'success'
        );

        return redirect('penerimaan_barang')->with($notification);
    }

    public function printPenerimaanBarang($id) {
        $purchase = null;
        $purchase_d = null;

        $hitung_h = 0;
        while($purchase == null && $hitung_h < 100) {
            // Get Header
            try
            {
                $client = new Client(['base_uri' => $this->base_api_url . '/inv/base/Purchase/' . $id]);  
                $response = $client->request('GET', ''); 
                $body = $response->getBody();
                $content = $body->getContents();
                $response_array = json_decode($content,TRUE);

                $purchase = $response_array['data'];         
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
                $client = new Client(['base_uri' => $this->base_api_url . 'inv/inv_trx/get_by_purchase_id/' . $id]);  
                $response = $client->request('GET', ''); 
                $body = $response->getBody();
                $content = $body->getContents();
                $response_array = json_decode($content,TRUE);

                $inv_trx = $response_array['data'];         
            } catch(RequestException $exception) {
                
            }
            $hitung_inv_trx++;
        }
        $purchase['inv_trx'] = $inv_trx;

        // Get Supplier
        $m_suppliers = null;
        if ($purchase != null) {
            try
            {
                $client = new Client(['base_uri' => $this->base_api_url . '/inv/base/MSupplier/' . $purchase['m_supplier_id']]);  
                $response = $client->request('GET', ''); 
                $body = $response->getBody();
                $content = $body->getContents();
                $response_array = json_decode($content,TRUE);

                $m_suppliers = $response_array['data'];         
            } catch(RequestException $exception) {
            }
        }

        $purchase['m_suppliers'] = $m_suppliers;

        $po_controller = new PoController();
        $purchase_d = $po_controller->getPODetailJson($id);
        $purchase_d = json_decode($purchase_d, TRUE);
        $purchase_d = $purchase_d['data'];

        $data = array(
            'purchase' => $purchase,
            'purchase_d' => $purchase_d,
            'user_name' => $this->user_name
        );
        // print_r($data);
        // exit();
        return view('pages.inv.penerimaan_barang.print_penerimaan_barang', $data);
    }
            
    public function getAllOpenPurchase(){
        $response = null;
        try
        {
                    $client = new Client(['base_uri' => $this->base_api_url . '/inv/po/all_open?site_id='.$this->site_id]);  
                    $response = $client->request('GET', ''); 
                    $body = $response->getBody();
                    $content = $body->getContents();
                    $response_array = json_decode($content,TRUE);
                    
                    $response = $content;         
        } catch(RequestException $exception) {
                    
        }    
                
        return $response;
    }

    public function getPenerimaanDetailJson($id){
        $response = null;
        $po_controller = new PoController();
        $datas = $po_controller->getPODetailJson($id);
        $datas = json_decode($datas, TRUE);
        $datas = $datas['data'];
        
        $penerimaan = $this->getPenerimaanByPurchaseIdJson($id);
        $penerimaan = json_decode($penerimaan, TRUE);
        $penerimaan = $penerimaan['data'];
        if(count($penerimaan) > 0)
            $penerimaan = $penerimaan;
        else
            $penerimaan = null;
            
        $data_show = array();
        for($i = 0; $i < count($datas); $i++){
            $volume = $datas[$i]['amount'];
            $m_item_id = $datas[$i]['m_item_id'];
            if($penerimaan != null){
                for($j = 0; $j < count($penerimaan); $j++){
                    $dt_penerimaan = $penerimaan[$j]['inv_trx_ds'];
                    for($k = 0; $k < count($dt_penerimaan); $k++){
                        if($m_item_id == $dt_penerimaan[$k]['m_item_id'])
                            $volume -= $dt_penerimaan[$k]['amount'];
                    }
                }
            }
            $volume = round($volume, 2);
            $datas[$i]['amount'] = $volume;

            if($volume > 0)
                array_push($data_show, $datas[$i]);
        }

        $response = array('data' => $data_show);
        $response = json_encode($response);

        return $response;
    }

    public function getPenerimaanByPurchaseIdJson($id){
        $response = null;
        try
        {
            $client = new Client(['base_uri' => $this->base_api_url . '/inv/inv_trx/get_by_purchase_id/'.$id]);  
            $response = $client->request('GET', ''); 
            $body = $response->getBody();
            $content = $body->getContents();
            $response_array = json_decode($content,TRUE);
                    
            $response = $content;         
        } catch(RequestException $exception) {
                    
        }    
                
        return $response;
    }

    private function checkIsClosedPo($id){
        $is_closed = true;
        $po_controller = new PoController();
        $datas = $po_controller->getPODetailJson($id);
        $datas = json_decode($datas, TRUE);
        $datas = $datas['data'];
        
        $penerimaan = $this->getPenerimaanByPurchaseIdJson($id);
        $penerimaan = json_decode($penerimaan, TRUE);
        $penerimaan = $penerimaan['data'];
        if(count($penerimaan) > 0)
            $penerimaan = $penerimaan;
        else
            $penerimaan = null;
            
        for($i = 0; $i < count($datas); $i++){
            $volume = $datas[$i]['amount'];
            $m_item_id = $datas[$i]['m_item_id'];
            if($penerimaan != null){
                for($j = 0; $j < count($penerimaan); $j++){
                    $dt_penerimaan = $penerimaan[$j]['inv_trx_ds'];
                    for($k = 0; $k < count($dt_penerimaan); $k++){
                        if($m_item_id == $dt_penerimaan[$k]['m_item_id'])
                            $volume -= $dt_penerimaan[$k]['amount'];
                    }
                }
            }
            $datas[$i]['amount'] = $volume;

            if($volume > 0)
                $is_closed = false;
        }

        return $is_closed;
    }

}
        
        
        