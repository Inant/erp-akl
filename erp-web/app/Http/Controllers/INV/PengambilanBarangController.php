<?php

namespace App\Http\Controllers\INV;

use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use App\Http\Controllers\Controller;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use App\Http\Controllers\RAB\RabController;
use Carbon\Carbon;
use DB;
use Exception;
use Illuminate\Support\Facades\DB as FacadesDB;

class PengambilanBarangController extends Controller
{
    private $base_api_url;
    private $site_id = null;
    private $username = null;
    private $user_name = null;
    public function __construct()
    {
        //Authenticate page menu
        $this->middleware(function ($request, $next) {
            Controller::isLogin(auth()->user()['role_id']); 
            $this->site_id = auth()->user()['site_id'];
            $this->username = auth()->user()['email'];
            $this->user_name = auth()->user()['name'];
            $this->user_id = auth()->user()['id'];
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
        
        return view('pages.inv.pengambilan_barang.pengambilan_barang_list', $data);
    }

    public function indexAuthPengambilanBarang() {
        return view('pages.inv.pengambilan_barang.auth_pengambilan_barang_list');
    }

    public function indexPengeluaran()
    {
        $is_error = false;
        $error_message = '';
        $data = array(
            'error' => array(
                'is_error' => $is_error,
                'error_message' => $error_message
            )
        );
        
        return view('pages.inv.pengambilan_barang.pengeluaran_barang_list', $data);
    }

    public function pengeluaranForm($id)
    { 
        $is_error = false;
        $error_message = '';
        try
        {
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            if ($this->site_id == null) {
                $client1 = new Client(['base_uri' => $this->base_api_url . 'order/option']); 
            }else{
                $client1 = new Client(['base_uri' => $this->base_api_url . 'order/option?site_id='.$this->site_id]); 
            } 
            $response1 = $client1->request('GET', '', ['headers' => $headers]); 
            $body1 = $response1->getBody();
            $content1 = $body1->getContents();
            $response_array1 = json_decode($content1,TRUE);

            $response1 = $content1;  
            $order_list = $response_array1['data'];

            $listMaterial = $this->getListPengambilanBarangDetail($id);
        } catch(RequestException $exception) {
            
        }

        $data = array(
            'error' => array(
                'is_error' => $is_error,
                'error_message' => $error_message
            ),
            'order_list' => $order_list,
            'site_id' => $this->site_id,
            'id'  => $id,
            'listMaterial' => $listMaterial
        );
        
        return view('pages.inv.pengambilan_barang.pengeluaran_barang_form', $data);
    }

    public function request()
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
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . 'rab/base/MCity']);  
            $response = $client->request('GET', '', ['headers' => $headers]); 
            $body = $response->getBody();
            $content =$body->getContents();
            $response_array = json_decode($content,TRUE);

