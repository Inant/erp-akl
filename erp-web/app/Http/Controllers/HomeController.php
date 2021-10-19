<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use App\Imports\ExcelDataImport;
use App\Exports\MaterialExport;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Yajra\DataTables\Facades\DataTables;
class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    private $base_api_url;
    public function __construct()
    {
        $this->middleware('auth');
        $this->base_api_url = env('API_URL');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $site_id = auth()->user()['site_id']; 
        $user_id = auth()->user()['id']; 
        try
        {
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            if ($site_id == null) {
                $client1 = new Client(['base_uri' => $this->base_api_url . 'order/option']); 
            }else{
                $client1 = new Client(['base_uri' => $this->base_api_url . 'order/option?site_id='.$site_id]); 
            } 
            $response1 = $client1->request('GET', '', ['headers' => $headers]); 
            $body1 = $response1->getBody();
            $content1 = $body1->getContents();
            $response_array1 = json_decode($content1,TRUE);

            $response1 = $content1;  
            $order_list = $response_array1['data'];
        } catch(RequestException $exception) {
            
        }
        $user_list=DB::table('users')
                        ->where('id', '!=', $user_id)
                        ->where('site_id', $site_id)
                        ->select('id', 'name')
                        ->get();
        $data=array(
            'order_list'     => $order_list,
            'user_list'     => $user_list,
            'user_id'       => $user_id,
            'list_bank'     => DB::table('list_bank')->get()
        );
        
        return view('pages.info.dashboard', $data);
    }
    public function kurvaS()
    {
        $site_id = auth()->user()['site_id']; 
        try
        {
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            if ($site_id == null) {
                $client1 = new Client(['base_uri' => $this->base_api_url . 'order/option']); 
            }else{
                $client1 = new Client(['base_uri' => $this->base_api_url . 'order/option?site_id='.$site_id]); 
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
        
        return view('pages.info.program', $data);
    }
    public function importPost(Request $request) 
    {
        // validasi
        $this->validate($request, [
            'file' => 'required|mimes:csv,xls,xlsx'
        ]);
 
        // menangkap file excel
        $file = $request->file('file');
        // membuat nama file unik
        $nama_file = rand().$file->getClientOriginalName();
        // $file = $request->file('products_csv');
        // upload ke folder file_siswa di dalam folder public
        $file->move('import_excel',$nama_file);
 
        // import data
        // Excel::import(new SiswaImport, public_path('/file_siswa/'.$nama_file));
        $array = Excel::toArray(new ExcelDataImport, public_path('/import_excel/'.$nama_file));
        // $results =Excel::import(new ExcelDataImport, public_path('/import_excel/'.$nama_file));
        foreach ($array[0] as $key => $value) {
            $items=DB::table('m_items')->where('no', 'ilike', $value[0])->first();
            // $unit=DB::table('m_units')->where('code', 'ilike', $value[3])->first();
            // $unit2=null;
            // if ($value[7] != null) {
            //     $unit2=DB::table('m_units')->where('code', 'ilike', $value[7])->first();
            // }
            // if ($unit == null) {
            //     $unit=DB::table('m_units')->insert(array('name' => $value[1],'code'=>$value[1]));
            // }
            $supplier=DB::table('m_suppliers')->where('name', 'ilike', $value[5])->first();
                            // $data=array(
                            //     'no'     => $value[0],
                            //     'name'           => $value[1],
                            //     'category'        => $value[2],
                            //     'm_unit_id'        => $unit->id,
                            //     'created_at'       => date('Y-m-d H:i:s'),
                            //     'updated_at'       => date('Y-m-d H:i:s'),
                            //     'volume'       => 0,
                            //     'late_time'       => 3,
                            //     'type'       => 1,
                            //     'status'       => 'Active',
                            //     'm_unit_child'  => $unit2 == null ? null : $unit2->id,
                            //     'amount_unit_child'       => $value[6],
                            // );
                            $data=array(
                                'm_supplier_id'     => $supplier->id,
                                'm_item_id'           => $items->id,
                                'best_price'        => $value[4],
                            );
                            DB::table('m_best_prices')->insert($data);
                            print_r($data);
                            echo "<br>";
            //                 $absensi=DB::table('tbl_absensi')->where('m_employee_id', $pegawai->id)->where('tanggal', date_format($tanggal, 'Y-m-d'))->first();
            //                 if ($absensi == null) {
            //                     DB::table('tbl_absensi')->insert($data);
            //                 }else{
            //                     DB::table('tbl_absensi')->where('id_absensi', $absensi->id_absensi)->update($data);
            //                 }
            //             }
            //         }
            //     }
            // }
        }
        
        unlink(public_path('/import_excel/'.$nama_file));
        exit();
        return redirect('/absensi');
    }
    public function getOrderNo($spk)
    {
        $spk = str_replace('%20', ' ', $spk);
        $spk = str_replace('|', '/', $spk);
        $query=DB::table('orders')->where('spk_number', $spk)->whereNull('deleted_at')->get();

        $data=array(
            'data'     => $query
        );
        return $data;
    }
    public function getReqNo($id)
    {
        $query=DB::table('project_req_developments')->where('order_id', $id)->whereNull('deleted_at')->get();

        $data=array(
            'data'     => $query
        );
        return $data;
    }
    public function getKurva($id)
    {
        $get_pw=DB::table('project_req_developments as prd')
                    ->where('prd.id', $id)
                    ->select('pw.*', 'prd.work_start', 'prd.finish_date')
                    ->join('project_works as pw', 'pw.rab_id', 'prd.rab_id')
                    ->orderBy('pw.id', 'desc')
                    ->get();
        $get_dev=DB::table('dev_projects')
                        ->join('dev_project_ds', 'dev_project_ds.dev_project_id', 'dev_projects.id')
                        ->where('project_req_development_id', $id)
                        ->select(DB::raw('MIN(dev_project_ds.work_start) as work_start'), DB::raw('MAX(dev_project_ds.work_end) as work_end'))
                        ->first();
        foreach ($get_pw as $key => $value) {
            $value->pws=DB::table('project_worksubs')->where('project_work_id', $value->id)->orderBy('id', 'desc')->get();
            foreach ($value->pws as $v) {
                $dps=DB::table('dev_project_ds')->where('project_worksub_id', $v->id)->first();
                $v->dps=$dps;
                $dps_duration=$dps != null ? DB::table('dev_project_d_durations')->where('dev_project_d_id', $dps->id)->get() : '';
                $v->dps_duration=$dps_duration;
            }
        }
        
        $data=array(
            'data'        => $get_pw,
            'html_content'  => view('pages.info.detail_kurva')->with(compact('get_pw', 'get_dev'))->render()
        );
        return $data;
    }
    public function reportProduction(){
        $site_id = auth()->user()['site_id'];
        try
        {
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            if ($site_id == null) {
                $client1 = new Client(['base_uri' => $this->base_api_url . 'order/option']); 
            }else{
                $client1 = new Client(['base_uri' => $this->base_api_url . 'order/option?site_id='.$site_id]); 
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
        return view('pages.info.report_production', $data);
    }
    public function getEsimateResult($id)
    {
        $site_id = auth()->user()['site_id'];
        $get_project_dev=DB::table('project_req_developments')->where('order_id', $id)->pluck('id');

        $get_pw=DB::table('project_req_developments as prd')
                    ->select('prd.*', 'r.order_id', 'r.id as rab_id')
                    ->whereIn('prd.id', $get_project_dev)
                    ->join('rabs as r', 'prd.rab_id', 'r.id')
                    ->orderBy('prd.id')
                    ->get();
        foreach ($get_pw as $key => $value) {
            $products=DB::table('order_ds')
                        ->join('products', 'products.id', 'order_ds.product_id')
                        ->join('kavlings', 'kavlings.id', 'products.kavling_id')
                        ->where('order_ds.order_id', $value->order_id)
                        ->select('products.*', 'kavlings.name as type_kavling')
                        ->get();
            $value->product=$products;
            foreach ($value->product as $k => $v) {
                $query=DB::table('project_works as pw')
                    ->where('pw.rab_id', $value->rab_id)
                    ->select(DB::raw('SUM(pwsd.amount) AS amount'), DB::raw('MAX(pwsd.m_unit_id) AS m_unit_id'), DB::raw('MAX(pwsd.base_price) AS base_price'), 'm_item_id')
                    ->join('project_worksubs as pws', 'pw.id', 'pws.project_work_id')
                    ->join('project_worksub_ds as pwsd', 'pwsd.project_worksub_id', 'pws.id')
                    ->groupBy('m_item_id')
                    ->get();
                $v->request=$query;
                foreach ($v->request as $m) {
                    $m->m_items=DB::table('m_items')->where('id', $m->m_item_id)->first();
                    $m->m_units=DB::table('m_units')->where('id', $m->m_unit_id)->first();
                }
            }

            $query_inv=DB::table('inv_requests as ir')
                    ->where('ir.project_req_development_id', $value->id)
                    ->select(DB::raw("SUM(CASE WHEN it.trx_type != 'RET_ITEM' THEN itd.amount ELSE 0 END) - SUM(CASE WHEN it.trx_type = 'RET_ITEM' THEN itd.amount ELSE 0 END) AS amount"), DB::raw('MAX(itd.m_unit_id) AS m_unit_id'), 'm_item_id')
                    ->join('inv_trxes as it', 'ir.id', 'it.inv_request_id')
                    ->join('inv_trx_ds as itd', 'itd.inv_trx_id', 'it.id')
                    ->groupBy('m_item_id')
                    ->get();
            $value->detail_inv=$query_inv;
            foreach ($value->detail_inv as $k => $v) {
                $v->m_items=DB::table('m_items')->where('id', $v->m_item_id)->first();
                $v->m_units=DB::table('m_units')->where('id', $v->m_unit_id)->first();
                $get_save_price=DB::table('m_item_prices')->where(['m_item_id' => $v->m_item_id, 'm_unit_id' => $v->m_unit_id, 'site_id'   => $site_id])->first();
                $v->base_price=$get_save_price != null ? $get_save_price->price : '';
            }
        }
        
        $req=array();
        foreach ($get_pw as $key => $value) {
            //count perkiraan
            foreach ($value->product as $k => $v) {
                foreach ($v->request as $m) {
                    if (empty($req)) {
                        $req[0]['m_items']=$m->m_items;
                        $req[0]['m_units']=$m->m_units;
                        $req[0]['item_rab']=$m->m_item_id;
                        $req[0]['item_amount']=$m->amount;
                    }else if (!empty($req)) {
                        $isi=count($req);
                        $is_there=false;
                        $index=0;
                        foreach ($req as $i => $n) {
                            if ($n['item_rab'] == $m->m_item_id) {
                                $is_there=true;
                                $index=$i;
                            break;
                            }
                        }
                        if ($is_there == false) {
                            $req[$isi]['m_items']=$m->m_items;
                            $req[$isi]['m_units']=$m->m_units;
                            $req[$isi]['item_rab']=$m->m_item_id;
                            $req[$isi]['item_amount']=$m->amount;
                        }else{
                            $req[$index]['item_rab']=$m->m_item_id;
                            $req[$index]['item_amount']+=$m->amount;
                        }
                    }
                }
            }

            //count produksi
            foreach ($value->detail_inv as $k => $v) {
                if (empty($req)) {
                    $req[0]['item_prod']=$v->m_item_id;
                    $req[0]['prod_amount']=$v->amount;
                }else if (!empty($req)) {
                    $isi=count($req);
                    $is_there=false;
                    $index=0;
                    foreach ($req as $i => $n) {
                        if (empty($n['prod_amount'])) {
                            $req[$i]['prod_amount']=0;
                        }
                        if (empty($n['item_rab'])) {
                            $req[$i]['item_rab']=0;
                        }
                        if ($n['item_rab'] == $v->m_item_id) {
                            $is_there=true;
                            $index=$i;
                        break;
                        }
                    }
                    if ($is_there == false) {
                        $req[$isi]['m_items']=$v->m_items;
                        $req[$isi]['m_units']=$v->m_units;
                        $req[$isi]['item_rab']=$v->m_item_id;
                        $req[$isi]['prod_amount']=$v->amount;
                    }else{
                        $req[$index]['prod_amount']+=$v->amount;
                    }
                }
            }
        }
        // return $req;
        // // // print_r($req);
        // exit;
        // $total_produksi=0;
        // $akun_biaya=DB::table('tbl_akun_detail')->whereIn('id_parent', [1])->pluck('id_akun');
        // $trx_akuntan=DB::table('tbl_trx_akuntansi as tra')
        //                     ->join('tbl_trx_akuntansi_detail as trd', 'tra.id_trx_akun', 'trd.id_trx_akun')
        //                     ->join('tbl_akun as ta', 'trd.id_akun', 'ta.id_akun')
        //                     ->select(DB::raw("SUM(CASE WHEN tipe = 'DEBIT' THEN jumlah ELSE 0 END) AS total_debit"), DB::raw("SUM(CASE WHEN tipe = 'KREDIT' THEN jumlah ELSE 0 END) AS total_kredit"), 'trd.id_akun', DB::raw('MAX(sifat_debit) as sifat_debit'), DB::raw('MAX(sifat_kredit) as sifat_kredit'), DB::raw('MAX(nama_akun) as nama_akun'))
        //                     ->where('tra.project_req_development_id', $id)
        //                     ->whereNotIn('trd.id_akun', [149])
        //                     ->whereIn('trd.id_akun', $akun_biaya)
        //                     ->groupBy('trd.id_akun')
        //                     ->get();

        // $account_project=DB::table('account_projects')->where('order_id', $id)->first();
        // $total_jasa_produksi=$total_item_produksi=0;
        // foreach ($trx_akuntan as $key => $value) {
        //     $total_item_produksi+=($value->id_akun == $account_project->cost_material_id || $value->id_akun == $account_project->cost_spare_part_id ? $value->total_debit - $value->total_kredit : 0);
        //     $total_jasa_produksi+=($value->id_akun == $account_project->cost_service_id ? $value->total_debit - $value->total_kredit : 0);
        //     // $total_produksi+=($value->total_debit - $value->total_kredit);
        // }
        // return $trx_akuntan;
        // // exit;
        // $get_rab=DB::table('project_req_developments as prd')
        //                     ->select('pw.*', 'prd.total', 'p.amount_set')
        //                     ->where('prd.id', $id)
        //                     ->join('project_works as pw', 'pw.rab_id', 'prd.rab_id')
        //                     ->join('rabs as r', 'pw.rab_id', 'r.id')
        //                     ->join('order_ds as od', 'od.id', 'r.order_d_id')
        //                     ->join('products as p', 'p.id', 'od.product_id')
        //                     ->get();
        // foreach ($get_rab as $key => $value) {
        //     $query=DB::table('project_worksubs as pws')
        //             ->where('pws.project_work_id', $value->id)
        //             ->get();
        //     $value->pws=$query;
        //     foreach ($value->pws as $k => $v) {
        //         $total_item=DB::table('project_worksub_ds as pwsd')
        //         ->where('project_worksub_id', $v->id)
        //         ->select(DB::raw('(SUM(pwsd.amount) * '.($value->total * $value->amount_set).') AS amount'), DB::raw('MAX(pwsd.m_unit_id) AS m_unit_id'), DB::raw('MAX(pwsd.base_price) AS base_price'), 'm_item_id')
        //         ->groupBy('m_item_id')
        //         ->get();
        //         $v->items=$total_item;
        //     }
        // }
        // $total_rab=$total_item_rab=$total_jasa_rab=0;
        // foreach ($get_rab as $key => $value) {
        //     $total_item=$total_work=0;
        //     foreach ($value->pws as $k => $v) {
        //         $total_work+=(($v->amount * ($value->total * $value->amount_set)) * $v->base_price);
        //         foreach ($v->items as $k1 => $v1) {
        //             $total_item+=($v1->amount * $v1->base_price);
        //         }
        //     }
        //     $total_jasa_rab+=$total_work;
        //     $total_item_rab+=$total_item;
        //     $total_rab+=($total_work + $total_item);
        // }
        
        $data=array(
            'data'        => $get_pw,
            'html_content'  => view('pages.info.view_report_production')->with(compact('get_pw', 'req'))->render()
        );
        return $data;
    }
    public function getSupplierDueDate(Request $request){
        $site_id = auth()->user()['site_id']; 
        $type=$request->get('type');
        $date_now=date('Y-m-d');
        $date_3days=date('Y-m-d', strtotime($date_now. '+3 days'));
        $query=DB::table('payment_suppliers')
                    ->join('m_suppliers', 'm_suppliers.id', 'payment_suppliers.m_supplier_id')
                    ->leftJoin('purchases', 'purchases.id', 'payment_suppliers.purchase_id')
                    ->leftJoin('purchase_assets', 'purchase_assets.id', 'payment_suppliers.purchase_asset_id')
                    ->select('payment_suppliers.*', 'm_suppliers.name as supplier', 'purchases.no as purchase_no', 'purchase_assets.no as purchase_asset_no', 'purchases.purchase_date as purchase_date', 'purchase_assets.purchase_date as purchase_asset_date', 'purchases.ekspedisi', 'purchase_assets.ekspedisi as purchase_asset_ekspedisi')
                    ->where('payment_suppliers.site_id', $site_id)
                    ->where('payment_suppliers.is_paid', false)
                    ->where('payment_suppliers.payment_po', $type)
                    ->where('payment_suppliers.due_date', '>=', $date_now)
                    ->where('payment_suppliers.due_date', '<=', $date_3days)
                    ->get();
        $data=DataTables::of($query)
                    ->make(true); 

        return $data;
    }
    public function getPOOpen(Request $request){
        $site_id = auth()->user()['site_id']; 
        $date_now=date('Y-m-d');
        $date_range=date('Y-m-d', strtotime($date_now. '-2 weeks'));
        $query=DB::table('purchases')
                    ->join('m_suppliers', 'm_suppliers.id', 'purchases.m_supplier_id')
                    ->select('m_suppliers.name as supplier', 'purchases.*')
                    ->where('purchases.site_id', $site_id)
                    ->where('purchases.is_closed', false)
                    ->where('purchases.purchase_date', '>=', $date_range)
                    ->where('purchases.purchase_date', '<=', $date_now)
                    ->get();
        $data=DataTables::of($query)
                    ->make(true); 

        return $data;
    }
    public function getPOAssetOpen(Request $request){
        $site_id = auth()->user()['site_id']; 
        $date_now=date('Y-m-d');
        $date_range=date('Y-m-d', strtotime($date_now. '-2 weeks'));
        $query=DB::table('purchase_assets')
                    ->join('m_suppliers', 'm_suppliers.id', 'purchase_assets.m_supplier_id')
                    ->select('m_suppliers.name as supplier', 'purchase_assets.*')
                    ->where('purchase_assets.site_id', $site_id)
                    ->where('purchase_assets.is_closed', false)
                    ->where('purchase_assets.purchase_date', '>=', $date_range)
                    ->where('purchase_assets.purchase_date', '<=', $date_now)
                    ->get();
        $data=DataTables::of($query)
                    ->make(true); 

        return $data;
    }
    public function getMyMemoTo(){
        $site_id = auth()->user()['site_id']; 
        $user_id = auth()->user()['id']; 
        $query=DB::table('memos')
                    ->join('users', 'users.id', 'memos.to_user')
                    ->select('memos.*', 'users.name')
                    ->where('memos.user_id', $user_id)
                    ->whereNull('memos.deleted_at')
                    ->get();
        $data=array(
            'data'  => $query
        );
        return $data;
    }
    public function getMyMemo(){
        $site_id = auth()->user()['site_id']; 
        $user_id = auth()->user()['id']; 
        $query=DB::table('memos')
                    ->join('users', 'users.id', 'memos.user_id')
                    ->select('memos.*', 'users.name')
                    ->where('memos.to_user', $user_id)
                    ->whereNull('memos.deleted_at')
                    ->get();
        $data=array(
            'data'  => $query
        );
        return $data;
    }
    public function createMemo(Request $request){
        $site_id = auth()->user()['site_id']; 
        $user_id = auth()->user()['id']; 
        $title=$request->input('title');
        $notes=$request->input('notes');
        $date_end=$request->input('date_end');
        $user_to=$request->input('to');
        try
        {
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . 'inv/base/Memo']);
            $reqBody = [
                'headers' => $headers,
                'json' => [
                    'user_id' => $user_id,
                    'title' => $title,
                    'notes' => $notes,
                    'date_end' => $date_end,
                    'status' => 0,
                    'to_user' => $user_to,
                ]
            ];
            // return $reqBody;
            // exit;
            $response = $client->request('POST', '', $reqBody); 
            $body = $response->getBody();
            $content = $body->getContents();
            $response_array = json_decode($content,TRUE);
            
        } catch(RequestException $exception) {
            
        }
        return $response_array;
    }
    public function deleteMemo($id){
        try
        {
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . 'inv/base/Memo/'.$id]);
            $response = $client->request('DELETE', ''); 
            $body = $response->getBody();
            $content = $body->getContents();
            $response_array = json_decode($content,TRUE);
            
        } catch(RequestException $exception) {
            
        }
        $data=array(
            'message' => 'success'
        );
        return $data;
    }
    public function detailMemo($id){
        $query=DB::table('memos')
                    ->join('users as u', 'u.id', 'memos.user_id')
                    ->join('users as u2', 'u2.id', 'memos.to_user')
                    ->select('memos.*', 'u.name', 'u2.name as to_user')
                    ->where('memos.id', $id)
                    ->first();
        $data=array(
            'data' => $query
        );
        return $data;
    }
    public function editMemo(Request $request){
        $id=$request->input('id');
        $status=$request->input('edit_status');
        try
        {
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . 'inv/base/Memo/'.$id]);
            $reqBody = [
                'headers' => $headers,
                'json' => [
                    'status' => $status,
                ]
            ];
            $response = $client->request('PUT', '', $reqBody); 
            $body = $response->getBody();
            $content = $body->getContents();
            $response_array = json_decode($content,TRUE);
            
        } catch(RequestException $exception) {
            
        }
        return $response_array;
    }
    public function getProjectWalk(Request $request){
        $site_id = auth()->user()['site_id']; 
        $query=DB::table('orders')
                    ->join('customers as c', 'orders.customer_id', 'c.id')
                    ->join('project_req_developments as prd', 'orders.id', 'prd.order_id')
                    ->join('projects as p', 'p.order_id', 'orders.id')
                    ->join('rabs as r', 'r.id', 'prd.rab_id')
                    ->join('kavlings as k', 'k.id', 'r.kavling_id')
                    ->join('dev_projects', 'dev_projects.project_req_development_id', 'prd.id')
                    ->join('inv_requests', 'inv_requests.id', 'dev_projects.inv_request_id')
                    ->join('inv_request_ds', 'inv_requests.id', 'inv_request_ds.inv_request_id')
                    ->join('m_warehouses', 'm_warehouses.id', 'inv_request_ds.m_warehouse_id')
                    ->select('prd.id', DB::raw('MAX(p.name) as project_name'), DB::raw('MAX(c.coorporate_name) as customer_name'), DB::raw('MAX(m_warehouses.name) as warehouse_name'), DB::raw('MAX(k.name) as nama_kavling'), DB::raw('MAX(prd.total) as total_kavling'))
                    ->where('dev_projects.is_done', false)
                    ->where('orders.site_id', $site_id)
                    ->groupBy('prd.id')
                    ->get();
        $data=DataTables::of($query)
                    ->make(true); 

        return $data;
    }
    public function compareMaterial()
    {
        $site_id = auth()->user()['site_id']; 
        
        return view('pages.info.import_compare_m');
    }
    public function importCompare(Request $request) 
    {
        // validasi
        $this->validate($request, [
            'file' => 'required|mimes:csv,xls,xlsx'
        ]);
 
        // menangkap file excel
        $file = $request->file('file');
        // membuat nama file unik
        $nama_file = rand().$file->getClientOriginalName();
        // $file = $request->file('products_csv');
        // upload ke folder file_siswa di dalam folder public
        $file->move('import_excel',$nama_file);
 
        // import data
        // Excel::import(new SiswaImport, public_path('/file_siswa/'.$nama_file));
        $array = Excel::toArray(new ExcelDataImport, public_path('/import_excel/'.$nama_file));
        // $results =Excel::import(new ExcelDataImport, public_path('/import_excel/'.$nama_file));
        $temp=array();
        foreach ($array[0] as $key => $value) {
            $items=DB::table('m_items')->where('no', 'ilike', $value[0])->first();
            $data=array(
                'm_item_id'           => $value[0],
                'price'        => $items != null ? $value[1] : '-',
            );
            $temp[$key]=(object)$data;
        }
        return Excel::download(new MaterialExport($temp), 'material.xlsx');
        unlink(public_path('/import_excel/'.$nama_file));
    }
    public function getBillOpen(){
        $site_id = auth()->user()['site_id'];
        $query=DB::table('customer_bills as cb')
                    ->join('orders', 'orders.id', 'cb.order_id')
                    ->join('customers', 'orders.customer_id', 'customers.id')
                    ->select('orders.order_no', 'orders.id as order_id', 'cb.*', 'customers.coorporate_name', DB::raw("COALESCE((SELECT SUM(amount) from customer_bill_others where order_id=orders.id and description='addendum'), 0) - COALESCE((SELECT SUM(amount) from customer_bill_others where order_id=orders.id and description='discount_payment'), 0) AS total_adendum"))
                    ->where('cb.is_paid', false)
                    ->get();
        $data=DataTables::of($query)
                    ->addColumn('action', function($row){
                        return '<button hidden onclick="doShowDetail(this);" data-toggle="modal" data-no="'.$row->no.'" data-id="'.$row->id.'" data-amount="'.$row->amount.'" data-order_id="'.$row->order_id.'" data-end_payment="'.$row->end_payment.'" data-total_adendum="'.$row->total_adendum.'" data-target="#modalBillDetail" class="btn waves-effect waves-light btn-xs btn-info"><i class="mdi mdi-credit-card-plus"></i></button>';
                    })
                    ->make(true); 

        return $data;
    }
    public function getBillInstallOpen(){
        $site_id = auth()->user()['site_id'];
        $query=DB::table('customer_bills as cb')
                    ->join('install_orders', 'install_orders.id', 'cb.install_order_id')
                    ->join('customers', 'install_orders.customer_id', 'customers.id')
                    ->select('install_orders.no as order_no', 'cb.*', 'customers.coorporate_name')
                    ->where('cb.is_paid', false)
                    ->get();
        $data=DataTables::of($query)
                    ->addColumn('action', function($row){
                        return '<button hidden onclick="doShowDetail2(this);" data-toggle="modal" data-no="'.$row->no.'" data-id="'.$row->id.'" data-amount="'.$row->amount.'" data-install_order_id="'.$row->install_order_id.'" data-end_payment="'.$row->end_payment.'" data-target="#modalBillDetail2" class="btn waves-effect waves-light btn-xs btn-info"><i class="mdi mdi-credit-card-plus"></i></button>';
                    })
                    ->make(true); 

        return $data;
    }
}
