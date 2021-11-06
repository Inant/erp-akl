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
use Exception;
use Illuminate\Database\QueryException;

class PenawaranController extends Controller
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

    private function currency($val){
        $data=explode('.', $val);
        $new=implode('', $data);
        return $new;
    }

    public function index() {
        // return Controller::isLogin(auth()->user()['role_id']);
        return view('pages.penjualan.penawaran.penawaran_list');
    }

    public function listPenawaranJson() {
        $response = null;
        try
        {
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . '/penjualan/penawaran?site_id='.$this->site_id]);  
            $response = $client->request('GET', '', ['headers' => $headers]);  
            $body = $response->getBody();
            $content = $body->getContents();
            $response_array = json_decode($content,TRUE);

            $response = $content;  
            // return $response;          
        } catch(RequestException $exception) {
            return $exception->getmessage();
        }    
        $data=DataTables::of($response_array['data'])
                                ->make(true);             

        return $data;
    }

    public function create() {

        // $gudang = DB::table('m_warehouses')->where('site_id', $this->site_id)->whereNull('deleted_at')->get();
        
        $data = array(
            'site_id' => $this->site_id,
            // 'm_warehouse_id' => $this->m_warehouse_id,
            // 'gudang'    => $gudang
        );

        return view('pages.penjualan.penawaran.create_penawaran', $data);
    }

    public function getAlamatCustomer(Request $request)
    {
        $getAlamat = DB::table('customers')->select('address')->where('id', $request->get('id'))->first();

        return json_encode($getAlamat);
    }

    public function getAllItems()
    {
        $datas = DB::select("
                    select 
                        mi.*,
                        mu.name AS m_unit_name,
                        mip.price as item_prices
                    from m_items mi
                    join m_harga_jual on mi.id = m_harga_jual.m_item_id
                    join m_units mu ON mi.m_unit_id = mu.id
                    left join m_item_prices mip ON mi.id = mip.m_item_id
                    where mi.deleted_at is null
                    order by mi.name asc
                    ");
        foreach ($datas as $key => $value) {
            $value->best_prices=DB::table('m_best_prices')
                                ->join('m_suppliers', 'm_suppliers.id', '=', 'm_best_prices.m_supplier_id')
                                ->where('m_item_id', $value->id)
                                ->select('m_best_prices.best_price', 'm_suppliers.name')
                                ->first();
        }
        return response()->json(['data' => $datas]);
    }

    public function getItemPrice(Request $request)
    {
        $itemPrice = DB::table('m_harga_jual')->where('m_item_id', $request->item_id);
        if ($request->tipe == 'retail') {
            $itemPrice->select('retail as harga');
        }
        elseif ($request->tipe == 'grosir') {
            $itemPrice->select('grosir as harga');
        }
        elseif ($request->tipe == 'distributor') {
            $itemPrice->select('distributor as harga');
        }
        else{
            $itemPrice = 'not valid';
        }
        // return json_encode($request->tipe);
        return json_encode($itemPrice != 'not valid' ? $itemPrice->first() : $itemPrice);
    }

    public function salesOrder($idPenawaran)
    {
        $allMaterial = DB::table('m_items as i')->join('m_harga_jual as h', 'i.id', 'h.m_item_id')->select('i.id', 'i.name', 'i.no', 'h.retail', 'h.grosir', 'h.distributor')->get();
        $data = array(
            'site_id' => $this->site_id,
            'allMaterial' => $allMaterial,
            // 'm_warehouse_id' => $this->m_warehouse_id,
            // 'gudang'    => $gudang
        );

        return view('pages.penjualan.penawaran.sales_order', $data);
    }

    public function store(Request $request)
    {
        $tipe_customer = $request->post('tipe_customer');
        $customer_id = $request->post('customer_id');
        $m_item_id = $request->post('m_item_id');
        $total=$this->currency($request->total);
        $qty = $request->post('qty');
        $price = $request->post('price');
        $tanggal_penawaran = $request->post('tanggal_penawaran');
        $alamat_kirim = $request->post('alamat_kirim');
        $diskon = $request->post('diskon');
        $grandtotal = $request->post('grandtotal');
        $period_year = Carbon::now()->year;
        $period_month = Carbon::now()->month;
        $rabcon = new RabController();
        $nomorPenawaran = $rabcon->generateTransactionNo('QO', $period_year, $period_month, $this->site_id );
        $sesuai_alamat_customer = $request->post('sesuai_alamat_customer');
        $dengan_pengiriman = $request->post('dengan_pengiriman');
        $biaya_pengiriman = $request->post('biaya_pengiriman');
        $penawaran = [];
        try
        {
            // ini_set('memory_limit', '-1');
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . '/inv/base/Penawaran']);
            $reqBody = [
                'headers' => $headers,
                'json' => [
                    'no' => $nomorPenawaran,
                    'tanggal_penawaran' => $tanggal_penawaran,
                    'tipe_customer' => $tipe_customer,
                    'customer_id' => $customer_id,
                    'is_closed' => false,
                    'total' => $total,
                    'site_id' => $this->site_id,
                    'alamat_kirim' => $alamat_kirim,
                    'diskon'    => $diskon,
                    'grandtotal'    => $grandtotal,
                    'sesuai_alamat_customer' => $sesuai_alamat_customer,
                    'dengan_pengiriman' => $dengan_pengiriman,
                    'biaya_pengiriman' => $biaya_pengiriman,
                    ]
                ]; 
                $response = $client->request('POST', '', $reqBody); 
                $body = $response->getBody();
                $content = $body->getContents();
                $response_array = json_decode($content,TRUE);
                $penawaran = $response_array['data'];
                // return $response_array;
        } catch(RequestException $exception) {
            return $request;
            return $exception->getMessage();
        }
        
        // $temp_journal=array();
        // $total_penjualan=$total_hpp=$ppn=0;
        for ($i = 0; $i < count($m_item_id); $i++) {
            try
            {
                $headers = [
                    'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                    'Accept'        => 'application/json',
                ];
                $client = new Client(['base_uri' => $this->base_api_url . '/inv/base/PenawaranDetail']);
                $reqBody = [
                    'headers' => $headers,
                    'json' => [
                        'penawaran_id' => $penawaran['id'],
                        'm_item_id' => $m_item_id[$i],
                        'amount' => $qty[$i],
                        'base_price' => $price[$i]
                    ]
                ]; 
                $response = $client->request('POST', '', $reqBody); 
            } catch(RequestException $exception) {
            }
            
        }
        // $input_jurnal=array(
        //     'inv_sale_id' => $inv_sale['id'],
        //     'customer_id' => $customer_id,
        //     'total' => $total,
        //     'ppn' => $ppn,
        //     'wop' => $cara_bayar_single,
        //     'user_id'   => $this->user_id,
        //     'deskripsi'     => 'Permintaan Penjualan Material dari No '.$nomorPenawaran,
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
        
        return redirect('penjualan/penawaran')->with($notification);
    }

    public function delete($id)
    {
        DB::beginTransaction();
        try {
            //delete penawaran detail
            DB::table('penawaran_detail')->where('penawaran_id', $id)->delete();
            
            // delete penawaran
            DB::table('penawaran')->where('id', $id)->delete();

            DB::commit();
            $notification = array(
                'message' => 'Data berhasil dihapus.',
                'alert-type' => 'success'
            );
        } catch (Exception $e) {
            DB::rollBack();
            $notification = array(
                'message' => 'Gagal menghapus data.',
                'alert-type' => 'danger'
            );
        }
        catch(QueryException $e){
            DB::rollBack();
            $notification = array(
                'message' => 'Gagal menghapus data.',
                'alert-type' => 'danger'
            );
        }

        return redirect('penjualan/penawaran')->with($notification);
    }
}