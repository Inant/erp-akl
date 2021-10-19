<?php

namespace App\Http\Controllers\INV;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\RAB\RabController;
use App\Http\Controllers\Accounting\AkuntanController;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Carbon\Carbon;
use DB;
use Yajra\DataTables\Facades\DataTables;

class PenerimaanServiceController extends Controller
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
            
        return view('pages.inv.penerimaan_service.penerimaan_service_list', $data);
    }
        
    public function receive($id)
    {
        $is_error = false;
        $error_message = '';  
        
        $query=DB::table('purchase_services')
                    ->join('m_suppliers', 'm_suppliers.id', '=', 'purchase_services.m_supplier_id')
                    ->where('purchase_services.id', $id)
                    ->first();
        
        $list_bank=DB::table('list_bank')->get();
        $data = array(
                'error' => array(
                    'is_error' => $is_error,
                    'error_message' => $error_message
                ),
                'purchase_id' => $id,
                'data'        => $query,
                'site_id'     => $this->site_id,
                'list_bank'   => $list_bank
        );
        
        return view('pages.inv.penerimaan_service.penerimaan_service_receive', $data);
    }
    
    public function receivePost(Request $request)
    {
        $submit = $request->submit; //receive || decline
        $purchase_d_id = $request->post('id');//id purchase detail
        $purchase_id = $request->post('purchase_id');
        $m_item_name = $request->post('m_item_name');
        $m_unit_id = $request->post('m_unit_id');
        $receive_volume = $request->post('receive_volume');
        // $notes = $request->post('notes');
        $price = $request->post('price');
        $m_warehouse_id = $request->post('m_warehouse_id');
        $driver = $request->post('driver');
        $no_surat_jalan = $request->post('no_surat_jalan');
        $condition = $request->post('condition');

        // update price item per site
        
        //check purchase id yang kosong dan menyimpan purchase id yang memiliki item yang tidak kosong
        $id=array_unique($purchase_id);
        $temp_id=array();
        foreach ($id as $key => $value) {
            $a=0;
            foreach ($purchase_id as $k => $v) {
                if ($value == $purchase_id[$k]) {
                    $a=$a+$receive_volume[$k];
                }
            }
            if ($a > 0) {
                $temp_id[]=$value;
            }
        }
        
        
        DB::beginTransaction();
        try
        {
            foreach ($temp_id as $key => $value) {
                try
                {
                        $headers = [
                            'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                            'Accept'        => 'application/json',
                        ];
                        $client = new Client(['base_uri' => $this->base_api_url . '/inv/base/PurchaseService/'.$value]);
                    
                        $response = $client->request('GET', '', ['headers' => $headers]); 
                        $body = $response->getBody();
                        $content = $body->getContents();
                        $response_array = json_decode($content,TRUE);
                        $purchases=$response_array['data'];
                } catch(RequestException $exception) {
                }    

                //insert inv_trx
                $period_year = Carbon::now()->year;
                $period_month = Carbon::now()->month;
                $rabcon = new RabController();
                $inv_no = $rabcon->generateTransactionNo('RCV_SVC', $period_year, $period_month, $this->site_id );
                
                try
                {
                    $headers = [
                        'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                        'Accept'        => 'application/json',
                    ];
                    $client = new Client(['base_uri' => $this->base_api_url . 'inv/base/InvTrxService']);
                    $reqBody = [
                        'headers' => $headers,
                        'json' => [
                            'purchase_service_id' => $value,
                            // 'trx_type' => 'RECEIPT',
                            // 'no_surat_jalan' => $no_surat_jalan,
                            // 'inv_request_id' => null,
                            'no' => $inv_no,
                            'inv_trx_date' => Carbon::now()->toDateString(),
                            'site_id' => $this->site_id,
                            'is_entry' => true,
                            // 'ekspedisi' => $driver,
                            ]
                        ]; 
                        $response = $client->request('POST', '', $reqBody); 
                        $body = $response->getBody();
                        $content = $body->getContents();
                        $response_array = json_decode($content,TRUE);
                        $inv_trx = $response_array['data'];
   
                } catch(RequestException $exception) {
                }
                $temp_journal=array();
                $total_penerimaan=0;
                //insert inv_trx_d
                for($i = 0; $i < count($m_item_name); $i++){
                    if ($value == $purchase_id[$i]) {
                        if($receive_volume[$i] > 0) {
                            if ($purchases['with_ppn'] == true) {
                                $before_ppn=$price[$i] / 1.1;
                                $price[$i]=$before_ppn;
                            }
                            try
                            {
                                $headers = [
                                    'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                                    'Accept'        => 'application/json',
                                ];
                                $client = new Client(['base_uri' => $this->base_api_url . '/inv/base/InvTrxServiceD']);
                                $reqBody = [
                                    'headers' => $headers,
                                    'json' => [
                                        'inv_trx_service_id' => $inv_trx['id'],
                                        'service_name' => $m_item_name[$i],
                                        'amount' => $receive_volume[$i],
                                        'm_unit_id' => $m_unit_id[$i],
                                        // 'notes' => $notes[$i],
                                        'm_warehouse_id' => $m_warehouse_id[$i],
                                        'condition' => $condition[$i],
                                        'base_price'    => $price[$i]
                                        ]
                                    ]; 
                                    $response = $client->request('POST', '', $reqBody); 
                                    $body = $response->getBody();
                                    $content = $body->getContents();
                                    $response_array = json_decode($content,TRUE);
                            } catch(RequestException $exception) {
                            }
                            
                            // $total_material+=($receive_volume[$i]*$price[$i]);
                            $total_penerimaan+=($receive_volume[$i]*$price[$i]);
                            $temp_journal[]=array(
                                'total' => ($receive_volume[$i]*$price[$i]),
                            );    
                            
                        }
                    }
                }
                $input_jurnal=array(
                    'purchase_service_id' => $value,
                    'inv_trx_service_id' => $inv_trx['id'],
                    'data' => $temp_journal,
                    'total' => $total_penerimaan,
                    'user_id'   => $this->user_id,
                    'deskripsi'     => 'Penerimaan Jasa dari No '.$inv_no,
                    'tgl'       => date('Y-m-d'),
                    'wop'       => $purchases['wop'],
                    'without_ppn'       => $purchases['is_without_ppn'],
                    'location_id'   => $this->site_id
                );
                $this->journalPenerimaan($input_jurnal);

                if($this->checkIsClosedPo($value)){
                    try
                    {
                        $headers = [
                            'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                            'Accept'        => 'application/json',
                        ];
                        $client = new Client(['base_uri' => $this->base_api_url . '/inv/base/PurchaseService/'.$value]);
                        $reqBody = [
                            'headers' => $headers,
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
            }
            
            // //update average price
            // $total_penerimaan=0;
            // for($i = 0; $i < count($m_item_id); $i++){
            //     if($receive_volume[$i] > 0) {
            //         $query=DB::table('m_items')->where('m_group_item_id', $m_item_id[$i])->get();
            //         $data=array(
            //             'm_item_id' => $m_item_id[$i],
            //             'amount' => $receive_volume[$i],
            //             'm_unit_id' => $m_unit_id[$i],
            //             'price' => $price[$i],
            //             'site_id'   => $this->site_id
            //         );
            //         $this->updateItemPrice($data);
            //         // $total_penerimaan+=($receive_volume[$i] * $price[$i]);
            //         if (count($query) != 0) {
            //             $total_item=count($query);
            //             $price_set=$price[$i] / $total_item;
            //             foreach ($query as $key => $value) {
            //                 $data=array(
            //                     'm_item_id' => $value->id,
            //                     'amount' => $receive_volume[$i] * $value->amount_in_set,
            //                     'm_unit_id' => $value->m_unit_id,
            //                     'price' => $price_set / $value->amount_in_set,
            //                     'site_id'   => $this->site_id
            //                 );
            //                 $this->updateItemPrice($data);
            //             }
            //         }
            //     }
            // }
            
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
        return redirect('penerimaan_service/close_purchase')->with($notification);
    }

    
    private function journalPO($data){
        $inv_no=$data['inv_no'];
        $total=$data['total'];
        $wop=$data['wop'];
        $data_trx=array(
                    'deskripsi'     => 'Penerimaan Material dengan INV NO '.$inv_no,
                    'location_id'     => $this->site_id,
                    'tanggal'       => date('Y-m-d'),
                );
        $insert=DB::table('tbl_trx_akuntansi')->insert($data_trx);
        if ($insert) {
            $id_last=DB::table('tbl_trx_akuntansi')->max('id_trx_akun');
            $data=array(
                'id_trx_akun'   => $id_last,
                'id_akun'       => $wop == 'cash' ? 8 : 11,
                'jumlah'        => $total,
                'tipe'          => "KREDIT",
                'keterangan'    => 'akun',
            );
            DB::table('tbl_trx_akuntansi_detail')->insert($data);
            $data1=array(
                'id_trx_akun'   => $id_last,
                'id_akun'       => 10,
                'jumlah'        => $total,
                'tipe'          => "DEBIT",
                'keterangan'    => 'lawan',
            );
            DB::table('tbl_trx_akuntansi_detail')->insert($data1);
        }
    }
    private function updateItemPrice($data){
        $get_save_price=DB::table('m_item_prices')->where(['m_item_id' => $data['m_item_id'], 'm_unit_id' => $data['m_unit_id'], 'site_id'   => $data['site_id']])->first();
        
        if ($get_save_price == null) {
            try
            {
                $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . '/inv/base/MItemPrice']);
                $reqBody = [
                    'headers' => $headers,
                'json' => $data
                    ]; 
                    $response = $client->request('POST', '', $reqBody); 
                    $body = $response->getBody();
                    $content = $body->getContents();
                    $response_array = json_decode($content,TRUE);
            } catch(RequestException $exception) {
            }
        }else{
            $stok_before=$get_save_price->amount;
            $price_before=$get_save_price->price;
            $sum_before=$stok_before*$price_before;
            $average=($sum_before + ($data['amount']*$data['price'])) / ($stok_before+$data['amount']);
            $data_update=array(
                'amount'        => ($stok_before+$data['amount']),
                'price'         => $average,
                'updated_at'    => date('Y-m-d H:i:s')
            );
            try
            {
                $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . '/inv/base/MItemPrice/'.$get_save_price->id]);
                $reqBody = [
                    'headers' => $headers,
                'json' => $data_update
                    ]; 
                    $response = $client->request('PUT', '', $reqBody); 
                    $body = $response->getBody();
                    $content = $body->getContents();
                    $response_array = json_decode($content,TRUE);
            } catch(RequestException $exception) {
            }
        }
    }
    public function decline($id) {
        try
        {
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . '/inv/base/Purchase/'.$id]);
            $reqBody = [
                'headers' => $headers,
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
                $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . '/inv/base/Purchase/' . $id]);  
                $response = $client->request('GET', '', ['headers' => $headers]); 
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
                $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . 'inv/inv_trx/get_by_purchase_id/' . $id]);  
                $response = $client->request('GET', '', ['headers' => $headers]); 
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
                $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . '/inv/base/MSupplier/' . $purchase['m_supplier_id']]);  
                $response = $client->request('GET', '', ['headers' => $headers]); 
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
                    $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . '/service/all_open?site_id='.$this->site_id]);  
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

    public function getPenerimaanDetailJson($id){
        $getPurchase=DB::table('purchase_services')->where('id', $id)->first();
        $getPOBySupplier=DB::table('purchase_services')->where('m_supplier_id', $getPurchase->m_supplier_id)->where('is_closed', '!=', true)->get();
        
        $response = null;
        $po_controller = new PembelianJasaController();
        $datas=array();
        foreach ($getPOBySupplier as $key => $value) {
            $data1 = $po_controller->getPOServiceDetailJson($value->id);
            $data1 = json_decode($data1, TRUE);
            $data1 = $data1['data'];
            foreach ($data1 as $v) {
                $datas[]=$v;
            }
        }
        
        $penerimaan=array();
        foreach ($getPOBySupplier as $key => $value) {
            $penerimaan1 = $this->getPenerimaanByPurchaseIdJson($value->id);
            $penerimaan1 = json_decode($penerimaan1, TRUE);
            $penerimaan1 = $penerimaan1['data'];
            foreach ($penerimaan1 as $v) {
                $penerimaan[]=$v;
            }
        }
        
        if(count($penerimaan) > 0)
            $penerimaan = $penerimaan;
        else
            $penerimaan = null;
            
        $data_show = array();
        for($i = 0; $i < count($datas); $i++){
            $volume = $datas[$i]['amount'];
            $service_name = $datas[$i]['service_name'];
            if($penerimaan != null){
                for($j = 0; $j < count($penerimaan); $j++){
                    $dt_penerimaan = $penerimaan[$j]['inv_trx_service_ds'];
                    for($k = 0; $k < count($dt_penerimaan); $k++){
                        if($service_name == $dt_penerimaan[$k]['service_name'])
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
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . '/service/get_by_inv_d/'.$id]);  
            $response = $client->request('GET', '', ['headers' => $headers]); 
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
        $po_controller = new PembelianJasaController();
        $datas = $po_controller->getPOServiceDetailJson($id);
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
            $m_item_id = $datas[$i]['service_name'];
            if($penerimaan != null){
                for($j = 0; $j < count($penerimaan); $j++){
                    $dt_penerimaan = $penerimaan[$j]['inv_trx_service_ds'];
                    for($k = 0; $k < count($dt_penerimaan); $k++){
                        if($m_item_id == $dt_penerimaan[$k]['service_name'])
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

    public function closePurchase()
    {
        $is_error = false;
        $error_message = '';  
        
        $data = array(
            'error' => array(
                'is_error' => $is_error,
                'error_message' => $error_message
                )
            );
            
        return view('pages.inv.penerimaan_service.close_purchase_list', $data);
    }

    public function getAllClosePurchase(){
        $response = null;
        try
        {
                    $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . '/service/all_close?site_id='.$this->site_id]);  
                    $response = $client->request('GET', '', ['headers' => $headers]); 
                    $body = $response->getBody();
                    $content = $body->getContents();
                    $response_array = json_decode($content,TRUE);
                    
                    $response = $content;         
        } catch(RequestException $exception) {
                    
        }    
                
        return $response;
    }

    public function printPenerimaanBarang2($id) {
        $purchase = null;
        $purchase_d = null;
        
        $inv_trx=DB::table('inv_trx_services')->where('id', $id)->first();
        
        $trx_d=DB::table('inv_trx_service_ds')
                    ->where('inv_trx_service_id', $id)
                    ->groupBy('service_name')
                    ->select('service_name', DB::raw('MAX(m_unit_id) as m_unit_id'), DB::raw('SUM(amount) as amount'))
                    ->get();
        foreach ($trx_d as $key => $value) {
            $value->m_units=DB::table('m_units')->where('id', $value->m_unit_id)->first();
        }
        // try
        // {
        //     $client = new Client(['base_uri' => $this->base_api_url . 'inv/inv_trx/get_by_inv_trx_id/' . $id]);  
        //     $response = $client->request('GET', '', ['headers' => $headers]); 
        //     $body = $response->getBody();
        //     $content = $body->getContents();
        //     $response_array = json_decode($content,TRUE);

        //     $purchase_d = $response_array['data'];         
        // } catch(RequestException $exception) {
            
        // }
        
        $hitung_h = 0;
        while($purchase == null && $hitung_h < 100) {
            // Get Header
            try
            {
                $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . '/inv/base/PurchaseService/' . $inv_trx->purchase_service_id]);  
                $response = $client->request('GET', '', ['headers' => $headers]); 
                $body = $response->getBody();
                $content = $body->getContents();
                $response_array = json_decode($content,TRUE);

                $purchase = $response_array['data'];         
            } catch(RequestException $exception) {
                
            }
            $hitung_h++;
        }
        
        $purchase['inv_trx'] = $inv_trx;

        // Get Supplier
        $m_suppliers = null;
        if ($purchase != null) {
            try
            {
                $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . '/inv/base/MSupplier/' . $purchase['m_supplier_id']]);  
                $response = $client->request('GET', '', ['headers' => $headers]); 
                $body = $response->getBody();
                $content = $body->getContents();
                $response_array = json_decode($content,TRUE);

                $m_suppliers = $response_array['data'];         
            } catch(RequestException $exception) {
            }
        }

        $purchase['m_suppliers'] = $m_suppliers;

        $data = array(
            'purchase' => $purchase,
            'purchase_d' => $trx_d,
            'user_name' => $this->user_name
        );
        return view('pages.inv.penerimaan_service.print_penerimaan_service_closed', $data);
    }

    public function getPOByMaterial(){
        $id=$_GET['m_item_id'];
        
        if($this->site_id != null) {
            if ($id == null) {
                $datas =  DB::table('purchases')->where('site_id', $this->site_id)->whereNotNull('m_supplier_id')
                ->get();
            }else{
                $datas = DB::table('purchases')
                    ->join('purchase_ds', 'purchases.id', '=', 'purchase_ds.purchase_id')
                    ->where('site_id', $this->site_id)->whereNotNull('m_supplier_id')
                    ->where('m_item_id', $id)
                    ->select('purchases.*')
                    // ->select(DB::raw('MAX(purchases.id) AS id'), DB::raw('MAX(purchases.m_supplier_id) AS m_supplier_id'), DB::raw('MAX(purchases.site_id) AS site_id'))
                    ->groupBy('purchases.id')
                    ->get();
            }
        } else {
            if ($id == null) {
                $datas = DB::table('purchases')->whereNotNull('m_supplier_id')
                ->get();
            }else{
                $datas = DB::table('purchases')
                    ->join('purchase_ds', 'purchases.id', '=', 'purchase_ds.purchase_id')
                    ->where('site_id', $this->site_id)->whereNotNull('m_supplier_id')
                    ->where('m_item_id', $id)
                    ->select('purchases.*')
                    // ->select(DB::raw('MAX(purchases.id) AS id'), DB::raw('MAX(purchases.m_supplier_id) AS m_supplier_id'), DB::raw('MAX(purchases.site_id) AS site_id'))
                    ->groupBy('purchases.id')
                    ->get();
            }
        }

        foreach($datas as $data){
            $data->m_suppliers = DB::table('m_suppliers')->where('id', $data->m_supplier_id)->first();
            $data->sites = DB::table('sites')->where('id', $data->site_id)->first();
        }
        $response=DataTables::of($datas)
                                    ->make(true);
        return $response;
    }
    private function journalPenerimaan($data){
        $data_trx=array(
            'deskripsi'     => $data['deskripsi'],
            'location_id'     => $data['location_id'],
            'tanggal'       => $data['tgl'],
            'user_id'       => $data['user_id'],
            'purchase_service_id'   => $data['purchase_service_id'],
            'inv_trx_service_id'   => $data['inv_trx_service_id'],
        );
        $insert=DB::table('tbl_trx_akuntansi')->insert($data_trx);
        if ($insert) {
            $id_last=DB::table('tbl_trx_akuntansi')->max('id_trx_akun');
            $total_ppn=0;
            foreach ($data['data'] as $value) {
                if ($data['without_ppn'] == false) {
                    $ppn=$value['total'] * (1/10);
                }else{
                    $ppn=0;
                }
                $total_ppn+=$ppn;
                $akun=array(
                    'id_trx_akun'   => $id_last,
                    'id_akun'       => 93,
                    'jumlah'        => $value['total'],
                    'tipe'          => "DEBIT",
                    'keterangan'    => 'akun',
                );
                DB::table('tbl_trx_akuntansi_detail')->insert($akun);
            }
            // $akun=array(
            //     'id_trx_akun'   => $id_last,
            //     'id_akun'       => 93,
            //     'jumlah'        => $data['total'],
            //     'tipe'          => "DEBIT",
            //     'keterangan'    => 'akun',
            // );
            // DB::table('tbl_trx_akuntansi_detail')->insert($akun);
            if ($total_ppn != 0) {
                $lawan=array(
                    'id_trx_akun'   => $id_last,
                    'id_akun'       => ($data['wop'] == 'credit' ? 133 : 67),
                    'jumlah'        => $total_ppn,
                    'tipe'          => "DEBIT",
                    'keterangan'    => 'akun',
                );
                DB::table('tbl_trx_akuntansi_detail')->insert($lawan);   
            }
            $lawan=array(
                'id_trx_akun'   => $id_last,
                'id_akun'       => ($data['wop'] == 'cash' ? 139 : 147),
                'jumlah'        => $data['total'] + $total_ppn,
                'tipe'          => "KREDIT",
                'keterangan'    => 'lawan',
            );
            DB::table('tbl_trx_akuntansi_detail')->insert($lawan);
        }
    }
}
        
        
        