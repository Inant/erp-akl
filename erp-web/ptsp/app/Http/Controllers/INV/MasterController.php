<?php

namespace App\Http\Controllers\INV;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use App\Http\Controllers\RAB\RabController;
use Carbon\Carbon;
use DB;

class MasterController extends Controller
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

    public function indexMasterMaterial() {
        return view('pages.inv.master_material.master_material_list');
    }

    public function createItem() {
        return view('pages.inv.master_material.master_material_create'); 
    }

    public function createItemPost(Request $request) {
        $no = $request->post('no');
        $name = $request->post('name');
        $lead_time = $request->post('lead_time');
        $m_unit_id = $request->post('m_unit_id');
        $cek=DB::table('m_items')->where('no', $no)->count();
        
        if ($cek == 0) {
            try
            {
                $client = new Client(['base_uri' => $this->base_api_url . 'inv/base/MItem']);
                $reqBody = [
                    'json' => [
                        'no' => $no,
                        'name' => $name,
                        'category' => '',
                        'volume' => 0,
                        'late_time' => $lead_time,
                        'm_unit_id' => $m_unit_id,
                        'type' => 1
                    ]
                ]; 
                $response = $client->request('POST', '', $reqBody); 
            } catch(RequestException $exception) {
            }

            $notification = array(
                'message' => 'Success receipt material',
                'alert-type' => 'success'
            );

            return redirect('master_material')->with($notification);
        }else{
            $notification = array(
                'message' => 'Failed receipt material',
                'alert-type' => 'warning'
            );

            return redirect('master_material')->with($notification);
        }
    }

    public function editItem($id) {
        try
        {
            $client = new Client(['base_uri' => $this->base_api_url . 'inv/base/MItem/' . $id]);  
            $response = $client->request('GET', ''); 
            $body = $response->getBody();
            $content = $body->getContents();
            $response_array = json_decode($content,TRUE);
            
            $m_items = $response_array['data'];
        } catch(RequestException $exception) {    
        }  

        $data = array(
            'm_items' => $m_items
        );

        return view('pages.inv.master_material.master_material_edit', $data); 
    }

    public function editItemPost(Request $request, $id) {
        $no = $request->post('no');
        $name = $request->post('name');
        $lead_time = $request->post('lead_time');
        $m_unit_id = $request->post('m_unit_id');

        try
        {
            $client = new Client(['base_uri' => $this->base_api_url . 'inv/base/MItem/' . $id]);  
            $reqBody = [
                'json' => [
                    'no' => $no,
                    'name' => $name,
                    'late_time' => $lead_time,
                    'm_unit_id' => $m_unit_id,
                ]
            ]; 
            $response = $client->request('PUT', '', $reqBody);
        } catch(RequestException $exception) {    
        }  

        $notification = array(
            'message' => 'Success edit material',
            'alert-type' => 'success'
        );

        return redirect('master_material')->with($notification);
    }

    public function deleteItem($id) {
        try
        {
            $client = new Client(['base_uri' => $this->base_api_url . 'inv/base/MItem/' . $id]);  
            $response = $client->request('DELETE', ''); 
        } catch(RequestException $exception) {    
        }

        $notification = array(
            'message' => 'Success delete material',
            'alert-type' => 'success'
        );

        return redirect('master_material')->with($notification);
    }

    public function indexMasterSatuan() {

        return view('pages.inv.master_satuan.master_satuan_list');
    }

    public function createUnit() {
        return view('pages.inv.master_satuan.master_satuan_create'); 
    }

    public function createUnitPost(Request $request) {
        $code = $request->post('code');
        $name = $request->post('name');

        try
        {
            $client = new Client(['base_uri' => $this->base_api_url . 'inv/base/MUnit']);
            $reqBody = [
                'json' => [
                    'code' => $code,
                    'name' => $name
                ]
            ]; 
            $response = $client->request('POST', '', $reqBody); 
        } catch(RequestException $exception) {
        }

        $notification = array(
            'message' => 'Success create satuan',
            'alert-type' => 'success'
        );

        return redirect('master_satuan')->with($notification);
    }

    public function editUnit($id) {
        try
        {
            $client = new Client(['base_uri' => $this->base_api_url . 'inv/base/MUnit/' . $id]);  
            $response = $client->request('GET', ''); 
            $body = $response->getBody();
            $content = $body->getContents();
            $response_array = json_decode($content,TRUE);
            
            $m_units = $response_array['data'];
        } catch(RequestException $exception) {    
        }  

        $data = array(
            'm_units' => $m_units
        );

        return view('pages.inv.master_satuan.master_satuan_edit', $data); 
    }

    public function editUnitPost(Request $request, $id) {
        $code = $request->post('code');
        $name = $request->post('name');

        try
        {
            $client = new Client(['base_uri' => $this->base_api_url . 'inv/base/MUnit/' . $id]);  
            $reqBody = [
                'json' => [
                    'code' => $code,
                    'name' => $name
                ]
            ]; 
            $response = $client->request('PUT', '', $reqBody);
        } catch(RequestException $exception) {    
        }  
        
        $notification = array(
            'message' => 'Success edit satuan',
            'alert-type' => 'success'
        );

        return redirect('master_satuan')->with($notification);
    }

    public function deleteUnit($id) {
        try
        {
            $client = new Client(['base_uri' => $this->base_api_url . 'inv/base/MUnit/' . $id]);  
            $response = $client->request('DELETE', ''); 
        } catch(RequestException $exception) {    
        }

        $notification = array(
            'message' => 'Success delete satuan',
            'alert-type' => 'success'
        );

        return redirect('master_satuan')->with($notification);
    }
    

    public function GetItemJson() {
        $response = null;
        try
        {
            $client = new Client(['base_uri' => $this->base_api_url . 'inv/base/MItem']);  
            $response = $client->request('GET', ''); 
            $body = $response->getBody();
            $content = $body->getContents();
            $response_array = json_decode($content,TRUE);

            $response = $content;         
        } catch(RequestException $exception) {
            
        }    

        return $response;
    }

    public function GetUnitJson() {
        $response = null;
        try
        {
            $client = new Client(['base_uri' => $this->base_api_url . 'inv/base/MUnit']);  
            $response = $client->request('GET', ''); 
            $body = $response->getBody();
            $content = $body->getContents();
            $response_array = json_decode($content,TRUE);

            $response = $content;         
        } catch(RequestException $exception) {
            
        }    

        return $response;
    }

    public function indexLagi() {
        echo 'test';
    }
    public function indexMasterKavling() {
        return view('pages.inv.master_kavling.master_kavling_list');
    }
    public function GetKavlingJson() {
        $response = null;
        try
        {
            $client = new Client(['base_uri' => $this->base_api_url . 'rab/kavling']);  
            $response = $client->request('GET', ''); 
            $body = $response->getBody();
            $content = $body->getContents();
            $response_array = json_decode($content,TRUE);

            $response = $content;         
        } catch(RequestException $exception) {
            
        }    

        return $response;
    }
    public function createKavling() {
        
        
        $site_location = null;
        $site_ = null;

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

        //set site location
        try
        {
            $client = new Client(['base_uri' => $this->base_api_url . 'inv/base/Site']);  
            $response = $client->request('GET', ''); 
            $body = $response->getBody();
            $content = $body->getContents();
            $response_array = json_decode($content, true);

            $site = $response_array['data'];
            
        } catch(RequestException $exception) {
            $is_error = true;
            $error_message .= $exception->getMessage();
        }
        $data=array('site' => $site,
                    'kota' => $site_location);
        return view('pages.inv.master_kavling.master_kavling_create', $data); 
    }
    public function createKavlingPost(Request $request) {
        $code = $request->post('code');
        $area = $request->post('area');
        $price = $request->post('price');
        $site = $request->post('site');
        $status = $request->post('status');
        
        try
        {
            $client = new Client(['base_uri' => $this->base_api_url . 'inv/base/Project']);
            $reqBody = [
                'json' => [
                    'site_id'       => $site,
                    'name'          => $code,
                    'area'          => $area,
                    'base_price'    => $price,
                    'sale_status'   => $status
                ]
            ]; 
            $response = $client->request('POST', '', $reqBody); 
        } catch(RequestException $exception) {
        }

        $notification = array(
            'message' => 'Success make kavling',
            'alert-type' => 'success'
        );

        return redirect('master_kavling')->with($notification);
    }
    public function editKavling($id) {
        $site=null;
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
        try
        {
            $client = new Client(['base_uri' => $this->base_api_url . 'inv/base/Site']);  
            $response = $client->request('GET', ''); 
            $body = $response->getBody();
            $content = $body->getContents();
            $response_array = json_decode($content, true);

            $site = $response_array['data'];
            
        } catch(RequestException $exception) {
            $is_error = true;
            $error_message .= $exception->getMessage();
        }
        try
        {
            $client = new Client(['base_uri' => $this->base_api_url . 'inv/base/Project/' . $id]);  
            $response = $client->request('GET', ''); 
            $body = $response->getBody();
            $content = $body->getContents();
            $response_array = json_decode($content,TRUE);
            
            $m_units = $response_array['data'];
        } catch(RequestException $exception) {    
        }  

        $data = array(
            'm_units' => $m_units,
            'site' => $site,
            'kota' => $site_location
        );
        
        return view('pages.inv.master_kavling.master_kavling_edit', $data); 
    }

    public function editKavlingPost(Request $request, $id) {
        $code = $request->post('code');
        $area = $request->post('area');
        $price = $request->post('price');
        $site = $request->post('site');
        $status = $request->post('status');
        
        try
        {
            $client = new Client(['base_uri' => $this->base_api_url . 'inv/base/Project/' . $id]);  
            $reqBody = [
                'json' => [
                    'site_id'       => $site,
                    'name'          => $code,
                    'area'          => $area,
                    'base_price'    => $price,
                    'sale_status'   => $status
                ]
            ]; 
            $response = $client->request('PUT', '', $reqBody);
        } catch(RequestException $exception) {    
        }  
        
        $notification = array(
            'message' => 'Success edit kavling',
            'alert-type' => 'success'
        );

        return redirect('master_kavling')->with($notification);
    }
    public function deleteKavling($id) {
        try
        {
            $client = new Client(['base_uri' => $this->base_api_url . 'inv/base/Project/' . $id]);  
            $response = $client->request('DELETE', ''); 
        } catch(RequestException $exception) {    
        }

        $notification = array(
            'message' => 'Success delete kavling',
            'alert-type' => 'success'
        );

        return redirect('master_kavling')->with($notification);
    }
    public function indexMasterSuplier() {
        return view('pages.inv.master_suplier.master_suplier_list');
    }
    public function GetSuplierJson() {
        $response = null;
        try
        {
            $client = new Client(['base_uri' => $this->base_api_url . 'inv/base/MSupplier']);  
            $response = $client->request('GET', ''); 
            $body = $response->getBody();
            $content = $body->getContents();
            $response_array = json_decode($content,TRUE);

            $response = $content;         
        } catch(RequestException $exception) {
            
        }    

        return $response;
    }
    public function createSuplier() {
        return view('pages.inv.master_suplier.master_suplier_create'); 
    }
    public function createSuplierPost(Request $request) {
        try
        {
            $client = new Client(['base_uri' => $this->base_api_url . 'inv/base/MSupplier']);
            $reqBody = [
                'json' => [
                    'name'           => $request->post('nama'),
                    'address'        => $request->post('address'),
                    'no'             => $request->post('no'),
                    'city'           => $request->post('city'),
                    'phone'          => $request->post('phone'),
                    'notes'          => $request->post('note'),
                    'director'       => $request->post('director'),
                    'director_phone' => $request->post('director_phone'),
                    'person_name'    => $request->post('person_name'),
                    'person_phone'   => $request->post('person_phone')
                ]
            ]; 
            $response = $client->request('POST', '', $reqBody); 
        } catch(RequestException $exception) {
        }
     
        $notification = array(
            'message' => 'Success receipt material',
            'alert-type' => 'success'
        );

        return redirect('master_suplier')->with($notification);
    }
    public function editSuplier($id) {
        try
        {
            $client = new Client(['base_uri' => $this->base_api_url . 'inv/base/MSupplier/' . $id]);  
            $response = $client->request('GET', ''); 
            $body = $response->getBody();
            $content = $body->getContents();
            $response_array = json_decode($content,TRUE);
            
            $m_suplier = $response_array['data'];
        } catch(RequestException $exception) {    
        }  

        $data = array(
            'm_suplier' => $m_suplier
        );
        
        return view('pages.inv.master_suplier.master_suplier_edit', $data); 
    }
    public function editSuplierPost(Request $request, $id) {
        $name           = $request->post('nama');
        $address        = $request->post('address');
        $no             = $request->post('no');
        $city           = $request->post('city');
        $phone          = $request->post('phone');
        $notes          = $request->post('note');
        $director       = $request->post('director');
        $director_phone = $request->post('director_phone');
        $person_name    = $request->post('person_name');
        $person_phone   = $request->post('person_phone');
     
        try
        {
            $client = new Client(['base_uri' => $this->base_api_url . 'inv/base/MSupplier/' . $id]);  
            $reqBody = [
                'json' => [
                    'name'           => $name,
                    'address'        => $address,
                    'no'             => $no,
                    'city'           => $city,
                    'phone'          => $phone,
                    'notes'          => $notes,
                    'director'       => $director,
                    'director_phone' => $director_phone,
                    'person_name'    => $person_name,
                    'person_phone'   => $person_phone
                ]
            ]; 
            $response = $client->request('PUT', '', $reqBody);
        } catch(RequestException $exception) {    
        }  
        
        $notification = array(
            'message' => 'Success edit suplier',
            'alert-type' => 'success'
        );

        return redirect('master_suplier')->with($notification);
    }
    public function deleteSuplier($id) {
        try
        {
            $client = new Client(['base_uri' => $this->base_api_url . 'inv/base/MSupplier/' . $id]);  
            $response = $client->request('DELETE', ''); 
        } catch(RequestException $exception) {    
        }

        $notification = array(
            'message' => 'Success delete suplier',
            'alert-type' => 'success'
        );

        return redirect('master_suplier')->with($notification);
    }
}