<?php

namespace App\Http\Controllers\INV;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Carbon\Carbon;

class PembelianRutinController extends Controller
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
        $rab = null;
        $is_error = false;
        $error_message = '';

        $data = array(
            'error' => array(
                'is_error' => $is_error,
                'error_message' => $error_message
            )
        );
        
        return view('pages.inv.pembelian_rutin.pembelian_rutin_create', $data);
    }

    public function createPost(Request $request){

        $project_worksub_d_id = $request->post('selected_project_worksub_d_id'); //order dari rab
        $purchase_d_id = $request->post('selected_purchase_d_id'); // order dari po canceled
        $suppl_single = $request->post('suppl_single');
        $cara_bayar_single = $request->post('cara_bayar_single');

        $m_item_id = $request->post('m_item_id');
        $volume = $request->post('volume');
        $m_unit_id = $request->post('m_unit_id');
        $perkiraan_harga_suppl = $request->post('perkiraan_harga_suppl');
        $suppl = array();
        $cara_bayar = array();

        // update project_worksub_d
        for($i = 0; $i < count($project_worksub_d_id); $i++){
            try
            {
                $client = new Client(['base_uri' => $this->base_api_url . 'rab/base/ProjectWorksubD/'.$project_worksub_d_id[$i]]);
                $reqBody = [
                    'json' => [
                        'buy_date' => date('Y-m-d')
                    ]
                ]; 
                $response = $client->request('PUT', '', $reqBody); 
                $body = $response->getBody();
                $content = $body->getContents();
                $response_array = json_decode($content,TRUE);
            } catch(RequestException $exception) {
                // $is_error = true;
                // $error_message .= $exception->getMessage();
            }
        }

        // update purchase_ds
        for($i = 0; $i < count($purchase_d_id); $i++){
            try
            {
                $client = new Client(['base_uri' => $this->base_api_url . 'rab/base/PurchaseD/'.$purchase_d_id[$i]]);
                $reqBody = [
                    'json' => [
                        'buy_date' => date('Y-m-d')
                    ]
                ]; 
                $response = $client->request('PUT', '', $reqBody); 
                $body = $response->getBody();
                $content = $body->getContents();
                $response_array = json_decode($content,TRUE);
            } catch(RequestException $exception) {
                // $is_error = true;
                // $error_message .= $exception->getMessage();
            }
        }

        // set suppl
        for($i = 0; $i < count($m_item_id); $i++){
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
            $po_no = $this->generateTransactionNo('PO', $period_year, $period_month, $this->site_id );
            $sum_perkiraan_harga_suppl = 0;
            for($j = 0; $j < count($perkiraan_harga_suppl); $j++){
                if($suppl[$j] == $supplPo[$i])
                    $sum_perkiraan_harga_suppl += ($perkiraan_harga_suppl[$j]*$volume[$j]);
            }

            try
            {
                $client = new Client(['base_uri' => $this->base_api_url . 'inv/base/Purchase']);
                $reqBody = [
                    'json' => [
                        'no' => $po_no,
                        'base_price' => $sum_perkiraan_harga_suppl,
                        'm_supplier_id' => $supplPo[$i],
                        'wop' => $wopPo[$i],
                        'purchase_date' => Carbon::now()->toDateString(),
                        'is_closed' => false,
                        'is_special' => false,
                        'site_id' => $this->site_id
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

            for($j = 0; $j < count($m_item_id); $j++){
                if($suppl[$j] == $supplPo[$i]){
                    try
                    {
                        $client = new Client(['base_uri' => $this->base_api_url . 'inv/base/PurchaseD']);
                        $reqBody = [
                            'json' => [
                                'purchase_id' => $purchase['id'],
                                'm_item_id' => $m_item_id[$j],
                                'amount' => $volume[$j],
                                'm_unit_id' => $m_unit_id[$j],
                                'base_price' => $perkiraan_harga_suppl[$j]
                            ]
                        ]; 
                        $response = $client->request('POST', '', $reqBody); 
                        $body = $response->getBody();
                        $content = $body->getContents();
                        $response_array = json_decode($content,TRUE);
                        $data = $response_array['data'];
                    } catch(RequestException $exception) {
                    }

                    try
                    {
                        $client = new Client(['base_uri' => $this->base_api_url . 'inv/best_price']);
                        $reqBody = [
                            'json' => [
                                'm_supplier_id' => $supplPo[$i],
                                'm_item_id' => $m_item_id[$j],
                                'best_price' => $perkiraan_harga_suppl[$j]
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
            'message' => 'Success purchase material',
            'alert-type' => 'success'
        );

        return redirect('po_konstruksi')->with($notification);
    }

    public function getMaterialPembelianRutin(){
        $response = null;
        try
        {
            $client = new Client(['base_uri' => $this->base_api_url . 'rab/get_material_pembelian_rutin?site_id='.$this->site_id]);  
            $response = $client->request('GET', ''); 
            $body = $response->getBody();
            $content = $body->getContents();
            $response_array = json_decode($content,TRUE);

            $response = $content;         
        } catch(RequestException $exception) {
            
        }    

        return $response;
    }

    public function getAllSupplier(){
        $response = null;
        try
        {
            $client = new Client(['base_uri' => $this->base_api_url . '/inv/base/MSupplier']);  
            $response = $client->request('GET', ''); 
            $body = $response->getBody();
            $content = $body->getContents();
            $response_array = json_decode($content,TRUE);

            $response = $content;         
        } catch(RequestException $exception) {
            
        }    

        return $response;
    }

    public function generateTransactionNo($trasaction_code, $period_year, $period_month, $site_id){
        try
        {
            $client = new Client(['base_uri' => $this->base_api_url . '/master/m_sequence/generate_trx_no']);
            $reqBody = [
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

    public function getPoCanceled(){
        $response = null;
        try
        {
            $client = new Client(['base_uri' => $this->base_api_url . '/inv/po_canceled?site_id='.$this->site_id]);  
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
