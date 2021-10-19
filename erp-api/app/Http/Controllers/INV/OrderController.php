<?php
namespace App\Http\Controllers\INV;

use App\Models\Order;
use App\Models\OrderD;
use App\Models\Customer;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\CustomerProject;
use DB;
use App\Models\InstallOrder;
use App\Models\InstallOrderD;
use Illuminate\Database\QueryException;

class OrderController extends Controller
{
    public function getOrderList(Request $request)
    {
        $site_id=$request->get('site_id');
        if ($site_id == '') {
            $data = Order::orderBy('orders.updated_at','desc')->whereBetween('order_date',[$_GET['dari'],$_GET['sampai']])->get();
        }else{
            $data = Order::orderBy('orders.updated_at','desc')->where('site_id', $site_id)->whereBetween('order_date',[$_GET['dari'],$_GET['sampai']])->get();
        }
        
        foreach($data as $value){
            $query=DB::table('order_ds')->join('products', 'products.id', 'order_ds.product_id')->where('order_ds.order_id', $value->id)
            ->select('order_ds.*', 'products.amount_set')->orderBy('order_ds.updated_at','desc')->get();
            $total_kontrak=0;
            foreach ($query as $k => $v) {
                $total=(($v->amount_set * $v->total) * $v->price);
                $total_kontrak+=$total;
            }
            $customer=Customer::find($value->customer_id);
            if($value->customer_project_id != null){
                $project=CustomerProject::find($value->customer_project_id);
                $value->project_name=$project->name;
            }
            $value->customer_name=$customer->name;
            $value->customer_coorporate=$customer->coorporate_name;
            $value->total_kontrak=$total_kontrak;
        }
        return response()->json(['data'=>$data]);
    }   
    public function getOrderListJson($orderId)
    {
        $data = OrderD::where(['order_ds.order_id'=> $orderId])
                ->join('products', 'products.id', '=', 'order_ds.product_id')
                ->leftJoin('kavlings', 'products.kavling_id', '=', 'kavlings.id')
                ->leftJoin('rabs', 'rabs.order_d_id', '=', 'order_ds.id')
                ->select('products.name', 'products.item','products.series','products.panjang', 'products.lebar', 'products.description', 'products.image', 'products.price', 'products.amount_set', 'products.installation_fee','order_ds.id as id_detail','order_ds.*', 'rabs.*', 'kavlings.name as type_kavling')
                ->orderBy('order_ds.created_at','asc')
                ->get();
        return response()->json(['data'=>$data]);
    }
    public function getOrderListJsonNonRab($orderId)
    {
        $data = OrderD::where(['order_ds.order_id'=> $orderId, 'in_rab' => 0])
                ->join('products', 'products.id', '=', 'order_ds.product_id')
                ->leftJoin('kavlings', 'products.kavling_id', '=', 'kavlings.id')
                ->select('products.name', 'products.item','products.series','products.panjang', 'products.lebar', 'products.description', 'products.image', 'products.price', 'products.amount_set', 'products.installation_fee', 'order_ds.*', 'kavlings.name as type_kavling')
                ->orderBy('order_ds.created_at','asc')
                ->get();
        return response()->json(['data'=>$data]);
    }
    public function getOrderDetailById($orderId)
    {
        $data = OrderD::where(['order_id'=> $orderId])
                ->get();
        return response()->json(['data'=>$data]);
    }
    public function getOptionSpkList(Request $request)
    {
        // $site_id=$request->get('site_id');
        // if ($site_id == '') {
        //     $data = Order::orderBy('id', 'DESC')->get();
        // }else{
        //     $data = Order::where('site_id', $site_id)->orderBy('id', 'DESC')->get();
        // }
        $site_id=$request->get('site_id');
        if ($site_id == '') {
            $data = Order::select('spk_number', 'is_done')->where('spk_number', '!=', null)->orderBy('spk_number', 'DESC')->distinct()->get();
        }else{
            $data = Order::select('spk_number', 'is_done')->where('spk_number', '!=', null)->where('site_id', $site_id)->orderBy('spk_number', 'DESC')->distinct()->get();
        }
        return response()->json(['data'=>$data]);
    }
    public function getOptionOrderList(Request $request)
    {
        $site_id=$request->get('site_id');
        if ($site_id == '') {
            $data = Order::orderBy('id', 'DESC')->get();
        }else{
            $data = Order::where('site_id', $site_id)->orderBy('id', 'DESC')->get();
        }
        
        return response()->json(['data'=>$data]);
    }
    public function getOrderInstallList(Request $request)
    {
        $message = '';
        try{
            $site_id=$request->get('site_id');
            if ($request->get('site_id') == '') {
                $data = InstallOrder::select('install_orders.*', 'orders.order_no', 'orders.spk_number')->join('orders', 'orders.id', 'install_orders.order_id')->whereBetween('install_orders.order_date',[$_GET['dari'],$_GET['sampai']])->get();    
            }else{
                $data = InstallOrder::select('install_orders.*', 'orders.order_no', 'orders.spk_number')->join('orders', 'orders.id', 'install_orders.order_id')->where('install_orders.site_id', $site_id)->whereBetween('install_orders.order_date',[$_GET['dari'],$_GET['sampai']])->get();
            }
            
            foreach($data as $value){
                $query=DB::table('install_order_ds')->join('products', 'products.id', 'install_order_ds.product_id')->where('install_order_ds.install_order_id', $value->id)->select('install_order_ds.*', 'products.amount_set', 'products.installation_fee')->get();
                $total_kontrak=0;
                foreach ($query as $k => $v) {
                    $total_kontrak+=($v->amount_set * $v->total) * $v->installation_fee;
                }
                $project=DB::table('customer_projects as cp')->select('cp.name')->join('orders as o', 'o.customer_project_id', 'cp.id')->where('o.id', $value->order_id)->first();
                $value->project_name=isset($project->name) ? $project->name : '-';
                $customer=Customer::find($value->customer_id);
                $value->customer_name=$customer->name;
                $value->customer_coorporate=$customer->coorporate_name;
                $value->total_kontrak=$total_kontrak;
            }
            $message = 'sukses';
        }
        catch(\Exception $e){
            $message = 'fail '. $e->getMessage();
        }
        catch(\Illuminate\Database\QueryException $e){
            $message = 'fail '. $e->getMessage();
        }
        finally{
            return response()->json(['data'=>$data]);
        }
    }
    public function getOptionOrderInstallList(Request $request)
    {
        $site_id=$request->get('site_id');
        if ($site_id == '') {
            $data = InstallOrder::select('install_orders.*', 'orders.order_no')->join('orders', 'orders.id', 'install_orders.order_id')->orderBy('install_orders.id', 'DESC')->get();
        }else{
            $data = InstallOrder::select('install_orders.*', 'orders.order_no')->join('orders', 'orders.id', 'install_orders.order_id')->where('install_orders.site_id', $site_id)->orderBy('install_orders.id', 'DESC')->get();
        }
        return response()->json(['data'=>$data]);
    }
    public function getInstallOrderListJson($orderId)
    {
        $data = InstallOrderD::where(['install_order_ds.install_order_id'=> $orderId])
                ->join('products', 'products.id', '=', 'install_order_ds.product_id')
                ->leftJoin('kavlings', 'products.kavling_id', '=', 'kavlings.id')
                ->select('products.name', 'products.item','products.series','products.panjang', 'products.lebar', 'products.description', 'products.image', 'products.price', 'products.amount_set', 'products.installation_fee', 'install_order_ds.*', 'kavlings.name as type_kavling')
                ->orderBy('install_order_ds.created_at','asc')
                ->get();
        return response()->json(['data'=>$data]);
    }
}
