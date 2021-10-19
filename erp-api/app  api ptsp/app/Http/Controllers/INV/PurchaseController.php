<?php
namespace App\Http\Controllers\INV;

use App\Models\Purchase;
use App\Models\PurchaseD;
use App\Models\PurchaseApproval;
use App\Models\MSupplier;
use App\Models\MItem;
use App\Models\MUnit;
use App\Models\Site;
use App\Models\MBestPrice;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;

class PurchaseController extends Controller
{
    public function getPOKonstruksi(){
        $site_id = null;
        if(isset($_GET['site_id']))
            $site_id = $_GET['site_id'];

        if($site_id != null) {
            $datas = Purchase::where('is_special', false)
                    ->where('site_id', $site_id)
                    ->orderBy('id', 'desc')
                    ->get();
        } else {
            $datas = Purchase::where('is_special', false)
                    ->orderBy('id', 'desc')
                    ->get();
        }

        foreach($datas as $data){
            $data['m_suppliers'] = MSupplier::find($data['m_supplier_id']);
        }
        
        return response()->json(['data'=>$datas]);
    }

    public function getPurchaseById($id){
        $datas = Purchase::find($id);

        return response()->json(['data'=>$datas]);
    }

    public function getPurchaseDByPurchaseId($purchaseId){
        $datas = PurchaseD::where('purchase_id', $purchaseId)
                ->get();

        foreach($datas as $data){
            $data['m_items'] = DB::select('select * from m_items where id = ' . $data['m_item_id'])[0];
            $data['m_units'] = MUnit::find($data['m_unit_id']);
            $data['purchases'] = Purchase::find($purchaseId);
        }
        
        return response()->json(['data'=>$datas]);
    }

    public function getAllOpenPurchase(){
        $site_id = null;
        if(isset($_GET['site_id']))
            $site_id = $_GET['site_id'];

        if($site_id != null) {
            // $datas = Purchase::where('is_closed', false)->where('site_id', $site_id)->whereNotNull('m_supplier_id')
            $datas = Purchase::where('site_id', $site_id)->whereNotNull('m_supplier_id')
            ->get();
        } else {
            // $datas = Purchase::where('is_closed', false)->whereNotNull('m_supplier_id')
            $datas = Purchase::whereNotNull('m_supplier_id')
                ->get();
        }

        foreach($datas as $data){
            $data['m_suppliers'] = MSupplier::find($data['m_supplier_id']);
            $data['sites'] = Site::find($data['site_id']);
        }
        
        return response()->json(['data'=>$datas]);
    }

    public function getPOKhusus(){
        $datas = Purchase::where('is_special', true)
                // ->leftJoin('purchase_approvals', 'purchases.id', '=', 'purchase_approvals.purchase_id')
                ->select(
                    'purchases.*'
                )
                ->get();

        foreach($datas as $data){
            if($data['m_supplier_id'] != null)
                $data['m_suppliers'] = MSupplier::find($data['m_supplier_id']);
        }
        
        return response()->json(['data'=>$datas]);
    }

    public function getPOKhususApproval(){
        $datas = Purchase::where('is_special', true)->where('is_closed', false)
                ->leftJoin('purchase_approvals', 'purchases.id', '=', 'purchase_approvals.purchase_id')
                ->select(
                    'purchases.*',
                    'purchase_approvals.is_apv',
                    'purchase_approvals.apv_date',
                    'purchase_approvals.apv_by'
                )
                ->where(function ($q){
                    $q->WhereNull('purchase_approvals.id');/*->orWhere('purchase_approvals.is_apv', '=', false);*/
                })
                ->get();

        foreach($datas as $data){
            if($data['m_supplier_id'] != null)
                $data['m_suppliers'] = MSupplier::find($data['m_supplier_id']);
        }
        
        return response()->json(['data'=>$datas]);
    }

