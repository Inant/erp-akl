<?php

namespace App\Http\Controllers\INV;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use App\Http\Controllers\RAB\RabController;
use App\Http\Controllers\Accounting\AkuntanController;
use Carbon\Carbon;
use DB;
use Illuminate\Support\Facades\View;
use App\Imports\ExcelDataImport;
use Maatwebsite\Excel\Facades\Excel; 
use App\Exports\OrderExport;
use App\Exports\InstallOrderExport;
use App\Exports\BillOrderExport;
use App\Exports\BillInstallOrderExport;
use PhpOffice\PhpSpreadsheet\Shared\Date;
class OrderController extends Controller
{
    private $base_api_url;
    private $site_id = null;
    private $username = null;
    public function __construct()
    {
        //Authenticate page menu
        $this->middleware(function ($request, $next) {
            Controller::isLogin(auth()->user()['role_id']);
            $this->site_id = auth()->user()['site_id']; 
            $this->username = auth()->user()['email'];
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
        
        return view('pages.inv.order.order_list', $data);
    }
    public function create()
    {
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

        $product_equivalent = DB::table('m_products')
                            ->whereNull('deleted_at')
                            ->get();
        
        $data = array(
            'customer' => $customer,
            'product_equivalent' => $product_equivalent
        );
        return view('pages.inv.order.order_create', $data);
    }
    public function edit($id)
    {
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
            $product_equivalent = DB::table('m_products')
                            ->whereNull('deleted_at')
                            ->get();            
        } catch(RequestException $exception) {      
        }

        try
        {
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . 'inv/base/Order/' . $id]);  
            $response = $client->request('GET', '', ['headers' => $headers]); 
            $body = $response->getBody();
            $content = $body->getContents();
            $response_array = json_decode($content,TRUE);
            
            $order = $response_array['data'];
        } catch(RequestException $exception) {    
        }

