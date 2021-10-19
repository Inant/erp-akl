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
            
        return view('pages.inv.penerimaan_barang.penerimaan_barang_list', $data);
    }
        
    public function receive($id)
    {
        $is_error = false;
        $error_message = '';  
        
        $query=DB::table('purchases')
                    ->join('m_suppliers', 'm_suppliers.id', '=', 'purchases.m_supplier_id')
                    ->where('purchases.id', $id)
                    ->first();
        $warehouse = DB::table('m_warehouses')->where('site_id', $this->site_id)->get();
        
        $list_bank=DB::table('list_bank')->get();
        $data = array(
                'error' => array(
                    'is_error' => $is_error,
                    'error_message' => $error_message
                ),
                'purchase_id' => $id,
                'data'        => $query,
                'site_id'     => $this->site_id,
                'list_bank'   => $list_bank,
                'warehouse'   => $warehouse
        );
        
        return view('pages.inv.penerimaan_barang.penerimaan_barang_receive', $data);
    }
    
    public function receivePost(Request $request)
    {
        $submit = $request->submit; //receive || decline
        $purchase_d_id = $request->post('id');//id purchase detail
        $purchase_id = $request->post('purchase_id');
        $m_item_id = $request->post('m_item_id');
        $m_unit_id = $request->post('m_unit_id');
        $receive_volume = $request->post('receive_volume');
        $notes = $request->post('notes');
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
                        $client = new Client(['base_uri' => $this->base_api_url . '/inv/base/Purchase/'.$value]);
                    
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
                $inv_no = $rabcon->generateTransactionNo('INV_RCV', $period_year, $period_month, $this->site_id );
                
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
                            'purchase_id' => $value,
                            'trx_type' => 'RECEIPT',
                            'no_surat_jalan' => $no_surat_jalan,
                            'inv_request_id' => null,
                            'no' => $inv_no,
                            'inv_trx_date' => Carbon::now()->toDateString(),
                            'site_id' => $this->site_id,
                            'is_entry' => true,
                            'ekspedisi' => $driver,
                            ]
                        ]; 
                        $response = $client->request('POST', '', $reqBody); 
                        $body = $response->getBody();
                        $content = $body->getContents();
                        $response_array = json_decode($content,TRUE);
                        $inv_trx = $response_array['data'];
   
                } catch(RequestException $exception) {
                }
                $total_purchase=$total_material=$total_spare_part=0;
                //insert inv_trx_d
                $temp_journal=array();
                $total_penerimaan=0;
                for($i = 0; $i < count($m_item_id); $i++){
                    if ($value == $purchase_id[$i]) {
                        if($receive_volume[$i] > 0) {
                            if ($purchases['with_ppn'] == true) {
                                $before_ppn=$price[$i] / 1.1;
                                $price[$i]=$before_ppn;
                            }
                            $total_purchase+=($receive_volume[$i]*$price[$i]);
                            $query=DB::table('m_items')->where('m_group_item_id', $m_item_id[$i])->get();
                            //update average price
                            $get_stock=DB::table('stocks')->where('type', 'STK_NORMAL')->where('m_item_id', $m_item_id[$i])->where('site_id', $this->site_id)->select(DB::raw('SUM(amount) as amount'))->first();
                            $data_update=array(
                                'm_item_id' => $m_item_id[$i],
                                'amount' => $receive_volume[$i],
                                'm_unit_id' => $m_unit_id[$i],
                                'price' => $price[$i],
                                'site_id'   => $this->site_id
                            );
                            $sisa_stok=$get_stock != null ? $get_stock->amount : 0;
                            $this->updateItemPrice($data_update, $sisa_stok);

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
                                        'amount' => $receive_volume[$i],
                                        'm_unit_id' => $m_unit_id[$i],
                                        'notes' => '',
                                        'purchase_d_id' => $purchase_d_id[$i],
                                        'm_warehouse_id' => $m_warehouse_id,
                                        'condition' => (count($query) != 0 ? 3 : $condition[$i]),
                                        'base_price'    => $price[$i]
                                        ]
                                    ]; 
                                    $response = $client->request('POST', '', $reqBody); 
                                    $body = $response->getBody();
                                    $content = $body->getContents();
                                    $response_array = json_decode($content,TRUE);
                            } catch(RequestException $exception) {
                            }
                            if ($condition[$i] == 1 && count($query) == 0) {
                                //update stock
                                $cek_stok=DB::table('stocks')
                                            ->where('m_warehouse_id', $m_warehouse_id)
                                            ->where('site_id', $this->site_id)
                                            ->where('m_item_id', $m_item_id[$i])
                                            ->where('m_unit_id', $m_unit_id[$i])
                                            ->where('type', 'STK_NORMAL')
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
                                                'amount' => $receive_volume[$i],
                                                'amount_in' => $receive_volume[$i],
                                                'amount_out' => 0,
                                                'm_unit_id' => $m_unit_id[$i],
                                                'm_warehouse_id' => $m_warehouse_id,
                                                'type'  => 'STK_NORMAL'
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
                                                'amount' => $cek_stok->amount + $receive_volume[$i],
                                                'amount_in' => $cek_stok->amount_in + $receive_volume[$i]
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
                            if (count($query) != 0) {
                                $total_item=count($query);
                                $price_set=$price[$i] / $total_item;
                                foreach ($query as $k => $v) {
                                    $items=DB::table('m_items')->where('id', $v->id)->first();
                                    //update average
                                    $get_stock=DB::table('stocks')->where('type', 'STK_NORMAL')->where('m_item_id', $v->id)->where('site_id', $this->site_id)->select(DB::raw('SUM(amount) as amount'))->first();
                                    $data_update=array(
                                        'm_item_id' => $v->id,
                                        'amount' => $receive_volume[$i] * $v->amount_in_set,
                                        'm_unit_id' => $v->m_unit_id,
                                        'price' => $price_set / $v->amount_in_set,
                                        'site_id'   => $this->site_id
                                    );
                                    $sisa_stok=$get_stock != null ? $get_stock->amount : 0;
                                    $this->updateItemPrice($data_update, $sisa_stok);
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
                                                'm_item_id' => $v->id,
                                                'amount' => $receive_volume[$i] * $v->amount_in_set,
                                                'm_unit_id' => $v->m_unit_id,
                                                'notes' => $notes[$i],
                                                'purchase_d_id' => $purchase_d_id[$i],
                                                'm_warehouse_id' => $m_warehouse_id,
                                                'condition' => $condition[$i],
                                                'base_price'    => $price_set / $v->amount_in_set
                                                ]
                                            ]; 
                                            $response = $client->request('POST', '', $reqBody); 
                                            $body = $response->getBody();
                                            $content = $body->getContents();
                                            $response_array = json_decode($content,TRUE);
                                    } catch(RequestException $exception) {
                                    }
                                    if ($condition[$i] == 1) {
                                        $cek_stok=DB::table('stocks')
                                                    ->where('m_warehouse_id', $m_warehouse_id)
                                                    ->where('site_id', $this->site_id)
                                                    ->where('m_item_id', $v->id)
                                                    ->where('m_unit_id', $v->m_unit_id)
                                                    ->where('type', 'STK_NORMAL')
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
                                                        'm_item_id' => $v->id,
                                                        'amount' => $receive_volume[$i] * $v->amount_in_set,
                                                        'amount_in' => $receive_volume[$i] * $v->amount_in_set,
                                                        'amount_out' => 0,
                                                        'm_unit_id' => $v->m_unit_id,
                                                        'm_warehouse_id' => $m_warehouse_id,
                                                        'type'  => 'STK_NORMAL'
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
                                                        'amount' => $cek_stok->amount + ($receive_volume[$i] * $v->amount_in_set),
                                                        'amount_in' => $cek_stok->amount_in + ($receive_volume[$i] * $v->amount_in_set)
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
                            
                            $item=DB::table('m_items')->where('id', $m_item_id[$i])->first();
                            // $get_purchase=DB::table('purchases')->where('id', $value)->first();
                            $total_penerimaan+=($receive_volume[$i]*$price[$i]);
                            if ($condition[$i] == 1){
                                if ($item->category == 'MATERIAL') {
                                    $temp_journal[]=array(
                                        'total' => ($receive_volume[$i]*$price[$i]),
                                        'm_warehouse_id' => $m_warehouse_id,
                                        'without_ppn'       => $purchases['is_without_ppn'],
                                        'type'      => 'material',
                                        'm_item_id' => $m_item_id[$i],
                                    );
                                    // $input_jurnal=array(
                                    //     'purchase_id' => $value,
                                    //     'inv_trx_id' => $inv_trx['id'],
                                    //     'total' => ($receive_volume[$i]*$price[$i]),
                                    //     'user_id'   => $this->user_id,
                                    //     'deskripsi'     => 'Penerimaan Material dari No '.$inv_no,
                                    //     'tgl'       => date('Y-m-d'),
                                    //     'm_warehouse_id' => $m_warehouse_id,
                                    //     'wop'       => $purchases['wop'],
                                    //     'without_ppn'       => $purchases['is_without_ppn'],
                                    //     'type'      => 'material',
                                    //     'm_supplier_id' => ($get_purchase != null ? $get_purchase->m_supplier_id : 0),
                                    //     'location_id'   => $this->site_id
                                    // );
                                    // $this->journalPenerimaan($input_jurnal);
                                }else{
                                    $temp_journal[]=array(
                                        'total' => ($receive_volume[$i]*$price[$i]),
                                        'm_warehouse_id' => $m_warehouse_id,
                                        'without_ppn'       => $purchases['is_without_ppn'],
                                        'type'      => 'spare part',
                                        'm_item_id' => $m_item_id[$i],
                                    );
                                }
                            }else{
                                $temp_journal[]=array(
                                    'total' => ($receive_volume[$i]*$price[$i]),
                                    'm_warehouse_id' => $m_warehouse_id,
                                    'without_ppn'       => $purchases['is_without_ppn'],
                                    'type'      => 'rest',
                                    'm_item_id' => $m_item_id[$i],
                                );
                            }
                        }
                    }
                }
                $get_purchase=DB::table('purchases')->where('id', $value)->first();
                $input_jurnal=array(
                    'purchase_id' => $value,
                    'inv_trx_id' => $inv_trx['id'],
                    'data' => $temp_journal,
                    'total' => $total_penerimaan,
                    'user_id'   => $this->user_id,
                    'deskripsi'     => 'Penerimaan PO dari No '.$inv_no,
                    'tgl'       => date('Y-m-d'),
                    'wop'       => $purchases['wop'],
                    'm_supplier_id' => ($get_purchase != null ? $get_purchase->m_supplier_id : 0),
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
            $client = new Client(['base_uri' => $this->base_api_url . '/inv/base/Purchase/'.$value]);
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
            
            //update average price
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

        return redirect('penerimaan_barang')->with($notification);
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
    private function updateItemPrice($data, $sisa_stok){
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
            if ($sisa_stok == 0) {
                $data_update=array(
                    'amount'        => $data['amount'],
                    'price'         => $data['price'],
                    'updated_at'    => date('Y-m-d H:i:s')
                );
            }else{
                $stok_before=$get_save_price->amount;
                $price_before=$get_save_price->price;
                // $sum_before=$stok_before*$price_before;
                // $average=($sum_before + ($data['amount']*$data['price'])) / ($stok_before+$data['amount']);
                $sum_before=$sisa_stok*$price_before;
                $average=($sum_before + ($data['amount']*$data['price'])) / ($sisa_stok+$data['amount']);
                $data_update=array(
                    'amount'        => ($stok_before+$data['amount']),
                    'price'         => $average,
                    'updated_at'    => date('Y-m-d H:i:s')
                );
            }
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
            $client = new Client(['base_uri' => $this->base_api_url . '/inv/po/all_open?site_id='.$this->site_id]);  
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

    // public function getPenerimaanDetailJson($id){
    //     $response = null;
    //     $po_controller = new PoController();
    //     $datas = $po_controller->getPODetailJson($id);
    //     $datas = json_decode($datas, TRUE);
    //     $datas = $datas['data'];
        
    //     $penerimaan = $this->getPenerimaanByPurchaseIdJson($id);
    //     $penerimaan = json_decode($penerimaan, TRUE);
    //     $penerimaan = $penerimaan['data'];
    //     if(count($penerimaan) > 0)
    //         $penerimaan = $penerimaan;
    //     else
    //         $penerimaan = null;
            
    //     $data_show = array();
    //     for($i = 0; $i < count($datas); $i++){
    //         $volume = $datas[$i]['amount'];
    //         $m_item_id = $datas[$i]['m_item_id'];
    //         if($penerimaan != null){
    //             for($j = 0; $j < count($penerimaan); $j++){
    //                 $dt_penerimaan = $penerimaan[$j]['inv_trx_ds'];
    //                 for($k = 0; $k < count($dt_penerimaan); $k++){
    //                     if($m_item_id == $dt_penerimaan[$k]['m_item_id'])
    //                         $volume -= $dt_penerimaan[$k]['amount'];
    //                 }
    //             }
    //         }
    //         $volume = round($volume, 2);
    //         $datas[$i]['amount'] = $volume;

    //         if($volume > 0)
    //             array_push($data_show, $datas[$i]);
    //     }

    //     $response = array('data' => $data_show);
    //     $response = json_encode($response);

    //     return $response;
    // }

    public function getPenerimaanDetailJson($id){
        $getPurchase=DB::table('purchases')->where('id', $id)->first();
        $getPOBySupplier=DB::table('purchases')->where('m_supplier_id', $getPurchase->m_supplier_id)->where('acc_ao', true)->where('is_closed', '!=', true)->get();
        
        $response = null;
        $po_controller = new PoController();
        $datas=array();
        foreach ($getPOBySupplier as $key => $value) {
            $data1 = $po_controller->getPODetailJson($value->id);
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
            $m_item_id = $datas[$i]['m_item_id'];
            if($penerimaan != null){
                for($j = 0; $j < count($penerimaan); $j++){
                    if ($datas[$i]['purchase_id'] == $penerimaan[$j]['purchase_id']) {
                        $dt_penerimaan = $penerimaan[$j]['inv_trx_ds'];
                        for($k = 0; $k < count($dt_penerimaan); $k++){
                            if($m_item_id == $dt_penerimaan[$k]['m_item_id'])
                                $volume -= $dt_penerimaan[$k]['amount'];
                        }
                    }
                }
            }
            $volume = round($volume, 2);
            $datas[$i]['amount_po'] = $datas[$i]['amount'];
            $datas[$i]['amount'] = $volume;

            if($volume > 0)
                array_push($data_show, $datas[$i]);
        }

        $response = array('data' => $data_show);
        $response = json_encode($response);

        return $response;
    }

    public function getPenerimaanDetailJsonById($id){
        $getPOBySupplier=DB::table('purchases')->where('id', $id)->get();
        
        $response = null;
        $po_controller = new PoController();
        $datas=array();
        foreach ($getPOBySupplier as $key => $value) {
            $data1 = $po_controller->getPODetailJson($value->id);
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
            $m_item_id = $datas[$i]['m_item_id'];
            if($penerimaan != null){
                for($j = 0; $j < count($penerimaan); $j++){
                    if ($datas[$i]['purchase_id'] == $penerimaan[$j]['purchase_id']) {
                        $dt_penerimaan = $penerimaan[$j]['inv_trx_ds'];
                        for($k = 0; $k < count($dt_penerimaan); $k++){
                            if($m_item_id == $dt_penerimaan[$k]['m_item_id'])
                                $volume -= $dt_penerimaan[$k]['amount'];
                        }
                    }
                }
            }
            $volume = round($volume, 2);
            $datas[$i]['amount_po'] = $datas[$i]['amount'];
            $datas[$i]['amount'] = $volume;

            
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
            $client = new Client(['base_uri' => $this->base_api_url . '/inv/inv_trx/get_by_purchase_id/'.$id]);  
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
            
        return view('pages.inv.penerimaan_barang.close_purchase_list', $data);
    }

    public function getAllClosePurchase(){
        $response = null;
        try
        {
                    $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . '/inv/po/all_close?site_id='.$this->site_id]);  
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

    public function printPenerimaanBarang2($id) {
        $purchase = null;
        $purchase_d = null;
        
        $inv_trx=DB::table('inv_trxes')->where('id', $id)->first();

        $trx_d=DB::table('inv_trx_ds')
                    ->where('inv_trx_id', $id)
                    ->groupBy('m_item_id')
                    ->select('m_item_id', DB::raw('MAX(m_unit_id) as m_unit_id'), DB::raw('SUM(amount) as amount'))
                    ->get();
        foreach ($trx_d as $key => $value) {
            $value->m_items=DB::table('m_items')->where('id', $value->m_item_id)->first();
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
            $client = new Client(['base_uri' => $this->base_api_url . '/inv/base/Purchase/' . $inv_trx->purchase_id]);  
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

        // $po_controller = new PoController();
        // $purchase_d = $po_controller->getPODetailJson($id);
        // $purchase_d = json_decode($purchase_d, TRUE);
        // $purchase_d = $purchase_d['data'];

        $data = array(
            'purchase' => $purchase,
            'purchase_d' => $trx_d,
            'user_name' => $this->user_name
        );
        
        
        return view('pages.inv.penerimaan_barang.print_penerimaan_barang_closed', $data);
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
    public function indexATK()
    {
        $is_error = false;
        $error_message = '';  
        
        $data = array(
            'error' => array(
                'is_error' => $is_error,
                'error_message' => $error_message
                )
            );
            
        return view('pages.inv.penerimaan_barang.penerimaan_atk_list', $data);
    }
    public function getAllOpenPurchaseAsset(){
        $response = null;
        try
        {
                    $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . '/inv/po/atk_all_open?site_id='.$this->site_id]);  
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
    public function receiveATK($id)
    {
        $is_error = false;
        $error_message = '';  
        
        $query=DB::table('purchase_assets')
                    ->join('m_suppliers', 'm_suppliers.id', '=', 'purchase_assets.m_supplier_id')
                    ->where('purchase_assets.id', $id)
                    ->first();
        $list_bank=DB::table('list_bank')->get();
        $data = array(
                'error' => array(
                    'is_error' => $is_error,
                    'error_message' => $error_message
                ),
                'purchase_asset_id' => $id,
                'data'        => $query,
                'site_id'     => $this->site_id,
                'list_bank'   => $list_bank
        );
        
        return view('pages.inv.penerimaan_barang.penerimaan_atk_receive', $data);
    }
    private function cekAmortAccount($id){
        $acccon = new AkuntanController();
        $account_asset=$acccon->accountAsset();
        $amort_id=0;
        foreach ($account_asset as $value) {
            if ($value['id'] == $id) {
                $amort_id=$value['amort_id'];
            }
        }
        return $amort_id;
    }
    public function createAccount($m_item_id, $id, $amort_id){
        $item=DB::table('m_items')->where('id', $m_item_id)->first();
        $aktiva=$this->getNoAkun($id);
        $no_aktiva=$this->explodeNoAkun($aktiva['no_akun_main']);
        $data_aktiva=array(
            'no_akun'   => $no_aktiva[0].'.'.$no_aktiva[1].'.'.$no_aktiva[2].'.'.($aktiva['total'] + 1),
            'nama_akun' => $aktiva['nama_akun'].' '.$item->name,
            'id_main_akun' => $id,
            'level' => 3,
            'sifat_debit'     => 1,
            'sifat_kredit'    => 0,   
            'id_parent'       => $aktiva['id_parent'],
            'turunan1'        => $aktiva['turunan1'],
            'turunan2'        => $id,
            'turunan3'        => $aktiva['turunan2'],
            'turunan4'        => $aktiva['turunan4'], 
        );
        $aktiva_id=$this->saveAccount($data_aktiva);

        $amort_aktiva=$this->getNoAkun($amort_id);
        $no_amort_aktiva=$this->explodeNoAkun($amort_aktiva['no_akun_main']);
        $data_amort_aktiva=array(
            'no_akun'   => $no_amort_aktiva[0].'.'.$no_amort_aktiva[1].'.'.$no_amort_aktiva[2].'.'.$no_amort_aktiva[3].'.'.($amort_aktiva['total'] + 1),
            'nama_akun' => $amort_aktiva['nama_akun'].' '.$item->name,
            'id_main_akun' => $amort_id,
            'level' => 4,
            'sifat_debit'     => 1,
            'sifat_kredit'    => 0,   
            'id_parent'       => $amort_aktiva['id_parent'],
            'turunan1'        => $amort_aktiva['turunan1'],
            'turunan2'        => $amort_aktiva['turunan2'],
            'turunan3'        => $amort_id,
            'turunan4'        => $amort_aktiva['turunan4'], 
        );
        $aktiva_amort_id=$this->saveAccount($data_amort_aktiva);
        try
        {
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . '/inv/base/AccountAsset']);
            $reqBody = [
                'headers' => $headers,
                'json' => [
                    'm_item_id' => $m_item_id,
                    'asset_id' => $aktiva_id,
                    'amort_asset_id' => $aktiva_amort_id,
                    ]
                ]; 
                $response = $client->request('POST', '', $reqBody); 
                $body = $response->getBody();
                $content = $body->getContents();
                $response_array = json_decode($content,TRUE);
        } catch(RequestException $exception) {
        }
    }
    private function saveAccount($data){
        $akun=array(
            'no_akun'         => $data['no_akun'],
            'nama_akun'       => $data['nama_akun'],
            'level'           => $data['level'],
            'id_main_akun'    => $data['id_main_akun'],
            'sifat_debit'     => $data['sifat_debit'],
            'sifat_kredit'    => $data['sifat_kredit'],
        );
        DB::table('tbl_akun')->insert($akun);
        $row=DB::table('tbl_akun')->max('id_akun');
        $data_d=array(
            'id_akun'           => $row,
            'id_parent'         => $data['id_parent'],
            'turunan1'          => $data['turunan1'],
            'turunan2'          => $data['turunan2'],
            'turunan3'          => $data['turunan3'],
            'turunan4'          => $data['turunan4'],
        );
        DB::table('tbl_akun_detail')->insert($data_d);
        return $row;
    }
    private function explodeNoAkun($no){
        $data=explode('.', $no);
        return $data;
    }
    private function getNoAkun($id){
        $data=DB::table('tbl_akun')
                ->select(DB::raw('MAX(id_main_akun) as id_main_akun'), DB::raw('MAX(no_akun) as no_akun'), DB::raw('COUNT(id_akun) as total_akun'))
                ->where('id_main_akun', $id)
                ->first();
        $data2=DB::table('tbl_akun')->where('id_akun', $id)->first();
        $data_d=DB::table('tbl_akun_detail')->where('id_akun', $id)->first();
        $akun= array('no_akun'=>$data->no_akun,'id_main_akun'=>$data->id_main_akun, 'no_akun_main'=>$data2->no_akun, 'nama_akun'=>$data2->nama_akun, 'total'=>$data->total_akun, 'id_parent' => $data_d->id_parent, 'turunan1' => $data_d->turunan1, 'turunan2' => $data_d->turunan2, 'turunan3' => $data_d->turunan3, 'turunan4' => $data_d->turunan4);
        return $akun;
    }
    public function receiveATKPost(Request $request)
    {
        $tipe_asset = $request->post('tipe_asset');
        
        $submit = $request->submit; //receive || decline
        $purchase_d_id = $request->post('id');//id purchase detail
        $purchase_id = $request->post('purchase_asset_id');
        $m_item_id = $request->post('m_item_id');
        $m_unit_id = $request->post('m_unit_id');
        $receive_volume = $request->post('receive_volume');
        $notes = $request->post('notes');
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
            $client = new Client(['base_uri' => $this->base_api_url . '/inv/base/PurchaseAsset/'.$value]);
                    
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
                $inv_no = $rabcon->generateTransactionNo('INV_RCV', $period_year, $period_month, $this->site_id );
                
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
                            'purchase_asset_id' => $value,
                            'trx_type' => 'RECEIPT',
                            'no_surat_jalan' => $no_surat_jalan,
                            'inv_request_id' => null,
                            'no' => $inv_no,
                            'inv_trx_date' => Carbon::now()->toDateString(),
                            'site_id' => $this->site_id,
                            'is_entry' => true,
                            'ekspedisi' => $driver,
                            ]
                        ]; 
                        $response = $client->request('POST', '', $reqBody); 
                        $body = $response->getBody();
                        $content = $body->getContents();
                        $response_array = json_decode($content,TRUE);
                        $inv_trx = $response_array['data'];
   
                } catch(RequestException $exception) {
                }
                $total_purchase=0;
                //insert inv_trx_d
                $temp_journal=array();
                $total_penerimaan=0;
                for($i = 0; $i < count($m_item_id); $i++){
                    if ($value == $purchase_id[$i]) {
                        if($receive_volume[$i] > 0) {
                            if ($purchases['with_ppn'] == true) {
                                $before_ppn=$price[$i] / 1.1;
                                $price[$i]=$before_ppn;
                            }
                            $total_purchase+=($receive_volume[$i]*$price[$i]);
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
                                        'amount' => $receive_volume[$i],
                                        'm_unit_id' => $m_unit_id[$i],
                                        'notes' => $notes[$i],
                                        'purchase_d_id' => $purchase_d_id[$i],
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
                            //update average price
                            $get_stock=DB::table('stocks')->where('type', 'STK_NORMAL')->where('m_item_id', $m_item_id[$i])->where('site_id', $this->site_id)->select(DB::raw('SUM(amount) as amount'))->first();
                            $data_update=array(
                                'm_item_id' => $m_item_id[$i],
                                'amount' => $receive_volume[$i],
                                'm_unit_id' => $m_unit_id[$i],
                                'price' => $price[$i],
                                'site_id'   => $this->site_id
                            );
                            $sisa_stok=$get_stock != null ? $get_stock->amount : 0;
                            $this->updateItemPrice($data_update, $sisa_stok);

                            if ($condition[$i] == 1) {
                                try
                                {
                                    $headers = [
                                        'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                                        'Accept'        => 'application/json',
                                    ];
                                    $client = new Client(['base_uri' => $this->base_api_url . '/inv/base/MaterialAsset']);
                                    $reqBody = [
                                        'headers' => $headers,
                                        'json' => [
                                            'purchase_asset_id' => $value,
                                            'inv_trx_id' => $inv_trx['id'],
                                            'm_item_id' => $m_item_id[$i],
                                            'amount' => $receive_volume[$i],
                                            'm_unit_id' => $m_unit_id[$i],
                                            'base_price'    => $price[$i],
                                            'site_id' => $this->site_id,
                                            ]
                                        ]; 
                                        $response = $client->request('POST', '', $reqBody); 
                                        $body = $response->getBody();
                                        $content = $body->getContents();
                                        $response_array = json_decode($content,TRUE);
                                } catch(RequestException $exception) {
                                }
                                
                                $cek_stok=DB::table('stocks')
                                            ->where('m_warehouse_id', $m_warehouse_id[$i])
                                            ->where('site_id', $this->site_id)
                                            ->where('m_item_id', $m_item_id[$i])
                                            ->where('m_unit_id', $m_unit_id[$i])
                                            ->where('type', 'STK_NORMAL')
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
                                                'amount' => $receive_volume[$i],
                                                'amount_in' => $receive_volume[$i],
                                                'amount_out' => 0,
                                                'm_unit_id' => $m_unit_id[$i],
                                                'm_warehouse_id' => $m_warehouse_id[$i],
                                                'type'  => 'STK_NORMAL'
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
                                                'amount' => $cek_stok->amount + $receive_volume[$i],
                                                'amount_in' => $cek_stok->amount_in + $receive_volume[$i]
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
                            $cek_account_asset=DB::table('account_assets')->where('m_item_id', $m_item_id[$i])->first();
                            if ($cek_account_asset == null) {
                                // foreach ($tipe_asset as $key) {
                                if ($condition[$i] == 1) {  
                                    $amort_id=$this->cekAmortAccount($tipe_asset[$i]);
                                    $this->createAccount($m_item_id[$i], $tipe_asset[$i], $amort_id);
                                }
                                // }
                            }
                            $cek_account_asset=DB::table('account_assets')->where('m_item_id', $m_item_id[$i])->first();
                            // echo $cek_account_asset->asset_id;
                            
                            $item=DB::table('m_items')->where('id', $m_item_id[$i])->first();
                            $get_purchase=DB::table('purchase_assets')->where('id', $value)->first();
                            $total_penerimaan+=($receive_volume[$i]*$price[$i]);

                            if ($condition[$i] == 1) {   
                                $temp_journal[]=array(
                                    'total' => ($receive_volume[$i]*$price[$i]),
                                    'm_warehouse_id' => $m_warehouse_id[$i],
                                    'without_ppn'       => $purchases['is_without_ppn'],
                                    'akun'      => $cek_account_asset->asset_id,
                                    'm_item_id' => $m_item_id[$i],
                                );    
                                // $input_jurnal=array(
                                //     'purchase_asset_id' => $value,
                                //     'inv_trx_id' => $inv_trx['id'],
                                //     'total' => ($receive_volume[$i]*$price[$i]),
                                //     'user_id'   => $this->user_id,
                                //     'deskripsi'     => 'Penerimaan Asset dari No '.$inv_no,
                                //     'tgl'       => date('Y-m-d'),
                                //     'm_warehouse_id' => $m_warehouse_id[$i],
                                //     'wop'       => $purchases['wop'],
                                //     'without_ppn'       => $purchases['is_without_ppn'],
                                //     'akun'      => $cek_account_asset->asset_id,
                                //     'type'      => 'asset',
                                //     'm_supplier_id' => ($get_purchase != null ? $get_purchase->m_supplier_id : 0),
                                //     'location_id'   => $this->site_id
                                // );
                                // $this->journalPenerimaanATK($input_jurnal);
                                
                            }else{
                                $temp_journal[]=array(
                                    'total' => ($receive_volume[$i]*$price[$i]),
                                    'm_warehouse_id' => $m_warehouse_id[$i],
                                    'without_ppn'       => $purchases['is_without_ppn'],
                                    'akun'      => 92,
                                    'm_item_id' => $m_item_id[$i],
                                );    
                                // $input_jurnal=array(
                                //     'purchase_asset_id' => $value,
                                //     'inv_trx_id' => $inv_trx['id'],
                                //     'total' => ($receive_volume[$i]*$price[$i]),
                                //     'user_id'   => $this->user_id,
                                //     'deskripsi'     => 'Penerimaan Asset dari No '.$inv_no,
                                //     'tgl'       => date('Y-m-d'),
                                //     'm_warehouse_id' => $m_warehouse_id[$i],
                                //     'wop'       => $purchases['wop'],
                                //     'without_ppn'       => $purchases['is_without_ppn'],
                                //     'akun'      => 92,
                                //     'type'      => 'asset',
                                //     'm_supplier_id' => ($get_purchase != null ? $get_purchase->m_supplier_id : 0),
                                //     'location_id'   => $this->site_id
                                // );
                                // $this->journalPenerimaanATK($input_jurnal);
                            }
                        }
                    }
                }
                $get_purchase=DB::table('purchase_assets')->where('id', $value)->first();
                $input_jurnal=array(
                    'purchase_asset_id' => $value,
                    'inv_trx_id' => $inv_trx['id'],
                    'data' => $temp_journal,
                    'total' => $total_penerimaan,
                    'user_id'   => $this->user_id,
                    'deskripsi'     => 'Penerimaan ATK dari No '.$inv_no,
                    'tgl'       => date('Y-m-d'),
                    'wop'       => $purchases['wop'],
                    'm_supplier_id' => ($get_purchase != null ? $get_purchase->m_supplier_id : 0),
                    'location_id'   => $this->site_id
                );
                $this->journalPenerimaanATK($input_jurnal);

                if($this->checkIsClosedPoAssets($value)){
                    try
                    {
                        $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . '/inv/base/PurchaseAsset/'.$value]);
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

                // $period_year = date('Y');
                // $period_month = date('m');
                // $rabcon = new RabController();
                // $bill_no = $rabcon->generateTransactionNo('PAID_SPPL', $period_year, $period_month, $this->site_id );
                // try
                // {
                //     $client = new Client(['base_uri' => $this->base_api_url . 'inv/base/PaymentSupplier']);
                //     $reqBody = [
                //         'json' => [
                //             'purchase_asset_id' => $value,
                //             'wop' => $request->input('wop'),
                //             'bank_number' => $request->input('bank_number'),
                //             'atas_nama' => $request->input('atas_nama'),
                //             'ref_code' => $request->input('ref_code'),
                //             'id_bank' => $request->input('bank_id'),
                //             'amount' => $total_purchase,
                //             'description' => $request->input('description'),
                //             'pay_date' => $request->input('pay_date'),
                //             'no'  => $bill_no,
                //         ]
                //     ]; 
                    
                //     $response = $client->request('POST', '', $reqBody); 
                //     $body = $response->getBody();
                //     $content = $body->getContents();
                //     $response_array = json_decode($content,TRUE);
                // } catch(RequestException $exception) {
                // }
            }
            
            //update average price
            // $total_penerimaan=0;
            // for($i = 0; $i < count($m_item_id); $i++){
            //     if($receive_volume[$i] > 0) {
            //         $data=array(
            //             'm_item_id' => $m_item_id[$i],
            //             'amount' => $receive_volume[$i],
            //             'm_unit_id' => $m_unit_id[$i],
            //             'price' => $price[$i],
            //             'site_id'   => $this->site_id
            //         );
            //         $this->updateItemPrice($data);
            //         $total_penerimaan+=($receive_volume[$i] * $price[$i]);

            //     }
            // }
            // if ($total_penerimaan != 0) {
            //     $data_po=array(
            //         'inv_no'         => $inv_no,
            //         'total'         => $total_penerimaan,
            //         'wop'         => $purchases['wop'],
            //     );
            //     $this->journalPO($data_po);
            // }
            
            DB::commit();
        }
        catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }


        $notification = array(
            'message' => 'Success receipt atk',
            'alert-type' => 'success'
        );

        return redirect('penerimaan_barang/atk')->with($notification);
    }
    public function getPenerimaanATKDetailJson($id){
        $getPurchase=DB::table('purchase_assets')->where('id', $id)->first();
        $getPOBySupplier=DB::table('purchase_assets')->where('m_supplier_id', $getPurchase->m_supplier_id)->where('acc_ao', true)->where('is_closed', '!=', true)->get();
        
        $response = null;
        $po_controller = new PoController();
        $datas=array();
        foreach ($getPOBySupplier as $key => $value) {
            $data1 = $po_controller->getPOATKDetailJson($value->id);
            $data1 = json_decode($data1, TRUE);
            $data1 = $data1['data'];
            foreach ($data1 as $v) {
                $datas[]=$v;
            }
        }
        
        $penerimaan=array();
        foreach ($getPOBySupplier as $key => $value) {
            $penerimaan1 = $this->getPenerimaanByPurchaseAssetIdJson($value->id);
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
            $m_item_id = $datas[$i]['m_item_id'];
            if($penerimaan != null){
                for($j = 0; $j < count($penerimaan); $j++){
                    if ($datas[$i]['purchase_asset_id'] == $penerimaan[$j]['purchase_asset_id']) {
                        $dt_penerimaan = $penerimaan[$j]['inv_trx_ds'];
                        for($k = 0; $k < count($dt_penerimaan); $k++){
                            if($m_item_id == $dt_penerimaan[$k]['m_item_id'])
                                $volume -= $dt_penerimaan[$k]['amount'];
                        }
                    }
                }
            }
            $volume = round($volume, 2);
            $datas[$i]['amount_po'] = $datas[$i]['amount'];
            $datas[$i]['amount'] = $volume;

            if($volume > 0)
                array_push($data_show, $datas[$i]);
        }

        $response = array('data' => $data_show);
        $response = json_encode($response);

        return $response;
    }
    public function getPenerimaanATKDetailJsonById($id){
        $getPOBySupplier=DB::table('purchase_assets')->where('id', $id)->get();
        
        $response = null;
        $po_controller = new PoController();
        $datas=array();
        foreach ($getPOBySupplier as $key => $value) {
            $data1 = $po_controller->getPOATKDetailJson($value->id);
            $data1 = json_decode($data1, TRUE);
            $data1 = $data1['data'];
            foreach ($data1 as $v) {
                $datas[]=$v;
            }
        }
        
        $penerimaan=array();
        foreach ($getPOBySupplier as $key => $value) {
            $penerimaan1 = $this->getPenerimaanByPurchaseAssetIdJson($value->id);
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
            $m_item_id = $datas[$i]['m_item_id'];
            if($penerimaan != null){
                for($j = 0; $j < count($penerimaan); $j++){
                    if ($datas[$i]['purchase_asset_id'] == $penerimaan[$j]['purchase_asset_id']) {
                        $dt_penerimaan = $penerimaan[$j]['inv_trx_ds'];
                        for($k = 0; $k < count($dt_penerimaan); $k++){
                            if($m_item_id == $dt_penerimaan[$k]['m_item_id'])
                                $volume -= $dt_penerimaan[$k]['amount'];
                        }
                    }
                }
            }
            $volume = round($volume, 2);
            $datas[$i]['amount_po'] = $datas[$i]['amount'];
            $datas[$i]['amount'] = $volume;

            
            array_push($data_show, $datas[$i]);
        }

        $response = array('data' => $data_show);
        $response = json_encode($response);

        return $response;
    }
    public function getPenerimaanByPurchaseAssetIdJson($id){
        $response = null;
        try
        {
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . '/inv/inv_trx/get_by_purchase_asset_id/'.$id]);  
            $response = $client->request('GET', '', ['headers' => $headers]);  
            $body = $response->getBody();
            $content = $body->getContents();
            $response_array = json_decode($content,TRUE);
                    
            $response = $content;         
        } catch(RequestException $exception) {
                    
        }    
                
        return $response;
    }
    public function printPenerimaanATK($id) {
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
            $client = new Client(['base_uri' => $this->base_api_url . '/inv/base/PurchaseAsset/' . $id]);  
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
            $client = new Client(['base_uri' => $this->base_api_url . 'inv/inv_trx/get_by_purchase_asset_id/' . $id]);  
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
        $purchase_d = $po_controller->getPOATKDetailJson($id);
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
    public function declineAtk($id) {
        try
        {
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . '/inv/base/PurchaseAsset/'.$id]);
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

        return redirect('penerimaan_barang/atk')->with($notification);
    }
    public function closePurchaseATK()
    {
        $is_error = false;
        $error_message = '';  
        
        $data = array(
            'error' => array(
                'is_error' => $is_error,
                'error_message' => $error_message
                )
            );
            
        return view('pages.inv.penerimaan_barang.close_purchase_atk_list', $data);
    }
    public function getAllClosePurchaseATK(){
        $response = null;
        try
        {
                    $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . '/inv/po/atk_all_close?site_id='.$this->site_id]);  
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
    public function printPenerimaanATK2($id) {
        $purchase = null;
        $purchase_d = null;
        
        $inv_trx=DB::table('inv_trxes')->where('id', $id)->first();

        $trx_d=DB::table('inv_trx_ds')
                    ->where('inv_trx_id', $id)
                    ->groupBy('m_item_id')
                    ->select('m_item_id', DB::raw('MAX(m_unit_id) as m_unit_id'), DB::raw('SUM(amount) as amount'))
                    ->get();
        foreach ($trx_d as $key => $value) {
            $value->m_items=DB::table('m_items')->where('id', $value->m_item_id)->first();
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
            $client = new Client(['base_uri' => $this->base_api_url . '/inv/base/PurchaseAsset/' . $inv_trx->purchase_asset_id]);  
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

        // $po_controller = new PoController();
        // $purchase_d = $po_controller->getPODetailJson($id);
        // $purchase_d = json_decode($purchase_d, TRUE);
        // $purchase_d = $purchase_d['data'];

        $data = array(
            'purchase' => $purchase,
            'purchase_d' => $trx_d,
            'user_name' => $this->user_name
        );
        // print_r($data);
        // exit();
        return view('pages.inv.penerimaan_barang.print_penerimaan_barang_closed', $data);
    }
    private function checkIsClosedPoAssets($id){
        $is_closed = true;
        $po_controller = new PoController();
        $datas = $po_controller->getPOATKDetailJson($id);
        $datas = json_decode($datas, TRUE);
        $datas = $datas['data'];
        
        $penerimaan = $this->getPenerimaanByPurchaseAssetIdJson($id);
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
    private function journalPenerimaan($data){       
        $data_trx=array(
            'deskripsi'     => $data['deskripsi'],
            'location_id'     => $data['location_id'],
            'tanggal'       => $data['tgl'],
            'user_id'       => $data['user_id'],
            'purchase_id' => $data['purchase_id'],
            'm_supplier_id' => $data['m_supplier_id'],
            'inv_trx_id'   => $data['inv_trx_id']
        );
        $insert=DB::table('tbl_trx_akuntansi')->insert($data_trx);
        if ($insert) {
            $id_last=DB::table('tbl_trx_akuntansi')->max('id_trx_akun');
            $total_ppn=0;
            foreach ($data['data'] as $value) {
                $akun=($value['type'] == 'material' ?  ($value['m_warehouse_id'] == 2 ? 141 : 142) : ($value['type'] == 'spare part' ?  ($value['m_warehouse_id'] == 2 ? 143 : 144) : 92));
                if ($value['without_ppn'] == false) {
                    $ppn=$value['total'] * (1/10);
                }else{
                    $ppn=0;
                }
                $total_ppn+=$ppn;
                $akun=array(
                    'id_trx_akun'   => $id_last,
                    'id_akun'       => $akun,
                    'jumlah'        => $value['total'],
                    'tipe'          => "DEBIT",
                    'keterangan'    => 'akun',
                    'm_item_id'     => $value['m_item_id'],
                );
                DB::table('tbl_trx_akuntansi_detail')->insert($akun);
            }
            if ($total_ppn != 0) {
                $lawan=array(
                    'id_trx_akun'   => $id_last,
                    'id_akun'       => ($data['wop'] == 'credit' ? 133 : 67),
                    'jumlah'        => $total_ppn,
                    'tipe'          => "DEBIT",
                    'keterangan'    => 'lawan',
                );
                DB::table('tbl_trx_akuntansi_detail')->insert($lawan);   
            }
            $lawan=array(
                'id_trx_akun'   => $id_last,
                'id_akun'       => ($data['wop'] == 'cash' ? 139 : 147),
                // 'id_akun'       => 20,
                'jumlah'        => $data['total'] + $total_ppn,
                // 'jumlah'        => $data['total'],
                'tipe'          => "KREDIT",
                'keterangan'    => 'lawan',
            );
            DB::table('tbl_trx_akuntansi_detail')->insert($lawan);
        }
    }
    private function journalPenerimaanATK($data){
        $data_trx=array(
            'deskripsi'     => $data['deskripsi'],
            'location_id'     => $data['location_id'],
            'user_id'       => $data['user_id'],
            'tanggal'       => $data['tgl'],
            'purchase_asset_id' => $data['purchase_asset_id'],
            'm_supplier_id' => $data['m_supplier_id'],
            'inv_trx_id'   => $data['inv_trx_id']
        );
        $insert=DB::table('tbl_trx_akuntansi')->insert($data_trx);
        if ($insert) {
            $id_last=DB::table('tbl_trx_akuntansi')->max('id_trx_akun');
            $total_ppn=0;
            foreach ($data['data'] as $value) {
                if ($value['without_ppn'] == false) {
                    $ppn=$value['total'] * (1/10);
                }else{
                    $ppn=0;
                }
                $total_ppn+=$ppn;
                $akun=array(
                    'id_trx_akun'   => $id_last,
                    'id_akun'       => $value['akun'],
                    'jumlah'        => $value['total'],
                    'tipe'          => "DEBIT",
                    'keterangan'    => 'akun',
                    'm_item_id'     => $value['m_item_id'],
                );
                DB::table('tbl_trx_akuntansi_detail')->insert($akun);
                if ($ppn != 0) {
                    $lawan=array(
                        'id_trx_akun'   => $id_last,
                        'id_akun'       => ($data['wop'] == 'credit' ? 133 : 67),
                        'jumlah'        => $ppn,
                        'tipe'          => "DEBIT",
                        'keterangan'    => 'lawan',
                    );
                    DB::table('tbl_trx_akuntansi_detail')->insert($lawan);   
                }
            }
            // $akun=array(
            //     'id_trx_akun'   => $id_last,
            //     'id_akun'       => $data['akun'],
            //     'jumlah'        => $data['total'],
            //     'tipe'          => "DEBIT",
            //     'keterangan'    => 'akun',
            // );
            // DB::table('tbl_trx_akuntansi_detail')->insert($akun);
            // if ($ppn != 0) {
            //     $lawan=array(
            //         'id_trx_akun'   => $id_last,
            //         'id_akun'       => ($data['wop'] == 'credit' ? 133 : 67),
            //         'jumlah'        => $ppn,
            //         'tipe'          => "DEBIT",
            //         'keterangan'    => 'lawan',
            //     );
            //     DB::table('tbl_trx_akuntansi_detail')->insert($lawan);   
            // }
            $lawan=array(
                'id_trx_akun'   => $id_last,
                'id_akun'       => ($data['wop'] == 'cash' ? 139 : 147),
                // 'id_akun'       => 20,
                'jumlah'        => $data['total'] + $total_ppn,
                // 'jumlah'        => $data['total'],
                'tipe'          => "KREDIT",
                'keterangan'    => 'lawan', 
            );
            DB::table('tbl_trx_akuntansi_detail')->insert($lawan);
        }
    }
}
        
        
        