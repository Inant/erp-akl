<?php

namespace App\Http\Controllers\INV;

use Yajra\DataTables\Facades\DataTables;
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

    public function indexMasterMaterial()
    {
        return view('pages.inv.master_material.master_material_list');
    }

    public function createItem()
    {
        return view('pages.inv.master_material.master_material_create');
    }

    public function createItemPost(Request $request)
    {
        $no = $request->post('no');
        $name = $request->post('name');
        $lead_time = $request->post('lead_time');
        $m_unit_id = $request->post('m_unit_id');
        $category = $request->post('category');
        $m_unit_id2 = $request->post('m_unit_id2');
        $amount_child = $request->post('amount_child');
        if ($m_unit_id2 != null) {
            if ($amount_child == null || $amount_child == 0) {
                $amount_child = 1;
            }
        } else {
            $amount_child = 1;
        }
        $cek = DB::table('m_items')->where('no', $no)->count();

        if ($cek == 0) {
            try {
                $headers = [
                    'Authorization' => 'Bearer ' . auth()->user()['remember_token'],
                    'Accept'        => 'application/json',
                ];
                $client = new Client(['base_uri' => $this->base_api_url . 'inv/base/MItem']);
                $reqBody = [
                    'headers' => $headers,
                    'json' => [
                        'no' => $no,
                        'name' => $name,
                        'category' => $category,
                        'volume' => 0,
                        'late_time' => $lead_time,
                        'm_unit_id' => $m_unit_id,
                        'm_unit_child' => $m_unit_id2,
                        'amount_unit_child' => $amount_child,
                        'type' => 1,
                        'm_group_item_id'   => ($category == 'SPARE PART' ? $request->item_set : null),
                        'amount_in_set'     => ($category == 'SPARE PART' ? $request->amount_set : null)
                    ]
                ];
                $response = $client->request('POST', '', $reqBody);
                $body = $response->getBody();
                $content = $body->getContents();
                $response_array = json_decode($content, TRUE);

                $m_items = $response_array['data'];
            } catch (RequestException $exception) {
            }

            $notification = array(
                'message' => 'Success receipt material',
                'alert-type' => 'success'
            );

            return redirect('master_material')->with($notification);
        } else {
            $notification = array(
                'message' => 'Failed receipt material',
                'alert-type' => 'warning'
            );

            return redirect('master_material')->with($notification);
        }
    }

    public function editItem($id)
    {
        try {
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . 'inv/base/MItem/' . $id]);
            $response = $client->request('GET', '', ['headers' => $headers]); 
            $body = $response->getBody();
            $content = $body->getContents();
            $response_array = json_decode($content, TRUE);

            $m_items = $response_array['data'];
        } catch (RequestException $exception) {
        }

        $data = array(
            'm_items' => $m_items
        );

        return view('pages.inv.master_material.master_material_edit', $data);
    }

    public function editItemPost(Request $request, $id)
    {
        $no = $request->post('no');
        $name = $request->post('name');
        $lead_time = $request->post('lead_time');
        $m_unit_id = $request->post('m_unit_id');
        $category = $request->post('category');
        $m_unit_id2 = $request->post('m_unit_id2');
        $amount_child = $request->post('amount_child');
        if ($m_unit_id2 != null) {
            if ($amount_child == null || $amount_child == 0) {
                $amount_child = 1;
            }
        } else {
            $amount_child = 1;
        }
        try {
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . 'inv/base/MItem/' . $id]);
            $reqBody = [
                'headers' => $headers,
                'json' => [
                    'no' => $no,
                    'name' => $name,
                    'late_time' => $lead_time,
                    'm_unit_id' => $m_unit_id,
                    'category' => $category,
                    'm_unit_child' => $m_unit_id2,
                    'amount_unit_child' => $amount_child,
                    'm_group_item_id'   => ($category == 'SPARE PART' ? $request->item_set : null),
                    'amount_in_set'     => ($category == 'SPARE PART' ? $request->amount_set : null)
                ]
            ];
            $response = $client->request('PUT', '', $reqBody);
        } catch (RequestException $exception) {
        }

        $notification = array(
            'message' => 'Success edit material',
            'alert-type' => 'success'
        );

        return redirect('master_material')->with($notification);
    }

    public function deleteItem($id)
    {
        try {
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . 'inv/base/MItem/' . $id]);
            $response = $client->request('DELETE', '', ['headers' => $headers]);
        } catch (RequestException $exception) {
        }

        $notification = array(
            'message' => 'Success delete material',
            'alert-type' => 'success'
        );

        return redirect('master_material')->with($notification);
    }

    public function indexMasterSatuan()
    {

        return view('pages.inv.master_satuan.master_satuan_list');
    }

    public function createUnit()
    {
        return view('pages.inv.master_satuan.master_satuan_create');
    }

    public function createUnitPost(Request $request)
    {
        $code = $request->post('code');
        $name = $request->post('name');

        try {
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . 'inv/base/MUnit']);
            $reqBody = [
                'headers' => $headers,
                'json' => [
                    'code' => $code,
                    'name' => $name
                ]
            ];
            $response = $client->request('POST', '', $reqBody);
        } catch (RequestException $exception) {
        }

        $notification = array(
            'message' => 'Success create satuan',
            'alert-type' => 'success'
        );

        return redirect('master_satuan')->with($notification);
    }

    public function editUnit($id)
    {
        try {
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . 'inv/base/MUnit/' . $id]);
            $response = $client->request('GET', '', ['headers' => $headers]); 
            $body = $response->getBody();
            $content = $body->getContents();
            $response_array = json_decode($content, TRUE);

            $m_units = $response_array['data'];
        } catch (RequestException $exception) {
        }

        $data = array(
            'm_units' => $m_units
        );

        return view('pages.inv.master_satuan.master_satuan_edit', $data);
    }

    public function editUnitPost(Request $request, $id)
    {
        $code = $request->post('code');
        $name = $request->post('name');

        try {
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . 'inv/base/MUnit/' . $id]);
            $reqBody = [
                'headers' => $headers,
                'json' => [
                    'code' => $code,
                    'name' => $name
                ]
            ];
            $response = $client->request('PUT', '', $reqBody);
        } catch (RequestException $exception) {
        }

        $notification = array(
            'message' => 'Success edit satuan',
            'alert-type' => 'success'
        );

        return redirect('master_satuan')->with($notification);
    }

    public function deleteUnit($id)
    {
        try {
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . 'inv/base/MUnit/' . $id]);
            $response = $client->request('DELETE', '', ['headers' => $headers]);
        } catch (RequestException $exception) {
        }

        $notification = array(
            'message' => 'Success delete satuan',
            'alert-type' => 'success'
        );

        return redirect('master_satuan')->with($notification);
    }


    public function GetItemJson()
    {
        // $response = null;
        // try
        // {
        // $client = new Client(['base_uri' => $this->base_api_url . 'inv/base/MItem']);  
        //     $response = $client->request('GET', '', ['headers' => $headers]);  
        //     $body = $response->getBody();
        //     $content = $body->getContents();
        //     $response_array = json_decode($content,TRUE);

        //     $response = $content;       
        //     $data=DataTables::of($response_array['data'])
        //                             ->make(true);          
        // } catch(RequestException $exception) {

        // }    
        $query = DB::table('m_items')->whereNull('deleted_at')->get();
        foreach ($query as $key => $value) {
            $value->m_units = DB::table('m_units')->select('id', 'name')->where('id', $value->m_unit_id)->first();
            $value->m_unit_childs = DB::table('m_units')->select('id', 'name')->where('id', $value->m_unit_child)->first();
            $value->item_set = DB::table('m_items')->select('id', 'name')->where('id', $value->m_group_item_id)->first();
        }
        $data = DataTables::of($query)
            ->make(true);
        return $data;
    }

    public function GetUnitJson()
    {
        $response = null;
        try {
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . 'inv/base/MUnit']);
            $response = $client->request('GET', '', ['headers' => $headers]); 
            $body = $response->getBody();
            $content = $body->getContents();
            $response_array = json_decode($content, TRUE);

            $response = $content;
        } catch (RequestException $exception) {
        }

        return $response;
    }

    public function indexLagi()
    {
        echo 'test';
    }
    public function indexMasterKavling()
    {
        return view('pages.inv.master_kavling.master_kavling_proyek');
    }
    public function GetKavlingJson()
    {
        // $response = null;
        // try
        // {
        // $client = new Client(['base_uri' => $this->base_api_url . 'rab/kavling']);  
        //     $response = $client->request('GET', '', ['headers' => $headers]);  
        //     $body = $response->getBody();
        //     $content = $body->getContents();
        //     $response_array = json_decode($content,TRUE);

        //     $response = $content;         
        // } catch(RequestException $exception) {

        // }    

        // return $response;
        $response = null;
        try {
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . 'inv/base/Kavling']);
            $response = $client->request('GET', '', ['headers' => $headers]); 
            $body = $response->getBody();
            $content = $body->getContents();
            $response_array = json_decode($content, TRUE);

            $response = $content;
        } catch (RequestException $exception) {
        }
        foreach ($response_array['data'] as $key => $value) {
            $customer = DB::table('customers')->select('coorporate_name')->where('id', $value['customer_id'])->first();
            $response_array['data'][$key]['customer'] = $customer->coorporate_name;
        }
        return $response_array;
        // return $response['data'];
    }
    public function createKavling()
    {


        $site_location = null;
        $site_ = null;

        //set site location
        try {
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . 'rab/base/MCity']);
            $response = $client->request('GET', '', ['headers' => $headers]); 
            $body = $response->getBody();
            $content = $body->getContents();
            $response_array = json_decode($content, TRUE);

            $site_location = $response_array['data'];
        } catch (RequestException $exception) {
            $is_error = true;
            $error_message .= $exception->getMessage();
        }

        //set site location
        try {
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . 'inv/base/Site']);
            $response = $client->request('GET', '', ['headers' => $headers]); 
            $body = $response->getBody();
            $content = $body->getContents();
            $response_array = json_decode($content, true);

            $site = $response_array['data'];
        } catch (RequestException $exception) {
            $is_error = true;
            $error_message .= $exception->getMessage();
        }
        $data = array(
            'site' => $site,
            'kota' => $site_location
        );
        return view('pages.inv.master_kavling.master_kavling_create', $data);
    }
    public function createKavlingPost(Request $request)
    {
        $code = $request->post('code');
        $area = $request->post('area');
        $price = $request->post('price');
        $site = $request->post('site');
        $status = $request->post('status');

        try {
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . 'inv/base/Project']);
            $reqBody = [
                'headers' => $headers,
                'json' => [
                    'site_id'       => $site,
                    'name'          => $code,
                    'area'          => $area,
                    'base_price'    => $price,
                    'sale_status'   => $status
                ]
            ];
            $response = $client->request('POST', '', $reqBody);
        } catch (RequestException $exception) {
        }

        $notification = array(
            'message' => 'Success make kavling',
            'alert-type' => 'success'
        );

        return redirect('master_kavling')->with($notification);
    }
    public function editKavling($id)
    {
        $site = null;
        try {
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . 'rab/base/MCity']);
            $response = $client->request('GET', '', ['headers' => $headers]); 
            $body = $response->getBody();
            $content = $body->getContents();
            $response_array = json_decode($content, TRUE);

            $site_location = $response_array['data'];
        } catch (RequestException $exception) {
            $is_error = true;
            $error_message .= $exception->getMessage();
        }
        try {
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . 'inv/base/Site']);
            $response = $client->request('GET', '', ['headers' => $headers]); 
            $body = $response->getBody();
            $content = $body->getContents();
            $response_array = json_decode($content, true);

            $site = $response_array['data'];
        } catch (RequestException $exception) {
            $is_error = true;
            $error_message .= $exception->getMessage();
        }
        try {
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . 'inv/base/Project/' . $id]);
            $response = $client->request('GET', '', ['headers' => $headers]); 
            $body = $response->getBody();
            $content = $body->getContents();
            $response_array = json_decode($content, TRUE);

            $m_units = $response_array['data'];
        } catch (RequestException $exception) {
        }

        $data = array(
            'm_units' => $m_units,
            'site' => $site,
            'kota' => $site_location
        );

        return view('pages.inv.master_kavling.master_kavling_edit', $data);
    }

    public function editKavlingPost(Request $request, $id)
    {
        $code = $request->post('code');
        $area = $request->post('area');
        $price = $request->post('price');
        $site = $request->post('site');
        $status = $request->post('status');

        try {
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . 'inv/base/Project/' . $id]);
            $reqBody = [
                'headers' => $headers,
                'json' => [
                    'site_id'       => $site,
                    'name'          => $code,
                    'area'          => $area,
                    'base_price'    => $price,
                    'sale_status'   => $status
                ]
            ];
            $response = $client->request('PUT', '', $reqBody);
        } catch (RequestException $exception) {
        }

        $notification = array(
            'message' => 'Success edit kavling',
            'alert-type' => 'success'
        );

        return redirect('master_kavling')->with($notification);
    }
    public function deleteKavling($id)
    {
        try {
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . 'inv/base/Project/' . $id]);
            $response = $client->request('DELETE', '', ['headers' => $headers]);
        } catch (RequestException $exception) {
        }

        $notification = array(
            'message' => 'Success delete kavling',
            'alert-type' => 'success'
        );

        return redirect('master_kavling')->with($notification);
    }
    public function indexMasterSuplier()
    {
        return view('pages.inv.master_suplier.master_suplier_list');
    }
    public function GetSuplierJson()
    {
        $response = null;
        try {
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . 'inv/base/MSupplier']);
            $response = $client->request('GET', '', ['headers' => $headers]); 
            $body = $response->getBody();
            $content = $body->getContents();
            $response_array = json_decode($content, TRUE);

            $response = $content;
        } catch (RequestException $exception) {
        }

        return $response;
    }
    public function createSuplier()
    {
        return view('pages.inv.master_suplier.master_suplier_create');
    }
    public function createSuplierPost(Request $request)
    {
        try {
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . 'inv/base/MSupplier']);
            $reqBody = [
                'headers' => $headers,
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
                    'rekening_number'    => $request->post('rekening_number'),
                    'person_phone'   => $request->post('person_phone')
                ]
            ];
            $response = $client->request('POST', '', $reqBody);
        } catch (RequestException $exception) {
        }

        $notification = array(
            'message' => 'Success receipt material',
            'alert-type' => 'success'
        );

        return redirect('master_suplier')->with($notification);
    }
    public function editSuplier($id)
    {
        try {
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . 'inv/base/MSupplier/' . $id]);
            $response = $client->request('GET', '', ['headers' => $headers]); 
            $body = $response->getBody();
            $content = $body->getContents();
            $response_array = json_decode($content, TRUE);

            $m_suplier = $response_array['data'];
        } catch (RequestException $exception) {
        }

        $data = array(
            'm_suplier' => $m_suplier
        );

        return view('pages.inv.master_suplier.master_suplier_edit', $data);
    }
    public function editSuplierPost(Request $request, $id)
    {
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

        try {
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . 'inv/base/MSupplier/' . $id]);
            $reqBody = [
                'headers' => $headers,
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
                    'rekening_number'    => $request->post('rekening_number'),
                    'person_phone'   => $person_phone
                ]
            ];
            $response = $client->request('PUT', '', $reqBody);
        } catch (RequestException $exception) {
        }

        $notification = array(
            'message' => 'Success edit suplier',
            'alert-type' => 'success'
        );

        return redirect('master_suplier')->with($notification);
    }
    public function deleteSuplier($id)
    {
        try {
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . 'inv/base/MSupplier/' . $id]);
            $response = $client->request('DELETE', '', ['headers' => $headers]);
        } catch (RequestException $exception) {
        }

        $notification = array(
            'message' => 'Success delete suplier',
            'alert-type' => 'success'
        );

        return redirect('master_suplier')->with($notification);
    }

    public function indexProduct()
    {
        return view('pages.inv.master_product.master_product_list');
    }

    public function createProduct()
    {
        try {
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . '/crm/customerdatamain']);
            $response = $client->request('GET', '', ['headers' => $headers]); 
            $body = $response->getBody();
            $content = $body->getContents();
            $response_array = json_decode($content, TRUE);
            $customer = $response_array['data'];
        } catch (RequestException $exception) {
        }

        $product_equivalent = DB::table('m_products')
            ->whereNull('deleted_at')
            ->get();

        $data = array(
            'customer' => $customer,
            'product_equivalent' => $product_equivalent
        );
        return view('pages.inv.master_product.master_product_create', $data);
    }

    public function createProductPost(Request $request)
    {
        $customer_id = $request->post('customer_id');
        $prod_name = $request->post('name');
        $description = $request->post('description');
        $m_unit_id = $request->post('m_unit_id');
        $price = $request->post('price');
        $item = $request->post('item');
        $series = $request->post('series');
        $panjang = $request->post('panjang');
        $lebar = $request->post('lebar');
        $set = $request->post('set');
        $kavling_id = $request->post('kavling_id');
        $installation_fee = $request->post('installation_fee');
        $product_equivalent = $request->post('product_equivalent');
        $file = $request->file('image');
        $image = '';
        if ($file != null) {
            $tujuan_upload = 'upload/product';
            // upload file
            $name = time() . '.' . $file->getClientOriginalExtension();
            $file->move($tujuan_upload, $name);

            $image = $name;
        } else {
        }

        try {
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . 'inv/base/Product']);
            $reqBody = [
                'headers' => $headers,
                'json' => [
                    'customer_id' => $customer_id,
                    'name' => $prod_name,
                    'description' => $description,
                    'image' => $image,
                    'price' => $price,
                    'item' => $item,
                    'series' => $series,
                    'panjang' => $panjang,
                    'lebar' => $lebar,
                    'installation_fee' => $installation_fee,
                    'm_unit_id' => $m_unit_id,
                    'amount_set'    => $set,
                    'kavling_id'    => $kavling_id,
                    'is_active' => 1,
                    'm_product_id' => $product_equivalent
                ]
            ];
            $response = $client->request('POST', '', $reqBody);
        } catch (RequestException $exception) {
        }

        $notification = array(
            'message' => 'Success receipt product',
            'alert-type' => 'success'
        );

        return redirect('master_product')->with($notification);
    }

    public function editProduct($id)
    {
        try {
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . 'inv/base/Product/' . $id]);
            $response = $client->request('GET', '', ['headers' => $headers]); 
            $body = $response->getBody();
            $content = $body->getContents();
            $response_array = json_decode($content, TRUE);

            $product = $response_array['data'];
        } catch (RequestException $exception) {
        }

        $kavling = DB::table('kavlings')->where('customer_id', $product['customer_id'])->get();
        try {
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . '/crm/customerdatamain']);
            $response = $client->request('GET', '', ['headers' => $headers]); 
            $body = $response->getBody();
            $content = $body->getContents();
            $response_array = json_decode($content, TRUE);
            $customer = $response_array['data'];
        } catch (RequestException $exception) {
        }

        $data = array(
            'customer' => $customer,
            'product' => $product,
            'kavling'   => $kavling
        );

        return view('pages.inv.master_product.master_product_edit', $data);
    }

    public function editProductPost(Request $request, $id)
    {

        try {
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . 'inv/base/Product/' . $id]);
            $response = $client->request('GET', '', ['headers' => $headers]); 
            $body = $response->getBody();
            $content = $body->getContents();
            $response_array = json_decode($content, TRUE);

            $product = $response_array['data'];
        } catch (RequestException $exception) {
        }
        $image = $product['image'];
        $customer_id = $request->post('customer_id');
        $prod_name = $request->post('name');
        $description = $request->post('description');
        $m_unit_id = $request->post('m_unit_id');
        $price = $request->post('price');
        $item = $request->post('item');
        $series = $request->post('series');
        $panjang = $request->post('panjang');
        $lebar = $request->post('lebar');
        $set = $request->post('set');
        $kavling_id = $request->post('kavling_id');
        $installation_fee = $request->post('installation_fee');
        $file = $request->file('image');
        if ($file != null) {
            $tujuan_upload = 'upload/product';
            $name = time() . '.' . $file->getClientOriginalExtension();
            $file->move($tujuan_upload, $name);
            unlink($tujuan_upload . '/' . $product['image']);
            $image = $name;
        }

        try {
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . 'inv/base/Product/' . $id]);
            $reqBody = [
                'headers' => $headers,
                'json' => [
                    'customer_id' => $customer_id,
                    'name' => $prod_name,
                    'description' => $description,
                    'image' => $image,
                    'price' => $price,
                    'item' => $item,
                    'series' => $series,
                    'panjang' => $panjang,
                    'lebar' => $lebar,
                    'm_unit_id' => $m_unit_id,
                    'installation_fee' => $installation_fee,
                    'amount_set'    => $set,
                    'kavling_id'    => $kavling_id,
                    'is_active' => 1
                ]
            ];
            $response = $client->request('PUT', '', $reqBody);
        } catch (RequestException $exception) {
        }

        $notification = array(
            'message' => 'Success edit product',
            'alert-type' => 'success'
        );

        return redirect('master_product')->with($notification);
    }

    public function deleteProduct($id)
    {
        try {
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . 'inv/base/Product/' . $id]);
            $response = $client->request('DELETE', '', ['headers' => $headers]);
            // $detail=DB::table('products')->where('id', $id)->first();
            // $folder_upload = 'upload/product';
            // unlink($folder_upload.'/'.$detail->image);
        } catch (RequestException $exception) {
        }

        $notification = array(
            'message' => 'Success delete product',
            'alert-type' => 'success'
        );

        return redirect('master_product')->with($notification);
    }
    public function GetProductJson()
    {
        $response = null;
        try {
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . 'master/product']);
            $response = $client->request('GET', '', ['headers' => $headers]); 
            $body = $response->getBody();
            $content = $body->getContents();
            $response_array = json_decode($content, TRUE);
            foreach ($response_array['data'] as $key => $value) {
                $response_array['data'][$key]['kavling'] = DB::table('kavlings')->where('id', $value['kavling_id'])->first();
            }
            $response = $content;

            $data = DataTables::of($response_array['data'])
                ->addColumn('action', function ($row) {
                    return '<a href="' . url('/') . '/master_product/edit/' . $row['id'] . '" class="btn btn-success btn-sm"><i class="mdi mdi-pencil"></i></a>' . ' 
                                        ' . '<a href="' . url('/') . '/master_product/delete/' . $row['id'] . '" class="btn btn-danger btn-sm" onclick="javasciprt: return confirm(\'Are You Sure ?\')"><i class="mdi mdi-delete"></i></a>';
                })
                ->editColumn('image', function ($row) {
                    return '<img src="upload/product/' . $row['image'] . '" width="100px">';
                })
                ->rawColumns(['image', 'action'])
                ->make(true);
        } catch (RequestException $exception) {
        }

        return $data;
    }
    public function createProductPostJson(Request $request)
    {
        $customer_id = $request->post('customer_id1');
        $prod_name = $request->post('name');
        $description = $request->post('description');
        $m_unit_id = $request->post('m_unit_id');
        $price = $request->post('price');
        $item = $request->post('item');
        $series = $request->post('series');
        $panjang = $request->post('panjang');
        $lebar = $request->post('lebar');
        $set = $request->post('set');
        $kavling_id = $request->post('kavling_id');
        $installation_fee = $request->post('installation_fee');
        $product_equivalent = $request->post('product_equivalent');
        $file = $request->file('image');
        $image = '';
        if ($file != null) {
            $tujuan_upload = 'upload/product';
            // upload file
            $name = time() . '.' . $file->getClientOriginalExtension();
            $file->move($tujuan_upload, $name);

            $image = $name;
        } else {
        }

        try {
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . 'inv/base/Product']);
            $reqBody = [
                'headers' => $headers,
                'json' => [
                    'customer_id' => $customer_id,
                    'name' => $prod_name,
                    'description' => $description,
                    'image' => $image,
                    'item' => $item,
                    'series' => $series,
                    'panjang' => $panjang,
                    'lebar' => $lebar,
                    'price' => $price,
                    'm_unit_id' => $m_unit_id,
                    'kavling_id' => $kavling_id,
                    'installation_fee' => $installation_fee,
                    'amount_set'    => $set,
                    'is_active' => 1,
                    'm_product_id' => $product_equivalent
                ]
            ];
            $response = $client->request('POST', '', $reqBody);
        } catch (RequestException $exception) {
        }

        $notification = array(
            'message' => 'Success receipt product',
            'alert-type' => 'success'
        );

        return $notification;
    }

    public function indexProductEquivalent()
    {
        return view('pages.inv.master_product_equivalent.master_product_equivalent_list');
    }

    public function GetProductEquivalentJson()
    {
        $response = null;
        try {
            $data = DB::table('m_products')->get();
            $data = json_decode(json_encode($data), true);
            $data = DataTables::of($data)
                ->addColumn('action', function ($row) {
                    return '<div class="text-center"><a href="' . url('/') . '/master_product_equivalent/edit/' . $row['id'] . '" class="btn btn-success btn-sm"><i class="mdi mdi-pencil"></i></a></div>';
                })
                ->make(true);
        } catch (RequestException $exception) {
        }

        return $data;
    }

    public function createProductEquivalent()
    {
        return view('pages.inv.master_product_equivalent.master_product_equivalent_create');
    }

    public function createProductEquivalentPost(Request $request)
    {
        $code = $request->post('code');
        $name = $request->post('name');

        // array
        $m_item_id = $request->post('m_item_id');
        $dimensi = $request->post('dimensi');
        $operator = $request->post('operator');
        $equivalent = $request->post('equivalent');
        $qty_item = $request->post('qty_item');

        $m_product_id = DB::table('m_products')->insertGetId(
            [
                'code' => $code, 'name' => $name
            ]
        );

        for ($i = 0; $i < count($m_item_id); $i++) {
            # code...
            DB::table('m_product_ds')->insert(
                [
                    'm_product_id' => $m_product_id,
                    'm_item_id' => $m_item_id[$i],
                    'formula' => $dimensi[$i] . ';' . $operator[$i] . ';' . $equivalent[$i],
                    'qty_item' => $qty_item[$i]
                ]
            );
        }


        $notification = array(
            'message' => 'Success add product equivalent',
            'alert-type' => 'success'
        );

        return redirect('master_product_equivalent')->with($notification);
    }

    public function editProductEquivalent($id)
    {

        $m_products = DB::table('m_products')
            ->where('id', $id)
            ->first();

        $m_product_ds = DB::table('m_product_ds')
            ->where('m_product_id', $id)
            ->get();

        $m_items = DB::table('m_items')
            ->get();

        $data = array(
            'id' => $id,
            'm_products' => $m_products,
            'm_product_ds' => $m_product_ds,
            'm_items' => $m_items
        );


        return view('pages.inv.master_product_equivalent.master_product_equivalent_edit', $data);
    }

    public function editProductEquivalentPost(Request $request, $id)
    {
        $code = $request->post('code');
        $name = $request->post('name');

        // array
        $m_item_id = $request->post('m_item_id');
        $dimensi = $request->post('dimensi');
        $operator = $request->post('operator');
        $equivalent = $request->post('equivalent');
        $qty_item = $request->post('qty_item');

        $m_product_id = DB::table('m_products')
            ->where('id', $id)
            ->update(
                [
                    'code' => $code, 'name' => $name
                ]
            );

        DB::table('m_product_ds')
            ->where('m_product_id', $id)
            ->delete();

        for ($i = 0; $i < count($m_item_id); $i++) {
            # code...

            DB::table('m_product_ds')->insert(
                [
                    'm_product_id' => $id,
                    'm_item_id' => $m_item_id[$i],
                    'formula' => $dimensi[$i] . ';' . $operator[$i] . ';' . $equivalent[$i],
                    'qty_item' => $qty_item[$i]
                ]
            );
        }


        $notification = array(
            'message' => 'Success update product equivalent',
            'alert-type' => 'success'
        );

        return redirect('master_product_equivalent')->with($notification);
    }


    public function indexWarehouse()
    {
        return view('pages.inv.master_warehouse.master_warehouse_list');
    }
    public function createWarehouse()
    {
        try {
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . 'inv/base/Site']);
            $response = $client->request('GET', '', ['headers' => $headers]); 
            $body = $response->getBody();
            $content = $body->getContents();
            $response_array = json_decode($content, TRUE);
        } catch (RequestException $exception) {
        }
        $data = array(
            'site' => $response_array['data']
        );
        return view('pages.inv.master_warehouse.master_warehouse_create', $data);
    }
    public function createWarehousePost(Request $request)
    {
        $site_id = $request->post('site_id');
        $name = $request->post('name');
        $kode = $request->post('kode');

        try {
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . 'inv/base/MWarehouse']);
            $reqBody = [
                'headers' => $headers,
                'json' => [
                    'site_id' => $site_id,
                    'name' => $name,
                    'code' => $kode,
                ]
            ];
            $response = $client->request('POST', '', $reqBody);
        } catch (RequestException $exception) {
        }

        $notification = array(
            'message' => 'Success receipt Warehouse',
            'alert-type' => 'success'
        );

        return redirect('master_warehouse')->with($notification);
    }
    public function GetWarehouseJson()
    {
        $response = null;
        try {
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . 'inv/base/MWarehouse']);
            $response = $client->request('GET', '', ['headers' => $headers]); 
            $body = $response->getBody();
            $content = $body->getContents();
            $response_array = json_decode($content, TRUE);

            $response = $content;
            foreach ($response_array['data'] as $key => $value) {
                $site = DB::table('sites')->where('id', $value['site_id'])->first();
                $response_array['data'][$key]['site_name'] = $site != null ? $site->name : '';
            }

            $data = DataTables::of($response_array['data'])
                ->addColumn('action', function ($row) {
                    return '<a href="' . url('/') . '/master_warehouse/edit/' . $row['id'] . '" class="btn btn-success btn-sm"><i class="mdi mdi-pencil"></i></a>' . ' 
                                        ' . '<a href="' . url('/') . '/master_warehouse/delete/' . $row['id'] . '" class="btn btn-danger btn-sm" onclick="javasciprt: return confirm(\'Are You Sure ?\')"><i class="mdi mdi-delete"></i></a>';
                })
                ->rawColumns(['action'])
                ->make(true);
        } catch (RequestException $exception) {
        }

        return $data;
    }
    public function editWarehouse($id)
    {
        try {
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . 'inv/base/Site']);
            $response = $client->request('GET', '', ['headers' => $headers]); 
            $body = $response->getBody();
            $content = $body->getContents();
            $response_array = json_decode($content, TRUE);
            $site = $response_array['data'];
        } catch (RequestException $exception) {
        }
        try {
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . 'inv/base/MWarehouse/' . $id]);
            $response = $client->request('GET', '', ['headers' => $headers]); 
            $body = $response->getBody();
            $content = $body->getContents();
            $response_array = json_decode($content, TRUE);
            $warehouse = $response_array['data'];
        } catch (RequestException $exception) {
        }
        $data = array(
            'site' => $site,
            'warehouse' => $warehouse
        );

        return view('pages.inv.master_warehouse.master_warehouse_edit', $data);
    }
    public function editWarehousePost(Request $request, $id)
    {

        $name = $request->post('name');
        $kode = $request->post('kode');

        try {
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . 'inv/base/MWarehouse/' . $id]);
            $reqBody = [
                'headers' => $headers,
                'json' => [
                    'name' => $name,
                    'code' => $kode,
                ]
            ];
            $response = $client->request('PUT', '', $reqBody);
        } catch (RequestException $exception) {
        }

        $notification = array(
            'message' => 'Success edit Warehouse',
            'alert-type' => 'success'
        );

        return redirect('master_warehouse')->with($notification);
    }
    public function deleteWarehouse($id)
    {
        try {
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . 'inv/base/MWarehouse/' . $id]);
            $response = $client->request('DELETE', '', ['headers' => $headers]);
        } catch (RequestException $exception) {
        }

        $notification = array(
            'message' => 'Success delete Warehouse',
            'alert-type' => 'success'
        );

        return redirect('master_warehouse')->with($notification);
    }
    public function getWarehouseBySite($id)
    {
        $warehouse = DB::table('m_warehouses')->where('site_id', $id)->get();
        $data = array(
            'data'  => $warehouse
        );
        return $data;
    }
    public function createKavlingPostJson(Request $request)
    {
        $customer_id = $request->post('customer_id1');
        $prod_name = $request->post('name');
        $total = $request->post('total');

        try {
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . 'inv/base/Kavling']);
            $reqBody = [
                'headers' => $headers,
                'json' => [
                    'customer_id' => $customer_id,
                    'name' => $prod_name,
                    'amount' => $total
                ]
            ];
            $response = $client->request('POST', '', $reqBody);
        } catch (RequestException $exception) {
        }

        $notification = array(
            'message' => 'Success create kavling',
            'alert-type' => 'success'
        );

        return $notification;
    }
    public function updateKavlingPostJson(Request $request)
    {
        $total = $request->post('total_kavling');
        $id = $request->post('kavling_id');
        try {
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . 'inv/base/Kavling/' . $id]);
            $reqBody = [
                'headers' => $headers,
                'json' => [
                    'amount' => $total
                ]
            ];
            $response = $client->request('PUT', '', $reqBody);
        } catch (RequestException $exception) {
        }

        $notification = array(
            'message' => 'Success update kavling',
            'alert-type' => 'success'
        );

        return $notification;
    }
    public function getKavlingByCust($id)
    {
        $kavling = DB::table('kavlings')->where('customer_id', $id)->get();
        $data = array(
            'data'  => $kavling
        );
        return $data;
    }
    public function getKavlingById($id)
    {
        $kavling = DB::table('kavlings')->where('id', $id)->first();
        $data = array(
            'data'  => $kavling
        );
        return $data;
    }
    public function getSparePart()
    {
        $query = DB::table('m_items')->where('category', 'SPARE PART')->whereNull('deleted_at')->get();
        $data = array(
            'data'      => $query
        );
        return $data;
    }
    public function createProjectCustPostJson(Request $request)
    {
        // $customer_id = $request->post('customer_id');
        $name = $request->post('name_project');
        try {
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . 'inv/base/CustomerProject']);
            $reqBody = [
                'headers' => $headers,
                'json' => [
                    // 'customer_id' => $customer_id,
                    'site_id'   => $this->site_id,
                    'name' => $name,
                ]
            ];
            $response = $client->request('POST', '', $reqBody);
        } catch (RequestException $exception) {
        }

        $notification = array(
            'message' => 'Success create project',
            'alert-type' => 'success'
        );

        return $notification;
    }
    public function getCustProject()
    {
        $project = DB::table('customer_projects')->where('site_id', $this->site_id)->get();
        $data = array(
            'data'  => $project
        );
        return $data;
    }

    public function GetMasterEquivalentJson()
    {
        $query = DB::table('m_equivalents')->get();
        $data = array(
            'data'      => $query
        );
        return $data;
    }
    public function indexWorksub()
    {
        return view('pages.inv.master_worksub.master_worksub_list');
    }
    public function createWorksub()
    {
        try {
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . 'inv/base/Site']);
            $response = $client->request('GET', '', ['headers' => $headers]); 
            $body = $response->getBody();
            $content = $body->getContents();
            $response_array = json_decode($content, TRUE);
        } catch (RequestException $exception) {
        }
        $data = array(
            'site' => $response_array['data']
        );
        return view('pages.inv.master_worksub.master_worksub_create', $data);
    }
    public function createWorksubPost(Request $request)
    {
        $name = $request->post('name');
        $amount = $request->post('amount');
        $m_unit_id = $request->post('m_unit_id');

        try {
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . 'inv/base/Worksub']);
            $reqBody = [
                'headers' => $headers,
                'json' => [
                    'name' => $name,
                    'price' => $this->currency($amount),
                    'm_unit_id' => $m_unit_id,
                ]
            ];
            $response = $client->request('POST', '', $reqBody);
        } catch (RequestException $exception) {
        }

        $notification = array(
            'message' => 'Success create Work',
            'alert-type' => 'success'
        );

        return redirect('master_worksub')->with($notification);
    }
    public function GetWorksubJson()
    {
        $response = null;
        try {
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . 'inv/base/Worksub']);
            $response = $client->request('GET', '', ['headers' => $headers]); 
            $body = $response->getBody();
            $content = $body->getContents();
            $response_array = json_decode($content, TRUE);

            $response = $content;
            foreach ($response_array['data'] as $key => $value) {
                $m_unit = DB::table('m_units')->where('id', $value['m_unit_id'])->first();
                $response_array['data'][$key]['m_units'] = $m_unit;
            }

            $data = DataTables::of($response_array['data'])
                ->addColumn('action', function ($row) {
                    return '<a href="' . url('/') . '/master_worksub/edit/' . $row['id'] . '" class="btn btn-success btn-sm"><i class="mdi mdi-pencil"></i></a>' . ' 
                                        ' . '<a href="' . url('/') . '/master_worksub/delete/' . $row['id'] . '" class="btn btn-danger btn-sm" onclick="javasciprt: return confirm(\'Are You Sure ?\')"><i class="mdi mdi-delete"></i></a>';
                })
                ->rawColumns(['action'])
                ->make(true);
        } catch (RequestException $exception) {
        }

        return $data;
    }
    private function currency($val)
    {
        $data = explode('.', $val);
        $new = implode('', $data);
        return $new;
    }
    public function editWorksub($id)
    {
        try {
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . 'inv/base/Worksub/' . $id]);
            $response = $client->request('GET', '', ['headers' => $headers]); 
            $body = $response->getBody();
            $content = $body->getContents();
            $response_array = json_decode($content, TRUE);
            $worksub = $response_array['data'];
        } catch (RequestException $exception) {
        }
        $data = array(
            'worksub' => $worksub
        );

        return view('pages.inv.master_worksub.master_worksub_edit', $data);
    }
    public function editWorksubPost(Request $request, $id)
    {
        // dd($request->all());
        $name = $request->post('name');
        $amount = $request->post('amount');
        $m_unit_id = $request->post('m_unit_id');

        try {
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . 'inv/base/Worksub/' . $id]);
            $reqBody = [
                'headers' => $headers,
                'json' => [
                    'name' => $name,
                    'price' => $this->currency($amount),
                    'm_unit_id' => $m_unit_id,
                ]
            ];
            $response = $client->request('PUT', '', $reqBody);
        } catch (RequestException $exception) {
        }

        $notification = array(
            'message' => 'Success edit worksub',
            'alert-type' => 'success'
        );

        return redirect('master_worksub')->with($notification);
    }
    public function deleteWorksub($id)
    {
        try {
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . 'inv/base/Worksub/' . $id]);
            $response = $client->request('DELETE', '', ['headers' => $headers]);
        } catch (RequestException $exception) {
        }

        $notification = array(
            'message' => 'Success delete worksub',
            'alert-type' => 'success'
        );

        return redirect('master_worksub')->with($notification);
    }
}
