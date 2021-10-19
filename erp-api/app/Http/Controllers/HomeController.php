<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Carbon\Carbon;
use App\Models\Giro;

class HomeController extends Controller
{
    private $dateNow;
    private $now;
    public function __construct()
    {
        $this->dateNow = Carbon::now()->toDateString();
        $this->now = Carbon::now();
    }

    public function getProgramByUserId($userId) {
        $program = DB::table('programs')
            ->join('users', 'programs.user_id', '=', 'users.id')
            ->select('programs.*', 'users.name as username')
            ->where(['deleted_at'=>NULL, 'status'=>'Active', 'programs.user_id'=>$userId])
            ->get();
        return response()->json(['data'=>$program]);
    }
    public function getAllGiro(Request $request){
        $site_id=$request->site_id;
        
        if ($site_id == null) {
            $data['data']=Giro::where('is_fill', 0)->get();
        }else{
            $data['data']=Giro::where('is_fill', 0)->where('site_id', $site_id)->get();
        }
        foreach ($data['data'] as $key => $value) {
            $value->Site;
            $value->paid_cust=DB::table('paid_customers')->where('id', $value->paid_customer_id)->first();
            // $value->CustomerBill;
            // $value->CustomerBillD;
            // $value->Order;
            // $value->CustomerBill->Customer;
        }
        return $data;
    }
    public function getAllGiroSupplier(Request $request){
        $site_id=$request->site_id;
        
        if ($site_id == null) {
            $data['data']=Giro::where('is_fill', 1)->get();
        }else{
            $data['data']=Giro::where('is_fill', 1)->where('site_id', $site_id)->get();
        }
        foreach ($data['data'] as $key => $value) {
            $value->Site;
            $value->paid_supplier=DB::table('paid_suppliers')->where('id', $value->paid_supplier_id)->first();
            // $value->CustomerBill;
            // $value->CustomerBillD;
            // $value->Order;
            // $value->CustomerBill->Customer;
        }
        return $data;
    }
}

