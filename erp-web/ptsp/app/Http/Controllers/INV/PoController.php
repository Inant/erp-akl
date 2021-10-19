<?php

namespace App\Http\Controllers\INV;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Carbon\Carbon;

class PoController extends Controller
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

    public function poKonstruksiIndex()
    {
        $is_error = false;
        $error_message = '';  

        $data = array(
            'error' => array(
                'is_error' => $is_error,
                'error_message' => $error_message
            )
        );
        
        return view('pages.inv.purchase_order.po_konstruksi_list', $data);
    }

    public function poKhususIndex()
    {
        $is_error = false;
        $error_message = '';  

        $data = array(
            'error' => array(
                'is_error' => $is_error,
                'error_message' => $error_message
            )
        );
        
        return view('pages.inv.purchase_order.po_khusus_list', $data);
    }

    public function poKhususApprovalIndex()
    {
        $is_error = false;
        $error_message = '';  

        $data = array(
            'error' => array(
                'is_error' => $is_error,
                'error_message' => $error_message
            )
        );
        
        return view('pages.inv.purchase_order.po_khusus_approval_list', $data);
    }

    public function poKhususApproval($id)
    {
        $is_error = false;
        $error_message = '';  

        $purchase = $this->getPurchaseById($id);
        $purchase = json_decode($purchase, TRUE);
        $purchase = $purchase['data'];

        $data = array(
            'error' => array(
                'is_error' => $is_error,
                'error_message' => $error_message
            ),
            'purchase' => $purchase
        );

        // echo json_encode($purchase);
        
        return view('pages.inv.purchase_order.po_khusus_approval_detail', $data);
    }

    public function poKhususApprovalApprove($id){
        $is_error = false;
        $error_message = '';  

        $purchase = $this->getPurchaseById($id);
        $purchase = json_decode($purchase, TRUE);
        $purchase = $purchase['data'];

        $data = array(
            'error' => array(
                'is_error' => $is_error,
                'error_message' => $error_message
            ),
            'purchase' => $purchase
        );

        // echo json_encode($purchase);
        
        return view('pages.inv.purchase_order.po_khusus_approval_approve', $data);
    }

    public function poKhususApprovalApprovePost(Request $request)
    {
        $purchase_id = $request->post('purchase_id');
        $apv_decision = $request->post('apv_decision');

        $purchase_approval = $this->getPurchaseApprovalByPurchaseId($purchase_id);
        $purchase_approval = json_decode($purchase_approval, TRUE);
        $purchase_approval = $purchase_approval['data'];

        if($purchase_approval == null)
        {
            try
                    {
                        $client = new Client(['base_uri' => $this->base_api_url . '/inv/base/PurchaseApproval']);
                        $reqBody = [
                            'json' => [
                                'purchase_id' => $purchase_id,
                                'is_apv' => true,
                                'apv_date' => Carbon::now()->toDateString(),
                                'apv_by' => $this->username,
                                'apv_decision' => $apv_decision
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
            'message' => 'Purchase Order succesfully approve',
            'alert-type' => 'success'
        );

        return redirect('po_spesial_approval')->with($notification);
    }

    public function poKhususApprovalDecline($id){
        $purchase_approval = $this->getPurchaseApprovalByPurchaseId($id);
        $purchase_approval = json_decode($purchase_approval, TRUE);
        $purchase_approval = $purchase_approval['data'];

        if($purchase_approval == null)
        {
            try
                    {
                        $client = new Client(['base_uri' => $this->base_api_url . '/inv/base/PurchaseApproval']);
                        $reqBody = [
                            'json' => [
                                'purchase_id' => $id,
                                'is_apv' => false,
                                'apv_date' => Carbon::now()->toDateString(),
                                'apv_by' => $this->username
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
            'message' => 'Purchase Order succesfully decline',
            'alert-type' => 'success'
        );

        return redirect('po_spesial_approval')->with($notification);
    }

    public function printPO($id) {
        $purchase = null;
        $purchase_d = null;

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

        // Get Sites
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

        try
        {
            $client = new Client(['base_uri' => $this->base_api_url . '/inv/po/purchase_d_by_purchase_id/'.$id]);  
            $response = $client->request('GET', ''); 
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
        return view('pages.inv.purchase_order.print_purchase_order', $data);
    }

    public function getPOKonstruksiJson(){
        $response = null;
        try
        {
            $client = new Client(['base_uri' => $this->base_api_url . '/inv/po/po_konstruksi?site_id='.$this->site_id]);  
            $response = $client->request('GET', ''); 
            $body = $response->getBody();
            $content = $body->getContents();
            $response_array = json_decode($content,TRUE);

            $response = $content;         
        } catch(RequestException $exception) {
            
        }    

        return $response;
    }

    public function getPOKhususJson(){
        $response = null;
        try
        {
            $client = new Client(['base_uri' => $this->base_api_url . '/inv/po/po_khusus']);  
            $response = $client->request('GET', ''); 
            $body = $response->getBody();
            $content = $body->getContents();
            $response_array = json_decode($content,TRUE);

            $response = $content;         
        } catch(RequestException $exception) {
            
        }    

        return $response;
    }

    public function getPOKhususApprovalJson(){
        $response = null;
        try
        {
            $client = new Client(['base_uri' => $this->base_api_url . '/inv/po/po_khusus_approval']);  
            $response = $client->request('GET', ''); 
            $body = $response->getBody();
            $content = $body->getContents();
            $response_array = json_decode($content,TRUE);

            $response = $content;         
        } catch(RequestException $exception) {
            
        }    

        return $response;
    }

    public function getPOKhususPembelianKhususJson(){
        $response = null;
        try
        {
            $client = new Client(['base_uri' => $this->base_api_url . '/inv/po/pembelian_khusus']);  
            $response = $client->request('GET', ''); 
            $body = $response->getBody();
            $content = $body->getContents();
            $response_array = json_decode($content,TRUE);

            $response = $content;         
        } catch(RequestException $exception) {
            
        }    

        return $response;
    }

    public function getPODetailJson($id){
        $response = null;
        try
        {
            $client = new Client(['base_uri' => $this->base_api_url . '/inv/po/purchase_d_by_purchase_id/'.$id]);  
            $response = $client->request('GET', ''); 
            $body = $response->getBody();
            $content = $body->getContents();
            $response_array = json_decode($content,TRUE);

            $response = $content;         
        } catch(RequestException $exception) {
            
        }    

        return $response;
    }

    public function getPurchaseById($id){
        $response = null;
        try
        {
            $client = new Client(['base_uri' => $this->base_api_url . '/inv/purchase/'.$id]);  
            $response = $client->request('GET', ''); 
            $body = $response->getBody();
            $content = $body->getContents();
            $response_array = json_decode($content,TRUE);

            $response = $content;         
        } catch(RequestException $exception) {
            
        }    

        return $response;
    }

    public function getPurchaseApprovalByPurchaseId($id){
        $response = null;
        try
        {
            $client = new Client(['base_uri' => $this->base_api_url . '/inv/purchase_approval/'.$id]);  
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
