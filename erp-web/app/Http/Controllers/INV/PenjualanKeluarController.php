<?php

namespace App\Http\Controllers\INV;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use GuzzleHttp\Client;
use Yajra\DataTables\Facades\DataTables;
use GuzzleHttp\Exception\RequestException;
use App\Http\Controllers\RAB\RabController;
use App\Http\Controllers\Accounting\AkuntanController;
use Carbon\Carbon;
use DB;
class PenjualanKeluarController extends Controller
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
        return view('pages.inv.penjualan_keluar.penjualan_keluar_list');
    }

    public function create() {

        $gudang = DB::table('m_warehouses')->where('site_id', $this->site_id)->whereNull('deleted_at')->get();
        $data = array(
            'site_id' => $this->site_id,
            'm_warehouse_id' => $this->m_warehouse_id,
            'gudang'    => $gudang
        );

        return view('pages.inv.penjualan_keluar.penjualan_keluar_create', $data);
    }

    public function createPost(Request $request) {
        $bill_no = $request->post('bill_no');
        $invoice = $request->post('invoice');
        $customer_id = $request->post('customer_id');
        $without_ppn = $request->post('without_ppn');
        $cara_bayar_single = $request->post('cara_bayar_single');
        $m_warehouse_id = $request->post('m_warehouse_id');
        $m_item_no = $request->post('m_item_no');
        $m_item_id = $request->post('m_item_id');
        // $stok_site = $request->post('stok_site');
        $total=$this->currency($request->total_bayar);
        $qty = $request->post('qty');
        $price = $request->post('price');
        $m_unit_id = $request->post('m_unit_id');
        $create_date = $request->post('create_date');
        $isSubmit = true;
        $inv_sale=null;
        $inv_trx=null;
        if ($isSubmit) {
            $period_year = Carbon::now()->year;
            $period_month = Carbon::now()->month;
            $rabcon = new RabController();
            $inv_no = $rabcon->generateTransactionNo('INV_OUT', $period_year, $period_month, $this->site_id );
            $inv_sale_no = $rabcon->generateTransactionNo('INV_SALE', $period_year, $period_month, $this->site_id );
            try
            {
                // ini_set('memory_limit', '-1');
                $headers = [
                    'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                    'Accept'        => 'application/json',
                ];
                $client = new Client(['base_uri' => $this->base_api_url . '/inv/base/InvSale']);
                $reqBody = [
                    'headers' => $headers,
                    'json' => [
                        'site_id' => $this->site_id,
                        'customer_id' => $customer_id,
                        'wop' => $cara_bayar_single,
                        'is_without_ppn' => $without_ppn,
                        'base_price'    => $total,
                        'm_warehouse_id' => $m_warehouse_id,
                        'create_date' => $create_date,
                        'no' => $inv_sale_no,
                        'invoice' => $invoice,
                        'bill_no' => $bill_no,
                        ]
                    ]; 
                    $response = $client->request('POST', '', $reqBody); 
                    $body = $response->getBody();
                    $content = $body->getContents();
                    $response_array = json_decode($content,TRUE);
                    $inv_sale = $response_array['data'];
            } catch(RequestException $exception) {
            }
            
            $temp_journal=array();
            $total_penjualan=$total_hpp=$ppn=0;
            for ($i = 0; $i < count($m_item_id); $i++) {
                try
                {
                    $headers = [
                        'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                        'Accept'        => 'application/json',
                    ];
                    $client = new Client(['base_uri' => $this->base_api_url . '/inv/base/InvSaleD']);
                    $reqBody = [
                        'headers' => $headers,
                        'json' => [
                            'inv_sale_id' => $inv_sale['id'],
                            'm_item_id' => $m_item_id[$i],
                            'amount' => $qty[$i],
                            'm_warehouse_id' => $m_warehouse_id,
                            'base_price' => $price[$i]
                        ]
                    ]; 
                    $response = $client->request('POST', '', $reqBody); 
                } catch(RequestException $exception) {
                }
                $ppn+=($without_ppn ? 0 : (($qty[$i]*$price[$i]) * 0.1));
            }
            $input_jurnal=array(
                'inv_sale_id' => $inv_sale['id'],
                'customer_id' => $customer_id,
                'total' => $total,
                'ppn' => $ppn,
                'wop' => $cara_bayar_single,
                'user_id'   => $this->user_id,
                'deskripsi'     => 'Permintaan Penjualan Material dari No '.$inv_sale_no,
                'tgl'       => $create_date,
                'location_id'   => $this->site_id
            );
            $this->journalPermintaanPenjualan($input_jurnal);
            $notification = array(
                'message' => 'Success Penjualan Keluar',
                'alert-type' => 'success'
            );
        } else {
            $notification = array(
                'message' => 'Error, Stock cannot smaller than request',
                'alert-type' => 'error'
            );
        }
        
        return redirect('penjualan_keluar')->with($notification);
    }
    private function journalPermintaanPenjualan($data){       
        $data_trx=array(
            'deskripsi'     => $data['deskripsi'],
            'location_id'     => $data['location_id'],
            'tanggal'       => $data['tgl'],
            'user_id'       => $data['user_id'],
            'customer_id'   => $data['customer_id'],
            'inv_sale_id' => $data['inv_sale_id']
        );
        $insert=DB::table('tbl_trx_akuntansi')->insert($data_trx);
        if ($insert) {
            $id_last=DB::table('tbl_trx_akuntansi')->max('id_trx_akun');
            $total_ppn=0;
            $lawan=array(
                'id_trx_akun'   => $id_last,
                'id_akun'       => 151,
                'jumlah'        => $data['total'] + $data['ppn'],
                'tipe'          => "DEBIT",
                'keterangan'    => 'lawan',
            );
            DB::table('tbl_trx_akuntansi_detail')->insert($lawan);
            if ($data['ppn'] != 0) {
                $ppn=array(
                    'id_trx_akun'   => $id_last,
                    'id_akun'       => $data['ppn'] == 'cash' ? 133 : 67,
                    'jumlah'        => $data['ppn'],
                    'tipe'          => "KREDIT",
                    'keterangan'    => 'lawan',
                );
                DB::table('tbl_trx_akuntansi_detail')->insert($ppn);
            }
            $akun=array(
                'id_trx_akun'   => $id_last,
                'id_akun'       => 97,
                'jumlah'        => $data['total'],
                'tipe'          => "KREDIT",
                'keterangan'    => 'akun',
            );
            DB::table('tbl_trx_akuntansi_detail')->insert($akun);
        }
    }
    private function journalPenjualan($data){       
        $data_trx=array(
            'deskripsi'     => $data['deskripsi'],
            'location_id'     => $data['location_id'],
            'tanggal'       => $data['tgl'],
            'user_id'       => $data['user_id'],
            'customer_id'   => $data['customer_id'],
            'inv_sale_id' => $data['inv_sale_id'],
            'inv_trx_id'   => $data['inv_trx_id']
        );
        $insert=DB::table('tbl_trx_akuntansi')->insert($data_trx);
        if ($insert) {
            $id_last=DB::table('tbl_trx_akuntansi')->max('id_trx_akun');
            foreach ($data['data'] as $value) {
                $akun=($value['type'] == 'material' ?  ($value['m_warehouse_id'] == 2 ? 141 : 142) : ($value['m_warehouse_id'] == 2 ? 143 : 144));
                $akun=array(
                    'id_trx_akun'   => $id_last,
                    'id_akun'       => $akun,
                    'jumlah'        => $value['total'],
                    'tipe'          => "KREDIT",
                    'keterangan'    => 'akun',
                );
                DB::table('tbl_trx_akuntansi_detail')->insert($akun);
                $lawan=array(
                    'id_trx_akun'   => $id_last,
                    'id_akun'       => $value['akun'],
                    'jumlah'        => $value['total'],
                    'tipe'          => "DEBIT",
                    'keterangan'    => 'lawan',
                );
                DB::table('tbl_trx_akuntansi_detail')->insert($lawan);
            }
        }
    }
    public function listPenjualanKeluarJson() {
        $response = null;
        try
        {
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . '/inv/penjualan_keluar?site_id='.$this->site_id]);  
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

    public function listPenjualanKeluarDetailJson($id) {
        $response = null;
        try
        {
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . '/inv/penjualan_keluar_detail/'.$id]);  
            $response = $client->request('GET', '', ['headers' => $headers]);  
            $body = $response->getBody();
            $content = $body->getContents();
            $response_array = json_decode($content,TRUE);

            $response = $content;         
        } catch(RequestException $exception) {
            
        }    

        return $response;
    }
    private function currency($val){
        $data=explode('.', $val);
        $new=implode('', $data);
        return $new;
    }
    public function send($id) {
        $is_error = false;
        $error_message = '';  
        
        $query=DB::table('inv_sales')
                    ->join('customers', 'customers.id', '=', 'inv_sales.customer_id')
                    ->join('m_warehouses', 'm_warehouses.id', '=', 'inv_sales.m_warehouse_id')
                    ->where('inv_sales.id', $id)
                    ->select('inv_sales.*', 'customers.coorporate_name', 'm_warehouses.name as warehouse_name')
                    ->first();
        $data = array(
                'error' => array(
                    'is_error' => $is_error,
                    'error_message' => $error_message
                ),
                'id' => $id,
                'data'        => $query,
                'site_id'     => $this->site_id,
        );
        return view('pages.inv.penjualan_keluar.penjualan_keluar_kirim', $data);
    }
    public function pengajuanInvSaleDetail($id) {
        $item = null;
        try
        {
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . '/inv/penjualan_keluar_detail/'.$id]);  
            $response = $client->request('GET', '', ['headers' => $headers]);  
            $body = $response->getBody();
            $content = $body->getContents();
            $response_array = json_decode($content,TRUE);
            $item=$response_array['data'];
        } catch(RequestException $exception) {
            
        }    
        foreach($response_array['data'] as $key => $value){
            $query=DB::table('inv_trx_ds')->join('inv_trxes', 'inv_trxes.id', 'inv_trx_ds.inv_trx_id')->where('inv_sale_id', $value['inv_sale_id'])->where('m_item_id', $value['m_item_id'])->select(DB::raw('COALESCE(SUM(amount), 0) as amount'))->first();
            $response_array['data'][$key]['total_used']=$query->amount;
        }
        return $response_array;
    }
    public function saveSend(Request $request) {
        
        $id = $request->post('id');
        $m_warehouse_id = $request->post('m_warehouse_id');
        $m_item_id = $request->post('m_item_id');
        $qty = $request->post('qty');
        $price = $request->post('price');
        $m_unit_id = $request->post('m_unit_id');
        $isSubmit = true;
        $inv_trx=null;
        if ($isSubmit) {
            $period_year = Carbon::now()->year;
            $period_month = Carbon::now()->month;
            $rabcon = new RabController();
            $inv_no = $rabcon->generateTransactionNo('INV_OUT', $period_year, $period_month, $this->site_id );
            //insert inv_trx
            try
            {
                $headers = [
                    'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                    'Accept'        => 'application/json',
                ];
                $client = new Client(['base_uri' => $this->base_api_url . '/inv/base/InvSale/'.$id]);
                $reqBody = [
                    'headers' => $headers,
                    'json' => [
                        'is_closed' => 1,
                        ]
                    ]; 
                    $response = $client->request('PUT', '', $reqBody); 
            } catch(RequestException $exception) {
                
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
                        'trx_type' => 'INV_SALE',
                        'inv_request_id' => null,
                        'no' => $inv_no,
                        'inv_trx_date' => Carbon::now()->toDateString(),
                        'site_id' => $this->site_id,
                        'is_entry' => false,
                        'inv_sale_id' => $id
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
            $total_penjualan=$total_hpp=$ppn=0;
            for ($i = 0; $i < count($m_item_id); $i++) {

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
                            'condition' => 1,
                            'value' => $price[$i] * $qty[$i],
                            'base_price'    => $price[$i]
                            ]
                        ]; 
                        $response = $client->request('POST', '', $reqBody); 
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
                                'amount' => $qty[$i],
                                'amount_in' => 0,
                                'amount_out' => $qty[$i],
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

                $cek_akun=DB::table('account_hpps')->where('m_item_id', $m_item_id[$i])->first();
                $item=DB::table('m_items')->where('id', $m_item_id[$i])->first();
                $akun_hpp=null;
                if($cek_akun == null){
                    // $cost_material=$this->getNoAkun(28);
                    // $no_cm=$this->explodeNoAkun($cost_material['no_akun_main']);
                    // $data_cm=array(
                    //     'no_akun'         => $no_cm[0].'.'.$no_cm[1].'.'.($cost_material['total'] + 1).'.'.$no_cm[3],
                    //     'nama_akun'       => 'HPP '.$item->name,
                    //     'id_main_akun'    => 28,
                    //     'level'           => 2,
                    //     'sifat_debit'     => 1,
                    //     'sifat_kredit'    => 0,   
                    //     'id_parent'       => $cost_material['id_parent'],
                    //     'turunan1'        => 28,
                    //     'turunan2'        => $cost_material['turunan2'],
                    //     'turunan3'        => $cost_material['turunan3'],
                    //     'turunan4'        => $cost_material['turunan4'], 
                    // );
                    
                    // $id_cp=$this->saveAccount($data_cm);
                    $id_cp=2631;
                    try
                    {
                        $headers = [
                                        'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                                        'Accept'        => 'application/json',
                        ];
                        $client = new Client(['base_uri' => $this->base_api_url . '/inv/base/AccountHpp']);
                        $reqBody = [
                            'headers' => $headers,
                            'json' => [
                                    'm_item_id' => $m_item_id[$i],
                                    'hpp_id' => $id_cp
                                ]
                            ]; 
                            $response = $client->request('POST', '', $reqBody); 
                            $body = $response->getBody();
                            $content = $body->getContents();
                            $response_array = json_decode($content,TRUE);
                            
                    } catch(RequestException $exception) {
                    }
                    $akun_hpp=$id_cp;
                }else{
                    $akun_hpp=$cek_akun->hpp_id;
                }
                $total_penjualan+=($qty[$i]*$price[$i]);
                if ($item->category == 'MATERIAL') {
                    $temp_journal[]=array(
                        'total' => ($qty[$i]*$price[$i]),
                        'm_warehouse_id' => $m_warehouse_id[$i],
                        'akun'      => $akun_hpp,
                        'type'      => 'material',
                    );
                }else{
                    $temp_journal[]=array(
                        'total' => ($qty[$i]*$price[$i]),
                        'm_warehouse_id' => $m_warehouse_id[$i],
                        'akun'      => $akun_hpp,
                        'type'      => 'spare part',
                    );
                }
            }
            $inv_sale=DB::table('inv_sales')->where('id', $id)->first();
            $input_jurnal=array(
                'inv_trx_id' => $inv_trx['id'],
                'inv_sale_id' => $id,
                'customer_id' => $inv_sale->customer_id,
                'data'  => $temp_journal,
                'user_id'   => $this->user_id,
                'deskripsi'     => 'Penjualan Material dari No '.$inv_no,
                'tgl'       => date('Y-m-d'),
                'location_id'   => $this->site_id
            );
            $this->journalPenjualan($input_jurnal);
            $notification = array(
                'message' => 'Success Penjualan Keluar',
                'alert-type' => 'success'
            );
        } else {
            $notification = array(
                'message' => 'Error, Stock cannot smaller than request',
                'alert-type' => 'error'
            );
        }
        
        return redirect('penjualan_keluar')->with($notification);
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
        $akun= array('no_akun'=>$data->no_akun,'id_main_akun'=>$data->id_main_akun, 'no_akun_main'=>$data2->no_akun, 'total'=>$data->total_akun, 'id_parent' => $data_d->id_parent, 'turunan1' => $data_d->turunan1, 'turunan2' => $data_d->turunan2, 'turunan3' => $data_d->turunan3, 'turunan4' => $data_d->turunan4);
        return $akun;
    }
    public function listPembayaranPenjualanKeluar()
    {
        return view('pages.inv.penjualan_keluar.paid_penjualan_keluar_list');
    }
    public function formPembayaranPenjualanKeluar()
    {
        $data=array(
            'list_bank' => DB::table('list_bank')->get()
        );
        return view('pages.inv.penjualan_keluar.paid_penjualan_keluar_form', $data);
    }
    public function getBill($id)
    {
        $query=DB::table('inv_sales')->where('customer_id', $id)->where('is_closed', true)->where('is_paid', false)->get();
        foreach ($query as $key => $value) {
            $dt=DB::table('inv_trxes as it')->join('inv_trx_ds as itd', 'it.id', 'itd.inv_trx_id')->where('inv_sale_id', $value->id)->select('itd.*')->get();
            $total=0;
            $paidless=$this->paidless($value->id);
            foreach ($dt as $row) {
                $amount=($row->amount * $row->base_price);
                $total+=$amount + ($value->is_without_ppn == false ? ($amount * 0.1) : 0);
            }
            $value->paid_amount=$paidless->amount;
            $value->total=$total;
        }
        $data=array(
            'data'  => $query
        );
        return $data;
    }
    private function paidless($id){
        $query=DB::table('paid_sell_item_ds')->where('inv_sale_id', $id)->select(DB::raw('COALESCE(SUM(amount), 0) as amount'))->first();
        return $query;
    }
    public function saveBill(Request $request) {
        
        $bill_id=$request->check_id;
        $total=$this->currency($request->total);
        $paid_more=$this->currency($request->paid_more);
        $paid_less=$this->currency($request->paid_less);
        $amount_bill=$request->amount;
        $total_all=$request->total_all;
        $pay_date=$request->pay_date;
        $notes=$request->notes;
        $get_bill_id=$request->bill_id;
        
        $period_year = date('Y');
        $period_month = date('m');
        $rabcon = new RabController();
        $bill_no = $rabcon->generateTransactionNo('PAID_SELL', $period_year, $period_month, $this->site_id );
        $paid_sell=null;
        try
        {
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . 'inv/base/PaidSellItem']);
            $reqBody = [
                'headers' => $headers,
                'json' => [
                    'customer_id' => $request->input('customer_id'),
                    'no' => $bill_no,
                    'amount' => $total,
                    'notes' => $notes,
                    'wop' => $request->input('wop'),
                    'bank_number' => $request->input('bank_number'),
                    'ref_code' => $request->input('ref_code'),
                    'id_bank' => $request->input('id_bank'),
                    'amount' => $total,
                    'paid_date' => $pay_date,
                    'site_id' => $this->site_id,
                ]
            ]; 
            
            $response = $client->request('POST', '', $reqBody); 
            $body = $response->getBody();
            $content = $body->getContents();
            $response_array = json_decode($content,TRUE);
            $paid_sell=$response_array['data'];
        } catch(RequestException $exception) {
        }
        
        $temp_total=$total;
        $temp_no_bill='';
        foreach ($bill_id as $key => $value) {
            foreach ($get_bill_id as $k => $v) {
                if ($value == $v) {
                    $amount=$amount_bill[$k];
                }
            }
            // $detail_bill=$this->getBillDetailCustomerJson($value);
            $cek_amount=$amount;
            if ($temp_total >= $amount) {
                $amount=($key == (count($bill_id)-1) ? $temp_total : $amount);
                $temp_total-=$amount;
            }else{
                $amount=$temp_total;
                $temp_total=0;
            }
            
            $detail_bill=DB::table('inv_sales')->where('id', $value)->first();
            $temp_no_bill.=$detail_bill->no.', ';
            
            try
            {
                $headers = [
                    'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                    'Accept'        => 'application/json',
                ];
                $client = new Client(['base_uri' => $this->base_api_url . 'inv/base/PaidSellItemD']);
                $reqBody = [
                    'headers' => $headers,
                    'json' => [
                        'paid_sell_item_id'  => $paid_sell['id'],
                        'inv_sale_id' => $value,
                        'amount' => $amount,
                    ]
                ]; 
                $response = $client->request('POST', '', $reqBody); 
                $body = $response->getBody();
                $content = $body->getContents();
                $response_array = json_decode($content,TRUE);
            } catch(RequestException $exception) {
            }
            if (($cek_amount - $amount) <= 10000) {
                DB::table('inv_sales')->where('id', $value)->update(array('is_paid' => true));
            }
        }
        // if ($request->wop == 'giro') {
        //     $giro_no = $this->generateTransactionNo('GIRO', $period_year, $period_month, $this->site_id );
        //     try
        //     {
        //         $headers = [
        //         'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
        //         'Accept'        => 'application/json',
        //     ];
        //     $client = new Client(['base_uri' => $this->base_api_url . 'inv/base/Giro']);
        //         $reqBody = [
        //             'headers' => $headers,
        //         'json' => [
        //                 // 'customer_bill_id' => $request->input('bill_id'),
        //                 // 'customer_bill_d_id' => $cust_bill_d['id'],
        //                 // 'order_id' => $cust_bill['order_id'],
        //                 'paid_customer_id'  => $paid_cust['id'],
        //                 'amount' => $total,
        //                 'pay_date' => null,
        //                 'site_id'   => $this->site_id,
        //                 'no'  => $giro_no
        //             ]
        //         ]; 
        //         $response = $client->request('POST', '', $reqBody); 
        //         $body = $response->getBody();
        //         $content = $body->getContents();
        //         $response_array = json_decode($content,TRUE);
        //     } catch(RequestException $exception) {
        //     }
        // }
        if (($total_all - $total) > 10000) {
            $total_all=$total;
            $paid_more=0;
            $paid_less=0;
        }
        $input_jurnal=array(
            'paid_sell_item_id'  => $paid_sell['id'],
            'customer_id' => $request->input('customer_id'),
            'bkm' => $request->input('bkm'),
            'total' => $total,
            'total_all' => $total_all,
            'paid_more' => $paid_more,
            'paid_less' => $paid_less,
            'user_id'   => $this->user_id,
            'akun'      => $request->input('wop') == 'giro' ? 36 : $request->account_payment,
            'lawan'      => 151,
            'deskripsi'     => 'Pembayaran Penjualan No '.$paid_sell['no'].' dari No Permintaan '.rtrim($temp_no_bill, ', '),
            'tgl'       => $pay_date,
            'location_id'   => $this->site_id
        );
        $this->journalPaidCustBill($input_jurnal);
        
        return redirect('penjualan_keluar/paid');
    }
    private function journalPaidCustBill($data){
        $data_trx=array(
            'deskripsi'     => $data['deskripsi'],
            'location_id'     => $data['location_id'],
            'tanggal'       => $data['tgl'],
            'user_id'       => $data['user_id'],
            'paid_sell_item_id'  => $data['paid_sell_item_id'],
            'customer_id'  => $data['customer_id'],
        );
        $insert=DB::table('tbl_trx_akuntansi')->insert($data_trx);
        if ($insert) {
            $id_last=DB::table('tbl_trx_akuntansi')->max('id_trx_akun');
            $no=$data['bkm'];
            if ($no == '') {
                $acccon = new AkuntanController();
                $no=$acccon->createNo($data['akun'], "DEBIT");
            }
            $akun=array(
                'id_trx_akun'   => $id_last,
                'id_akun'       => $data['akun'],
                'jumlah'        => $data['total'],
                'tipe'          => "DEBIT",
                'keterangan'    => 'akun',
                'no'            => ($data['akun'] != 36 ? $no : '')
            );
            DB::table('tbl_trx_akuntansi_detail')->insert($akun);
            if($data['akun'] == 24 || $data['akun'] == 101){
                $acccon = new AkuntanController();
                $warehouse_id=$data['akun'] == 24 ? 2 : 3;
                $acccon->countSaldoCash($this->site_id, $warehouse_id, "in", $data['total']);
            }
            $lawan=array(
                'id_trx_akun'   => $id_last,
                'id_akun'       => $data['lawan'],
                'jumlah'        => $data['total_all'],
                'tipe'          => "KREDIT",
                'keterangan'    => 'lawan',
            );
            DB::table('tbl_trx_akuntansi_detail')->insert($lawan);
            if ($data['paid_less'] > 0) {
                $paid_less=array(
                    'id_trx_akun'   => $id_last,
                    'id_akun'       => 165,
                    'jumlah'        => $data['paid_less'],
                    'tipe'          => "DEBIT",
                    'keterangan'    => 'lawan',
                );
                DB::table('tbl_trx_akuntansi_detail')->insert($paid_less);
            }
            if ($data['paid_more'] > 0) {
                $paid_more=array(
                    'id_trx_akun'   => $id_last,
                    'id_akun'       => 162,
                    'jumlah'        => $data['paid_more'],
                    'tipe'          => "KREDIT",
                    'keterangan'    => 'lawan',
                );
                DB::table('tbl_trx_akuntansi_detail')->insert($paid_more);
            }
        }
    }
    public function listPaidSellItem(){
        $query=DB::table('paid_sell_items')
                    ->select('paid_sell_items.*', 'customers.coorporate_name')        
                    ->join('customers', 'customers.id', 'paid_sell_items.customer_id')
                    ->where('site_id', $this->site_id)
                    ->get();
        foreach($query as $row){
            $dt=DB::table('paid_sell_item_ds')
                    ->join('inv_sales', 'inv_sales.id', 'paid_sell_item_ds.inv_sale_id')
                    ->where('paid_sell_item_id', $row->id)
                    ->select('inv_sales.no')->get();
            $no='';
            foreach($dt as $value){
                $no.=($value->no.', ');
            }
            $row->no_sale=rtrim($no, ', ');
        }
        $data=DataTables::of($query)->make(true);

        return $data;
    }
    public function listDetailPaidSellItem(){
        $query=DB::table('paid_sell_item_ds as pcs')
                ->join('paid_sell_items as pc', 'pc.id', 'pcs.paid_sell_item_id')
                ->join('inv_sales as is', 'is.id', 'pcs.inv_sale_id')
                ->join('customers as c', 'c.id', 'pc.customer_id')
                ->select('pc.no as paid_no', 'is.no as bill_no', 'c.coorporate_name', 'pc.paid_date', 'pcs.amount')
                ->get();
        $data=DataTables::of($query)
                    ->make(true);   
        return $data;       
    }
    public function listPaidSellItemD($id){
        $query=DB::table('paid_sell_item_ds')
                    ->select('inv_sales.*', 'paid_sell_item_ds.amount')
                    ->join('inv_sales', 'inv_sales.id', 'paid_sell_item_ds.inv_sale_id')
                    ->where('paid_sell_item_id', $id)
                    ->get();
        $data=array(
            'data'  => $query
        );
        return $data;
    }
    public function printSPPJB($id){
        $is_error = false;
        $error_message = '';  
        
        $query=DB::table('inv_sales')
                    ->join('customers', 'customers.id', '=', 'inv_sales.customer_id')
                    ->join('m_warehouses', 'm_warehouses.id', '=', 'inv_sales.m_warehouse_id')
                    ->where('inv_sales.id', $id)
                    ->select('inv_sales.*', 'customers.*', 'm_warehouses.name as warehouse_name')
                    ->first();
        $detail=DB::table('inv_sale_ds')
                    ->join('m_items', 'm_items.id', '=', 'inv_sale_ds.m_item_id')
                    ->join('m_units', 'm_units.id', '=', 'm_items.m_unit_id')
                    ->where('inv_sale_ds.inv_sale_id', $id)
                    ->select('inv_sale_ds.*', 'm_items.name as item_name', 'm_items.no as item_no', 'm_units.name as unit_name')
                    ->get();
        $data = array(
                'error' => array(
                    'is_error' => $is_error,
                    'error_message' => $error_message
                ),
                'id' => $id,
                'data'        => $query,
                'detail'        => $detail,
                'site_id'     => $this->site_id,
        );
        return view('pages.inv.penjualan_keluar.print_sppjb', $data);
    }
    public function printSuratJalan($id){
        $is_error = false;
        $error_message = '';  
        
        $query=DB::table('inv_sales')
                    ->leftJoin('customers', 'customers.id', '=', 'inv_sales.customer_id')
                    ->join('inv_trxes', 'inv_trxes.inv_sale_id', '=', 'inv_sales.id')
                    ->where('inv_sales.id', $id)
                    ->select('inv_sales.*', 'customers.*', 'inv_trxes.inv_trx_date', 'inv_trxes.no as inv_no')
                    ->first();
        $detail=DB::table('inv_trx_ds')
                    ->join('inv_trxes', 'inv_trxes.id', '=', 'inv_trx_ds.inv_trx_id')
                    ->join('m_items', 'm_items.id', '=', 'inv_trx_ds.m_item_id')
                    ->join('m_units', 'm_units.id', '=', 'm_items.m_unit_id')
                    ->where('inv_trxes.inv_sale_id', $id)
                    ->select('inv_trx_ds.*', 'm_items.name as item_name', 'm_items.no as item_no', 'm_units.name as unit_name')
                    ->get();
        $data = array(
                'error' => array(
                    'is_error' => $is_error,
                    'error_message' => $error_message
                ),
                'id' => $id,
                'data'        => $query,
                'detail'        => $detail,
                'site_id'     => $this->site_id,
        );
        return view('pages.inv.penjualan_keluar.print_surat_jalan', $data);
    }
}