    public function getPOKhususPembelianKhusus(){
        $datas = Purchase::where('is_special', true)->where('is_closed', false)
                ->leftJoin('purchase_approvals', 'purchases.id', '=', 'purchase_approvals.purchase_id')
                ->select(
                    'purchases.*',
                    'purchase_approvals.is_apv',
                    'purchase_approvals.apv_date',
                    'purchase_approvals.apv_by'
                )
                ->where('purchase_approvals.is_apv', true)
                ->where('purchase_approvals.apv_decision', 'BUY')
                ->whereNull('purchases.m_supplier_id')
                ->get();

        foreach($datas as $data){
            if($data['m_supplier_id'] != null)
                $data['m_suppliers'] = MSupplier::find($data['m_supplier_id']);
        }
        
        return response()->json(['data'=>$datas]);
    }

    public function getPurchaseApprovalByPurchaseId($id){
        $datas = PurchaseApproval::where('purchase_id', $id)->first();

        return response()->json(['data'=>$datas]);
    }

    public function postBestPrice(Request $request) {
        $best_prices = MBestPrice::where('m_item_id', $request->m_item_id)
                        ->get();
        if(count($best_prices) > 0) {
            //edit
            $object = MBestPrice::findOrFail($best_prices[0]->id);
            if($object->best_price > $request->best_price) {
                $object->update($request->all());
                return response()->json(
                    [
                        'data'=>$object,
                        'responseMessage' => 'Success update'
                    ], 
                    200
                );
            } else {
                return response()->json(
                    [
                        'data'=>$object,
                        'responseMessage' => 'Success with no create and update'
                    ], 
                    200
                );
            }
        } else {
            //add
            $object = MBestPrice::create($request->all());
            return response()->json(
                [
                    'data'=>$object,
                    'responseMessage' => 'Success create'
                ], 
                201
            );
        }
    }

    public function getPoCanceled() {
        $site_id = null;
        if(isset($_GET['site_id']))
            $site_id = $_GET['site_id'];

        if($site_id != null) {
            $datas = DB::select("
                select 
                    pd.id as purchase_d_id,
                    p.no as no,
                    pd.m_item_id,
                    mi.name as m_item_name,
                    pd.amount as volume,
                    mi.m_unit_id,
                    mu.name as m_unit_name
                from purchases p
                join purchase_ds pd on p.id = pd.purchase_id
                join m_items mi on pd.m_item_id = mi.id
                join m_units mu on mi.m_unit_id = mu.id
                where p.is_closed 
                    and p.is_receive = false
                    and p.site_id = ?
            ", [$site_id]);
        } else {
            $datas = DB::select("
                select 
                    pd.id as purchase_d_id,
                    p.no as no,
                    pd.m_item_id,
                    mi.name as m_item_name,
                    pd.amount as volume,
                    mi.m_unit_id,
                    mu.name as m_unit_name
                from purchases p
                join purchase_ds pd on p.id = pd.purchase_id
                join m_items mi on pd.m_item_id = mi.id
                join m_units mu on mi.m_unit_id = mu.id
                where p.is_closed 
                    and p.is_receive = false
            ");
        }

        foreach($datas as $data){
            $data->m_items = MItem::find($data->m_item_id);
            $data->m_units = MUnit::find($data->m_unit_id);
        }

        return response()->json(['data'=>$datas]);
    }

    public function getPurchaseAll(){
        $purchase = DB::select("select m.name, p.no, p.id, p.purchase_date, p.updated_at, p.ekspedisi, p.base_price, p.is_closed from purchases p inner join m_suppliers m on p.m_supplier_id = m.id");

        // $response = array();
        // foreach($customers as $customer){
        //     $counts = DB::select("select count(*) as hitung from followup_histories where customer_id = ".$customer->id."");
        //     $customer->last_followup_seq = $counts[0]->hitung;
        // }
        
        return response()->json(['data' => $purchase]);
    }
    public function getPurchaseDetail($id){
        $purchase = DB::select("
            select mi.name as name, pd.amount, mu.name as unit, pd.base_price  
            from purchase_ds pd 
            inner join m_items mi on pd.m_item_id = mi.id
            inner join m_units mu on pd.m_unit_id = mu.id
            where pd.purchase_id = $id");

        // $response = array();
        // foreach($customers as $customer){
        //     $counts = DB::select("select count(*) as hitung from followup_histories where customer_id = ".$customer->id."");
        //     $customer->last_followup_seq = $counts[0]->hitung;
        // }
        
        return response()->json(['data' => $purchase]);
    }
}