        try
        {
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . 'order/order_d/' . $id]);  
            $response = $client->request('GET', '', ['headers' => $headers]); 
            $body = $response->getBody();
            $content = $body->getContents();
            $response_array = json_decode($content,TRUE);
            
            $order_d = $response_array['data'];
        } catch(RequestException $exception) {    
        }
        
        $product_equivalent = DB::table('m_products')
                            ->whereNull('deleted_at')
                            ->get();
        $data = array(
            'customer' => $customer,
            'order' => $order,
            'order_d' => $order_d,
            'product_equivalent' => $product_equivalent
        );
        
        return view('pages.inv.order.order_edit', $data);
    }
    public function suggestProduct(Request $request){
        if($request->has('q')){
            $key=$request->q;
            $data=DB::table('products')->join('kavlings', 'kavlings.id', 'products.kavling_id')->select('products.*', 'kavlings.name as type_kavling', 'kavlings.amount')->where('name', 'like', '%'.$key.'%')->get();
            return $data;
        }
    }
    function fetch(Request $request)
    {
        $search = $request->search;
        $customer_id=$request->customer_id;
        if($search == ''){
            $data=DB::table('products')
                        ->select('products.*', 'kavlings.name as type_kavling')
                        ->join('kavlings', 'kavlings.id', 'products.kavling_id')
                        ->where('products.customer_id', $customer_id)
                        ->get();
        }else{
            $data=DB::table('products')
                        ->join('kavlings', 'kavlings.id', 'products.kavling_id')
                        ->select('products.*', 'kavlings.name as type_kavling')
                        ->where('products.customer_id', $customer_id)
                        ->whereRaw("products.id IN (select id from products where name ilike '%$search%' or item ilike '%$search%')")
                        ->whereNull('products.deleted_at')->limit(15)->get();
        }
  
        $response = array();
        foreach($data as $data){
           $response[] = array("value"=>$data->id,"label"=>('Item : '.$data->item.', Type Kavling : '.$data->type_kavling.', Series : '.$data->series));
        }
        return response()->json($response);
    }
    public function getProduct($id){
        $data=DB::table('products')->select('products.*', 'kavlings.name as type_kavling', 'kavlings.amount')->leftJoin('kavlings', 'kavlings.id', 'products.kavling_id')->where('products.id', $id)->first();
        return response()->json($data);
    }
    public function save(Request $request){
        $period_year = date('Y');
        $period_month = date('m');
        $editedProd=$request->input('editProd');
        $id_product=$request->input('id_product');
        $nameProd=$request->input('name');
        $deskripsi=$request->input('deskripsi');
        $item=$request->input('item');
        $series=$request->input('series');
        $panjang=$request->input('panjang');
        $lebar=$request->input('lebar');
        $total_set=$request->input('set');
        $fee_install=$request->input('fee_install');
        $file=$request->file('file');
        
        if($editedProd != null){
            for ($i=0; $i < count($editedProd); $i++) { 
                $index=array_search($editedProd[$i], $id_product);
                $detail=DB::table('products')->where('id', $id_product[$index])->first();
                $image=$detail->image;
                if($file[$index]){
                    $tujuan_upload = 'upload/product';
                        // upload file
                    $name=time().'.'.$file[$index]->getClientOriginalExtension();
                    $file[$index]->move($tujuan_upload, $name);
                    
                    $image=$name;
                }
                $data=array(
                    'name'          => $nameProd[$index],
                    'description'   => $deskripsi[$index],
                    'item'   => $item[$index],
                    'series'   => $series[$index],
                    'panjang'   => $panjang[$index],
                    'lebar'   => $lebar[$index],
                    'amount_set'   => $total_set[$index],
                    'installation_fee'   => $fee_install[$index],
                    'image'         => $image,
                    'm_unit_id'      => $detail->m_unit_id,
                    'price'      => $detail->price,
                );
                DB::table('products')->insert($data);
                $last_id=DB::table('products')->max('id');
                $id_product[$index]=$last_id;
            }
        }
        
        $rabcon = new RabController();
        $ord_no = $rabcon->generateTransactionNo('ORD', $period_year, $period_month, $this->site_id );
        try
        {
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . 'inv/base/Order']);
            $reqBody = [
                'headers' => $headers,
                'json' => [
                    'customer_id' => $request->input('customer_id'),
                    'customer_project_id' => $request->input('customer_project_id'),
                    'order_name' => $request->input('order_name'),
                    'order_date' => date('Y-m-d'),
                    'is_done' => 0,
                    'spk_number' => $request->no_spk,
                    'site_id' => $this->site_id,
                    'order_no'  => $ord_no
                ]
            ]; 
            
            $response = $client->request('POST', '', $reqBody); 
        } catch(RequestException $exception) {
        }
        
        $order=DB::table('orders')->where('order_no', $ord_no)->first();
        $customer=DB::table('customers')->where('id', $request->input('customer_id'))->first();
        try
        {
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . 'inv/base/Project']);
            $reqBody = [
                'headers' => $headers,
                'json' => [
                    'site_id'       => $this->site_id,
                    'name'          => 'Project '.$customer->coorporate_name.' No. Order '.$ord_no,
                    'base_price'    => 0,
                    'customer_id'   => $request->input('customer_id'),
                    'sale_status'   => 'Available',
                    'order_id'      => $order->id,
                ]
            ]; 
            $response = $client->request('POST', '', $reqBody); 
        } catch(RequestException $exception) {
        }
        if($response){
            $total_produk=$request->input('total_produk');
            for ($i=0; $i < count($id_product); $i++) { 
                // $cek_project=DB::table('projects')->where('product_id', $id_product[$i])->first();
                // if($cek_project == null){
                //     // create project from product
                //     $product=DB::table('products')->where('id', $id_product[$i])->first();
                //     try
                //     {
                // $client = new Client(['base_uri' => $this->base_api_url . 'inv/base/Project']);
                //         $reqBody = [
                //             'json' => [
                //                 'site_id'       => $this->site_id,
                //                 'name'          => 'Project '.$product->item.' '.$product->name.' '.$product->series,
                //                 'base_price'    => 0,
                //                 'customer_id'   => $request->input('customer_id'),
                //                 'sale_status'   => 'Available',
                //                 'product_id'      => $id_product[$i],
                //                 // 'order_id'      => $order->id,
                //             ]
                //         ]; 
                //         $response = $client->request('POST', '', $reqBody); 
                //     } catch(RequestException $exception) {
                //     }
                // }
                //create order detail
                $detail=DB::table('products')->where('id', $id_product[$i])->first();
                try
                {
                    $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . 'inv/base/OrderD']);
                    $reqBody = [
                        'headers' => $headers,
                'json' => [
                            'order_id' => $order->id,
                            'product_id' => $id_product[$i],
                            'in_rab' => 0,
                            'total'     => $total_produk[$i],
                            'price'      => $detail->price,
                        ]
                    ]; 
                    $response_pd = $client->request('POST', '', $reqBody); 
                } catch(RequestException $exception) {
                }
                $order_d=DB::table('order_ds')->max('id');
                $spk=explode('-ORD-', $ord_no);
                for ($j=1; $j <= $total_produk[$i]; $j++) { 
                    $a=1;
                    for ($m=0; $m < $total_set[$i]; $m++) { 
                        $ord_no_detail=$detail->item.'-'.$request->no_spk.'-'.$id_product[$i].'-'.$j.'-'.$a;
                        try
                        {
                            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . 'inv/base/ProductSub']);
                            $reqBody = [
                                'headers' => $headers,
                'json' => [
                                    'product_id' => $id_product[$i],
                                    'order_d_id' => $order_d,
                                    'no' => $ord_no_detail,
                                ]
                            ]; 
                            $response_pd = $client->request('POST', '', $reqBody); 
                        } catch(RequestException $exception) {
                        }
                        $a++;
                    }
                    
                    // $product_sub=DB::table('product_subs')->where('no', $ord_no_detail)->first();
                }
            }
            $notification = array(
                'message' => 'Success add Order',
                'alert-type' => 'success'
            );
        }else{
            $notification = array(
                'message' => 'Failed add Order',
                'alert-type' => 'error'
            );
        }
        // $account_project=$this->createAccount('Order '.$ord_no);
        // try
        // {
        //     $headers = [
        //         'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
        //         'Accept'        => 'application/json',
        //     ];
        //     $client = new Client(['base_uri' => $this->base_api_url . 'inv/base/AccountProject']);
        //     $reqBody = [
        //         'headers' => $headers,
        //         'json' => [
        //             'customer_id' => $request->input('customer_id'),
        //             'order_id' => $order->id,
        //             'cost_material_id' => $account_project['id_cm1'],
        //             'cost_spare_part_id' => $account_project['id_sp'],
        //             'cost_service_id' => $account_project['id_j'],
        //             'cost_finish_project_id' => $account_project['id_cp'],
        //             'dp_id' => $account_project['id_dp'],
        //             'profit_id' => $account_project['id_pp'],
        //         ]
        //     ]; 
            
        //     $response = $client->request('POST', '', $reqBody); 
        // } catch(RequestException $exception) {
        // }
        return redirect('/order')->with('notification');
    }

    public function update(Request $request){
        $id=$request->input('order_id');
        $deleted_order_d=$request->input('deleted_order_d');
        $order_d_id=$request->input('order_d_id');
        $period_year = date('Y');
        $period_month = date('m');
        $editedProd=$request->input('editProd');
        $id_product=$request->input('id_product');
        $nameProd=$request->input('name');
        $deskripsi=$request->input('deskripsi');
        $item=$request->input('item');
        $series=$request->input('series');
        $panjang=$request->input('panjang');
        $lebar=$request->input('lebar');
        $file=$request->file('file');

/*         //set produk baru berdasarkan row
        if($editedProd != null){
            for ($i=0; $i < count($editedProd); $i++) { 
                $index=array_search($editedProd[$i], $id_product);
                $detail=DB::table('products')->where('id', $id_product[$index])->first();
                $image=$detail->image;
                if($file[$index]){
                    $tujuan_upload = 'upload/product';
                        // upload file
                    $name=time().'.'.$file[$index]->getClientOriginalExtension();
                    $file[$index]->move($tujuan_upload, $name);
                    
                    $image=$name;
                }
                $data=array(
                    'name'          => $nameProd[$index],
                    'description'   => $deskripsi[$index],
                    'image'         => $image,
                    'item'   => $item[$index],
                    'series'   => $series[$index],
                    'panjang'   => $panjang[$index],
                    'lebar'   => $lebar[$index],
                    'm_unit_id'      => $detail->m_unit_id,
                    'price'      => $detail->price,
                );
                DB::table('products')->insert($data);
                $last_id=DB::table('products')->max('id');
                $id_product[$index]=$last_id;
            }
        }
        //get data order
        $order=DB::table('orders')->where('id', $id)->first();
        try
        {
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . 'inv/base/Order/' . $id]);  
            $reqBody = [
                'headers' => $headers,
                'json' => [
                    'customer_id' => $request->input('customer_id'),
                    'order_name' => $request->input('order_name'),
                    'order_date' => date('Y-m-d'),
                    'is_done' => $order->is_done,
                    'site_id' => $this->site_id,
                    'order_no'  => $order->order_no
                ]
            ]; 
            $response = $client->request('PUT', '', $reqBody); 
        } catch(RequestException $exception) {    
        }
        
        if($response){
            $total_produk=$request->input('total_produk');
            $x = $id_product!=null ? count($id_product) : 0;
            for ($i=0; $i < $x; $i++) { 
                if($order_d_id[$i] != 0){//jika order id tidak kosong (menandakan order detail sudah ada sebelumnya), maka edit order detail
                    //jika order di daftar hapus, maka command dibawah ini
                    if($deleted_order_d != null){
                        if (in_array($order_d_id[$i], $deleted_order_d)){
                            DB::table('order_ds')->where('id', $order_d_id[$i])->delete();//hapus order detail yang ada dalam list delete
                        }
                    }else{//jika tidak ditemukan di order hapus
                        $get_detail=DB::table('order_ds')->where('id', $order_d_id[$i])->first();
                        if($get_detail->total == $total_produk[$i]){
                            //do nothing
                        }else if($get_detail->total > $total_produk[$i]){
                            $selisih=$get_detail->total - $total_produk[$i];
                            DB::table('order_ds')->where('id', $order_d_id[$i])->update(['total' => $total_produk[$i]]);//update order detail
                            DB::delete('delete from product_subs where id in 
                            (SELECT id from product_subs where order_d_id='.$order_d_id[$i].' order by id desc limit '.$selisih.')');//menghapus pengurangan total order produk
                        }else{
                            DB::table('order_ds')->where('id', $order_d_id[$i])->update(['total' => $total_produk[$i]]);//update order detail
                            for ($j=$get_detail->total + 1; $j <= $total_produk[$i]; $j++) { //perulangan untuk menambah penambahan total order produk
                                $ord_no_detail=$order->order_no.'/'.$id_product[$i].'/'.$j;
                                try
                                {
                                    $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . 'inv/base/ProductSub']);
                                    $reqBody = [
                                        'headers' => $headers,
                'json' => [
                                            'product_id' => $id_product[$i],
                                            'order_d_id' => $order_d_id[$i],
                                            'no' => $ord_no_detail,
                                        ]
                                    ]; 
                                    $response_pd = $client->request('POST', '', $reqBody); 
                                } catch(RequestException $exception) {
                                }
                            }
                        }
                    }
                }else{
                    try
                    {
                        $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . 'inv/base/OrderD']);
                        $reqBody = [
                            'headers' => $headers,
                'json' => [
                                'order_id' => $order->id,
                                'product_id' => $id_product[$i],
                                'in_rab' => 0,
                                'total'     => $total_produk[$i]
                            ]
                        ]; 
                        $response_pd = $client->request('POST', '', $reqBody); 
                    } catch(RequestException $exception) {
                    }
                    $order_d=DB::table('order_ds')->max('id');
                    for ($j=1; $j <= $total_produk[$i]; $j++) { 
                        $ord_no_detail=$order->order_no.'/'.$id_product[$i].'/'.$j;
                        try
                        {
                            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . 'inv/base/ProductSub']);
                            $reqBody = [
                                'headers' => $headers,
                'json' => [
                                    'product_id' => $id_product[$i],
                                    'order_d_id' => $order_d,
                                    'no' => $ord_no_detail,
                                ]
                            ]; 
                            $response_pd = $client->request('POST', '', $reqBody); 
                        } catch(RequestException $exception) {
                        }
                    }
                }
                
            }
 */            DB::table('orders')->where('id',$id)->update(
                [
                    'customer_id' => $request->customer_id,
                    'customer_project_id' => $request->customer_project_id,
                    'order_name' => $request->order_name,
                    'updated_at' => date('Y-m-d H:i:s')
                ]
            );
            $notification = array(
                'message' => 'Success edit Order',
                'alert-type' => 'success'
            );
/*         }else{
            $notification = array(
                'message' => 'Failed edit Order',
                'alert-type' => 'error'
            );
        }
 */        
       return redirect('/order?dari='.$request->dari.'&sampai='.$request->sampai)->with('notification');
    }

    public function GetOrderJson() {
        $response = null;
        try
        {
            if ($this->site_id == '') {
                $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . 'order/list?dari='.$_GET['dari'].'&sampai='.$_GET['sampai']]);
            }else{
                $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . 'order/list?site_id='.$this->site_id.'&dari='.$_GET['dari'].'&sampai='.$_GET['sampai']]); 
            }
            $response = $client->request('GET', '', ['headers' => $headers]); 
            $body = $response->getBody();
            $content = $body->getContents();
            $response_array = json_decode($content,TRUE);

            $response = $content;  
            
            
            $data=DataTables::of($response_array['data'])
            ->addColumn('action', function ($row) {
                                        $getRoleEdit = \DB::table('user_permission as up')->join('menus as m','up.menu_id','m.id')->select(DB::raw('count(up.id) as ttl'))->where('up.role_id',auth()->user()['role_id'])->where('m.url','order/edit')->first();
                                        $edit = $getRoleEdit->ttl==1 ? '<a href="'.url('/order/edit/'.$row['id'].'?dari='.$_GET['dari'].'&sampai='.$_GET['sampai']).'" target="_blank" class="btn btn-warning btn-sm"><i class="mdi mdi-pencil"></i></a>' : '';
                                        return '<button type="button" id="modal_detail" class="btn btn-success btn-sm" data-toggle="modal" data-order_no="'.$row['order_no'].'" data-id="'.$row['id'].'" data-target=".bs-example-modal-lg" onclick="getDetail(this)"><i class="mdi mdi-eye"></i></button>'.$edit.'<a href="'.url('/order/delete/'.$row['id']).'" class="btn btn-danger btn-sm" onclick="javasciprt: return confirm(\'Are You Sure ?\')"><i class="mdi mdi-delete"></i></a>'.($row['is_closed'] == false ? '&nbsp;<a href="'.url('/order/close/'.$row['id']).'" class="btn btn-info btn-sm" onclick="javasciprt: return confirm(\'Are You Sure ?\')"><i class="mdi mdi-recycle"></i></a>&nbsp;' : '');
                                        // '.'<a href="/order/edit/'.$row['id'].'" class="btn btn-info btn-sm"><i class="mdi mdi-pencil"></i></a>'.'
                                    })
                                    ->rawColumns(['order_name', 'action'])
                                    ->make(true);          
        } catch(RequestException $exception) {
            
        }    

        return $data;
    }
    public function GetOrderDetailJson($id) {
        $response = null;
        try
        {
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . 'order/order_d/'.$id]); 
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
    public function deleteOrder($id) {
        try
        {
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . 'inv/base/Order/' . $id]);  
            $response = $client->request('DELETE', '', ['headers' => $headers]); 
        } catch(RequestException $exception) {    
        }

        $notification = array(
            'message' => 'Success delete order',
            'alert-type' => 'success'
        );

        return redirect('order')->with($notification);
    }
    public function bills()
    {
        $is_error = false;
        $error_message = '';  

        $data = array(
            'error' => array(
                'is_error' => $is_error,
                'error_message' => $error_message
            )
        );
        
        return view('pages.inv.order.bill_costumer_list', $data);
    }
    public function GetOrderBillJson() {
        $response = null;
        try
        {
            if ($this->site_id == '') {
                $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . 'order/list?dari=$_GET[dari]&sampai=$_GET[sampai]']);     
            }else{
                $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . 'order/list?site_id='.$this->site_id."&dari=$_GET[dari]&sampai=$_GET[sampai]"]); 
            }
            $response = $client->request('GET', '', ['headers' => $headers]); 
            $body = $response->getBody();
            $content = $body->getContents();
            $response_array = json_decode($content,TRUE);

            $response = $content;  
            foreach ($response_array['data'] as $key => $value) {
                $total=$this->countTotalOrder($value['id']);
                $sub_total=$total['total_product'];
                $ppn=$sub_total * (1/10);
                $response_array['data'][$key]['total']=$sub_total + $ppn;
            }
            
            $data=DataTables::of($response_array['data'])
                                    ->addColumn('action', function ($row) {
                                        return $row['paid_off_date'] != null ? '<a href="'.url('/order/bill/'.$row['id']).'" class="btn btn-info btn-sm">Detail Tagihan</a>' : '<a href="'.url('/order/bill/'.$row['id']).'" class="btn btn-success btn-sm"><i class="mdi mdi-plus"></i></a>';
                                    })
                                    ->rawColumns(['order_name', 'action'])
                                    ->make(true);          
        } catch(RequestException $exception) {
            
        }    

        return $data;
    }
    
    private function countTotalOrder($id){
        $query=DB::table('order_ds')
                    ->join('products', 'products.id', 'order_ds.product_id')
                    ->select(DB::raw('(order_ds.total * products.amount_set) * order_ds.price as total_product'), DB::raw('(order_ds.total * products.amount_set) * products.installation_fee as total_installation'))
                    ->where('order_ds.order_id', $id)
                    ->get();
        $total_produk=$total_instalasi=0;
        foreach ($query as $key => $value) {
            $total_produk+=$value->total_product;
            // $total_instalasi+=0;
        }
        return array('total_product' => $total_produk, 'total_installation' => $total_instalasi, 'ppn' => ($total_produk + $total_instalasi) * (1/10));
    }
    public function billForm($id)
    {
        
        try
        {
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . 'inv/base/Order/' . $id]);  
            $response = $client->request('GET', '', ['headers' => $headers]); 
            $body = $response->getBody();
            $content = $body->getContents();
            $response_array = json_decode($content,TRUE);
            
            $order = $response_array['data'];
        } catch(RequestException $exception) {    
        }

        try
        {
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . 'order/order_d/' . $id]);  
            $response = $client->request('GET', '', ['headers' => $headers]); 
            $body = $response->getBody();
            $content = $body->getContents();
            $response_array = json_decode($content,TRUE);
            
            $order_d = $response_array['data'];
        } catch(RequestException $exception) {    
        }

        try
        {
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . 'inv/base/Customer/' . $order['customer_id']]);  
            $response = $client->request('GET', '', ['headers' => $headers]); 
            $body = $response->getBody();
            $content = $body->getContents();
            $response_array = json_decode($content,TRUE);
            
            $customer = $response_array['data'];
        } catch(RequestException $exception) {    
        }
        $customer_paid=DB::table('customer_paids')->where('order_id', $id)->orderBy('id')->get();
        $customer_bill=DB::table('customer_bills')->where('order_id', $id)->orderBy('id')->get();
        $lastInv = DB::table('customer_bills')->select('invoice_no')->orderBy('id','desc')->limit(1)->get();
        $inv = count($lastInv)==0 ? '00000001' : sprintf($lastInv[0]->invoice_no+1);
        //$inv = count($lastInv)==0 ? '00000001' : sprintf("%08s",$lastInv[0]->invoice_no+1);
        $customer_bill_other=DB::table('customer_bill_others')->where('order_id', $id)->get();
        $list_bank=DB::table('list_bank')->get();
        $data = array(
            'customer' => $customer,
            'order' => $order,
            'order_d' => $order_d,
            'tagihan' => $this->countTotalOrder($order['id']),
            'customer_bill' => $customer_bill,
            'customer_paid' => $customer_paid,
            'customer_bill_other' => $customer_bill_other,
            'list_bank' => $list_bank,
            'inv' => $inv
        );
        return view('pages.inv.order.bill_customer_form', $data);
    }
    public function saveBillPost(Request $request){

        $total=$this->currency($request->input('total'));
        $total_addendum=$this->currency($request->input('total_addendum'));
        $sub_total=$request->input('sub_total');
        $period_year = date('Y');
        $period_month = date('m');
        $rabcon = new RabController();
        $bill_no = $rabcon->generateTransactionNo('BILL', $period_year, $period_month, $this->site_id );
        $cust_bill=array();
        try
        {
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . 'inv/base/CustomerBill']);
            $reqBody = [
                'headers' => $headers,
                'json' => [
                    'customer_id' => $request->input('customer_id'),
                    'order_id' => $request->input('order_id'),
                    'bill_no' => $request->input('no'),
                    'invoice_no' => $request->input('invoice_no'),
                    'bill_address' => $request->input('address'),
                    // 'atas_nama' => $request->input('atas_nama'),
                    // 'ref_code' => $request->input('ref_code'),
                    // 'id_bank' => $request->input('id_bank'),
                    'create_date'   => $request->date_create,
                    'amount' => $this->currency($request->input('total')),
                    'description' => $request->input('deskripsi'),
                    'due_date' => $request->input('due_date'),
                    'no'  => $bill_no,
                    'notes' => $request->input('notes'),
                    'end_payment'   => ($total == $sub_total ? 1 : 0)
                ]
            ]; 
            
            $response = $client->request('POST', '', $reqBody); 
            $body = $response->getBody();
            $content = $body->getContents();
            $response_array = json_decode($content,TRUE);
            $cust_bill=$response_array['data'];
        } catch(RequestException $exception) {
        }
        $order=DB::table('orders')->where('id', $request->input('order_id'))->first();
        $total_all=($total == $sub_total ? ($total) : $total);
        // $total_ppn=($total == $sub_total ? (($total + $total_addendum) * (1/10)) : ($total * (1/10)));
        // $save_total=$total_all / 1.1;
        $total_ppn=$total_all * 0.1;
        // $account_project=DB::table('account_projects')->where('order_id', $request->input('order_id'))->first();
        $input_jurnal=array(
            'order_id' => $request->input('order_id'),
            'customer_id' => $order->customer_id,
            'customer_bill_id' => $cust_bill['id'],
            'total' => $total_all,
            'total_tagihan' => 0,
            'paid_more' => 0,
            'ppn' => $total_ppn,
            'type_ppn' => 'DEBIT',
            'user_id'   => $this->user_id,
            'deskripsi'     => 'Pembuatan Tagihan '.$request->input('deskripsi').' No Order '.$order->order_no,
            'tgl'       => $request->date_create,
            'akun'      => 151,
            // 'lawan'      => $account_project->dp_id,
            'lawan'      => 5759, //kepala 2 uang muka proyek 2.1.10.1
            'location_id'   => $this->site_id,

        );
        $this->journalOrderPayment($input_jurnal);
        if ($total == $sub_total) {
            try
            {
                $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . 'inv/base/Order/'.$request->input('order_id')]);
                $reqBody = [
                    'headers' => $headers,
                'json' => [
                        'paid_off_date' => date('Y-m-d H:i:s'),
                    ]
                ]; 
                
                $response = $client->request('PUT', '', $reqBody); 
            } catch(RequestException $exception) {
            }
        }
        
        return redirect('order/bill/'.$request->input('order_id'));
    }
    public function saveBillOtherPost(Request $request){
        
        $total=$this->currency($request->input('total'));
        try
        {
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . 'inv/base/CustomerBillOther']);
            $reqBody = [
                'headers' => $headers,
                'json' => [
                    'customer_id' => $request->input('customer_id'),
                    'order_id' => $request->input('order_id') ? $request->input('order_id') : 0,
                    'install_order_id' => $request->input('install_order_id') ? $request->input('install_order_id') : 0,
                    'notes' => $request->input('notes'),
                    'amount' => $this->currency($request->input('total')),
                    'description' => $request->input('tipe'),
                    'is_increase' => $request->input('tipe') == 'addendum' ? 1 : 0,
                ]
            ]; 

            $response = $client->request('POST', '', $reqBody); 
            $body = $response->getBody();
            $content = $body->getContents();
            $response_array = json_decode($content,TRUE);
        } catch(RequestException $exception) {
        }
        
        // $order=DB::table('orders')->where('id', $request->input('order_id'))->first();
        // $input_jurnal=array(
        //     'order_id' => $request->input('order_id'),
        //     'total' => $this->currency($request->input('total')),
        //     'user_id'   => $this->user_id,
        //     'deskripsi'     => 'Permintaan Addendum No Order '.$order->order_no,
        //     'tgl'       => date('Y-m-d'),
        //     'akun'      => $request->input('tipe') == 'addendum' ? 22 : 24,
        //     'lawan'      => $request->input('tipe') == 'addendum' ? 24 : 22,
        //     'location_id'   => $this->site_id
        // );
        // $this->journalOrderPayment($input_jurnal);
        if($request->input('order_id')){
            return redirect('order/bill/'.$request->input('order_id'));
        }else{
            return redirect('order/bill_install/'.$request->input('install_order_id'));
        }
    }
    public function deleteBill($id)
    {
        try
        {
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . 'inv/base/CustomerBill/'.$id]);
            
            $response = $client->request('GET', '', ['headers' => $headers]); 
            $body = $response->getBody();
            $content = $body->getContents();
            $response_array = json_decode($content,TRUE);
            $cust_bill=$response_array['data'];
        } catch(RequestException $exception) {
        }
        
        try
        {
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . 'inv/base/Order/' . $cust_bill['order_id']]);  
            $reqBody = [
                'headers' => $headers,
                'json' => [
                    'paid_off_date' => null,
                ]
            ]; 
            
            $response = $client->request('PUT', '', $reqBody); 
        } catch(RequestException $exception) {    
        }   
        
        $query=DB::table('customer_bills')->where('id', $id)->delete();

        return redirect('order/bill/'. $cust_bill['order_id']);
    }
    private function currency($val){
        $data=explode('.', $val);
        $new=implode('', $data);
        return $new;
    }
    private function journalOrderPayment($data){
        $data_trx=array(
            'deskripsi'     => $data['deskripsi'],
            'location_id'     => $data['location_id'],
            'tanggal'       => $data['tgl'],
            'user_id'       => $data['user_id'],
            'customer_id'  => $data['customer_id'],
            'customer_bill_id' => $data['customer_bill_id'],
            'order_id'   => $data['order_id']
        );
        $insert=DB::table('tbl_trx_akuntansi')->insert($data_trx);
        if ($insert) {
            $id_last=DB::table('tbl_trx_akuntansi')->max('id_trx_akun');
            $no=null;
            if ($data['akun'] != 36) {
                $acccon = new AkuntanController();
                $no=$acccon->createNo($data['akun'], "DEBIT");
            }
            $akun=array(
                'id_trx_akun'   => $id_last,
                'id_akun'       => $data['akun'],
                'jumlah'        => ($data['total'] + ($data['ppn'] != 0 && $data['type_ppn'] == 'DEBIT' ? $data['ppn'] : 0)),
                'tipe'          => "DEBIT",
                'keterangan'    => 'akun',
                'no'        => $no,
                'order_id' => $data['order_id'],
            );
            DB::table('tbl_trx_akuntansi_detail')->insert($akun);
            if($data['akun'] == 24 || $data['akun'] == 101){
                $acccon = new AkuntanController();
                $warehouse_id=$data['akun'] == 24 ? 2 : 3;
                $total=($data['total'] + ($data['ppn'] != 0 && $data['type_ppn'] == 'DEBIT' ? $data['ppn'] : 0));
                $acccon->countSaldoCash($this->site_id, $warehouse_id, "in", $total);
            }
            $lawan=array(
                'id_trx_akun'   => $id_last,
                'id_akun'       => $data['lawan'],
                'jumlah'        => $data['total'] - $data['paid_more'],
                'tipe'          => "KREDIT",
                'keterangan'    => 'lawan',
                'order_id' => $data['order_id'],
            );
            DB::table('tbl_trx_akuntansi_detail')->insert($lawan);
            if($data['lawan'] == 24 || $data['lawan'] == 101){
                $acccon = new AkuntanController();
                $warehouse_id=$data['lawan'] == 24 ? 2 : 3;
                $total=$data['total'] - $data['paid_more'];
                $acccon->countSaldoCash($this->site_id, $warehouse_id, "out", $total);
            }
            if ($data['ppn'] != 0 && $data['type_ppn'] == 'DEBIT') {
                $ppn=array(
                    'id_trx_akun'   => $id_last,
                    'id_akun'       => 67,
                    'jumlah'        => $data['ppn'],
                    'tipe'          => "KREDIT",
                    'keterangan'    => 'lawan',
                    'order_id' => $data['order_id'],
                );
                DB::table('tbl_trx_akuntansi_detail')->insert($ppn);
            }
            if ($data['paid_more'] != 0) {
                $ppn=array(
                    'id_trx_akun'   => $id_last,
                    'id_akun'       => 162,
                    'jumlah'        => $data['paid_more'],
                    'tipe'          => "KREDIT",
                    'keterangan'    => 'lawan',
                    'order_id' => $data['order_id'],
                );
                DB::table('tbl_trx_akuntansi_detail')->insert($ppn);
            }
        }
    }
    private function journalOrderInstallPayment($data){
        $data_trx=array(
            'deskripsi'     => $data['deskripsi'],
            'location_id'     => $data['location_id'],
            'tanggal'       => $data['tgl'],
            'user_id'       => $data['user_id'],
            'customer_id'   => $data['customer_id'],
            'customer_bill_id'   => $data['customer_bill_id'],
            'install_order_id'   => $data['install_order_id']
        );
        $insert=DB::table('tbl_trx_akuntansi')->insert($data_trx);
        if ($insert) {
            $id_last=DB::table('tbl_trx_akuntansi')->max('id_trx_akun');
            $no=null;
            if ($data['akun'] != 36) {
                $acccon = new AkuntanController();
                $no=$acccon->createNo($data['akun'], "DEBIT");
            }
            $akun=array(
                'id_trx_akun'   => $id_last,
                'id_akun'       => $data['akun'],
                'jumlah'        => $data['total'] + ($data['ppn'] != 0 && $data['type_ppn'] == 'DEBIT' ? $data['ppn'] - $data['pph'] : 0) - $data['paid_more'],
                'tipe'          => "DEBIT",
                'keterangan'    => 'akun',
                'no'        => $no
            );
            DB::table('tbl_trx_akuntansi_detail')->insert($akun);
            if($data['akun'] == 24 || $data['akun'] == 101){
                $acccon = new AkuntanController();
                $warehouse_id=$data['akun'] == 24 ? 2 : 3;
                $total=($data['total'] + ($data['ppn'] != 0 && $data['type_ppn'] == 'DEBIT' ? $data['ppn'] - $data['pph'] : 0) - $data['paid_more']);
                $acccon->countSaldoCash($this->site_id, $warehouse_id, "in", $total);
            }
            $lawan=array(
                'id_trx_akun'   => $id_last,
                'id_akun'       => $data['lawan'],
                'jumlah'        => $data['total'],
                'tipe'          => "KREDIT",
                'keterangan'    => 'lawan',
            );
            DB::table('tbl_trx_akuntansi_detail')->insert($lawan);
            if($data['lawan'] == 24 || $data['lawan'] == 101){
                $acccon = new AkuntanController();
                $warehouse_id=$data['lawan'] == 24 ? 2 : 3;
                $total=$data['total'];
                $acccon->countSaldoCash($this->site_id, $warehouse_id, "out", $total);
            }
            if ($data['ppn'] != 0 && $data['type_ppn'] == 'DEBIT') {
                $ppn=array(
                    'id_trx_akun'   => $id_last,
                    'id_akun'       => 67,
                    'jumlah'        => $data['ppn'],
                    'tipe'          => "KREDIT",
                    'keterangan'    => 'lawan',
                );
                DB::table('tbl_trx_akuntansi_detail')->insert($ppn);
            }
            if ($data['pph'] != 0 && $data['type_ppn'] == 'DEBIT') {
                $ppn=array(
                    'id_trx_akun'   => $id_last,
                    'id_akun'       => 135,
                    'jumlah'        => $data['pph'],
                    'tipe'          => "DEBIT",
                    'keterangan'    => 'lawan',
                );
                DB::table('tbl_trx_akuntansi_detail')->insert($ppn);
            }
            if ($data['paid_more'] != 0) {
                $ppn=array(
                    'id_trx_akun'   => $id_last,
                    'id_akun'       => 162,
                    'jumlah'        => $data['paid_more'],
                    'tipe'          => "DEBIT",
                    'keterangan'    => 'lawan',
                );
                DB::table('tbl_trx_akuntansi_detail')->insert($ppn);
            }
        }
    }
    public function printBill($id)
    {
        try
        {
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . 'inv/base/CustomerBill/'.$id]);
            
            $response = $client->request('GET', '', ['headers' => $headers]); 
            $body = $response->getBody();
            $content = $body->getContents();
            $response_array = json_decode($content,TRUE);
            $cust_bill=$response_array['data'];
        } catch(RequestException $exception) {
        }
        try
        {
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . 'inv/base/Order/'.$cust_bill['order_id']]);
            
            $response = $client->request('GET', '', ['headers' => $headers]); 
            $body = $response->getBody();
            $content = $body->getContents();
            $response_array = json_decode($content,TRUE);
            $order=$response_array['data'];
        } catch(RequestException $exception) {
        }
        try
        {
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . 'inv/base/Customer/'.$order['customer_id']]);
            
            $response = $client->request('GET', '', ['headers' => $headers]); 
            $body = $response->getBody();
            $content = $body->getContents();
            $response_array = json_decode($content,TRUE);
            $customer=$response_array['data'];
        } catch(RequestException $exception) {
        }
        $cust_bill_other=DB::table('customer_bill_others')->where('order_id', $cust_bill['order_id'])->get();
        $data=array(
            'cust_bill' => $cust_bill,
            'cust_bill_other' => $cust_bill_other,
            'order'     => $order,
            'customer'  => $customer
        );
        return view('pages.inv.order.print_bill_order', $data);
    }
    public function detailCustomerBill($id){
        $query['data']=DB::table('customer_bill_ds')
                            ->where('customer_bill_id', $id)
                            ->leftJoin('list_bank', 'list_bank.id_bank', 'customer_bill_ds.id_bank')
                            ->select('customer_bill_ds.*', 'list_bank.bank_name')
                            ->get();
        foreach ($query['data'] as $key => $value) {
            $value->number='';
            $detail=DB::table('paid_customer_ds as pcd')->where('customer_bill_d_id', $value->id)->first();
            if($detail != null){
                $get_no=DB::table('tbl_trx_akuntansi as tra')->where('paid_customer_id', $detail->paid_customer_id)->join('tbl_trx_akuntansi_detail as trd', 'tra.id_trx_akun', 'trd.id_trx_akun')->select(DB::raw('MAX(trd.no) as no'))->first();
                $value->number=$get_no->no;
            }
        }
        return $query;
    }
    public function saveBillDetailPost(Request $request){
        $total=$this->currency($request->input('total_bill'));
        $total_awal=$request->input('total_awal');
        $total_min=$request->input('total_min');
        $total_ppn=$request->input('total_ppn');
        $paid_more=$request->input('paid_more');
        $total_tagihan=$total-$paid_more;
        $period_year = date('Y');
        $period_month = date('m');
        $rabcon = new RabController();
        $bill_no = $rabcon->generateTransactionNo('PAY_BILL', $period_year, $period_month, $this->site_id );
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
                    'customer_bill_id' => $request->input('bill_id'),
                    'wop' => $request->input('wop'),
                    'bank_number' => $request->input('bank_number'),
                    'atas_nama' => $request->input('atas_nama'),
                    'ref_code' => $request->input('ref_code'),
                    'id_bank' => $request->input('id_bank'),
                    'amount' => $total,
                    'pay_date' => date('Y-m-d'),
                    'no'  => $bill_no
                ]
            ]; 
            $response = $client->request('POST', '', $reqBody); 
            $body = $response->getBody();
            $content = $body->getContents();
            $response_array = json_decode($content,TRUE);
            $cust_bill_d=$response_array['data'];
        } catch(RequestException $exception) {
        }
        if ($total >= $total_min) {
            try
            {
                $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . 'inv/base/CustomerBill/'.$request->input('bill_id')]);
                
                $reqBody = [
                    'headers' => $headers,
                'json' => [
                        'is_paid' => 1,
                    ]
                ]; 
                $response = $client->request('PUT', '', $reqBody); 
            } catch(RequestException $exception) {
            }
        }
        try
        {
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . 'inv/base/CustomerBill/'.$request->input('bill_id')]);
            
            $response = $client->request('GET', '', ['headers' => $headers]); 
            $body = $response->getBody();
            $content = $body->getContents();
            $response_array = json_decode($content,TRUE);
            $cust_bill=$response_array['data'];
        } catch(RequestException $exception) {
        }
        if ($request->wop == 'giro') {
            $giro_no = $rabcon->generateTransactionNo('GIRO', $period_year, $period_month, $this->site_id );
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
                        'customer_bill_id' => $request->input('bill_id'),
                        'customer_bill_d_id' => $cust_bill_d['id'],
                        'order_id' => $cust_bill['order_id'],
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
        $order=DB::table('orders')->where('id', $cust_bill['order_id'])->first();
        $input_jurnal=array(
            'order_id' => $request->input('order_id'),
            'customer_id' => $order->customer_id,
            'total' => $total,
            'total_tagihan' => $total_tagihan,
            'ppn' => $total_ppn,
            'paid_more' => $paid_more,
            'type_ppn' => 'KREDIT',
            'user_id'   => $this->user_id,
            'deskripsi'     => 'Pembayaran Tagihan No Order '.$order->order_no,
            'tgl'       => date('Y-m-d'),
            'akun'      => $request->input('wop') == 'giro' ? 36 : $request->account_payment,
            'lawan'      => 151,
            'location_id'   => $this->site_id
        );
        
        $this->journalOrderPayment($input_jurnal);
        $query=DB::table('customer_bills')->where('order_id', $cust_bill['order_id'])->where('end_payment', 'true')->first();
        if ($query != null) {
            $cek=DB::table('customer_bills')->where('order_id', $cust_bill['order_id'])->select(DB::raw('COUNT(id) as total_bill'), DB::raw('SUM(CASE WHEN is_paid = true THEN 1 ELSE 0 END) as total_paid'))->first();
            if ($cek->total_bill == $cek->total_paid) {
                try
                {
                    $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . 'inv/base/Order/'.$cust_bill['order_id']]);
                    
                    $reqBody = [
                        'headers' => $headers,
                'json' => [
                            'is_done' => 1,
                        ]
                    ]; 
                    $response = $client->request('PUT', '', $reqBody); 
                } catch(RequestException $exception) {
                }
            }
            $project_req_developments=DB::table('project_req_developments')->where('order_id', $cust_bill['order_id'])->pluck('id');
            // $account_project=DB::table('account_projects')->where('order_id', $cust_bill['order_id'])->first();
            $trx_akuntan=DB::table('tbl_trx_akuntansi as tra')
                            ->join('tbl_trx_akuntansi_detail as trd', 'tra.id_trx_akun', 'trd.id_trx_akun')
                            ->join('tbl_akun as ta', 'trd.id_akun', 'ta.id_akun')
                            ->select(DB::raw("SUM(CASE WHEN tipe = 'DEBIT' THEN jumlah ELSE 0 END) AS total_debit"), DB::raw("SUM(CASE WHEN tipe = 'KREDIT' THEN jumlah ELSE 0 END) AS total_kredit"), 'trd.id_akun', DB::raw('MAX(sifat_debit) as sifat_debit'), DB::raw('MAX(sifat_kredit) as sifat_kredit'), DB::raw('MAX(nama_akun) as nama_akun'))
                            // ->whereIn('tra.project_req_development_id', $project_req_developments)
                            // ->whereIn('trd.id_akun', [$account_project->dp_id])
                            ->where('id_akun', 5759)
                            ->where('order_id', $cust_bill['order_id'])
                            ->groupBy('trd.id_akun')
                            ->first();
            $order=DB::table('orders')->where('id', $cust_bill['order_id'])->first();
            $input_jurnal=array(
                'order_id' => $cust_bill['order_id'],
                'total' => $trx_akuntan->total_kredit - $trx_akuntan->total_debit,
                'user_id'   => $this->user_id,
                'deskripsi'     => 'Jurnal Pendapatan No Order '.$order->order_no,
                'tgl'       => date('Y-m-d'),
                // 'akun'      => $account_project->dp_id,
                'akun'      => 5759, //uang muka proyek
                // 'lawan'      => $account_project->profit_id,
                'lawan'      => 5760, //Pendapatan Proyek
                'location_id'   => $this->site_id
            );
            $this->journalPendapatanProyek($input_jurnal);
        }
        return redirect('order/list_tagihan');
    }
    public function printBillKwitansi($id)
    {
        $cust_bill_d=DB::table('customer_bill_ds')->where('id', $id)->first();
        try
        {
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . 'inv/base/CustomerBill/'.$cust_bill_d->customer_bill_id]);
            
            $response = $client->request('GET', '', ['headers' => $headers]); 
            $body = $response->getBody();
            $content = $body->getContents();
            $response_array = json_decode($content,TRUE);
            $cust_bill=$response_array['data'];
        } catch(RequestException $exception) {
        }
        try
        {
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . 'inv/base/Order/'.$cust_bill['order_id']]);
            
            $response = $client->request('GET', '', ['headers' => $headers]); 
            $body = $response->getBody();
            $content = $body->getContents();
            $response_array = json_decode($content,TRUE);
            $order=$response_array['data'];
        } catch(RequestException $exception) {
        }
        try
        {
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . 'inv/base/Customer/'.$order['customer_id']]);
            
            $response = $client->request('GET', '', ['headers' => $headers]); 
            $body = $response->getBody();
            $content = $body->getContents();
            $response_array = json_decode($content,TRUE);
            $customer=$response_array['data'];
        } catch(RequestException $exception) {
        }
        $data=array(
            'cust_bill' => $cust_bill,
            'cust_bill_d' => $cust_bill_d,
            'order'     => $order,
            'customer'  => $customer
        );
        // return $data;
        return view('pages.inv.order.print_kwitansi', $data);
    }
    public function createAccount($nama_proyek){
        $cost_material=$this->getNoAkun(152);
        $no_cm=$this->explodeNoAkun($cost_material['no_akun_main']);
        $data_cm=array(
            'no_akun'   => $no_cm[0].'.'.$no_cm[1].'.'.$no_cm[2].'.'.($cost_material['total'] + 1),
            'nama_akun' => 'Biaya Dalam Proses Proyek '.$nama_proyek,
            'id_main_akun' => 152,
            'level' => 3,
            'sifat_debit'     => 1,
            'sifat_kredit'    => 0,   
            'id_parent'       => $cost_material['id_parent'],
            'turunan1'        => $cost_material['turunan1'],
            'turunan2'        => 152,
            'turunan3'        => $cost_material['turunan3'],
            'turunan4'        => $cost_material['turunan4'], 
        );
        $id_cm=$this->saveAccount($data_cm);
        $id_cm1=$id_sp=$id_j=0;
        for ($i=1; $i <= 3; $i++) { 
            $data_cm=array(
                'no_akun'   => $no_cm[0].'.'.$no_cm[1].'.'.$no_cm[2].'.'.($cost_material['total'] + 1).'.'.$i,
                'nama_akun' => 'Biaya Dalam Proses Proyek '.($i == 1 ? 'Material ' : ($i == 2 ? 'Spare Part ' : 'Jasa ')).$nama_proyek,
                'id_main_akun' => $id_cm,
                'level' => 4,
                'sifat_debit'     => 1,
                'sifat_kredit'    => 0,   
                'id_parent'       => $cost_material['id_parent'],
                'turunan1'        => $cost_material['turunan1'],
                'turunan2'        => 152,
                'turunan3'        => $id_cm,
                'turunan4'        => $cost_material['turunan4'], 
            );
            $id_ct=$this->saveAccount($data_cm);
            if ($i == 1) {
                $id_cm1=$id_ct;
            }elseif($i == 2){
                $id_sp=$id_ct;
            }else{
                $id_j=$id_ct;
            }
        }
        $cost_proyek=$this->getNoAkun(153);
        $no_cp=$this->explodeNoAkun($cost_proyek['no_akun_main']);
        $data_cp=array(
            'no_akun'   => $no_cp[0].'.'.$no_cp[1].'.'.$no_cp[2].'.'.($cost_proyek['total'] + 1),
            'nama_akun' => 'Biaya Produk Jadi Proyek '.$nama_proyek,
            'id_main_akun' => 153,
            'level' => 3,
            'sifat_debit'     => 1,
            'sifat_kredit'    => 0,   
            'id_parent'       => $cost_proyek['id_parent'],
            'turunan1'        => $cost_proyek['turunan1'],
            'turunan2'        => 153,
            'turunan3'        => $cost_proyek['turunan3'],
            'turunan4'        => $cost_proyek['turunan4'], 
        );
        $id_cp=$this->saveAccount($data_cp);

        $dp=$this->getNoAkun(154);
        $no_dp=$this->explodeNoAkun($dp['no_akun_main']);
        $data_dp=array(
            'no_akun'   => $no_dp[0].'.'.$no_dp[1].'.'.$no_dp[2].'.'.($dp['total'] + 1),
            'nama_akun' => 'Uang Muka Proyek '.$nama_proyek,
            'id_main_akun' => 154,
            'level' => 3,
            'sifat_debit'     => 0,
            'sifat_kredit'    => 1,   
            'id_parent'       => $dp['id_parent'],
            'turunan1'        => $dp['turunan1'],
            'turunan2'        => 154,
            'turunan3'        => $dp['turunan3'],
            'turunan4'        => $dp['turunan4'], 
        );
        $id_dp=$this->saveAccount($data_dp);

        $pp=$this->getNoAkun(22);
        $no_pp=$this->explodeNoAkun($pp['no_akun_main']);
        $data_pp=array(
            'no_akun'   => $no_pp[0].'.'.$no_pp[1].'.'.($pp['total'] + 1).'.0',
            'nama_akun' => 'Pendapatan Proyek '.$nama_proyek,
            'id_main_akun' => 22,
            'level' => 2,
            'sifat_debit'     => 0,
            'sifat_kredit'    => 1,   
            'id_parent'       => $pp['id_parent'],
            'turunan1'        => 22,
            'turunan2'        => 0,
            'turunan3'        => $pp['turunan3'],
            'turunan4'        => $pp['turunan4'], 
        );
        $id_pp=$this->saveAccount($data_pp);
        
        $data=array(
            'id_cm' => $id_cm,
            'id_cm1' => $id_cm1,
            'id_sp' => $id_sp,
            'id_j' => $id_j,
            'id_cp' => $id_cp,
            'id_dp' => $id_dp,
            'id_pp' => $id_pp
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
    private function journalPendapatanProyek($data){
        
        $data_trx=array(
            'deskripsi'     => $data['deskripsi'],
            'location_id'     => $data['location_id'],
            'tanggal'       => $data['tgl'],
            'user_id'       => $data['user_id'],
            'order_id'   => $data['order_id']
        );
        $insert=DB::table('tbl_trx_akuntansi')->insert($data_trx);
        if ($insert) {
            $id_last=DB::table('tbl_trx_akuntansi')->max('id_trx_akun');
            $akun=array(
                'id_trx_akun'   => $id_last,
                'id_akun'       => $data['akun'],
                'jumlah'        => $data['total'],
                'tipe'          => "DEBIT",
                'keterangan'    => 'akun',
                'order_id'   => $data['order_id']
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
                'jumlah'        => $data['total'],
                'tipe'          => "KREDIT",
                'keterangan'    => 'lawan',
                'order_id'   => $data['order_id']
            );
            DB::table('tbl_trx_akuntansi_detail')->insert($lawan);
            if($data['lawan'] == 24 || $data['lawan'] == 101){
                $acccon = new AkuntanController();
                $warehouse_id=$data['lawan'] == 24 ? 2 : 3;
                $acccon->countSaldoCash($this->site_id, $warehouse_id, "out", $data['total']);
            }
        }
    }
    public function closeProject($id){
        $project_req_developments=DB::table('project_req_developments')->where('order_id', $id)->pluck('id');
        // $account_project=DB::table('account_projects')->where('order_id', $id)->first();
        $trx_akuntan=DB::table('tbl_trx_akuntansi as tra')
                        ->join('tbl_trx_akuntansi_detail as trd', 'tra.id_trx_akun', 'trd.id_trx_akun')
                        ->join('tbl_akun as ta', 'trd.id_akun', 'ta.id_akun')
                        ->select(DB::raw("SUM(CASE WHEN tipe = 'DEBIT' THEN jumlah ELSE 0 END) AS total_debit"), DB::raw("SUM(CASE WHEN tipe = 'KREDIT' THEN jumlah ELSE 0 END) AS total_kredit"), 'trd.id_akun', DB::raw('MAX(sifat_debit) as sifat_debit'), DB::raw('MAX(sifat_kredit) as sifat_kredit'), DB::raw('MAX(nama_akun) as nama_akun'))
                        // ->whereIn('tra.project_req_development_id', $project_req_developments)
                        // ->whereIn('trd.id_akun', [$account_project->cost_material_id, $account_project->cost_spare_part_id, $account_project->cost_service_id])
                        ->whereIn('trd.id_akun', [5755, 5756, 5757]) //akun biaya material, sparepart, jasa
                        ->where('trd.order_id', $id)
                        ->groupBy('trd.id_akun')
                        ->get();
        $order=DB::table('orders')->where('id', $id)->first();
        foreach ($trx_akuntan as $key => $value) {
            $input_jurnal=array(
                'order_id' => $id,
                'total' => ($value->total_debit - $value->total_kredit),
                'user_id'   => $this->user_id,
                'deskripsi'     => 'Jurnal HPP Proyek No Order '.$order->order_no,
                'tgl'       => date('Y-m-d'),
                'akun'      => 84,
                'lawan'      => $value->id_akun,
                'location_id'   => $this->site_id
            );
            $this->journalPendapatanProyek($input_jurnal);
        }
        $trx_dp=DB::table('tbl_trx_akuntansi as tra')
                        ->join('tbl_trx_akuntansi_detail as trd', 'tra.id_trx_akun', 'trd.id_trx_akun')
                        ->join('tbl_akun as ta', 'trd.id_akun', 'ta.id_akun')
                        ->select(DB::raw("SUM(CASE WHEN tipe = 'DEBIT' THEN jumlah ELSE 0 END) AS total_debit"), DB::raw("SUM(CASE WHEN tipe = 'KREDIT' THEN jumlah ELSE 0 END) AS total_kredit"), 'trd.id_akun', DB::raw('MAX(sifat_debit) as sifat_debit'), DB::raw('MAX(sifat_kredit) as sifat_kredit'), DB::raw('MAX(nama_akun) as nama_akun'))
                        // ->whereIn('tra.project_req_development_id', $project_req_developments)
                        // ->whereIn('trd.id_akun', [$account_project->dp_id])
                        ->where('id_akun', 5759) 
                        ->where('order_id', $id)
                        ->groupBy('trd.id_akun')
                        ->get();
        foreach ($trx_dp as $key => $value) {
            $input_jurnal=array(
                'order_id' => $id,
                'total' => ($value->total_kredit - $value->total_debit),
                'user_id'   => $this->user_id,
                'deskripsi'     => 'Jurnal Pendapatan Proyek No Order '.$order->order_no,
                'tgl'       => date('Y-m-d'),
                // 'lawan'      => $account_project->profit_id,
                'lawan'      => 5760, //pendapatan proyek
                'akun'      => $value->id_akun,
                'location_id'   => $this->site_id
            );
            $this->journalPendapatanProyek($input_jurnal);
        }
        
        try
        {
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . 'inv/base/Order/'.$id]);
            $reqBody = [
                'headers' => $headers,
                'json' => [
                    'is_closed' => true,
                ]
            ]; 
            
            $response = $client->request('PUT', '', $reqBody); 
        } catch(RequestException $exception) {
        }
        return redirect('order');
    }
    public function importItemPost(Request $request) 
    {
        // dd($request);
        // echo 'asdf';
        // validasi
        try{
            $this->validate($request, [
                'file' => 'required|mimes:csv,xls,xlsx'
            ]);
            $customer_id = $request->customer_id;
            $kavling_id = $request->kavling_id;
            // menangkap file excel
            $file = $request->file('file');
            // membuat nama file unik
            
            $nama_file = rand().'tes'.$file->getClientOriginalName();
            
            // upload ke folder file_siswa di dalam folder public
            $file->move('import_excel', $nama_file);
            
            // import data
            $array = Excel::toArray(new ExcelDataImport, public_path('/import_excel/'.$nama_file));
            // return $array;
            $data = array();
            
            foreach ($array[0] as $key => $value) {
                if ($key > 1) {
                    $unit=DB::table('m_units')->where('name', $value[7])->first();
                    if ($unit != null) {
                        $data_import=array(
                            'item'  =>$value[1],
                            'name'  => $value[2],
                            'series'    =>$value[3],
                            'description'   => $value[4],
                            'kaca'      => $value[5],
                            'amount_set'    => $value[6],
                            'm_unit_id'     => $unit->id,
                            'panjang'       => $value[8],
                            'lebar'         => $value[9],
                            'price'         => $value[10],
                            'installation_fee'         => array_key_exists(11, $value) ? $value[11] : 0,
                            'customer_id'   => $customer_id,
                            'kavling_id'    => $kavling_id,
                            'created_at'    => date('Y-m-d H:i:s'),
                            'updated_at'    => date('Y-m-d H:i:s'),
                            'is_active' => 1
                        );
                        DB::table('products')->insert($data_import);
                        $id=DB::table('products')->max('id');
                        array_push($data, $id);   
                    }
                }
            }
            
            unlink(public_path('/import_excel/'.$nama_file));
            // return public_path('/import_excel/'.$nama_file);
            return response()->json(['data'=>$data]);
        }
        catch (RequestException $e){
            return $e->getMessage();
        }
        catch(Exception $e){
            return $e->getMessage();
        }
        
    }
    public function getItemCustomer($id){
        $query=DB::table('products')->join('customers as c', 'c.id', 'products.customer_id')
                                    ->join('kavlings as k', 'k.id', 'products.kavling_id')
                                    ->select('products.*', 'k.name as type_kavling')
                                    ->where('products.customer_id', $id)
                                    ->whereNull('products.deleted_at')
                                    ->get();
        $data=array(
            'data'  => $query
        );
        return $data;
    }
    public function createOrderInstall()
    {
        //basic variable
        $is_error = false;
        $error_message = '';

        //bussiness variable
        $site_location = null;

        
        $response = null;
        try
        {
            if ($this->site_id == null) {
                $headers = [
                                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                                'Accept'        => 'application/json',
                            ];
                $client1 = new Client(['base_uri' => $this->base_api_url . 'order/option']); 
            }else{
                $headers = [
                                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                                'Accept'        => 'application/json',
                            ];
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
        DB::table('temp_worksubs')->delete();
        $data = array(
            'error' => array(
                'is_error' => $is_error,
                'error_message' => $error_message
            ),
            // 'site_locations' => $site_location,
            'order_list' => $order_list
        );
        return view('pages.inv.order.form_order_install', $data);
    }
    public function detailOrder($id){
        $query=DB::table('order_ds')->join('products', 'products.id', 'order_ds.product_id')
                                    ->join('kavlings', 'kavlings.id', 'products.kavling_id')
                                    ->select('order_ds.*', 'products.item', 'products.name', 'products.series', 'products.description', 'products.panjang', 'products.lebar', 'products.installation_fee', 'products.amount_set', 'kavlings.name as type_kavling')
                                    ->where('order_ds.order_id', $id)
                                    ->get();
        $data=array(
            'data'  => $query
        );
        return $data;
    }
    public function saveOrderInstall(Request $request){
        $period_year = date('Y');
        $period_month = date('m');
        $order_id=$request->input('order_id');
        $id=$request->input('id');
        $notes=$request->input('notes');
        $id_product=$request->input('id_product');
        $total_produk=$request->input('total_produk');
        $order=DB::table('orders')->where('id', $order_id)->first();
        $rabcon = new RabController();
        $ord_no = $rabcon->generateTransactionNo('ORD_INS', $period_year, $period_month, $this->site_id );
        try
        {
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . 'inv/base/InstallOrder']);
            $reqBody = [
                'headers' => $headers,
                'json' => [
                    'customer_id' => $order->customer_id,
                    'order_id' => $order_id,
                    'spk_no'    => $request->spk_no,
                    'order_date' => date('Y-m-d'),
                    'is_done' => 0,
                    'site_id' => $this->site_id,
                    'no'  => $ord_no,
                    'notes' => $notes
                ]
            ]; 
            
            $response = $client->request('POST', '', $reqBody); 
            $body = $response->getBody();
            $content = $body->getContents();
            $response_array = json_decode($content,TRUE);  
            $detail = $response_array['data'];
        } catch(RequestException $exception) {
        }
        
        for ($i=0; $i < count($id_product); $i++) { 
            //create order detail
            try
            {
                $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . 'inv/base/InstallOrderD']);
                $reqBody = [
                    'headers' => $headers,
                'json' => [
                        'install_order_id' => $detail['id'],
                        'order_d_id' => $id[$i],
                        'product_id' => $id_product[$i],
                        'total'     => $total_produk[$i]
                    ]
                ]; 
                $response = $client->request('POST', '', $reqBody); 
                $body = $response->getBody();
                $content = $body->getContents();
                $response_array = json_decode($content,TRUE);  
                $detail_pd = $response_array['data'];
            } catch(RequestException $exception) {
            }
            $worksubs=DB::table('temp_worksubs')->where('order_d_id', $id[$i])->get();
            
            foreach ($worksubs as $key => $value) {
                try
                {
                    $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . 'inv/base/InstallWorksub']);
                    $reqBody = [
                        'headers' => $headers,
                'json' => [
                            'install_order_id' => $detail['id'],
                            'install_order_d_id' => $detail_pd['id'],
                            'order_d_id' => $id[$i],
                            'product_id' => $id_product[$i],
                            'worksub_id' => $value->worksub_id,
                            'price_work'     => $value->price,
                        ]
                    ]; 
                    $response = $client->request('POST', '', $reqBody);
                } catch(RequestException $exception) {
                }
            }
            try
            {
                $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . 'inv/base/Product/'.$id_product[$i]]);
                $reqBody = [
                    'headers' => $headers,
                'json' => [
                        'installation_fee' => $request->input('fee_install')[$i],
                    ]
                ]; 
                $response_pd = $client->request('PUT', '', $reqBody); 
            } catch(RequestException $exception) {
            }
        }
        DB::table('temp_worksubs')->delete();
        return redirect('/order')->with('notification');
    }
    public function GetOrderInstallJson() {
        $response = null;
        try
        {
            if ($this->site_id == '') {
                $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . 'order/list_install_order?dari='.$_GET['dari'].'&sampai='.$_GET['sampai']]);     
            }else{
                $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . 'order/list_install_order?site_id='.$this->site_id.'&dari='.$_GET['dari'].'&sampai='.$_GET['sampai']]); 
            }
            $response = $client->request('GET', '', ['headers' => $headers]); 
            $body = $response->getBody();
            $content = $body->getContents();
            $response_array = json_decode($content,TRUE);

            $response = $content;  
            $data=DataTables::of($response_array['data'])
                                    ->addColumn('action', function ($row) {
                                        return '<button type="button" id="modal_detail" class="btn btn-success btn-sm" data-toggle="modal" data-id="'.$row['id'].'" data-no="'.$row['no'].'" data-target="#modal_instal_order" onclick="getDetailInstall(this)"><i class="mdi mdi-eye"></i></button>'.'
                                        
                                        '.'<a href="'.url('/order/delete_install/'.$row['id']).'" class="btn btn-danger btn-sm" onclick="javasciprt: return confirm(\'Are You Sure ?\')"><i class="mdi mdi-delete"></i></a>'.($row['is_closed'] == false ?  '&nbsp;<a href="'.url('/order/close_install/'.$row['id']).'" class="btn btn-info btn-sm" onclick="javasciprt: return confirm(\'Are You Sure ?\')"><i class="mdi mdi-recycle"></i></a>' : '');
                                    })
                                    ->rawColumns(['order_name', 'action'])
                                    ->make(true);          
        } catch(RequestException $exception) {
            
        }    

        return $data;
    }

    public function GetInstallOrderBillJson() {
        $response = null;
        $message = '';
        try
        {
            if ($this->site_id == '') {
                $headers = [
                    'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                    'Accept'        => 'application/json',
                ];
                $client = new Client(['base_uri' => $this->base_api_url . "order/list_install_order?dari=$_GET[dari]&sampai=$_GET[sampai]"]);     
            }else{
                $headers = [
                    'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                    'Accept'        => 'application/json',
                ];
                $client = new Client(['base_uri' => $this->base_api_url . "order/list_install_order?site_id=".$this->site_id."&dari=$_GET[dari]&sampai=$_GET[sampai]"]); 
            }

            $response = $client->request('GET', '', ['headers' => $headers]); 
            
            $body = $response->getBody();
            $content = $body->getContents();
            $response_array = json_decode($content,TRUE);

            $response = $content;  
            // return $response_array['data'];
            foreach ($response_array['data'] as $key => $value) {
                $total=$this->countTotalOrderInstall($value['id']);
                $sub_total=$total['total_installation'];
                $ppn=$sub_total * (1/10);
                $response_array['data'][$key]['total']=$sub_total + $ppn;
            }
            
            $data=DataTables::of($response_array['data'])
                                    ->addColumn('action', function ($row) {
                                        return $row['paid_off_date'] != null ? '<a href="'.url('/order/bill_install/'.$row['id']).'" class="btn btn-info btn-sm">Detail Tagihan</a>' : '<a href="'.url('/order/bill_install/'.$row['id']).'" class="btn btn-success btn-sm"><i class="mdi mdi-plus"></i></a>';
                                    })
                                    ->rawColumns(['order_name', 'action'])
                                    ->make(true); 
            $message = 'sukses';         
        } catch(RequestException $exception) {
            $message = 'fail'.$exception->getMessage();
        }    

        return $data;
    }

    private function countTotalOrderInstall($id){
        $query=DB::table('install_order_ds')
                    ->join('products', 'products.id', 'install_order_ds.product_id')
                    ->select(DB::raw('(install_order_ds.total * products.amount_set) * products.price as total_product'), DB::raw('(install_order_ds.total * products.amount_set) * products.installation_fee as total_installation'))
                    ->where('install_order_ds.install_order_id', $id)
                    ->get();
        $total_produk=$total_instalasi=0;
        foreach ($query as $key => $value) {
            $total_produk+=0;
            $total_instalasi+=$value->total_installation;
        }
        return array('total_product' => $total_produk, 'total_installation' => $total_instalasi, 'ppn' => ($total_produk + $total_instalasi) * (1/10));
    }
    public function billInstallForm($id)
    {
        
        // try
        // {
        // $client = new Client(['base_uri' => $this->base_api_url . 'inv/base/InstallOrder/' . $id]);  
        //     $response = $client->request('GET', '', ['headers' => $headers]); 
        //     $body = $response->getBody();
        //     $content = $body->getContents();
        //     $response_array = json_decode($content,TRUE);
            
        //     $install_order = $response_array['data'];
        // } catch(RequestException $exception) {    
        // }

        try
        {
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . 'order/list_order_d/' . $id]);  
            $response = $client->request('GET', '', ['headers' => $headers]); 
            $body = $response->getBody();
            $content = $body->getContents();
            $response_array = json_decode($content,TRUE);
            
            $install_order_d = $response_array['data'];
        } catch(RequestException $exception) {    
        }
        $install_order=DB::table('install_orders')->join('orders', 'install_orders.order_id', 'orders.id')->select('install_orders.*', 'orders.order_no', 'orders.spk_number')->where('install_orders.id', $id)->first();
        try
        {
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . 'inv/base/Customer/' . $install_order->customer_id]);  
            $response = $client->request('GET', '', ['headers' => $headers]); 
            $body = $response->getBody();
            $content = $body->getContents();
            $response_array = json_decode($content,TRUE);
            
            $customer = $response_array['data'];
        } catch(RequestException $exception) {    
        }
        $customer_bill=DB::table('customer_bills')->where('install_order_id', $id)->orderBy('id')->get();
        $customer_bill_other=DB::table('customer_bill_others')->where('install_order_id', $id)->get();
        $list_bank=DB::table('list_bank')->get();
        //$lastInv = DB::table('customer_bills')->select('bill_no')->orderBy('id','desc')->limit(1)->get();
        //$inv = count($lastInv)==0 ? '00000001' : sprintf("%08s",$lastInv[0]->bill_no+1);
        //$inv = count($lastInv)==0 ? '00000001' : sprintf($lastInv[0]->invoice_no+1);
        $lastInv = DB::table('customer_bills')->select('invoice_no')->orderBy('id','desc')->limit(1)->get();
        $inv = count($lastInv)==0 ? '00000001' : sprintf($lastInv[0]->invoice_no+1);
        
        
        $data = array(
            'customer' => $customer,
            'install_order' => $install_order,
            'install_order_d' => $install_order_d,
            'tagihan' => $this->countTotalOrderInstall($id),
            'customer_bill' => $customer_bill,
            'customer_bill_other' => $customer_bill_other,
            'list_bank' => $list_bank,
            'inv' => $inv
        );
        return view('pages.inv.order.bill_install_customer_form', $data);
    }
    public function saveBillInstallPost(Request $request){
        $total=$this->currency($request->input('total'));
        $total_addendum=$this->currency($request->input('total_addendum'));
        $sub_total=$request->input('sub_total');
        $period_year = date('Y');
        $period_month = date('m');
        $rabcon = new RabController();
        $bill_no = $rabcon->generateTransactionNo('BILL', $period_year, $period_month, $this->site_id );
        $bill_cust=array();
        try
        {
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . 'inv/base/CustomerBill']);
            $reqBody = [
                'headers' => $headers,
                'json' => [
                    'customer_id' => $request->input('customer_id'),
                    'install_order_id' => $request->input('install_order_id'),
                    'bill_no' => $request->input('no'),
                    'invoice_no' => $request->input('invoice_no'),
                    'bill_address' => $request->input('address'),
                    'amount' => $this->currency($request->input('total')),
                    'description' => $request->input('deskripsi'),
                    'due_date' => $request->input('due_date'),
                    'create_date'   => $request->date_create,
                    'no'  => $bill_no,
                    'notes' => $request->input('notes'),
                    'with_pph' => $request->input('with_pph') ? 1 : 0,
                    'end_payment'   => ($total == $sub_total ? 1 : 0)
                ]
            ]; 
            
            $response = $client->request('POST', '', $reqBody); 
            $body = $response->getBody();
            $content = $body->getContents();
            $response_array = json_decode($content,TRUE);
            $bill_cust=$response_array['data'];
        } catch(RequestException $exception) {
        }
        $install_order=DB::table('install_orders')->where('id', $request->input('install_order_id'))->first();
        $total_all=($total == $sub_total ? ($total) : $total);
        $total_ppn=$total_all * 0.1;
        $total_pph=$request->input('with_pph') ? ($total_all * 0.02) : 0;
        // $total_all-=$total_pph;
        
        // $account_project=DB::table('account_projects')->where('order_id', $request->input('order_id'))->first();
        $input_jurnal=array(
            'install_order_id' => $request->input('install_order_id'),
            'customer_id'   => $install_order->customer_id,
            'customer_bill_id'   => $bill_cust['id'],
            'total' => $total_all,
            'ppn' => $total_ppn,
            'pph' => $total_pph,
            'paid_more' => 0,
            'type_ppn' => 'DEBIT',
            'user_id'   => $this->user_id,
            'deskripsi'     => 'Pembuatan Tagihan '.$request->input('deskripsi').' No Order Instalasi '.$install_order->no,
            'tgl'       => $request->date_create,
            'akun'      => 151,
            'lawan'      => 170,
            'location_id'   => $this->site_id
        );
        $this->journalOrderInstallPayment($input_jurnal);
        if ($total == $sub_total) {
            try
            {
                $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . 'inv/base/InstallOrder/'.$request->input('install_order_id')]);
                $reqBody = [
                    'headers' => $headers,
                'json' => [
                        'paid_off_date' => date('Y-m-d H:i:s'),
                    ]
                ]; 
                
                $response = $client->request('PUT', '', $reqBody); 
            } catch(RequestException $exception) {
            }
        }
        
        return redirect('order/bill_install/'.$request->input('install_order_id'));
    }
    public function saveBillInstallDetailPost(Request $request){
        $total=$this->currency($request->input('total_bill'));
        $total_awal=$request->input('total_awal');
        $total_min=$request->input('total_min');
        $total_ppn=$request->input('total_ppn');
        $total_pph=$request->input('total_pph');
        $paid_more=$request->input('paid_more');
        $period_year = date('Y');
        $period_month = date('m');
        $rabcon = new RabController();
        $bill_no = $rabcon->generateTransactionNo('PAY_BILL', $period_year, $period_month, $this->site_id );
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
                    'customer_bill_id' => $request->input('bill_id'),
                    'wop' => $request->input('wop'),
                    'bank_number' => $request->input('bank_number'),
                    'atas_nama' => $request->input('atas_nama'),
                    'ref_code' => $request->input('ref_code'),
                    'id_bank' => $request->input('id_bank'),
                    'amount' => $total,
                    'pay_date' => date('Y-m-d'),
                    'no'  => $bill_no
                ]
            ]; 
            $response = $client->request('POST', '', $reqBody); 
            $body = $response->getBody();
            $content = $body->getContents();
            $response_array = json_decode($content,TRUE);
        } catch(RequestException $exception) {
        }
        if ($total >= $total_min) {
            try
            {
                $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . 'inv/base/CustomerBill/'.$request->input('bill_id')]);
                
                $reqBody = [
                    'headers' => $headers,
                'json' => [
                        'is_paid' => 1,
                    ]
                ]; 
                $response = $client->request('PUT', '', $reqBody); 
            } catch(RequestException $exception) {
            }
        }
        try
        {
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . 'inv/base/CustomerBill/'.$request->input('bill_id')]);
            
            $response = $client->request('GET', '', ['headers' => $headers]); 
            $body = $response->getBody();
            $content = $body->getContents();
            $response_array = json_decode($content,TRUE);
            $cust_bill=$response_array['data'];
        } catch(RequestException $exception) {
        }
        if ($request->wop == 'giro') {
            $giro_no = $rabcon->generateTransactionNo('GIRO', $period_year, $period_month, $this->site_id );
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
                        'customer_bill_id' => $request->input('bill_id'),
                        'customer_bill_d_id' => $cust_bill_d['id'],
                        'install_order_id' => $cust_bill['install_order_id'],
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
        $install_order=DB::table('install_orders')->where('id', $cust_bill['install_order_id'])->first();
        $input_jurnal=array(
            'install_order_id' => $request->input('install_order_id'),
            'customer_id'   => $install_order->customer_id,
            'total' => $total_awal + $total_ppn + $total_pph,
            'ppn' => $total_ppn,
            'pph' => $total_pph,
            'paid_more' => $paid_more,
            'type_ppn' => 'KREDIT',
            'user_id'   => $this->user_id,
            'deskripsi'     => 'Pembayaran Tagihan '.$cust_bill['description'].' No Order Instalasi '.$install_order->no,
            'tgl'       => date('Y-m-d'),
            'akun'      => $request->input('wop') == 'giro' ? 36 : $request->account_payment,
            'lawan'      => 151,
            'location_id'   => $this->site_id
        );
        $this->journalOrderInstallPayment($input_jurnal);
        $query=DB::table('customer_bills')->where('install_order_id', $cust_bill['install_order_id'])->where('end_payment', 'true')->first();
        if ($query != null) {
            $cek=DB::table('customer_bills')->where('install_order_id', $cust_bill['install_order_id'])->select(DB::raw('COUNT(id) as total_bill'), DB::raw('SUM(CASE WHEN is_paid = true THEN 1 ELSE 0 END) as total_paid'))->first();
            if ($cek->total_bill == $cek->total_paid) {
                try
                {
                    $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . 'inv/base/InstallOrder/'.$cust_bill['install_order_id']]);
                    
                    $reqBody = [
                        'headers' => $headers,
                'json' => [
                            'is_done' => 1,
                        ]
                    ]; 
                    $response = $client->request('PUT', '', $reqBody); 
                } catch(RequestException $exception) {
                }
            }
            $trx_akuntan=DB::table('tbl_trx_akuntansi as tra')
                            ->join('tbl_trx_akuntansi_detail as trd', 'tra.id_trx_akun', 'trd.id_trx_akun')
                            ->join('tbl_akun as ta', 'trd.id_akun', 'ta.id_akun')
                            ->select(DB::raw("SUM(CASE WHEN tipe = 'DEBIT' THEN jumlah ELSE 0 END) AS total_debit"), DB::raw("SUM(CASE WHEN tipe = 'KREDIT' THEN jumlah ELSE 0 END) AS total_kredit"), 'trd.id_akun', DB::raw('MAX(sifat_debit) as sifat_debit'), DB::raw('MAX(sifat_kredit) as sifat_kredit'), DB::raw('MAX(nama_akun) as nama_akun'))
                            // ->whereIn('tra.project_req_development_id', $project_req_developments)
                            ->whereIn('trd.id_akun', [170])
                            ->where('tra.install_order_id', $cust_bill['install_order_id'])
                            ->groupBy('trd.id_akun')
                            ->first();
            $install_orders=DB::table('install_orders')->where('id', $cust_bill['install_order_id'])->first();
            $input_jurnal=array(
                'install_order_id' => $cust_bill['install_order_id'],
                'total' => $trx_akuntan->total_kredit - $trx_akuntan->total_debit,
                'user_id'   => $this->user_id,
                'deskripsi'     => 'Jurnal Pendapatan No Order Instalasi '.$install_orders->no,
                'tgl'       => date('Y-m-d'),
                'akun'      => 170,
                'lawan'      => 178,
                'location_id'   => $this->site_id
            );
            $this->journalPendapatanProyekInstallasi($input_jurnal);
        }
        return redirect('order/list_tagihan');
    }
    private function journalPendapatanProyekInstallasi($data){
        
        $data_trx=array(
            'deskripsi'     => $data['deskripsi'],
            'location_id'     => $data['location_id'],
            'tanggal'       => $data['tgl'],
            'user_id'       => $data['user_id'],
            'install_order_id'   => $data['install_order_id']
        );
        $insert=DB::table('tbl_trx_akuntansi')->insert($data_trx);
        if ($insert) {
            $id_last=DB::table('tbl_trx_akuntansi')->max('id_trx_akun');
            $akun=array(
                'id_trx_akun'   => $id_last,
                'id_akun'       => $data['akun'],
                'jumlah'        => $data['total'],
                'tipe'          => "DEBIT",
                'keterangan'    => 'akun',
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
                'jumlah'        => $data['total'],
                'tipe'          => "KREDIT",
                'keterangan'    => 'lawan',
            );
            DB::table('tbl_trx_akuntansi_detail')->insert($lawan);
            if($data['lawan'] == 24 || $data['lawan'] == 101){
                $acccon = new AkuntanController();
                $warehouse_id=$data['lawan'] == 24 ? 2 : 3;
                $acccon->countSaldoCash($this->site_id, $warehouse_id, "out", $data['total']);
            }
        }
    }
    public function printBillInstall($id)
    {
        try
        {
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . 'inv/base/CustomerBill/'.$id]);
            
            $response = $client->request('GET', '', ['headers' => $headers]); 
            $body = $response->getBody();
            $content = $body->getContents();
            $response_array = json_decode($content,TRUE);
            $cust_bill=$response_array['data'];
        } catch(RequestException $exception) {
        }
        try
        {
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . 'inv/base/InstallOrder/'.$cust_bill['install_order_id']]);
            
            $response = $client->request('GET', '', ['headers' => $headers]); 
            $body = $response->getBody();
            $content = $body->getContents();
            $response_array = json_decode($content,TRUE);
            $install_order=$response_array['data'];
        } catch(RequestException $exception) {
        }
        try
        {
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . 'inv/base/Customer/'.$install_order['customer_id']]);
            
            $response = $client->request('GET', '', ['headers' => $headers]); 
            $body = $response->getBody();
            $content = $body->getContents();
            $response_array = json_decode($content,TRUE);
            $customer=$response_array['data'];
        } catch(RequestException $exception) {
        }
        $cust_bill_other=DB::table('customer_bill_others')->where('install_order_id', $cust_bill['install_order_id'])->get();
        $order=DB::table('orders')->where('id', $install_order['order_id'])->first();
        $data=array(
            'cust_bill' => $cust_bill,
            'cust_bill_other' => $cust_bill_other,
            'install_order'     => $install_order,
            'order'     => $order,
            'customer'  => $customer
        );
        return view('pages.inv.order.print_bill_install_order', $data);
    }
    public function printBillKwitansiInstall($id)
    {
        $cust_bill_d=DB::table('customer_bill_ds')->where('id', $id)->first();
        try
        {
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . 'inv/base/CustomerBill/'.$cust_bill_d->customer_bill_id]);
            
            $response = $client->request('GET', '', ['headers' => $headers]); 
            $body = $response->getBody();
            $content = $body->getContents();
            $response_array = json_decode($content,TRUE);
            $cust_bill=$response_array['data'];
        } catch(RequestException $exception) {
        }
        try
        {
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . 'inv/base/InstallOrder/'.$cust_bill['install_order_id']]);
            
            $response = $client->request('GET', '', ['headers' => $headers]); 
            $body = $response->getBody();
            $content = $body->getContents();
            $response_array = json_decode($content,TRUE);
            $install_order=$response_array['data'];
        } catch(RequestException $exception) {
        }
        try
        {
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . 'inv/base/Customer/'.$install_order['customer_id']]);
            
            $response = $client->request('GET', '', ['headers' => $headers]); 
            $body = $response->getBody();
            $content = $body->getContents();
            $response_array = json_decode($content,TRUE);
            $customer=$response_array['data'];
        } catch(RequestException $exception) {
        }
        $data=array(
            'cust_bill' => $cust_bill,
            'cust_bill_d' => $cust_bill_d,
            'install_order'     => $install_order,
            'customer'  => $customer
        );
        // return $data;
        return view('pages.inv.order.print_kwitansi_install', $data);
    }
    public function GetOrderInstallDetailJson($id) {
        $response = null;
        try
        {
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . 'order/list_order_d/'.$id]); 
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
    public function closeOrderInstall($id){
        // $project_req_developments=DB::table('project_req_developments')->where('order_id', $id)->pluck('id');
        // $account_project=DB::table('account_projects')->where('order_id', $id)->first();
        $trx_akuntan=DB::table('tbl_trx_akuntansi as tra')
                        ->join('tbl_trx_akuntansi_detail as trd', 'tra.id_trx_akun', 'trd.id_trx_akun')
                        ->join('tbl_akun as ta', 'trd.id_akun', 'ta.id_akun')
                        ->select(DB::raw("SUM(CASE WHEN tipe = 'DEBIT' THEN jumlah ELSE 0 END) AS total_debit"), DB::raw("SUM(CASE WHEN tipe = 'KREDIT' THEN jumlah ELSE 0 END) AS total_kredit"), 'trd.id_akun', DB::raw('MAX(sifat_debit) as sifat_debit'), DB::raw('MAX(sifat_kredit) as sifat_kredit'), DB::raw('MAX(nama_akun) as nama_akun'))
                        ->where('tra.install_order_id', $id)
                        ->whereIn('trd.id_akun', [169])
                        ->groupBy('trd.id_akun')
                        ->get();
        
        $install_order=DB::table('install_orders')->where('id', $id)->first();
        
        foreach ($trx_akuntan as $key => $value) {
            $input_jurnal=array(
                'install_order_id' => $id,
                'total' => ($value->total_debit - $value->total_kredit),
                'user_id'   => $this->user_id,
                'deskripsi'     => 'Jurnal HPP No Order Instalasi '.$install_order->no,
                'tgl'       => date('Y-m-d'),
                'akun'      => 84,
                'lawan'      => $value->id_akun,
                'location_id'   => $this->site_id
            );
            $this->journalPendapatanProyekInstallasi($input_jurnal);
        }
        try
        {
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . 'inv/base/InstallOrder/'.$id]);
            $reqBody = [
                'headers' => $headers,
                'json' => [
                    'is_closed' => true,
                ]
            ]; 
            
            $response = $client->request('PUT', '', $reqBody); 
        } catch(RequestException $exception) {
        }
        return redirect('order');
    }
    public function deleteOrderInstall($id) {
        try
        {
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . 'inv/base/InstallOrder/' . $id]);  
            $response = $client->request('DELETE', '', ['headers' => $headers]); 
        } catch(RequestException $exception) {    
        }

        $notification = array(
            'message' => 'Success delete order installation',
            'alert-type' => 'success'
        );

        return redirect('order')->with($notification);
    }
    public function savePaidPost(Request $request){
        $total=$this->currency($request->input('total'));
        // $total_addendum=$this->currency($request->input('total_addendum'));
        $total_ppn=$this->currency($request->input('ppn'));
        $sub_total=$request->input('sub_total');
        $period_year = date('Y');
        $period_month = date('m');
        $rabcon = new RabController();
        $bill_no = $rabcon->generateTransactionNo('PAID', $period_year, $period_month, $this->site_id );
        try
        {
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . 'inv/base/CustomerPaid']);
            $reqBody = [
                'headers' => $headers,
                'json' => [
                    'customer_id' => $request->input('customer_id'),
                    'order_id' => $request->input('order_id'),
                    'wop' => $request->input('wop'),
                    'bank_number' => $request->input('bank_number'),
                    'atas_nama' => $request->input('atas_nama'),
                    'ref_code' => $request->input('ref_code'),
                    'id_bank' => $request->input('id_bank'),
                    'amount' => $this->currency($request->input('total')),
                    'due_date' => $request->input('pay_date'),
                    'no'  => $bill_no,
                    'notes' => $request->input('notes'),
                ]
            ]; 
            
            $response = $client->request('POST', '', $reqBody); 
            $body = $response->getBody();
            $content = $body->getContents();
            $response_array = json_decode($content,TRUE);
        } catch(RequestException $exception) {
        }
        $order=DB::table('orders')->where('id', $request->input('order_id'))->first();
        
        // $account_project=DB::table('account_projects')->where('order_id', $request->input('order_id'))->first();
        $input_jurnal=array(
            'order_id' => $request->input('order_id'),
            'customer_id' => $order->customer_id,
            'total' => $total,
            'ppn' => 0,
            'type_ppn' => 'DEBIT',
            'user_id'   => $this->user_id,
            'deskripsi'     => 'Pembayaran No Order '.$order->order_no,
            'tgl'       => date('Y-m-d'),
            'akun'      => 20,
            // 'lawan'      => $account_project->dp_id,
            'lawan'      => 5759, //uang muka proyek
            'location_id'   => $this->site_id
        );
        $this->journalOrderPayment($input_jurnal);
        return redirect('order/bill/'.$request->input('order_id'));
    }
    public function getCustProjectOrder($id) {
        $project=DB::table('orders')->where('customer_project_id', $id)->get();
        $data=array(
            'data'  => $project
        );
        return $data;
    }
    public function getBillOrder(){
        $site_id = auth()->user()['site_id'];
        $query=DB::table('customer_bills as cb')
                    ->join('orders', 'orders.id', 'cb.order_id')
                    ->join('customers', 'orders.customer_id', 'customers.id')
                    ->leftJoin('tbl_trx_akuntansi as tra', 'tra.customer_bill_id', 'cb.id')
                    ->select('orders.order_no', 'orders.spk_number', 'orders.id as order_id', 'cb.*', 'tra.id_trx_akun', 'customers.coorporate_name', DB::raw("COALESCE((SELECT SUM(amount) from customer_bill_others where order_id=orders.id and description='addendum'), 0) - COALESCE((SELECT SUM(amount) from customer_bill_others where order_id=orders.id and description='discount_payment'), 0) AS total_adendum"))
                    ->where('orders.site_id', $site_id)
                    ->whereBetween('due_date',[$_GET['dari'],$_GET['sampai']])
                    ->get();
        foreach($query as $row){
            $aft_ppn=$row->amount + ($row->amount * 0.1);
            $row->amount=$aft_ppn;
        }
        $data=DataTables::of($query)
                    ->addColumn('action', function($row){
                        return ($row->id_trx_akun != null && $row->is_paid == false? '<a href="'.url("akuntansi/delete_jurnal/".$row->id_trx_akun).'" class="btn btn-danger btn-sm" title="hapus tagihan"><i class="mdi mdi-delete"></i></a>&nbsp;' : '').'<a href="'.url("order/print_bill/".$row->id).'" class="btn btn-success btn-sm" target="_blank" title="print invoice"><i class="mdi mdi-printer"></i></a>&nbsp;<a href="'.url("order/print_kwitansi_other/".$row->id).'" class="btn btn-danger btn-sm" target="_blank" title="print kwitansi"><i class="mdi mdi-printer"></i></a>&nbsp;<button onclick="doShowDetail(this);" data-toggle="modal" data-no="'.$row->no.'" data-id="'.$row->id.'" data-amount="'.$row->amount.'" data-order_id="'.$row->order_id.'" data-end_payment="'.$row->end_payment.'" data-total_adendum="'.$row->total_adendum.'" data-target="#modalBillDetail" class="btn waves-effect waves-light btn-sm btn-info"><i class="mdi mdi-credit-card-plus"></i></button>';
                    })
                    ->make(true); 

        return $data;
    }
    public function getBillInstallOrder(){
        $site_id = auth()->user()['site_id'];
        $query=DB::table('customer_bills as cb')
                    ->join('install_orders', 'install_orders.id', 'cb.install_order_id')
                    ->leftJoin('orders', 'install_orders.order_id', 'orders.id')
                    ->leftJoin('tbl_trx_akuntansi as tra', 'tra.customer_bill_id', 'cb.id')
                    ->join('customers', 'install_orders.customer_id', 'customers.id')
                    ->select('install_orders.no as order_no', 'install_orders.spk_no', 'orders.spk_number', 'cb.*', 'customers.coorporate_name', 'tra.id_trx_akun')
                    ->where('install_orders.site_id', $site_id)
                    ->whereBetween('due_date',[$_GET['dari'],$_GET['sampai']])
                    ->get();
        foreach($query as $row){
            $aft_ppn=$row->amount + (($row->amount * 0.1) - ($row->with_pph == true ? ($row->amount * 0.02) : 0));
            $row->amount=$aft_ppn;
        }
        $data=DataTables::of($query)
                    ->addColumn('action', function($row){
                        return ($row->id_trx_akun != null && $row->is_paid == false ? '<a href="'.url("akuntansi/delete_jurnal/".$row->id_trx_akun).'" class="btn btn-danger btn-sm" title="hapus tagihan"><i class="mdi mdi-delete"></i></a>&nbsp;' : '').'<a href="'.url("order/print_bill_install/".$row->id).'" class="btn btn-success btn-sm" target="_blank"><i class="mdi mdi-printer"></i></a>&nbsp;<a href="'.url("order/print_kwitansi_other/".$row->id).'" class="btn btn-danger btn-sm" target="_blank" title="print kwitansi"><i class="mdi mdi-printer"></i></a>&nbsp;<button onclick="doShowDetail(this);" data-toggle="modal" data-no="'.$row->no.'" data-id="'.$row->id.'" data-amount="'.$row->amount.'" data-install_order_id="'.$row->install_order_id.'" data-end_payment="'.$row->end_payment.'" data-target="#modalBillDetail" class="btn waves-effect waves-light btn-sm btn-info"><i class="mdi mdi-credit-card-plus"></i></button>';
                    })
                    ->make(true); 

        return $data;
    }
    public function listTagihan(){
        $is_error = false;
        $error_message = '';  

        $data = array(
            'error' => array(
                'is_error' => $is_error,
                'error_message' => $error_message
            ),
            'list_bank' => isset($_GET['dari']) ?  DB::table('list_bank')->get() : '',
        );
        
        return view('pages.inv.order.daftar_tagihan', $data);
    }
    public function saveWorkTemp(Request $request){
        $order_d_id=$request->order_d_id;
        $work_id=$request->work_id;
        $price=$request->price;
        $query=DB::table('temp_worksubs')->where('order_d_id', $order_d_id)->whereNotIn('worksub_id', $work_id)->delete();
        foreach ($work_id as $key => $value) {
            $cek=DB::table('temp_worksubs')->where('order_d_id', $order_d_id)->where('worksub_id', $value)->first();
            try
            {
                $reqBody = [
                    'headers' => $headers,
                    'json' => [
                        'order_d_id' => $order_d_id,
                        'worksub_id' => $value,
                        'price' => $price[$key],
                    ]
                ]; 
                if ($cek == null) {
                    $headers = [
                        'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                        'Accept'        => 'application/json',
                    ];
                    $client = new Client(['base_uri' => $this->base_api_url . 'inv/base/TempWorksub']);
                    $response = $client->request('POST', '', $reqBody); 
                }else{
                    $headers = [
                        'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                        'Accept'        => 'application/json',
                    ];
                    $client = new Client(['base_uri' => $this->base_api_url . 'inv/base/TempWorksub/'.$cek->id]);
                    $response = $client->request('PUT', '', $reqBody); 
                }
            } catch(RequestException $exception) {
            }
        }
    }
    public function getWorkInstall($id){
        $query=DB::table('temp_worksubs')->join('worksubs', 'worksubs.id', 'temp_worksubs.worksub_id')->select('temp_worksubs.*', 'worksubs.name')->where('order_d_id', $id)->get();
        $data=array(
            'data'  => $query
        );
        return $data;
    }
    public function getWorkInstallOrder($id){
        $query=DB::table('install_worksubs')->join('worksubs', 'worksubs.id', 'install_worksubs.worksub_id')->select('install_worksubs.*', 'worksubs.name')->where('install_order_d_id', $id)->get();
        $data=array(
            'data'  => $query
        );
        return $data;
    }
    public function editSPJB(Request $request){
        $id=$request->id;
        try
        {
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $reqBody = [
                'headers' => $headers,
                'json' => [
                    'spk_number' => $request->spk_no,
                ]
            ]; 
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . 'inv/base/Order/'.$id]);
            $response = $client->request('PUT', '', $reqBody); 
        } catch(RequestException $exception) {
        }
        return redirect('order');
    }
    public function editSPK(Request $request){
        $id=$request->id;
        try
        {
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $reqBody = [
                'headers' => $headers,
                'json' => [
                    'spk_no' => $request->spk_no,
                ]
            ]; 
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . 'inv/base/InstallOrder/'.$id]);
            $response = $client->request('PUT', '', $reqBody); 
        } catch(RequestException $exception) {
        }
        return redirect('order');
    }
    public function listBillCustomer(){
        $is_error = false;
        $error_message = '';  

        $data = array(
            'error' => array(
                'is_error' => $is_error,
                'error_message' => $error_message
            ),
        );
        
        return view('pages.inv.order.daftar_penagihan', $data);
    }
    public function listBillCustomerJson(){
        $data=DB::table('customer_bills as cb')->whereNull('cb.deleted_at')
                    ->join('customers as c', 'c.id', 'cb.customer_id')
                    ->select('cb.*', 'c.coorporate_name')
                    ->get();
        foreach($data as $dd){
            $dt=DB::table('customer_bill_histories')
                    ->select('*')
                    ->whereRaw('id = (select max(id) from customer_bill_histories where customer_bill_id='.$dd->id.')')
                    ->first();
            $dd->status=$dt != null ? $dt->status_bill : null;
            $dd->reason_of_bill=$dt != null ? $dt->reason_of_bill : null;
        }
        
        $data=DataTables::of($data)
                                ->make(true);    
        return $data;
    }
    public function reportFollowup(Request $request){
        $id=$request->cb_id;
        try
        {
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . 'inv/base/CustomerBillHistorie']);
            $reqBody = [
                'headers' => $headers,
                'json' => [
                    'customer_bill_id' => $id,
                    'date_bill' => date('Y-m-d H:i:s'),
                    'status_bill' => $request->status,
                    'reason_of_bill' => $request->alasan,
                    'user_id' => $this->user_id,
                ]
            ]; 
            $response = $client->request('POST', '', $reqBody); 
        } catch(RequestException $exception) {
        }
        return 1;
    }
    public function listFollowUpJson($id){
        $data=DB::table('customer_bill_histories as cbh')
                    ->join('users as u', 'cbh.user_id', 'u.id')
                    ->select('cbh.*', 'u.name')
                    ->where('customer_bill_id', $id)
                    ->orderBy('id')
                    ->get();
        
        $data=DataTables::of($data)
                                ->make(true);    
        return $data;
    }
    public function printKwitansiOther($id)
    {
        try
        {
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . 'inv/base/CustomerBill/'.$id]);
            
            $response = $client->request('GET', '', ['headers' => $headers]); 
            $body = $response->getBody();
            $content = $body->getContents();
            $response_array = json_decode($content,TRUE);
            $cust_bill=$response_array['data'];
        } catch(RequestException $exception) {
        }
        if($cust_bill['order_id'] != null){
            $get_cust=DB::table('orders')->where('id', $cust_bill['order_id'])->first();
        }else{
            $get_cust=DB::table('install_orders')->where('id', $cust_bill['install_order_id'])->first();
        }
        try
        {
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . 'inv/base/Customer/'.$get_cust->customer_id]);
            
            $response = $client->request('GET', '', ['headers' => $headers]); 
            $body = $response->getBody();
            $content = $body->getContents();
            $response_array = json_decode($content,TRUE);
            $customer=$response_array['data'];
        } catch(RequestException $exception) {
        }
        $cust_bill_other=DB::table('customer_bill_others')->where('order_id', $cust_bill['order_id'])->get();
        $data=array(
            'cust_bill' => $cust_bill,
            'cust_bill_other' => $cust_bill_other,
            'customer'  => $customer
        );
        
        return view('pages.inv.order.print_kwitansi_other', $data);
    }
    public function export()
    {
        $response = null;
        try
        {
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            if ($this->site_id == '') {
                $client = new Client(['base_uri' => $this->base_api_url . 'order/list?dari='.$_GET['dari'].'&sampai='.$_GET['sampai']]);     
            }else{
                
                $client = new Client(['base_uri' => $this->base_api_url . 'order/list?site_id='.$this->site_id.'&dari='.$_GET['dari'].'&sampai='.$_GET['sampai']]); 
            }
            $response = $client->request('GET', '', ['headers' => $headers]); 
            $body = $response->getBody();
            $content = $body->getContents();
            $response_array = json_decode($content,TRUE);

            
        } catch(RequestException $exception) {
            
        }   
        
        return Excel::download(new OrderExport($response_array), 'daftar pengadaan.xlsx');
    }
    public function exportInstall()
    {
        $response = null;
        try
        {
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            if ($this->site_id == '') {
                $client = new Client(['base_uri' => $this->base_api_url . 'order/list_install_order?dari='.$_GET['dari'].'&sampai='.$_GET['sampai']]);     
            }else{
                $client = new Client(['base_uri' => $this->base_api_url . 'order/list_install_order?site_id='.$this->site_id.'&dari='.$_GET['dari'].'&sampai='.$_GET['sampai']]); 
            }
            $response = $client->request('GET', '', ['headers' => $headers]); 
            $body = $response->getBody();
            $content = $body->getContents();
            $response_array = json_decode($content,TRUE);
            foreach ($response_array['data'] as $key => $value) {
                $total=$this->countTotalOrderInstall($value['id']);
                $sub_total=$total['total_installation'];
                $ppn=$sub_total * (1/10);
                $response_array['data'][$key]['total']=$sub_total + $ppn;
            }
            
        } catch(RequestException $exception) {
            
        }   
        return Excel::download(new InstallOrderExport($response_array), 'daftar pemasangan.xlsx');
    }
    public function exportBillOrder()
    {
        $site_id = auth()->user()['site_id'];
        $query=DB::table('customer_bills as cb')
                    ->join('orders', 'orders.id', 'cb.order_id')
                    ->join('customers', 'orders.customer_id', 'customers.id')
                    ->leftJoin('tbl_trx_akuntansi as tra', 'tra.customer_bill_id', 'cb.id')
                    ->select('orders.order_no', 'orders.spk_number', 'orders.id as order_id', 'cb.*', 'tra.id_trx_akun', 'customers.coorporate_name', DB::raw("COALESCE((SELECT SUM(amount) from customer_bill_others where order_id=orders.id and description='addendum'), 0) - COALESCE((SELECT SUM(amount) from customer_bill_others where order_id=orders.id and description='discount_payment'), 0) AS total_adendum"))
                    ->where('orders.site_id', $site_id)
                    ->orderBy('cb.create_date')
                    ->get();
        foreach($query as $row){
            $aft_ppn=$row->amount + ($row->amount * 0.1);
            $row->amount=$aft_ppn;
        }
        $data=array(
            'data'  => $query
        );
        return Excel::download(new BillOrderExport($data), 'daftar tagihan order customer.xlsx');
    }
    public function exportBillInstallOrder()
    {
        $site_id = auth()->user()['site_id'];
        $query=DB::table('customer_bills as cb')
                    ->join('install_orders', 'install_orders.id', 'cb.install_order_id')
                    ->leftJoin('orders', 'install_orders.order_id', 'orders.id')
                    ->leftJoin('tbl_trx_akuntansi as tra', 'tra.customer_bill_id', 'cb.id')
                    ->join('customers', 'install_orders.customer_id', 'customers.id')
                    ->select('install_orders.no as order_no', 'install_orders.spk_no', 'orders.spk_number', 'cb.*', 'customers.coorporate_name', 'tra.id_trx_akun')
                    ->where('install_orders.site_id', $site_id)
                    ->orderBy('cb.create_date')
                    ->get();
        foreach($query as $row){
            $aft_ppn=$row->amount + (($row->amount * 0.1) - ($row->with_pph == true ? ($row->amount * 0.02) : 0));
            $row->amount=$aft_ppn;
        }
        $data=array(
            'data'  => $query
        );
        return Excel::download(new BillInstallOrderExport($data), 'daftar tagihan install order customer.xlsx');
    }
    public function printBillWord($id)
    {
        try
        {
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . 'inv/base/CustomerBill/'.$id]);
            
            $response = $client->request('GET', '', ['headers' => $headers]); 
            $body = $response->getBody();
            $content = $body->getContents();
            $response_array = json_decode($content,TRUE);
            $cust_bill=$response_array['data'];
        } catch(RequestException $exception) {
        }
        try
        {
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . 'inv/base/Order/'.$cust_bill['order_id']]);
            
            $response = $client->request('GET', '', ['headers' => $headers]); 
            $body = $response->getBody();
            $content = $body->getContents();
            $response_array = json_decode($content,TRUE);
            $order=$response_array['data'];
        } catch(RequestException $exception) {
        }
        try
        {
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . 'inv/base/Customer/'.$order['customer_id']]);
            
            $response = $client->request('GET', '', ['headers' => $headers]); 
            $body = $response->getBody();
            $content = $body->getContents();
            $response_array = json_decode($content,TRUE);
            $customer=$response_array['data'];
        } catch(RequestException $exception) {
        }
        $cust_bill_other=DB::table('customer_bill_others')->where('order_id', $cust_bill['order_id'])->get();
        $data=array(
            'cust_bill' => $cust_bill,
            'cust_bill_other' => $cust_bill_other,
            'order'     => $order,
            'customer'  => $customer
        );
        $view = View::make('pages.inv.order.print_bill_order_word', $data)->render();
        echo(($view));exit;
        // $file_name = strtotime(date('Y-m-d H:i:s')) . '_tagihan.docx';
        // $headers = array(
        //             "Content-type"=>"application/vnd.openxmlformats-officedocument.wordprocessingml.document",
        //             "Content-Disposition"=>"attachment;Filename=$file_name"
        //         );
        // return response()->make($view, 200, $headers);
        $file_name = strtotime(date('Y-m-d H:i:s')) . '_tagihan.docx';
        $phpWord = new \PhpOffice\PhpWord\PhpWord();
        $section = $phpWord->addSection();
        // $section->addText($view);
        \PhpOffice\PhpWord\Shared\Html::addHtml($section, htmlspecialchars($view), false, false);
        $objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');
        ob_clean();
        $objWriter->save(public_path($file_name));
        return response()->download(public_path($file_name));
    }
    public function updateProduct(Request $request)
    {
        $update = array(
            'price' => $request->price,
            'total' => $request->total,
            'updated_at' => date('Y-m-d H:i:s')
        );
        DB::table('order_ds')->where('id',$request->id_detail)->update($update);
        DB::table('orders')->where('id',$request->order_id)->update(['updated_at' => date('Y-m-d H:i:s')]);
        return redirect('/order/edit/'.$request->order_id.'?dari='.$request->dari.'&sampai='.$request->sampai.'#detailOrder')->with('notification');
    }
}
