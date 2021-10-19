<?php

namespace App\Http\Controllers\INV;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use App\Exports\DebtListExport;
use App\Exports\PiutangListExport;
use App\Exports\HistoryPurchaseSupplierExport;
use App\Exports\AgeDebtSupplierExport;
use App\Exports\RingkasanUmurPiutangExport;
use App\Exports\SellCustomerExport;
use App\Exports\PiutangAllExport;
use App\Exports\DebtSupplierExport;
use App\Exports\StockInExport;
use App\Exports\StockInTaxExport;
use App\Exports\StockExport;
use App\Exports\StockPeriodicExport;
use App\Exports\ItemBuyExport;
use App\Exports\PaidSupplierExport;
use App\Exports\RecaptDebtExport;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;
use DB;
use Yajra\DataTables\Facades\DataTables;
use App\Http\Controllers\RAB\RabController;
use App\Http\Controllers\Accounting\AkuntanController;

class TransactionController extends Controller
{
    private $base_api_url;
    private $site_id = null;
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
        $data = array(
            'data' => array(),
            'date_gte' => null,
            'date_lte' => null,
            'm_item_id' => 'all',
            'is_entry' => 'all',
            'warehouse' => DB::table('m_warehouses')->where('site_id', $this->site_id)->get(),
        );
        
        return view('pages.inv.inventory_transaction.inventory_transaction_list', $data);
    }

    public function siteStockIndex() {
        $data=array(
            'm_warehouse_id'    => $this->m_warehouse_id
        );
        return view('pages.inv.inventory_transaction.site_stock_list', $data);
    }

    public function indexPost(Request $request) {
        $date_gte = $request->post('date_gte');
        $date_lte = $request->post('date_lte');
        $m_item_id = $request->post('m_item_id');
        $is_entry = $request->post('is_entry');
        $m_warehouse_id = $request->post('m_warehouse_id');
        $mutasi_stok = null;
        try
        {
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . 'inv/mutasi_stok']);
            $reqBody = [
                'headers' => $headers,
                'json' => [
                    'site_id' => $this->site_id,
                    'date_gte' => $date_gte,
                    'date_lte' => $date_lte,
                    'm_item_id' => $m_item_id,
                    'is_entry' => $is_entry,
                    'm_warehouse_id' => $m_warehouse_id,
                    ]
                ];

            $response = $client->request('POST', '', $reqBody);
            $body = $response->getBody();
            $content = $body->getContents();
            $response_array = json_decode($content,TRUE);
            $mutasi_stok = $response_array['data'];
        } catch(RequestException $exception) {
            return $exception->getMessage();
        }

        $data = array(
            'data' => $mutasi_stok,
            'date_gte' => $date_gte,
            'date_lte' => $date_lte,
            'm_item_id' => $m_item_id,
            'is_entry' => $is_entry,
            'warehouse' => DB::table('m_warehouses')->where('site_id', $this->site_id)->get(),
            'm_warehouse_id' => $m_warehouse_id,
        );

        return view('pages.inv.inventory_transaction.inventory_transaction_list', $data);
    }

    // function json
    public function getStok() {
        $response = null;
        // try
        // {
        //     if($this->site_id != null)
        //         $headers = [
        //         'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
        //         'Accept'        => 'application/json',
        //     ];
        //     $client = new Client(['base_uri' => $this->base_api_url . '/inv/stok/' . $this->site_id]);
        // //     else
        // //         $headers = [
        //         'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
        //         'Accept'        => 'application/json',
        //     ];
        //     $client = new Client(['base_uri' => $this->base_api_url . '/inv/stok']);
        //     $response = $client->request('GET', '', ['headers' => $headers]); 
        //     $body = $response->getBody();
        //     $content = $body->getContents();
        //     $response_array = json_decode($content,TRUE);

        //     $response = $content;
        // } catch(RequestException $exception) {

        // }
        $cek_stok=DB::table('stocks as s')
                            ->where('s.site_id', $this->site_id)
                            ->leftJoin('m_item_prices as mip', 's.m_item_id', 'mip.m_item_id')
                            ->join('m_items as mi', 's.m_item_id', 'mi.id')
                            ->whereNotIn('mi.category', ['ATK', 'ALAT KERJA'])
                            ->select('s.*', 's.amount as stok', DB::raw('mip.price * s.amount as value'), 'mip.price as last_price', 'mip.updated_at as last_update_in')
                            ->orderBy('mip.updated_at', 'DESC')
                            ->get();
        foreach ($cek_stok as $key => $value) {
            $value->m_items=DB::table('m_items')->where('id', $value->m_item_id)->first();
            $value->m_units=DB::table('m_units')->where('id', $value->m_unit_id)->first();
            $value->m_warehouse=DB::table('m_warehouses')->where('id', $value->m_warehouse_id)->first();
            $value->sites=DB::table('sites')->where('id', $value->site_id)->first();
            $value->last_update_out=null;
        }
        $response['data']=$cek_stok;
        return $response;
    }

    public function getWarehouse()
    {   
        $respose = [];
        $warehouses = DB::table('m_warehouses')->where('site_id', $this->site_id)->get();
        $response['data'] = $warehouses;
        return $response;
    }

    public function getStokSiteAll() {
        $response = null;
        $cek_stok=DB::table('stocks as s')
                            ->where('s.site_id', $this->site_id)
                            ->where('s.type', 'STK_NORMAL')
                            ->join('m_items as mi', 's.m_item_id', 'mi.id')
                            ->whereNotIn('mi.category', ['ATK', 'ALAT KERJA'])
                            ->join('m_item_prices as mip', 's.m_item_id', 'mip.m_item_id')
                            ->select(DB::raw('MAX(s.m_item_id) as m_item_id'), DB::raw('MAX(s.m_unit_id) as m_unit_id'), DB::raw('MAX(s.site_id) as site_id'), DB::raw('SUM(s.amount) as stok'), DB::raw('MAX(mip.price) as last_price'), DB::raw('MAX(s.updated_at) as last_update_in'))
                            ->groupBy('s.m_item_id')
                            ->get();
        foreach ($cek_stok as $key => $value) {
            $value->value=$value->stok;
            $value->m_items=DB::table('m_items')->where('id', $value->m_item_id)->first();
            $value->m_units=DB::table('m_units')->where('id', $value->m_unit_id)->first();
            $value->sites=DB::table('sites')->where('id', $value->site_id)->first();
            $value->last_update_out=null;
        }
        $response['data']=$cek_stok;
        return $response;
    }
    
    public function getStokRest() {
        $response = null;
        // try
        // {
        //     // if($this->site_id != null)
        //         $headers = [
        //         'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
        //         'Accept'        => 'application/json',
        //     ];
        //     $client = new Client(['base_uri' => $this->base_api_url . '/inv/stok_rest/' . $this->site_id]);
        // //     // else
        // //         // $headers = [
        //         'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
        //         'Accept'        => 'application/json',
        //     ];
        //     $client = new Client(['base_uri' => $this->base_api_url . '/inv/stok']);
        //     $response = $client->request('GET', '', ['headers' => $headers]); 
        //     $body = $response->getBody();
        //     $content = $body->getContents();
        //     $response_array = json_decode($content,TRUE);

        //     $response = $content;
        // } catch(RequestException $exception) {

        // }

        // return $response;
        $cek_stok=DB::table('stock_rests as s')
                            ->where('s.site_id', $this->site_id)
                            ->join('m_item_prices as mip', 's.m_item_id', 'mip.m_item_id')
                            ->select('s.*', 's.amount_pieces as amount_rest', 's.amount as stok', DB::raw('mip.price * s.amount as value'), 'mip.price as last_price', 's.updated_at as last_update_in')
                            ->orderBy('s.updated_at', 'DESC')
                            ->get();
        foreach ($cek_stok as $key => $value) {
            $value->m_items=DB::table('m_items')->where('id', $value->m_item_id)->first();
            $value->m_units=DB::table('m_units')->where('id', $value->m_unit_id)->first();
            $value->m_warehouse=DB::table('m_warehouses')->where('id', $value->m_warehouse_id)->first();
            $value->sites=DB::table('sites')->where('id', $value->site_id)->first();
            $value->last_update_out=null;
        }
        $response['data']=$cek_stok;
        return $response;
    }

    public function getPurchase(Request $request)
    {
        $is_error = false;
        $error_message = '';  
        $suppliers = null;
        try
        {
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . '/crm/base/MSupplier']);  
            $response = $client->request('GET', '', ['headers' => $headers]);  
            $body = $response->getBody();
            $content = $body->getContents();
            $response_array = json_decode($content,TRUE);
            $suppliers = $response_array['data'];
        } catch(RequestException $exception) {      
        }
        $date1=date('Y-m-d');
        $date2=date('Y-m-d');
        $supplier_selected=array();
        $all_supplier=false;
        if ($request->suppl_single) {
            $supplier=$request->suppl_single;
            $supplier_selected=$request->suppl_single;
            $all_supplier=false;
            foreach ($supplier as $key => $value) {
                if ($value == 'all') {
                    $all_supplier=true;
                }
            }
            if ($request->input('date')) {
                $date1=$request->input('date');
                $date2=$request->input('date2');
            }
        }
        $data = array(
            'error' => array(
                'is_error' => $is_error,
                'error_message' => $error_message
            ),
            'suppliers' => $suppliers,
            'date1'     => $date1,
            'date2'     => $date2,
            'supplier_selected' => $supplier_selected,
            'all_supplier'  => $all_supplier
        );

        return view('pages.inv.purchase_order.purchase_listr', $data);
    }
    
    public function exportPurchase(Request $request) {
        
        $date1=null;
        $date2=null;
        $m_supplier_id=null;
        if ($request->input('date')) {
            $date1=$request->input('date');
            $date2=$request->input('date2');
        }
        if ($request->input('m_supplier_id')) {
            // $supplier=explode(',', $request->input('m_supplier_id'));
            // $m_supplier_id=$supplier;
            foreach ($request->input('m_supplier_id') as $key => $value) {
                if ($value == 'all') {
                    $m_supplier_id=DB::table('m_suppliers')->pluck('id');
                }
            }
        }
        $query=DB::table('payment_suppliers')
                    ->join('m_suppliers', 'm_suppliers.id', 'payment_suppliers.m_supplier_id')
                    ->leftJoin('purchases', 'purchases.id', 'payment_suppliers.purchase_id')
                    ->leftJoin('purchase_assets', 'purchase_assets.id', 'payment_suppliers.purchase_asset_id')
                    ->leftJoin('purchase_services', 'purchase_services.id', 'payment_suppliers.purchase_service_id')
                    ->select('payment_suppliers.*', 'm_suppliers.name as supplier', 'purchases.no as purchase_no', 'purchase_assets.no as purchase_asset_no', 'purchase_services.no as purchase_service_no', 'purchases.purchase_date as purchase_date', 'purchase_assets.purchase_date as purchase_asset_date', 'purchase_services.purchase_date as purchase_service_date', 'purchases.ekspedisi', 'purchase_assets.ekspedisi as purchase_asset_ekspedisi', 'purchase_services.ekspedisi as purchase_service_ekspedisi');
        if($date1 != null){
            $query->where('create_date', '>=', $date1);
            $query->where('create_date', '<=', $date2);
        }
        if($m_supplier_id != null){
            $query->whereIn('payment_suppliers.m_supplier_id', $m_supplier_id);
        }
        $query=$query->get();
        foreach($query as $value){
            $pay_date=DB::table('payment_supplier_ds')->select('pay_date')->where('payment_supplier_id', $value->id)->orderBy('id', 'DESC')->first();
            $value->pay_date=$pay_date != null ? $pay_date->pay_date : '';
            $value->paid_for_week=$this->cekListPaidforWeek($value->due_date, $value->is_paid);
        }

        $data = [
            'data' => $query,
            'date1' => $date1,
            'date2' => $date2
        ];
        return view('exports.export_tagihan_supplier', ['data' => $data]);
    }
    
    public function getPurchaseJson()
    {
        $response = null;
        try
        {
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . '/inv/getPurchase?site_id='.$this->site_id]);  
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
        // return $response;
    }
    public function getPurchaseDetJson($id)
    {
        $response = null;
        try
        {
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . '/inv/getPurchase/detail/'.$id]);  
            $response = $client->request('GET', '', ['headers' => $headers]);  
            $body = $response->getBody();
            $content = $body->getContents();
            $response_array = json_decode($content,TRUE);

            $response = $content;         
        } catch(RequestException $exception) {
            
        }    

        return $response;
    }
    public function isClosed($id)
    {
        $update=DB::table('purchases')->where('id', $id)->update(
            [
                'is_closed' => true,
            ]
        );
        if($update){
            return redirect('inventory/purchase');
        }else{
            echo "gagal";
        }
    }
    public function listAccProduct() {
        return view('pages.inv.inventory_transaction.inventory_prod_order_list');
    }
    public function jsonAccProduct() {
        $query=DB::table('inv_orders')
                    ->join('rabs', 'rabs.id', '=', 'inv_orders.rab_id')
                    ->join('projects', 'projects.id', '=', 'inv_orders.project_id')
                    ->join('orders', 'orders.id', '=', 'inv_orders.order_id')
                    ->join('customers', 'orders.customer_id', '=', 'customers.id')
                    ->where('inv_orders.type', 'RECEIPT')
                    ->select('inv_orders.*', 'rabs.no as rab_no', 'orders.order_no', 'customers.coorporate_name', 'projects.name')
                    ->get();
        $data=DataTables::of($query)
                    ->make(true); 
        return $data;
    }
    public function jsonAccProductDetail($id) {
        $data['data']=DB::table('inv_order_ds')
                    ->join('product_subs', 'product_subs.id', '=', 'inv_order_ds.product_sub_id')
                    ->join('products', 'products.id', '=', 'product_subs.product_id')
                    ->select('inv_order_ds.*', 'inv_order_ds.no as prod_no', 'product_subs.*', 'products.name', 'products.price', 'products.item', 'products.series', 'products.panjang', 'products.lebar')
                    ->where('inv_order_id', $id)
                    ->get();
        return $data;
    }
    public function addAccProduct() {
        //basic variable
        $is_error = false;
        $error_message = '';

        try
        {
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client1 = new Client(['base_uri' => $this->base_api_url . 'order/list']); 
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
            'order_list' => $order_list
        );
        return view('pages.inv.inventory_transaction.inventory_prod_order_form', $data);
    }
    public function addAccProductPost(Request $request) {
        $product_sub_id=$request->input('product_sub_id');
        $check_prod_sub_id=$request->input('check_prod_sub_id');
        $storage_locations=$request->input('storage');
        
        $period_year = date('Y');
        $period_month = date('m');

        $get_rab=DB::table('rabs')->where('id', $request->input('rab_no'))->first();
        $inv_order_no = $this->generateTransactionNo('INV_ORD', $period_year, $period_month, $this->site_id );

        try
        {
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . 'inv/base/InvOrder']);
            $reqBody = [
                'headers' => $headers,
                'json' => [
                    'no'          => $inv_order_no,
                    'order_id'    => $request->input('order_id'),
                    'project_id'    => $get_rab->project_id,
                    'rab_id'    => $request->input('rab_no'),
                    'site_id'       => $this->site_id,
                    'is_entry'    => true
                ]
            ]; 
            $response = $client->request('POST', '', $reqBody); 
            $body = $response->getBody();
            $content = $body->getContents();
            $response_array = json_decode($content,TRUE);
            $inv_order=$response_array['data'];
        } catch(RequestException $exception) {
        } 


        foreach ($product_sub_id as $key => $value) {
            $cek=in_array($value, $check_prod_sub_id);
            if ($cek == true) {
                $product_sub=DB::table('product_subs')->where('id', $value)->first();
                try
                {
                    $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . 'inv/base/InvOrderD']);
                    $reqBody = [
                        'headers' => $headers,
                'json' => [
                            'inv_order_id'    => $inv_order['id'],
                            'product_sub_id'    => $value,
                            'order_d_id'    => $product_sub->order_d_id,
                            'storage_locations' => $storage_locations[$key]
                        ]
                    ]; 
                    $response = $client->request('POST', '', $reqBody);
                } catch(RequestException $exception) {
                }
            }
        }
        
        return redirect('inventory/acc_product');
        
    }
    public function getProductSubByRab($id){
        $query['data']=DB::table('rabs')
                    ->select('product_subs.*')
                    ->join('product_subs', 'product_subs.order_d_id', '=', 'rabs.order_d_id')
                    ->where('rabs.id', $id)
                    ->whereRaw('product_subs.id not in (select product_sub_id from inv_order_ds)')
                    ->orderBy('product_subs.id')
                    ->get();
        return $query;
    }
    private function generateTransactionNo($trasaction_code, $period_year, $period_month, $site_id){
        try
        {
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . '/master/m_sequence/generate_trx_no']);
            $reqBody = [
                'headers' => $headers,
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
    public function calcStock(){
        $this_month=date('Y-m');
        $date_before=date('Y-m', strtotime("- 1 months",  strtotime($this_month)));
        $id=$this->site_id;
        $datas = DB::select("
                select 
                    (COALESCE(inv_in.site_id, inv_out.site_id)) as site_id, 
                    (COALESCE(inv_in.m_item_id, inv_out.m_item_id)) as m_item_id,
                    (COALESCE(inv_in.m_unit_id, inv_out.m_unit_id)) as m_unit_id,
                    (COALESCE(inv_in.m_warehouse_id, inv_out.m_warehouse_id)) as m_warehouse_id, 
                    (COALESCE(inv_in.amount, 0)) as amount_in,
                    (COALESCE(inv_out.amount, 0) - COALESCE(inv_out.amount_ret, 0)) as amount_out,
                    (COALESCE(inv_out.amount_ret, 0)) as amount_ret,
                    ((COALESCE(inv_in.amount, 0) + COALESCE(inv_out.amount_ret, 0)) - (COALESCE(inv_out.amount, 0))) as stok,
                    inv_in.updated_at as last_update_in,
                    inv_out.updated_at as last_update_out
                from (select site_id, m_item_id, max(m_unit_id) as m_unit_id, sum(amount) as amount, itd.m_warehouse_id as m_warehouse_id, max(it.updated_at) as updated_at from inv_trxes it
                join inv_trx_ds itd on it.id = itd.inv_trx_id
                where is_entry = true and site_id = ".$id." and trx_type != 'RET_ITEM' and trx_type != 'TRF_STK' and inv_trx_date::text like '%".$this_month."%' and itd.condition = 1 and itd.type_material != 'TRF_STK'
                group by site_id, m_item_id, itd.m_warehouse_id) inv_in
                full outer join (select site_id, m_item_id, max(m_unit_id) as m_unit_id, itd.m_warehouse_id, coalesce((SELECT sum(amount) as amount from inv_trxes it1
                join inv_trx_ds itd1 on it1.id = itd1.inv_trx_id
                where trx_type = 'RET_ITEM' and it1.site_id=it.site_id and itd1.m_item_id=itd.m_item_id and itd1.m_warehouse_id=itd.m_warehouse_id and itd1.condition = 1 and itd1.type_material != 'TRF_STK'), 0) AS amount_ret, sum(amount) as amount, max(it.updated_at) as updated_at from inv_trxes it
                join inv_trx_ds itd on it.id = itd.inv_trx_id
                where is_entry = false and site_id = ".$id." and trx_type != 'TRF_STK' and inv_trx_date::text like '%".$this_month."%' and itd.condition = 1 and itd.type_material != 'TRF_STK'
                group by site_id, m_item_id, itd.m_warehouse_id) inv_out on inv_in.m_item_id = inv_out.m_item_id and inv_in.site_id = inv_out.site_id  and inv_in.m_warehouse_id = inv_out.m_warehouse_id
                ");
                
        $temp_id=[];
        foreach ($datas as $key => $value) {
            $query=DB::table('calculate_stocks')
                        ->where('site_id', $value->site_id)
                        ->where('m_warehouse_id', $value->m_warehouse_id)
                        ->where('m_item_id', $value->m_item_id)
                        ->where('last_month', $date_before)
                        ->where('type', 'STK_NORMAL')
                        ->first();
            $cek_this_month=DB::table('calculate_stocks')
                        ->where('site_id', $value->site_id)
                        ->where('m_warehouse_id', $value->m_warehouse_id)
                        ->where('m_item_id', $value->m_item_id)
                        ->where('last_month', $this_month)
                        ->where('type', 'STK_NORMAL')
                        ->first();
            if($query != null){
                array_push($temp_id, $query->id);
            }
            
            $get_save_price=DB::table('m_item_prices')->where(['m_item_id' => $value->m_item_id, 'm_unit_id' => $value->m_unit_id, 'site_id' => $value->site_id])->first();
            if ($cek_this_month == null) {
                try
                {
                    $headers = [
                        'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                        'Accept'        => 'application/json',
                    ];
                    $client = new Client(['base_uri' => $this->base_api_url . '/inv/base/CalculateStock']);
                    $reqBody = [
                        'headers' => $headers,
                        'json' => [
                            'site_id' => $value->site_id,
                            'm_item_id' => $value->m_item_id,
                            'amount' => ($query != null ? $query->amount : 0) + $value->stok,
                            'amount_in' => ($query != null ? $query->amount_in : 0) + $value->amount_in,
                            'amount_out' => ($query != null ? $query->amount_out : 0) + $value->amount_out,
                            'm_unit_id' => $value->m_unit_id,
                            'm_warehouse_id' => $value->m_warehouse_id,
                            'type'  => 'STK_NORMAL',
                            'last_month'    => $this_month,
                            'price'     => $get_save_price->price
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
                    $client = new Client(['base_uri' => $this->base_api_url . '/inv/base/CalculateStock/'.$cek_this_month->id]);
                    $reqBody = [
                        'headers' => $headers,
                        'json' => [
                            'amount' => ($query != null ? $query->amount : 0) + $value->stok,
                            'amount_in' => ($query != null ? $query->amount_in : 0) + $value->amount_in,
                            'amount_out' => ($query != null ? $query->amount_out : 0) + $value->amount_out,
                            'price'     => $get_save_price->price
                            ]
                        ]; 
                        $response = $client->request('PUT', '', $reqBody); 
                        $body = $response->getBody();
                        $content = $body->getContents();
                        $response_array = json_decode($content,TRUE);
                } catch(RequestException $exception) {
                }
            }
            
            $cek_stok=DB::table('stocks')
                            ->where('site_id', $value->site_id)
                            ->where('m_warehouse_id', $value->m_warehouse_id)
                            ->where('m_item_id', $value->m_item_id)
                            ->where('type', 'STK_NORMAL')
                            ->first();
            $cek_stok_calculate=DB::table('calculate_stocks')
                            ->where('site_id', $value->site_id)
                            ->where('m_warehouse_id', $value->m_warehouse_id)
                            ->where('m_item_id', $value->m_item_id)
                            ->where('last_month', $date_before)
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
                            'site_id' => $value->site_id,
                            'm_item_id' => $value->m_item_id,
                            'amount' => $value->stok,
                            'amount_in' => $value->amount_in,
                            'amount_out' => $value->amount_out,
                            'm_unit_id' => $value->m_unit_id,
                            'm_warehouse_id' => $value->m_warehouse_id,
                            'type'  => 'STK_NORMAL',
                            ]
                        ]; 
                        $response = $client->request('POST', '', $reqBody); 
                        $body = $response->getBody();
                        $content = $body->getContents();
                        $response_array = json_decode($content,TRUE);
                } catch(RequestException $exception) {
                }
            }else{
                $update_data=array(
                    'amount' => ($cek_stok_calculate != null ? $cek_stok_calculate->amount : 0) + $value->stok,
                    'amount_in' => ($cek_stok_calculate != null ? $cek_stok_calculate->amount_in : 0) + $value->amount_in,
                    'amount_out' => ($cek_stok_calculate != null ? $cek_stok_calculate->amount_out : 0) + $value->amount_out,
                );
                DB::table('stocks')->where('id', $cek_stok->id)->update($update_data);
                // try
                // {
                //     $headers = [
            //     'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
            //     'Accept'        => 'application/json',
            // ];
            // $client = new Client(['base_uri' => $this->base_api_url . '/inv/base/Stock/'.$cek_stok->id]);
            //     //     $reqBody = [
            //     //         'headers' => $headers,
            //     'json' => [
                //             'amount' => ($cek_stok_calculate != null ? $cek_stok_calculate->amount : 0) + $value->stok,
                //             'amount_in' => ($cek_stok_calculate != null ? $cek_stok_calculate->amount_in : 0) + $value->amount_in,
                //             'amount_out' => ($cek_stok_calculate != null ? $cek_stok_calculate->amount_out : 0) + $value->amount_out,
                //             ]
                //         ]; 
                //         $response = $client->request('PUT', '', $reqBody); 
                //         $body = $response->getBody();
                //         $content = $body->getContents();
                //         $response_array = json_decode($content,TRUE);
                // } catch(RequestException $exception) {
                // }
            }

        }
        
        $query_not_in=DB::table('calculate_stocks')
                        ->where('last_month', $date_before)
                        ->whereNotIn('id', $temp_id)
                        ->where('type', 'STK_NORMAL')
                        ->get();
        foreach ($query_not_in as $key => $value) {
            $get_save_price=DB::table('m_item_prices')->where(['m_item_id' => $value->m_item_id, 'm_unit_id' => $value->m_unit_id, 'site_id' => $value->site_id])->first();

            $cek_this_month=DB::table('calculate_stocks')
                        ->where('site_id', $value->site_id)
                        ->where('m_warehouse_id', $value->m_warehouse_id)
                        ->where('m_item_id', $value->m_item_id)
                        ->where('last_month', $this_month)
                        ->where('type', 'STK_NORMAL')
                        ->first();
            if ($cek_this_month == null) {
                try
                {
                    $headers = [
                        'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                        'Accept'        => 'application/json',
                    ];
                    $client = new Client(['base_uri' => $this->base_api_url . '/inv/base/CalculateStock']);
                    $reqBody = [
                        'headers' => $headers,
                        'json' => [
                            'site_id' => $value->site_id,
                            'm_item_id' => $value->m_item_id,
                            'amount' => $value->amount,
                            'amount_in' => $value->amount_in,
                            'amount_out' => $value->amount_out,
                            'm_unit_id' => $value->m_unit_id,
                            'm_warehouse_id' => $value->m_warehouse_id,
                            'type'  => 'STK_NORMAL',
                            'last_month'    => $this_month,
                            'price'     => $get_save_price->price
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
                    $client = new Client(['base_uri' => $this->base_api_url . '/inv/base/CalculateStock/'.$cek_this_month->id]);
                    $reqBody = [
                        'headers' => $headers,
                        'json' => [
                            'amount' => $value->amount,
                            'amount_in' => $value->amount_in,
                            'amount_out' => $value->amount_out,
                            'price'     => $get_save_price->price
                            ]
                        ]; 
                        $response = $client->request('PUT', '', $reqBody); 
                        $body = $response->getBody();
                        $content = $body->getContents();
                        $response_array = json_decode($content,TRUE);
                } catch(RequestException $exception) {
                }
            }
            $cek_stok=DB::table('stocks')
                            ->where('site_id', $value->site_id)
                            ->where('m_warehouse_id', $value->m_warehouse_id)
                            ->where('m_item_id', $value->m_item_id)
                            ->where('type', 'STK_NORMAL')
                            ->first();
            $update_data=array(
                'amount' => $value->amount,
                'amount_in' => $value->amount_in,
                'amount_out' => $value->amount_out
            );
            DB::table('stocks')->where('id', $cek_stok->id)->update($update_data);
        }
        
        $this->calcStockTrf();
        return redirect('inventory/stock');
    }
    public function calcStockTrf(){
        $this_month=date('Y-m');
        $date_before=date('Y-m', strtotime("- 1 months",  strtotime($this_month)));
        $id=$this->site_id;
        $datas = DB::select("
                select 
                    (COALESCE(inv_in.site_id, inv_out.site_id)) as site_id, 
                    (COALESCE(inv_in.m_item_id, inv_out.m_item_id)) as m_item_id,
                    (COALESCE(inv_in.m_unit_id, inv_out.m_unit_id)) as m_unit_id,
                    (COALESCE(inv_in.m_warehouse_id, inv_out.m_warehouse_id)) as m_warehouse_id, 
                    (COALESCE(inv_in.amount, 0)) as amount_in,
                    (COALESCE(inv_out.amount, 0) - COALESCE(inv_out.amount_ret, 0)) as amount_out,
                    (COALESCE(inv_out.amount_ret, 0)) as amount_ret,
                    ((COALESCE(inv_in.amount, 0) + COALESCE(inv_out.amount_ret, 0)) - (COALESCE(inv_out.amount, 0))) as stok,
                    inv_in.updated_at as last_update_in,
                    inv_out.updated_at as last_update_out
                from (select site_id, m_item_id, max(m_unit_id) as m_unit_id, sum(amount) as amount, itd.m_warehouse_id as m_warehouse_id, max(it.updated_at) as updated_at from inv_trxes it
                join inv_trx_ds itd on it.id = itd.inv_trx_id
                where is_entry = true and site_id = ".$id." and trx_type != 'RET_ITEM' and itd.created_at::text like '%".$this_month."%' and itd.condition = 1 and itd.type_material = 'TRF_STK'
                group by site_id, m_item_id, itd.m_warehouse_id) inv_in
                full outer join (select site_id, m_item_id, max(m_unit_id) as m_unit_id, itd.m_warehouse_id, coalesce((SELECT sum(amount) as amount from inv_trxes it1
                join inv_trx_ds itd1 on it1.id = itd1.inv_trx_id
                where trx_type = 'RET_ITEM' and it1.site_id=it.site_id and itd1.m_item_id=itd.m_item_id and itd1.m_warehouse_id=itd.m_warehouse_id and itd1.condition = 1 and itd1.type_material = 'TRF_STK'), 0) AS amount_ret, sum(amount) as amount, max(it.updated_at) as updated_at from inv_trxes it
                join inv_trx_ds itd on it.id = itd.inv_trx_id
                where is_entry = false and site_id = ".$id." and itd.created_at::text like '%".$this_month."%' and itd.condition = 1 and itd.type_material = 'TRF_STK'
                group by site_id, m_item_id, itd.m_warehouse_id) inv_out on inv_in.m_item_id = inv_out.m_item_id and inv_in.site_id = inv_out.site_id  and inv_in.m_warehouse_id = inv_out.m_warehouse_id
                ");
        $temp_id=[];
        foreach ($datas as $key => $value) {
            $query=DB::table('calculate_stocks')
                        ->where('site_id', $value->site_id)
                        ->where('m_warehouse_id', $value->m_warehouse_id)
                        ->where('m_item_id', $value->m_item_id)
                        ->where('last_month', $date_before)
                        ->where('type', 'TRF_STK')
                        ->first();
            $cek_this_month=DB::table('calculate_stocks')
                        ->where('site_id', $value->site_id)
                        ->where('m_warehouse_id', $value->m_warehouse_id)
                        ->where('m_item_id', $value->m_item_id)
                        ->where('last_month', $this_month)
                        ->where('type', 'TRF_STK')
                        ->first();
            if($query != null){
                array_push($temp_id, $query->id);
            }
            if ($cek_this_month == null) {
                try
                {
                    $headers = [
                        'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                        'Accept'        => 'application/json',
                    ];
                    $client = new Client(['base_uri' => $this->base_api_url . '/inv/base/CalculateStock']);
                    $reqBody = [
                        'headers' => $headers,
                        'json' => [
                            'site_id' => $value->site_id,
                            'm_item_id' => $value->m_item_id,
                            'amount' => $value->stok,
                            'amount_in' => $value->amount_in,
                            'amount_out' => $value->amount_out,
                            'm_unit_id' => $value->m_unit_id,
                            'm_warehouse_id' => $value->m_warehouse_id,
                            'type'  => 'TRF_STK',
                            'last_month'    => $this_month,
                            'price'     => 0,
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
                    $client = new Client(['base_uri' => $this->base_api_url . '/inv/base/CalculateStock/'.$cek_this_month->id]);
                    $reqBody = [
                        'headers' => $headers,
                        'json' => [
                            'amount' => ($query != null ? $query->amount : 0) + $value->stok,
                            'amount_in' => ($query != null ? $query->amount_in : 0) + $value->amount_in,
                            'amount_out' => ($query != null ? $query->amount_out : 0) + $value->amount_out,
                            'price'     => 0,
                            ]
                        ]; 
                        $response = $client->request('PUT', '', $reqBody); 
                        $body = $response->getBody();
                        $content = $body->getContents();
                        $response_array = json_decode($content,TRUE);
                } catch(RequestException $exception) {
                }
            }
            $cek_stok=DB::table('stocks')
                            ->where('site_id', $value->site_id)
                            ->where('m_warehouse_id', $value->m_warehouse_id)
                            ->where('m_item_id', $value->m_item_id)
                            ->where('type', 'TRF_STK')
                            ->first();
            $cek_stok_calculate=DB::table('calculate_stocks')
                            ->where('site_id', $value->site_id)
                            ->where('m_warehouse_id', $value->m_warehouse_id)
                            ->where('m_item_id', $value->m_item_id)
                            ->where('last_month', $date_before)
                            ->where('type', 'TRF_STK')
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
                            'site_id' => $value->site_id,
                            'm_item_id' => $value->m_item_id,
                            'amount' => $value->stok,
                            'amount_in' => $value->amount_in,
                            'amount_out' => $value->amount_out,
                            'm_unit_id' => $value->m_unit_id,
                            'm_warehouse_id' => $value->m_warehouse_id,
                            'type'  => 'TRF_STK',
                            ]
                        ]; 
                        $response = $client->request('POST', '', $reqBody); 
                        $body = $response->getBody();
                        $content = $body->getContents();
                        $response_array = json_decode($content,TRUE);
                } catch(RequestException $exception) {
                }
            }else{
                $update_data=array(
                    'amount' => ($cek_stok_calculate != null ? $cek_stok_calculate->amount : 0) + $value->stok,
                    'amount_in' => ($cek_stok_calculate != null ? $cek_stok_calculate->amount_in : 0) + $value->amount_in,
                    'amount_out' => ($cek_stok_calculate != null ? $cek_stok_calculate->amount_out : 0) + $value->amount_out,
                );
                DB::table('stocks')->where('id', $cek_stok->id)->update($update_data);
                // try
                // {
                //     $headers = [
            //     'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
            //     'Accept'        => 'application/json',
            // ];
            // $client = new Client(['base_uri' => $this->base_api_url . '/inv/base/Stock/'.$cek_stok->id]);
            //     //     $reqBody = [
            //     //         'headers' => $headers,
            //     'json' => [
                //             'amount' => ($cek_stok_calculate != null ? $cek_stok_calculate->amount : 0) + $value->stok,
                //             'amount_in' => ($cek_stok_calculate != null ? $cek_stok_calculate->amount_in : 0) + $value->amount_in,
                //             'amount_out' => ($cek_stok_calculate != null ? $cek_stok_calculate->amount_out : 0) + $value->amount_out,
                //             ]
                //         ]; 
                //         $response = $client->request('PUT', '', $reqBody); 
                //         $body = $response->getBody();
                //         $content = $body->getContents();
                //         $response_array = json_decode($content,TRUE);
                // } catch(RequestException $exception) {
                // }
            }
        }
        $query_not_in=DB::table('calculate_stocks')
                        ->where('last_month', $date_before)
                        ->whereNotIn('id', $temp_id)
                        ->where('type', 'TRF_STK')
                        ->get();
        foreach ($query_not_in as $key => $value) {
            $cek_this_month=DB::table('calculate_stocks')
                        ->where('site_id', $value->site_id)
                        ->where('m_warehouse_id', $value->m_warehouse_id)
                        ->where('m_item_id', $value->m_item_id)
                        ->where('last_month', $this_month)
                        ->where('type', 'TRF_STK')
                        ->first();
            if ($cek_this_month == null) {
                try
                {
                    $headers = [
                        'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                        'Accept'        => 'application/json',
                    ];
                    $client = new Client(['base_uri' => $this->base_api_url . '/inv/base/CalculateStock']);
                    $reqBody = [
                        'headers' => $headers,
                        'json' => [
                            'site_id' => $value->site_id,
                            'm_item_id' => $value->m_item_id,
                            'amount' => $value->amount,
                            'amount_in' => $value->amount_in,
                            'amount_out' => $value->amount_out,
                            'm_unit_id' => $value->m_unit_id,
                            'm_warehouse_id' => $value->m_warehouse_id,
                            'type'  => 'TRF_STK',
                            'last_month'    => $this_month,
                            'price'     => 0
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
                    $client = new Client(['base_uri' => $this->base_api_url . '/inv/base/CalculateStock/'.$cek_this_month->id]);
                    $reqBody = [
                        'headers' => $headers,
                        'json' => [
                            'amount' => $value->amount,
                            'amount_in' => $value->amount_in,
                            'amount_out' => $value->amount_out,
                            'price'     => 0
                            ]
                        ]; 
                        $response = $client->request('PUT', '', $reqBody); 
                        $body = $response->getBody();
                        $content = $body->getContents();
                        $response_array = json_decode($content,TRUE);
                } catch(RequestException $exception) {
                }
            }
            $cek_stok=DB::table('stocks')
                            ->where('site_id', $value->site_id)
                            ->where('m_warehouse_id', $value->m_warehouse_id)
                            ->where('m_item_id', $value->m_item_id)
                            ->where('type', 'TRF_STK')
                            ->first();
            $update_data=array(
                'amount' => $value->amount,
                'amount_in' => $value->amount_in,
                'amount_out' => $value->amount_out,
                'price'     => 0
            );
            DB::table('stocks')->where('id', $cek_stok->id)->update($update_data);
        }
    }
    public function formBillSupplier()
    {
        $is_error = false;
        $error_message = '';  
        $list_bank=DB::table('list_bank')->get();
        $data = array(
            'error' => array(
                'is_error' => $is_error,
                'error_message' => $error_message
            ),
            'list_bank'     => $list_bank
        );

        return view('pages.inv.purchase_order.form_bill_supplier', $data);
    }
    public function suggestPurchaseJson(Request $request)
    {
        $data=array();
        if($request->has('q')){
            $key=$request->q;
            $query=DB::table('purchases')
                        ->select('purchases.*', 'purchases.no as text', DB::raw('COALESCE((SELECT SUM(delivery_fee) from payment_suppliers where purchase_id=purchases.id), 0) as delivery_fee_used'))
                        ->where('acc_ao', true)
                        ->where('status_payment', false)
                        ->where('purchases.no', 'like', '%'.$key.'%');
            $data=$query->limit(15)->get();
        }
        return $data;
    }
    public function getInvByPurchaseId($id)
    {
        $cek_inv_id=DB::table('payment_suppliers')
                        ->where('payment_suppliers.purchase_id', $id)
                        ->pluck('inv_id');
        $query['data']=DB::table('inv_trxes')->where('purchase_id', $id)->whereNotIn('id', $cek_inv_id)->select('id', 'no')->get();
        return $query;
    }
    public function getTotalInvByPurchaseId($id, $purchase_id)
    {
        $query=DB::table('inv_trx_ds')
                            ->where('inv_trx_id', $id)
                            ->select('m_item_id', DB::raw('CAST(SUM(amount) * (select base_price from purchase_ds where m_item_id = inv_trx_ds.m_item_id and purchase_id = '.$purchase_id.') AS int) as total'))
                            ->groupBy('m_item_id')
                            ->get();
        $total=0;
        foreach ($query as $key => $value) {
            $total+=$value->total;
        }
        return $total;
    }
    public function suggestPurchaseAssetJson(Request $request)
    {
        $data=array();
        if($request->has('q')){
            $key=$request->q;
            $query=DB::table('purchase_assets')
                        ->select('purchase_assets.*', 'purchase_assets.no as text', DB::raw('COALESCE((SELECT SUM(delivery_fee) from payment_suppliers where purchase_asset_id=purchase_assets.id), 0) as delivery_fee_used'))
                        ->where('acc_ao', true)
                        ->where('status_payment', false)
                        ->where('purchase_assets.no', 'like', '%'.$key.'%');
            $data=$query->limit(15)->get();
        }
        return $data;
    }
    public function getInvByPurchaseAssetId($id)
    {
        $cek_inv_id=DB::table('payment_suppliers')
                        ->where('payment_suppliers.purchase_asset_id', $id)
                        ->pluck('inv_id');
        $query['data']=DB::table('inv_trxes')->where('purchase_asset_id', $id)->whereNotIn('id', $cek_inv_id)->select('id', 'no')->get();
        return $query;
    }
    public function getTotalInvByPurchaseAssetId($id, $purchase_id)
    {
        $query=DB::table('inv_trx_ds')
                            ->where('inv_trx_id', $id)
                            ->select('m_item_id', DB::raw('CAST(SUM(amount) * (select base_price from purchase_asset_ds where m_item_id = inv_trx_ds.m_item_id and purchase_asset_id = '.$purchase_id.') AS int) as total'))
                            ->groupBy('m_item_id')
                            ->get();
        $total=0;
        foreach ($query as $key => $value) {
            $total+=$value->total;
        }
        return $total;
    }
    public function saveBillSupplier(Request $request)
    {
        // dd($request->all());
        // $purchase_id=$request->input('purchase_id');
        // $purchase_asset_id=$request->input('purchase_asset_id');
        // $purchase_service_id=$request->input('purchase_service_id');
        $type_po=$request->input('type_po');
        $no_surat_jalan=$request->input('no_surat_jalan');
        $no_surat_jalan_jasa=$request->input('no_surat_jalan_jasa');
        // $inv_id=$request->input('inv_id');
        // $inv_trx_service_id=$request->input('inv_trx_service_id');
        $description=$request->input('description');
        $due_date=$request->input('due_date');
        $total=$this->currency($request->input('total'));
        $amount_tagihan=$request->input('amount_tagihan');
        $delivery_fee=$request->input('delivery_fee');
        $total_purchase=$request->input('total_purchase');
        $payment_po=$request->input('payment_po');
        
        $is_paid=false;
        
        // if ($payment_po == 'cash') {
        //     $is_paid=($total == $total_purchase ? true : false);
        //     if ($type_po == 1) {
        //         try
        //         {
        //             $headers = [
        //                 'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
        //                 'Accept'        => 'application/json',
        //             ];
        //             $client = new Client(['base_uri' => $this->base_api_url . 'inv/base/Purchase/'.$purchase_id]);
        //             $reqBody = [
        //                 'headers' => $headers,
        //                 'json' => [
        //                     'status_payment'   => true
        //                 ]
        //             ]; 
                    
        //             $response = $client->request('PUT', '', $reqBody); 
        //             $body = $response->getBody();
        //             $content = $body->getContents();
        //             $response_array = json_decode($content,TRUE);
        //         } catch(RequestException $exception) {
        //         }
        //     }else if ($type_po == 0) {
        //         try
        //         {
        //             $headers = [
        //                 'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
        //                 'Accept'        => 'application/json',
        //             ];
        //             $client = new Client(['base_uri' => $this->base_api_url . 'inv/base/PurchaseAsset/'.$purchase_asset_id]);
        //             $reqBody = [
        //                 'headers' => $headers,
        //                 'json' => [
        //                     'status_payment'   => true
        //                 ]
        //             ]; 
                    
        //             $response = $client->request('PUT', '', $reqBody); 
        //             $body = $response->getBody();
        //             $content = $body->getContents();
        //             $response_array = json_decode($content,TRUE);
        //         } catch(RequestException $exception) {
        //         }
        //     }else{
        //         try
        //         {
        //             $headers = [
        //                 'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
        //                 'Accept'        => 'application/json',
        //             ];
        //             $client = new Client(['base_uri' => $this->base_api_url . 'inv/base/PurchaseService/'.$purchase_service_id]);
        //             $reqBody = [
        //                 'headers' => $headers,
        //                 'json' => [
        //                     'status_payment'   => true
        //                 ]
        //             ]; 
                    
        //             $response = $client->request('PUT', '', $reqBody); 
        //             $body = $response->getBody();
        //             $content = $body->getContents();
        //             $response_array = json_decode($content,TRUE);
        //         } catch(RequestException $exception) {
        //         }
        //     }
        // }
        $period_year = date('Y');
        $period_month = date('m');
        $rabcon = new RabController();
        $bill_no = $rabcon->generateTransactionNo('PAID_SPPL', $period_year, $period_month, $this->site_id );
        try
        {
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . 'inv/base/PaymentSupplier']);
            $reqBody = [
                'headers' => $headers,
                'json' => [
                    // 'purchase_id' => $type_po == 1 ? $purchase_id : 0,
                    // 'purchase_asset_id' => $type_po == 0 ? $purchase_asset_id : 0,
                    // 'purchase_service_id' => $type_po == 2 ? $purchase_service_id : 0,
                    // 'inv_id' => $inv_id,
                    // 'inv_trx_service_id' => $inv_trx_service_id,
                    'no_surat_jalan'    => $no_surat_jalan,
                    'no_surat_jalan_jasa'    => $no_surat_jalan_jasa,
                    'amount' => $total,
                    'delivery_fee' => $delivery_fee,
                    'due_date' => $request->input('due_date'),
                    'create_date' => $request->input('date_create'),
                    'paid_no' => $request->input('paid_no'),
                    'bill_no' => $request->input('bill_no'),
                    'no'  => $bill_no,
                    'is_paid'   => false,
                    'user_id'   => $this->user_id,
                    'm_supplier_id' => $request->input('m_supplier_id'),
                    'payment_po'   => 'credit',
                    'site_id'   => $this->site_id,
                    'amount_tagihan'   => $amount_tagihan
                ]
            ]; 
            
            $response = $client->request('POST', '', $reqBody); 
            $body = $response->getBody();
            $content = $body->getContents();
            $response_array = json_decode($content,TRUE);
            $payment_supplier=$response_array['data'];

            if($total != $amount_tagihan){
                $getNoPnm=DB::table('inv_trxes')
                        ->where('inv_trxes.no_surat_jalan', $no_surat_jalan)
                        ->pluck('no')->toArray();
                $noPnm = implode(', ', $getNoPnm);
                $input_tbl_trx_akuntansi=array(
                    'user_id'   => $this->user_id,
                    'deskripsi'     => 'Penyesuaian Tagihan terhadap persediaan No ' . $noPnm,
                    'tanggal'       => $request->input('date_create'),
                    'location_id'   => $this->site_id,
                    'm_supplier_id' => $request->input('m_supplier_id'),
                    'payment_supplier_id' => $payment_supplier['id']
                );
                $lasInsertedTrxAkuntansi = DB::table('tbl_trx_akuntansi')
                ->insertGetId($input_tbl_trx_akuntansi, 'id_trx_akun');
    

                if($total > $amount_tagihan){ //cek apakah tagihan by sistem lebih besar dari tagihan supplier
                    // total > $amoun tagihan => persediaan (-), kurang bayar(+)
                    // insert akun
                    DB::table('tbl_trx_akuntansi_detail')->insert([
                        'id_trx_akun' => $lasInsertedTrxAkuntansi,
                        'id_akun' => 141,
                        'jumlah' => $total - $amount_tagihan,
                        'tipe' => 'KREDIT',
                        'keterangan' => 'akun'
                    ]);

                    // insert akun
                    DB::table('tbl_trx_akuntansi_detail')->insert([
                        'id_trx_akun' => $lasInsertedTrxAkuntansi,
                        'id_akun' => 166,
                        'jumlah' => $total - $amount_tagihan,
                        'tipe' => 'DEBIT',
                        'keterangan' => 'lawan'
                    ]);
                    
                }
                else{
                    // $total < $amoun tagihan => persediaan (+), lebih bayar(-)
                    // insert akun
                    DB::table('tbl_trx_akuntansi_detail')->insert([
                        'id_trx_akun' => $lasInsertedTrxAkuntansi,
                        'id_akun' => 141,
                        'jumlah' => $amount_tagihan - $total,
                        'tipe' => 'DEBIT',
                        'keterangan' => 'akun'
                    ]);

                    // insert akun
                    DB::table('tbl_trx_akuntansi_detail')->insert([
                        'id_trx_akun' => $lasInsertedTrxAkuntansi,
                        'id_akun' => 163,
                        'jumlah' => $amount_tagihan - $total,
                        'tipe' => 'KREDIT',
                        'keterangan' => 'lawan'
                    ]);
                }
            }
            
        } catch(RequestException $exception) {
        }
        
        if($type_po == 0){
            $query=DB::table('inv_trxes')
                        ->where('inv_trxes.no_surat_jalan', $no_surat_jalan)
                        ->get();
            foreach($query as $row){
                $dt=DB::table('inv_trx_ds')->whereIn('condition', [0, 1, 2])->where('inv_trx_id', $row->id)->get();
                $amount=0;
                foreach($dt as $val){
                    $amount+=($val->amount * $val->base_price);
                }
                try
                {
                    $headers = [
                        'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                        'Accept'        => 'application/json',
                    ];
                    $client = new Client(['base_uri' => $this->base_api_url . 'inv/base/PaymentSupplierDetail']);
                    $reqBody = [
                        'headers' => $headers,
                        'json' => [
                            'payment_supplier_id'   => $payment_supplier['id'],
                            'purchase_id' => $row->purchase_id,
                            'purchase_asset_id' => $row->purchase_asset_id,
                            'purchase_service_id' => 0,
                            'inv_trx_id' => $row->id,
                            'inv_trx_service_id' => 0,
                            'total' => $amount,
                        ]
                    ]; 
                    
                    $response = $client->request('POST', '', $reqBody); 
                    $body = $response->getBody();
                    $content = $body->getContents();
                    $response_array = json_decode($content,TRUE);
                    $payment_supplier=$response_array['data'];
                } catch(RequestException $exception) {
                }
            }
        }else{
            $query=DB::table('inv_trx_services')
                        ->where('inv_trx_services.no_surat_jalan', $no_surat_jalan_jasa)
                        ->get();
            foreach($query as $row){
                $dt=DB::table('inv_trx_service_ds')->whereIn('condition', [1, 2])->where('inv_trx_service_id', $row->id)->get();
                $amount=0;
                foreach($dt as $val){
                    $amount+=($val->amount * $val->base_price);
                }
                try
                {
                    $headers = [
                        'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                        'Accept'        => 'application/json',
                    ];
                    $client = new Client(['base_uri' => $this->base_api_url . 'inv/base/PaymentSupplierDetail']);
                    $reqBody = [
                        'headers' => $headers,
                        'json' => [
                            'payment_supplier_id'   => $payment_supplier['id'],
                            'purchase_id' => 0,
                            'purchase_asset_id' => 0,
                            'purchase_service_id' => $row->purchase_service_id,
                            'inv_trx_id' => 0,
                            'inv_trx_service_id' => $row->id,
                            'total' => $amount,
                        ]
                    ]; 
                    
                    $response = $client->request('POST', '', $reqBody); 
                    $body = $response->getBody();
                    $content = $body->getContents();
                    $response_array = json_decode($content,TRUE);
                    $payment_supplier=$response_array['data'];
                } catch(RequestException $exception) {
                }
            }
        }
        
        return redirect('inventory/purchase');
    }
    public function getPaymentSupplier(Request $request){
        $date1=null;
        $date2=null;
        $m_supplier_id=null;
        if ($request->input('date')) {
            $date1=$request->input('date');
            $date2=$request->input('date2');
        }
        if ($request->input('m_supplier_id')) {
            $supplier=explode(',', $request->input('m_supplier_id'));
            $m_supplier_id=$supplier;
            foreach ($supplier as $key => $value) {
                if ($value == 'all') {
                    $m_supplier_id=DB::table('m_suppliers')->pluck('id');
                }
            }
        }
        $query=DB::table('payment_suppliers')
                    ->join('m_suppliers', 'm_suppliers.id', 'payment_suppliers.m_supplier_id')
                    ->leftJoin('purchases', 'purchases.id', 'payment_suppliers.purchase_id')
                    ->leftJoin('purchase_assets', 'purchase_assets.id', 'payment_suppliers.purchase_asset_id')
                    ->leftJoin('purchase_services', 'purchase_services.id', 'payment_suppliers.purchase_service_id')
                    ->select('payment_suppliers.*', 'm_suppliers.name as supplier', 'purchases.no as purchase_no', 'purchase_assets.no as purchase_asset_no', 'purchase_services.no as purchase_service_no', 'purchases.purchase_date as purchase_date', 'purchase_assets.purchase_date as purchase_asset_date', 'purchase_services.purchase_date as purchase_service_date', 'purchases.ekspedisi', 'purchase_assets.ekspedisi as purchase_asset_ekspedisi', 'purchase_services.ekspedisi as purchase_service_ekspedisi');
        if($date1 != null){
            $query->where('create_date', '>=', $date1);
            $query->where('create_date', '<=', $date2);
        }
        if($m_supplier_id != null){
            $query->whereIn('payment_suppliers.m_supplier_id', $m_supplier_id);
        }
        $query=$query->get();
        foreach($query as $value){
            $pay_date=DB::table('payment_supplier_ds')->select('pay_date')->where('payment_supplier_id', $value->id)->orderBy('id', 'DESC')->first();
            $value->pay_date=$pay_date != null ? $pay_date->pay_date : '';
            $value->paid_for_week=$this->cekListPaidforWeek($value->due_date, $value->is_paid);
        }
        $data=DataTables::of($query)
                    ->make(true); 

        return $data;
    }

    public function cetakPurchase($id){
        $query=DB::table('payment_suppliers')
                    ->join('m_suppliers', 'm_suppliers.id', 'payment_suppliers.m_supplier_id')
                    ->leftJoin('purchases', 'purchases.id', 'payment_suppliers.purchase_id')
                    ->leftJoin('purchase_assets', 'purchase_assets.id', 'payment_suppliers.purchase_asset_id')
                    ->leftJoin('purchase_services', 'purchase_services.id', 'payment_suppliers.purchase_service_id')
                    ->select('payment_suppliers.*', 'm_suppliers.name as supplier', 'purchases.no as purchase_no', 'purchase_assets.no as purchase_asset_no', 'purchase_services.no as purchase_service_no', 'purchases.purchase_date as purchase_date', 'purchase_assets.purchase_date as purchase_asset_date', 'purchase_services.purchase_date as purchase_service_date', 'purchases.ekspedisi', 'purchase_assets.ekspedisi as purchase_asset_ekspedisi', 'purchase_services.ekspedisi as purchase_service_ekspedisi')
                    ->where('payment_suppliers.id', $id)
                    ->get();
        // foreach($query as $value){
        //     $pay_date=DB::table('payment_supplier_ds')->select('pay_date')->where('payment_supplier_id', $value->id)->orderBy('id', 'DESC')->first();
        //     $value->pay_date=$pay_date != null ? $pay_date->pay_date : '';
        //     // $value->paid_for_week=$this->cekListPaidforWeek($value->due_date, $value->is_paid);
        // }
        $data=array(
            'data'  => $query
        );
        return view('pages.inv.purchase_order.cetak_purchase', $data);
    }
    
    public function multipleCetakPurchase(Request $request){
        $idPayments = $request->get('id_payments');
        $idPayments = explode(',', $idPayments);

        $query=DB::table('payment_suppliers')
                    ->join('m_suppliers', 'm_suppliers.id', 'payment_suppliers.m_supplier_id')
                    ->leftJoin('purchases', 'purchases.id', 'payment_suppliers.purchase_id')
                    ->leftJoin('purchase_assets', 'purchase_assets.id', 'payment_suppliers.purchase_asset_id')
                    ->leftJoin('purchase_services', 'purchase_services.id', 'payment_suppliers.purchase_service_id')
                    ->select('payment_suppliers.*', 'm_suppliers.name as supplier', 'purchases.no as purchase_no', 'purchase_assets.no as purchase_asset_no', 'purchase_services.no as purchase_service_no', 'purchases.purchase_date as purchase_date', 'purchase_assets.purchase_date as purchase_asset_date', 'purchase_services.purchase_date as purchase_service_date', 'purchases.ekspedisi', 'purchase_assets.ekspedisi as purchase_asset_ekspedisi', 'purchase_services.ekspedisi as purchase_service_ekspedisi')
                    ->whereIn('payment_suppliers.id', $idPayments)
                    ->get();
        // foreach($query as $value){
        //     $pay_date=DB::table('payment_supplier_ds')->select('pay_date')->where('payment_supplier_id', $value->id)->orderBy('id', 'DESC')->first();
        //     $value->pay_date=$pay_date != null ? $pay_date->pay_date : '';
        //     // $value->paid_for_week=$this->cekListPaidforWeek($value->due_date, $value->is_paid);
        // }
        $data=array(
            'data'  => $query
        );
        // return $data;
        return view('pages.inv.purchase_order.multiple_cetak_purchase', $data);
    }

    private function cekListPaidforWeek($date, $status){
        $now=date('Y-m-d');
        $date_for_week=date("Y-m-d", strtotime("+1 week"));
        if ($date >= $now && $date <= $date_for_week) {
            if ($status == 0) {
                return 1;
            }
        }else{
            return 0;
        }
    }
    public function formPaidCredit($id)
    {
        $is_error = false;
        $error_message = '';  
        try
        {
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . 'inv/base/PaymentSupplier/'.$id]);
            
            $response = $client->request('GET', '', ['headers' => $headers]);  
            $body = $response->getBody();
            $content = $body->getContents();
            $response_array = json_decode($content,TRUE);
            $payment_supplier=$response_array['data'];
        } catch(RequestException $exception) {
        }
        $inv_trxes=array();
        if ($payment_supplier['inv_id'] != null) {   
            try
            {
                $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . 'inv/base/InvTrx/'.$payment_supplier['inv_id']]);
                
                $response = $client->request('GET', '', ['headers' => $headers]);  
                $body = $response->getBody();
                $content = $body->getContents();
                $response_array = json_decode($content,TRUE);
                $inv_trxes=$response_array['data'];
            } catch(RequestException $exception) {
            }
        }
        try
        {
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . 'inv/base/MSupplier/'.$payment_supplier['m_supplier_id']]);
            
            $response = $client->request('GET', '', ['headers' => $headers]);  
            $body = $response->getBody();
            $content = $body->getContents();
            $response_array = json_decode($content,TRUE);
            $m_suppliers=$response_array['data'];
        } catch(RequestException $exception) {
        }

        $payment_supplier_ds=DB::table('payment_supplier_ds')
                                ->leftJoin('list_bank', 'list_bank.id_bank', 'payment_supplier_ds.id_bank')
                                ->where('payment_supplier_id', $id)->get();
        $with_ppn=false;
        if ($payment_supplier['purchase_id'] != null) {
            $purchases=DB::table('purchases')->where('id', $payment_supplier['purchase_id'])->first();
            $with_ppn=$purchases->with_ppn;
        }else if ($payment_supplier['purchase_asset_id'] != null){
            $purchases=DB::table('purchase_assets')->where('id', $payment_supplier['purchase_asset_id'])->first();
            $with_ppn=$purchases->with_ppn;
        }else{
            $purchases=DB::table('purchase_services')->where('id', $payment_supplier['purchase_service_id'])->first();
            $with_ppn=$purchases->with_ppn;
        }
        $payment_supplier['with_ppn']=$with_ppn;
        $list_bank=DB::table('list_bank')->get();
        $data = array(
            'error' => array(
                'is_error' => $is_error,
                'error_message' => $error_message
            ),
            'payment_supplier'  => $payment_supplier,
            'payment_supplier_ds'  => $payment_supplier_ds,
            'inv_trxes'  => $inv_trxes,
            'm_suppliers'  => $m_suppliers,
            'list_bank'     => $list_bank
        );

        return view('pages.inv.purchase_order.form_bill_credit_supplier', $data);
    }
    public function saveCreditPaid(Request $request)
    {
        $payment_supplier_id=$request->input('id');
        $total_all=$request->input('total_all');
        $total_ppn=$this->currency($request->input('total_ppn'));
        $purchase_id=$request->input('purchase_id');
        $inv_trx_id=$request->input('inv_trx_id');
        $inv_trx_service_id=$request->input('inv_trx_service_id');
        $purchase_asset_id=$request->input('purchase_asset_id');
        $purchase_service_id=$request->input('purchase_service_id');
        $total=$this->currency($request->input('total'));
        $total_bayar=$this->currency($request->input('total_bayar'));
        $delivery_fee=$this->currency($request->input('delivery_fee'));
        $paid_more=$request->paid_more;
        $with_ppn=$request->with_ppn;
        $total_produk=$total_bayar - ($total_ppn + $delivery_fee + $paid_more);
        
        if ($total >= $total_all) {
            try
            {
                $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . 'inv/base/PaymentSupplier/'.$payment_supplier_id]);
                $reqBody = [
                    'headers' => $headers,
                'json' => [
                        'is_paid'   => true
                    ]
                ]; 
                
                $response = $client->request('PUT', '', $reqBody); 
                $body = $response->getBody();
                $content = $body->getContents();
                $response_array = json_decode($content,TRUE);
            } catch(RequestException $exception) {
            }
        }
        try
        {
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . 'inv/base/PaymentSupplierD']);
            $reqBody = [
                'headers' => $headers,
                'json' => [
                    'payment_supplier_id'   => $payment_supplier_id,
                    'wop' => $request->input('wop'),
                    'bank_number' => $request->input('bank_number'),
                    'atas_nama' => $request->input('atas_nama'),
                    'ref_code' => $request->input('ref_code'),
                    'id_bank' => $request->input('id_bank'),
                    'amount' => $total,
                    'delivery_fee' => $delivery_fee,
                    'description' => $request->input('description'),
                    'pay_date' => $request->input('pay_date'),
                    'user_id'   => $this->user_id,
                ]
            ]; 
            
            $response = $client->request('POST', '', $reqBody); 
            $body = $response->getBody();
            $content = $body->getContents();
            $response_array = json_decode($content,TRUE);
        } catch(RequestException $exception) {
        }
        $payment_supplier=DB::table('payment_suppliers')->where('id', $payment_supplier_id)->first();
        $tipe='';
        if ($payment_supplier->purchase_id != 0 && $payment_supplier->purchase_id != null) {
            $po=DB::table('purchases')->where('id', $purchase_id)->first();
        }else if ($payment_supplier->purchase_asset_id != 0 && $payment_supplier->purchase_asset_id != null) {
            $po=DB::table('purchase_assets')->where('id', $purchase_asset_id)->first();
            $tipe='Asset';
        }else{
            $po=DB::table('purchase_services')->where('id', $purchase_service_id)->first();
            $tipe='Jasa';
        }
        if ($with_ppn == 1) {
            $total_ppn=$total_produk - ($total_produk / 1.1);
            $total_produk=$total_produk-$total_ppn;
        }
        
        $input_jurnal=array(
            'purchase_id' => $payment_supplier->purchase_id,
            'purchase_asset_id' => $payment_supplier->purchase_asset_id,
            'purchase_service_id' => $payment_supplier->purchase_service_id,
            'total' => $total_bayar,
            'total_all' => $total_produk,
            'total_ppn' => $total_ppn,
            'paid_more' => $paid_more,
            'delivery_fee' => $delivery_fee,
            'user_id'   => $this->user_id,
            'akun'      => $request->input('account_payment'),
            'lawan'      => $payment_supplier->payment_po == 'credit' ? 147 : 139,
            'ppn_akun'      => $payment_supplier->payment_po == 'credit' ? 67 : 133,
            'deskripsi'     => 'Pembayaran PO '.$tipe.' dari No Pembelian '.$po->no,
            'm_supplier_id' => $po->m_supplier_id,
            'tgl'       => date('Y-m-d'),
            'inv_trx_id'    => $inv_trx_id,
            'inv_trx_service_id' => $inv_trx_service_id,
            'location_id'   => $this->site_id
        );
        $this->journalPayment($input_jurnal);

        return redirect('inventory/purchase');
    }
    public function detailPayment($id)
    {
        try
        {
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . 'inv/base/PaymentSupplier/'.$id]);
            
            $response = $client->request('GET', '', ['headers' => $headers]);  
            $body = $response->getBody();
            $content = $body->getContents();
            $response_array = json_decode($content,TRUE);
            $payment_supplier=$response_array['data'];
        } catch(RequestException $exception) {
        }
        try
        {
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . 'inv/base/InvTrx/'.$payment_supplier['inv_id']]);
            
            $response = $client->request('GET', '', ['headers' => $headers]);  
            $body = $response->getBody();
            $content = $body->getContents();
            $response_array = json_decode($content,TRUE);
            $inv_trxes=$response_array['data'];
        } catch(RequestException $exception) {
        }
        try
        {
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . 'inv/base/MSupplier/'.$payment_supplier['m_supplier_id']]);
            
            $response = $client->request('GET', '', ['headers' => $headers]);  
            $body = $response->getBody();
            $content = $body->getContents();
            $response_array = json_decode($content,TRUE);
            $m_suppliers=$response_array['data'];
        } catch(RequestException $exception) {
        }

        $payment_supplier_ds=DB::table('payment_supplier_ds')
                                ->leftJoin('list_bank', 'list_bank.id_bank', 'payment_supplier_ds.id_bank')
                                ->where('payment_supplier_id', $id)->get();

        $data = array(
            'payment_supplier'  => $payment_supplier,
            'payment_supplier_ds'  => $payment_supplier_ds,
            'inv_trxes'  => $inv_trxes,
            'm_suppliers'  => $m_suppliers,
        );

        return $data;
    }
    private function journalPayment($data){
        $data_trx=array(
            'deskripsi'     => $data['deskripsi'],
            'location_id'     => $data['location_id'],
            'tanggal'       => $data['tgl'],
            'user_id'       => $data['user_id'],
            'purchase_id'   => $data['purchase_id'],
            'purchase_asset_id'   => $data['purchase_asset_id'],
            'purchase_service_id'   => $data['purchase_service_id'],
            'inv_trx_id'   => $data['inv_trx_id'],
            'inv_trx_service_id'   => $data['inv_trx_service_id'],
            'm_supplier_id'   => $data['m_supplier_id'],
        );
        $insert=DB::table('tbl_trx_akuntansi')->insert($data_trx);
        if ($insert) {
            $id_last=DB::table('tbl_trx_akuntansi')->max('id_trx_akun');
            $lawan=array(
                'id_trx_akun'   => $id_last,
                'id_akun'       => $data['lawan'],
                'jumlah'        => $data['total_all'],
                'tipe'          => "DEBIT",
                'keterangan'    => 'lawan',
            );
            DB::table('tbl_trx_akuntansi_detail')->insert($lawan);
            if($data['lawan'] == 24 || $data['lawan'] == 101){
                $acccon = new AkuntanController();
                $warehouse_id=$data['lawan'] == 24 ? 2 : 3;
                $acccon->countSaldoCash($this->site_id, $warehouse_id, "in", $data['total_all']);
            }
            $lawan=array(
                'id_trx_akun'   => $id_last,
                'id_akun'       => $data['ppn_akun'],
                'jumlah'        => $data['total_ppn'],
                'tipe'          => "DEBIT",
                'keterangan'    => 'lawan',
            );
            DB::table('tbl_trx_akuntansi_detail')->insert($lawan);
            if ($data['paid_more'] != 0) {
                $lawan1=array(
                    'id_trx_akun'   => $id_last,
                    'id_akun'       => 163,
                    'jumlah'        => $data['paid_more'],
                    'tipe'          => "DEBIT",
                    'keterangan'    => 'lawan',
                );
                DB::table('tbl_trx_akuntansi_detail')->insert($lawan1);
            }
            if ($data['delivery_fee'] != 0) {
                $lawan1=array(
                    'id_trx_akun'   => $id_last,
                    'id_akun'       => 89,
                    'jumlah'        => $data['delivery_fee'],
                    'tipe'          => "DEBIT",
                    'keterangan'    => 'lawan',
                );
                DB::table('tbl_trx_akuntansi_detail')->insert($lawan1);
            }
            $acccon = new AkuntanController();
            $no=$acccon->createNo($data['akun'], "KREDIT");
            $akun=array(
                'id_trx_akun'   => $id_last,
                'id_akun'       => $data['akun'],
                // 'id_akun'       => 20,
                'jumlah'        => $data['total'],
                'tipe'          => "KREDIT",
                'keterangan'    => 'akun',
                'no'            => $no
            );
            DB::table('tbl_trx_akuntansi_detail')->insert($akun);
            if($data['akun'] == 24 || $data['akun'] == 101){
                $acccon = new AkuntanController();
                $warehouse_id=$data['akun'] == 24 ? 2 : 3;
                $acccon->countSaldoCash($this->site_id, $warehouse_id, "out", $data['total']);
            }
        }
    }
    private function currency($val){
        $data=explode('.', $val);
        $new=implode('', $data);
        return $new;
    }
    public function materialAssetList(){
        return view('pages.inv.inventory_transaction.material_asset_list');
    }
    public function jsonMaterialAssetList(){
        $query=DB::table('material_assets')
                    ->select('material_assets.*', 'm_items.name', 'm_items.no', 'm_units.name as unit_name')
                    ->join('m_items', 'm_items.id', 'material_assets.m_item_id')
                    ->join('m_units', 'm_units.id', 'material_assets.m_unit_id')
                    ->where('site_id', $this->site_id)
                    ->get();
        $data=DataTables::of($query)
                    ->make(true); 
        return $data;        
    }
    public function addAmortisasiAsset(Request $request){
        $startDate = time();
        // $total_bulan=$request->total_bulan;
        $total_bulan=$request->total_bulan_save;
        $date=date('Y-m', strtotime('+'.$total_bulan.' month', $startDate));
        
        try
        {
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . '/inv/base/MaterialAsset/'.$request->input('id')]);
            $reqBody = [
                'headers' => $headers,
                'json' => [
                    'amount_amortisasi'    => $this->currency($request->amount),
                    'end_date_amortisasi' => $date,
                    ]
                ]; 
                $response = $client->request('PUT', '', $reqBody); 
                $body = $response->getBody();
                $content = $body->getContents();
                $response_array = json_decode($content,TRUE);
        } catch(RequestException $exception) {
        }
        return redirect('inventory/material_asset');
    }
    public function jurnalAmortisasi(){
        $now=date('Y-m');
        $query=DB::table('material_assets')->whereNotNull('amount_amortisasi')->where('end_date_amortisasi', '>=', $now)->get();
        foreach ($query as $key => $value) {
            $m_items=DB::table('m_items')->where('id', $value->m_item_id)->first();
            $cek_account_asset=DB::table('account_assets')->where('m_item_id', $value->m_item_id)->first();
            // $total_amortisasi=($value->amount * $value->base_price) * ($value->amount_amortisasi / 100);
            if($cek_account_asset != null){
                $main=DB::table('tbl_akun')->where('id_akun', $cek_account_asset->amort_asset_id)->first();
                $lawan_akun=$main->id_main_akun == 50 ? 6 : ($main->id_main_akun == 51 ? 7 : ($main->id_main_akun == 52 ? 8 : ($main->id_main_akun == 53 ? 9 : ($main->id_main_akun == 54 ? 10 : ($main->id_main_akun == 179 ? 11 : 0)))));
                $input_jurnal=array(
                    'inv_trx_id' => $value->inv_trx_id,
                    'purchase_asset_id' => $value->purchase_asset_id,
                    'total' => $value->amount_amortisasi,
                    // 'user_id'   => $this->user_id,
                    'lawan' => $lawan_akun,
                    'akun'  => $cek_account_asset->amort_asset_id,
                    'deskripsi'     => 'Amortisasi Asset '.$m_items->name,
                    'tgl'       => date('Y-m-d'),
                    'location_id'   => $value->site_id
                );
                $this->saveJournalAmortisasi($input_jurnal);
            }
        }
        // return $query;
    }
    private function saveJournalAmortisasi($data){
        $data_trx=array(
            'deskripsi'     => $data['deskripsi'],
            'location_id'     => $data['location_id'],
            'tanggal'       => $data['tgl'],
            // 'user_id'       => $data['user_id'],
            'inv_trx_id'   => $data['inv_trx_id'],
            'purchase_asset_id' => $data['purchase_asset_id'],
        );
        $insert=DB::table('tbl_trx_akuntansi')->insert($data_trx);
        if ($insert) {
            $id_last=DB::table('tbl_trx_akuntansi')->max('id_trx_akun');
            $lawan=array(
                'id_trx_akun'   => $id_last,
                'id_akun'       => $data['lawan'],
                'jumlah'        => $data['total'],
                'tipe'          => "DEBIT",
                'keterangan'    => 'lawan',
            );
            DB::table('tbl_trx_akuntansi_detail')->insert($lawan);
            $akun=array(
                'id_trx_akun'   => $id_last,
                'id_akun'       => $data['akun'],
                'jumlah'        => $data['total'],
                'tipe'          => "KREDIT",
                'keterangan'    => 'akun',
            );
            DB::table('tbl_trx_akuntansi_detail')->insert($akun);
        }
    }
    public function getItemStock(){
        $query=DB::table('stocks')
                    ->groupBy('stocks.m_item_id')
                    ->select('stocks.m_item_id as id', DB::raw('MAX(stocks.m_unit_id) as m_unit_id'), DB::raw('MAX(m_units.name) as m_unit_name'), DB::raw('MAX(m_units.name) as m_unit_name'), DB::raw('MAX(m_units.name) as m_unit_name'), DB::raw('MAX(m_items.name) as name'), DB::raw('MAX(m_items.no) as no'))
                    ->join('m_items', 'm_items.id', 'stocks.m_item_id')
                    ->join('m_units', 'm_units.id', 'stocks.m_unit_id')
                    ->get();
        $data=array(
            'data'  => $query
        );
        return $data;
    }
    public function suggestPurchaseServiceJson(Request $request)
    {
        $data=array();
        if($request->has('q')){
            $key=$request->q;
            $query=DB::table('purchase_services')
                        ->select('purchase_services.*', 'purchase_services.no as text', DB::raw('COALESCE((SELECT SUM(delivery_fee) from payment_suppliers where purchase_service_id=purchase_services.id), 0) as delivery_fee_used'))
                        ->where('acc_ao', true)
                        ->where('status_payment', false)
                        ->where('purchase_services.no', 'like', '%'.$key.'%');
            $data=$query->limit(15)->get();
        }
        return $data;
    }
    public function getInvByPurchaseServiceId($id)
    {
        $cek_inv_id=DB::table('payment_suppliers')
                        ->where('payment_suppliers.purchase_service_id', $id)
                        ->pluck('inv_id');
        $query['data']=DB::table('inv_trx_services')->where('purchase_service_id', $id)->whereNotIn('id', $cek_inv_id)->select('id', 'no')->get();
        return $query;
    }
    public function getTotalInvByPurchaseServiceId($id, $purchase_id)
    {
        $query=DB::table('inv_trx_service_ds')
                            ->where('inv_trx_service_id', $id)
                            ->select('service_name', DB::raw('CAST(SUM(amount) * (select base_price from purchase_service_ds where service_name = inv_trx_service_ds.service_name and purchase_service_id = '.$purchase_id.') AS int) as total'))
                            ->groupBy('service_name')
                            ->get();
        $total=0;
        foreach ($query as $key => $value) {
            $total+=$value->total;
        }
        return $total;
    }
    public function getStokAllD(Request $request) {
        $data=array(
            'm_warehouse_id'    => $this->m_warehouse_id,
        );
        return view('pages.inv.inventory_transaction.stock_d_list', $data);
    }
    public function getStokAllDJson(Request $request) {
        $date=0;
        if ($request->input('bulan')) {
            $date=$request->input('tahun').'-'.$request->input('bulan');
        }else{
            $date=date('Y-m');
        }
        
        if ($this->site_id != null) {
            $query=DB::table('calculate_stocks as cs')
                            ->where('site_id', $this->site_id)
                            ->where('last_month', $date)
                            ->select('cs.*', 'cs.amount as stok', DB::raw('price * amount as value'), 'price as last_price', 'updated_at as last_update_in')
                            ->where('site_id', $this->site_id)
                            ->orderBy('updated_at', 'DESC')
                            ->get();
        }else{
            $query=DB::table('calculate_stocks as cs')
                            ->where('site_id', $this->site_id)
                            ->where('last_month', $date)
                            ->select('cs.*', 'cs.amount as stok', DB::raw('price * amount as value'), 'price as last_price', 'updated_at as last_update_in')
                            ->orderBy('updated_at', 'DESC')
                            ->get();
        }
        
        foreach ($query as $key => $value) {
            $value->m_items=DB::table('m_items')->where('id', $value->m_item_id)->first();
            $value->m_units=DB::table('m_units')->where('id', $value->m_unit_id)->first();
            $value->m_warehouse=DB::table('m_warehouses')->where('id', $value->m_warehouse_id)->first();
            $value->sites=DB::table('sites')->where('id', $value->site_id)->first();
            $value->last_update_out=null;
        }
        $data=DataTables::of($query)
                ->make(true); 
        return $data;
    }
    public function debtList(Request $request) {

        $suppliers = null;
        try
        {
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . '/crm/base/MSupplier']);  
            $response = $client->request('GET', '', ['headers' => $headers]);  
            $body = $response->getBody();
            $content = $body->getContents();
            $response_array = json_decode($content,TRUE);
            $suppliers = $response_array['data'];
        } catch(RequestException $exception) {      
        }
        
        $data_temp=array();
        $date1=date('Y-m-d');
        $date2=date('Y-m-d');
        $supplier_selected=array();
        $all_supplier=false;
        if ($request->input('suppl_single')) {
            $supplier=$request->input('suppl_single');
            $supplier_selected=$request->input('suppl_single');
            $all_supplier=false;
            foreach ($supplier as $key => $value) {
                if ($value == 'all') {
                    $all_supplier=true;
                    $supplier=DB::table('m_suppliers')->pluck('id');
                }
            }
            $id=[147];//Hutang usaha
            $query=DB::table('tbl_akun_detail')
                        ->whereIn('id_akun', $id)
                        // ->orWhereIn('turunan1', $id)
                        // ->orWhereIn('turunan2', $id)
                        // ->orWhereIn('turunan3', $id)
                        // ->orWhereIn('turunan4', $id)
                        ->pluck('id_akun');
                        
            $user_id = request()->session()->get('user.id');
            $user = DB::table('users')->where('id', $this->user_id)->first();
            $location_id = $user->site_id;

            if ($request->input('date')) {
                $date1=$request->input('date');
                $date2=$request->input('date2');
            }
            // $akun=DB::table('tbl_akun')->where('id_akun', $id)->first();
            $startTime = strtotime( $date1 );
            $endTime = strtotime( $date2 );
            $date=date('Y-m', $startTime);

            $first_date_month=$date.'-01';
            $saldo_before_start_date=0;
            foreach ($supplier as $key => $value) {
                $min=$startTime - 86400;//kurangi sehari
                $perubahan_saldo=DB::table('tbl_trx_akuntansi_detail as trd')
                                    ->select(DB::raw("COALESCE(SUM(CASE WHEN tipe = 'DEBIT' THEN jumlah ELSE 0 END), 0) AS total_debit"), DB::raw("COALESCE(SUM(CASE WHEN tipe = 'KREDIT' THEN jumlah ELSE 0 END), 0) AS total_kredit"))
                                    ->join('tbl_trx_akuntansi as tra', 'trd.id_trx_akun', 'tra.id_trx_akun')
                                    ->whereIn('trd.id_akun', $query)
                                    ->where('tra.location_id', $location_id)
                                    // ->where('tanggal', '>=', $first_date_month)
                                    ->where('tra.m_supplier_id', $value)
                                    ->where('tanggal', '<=', date('Y-m-d', $min))
                                    ->whereNull('notes')
                                    ->first();
                $saldo_before_start_date = $perubahan_saldo->total_kredit - $perubahan_saldo->total_debit;
                
                $bulan=explode('-', $date);
                $detail=array();
                $account_payment=DB::table('tbl_akun')->where('id_main_akun', 13)->pluck('id_akun')->toArray();
                
                for ( $i = $startTime; $i <= $endTime; $i = $i + 86400 ) {
                    $thisDate = date( 'Y-m-d', $i ); // 2010-05-01, 2010-05-02, etc
                    $dtSaldo=DB::table('tbl_trx_akuntansi_detail as trd')
                                        ->select('trd.*', 'trd.no as note_no', 'tra.*', 'ta.nama_akun', 'ta.no_akun', 'p.no as purchase_no', 'pa.no as purchase_asset_no', 'ps.no as ps_no', 'it.no_surat_jalan', 'p.notes as p_notes', 'pa.notes as pa_notes', DB::raw('COALESCE((SELECT sum(tbl_trx_akuntansi_detail.jumlah) FROM tbl_trx_akuntansi_detail where tbl_trx_akuntansi_detail.id_trx_akun=trd.id_trx_akun and tbl_trx_akuntansi_detail.id_akun=67), 0) as ppn'))
                                        ->join('tbl_trx_akuntansi as tra', 'trd.id_trx_akun', 'tra.id_trx_akun')
                                        ->join('tbl_akun as ta', 'trd.id_akun', 'ta.id_akun')
                                        ->leftJoin('inv_trxes as it', 'it.id', 'tra.inv_trx_id')
                                        ->leftJoin('purchases as p', 'tra.purchase_id', 'p.id')
                                        ->leftJoin('purchase_assets as pa', 'tra.purchase_asset_id', 'pa.id')
                                        ->leftJoin('paid_suppliers as ps', 'tra.paid_supplier_id', 'ps.id')
                                        ->whereIn('trd.id_akun', $query)
                                        ->where('tra.location_id', $location_id)
                                        ->where('tanggal', $thisDate)
                                        ->where('tra.m_supplier_id', $value)
                                        ->whereNull('tra.notes')
                                        ->get();
                    if (count($dtSaldo) > 0) {
                        foreach ($dtSaldo as $v) {
                            $source=DB::table('tbl_trx_akuntansi_detail as trd')
                                        ->select('no')
                                        ->whereIn('id_akun', $account_payment)
                                        ->where('id_trx_akun', $v->id_trx_akun)
                                        ->first();
                            $v->source=($source != null ? $source->no : null);
                        }
                        $detail[$i]['date']=$thisDate;
                        $detail[$i]['dt']=$dtSaldo;
                    }
                }
                $data_temp[$value]['supplier']=DB::table('m_suppliers')->where('id', $value)->first();
                $data_temp[$value]['data']=$detail;
                $data_temp[$value]['perubahan_saldo']=$perubahan_saldo;
            }
            
        }
        $data = array(
            'suppliers' => $suppliers,
            'date1'     => $date1,
            'date2'     => $date2,
            'data'      => $data_temp,
            'supplier_selected' => $supplier_selected,
            'all_supplier'  => $all_supplier
        );
        // return $data_temp;
        return view('pages.inv.inventory_transaction.debt_list', $data);
    }
    public function piutangList(Request $request) {
        $customer = null;
        try
        {
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . '/crm/customerdatamain']);  
            $response = $client->request('GET', '', ['headers' => $headers]);  
            $body = $response->getBody();
            $content = $body->getContents();
            $response_array = json_decode($content,TRUE);
            $customer = $response_array['data'];
        } catch(RequestException $exception) {      
        }
        $data_temp_cust=array();
        $date1=date('Y-m-d');
        $date2=date('Y-m-d');
        $customer_selected=array();
        $all_customer=false;
        if ($request->input('customer_id')) {
            $user_id = request()->session()->get('user.id');
            $user = DB::table('users')->where('id', $this->user_id)->first();
            $location_id = $user->site_id;

            if ($request->input('date')) {
                $date1=$request->input('date');
                $date2=$request->input('date2');
            }
            // $akun=DB::table('tbl_akun')->where('id_akun', $id)->first();
            $startTime = strtotime( $date1 );
            $endTime = strtotime( $date2 );
            $date=date('Y-m', $startTime);

            $first_date_month=$date.'-01';
            $saldo_before_start_date=0;

            //get piutang cust data
            $customer_id=$request->input('customer_id');
            $customer_selected=$request->input('customer_id');
            $all_customer=false;
            if ($customer_id != null) {
                foreach ($customer_id as $key => $value) {
                    if ($value == 'all') {
                        $all_customer=true;
                        $customer_id=DB::table('customers')->pluck('id');
                    }
                }
                $piutang_cust_id=[151];
                $piutang_cust=DB::table('tbl_akun_detail')
                            ->whereIn('id_akun', $piutang_cust_id)
                            ->orWhereIn('turunan1', $piutang_cust_id)
                            ->orWhereIn('turunan2', $piutang_cust_id)
                            ->orWhereIn('turunan3', $piutang_cust_id)
                            ->orWhereIn('turunan4', $piutang_cust_id)
                            ->pluck('id_akun');
                foreach ($customer_id as $key => $value) {
                    $min=$startTime - 86400;//kurangi sehari
                    $perubahan_saldo=DB::table('tbl_trx_akuntansi_detail as trd')
                                        ->select(DB::raw("COALESCE(SUM(CASE WHEN tipe = 'DEBIT' THEN jumlah ELSE 0 END), 0) AS total_debit"), DB::raw("COALESCE(SUM(CASE WHEN tipe = 'KREDIT' THEN jumlah ELSE 0 END), 0) AS total_kredit"))
                                        ->join('tbl_trx_akuntansi as tra', 'trd.id_trx_akun', 'tra.id_trx_akun')
                                        ->whereIn('trd.id_akun', $piutang_cust)
                                        ->where('tra.location_id', $location_id)
                                        // ->where('tanggal', '>=', $first_date_month)
                                        ->where('tra.customer_id', $value)
                                        ->where('tanggal', '<=', date('Y-m-d', $min))
                                        ->whereNull('notes')
                                        ->first();
                    $saldo_before_start_date = $perubahan_saldo->total_kredit - $perubahan_saldo->total_debit;
                    
                    $bulan=explode('-', $date);
                    $detail=array();
                    $account_payment=DB::table('tbl_akun')->where('id_main_akun', 13)->pluck('id_akun');
                    for ( $i = $startTime; $i <= $endTime; $i = $i + 86400 ) {
                        $thisDate = date( 'Y-m-d', $i ); // 2010-05-01, 2010-05-02, etc
                        $dtSaldo=DB::table('tbl_trx_akuntansi_detail as trd')
                                            ->select('trd.*', 'trd.no as note_no', 'tra.*', 'ta.nama_akun', 'ta.no_akun', 'o.order_no as order_no', 'io.no as install_order_no', 'pc.no as paid_cust_no', 'cb.bill_no', 'cb.no as cb_no')
                                            ->join('tbl_trx_akuntansi as tra', 'trd.id_trx_akun', 'tra.id_trx_akun')
                                            ->join('tbl_akun as ta', 'trd.id_akun', 'ta.id_akun')
                                            ->leftJoin('orders as o', 'tra.order_id', 'o.id')
                                            ->leftJoin('install_orders as io', 'tra.install_order_id', 'io.id')
                                            ->leftJoin('paid_customers as pc', 'tra.paid_customer_id', 'pc.id')
                                            ->leftJoin('customer_bills as cb', 'tra.customer_bill_id', 'cb.id')
                                            ->whereIn('trd.id_akun', $piutang_cust)
                                            ->where('tra.location_id', $location_id)
                                            ->where('tanggal', $thisDate)
                                            ->where('tra.customer_id', $value)
                                            ->whereNull('tra.notes')
                                            ->get();
                        if (count($dtSaldo) > 0) {
                            foreach ($dtSaldo as $v) {
                                $v->id_source=null;
                                $v->no_source=null;
                                $query=DB::table('tbl_trx_akuntansi_detail')
                                            ->where('id_trx_akun', $v->id_trx_akun)
                                            ->whereIn('id_akun', $account_payment)
                                            ->first();
                                if ($query != null) {
                                    $v->id_source=$query->id_trx_akun_detail;
                                    $v->no_source=$query->no;
                                }
                            }
                            $detail[$i]['date']=$thisDate;
                            $detail[$i]['dt']=$dtSaldo;
                        }
                    }
                    $data_temp_cust[$value]['customer']=DB::table('customers')->where('id', $value)->first();
                    $data_temp_cust[$value]['data']=$detail;
                    $data_temp_cust[$value]['perubahan_saldo']=$perubahan_saldo;
                }
            }
        }
        $data = array(
            'customer'  => $customer,
            'date1'     => $date1,
            'date2'     => $date2,
            'data_cust'      => $data_temp_cust,
            'customer_selected' => $customer_selected,
            'all_customer'      => $all_customer
        );
        
        return view('pages.inv.inventory_transaction.piutang_list', $data);
    }
    public function historyPurchaseBySupplier(Request $request) {

        $suppliers = null;
        try
        {
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . '/crm/base/MSupplier']);  
            $response = $client->request('GET', '', ['headers' => $headers]);  
            $body = $response->getBody();
            $content = $body->getContents();
            $response_array = json_decode($content,TRUE);
            $suppliers = $response_array['data'];
        } catch(RequestException $exception) {      
        }
        $data_temp=array();
        $date1=date('Y-m-d');
        $date2=date('Y-m-d');
        $supplier_selected=array();
        $all_supplier=false;
        if ($request->input('suppl_single')) {
            $supplier=$request->input('suppl_single');
            $supplier_selected=$request->input('suppl_single');
            $all_supplier=false;
            foreach ($supplier as $key => $value) {
                if ($value == 'all') {
                    $all_supplier=true;
                    $supplier=DB::table('m_suppliers')->pluck('id');
                }
            }
            $asset_id=DB::table('account_assets')->pluck('asset_id')->toArray();
            
            array_push($asset_id, 141, 142, 143, 144, 92);

            $user_id = request()->session()->get('user.id');
            $user = DB::table('users')->where('id', $this->user_id)->first();
            $location_id = $user->site_id;

            if ($request->input('date')) {
                $date1=$request->input('date');
                $date2=$request->input('date2');
            }
            // $akun=DB::table('tbl_akun')->where('id_akun', $id)->first();
            $startTime = strtotime( $date1 );
            $endTime = strtotime( $date2 );
            $date=date('Y-m', $startTime);

            $first_date_month=$date.'-01';
            $saldo_before_start_date=0;
            foreach ($supplier as $key => $value) {
                $bulan=explode('-', $date);
                $detail=array();
                for ( $i = $startTime; $i <= $endTime; $i = $i + 86400 ) {
                    $thisDate = date( 'Y-m-d', $i ); // 2010-05-01, 2010-05-02, etc
                    $dtSaldo=DB::table('tbl_trx_akuntansi_detail as trd')
                                        ->select(DB::raw('SUM(trd.jumlah) as total'), DB::raw('MAX(p.no) as purchase_no'), DB::raw('MAX(pa.no) as purchase_asset_no'), DB::raw('MAX(p.id) as purchase_id'), DB::raw('MAX(pa.id) as purchase_asset_id'), DB::raw('MAX(p.notes) as purchase_notes'), DB::raw('MAX(pa.notes) as purchase_asset_notes'), DB::raw('MAX(trd.id_trx_akun) as id_trx_akun'), 'it.no_surat_jalan', DB::raw('MAX(it.inv_trx_date) as inv_trx_date'), DB::raw('MAX(it.id) as inv_trx_id'), DB::raw('MAX(ps.paid_no) as paid_no'), DB::raw('MAX(ps.no) as bill_no'))
                                        ->join('tbl_trx_akuntansi as tra', 'trd.id_trx_akun', 'tra.id_trx_akun')
                                        ->join('tbl_akun as ta', 'trd.id_akun', 'ta.id_akun')
                                        ->join('inv_trxes as it', 'tra.inv_trx_id', 'it.id')
                                        ->leftJoin('payment_supplier_details as psd', 'psd.inv_trx_id', 'it.id')
                                        ->leftJoin('payment_suppliers as ps', 'ps.id', 'psd.payment_supplier_id')
                                        ->leftJoin('purchases as p', 'tra.purchase_id', 'p.id')
                                        ->leftJoin('purchase_assets as pa', 'tra.purchase_asset_id', 'pa.id')
                                        ->whereIn('trd.id_akun', $asset_id)
                                        ->where('tra.location_id', $location_id)
                                        ->where('tanggal', $thisDate)
                                        ->where('tra.m_supplier_id', $value)
                                        ->whereNull('tra.notes')
                                        ->whereNotNull('tra.inv_trx_id')
                                        ->groupBy('it.no_surat_jalan')
                                        ->get();
                    if (count($dtSaldo) > 0) {
                        $detail[$i]['date']=$thisDate;
                        $detail[$i]['dt']=$dtSaldo;
                    }
                }
                $data_temp[$value]['supplier']=DB::table('m_suppliers')->where('id', $value)->first();
                $data_temp[$value]['data']=$detail;
            }
            
        }
        $data = array(
            'suppliers' => $suppliers,
            'date1'     => $date1,
            'date2'     => $date2,
            'data'      => $data_temp,
            'supplier_selected' => $supplier_selected,
            'all_supplier'  => $all_supplier
        );
        // return($data);
        return view('pages.inv.inventory_transaction.history_purchase_supplier', $data);
    }
    public function ageDebtSupplier(Request $request) {
        $supplier=DB::table('m_suppliers')->get();
        $user_id = request()->session()->get('user.id');
        $user = DB::table('users')->where('id', $this->user_id)->first();
        $location_id = $user->site_id;
        $date_now=date('Y-m-d');
        $one_month_before=date('Y-m-d', strtotime("- 1 month", strtotime($date_now)));
        $two_month_before=date('Y-m-d', strtotime("- 2 month", strtotime($date_now)));
        $three_month_before=date('Y-m-d', strtotime("- 3 month", strtotime($date_now)));
        $four_month_before=date('Y-m-d', strtotime("- 4 month", strtotime($date_now)));
        $five_month_before=date('Y-m-d', strtotime("- 5 month", strtotime($date_now)));
        
        foreach ($supplier as $key => $value) {
            $detail=DB::table('payment_suppliers')
                            ->select(DB::raw("COALESCE(SUM(CASE WHEN due_date < '".$date_now."' and due_date >= '".$one_month_before."' THEN amount ELSE 0 END), 0) as total_in_one_months"), DB::raw("COALESCE(SUM(CASE WHEN due_date < '".$one_month_before."' and due_date >= '".$two_month_before."' THEN amount ELSE 0 END), 0) as total_in_two_months"), DB::raw("COALESCE(SUM(CASE WHEN due_date < '".$two_month_before."' and due_date >= '".$three_month_before."' THEN amount ELSE 0 END), 0) as total_in_three_months"), DB::raw("COALESCE(SUM(CASE WHEN due_date < '".$three_month_before."' and due_date >= '".$four_month_before."' THEN amount ELSE 0 END), 0) as total_in_four_months"), DB::raw("COALESCE(SUM(CASE WHEN due_date < '".$four_month_before."' THEN amount ELSE 0 END), 0) as total_in_five_months"))
                            ->where('m_supplier_id', $value->id)
                            ->where('is_paid', false)
                            ->first();
            $value->detail=$detail;
        }
        
        $data = array(
            'data'      => $supplier,
            'one_month_before' => $one_month_before,
            'two_month_before' => $two_month_before,
            'three_month_before' => $three_month_before,
            'four_month_before' => $four_month_before
        );
        // return($data);
        return view('pages.inv.inventory_transaction.age_debt_list', $data);
    }
    public function ageDebtSupplierJson(Request $request) {
        $detail=DB::table('payment_suppliers')
                    ->select('*')
                    ->where('m_supplier_id', $request->id)
                    ->where('due_date', '<', $request->start_date)
                    ->where('is_paid', false);
        if ($request->month != 5) {
            $detail->where('due_date', '>=', $request->end_date);
        }
        $detail=$detail->get();
        $data=DataTables::of($detail)->make(true);
        return $data;
    }
    public function paidListCustomer(){
        return view('pages.inv.inventory_transaction.payment_customers');
    }
    public function paidCustomerBill(){
        $customer = null;
        try
        {
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . '/crm/customerdatamain']);  
            $response = $client->request('GET', '', ['headers' => $headers]);  
            $body = $response->getBody();
            $content = $body->getContents();
            $response_array = json_decode($content,TRUE);
            $customer = $response_array['data'];
        } catch(RequestException $exception) {      
        }
        $data=array(
            'customer'  => $customer,
            'list_bank' => DB::table('list_bank')->get(),
            'akun_kurang_lebih' => DB::table('tbl_akun')->whereIn('no_akun', ['2.1.6.5', '8.5.1.0', '8.7.1.0'])->get()->reverse(),
        );
        return view('pages.inv.inventory_transaction.paid_bill_customer', $data);
    }
    
     public function getSaldoLebihKurangCustomer($id){
        $query=DB::table('calculateplusminus')->select('nominal')->where('customer_id', $id)->first();

        if($query){
            $nominal = $query->nominal;
        }
        else{
            $nominal = 0;
        }
        return $nominal;
    }
    
    public function getBillCustomerJson($id){
        $query=DB::table('customer_bills')->where('customer_id', $id)->where('is_paid', false)->orderBy('with_pph')->get();
        foreach ($query as $key => $value) {
            $get_total=$this->getBillDetailCustomerJson($value->id);
            $value->total=$get_total['total'];
            $value->paid=$get_total['paid'];
            // $getKekurangan = DB::table('kekurangan_bayar')->select('kekurangan')->where('customer_bill_id', $value->id)->orderBy('id', 'DESC');
            // if($getKekurangan->count() > 0){
            //     $value->kekurangan = $getKekurangan->first()->kekurangan;
            // }
            // else{
            //     $value->kekurangan = 
            // }
        }
        $data=array(
            'data'  => $query
        );
        return $data;
    }
    public function getBillDetailCustomerJson($id){
        $query=DB::table('customer_bills')->where('id', $id)->first();
        $total=$query->amount;
        // if ($query->end_payment == true) {
        //     if ($query->install_order_id != null) {
        //         $get_addendum=DB::table('customer_bill_others')
        //                             ->select(DB::raw('COALESCE(SUM(CASE WHEN is_increase = 1 THEN amount ELSE 0 END), 0) as total_add'), DB::raw('COALESCE(SUM(CASE WHEN is_increase = 0 THEN amount ELSE 0 END), 0) as total_min'))
        //                             ->where('install_order_id', $query->install_order_id)
        //                             ->first();
        //         $total+=($get_addendum->total_add - $get_addendum->total_min);
        //     }else{
        //         $get_addendum=DB::table('customer_bill_others')
        //                             ->where('order_id', $query->order_id)
        //                             ->select(DB::raw('COALESCE(SUM(CASE WHEN is_increase = 1 THEN amount ELSE 0 END), 0) as total_add'), DB::raw('COALESCE(SUM(CASE WHEN is_increase = 0 THEN amount ELSE 0 END), 0) as total_min'))
        //                             ->get();
        //         $total+=($get_addendum->total_add - $get_addendum->total_min);
        //     }
        // }
        $paid=DB::table('customer_bill_ds')->select(DB::raw('COALESCE(SUM(amount), 0) as amount_paid'))->where('customer_bill_id', $id)->first();
        // $total-=$paid->amount_paid;

        $data=array(
            'data'  => $query,
            'total' => $total,
            'paid'  => $paid->amount_paid
        );
        return $data;
    }
    public function saveBillCust(Request $request) {
        $bill_id=$request->check_id;
        $total=$this->currency($request->total);
        $paid_more=$this->currency($request->paid_more);
        $paid_less=$this->currency($request->paid_less);
        $amount_bill=$request->amount;
        $total_all=$request->total_all;
        $pay_date=$request->pay_date;
        $notes=$request->notes;
        $check_pph=$request->check_pph;
        $get_bill_id=$request->bill_id;
        $pph=$request->pph;
        $paid_more_pph=0;
        $jumlah_bayar = $request->jumlah_bayar;
        $uang_diterima = $this->currency($request->uang_diterima);
        $saldo_lebih_bayar = $request->saldo_lebih_bayar;
        if ($paid_more != 0) {
            if ($bill_id != null) {
                foreach ($bill_id as $key => $value) {
                    foreach ($get_bill_id as $k => $v) {
                        if ($value == $v) {
                            $paid_more_pph+=$pph[$k];
                        }
                    }
                }
            }

            if ($paid_more >= $paid_more_pph) {
                $paid_more-=$paid_more_pph;
            }else{
                $paid_more_pph=0;
            }
        }
        
        $period_year = date('Y');
        $period_month = date('m');
        $bill_no = $this->generateTransactionNo('BILL', $period_year, $period_month, $this->site_id );
        $paid_cust=null;
        try
        {
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . 'inv/base/PaidCustomer']);
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
            $paid_cust=$response_array['data'];
        } catch(RequestException $exception) {
        }
        $temp_total=$total;
        $temp_no_bill='';
        foreach ($bill_id as $key => $value) {
            foreach ($get_bill_id as $k => $v) {
                if ($value == $v) {
                    $amount=$amount_bill[$k];

                    $detail_bill=DB::table('customer_bills')->where('id', $value)->first();

                    $jumlah_bayar_plus_lebih_bayar = $jumlah_bayar[$k] + $saldo_lebih_bayar;
                    if ($jumlah_bayar_plus_lebih_bayar >= $amount_bill[$k]) {
                        DB::table('customer_bills')->where('id', $value)->update(array('is_paid' => true));
                    }

                    if($detail_bill->terbayar == null){
                        DB::table('customer_bills')->where('id', $value)->update(array('terbayar' => $jumlah_bayar_plus_lebih_bayar));
                    }
                    else{
                        DB::table('customer_bills')->where('id', $value)->increment('terbayar', $jumlah_bayar_plus_lebih_bayar);
                    }

                    $saldo_lebih_bayar = $jumlah_bayar_plus_lebih_bayar - $amount_bill[$k];

                    // if($saldo_lebih_bayar > 0){
                    $cekCalculate = DB::table('calculateplusminus')->where('customer_id', $request->input('customer_id'))->count('customer_id');
            
                    if($cekCalculate > 0){
                        $getNominal = DB::table('calculateplusminus')->select('nominal')->where('customer_id', $request->input('customer_id'))->first();
                        $nominal = $getNominal->nominal;
                        if($saldo_lebih_bayar >= 0){
                            DB::table('calculateplusminus')
                            ->where('customer_id', $request->input('customer_id'))
                            ->update([
                                'nominal' => $saldo_lebih_bayar
                            ]);
                        }
                        else{
                            DB::table('calculateplusminus')
                            ->where('customer_id', $request->input('customer_id'))
                            ->update([
                                'nominal' => 0
                            ]);
                        }
                    }
                    else{
                        if($saldo_lebih_bayar >= 0){
                            DB::table('calculateplusminus')->insert([
                                'customer_id' => $request->input('customer_id'),
                                'nominal' => $saldo_lebih_bayar,
                                'is_plus' => true
                            ]);
                        }
                        else{
                            DB::table('calculateplusminus')->insert([
                                'customer_id' => $request->input('customer_id'),
                                'nominal' => 0,
                                'is_plus' => true
                            ]);
                        }
                    }
                    // }
                    if($saldo_lebih_bayar < 0){
                        DB::table('kekurangan_bayar')->insert([
                            'customer_bill_id' => $value,
                            'jumlah_tagihan' => $amount_bill[$k],
                            'terbayar' => $jumlah_bayar_plus_lebih_bayar,
                            'kekurangan' => $amount_bill[$k] - $jumlah_bayar_plus_lebih_bayar,
                            'tanggal' => date('Y-m-d')
                        ]);
                    }
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

            $temp_no_bill.=$detail_bill->no.', ';
            $bill_d_no = $this->generateTransactionNo('PAY_BILL', $period_year, $period_month, $this->site_id );
            $cust_bill_d=null;
            try
            {
                $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . 'inv/base/CustomerBillD']);
                $reqBody = [
                    'headers' => $headers,
                'json' => [
                        'customer_bill_id' => $value,
                        'wop' => $request->input('wop'),
                        'bank_number' => $request->input('bank_number'),
                        'atas_nama' => $request->input('atas_nama'),
                        'ref_code' => $request->input('ref_code'),
                        'id_bank' => $request->input('id_bank'),
                        'amount' => $jumlah_bayar_plus_lebih_bayar,
                        'pay_date' => $pay_date,
                        'no'  => $bill_d_no
                    ]
                ]; 
                $response = $client->request('POST', '', $reqBody); 
                $body = $response->getBody();
                $content = $body->getContents();
                $response_array = json_decode($content,TRUE);
                $cust_bill_d=$response_array['data'];
            } catch(RequestException $exception) {
            }

            try
            {
                $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . 'inv/base/PaidCustomerD']);
                $reqBody = [
                    'headers' => $headers,
                'json' => [
                        'paid_customer_id'  => $paid_cust['id'],
                        'customer_bill_id' => $value,
                        'customer_bill_d_id' => $cust_bill_d['id'],
                        'amount' => $amount,
                    ]
                ]; 
                $response = $client->request('POST', '', $reqBody); 
                $body = $response->getBody();
                $content = $body->getContents();
                $response_array = json_decode($content,TRUE);
            } catch(RequestException $exception) {
            }
        }
        if ($request->wop == 'giro') {
            $giro_no = $this->generateTransactionNo('GIRO', $period_year, $period_month, $this->site_id );
            try
            {
                $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . 'inv/base/Giro']);
                $reqBody = [
                    'headers' => $headers,
                'json' => [
                        // 'customer_bill_id' => $request->input('bill_id'),
                        // 'customer_bill_d_id' => $cust_bill_d['id'],
                        // 'order_id' => $cust_bill['order_id'],
                        'paid_customer_id'  => $paid_cust['id'],
                        'amount' => $total,
                        'pay_date' => null,
                        'site_id'   => $this->site_id,
                        'no'  => $giro_no
                    ]
                ]; 
                $response = $client->request('POST', '', $reqBody); 
                $body = $response->getBody();
                $content = $body->getContents();
                $response_array = json_decode($content,TRUE);
            } catch(RequestException $exception) {
            }
        }
        if (($total_all - $total) > 10000) {
            $total_all=$total;
            $paid_more=0;
            $paid_less=0;
            $paid_more_pph=0;
        }
        $input_jurnal=array(
            'paid_customer_id'  => $paid_cust['id'],
            'customer_id' => $request->input('customer_id'),
            'bkm' => $request->input('bkm'),
            'total' => $total,
            'total_all' => $total_all,
            'paid_more' => $paid_more,
            'paid_more_pph' => $paid_more_pph,
            'paid_less' => $paid_less,
            'user_id'   => $this->user_id,
            'akun'      => $request->input('wop') == 'giro' ? 36 : $request->account_payment,
            'lawan'      => 151,
            'deskripsi'     => 'Pembayaran Customer No '.$paid_cust['no'].' dari No Tagihan '.rtrim($temp_no_bill, ', '),
            'tgl'       => $pay_date,
            'location_id'   => $this->site_id
        );

        

        // save to plussminusbill & calculateplussminus
        if ($uang_diterima != $total_all) {
            //$akunKurangLebih=$this->createAccount($request->input('bkm'));

            $input_jurnal=array(
                'paid_customer_id'  => $paid_cust['id'],
                'customer_id' => $request->input('customer_id'),
                'bkm' => $request->input('bkm'),
                'total' => $total,
                'total_all' => $total_all,
                'paid_more' => $paid_more,
                'paid_more_pph' => $paid_more_pph,
                'paid_less' => $paid_less,
                'user_id'   => $this->user_id,
                'akun'      => $request->input('wop') == 'giro' ? 36 : $request->account_payment,
                'lawan'      => 151,
                'deskripsi'     => 'Pembayaran Customer No '.$paid_cust['no'].' dari No Tagihan '.rtrim($temp_no_bill, ', '),
                'tgl'       => $pay_date,
                'location_id'   => $this->site_id,
                'selisih' => $uang_diterima - $total,
                'akun_kurang_lebih' => $request->akun_kurang_lebih,
                'uang_diterima' => $uang_diterima,
            );

            $this->journalPaidCustBill($input_jurnal);
            // save to plussminusbill
            $id_last=DB::table('tbl_trx_akuntansi')->max('id_trx_akun');
            DB::table('plusminusbill')->insert([
                'customer_id' => $request->input('customer_id'),
                'nominal' => $uang_diterima - $total_all,
                'is_plus' => true,
                'id_trx_akun' => $id_last,
                'tanggal' => $pay_date,
            ]);
            
            // save to calculateplussminus (kondisinya lebih bayar)
            if($uang_diterima - $total_all > 0){
                // $cekCalculate = DB::table('calculateplusminus')->where('customer_id', $request->input('customer_id'))->count('customer_id');
    
                // if($cekCalculate > 0){
                //     $getNominal = DB::table('calculateplusminus')->select('nominal')->where('customer_id', $request->input('customer_id'))->first();
                //     $nominal = $getNominal->nominal;
    
                //     DB::table('calculateplusminus')
                //     ->where('customer_id', $request->input('customer_id'))
                //     ->update([
                //         'nominal' => $nominal + ($uang_diterima - $total_all)
                //     ]);
                // }
                // else{
                //     DB::table('calculateplusminus')->insert([
                //         'customer_id' => $request->input('customer_id'),
                //         'nominal' => $uang_diterima - $total_all,
                //         'is_plus' => true
                //     ]);
                // }
            }
            elseif($uang_diterima - $total_all < 0){
                // foreach ($bill_id as $key => $value) {
                //     DB::table('kekurangan_bayar')->insert([
                //         'customer_bill_id' => $value,
                //         'jumlah_tagihan' => $amount_bill[$key],
                //         'terbayar' => $jumlah_bayar[$key],
                //         'kekurangan' => $amount_bill[$key] - $jumlah_bayar[$key],
                //         'tanggal' => date('Y-m-d')
                //     ]);
                // }
            }
        }
        else{
            $input_jurnal=array(
                'paid_customer_id'  => $paid_cust['id'],
                'customer_id' => $request->input('customer_id'),
                'bkm' => $request->input('bkm'),
                'total' => $total,
                'total_all' => $total_all,
                'paid_more' => $paid_more,
                'paid_more_pph' => $paid_more_pph,
                'paid_less' => $paid_less,
                'user_id'   => $this->user_id,
                'akun'      => $request->input('wop') == 'giro' ? 36 : $request->account_payment,
                'lawan'      => 151,
                'deskripsi'     => 'Pembayaran Customer No '.$paid_cust['no'].' dari No Tagihan '.rtrim($temp_no_bill, ', '),
                'tgl'       => $pay_date,
                'location_id'   => $this->site_id,
                'selisih' => $uang_diterima - $total,
                'uang_diterima' => $uang_diterima,
            );

            $this->journalPaidCustBill($input_jurnal);
        }
        return redirect('inventory/paid_list_customer');
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

    public function createAccount($noBkm){
        $kurangLebihAkun=$this->getNoAkun(65);
        $noAkun=$this->explodeNoAkun($kurangLebihAkun['no_akun_main']);
        $dataNewAkun=array(
            'no_akun'   => $noAkun[0].'.'.$noAkun[1].'.'.$noAkun[2].'.'.($kurangLebihAkun['total'] + 1),
            'nama_akun' => 'Kurang Bayar /Lebih Bayar '.$noBkm,
            'id_main_akun' => 65,
            'level' => 3,
            'sifat_debit'     => 0,
            'sifat_kredit'    => 1,   
            'id_parent'       => $kurangLebihAkun['id_parent'],
            'turunan1'        => $kurangLebihAkun['turunan1'],
            'turunan2'        => 65,
            'turunan3'        => $kurangLebihAkun['turunan3'],
            'turunan4'        => $kurangLebihAkun['turunan4'], 
        );
        $idNewAkun=$this->saveAccount($dataNewAkun);
        $data=array(
            'idNewAkun' => $idNewAkun,
        );
        return $data;
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
    
    private function journalPaidCustBill($data){
        $data_trx=array(
            'deskripsi'     => $data['deskripsi'],
            'location_id'     => $data['location_id'],
            'tanggal'       => $data['tgl'],
            'user_id'       => $data['user_id'],
            'paid_customer_id'  => $data['paid_customer_id'],
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
            
            if($data['selisih'] == 0){
                $akun=array(
                    'id_trx_akun'   => $id_last,
                    'id_akun'       => $data['akun'],
                    'jumlah'        => $data['total'],
                    'tipe'          => "DEBIT",
                    'keterangan'    => 'akun',
                    'no'            => ($data['akun'] != 36 ? $no : '')
                );
            }
            // jika ada selisih bayar
            else{
                $akun=array(
                    'id_trx_akun'   => $id_last,
                    'id_akun'       => $data['akun'],
                    'jumlah'        => $data['uang_diterima'],
                    'tipe'          => "DEBIT",
                    'keterangan'    => 'akun',
                    'no'            => ($data['akun'] != 36 ? $no : '')
                );
            }
            DB::table('tbl_trx_akuntansi_detail')->insert($akun);
            if($data['akun'] == 24 || $data['akun'] == 101){
                $acccon = new AkuntanController();
                $warehouse_id=$data['akun'] == 24 ? 2 : 3;
                $acccon->countSaldoCash($this->site_id, $warehouse_id, "in", $data['total']);
            }
            if($data['selisih'] == 0){
                $lawan=array(
                    'id_trx_akun'   => $id_last,
                    'id_akun'       => $data['lawan'],
                    'jumlah'        => $data['total_all'],
                    'tipe'          => "KREDIT",
                    'keterangan'    => 'lawan',
                );
                DB::table('tbl_trx_akuntansi_detail')->insert($lawan);
            }
            // jika bayar lebih
            elseif($data['selisih'] > 0){
                // untuk lebih bayar nunggu dari mas arif
                $lawanPiutang=array(
                    'id_trx_akun'   => $id_last,
                    'id_akun'       => $data['lawan'],
                    'jumlah'        => $data['total_all'],
                    'tipe'          => "KREDIT",
                    'keterangan'    => 'lawan',
                );
                DB::table('tbl_trx_akuntansi_detail')->insert($lawanPiutang);

                $lawanKurangLebih=array(
                    'id_trx_akun'   => $id_last,
                    'id_akun'       => $data['akun_kurang_lebih'],
                    'jumlah'        => $data['selisih'],
                    'tipe'          => "KREDIT",
                    'keterangan'    => 'lawan',
                );
                DB::table('tbl_trx_akuntansi_detail')->insert($lawanKurangLebih);
            }
            // jika bayar kurang
            else{
                $lawanPiutang=array(
                    'id_trx_akun'   => $id_last,
                    'id_akun'       => $data['lawan'],
                    'jumlah'        => $data['uang_diterima'],
                    'tipe'          => "KREDIT",
                    'keterangan'    => 'lawan',
                );
                DB::table('tbl_trx_akuntansi_detail')->insert($lawanPiutang);

                // $lawanKurangLebih=array(
                //     'id_trx_akun'   => $id_last,
                //     'id_akun'       => $data['akun_kurang_lebih'],
                //     'jumlah'        => $data['selisih'] * -1,
                //     'tipe'          => "DEBIT",
                //     'keterangan'    => 'lawan',
                // );
                // DB::table('tbl_trx_akuntansi_detail')->insert($lawanKurangLebih);
            }
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
            if ($data['paid_more_pph'] > 0) {
                $paid_more=array(
                    'id_trx_akun'   => $id_last,
                    'id_akun'       => 99,
                    'jumlah'        => $data['paid_more_pph'],
                    'tipe'          => "KREDIT",
                    'keterangan'    => 'lawan',
                );
                DB::table('tbl_trx_akuntansi_detail')->insert($paid_more);
            }
        }
    }
    
    public function listPaidCustomers(){
        $query=DB::table('paid_customers')
                    ->select('paid_customers.*', 'customers.coorporate_name')        
                    ->join('customers', 'customers.id', 'paid_customers.customer_id')
                    ->where('site_id', $this->site_id)
                    ->get();
        foreach($query as $row){
            $dt=DB::table('paid_customer_ds')
                    ->join('customer_bills', 'customer_bills.id', 'paid_customer_ds.customer_bill_id')
                    ->where('paid_customer_id', $row->id)
                    ->select('bill_no','invoice_no', 'invoice_no', 'customer_bills.no')->get();
            $bill_no=$invoice_no=$no='';
            foreach($dt as $value){
                $bill_no.=($value->bill_no.', ');
                $invoice_no.=($value->invoice_no.', ');
                $no.=($value->no.', ');
            }
            $row->no_payment=rtrim($no, ', ');
            $row->bill_no=rtrim($bill_no, ', ');
            $row->invoice_no=rtrim($invoice_no, ', ');
        }
        $data=DataTables::of($query)->make(true);

        return $data;
    }
    
    public function listPaidCustomersD($id){
        $query=DB::table('paid_customer_ds')
                    ->select('customer_bills.*', 'paid_customer_ds.amount', 'paid_customer_ds.customer_bill_d_id')
                    ->join('customer_bills', 'customer_bills.id', 'paid_customer_ds.customer_bill_id')
                    ->where('paid_customer_id', $id)
                    ->get();
        $data=array(
            'data'  => $query
        );
        return $data;
    }
    public function paidListSupplier(){
        return view('pages.inv.inventory_transaction.payment_supplier');
    }
    public function paidSupplierBill(){
        $suppliers = null;
        try
        {
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . '/crm/base/MSupplier']);  
            $response = $client->request('GET', '', ['headers' => $headers]);  
            $body = $response->getBody();
            $content = $body->getContents();
            $response_array = json_decode($content,TRUE);
            $suppliers = $response_array['data'];
        } catch(RequestException $exception) {      
        }
        $data=array(
            'suppliers'  => $suppliers,
            'list_bank' => DB::table('list_bank')->get()
        );
        return view('pages.inv.inventory_transaction.paid_bill_supplier', $data);
    }
    public function getBillSupplierJson($id){
        $query=DB::table('payment_suppliers')->where('m_supplier_id', $id)->where('is_paid', false)->get();
        foreach ($query as $key => $value) {
            $get_total=$this->getBillDetailSupplierJson($value->id);
            $value->detail=$get_total['data'];
            $value->total=$get_total['total'];
            $value->paid=$get_total['paid'];
            $value->total_without_ppn=$get_total['total_without_ppn'];
            $value->ppn=$get_total['ppn'];
            $value->without_ppn=$get_total['without_ppn'];
        }
        $data=array(
            'data'  => $query
        );
        return $data;
    }
    public function getBillDetailSupplierJson($id){
        $query=DB::table('payment_suppliers')
                        ->where('payment_suppliers.id', $id)
                        ->select('payment_suppliers.*', 'purchases.with_ppn as with_ppn_p', 'purchase_assets.with_ppn as with_ppn_pa', 'purchase_services.with_ppn as with_ppn_ps', 'purchases.no as purchase_no', 'purchase_assets.no as purchase_asset_no', 'purchase_services.no as purchase_service_no', 'purchases.notes as p_notes', 'purchase_assets.notes as pa_notes', 'purchase_services.notes as ps_notes', 'purchases.is_without_ppn as without_ppn_p', 'purchase_assets.is_without_ppn as without_ppn_pa', 'purchase_services.is_without_ppn as without_ppn_ps')
                        ->leftJoin('purchases', 'purchases.id', 'payment_suppliers.purchase_id')
                        ->leftJoin('purchase_assets', 'purchase_assets.id', 'payment_suppliers.purchase_asset_id')
                        ->leftJoin('purchase_services', 'purchase_services.id', 'payment_suppliers.purchase_service_id')
                        ->first();
        $total=$query->amount;
        $total_without_ppn=$query->amount;
        $with_ppn=false;
        if ($query->with_ppn_p == true) {
            $with_ppn=true;
        }else if ($query->with_ppn_pa == true) {
            $with_ppn=true;
        }else if ($query->with_ppn_ps == true) {
            $with_ppn=true;
        }
        // if ($with_ppn == true) {
        //     $total=($total / 1.1);
        // }
        $ppn=0;
        if ($with_ppn == false) {
            $ppn=($total * 0.1);
            $total+=($total * 0.1);
        }
        $without_ppn=0;
        if ($query->without_ppn_p == true) {
            $without_ppn=1;
        }else if ($query->without_ppn_pa == true) {
            $without_ppn=1;
        }else if ($query->without_ppn_ps == true) {
            $without_ppn=1;
        }
        $paid=DB::table('payment_supplier_ds')->select(DB::raw('COALESCE(SUM(amount), 0) as amount_paid'))->where('payment_supplier_id', $id)->first();
        $data=array(
            'data'  => $query,
            'total' => $total,
            'paid'  => $paid->amount_paid,
            'total_without_ppn' => $total_without_ppn,
            'ppn' => $ppn,
            'without_ppn' => $without_ppn
        );
        return $data;
    }
    public function savePaidSupplier(Request $request) {
        $bill_id=$request->check_id;
        $get_bill_id=$request->bill_id;
        // $total_bayar=$this->currency($request->total_bayar);
        $total=$this->currency($request->total);
        $paid_more=$this->currency($request->paid_more);
        $paid_less=$this->currency($request->paid_less);
        $total_all=$request->total_all;
        $pay_date=$request->pay_date;
        $paid_no=$request->paid_no;
        $notes=$request->notes;
        $amount_bill=$request->amount;

        $period_year = date('Y');
        $period_month = date('m');
        $bill_no = $this->generateTransactionNo('PAID_SPPL', $period_year, $period_month, $this->site_id );
        $paid_sppl=null;
        try
        {
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . 'inv/base/PaidSupplier']);
            $reqBody = [
                'headers' => $headers,
                'json' => [
                    'm_supplier_id' => $request->input('supplier_id'),
                    'no' => $bill_no,
                    'notes' => $notes,
                    'wop' => $request->input('wop'),
                    'bank_number' => $request->input('bank_number'),
                    'ref_code' => $request->input('ref_code'),
                    'id_bank' => $request->input('id_bank'),
                    'amount' => $total,
                    'amount_ppn' => $request->ppn,
                    'paid_date' => $pay_date,
                    'site_id' => $this->site_id,
                ]
            ]; 
            $response = $client->request('POST', '', $reqBody); 
            $body = $response->getBody();
            $content = $body->getContents();
            $response_array = json_decode($content,TRUE);
            $paid_sppl=$response_array['data'];
        } catch(RequestException $exception) {
        }
        
        $temp_total=$total;
        $temp_amount=array();
        $temp_no_bill='';
        foreach ($bill_id as $key => $value) {
            $bill_detail=$this->getBillDetailSupplierJson($value);
            // $amount=$bill_detail['total'];
            $get_paid_no='';
            foreach ($get_bill_id as $k => $v) {
                if ($value == $v) {
                    $amount=$amount_bill[$k];
                    $get_paid_no=$paid_no[$k];
                }
            }
            $cek_amount=$amount;
            $saving_amount=($key == (count($bill_id)-1) ? $temp_total : $amount);
            if ($temp_total >= $amount) {
                $temp_total-=$amount;//untuk mengurangi sisa total yang dibayar dengan total tagihan
            }else{
                $amount=$temp_total;//total tagihan di ganti dengan sisa yang dibayar jika sisa kurang dari total tagihan
                $temp_total=0;
            }
            $get_bill=DB::table('payment_suppliers')->where('id', $value)->first();
            //update nomor invoice
            
            DB::table('payment_suppliers')->where('id', $value)->update(array('paid_no' => $get_paid_no));//update no tagihan
            $temp_no_bill.=$get_bill->no.', ';
            $temp_amount[]=array('amount' => (($cek_amount - $amount) <= 10000 ? $cek_amount : $amount), 'ppn' => ($bill_detail['without_ppn'] == 0 ? $amount * 0.1 : 0), 'tipe' => $get_bill->payment_po);
            $supplier_d=null;
            try
            {
                $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . 'inv/base/PaymentSupplierD']);
                $reqBody = [
                    'headers' => $headers,
                'json' => [
                        'payment_supplier_id'   => $value,
                        'wop' => $request->input('wop'),
                        'bank_number' => $request->input('bank_number'),
                        'atas_nama' => $request->input('atas_nama'),
                        'ref_code' => $request->input('ref_code'),
                        'id_bank' => $request->input('id_bank'),
                        'amount' => $saving_amount,
                        // 'delivery_fee' => $delivery_fee,
                        // 'description' => $description,
                        'pay_date' => $request->input('pay_date'),
                        'user_id'   => $this->user_id,
                    ]
                ]; 
                $response = $client->request('POST', '', $reqBody); 
                $body = $response->getBody();
                $content = $body->getContents();
                $response_array = json_decode($content,TRUE);
                $supplier_d=$response_array['data'];
            } catch(RequestException $exception) {
            }

            try
            {
                $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . 'inv/base/PaidSupplierD']);
                $reqBody = [
                    'headers' => $headers,
                'json' => [
                        'paid_supplier_id'  => $paid_sppl['id'],
                        'payment_supplier_id' => $value,
                        'payment_supplier_d_id' => $supplier_d['id'],
                        'amount' => $saving_amount,
                    ]
                ]; 
                $response = $client->request('POST', '', $reqBody); 
                $body = $response->getBody();
                $content = $body->getContents();
                $response_array = json_decode($content,TRUE);
            } catch(RequestException $exception) {
            }
            if (($cek_amount - $amount) <= 10000) {
                DB::table('payment_suppliers')->where('id', $value)->update(array('is_paid' => true));
            }
        }
        if ($request->wop == 'giro') {
            $giro_no = $this->generateTransactionNo('GIRO', $period_year, $period_month, $this->site_id );
            try
            {
                $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . 'inv/base/Giro']);
                $reqBody = [
                    'headers' => $headers,
                'json' => [
                        'paid_supplier_id'  => $paid_sppl['id'],
                        'amount' => $total,
                        'pay_date' => null,
                        'site_id'   => $this->site_id,
                        'is_fill'   => 1,
                        'no'  => $giro_no
                    ]
                ]; 
                $response = $client->request('POST', '', $reqBody); 
                $body = $response->getBody();
                $content = $body->getContents();
                $response_array = json_decode($content,TRUE);
            } catch(RequestException $exception) {
            }
        }
        if (($total_all - $total) > 10000) {
            $total_all=$total;
            $paid_more=0;
            $paid_less=0;
        }
        $input_jurnal=array(
            'paid_supplier_id'  => $paid_sppl['id'],
            'm_supplier_id' => $request->input('supplier_id'),
            'total' => $temp_amount,
            'total_all' => $total,
            'paid_more' => $paid_more,
            'paid_less' => $paid_less,
            'user_id'   => $this->user_id,
            'akun'      => $request->input('wop') == 'giro' ? 36 : $request->account_payment,
            'deskripsi'     => 'Pembayaran Supplier No '.$paid_sppl['no'].' dari Tagihan No '.rtrim($temp_no_bill, ', '),
            'tgl'       => $request->pay_date,
            'no_bkk'    => $request->bkk,
            'location_id'   => $this->site_id
        );
        $this->journalPaidSupplier($input_jurnal);
        return redirect('inventory/paid_list_supplier');
    }
    private function journalPaidSupplier($data){
        $data_trx=array(
            'deskripsi'     => $data['deskripsi'],
            'location_id'     => $data['location_id'],
            'tanggal'       => $data['tgl'],
            'user_id'       => $data['user_id'],
            'paid_supplier_id'  => $data['paid_supplier_id'],
            'm_supplier_id'  => $data['m_supplier_id'],
        );
        $insert=DB::table('tbl_trx_akuntansi')->insert($data_trx);
        if ($insert) {
            $id_last=DB::table('tbl_trx_akuntansi')->max('id_trx_akun');

            foreach ($data['total'] as $key => $value) {
                $lawan=array(
                    'id_trx_akun'   => $id_last,
                    'id_akun'       => $value['tipe'] == 'credit' ? 147 : 139,
                    'jumlah'        => $value['amount'],
                    'tipe'          => "DEBIT",
                    'keterangan'    => 'akun',
                );
                DB::table('tbl_trx_akuntansi_detail')->insert($lawan);
                // if ($value['ppn'] > 0) {
                //     $lawan=array(
                //         'id_trx_akun'   => $id_last,
                //         'id_akun'       => $value['tipe'] == 'credit' ? 67 : 133,
                //         'jumlah'        => $value['ppn'],
                //         'tipe'          => "DEBIT",
                //         'keterangan'    => 'akun',
                //     );
                //     DB::table('tbl_trx_akuntansi_detail')->insert($lawan);
                // }
            }
            $no=$data['no_bkk'];
            if ($no == '') {
                $acccon = new AkuntanController();
                $no=$acccon->createNo($data['akun'], "KREDIT");
            }
            $akun=array(
                'id_trx_akun'   => $id_last,
                'id_akun'       => $data['akun'],
                'jumlah'        => $data['total_all'],
                'tipe'          => "KREDIT",
                'keterangan'    => 'lawan',
                'no'            => ($data['akun'] != 36 ? $no : '')
            );
            DB::table('tbl_trx_akuntansi_detail')->insert($akun);
            if($data['akun'] == 24 || $data['akun'] == 101){
                $acccon = new AkuntanController();
                $warehouse_id=$data['akun'] == 24 ? 2 : 3;
                $acccon->countSaldoCash($this->site_id, $warehouse_id, "out", $data['total_all']);
            }
            if ($data['paid_less'] > 0) {
                $paid_less=array(
                    'id_trx_akun'   => $id_last,
                    'id_akun'       => 166,
                    'jumlah'        => $data['paid_less'],
                    'tipe'          => "KREDIT",
                    'keterangan'    => 'lawan',
                );
                DB::table('tbl_trx_akuntansi_detail')->insert($paid_less);
            }
            if ($data['paid_more'] > 0) {
                $paid_more=array(
                    'id_trx_akun'   => $id_last,
                    'id_akun'       => 163,
                    'jumlah'        => $data['paid_more'],
                    'tipe'          => "DEBIT",
                    'keterangan'    => 'lawan',
                );
                DB::table('tbl_trx_akuntansi_detail')->insert($paid_more);
            }
        }
    }
    public function listPaidSuppliers(){
        $query=DB::table('paid_suppliers')
                    ->select('paid_suppliers.*', 'm_suppliers.name')        
                    ->join('m_suppliers', 'm_suppliers.id', 'paid_suppliers.m_supplier_id')
                    ->where('site_id', $this->site_id)
                    ->get();
        foreach($query as $row){
            $dt=DB::table('paid_supplier_ds')
                    ->join('payment_suppliers', 'payment_suppliers.id', 'paid_supplier_ds.payment_supplier_id')
                    ->where('paid_supplier_id', $row->id)
                    ->pluck('payment_suppliers.paid_no')->toArray();
            $txt=implode(', ',$dt);
            $row->dt=rtrim($txt, ', ');
        }
        $data=DataTables::of($query)->make(true);

        return $data;
    }
    public function listPaidSuppliersD($id){
        $query=DB::table('paid_supplier_ds')
                    ->select('payment_suppliers.*', 'paid_supplier_ds.amount')
                    ->join('payment_suppliers', 'payment_suppliers.id', 'paid_supplier_ds.payment_supplier_id')
                    ->where('paid_supplier_id', $id)
                    ->get();
        $data=array(
            'data'  => $query
        );
        return $data;
    }
    public function exportDebtList(Request $request) {
        
        $data_temp=array();
        $supplier_selected=array();
        $all_supplier=false;
        $supplier=$request->input('suppl_single');
        $supplier_selected=$request->input('suppl_single');
        $all_supplier=false;
        foreach ($supplier as $key => $value) {
            if ($value == 'all') {
                $all_supplier=true;
                $supplier=DB::table('m_suppliers')->pluck('id');
            }
        }
        $id=[147];//Hutang usaha
        $query=DB::table('tbl_akun_detail')
                    ->whereIn('id_akun', $id)
                    // ->orWhereIn('turunan1', $id)
                    // ->orWhereIn('turunan2', $id)
                    // ->orWhereIn('turunan3', $id)
                    // ->orWhereIn('turunan4', $id)
                    ->pluck('id_akun');
                    
        $user_id = request()->session()->get('user.id');
        $user = DB::table('users')->where('id', $this->user_id)->first();
        $location_id = $user->site_id;

        if ($request->input('date')) {
            $date1=$request->input('date');
            $date2=$request->input('date2');
        }
        // $akun=DB::table('tbl_akun')->where('id_akun', $id)->first();
        $startTime = strtotime( $date1 );
        $endTime = strtotime( $date2 );
        $date=date('Y-m', $startTime);

        $first_date_month=$date.'-01';
        $saldo_before_start_date=0;
        foreach ($supplier as $key => $value) {
            $min=$startTime - 86400;//kurangi sehari
            $perubahan_saldo=DB::table('tbl_trx_akuntansi_detail as trd')
                                ->select(DB::raw("COALESCE(SUM(CASE WHEN tipe = 'DEBIT' THEN jumlah ELSE 0 END), 0) AS total_debit"), DB::raw("COALESCE(SUM(CASE WHEN tipe = 'KREDIT' THEN jumlah ELSE 0 END), 0) AS total_kredit"))
                                ->join('tbl_trx_akuntansi as tra', 'trd.id_trx_akun', 'tra.id_trx_akun')
                                ->whereIn('trd.id_akun', $query)
                                ->where('tra.location_id', $location_id)
                                // ->where('tanggal', '>=', $first_date_month)
                                ->where('tra.m_supplier_id', $value)
                                ->where('tanggal', '<=', date('Y-m-d', $min))
                                ->whereNull('notes')
                                ->first();
            $saldo_before_start_date = $perubahan_saldo->total_kredit - $perubahan_saldo->total_debit;
            
            $bulan=explode('-', $date);
            $detail=array();
            $account_payment=DB::table('tbl_akun')->where('id_main_akun', 13)->pluck('id_akun')->toArray();
            
            for ( $i = $startTime; $i <= $endTime; $i = $i + 86400 ) {
                $thisDate = date( 'Y-m-d', $i ); // 2010-05-01, 2010-05-02, etc
                $dtSaldo=DB::table('tbl_trx_akuntansi_detail as trd')
                                    ->select('trd.*', 'trd.no as note_no', 'tra.*', 'ta.nama_akun', 'ta.no_akun', 'p.no as purchase_no', 'pa.no as purchase_asset_no', 'ps.no as ps_no', 'it.no_surat_jalan', 'p.notes as p_notes', 'pa.notes as pa_notes', DB::raw('COALESCE((SELECT sum(tbl_trx_akuntansi_detail.jumlah) FROM tbl_trx_akuntansi_detail where tbl_trx_akuntansi_detail.id_trx_akun=trd.id_trx_akun and tbl_trx_akuntansi_detail.id_akun=67), 0) as ppn'))
                                    ->join('tbl_trx_akuntansi as tra', 'trd.id_trx_akun', 'tra.id_trx_akun')
                                    ->join('tbl_akun as ta', 'trd.id_akun', 'ta.id_akun')
                                    ->leftJoin('inv_trxes as it', 'it.id', 'tra.inv_trx_id')
                                    ->leftJoin('purchases as p', 'tra.purchase_id', 'p.id')
                                    ->leftJoin('purchase_assets as pa', 'tra.purchase_asset_id', 'pa.id')
                                    ->leftJoin('paid_suppliers as ps', 'tra.paid_supplier_id', 'ps.id')
                                    ->whereIn('trd.id_akun', $query)
                                    ->where('tra.location_id', $location_id)
                                    ->where('tanggal', $thisDate)
                                    ->where('tra.m_supplier_id', $value)
                                    ->whereNull('tra.notes')
                                    ->get();
                if (count($dtSaldo) > 0) {
                    foreach ($dtSaldo as $v) {
                        $source=DB::table('tbl_trx_akuntansi_detail as trd')
                                    ->select('no')
                                    ->whereIn('id_akun', $account_payment)
                                    ->where('id_trx_akun', $v->id_trx_akun)
                                    ->first();
                        $v->source=($source != null ? $source->no : null);
                    }
                    $detail[$i]['date']=$thisDate;
                    $detail[$i]['dt']=$dtSaldo;
                }
            }
            $data_temp[$value]['supplier']=DB::table('m_suppliers')->where('id', $value)->first();
            $data_temp[$value]['data']=$detail;
            $data_temp[$value]['perubahan_saldo']=$perubahan_saldo;
        }
        $data = array(
            'date1'     => $date1,
            'date2'     => $date2,
            'data'      => $data_temp,
        );
        return Excel::download(new DebtListExport($data), 'debt_list.xlsx');
    }
    public function exportPiutangList(Request $request) {
        $data_temp_cust=array();
        $customer_selected=array();
        $user_id = request()->session()->get('user.id');
        $user = DB::table('users')->where('id', $this->user_id)->first();
        $location_id = $user->site_id;

        if ($request->input('date')) {
            $date1=$request->input('date');
            $date2=$request->input('date2');
        }
        // $akun=DB::table('tbl_akun')->where('id_akun', $id)->first();
        $startTime = strtotime( $date1 );
        $endTime = strtotime( $date2 );
        $date=date('Y-m', $startTime);

        $first_date_month=$date.'-01';
        $saldo_before_start_date=0;

        //get piutang cust data
        $customer_id=$request->input('customer_id');
        $customer_selected=$request->input('customer_id');
        $all_customer=false;
        $account_payment=DB::table('tbl_akun')->where('id_main_akun', 13)->pluck('id_akun');
        if ($customer_id != null) {
            foreach ($customer_id as $key => $value) {
                if ($value == 'all') {
                    $all_customer=true;
                    $customer_id=DB::table('customers')->pluck('id');
                }
            }
            $piutang_cust_id=[151];
            $piutang_cust=DB::table('tbl_akun_detail')
                        ->whereIn('id_akun', $piutang_cust_id)
                        ->orWhereIn('turunan1', $piutang_cust_id)
                        ->orWhereIn('turunan2', $piutang_cust_id)
                        ->orWhereIn('turunan3', $piutang_cust_id)
                        ->orWhereIn('turunan4', $piutang_cust_id)
                        ->pluck('id_akun');
            foreach ($customer_id as $key => $value) {
                $min=$startTime - 86400;//kurangi sehari
                $perubahan_saldo=DB::table('tbl_trx_akuntansi_detail as trd')
                                    ->select(DB::raw("COALESCE(SUM(CASE WHEN tipe = 'DEBIT' THEN jumlah ELSE 0 END), 0) AS total_debit"), DB::raw("COALESCE(SUM(CASE WHEN tipe = 'KREDIT' THEN jumlah ELSE 0 END), 0) AS total_kredit"))
                                    ->join('tbl_trx_akuntansi as tra', 'trd.id_trx_akun', 'tra.id_trx_akun')
                                    ->whereIn('trd.id_akun', $piutang_cust)
                                    ->where('tra.location_id', $location_id)
                                    // ->where('tanggal', '>=', $first_date_month)
                                    ->where('tra.customer_id', $value)
                                    ->where('tanggal', '<=', date('Y-m-d', $min))
                                    ->whereNull('notes')
                                    ->first();
                $saldo_before_start_date = $perubahan_saldo->total_kredit - $perubahan_saldo->total_debit;
                
                $bulan=explode('-', $date);
                $detail=array();
                for ( $i = $startTime; $i <= $endTime; $i = $i + 86400 ) {
                    $thisDate = date( 'Y-m-d', $i ); // 2010-05-01, 2010-05-02, etc
                    $dtSaldo=DB::table('tbl_trx_akuntansi_detail as trd')
                                    ->select('trd.*', 'trd.no as note_no', 'tra.*', 'ta.nama_akun', 'ta.no_akun', 'o.order_no as order_no', 'io.no as install_order_no', 'pc.no as paid_cust_no', 'cb.bill_no', 'cb.no as cb_no')
                                    ->join('tbl_trx_akuntansi as tra', 'trd.id_trx_akun', 'tra.id_trx_akun')
                                    ->join('tbl_akun as ta', 'trd.id_akun', 'ta.id_akun')
                                    ->leftJoin('orders as o', 'tra.order_id', 'o.id')
                                    ->leftJoin('install_orders as io', 'tra.install_order_id', 'io.id')
                                    ->leftJoin('paid_customers as pc', 'tra.paid_customer_id', 'pc.id')
                                    ->leftJoin('customer_bills as cb', 'tra.customer_bill_id', 'cb.id')
                                    ->whereIn('trd.id_akun', $piutang_cust)
                                    ->where('tra.location_id', $location_id)
                                    ->where('tanggal', $thisDate)
                                    ->where('tra.customer_id', $value)
                                    ->whereNull('tra.notes')
                                    ->get();
                    if (count($dtSaldo) > 0) {
                        foreach ($dtSaldo as $v) {
                            $v->id_source=null;
                            $v->no_source=null;
                            $query=DB::table('tbl_trx_akuntansi_detail')
                                        ->where('id_trx_akun', $v->id_trx_akun)
                                        ->whereIn('id_akun', $account_payment)
                                        ->first();
                            if ($query != null) {
                                $v->id_source=$query->id_trx_akun_detail;
                                $v->no_source=$query->no;
                            }
                        }
                        $detail[$i]['date']=$thisDate;
                        $detail[$i]['dt']=$dtSaldo;
                    }
                }
                $data_temp_cust[$value]['customer']=DB::table('customers')->where('id', $value)->first();
                $data_temp_cust[$value]['data']=$detail;
                $data_temp_cust[$value]['perubahan_saldo']=$perubahan_saldo;
            }
        }
        $data = array(
            'date1'     => $date1,
            'date2'     => $date2,
            'data_cust'      => $data_temp_cust,
        );
        
        return Excel::download(new PiutangListExport($data), 'piutang_list.xlsx');
    }
    public function exportHistoryPurchaseBySupplier(Request $request) {
        $export_id=$request->export_id1 ? $request->export_id1 : array();
        $data_temp=array();
        $supplier_selected=array();
        $supplier=$request->input('suppl_single');
        $supplier_selected=$request->input('suppl_single');
        $all_supplier=false;
        foreach ($supplier as $key => $value) {
            if ($value == 'all') {
                $all_supplier=true;
                $supplier=DB::table('m_suppliers')->pluck('id');
            }
        }
        $asset_id=DB::table('account_assets')->pluck('asset_id')->toArray();
        
        array_push($asset_id, 141, 142, 143, 144, 92);

        $user_id = request()->session()->get('user.id');
        $user = DB::table('users')->where('id', $this->user_id)->first();
        $location_id = $user->site_id;

        if ($request->input('date')) {
            $date1=$request->input('date');
            $date2=$request->input('date2');
        }
        // $akun=DB::table('tbl_akun')->where('id_akun', $id)->first();
        $startTime = strtotime( $date1 );
        $endTime = strtotime( $date2 );
        $date=date('Y-m', $startTime);

        $first_date_month=$date.'-01';
        $saldo_before_start_date=0;
        foreach ($supplier as $key => $value) {
            $bulan=explode('-', $date);
            $detail=array();
            for ( $i = $startTime; $i <= $endTime; $i = $i + 86400 ) {
                $thisDate = date( 'Y-m-d', $i ); // 2010-05-01, 2010-05-02, etc
                $dtSaldo=DB::table('tbl_trx_akuntansi_detail as trd')
                                    ->select(DB::raw('SUM(trd.jumlah) as total'), DB::raw('MAX(p.no) as purchase_no'), DB::raw('MAX(pa.no) as purchase_asset_no'), DB::raw('MAX(p.id) as purchase_id'), DB::raw('MAX(pa.id) as purchase_asset_id'), DB::raw('MAX(p.notes) as purchase_notes'), DB::raw('MAX(pa.notes) as purchase_asset_notes'), DB::raw('MAX(trd.id_trx_akun) as id_trx_akun'), 'it.no_surat_jalan', DB::raw('MAX(it.inv_trx_date) as inv_trx_date'), DB::raw('MAX(it.id) as inv_trx_id'), DB::raw('MAX(ps.paid_no) as paid_no'), DB::raw('MAX(ps.no) as bill_no'))
                                    ->join('tbl_trx_akuntansi as tra', 'trd.id_trx_akun', 'tra.id_trx_akun')
                                    ->join('tbl_akun as ta', 'trd.id_akun', 'ta.id_akun')
                                    ->join('inv_trxes as it', 'tra.inv_trx_id', 'it.id')
                                    ->leftJoin('payment_supplier_details as psd', 'psd.inv_trx_id', 'it.id')
                                    ->leftJoin('payment_suppliers as ps', 'ps.id', 'psd.payment_supplier_id')
                                    ->leftJoin('purchases as p', 'tra.purchase_id', 'p.id')
                                    ->leftJoin('purchase_assets as pa', 'tra.purchase_asset_id', 'pa.id')
                                    ->whereIn('trd.id_akun', $asset_id)
                                    ->whereIn('it.id', $export_id)
                                    ->where('tra.location_id', $location_id)
                                    ->where('tanggal', $thisDate)
                                    ->where('tra.m_supplier_id', $value)
                                    ->whereNull('tra.notes')
                                    ->whereNotNull('tra.inv_trx_id')
                                    ->groupBy('it.no_surat_jalan')
                                    ->get();
                if (count($dtSaldo) > 0) {
                    $detail[$i]['date']=$thisDate;
                    $detail[$i]['dt']=$dtSaldo;
                }
            }
            $data_temp[$value]['supplier']=DB::table('m_suppliers')->where('id', $value)->first();
            $data_temp[$value]['data']=$detail;
        }
        $data = array(
            'date1'     => $date1,
            'date2'     => $date2,
            'data'      => $data_temp,
        );
        // return($data);
        return Excel::download(new HistoryPurchaseSupplierExport($data), 'history_purchase_supplier.xlsx');
    }
    public function exportAgeDebtSupplier(Request $request) {
        $supplier=DB::table('m_suppliers')->get();
        $user_id = request()->session()->get('user.id');
        $user = DB::table('users')->where('id', $this->user_id)->first();
        $location_id = $user->site_id;
        $date_now=date('Y-m-d');
        $one_month_before=date('Y-m-d', strtotime("- 1 month", strtotime($date_now)));
        $two_month_before=date('Y-m-d', strtotime("- 2 month", strtotime($date_now)));
        $three_month_before=date('Y-m-d', strtotime("- 3 month", strtotime($date_now)));
        $four_month_before=date('Y-m-d', strtotime("- 4 month", strtotime($date_now)));
        $five_month_before=date('Y-m-d', strtotime("- 5 month", strtotime($date_now)));
        
        foreach ($supplier as $key => $value) {
            $detail=DB::table('payment_suppliers')
                            ->select(DB::raw("COALESCE(SUM(CASE WHEN due_date < '".$date_now."' and due_date >= '".$one_month_before."' THEN amount ELSE 0 END), 0) as total_in_one_months"), DB::raw("COALESCE(SUM(CASE WHEN due_date < '".$one_month_before."' and due_date >= '".$two_month_before."' THEN amount ELSE 0 END), 0) as total_in_two_months"), DB::raw("COALESCE(SUM(CASE WHEN due_date < '".$two_month_before."' and due_date >= '".$three_month_before."' THEN amount ELSE 0 END), 0) as total_in_three_months"), DB::raw("COALESCE(SUM(CASE WHEN due_date < '".$three_month_before."' and due_date >= '".$four_month_before."' THEN amount ELSE 0 END), 0) as total_in_four_months"), DB::raw("COALESCE(SUM(CASE WHEN due_date < '".$four_month_before."' THEN amount ELSE 0 END), 0) as total_in_five_months"))
                            ->where('m_supplier_id', $value->id)
                            ->where('is_paid', false)
                            ->first();
            $value->detail=$detail;
        }
        
        $data = array(
            'data'      => $supplier,
            'one_month_before' => $one_month_before,
            'two_month_before' => $two_month_before,
            'three_month_before' => $three_month_before,
            'four_month_before' => $four_month_before
        );
        // return($data);
        return Excel::download(new AgeDebtSupplierExport($data), 'age_debt_supplier.xlsx');
    }
    public function piutangAll(Request $request) {
        $customer = null;
        try
        {
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . '/crm/customerdatamain']);  
            $response = $client->request('GET', '', ['headers' => $headers]);  
            $body = $response->getBody();
            $content = $body->getContents();
            $response_array = json_decode($content,TRUE);
            $customer = $response_array['data'];
        } catch(RequestException $exception) {      
        }
        $data_temp_cust=array();
        $date1=date('Y-m-d');
        $date2=date('Y-m-d');
        $customer_selected=array();
        $all_customer=false;
        $saldo_before_start_date=0;
        if ($request->input('customer_id')) {
            $user_id = request()->session()->get('user.id');
            $user = DB::table('users')->where('id', $this->user_id)->first();
            $location_id = $user->site_id;

            if ($request->input('date')) {
                $date1=$request->input('date');
                $date2=$request->input('date2');
            }
            $startTime = strtotime( $date1 );
            $endTime = strtotime( $date2 );
            $date=date('Y-m', $startTime);

            $first_date_month=$date.'-01';

            //get piutang cust data
            $customer_id=$request->input('customer_id');
            $customer_selected=$request->input('customer_id');
            $all_customer=false;
            if ($customer_id != null) {
                foreach ($customer_id as $key => $value) {
                    if ($value == 'all') {
                        $all_customer=true;
                        $customer_id=DB::table('customers')->pluck('id');
                    }
                }
                $account_payment=DB::table('tbl_akun')->where('id_main_akun', 13)->pluck('id_akun')->toArray();
                $piutang_cust_id=[151];
                $piutang_cust=DB::table('tbl_akun_detail')
                            ->whereIn('id_akun', $piutang_cust_id)
                            ->orWhereIn('turunan1', $piutang_cust_id)
                            ->orWhereIn('turunan2', $piutang_cust_id)
                            ->orWhereIn('turunan3', $piutang_cust_id)
                            ->orWhereIn('turunan4', $piutang_cust_id)
                            ->pluck('id_akun');
                $min=$startTime - 86400;//kurangi sehari
                $saldo_awal=DB::table('tbl_trx_akuntansi_detail as trd')
                                    ->select(DB::raw("COALESCE(SUM(CASE WHEN tipe = 'DEBIT' THEN jumlah ELSE 0 END), 0) AS total_debit"), DB::raw("COALESCE(SUM(CASE WHEN tipe = 'KREDIT' THEN jumlah ELSE 0 END), 0) AS total_kredit"))
                                    ->join('tbl_trx_akuntansi as tra', 'trd.id_trx_akun', 'tra.id_trx_akun')
                                    ->whereIn('trd.id_akun', $piutang_cust_id)
                                    ->where('tra.location_id', $location_id)
                                    // ->where('tanggal', '>=', $first_date_month)
                                    ->whereIn('tra.customer_id', $customer_id)
                                    ->where('tanggal', '<=', date('Y-m-d', $min))
                                    ->whereNull('notes')
                                    ->first();
                $saldo_before_start_date=$saldo_awal->total_debit - $saldo_awal->total_kredit;
                $dtSaldo=DB::table('tbl_trx_akuntansi_detail as trd')
                            ->select('trd.*', 'trd.no as note_no', 'tra.*', 'ta.nama_akun', 'cb.no','cb.is_paid as paid','cb.invoice_no', 'ta.no_akun', 'o.order_no as order_no', 'io.no as install_order_no', 'pc.no as paid_cust_no', 'cb.bill_no as customer_bill_no', 'c.coorporate_name')
                            ->join('tbl_trx_akuntansi as tra', 'trd.id_trx_akun', 'tra.id_trx_akun')
                            ->join('tbl_akun as ta', 'trd.id_akun', 'ta.id_akun')
                            ->join('customers as c', 'c.id', 'tra.customer_id')
                            ->leftJoin('orders as o', 'tra.order_id', 'o.id')
                            ->leftJoin('install_orders as io', 'tra.install_order_id', 'io.id')
                            ->leftJoin('customer_bills as cb', 'tra.customer_bill_id', 'cb.id')
                            ->leftJoin('paid_customers as pc', 'tra.paid_customer_id', 'pc.id')
                            ->whereIn('trd.id_akun', $piutang_cust)
                            ->where('tra.location_id', $location_id)
                            ->where('tanggal', '>=', $date1)
                            ->where('tanggal', '<=', $date2)
                            ->whereIn('tra.customer_id', $customer_id)
                            ->whereNull('tra.notes')
                            ->orderBy('tanggal')
                            ->get();
                foreach ($dtSaldo as $v) {
                    $source=DB::table('tbl_trx_akuntansi_detail as trd')
                                ->select('no')
                                ->whereIn('id_akun', $account_payment)
                                ->where('id_trx_akun', $v->id_trx_akun)
                                ->first();
                    $order_id=null;
                    if($v->paid_customer_id != null ){
                        $get_install_order=DB::table('paid_customer_ds as pc')
                                        ->where('paid_customer_id', $v->paid_customer_id)
                                        ->join('customer_bills as cb', 'cb.id', 'pc.customer_bill_id')
                                        ->groupBy('cb.install_order_id')
                                        ->pluck('cb.install_order_id');
                        $order_from_install=DB::table('install_orders')->whereIn('id', $get_install_order)->groupBy('order_id')->pluck('order_id');
                        $get_order=DB::table('paid_customer_ds as pc')
                                        ->where('paid_customer_id', $v->paid_customer_id)
                                        ->orWhereIn('cb.order_id', $order_from_install)
                                        // ->whereNotNull('cb.order_id')
                                        ->join('customer_bills as cb', 'cb.id', 'pc.customer_bill_id')
                                        ->groupBy('cb.order_id')
                                        ->pluck('cb.order_id');
                        $order_id=DB::table('orders')->whereIn('id', $get_order)->get();
                    }
                    $v->order_id=$order_id;
                    $v->source=($source != null ? $source->no : null);
                }
                $data_temp_cust=$dtSaldo;
                
            }
        }
        
        // return $data_temp_cust;
        $data = array(
            'customer'  => $customer,
            'date1'     => $date1,
            'date2'     => $date2,
            'data_cust'      => $data_temp_cust,
            'customer_selected' => $customer_selected,
            'saldo_awal'    => $saldo_before_start_date,
            'all_customer'      => $all_customer
        );
        
        return view('pages.inv.inventory_transaction.piutang_all_recapt', $data);
    }
    
    public function piutangAll2(Request $request) {
        $customer = null;
        try
        {
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . '/crm/customerdatamain']);  
            $response = $client->request('GET', '', ['headers' => $headers]);  
            $body = $response->getBody();
            $content = $body->getContents();
            $response_array = json_decode($content,TRUE);
            $customer = $response_array['data'];
        } catch(RequestException $exception) {      
        }
        $data_temp_cust=array();
        $date1=date('Y-m-d');
        $date2=date('Y-m-d');
        $customer_selected=array();
        $all_customer=false;
        $saldo_before_start_date=0;
        if ($request->input('customer_id')) {
            $user_id = request()->session()->get('user.id');
            $user = DB::table('users')->where('id', $this->user_id)->first();
            $location_id = $user->site_id;

            if ($request->input('date')) {
                $date1=$request->input('date');
                $date2=$request->input('date2');
            }
            $startTime = strtotime( $date1 );
            $endTime = strtotime( $date2 );
            $date=date('Y-m', $startTime);

            $first_date_month=$date.'-01';

            //get piutang cust data
            $customer_id=$request->input('customer_id');
            $customer_selected=$request->input('customer_id');
            $all_customer=false;
            if ($customer_id != null) {
                foreach ($customer_id as $key => $value) {
                    if ($value == 'all') {
                        $all_customer=true;
                        $customer_id=DB::table('customers')->pluck('id');
                    }
                }
                $account_payment=DB::table('tbl_akun')->where('id_main_akun', 13)->pluck('id_akun')->toArray();
                $piutang_cust_id=[151];
                $piutang_cust=DB::table('tbl_akun_detail')
                            ->whereIn('id_akun', $piutang_cust_id)
                            ->orWhereIn('turunan1', $piutang_cust_id)
                            ->orWhereIn('turunan2', $piutang_cust_id)
                            ->orWhereIn('turunan3', $piutang_cust_id)
                            ->orWhereIn('turunan4', $piutang_cust_id)
                            ->pluck('id_akun');
                foreach ($customer_id as $key => $value) {
                    $min=$startTime - 86400;//kurangi sehari
                    $saldo_awal=DB::table('tbl_trx_akuntansi_detail as trd')
                                        ->select(DB::raw("COALESCE(SUM(CASE WHEN tipe = 'DEBIT' THEN jumlah ELSE 0 END), 0) AS total_debit"), DB::raw("COALESCE(SUM(CASE WHEN tipe = 'KREDIT' THEN jumlah ELSE 0 END), 0) AS total_kredit"))
                                        ->join('tbl_trx_akuntansi as tra', 'trd.id_trx_akun', 'tra.id_trx_akun')
                                        ->whereIn('trd.id_akun', $piutang_cust_id)
                                        ->where('tra.location_id', $location_id)
                                        // ->where('tanggal', '>=', $first_date_month)
                                        ->where('tra.customer_id', $value)
                                        ->where('tanggal', '<=', date('Y-m-d', $min))
                                        ->whereNull('notes')
                                        ->first();
                    $saldo_before_start_date=$saldo_awal->total_debit - $saldo_awal->total_kredit;
                    $dtSaldo=DB::table('tbl_trx_akuntansi_detail as trd')
                                ->select('trd.*', 'trd.no as note_no', 'tra.*', 'ta.nama_akun', 'cb.no','cb.is_paid as paid','cb.invoice_no', 'ta.no_akun', 'o.order_no as order_no', 'io.no as install_order_no', 'pc.no as paid_cust_no', 'cb.bill_no as customer_bill_no', 'c.coorporate_name')
                                ->join('tbl_trx_akuntansi as tra', 'trd.id_trx_akun', 'tra.id_trx_akun')
                                ->join('tbl_akun as ta', 'trd.id_akun', 'ta.id_akun')
                                ->join('customers as c', 'c.id', 'tra.customer_id')
                                ->leftJoin('orders as o', 'tra.order_id', 'o.id')
                                ->leftJoin('install_orders as io', 'tra.install_order_id', 'io.id')
                                ->leftJoin('customer_bills as cb', 'tra.customer_bill_id', 'cb.id')
                                ->leftJoin('paid_customers as pc', 'tra.paid_customer_id', 'pc.id')
                                ->whereIn('trd.id_akun', $piutang_cust)
                                ->where('tra.location_id', $location_id)
                                ->where('tanggal', '>=', $date1)
                                ->where('tanggal', '<=', $date2)
                                ->where('tra.customer_id', $value)
                                ->whereNull('tra.notes')
                                ->orderBy('tanggal')
                                ->get();
                    $detail = array();
                    foreach ($dtSaldo as $v) {
                        $source=DB::table('tbl_trx_akuntansi_detail as trd')
                                    ->select('no')
                                    ->whereIn('id_akun', $account_payment)
                                    ->where('id_trx_akun', $v->id_trx_akun)
                                    ->first();
                        $order_id=null;
                        if($v->paid_customer_id != null ){
                            $get_install_order=DB::table('paid_customer_ds as pc')
                                            ->where('paid_customer_id', $v->paid_customer_id)
                                            ->join('customer_bills as cb', 'cb.id', 'pc.customer_bill_id')
                                            ->groupBy('cb.install_order_id')
                                            ->pluck('cb.install_order_id');
                            $order_from_install=DB::table('install_orders')->whereIn('id', $get_install_order)->groupBy('order_id')->pluck('order_id');
                            $get_order=DB::table('paid_customer_ds as pc')
                                            ->where('paid_customer_id', $v->paid_customer_id)
                                            ->orWhereIn('cb.order_id', $order_from_install)
                                            // ->whereNotNull('cb.order_id')
                                            ->join('customer_bills as cb', 'cb.id', 'pc.customer_bill_id')
                                            ->groupBy('cb.order_id')
                                            ->pluck('cb.order_id');
                            $order_id=DB::table('orders')->whereIn('id', $get_order)->get();
                        }
                        $v->order_id=$order_id;
                        $v->source=($source != null ? $source->no : null);
                        $detail = $dtSaldo;
                    }
                    $data_temp_cust[$value]['customer']=DB::table('customers')->where('id', $value)->first();
                    $data_temp_cust[$value]['saldoAwal']=$saldo_awal;
                    $data_temp_cust[$value]['data']=$detail;
                }
                
            }
        }
        
        // return $data_temp_cust;
        $data = array(
            'customer'  => $customer,
            'date1'     => $date1,
            'date2'     => $date2,
            'data_cust'      => $data_temp_cust,
            'customer_selected' => $customer_selected,
            'all_customer'      => $all_customer
        );
        
        return view('pages.inv.inventory_transaction.piutang_all_recapt2', $data);
    }
    
    public function piutangSaldoAll(Request $request) {
        $customer = null;
        try
        {
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . '/crm/customerdatamain']);  
            $response = $client->request('GET', '', ['headers' => $headers]);  
            $body = $response->getBody();
            $content = $body->getContents();
            $response_array = json_decode($content,TRUE);
            $customer = $response_array['data'];
        } catch(RequestException $exception) {      
        }
        $data_temp_cust=array();
        $date1=date('Y-m-d');
        $date2=date('Y-m-d');
        $customer_selected=array();
        $saldo_awal=array();
        $all_customer=false;
        if ($request->input('customer_id')) {
            $user_id = request()->session()->get('user.id');
            $user = DB::table('users')->where('id', $this->user_id)->first();
            $location_id = $user->site_id;

            if ($request->input('date')) {
                $date1=$request->input('date');
                $date2=$request->input('date2');
            }
            // $akun=DB::table('tbl_akun')->where('id_akun', $id)->first();
            $startTime = strtotime( $date1 );
            $endTime = strtotime( $date2 );
            $date=date('Y-m', $startTime);

            $first_date_month=$date.'-01';
            $saldo_before_start_date=0;

            //get piutang cust data
            $customer_id=$request->input('customer_id');
            $customer_selected=$request->input('customer_id');
            $all_customer=false;
            if ($customer_id != null) {
                foreach ($customer_id as $key => $value) {
                    if ($value == 'all') {
                        $all_customer=true;
                        $customer_id=DB::table('customers')->pluck('id');
                    }
                }
                $piutang_cust_id=[151];
                $piutang_cust=DB::table('tbl_akun_detail')
                            ->whereIn('id_akun', $piutang_cust_id)
                            ->orWhereIn('turunan1', $piutang_cust_id)
                            ->orWhereIn('turunan2', $piutang_cust_id)
                            ->orWhereIn('turunan3', $piutang_cust_id)
                            ->orWhereIn('turunan4', $piutang_cust_id)
                            ->pluck('id_akun');
                $min=$startTime - 86400;//kurangi sehari
                $saldo_awal=DB::table('tbl_trx_akuntansi_detail as trd')
                                    ->select(DB::raw("COALESCE(SUM(CASE WHEN tipe = 'DEBIT' THEN jumlah ELSE 0 END), 0) AS total_debit"), DB::raw("COALESCE(SUM(CASE WHEN tipe = 'KREDIT' THEN jumlah ELSE 0 END), 0) AS total_kredit"))
                                    ->join('tbl_trx_akuntansi as tra', 'trd.id_trx_akun', 'tra.id_trx_akun')
                                    ->whereIn('trd.id_akun', $piutang_cust)
                                    ->where('tra.location_id', $location_id)
                                    // ->where('tanggal', '>=', $first_date_month)
                                    ->whereIn('tra.customer_id', $customer_id)
                                    ->where('tanggal', '<=', date('Y-m-d', $min))
                                    ->whereNull('notes')
                                    ->first();
                // $saldo_before_start_date = $perubahan_saldo->total_kredit - $perubahan_saldo->total_debit;
                foreach ($customer_id as $key => $value) {
                    $bulan=explode('-', $date);
                    $detail=array();
                    $account_payment=DB::table('tbl_akun')->where('id_main_akun', 13)->pluck('id_akun');
                    for ( $i = $startTime; $i <= $endTime; $i = $i + 86400 ) {
                        $thisDate = date( 'Y-m-d', $i ); // 2010-05-01, 2010-05-02, etc
                        $dtSaldo=DB::table('tbl_trx_akuntansi_detail as trd')
                                            ->select('trd.*', 'trd.no as note_no', 'tra.*', 'ta.nama_akun', 'ta.no_akun', 'o.order_no as order_no', 'io.no as install_order_no', 'pc.no as paid_cust_no')
                                            ->join('tbl_trx_akuntansi as tra', 'trd.id_trx_akun', 'tra.id_trx_akun')
                                            ->join('tbl_akun as ta', 'trd.id_akun', 'ta.id_akun')
                                            ->leftJoin('orders as o', 'tra.order_id', 'o.id')
                                            ->leftJoin('install_orders as io', 'tra.install_order_id', 'io.id')
                                            ->leftJoin('paid_customers as pc', 'tra.paid_customer_id', 'pc.id')
                                            ->whereIn('trd.id_akun', $piutang_cust)
                                            ->where('tra.location_id', $location_id)
                                            ->where('tanggal', $thisDate)
                                            ->where('tra.customer_id', $value)
                                            ->whereNull('tra.notes')
                                            ->get();
                        if (count($dtSaldo) > 0) {
                            foreach ($dtSaldo as $v) {
                                $v->id_source=null;
                                $v->no_source=null;
                                $query=DB::table('tbl_trx_akuntansi_detail')
                                            ->where('id_trx_akun', $v->id_trx_akun)
                                            ->whereIn('id_akun', $account_payment)
                                            ->first();
                                if ($query != null) {
                                    $v->id_source=$query->id_trx_akun_detail;
                                    $v->no_source=$query->no;
                                }
                            }
                            $detail[$i]['date']=$thisDate;
                            $detail[$i]['dt']=$dtSaldo;
                        }
                    }
                    $data_temp_cust[$value]['customer']=DB::table('customers')->where('id', $value)->first();
                    $data_temp_cust[$value]['data']=$detail;
                    // $data_temp_cust[$value]['perubahan_saldo']=$perubahan_saldo;
                }
            }
        }
        $data = array(
            'customer'  => $customer,
            'date1'     => $date1,
            'date2'     => $date2,
            'data_cust'      => $data_temp_cust,
            'saldo_awal'   => $saldo_awal,
            'customer_selected' => $customer_selected,
            'all_customer'      => $all_customer
        );
        
        return view('pages.inv.inventory_transaction.piutang_saldo_all', $data);
    }
    public function sellCustomer(Request $request) {
        $customer = null;
        try
        {
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . '/crm/customerdatamain']);  
            $response = $client->request('GET', '', ['headers' => $headers]);  
            $body = $response->getBody();
            $content = $body->getContents();
            $response_array = json_decode($content,TRUE);
            $customer = $response_array['data'];
        } catch(RequestException $exception) {      
        }
        $data_temp_cust=array();
        $date1=date('Y-m-d');
        $date2=date('Y-m-d');
        $customer_selected=array();
        $all_customer=false;
        if ($request->input('customer_id')) {
            $user_id = request()->session()->get('user.id');
            $user = DB::table('users')->where('id', $this->user_id)->first();
            $location_id = $user->site_id;

            if ($request->input('date')) {
                $date1=$request->input('date');
                $date2=$request->input('date2');
            }
            // $akun=DB::table('tbl_akun')->where('id_akun', $id)->first();
            $startTime = strtotime( $date1 );
            $endTime = strtotime( $date2 );
            $date=date('Y-m', $startTime);

            $first_date_month=$date.'-01';
            $saldo_before_start_date=0;

            //get piutang cust data
            $customer_id=$request->input('customer_id');
            $customer_selected=$request->input('customer_id');
            $all_customer=false;
            if ($customer_id != null) {
                foreach ($customer_id as $key => $value) {
                    if ($value == 'all') {
                        $all_customer=true;
                        $customer_id=DB::table('customers')->pluck('id');
                    }
                }
                $piutang_cust_id=[151];
                $piutang_cust=DB::table('tbl_akun_detail')
                            ->whereIn('id_akun', $piutang_cust_id)
                            ->orWhereIn('turunan1', $piutang_cust_id)
                            ->orWhereIn('turunan2', $piutang_cust_id)
                            ->orWhereIn('turunan3', $piutang_cust_id)
                            ->orWhereIn('turunan4', $piutang_cust_id)
                            ->pluck('id_akun');
                $dtSaldo=DB::table('tbl_trx_akuntansi_detail as trd')
                            ->select('trd.*', 'trd.no as note_no', 'tra.*', 'ta.nama_akun', 'ta.no_akun', 'o.order_no as order_no',  'io.no as install_order_no', 'io.spk_no as spk_number_ins', 'pc.no as paid_cust_no', 'c.coorporate_name',  'cb.bill_no as bill_no', 'cb.with_pph', 'cb.amount as bill_amount', 'o.spk_number', 'cb.invoice_no')
                            ->join('tbl_trx_akuntansi as tra', 'trd.id_trx_akun', 'tra.id_trx_akun')
                            ->join('tbl_akun as ta', 'trd.id_akun', 'ta.id_akun')
                            ->join('customers as c', 'c.id', 'tra.customer_id')
                            //->join('inv_sales as invs', 'invs.id',  'tra.inv_sale_id')
                            ->leftJoin('orders as o', 'tra.order_id', 'o.id')
                            ->leftJoin('install_orders as io', 'tra.install_order_id', 'io.id')
                            ->leftJoin('paid_customers as pc', 'tra.paid_customer_id', 'pc.id')
                            ->leftJoin('customer_bills as cb', 'tra.customer_bill_id', 'cb.id')
                           ->leftjoin('inv_sales as invs', 'invs.id',  'tra.inv_sale_id')
                           // ->leftJoin('inv_sales as invs', 'tra.inv_sale_id', 'invs.id') 'invs.invoice as invoice_invs', 'invs.bill_no as bill_no_invs'
                            ->whereIn('trd.id_akun', $piutang_cust)
                            ->where('tra.location_id', $location_id)
                            ->where('tipe', 'DEBIT')
                            ->where('tanggal', '>=', $date1)
                            ->where('tanggal', '<=', $date2)
                            ->whereIn('tra.customer_id', $customer_id)
                            ->whereNull('tra.notes')
                            ->orderBy('tanggal')
                            ->get();
                foreach ($dtSaldo as $key => $value) {
                    if ($value->install_order_id != null) {
                        $install_order=DB::table('install_orders')
                                            ->where('install_orders.id', $value->install_order_id)
                                            ->leftJoin('orders', 'install_orders.order_id', 'orders.id')
                                            ->select('orders.spk_number')
                                            ->first();
                       // $value->spk_number_ins=$install_order->spk_number_ins;
                    }
                    
                    $value->dpp=DB::table('tbl_trx_akuntansi_detail')->where('id_trx_akun', $value->id_trx_akun)->whereNotIn('id_akun', [151, 67, 135])->select('jumlah')->first();
                    $value->ppn=DB::table('tbl_trx_akuntansi_detail')->where('id_trx_akun', $value->id_trx_akun)->where('id_akun', 67)->select('jumlah')->first();
                    $value->pph=DB::table('tbl_trx_akuntansi_detail')->where('id_trx_akun', $value->id_trx_akun)->where('id_akun', 135)->select('jumlah')->first();
                }
                $data_temp_cust=$dtSaldo;
                
            }
        }
        // return $data_temp_cust;
        $data = array(
            'customer'  => $customer,
            'date1'     => $date1,
            'date2'     => $date2,
            'data_cust'      => $data_temp_cust,
            'customer_selected' => $customer_selected,
            'all_customer'      => $all_customer
        );
        // dd($data);
        return view('pages.inv.inventory_transaction.sell_customer', $data);
    }
    public function recaptMaterialRequest(){
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

        $data=array(
            'order_list'     => $order_list
        );
        return view('pages.inv.inventory_transaction.recapt_material_request', $data);
    }
    public function getRecaptMaterialRequest($id){
        $rab=DB::table('rabs')->select('rabs.*', 'kavlings.*', 'rabs.id as rab_id')->join('kavlings', 'rabs.kavling_id', 'kavlings.id')->where('rabs.id', $id)->get();
        $order=DB::table('orders')->where('orders.id', $rab[0]->order_id)->join('customers', 'orders.customer_id', 'customers.id')->first();
        foreach ($rab as $key => $value) {
            $value->detail=$this->showAllMaterialGroupByMaterial($value->rab_id);
        }
        $data=array(
            'data'        => $rab,
            'html_content'  => view('pages.inv.inventory_transaction.view_recapt_material_request')->with(compact('rab', 'order'))->render()
        );
        return $data;
    }
    public function showAllMaterialGroupByMaterial($id){
        $response = null;
        try
        {
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . 'rab/show_all_mterial_group_by_material/'.$id]);  
            $response = $client->request('GET', '', ['headers' => $headers]);  
            $body = $response->getBody();
            $content = $body->getContents();
            $response_array = json_decode($content,TRUE);

            $response = $response_array['data'];         
        } catch(RequestException $exception) {
            
        }    

        return $response;
    }
    public function debtSupplier(Request $request) {

        $suppliers = null;
        try
        {
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . '/crm/base/MSupplier']);  
            $response = $client->request('GET', '', ['headers' => $headers]);  
            $body = $response->getBody();
            $content = $body->getContents();
            $response_array = json_decode($content,TRUE);
            $suppliers = $response_array['data'];
        } catch(RequestException $exception) {      
        }
        $data_temp=array();
        $date1=date('Y-m-d');
        $date2=date('Y-m-d');
        $supplier_selected=array();
        $all_supplier=false;
        $saldo_before_start_date=0;
        if ($request->input('suppl_single')) {
            $supplier=$request->input('suppl_single');
            $supplier_selected=$request->input('suppl_single');
            $all_supplier=false;
            foreach ($supplier as $key => $value) {
                if ($value == 'all') {
                    $all_supplier=true;
                    $supplier=DB::table('m_suppliers')->pluck('id');
                }
            }
            $id=[147];//Hutang usaha
            $query=DB::table('tbl_akun_detail')
                        ->whereIn('id_akun', $id)
                        // ->orWhereIn('turunan1', $id)
                        // ->orWhereIn('turunan2', $id)
                        // ->orWhereIn('turunan3', $id)
                        // ->orWhereIn('turunan4', $id)
                        ->pluck('id_akun');
                        
            $user_id = request()->session()->get('user.id');
            $user = DB::table('users')->where('id', $this->user_id)->first();
            $location_id = $user->site_id;

            if ($request->input('date')) {
                $date1=$request->input('date');
                $date2=$request->input('date2');
            }
            $startTime = strtotime( $date1 );
            $endTime = strtotime( $date2 );
            $date=date('Y-m', $startTime);

            $first_date_month=$date.'-01';
            
            $min=$startTime - 86400;//kurangi sehari
            $saldo_awal=DB::table('tbl_trx_akuntansi_detail as trd')
                                ->select(DB::raw("COALESCE(SUM(CASE WHEN tipe = 'DEBIT' THEN jumlah ELSE 0 END), 0) AS total_debit"), DB::raw("COALESCE(SUM(CASE WHEN tipe = 'KREDIT' THEN jumlah ELSE 0 END), 0) AS total_kredit"))
                                ->join('tbl_trx_akuntansi as tra', 'trd.id_trx_akun', 'tra.id_trx_akun')
                                ->whereIn('trd.id_akun', $query)
                                ->where('tra.location_id', $location_id)
                                // ->where('tanggal', '>=', $first_date_month)
                                ->whereIn('tra.m_supplier_id', $supplier)
                                ->where('tanggal', '<=', date('Y-m-d', $min))
                                ->whereNull('notes')
                                ->first();
            $saldo_before_start_date=$saldo_awal != null ? ($saldo_awal->total_kredit - $saldo_awal->total_debit) : 0;

            $dtSaldo=DB::table('tbl_trx_akuntansi_detail as trd')
                        ->select('trd.*', 'trd.no as note_no', 'tra.*', 'ta.nama_akun', 'ta.no_akun', 'm.name', 'p.no as purchase_no', 'pa.no as purchase_asset_no', 'it.no_surat_jalan')
                        ->join('tbl_trx_akuntansi as tra', 'trd.id_trx_akun', 'tra.id_trx_akun')
                        ->join('tbl_akun as ta', 'trd.id_akun', 'ta.id_akun')
                        ->join('m_suppliers as m', 'm.id', 'tra.m_supplier_id')
                        ->leftJoin('inv_trxes as it', 'it.id', 'tra.inv_trx_id')
                        ->leftJoin('purchases as p', 'tra.purchase_id', 'p.id')
                        ->leftJoin('purchase_assets as pa', 'tra.purchase_asset_id', 'pa.id')
                        ->whereIn('trd.id_akun', $query)
                        ->where('tra.location_id', $location_id)
                        // ->where('tipe', 'KREDIT')
                        ->where('tanggal', '>=', $date1)
                        ->where('tanggal', '<=', $date2)
                        ->whereIn('tra.m_supplier_id', $supplier)
                        ->whereNull('tra.notes')
                        ->orderBy('tanggal')
                        ->get();
            $data_temp=$dtSaldo;
        }
        $data = array(
            'suppliers' => $suppliers,
            'date1'     => $date1,
            'date2'     => $date2,
            'data'      => $data_temp,
            'saldo_awal'    => $saldo_before_start_date,
            'supplier_selected' => $supplier_selected,
            'all_supplier'  => $all_supplier
        );
        
        return view('pages.inv.inventory_transaction.debt_supplier', $data);
    }
    public function getKavlingInRab($id){
        $order=DB::table('rabs')
                    ->select('rabs.*', 'kavlings.name')
                    ->where('order_id', $id)
                    ->join('kavlings', 'kavlings.id', 'rabs.kavling_id')
                    ->get();
        return $order;
    }
    public function biddReport(){
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

        $data=array(
            'order_list'     => $order_list
        );
        return view('pages.inv.inventory_transaction.bidd_report', $data);
    }
    public function getBiddReport($id){
        $rab=DB::table('rabs')
                    ->select('rabs.no', 'rabs.order_id', 'kavlings.name as kavling_name', 'products.*', 'order_ds.total', 'm_units.name as unit_name')
                    ->where('rabs.id', $id)
                    ->join('kavlings', 'kavlings.id', 'rabs.kavling_id')
                    ->join('order_ds', 'order_ds.order_id', 'rabs.order_id')
                    ->join('products', function($join){
                        $join->on('kavlings.id', '=', 'products.kavling_id');
                        $join->on('order_ds.product_id', '=', 'products.id');
                    })
                    ->join('m_units', 'm_units.id', 'products.m_unit_id')
                    ->get();
        $amount_contract=0;
        $order=DB::table('orders')->where('orders.id', $rab[0]->order_id)->join('customers', 'orders.customer_id', 'customers.id')->first();
        foreach ($rab as $key => $value) {
            $jasa=DB::table('project_works as pw')
                        ->join('project_worksubs as pws', 'pw.id', 'pws.project_work_id')
                        ->where('pw.product_id', $value->id)
                        ->where('pw.rab_id', $id)
                        ->get();
            $material=DB::table('project_works as pw')
                        ->select('m_item_id', DB::raw('MAX(pwsd.base_price) as base_price'), DB::raw('SUM(pwsd.amount) as amount'), DB::raw('MAX(mi.category) as category'))
                        ->join('project_worksubs as pws', 'pw.id', 'pws.project_work_id')
                        ->join('project_worksub_ds as pwsd', 'pws.id', 'pwsd.project_worksub_id')
                        ->join('m_items as mi', 'pwsd.m_item_id', 'mi.id')
                        ->where('pw.product_id', $value->id)
                        ->where('pw.rab_id', $id)
                        ->groupBy('pwsd.m_item_id')
                        ->get();
            $total_jasa=$total_material=$total_kaca=$total_spare_part=0;
            foreach ($jasa as $v) {
                $total_jasa+=($v->amount * $v->base_price);
            }
            foreach ($material as $v) {
                $total_material+=($v->category == 'MATERIAL' ? ($v->amount * $v->base_price) : 0);
                $total_kaca+=($v->category == 'KACA' ? ($v->amount * $v->base_price) : 0);
                $total_spare_part+=($v->category == 'SPARE PART' ? ($v->amount * $v->base_price) : 0);
            }
            $value->jasa=$total_jasa;
            $value->material=$total_material;
            $value->kaca=$total_kaca;
            $value->spare_part=$total_spare_part;
            $value->jumlah=($total_jasa + $total_material + $total_kaca + $total_spare_part);
            $amount_contract+=($value->total * $value->price);
        }
        $data=array(
            'data'        => $order,
            'html_content'  => view('pages.inv.inventory_transaction.view_bidd_recapt')->with(compact('rab', 'amount_contract', 'order'))->render()
        );
        return $data;
    }
    public function calcPrice(Request $request){
        $this_month=date('Y-m');
        if ($request->input('bulan')) {
            $this_month=$request->input('tahun').'-'.$request->input('bulan');
        }
        if($request->submit){
            $date_before=date('Y-m', strtotime("- 1 months",  strtotime($this_month)));
            $id=$this->site_id;
            $items=DB::table('m_items')->whereNull('deleted_at')->get();
            foreach ($items as $key => $value) {
                $inv_detail=DB::table('inv_trxes as it')
                                ->join('inv_trx_ds as itd', 'itd.inv_trx_id', 'it.id')
                                ->where('it.site_id', $this->site_id)
                                ->where('it.trx_type', 'RECEIPT')
                                ->where('itd.m_item_id', $value->id)
                                ->where('itd.condition', 1)
                                ->where('itd.type_material', 'STK_NORMAL')
                                ->where('it.inv_trx_date', 'like', '%'.$this_month.'%')
                                ->select('itd.*')
                                ->get();
                $total_all=$total_material=0;
                foreach ($inv_detail as $k => $v) {
                    $total_all+=($v->amount * $v->base_price);
                    $total_material+=$v->amount;
                }
                $total_pengeluaran=$total_material_pengeluaran=0;
                $inv_detail=DB::table('inv_trxes as it')
                                ->join('inv_trx_ds as itd', 'itd.inv_trx_id', 'it.id')
                                ->where('it.site_id', $this->site_id)
                                ->whereIn('it.trx_type', ['REQ_ITEM', 'REQ_ITEM_SP', 'INV_SALE', 'STK_ADJ'])
                                ->where('itd.m_item_id', $value->id)
                                ->where('itd.condition', 1)
                                ->where('itd.type_material', 'STK_NORMAL')
                                ->where('it.inv_trx_date', 'like', '%'.$this_month.'%')
                                ->select('itd.*')
                                ->get();
                foreach ($inv_detail as $k => $v) {
                    $total_pengeluaran+=($v->amount * $v->base_price);
                    $total_material_pengeluaran+=$v->amount;
                }
                $query=DB::table('calculate_prices')
                            ->where('site_id', $this->site_id)
                            ->where('m_item_id', $value->id)
                            ->where('last_month', $date_before)
                            ->first();
                $cek_this_month=DB::table('calculate_prices')
                            ->where('site_id', $this->site_id)
                            ->where('m_item_id', $value->id)
                            ->where('last_month', $this_month)
                            ->first();
                $stok_change=($total_material - $total_material_pengeluaran);
                $total_stok_change=($total_all - $total_pengeluaran);
                $total_temp=($stok_change + ($query != null ? $query->amount : 0));
                $price=($query != null ? ((($query->amount * $query->price) + $total_stok_change) / ($total_temp != 0 ? $total_temp : 1)) : ($total_stok_change != 0 ? ($total_stok_change / $stok_change) : 0));
                if ($cek_this_month == null) {
                    $input_data=[
                        'site_id' => $this->site_id,
                        'm_item_id' => $value->id,
                        'amount' => $total_temp,
                        'amount_in' => $query != null ? ($query->amount_in + $total_material) : $total_material,
                        'amount_out' => $query != null ? ($query->amount_out + $total_material_pengeluaran) : $total_material_pengeluaran,
                        'm_unit_id' => $value->m_unit_id,
                        'last_month'    => $this_month,
                        'price'     => $price,
                        'created_at'    => date('Y-m-d H:i:s'),
                        'updated_at'    => date('Y-m-d H:i:s'),
                    ];

                    DB::table('calculate_prices')->insert($input_data);
                }else{
                    $update_data=[
                        'amount' => $total_temp,
                        'amount_in' => $query != null ? ($query->amount_in + $total_material) : $total_material,
                        'amount_out' => $query != null ? ($query->amount_out + $total_material_pengeluaran) : $total_material_pengeluaran,
                        'price'     => $price,
                        'updated_at'    => date('Y-m-d H:i:s'),
                    ];
                    DB::table('calculate_prices')->where('id', $cek_this_month->id)->update($update_data);
                }
            }
            return redirect('inventory/calc_price');
        }
        $bulan=explode('-', $this_month);
        return  view('pages.inv.inventory_transaction.calc_price')
                ->with(compact('bulan'));
    }
    public function siteStockHistory(Request $request) {
        return view('pages.inv.inventory_transaction.history_stock');
    }
    public function siteStockHistoryJson(Request $request) {
        $location_id=$this->site_id;
        $date1=date('Y-m-d');
        $date2=date('Y-m-d');
        if ($request->input('date')) {
            $date1=$request->input('date');
            $date2=$request->input('date2');
        }
        
        $startTime = strtotime( $date1 );
        $endTime = strtotime( $date2 );
        $date=date('Y-m', $startTime);
        $date_before=date('Y-m', strtotime("- 1 months",  strtotime($date)));
        $first_date_month=$date.'-01';
        $min=$startTime - 86400;//kurangi sehari
        $items=DB::table('m_items')->select('m_items.id', 'm_items.name', 'm_items.no', 'm_units.name as unit_name')->join('m_units', 'm_units.id', 'm_items.m_unit_id')->whereNull('m_items.deleted_at')->whereNotIn('category', ['ATK', 'ALAT KERJA'])->get();
        foreach ($items as $key => $value) {
            $price_month=DB::table('calculate_prices')->select('amount', 'amount_in', 'amount_out', 'price')->where('last_month', $date_before)->where('m_item_id', $value->id)->where('site_id', $this->site_id)->first();
            $total_change_in=$change_in=$change_out=$total_change_out=0;
            if($first_date_month < date('Y-m-d', $min)){
                $inv_detail=DB::table('inv_trxes as it')
                            ->join('inv_trx_ds as itd', 'itd.inv_trx_id', 'it.id')
                            ->where('it.site_id', $this->site_id)
                            // ->where('it.trx_type', 'RECEIPT')
                            ->whereIn('it.trx_type', ['REQ_ITEM', 'REQ_ITEM_SP', 'INV_SALE', 'STK_ADJ', 'RECEIPT'])
                            ->where('itd.m_item_id', $value->id)
                            ->where('itd.condition', 1)
                            ->where('itd.type_material', 'STK_NORMAL')
                            ->where('it.inv_trx_date', '>=', $first_date_month)
                            ->where('it.inv_trx_date', '<=', date('Y-m-d', $min))
                            ->select('itd.amount', 'itd.base_price', 'it.trx_type')
                            ->get();
                foreach ($inv_detail as $k => $v) {
                    $total_change_in+=($v->trx_type == 'RECEIPT' ? ($v->amount * $v->base_price) : 0);
                    $total_change_out+=($v->trx_type != 'RECEIPT' ? ($v->amount * $v->base_price) : 0);
                    $change_in+=($v->trx_type == 'RECEIPT' ? $v->amount : 0);
                    $change_out+=($v->trx_type != 'RECEIPT' ? $v->amount : 0);
                }
            }

            $value_stok_change=$total_change_in - $total_change_out;
            $total_stock_change=$change_in - $change_out;
            $total_stock_first=$price_month != null ? $price_month->amount + $total_stock_change : $total_stock_change;
            $price_first=$price_month != null ? (($price_month->amount * $price_month->price) + $value_stok_change) / ($total_stock_first != 0 ? $total_stock_first : 1) : ($value_stok_change / ($total_stock_first != 0 ? $total_stock_first : 1));

            $value->stok_awal=$total_stock_first;
            $value->price_first=$price_first;
            $value->value_first=round(($price_first * $total_stock_first), 0);
            $total_penerimaan=$total_material_penerimaan=$total_pengeluaran=$total_material_pengeluaran=0;
            $inv_detail=DB::table('inv_trxes as it')
                            ->join('inv_trx_ds as itd', 'itd.inv_trx_id', 'it.id')
                            ->where('it.site_id', $this->site_id)
                            // ->where('it.trx_type', 'RECEIPT')
                            ->whereIn('it.trx_type', ['REQ_ITEM', 'REQ_ITEM_SP', 'INV_SALE', 'STK_ADJ', 'RECEIPT'])
                            ->where('itd.m_item_id', $value->id)
                            ->where('itd.condition', 1)
                            ->where('itd.type_material', 'STK_NORMAL')
                            ->where('it.inv_trx_date', '>=', $date1)
                            ->where('it.inv_trx_date', '<=', $date2)
                            ->select('itd.amount', 'itd.base_price', 'it.trx_type')
                            ->get();
            foreach ($inv_detail as $k => $v) {
                $total_penerimaan+=($v->trx_type == 'RECEIPT' ? ($v->amount * $v->base_price) : 0);
                $total_material_penerimaan+=($v->trx_type == 'RECEIPT' ? $v->amount : 0);
                $total_pengeluaran+=($v->trx_type != 'RECEIPT' ? ($v->amount * $v->base_price) : 0);
                $total_material_pengeluaran+=($v->trx_type != 'RECEIPT' ? $v->amount : 0);
            }
            $value->total_penerimaan=($total_penerimaan / ($total_material_penerimaan != 0 ? $total_material_penerimaan : 1));
            $value->total_material_penerimaan=$total_material_penerimaan;
            $value->total_pengeluaran=($total_pengeluaran / ($total_material_pengeluaran != 0 ? $total_material_pengeluaran : 1));
            $value->total_material_pengeluaran=$total_material_pengeluaran;

            $value_stock_run=$total_penerimaan - $total_pengeluaran;
            $stok_run=$total_material_penerimaan - $total_material_pengeluaran;
            $stock_all=$total_stock_first + $stok_run;
            $value_stok_all=(($total_stock_first * $price_first) + $value_stock_run);
            $price_last=$value_stok_all / ($stock_all != 0 ? $stock_all : 1);
            $value->stok_last=$stock_all;
            $value->price_last=$price_last;
            $value->value_item=$stock_all * $price_last;
        }
        
        $data=DataTables::of($items)
                    ->make(true); 
        return $data;
    }
    public function exportSellCustomer(Request $request) {
        $data_temp_cust=array();
        $date1=date('Y-m-d');
        $date2=date('Y-m-d');
        $customer_selected=array();
        $all_customer=false;
        if ($request->input('customer_id')) {
            $user_id = request()->session()->get('user.id');
            $user = DB::table('users')->where('id', $this->user_id)->first();
            $location_id = $user->site_id;

            if ($request->input('date')) {
                $date1=$request->input('date');
                $date2=$request->input('date2');
            }
            // $akun=DB::table('tbl_akun')->where('id_akun', $id)->first();
            $startTime = strtotime( $date1 );
            $endTime = strtotime( $date2 );
            $date=date('Y-m', $startTime);

            $first_date_month=$date.'-01';
            $saldo_before_start_date=0;

            //get piutang cust data
            $customer_id=$request->input('customer_id');
            if ($customer_id != null) {
                foreach ($customer_id as $key => $value) {
                    if ($value == 'all') {
                        $customer_id=DB::table('customers')->pluck('id');
                    }
                }
                $piutang_cust_id=[151];
                $piutang_cust=DB::table('tbl_akun_detail')
                            ->whereIn('id_akun', $piutang_cust_id)
                            ->orWhereIn('turunan1', $piutang_cust_id)
                            ->orWhereIn('turunan2', $piutang_cust_id)
                            ->orWhereIn('turunan3', $piutang_cust_id)
                            ->orWhereIn('turunan4', $piutang_cust_id)
                            ->pluck('id_akun');
                $dtSaldo=DB::table('tbl_trx_akuntansi_detail as trd')
                            ->select('trd.*', 'trd.no as note_no', 'tra.*', 'ta.nama_akun', 'ta.no_akun', 'o.order_no as order_no', 'io.no as install_order_no' , 'io.spk_no as spk_number_ins',  'pc.no as paid_cust_no', 'c.coorporate_name', 'cb.bill_no as bill_no', 'cb.with_pph', 'cb.amount as bill_amount', 'o.spk_number','cb.invoice_no')
                            ->join('tbl_trx_akuntansi as tra', 'trd.id_trx_akun', 'tra.id_trx_akun')
                            ->join('tbl_akun as ta', 'trd.id_akun', 'ta.id_akun')
                            ->join('customers as c', 'c.id', 'tra.customer_id')
                            ->leftJoin('orders as o', 'tra.order_id', 'o.id')
                            ->leftJoin('install_orders as io', 'tra.install_order_id', 'io.id')
                            ->leftJoin('paid_customers as pc', 'tra.paid_customer_id', 'pc.id')
                            ->leftJoin('customer_bills as cb', 'tra.customer_bill_id', 'cb.id')
                            ->whereIn('trd.id_akun', $piutang_cust)
                            ->where('tra.location_id', $location_id)
                            ->where('tipe', 'DEBIT')
                            ->where('tanggal', '>=', $date1)
                            ->where('tanggal', '<=', $date2)
                            ->whereIn('tra.customer_id', $customer_id)
                            ->whereNull('tra.notes')
                            ->orderBy('tanggal')
                            ->get();
                foreach ($dtSaldo as $key => $value) {
                    if ($value->install_order_id != null) {
                        $install_order=DB::table('install_orders')
                                            ->where('install_orders.id', $value->install_order_id)
                                            ->leftJoin('orders', 'install_orders.order_id', 'orders.id')
                                            ->select('orders.spk_number')
                                            ->first();
                        //$value->spk_number_ins=$install_order->spk_number;
                    }
                    
                    $value->dpp=DB::table('tbl_trx_akuntansi_detail')->where('id_trx_akun', $value->id_trx_akun)->whereNotIn('id_akun', [151, 67, 135])->select('jumlah')->first();
                    $value->ppn=DB::table('tbl_trx_akuntansi_detail')->where('id_trx_akun', $value->id_trx_akun)->where('id_akun', 67)->select('jumlah')->first();
                    $value->pph=DB::table('tbl_trx_akuntansi_detail')->where('id_trx_akun', $value->id_trx_akun)->where('id_akun', 135)->select('jumlah')->first();
                }
                $data_temp_cust=$dtSaldo;
                
            }
        }
        // return $data_temp_cust;
        $data = array(
            'date1'     => $date1,
            'date2'     => $date2,
            'data_cust'      => $data_temp_cust,
        );
        return Excel::download(new SellCustomerExport($data), 'laporan_penjualan_customer.xlsx');
    }
    public function exportPiutangAll(Request $request) {
        
        $data_temp_cust=array();
        $date1=date('Y-m-d');
        $date2=date('Y-m-d');
        $saldo_before_start_date=0;
        if ($request->input('customer_id')) {
            $user_id = request()->session()->get('user.id');
            $user = DB::table('users')->where('id', $this->user_id)->first();
            $location_id = $user->site_id;

            if ($request->input('date')) {
                $date1=$request->input('date');
                $date2=$request->input('date2');
            }
            $startTime = strtotime( $date1 );
            $endTime = strtotime( $date2 );
            $date=date('Y-m', $startTime);

            $first_date_month=$date.'-01';

            //get piutang cust data
            $customer_id=$request->input('customer_id');
            if ($customer_id != null) {
                foreach ($customer_id as $key => $value) {
                    if ($value == 'all') {
                        $customer_id=DB::table('customers')->pluck('id');
                    }
                }
                $account_payment=DB::table('tbl_akun')->where('id_main_akun', 13)->pluck('id_akun')->toArray();
                $piutang_cust_id=[151];
                $piutang_cust=DB::table('tbl_akun_detail')
                            ->whereIn('id_akun', $piutang_cust_id)
                            ->orWhereIn('turunan1', $piutang_cust_id)
                            ->orWhereIn('turunan2', $piutang_cust_id)
                            ->orWhereIn('turunan3', $piutang_cust_id)
                            ->orWhereIn('turunan4', $piutang_cust_id)
                            ->pluck('id_akun');
                $min=$startTime - 86400;//kurangi sehari
                $saldo_awal=DB::table('tbl_trx_akuntansi_detail as trd')
                                    ->select(DB::raw("COALESCE(SUM(CASE WHEN tipe = 'DEBIT' THEN jumlah ELSE 0 END), 0) AS total_debit"), DB::raw("COALESCE(SUM(CASE WHEN tipe = 'KREDIT' THEN jumlah ELSE 0 END), 0) AS total_kredit"))
                                    ->join('tbl_trx_akuntansi as tra', 'trd.id_trx_akun', 'tra.id_trx_akun')
                                    ->whereIn('trd.id_akun', $piutang_cust_id)
                                    ->where('tra.location_id', $location_id)
                                    // ->where('tanggal', '>=', $first_date_month)
                                    ->whereIn('tra.customer_id', $customer_id)
                                    ->where('tanggal', '<=', date('Y-m-d', $min))
                                    ->whereNull('notes')
                                    ->first();
                $saldo_before_start_date=$saldo_awal->total_debit - $saldo_awal->total_kredit;
                $dtSaldo=DB::table('tbl_trx_akuntansi_detail as trd')
                            ->select('trd.*', 'trd.no as note_no', 'tra.*', 'ta.nama_akun', 'ta.no_akun',  'cb.no', 'o.order_no as order_no', 'io.no as install_order_no', 'pc.no as paid_cust_no', 'cb.bill_no as customer_bill_no', 'c.coorporate_name')
                            ->join('tbl_trx_akuntansi as tra', 'trd.id_trx_akun', 'tra.id_trx_akun')
                            ->join('tbl_akun as ta', 'trd.id_akun', 'ta.id_akun')
                            ->join('customers as c', 'c.id', 'tra.customer_id')
                            ->leftJoin('orders as o', 'tra.order_id', 'o.id')
                            ->leftJoin('install_orders as io', 'tra.install_order_id', 'io.id')
                            ->leftJoin('customer_bills as cb', 'tra.customer_bill_id', 'cb.id')
                            ->leftJoin('paid_customers as pc', 'tra.paid_customer_id', 'pc.id')
                            ->whereIn('trd.id_akun', $piutang_cust)
                            ->where('tra.location_id', $location_id)
                            ->where('tanggal', '>=', $date1)
                            ->where('tanggal', '<=', $date2)
                            ->whereIn('tra.customer_id', $customer_id)
                            ->whereNull('tra.notes')
                            ->orderBy('tanggal')
                            ->get();
                foreach ($dtSaldo as $v) {
                    $source=DB::table('tbl_trx_akuntansi_detail as trd')
                                ->select('no')
                                ->whereIn('id_akun', $account_payment)
                                ->where('id_trx_akun', $v->id_trx_akun)
                                ->first();
                    $v->source=($source != null ? $source->no : null);
                }
                $data_temp_cust=$dtSaldo;
                
            }
        }
        
        // return $data_temp_cust;
        $data = array(
            'date1'     => $date1,
            'date2'     => $date2,
            'data_cust'      => $data_temp_cust,
            'saldo_awal'    => $saldo_before_start_date,
        );
        
        // return Excel::download(new PiutangAllExport($data), 'laporan_piutang_pembayaran_customer.xlsx');
        return view('exports.export_piutang_all', [
            'data' => $data,
        ]);
    }
    
    public function exportPiutangAll2(Request $request) {
        
        $customer = null;
        try
        {
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . '/crm/customerdatamain']);  
            $response = $client->request('GET', '', ['headers' => $headers]);  
            $body = $response->getBody();
            $content = $body->getContents();
            $response_array = json_decode($content,TRUE);
            $customer = $response_array['data'];
        } catch(RequestException $exception) {      
        }
        $data_temp_cust=array();
        $date1=date('Y-m-d');
        $date2=date('Y-m-d');
        $customer_selected=array();
        $all_customer=false;
        $saldo_before_start_date=0;
        if ($request->input('customer_id')) {
            $user_id = request()->session()->get('user.id');
            $user = DB::table('users')->where('id', $this->user_id)->first();
            $location_id = $user->site_id;

            if ($request->input('date')) {
                $date1=$request->input('date');
                $date2=$request->input('date2');
            }
            $startTime = strtotime( $date1 );
            $endTime = strtotime( $date2 );
            $date=date('Y-m', $startTime);

            $first_date_month=$date.'-01';

            //get piutang cust data
            $customer_id=$request->input('customer_id');
            $customer_selected=$request->input('customer_id');
            $all_customer=false;
            if ($customer_id != null) {
                foreach ($customer_id as $key => $value) {
                    if ($value == 'all') {
                        $all_customer=true;
                        $customer_id=DB::table('customers')->pluck('id');
                    }
                }
                $account_payment=DB::table('tbl_akun')->where('id_main_akun', 13)->pluck('id_akun')->toArray();
                $piutang_cust_id=[151];
                $piutang_cust=DB::table('tbl_akun_detail')
                            ->whereIn('id_akun', $piutang_cust_id)
                            ->orWhereIn('turunan1', $piutang_cust_id)
                            ->orWhereIn('turunan2', $piutang_cust_id)
                            ->orWhereIn('turunan3', $piutang_cust_id)
                            ->orWhereIn('turunan4', $piutang_cust_id)
                            ->pluck('id_akun');
                foreach ($customer_id as $key => $value) {
                    $min=$startTime - 86400;//kurangi sehari
                    $saldo_awal=DB::table('tbl_trx_akuntansi_detail as trd')
                                        ->select(DB::raw("COALESCE(SUM(CASE WHEN tipe = 'DEBIT' THEN jumlah ELSE 0 END), 0) AS total_debit"), DB::raw("COALESCE(SUM(CASE WHEN tipe = 'KREDIT' THEN jumlah ELSE 0 END), 0) AS total_kredit"))
                                        ->join('tbl_trx_akuntansi as tra', 'trd.id_trx_akun', 'tra.id_trx_akun')
                                        ->whereIn('trd.id_akun', $piutang_cust_id)
                                        ->where('tra.location_id', $location_id)
                                        // ->where('tanggal', '>=', $first_date_month)
                                        ->where('tra.customer_id', $value)
                                        ->where('tanggal', '<=', date('Y-m-d', $min))
                                        ->whereNull('notes')
                                        ->first();
                    $saldo_before_start_date=$saldo_awal->total_debit - $saldo_awal->total_kredit;
                    $dtSaldo=DB::table('tbl_trx_akuntansi_detail as trd')
                                ->select('trd.*', 'trd.no as note_no', 'tra.*', 'ta.nama_akun', 'cb.no','cb.is_paid as paid','cb.invoice_no', 'ta.no_akun', 'o.order_no as order_no', 'io.no as install_order_no', 'pc.no as paid_cust_no', 'cb.bill_no as customer_bill_no', 'c.coorporate_name')
                                ->join('tbl_trx_akuntansi as tra', 'trd.id_trx_akun', 'tra.id_trx_akun')
                                ->join('tbl_akun as ta', 'trd.id_akun', 'ta.id_akun')
                                ->join('customers as c', 'c.id', 'tra.customer_id')
                                ->leftJoin('orders as o', 'tra.order_id', 'o.id')
                                ->leftJoin('install_orders as io', 'tra.install_order_id', 'io.id')
                                ->leftJoin('customer_bills as cb', 'tra.customer_bill_id', 'cb.id')
                                ->leftJoin('paid_customers as pc', 'tra.paid_customer_id', 'pc.id')
                                ->whereIn('trd.id_akun', $piutang_cust)
                                ->where('tra.location_id', $location_id)
                                ->where('tanggal', '>=', $date1)
                                ->where('tanggal', '<=', $date2)
                                ->where('tra.customer_id', $value)
                                ->whereNull('tra.notes')
                                ->orderBy('tanggal')
                                ->get();
                    $detail = array();
                    foreach ($dtSaldo as $v) {
                        $source=DB::table('tbl_trx_akuntansi_detail as trd')
                                    ->select('no')
                                    ->whereIn('id_akun', $account_payment)
                                    ->where('id_trx_akun', $v->id_trx_akun)
                                    ->first();
                        $order_id=null;
                        if($v->paid_customer_id != null ){
                            $get_install_order=DB::table('paid_customer_ds as pc')
                                            ->where('paid_customer_id', $v->paid_customer_id)
                                            ->join('customer_bills as cb', 'cb.id', 'pc.customer_bill_id')
                                            ->groupBy('cb.install_order_id')
                                            ->pluck('cb.install_order_id');
                            $order_from_install=DB::table('install_orders')->whereIn('id', $get_install_order)->groupBy('order_id')->pluck('order_id');
                            $get_order=DB::table('paid_customer_ds as pc')
                                            ->where('paid_customer_id', $v->paid_customer_id)
                                            ->orWhereIn('cb.order_id', $order_from_install)
                                            // ->whereNotNull('cb.order_id')
                                            ->join('customer_bills as cb', 'cb.id', 'pc.customer_bill_id')
                                            ->groupBy('cb.order_id')
                                            ->pluck('cb.order_id');
                            $order_id=DB::table('orders')->whereIn('id', $get_order)->get();
                        }
                        $v->order_id=$order_id;
                        $v->source=($source != null ? $source->no : null);
                        $detail = $dtSaldo;
                    }
                    $data_temp_cust[$value]['customer']=DB::table('customers')->where('id', $value)->first();
                    $data_temp_cust[$value]['saldoAwal']=$saldo_awal;
                    $data_temp_cust[$value]['data']=$detail;
                }
                
            }
        }
        
        // return $data_temp_cust;
        $data = array(
            'customer'  => $customer,
            'date1'     => $date1,
            'date2'     => $date2,
            'data_cust'      => $data_temp_cust,
            'customer_selected' => $customer_selected,
            'all_customer'      => $all_customer
        );
        
        // return Excel::download(new PiutangAllExport($data), 'laporan_piutang_pembayaran_customer.xlsx');
        return view('exports.export_piutang_all2', $data);
    }
    public function exportDebtSupplier(Request $request) {

        $data_temp=array();
        $date1=date('Y-m-d');
        $date2=date('Y-m-d');
        $saldo_before_start_date=0;
        if ($request->input('suppl_single')) {
            $supplier=$request->input('suppl_single');
            foreach ($supplier as $key => $value) {
                if ($value == 'all') {
                    $supplier=DB::table('m_suppliers')->pluck('id');
                }
            }
            $id=[147];//Hutang usaha
            $query=DB::table('tbl_akun_detail')
                        ->whereIn('id_akun', $id)
                        // ->orWhereIn('turunan1', $id)
                        // ->orWhereIn('turunan2', $id)
                        // ->orWhereIn('turunan3', $id)
                        // ->orWhereIn('turunan4', $id)
                        ->pluck('id_akun');
                        
            $user_id = request()->session()->get('user.id');
            $user = DB::table('users')->where('id', $this->user_id)->first();
            $location_id = $user->site_id;

            if ($request->input('date')) {
                $date1=$request->input('date');
                $date2=$request->input('date2');
            }
            $startTime = strtotime( $date1 );
            $endTime = strtotime( $date2 );
            $date=date('Y-m', $startTime);

            $first_date_month=$date.'-01';
            
            $min=$startTime - 86400;//kurangi sehari
            $saldo_awal=DB::table('tbl_trx_akuntansi_detail as trd')
                                ->select(DB::raw("COALESCE(SUM(CASE WHEN tipe = 'DEBIT' THEN jumlah ELSE 0 END), 0) AS total_debit"), DB::raw("COALESCE(SUM(CASE WHEN tipe = 'KREDIT' THEN jumlah ELSE 0 END), 0) AS total_kredit"))
                                ->join('tbl_trx_akuntansi as tra', 'trd.id_trx_akun', 'tra.id_trx_akun')
                                ->whereIn('trd.id_akun', $query)
                                ->where('tra.location_id', $location_id)
                                // ->where('tanggal', '>=', $first_date_month)
                                ->whereIn('tra.m_supplier_id', $supplier)
                                ->where('tanggal', '<=', date('Y-m-d', $min))
                                ->whereNull('notes')
                                ->first();
            $saldo_before_start_date=$saldo_awal != null ? ($saldo_awal->total_kredit - $saldo_awal->total_debit) : 0;

            $dtSaldo=DB::table('tbl_trx_akuntansi_detail as trd')
                        ->select('trd.*', 'trd.no as note_no', 'tra.*', 'ta.nama_akun', 'ta.no_akun', 'm.name', 'p.no as purchase_no', 'pa.no as purchase_asset_no', 'it.no_surat_jalan')
                        ->join('tbl_trx_akuntansi as tra', 'trd.id_trx_akun', 'tra.id_trx_akun')
                        ->join('tbl_akun as ta', 'trd.id_akun', 'ta.id_akun')
                        ->join('m_suppliers as m', 'm.id', 'tra.m_supplier_id')
                        ->leftJoin('inv_trxes as it', 'it.id', 'tra.inv_trx_id')
                        ->leftJoin('purchases as p', 'tra.purchase_id', 'p.id')
                        ->leftJoin('purchase_assets as pa', 'tra.purchase_asset_id', 'pa.id')
                        ->whereIn('trd.id_akun', $query)
                        ->where('tra.location_id', $location_id)
                        // ->where('tipe', 'KREDIT')
                        ->where('tanggal', '>=', $date1)
                        ->where('tanggal', '<=', $date2)
                        ->whereIn('tra.m_supplier_id', $supplier)
                        ->whereNull('tra.notes')
                        ->orderBy('tanggal')
                        ->get();
            $data_temp=$dtSaldo;
        }
        $data = array(
            'date1'     => $date1,
            'date2'     => $date2,
            'data'      => $data_temp,
            'saldo_awal'    => $saldo_before_start_date,
        );
        return Excel::download(new DebtSupplierExport($data), 'laporan_hutang_supplier.xlsx');
    }
    public function recaptDebt(Request $request) {

        $suppliers = null;
        try
        {
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . '/crm/base/MSupplier']);  
            $response = $client->request('GET', '', ['headers' => $headers]);  
            $body = $response->getBody();
            $content = $body->getContents();
            $response_array = json_decode($content,TRUE);
            $suppliers = $response_array['data'];
        } catch(RequestException $exception) {      
        }
        $data_temp=array();
        $date1=date('Y-m-d');
        $date2=date('Y-m-d');
        $supplier_selected=array();
        $all_supplier=false;
        $saldo_before_start_date=0;
        if ($request->input('suppl_single')) {
            $supplier=$request->input('suppl_single');
            $supplier_selected=$request->input('suppl_single');
            $all_supplier=false;
            foreach ($supplier as $key => $value) {
                if ($value == 'all') {
                    $all_supplier=true;
                    $supplier=DB::table('m_suppliers')->pluck('id');
                }
            }
            $id=[55];//Hutang usaha
            $query=DB::table('tbl_akun_detail')
                        ->whereIn('id_akun', $id)
                        // ->orWhereIn('turunan1', $id)
                        // ->orWhereIn('turunan2', $id)
                        // ->orWhereIn('turunan3', $id)
                        // ->orWhereIn('turunan4', $id)
                        ->pluck('id_akun');
                        
            $user_id = request()->session()->get('user.id');
            $user = DB::table('users')->where('id', $this->user_id)->first();
            $location_id = $user->site_id;

            if ($request->input('date')) {
                $date1=$request->input('date');
                $date2=$request->input('date2');
            }
            $startTime = strtotime( $date1 );
            $endTime = strtotime( $date2 );
            $date=date('Y-m', $startTime);

            $first_date_month=$date.'-01';
            
            $min=$startTime - 86400;//kurangi sehari
            $saldo_awal=DB::table('tbl_trx_akuntansi_detail as trd')
                                ->select(DB::raw("COALESCE(SUM(CASE WHEN tipe = 'DEBIT' THEN jumlah ELSE 0 END), 0) AS total_debit"), DB::raw("COALESCE(SUM(CASE WHEN tipe = 'KREDIT' THEN jumlah ELSE 0 END), 0) AS total_kredit"))
                                ->join('tbl_trx_akuntansi as tra', 'trd.id_trx_akun', 'tra.id_trx_akun')
                                ->whereIn('trd.id_akun', $query)
                                ->where('tra.location_id', $location_id)
                                // ->where('tanggal', '>=', $first_date_month)
                                ->whereIn('tra.m_supplier_id', $supplier)
                                ->where('tanggal', '<=', date('Y-m-d', $min))
                                ->whereNull('notes')
                                ->first();
            $saldo_before_start_date=$saldo_awal != null ? ($saldo_awal->total_kredit - $saldo_awal->total_debit) : 0;

            $dtSaldo=DB::table('tbl_trx_akuntansi_detail as trd')
                        ->select('trd.*', 'trd.no as note_no', 'tra.*', 'ta.nama_akun', 'ta.no_akun', 'm.name', 'p.no as purchase_no', 'pa.no as purchase_asset_no', 'it.no_surat_jalan')
                        ->join('tbl_trx_akuntansi as tra', 'trd.id_trx_akun', 'tra.id_trx_akun')
                        ->join('tbl_akun as ta', 'trd.id_akun', 'ta.id_akun')
                        ->join('m_suppliers as m', 'm.id', 'tra.m_supplier_id')
                        ->leftJoin('inv_trxes as it', 'it.id', 'tra.inv_trx_id')
                        ->leftJoin('purchases as p', 'tra.purchase_id', 'p.id')
                        ->leftJoin('purchase_assets as pa', 'tra.purchase_asset_id', 'pa.id')
                        ->whereIn('trd.id_akun', $query)
                        ->where('tra.location_id', $location_id)
                        // ->where('tipe', 'KREDIT')
                        ->where('tanggal', '>=', $date1)
                        ->where('tanggal', '<=', $date2)
                        ->whereIn('tra.m_supplier_id', $supplier)
                        ->whereNull('tra.notes')
                        ->orderBy('tanggal')
                        ->get();
            $data_temp=$dtSaldo;
        }
        $data = array(
            'suppliers' => $suppliers,
            'date1'     => $date1,
            'date2'     => $date2,
            'data'      => $data_temp,
            'saldo_awal'    => $saldo_before_start_date,
            'supplier_selected' => $supplier_selected,
            'all_supplier'  => $all_supplier
        );
        
        return view('pages.inv.inventory_transaction.recapt_debt', $data);
    }
    public function exportRecaptDebt(Request $request) {

        $suppliers = null;
        try
        {
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . '/crm/base/MSupplier']);  
            $response = $client->request('GET', '', ['headers' => $headers]);  
            $body = $response->getBody();
            $content = $body->getContents();
            $response_array = json_decode($content,TRUE);
            $suppliers = $response_array['data'];
        } catch(RequestException $exception) {      
        }
        $data_temp=array();
        $date1=date('Y-m-d');
        $date2=date('Y-m-d');
        $supplier_selected=array();
        $all_supplier=false;
        $saldo_before_start_date=0;
        if ($request->input('suppl_single')) {
            $supplier=$request->input('suppl_single');
            $supplier_selected=$request->input('suppl_single');
            $all_supplier=false;
            foreach ($supplier as $key => $value) {
                if ($value == 'all') {
                    $all_supplier=true;
                    $supplier=DB::table('m_suppliers')->pluck('id');
                }
            }
            $id=[55];//Hutang usaha
            $query=DB::table('tbl_akun_detail')
                        ->whereIn('id_akun', $id)
                        // ->orWhereIn('turunan1', $id)
                        // ->orWhereIn('turunan2', $id)
                        // ->orWhereIn('turunan3', $id)
                        // ->orWhereIn('turunan4', $id)
                        ->pluck('id_akun');
                        
            $user_id = request()->session()->get('user.id');
            $user = DB::table('users')->where('id', $this->user_id)->first();
            $location_id = $user->site_id;

            if ($request->input('date')) {
                $date1=$request->input('date');
                $date2=$request->input('date2');
            }
            $startTime = strtotime( $date1 );
            $endTime = strtotime( $date2 );
            $date=date('Y-m', $startTime);

            $first_date_month=$date.'-01';
            
            $min=$startTime - 86400;//kurangi sehari
            $saldo_awal=DB::table('tbl_trx_akuntansi_detail as trd')
                                ->select(DB::raw("COALESCE(SUM(CASE WHEN tipe = 'DEBIT' THEN jumlah ELSE 0 END), 0) AS total_debit"), DB::raw("COALESCE(SUM(CASE WHEN tipe = 'KREDIT' THEN jumlah ELSE 0 END), 0) AS total_kredit"))
                                ->join('tbl_trx_akuntansi as tra', 'trd.id_trx_akun', 'tra.id_trx_akun')
                                ->whereIn('trd.id_akun', $query)
                                ->where('tra.location_id', $location_id)
                                // ->where('tanggal', '>=', $first_date_month)
                                ->whereIn('tra.m_supplier_id', $supplier)
                                ->where('tanggal', '<=', date('Y-m-d', $min))
                                ->whereNull('notes')
                                ->first();
            $saldo_before_start_date=$saldo_awal != null ? ($saldo_awal->total_kredit - $saldo_awal->total_debit) : 0;

            $dtSaldo=DB::table('tbl_trx_akuntansi_detail as trd')
                        ->select('trd.*', 'trd.no as note_no', 'tra.*', 'ta.nama_akun', 'ta.no_akun', 'm.name', 'p.no as purchase_no', 'pa.no as purchase_asset_no', 'it.no_surat_jalan')
                        ->join('tbl_trx_akuntansi as tra', 'trd.id_trx_akun', 'tra.id_trx_akun')
                        ->join('tbl_akun as ta', 'trd.id_akun', 'ta.id_akun')
                        ->join('m_suppliers as m', 'm.id', 'tra.m_supplier_id')
                        ->leftJoin('inv_trxes as it', 'it.id', 'tra.inv_trx_id')
                        ->leftJoin('purchases as p', 'tra.purchase_id', 'p.id')
                        ->leftJoin('purchase_assets as pa', 'tra.purchase_asset_id', 'pa.id')
                        ->whereIn('trd.id_akun', $query)
                        ->where('tra.location_id', $location_id)
                        // ->where('tipe', 'KREDIT')
                        ->where('tanggal', '>=', $date1)
                        ->where('tanggal', '<=', $date2)
                        ->whereIn('tra.m_supplier_id', $supplier)
                        ->whereNull('tra.notes')
                        ->orderBy('tanggal')
                        ->get();
            $data_temp=$dtSaldo;
        }
        $data = array(
            'date1'     => $date1,
            'date2'     => $date2,
            'data'      => $data_temp,
            'saldo_awal'    => $saldo_before_start_date,
        );
        return Excel::download(new RecaptDebtExport($data), 'laporan hutang usaha.xlsx');
    }
    public function recaptStockIn(Request $request) {
        $data_temp=array();
        $date1=date('Y-m-d');
        $date2=date('Y-m-d');
        $no='';
        $user_id = request()->session()->get('user.id');
        $user = DB::table('users')->where('id', $this->user_id)->first();
        $location_id = $user->site_id;

        if ($request->input('date')) {
            $date1=$request->input('date');
            $date2=$request->input('date2');
            $no=$request->input('no');
        }
        $startTime = strtotime( $date1 );
        $endTime = strtotime( $date2 );
        $date=date('Y-m', $startTime);

        $first_date_month=$date.'-01';
        
        $min=$startTime - 86400;//kurangi sehari

        $dtSaldo=DB::table('inv_trxes as it')
                    ->select('itd.*', 'it.no as inv_no', 'it.inv_trx_date', 'mi.name', 'p.no as purchase_no', 'pa.no as purchase_asset_no', 'it.no_surat_jalan', 'mi.no as code_item')
                    ->join('inv_trx_ds as itd', 'it.id', 'itd.inv_trx_id')
                    ->join('m_items as mi', 'mi.id', 'itd.m_item_id')
                    ->leftJoin('purchases as p', 'it.purchase_id', 'p.id')
                    ->leftJoin('purchase_assets as pa', 'it.purchase_asset_id', 'pa.id')
                    ->where('it.site_id', $location_id)
                    ->where('trx_type', 'RECEIPT')
                    ->where('itd.condition', 1)
                    ->where('it.inv_trx_date', '>=', $date1)
                    ->where('it.inv_trx_date', '<=', $date2)
                    ->orderBy('it.inv_trx_date');
                    // ->get();
        if($no != ''){
            $getPO=DB::table('purchases as p')
            ->join('inv_trxes as it', 'it.purchase_id', 'p.id')
            ->where('it.inv_trx_date', '>=', $date1)
            ->where('it.inv_trx_date', '<=', $date2)
            ->where('p.no', 'ilike', '%'.$no.'%')
            ->pluck('it.id');
            $getPOAsset=DB::table('purchase_assets as p')
            ->join('inv_trxes as it', 'it.purchase_asset_id', 'p.id')
            ->where('it.inv_trx_date', '>=', $date1)
            ->where('it.inv_trx_date', '<=', $date2)
            ->where('p.no', 'ilike', '%'.$no.'%')
            ->pluck('it.id');
            $getSuratJalan=DB::table('inv_trxes as it')
            ->where('it.inv_trx_date', '>=', $date1)
            ->where('it.inv_trx_date', '<=', $date2)
            ->where('it.no_surat_jalan', 'ilike', '%'.$no.'%')
            ->pluck('it.id');
            $temp_id=array();
            foreach($getPO as $row){
                array_push($temp_id, $row);
            }
            foreach($getPOAsset as $row){
                array_push($temp_id, $row);
            }
            foreach($getSuratJalan as $row){
                array_push($temp_id, $row);
            }
            if($temp_id != null){
                $dtSaldo->whereIn('it.id', $temp_id);
            }
        }
        $dtSaldo=$dtSaldo->get();
        $data_temp=$dtSaldo;
        $data = array(
            'no'        => $no,
            'date1'     => $date1,
            'date2'     => $date2,
            'data'      => $data_temp,
        );
        return view('pages.inv.inventory_transaction.recapt_stock_in', $data);
    }
    public function exportRecaptStockIn(Request $request) {
        $data_temp=array();
        $date1=date('Y-m-d');
        $date2=date('Y-m-d');
        $no='';
        $user_id = request()->session()->get('user.id');
        $user = DB::table('users')->where('id', $this->user_id)->first();
        $location_id = $user->site_id;

        if ($request->input('date')) {
            $date1=$request->input('date');
            $date2=$request->input('date2');
            $no=$request->input('no');
        }
        $startTime = strtotime( $date1 );
        $endTime = strtotime( $date2 );
        $date=date('Y-m', $startTime);

        $first_date_month=$date.'-01';
        
        $min=$startTime - 86400;//kurangi sehari

        $dtSaldo=DB::table('inv_trxes as it')
                    ->select('itd.*', 'it.no as inv_no', 'it.inv_trx_date', 'mi.name', 'p.no as purchase_no', 'pa.no as purchase_asset_no', 'it.no_surat_jalan', 'mi.no as code_item')
                    ->join('inv_trx_ds as itd', 'it.id', 'itd.inv_trx_id')
                    ->join('m_items as mi', 'mi.id', 'itd.m_item_id')
                    ->leftJoin('purchases as p', 'it.purchase_id', 'p.id')
                    ->leftJoin('purchase_assets as pa', 'it.purchase_asset_id', 'pa.id')
                    ->where('it.site_id', $location_id)
                    ->where('trx_type', 'RECEIPT')
                    ->where('itd.condition', 1)
                    ->where('it.inv_trx_date', '>=', $date1)
                    ->where('it.inv_trx_date', '<=', $date2)
                    ->orderBy('it.inv_trx_date');
                    // ->get();
        if($no != ''){
            $getPO=DB::table('purchases as p')
            ->join('inv_trxes as it', 'it.purchase_id', 'p.id')
            ->where('it.inv_trx_date', '>=', $date1)
            ->where('it.inv_trx_date', '<=', $date2)
            ->where('p.no', 'ilike', '%'.$no.'%')
            ->pluck('it.id');
            $getPOAsset=DB::table('purchase_assets as p')
            ->join('inv_trxes as it', 'it.purchase_asset_id', 'p.id')
            ->where('it.inv_trx_date', '>=', $date1)
            ->where('it.inv_trx_date', '<=', $date2)
            ->where('p.no', 'ilike', '%'.$no.'%')
            ->pluck('it.id');
            $getSuratJalan=DB::table('inv_trxes as it')
            ->where('it.inv_trx_date', '>=', $date1)
            ->where('it.inv_trx_date', '<=', $date2)
            ->where('it.no_surat_jalan', 'ilike', '%'.$no.'%')
            ->pluck('it.id');
            $temp_id=array();
            foreach($getPO as $row){
                array_push($temp_id, $row);
            }
            foreach($getPOAsset as $row){
                array_push($temp_id, $row);
            }
            foreach($getSuratJalan as $row){
                array_push($temp_id, $row);
            }
            if($temp_id != null){
                $dtSaldo->whereIn('it.id', $temp_id);
            }
        }
        $dtSaldo=$dtSaldo->get();
        $data_temp=$dtSaldo;
        $data = array(
            'no'        => $no,
            'date1'     => $date1,
            'date2'     => $date2,
            'data'      => $data_temp,
        );
        return Excel::download(new StockInExport($data), 'laporan_stok_masuk.xlsx');
    }
    public function suggestSuratJalanJson(Request $request)
    {
        $data=array();
        if($request->has('q')){
            $key=$request->q;
            $reg=DB::table('payment_suppliers')->where('no_surat_jalan', 'like', '%'.$key.'%')->pluck('no_surat_jalan');
            $query=DB::table('inv_trxes')
                        ->select('inv_trxes.no_surat_jalan as text')
                        ->whereNotIn('inv_trxes.no_surat_jalan', $reg)
                        ->where('inv_trxes.no_surat_jalan', 'like', '%'.$key.'%')
                        ->groupBy('inv_trxes.no_surat_jalan');
            $data=$query->limit(15)->get();
        }
        return $data;
    }
    public function suggestSuratJalanJasaJson(Request $request)
    {
        $data=array();
        if($request->has('q')){
            $key=$request->q;
            $reg=DB::table('payment_suppliers')->where('no_surat_jalan_jasa', 'like', '%'.$key.'%')->pluck('no_surat_jalan_jasa');
            $query=DB::table('inv_trx_services')
                        ->select('inv_trx_services.no_surat_jalan as text')
                        ->whereNotIn('inv_trx_services.no_surat_jalan', $reg)
                        ->where('inv_trx_services.no_surat_jalan', 'like', '%'.$key.'%')
                        ->groupBy('inv_trx_services.no_surat_jalan');
            $data=$query->limit(15)->get();
        }
        return $data;
    }
    public function detailSuratJalanJson(Request $request)
    {
        $no=$request->no;
        $query=DB::table('inv_trxes')
                        ->where('inv_trxes.no_surat_jalan', $no)
                        ->get();
        $total=$shipping=0;
        $m_supplier_id=0;
        $items=array();
        foreach($query as $row){
            $dt=DB::table('inv_trx_ds')->select('inv_trx_ds.*', 'mi.name', 'mi.no as code')->join('m_items as mi', 'inv_trx_ds.m_item_id', 'mi.id')->whereIn('condition', [0, 1, 2])->where('inv_trx_id', $row->id)->get();
            $amount=0;
            $purchase=null;
            if($row->purchase_id != null){
                $purchase=DB::table('purchases')->where('id', $row->purchase_id)->first();
            }else{
                $purchase=DB::table('purchase_assets')->where('id', $row->purchase_asset_id)->first();
            }
            foreach($dt as $val){
                $amount+=($val->amount * $val->base_price);
                $val->ppn=$purchase->is_without_ppn;
                $items[]=$val;
            }
            $amount=$purchase->is_without_ppn == true ? $amount : ($amount + ($amount*0.1));
            $m_supplier_id=$purchase->m_supplier_id;
            $shipping+=$purchase->delivery_fee;
            $total+=$amount;
        }
        $data=array(
            'inv'   => $query,
            'items' => $items,
            'total' => $total,
            'supplier' => DB::table('m_suppliers')->where('id', $m_supplier_id)->first(),
            'm_supplier_id' => $m_supplier_id,
            'delivery_fee'  => $shipping
        );
        return $data;
    }
    public function detailSuratJalanJasaJson(Request $request)
    {
        $no=$request->no;
        $query=DB::table('inv_trx_services')
                        ->where('inv_trx_services.no_surat_jalan', $no)
                        ->get();
        $total=$shipping=0;
        $m_supplier_id=0;
        $items=array();
        foreach($query as $row){
            $dt=DB::table('inv_trx_service_ds')->whereIn('condition', [1, 2])->where('inv_trx_service_id', $row->id)->get();
            $purchase=DB::table('purchase_services')->where('id', $row->purchase_service_id)->first();
            $amount=0;
            foreach($dt as $val){
                $amount+=($val->amount * $val->base_price);
                $val->ppn=$purchase->is_without_ppn;
                $items[]=$val;
            }
            $amount=$purchase->is_without_ppn == true ? $amount : ($amount + ($amount*0.1));
            $m_supplier_id=$purchase->m_supplier_id;
            $shipping+=$purchase->delivery_fee;
            $total+=$amount;
        }
        $data=array(
            'inv'   => $query,
            'total' => $total,
            'items' => $items,
            'supplier' => DB::table('m_suppliers')->where('id', $m_supplier_id)->first(),
            'm_supplier_id' => $m_supplier_id,
            'delivery_fee'  => $shipping
        );
        return $data;
    }
    public function stockCard(Request $request) {

        $items=DB::table('m_items')->select('m_items.id', 'm_items.name', 'm_items.no', 'm_units.name as unit_name')->join('m_units', 'm_units.id', 'm_items.m_unit_id')->whereNull('m_items.deleted_at')->whereNotIn('category', ['ATK', 'ALAT KERJA'])->orderBy('m_items.name')->get();
        
        $data_temp=array();
        $date1=date('Y-m-d');
        $date2=date('Y-m-d');
        $item_selected=null;
        $all_supplier=false;
        $saldo_before_start_date=0;
        if ($request->input('item')) {   
            $item_selected  = $request->item;
            $user_id = request()->session()->get('user.id');
            $user = DB::table('users')->where('id', $this->user_id)->first();
            $location_id = $user->site_id;
            $m_item_id=$request->item;
            if ($request->input('date')) {
                $date1=$request->input('date');
                $date2=$request->input('date2');
            }
            $startTime = strtotime( $date1 );
            $endTime = strtotime( $date2 );
            $date=date('Y-m', $startTime);
            $date_before=date('Y-m', strtotime("- 1 months",  strtotime($date)));
            $first_date_month=$date.'-01';
            
            $min=$startTime - 86400;//kurangi sehari
            $price_month=DB::table('calculate_prices')->select('amount', 'amount_in', 'amount_out', 'price')->where('last_month', $date_before)->where('m_item_id', $m_item_id)->where('site_id', $this->site_id)->first();
            $total_change_in=$change_in=$change_out=$total_change_out=0;
            if($first_date_month < date('Y-m-d', $min)){
                $stok_change=DB::table('inv_trxes as it')
                            ->join('inv_trx_ds as itd', 'itd.inv_trx_id', 'it.id')
                            ->where('it.site_id', $this->site_id)
                            ->whereIn('it.trx_type', ['REQ_ITEM', 'REQ_ITEM_SP', 'INV_SALE', 'STK_ADJ', 'RECEIPT'])
                            ->where('itd.m_item_id', $m_item_id)
                            ->where('itd.condition', 1)
                            ->where('itd.type_material', 'STK_NORMAL')
                            ->where('it.inv_trx_date', '>=', $first_date_month)
                            ->where('it.inv_trx_date', '<=', date('Y-m-d', $min))
                            ->select('itd.amount', 'itd.base_price', 'it.trx_type')
                            ->get();
                foreach ($stok_change as $k => $v) {
                    $total_change_in+=($v->trx_type == 'RECEIPT' ? ($v->amount * $v->base_price) : 0);
                    $total_change_out+=($v->trx_type != 'RECEIPT' ? ($v->amount * $v->base_price) : 0);
                    $change_in+=($v->trx_type == 'RECEIPT' ? $v->amount : 0);
                    $change_out+=($v->trx_type != 'RECEIPT' ? $v->amount : 0);
                }
            }
            $value_stok_change=$total_change_in - $total_change_out;
            $total_stock_change=$change_in - $change_out;
            $total_stock_first=$price_month != null ? $price_month->amount + $total_stock_change : $total_stock_change;
            $price_first=$price_month != null ? (($price_month->amount * $price_month->price) + $value_stok_change) / ($total_stock_first != 0 ? $total_stock_first : 1) : ($value_stok_change / ($total_stock_first != 0 ? $total_stock_first : 1));
            $inv_detail=DB::table('inv_trxes as it')
                            ->join('inv_trx_ds as itd', 'itd.inv_trx_id', 'it.id')
                            ->where('it.site_id', $this->site_id)
                            ->whereIn('it.trx_type', ['REQ_ITEM', 'REQ_ITEM_SP', 'INV_SALE', 'STK_ADJ', 'RECEIPT'])
                            ->where('itd.m_item_id', $m_item_id)
                            ->where('itd.condition', 1)
                            ->where('itd.type_material', 'STK_NORMAL')
                            ->where('it.inv_trx_date', '>=', $date1)
                            ->where('it.inv_trx_date', '<=', $date2)
                            ->select('itd.amount', 'itd.base_price', 'it.trx_type', 'it.inv_trx_date')
                            ->get();
            $data_temp=array(
                'item'          => DB::table('m_items')->where('id', $m_item_id)->first(),
                'first_date'    => $first_date_month,
                'stock_first'   => $total_stock_first,
                'price_first'   => $price_first,
                'stock_range'   => $inv_detail
            );
        }
        $data = array(
            'items' => $items,
            'date1'     => $date1,
            'date2'     => $date2,
            'data'      => $data_temp,
            'item_selected' => $item_selected,
        );
        
        return view('pages.inv.inventory_transaction.stock_card', $data);
    }
    public function exportStock(){
        $data=$this->getStok();
        return Excel::download(new StockExport($data), 'stok.xlsx');
    }
    public function exportRecaptStockInTax(Request $request) {
        $data_temp=array();
        $date1=date('Y-m-d');
        $date2=date('Y-m-d');
        $no='';
        $user_id = request()->session()->get('user.id');
        $user = DB::table('users')->where('id', $this->user_id)->first();
        $location_id = $user->site_id;

        if ($request->input('date')) {
            $date1=$request->input('date');
            $date2=$request->input('date2');
            $no=$request->input('no');
        }
        $startTime = strtotime( $date1 );
        $endTime = strtotime( $date2 );
        $date=date('Y-m', $startTime);

        $first_date_month=$date.'-01';
        
        $min=$startTime - 86400;//kurangi sehari

        $dtSaldo=DB::table('inv_trxes as it')
                    ->select('it.id', 'it.no as inv_no', 'it.inv_trx_date', 'p.no as purchase_no', 'pa.no as purchase_asset_no', 'it.no_surat_jalan', 'p.with_ppn as p_with_ppn', 'pa.with_ppn as pa_with_ppn', 'p.is_without_ppn as p_without_ppn', 'pa.is_without_ppn as pa_without_ppn', 'ms1.name as supplier1', 'ms2.name as supplier2')
                    // ->join('inv_trx_ds as itd', 'it.id', 'itd.inv_trx_id')
                    // ->join('m_items as mi', 'mi.id', 'itd.m_item_id')
                    ->leftJoin('purchases as p', 'it.purchase_id', 'p.id')
                    ->leftJoin('purchase_assets as pa', 'it.purchase_asset_id', 'pa.id')
                    ->leftJoin('m_suppliers as ms1', 'ms1.id', 'p.m_supplier_id')
                    ->leftJoin('m_suppliers as ms2', 'ms2.id', 'pa.m_supplier_id')
                    ->where('it.site_id', $location_id)
                    ->where('trx_type', 'RECEIPT')
                    // ->where('itd.condition', 1)
                    ->where('it.inv_trx_date', '>=', $date1)
                    ->where('it.inv_trx_date', '<=', $date2)
                    ->orderBy('it.inv_trx_date');
                    // ->get();
        if($no != ''){
            $getPO=DB::table('purchases as p')
            ->join('inv_trxes as it', 'it.purchase_id', 'p.id')
            ->where('it.inv_trx_date', '>=', $date1)
            ->where('it.inv_trx_date', '<=', $date2)
            ->where('p.no', 'ilike', '%'.$no.'%')
            ->pluck('it.id');
            $getPOAsset=DB::table('purchase_assets as p')
            ->join('inv_trxes as it', 'it.purchase_asset_id', 'p.id')
            ->where('it.inv_trx_date', '>=', $date1)
            ->where('it.inv_trx_date', '<=', $date2)
            ->where('p.no', 'ilike', '%'.$no.'%')
            ->pluck('it.id');
            $getSuratJalan=DB::table('inv_trxes as it')
            ->where('it.inv_trx_date', '>=', $date1)
            ->where('it.inv_trx_date', '<=', $date2)
            ->where('it.no_surat_jalan', 'ilike', '%'.$no.'%')
            ->pluck('it.id');
            $temp_id=array();
            foreach($getPO as $row){
                array_push($temp_id, $row);
            }
            foreach($getPOAsset as $row){
                array_push($temp_id, $row);
            }
            foreach($getSuratJalan as $row){
                array_push($temp_id, $row);
            }
            if($temp_id != null){
                $dtSaldo->whereIn('it.id', $temp_id);
            }
        }
        $dtSaldo=$dtSaldo->get();
        foreach($dtSaldo as $row){
            $item=DB::table('inv_trx_ds as itd')
                        ->where('condition', '!=', 3)
                        ->leftJoin('m_items as mi', 'mi.id', 'itd.m_item_id')
                        ->leftJoin('m_units as mu', 'mu.id', 'itd.m_unit_id')
                        ->where('itd.inv_trx_id', $row->id)
                        ->select('mi.name as item_name', 'mi.no as code_item', 'itd.*', 'mu.name as unit_name')
                        ->get();
            $row->item=$item;
        }
        $data_temp=$dtSaldo;
        $data = array(
            'no'        => $no,
            'date1'     => $date1,
            'date2'     => $date2,
            'data'      => $data_temp,
        );
        
        return Excel::download(new StockInTaxExport($data), 'laporan_stok_masuk.xlsx');
    }
    public function hitungStok(Request $request){
        $this_month=date('Y-m');
        if ($request->input('bulan')) {
            $month=$request->input('tahun').'-'.$request->input('bulan');
            $this_month=date('Y-m', strtotime($month));
        }
        
        $date_before=date('Y-m', strtotime("- 1 months",  strtotime($this_month)));
        $id=$this->site_id;
        $datas = DB::select("
                select 
                    (COALESCE(inv_in.site_id, inv_out.site_id)) as site_id, 
                    (COALESCE(inv_in.m_item_id, inv_out.m_item_id)) as m_item_id,
                    (COALESCE(inv_in.m_unit_id, inv_out.m_unit_id)) as m_unit_id,
                    (COALESCE(inv_in.m_warehouse_id, inv_out.m_warehouse_id)) as m_warehouse_id, 
                    (COALESCE(inv_in.amount, 0)) as amount_in,
                    (COALESCE(inv_out.amount, 0) - COALESCE(inv_out.amount_ret, 0)) as amount_out,
                    (COALESCE(inv_out.amount_ret, 0)) as amount_ret,
                    ((COALESCE(inv_in.amount, 0) + COALESCE(inv_out.amount_ret, 0)) - (COALESCE(inv_out.amount, 0))) as stok,
                    inv_in.updated_at as last_update_in,
                    inv_out.updated_at as last_update_out
                from (select site_id, m_item_id, max(m_unit_id) as m_unit_id, sum(amount) as amount, itd.m_warehouse_id as m_warehouse_id, max(it.updated_at) as updated_at from inv_trxes it
                join inv_trx_ds itd on it.id = itd.inv_trx_id
                where is_entry = true and site_id = ".$id." and trx_type != 'RET_ITEM' and trx_type != 'TRF_STK' and inv_trx_date::text like '%".$this_month."%' and itd.condition = 1 and itd.type_material != 'TRF_STK'
                group by site_id, m_item_id, itd.m_warehouse_id) inv_in
                full outer join (select site_id, m_item_id, max(m_unit_id) as m_unit_id, itd.m_warehouse_id, coalesce((SELECT sum(amount) as amount from inv_trxes it1
                join inv_trx_ds itd1 on it1.id = itd1.inv_trx_id
                where trx_type = 'RET_ITEM' and it1.site_id=it.site_id and itd1.m_item_id=itd.m_item_id and itd1.m_warehouse_id=itd.m_warehouse_id and itd1.condition = 1 and itd1.type_material != 'TRF_STK'), 0) AS amount_ret, sum(amount) as amount, max(it.updated_at) as updated_at from inv_trxes it
                join inv_trx_ds itd on it.id = itd.inv_trx_id
                where is_entry = false and site_id = ".$id." and trx_type != 'TRF_STK' and inv_trx_date::text like '%".$this_month."%' and itd.condition = 1 and itd.type_material != 'TRF_STK'
                group by site_id, m_item_id, itd.m_warehouse_id) inv_out on inv_in.m_item_id = inv_out.m_item_id and inv_in.site_id = inv_out.site_id  and inv_in.m_warehouse_id = inv_out.m_warehouse_id
                ");
                
        $temp_id=[];
        foreach ($datas as $key => $value) {
            $query=DB::table('calculate_stocks')
                        ->where('site_id', $value->site_id)
                        ->where('m_warehouse_id', $value->m_warehouse_id)
                        ->where('m_item_id', $value->m_item_id)
                        ->where('last_month', $date_before)
                        ->where('type', 'STK_NORMAL')
                        ->first();
            $cek_this_month=DB::table('calculate_stocks')
                        ->where('site_id', $value->site_id)
                        ->where('m_warehouse_id', $value->m_warehouse_id)
                        ->where('m_item_id', $value->m_item_id)
                        ->where('last_month', $this_month)
                        ->where('type', 'STK_NORMAL')
                        ->first();
            if($query != null){
                array_push($temp_id, $query->id);
            }
            
            $get_save_price=DB::table('m_item_prices')->where(['m_item_id' => $value->m_item_id, 'm_unit_id' => $value->m_unit_id, 'site_id' => $value->site_id])->first();
            if ($cek_this_month == null) {
                try
                {
                    $headers = [
                        'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                        'Accept'        => 'application/json',
                    ];
                    $client = new Client(['base_uri' => $this->base_api_url . '/inv/base/CalculateStock']);
                    $reqBody = [
                        'headers' => $headers,
                        'json' => [
                            'site_id' => $value->site_id,
                            'm_item_id' => $value->m_item_id,
                            'amount' => ($query != null ? $query->amount : 0) + $value->stok,
                            'amount_in' => ($query != null ? $query->amount_in : 0) + $value->amount_in,
                            'amount_out' => ($query != null ? $query->amount_out : 0) + $value->amount_out,
                            'm_unit_id' => $value->m_unit_id,
                            'm_warehouse_id' => $value->m_warehouse_id,
                            'type'  => 'STK_NORMAL',
                            'last_month'    => $this_month,
                            'price'     => isset($get_save_price->price) ? $get_save_price->price : 0 
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
                    $client = new Client(['base_uri' => $this->base_api_url . '/inv/base/CalculateStock/'.$cek_this_month->id]);
                    $reqBody = [
                        'headers' => $headers,
                        'json' => [
                            'amount' => ($query != null ? $query->amount : 0) + $value->stok,
                            'amount_in' => ($query != null ? $query->amount_in : 0) + $value->amount_in,
                            'amount_out' => ($query != null ? $query->amount_out : 0) + $value->amount_out,
                            'price'     => isset($get_save_price->price) ? $get_save_price->price : 0
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
        
        $query_not_in=DB::table('calculate_stocks')
                        ->where('last_month', $date_before)
                        ->whereNotIn('id', $temp_id)
                        ->where('type', 'STK_NORMAL')
                        ->get();
        foreach ($query_not_in as $key => $value) {
            $get_save_price=DB::table('m_item_prices')->where(['m_item_id' => $value->m_item_id, 'm_unit_id' => $value->m_unit_id, 'site_id' => $value->site_id])->first();

            $cek_this_month=DB::table('calculate_stocks')
                        ->where('site_id', $value->site_id)
                        ->where('m_warehouse_id', $value->m_warehouse_id)
                        ->where('m_item_id', $value->m_item_id)
                        ->where('last_month', $this_month)
                        ->where('type', 'STK_NORMAL')
                        ->first();
            if ($cek_this_month == null) {
                try
                {
                    $headers = [
                        'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                        'Accept'        => 'application/json',
                    ];
                    $client = new Client(['base_uri' => $this->base_api_url . '/inv/base/CalculateStock']);
                    $reqBody = [
                        'headers' => $headers,
                        'json' => [
                            'site_id' => $value->site_id,
                            'm_item_id' => $value->m_item_id,
                            'amount' => $value->amount,
                            'amount_in' => $value->amount_in,
                            'amount_out' => $value->amount_out,
                            'm_unit_id' => $value->m_unit_id,
                            'm_warehouse_id' => $value->m_warehouse_id,
                            'type'  => 'STK_NORMAL',
                            'last_month'    => $this_month,
                            'price'     => isset($get_save_price->price) ? $get_save_price->price : 0
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
                    $client = new Client(['base_uri' => $this->base_api_url . '/inv/base/CalculateStock/'.$cek_this_month->id]);
                    $reqBody = [
                        'headers' => $headers,
                        'json' => [
                            'amount' => $value->amount,
                            'amount_in' => $value->amount_in,
                            'amount_out' => $value->amount_out,
                            'price'     => $get_save_price->price
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
        
        $this->hitungStokTrf($this_month);
        return redirect('inventory/hitung_stok');
    }
    public function hitungStokTrf($date){
        $this_month=$date;
        $date_before=date('Y-m', strtotime("- 1 months",  strtotime($this_month)));
        $id=$this->site_id;
        $datas = DB::select("
                select 
                    (COALESCE(inv_in.site_id, inv_out.site_id)) as site_id, 
                    (COALESCE(inv_in.m_item_id, inv_out.m_item_id)) as m_item_id,
                    (COALESCE(inv_in.m_unit_id, inv_out.m_unit_id)) as m_unit_id,
                    (COALESCE(inv_in.m_warehouse_id, inv_out.m_warehouse_id)) as m_warehouse_id, 
                    (COALESCE(inv_in.amount, 0)) as amount_in,
                    (COALESCE(inv_out.amount, 0) - COALESCE(inv_out.amount_ret, 0)) as amount_out,
                    (COALESCE(inv_out.amount_ret, 0)) as amount_ret,
                    ((COALESCE(inv_in.amount, 0) + COALESCE(inv_out.amount_ret, 0)) - (COALESCE(inv_out.amount, 0))) as stok,
                    inv_in.updated_at as last_update_in,
                    inv_out.updated_at as last_update_out
                from (select site_id, m_item_id, max(m_unit_id) as m_unit_id, sum(amount) as amount, itd.m_warehouse_id as m_warehouse_id, max(it.updated_at) as updated_at from inv_trxes it
                join inv_trx_ds itd on it.id = itd.inv_trx_id
                where is_entry = true and site_id = ".$id." and trx_type != 'RET_ITEM' and inv_trx_date::text like '%".$this_month."%' and itd.condition = 1 and itd.type_material = 'TRF_STK'
                group by site_id, m_item_id, itd.m_warehouse_id) inv_in
                full outer join (select site_id, m_item_id, max(m_unit_id) as m_unit_id, itd.m_warehouse_id, coalesce((SELECT sum(amount) as amount from inv_trxes it1
                join inv_trx_ds itd1 on it1.id = itd1.inv_trx_id
                where trx_type = 'RET_ITEM' and it1.site_id=it.site_id and itd1.m_item_id=itd.m_item_id and itd1.m_warehouse_id=itd.m_warehouse_id and itd1.condition = 1 and itd1.type_material = 'TRF_STK'), 0) AS amount_ret, sum(amount) as amount, max(it.updated_at) as updated_at from inv_trxes it
                join inv_trx_ds itd on it.id = itd.inv_trx_id
                where is_entry = false and site_id = ".$id." and inv_trx_date::text like '%".$this_month."%' and itd.condition = 1 and itd.type_material = 'TRF_STK'
                group by site_id, m_item_id, itd.m_warehouse_id) inv_out on inv_in.m_item_id = inv_out.m_item_id and inv_in.site_id = inv_out.site_id  and inv_in.m_warehouse_id = inv_out.m_warehouse_id
                ");
        $temp_id=[];
        foreach ($datas as $key => $value) {
            $query=DB::table('calculate_stocks')
                        ->where('site_id', $value->site_id)
                        ->where('m_warehouse_id', $value->m_warehouse_id)
                        ->where('m_item_id', $value->m_item_id)
                        ->where('last_month', $date_before)
                        ->where('type', 'TRF_STK')
                        ->first();
            $cek_this_month=DB::table('calculate_stocks')
                        ->where('site_id', $value->site_id)
                        ->where('m_warehouse_id', $value->m_warehouse_id)
                        ->where('m_item_id', $value->m_item_id)
                        ->where('last_month', $this_month)
                        ->where('type', 'TRF_STK')
                        ->first();
            if($query != null){
                array_push($temp_id, $query->id);
            }
            if ($cek_this_month == null) {
                try
                {
                    $headers = [
                        'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                        'Accept'        => 'application/json',
                    ];
                    $client = new Client(['base_uri' => $this->base_api_url . '/inv/base/CalculateStock']);
                    $reqBody = [
                        'headers' => $headers,
                        'json' => [
                            'site_id' => $value->site_id,
                            'm_item_id' => $value->m_item_id,
                            'amount' => $value->stok,
                            'amount_in' => $value->amount_in,
                            'amount_out' => $value->amount_out,
                            'm_unit_id' => $value->m_unit_id,
                            'm_warehouse_id' => $value->m_warehouse_id,
                            'type'  => 'TRF_STK',
                            'last_month'    => $this_month,
                            'price'     => 0,
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
                    $client = new Client(['base_uri' => $this->base_api_url . '/inv/base/CalculateStock/'.$cek_this_month->id]);
                    $reqBody = [
                        'headers' => $headers,
                        'json' => [
                            'amount' => ($query != null ? $query->amount : 0) + $value->stok,
                            'amount_in' => ($query != null ? $query->amount_in : 0) + $value->amount_in,
                            'amount_out' => ($query != null ? $query->amount_out : 0) + $value->amount_out,
                            'price'     => 0,
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
        $query_not_in=DB::table('calculate_stocks')
                        ->where('last_month', $date_before)
                        ->whereNotIn('id', $temp_id)
                        ->where('type', 'TRF_STK')
                        ->get();
        foreach ($query_not_in as $key => $value) {
            $cek_this_month=DB::table('calculate_stocks')
                        ->where('site_id', $value->site_id)
                        ->where('m_warehouse_id', $value->m_warehouse_id)
                        ->where('m_item_id', $value->m_item_id)
                        ->where('last_month', $this_month)
                        ->where('type', 'TRF_STK')
                        ->first();
            if ($cek_this_month == null) {
                try
                {
                    $headers = [
                        'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                        'Accept'        => 'application/json',
                    ];
                    $client = new Client(['base_uri' => $this->base_api_url . '/inv/base/CalculateStock']);
                    $reqBody = [
                        'headers' => $headers,
                        'json' => [
                            'site_id' => $value->site_id,
                            'm_item_id' => $value->m_item_id,
                            'amount' => $value->amount,
                            'amount_in' => $value->amount_in,
                            'amount_out' => $value->amount_out,
                            'm_unit_id' => $value->m_unit_id,
                            'm_warehouse_id' => $value->m_warehouse_id,
                            'type'  => 'TRF_STK',
                            'last_month'    => $this_month,
                            'price'     => 0
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
                    $client = new Client(['base_uri' => $this->base_api_url . '/inv/base/CalculateStock/'.$cek_this_month->id]);
                    $reqBody = [
                        'headers' => $headers,
                        'json' => [
                            'amount' => $value->amount,
                            'amount_in' => $value->amount_in,
                            'amount_out' => $value->amount_out,
                            'price'     => $get_save_price->price
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
    public function indexHitungStok(){
        return  view('pages.inv.inventory_transaction.hitung_stok');
    }
    public function siteStockPeriodic(Request $request) {
        $warehouse=DB::table('m_warehouses')->where('site_id', $this->site_id)->get();
        $data=array(
            'warehouse' => $warehouse
        );
        return view('pages.inv.inventory_transaction.stock_periodic', $data);
    }
    public function siteStockPeriodicJson(Request $request) {
        $location_id=$this->site_id;
        $date1=date('Y-m-d');
        $date2=date('Y-m-d');
        if ($request->input('date')) {
            $date1=$request->input('date');
            $date2=$request->input('date2');
        }
        $category=$request->category;
        $warehouse_id=$request->warehouse_id;
        $startTime = strtotime( $date1 );
        $endTime = strtotime( $date2 );
        $date=date('Y-m', $startTime);
        $date_before=date('Y-m', strtotime("- 1 months",  strtotime($date)));
        $first_date_month=$date.'-01';
        $min=$startTime - 86400;//kurangi sehari
        $item_id=DB::table('calculate_stocks as cs')
                    ->join('m_items as mi', 'mi.id', 'cs.m_item_id')
                    ->groupBy('mi.id')
                    ->whereNotIn('category', ['ATK', 'ALAT KERJA']);
        if($category != null){
            if($category != 'all'){
                $item_id->where('category', $category);
            }
        }
        $item_id=$item_id->pluck('mi.id');

        $warehouse=DB::table('m_warehouses')->where('code', '!=', 'WH_S1');
        if($warehouse_id != null){
            $warehouse->where('id', $warehouse_id);
        }
        $warehouse=$warehouse->get();

        $items=DB::table('m_items')->select('m_items.id', 'm_items.name', 'm_items.no', 'm_units.name as unit_name', 'category')->join('m_units', 'm_units.id', 'm_items.m_unit_id')->whereIn('m_items.id', $item_id)->get();
        $data_temp=array();
        foreach ($items as $key => $value) {
            $price_month=DB::table('calculate_prices')->select('amount', 'amount_in', 'amount_out', 'price')->where('last_month', $date_before)->where('m_item_id', $value->id)->where('site_id', $this->site_id)->first();
            $total_change_in=$change_in=$change_out=$total_change_out=0;
            if($first_date_month < date('Y-m-d', $min)){
                $inv_detail=DB::table('inv_trxes as it')
                            ->join('inv_trx_ds as itd', 'itd.inv_trx_id', 'it.id')
                            ->where('it.site_id', $this->site_id)
                            // ->where('it.trx_type', 'RECEIPT')
                            ->whereIn('it.trx_type', ['REQ_ITEM', 'REQ_ITEM_SP', 'INV_SALE', 'STK_ADJ', 'RECEIPT'])
                            ->where('itd.m_item_id', $value->id)
                            ->where('itd.condition', 1)
                            ->where('itd.type_material', 'STK_NORMAL')
                            ->where('it.inv_trx_date', '>=', $first_date_month)
                            ->where('it.inv_trx_date', '<=', date('Y-m-d', $min))
                            ->select('itd.amount', 'itd.base_price', 'it.trx_type')
                            ->get();
                foreach ($inv_detail as $k => $v) {
                    $total_change_in+=($v->trx_type == 'RECEIPT' ? ($v->amount * $v->base_price) : 0);
                    $total_change_out+=($v->trx_type != 'RECEIPT' ? ($v->amount * $v->base_price) : 0);
                    $change_in+=($v->trx_type == 'RECEIPT' ? $v->amount : 0);
                    $change_out+=($v->trx_type != 'RECEIPT' ? $v->amount : 0);
                }
            }

            $value_stok_change=$total_change_in - $total_change_out;
            $total_stock_change=$change_in - $change_out;
            $total_stock_first=$price_month != null ? $price_month->amount + $total_stock_change : $total_stock_change;
            $price_first=$price_month != null ? (($price_month->amount * $price_month->price) + $value_stok_change) / ($total_stock_first != 0 ? $total_stock_first : 1) : ($value_stok_change / ($total_stock_first != 0 ? $total_stock_first : 1));
            $total_penerimaan=$total_material_penerimaan=$total_pengeluaran=$total_material_pengeluaran=0;
            $inv_detail=DB::table('inv_trxes as it')
                            ->join('inv_trx_ds as itd', 'itd.inv_trx_id', 'it.id')
                            ->where('it.site_id', $this->site_id)
                            // ->where('it.trx_type', 'RECEIPT')
                            ->whereIn('it.trx_type', ['REQ_ITEM', 'REQ_ITEM_SP', 'INV_SALE', 'STK_ADJ', 'RECEIPT'])
                            ->where('itd.m_item_id', $value->id)
                            ->where('itd.condition', 1)
                            ->where('itd.type_material', 'STK_NORMAL')
                            ->where('it.inv_trx_date', '>=', $date1)
                            ->where('it.inv_trx_date', '<=', $date2)
                            ->select('itd.amount', 'itd.base_price', 'it.trx_type')
                            ->get();
            foreach ($inv_detail as $k => $v) {
                $total_penerimaan+=($v->trx_type == 'RECEIPT' ? ($v->amount * $v->base_price) : 0);
                $total_material_penerimaan+=($v->trx_type == 'RECEIPT' ? $v->amount : 0);
                $total_pengeluaran+=($v->trx_type != 'RECEIPT' ? ($v->amount * $v->base_price) : 0);
                $total_material_pengeluaran+=($v->trx_type != 'RECEIPT' ? $v->amount : 0);
            }

            $value_stock_run=$total_penerimaan - $total_pengeluaran;
            $stok_run=$total_material_penerimaan - $total_material_pengeluaran;
            $stock_all=$total_stock_first + $stok_run;
            $value_stok_all=(($total_stock_first * $price_first) + $value_stock_run);
            $price_last=$value_stok_all / ($stock_all != 0 ? $stock_all : 1);
            
            // $value->price_last=$price_last;

            foreach($warehouse as $row){
                $item=DB::table('calculate_stocks as cs')->where('m_warehouse_id', $row->id)->where('m_item_id', $value->id)->where('last_month', $date_before)->where('type', 'STK_NORMAL')->first();
                $datas = DB::select("
                select 
                    (COALESCE(inv_in.site_id, inv_out.site_id)) as site_id, 
                    (COALESCE(inv_in.m_item_id, inv_out.m_item_id)) as m_item_id,
                    (COALESCE(inv_in.m_unit_id, inv_out.m_unit_id)) as m_unit_id,
                    (COALESCE(inv_in.m_warehouse_id, inv_out.m_warehouse_id)) as m_warehouse_id, 
                    (COALESCE(inv_in.amount, 0)) as amount_in,
                    (COALESCE(inv_out.amount, 0) - COALESCE(inv_out.amount_ret, 0)) as amount_out,
                    (COALESCE(inv_out.amount_ret, 0)) as amount_ret,
                    ((COALESCE(inv_in.amount, 0) + COALESCE(inv_out.amount_ret, 0)) - (COALESCE(inv_out.amount, 0))) as stok,
                    inv_in.updated_at as last_update_in,
                    inv_out.updated_at as last_update_out
                from (select site_id, m_item_id, max(m_unit_id) as m_unit_id, sum(amount) as amount, itd.m_warehouse_id as m_warehouse_id, max(it.updated_at) as updated_at from inv_trxes it
                join inv_trx_ds itd on it.id = itd.inv_trx_id
                where is_entry = true and site_id = ".$this->site_id." and trx_type != 'RET_ITEM' and trx_type != 'TRF_STK' and inv_trx_date::timestamp >= '".date('Y-m-d H:i:s', strtotime($date1))."' and inv_trx_date::timestamp <= '".date('Y-m-d H:i:s', strtotime($date2))."' and itd.m_warehouse_id = ".$row->id." and m_item_id = ".$value->id." and itd.condition = 1 and itd.type_material != 'TRF_STK'
                group by site_id, m_item_id, itd.m_warehouse_id) inv_in
                full outer join (select site_id, m_item_id, max(m_unit_id) as m_unit_id, itd.m_warehouse_id, coalesce((SELECT sum(amount) as amount from inv_trxes it1
                join inv_trx_ds itd1 on it1.id = itd1.inv_trx_id
                where trx_type = 'RET_ITEM' and it1.site_id=it.site_id and itd1.m_item_id=itd.m_item_id and itd1.m_warehouse_id=itd.m_warehouse_id and itd1.condition = 1 and itd1.type_material != 'TRF_STK'), 0) AS amount_ret, sum(amount) as amount, max(it.updated_at) as updated_at from inv_trxes it
                join inv_trx_ds itd on it.id = itd.inv_trx_id
                where is_entry = false and site_id = ".$this->site_id." and itd.m_warehouse_id = ".$row->id." and m_item_id = ".$value->id." and trx_type != 'TRF_STK' and inv_trx_date::timestamp >= '".date('Y-m-d H:i:s', strtotime($date1))."' and inv_trx_date::timestamp <= '".date('Y-m-d H:i:s', strtotime($date2))."' and itd.condition = 1 and itd.type_material != 'TRF_STK'
                group by site_id, m_item_id, itd.m_warehouse_id) inv_out on inv_in.m_item_id = inv_out.m_item_id and inv_in.site_id = inv_out.site_id  and inv_in.m_warehouse_id = inv_out.m_warehouse_id
                ");
                
                $stok_awal=$item != null ? $item->amount : 0;
                $amount_in_before=$item != null ? $item->amount_in : 0;
                $amount_out_before=$item != null ? $item->amount_out : 0;
                $stok=$stok_awal + ($datas != null ? $datas[0]->stok : 0);
                // $amount_in=$amount_in_before + ($datas != null ? $datas[0]->amount_in : 0);
                // $amount_out=$amount_out_before + ($datas != null ? $datas[0]->amount_out : 0);
                if($stok != 0){
                    $data_temp[]=(object)array(
                        'm_item_id'     => $value->id,
                        'm_warehouse_id'     => $row->id,
                        'name'     => $value->name,
                        'no'     => $value->no,
                        'unit_name'     => $value->unit_name,
                        'category'     => $value->category,
                        'price_last'     => $price_last,
                        'warehouse'     => $row->name,
                        'stok'          => $stok,
                        'stok_awal'     => $stok_awal,
                        'amount_in_before'  => $amount_in_before,
                        'amount_out_before'  => $amount_out_before,
                        'amount_in'    => ($datas != null ? $datas[0]->amount_in : 0),
                        'amount_out'    => ($datas != null ? $datas[0]->amount_out : 0),
                        'total'          => $stok * $price_last,
                    );
                }
            }
        }
        
        $data=DataTables::of($data_temp)
                    ->make(true); 
        return $data;
    }
    public function exportStockPeriodic(Request $request) {
        $location_id=$this->site_id;
        $date1=date('Y-m-d');
        $date2=date('Y-m-d');
        if ($request->input('date')) {
            $date1=$request->input('date');
            $date2=$request->input('date2');
        }
        $category=$request->category;
        $warehouse_id=$request->warehouse_id;
        $startTime = strtotime( $date1 );
        $endTime = strtotime( $date2 );
        $date=date('Y-m', $startTime);
        $date_before=date('Y-m', strtotime("- 1 months",  strtotime($date)));
        $first_date_month=$date.'-01';
        $min=$startTime - 86400;//kurangi sehari
        $item_id=DB::table('calculate_stocks as cs')
                    ->join('m_items as mi', 'mi.id', 'cs.m_item_id')
                    ->groupBy('mi.id')
                    ->whereNotIn('category', ['ATK', 'ALAT KERJA']);
        if($category != null){
            if($category != 'all'){
                $item_id->where('category', $category);
            }
        }
        $item_id=$item_id->pluck('mi.id');

        $warehouse=DB::table('m_warehouses')->where('code', '!=', 'WH_S1');
        if($warehouse_id != null){
            $warehouse->where('id', $warehouse_id);
        }
        $warehouse=$warehouse->get();

        $items=DB::table('m_items')->select('m_items.id', 'm_items.name', 'm_items.no', 'm_units.name as unit_name', 'category')->join('m_units', 'm_units.id', 'm_items.m_unit_id')->whereIn('m_items.id', $item_id)->get();
        $data_temp=array();
        foreach ($items as $key => $value) {
            $price_month=DB::table('calculate_prices')->select('amount', 'amount_in', 'amount_out', 'price')->where('last_month', $date_before)->where('m_item_id', $value->id)->where('site_id', $this->site_id)->first();
            $total_change_in=$change_in=$change_out=$total_change_out=0;
            if($first_date_month < date('Y-m-d', $min)){
                $inv_detail=DB::table('inv_trxes as it')
                            ->join('inv_trx_ds as itd', 'itd.inv_trx_id', 'it.id')
                            ->where('it.site_id', $this->site_id)
                            // ->where('it.trx_type', 'RECEIPT')
                            ->whereIn('it.trx_type', ['REQ_ITEM', 'REQ_ITEM_SP', 'INV_SALE', 'STK_ADJ', 'RECEIPT'])
                            ->where('itd.m_item_id', $value->id)
                            ->where('itd.condition', 1)
                            ->where('itd.type_material', 'STK_NORMAL')
                            ->where('it.inv_trx_date', '>=', $first_date_month)
                            ->where('it.inv_trx_date', '<=', date('Y-m-d', $min))
                            ->select('itd.amount', 'itd.base_price', 'it.trx_type')
                            ->get();
                foreach ($inv_detail as $k => $v) {
                    $total_change_in+=($v->trx_type == 'RECEIPT' ? ($v->amount * $v->base_price) : 0);
                    $total_change_out+=($v->trx_type != 'RECEIPT' ? ($v->amount * $v->base_price) : 0);
                    $change_in+=($v->trx_type == 'RECEIPT' ? $v->amount : 0);
                    $change_out+=($v->trx_type != 'RECEIPT' ? $v->amount : 0);
                }
            }

            $value_stok_change=$total_change_in - $total_change_out;
            $total_stock_change=$change_in - $change_out;
            $total_stock_first=$price_month != null ? $price_month->amount + $total_stock_change : $total_stock_change;
            $price_first=$price_month != null ? (($price_month->amount * $price_month->price) + $value_stok_change) / ($total_stock_first != 0 ? $total_stock_first : 1) : ($value_stok_change / ($total_stock_first != 0 ? $total_stock_first : 1));
            $total_penerimaan=$total_material_penerimaan=$total_pengeluaran=$total_material_pengeluaran=0;
            $inv_detail=DB::table('inv_trxes as it')
                            ->join('inv_trx_ds as itd', 'itd.inv_trx_id', 'it.id')
                            ->where('it.site_id', $this->site_id)
                            // ->where('it.trx_type', 'RECEIPT')
                            ->whereIn('it.trx_type', ['REQ_ITEM', 'REQ_ITEM_SP', 'INV_SALE', 'STK_ADJ', 'RECEIPT'])
                            ->where('itd.m_item_id', $value->id)
                            ->where('itd.condition', 1)
                            ->where('itd.type_material', 'STK_NORMAL')
                            ->where('it.inv_trx_date', '>=', $date1)
                            ->where('it.inv_trx_date', '<=', $date2)
                            ->select('itd.amount', 'itd.base_price', 'it.trx_type')
                            ->get();
            foreach ($inv_detail as $k => $v) {
                $total_penerimaan+=($v->trx_type == 'RECEIPT' ? ($v->amount * $v->base_price) : 0);
                $total_material_penerimaan+=($v->trx_type == 'RECEIPT' ? $v->amount : 0);
                $total_pengeluaran+=($v->trx_type != 'RECEIPT' ? ($v->amount * $v->base_price) : 0);
                $total_material_pengeluaran+=($v->trx_type != 'RECEIPT' ? $v->amount : 0);
            }

            $value_stock_run=$total_penerimaan - $total_pengeluaran;
            $stok_run=$total_material_penerimaan - $total_material_pengeluaran;
            $stock_all=$total_stock_first + $stok_run;
            $value_stok_all=(($total_stock_first * $price_first) + $value_stock_run);
            $price_last=$value_stok_all / ($stock_all != 0 ? $stock_all : 1);
            
            // $value->price_last=$price_last;

            foreach($warehouse as $row){
                $item=DB::table('calculate_stocks as cs')->where('m_warehouse_id', $row->id)->where('m_item_id', $value->id)->where('last_month', $date_before)->where('type', 'STK_NORMAL')->first();
                $datas = DB::select("
                select 
                    (COALESCE(inv_in.site_id, inv_out.site_id)) as site_id, 
                    (COALESCE(inv_in.m_item_id, inv_out.m_item_id)) as m_item_id,
                    (COALESCE(inv_in.m_unit_id, inv_out.m_unit_id)) as m_unit_id,
                    (COALESCE(inv_in.m_warehouse_id, inv_out.m_warehouse_id)) as m_warehouse_id, 
                    (COALESCE(inv_in.amount, 0)) as amount_in,
                    (COALESCE(inv_out.amount, 0) - COALESCE(inv_out.amount_ret, 0)) as amount_out,
                    (COALESCE(inv_out.amount_ret, 0)) as amount_ret,
                    ((COALESCE(inv_in.amount, 0) + COALESCE(inv_out.amount_ret, 0)) - (COALESCE(inv_out.amount, 0))) as stok,
                    inv_in.updated_at as last_update_in,
                    inv_out.updated_at as last_update_out
                from (select site_id, m_item_id, max(m_unit_id) as m_unit_id, sum(amount) as amount, itd.m_warehouse_id as m_warehouse_id, max(it.updated_at) as updated_at from inv_trxes it
                join inv_trx_ds itd on it.id = itd.inv_trx_id
                where is_entry = true and site_id = ".$this->site_id." and trx_type != 'RET_ITEM' and trx_type != 'TRF_STK' and inv_trx_date::timestamp >= '".date('Y-m-d H:i:s', strtotime($date1))."' and inv_trx_date::timestamp <= '".date('Y-m-d H:i:s', strtotime($date2))."' and itd.m_warehouse_id = ".$row->id." and m_item_id = ".$value->id." and itd.condition = 1 and itd.type_material != 'TRF_STK'
                group by site_id, m_item_id, itd.m_warehouse_id) inv_in
                full outer join (select site_id, m_item_id, max(m_unit_id) as m_unit_id, itd.m_warehouse_id, coalesce((SELECT sum(amount) as amount from inv_trxes it1
                join inv_trx_ds itd1 on it1.id = itd1.inv_trx_id
                where trx_type = 'RET_ITEM' and it1.site_id=it.site_id and itd1.m_item_id=itd.m_item_id and itd1.m_warehouse_id=itd.m_warehouse_id and itd1.condition = 1 and itd1.type_material != 'TRF_STK'), 0) AS amount_ret, sum(amount) as amount, max(it.updated_at) as updated_at from inv_trxes it
                join inv_trx_ds itd on it.id = itd.inv_trx_id
                where is_entry = false and site_id = ".$this->site_id." and itd.m_warehouse_id = ".$row->id." and m_item_id = ".$value->id." and trx_type != 'TRF_STK' and inv_trx_date::timestamp >= '".date('Y-m-d H:i:s', strtotime($date1))."' and inv_trx_date::timestamp <= '".date('Y-m-d H:i:s', strtotime($date2))."' and itd.condition = 1 and itd.type_material != 'TRF_STK'
                group by site_id, m_item_id, itd.m_warehouse_id) inv_out on inv_in.m_item_id = inv_out.m_item_id and inv_in.site_id = inv_out.site_id  and inv_in.m_warehouse_id = inv_out.m_warehouse_id
                ");
                $stok_awal=$item != null ? $item->amount : 0;
                $amount_in_before=$item != null ? $item->amount_in : 0;
                $amount_out_before=$item != null ? $item->amount_out : 0;
                $stok=$stok_awal + ($datas != null ? $datas[0]->stok : 0);
                // $amount_in=$amount_in_before + ($datas != null ? $datas[0]->amount_in : 0);
                // $amount_out=$amount_out_before + ($datas != null ? $datas[0]->amount_out : 0);
                if($stok != 0){
                    $data_temp[]=(object)array(
                        'm_item_id'     => $value->id,
                        'name'     => $value->name,
                        'no'     => $value->no,
                        'unit_name'     => $value->unit_name,
                        'category'     => $value->category,
                        'price_last'     => $price_last,
                        'warehouse'     => $row->name,
                        'stok'          => $stok,
                        'stok_awal'     => $stok_awal,
                        'amount_in_before'  => $amount_in_before,
                        'amount_out_before'  => $amount_out_before,
                        'amount_in'    => ($datas != null ? $datas[0]->amount_in : 0),
                        'amount_out'    => ($datas != null ? $datas[0]->amount_out : 0),
                        'total'          => $stok * $price_last,
                    );
                }
            }
        }
        $data=array(
            'warehouse' => ($warehouse_id != null ? $warehouse[0]->name : ''),
            'category'  => ($category != 'all' ? $category : ''),
            'date1'  => $date1,
            'date2'     => $date2,
            'data' => $data_temp,
        );
        return Excel::download(new StockPeriodicExport($data), 'stok_periodic.xlsx');
    }
    public function hitungStokItem(Request $request){
        $item=$request->item_id;
        
        $this_month=date('Y-m');
        $date_before=date('Y-m', strtotime("- 1 months",  strtotime($this_month)));
        $id=$this->site_id;
        //hitung stok normal
        foreach($item as $row){
            $datas = DB::select("
                select 
                    (COALESCE(inv_in.site_id, inv_out.site_id)) as site_id, 
                    (COALESCE(inv_in.m_item_id, inv_out.m_item_id)) as m_item_id,
                    (COALESCE(inv_in.m_unit_id, inv_out.m_unit_id)) as m_unit_id,
                    (COALESCE(inv_in.m_warehouse_id, inv_out.m_warehouse_id)) as m_warehouse_id, 
                    (COALESCE(inv_in.amount, 0)) as amount_in,
                    (COALESCE(inv_out.amount, 0) - COALESCE(inv_out.amount_ret, 0)) as amount_out,
                    (COALESCE(inv_out.amount_ret, 0)) as amount_ret,
                    ((COALESCE(inv_in.amount, 0) + COALESCE(inv_out.amount_ret, 0)) - (COALESCE(inv_out.amount, 0))) as stok,
                    inv_in.updated_at as last_update_in,
                    inv_out.updated_at as last_update_out
                from (select site_id, m_item_id, max(m_unit_id) as m_unit_id, sum(amount) as amount, itd.m_warehouse_id as m_warehouse_id, max(it.updated_at) as updated_at from inv_trxes it
                join inv_trx_ds itd on it.id = itd.inv_trx_id
                where is_entry = true and site_id = ".$id." and m_item_id = ".$row." and trx_type != 'RET_ITEM' and trx_type != 'TRF_STK' and inv_trx_date::text like '%".$this_month."%' and itd.condition = 1 and itd.type_material != 'TRF_STK'
                group by site_id, m_item_id, itd.m_warehouse_id) inv_in
                full outer join (select site_id, m_item_id, max(m_unit_id) as m_unit_id, itd.m_warehouse_id, coalesce((SELECT sum(amount) as amount from inv_trxes it1
                join inv_trx_ds itd1 on it1.id = itd1.inv_trx_id
                where trx_type = 'RET_ITEM' and it1.site_id=it.site_id and itd1.m_item_id=itd.m_item_id and itd1.m_warehouse_id=itd.m_warehouse_id and itd1.condition = 1 and itd1.type_material != 'TRF_STK'), 0) AS amount_ret, sum(amount) as amount, max(it.updated_at) as updated_at from inv_trxes it
                join inv_trx_ds itd on it.id = itd.inv_trx_id
                where is_entry = false and site_id = ".$id." and m_item_id = ".$row." and trx_type != 'TRF_STK' and inv_trx_date::text like '%".$this_month."%' and itd.condition = 1 and itd.type_material != 'TRF_STK'
                group by site_id, m_item_id, itd.m_warehouse_id) inv_out on inv_in.m_item_id = inv_out.m_item_id and inv_in.site_id = inv_out.site_id  and inv_in.m_warehouse_id = inv_out.m_warehouse_id
                ");
            if($datas != null){
                foreach($datas as $value){
                    $cek_stok=DB::table('stocks')
                        ->where('site_id', $value->site_id)
                        ->where('m_warehouse_id', $value->m_warehouse_id)
                        ->where('m_item_id', $value->m_item_id)
                        ->where('type', 'STK_NORMAL')
                        ->first();
                    $cek_stok_calculate=DB::table('calculate_stocks')
                                    ->where('site_id', $value->site_id)
                                    ->where('m_warehouse_id', $value->m_warehouse_id)
                                    ->where('m_item_id', $value->m_item_id)
                                    ->where('last_month', $date_before)
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
                                    'site_id' => $value->site_id,
                                    'm_item_id' => $value->m_item_id,
                                    'amount' => $value->stok,
                                    'amount_in' => $value->amount_in,
                                    'amount_out' => $value->amount_out,
                                    'm_unit_id' => $value->m_unit_id,
                                    'm_warehouse_id' => $value->m_warehouse_id,
                                    'type'  => 'STK_NORMAL',
                                    ]
                                ]; 
                                $response = $client->request('POST', '', $reqBody); 
                                $body = $response->getBody();
                                $content = $body->getContents();
                                $response_array = json_decode($content,TRUE);
                        } catch(RequestException $exception) {
                        }
                    }else{
                        $update_data=array(
                            'amount' => ($cek_stok_calculate != null ? $cek_stok_calculate->amount : 0) + $value->stok,
                            'amount_in' => ($cek_stok_calculate != null ? $cek_stok_calculate->amount_in : 0) + $value->amount_in,
                            'amount_out' => ($cek_stok_calculate != null ? $cek_stok_calculate->amount_out : 0) + $value->amount_out,
                        );
                        DB::table('stocks')->where('id', $cek_stok->id)->update($update_data);
                    }
                }
            }else{
                $query_not_in=DB::table('calculate_stocks')
                    ->where('last_month', $date_before)
                    ->where('m_item_id', $row)
                    ->where('type', 'STK_NORMAL')
                    ->get();
                foreach ($query_not_in as $key => $value) {
                    $get_save_price=DB::table('m_item_prices')->where(['m_item_id' => $value->m_item_id, 'm_unit_id' => $value->m_unit_id, 'site_id' => $value->site_id])->first();

                    $cek_this_month=DB::table('calculate_stocks')
                                ->where('site_id', $value->site_id)
                                ->where('m_warehouse_id', $value->m_warehouse_id)
                                ->where('m_item_id', $value->m_item_id)
                                ->where('last_month', $this_month)
                                ->where('type', 'STK_NORMAL')
                                ->first();
                    if ($cek_this_month == null) {
                        try
                        {
                            $headers = [
                                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                                'Accept'        => 'application/json',
                            ];
                            $client = new Client(['base_uri' => $this->base_api_url . '/inv/base/CalculateStock']);
                            $reqBody = [
                                'headers' => $headers,
                                'json' => [
                                    'site_id' => $value->site_id,
                                    'm_item_id' => $value->m_item_id,
                                    'amount' => $value->amount,
                                    'amount_in' => $value->amount_in,
                                    'amount_out' => $value->amount_out,
                                    'm_unit_id' => $value->m_unit_id,
                                    'm_warehouse_id' => $value->m_warehouse_id,
                                    'type'  => 'STK_NORMAL',
                                    'last_month'    => $this_month,
                                    'price'     => $get_save_price->price
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
                            $client = new Client(['base_uri' => $this->base_api_url . '/inv/base/CalculateStock/'.$cek_this_month->id]);
                            $reqBody = [
                                'headers' => $headers,
                                'json' => [
                                    'amount' => $value->amount,
                                    'amount_in' => $value->amount_in,
                                    'amount_out' => $value->amount_out,
                                    'price'     => $get_save_price->price
                                    ]
                                ]; 
                                $response = $client->request('PUT', '', $reqBody); 
                                $body = $response->getBody();
                                $content = $body->getContents();
                                $response_array = json_decode($content,TRUE);
                        } catch(RequestException $exception) {
                        }
                    }
                    $cek_stok=DB::table('stocks')
                                    ->where('site_id', $value->site_id)
                                    ->where('m_warehouse_id', $value->m_warehouse_id)
                                    ->where('m_item_id', $value->m_item_id)
                                    ->where('type', 'STK_NORMAL')
                                    ->first();
                    $update_data=array(
                        'amount' => $value->amount,
                        'amount_in' => $value->amount_in,
                        'amount_out' => $value->amount_out
                    );
                    DB::table('stocks')->where('id', $cek_stok->id)->update($update_data);
                }
            }
                
        }
        //hitung stok transfer
        foreach($item as $row){
            $datas = DB::select("
                select 
                    (COALESCE(inv_in.site_id, inv_out.site_id)) as site_id, 
                    (COALESCE(inv_in.m_item_id, inv_out.m_item_id)) as m_item_id,
                    (COALESCE(inv_in.m_unit_id, inv_out.m_unit_id)) as m_unit_id,
                    (COALESCE(inv_in.m_warehouse_id, inv_out.m_warehouse_id)) as m_warehouse_id, 
                    (COALESCE(inv_in.amount, 0)) as amount_in,
                    (COALESCE(inv_out.amount, 0) - COALESCE(inv_out.amount_ret, 0)) as amount_out,
                    (COALESCE(inv_out.amount_ret, 0)) as amount_ret,
                    ((COALESCE(inv_in.amount, 0) + COALESCE(inv_out.amount_ret, 0)) - (COALESCE(inv_out.amount, 0))) as stok,
                    inv_in.updated_at as last_update_in,
                    inv_out.updated_at as last_update_out
                from (select site_id, m_item_id, max(m_unit_id) as m_unit_id, sum(amount) as amount, itd.m_warehouse_id as m_warehouse_id, max(it.updated_at) as updated_at from inv_trxes it
                join inv_trx_ds itd on it.id = itd.inv_trx_id
                where is_entry = true and site_id = ".$id." and m_item_id = ".$row." and trx_type != 'RET_ITEM' and itd.created_at::text like '%".$this_month."%' and itd.condition = 1 and itd.type_material = 'TRF_STK'
                group by site_id, m_item_id, itd.m_warehouse_id) inv_in
                full outer join (select site_id, m_item_id, max(m_unit_id) as m_unit_id, itd.m_warehouse_id, coalesce((SELECT sum(amount) as amount from inv_trxes it1
                join inv_trx_ds itd1 on it1.id = itd1.inv_trx_id
                where trx_type = 'RET_ITEM' and it1.site_id=it.site_id and itd1.m_item_id=itd.m_item_id and itd1.m_warehouse_id=itd.m_warehouse_id and itd1.condition = 1 and itd1.type_material = 'TRF_STK'), 0) AS amount_ret, sum(amount) as amount, max(it.updated_at) as updated_at from inv_trxes it
                join inv_trx_ds itd on it.id = itd.inv_trx_id
                where is_entry = false and site_id = ".$id." and m_item_id = ".$row." and itd.created_at::text like '%".$this_month."%' and itd.condition = 1 and itd.type_material = 'TRF_STK'
                group by site_id, m_item_id, itd.m_warehouse_id) inv_out on inv_in.m_item_id = inv_out.m_item_id and inv_in.site_id = inv_out.site_id  and inv_in.m_warehouse_id = inv_out.m_warehouse_id
                ");
                if($datas != null){
                    foreach($datas as $value){
                        $cek_stok=DB::table('stocks')
                                ->where('site_id', $value->site_id)
                                ->where('m_warehouse_id', $value->m_warehouse_id)
                                ->where('m_item_id', $value->m_item_id)
                                ->where('type', 'TRF_STK')
                                ->first();
                        $cek_stok_calculate=DB::table('calculate_stocks')
                                        ->where('site_id', $value->site_id)
                                        ->where('m_warehouse_id', $value->m_warehouse_id)
                                        ->where('m_item_id', $value->m_item_id)
                                        ->where('last_month', $date_before)
                                        ->where('type', 'TRF_STK')
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
                                        'site_id' => $value->site_id,
                                        'm_item_id' => $value->m_item_id,
                                        'amount' => $value->stok,
                                        'amount_in' => $value->amount_in,
                                        'amount_out' => $value->amount_out,
                                        'm_unit_id' => $value->m_unit_id,
                                        'm_warehouse_id' => $value->m_warehouse_id,
                                        'type'  => 'TRF_STK',
                                        ]
                                    ]; 
                                    $response = $client->request('POST', '', $reqBody); 
                                    $body = $response->getBody();
                                    $content = $body->getContents();
                                    $response_array = json_decode($content,TRUE);
                            } catch(RequestException $exception) {
                            }
                        }else{
                            $update_data=array(
                                'amount' => ($cek_stok_calculate != null ? $cek_stok_calculate->amount : 0) + $value->stok,
                                'amount_in' => ($cek_stok_calculate != null ? $cek_stok_calculate->amount_in : 0) + $value->amount_in,
                                'amount_out' => ($cek_stok_calculate != null ? $cek_stok_calculate->amount_out : 0) + $value->amount_out,
                            );
                            DB::table('stocks')->where('id', $cek_stok->id)->update($update_data);
                        }
                    }
                }else{
                    $query_not_in=DB::table('calculate_stocks')
                        ->where('last_month', $date_before)
                        ->where('m_item_id', $row)
                        ->where('type', 'TRF_STK')
                        ->get();
                    foreach ($query_not_in as $key => $value) {
                        $get_save_price=DB::table('m_item_prices')->where(['m_item_id' => $value->m_item_id, 'm_unit_id' => $value->m_unit_id, 'site_id' => $value->site_id])->first();

                        $cek_this_month=DB::table('calculate_stocks')
                                    ->where('site_id', $value->site_id)
                                    ->where('m_warehouse_id', $value->m_warehouse_id)
                                    ->where('m_item_id', $value->m_item_id)
                                    ->where('last_month', $this_month)
                                    ->where('type', 'TRF_STK')
                                    ->first();
                        if ($cek_this_month == null) {
                            try
                            {
                                $headers = [
                                    'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                                    'Accept'        => 'application/json',
                                ];
                                $client = new Client(['base_uri' => $this->base_api_url . '/inv/base/CalculateStock']);
                                $reqBody = [
                                    'headers' => $headers,
                                    'json' => [
                                        'site_id' => $value->site_id,
                                        'm_item_id' => $value->m_item_id,
                                        'amount' => $value->amount,
                                        'amount_in' => $value->amount_in,
                                        'amount_out' => $value->amount_out,
                                        'm_unit_id' => $value->m_unit_id,
                                        'm_warehouse_id' => $value->m_warehouse_id,
                                        'type'  => 'TRF_STK',
                                        'last_month'    => $this_month,
                                        'price'     => $get_save_price->price
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
                                $client = new Client(['base_uri' => $this->base_api_url . '/inv/base/CalculateStock/'.$cek_this_month->id]);
                                $reqBody = [
                                    'headers' => $headers,
                                    'json' => [
                                        'amount' => $value->amount,
                                        'amount_in' => $value->amount_in,
                                        'amount_out' => $value->amount_out,
                                        'price'     => $get_save_price->price
                                        ]
                                    ]; 
                                    $response = $client->request('PUT', '', $reqBody); 
                                    $body = $response->getBody();
                                    $content = $body->getContents();
                                    $response_array = json_decode($content,TRUE);
                            } catch(RequestException $exception) {
                            }
                        }
                        $cek_stok=DB::table('stocks')
                                        ->where('site_id', $value->site_id)
                                        ->where('m_warehouse_id', $value->m_warehouse_id)
                                        ->where('m_item_id', $value->m_item_id)
                                        ->where('type', 'TRF_STK')
                                        ->first();
                        $update_data=array(
                            'amount' => $value->amount,
                            'amount_in' => $value->amount_in,
                            'amount_out' => $value->amount_out
                        );
                        DB::table('stocks')->where('id', $cek_stok->id)->update($update_data);
                    }
                }
        }
        
        return redirect('inventory/hitung_stok');
    }
    public function reportItemBuy(Request $request){
        $suppliers = null;
        try
        {
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . '/crm/base/MSupplier']);  
            $response = $client->request('GET', '', ['headers' => $headers]);  
            $body = $response->getBody();
            $content = $body->getContents();
            $response_array = json_decode($content,TRUE);
            $suppliers = $response_array['data'];
        } catch(RequestException $exception) {      
        }
        $data_temp=array();
        $date1=date('Y-m-d');
        $date2=date('Y-m-d');
        $supplier_selected=array();
        $all_supplier=false;
        if ($request->input('suppl_single')) {
            $supplier=$request->input('suppl_single');
            $supplier_selected=$request->input('suppl_single');
            $all_supplier=false;
            foreach ($supplier as $key => $value) {
                if ($value == 'all') {
                    $all_supplier=true;
                    $supplier=DB::table('m_suppliers')->pluck('id')->toArray();
                }
            }
            
            $user_id = request()->session()->get('user.id');
            $user = DB::table('users')->where('id', $this->user_id)->first();
            $location_id = $user->site_id;

            if ($request->input('date')) {
                $date1=$request->input('date');
                $date2=$request->input('date2');
            }
            // $akun=DB::table('tbl_akun')->where('id_akun', $id)->first();
            $startTime = strtotime( $date1 );
            $endTime = strtotime( $date2 );
            $date=date('Y-m', $startTime);
            $query_data=DB::table('inv_trxes as it')
                        ->select('it.no_surat_jalan', 'it.inv_trx_date', 'p.m_supplier_id', 'pa.m_supplier_id as m_supplier_id2', 'p.no as p_no', 'pa.no as pa_no', 'mi.name as item_name', 'mi.no as item_no', 'mu.name as unit_name', 'itd.*', 'p.notes as p_notes', 'pa.notes as pa_notes', 'p.is_without_ppn as p_without_ppn', 'pa.is_without_ppn as pa_without_ppn', 'ms.name as supplier1', 'ms2.name as supplier2')
                        ->join('inv_trx_ds as itd', 'it.id', 'itd.inv_trx_id')
                        ->join('m_items as mi', 'itd.m_item_id', 'mi.id')
                        ->join('m_units as mu', 'itd.m_unit_id', 'mu.id')
                        ->leftJoin('purchases as p', 'p.id', 'it.purchase_id')
                        ->leftJoin('purchase_assets as pa', 'pa.id', 'it.purchase_asset_id')
                        ->leftJoin('m_suppliers as ms', 'ms.id', 'p.m_supplier_id')
                        ->leftJoin('m_suppliers as ms2', 'ms2.id', 'pa.m_supplier_id')
                        ->where('inv_trx_date', '>=', $date1)
                        ->where('inv_trx_date', '<=', $date2)
                        ->whereIn('condition', [0,1])
                        ->where('trx_type', 'RECEIPT')
                        ->get();
            foreach($query_data as $key => $row){
                if($row->m_supplier_id != null){
                    $there=in_array($row->m_supplier_id, $supplier);
                    if($there == false){
                        unset($query_data[$key]);
                    }
                }else{
                    $there=in_array($row->m_supplier_id2, $supplier);
                    if($there == false){
                        unset($query_data[$key]);
                    }
                }
            }
            $data_temp=$query_data;
            
        }
        $data = array(
            'suppliers' => $suppliers,
            'date1'     => $date1,
            'date2'     => $date2,
            'data'      => $data_temp,
            'supplier_selected' => $supplier_selected,
            'all_supplier'  => $all_supplier
        );
        
        return view('pages.inv.inventory_transaction.report_item_buy', $data);
    }
    public function exportItemBuy(Request $request){
        $suppliers = null;
        try
        {
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . '/crm/base/MSupplier']);  
            $response = $client->request('GET', '', ['headers' => $headers]);  
            $body = $response->getBody();
            $content = $body->getContents();
            $response_array = json_decode($content,TRUE);
            $suppliers = $response_array['data'];
        } catch(RequestException $exception) {      
        }
        $data_temp=array();
        $date1=date('Y-m-d');
        $date2=date('Y-m-d');
        $supplier_selected=array();
        $all_supplier=false;
        if ($request->input('suppl_single')) {
            $supplier=$request->input('suppl_single');
            $supplier_selected=$request->input('suppl_single');
            $all_supplier=false;
            foreach ($supplier as $key => $value) {
                if ($value == 'all') {
                    $all_supplier=true;
                    $supplier=DB::table('m_suppliers')->pluck('id')->toArray();
                }
            }
            
            $user_id = request()->session()->get('user.id');
            $user = DB::table('users')->where('id', $this->user_id)->first();
            $location_id = $user->site_id;

            if ($request->input('date')) {
                $date1=$request->input('date');
                $date2=$request->input('date2');
            }
            // $akun=DB::table('tbl_akun')->where('id_akun', $id)->first();
            $startTime = strtotime( $date1 );
            $endTime = strtotime( $date2 );
            $date=date('Y-m', $startTime);
            $query_data=DB::table('inv_trxes as it')
                        ->select('it.no_surat_jalan', 'it.inv_trx_date', 'p.m_supplier_id', 'pa.m_supplier_id as m_supplier_id2', 'p.no as p_no', 'pa.no as pa_no', 'mi.name as item_name', 'mi.no as item_no', 'mu.name as unit_name', 'itd.*', 'p.notes as p_notes', 'pa.notes as pa_notes', 'p.is_without_ppn as p_without_ppn', 'pa.is_without_ppn as pa_without_ppn', 'ms.name as supplier1', 'ms2.name as supplier2')
                        ->join('inv_trx_ds as itd', 'it.id', 'itd.inv_trx_id')
                        ->join('m_items as mi', 'itd.m_item_id', 'mi.id')
                        ->join('m_units as mu', 'itd.m_unit_id', 'mu.id')
                        ->leftJoin('purchases as p', 'p.id', 'it.purchase_id')
                        ->leftJoin('purchase_assets as pa', 'pa.id', 'it.purchase_asset_id')
                        ->leftJoin('m_suppliers as ms', 'ms.id', 'p.m_supplier_id')
                        ->leftJoin('m_suppliers as ms2', 'ms2.id', 'pa.m_supplier_id')
                        ->where('inv_trx_date', '>=', $date1)
                        ->where('inv_trx_date', '<=', $date2)
                        ->whereIn('condition', [0,1])
                        ->where('trx_type', 'RECEIPT')
                        ->get();
            foreach($query_data as $key => $row){
                if($row->m_supplier_id != null){
                    $there=in_array($row->m_supplier_id, $supplier);
                    if($there == false){
                        unset($query_data[$key]);
                    }
                }else{
                    $there=in_array($row->m_supplier_id2, $supplier);
                    if($there == false){
                        unset($query_data[$key]);
                    }
                }
            }
            $data_temp=$query_data;
            
        }
        $data = array(
            'date1'     => $date1,
            'date2'     => $date2,
            'data'      => $data_temp,
        );
        return Excel::download(new ItemBuyExport($data), 'pembelian_barang.xlsx');
    }
    public function getStockIn(Request $request){
        
        $m_warehouse_id=$request->warehouse_id;
        $id=$request->id;
        $date=$request->date;
        $date2=$request->date2;
        $query=DB::table('inv_trx_ds as itd')
                    ->join('inv_trxes as it', 'it.id', 'itd.inv_trx_id')
                    ->where('inv_trx_date', '>=', $date)
                    ->where('inv_trx_date', '<=', $date2)
                    ->where('itd.m_warehouse_id', $m_warehouse_id)
                    ->where('m_item_id', $id)
                    ->where('is_entry', true)
                    ->select('itd.*', 'it.*')
                    ->get();
        foreach($query as $value){
            $value->purchase=DB::table('purchases')->where('id', $value->purchase_id)->select('no')->first();
            $value->purchase_asset=DB::table('purchase_assets')->where('id', $value->purchase_asset_id)->select('no')->first();
            $value->ts_warehouse=DB::table('ts_warehouses')->where('id', $value->ts_warehouse_id)->select('no', 'warehouse_from')->first();
            if($value->ts_warehouse != null){
                $warehouse_from=DB::table('m_warehouses')->where('id', $value->ts_warehouse->warehouse_from)->first();
                $value->ts_warehouse->warehouse=$warehouse_from != null ? $warehouse_from->name : '';
            }
            
        }
        $data=array(
            'data'  => $query
        );
        return $data;
    }
    public function getStockOut(Request $request){
        
        $m_warehouse_id=$request->warehouse_id;
        $id=$request->id;
        $date=$request->date;
        $date2=$request->date2;
        $query=DB::table('inv_trx_ds as itd')
                    ->join('inv_trxes as it', 'it.id', 'itd.inv_trx_id')
                    ->where('inv_trx_date', '>=', $date)
                    ->where('inv_trx_date', '<=', $date2)
                    ->where('itd.m_warehouse_id', $m_warehouse_id)
                    ->where('m_item_id', $id)
                    ->where('is_entry', false)
                    ->select('itd.*', 'it.*')
                    ->get();
        foreach($query as $value){
            $value->inv_sale=DB::table('inv_sales')->where('id', $value->inv_sale_id)->select('no')->first();
            $value->inv_request=DB::table('inv_requests')->where('id', $value->inv_request_id)->select('no')->first();
            // $value->ts_warehouse=DB::table('ts_warehouses')->where('id', $value->ts_warehouse_id)->select('no')->first();
        }
        $data=array(
            'data'  => $query
        );
        return $data;
    }
    public function listDetailPaidCust(){
        $query=DB::table('paid_customer_ds as pcs')
                ->join('paid_customers as pc', 'pc.id', 'pcs.paid_customer_id')
                ->join('customer_bills as cb', 'cb.id', 'pcs.customer_bill_id')
                ->join('customers as c', 'c.id', 'pc.customer_id')
                ->select('pc.no as paid_no', 'cb.no as bill_no', 'cb.bill_no as faktur_no', 'c.coorporate_name', 'pc.paid_date', 'pcs.amount')
                ->get();
        $data=DataTables::of($query)
                    ->make(true);   
        return $data;       
    }
    public function listDetailPaidSppl(){
        $query=DB::table('paid_supplier_ds as psd')
                ->join('paid_suppliers as ps', 'ps.id', 'psd.paid_supplier_id')
                ->join('payment_suppliers as pys', 'pys.id', 'psd.payment_supplier_id')
                ->join('m_suppliers as ms', 'ms.id', 'ps.m_supplier_id')
                ->select('ps.no as paid_no', 'pys.no as bill_no', 'pys.paid_no as invoice_no', 'ms.name', DB::raw("to_char( ps.paid_date, 'DD-MM-YYYY') as paid_date"), 'psd.amount')
                ->get();
        $data=DataTables::of($query)
                    ->make(true);   
        return $data;       
    }
    public function exportPaidSuppliers(){
        $query=DB::table('paid_suppliers')
                    ->select('paid_suppliers.*', 'm_suppliers.name')        
                    ->join('m_suppliers', 'm_suppliers.id', 'paid_suppliers.m_supplier_id')
                    ->where('site_id', $this->site_id)
                    ->get();
        foreach($query as $row){
            $dt=DB::table('paid_supplier_ds')
                    ->join('payment_suppliers', 'payment_suppliers.id', 'paid_supplier_ds.payment_supplier_id')
                    ->where('paid_supplier_id', $row->id)
                    ->pluck('payment_suppliers.paid_no')->toArray();
            $txt=implode(', ',$dt);
            $row->dt=rtrim($txt, ', ');
        }
        $data=DataTables::of($query)->make(true);

        $data = array(
            'data'      => $query,
        );
        return Excel::download(new PaidSupplierExport($data), 'pembayaran tagihan supplier.xlsx');
    }
    public function hitungStokYear(Request $request){
        $date1=$request->bulan;
        $date2=$request->bulan2;
        if($date1 < $date2){
            $year=$request->tahun;
            for($i=$date1; $i <= $date2; $i++){
                $this_month=date('Y-m', strtotime($year.'-'.$i));
                
                $date_before=date('Y-m', strtotime("- 1 months",  strtotime($this_month)));
                $id=$this->site_id;
                $datas = DB::select("
                        select 
                            (COALESCE(inv_in.site_id, inv_out.site_id)) as site_id, 
                            (COALESCE(inv_in.m_item_id, inv_out.m_item_id)) as m_item_id,
                            (COALESCE(inv_in.m_unit_id, inv_out.m_unit_id)) as m_unit_id,
                            (COALESCE(inv_in.m_warehouse_id, inv_out.m_warehouse_id)) as m_warehouse_id, 
                            (COALESCE(inv_in.amount, 0)) as amount_in,
                            (COALESCE(inv_out.amount, 0) - COALESCE(inv_out.amount_ret, 0)) as amount_out,
                            (COALESCE(inv_out.amount_ret, 0)) as amount_ret,
                            ((COALESCE(inv_in.amount, 0) + COALESCE(inv_out.amount_ret, 0)) - (COALESCE(inv_out.amount, 0))) as stok,
                            inv_in.updated_at as last_update_in,
                            inv_out.updated_at as last_update_out
                        from (select site_id, m_item_id, max(m_unit_id) as m_unit_id, sum(amount) as amount, itd.m_warehouse_id as m_warehouse_id, max(it.updated_at) as updated_at from inv_trxes it
                        join inv_trx_ds itd on it.id = itd.inv_trx_id
                        where is_entry = true and site_id = ".$id." and trx_type != 'RET_ITEM' and trx_type != 'TRF_STK' and inv_trx_date::text like '%".$this_month."%' and itd.condition = 1 and itd.type_material != 'TRF_STK'
                        group by site_id, m_item_id, itd.m_warehouse_id) inv_in
                        full outer join (select site_id, m_item_id, max(m_unit_id) as m_unit_id, itd.m_warehouse_id, coalesce((SELECT sum(amount) as amount from inv_trxes it1
                        join inv_trx_ds itd1 on it1.id = itd1.inv_trx_id
                        where trx_type = 'RET_ITEM' and it1.site_id=it.site_id and itd1.m_item_id=itd.m_item_id and itd1.m_warehouse_id=itd.m_warehouse_id and itd1.condition = 1 and itd1.type_material != 'TRF_STK'), 0) AS amount_ret, sum(amount) as amount, max(it.updated_at) as updated_at from inv_trxes it
                        join inv_trx_ds itd on it.id = itd.inv_trx_id
                        where is_entry = false and site_id = ".$id." and trx_type != 'TRF_STK' and inv_trx_date::text like '%".$this_month."%' and itd.condition = 1 and itd.type_material != 'TRF_STK'
                        group by site_id, m_item_id, itd.m_warehouse_id) inv_out on inv_in.m_item_id = inv_out.m_item_id and inv_in.site_id = inv_out.site_id  and inv_in.m_warehouse_id = inv_out.m_warehouse_id
                        ");
                        
                $temp_id=[];
                foreach ($datas as $key => $value) {
                    $query=DB::table('calculate_stocks')
                                ->where('site_id', $value->site_id)
                                ->where('m_warehouse_id', $value->m_warehouse_id)
                                ->where('m_item_id', $value->m_item_id)
                                ->where('last_month', $date_before)
                                ->where('type', 'STK_NORMAL')
                                ->first();
                    $cek_this_month=DB::table('calculate_stocks')
                                ->where('site_id', $value->site_id)
                                ->where('m_warehouse_id', $value->m_warehouse_id)
                                ->where('m_item_id', $value->m_item_id)
                                ->where('last_month', $this_month)
                                ->where('type', 'STK_NORMAL')
                                ->first();
                    if($query != null){
                        array_push($temp_id, $query->id);
                    }
                    
                    $get_save_price=DB::table('m_item_prices')->where(['m_item_id' => $value->m_item_id, 'm_unit_id' => $value->m_unit_id, 'site_id' => $value->site_id])->first();
                    if ($cek_this_month == null) {
                        try
                        {
                            $headers = [
                                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                                'Accept'        => 'application/json',
                            ];
                            $client = new Client(['base_uri' => $this->base_api_url . '/inv/base/CalculateStock']);
                            $reqBody = [
                                'headers' => $headers,
                                'json' => [
                                    'site_id' => $value->site_id,
                                    'm_item_id' => $value->m_item_id,
                                    'amount' => ($query != null ? $query->amount : 0) + $value->stok,
                                    'amount_in' => ($query != null ? $query->amount_in : 0) + $value->amount_in,
                                    'amount_out' => ($query != null ? $query->amount_out : 0) + $value->amount_out,
                                    'm_unit_id' => $value->m_unit_id,
                                    'm_warehouse_id' => $value->m_warehouse_id,
                                    'type'  => 'STK_NORMAL',
                                    'last_month'    => $this_month,
                                    'price'     => $get_save_price->price
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
                            $client = new Client(['base_uri' => $this->base_api_url . '/inv/base/CalculateStock/'.$cek_this_month->id]);
                            $reqBody = [
                                'headers' => $headers,
                                'json' => [
                                    'amount' => ($query != null ? $query->amount : 0) + $value->stok,
                                    'amount_in' => ($query != null ? $query->amount_in : 0) + $value->amount_in,
                                    'amount_out' => ($query != null ? $query->amount_out : 0) + $value->amount_out,
                                    'price'     => $get_save_price->price
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
                
                $query_not_in=DB::table('calculate_stocks')
                                ->where('last_month', $date_before)
                                ->whereNotIn('id', $temp_id)
                                ->where('type', 'STK_NORMAL')
                                ->get();
                foreach ($query_not_in as $key => $value) {
                    $get_save_price=DB::table('m_item_prices')->where(['m_item_id' => $value->m_item_id, 'm_unit_id' => $value->m_unit_id, 'site_id' => $value->site_id])->first();
        
                    $cek_this_month=DB::table('calculate_stocks')
                                ->where('site_id', $value->site_id)
                                ->where('m_warehouse_id', $value->m_warehouse_id)
                                ->where('m_item_id', $value->m_item_id)
                                ->where('last_month', $this_month)
                                ->where('type', 'STK_NORMAL')
                                ->first();
                    if ($cek_this_month == null) {
                        try
                        {
                            $headers = [
                                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                                'Accept'        => 'application/json',
                            ];
                            $client = new Client(['base_uri' => $this->base_api_url . '/inv/base/CalculateStock']);
                            $reqBody = [
                                'headers' => $headers,
                                'json' => [
                                    'site_id' => $value->site_id,
                                    'm_item_id' => $value->m_item_id,
                                    'amount' => $value->amount,
                                    'amount_in' => $value->amount_in,
                                    'amount_out' => $value->amount_out,
                                    'm_unit_id' => $value->m_unit_id,
                                    'm_warehouse_id' => $value->m_warehouse_id,
                                    'type'  => 'STK_NORMAL',
                                    'last_month'    => $this_month,
                                    'price'     => $get_save_price->price
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
                            $client = new Client(['base_uri' => $this->base_api_url . '/inv/base/CalculateStock/'.$cek_this_month->id]);
                            $reqBody = [
                                'headers' => $headers,
                                'json' => [
                                    'amount' => $value->amount,
                                    'amount_in' => $value->amount_in,
                                    'amount_out' => $value->amount_out,
                                    'price'     => $get_save_price->price
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
                
                $this->hitungStokTrf($this_month);
            }
        }
        
        return redirect('inventory/hitung_stok');
    }
    public function stockAdjustmentList()
    {
        $data = array(
            'data' => array(),
        );
        return view('pages.inv.stock_adjustment.stock_adjustment_list', $data);
    }
    public function stockAdjustmentForm() {
        $akun_option=array();
        $akun_option[''] = 'Pilih Akun / No Akun';
        $count=$c=0;
        $dataLevel0=DB::table('tbl_akun')->where('level', 0)->get();
        foreach ($dataLevel0 as $key => $value) {
            $id_akun=$no_akun=$nama_akun=0;
            $dataLevel1=DB::table('tbl_akun')->where('level', 1)->where('id_main_akun', $value->id_akun)->whereNotIn('id_akun', [152, 153, 154, 22])->get();
            foreach ($dataLevel1 as $k => $v) {
                $id_akun=$v->id_akun;
                $no_akun=$v->no_akun;
                $nama_akun=$v->nama_akun;
                $dataLevel2=DB::table('tbl_akun')->where('level', 2)->where('id_main_akun', $v->id_akun)->whereNotIn('id_akun', [152, 153, 154, 22])->get();
                foreach ($dataLevel2 as $k2 => $v2) {
                    $id_akun=$v2->id_akun;
                    $no_akun=$v2->no_akun;
                    $nama_akun=$v2->nama_akun;
                    $dataLevel3=DB::table('tbl_akun')->where('level', 3)->where('id_main_akun', $v2->id_akun)->whereNotIn('id_akun', [152, 153, 154, 22])->get();
                    foreach ($dataLevel3 as $k3 => $v3) {
                        $c++;
                        $id_akun=$v3->id_akun;
                        $no_akun=$v3->no_akun;
                        $nama_akun=$v3->nama_akun;
                        $dataLevel4=DB::table('tbl_akun')->where('level', 4)->where('id_main_akun', $v3->id_akun)->get();
                        foreach ($dataLevel4 as $k4 => $v4) {
                            $c++;
                            $id_akun=$v4->id_akun;
                            $no_akun=$v4->no_akun;
                            $nama_akun=$v4->nama_akun;
                            $akun_option[$id_akun]=$no_akun. ' | '.$nama_akun;
                        }
                        $akun_option[$id_akun]=$no_akun. ' | '.$nama_akun;
                    }
                    $akun_option[$id_akun]=$no_akun. ' | '.$nama_akun;
                    
                }
                $akun_option[$id_akun]=$no_akun. ' | '.$nama_akun;
            }
        }
        $gudang = DB::table('m_warehouses')->where('site_id', $this->site_id)->whereNull('deleted_at')->get();
        $data = array(
            'site_id' => $this->site_id,
            'm_warehouse_id' => $this->m_warehouse_id,
            'gudang'    => $gudang,
            'akun_option'   => $akun_option
        );

        return view('pages.inv.stock_adjustment.stock_adjustment_create', $data);
    }
    public function stockAdjustmentStore(Request $request) {
        $m_warehouse_id = $request->post('m_warehouse_id');
        $m_item_no = $request->post('m_item_no');
        $m_item_id = $request->post('m_item_id');
        $stok_site = $request->post('stok_site');
        $qty = $request->post('qty');
        $price = $request->post('price');
        $m_unit_id = $request->post('m_unit_id');
        $create_date = $request->post('create_date');
        $isSubmit = true;
        $stock_adjustment=null;
        $inv_trx=null;
        if ($isSubmit) {
            $period_year = Carbon::now()->year;
            $period_month = Carbon::now()->month;
            $rabcon = new RabController();
            $inv_no = $rabcon->generateTransactionNo('INV_OUT', $period_year, $period_month, $this->site_id );
            $stk_adj_no = $rabcon->generateTransactionNo('STK_ADJ', $period_year, $period_month, $this->site_id );
            try
            {
                $headers = [
                    'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                    'Accept'        => 'application/json',
                ];
                $client = new Client(['base_uri' => $this->base_api_url . '/inv/base/StockAdjustment']);
                $reqBody = [
                    'headers' => $headers,
                    'json' => [
                        'site_id' => $this->site_id,
                        'm_warehouse_id' => $m_warehouse_id,
                        'create_date' => $create_date,
                        'notes' => $request->notes,
                        'no' => $stk_adj_no
                    ]
                ]; 
                
                $response = $client->request('POST', '', $reqBody); 
                $body = $response->getBody();
                $content = $body->getContents();
                $response_array = json_decode($content,TRUE);
                $stock_adjustment = $response_array['data'];
            } catch(RequestException $exception) {
            }
            //write inv
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
                        'trx_type' => 'STK_ADJ',
                        'inv_request_id' => null,
                        'no' => $inv_no,
                        'inv_trx_date' => Carbon::now()->toDateString(),
                        'site_id' => $this->site_id,
                        'is_entry' => false,
                        'stock_adjustment_id' => $stock_adjustment['id'],
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
            $sub_total=0;
            for ($i = 0; $i < count($m_item_id); $i++) {
                $get_save_price=DB::table('m_item_prices')->where(['m_item_id' => $m_item_id[$i], 'm_unit_id' => $m_unit_id[$i], 'site_id' => $this->site_id])->first();
                try
                {
                    $headers = [
                        'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                        'Accept'        => 'application/json',
                    ];
                    $client = new Client(['base_uri' => $this->base_api_url . '/inv/base/StockAdjustmentD']);
                    $reqBody = [
                        'headers' => $headers,
                        'json' => [
                            'stock_adjustment_id' => $stock_adjustment['id'],
                            'm_item_id' => $m_item_id[$i],
                            'm_unit_id' => $m_unit_id[$i],
                            'amount' => $qty[$i],
                            'm_warehouse_id' => $m_warehouse_id,
                            'base_price' => $get_save_price != null ? $get_save_price->price : 0
                        ]
                    ]; 
                    $response = $client->request('POST', '', $reqBody); 
                } catch(RequestException $exception) {
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
                            'amount' => $qty[$i] > $stok_site[$i] ? $qty[$i] - $stok_site[$i] : $stok_site[$i] - $qty[$i],
                            'm_unit_id' => $m_unit_id[$i],
                            'm_warehouse_id' => $m_warehouse_id,
                            'condition' => 1,
                            'value' => ($get_save_price != null ? $get_save_price->price : 0) * $qty[$i],
                            'base_price'    => $get_save_price != null ? $get_save_price->price : 0
                            ]
                        ]; 
                        $response = $client->request('POST', '', $reqBody); 
                } catch(RequestException $exception) {
                }   
                //cek stok
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
                                'amount' => $qty[$i],
                                'amount_in' => $qty[$i] > $stok_site[$i] ? $qty[$i] : 0,
                                'amount_out' => $stok_site[$i] > $qty[$i] ? $qty[$i] : 0,
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
                                    'amount' => $qty[$i],
                                    'amount_in' => $qty[$i] > $stok_site[$i] ? $cek_stok->amount_in + ($qty[$i] - $stok_site[$i]) : $cek_stok->amount_in + 0,
                                    'amount_out' => $qty[$i] < $stok_site[$i] ? $cek_stok->amount_out + ($stok_site[$i] - $qty[$i]) : $cek_stok->amount_out + 0,
                                ]
                            ]; 
                            $response = $client->request('PUT', '', $reqBody); 
                            $body = $response->getBody();
                            $content = $body->getContents();
                            $response_array = json_decode($content,TRUE);
                    } catch(RequestException $exception) {
                    }
                }

                $item=DB::table('m_items')->where('id', $m_item_id[$i])->first();
                if ($qty[$i] > $stok_site[$i]) {
                    $sub_total+=(($qty[$i] - $stok_site[$i])*($get_save_price != null ? $get_save_price->price : 0));
                }
                else{
                    $sub_total+=(($stok_site[$i] - $qty[$i])*($get_save_price != null ? $get_save_price->price : 0));
                    
                }
                if ($item->category == 'MATERIAL') {
                    $temp_journal[]=array(
                        'total' => $qty[$i] > $stok_site[$i] ? (($qty[$i] - $stok_site[$i])*($get_save_price != null ? $get_save_price->price : 0)) : ($stok_site[$i] - $qty[$i])*($get_save_price != null ? $get_save_price->price : 0),
                        'm_warehouse_id' => $m_warehouse_id,
                        'type'      => 'material',
                        'm_item_id' => $m_item_id[$i]
                    );
                }else{
                    $temp_journal[]=array(
                        'total' => $qty[$i] > $stok_site[$i] ? (($qty[$i] - $stok_site[$i])*($get_save_price != null ? $get_save_price->price : 0)) : ($stok_site[$i] - $qty[$i])*($get_save_price != null ? $get_save_price->price : 0),
                        'm_warehouse_id' => $m_warehouse_id,
                        'type'      => 'spare part',
                        'm_item_id' => $m_item_id[$i]
                    );
                }
                $input_jurnal=array(
                    'inv_trx_id' => $inv_trx['id'],
                    'stock_adjustment_id' => $stock_adjustment['id'],
                    'data'  => $temp_journal,
                    'total' => $sub_total,
                    'user_id'   => $this->user_id,
                    'lawan'     => $request->akun,
                    'deskripsi'     => $request->notes != null ? 'Penyesuaian Stok dari No '.$inv_no.' dengan catatan : '.$request->notes : 'Penyesuaian Stok dari No '.$inv_no,
                    'tgl'       => date('Y-m-d'),
                    'location_id'   => $this->site_id
                );
            }
            $this->journalStockAdjustment($input_jurnal);

            $notification = array(
                'message' => 'Success Stock Adjustment',
                'alert-type' => 'success'
            );
        } else {
            $notification = array(
                'message' => 'Error, Stock cannot smaller than request',
                'alert-type' => 'error'
            );
        }
        
        return redirect('inventory/stock_adjustment')->with($notification);
    }
    private function journalStockAdjustment($data){       
        $data_trx=array(
            'deskripsi'     => $data['deskripsi'],
            'location_id'     => $data['location_id'],
            'tanggal'       => $data['tgl'],
            'user_id'       => $data['user_id'],
            'inv_trx_id' => $data['inv_trx_id'],
            'stock_adjustment_id'   => $data['stock_adjustment_id']
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
                    'm_item_id'     => $value['m_item_id'],
                );
                DB::table('tbl_trx_akuntansi_detail')->insert($akun);
            }
            $lawan=array(
                'id_trx_akun'   => $id_last,
                'id_akun'       => $data['lawan'],
                'jumlah'        => $data['total'],
                'tipe'          => "DEBIT",
                'keterangan'    => 'lawan',
            );
            DB::table('tbl_trx_akuntansi_detail')->insert($lawan);
        }
    }
    public function listStockAdjustmentJson() {
        $query=DB::table('stock_adjustments')->select('stock_adjustments.*', 'm_warehouses.name')->join('m_warehouses', 'm_warehouses.id', 'stock_adjustments.m_warehouse_id')->get();
        $data=DataTables::of($query)
                                ->make(true);             

        return $data;
    }
    public function listStockAdjustmentDetailJson($id) {
        $query=DB::table('stock_adjustment_ds')->select('stock_adjustment_ds.*', 'm_items.name as item_name', 'm_items.no as item_no', 'm_units.name as unit_name')->join('m_items', 'm_items.id', 'stock_adjustment_ds.m_item_id')->join('m_units', 'm_units.id', 'stock_adjustment_ds.m_unit_id')->where('stock_adjustment_id', $id)->get();
        $data=array(
            'data'  => $query
        );
        return $data;
    }
    public function calcPriceYear(Request $request){
        $date1=$request->bulan;
        $date2=$request->bulan2;
        if($date1 < $date2){
            $year=$request->tahun;
            for($i=$date1; $i <= $date2; $i++){
                $this_month=date('Y-m', strtotime($year.'-'.$i));
                $date_before=date('Y-m', strtotime("- 1 months",  strtotime($this_month)));
                $id=$this->site_id;
                $items=DB::table('m_items')->whereNull('deleted_at')->get();
                foreach ($items as $key => $value) {
                    $inv_detail=DB::table('inv_trxes as it')
                                    ->join('inv_trx_ds as itd', 'itd.inv_trx_id', 'it.id')
                                    ->where('it.site_id', $this->site_id)
                                    ->where('it.trx_type', 'RECEIPT')
                                    ->where('itd.m_item_id', $value->id)
                                    ->where('itd.condition', 1)
                                    ->where('itd.type_material', 'STK_NORMAL')
                                    ->where('it.inv_trx_date', 'like', '%'.$this_month.'%')
                                    ->select('itd.*')
                                    ->get();
                    $total_all=$total_material=0;
                    foreach ($inv_detail as $k => $v) {
                        $total_all+=($v->amount * $v->base_price);
                        $total_material+=$v->amount;
                    }
                    $total_pengeluaran=$total_material_pengeluaran=0;
                    $inv_detail=DB::table('inv_trxes as it')
                                    ->join('inv_trx_ds as itd', 'itd.inv_trx_id', 'it.id')
                                    ->where('it.site_id', $this->site_id)
                                    ->whereIn('it.trx_type', ['REQ_ITEM', 'REQ_ITEM_SP', 'INV_SALE', 'STK_ADJ'])
                                    ->where('itd.m_item_id', $value->id)
                                    ->where('itd.condition', 1)
                                    ->where('itd.type_material', 'STK_NORMAL')
                                    ->where('it.inv_trx_date', 'like', '%'.$this_month.'%')
                                    ->select('itd.*')
                                    ->get();
                    foreach ($inv_detail as $k => $v) {
                        $total_pengeluaran+=($v->amount * $v->base_price);
                        $total_material_pengeluaran+=$v->amount;
                    }
                    $query=DB::table('calculate_prices')
                                ->where('site_id', $this->site_id)
                                ->where('m_item_id', $value->id)
                                ->where('last_month', $date_before)
                                ->first();
                    $cek_this_month=DB::table('calculate_prices')
                                ->where('site_id', $this->site_id)
                                ->where('m_item_id', $value->id)
                                ->where('last_month', $this_month)
                                ->first();
                    $stok_change=($total_material - $total_material_pengeluaran);
                    $total_stok_change=($total_all - $total_pengeluaran);
                    $total_temp=($stok_change + ($query != null ? $query->amount : 0));
                    $price=($query != null ? ((($query->amount * $query->price) + $total_stok_change) / ($total_temp != 0 ? $total_temp : 1)) : ($total_stok_change != 0 ? ($total_stok_change / $stok_change) : 0));
                    if ($cek_this_month == null) {
                        $input_data=[
                            'site_id' => $this->site_id,
                            'm_item_id' => $value->id,
                            'amount' => $total_temp,
                            'amount_in' => $query != null ? ($query->amount_in + $total_material) : $total_material,
                            'amount_out' => $query != null ? ($query->amount_out + $total_material_pengeluaran) : $total_material_pengeluaran,
                            'm_unit_id' => $value->m_unit_id,
                            'last_month'    => $this_month,
                            'price'     => $price,
                            'created_at'    => date('Y-m-d H:i:s'),
                            'updated_at'    => date('Y-m-d H:i:s'),
                        ];

                        DB::table('calculate_prices')->insert($input_data);
                    }else{
                        $update_data=[
                            'amount' => $total_temp,
                            'amount_in' => $query != null ? ($query->amount_in + $total_material) : $total_material,
                            'amount_out' => $query != null ? ($query->amount_out + $total_material_pengeluaran) : $total_material_pengeluaran,
                            'price'     => $price,
                            'updated_at'    => date('Y-m-d H:i:s'),
                        ];
                        DB::table('calculate_prices')->where('id', $cek_this_month->id)->update($update_data);
                    }
                }
            }
        }
        return redirect('inventory/calc_price');
    }

    public function ringkasanUmurPiutang()
    {
        $customers=DB::table('customers')->get();
        $user_id = request()->session()->get('user.id');
        $user = DB::table('users')->where('id', $this->user_id)->first();
        $location_id = $user->site_id;
        $date_now=date('Y-m-d');
        $one_month_before=date('Y-m-d', strtotime("- 1 month", strtotime($date_now)));
        $two_month_before=date('Y-m-d', strtotime("- 2 month", strtotime($date_now)));
        $three_month_before=date('Y-m-d', strtotime("- 3 month", strtotime($date_now)));
        $four_month_before=date('Y-m-d', strtotime("- 4 month", strtotime($date_now)));
        $five_month_before=date('Y-m-d', strtotime("- 5 month", strtotime($date_now)));
        
        foreach ($customers as $key => $value) {
            $detail=DB::table('customer_bills')
                            ->select(DB::raw("COALESCE(SUM(CASE WHEN due_date < '".$date_now."' and due_date >= '".$one_month_before."' THEN amount ELSE 0 END), 0) as total_in_one_months"), DB::raw("COALESCE(SUM(CASE WHEN due_date < '".$one_month_before."' and due_date >= '".$two_month_before."' THEN amount ELSE 0 END), 0) as total_in_two_months"), DB::raw("COALESCE(SUM(CASE WHEN due_date < '".$two_month_before."' and due_date >= '".$three_month_before."' THEN amount ELSE 0 END), 0) as total_in_three_months"), DB::raw("COALESCE(SUM(CASE WHEN due_date < '".$three_month_before."' and due_date >= '".$four_month_before."' THEN amount ELSE 0 END), 0) as total_in_four_months"), DB::raw("COALESCE(SUM(CASE WHEN due_date < '".$four_month_before."' THEN amount ELSE 0 END), 0) as total_in_five_months"))
                            ->where('customer_id', $value->id)
                            ->where('is_paid', false)
                            ->first();
            $value->detail=$detail;
        }
        
        $data = array(
            'data'      => $customers,
            'one_month_before' => $one_month_before,
            'two_month_before' => $two_month_before,
            'three_month_before' => $three_month_before,
            'four_month_before' => $four_month_before
        );
        // return($data);
        return view('pages.inv.inventory_transaction.ringkasan_umur_piutang', $data);
    }

    public function ringkasanUmurPiutangJson(Request $request) {
        $detail=DB::table('customer_bills')
                    ->select('*')
                    ->where('customer_id', $request->id)
                    ->where('due_date', '<', $request->start_date)
                    ->where('is_paid', false);
        if ($request->month != 5) {
            $detail->where('due_date', '>=', $request->end_date);
        }
        $detail=$detail->get();
        $data=DataTables::of($detail)->make(true);
        return $data;
    }

    public function exportRingkasanUmurPiutang(Request $request) {
        $customers=DB::table('customers')->get();
        $user_id = request()->session()->get('user.id');
        $user = DB::table('users')->where('id', $this->user_id)->first();
        $location_id = $user->site_id;
        $date_now=date('Y-m-d');
        $one_month_before=date('Y-m-d', strtotime("- 1 month", strtotime($date_now)));
        $two_month_before=date('Y-m-d', strtotime("- 2 month", strtotime($date_now)));
        $three_month_before=date('Y-m-d', strtotime("- 3 month", strtotime($date_now)));
        $four_month_before=date('Y-m-d', strtotime("- 4 month", strtotime($date_now)));
        $five_month_before=date('Y-m-d', strtotime("- 5 month", strtotime($date_now)));
        
        foreach ($customers as $key => $value) {
            $detail=DB::table('customer_bills')
                            ->select(DB::raw("COALESCE(SUM(CASE WHEN due_date < '".$date_now."' and due_date >= '".$one_month_before."' THEN amount ELSE 0 END), 0) as total_in_one_months"), DB::raw("COALESCE(SUM(CASE WHEN due_date < '".$one_month_before."' and due_date >= '".$two_month_before."' THEN amount ELSE 0 END), 0) as total_in_two_months"), DB::raw("COALESCE(SUM(CASE WHEN due_date < '".$two_month_before."' and due_date >= '".$three_month_before."' THEN amount ELSE 0 END), 0) as total_in_three_months"), DB::raw("COALESCE(SUM(CASE WHEN due_date < '".$three_month_before."' and due_date >= '".$four_month_before."' THEN amount ELSE 0 END), 0) as total_in_four_months"), DB::raw("COALESCE(SUM(CASE WHEN due_date < '".$four_month_before."' THEN amount ELSE 0 END), 0) as total_in_five_months"))
                            ->where('customer_id', $value->id)
                            ->where('is_paid', false)
                            ->first();
            $value->detail=$detail;
        }
        
        $data = array(
            'data'      => $customers,
            'one_month_before' => $one_month_before,
            'two_month_before' => $two_month_before,
            'three_month_before' => $three_month_before,
            'four_month_before' => $four_month_before
        );
        // return($data);
        return Excel::download(new RingkasanUmurPiutangExport($data), 'ringkasan_umur_piutang.xlsx');
    }
}