            $site_location = $response_array['data'];
        } catch(RequestException $exception) {
            $is_error = true;
            $error_message .= $exception->getMessage();
        }
        // $response = null;
        
        try
        {
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            if ($this->site_id == null) {
                $client1 = new Client(['base_uri' => $this->base_api_url . 'order/option']); 
            }else{
                $client1 = new Client(['base_uri' => $this->base_api_url . 'order/option?site_id='.$this->site_id]); 
            } 
            $response1 = $client1->request('GET', '', ['headers' => $headers]); 
            $body1 = $response1->getBody();
            $content1 = $body1->getContents();
            $response_array1 = json_decode($content1,TRUE);

            $response1 = $content1;  
            $order_list = $response_array1['data'];
        } catch(RequestException $exception) {
            
        }
        $warehouse=DB::table('m_warehouses')->where('site_id', $this->site_id)->get();
        $data = array(
            'error' => array(
                'is_error' => $is_error,
                'error_message' => $error_message
            ),
            'site_locations' => $site_location, 
            'sites' => $all_sites,
            'projects' => $all_projects,
            'order_list' => $order_list,
            'warehouse' => $warehouse,
            'site_id' => $this->site_id
        );
        // return view('pages.rab.rab.rab_form_add', $data);
        
        return view('pages.inv.pengambilan_barang.pengambilan_barang_request', $data);
    }

    public function requestPost(Request $request)
    {
        $rab_id = $request->post('rab_no');
        $request_id = $request->post('request_id');
        // $pw_id = $request->post('project_work_id');
        $prod_sub_id = $request->post('prod_sub_id');
        $m_item_id = $request->post('m_item_id');
        $qty_rab = $request->post('qty_rab');
        $qty_sisa_rab = $request->post('qty_sisa_rab');
        $qty = $request->post('qty');
        $detail_note = $request->post('detail_note');
        $m_unit_id = $request->post('m_unit_id');
        $m_warehouse_id = $request->post('m_warehouse_id');
        $alasan = $request->post('alasan'); // untuk permintaan khusus
        
        //jika product label tidak ada yang dipilih
        if ($prod_sub_id == null) {
            return redirect(request()->headers->get('referer'));
        }
        
        $permintaan_normal = array();
        $permintaan_khusus = array();
        for($i = 0; $i < count($m_item_id); $i++) {
            if ($qty[$i] != 0) {
                if($qty_sisa_rab[$i] >= $qty[$i]) { // request normal
                    array_push($permintaan_normal, 
                        array(
                            'm_item_id' => $m_item_id[$i],
                            'qty_rab' => $qty_rab[$i],
                            'qty_sisa_rab' => $qty_sisa_rab[$i],
                            'qty' => $qty[$i],
                            'detail_note'  => $detail_note[$i],
                            'm_unit_id' => $m_unit_id[$i]
                        ));
                } else { // request khusus
                    if($qty_sisa_rab[$i] == 0) {
                        array_push($permintaan_khusus, 
                            array(
                                'm_item_id' => $m_item_id[$i],
                                'qty_rab' => $qty_rab[$i],
                                'qty_sisa_rab' => $qty_sisa_rab[$i],
                                'qty' => $qty[$i],
                                'detail_note'  => $detail_note[$i],
                                'm_unit_id' => $m_unit_id[$i]
                            ));
                    } else {
                        array_push($permintaan_normal, 
                        array(
                            'm_item_id' => $m_item_id[$i],
                            'qty_rab' => $qty_rab[$i],
                            'qty_sisa_rab' => $qty_sisa_rab[$i],
                            'qty' => $qty_sisa_rab[$i],
                            'detail_note'  => $detail_note[$i],
                            'm_unit_id' => $m_unit_id[$i]
                        ));
    
                        array_push($permintaan_khusus, 
                            array(
                                'm_item_id' => $m_item_id[$i],
                                'qty_rab' => $qty_rab[$i],
                                'qty_sisa_rab' => $qty_sisa_rab[$i],
                                'qty' => $qty[$i] - $qty_sisa_rab[$i],
                                'detail_note'  => $detail_note[$i],
                                'm_unit_id' => $m_unit_id[$i]
                            ));
                    }
                }
            }
            
        }

        $period_year = Carbon::now()->year;
        $period_month = Carbon::now()->month;
        $rabcon = new RabController();
        $memo_no = $rabcon->generateTransactionNo('MREQ', $period_year, $period_month, $this->site_id );
        $inv_req=null;
        if(is_countable($permintaan_normal) && count($permintaan_normal) > 0) {
            // //insert ke request
            try
            {
                $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . 'inv/base/InvRequest']);
                $reqBody = [
                'headers' => $headers,
                'json' => [
                        'req_type' => 'REQ_ITEM',
                        'rab_id' => $rab_id,
                        // 'project_work_id' => $pw_id,
                        'project_req_development_id' => $request_id,
                        'no' => $memo_no, 
                        'site_id' => $this->site_id
                    ]
                ];
                $response = $client->request('POST', '', $reqBody); 
                $body = $response->getBody();
                $content = $body->getContents();
                $response_array = json_decode($content,TRUE);
                $inv_req = $response_array['data'];
            } catch(RequestException $exception) {
            }

            for($i = 0; $i < count($permintaan_normal); $i++) {
                //insert detail
                try
                {
                    $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . '/inv/base/InvRequestD']);
                    $reqBody = [
                    'headers' => $headers,
                'json' => [
                            'inv_request_id' => $inv_req['id'],
                            'm_item_id' => $permintaan_normal[$i]['m_item_id'],
                            'amount' => $permintaan_normal[$i]['qty'],
                            'detail_notes' => $permintaan_normal[$i]['detail_note'],
                            'm_unit_id' => $permintaan_normal[$i]['m_unit_id'],
                            'm_warehouse_id' => $m_warehouse_id
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

        // permintaan khusus
        $inv_spc_req=null;
        if(is_countable($permintaan_khusus) && count($permintaan_khusus) > 0) {
            //insert ke special
            try
            {
                $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . '/inv/base/InvRequest']);
                $reqBody = [
                'headers' => $headers,
                'json' => [
                        'req_type' => 'SPECIAL',
                        'rab_id' => $rab_id,
                        // 'project_work_id' => $pw_id,
                        'project_req_development_id' => $request_id,
                        'no' => $memo_no,
                        'site_id' => $this->site_id
                    ]
                ]; 
                $response = $client->request('POST', '', $reqBody); 
                $body = $response->getBody();
                $content = $body->getContents();
                $response_array = json_decode($content,TRUE);
                $inv_spc_req = $response_array['data'];
            } catch(RequestException $exception) {
            }

            for($i = 0; $i < count($permintaan_khusus); $i++) {
                //insert detail
                try
                {
                    $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . '/inv/base/InvRequestD']);
                    $reqBody = [
                    'headers' => $headers,
                'json' => [
                            'inv_request_id' => $inv_spc_req['id'],
                            'm_item_id' => $permintaan_khusus[$i]['m_item_id'],
                            'amount' => $permintaan_khusus[$i]['qty'],
                            'm_unit_id' => $permintaan_khusus[$i]['m_unit_id'],
                            'detail_notes' => $permintaan_khusus[$i]['detail_note'],
                            'notes' => $alasan[$i],
                            'm_warehouse_id' => $m_warehouse_id
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

        for($i = 0; $i < count($prod_sub_id); $i++) {
            //insert detail
            $product_subs=DB::table('product_subs')->where('id', $prod_sub_id[$i])->first();
            $no=explode('-', $product_subs->no);
            $kode=$m_warehouse_id == 2 ? array('SBY') : array('MJK');
            array_splice($no, 1, 0, $kode);
            $no=join('-',$no);
            DB::table('product_subs')->where('id', $prod_sub_id[$i])->update(array('no' => $no));
            try
            {
                $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . '/inv/base/InvRequestProdD']);
                $reqBody = [
                'headers' => $headers,
                'json' => [
                        'inv_request_id' => $inv_req != null ? $inv_req['id'] : $inv_spc_req['id'],
                        'product_sub_id' => $prod_sub_id[$i],
                        // 'project_work_id' => $pw_id,
                        'label'         => $no,
                        'project_req_development_id' => $request_id,
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
            'message' => 'Success Create New Request',
            'alert-type' => 'success'
        );

        return redirect('material_request')->with($notification);
    }

    public function indexAuthPengambilanBarangPost(Request $request) {
        $inv_request_id = $request->post('inv_request_id');
        $inv_request_d_id = $request->post('inv_request_d_id');
        $m_item_id = $request->post('m_item_id');
        $qty = $request->post('qty');
        $m_unit_id = $request->post('m_unit_id');

        //insert ke special
        try
        {
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . '/inv/base/InvRequest/' . $inv_request_id ]);
            $reqBody = [
            'headers' => $headers,
                'json' => [
                    'user_auth' => $this->username
                ]
            ]; 
            $response = $client->request('PUT', '', $reqBody);
        } catch(RequestException $exception) {
        }

        for($i = 0; $i < count($inv_request_d_id); $i++) {
            //insert inv_trx_d
            try
            {
                $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . '/inv/base/InvRequestD/' . $inv_request_d_id[$i]]);
                $reqBody = [
                    'headers' => $headers,
                'json' => [
                        'amount_auth' => $qty[$i]
                        ]
                    ]; 
                    $response = $client->request('PUT', '', $reqBody); 
            } catch(RequestException $exception) {
            }
        }

        $notification = array(
            'message' => 'Success Auth Request',
            'alert-type' => 'success'
        );

        return redirect('auth_pengambilan_barang')->with($notification);
    }
    private function getItemStok($id){
        $response = null;
        try
        {
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . '/inv/stok/' . $this->site_id.'?m_item_id='.$id]);
            $response = $client->request('GET', '', ['headers' => $headers]);
            $body = $response->getBody();
            $content = $body->getContents();
            $response_array = json_decode($content,TRUE);

            $response = $content;
        } catch(RequestException $exception) {

        }
        return $response_array['data'];
    }
    // public function indexPengeluaranPost(Request $request) {
        
    //     $inv_request_id = $request->post('inv_request_id');
    //     $inv_request_d_id = $request->post('inv_request_d_id');
    //     $m_item_id = $request->post('m_item_id');
    //     $qty = $request->post('qty');
    //     $m_unit_id = $request->post('m_unit_id');
    //     $mandor = $request->post('mandor');
    //     $stok = $request->post('stok');
        
    //     $data_stok=array();
    //     $j=0;
    //     $isSubmit = true;
    //     // for ($i=0; $i < count($m_item_id); $i++) { 
    //     //     $stokItem=$this->getItemStok($m_item_id[$i]);
    //     //     $stokInput=$qty[$i];
    //     //     foreach ($stokItem as $key => $value) {
    //     //         if ($stokInput != 0 && $value['stok'] != 0) {
    //     //             if ($stokInput < $value['stok']) {
    //     //                 $data_stok[$j]['m_item_id']=$m_item_id[$i];
    //     //                 $data_stok[$j]['m_unit_id']=$value['m_units']['id'];
    //     //                 $data_stok[$j]['purchase_d_id']=$value['purchase_d_id'];
    //     //                 $data_stok[$j]['price']=$value['last_price'];
    //     //                 $data_stok[$j]['qty']=$stokInput;
    //     //                 $stokInput=0;
    //     //             }else{
    //     //                 $data_stok[$j]['m_item_id']=$m_item_id[$i];
    //     //                 $data_stok[$j]['m_unit_id']=$value['m_units']['id'];
    //     //                 $data_stok[$j]['purchase_d_id']=$value['purchase_d_id'];
    //     //                 $data_stok[$j]['price']=$value['last_price'];
    //     //                 $stokInput-=$value['stok'];
    //     //                 $data_stok[$j]['qty']=$stokInput;
    //     //             }
    //     //             $j++;
    //     //         }
    //     //     }
    //     //     if ($stokInput > 0) {
    //     //         $isSubmit = false;
    //     //     }
    //     // }
        
        
    //     for($i = 0; $i < count($inv_request_d_id); $i++) {
    //         if ($stok[$i] < $qty[$i]) {
    //             $isSubmit = false;
    //             break;
    //         }
    //     }

    //     if ($isSubmit) {
    //         // insert ke special
    //         try
    //         {
    //             $client = new Client(['base_uri' => $this->base_api_url . '/inv/base/InvRequest/' . $inv_request_id ]);
    //             $reqBody = [
    //                 'json' => [
    //                     'contractor' => $mandor
    //                 ]
    //             ]; 
    //             $response = $client->request('PUT', '', $reqBody);
    //         } catch(RequestException $exception) {
    //         }

    //         //insert inv_trx
    //         $period_year = Carbon::now()->year;
    //         $period_month = Carbon::now()->month;
    //         $rabcon = new RabController();
    //         $inv_no = $rabcon->generateTransactionNo('INV_OUT', $period_year, $period_month, $this->site_id );
    //         try
    //         {
    //             $client = new Client(['base_uri' => $this->base_api_url . '/inv/base/InvTrx']);
    //             $reqBody = [
    //                 'json' => [
    //                     'm_warehouse_id' => 1,
    //                     'purchase_id' => null,
    //                     'trx_type' => 'REQ_ITEM',
    //                     'inv_request_id' => $inv_request_id,
    //                     'no' => $inv_no,
    //                     'inv_trx_date' => Carbon::now()->toDateString(),
    //                     'site_id' => $this->site_id,
    //                     'is_entry' => false
    //                     ]
    //                 ]; 
    //                 $response = $client->request('POST', '', $reqBody); 
    //                 $body = $response->getBody();
    //                 $content = $body->getContents();
    //                 $response_array = json_decode($content,TRUE);
    //                 $inv_trx = $response_array['data'];
    //         } catch(RequestException $exception) {
    //         }

    //         for($i = 0; $i < count($data_stok); $i++) {
    //             // set nilai material untuk pengeluaran
    //             try
    //             {
    //                 $client = new Client(['base_uri' => $this->base_api_url . '/inv/value_out']);
    //                 $reqBody = [
    //                     'json' => [
    //                         'm_item_id' => $data_stok[$i]['m_item_id'],
    //                         'qty' => $data_stok[$i]['qty']
    //                         ]
    //                     ]; 
    //                     $response = $client->request('POST', '', $reqBody); 
    //                     $body = $response->getBody();
    //                     $content = $body->getContents();
    //                     $response_array = json_decode($content,TRUE);
    //                     $value = $response_array['data']['value'];
    //             } catch(RequestException $exception) {
    //             }

    //             //insert inv_trx_d
    //             try
    //             {
    //                 $client = new Client(['base_uri' => $this->base_api_url . '/inv/base/InvTrxD']);
    //                 $reqBody = [
    //                     'json' => [
    //                         'inv_trx_id' => $inv_trx['id'],
    //                         'm_item_id' => $data_stok[$i]['m_item_id'],
    //                         'amount' => $data_stok[$i]['qty'],
    //                         'm_unit_id' => $data_stok[$i]['m_unit_id'],
    //                         'purchase_d_id' => $data_stok[$i]['purchase_d_id'],
    //                         // 'value' => $value
    //                         'value' => $data_stok[$i]['qty']*$data_stok[$i]['price'],
    //                         ]
    //                     ]; 
    //                     $response = $client->request('POST', '', $reqBody); 
    //             } catch(RequestException $exception) {
    //             }   
    //         }

    //         $notification = array(
    //             'message' => 'Success Auth Request',
    //             'alert-type' => 'success'
    //         );
    //     } else {
    //         $notification = array(
    //             'message' => 'Error, Stock cannot smaller than request',
    //             'alert-type' => 'error'
    //         );
    //     }

    //     return redirect('pengeluaran_barang')->with($notification);
    // }

    public function indexPengeluaranPost(Request $request) {
        $inv_request_id = $request->post('inv_request_id');
        $inv_request_d_id = $request->post('inv_request_d_id');
        $m_item_id = $request->post('m_item_id');
        $qty = $request->post('qty');
        $m_unit_id = $request->post('m_unit_id');
        $type_stok = $request->post('type_stok');
        $m_warehouse_id = $request->post('m_warehouse_id');
        $mandor = $request->post('mandor');
        $stok = $request->post('stok');
        
        $inv_req_d_id_rest = $request->post('inv_req_d_id_rest');
        $qty_req_bullet = $request->post('qty_req_bullet');
        $qty_req_dec = $request->post('qty_req_dec');
        $persediaan_out=0;

        $isSubmit = true;
        // for($i = 0; $i < count($inv_request_d_id); $i++) {
        //     if ($stok[$i] < $qty[$i]) {
        //         $isSubmit = false;
        //         break;
        //     }
        // }
        $inv_requests=DB::table('inv_requests')->where('id', $inv_request_id)->first();

        if ($isSubmit) {
            // insert ke special
            try
            {
                $headers = [
                    'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                    'Accept'        => 'application/json',
                ];
                $client = new Client(['base_uri' => $this->base_api_url . '/inv/base/InvRequest/' . $inv_request_id ]);
                $reqBody = [
                    'headers' => $headers,
                    'json' => [
                        'contractor' => $mandor
                    ]
                ]; 
                $response = $client->request('PUT', '', $reqBody);
            } catch(RequestException $exception) {
            }

            //insert detail material tidak utuh
            if ($inv_req_d_id_rest != null) {
                for($i = 0; $i < count($inv_req_d_id_rest); $i++) {
                    if ($qty_req_bullet[$i] != 0 && $qty_req_bullet[$i] != null) {
                        $inv_request_d_rest=DB::table('inv_request_ds')->where('id', $inv_req_d_id_rest[$i])->first();
                        for($j = 1; $j <= $qty_req_bullet[$i]; $j++) {
                            try
                            {
                                $headers = [
                                    'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                                    'Accept'        => 'application/json',
                                ];
                                $client = new Client(['base_uri' => $this->base_api_url . '/inv/base/InvRequestRestD']);
                                $reqBody = [
                                'headers' => $headers,
                                'json' => [
                                        'inv_request_id' => $inv_request_id,
                                        'm_item_id' => $inv_request_d_rest->m_item_id,
                                        'amount' => $qty_req_dec[$i],
                                        'm_unit_id' => $inv_request_d_rest->m_unit_id,
                                        'notes' => 'Pengembalian Material'
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
                    //insert detail
                }
            }
            //insert inv_trx
            $period_year = Carbon::now()->year;
            $period_month = Carbon::now()->month;
            $rabcon = new RabController();
            $inv_no = $rabcon->generateTransactionNo('INV_OUT', $period_year, $period_month, $this->site_id );
            try
            {
                $headers = [
                    'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                    'Accept'        => 'application/json',
                ];
                $client = new Client(['base_uri' => $this->base_api_url . '/inv/base/InvTrx']);
                $reqBody = [
                    'headers' => $headers,
                    'json' => [
                        'm_warehouse_id' => 1,
                        'purchase_id' => null,
                        'trx_type' => 'REQ_ITEM',
                        'inv_request_id' => $inv_request_id,
                        'no' => $inv_no,
                        'inv_trx_date' => Carbon::now()->toDateString(),
                        'site_id' => $this->site_id,
                        'is_entry' => false
                        ]
                    ]; 
                    $response = $client->request('POST', '', $reqBody); 
                    $body = $response->getBody();
                    $content = $body->getContents();
                    $response_array = json_decode($content,TRUE);
                    $inv_trx = $response_array['data'];
            } catch(RequestException $exception) {
            }

            $total_material=$total_spare_part=0;
            $temp_journal=array();
            for($i = 0; $i < count($inv_request_d_id); $i++) {
                // set nilai material untuk pengeluaran
                // try
                // {
                //     $client = new Client(['base_uri' => $this->base_api_url . '/inv/value_out']);
                //     $reqBody = [
                //         'json' => [
                //             'm_item_id' => $m_item_id[$i],
                //             'qty' => $qty[$i]
                //             ]
                //         ]; 
                //         $response = $client->request('POST', '', $reqBody); 
                //         $body = $response->getBody();
                //         $content = $body->getContents();
                //         $response_array = json_decode($content,TRUE);
                //         $value = $response_array['data']['value'];
                // } catch(RequestException $exception) {
                // }
                
                //mengambil harga item tiap site
                if ($qty[$i] != 0) {
                    $get_save_price=DB::table('m_item_prices')->where(['m_item_id' => $m_item_id[$i], 'm_unit_id' => $m_unit_id[$i], 'site_id' => $this->site_id])->first();
                    $price = 0;
                    if(isset($get_save_price->price)){
                        $price = $get_save_price->price;
                        $persediaan_out+=($qty[$i] * $price);
                    }
                    else{
                        $persediaan_out+=($qty[$i] * 0);
                    }

                    //insert inv_trx_d
                    try
                    {
                        $headers = [
                            'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                            'Accept'        => 'application/json',
                        ];
                        $client = new Client(['base_uri' => $this->base_api_url . '/inv/base/InvTrxD']);
                        $reqBody = [
                            'headers' => $headers,
                            'json' => [
                                'inv_trx_id' => $inv_trx['id'],
                                'm_item_id' => $m_item_id[$i],
                                'amount' => $qty[$i],
                                'm_unit_id' => $m_unit_id[$i],
                                'm_warehouse_id' => $m_warehouse_id[$i],
                                'value' => ($qty[$i] * $price),
                                'type_material' => $type_stok[$i],
                                'base_price'    => $type_stok[$i] != 'TRF_STK' ? $price : 0
                                ]
                            ]; 
                            $response = $client->request('POST', '', $reqBody); 
                    } catch(RequestException $exception) {
                    }   

                    $item=DB::table('m_items')->where('id', $m_item_id[$i])->first();
                    if ($item->category == 'MATERIAL') {
                        if ($type_stok[$i] != 'TRF_STK') {
                            $total_material+=($qty[$i] * $price);
                            $temp_journal[]=array(
                                'total' => ($qty[$i] * $price),
                                'm_warehouse_id' => $m_warehouse_id[$i],
                                'type'      => 'material',
                                'm_item_id' => $m_item_id[$i],
                            );
                            // $input_jurnal=array(
                            //     'inv_trx_id' => $inv_trx['id'],
                            //     'total' => ($qty[$i] * $price),
                            //     'user_id'   => $this->user_id,
                            //     'deskripsi'     => 'Pengeluaran Material dari No '.$inv_no,
                            //     'tgl'       => date('Y-m-d'),
                            //     'type'      => 'material',
                            //     'project_req_development_id'    => $inv_requests->project_req_development_id,
                            //     'm_warehouse_id' => $m_warehouse_id[$i],
                            //     'inv_request_id'    => $inv_request_id,
                            //     'location_id'   => $this->site_id
                            // );
                            // $this->journalPengeluaran($input_jurnal);
                        }
                    }else{
                        // $total_spare_part+=($qty[$i] * $price);
                        if ($type_stok[$i] != 'TRF_STK') {
                            $total_material+=($qty[$i] * $price);
                            $temp_journal[]=array(
                                'total' => ($qty[$i] * $price),
                                'm_warehouse_id' => $m_warehouse_id[$i],
                                'type'      => 'spare part',
                                'm_item_id' => $m_item_id[$i],
                            );
                            // $input_jurnal=array(
                            //     'inv_trx_id' => $inv_trx['id'],
                            //     'total' => ($qty[$i] * $price),
                            //     'user_id'   => $this->user_id,
                            //     'deskripsi'     => 'Pengeluaran Spare Part dari No '.$inv_no,
                            //     'tgl'       => date('Y-m-d'),
                            //     'type'      => 'spare part',
                            //     'project_req_development_id'    => $inv_requests->project_req_development_id,
                            //     'm_warehouse_id' => $m_warehouse_id[$i],
                            //     'inv_request_id'    => $inv_request_id,
                            //     'location_id'   => $this->site_id
                            // );
                            // $this->journalPengeluaran($input_jurnal);
                        }
                    }

                    $cek_stok=DB::table('stocks')
                                ->where('m_warehouse_id', $m_warehouse_id[$i])
                                ->where('site_id', $this->site_id)
                                ->where('m_item_id', $m_item_id[$i])
                                ->where('m_unit_id', $m_unit_id[$i])
                                ->where('type', $type_stok[$i])
                                ->first();
                    if ($cek_stok == null) {
                        try
                        {
                            $headers = [
                                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                                'Accept'        => 'application/json',
                            ];
                            $client = new Client(['base_uri' => $this->base_api_url . '/inv/base/Stock']);
                            $reqBody = [
                                'headers' => $headers,
                                'json' => [
                                    'site_id' => $this->site_id,
                                    'm_item_id' => $m_item_id[$i],
                                    'amount' => $qty[$i],
                                    'amount_out' => $qty[$i],
                                    'm_unit_id' => $m_unit_id[$i],
                                    'm_warehouse_id' => $m_warehouse_id[$i],
                                    'type'  => $type_stok[$i]
                                    ]
                                ]; 
                                $response = $client->request('POST', '', $reqBody); 
                                $body = $response->getBody();
                                $content = $body->getContents();
                                $response_array = json_decode($content,TRUE);
                        } catch(RequestException $exception) {
                        }
                    }else{
                        try
                        {
                            $headers = [
                                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                                'Accept'        => 'application/json',
                            ];
                            $client = new Client(['base_uri' => $this->base_api_url . '/inv/base/Stock/'.$cek_stok->id]);
                            $reqBody = [
                                'headers' => $headers,
                                'json' => [
                                    'amount' => $cek_stok->amount - $qty[$i],
                                    'amount_out' => $cek_stok->amount_out + $qty[$i]
                                    ]
                                ]; 
                                $response = $client->request('PUT', '', $reqBody); 
                                $body = $response->getBody();
                                $content = $body->getContents();
                                $response_array = json_decode($content,TRUE);
                        } catch(RequestException $exception) {
                        }
                    }
                }
                
            }
            $input_jurnal=array(
                'inv_trx_id' => $inv_trx['id'],
                'data' => $temp_journal,
                'total' => $total_material,
                'user_id'   => $this->user_id,
                'deskripsi'     => 'Pengeluaran Material dari No '.$inv_no,
                'tgl'       => date('Y-m-d'),
                'project_req_development_id'    => $inv_requests->project_req_development_id,
                'inv_request_id'    => $inv_request_id,
                'location_id'   => $this->site_id
            );
            $this->journalPengeluaran($input_jurnal);
            //insert detail material tidak utuh ke inventory
            if ($inv_req_d_id_rest != null) {
                for($i = 0; $i < count($inv_req_d_id_rest); $i++) {
                    if ($qty_req_bullet[$i] != 0 && $qty_req_bullet[$i] != null) {
                        $inv_request_d_rest=DB::table('inv_request_ds')->where('id', $inv_req_d_id_rest[$i])->first();
                        for($j = 1; $j <= $qty_req_bullet[$i]; $j++) {

                            $get_save_price=DB::table('m_item_prices')
                                                ->where(['m_item_id' => $inv_request_d_rest->m_item_id, 'm_unit_id' => $inv_request_d_rest->m_unit_id, 'site_id' => $this->site_id])
                                                ->first();
                            
                            $persediaan_out+=($qty_req_dec[$i] * $price);
                            
                            try
                            {
                                $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . '/inv/base/InvTrxRestD']);
                                $reqBody = [
                                'headers' => $headers,
                'json' => [
                                        'inv_trx_id' => $inv_trx['id'],
                                        'm_item_id' => $inv_request_d_rest->m_item_id,
                                        'amount' => $qty_req_dec[$i],
                                        'm_unit_id' => $inv_request_d_rest->m_unit_id,
                                        'm_warehouse_id' => $inv_request_d_rest->m_warehouse_id,
                                    ]
                                ]; 
                                $response = $client->request('POST', '', $reqBody); 
                                $body = $response->getBody();
                                $content = $body->getContents();
                                $response_array = json_decode($content,TRUE);
                            } catch(RequestException $exception) {
                            }
                        }
                        $cek_stok=DB::table('stock_rests')
                                    ->where('m_warehouse_id', $inv_request_d_rest->m_warehouse_id)
                                    ->where('site_id', $this->site_id)
                                    ->where('m_item_id', $inv_request_d_rest->m_item_id)
                                    ->where('m_unit_id', $inv_request_d_rest->m_unit_id)
                                    ->where('amount_pieces', $qty_req_dec[$i])
                                    ->first();
                        if ($cek_stok == null) {
                            try
                            {
                                $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . '/inv/base/StockRest']);
                                $reqBody = [
                                    'headers' => $headers,
                'json' => [
                                        'site_id' => $this->site_id,
                                        'm_item_id' => $inv_request_d_rest->m_item_id,
                                        'amount' => $qty_req_bullet[$i],
                                        'amount_in' => $qty_req_bullet[$i],
                                        'amount_out' => 0,
                                        'm_unit_id' => $inv_request_d_rest->m_unit_id,
                                        'm_warehouse_id' => $inv_request_d_rest->m_warehouse_id,
                                        'type'  => 'STK_NORMAL',
                                        'amount_pieces' => $qty_req_dec[$i]
                                        ]
                                    ]; 
                                    $response = $client->request('POST', '', $reqBody); 
                                    $body = $response->getBody();
                                    $content = $body->getContents();
                                    $response_array = json_decode($content,TRUE);
                            } catch(RequestException $exception) {
                            }
                        }else{
                            try
                            {
                                $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . '/inv/base/StockRest/'.$cek_stok->id]);
                                $reqBody = [
                                    'headers' => $headers,
                'json' => [
                                        'amount' => $cek_stok->amount - $qty_req_bullet[$i],
                                        'amount_out' => $cek_stok->amount_out + $qty_req_bullet[$i]
                                        ]
                                    ]; 
                                    $response = $client->request('PUT', '', $reqBody); 
                                    $body = $response->getBody();
                                    $content = $body->getContents();
                                    $response_array = json_decode($content,TRUE);
                            } catch(RequestException $exception) {
                            }
                        }
                    }
                    //insert detail
                }
            }
            // $data=array(
            //     'inv_no'        => $inv_no,
            //     'total'         => $persediaan_out
            // );
            // $this->journalPengeluaran($data);

            $notification = array(
                'message' => 'Success Auth Request',
                'alert-type' => 'success'
            );
        } else {
            $notification = array(
                'message' => 'Error, Stock cannot smaller than request',
                'alert-type' => 'error'
            );
        }

        return redirect('pengeluaran_barang')->with($notification);
    }

    public function printPengeluaranBarang($id) {
        $inv_request = null;
        $inv_request_d = null;

        $hitung_h = 0;
        while($inv_request == null && $hitung_h < 10) {
            // Get Header
            try
            {
                $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . '/inv/base/InvRequest/' . $id]);  
                $response = $client->request('GET', '', ['headers' => $headers]); 
                $body = $response->getBody();
                $content = $body->getContents();
                $response_array = json_decode($content,TRUE);

                $inv_request = $response_array['data'];         
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
                $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . 'inv/inv_trx/get_by_inv_request_id/' . $id]);  
                $response = $client->request('GET', '', ['headers' => $headers]); 
                $body = $response->getBody();
                $content = $body->getContents();
                $response_array = json_decode($content,TRUE);

                $inv_trx = $response_array['data'];         
            } catch(RequestException $exception) {
                
            }
            $hitung_inv_trx++;
        }
        $inv_request['inv_trx'] = $inv_trx;

        // Get RAB
        $rab = null;
        try
        {
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . '/rab/base/Rab/' . $inv_request['rab_id']]);  
            $response = $client->request('GET', '', ['headers' => $headers]); 
            $body = $response->getBody();
            $content = $body->getContents();
            $response_array = json_decode($content,TRUE);

            $rab = $response_array['data'];         
        } catch(RequestException $exception) {
            
        }

        $project = null;
        if ($rab != null) {
            // Get RAB
            try
            {
                $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . '/rab/base/Project/' . $rab['project_id']]);  
                $response = $client->request('GET', '', ['headers' => $headers]); 
                $body = $response->getBody();
                $content = $body->getContents();
                $response_array = json_decode($content,TRUE);

                $project = $response_array['data'];         
            } catch(RequestException $exception) {
                
            }
        }
        $inv_request['project'] = $project;
        
        // Get detail
        try
        {
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . '/inv/pengambilan_barang_detail/' . $id]);  
            $response = $client->request('GET', '', ['headers' => $headers]); 
            $body = $response->getBody();
            $content = $body->getContents();
            $response_array = json_decode($content,TRUE);

            $inv_request_d = $response_array['data'];         
        } catch(RequestException $exception) {
            
        } 

        $data = array(
            'inv_request' => $inv_request != null ? $inv_request : null,
            'inv_request_d' => $inv_request_d,
            'user_name' => $this->user_name
        );
        return view('pages.inv.pengambilan_barang.print_pengeluaran_material', $data);
    }

    //JSON METHOD
    public function getStokSite($site_id){
        $response = null;
        try
        {
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . '/inv/stok/'.$site_id]);  
            $response = $client->request('GET', '', ['headers' => $headers]); 
            $body = $response->getBody();
            $content = $body->getContents();
            $response_array = json_decode($content,TRUE);

            $response = $response_array['data'];         
        } catch(RequestException $exception) {
            
        }

        return $response;
    }

    public function getAllMaterialJson()
    {
        $response = null;
        try
        {
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . 'rab/all_material']);  
            $response = $client->request('GET', '', ['headers' => $headers]); 
            $body = $response->getBody();
            $content = $body->getContents();
            $response_array = json_decode($content,TRUE);

            $response = $content;         
        } catch(RequestException $exception) {
            
        }    
        $data=json_decode(json_encode($response_array['data']));
        foreach ($data as $key => $value) {
            $dt=DB::table('m_units')->where('id', $value->m_unit_child)->first();
            $value->m_unit_childs=$dt != null ? $dt->name : '-';
        }
        
        $data1['data']=$data;
        return json_encode($data1);
    }

    public function getAllMaterialWithoutATKJson()
    {
        $response = null;
        try
        {
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . 'rab/all_material_without_atk']);  
            $response = $client->request('GET', '', ['headers' => $headers]); 
            $body = $response->getBody();
            $content = $body->getContents();
            $response_array = json_decode($content,TRUE);

            $response = $content;         
        } catch(RequestException $exception) {
            
        }    
        $data=json_decode(json_encode($response_array['data']));
        foreach ($data as $key => $value) {
            $dt=DB::table('m_units')->where('id', $value->m_unit_child)->first();
            $value->m_unit_childs=$dt != null ? $dt->name : '-';
        }
        
        $data1['data']=$data;
        return ($data1);
    }

    public function getAllMaterialATKJson()
    {
        $response = null;
        try
        {
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . 'rab/all_material_atk']);  
            $response = $client->request('GET', '', ['headers' => $headers]); 
            $body = $response->getBody();
            $content = $body->getContents();
            $response_array = json_decode($content,TRUE);

            $response = $content;         
        } catch(RequestException $exception) {
            
        }    
        $data=json_decode(json_encode($response_array['data']));
        foreach ($data as $key => $value) {
            $dt=DB::table('m_units')->where('id', $value->m_unit_child)->first();
            $value->m_unit_childs=$dt != null ? $dt->name : '-';
        }
        
        $data1['data']=$data;
        return json_encode($data1);
    }

    public function getAllMaterialRabJson(){
        $rab_id = $_GET['rab_id'];

        $response = null;
        try
        {
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . 'rab/all_material/'.$rab_id]);  
            $response = $client->request('GET', '', ['headers' => $headers]); 
            $body = $response->getBody();
            $content = $body->getContents();
            $response_array = json_decode($content,TRUE);
            $response = $content;        
        } catch(RequestException $exception) {
            
        }    

        return $response;
    }

    public function getAllMaterialRabJsonByPW(){
        $pw_id = $_GET['pw_id'];
        
        $response = null;
        try
        {
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . 'rab/all_material_by_pw/'.$pw_id]);  
            $response = $client->request('GET', '', ['headers' => $headers]); 
            $body = $response->getBody();
            $content = $body->getContents();
            $response_array = json_decode($content,TRUE);
            $response = $content;        
        } catch(RequestException $exception) {
            
        }    

        return $response;
    }

    public function getAllMaterialRabJsonByRequestDev(){
        // $pw_id = $_GET['pw_id'];
        $req_id = $_GET['req_id'];
        
        $response = null;
        try
        {
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . 'rab/all_material_by_req_dev/'.$req_id]);  
            $response = $client->request('GET', '', ['headers' => $headers]); 
            $body = $response->getBody();
            $content = $body->getContents();
            $response_array = json_decode($content,TRUE);
            $response = $content;        
        } catch(RequestException $exception) {
            
        }    

        return $response;
    }

    public function getListPengambilanBarang(){
        $response = null;
        try
        {
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . '/inv/pengambilan_barang']);  
            $response = $client->request('GET', '', ['headers' => $headers]); 
            $body = $response->getBody();
            $content = $body->getContents();
            $response_array = json_decode($content,TRUE);

            $response = $content;         

            $data=DataTables::of($response_array['data'])
                                    ->make(true);        
        } catch(RequestException $exception) {
            
        }    

        return $data;
    }

    public function getListPengambilanBarangAcc(){
        $response = null;
        try
        {
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . '/inv/acc_pengambilan_barang']);  
            $response = $client->request('GET', '', ['headers' => $headers]); 
            $body = $response->getBody();
            $content = $body->getContents();
            $response_array = json_decode($content,TRUE);

            $response = $content;         

            $data=DataTables::of($response_array['data'])
                                    ->make(true);        
        } catch(RequestException $exception) {
            
        }    

        return $data;
    }

    public function getListPengambilanBarangAuth(){
        $response = null;
        try
        {
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . '/inv/auth_pengambilan_barang']);  
            $response = $client->request('GET', '', ['headers' => $headers]); 
            $body = $response->getBody();
            $content = $body->getContents();
            $response_array = json_decode($content,TRUE);

            $response = $content;         

            $data=DataTables::of($response_array['data'])
                                    ->make(true);        
        } catch(RequestException $exception) {
            
        }    

        return $data;
    }

    public function getListPengembalianBarang(){
        $response = null;
        try
        {
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . '/inv/pengembalian_barang']);  
            $response = $client->request('GET', '', ['headers' => $headers]); 
            $body = $response->getBody();
            $content = $body->getContents();
            $response_array = json_decode($content,TRUE);

            $response = $content;         

            $data=DataTables::of($response_array['data'])
                                    ->make(true);        
        } catch(RequestException $exception) {
            
        }    

        return $data;
    }

    public function getListPengambilanBarangDetail($id){
        $response = null;
        try
        {
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . '/inv/pengambilan_barang_detail/' . $id]);  
            $response = $client->request('GET', '', ['headers' => $headers]); 
            $body = $response->getBody();
            $content = $body->getContents();
            $response_array = json_decode($content,TRUE);

            $response = $content;         
        } catch(RequestException $exception) {
            
        }    
        foreach ($response_array['data'] as $key => $value) {
            if(isset($value['m_items']['m_unit_child'])){
                $response_array['data'][$key]['m_unit_child']=DB::table('m_units')->where('id', $value['m_items']['m_unit_child'])->first();
            }

            $query=DB::table('inv_trx_ds')->join('inv_trxes', 'inv_trxes.id', 'inv_trx_ds.inv_trx_id')->where('inv_request_id', $value['inv_request_id'])->where('m_item_id', $value['m_item_id'])->select(DB::raw('COALESCE(SUM(amount), 0) as amount'))->first();

            $response_array['data'][$key]['total_used']=$query->amount;
        }
        
        $getInvReq=DB::table('inv_requests')->where('id', $id)->first();
        $id_refered_prod_sub=$getInvReq->id;
        if ($getInvReq->inv_request_id != null) {
            $getInvReqId=DB::table('inv_requests')->where('id', $getInvReq->inv_request_id)->first();
            $id_refered_prod_sub=$getInvReqId->id;
        }
        $inv_prod_sub=DB::table('inv_request_prod_ds')
                            ->select('product_subs.*')
                            ->where('inv_request_id', $id_refered_prod_sub)
                            ->join('product_subs', 'product_subs.id', '=', 'inv_request_prod_ds.product_sub_id')
                            ->get();
        $rest_material=DB::table('inv_request_rest_ds')
                            ->where('inv_request_id', $id)
                            ->select(DB::raw('MAX(m_item_id) as m_item_id'), DB::raw('MAX(amount) as amount'), DB::raw('MAX(m_unit_id) as m_unit_id'), DB::raw('COUNT(id) as total'))
                            ->groupBy('m_item_id')->get();
        foreach ($rest_material as $key => $value) {
            $value->m_items=DB::table('m_items')->where('id', $value->m_item_id)->first();
            $value->m_units=DB::table('m_units')->where('id', $value->m_unit_id)->first();
        }

        $totalProjectReq = DB::table('project_req_developments')->select('total')->where('id', $getInvReq->project_req_development_id)->first();

        $data['data']=array(
            'detail'    => $response_array['data'],
            'prod_sub'  => $inv_prod_sub,
            'rest'      => $rest_material,
            'totalProjectReq' => $totalProjectReq,
        );
        return $data;
        // return $data['data']['detail'];
    }

    public function getListAccPengambilanBarangDetail($id){
        $detailAcc=DB::table('inv_trxes')
                        ->join('inv_trx_ds', 'inv_trx_ds.inv_trx_id', '=', 'inv_trxes.id')
                        ->where('inv_trxes.inv_request_id', $id)
                        ->where('inv_trxes.trx_type', 'REQ_ITEM')
                        ->select('inv_trx_ds.*')
                        ->get();
        foreach ($detailAcc as $key => $value) {
            $value->m_items=DB::table('m_items')->where('id', $value->m_item_id)->first();
            $value->m_units=DB::table('m_units')->where('id', $value->m_unit_id)->first();
        }

        $detailRestAcc=DB::table('inv_trxes')
                        ->join('inv_trx_rest_ds as d', 'd.inv_trx_id', '=', 'inv_trxes.id')
                        ->where('inv_trxes.inv_request_id', $id)
                        ->select(DB::raw('MAX(d.amount) as amount'), DB::raw('COUNT(d.amount) as total'), DB::raw('MAX(d.m_item_id) as m_item_id'), DB::raw('MAX(d.m_unit_id) as m_unit_id'))
                        ->groupBy(['d.m_item_id', 'd.amount'])
                        ->get();
        foreach ($detailRestAcc as $key => $value) {
            $value->m_items=DB::table('m_items')->where('id', $value->m_item_id)->first();
            $value->m_units=DB::table('m_units')->where('id', $value->m_unit_id)->first();
        }

        $getInvReq=DB::table('inv_requests')->where('id', $id)->first();
        $id_refered_prod_sub=$getInvReq->id;
        if ($getInvReq->inv_request_id != null) {
            $getInvReqId=DB::table('inv_requests')->where('id', $getInvReq->inv_request_id)->first();
            $id_refered_prod_sub=$getInvReqId->id;
        }
        $inv_prod_sub=DB::table('inv_request_prod_ds')
                            ->select('product_subs.*')
                            ->where('inv_request_id', $id_refered_prod_sub)
                            ->join('product_subs', 'product_subs.id', '=', 'inv_request_prod_ds.product_sub_id')
                            ->get();

        $totalProjectReq = DB::table('project_req_developments')->select('total')->where('id', $getInvReq->project_req_development_id)->first();
        $data['data']=array(
            'detail'    => $detailAcc,
            'detail_rest'    => $detailRestAcc,
            'prod_sub'  => $inv_prod_sub,
            'totalProjectReq' => $totalProjectReq,
        );
        return $data;
    }

    public function getSisaMaterialRequestDetail($id){
        $response = null;
        try
        {
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . '/inv/inv_trx_by_request/' . $id]);  
            $response = $client->request('GET', '', ['headers' => $headers]); 
            $body = $response->getBody();
            $content = $body->getContents();
            $response_array = json_decode($content,TRUE);

            $response = $content;         
        } catch(RequestException $exception) {
            
        }    
        $detail_sisa=DB::table('inv_requests')
                        ->select('inv_request_ds.m_item_id', DB::raw('SUM(inv_request_ds.amount) AS amount'))
                        ->where('inv_requests.inv_request_id', $id)
                        ->join('inv_request_ds', 'inv_request_ds.inv_request_id', '=', 'inv_requests.id')
                        ->groupBy('m_item_id')
                        ->get();
        
        $data=array();
        foreach ($response_array['data'] as $key => $value) {
            $qty=$value['amount'];
            $warehouse=DB::table('m_warehouses')->where('id', $value['m_warehouse_id'])->first();
            foreach ($detail_sisa as $k => $v) {
                if ($v->m_item_id == $value['m_item_id']) {
                    $qty=$value['amount']-$v->amount;
                }
            }
            $unit_child=DB::table('m_units')->where('id', $value['m_items']['m_unit_child'])->first();
            // echo $value['amount'];
            $data['data'][]=array(
                'id' => $value['id'],
                'm_item_id' => $value['m_item_id'],
                'm_unit_id' => $value['m_unit_id'],
                'type_material' => $value['type_material'],
                'base_price' => $value['base_price'],
                'amount' => $qty,
                'm_items' => $value['m_items'],
                'm_units' => $value['m_units'],
                'm_unit_child' => $unit_child,
                'm_warehouse_id' => $value['m_warehouse_id'],
                'm_warehouses' => $warehouse,
            );
        }
        return $data;
    }

    public function getListPengeluaranBarangJson() {
        $response = null;
        try
        {
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . '/inv/pengeluaran_barang']);  
            $response = $client->request('GET', '', ['headers' => $headers]); 
            $body = $response->getBody();
            $content = $body->getContents();
            $response_array = json_decode($content,TRUE);

            $response = $content;    
            $data=DataTables::of($response_array['data'])
                                    ->make(true);             
        } catch(RequestException $exception) {
            
        }    

        return $data;
    }
    public function return()
    {
        //basic variable
        $is_error = false;
        $error_message = '';

        //bussiness variable
        $all_sites = null;
        $all_projects = null;
        $m_inv_request = null;

        try
        {
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            if ($this->site_id == null) {
                $client1 = new Client(['base_uri' => $this->base_api_url . 'order/option']); 
            }else{
                $client1 = new Client(['base_uri' => $this->base_api_url . 'order/option?site_id='.$this->site_id]); 
            } 
            $response1 = $client1->request('GET', '', ['headers' => $headers]); 
            $body1 = $response1->getBody();
            $content1 = $body1->getContents();
            $response_array1 = json_decode($content1,TRUE);

            $response1 = $content1;  
            $order_list = $response_array1['data'];
        } catch(RequestException $exception) {
            
        }

        $data = array(
            'error' => array(
                'is_error' => $is_error,
                'error_message' => $error_message
            ),
            'order_list' => $order_list,
            'site_id' => $this->site_id
        );

        // print_r($m_inv_request);exit();
        
        return view('pages.inv.pengambilan_barang.pengembalian_sisa_form', $data);
    }

    public function return_list()
    {
        $is_error = false;
        $error_message = '';
        $data = array(
            'error' => array(
                'is_error' => $is_error,
                'error_message' => $error_message
            )
        );
        return view('pages.inv.pengambilan_barang.pengembalian_sisa_list', $data);
    }
    public function returnListJson(){
        $response = null;
        try
        {
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . '/inv/pengembalian_sisa']);  
            $response = $client->request('GET', '', ['headers' => $headers]); 
            $body = $response->getBody();
            $content = $body->getContents();
            $response_array = json_decode($content,TRUE);

            $response = $content;         
        } catch(RequestException $exception) {
            
        }    

        return $response;
    }
    public function getListPengembalianBarangDetail($id){
        $response = null;
        try
        {
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . '/inv/pengembalian_sisa_detail/' . $id]);  
            $response = $client->request('GET', '', ['headers' => $headers]); 
            $body = $response->getBody();
            $content = $body->getContents();
            $response_array = json_decode($content,TRUE);

            $response = $content;         
        } catch(RequestException $exception) {
            
        }    

        return $response;
    }
    // public function returnPost(Request $request)
    // {
    //     $rab_id = $request->post('rab_no');

    //     $m_item_id = $request->post('m_item_id');
    //     $qty_rab = $request->post('qty_rab');
    //     $qty_sisa_rab = $request->post('qty_sisa_rab');
    //     $qty = $request->post('qty');
    //     $m_unit_id = $request->post('m_unit_id');
        
    //     $detail_barang = array();
    //     // $permintaan_khusus = array();
    //     for($i = 0; $i < count($m_item_id); $i++) {
    //             array_push($detail_barang, 
    //                 array(
    //                     'm_item_id' => $m_item_id[$i],
    //                     'm_unit_id' => $m_unit_id[$i],
    //                     // 'qty_rab' => $qty_rab[$i],
    //                     // 'qty_sisa_rab' => $qty_sisa_rab[$i],
    //                     'qty' => $qty[$i],
    //                 ));
    //     }
        
    //     $period_year = Carbon::now()->year;
    //     $period_month = Carbon::now()->month;
    //     $rabcon = new RabController();
    //     $memo_no = $rabcon->generateTransactionNo('MREQ', $period_year, $period_month, $this->site_id );
        
    //     // try
    //     // {
    //     //     $headers = [
    //             'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
    //             'Accept'        => 'application/json',
    //         ];
    //         $client = new Client(['base_uri' => $this->base_api_url . 'inv/base/InvReturn']);
    // //     //     $reqBody = [
    // //     //     'headers' => $headers,
    //             'json' => [
    //     //             'rab_id' => $rab_id,
    //     //             'no' => $memo_no,
    //     //             'user_id' => $this->user_id
    //     //         ]
    //     //     ];
    //     //     $response = $client->request('POST', '', $reqBody); 
    //     //     $body = $response->getBody();
    //     //     $content = $body->getContents();
    //     //     $response_array = json_decode($content,TRUE);
    //     //     $inv_ret = $response_array['data'];
    //     // } catch(RequestException $exception) {
    //     // }

    //     // for($i = 0; $i < count($detail_barang); $i++) {
    //     //     //insert detail
    //     //     try
    //     //     {
    //     //         $headers = [
    //             'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
    //             'Accept'        => 'application/json',
    //         ];
    //         $client = new Client(['base_uri' => $this->base_api_url . '/inv/base/InvReturnD']);
    // //     //         $reqBody = [
    // //     //         'headers' => $headers,
    //             'json' => [
    //     //                 'inv_return_id' => $inv_ret['id'],
    //     //                 'm_item_id' => $detail_barang[$i]['m_item_id'],
    //     //                 'amount' => $detail_barang[$i]['qty'],
    //     //                 'm_unit_id' => $detail_barang[$i]['m_unit_id']
    //     //             ]
    //     //         ]; 
    //     //         $response = $client->request('POST', '', $reqBody); 
    //     //         $body = $response->getBody();
    //     //         $content = $body->getContents();
    //     //         $response_array = json_decode($content,TRUE);
    //     //     } catch(RequestException $exception) {
    //     //     }
    //     // }

    //     try
    //         {
    //             $headers = [
    //             'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
    //             'Accept'        => 'application/json',
    //         ];
    //         $client = new Client(['base_uri' => $this->base_api_url . '/inv/base/InvRequest']);
    // //             $reqBody = [
    // //             'headers' => $headers,
    //             'json' => [
    //                     'req_type' => 'RET_ITEM',
    //                     'rab_id' => $rab_id,
    //                     'no' => $memo_no
    //                 ]
    //             ]; 
    //             $response = $client->request('POST', '', $reqBody); 
    //             $body = $response->getBody();
    //             $content = $body->getContents();
    //             $response_array = json_decode($content,TRUE);
    //             $inv_spc_req = $response_array['data'];
    //         } catch(RequestException $exception) {
    //         }

    //     for($i = 0; $i < count($detail_barang); $i++) {
    //         //insert detail
    //         try
    //         {
    //             $headers = [
    //             'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
    //             'Accept'        => 'application/json',
    //         ];
    //         $client = new Client(['base_uri' => $this->base_api_url . '/inv/base/InvRequestD']);
    // //             $reqBody = [
    // //             'headers' => $headers,
    //             'json' => [
    //                     'inv_request_id' => $inv_spc_req['id'],
    //                     'm_item_id' => $detail_barang[$i]['m_item_id'],
    //                     'amount' => $detail_barang[$i]['qty'],
    //                     'm_unit_id' => $detail_barang[$i]['m_unit_id'],
    //                     // 'detail_notes' => $permintaan_khusus[$i]['detail_note'],
    //                     'notes' => 'Pengembalian Material'
    //                 ]
    //             ]; 
    //             $response = $client->request('POST', '', $reqBody); 
    //             $body = $response->getBody();
    //             $content = $body->getContents();
    //             $response_array = json_decode($content,TRUE);
    //         } catch(RequestException $exception) {
    //         }
    //     }
        
        
    //     try
    //     {
    //         $headers = [
    //             'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
    //             'Accept'        => 'application/json',
    //         ];
    //         $client = new Client(['base_uri' => $this->base_api_url . '/inv/base/InvTrx']);
    // //         $reqBody = [
    // //             'headers' => $headers,
    //             'json' => [
    //                 'm_warehouse_id' => 1,
    //                 'purchase_id' => null,
    //                 'trx_type' => 'RET_ITEM',
    //                 'inv_request_id' => $inv_spc_req['id'],
    //                 'no' => $memo_no,
    //                 'inv_trx_date' => Carbon::now()->toDateString(),
    //                 'site_id' => $this->site_id,
    //                 'is_entry' => true
    //                 ]
    //             ]; 
    //             $response = $client->request('POST', '', $reqBody); 
    //             $body = $response->getBody();
    //             $content = $body->getContents();
    //             $response_array = json_decode($content,TRUE);
    //             $inv_trx = $response_array['data'];
    //     } catch(RequestException $exception) {
    //     }

    //     for($i = 0; $i < count($detail_barang); $i++) {
            
    //         //insert inv_trx_d
    //         try
    //         {
    //             $headers = [
    //             'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
    //             'Accept'        => 'application/json',
    //         ];
    //         $client = new Client(['base_uri' => $this->base_api_url . '/inv/base/InvTrxD']);
    // //             $reqBody = [
    // //                 'headers' => $headers,
    //             'json' => [
    //                     'inv_trx_id' => $inv_trx['id'],
    //                     'm_item_id' => $detail_barang[$i]['m_item_id'],
    //                     'amount' => $detail_barang[$i]['qty'],
    //                     'm_unit_id' => $detail_barang[$i]['m_unit_id']
    //                     ]
    //                 ]; 
    //                 $response = $client->request('POST', '', $reqBody); 
    //         } catch(RequestException $exception) {
    //         }   
    //     }
    //     $notification = array(
    //         'message' => 'Success Create New Request',
    //         'alert-type' => 'success'
    //     );

    //     return redirect('material_request/returnlist')->with($notification);
    // }

    public function returnPost(Request $request)
    {
        $inv_request_id = $request->post('inv_request_id');
        $inv_req_d_id = $request->post('inv_req_d_id');
        $m_warehouse_id = $request->post('m_warehouse_id');
        $m_item_id = $request->post('m_item_id');
        $m_unit_id = $request->post('m_unit_id');
        $type_material = $request->post('type_material');
        $base_price = $request->post('base_price');
        $qty_awal = $request->post('amount');
        $qty = $request->post('qty');

        $inv_req_d_id_rest = $request->post('inv_req_d_id_rest');
        $qty_req_bullet = $request->post('qty_req_bullet');
        $qty_req_dec = $request->post('qty_req_dec');
        $m_warehouse_rest = $request->post('m_warehouse_rest');
        $m_item_rest = $request->post('m_item_rest');
        $m_unit_rest = $request->post('m_unit_rest');
        
        
        try
        {
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . '/inv/base/InvRequest/'.$inv_request_id]);
            $response = $client->request('GET', '', ['headers' => $headers]); 
            $body = $response->getBody();
            $content = $body->getContents();
            $response_array = json_decode($content,TRUE);

            $inv_request = $response_array['data'];         
        } catch(RequestException $exception) {
            return 'gagal 1';
        }

        $period_year = Carbon::now()->year;
        $period_month = Carbon::now()->month;
        $rabcon = new RabController();
        $memo_no = $rabcon->generateTransactionNo('MREQ', $period_year, $period_month, $this->site_id );
        
        try
            {
                $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . '/inv/base/InvRequest']);
                $reqBody = [
                'headers' => $headers,
                'json' => [
                        'req_type' => 'RET_ITEM',
                        'rab_id' => $inv_request['rab_id'],
                        'inv_request_id' => $inv_request_id,
                        'no' => $memo_no,
                        'site_id' => $this->site_id,
                        'project_req_development_id' => $inv_request['project_req_development_id'],
                        'project_work_id' => $inv_request['project_work_id'],
                    ]
                ]; 
                $response = $client->request('POST', '', $reqBody); 
                $body = $response->getBody();
                $content = $body->getContents();
                $response_array = json_decode($content,TRUE);
                $inv_spc_req = $response_array['data'];
            } catch(RequestException $exception) {
                return 'gagal 2'.$exception->getMessage();
            }

        for($i = 0; $i < count($inv_req_d_id); $i++) {

            if ($qty[$i] != 0 && $qty[$i] != null) {
                // $inv_request_d=DB::table('inv_request_ds')->where('id', $inv_req_d_id[$i])->first();
                try
                {
                    $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . '/inv/base/InvRequestD']);
                    $reqBody = [
                    'headers' => $headers,
                'json' => [
                            'inv_request_id' => $inv_spc_req['id'],
                            'm_item_id' => $m_item_id[$i],
                            'amount' => $qty[$i],
                            'm_unit_id' => $m_unit_id[$i],
                            'm_warehouse_id' => $m_warehouse_id[$i],
                            'notes' => 'Pengembalian Material'
                        ]
                    ]; 
                    $response = $client->request('POST', '', $reqBody); 
                    $body = $response->getBody();
                    $content = $body->getContents();
                    $response_array = json_decode($content,TRUE);
                } catch(RequestException $exception) {
                    return 'gagal 3';
                }
            }
            //insert detail
        }

        //insert detail material tidak utuh
        if ($inv_req_d_id_rest != null) {
            for($i = 0; $i < count($inv_req_d_id_rest); $i++) {
                if ($qty_req_bullet[$i] != 0 && $qty_req_bullet[$i] != null) {
                    // $inv_request_d_rest=DB::table('inv_request_ds')->where('id', $inv_req_d_id_rest[$i])->first();
                    for($j = 1; $j <= $qty_req_bullet[$i]; $j++) {
                        try
                        {
                            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . '/inv/base/InvRequestRestD']);
                            $reqBody = [
                            'headers' => $headers,
                'json' => [
                                    'inv_request_id' => $inv_spc_req['id'],
                                    'm_item_id' => $m_item_rest[$i],
                                    'amount' => $qty_req_dec[$i],
                                    'm_unit_id' => $m_unit_rest[$i],
                                    'm_warehouse_id' => $m_warehouse_rest[$i],
                                    'notes' => 'Pengembalian Material'
                                ]
                            ]; 
                            $response = $client->request('POST', '', $reqBody); 
                            $body = $response->getBody();
                            $content = $body->getContents();
                            $response_array = json_decode($content,TRUE);
                        } catch(RequestException $exception) {
                            return 'gagal 4';
                        }
                    }
                }
                //insert detail
            }
        }
        
        try
        {
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . '/inv/base/InvTrx']);
            $reqBody = [
                'headers' => $headers,
                'json' => [
                    'm_warehouse_id' => 1,
                    'purchase_id' => null,
                    'trx_type' => 'RET_ITEM',
                    'inv_request_id' => $inv_spc_req['id'],
                    'no' => $memo_no,
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
            return 'gagal 5';
        }

        for($i = 0; $i < count($inv_req_d_id); $i++) {
            if ($qty[$i] != 0 && $qty[$i] != null) {
                // $inv_request_d=DB::table('inv_trx_ds')->where('id', $inv_req_d_id[$i])->first();
                $get_save_price=DB::table('m_item_prices')->where(['m_item_id' => $m_item_id[$i], 'm_unit_id' => $m_unit_id[$i], 'site_id' => $this->site_id])->first();
                try
                {
                    $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . '/inv/base/InvTrxD']);
                    $reqBody = [
                    'headers' => $headers,
                'json' => [
                            'inv_trx_id' => $inv_trx['id'],
                            'm_item_id' => $m_item_id[$i],
                            'amount' => $qty[$i],
                            'm_unit_id' => $m_unit_id[$i],
                            'm_warehouse_id' => $m_warehouse_id[$i],
                            'notes' => $request->input('storage')[$i],
                            'type_material' => $type_material[$i],
                            'base_price'    => $base_price[$i]
                        ]
                    ]; 
                    $response = $client->request('POST', '', $reqBody); 
                } catch(RequestException $exception) {
                    return 'gagal 6';
                }
                //simpan perubahan stok 
                $type_stok=($type_material[$i] != 'TRF_STK' ? 'STK_NORMAL' : 'TRF_STK');
                
                $cek_stok=DB::table('stocks')
                            ->where('m_warehouse_id', $m_warehouse_id[$i])
                            ->where('site_id', $this->site_id)
                            ->where('m_item_id', $m_item_id[$i])
                            ->where('m_unit_id', $m_unit_id[$i])
                            ->where('type', $type_stok)
                            ->first();
                if ($cek_stok == null) {
                    try
                    {
                        $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . '/inv/base/Stock']);
                        $reqBody = [
                            'headers' => $headers,
                'json' => [
                                'site_id' => $this->site_id,
                                'm_item_id' => $m_item_id[$i],
                                'amount' => $qty[$i],
                                'amount_in' => $qty[$i],
                                'amount_out' => 0,
                                'm_unit_id' => $m_unit_id[$i],
                                'm_warehouse_id' => $m_warehouse_id[$i],
                                'type'  => $type_stok
                                ]
                            ]; 
                            $response = $client->request('POST', '', $reqBody); 
                            $body = $response->getBody();
                            $content = $body->getContents();
                            $response_array = json_decode($content,TRUE);
                    } catch(RequestException $exception) {
                        return 'gagal 7';
                    }
                }else{
                    try
                    {
                        $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . '/inv/base/Stock/'.$cek_stok->id]);
                        $reqBody = [
                            'headers' => $headers,
                'json' => [
                                'amount' => $cek_stok->amount + $qty[$i],
                                'amount_out' => $cek_stok->amount_out - $qty[$i]
                                ]
                            ]; 
                            $response = $client->request('PUT', '', $reqBody); 
                            $body = $response->getBody();
                            $content = $body->getContents();
                            $response_array = json_decode($content,TRUE);
                    } catch(RequestException $exception) {
                        return 'gagal 8';
                    }
                }

                if ($type_stok != 'TRF_STK') {
                    $item=DB::table('m_items')->where('id', $m_item_id[$i])->first();
                    
                    if ($item->category == 'MATERIAL') {
                        if ($type_stok[$i] != 'TRF_STK') {
                            $input_jurnal=array(
                                'inv_trx_id' => $inv_trx['id'],
                                'total' => ($qty[$i] * $base_price[$i]),
                                'user_id'   => $this->user_id,
                                'deskripsi'     => 'Pengembalian Material dari No '.$memo_no,
                                'tgl'       => date('Y-m-d'),
                                'type'      => 'material',
                                'project_req_development_id' => $inv_request['project_req_development_id'],
                                'm_warehouse_id' => $m_warehouse_id[$i],
                                'inv_request_id' => $inv_spc_req['id'],
                                'location_id'   => $this->site_id
                            );
                            $this->journalPengembalian($input_jurnal);
                        }
                    }else{
                        if ($type_stok[$i] != 'TRF_STK') {
                            $input_jurnal=array(
                                'inv_trx_id' => $inv_trx['id'],
                                'total' => ($qty[$i] * $base_price[$i]),
                                'user_id'   => $this->user_id,
                                'deskripsi'     => 'Pengembalian Spare Part dari No '.$memo_no,
                                'tgl'       => date('Y-m-d'),
                                'type'      => 'spare part',
                                'project_req_development_id' => $inv_request['project_req_development_id'],
                                'm_warehouse_id' => $m_warehouse_id[$i],
                                'inv_request_id' => $inv_spc_req['id'],
                                'location_id'   => $this->site_id
                            );
                            $this->journalPengembalian($input_jurnal);
                        }
                    }
                }
            }
            //insert inv_trx_d
        }

        //insert detail material tidak utuh ke inventory
        if ($inv_req_d_id_rest != null) {
            for($i = 0; $i < count($inv_req_d_id_rest); $i++) {
                if ($qty_req_bullet[$i] != 0 && $qty_req_bullet[$i] != null) {
                    // $inv_request_d_rest=DB::table('inv_request_ds')->where('id', $inv_req_d_id_rest[$i])->first();
                    for($j = 1; $j <= $qty_req_bullet[$i]; $j++) {
                        try
                        {
                            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . '/inv/base/InvTrxRestD']);
                            $reqBody = [
                            'headers' => $headers,
                'json' => [
                                    'inv_trx_id' => $inv_trx['id'],
                                    'm_item_id' => $m_item_rest[$i],
                                    'amount' => $qty_req_dec[$i],
                                    'm_unit_id' => $m_unit_rest[$i],
                                    'm_warehouse_id' => $m_warehouse_rest[$i],
                                    'notes' => $request->input('storage_rest')[$i],
                                ]
                            ]; 
                            $response = $client->request('POST', '', $reqBody); 
                            $body = $response->getBody();
                            $content = $body->getContents();
                            $response_array = json_decode($content,TRUE);
                        } catch(RequestException $exception) {
                        }
                    }
                    //simpan perubahan stok 
                    $cek_stok=DB::table('stock_rests')
                                ->where('m_warehouse_id', $m_warehouse_rest[$i])
                                ->where('site_id', $this->site_id)
                                ->where('m_item_id', $m_item_rest[$i])
                                ->where('m_unit_id', $m_unit_rest[$i])
                                ->where('amount_pieces', $qty_req_dec[$i])
                                ->first();
                    if ($cek_stok == null) {
                        try
                        {
                            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . '/inv/base/StockRest']);
                            $reqBody = [
                                'headers' => $headers,
                'json' => [
                                    'site_id' => $this->site_id,
                                    'm_item_id' => $m_item_rest[$i],
                                    'amount' => $qty_req_bullet[$i],
                                    'amount_in' => $qty_req_bullet[$i],
                                    'amount_out' => 0,
                                    'm_unit_id' => $m_unit_rest[$i],
                                    'm_warehouse_id' => $m_warehouse_rest[$i],
                                    'type'  => 'STK_NORMAL',
                                    'amount_pieces' => $qty_req_dec[$i]
                                    ]
                                ]; 
                                $response = $client->request('POST', '', $reqBody); 
                                $body = $response->getBody();
                                $content = $body->getContents();
                                $response_array = json_decode($content,TRUE);
                        } catch(RequestException $exception) {
                        }
                    }else{
                        try
                        {
                            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . '/inv/base/StockRest/'.$cek_stok->id]);
                            $reqBody = [
                                'headers' => $headers,
                'json' => [
                                    'amount' => $cek_stok->amount + $qty_req_bullet[$i],
                                    'amount_out' => $cek_stok->amount_out - $qty_req_bullet[$i]
                                    ]
                                ]; 
                                $response = $client->request('PUT', '', $reqBody); 
                                $body = $response->getBody();
                                $content = $body->getContents();
                                $response_array = json_decode($content,TRUE);
                        } catch(RequestException $exception) {
                        }
                    }
                }
                //insert detail
            }
        }
        $notification = array(
            'message' => 'Success Create New Request',
            'alert-type' => 'success'
        );

        return redirect('material_request/returnlist')->with($notification);
    }

    public function getProductSubRab($id){
        $query['data']=DB::table('rabs')
                    ->select('product_subs.*')
                    ->join('product_subs', 'product_subs.order_d_id', '=', 'rabs.order_d_id')
                    ->where('rabs.id', $id)
                    ->whereRaw('product_subs.id not in (select product_sub_id from inv_request_prod_ds)')
                    ->orderBy('product_subs.id')
                    ->get();
        return $query;
    }

    public function getProductSubByPW($id, $limit){
        $query['data']=DB::table('rabs')
                    ->select('product_subs.*')
                    ->join('project_works', 'project_works.rab_id', '=', 'rabs.id')
                    ->join('product_subs', 'product_subs.order_d_id', '=', 'rabs.order_d_id')
                    ->where('project_works.id', $id)
                    ->whereRaw('product_subs.id not in (select product_sub_id from inv_request_prod_ds where project_work_id = '.$id.')')
                    ->orderBy('product_subs.id')
                    ->limit($limit)
                    ->get();
        return $query;
    }

    public function getProjectWorkRab($id){
        $query['data']=DB::table('project_works')
                    ->select('*')
                    ->where('project_works.name', 'not ilike', '%pasang%')
                    ->where('rab_id', $id)
                    ->get();
        return $query;
    }

    public function getRequestWorkByRab($id){
        $query['data']=DB::table('project_req_developments')
                    ->select('*')
                    ->where('rab_id', $id)
                    ->get();
        return $query;
    }

    public function getRequestWorkDetail($id){
        $project_req_dev=DB::table('project_req_developments')
                    ->select('project_req_developments.*', 'kavlings.amount as total_kavling', 'kavlings.id as kavling_id', DB::raw('COALESCE((SELECT MAX(order_ds.total) from order_ds join products on products.id=order_ds.product_id where order_ds.order_id=rabs.order_id and products.kavling_id=rabs.kavling_id), 0) as amount_kontrak'))
                    ->join('rabs', 'rabs.id', 'project_req_developments.rab_id')
                    ->join('kavlings', 'kavlings.id', 'rabs.kavling_id')
                    ->where('project_req_developments.id', $id)
                    ->first();
        // exit;
        // $query['data']=DB::table('project_req_developments')
        //             ->select('*', DB::raw('total * '.$project_req_dev->amount_set.' as total'), DB::raw(''.$project_req_dev->amount_set.' as total_request'), DB::raw('COALESCE((select count(inv_request_prod_ds.id) AS total from inv_requests join inv_request_prod_ds on inv_requests.id=inv_request_prod_ds.inv_request_id where inv_requests.project_req_development_id='.$id.' and inv_requests.project_work_id='.$pw_id.'), 0) AS total_used'))
        //             ->where('id', $id)
        //             ->first();
        $data=array(
            'data'  => $project_req_dev
        );
        return $data;
    }

    public function reRequest()
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
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . 'rab/base/MCity']);  
            $response = $client->request('GET', '', ['headers' => $headers]); 
            $body = $response->getBody();
            $content =$body->getContents();
            $response_array = json_decode($content,TRUE);

            $site_location = $response_array['data'];
        } catch(RequestException $exception) {
            $is_error = true;
            $error_message .= $exception->getMessage();
        }
        // $response = null;
        
        try
        {
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            if ($this->site_id == null) {
                $client1 = new Client(['base_uri' => $this->base_api_url . 'order/option']); 
            }else{
                $client1 = new Client(['base_uri' => $this->base_api_url . 'order/option?site_id='.$this->site_id]); 
            } 
            $response1 = $client1->request('GET', '', ['headers' => $headers]); 
            $body1 = $response1->getBody();
            $content1 = $body1->getContents();
            $response_array1 = json_decode($content1,TRUE);

            $response1 = $content1;  
            $order_list = $response_array1['data'];
        } catch(RequestException $exception) {
            
        }
        $warehouse=DB::table('m_warehouses')->where('site_id', $this->site_id)->get();
        $data = array(
            'error' => array(
                'is_error' => $is_error,
                'error_message' => $error_message
            ),
            'site_locations' => $site_location, 
            'sites' => $all_sites,
            'projects' => $all_projects,
            'order_list' => $order_list,
            'warehouse' => $warehouse,
            'site_id' => $this->site_id
        );
        // return view('pages.rab.rab.rab_form_add', $data);
        
        return view('pages.inv.pengambilan_barang.re_request_barang', $data);
    }
    public function reRequestPost(Request $request)
    {
        $rab_id = $request->post('rab_no');
        $req_no = $request->post('req_no');
        $m_item_id = $request->post('m_item_id');
        $qty_rab = $request->post('qty_rab');
        $qty_sisa_rab = $request->post('qty_sisa_rab');
        $qty = $request->post('qty');
        $detail_note = $request->post('note');
        $m_unit_id = $request->post('m_unit_id');
        $alasan = $request->post('alasan'); // untuk permintaan khusus
        $m_warehouse_id = $request->post('m_warehouse_id');
        
        $permintaan_normal = array();
        $permintaan_khusus = array();
        for($i = 0; $i < count($m_item_id); $i++) {
            if ($qty[$i] != 0) {
                if($qty_sisa_rab[$i] >= $qty[$i]) { // request normal
                    array_push($permintaan_normal, 
                        array(
                            'm_item_id' => $m_item_id[$i],
                            'qty_rab' => $qty_rab[$i],
                            'qty_sisa_rab' => $qty_sisa_rab[$i],
                            'qty' => $qty[$i],
                            'detail_note'  => $detail_note[$i],
                            'm_unit_id' => $m_unit_id[$i]
                        ));
                } else { // request khusus
                    if($qty_sisa_rab[$i] == 0) {
                        array_push($permintaan_khusus, 
                            array(
                                'm_item_id' => $m_item_id[$i],
                                'qty_rab' => $qty_rab[$i],
                                'qty_sisa_rab' => $qty_sisa_rab[$i],
                                'qty' => $qty[$i],
                                'detail_note'  => $detail_note[$i],
                                'm_unit_id' => $m_unit_id[$i]
                            ));
                    } else {
                        array_push($permintaan_normal, 
                        array(
                            'm_item_id' => $m_item_id[$i],
                            'qty_rab' => $qty_rab[$i],
                            'qty_sisa_rab' => $qty_sisa_rab[$i],
                            'qty' => $qty_sisa_rab[$i],
                            'detail_note'  => $detail_note[$i],
                            'm_unit_id' => $m_unit_id[$i]
                        ));
    
                        array_push($permintaan_khusus, 
                            array(
                                'm_item_id' => $m_item_id[$i],
                                'qty_rab' => $qty_rab[$i],
                                'qty_sisa_rab' => $qty_sisa_rab[$i],
                                'qty' => $qty[$i] - $qty_sisa_rab[$i],
                                'detail_note'  => $detail_note[$i],
                                'm_unit_id' => $m_unit_id[$i]
                            ));
                    }
                }
            }
        }
        $inv_requests=DB::table('inv_requests')->where('id', $req_no)->first();

        $period_year = Carbon::now()->year;
        $period_month = Carbon::now()->month;
        $rabcon = new RabController();
        $memo_no = $rabcon->generateTransactionNo('MREQ', $period_year, $period_month, $this->site_id );
        if(count($permintaan_normal) > 0) {
            // //insert ke request
            try
            {
                $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . 'inv/base/InvRequest']);
                $reqBody = [
                'headers' => $headers,
                'json' => [
                        'req_type' => 'REQ_ITEM',
                        'rab_id' => $inv_requests->rab_id,
                        'inv_request_id' => $req_no,
                        'no' => $memo_no,
                        'project_req_development_id'    => $inv_requests->project_req_development_id,
                        // 'project_work_id'    => $inv_requests->project_work_id,
                        'site_id' => $this->site_id
                    ]
                ];
                $response = $client->request('POST', '', $reqBody); 
                $body = $response->getBody();
                $content = $body->getContents();
                $response_array = json_decode($content,TRUE);
                $inv_req = $response_array['data'];
            } catch(RequestException $exception) {
            }

            for($i = 0; $i < count($permintaan_normal); $i++) {
                //insert detail
                try
                {
                    $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . '/inv/base/InvRequestD']);
                    $reqBody = [
                    'headers' => $headers,
                'json' => [
                            'inv_request_id' => $inv_req['id'],
                            'm_item_id' => $permintaan_normal[$i]['m_item_id'],
                            'amount' => $permintaan_normal[$i]['qty'],
                            'detail_notes' => $permintaan_normal[$i]['detail_note'],
                            'm_unit_id' => $permintaan_normal[$i]['m_unit_id'],
                            'm_warehouse_id' => $m_warehouse_id
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

        // permintaan khusus
        if(count($permintaan_khusus) > 0) {
            //insert ke special
            try
            {
                $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . '/inv/base/InvRequest']);
                $reqBody = [
                'headers' => $headers,
                'json' => [
                        'req_type' => 'SPECIAL',
                        'rab_id' => $inv_requests->rab_id,
                        'inv_request_id' => $req_no,
                        'no' => $memo_no,
                        'project_req_development_id'    => $inv_requests->project_req_development_id,
                        // 'project_work_id'    => $inv_requests->project_work_id,
                        'site_id' => $this->site_id
                    ]
                ]; 
                $response = $client->request('POST', '', $reqBody); 
                $body = $response->getBody();
                $content = $body->getContents();
                $response_array = json_decode($content,TRUE);
                $inv_spc_req = $response_array['data'];
            } catch(RequestException $exception) {
            }

            for($i = 0; $i < count($permintaan_khusus); $i++) {
                //insert detail
                try
                {
                    $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . '/inv/base/InvRequestD']);
                    $reqBody = [
                    'headers' => $headers,
                'json' => [
                            'inv_request_id' => $inv_spc_req['id'],
                            'm_item_id' => $permintaan_khusus[$i]['m_item_id'],
                            'amount' => $permintaan_khusus[$i]['qty'],
                            'm_unit_id' => $permintaan_khusus[$i]['m_unit_id'],
                            'detail_notes' => $permintaan_khusus[$i]['detail_note'],
                            'notes' => $alasan[$i],
                            'm_warehouse_id' => $m_warehouse_id
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

        $notification = array(
            'message' => 'Success Create New Request',
            'alert-type' => 'success'
        );

        return redirect('material_request')->with($notification);
    }
    
    public function getInvReqByRab($id){
        $query['data']=DB::table('rabs')
                    ->select('inv_requests.*')
                    ->join('inv_requests', 'inv_requests.rab_id', '=', 'rabs.id')
                    ->where('rabs.id', $id)
                    ->whereNull('inv_request_id')
                    ->get();
        return $query;
    }
    public function indexMaterialSupport()
    {
        $is_error = false;
        $error_message = '';
        $data = array(
            'error' => array(
                'is_error' => $is_error,
                'error_message' => $error_message
            )
        );
        
        return view('pages.inv.pengambilan_barang.pengambilan_material_penunjang_list', $data);
    }
    public function requestSupport()
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
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . 'rab/base/MCity']);  
            $response = $client->request('GET', '', ['headers' => $headers]); 
            $body = $response->getBody();
            $content =$body->getContents();
            $response_array = json_decode($content,TRUE);

            $site_location = $response_array['data'];
        } catch(RequestException $exception) {
            $is_error = true;
            $error_message .= $exception->getMessage();
        }
        // $response = null;
        
        try
        {
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            if ($this->site_id == null) {
                $client1 = new Client(['base_uri' => $this->base_api_url . 'order/option_install_order']); 
            }else{
                $client1 = new Client(['base_uri' => $this->base_api_url . 'order/option_install_order?site_id='.$this->site_id]); 
            } 
            $response1 = $client1->request('GET', '', ['headers' => $headers]); 
            $body1 = $response1->getBody();
            $content1 = $body1->getContents();
            $response_array1 = json_decode($content1,TRUE);

            $response1 = $content1;  
            $order_list = $response_array1['data'];
        } catch(RequestException $exception) {
            
        }
        
        $warehouse=DB::table('m_warehouses')->where('site_id', $this->site_id)->get();
        $data = array(
            'error' => array(
                'is_error' => $is_error,
                'error_message' => $error_message
            ),
            'site_locations' => $site_location, 
            'sites' => $all_sites,
            'projects' => $all_projects,
            'order_list' => $order_list,
            'warehouse' => $warehouse,
            'site_id' => $this->site_id
        );
        // return view('pages.rab.rab.rab_form_add', $data);
        
        return view('pages.inv.pengambilan_barang.pengambilan_material_penunjang_request', $data);
    }
    // public function saveRequestSupport(Request $request)
    // {
    //     $rab_id = $request->post('rab_no');
    //     $request_id = $request->post('request_id');
    //     $m_item_id = $request->post('m_item_id');
    //     $amount = $request->post('amount');
    //     $order_id = $request->post('order_id');
    //     $m_unit_id = $request->post('m_unit_id');
    //     $m_warehouse_id = $request->post('m_warehouse_id');
    //     $type_stok = $request->post('type_stok');
        
    //     if ($m_item_id == null) {
    //         return redirect(request()->headers->get('referer'));
    //     }
    //     if(count($m_item_id) > 0) {
    //         $period_year = Carbon::now()->year;
    //         $period_month = Carbon::now()->month;
    //         $rabcon = new RabController();
    //         $memo_no = $rabcon->generateTransactionNo('MREQ', $period_year, $period_month, $this->site_id );
    //         try
    //         {
    //             $headers = [
    //             'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
    //             'Accept'        => 'application/json',
    //         ];
    //         $client = new Client(['base_uri' => $this->base_api_url . 'inv/base/InvRequest']);
    // //             $reqBody = [
    // //             'headers' => $headers,
    //             'json' => [
    //                     'req_type' => 'REQ_ITEM_SP',
    //                     'rab_id' => $rab_id,
    //                     'project_req_development_id' => $request_id,
    //                     'no' => $memo_no, 
    //                     'site_id' => $this->site_id
    //                 ]
    //             ];
    //             $response = $client->request('POST', '', $reqBody); 
    //             $body = $response->getBody();
    //             $content = $body->getContents();
    //             $response_array = json_decode($content,TRUE);
    //             $inv_req = $response_array['data'];
    //         } catch(RequestException $exception) {
    //         }

    //         for($i = 0; $i < count($m_item_id); $i++){
    //             if($amount[$i] > 0) {
    //             //insert detail
    //                 try
    //                 {
    //                     $headers = [
    //             'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
    //             'Accept'        => 'application/json',
    //         ];
    //         $client = new Client(['base_uri' => $this->base_api_url . '/inv/base/InvRequestD']);
    // //                     $reqBody = [
    // //                     'headers' => $headers,
    //             'json' => [
    //                             'inv_request_id' => $inv_req['id'],
    //                             'detail_notes' => 'Material Penunjang',
    //                             'm_item_id' => $m_item_id[$i],
    //                             'amount' => $amount[$i],
    //                             'm_unit_id' => $m_unit_id[$i],
    //                             'm_warehouse_id' => $m_warehouse_id[$i]
    //                         ]
    //                     ]; 
    //                     $response = $client->request('POST', '', $reqBody); 
    //                     $body = $response->getBody();
    //                     $content = $body->getContents();
    //                     $response_array = json_decode($content,TRUE);
    //                 } catch(RequestException $exception) {
    //                 }
    //             }
    //         }

    //         $period_year = Carbon::now()->year;
    //         $period_month = Carbon::now()->month;
    //         $rabcon = new RabController();
    //         $inv_no = $rabcon->generateTransactionNo('MREQ', $period_year, $period_month, $this->site_id );
            
    //         try
    //         {
    //             $headers = [
    //             'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
    //             'Accept'        => 'application/json',
    //         ];
    //         $client = new Client(['base_uri' => $this->base_api_url . 'inv/base/InvTrx']);
    // //             $reqBody = [
    // //                 'headers' => $headers,
    //             'json' => [
    //                     'm_warehouse_id' => 1,
    //                     'inv_request_id' => $inv_req['id'],
    //                     'trx_type' => 'REQ_ITEM_SP',
    //                     'no' => $inv_no,
    //                     'inv_trx_date' => Carbon::now()->toDateString(),
    //                     'site_id' => $this->site_id,
    //                     'is_entry' => false
    //                     ]
    //                 ]; 
                 
    //                 $response = $client->request('POST', '', $reqBody); 
    //                 $body = $response->getBody();
    //                 $content = $body->getContents();
    //                 $response_array = json_decode($content,TRUE);
    //                 $inv_trx = $response_array['data'];

    //         } catch(RequestException $exception) {
    //         }
            
    //         //insert inv_trx_d
    //         for($i = 0; $i < count($m_item_id); $i++){
    //             if($amount[$i] > 0) {
    //                 $get_save_price=DB::table('m_item_prices')->where(['m_item_id' => $m_item_id[$i], 'm_unit_id' => $m_unit_id[$i], 'site_id' => $this->site_id])->first();
    //                 try
    //                 {
    //                     $headers = [
    //             'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
    //             'Accept'        => 'application/json',
    //         ];
    //         $client = new Client(['base_uri' => $this->base_api_url . '/inv/base/InvTrxD']);
    // //                     $reqBody = [
    // //                         'headers' => $headers,
    //             'json' => [
    //                             'inv_trx_id' => $inv_trx['id'],
    //                             'm_item_id' => $m_item_id[$i],
    //                             'amount' => $amount[$i],
    //                             'm_unit_id' => $m_unit_id[$i],
    //                             'm_warehouse_id' => $m_warehouse_id[$i],
    //                             'base_price'    => $type_stok[$i] != 'TRF_STK' ? $get_save_price->price : 0
    //                             ]
    //                         ]; 
    //                         $response = $client->request('POST', '', $reqBody); 
    //                         $body = $response->getBody();
    //                         $content = $body->getContents();
    //                         $response_array = json_decode($content,TRUE);
    //                 } catch(RequestException $exception) {
    //                 }

    //                 $item=DB::table('m_items')->where('id', $m_item_id[$i])->first();
                    
    //                 if ($item->category == 'MATERIAL') {
    //                     if ($type_stok[$i] != 'TRF_STK') {
    //                         $input_jurnal=array(
    //                             'inv_trx_id' => $inv_trx['id'],
    //                             'total' => ($amount[$i] * $get_save_price->price),
    //                             'user_id'   => $this->user_id,
    //                             'deskripsi'     => 'Pengeluaran Material Untuk Pasang dari No '.$inv_no,
    //                             'tgl'       => date('Y-m-d'),
    //                             'type'      => 'material',
    //                             'project_req_development_id' => $request_id,
    //                             'm_warehouse_id' => $m_warehouse_id[$i],
    //                             'inv_request_id' => $inv_req['id'],
    //                             'location_id'   => $this->site_id
    //                         );
    //                         $this->journalPengeluaran($input_jurnal);
    //                     }
    //                 }else{
    //                     if ($type_stok[$i] != 'TRF_STK') {
    //                         $input_jurnal=array(
    //                             'inv_trx_id' => $inv_trx['id'],
    //                             'total' => ($amount[$i] * $get_save_price->price),
    //                             'user_id'   => $this->user_id,
    //                             'deskripsi'     => 'Penerimaan Spare Part Untuk Pasang dari No '.$inv_no,
    //                             'tgl'       => date('Y-m-d'),
    //                             'type'      => 'spare part',
    //                             'project_req_development_id' => $request_id,
    //                             'm_warehouse_id' => $m_warehouse_id[$i],
    //                             'inv_request_id' => $inv_req['id'],
    //                             'location_id'   => $this->site_id
    //                         );
    //                         $this->journalPengeluaran($input_jurnal);
    //                     }
    //                 }

    //                 $cek_stok=DB::table('stocks')
    //                             ->where('m_warehouse_id', $m_warehouse_id[$i])
    //                             ->where('site_id', $this->site_id)
    //                             ->where('m_item_id', $m_item_id[$i])
    //                             ->where('m_unit_id', $m_unit_id[$i])
    //                             ->where('type', $type_stok)
    //                             ->first();
    //                 if ($cek_stok == null) {
    //                     try
    //                     {
    //                         $headers = [
    //             'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
    //             'Accept'        => 'application/json',
    //         ];
    //         $client = new Client(['base_uri' => $this->base_api_url . '/inv/base/Stock']);
    // //                         $reqBody = [
    // //                             'headers' => $headers,
    //             'json' => [
    //                                 'site_id' => $this->site_id,
    //                                 'm_item_id' => $m_item_id[$i],
    //                                 'amount' => $amount[$i],
    //                                 'amount_in' => 0,
    //                                 'amount_out' => $amount[$i],
    //                                 'm_unit_id' => $m_unit_id[$i],
    //                                 'm_warehouse_id' => $m_warehouse_id[$i],
    //                                 'type'  => $type_stok
    //                                 ]
    //                             ]; 
    //                             $response = $client->request('POST', '', $reqBody); 
    //                             $body = $response->getBody();
    //                             $content = $body->getContents();
    //                             $response_array = json_decode($content,TRUE);
    //                     } catch(RequestException $exception) {
    //                     }
    //                 }else{
    //                     try
    //                     {
    //                         $headers = [
    //             'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
    //             'Accept'        => 'application/json',
    //         ];
    //         $client = new Client(['base_uri' => $this->base_api_url . '/inv/base/Stock/'.$cek_stok->id]);
    // //                         $reqBody = [
    // //                             'headers' => $headers,
    //             'json' => [
    //                                 'amount' => $cek_stok->amount - $amount[$i],
    //                                 'amount_out' => $cek_stok->amount_out + $amount[$i]
    //                                 ]
    //                             ]; 
    //                             $response = $client->request('PUT', '', $reqBody); 
    //                             $body = $response->getBody();
    //                             $content = $body->getContents();
    //                             $response_array = json_decode($content,TRUE);
    //                     } catch(RequestException $exception) {
    //                     }
    //                 }
    //             }
    //         }
    //     }
        
    //     $notification = array(
    //         'message' => 'Success Create New Request',
    //         'alert-type' => 'success'
    //     );

    //     return redirect('material_request/material_support')->with($notification);
    // }
    public function saveRequestSupport(Request $request)
    {
        
        $install_order_id = $request->post('install_order_id');
        $prod_sub_id = $request->post('prod_sub_id');
        $m_item_id = $request->post('m_item_id');
        $amount = $request->post('amount');
        // $order_id = $request->post('order_id');
        $m_unit_id = $request->post('m_unit_id');
        $m_warehouse_id = $request->post('m_warehouse_id');
        $type_stok = $request->post('type_stok');
        
        if ($m_item_id == null) {
            return redirect(request()->headers->get('referer'));
        }
        if(count($m_item_id) > 0) {
            $period_year = Carbon::now()->year;
            $period_month = Carbon::now()->month;
            $rabcon = new RabController();
            $memo_no = $rabcon->generateTransactionNo('MREQ', $period_year, $period_month, $this->site_id );
            try
            {
                $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . 'inv/base/InvRequest']);
                $reqBody = [
                'headers' => $headers,
                'json' => [
                        'req_type' => 'REQ_ITEM_SP',
                        'install_order_id' => $install_order_id,
                        'no' => $memo_no, 
                        'site_id' => $this->site_id
                    ]
                ];
                $response = $client->request('POST', '', $reqBody); 
                $body = $response->getBody();
                $content = $body->getContents();
                $response_array = json_decode($content,TRUE);
                $inv_req = $response_array['data'];
            } catch(RequestException $exception) {
            }

            for($i = 0; $i < count($m_item_id); $i++){
                if($amount[$i] > 0) {
                //insert detail
                    try
                    {
                        $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . '/inv/base/InvRequestD']);
                        $reqBody = [
                        'headers' => $headers,
                'json' => [
                                'inv_request_id' => $inv_req['id'],
                                'detail_notes' => 'Material Penunjang',
                                'm_item_id' => $m_item_id[$i],
                                'amount' => $amount[$i],
                                'm_unit_id' => $m_unit_id[$i],
                                'm_warehouse_id' => $m_warehouse_id[$i]
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

            $period_year = Carbon::now()->year;
            $period_month = Carbon::now()->month;
            $rabcon = new RabController();
            $inv_no = $rabcon->generateTransactionNo('MREQ', $period_year, $period_month, $this->site_id );
            
            try
            {
                $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . 'inv/base/InvTrx']);
                $reqBody = [
                    'headers' => $headers,
                'json' => [
                        'm_warehouse_id' => 1,
                        'inv_request_id' => $inv_req['id'],
                        'trx_type' => 'REQ_ITEM_SP',
                        'no' => $inv_no,
                        'inv_trx_date' => Carbon::now()->toDateString(),
                        'site_id' => $this->site_id,
                        'is_entry' => false
                        ]
                    ]; 
                 
                    $response = $client->request('POST', '', $reqBody); 
                    $body = $response->getBody();
                    $content = $body->getContents();
                    $response_array = json_decode($content,TRUE);
                    $inv_trx = $response_array['data'];

            } catch(RequestException $exception) {
            }
            for($i = 0; $i < count($prod_sub_id); $i++) {
                //insert detail
                try
                {
                    $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . '/inv/base/InvRequestProdInstall']);
                    $reqBody = [
                    'headers' => $headers,
                'json' => [
                            'inv_request_id' => $inv_req['id'],
                            'product_sub_id' => $prod_sub_id[$i],
                            'install_order_id' => $install_order_id,
                        ]
                    ]; 
                    $response = $client->request('POST', '', $reqBody); 
                    $body = $response->getBody();
                    $content = $body->getContents();
                    $response_array = json_decode($content,TRUE);
                } catch(RequestException $exception) {
                }
            }

            //insert inv_trx_d
            for($i = 0; $i < count($m_item_id); $i++){
                if($amount[$i] > 0) {
                    $get_save_price=DB::table('m_item_prices')->where(['m_item_id' => $m_item_id[$i], 'm_unit_id' => $m_unit_id[$i], 'site_id' => $this->site_id])->first();
                    try
                    {
                        $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . '/inv/base/InvTrxD']);
                        $reqBody = [
                            'headers' => $headers,
                'json' => [
                                'inv_trx_id' => $inv_trx['id'],
                                'm_item_id' => $m_item_id[$i],
                                'amount' => $amount[$i],
                                'm_unit_id' => $m_unit_id[$i],
                                'm_warehouse_id' => $m_warehouse_id[$i],
                                'base_price'    => $type_stok[$i] != 'TRF_STK' ? $get_save_price->price : 0
                                ]
                            ]; 
                            $response = $client->request('POST', '', $reqBody); 
                            $body = $response->getBody();
                            $content = $body->getContents();
                            $response_array = json_decode($content,TRUE);
                    } catch(RequestException $exception) {
                    }

                    $item=DB::table('m_items')->where('id', $m_item_id[$i])->first();
                    
                    if ($item->category == 'MATERIAL') {
                        if ($type_stok[$i] != 'TRF_STK') {
                            $input_jurnal=array(
                                'inv_trx_id' => $inv_trx['id'],
                                'total' => ($amount[$i] * $get_save_price->price),
                                'user_id'   => $this->user_id,
                                'deskripsi'     => 'Pengeluaran Material Untuk Pasang dari No '.$inv_no,
                                'tgl'       => date('Y-m-d'),
                                'type'      => 'material',
                                'project_req_development_id' => 0,
                                'm_warehouse_id' => $m_warehouse_id[$i],
                                'inv_request_id' => $inv_req['id'],
                                'install_order_id' => $install_order_id,
                                'm_item_id' => $m_item_id[$i],
                                'location_id'   => $this->site_id
                            );
                            $this->journalPengeluaranSP($input_jurnal);
                        }
                    }else{
                        if ($type_stok[$i] != 'TRF_STK') {
                            $input_jurnal=array(
                                'inv_trx_id' => $inv_trx['id'],
                                'total' => ($amount[$i] * $get_save_price->price),
                                'user_id'   => $this->user_id,
                                'deskripsi'     => 'Pengeluaran Spare Part Untuk Pasang dari No '.$inv_no,
                                'tgl'       => date('Y-m-d'),
                                'type'      => 'spare part',
                                'project_req_development_id' => 0,
                                'm_warehouse_id' => $m_warehouse_id[$i],
                                'inv_request_id' => $inv_req['id'],
                                'install_order_id' => $install_order_id,
                                'm_item_id' => $m_item_id[$i],
                                'location_id'   => $this->site_id
                            );
                            $this->journalPengeluaranSP($input_jurnal);
                        }
                    }

                    $cek_stok=DB::table('stocks')
                                ->where('m_warehouse_id', $m_warehouse_id[$i])
                                ->where('site_id', $this->site_id)
                                ->where('m_item_id', $m_item_id[$i])
                                ->where('m_unit_id', $m_unit_id[$i])
                                ->where('type', $type_stok[$i])
                                ->first();
                    if ($cek_stok == null) {
                        try
                        {
                            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . '/inv/base/Stock']);
                            $reqBody = [
                                'headers' => $headers,
                'json' => [
                                    'site_id' => $this->site_id,
                                    'm_item_id' => $m_item_id[$i],
                                    'amount' => $amount[$i],
                                    'amount_in' => 0,
                                    'amount_out' => $amount[$i],
                                    'm_unit_id' => $m_unit_id[$i],
                                    'm_warehouse_id' => $m_warehouse_id[$i],
                                    'type'  => $type_stok[$i]
                                    ]
                                ]; 
                                $response = $client->request('POST', '', $reqBody); 
                                $body = $response->getBody();
                                $content = $body->getContents();
                                $response_array = json_decode($content,TRUE);
                        } catch(RequestException $exception) {
                        }
                    }else{
                        try
                        {
                            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . '/inv/base/Stock/'.$cek_stok->id]);
                            $reqBody = [
                                'headers' => $headers,
                'json' => [
                                    'amount' => $cek_stok->amount - $amount[$i],
                                    'amount_out' => $cek_stok->amount_out + $amount[$i]
                                    ]
                                ]; 
                                $response = $client->request('PUT', '', $reqBody); 
                                $body = $response->getBody();
                                $content = $body->getContents();
                                $response_array = json_decode($content,TRUE);
                        } catch(RequestException $exception) {
                        }
                    }
                }
            }
        }
        
        $notification = array(
            'message' => 'Success Create New Request',
            'alert-type' => 'success'
        );

        return redirect('material_request/material_support')->with($notification);
    }
    public function getListPengambilanSP(){
        $response = null;
        try
        {
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . '/inv/request_material_penunjang']);  
            $response = $client->request('GET', '', ['headers' => $headers]); 
            $body = $response->getBody();
            $content = $body->getContents();
            $response_array = json_decode($content,TRUE);

            $response = $content;         

            $data=DataTables::of($response_array['data'])
                                    ->make(true);        
        } catch(RequestException $exception) {
            
        }    

        return $data;
    }
    private function journalPengeluaran($data){
        $sum = array_sum(array_map(function($item) { 
            return $item['total']; 
        }, $data['data']));
        if($sum > 0) {
            $project_req_developments=DB::table('project_req_developments')->where('id', $data['project_req_development_id'])->first();
            $account_project=DB::table('account_projects')->where('order_id', $project_req_developments->order_id)->first();
            // $id_akun=($data['type'] == 'material' ?  ($data['m_warehouse_id'] == 2 ? 141 : 142) : ($data['m_warehouse_id'] == 2 ? 143 : 144));
            $data_trx=array(
                'deskripsi'     => $data['deskripsi'],
                'location_id'     => $data['location_id'],
                'tanggal'       => $data['tgl'],
                'inv_trx_id'   => $data['inv_trx_id'],
                'inv_request_id'   => $data['inv_request_id'],
                'user_id'       => $data['user_id'],
                'project_req_development_id'   => $data['project_req_development_id']
            );
            $insert=DB::table('tbl_trx_akuntansi')->insert($data_trx);
            if ($insert) {
                $id_last=DB::table('tbl_trx_akuntansi')->max('id_trx_akun');
                // $akun=array(
                //     'id_trx_akun'   => $id_last,
                //     'id_akun'       => ($data['type'] == 'material' ? $account_project->cost_material_id : $account_project->cost_spare_part_id),
                //     'jumlah'        => $data['total'],
                //     'tipe'          => "DEBIT",
                //     'keterangan'    => 'lawan',                
                // );
                // DB::table('tbl_trx_akuntansi_detail')->insert($akun);
                foreach ($data['data'] as $value) {
                    if($value['total'] > 0){
                        $id_akun=($value['type'] == 'material' ?  ($value['m_warehouse_id'] == 2 ? 141 : 142) : ($value['m_warehouse_id'] == 2 ? 143 : 144));
                        $akun=array(
                            'id_trx_akun'   => $id_last,
                            'id_akun'       => ($value['type'] == 'material' ? $account_project->cost_material_id : $account_project->cost_spare_part_id),
                            'jumlah'        => $value['total'],
                            'tipe'          => "DEBIT",
                            'keterangan'    => 'lawan',
                        );
                        DB::table('tbl_trx_akuntansi_detail')->insert($akun);
        
                        $akun=array(
                            'id_trx_akun'   => $id_last,
                            'id_akun'       => $id_akun,
                            'jumlah'        => $value['total'],
                            'tipe'          => "KREDIT",
                            'keterangan'    => 'akun',
                            'm_item_id' => $value['m_item_id'],
                        );
                        DB::table('tbl_trx_akuntansi_detail')->insert($akun);
                    }
                }
                // $lawan=array(
                //     'id_trx_akun'   => $id_last,
                //     'id_akun'       => $id_akun,
                //     'jumlah'        => $data['total'],
                //     'tipe'          => "KREDIT",
                //     'keterangan'    => 'akun',
                // );
                // DB::table('tbl_trx_akuntansi_detail')->insert($lawan);
            }
        }
    }
    private function journalPengembalian($data){
        $project_req_developments=DB::table('project_req_developments')->where('id', $data['project_req_development_id'])->first();
        $account_project=DB::table('account_projects')->where('order_id', $project_req_developments->order_id)->first();

        $id_akun=($data['type'] == 'material' ?  ($data['m_warehouse_id'] == 2 ? 141 : 142) : ($data['m_warehouse_id'] == 2 ? 143 : 144));
        $data_trx=array(
            'deskripsi'     => $data['deskripsi'],
            'location_id'     => $data['location_id'],
            'tanggal'       => $data['tgl'],
            'inv_trx_id'   => $data['inv_trx_id'],
            'inv_request_id'   => $data['inv_request_id'],
            'user_id'       => $data['user_id'],
            'project_req_development_id'   => $data['project_req_development_id']
        );
        $insert=DB::table('tbl_trx_akuntansi')->insert($data_trx);
        if ($insert) {
            $id_last=DB::table('tbl_trx_akuntansi')->max('id_trx_akun');
            $akun=array(
                'id_trx_akun'   => $id_last,
                'id_akun'       => $id_akun,
                'jumlah'        => $data['total'],
                'tipe'          => "DEBIT",
                'keterangan'    => 'lawan',
            );
            DB::table('tbl_trx_akuntansi_detail')->insert($akun);
            $lawan=array(
                'id_trx_akun'   => $id_last,
                'id_akun'       => ($data['type'] == 'material' ? $account_project->cost_material_id : $account_project->cost_spare_part_id),
                'jumlah'        => $data['total'],
                'tipe'          => "KREDIT",
                'keterangan'    => 'akun',
            );
            DB::table('tbl_trx_akuntansi_detail')->insert($lawan);
        }
    }
    private function journalPengeluaranSP($data){
        $id_akun=($data['type'] == 'material' ?  ($data['m_warehouse_id'] == 2 ? 141 : 142) : ($data['m_warehouse_id'] == 2 ? 143 : 144));
        $data_trx=array(
            'deskripsi'     => $data['deskripsi'],
            'location_id'     => $data['location_id'],
            'tanggal'       => $data['tgl'],
            'inv_trx_id'   => $data['inv_trx_id'],
            'inv_request_id'   => $data['inv_request_id'],
            'user_id'       => $data['user_id'],
            'project_req_development_id'   => $data['project_req_development_id'],
            'install_order_id' => $data['install_order_id'],
        );
        $insert=DB::table('tbl_trx_akuntansi')->insert($data_trx);
        if ($insert) {
            $id_last=DB::table('tbl_trx_akuntansi')->max('id_trx_akun');
            $akun=array(
                'id_trx_akun'   => $id_last,
                'id_akun'       => 169,
                'jumlah'        => $data['total'],
                'tipe'          => "DEBIT",
                'keterangan'    => 'lawan',                
            );
            DB::table('tbl_trx_akuntansi_detail')->insert($akun);
            $lawan=array(
                'id_trx_akun'   => $id_last,
                'id_akun'       => $id_akun,
                'jumlah'        => $data['total'],
                'tipe'          => "KREDIT",
                'keterangan'    => 'akun',
                'm_item_id'     => $data['m_item_id']
            );
            DB::table('tbl_trx_akuntansi_detail')->insert($lawan);
        }
    }
    public function printLabel($id){
        $cek=DB::table('inv_requests')
                ->where('inv_requests.id', $id)
                ->first();
        $query=DB::table('inv_requests')
                    ->select('product_subs.*')
                    ->join('inv_request_prod_ds', 'inv_request_prod_ds.inv_request_id', 'inv_requests.id')
                    ->join('product_subs', 'inv_request_prod_ds.product_sub_id', 'product_subs.id')
                    ->where('inv_requests.id', ($cek->inv_request_id == null ? $id : $cek->inv_request_id))
                    ->get();
        $data=array(
            'data'  => $query
        );
        return view('pages.inv.pengambilan_barang.print_label', $data);
    }
    public function getInvRequest($id){
        $query=DB::table('inv_requests')
                ->where('id', $id)
                ->first();
        $data=array(
            'data'  => $query
        );
        return $data;
    }
    public function getProductSubByKavling($req_id, $rab_id, $limit){
        $rab=DB::table('rabs')
                        ->where('id', $rab_id)
                        ->first();
        $order_d=DB::table('project_req_developments as prd')
                        ->join('order_ds', 'order_ds.order_id', 'prd.order_id')
                        ->join('products', 'order_ds.product_id', 'products.id')
                        ->select('products.*')
                        ->where('products.kavling_id', $rab->kavling_id)
                        ->where('prd.id', $req_id)
                        ->get();
        foreach ($order_d as $key => $value) {
            $product_sub_used=DB::table('product_subs')
                            ->join('inv_request_prod_ds as ird', 'ird.product_sub_id', 'product_subs.id')
                            ->where('product_subs.product_id', $value->id)
                            ->where('ird.project_req_development_id', $req_id)
                            ->count();
            $limit_total=($value->amount_set * $limit) - $product_sub_used;
            $product_sub=DB::table('product_subs')
                            ->where('product_id', $value->id)
                            ->whereRaw('product_subs.id not in (select product_sub_id from inv_request_prod_ds)')
                            ->orderBy('product_subs.id')
                            ->limit($limit_total)
                            ->get();
            $value->prod_sub=$product_sub;
        }
        $label=array();
        foreach ($order_d as $key => $value) {
            foreach ($value->prod_sub as $k) {
                array_push($label, $k);
            }
        }
        $data=array(
            'data'  => $label
        );
        return $data;
    }
    public function getLabelInstallOrder($id){
        $used_label=DB::table('inv_request_prod_installs as irp')
                    ->join('product_subs', 'product_subs.id', 'irp.product_sub_id')
                    ->where('irp.install_order_id', $id)
                    ->pluck('irp.product_sub_id');

        $query=DB::table('install_order_ds')
                    ->join('product_subs', 'product_subs.order_d_id', 'install_order_ds.order_d_id')
                    ->where('install_order_ds.install_order_id', $id)
                    ->whereNotIn('product_subs.id', $used_label)
                    ->select('product_subs.*')
                    ->orderBy('product_subs.id')
                    ->get();

        $data=array(
            'data'  => $query
        );
        return $data;
    }
    public function getInvRequestByLabel(Request $request){
        $no=$request->no;
        $query=DB::table('inv_request_prod_ds as irp')
                    ->join('inv_requests as ir', 'ir.id', 'irp.inv_request_id')
                    ->join('product_subs', 'product_subs.id', 'irp.product_sub_id')
                    ->where('product_subs.no', 'ilike', '%'.$no.'%')
                    ->whereNull('ir.inv_request_id')
                    ->select('ir.id', DB::raw('MAX(ir.no) as no'))
                    ->groupBy('ir.id')
                    ->get();

        // $query=DB::table('install_order_ds')
        //             ->join('product_subs', 'product_subs.order_d_id', 'install_order_ds.order_d_id')
        //             ->where('install_order_ds.install_order_id', $id)
        //             ->whereNotIn('product_subs.id', $used_label)
        //             ->select('product_subs.*')
        //             ->orderBy('product_subs.id')
        //             ->get();

        $data=array(
            'data'  => $query
        );
        return $data;
    }

    public function laporanPengeluaranMaterial(Request $request)
    {
        $data['warehouse'] = DB::table('m_warehouses')->where('site_id', $this->site_id)->get();

        $data['allSpkNumber'] = DB::table('orders')->select('spk_number')->where('spk_number', '!=', null)->distinct()->get();
        $data['date'] = date('Y-m-d');
        $data['date2'] = date('Y-m-d');
        $data['no_spk'] = '';
        $data['warehouse_id'] = 'all';
        if ($request->get('date') && $request->get('date2')) {
            $getLaporan = DB::table('inv_trxes as trx')
                            ->select('trx.*', 'o.spk_number', 'i.no as no_material', 'i.name as nama_material', 'trx_ds.amount', 'trx_ds.base_price')
                            ->join('inv_trx_ds as trx_ds', 'trx.id', 'trx_ds.inv_trx_id')
                            ->join('m_items as i', 'i.id', 'trx_ds.m_item_id')
                            ->leftJoin('inv_requests as req', 'req.id', 'trx.inv_request_id')
                            ->leftJoin('rabs as rab', 'rab.id', 'req.rab_id')
                            ->leftJoin('orders as o', 'o.id', 'rab.order_id')
                            ->whereBetween('inv_trx_date', [$request->get('date'), $request->get('date2')])
                            ->whereIn('trx_type', ['REQ_ITEM', 'STK_ADJ'])
                            ->where('trx.no', 'LIKE', 'SBY-OUT%')
                            ->orderBy('trx.inv_trx_date', 'ASC');
        }

        if ($request->get('no_spk')) {
            $getLaporan->where('o.spk_number', $request->get('no_spk'));
            $data['no_spk'] = $request->get('no_spk');
        }

        if($request->get('warehouse_id')){
            if($request->get('warehouse_id') != 'all')
                $getLaporan->where('trx_ds.m_warehouse_id', $request->get('warehouse_id'));
            
            $data['warehouse_id'] = $request->get('warehouse_id');
        }

        if ($request->get('date') && $request->get('date2')) {
            $data['laporan'] = $getLaporan->get();

            $data['date'] = $request->get('date');
            $data['date2'] = $request->get('date2');
        }
        
        return view('pages.inv.pengambilan_barang.laporan_pengeluaran_barang', $data);
    }
    
    public function exportLaporanPengeluaranMaterial(Request $request)
    {
        $data['warehouse'] = DB::table('m_warehouses')->where('site_id', $this->site_id)->get();

        // $getLaporan = '';

        if ($request->get('date') && $request->get('date2')) {
            $getLaporan = DB::table('inv_trxes as trx')
                            ->select('trx.*', 'o.spk_number', 'i.no as no_material', 'i.name as nama_material', 'trx_ds.amount', 'trx_ds.base_price')
                            ->join('inv_trx_ds as trx_ds', 'trx.id', 'trx_ds.inv_trx_id')
                            ->join('m_items as i', 'i.id', 'trx_ds.m_item_id')
                            ->leftJoin('inv_requests as req', 'req.id', 'trx.inv_request_id')
                            ->leftJoin('rabs as rab', 'rab.id', 'req.rab_id')
                            ->leftJoin('orders as o', 'o.id', 'rab.order_id')
                            ->whereBetween('inv_trx_date', [$request->get('date'), $request->get('date2')])
                            ->whereIn('trx_type', ['REQ_ITEM', 'STK_ADJ'])
                            ->where('trx.no', 'LIKE', 'SBY-OUT%')
                            ->orderBy('trx.inv_trx_date', 'ASC');
        }

        if ($request->get('no_spk')) {
            $getLaporan->where('o.spk_number', $request->get('no_spk'));
        }

        if($request->get('warehouse_id')){
            if($request->get('warehouse_id') != 'all')
            $getLaporan->where('trx_ds.m_warehouse_id', $request->get('warehouse_id'));
        }

        if ($request->get('date') && $request->get('date2')) {
            $data['laporan'] = $getLaporan->get();
        }
        
        return view('exports.export_laporan_pengeluaran_material', $data);
    }

    public function listPengeluaran()
    {
        $is_error = false;
        $error_message = '';
        $data = array(
            'error' => array(
                'is_error' => $is_error,
                'error_message' => $error_message
            )
        );
        
        return view('pages.inv.pengambilan_barang.hapus_pengeluaran_barang', $data);
    }

    public function getListPengeluaranMaterialJson() {
        $response = null;
        $message = 'tes';
        try
        {
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . '/inv/pengeluaran_barang_trx']);  
            $response = $client->request('GET', '', ['headers' => $headers]); 
            $body = $response->getBody();
            $content = $body->getContents();
            $response_array = json_decode($content,TRUE);
            $response = $content;
            $data=DataTables::of($response_array['data'])
                                    ->make(true);
            $message = 'sukses';
            
        } 
        catch(Exception $exception) {
            $message = $exception->getMessage();
            return $message;
        }
        catch(RequestException $exception) {
            $message = $exception->getMessage();
            return $message;
        }
        return $data;
    }

    public function hapusPengeluaran($id)
    {
        // 1 get detail inv trx
        // update stock 
        // delte trx_akuntansi dan detail
        // hapus detail trx
        // hapus trx

        // get detail trx
        $getDetailTrx = DB::table('inv_trx_ds')->where('inv_trx_id', $id)->get();
        // update stok di table stocks
        foreach($getDetailTrx as $key => $detail){
            DB::table('stocks')
                ->where('m_item_id', $detail->m_item_id)
                ->where('m_warehouse_id', $detail->m_warehouse_id)
                ->increment('amount', $detail->amount);

            DB::table('stocks')
                ->where('m_item_id', $detail->m_item_id)
                ->where('m_warehouse_id', $detail->m_warehouse_id)
                ->decrement('amount_out', $detail->amount);
            echo $detail->m_item_id . ', '.$detail->m_warehouse_id . ', '.$detail->amount;
        }
        // get id trx akun  by inv_trx_id
        $getIdTrxAccounting = DB::table('tbl_trx_akuntansi')->where('inv_trx_id', $id)->select('id_trx_akun')->first();

        if($getIdTrxAccounting != null){
            // delete detail trx akuntansi
            DB::table('tbl_trx_akuntansi_detail')->where('id_trx_akun', $getIdTrxAccounting->id_trx_akun)->delete();
            // delete trx akuntansi
            DB::table('tbl_trx_akuntansi')->where('id_trx_akun', $getIdTrxAccounting->id_trx_akun)->delete();
        }

        // delete detail trx by inv_trx_id
        DB::table('inv_trx_ds')->where('inv_trx_id', $id)->delete();
        // delete trx
        DB::table('inv_trxes')->where('id', $id)->delete();

        $notification = array(
            'message' => 'Berhasil menghapus data.',
            'alert-type' => 'success'
        );

        return redirect('pengeluaran_barang/list-pengeluaran')->with($notification);
    }
}
