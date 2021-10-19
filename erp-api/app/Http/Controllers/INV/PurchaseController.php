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
use App\Models\InvTrx;
use App\Models\InvTrxService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\PurchaseAsset;
use App\Models\PurchaseAssetD;
use App\Models\PurchaseService;
use App\Models\PurchaseServiceD;
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
            $datas = Purchase::where('site_id', $site_id)->whereNotNull('m_supplier_id')->where('acc_ao', true)->where('is_closed', false)
            ->get();
        } else {
            // $datas = Purchase::where('is_closed', false)->whereNotNull('m_supplier_id')
            $datas = Purchase::whereNotNull('m_supplier_id')->where('acc_ao', true)->where('is_closed', false)
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
                    mi.category as category,
                    mi.no as m_item_no,
                    pd.amount as volume,
                    mi.m_unit_id,
                    mu.name as m_unit_name,
                    mi.m_group_item_id,
                    mi.amount_in_set
                from purchases p
                join purchase_ds pd on p.id = pd.purchase_id
                join m_items mi on pd.m_item_id = mi.id
                join m_units mu on mi.m_unit_id = mu.id
                where p.is_closed 
                    and p.is_receive = false
                    and pd.buy_date is null
                    ". (isset($_GET['po_no']) ? "and p.no = '".$_GET['po_no']."' " : "") ."
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
                    mu.name as m_unit_name,
                    mi.m_group_item_id,
                    mi.amount_in_set
                from purchases p
                join purchase_ds pd on p.id = pd.purchase_id
                join m_items mi on pd.m_item_id = mi.id
                join m_units mu on mi.m_unit_id = mu.id
                where p.is_closed 
                    and pd.buy_date is null
                    ". (isset($_GET['po_no']) ? "and p.no = '".$_GET['po_no']."' " : "") ."
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
    public function getAllClosePurchase(){
        $site_id = null;
        if(isset($_GET['site_id']))
            $site_id = $_GET['site_id'];

        if($site_id != null) {
            // $datas = Purchase::where('is_closed', false)->where('site_id', $site_id)->whereNotNull('m_supplier_id')
            $datas = InvTrx::where('trx_type', 'RECEIPT')
                            ->join('purchases', 'purchases.id', '=', 'inv_trxes.purchase_id')
                            ->select('purchases.m_supplier_id', 'purchases.is_without_ppn', 'purchases.purchase_date', 'purchases.no AS no_po', 'purchases.is_closed', 'inv_trxes.*')
                            ->where('inv_trxes.site_id', $site_id)->get();
        } else {
            // $datas = Purchase::where('is_closed', false)->whereNotNull('m_supplier_id')
            $datas = InvTrx::where('trx_type', 'RECEIPT')
                            ->join('purchases', 'purchases.id', '=', 'inv_trxes.purchase_id')
                            ->select('purchases.m_supplier_id', 'purchases.is_without_ppn', 'purchases.purchase_date', 'purchases.no AS no_po', 'purchases.is_closed', 'inv_trxes.*')
                            ->get();
        }

        foreach($datas as $data){
            $data['m_suppliers'] = MSupplier::find($data['m_supplier_id']);
            $data['sites'] = Site::find($data['site_id']);
            $dt=DB::table('inv_trx_ds')->where('inv_trx_id', $data['id'])->whereNotIn('condition', [3])->get();
            $total=0;
            foreach ($dt as $key => $value) {
                $amount=($value->amount * $value->base_price);
                $total+=($amount + ($data['is_without_ppn'] == false ? $amount * 0.1 : 0));
            }
            $data['amount']=$total;
        }
        
        return response()->json(['data'=>$datas]);
    }
    public function getDetailClosePurchase($id){
        $datas = InvTrx::where('trx_type', 'RECEIPT')
                        ->join('purchases', 'purchases.id', '=', 'inv_trxes.purchase_id')
                        ->select('purchases.m_supplier_id', 'purchases.purchase_date', 'inv_trxes.*')
                        ->where('inv_trxes.id', $id)->get();
        
        foreach($datas as $data){
            $data['m_suppliers'] = MSupplier::find($data['m_supplier_id']);
            $data['sites'] = Site::find($data['site_id']);
        }
        
        return response()->json(['data'=>$datas]);
    }
    public function getPOATK(){
        $site_id = null;
        if(isset($_GET['site_id']))
            $site_id = $_GET['site_id'];

        if($site_id != null) {
            $datas = PurchaseAsset::where('is_special', false)
                    ->where('site_id', $site_id)
                    ->orderBy('id', 'desc')
                    ->get();
        } else {
            $datas = PurchaseAsset::where('is_special', false)
                    ->orderBy('id', 'desc')
                    ->get();
        }

        foreach($datas as $data){
            $data['m_suppliers'] = MSupplier::find($data['m_supplier_id']);
        }
        
        return response()->json(['data'=>$datas]);
    }
    public function getPurchaseDByPurchaseAssetId($purchaseId){
        $datas = PurchaseAssetD::where('purchase_asset_id', $purchaseId)
                ->get();

        foreach($datas as $data){
            $data['m_items'] = DB::select('select * from m_items where id = ' . $data['m_item_id'])[0];
            $data['m_units'] = MUnit::find($data['m_unit_id']);
            $data['purchase_assets'] = PurchaseAsset::find($purchaseId);
        }
        
        return response()->json(['data'=>$datas]);
    }
    public function getAllOpenPurchaseAsset(){
        $site_id = null;
        if(isset($_GET['site_id']))
            $site_id = $_GET['site_id'];

        if($site_id != null) {
            // $datas = Purchase::where('is_closed', false)->where('site_id', $site_id)->whereNotNull('m_supplier_id')
            $datas = PurchaseAsset::where('site_id', $site_id)->whereNotNull('m_supplier_id')->where('acc_ao', true)->where('is_closed', false)
            ->get();
        } else {
            // $datas = Purchase::where('is_closed', false)->whereNotNull('m_supplier_id')
            $datas = PurchaseAsset::whereNotNull('m_supplier_id')->where('acc_ao', true)->where('is_closed', false)
                ->get();
        }

        foreach($datas as $data){
            $data['m_suppliers'] = MSupplier::find($data['m_supplier_id']);
            $data['sites'] = Site::find($data['site_id']);
        }
        
        return response()->json(['data'=>$datas]);
    }
    public function getAllClosePurchaseAsset(){
        $site_id = null;
        if(isset($_GET['site_id']))
            $site_id = $_GET['site_id'];

        if($site_id != null) {
            // $datas = Purchase::where('is_closed', false)->where('site_id', $site_id)->whereNotNull('m_supplier_id')
            $datas = InvTrx::where('trx_type', 'RECEIPT')
                            ->join('purchase_assets', 'purchase_assets.id', '=', 'inv_trxes.purchase_asset_id')
                            ->select('purchase_assets.m_supplier_id', 'purchase_assets.is_without_ppn', 'purchase_assets.purchase_date', 'purchase_assets.no AS no_po', 'purchase_assets.is_closed', 'inv_trxes.*')
                            ->where('inv_trxes.site_id', $site_id)->get();
        } else {
            // $datas = Purchase::where('is_closed', false)->whereNotNull('m_supplier_id')
            $datas = InvTrx::where('trx_type', 'RECEIPT')
                            ->join('purchase_assets', 'purchase_assets.id', '=', 'inv_trxes.purchase_asset_id')
                            ->select('purchase_assets.m_supplier_id', 'purchase_assets.is_without_ppn', 'purchase_assets.purchase_date', 'purchase_assets.no AS no_po', 'purchase_assets.is_closed', 'inv_trxes.*')
                            ->get();
        }

        foreach($datas as $data){
            $data['m_suppliers'] = MSupplier::find($data['m_supplier_id']);
            $data['sites'] = Site::find($data['site_id']);
            $dt=DB::table('inv_trx_ds')->where('inv_trx_id', $data['id'])->whereNotIn('condition', [3])->get();
            $total=0;
            foreach ($dt as $key => $value) {
                $amount=($value->amount * $value->base_price);
                $total+=($amount + ($data['is_without_ppn'] == false ? $amount * 0.1 : 0));
            }
            $data['amount']=$total;
        }
        
        return response()->json(['data'=>$datas]);
    }
    public function getPOKonstruksiWithAO(){
        $site_id = null;
        if(isset($_GET['site_id']))
            $site_id = $_GET['site_id'];

        if($site_id != null) {
            $datas = Purchase::where('is_special', false)
                    ->where('site_id', $site_id)
                    ->where('with_ao', true)
                    ->orderBy('id', 'desc')
                    ->get();
        } else {
            $datas = Purchase::where('is_special', false)
                    ->orderBy('id', 'desc')
                    ->where('with_ao', true)
                    ->get();
        }

        foreach($datas as $data){
            $data['m_suppliers'] = MSupplier::find($data['m_supplier_id']);
        }
        
        return response()->json(['data'=>$datas]);
    }
    public function getPOAssetWithAO(){
        $site_id = null;
        if(isset($_GET['site_id']))
            $site_id = $_GET['site_id'];

        if($site_id != null) {
            $datas = PurchaseAsset::where('is_special', false)
                    ->where('site_id', $site_id)
                    ->where('with_ao', true)
                    ->orderBy('id', 'desc')
                    ->get();
        } else {
            $datas = PurchaseAsset::where('is_special', false)
                    ->orderBy('id', 'desc')
                    ->where('with_ao', true)
                    ->get();
        }

        foreach($datas as $data){
            $data['m_suppliers'] = MSupplier::find($data['m_supplier_id']);
        }
        
        return response()->json(['data'=>$datas]);
    }
    public function getPOService(){
        $site_id = null;
        if(isset($_GET['site_id']))
            $site_id = $_GET['site_id'];

        if($site_id != null) {
            $datas = PurchaseService::where('is_special', false)
                    ->where('site_id', $site_id)
                    ->orderBy('id', 'desc')
                    ->get();
        } else {
            $datas = PurchaseService::where('is_special', false)
                    ->orderBy('id', 'desc')
                    ->get();
        }

        foreach($datas as $data){
            $data['m_suppliers'] = MSupplier::find($data['m_supplier_id']);
        }
        
        return response()->json(['data'=>$datas]);
    }
    public function getPurchaseDByPurchaseServiceId($purchaseId){
        $datas = PurchaseServiceD::where('purchase_service_id', $purchaseId)
                ->get();

        foreach($datas as $data){
            $data['m_units'] = MUnit::find($data['m_unit_id']);
            $data['purchase_service'] = PurchaseService::find($purchaseId);
        }
        
        return response()->json(['data'=>$datas]);
    }
    public function getAllOpenPurchaseService(){
        $site_id = null;
        if(isset($_GET['site_id']))
            $site_id = $_GET['site_id'];

        if($site_id != null) {
            // $datas = Purchase::where('is_closed', false)->where('site_id', $site_id)->whereNotNull('m_supplier_id')
            $datas = PurchaseService::where('site_id', $site_id)->whereNotNull('m_supplier_id')->where('acc_ao', true)->where('is_closed', false)
            ->get();
        } else {
            // $datas = Purchase::where('is_closed', false)->whereNotNull('m_supplier_id')
            $datas = PurchaseService::whereNotNull('m_supplier_id')->where('acc_ao', true)->where('is_closed', false)
                ->get();
        }

        foreach($datas as $data){
            $data['m_suppliers'] = MSupplier::find($data['m_supplier_id']);
            $data['sites'] = Site::find($data['site_id']);
        }
        
        return response()->json(['data'=>$datas]);
    }
    public function getServiceByPurchaseId($purchaseId)
    {
        $datas = InvTrxService::where('purchase_service_id', $purchaseId)->get();
        foreach($datas as $data){
            $InvTrxServiceDs = $data -> InvTrxServiceDs;
            $data['purchases'] = PurchaseService::find($purchaseId);

            foreach($InvTrxServiceDs as $InvTrxServiceD){
                $InvTrxServiceD['m_units'] = MUnit::find($InvTrxServiceD['m_unit_id']);
            }
        }
        
        return response()->json(['data'=>$datas]);
    }
    public function getAllClosePurchaseService(){
        $site_id = null;
        if(isset($_GET['site_id']))
            $site_id = $_GET['site_id'];

        if($site_id != null) {
            $datas = InvTrxService::where('inv_trx_services.site_id', $site_id)
                            ->join('purchase_services', 'purchase_services.id', '=', 'inv_trx_services.purchase_service_id')
                            ->select('purchase_services.m_supplier_id', 'purchase_services.purchase_date', 'purchase_services.no AS no_po', 'purchase_services.is_closed', 'inv_trx_services.*')
                            ->get();
        } else {
            $datas = InvTrxService::select('purchase_services.m_supplier_id', 'purchase_services.purchase_date',        'purchase_services.no AS no_po', 'purchase_services.is_closed', 'inv_trx_services.*')
                            ->join('purchase_services', 'purchase_services.id', '=', 'inv_trx_services.purchase_service_id')
                            ->get();
        }

        foreach($datas as $data){
            $data['m_suppliers'] = MSupplier::find($data['m_supplier_id']);
            $data['sites'] = Site::find($data['site_id']);
        }
        
        return response()->json(['data'=>$datas]);
    }
}
