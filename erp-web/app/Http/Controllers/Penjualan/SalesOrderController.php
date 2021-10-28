<?php

namespace App\Http\Controllers\Penjualan;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use GuzzleHttp\Client;
use Yajra\DataTables\Facades\DataTables;
use GuzzleHttp\Exception\RequestException;
use App\Http\Controllers\RAB\RabController;
use App\Http\Controllers\Accounting\AkuntanController;
use Carbon\Carbon;
use DB;

class SalesOrderController extends Controller
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

    private function currency($val)
    {
        $data = explode('.', $val);
        $new = implode('', $data);
        return $new;
    }

    public function index()
    {
        // return Controller::isLogin(auth()->user()['role_id']);
        return view('pages.penjualan.sales-order.sales-order-list');
    }

    public function listSalesOrderJson()
    {
        $response = null;
        try {
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . '/penjualan/sales-order?site_id=' . $this->site_id]);
            $response = $client->request('GET', '', ['headers' => $headers]);
            $body = $response->getBody();
            $content = $body->getContents();
            $response_array = json_decode($content, TRUE);

            $response = $content;
            // return $response;          
        } catch (RequestException $exception) {
            return $exception->getmessage();
        }
        $data = DataTables::of($response_array['data'])
            ->make(true);

        return $data;
    }

    public function create($idPenawaran)
    {

        // $gudang = DB::table('m_warehouses')->where('site_id', $this->site_id)->whereNull('deleted_at')->get();
        $penawaran = DB::table('penawaran')->where('id', $idPenawaran)->first();
        $penawaranDetail = DB::table('penawaran_detail as pd')->join('penawaran as p', 'p.id', 'pd.penawaran_id')->join('m_items as i', 'i.id', 'pd.m_item_id')->join('m_units as u', 'u.id', 'i.m_unit_id')->select('pd.*', 'i.no as itemNo', 'i.name as itemName', 'u.name as unitName')->where('pd.penawaran_id', $idPenawaran)->get();
        $data = array(
            'site_id' => $this->site_id,
            'penawaran' => $penawaran,
            'penawaranDetail' => $penawaranDetail
            // 'm_warehouse_id' => $this->m_warehouse_id,
            // 'gudang'    => $gudang
        );

        return view('pages.penjualan.sales-order.create-sales-order', $data);
    }

    public function store(Request $request)
    {
        /*
        1. save to sales_order table
        2. save detail to sales_order_detail
        3. update penawaran status (in_sales_order) to true
        4. save jurnal
        */
        $penawaran_id = $request->post('penawaran_id');
        $tipe_customer = $request->post('tipe_customer');
        $customer_id = $request->post('customer_id');
        $m_item_id = $request->post('m_item_id');
        $total = $this->currency($request->total);
        $qty = $request->post('qty');
        $price = $request->post('price');
        $tanggal = $request->post('tanggal');
        $payment_method = $request->post('payment_method');
        $alamat_kirim = $request->post('alamat_kirim');
        $diskon = $request->post('diskon');
        $grandtotal = $request->post('grandtotal');
        $period_year = Carbon::now()->year;
        $period_month = Carbon::now()->month;
        $rabcon = new RabController();
        $nomorSo = $rabcon->generateTransactionNo('SO', $period_year, $period_month, $this->site_id);
        $sesuai_alamat_customer = $request->post('sesuai_alamat_customer');
        $penawaran = [];
        try {
            // ini_set('memory_limit', '-1');
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . '/inv/base/SalesOrder']);
            $reqBody = [
                'headers' => $headers,
                'json' => [
                    'penawaran_id' => $penawaran_id,
                    'payment_method' => $payment_method,
                    'total' => $total,
                    'no' => $nomorSo,
                    'tanggal' => $tanggal,
                    'tipe_customer' => $tipe_customer,
                    'customer_id' => $customer_id,
                    'sesuai_alamat_customer' => $sesuai_alamat_customer,
                    'alamat_kirim' => $alamat_kirim,
                ]
            ];
            $response = $client->request('POST', '', $reqBody);
            $body = $response->getBody();
            $content = $body->getContents();
            $response_array = json_decode($content, TRUE);
            $salesOrder = $response_array['data'];
            // return $response_array;
        } catch (RequestException $exception) {
            // return $request;
            return $exception->getMessage();
        }

        // $temp_journal=array();
        // $total_penjualan=$total_hpp=$ppn=0;
        for ($i = 0; $i < count($m_item_id); $i++) {
            try {
                $headers = [
                    'Authorization' => 'Bearer ' . auth()->user()['remember_token'],
                    'Accept'        => 'application/json',
                ];
                $client = new Client(['base_uri' => $this->base_api_url . '/inv/base/SalesOrderDetail']);
                $reqBody = [
                    'headers' => $headers,
                    'json' => [
                        'sales_order_id' => $salesOrder['id'],
                        'm_item_id' => $m_item_id[$i],
                        'amount' => $qty[$i],
                        'base_price' => $price[$i]
                    ]
                ];
                $response = $client->request('POST', '', $reqBody);
            } catch (RequestException $exception) {
                return $exception->getMessage();
            }
        }

        DB::table('penawaran')->where('id', $penawaran_id)->update(['in_sales_order' => true]);
        // $input_jurnal=array(
        //     'inv_sale_id' => $inv_sale['id'],
        //     'customer_id' => $customer_id,
        //     'total' => $total,
        //     'ppn' => $ppn,
        //     'wop' => $cara_bayar_single,
        //     'user_id'   => $this->user_id,
        //     'deskripsi'     => 'Permintaan Penjualan Material dari No '.$nomorSo,
        //     'tgl'       => $tanggal_penawaran,
        //     'location_id'   => $this->site_id
        // );
        // $this->journalPermintaanPenjualan($input_jurnal);
        $notification = array(
            'message' => 'Penawaran berhasil disimpan',
            'alert-type' => 'success'
        );
        // if ($isSubmit) {
        // } else {
        //     $notification = array(
        //         'message' => 'Error, Stock cannot smaller than request',
        //         'alert-type' => 'error'
        //     );
        // }

        return redirect('penjualan/sales-order')->with($notification);
    }

    public function getItems($idPenawaran)
    {
        $datas = DB::select("
                    select 
                        mi.*,
                        mu.name AS m_unit_name,
                        mip.price as item_prices
                    from m_items mi
                    join m_harga_jual on mi.id = m_harga_jual.m_item_id
                    join m_units mu ON mi.m_unit_id = mu.id
                    join penawaran_detail dp ON mi.id = dp.m_item_id
                    join penawaran p ON p.id = dp.penawaran_id
                    left join m_item_prices mip ON mi.id = mip.m_item_id
                    where mi.deleted_at is null and p.id = $idPenawaran
                    order by mi.name asc
                    ");
        foreach ($datas as $key => $value) {
            $value->best_prices = DB::table('m_best_prices')
                ->join('m_suppliers', 'm_suppliers.id', '=', 'm_best_prices.m_supplier_id')
                ->where('m_item_id', $value->id)
                ->select('m_best_prices.best_price', 'm_suppliers.name')
                ->first();
        }
        return response()->json(['data' => $datas]);
    }

    public function detail($id)
    {
        $detail = DB::select("select mi.no as itemNo, mi.name as itemName, mu.name AS unitName, sd.*
        from m_items mi
        join m_units mu ON mi.m_unit_id = mu.id
        join sales_order_detail sd ON mi.id = sd.m_item_id
        join sales_orders s ON s.id = sd.sales_order_id
        where mi.deleted_at is null and s.id = $id
        order by mi.name asc
        ");

        return response()->json(['data' => $detail]);
    }

    public function pembayaranSalesOrder()
    {
        return view('pages.penjualan.sales-order.pembayaran-sales-order');
    }

    public function formPembayaranSalesOrder($id)
    {
        $salesOrder = DB::table('sales_orders')->where('id', $id)->first();
        $salesOrderDetail = DB::table('sales_order_detail as sod')->join('sales_orders as s', 's.id', 'sod.sales_order_id')->join('m_items as i', 'i.id', 'sod.m_item_id')->join('m_units as u', 'u.id', 'i.m_unit_id')->select('sod.*', 'i.no as itemNo', 'i.name as itemName', 'u.name as unitName')->where('sod.sales_order_id', $id)->get();
        $data = array(
            'site_id' => $this->site_id,
            'salesOrder' => $salesOrder,
            'salesOrderDetail' => $salesOrderDetail
            // 'm_warehouse_id' => $this->m_warehouse_id,
            // 'gudang'    => $gudang
        );

        return view('pages.penjualan.sales-order.form-pembayaran-sales-order', $data);
    }

    public function getSalesOrderItems($id)
    {
        $datas = DB::select("
                    select 
                        mi.*,
                        mu.name AS m_unit_name,
                        dp.*
                    from m_items mi
                    join m_units mu ON mi.m_unit_id = mu.id
                    join sales_order_detail dp ON mi.id = dp.m_item_id
                    join sales_orders p ON p.id = dp.sales_order_id
                    where mi.deleted_at is null and p.id = $id
                    order by mi.name asc
                    ");
        return response()->json(['data' => $datas]);
    }

    public function storePembayaran(Request $request)
    {
        /*
        1. update status is_paid
        2. update nominal terbayar
        3. 
        4. save jurnal
        */
        $sales_order_id = $request->post('sales_order_id');
        $tipe_customer = $request->post('tipe_customer');
        $customer_id = $request->post('customer_id');
        $m_item_id = $request->post('m_item_id');
        $total = $this->currency($request->total);
        $qty = $request->post('qty');
        $price = $request->post('price');
        $tanggal = $request->post('tanggal');
        $payment_method = $request->post('payment_method');
        $alamat_kirim = $request->post('alamat_kirim');
        $diskon = $request->post('diskon');
        $grandtotal = $request->post('grandtotal');
        $period_year = Carbon::now()->year;
        $period_month = Carbon::now()->month;
        $rabcon = new RabController();
        $nomorSo = $rabcon->generateTransactionNo('SO', $period_year, $period_month, $this->site_id);
        $sesuai_alamat_customer = $request->post('sesuai_alamat_customer');
        $penawaran = [];
        try {
            // ini_set('memory_limit', '-1');
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . '/inv/base/SalesOrder']);
            $reqBody = [
                'headers' => $headers,
                'json' => [
                    'penawaran_id' => $penawaran_id,
                    'payment_method' => $payment_method,
                    'total' => $total,
                    'no' => $nomorSo,
                    'tanggal' => $tanggal,
                    'tipe_customer' => $tipe_customer,
                    'customer_id' => $customer_id,
                    'sesuai_alamat_customer' => $sesuai_alamat_customer,
                    'alamat_kirim' => $alamat_kirim,
                ]
            ];
            $response = $client->request('POST', '', $reqBody);
            $body = $response->getBody();
            $content = $body->getContents();
            $response_array = json_decode($content, TRUE);
            $salesOrder = $response_array['data'];
            // return $response_array;
        } catch (RequestException $exception) {
            // return $request;
            return $exception->getMessage();
        }

        // $temp_journal=array();
        // $total_penjualan=$total_hpp=$ppn=0;
        for ($i = 0; $i < count($m_item_id); $i++) {
            try {
                $headers = [
                    'Authorization' => 'Bearer ' . auth()->user()['remember_token'],
                    'Accept'        => 'application/json',
                ];
                $client = new Client(['base_uri' => $this->base_api_url . '/inv/base/SalesOrderDetail']);
                $reqBody = [
                    'headers' => $headers,
                    'json' => [
                        'sales_order_id' => $salesOrder['id'],
                        'm_item_id' => $m_item_id[$i],
                        'amount' => $qty[$i],
                        'base_price' => $price[$i]
                    ]
                ];
                $response = $client->request('POST', '', $reqBody);
            } catch (RequestException $exception) {
                return $exception->getMessage();
            }
        }

        DB::table('penawaran')->where('id', $penawaran_id)->update(['in_sales_order' => true]);
        // $input_jurnal=array(
        //     'inv_sale_id' => $inv_sale['id'],
        //     'customer_id' => $customer_id,
        //     'total' => $total,
        //     'ppn' => $ppn,
        //     'wop' => $cara_bayar_single,
        //     'user_id'   => $this->user_id,
        //     'deskripsi'     => 'Permintaan Penjualan Material dari No '.$nomorSo,
        //     'tgl'       => $tanggal_penawaran,
        //     'location_id'   => $this->site_id
        // );
        // $this->journalPermintaanPenjualan($input_jurnal);
        $notification = array(
            'message' => 'Penawaran berhasil disimpan',
            'alert-type' => 'success'
        );
        // if ($isSubmit) {
        // } else {
        //     $notification = array(
        //         'message' => 'Error, Stock cannot smaller than request',
        //         'alert-type' => 'error'
        //     );
        // }

        return redirect('penjualan/sales-order')->with($notification);
    }
}
