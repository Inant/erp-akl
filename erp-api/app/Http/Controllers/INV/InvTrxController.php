<?php
namespace App\Http\Controllers\INV;

use App\Models\InvTrx;
use App\Models\InvTrxD;
use App\Models\Purchase;
use App\Models\MItem;
use App\Models\MUnit;
use App\Models\Site;
use App\Models\TransferStock;
use App\Models\TsWarehouse;
use App\Models\InvSale;
use App\Models\MSupplier;
use App\Models\Rab;
use App\Models\Project;
use App\Models\InvRequest;
use App\Models\InvReturn;
use App\Models\InvReturnD;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\PurchaseAsset;
use App\Models\MWarehouse;
use \Exception;
use DB;

class InvTrxController extends Controller
{
    public function getAll()
    {
        $datas = InvTrx::all();
        foreach($datas as $data){
            $invTrxDs = $data -> InvTrxDs;
        }
        
        return response()->json(['data'=>$datas]);
    }

    public function getById($id)
    {
        $datas = InvTrx::find($id);
        $invTrxD = $datas -> InvTrxDs;
        
        return response()->json(['data'=>$datas]);
    }

    public function getByPurchaseId($purchaseId)
    {
        $datas = InvTrx::where('purchase_id', $purchaseId)->get();
        foreach($datas as $data){
            $invTrxDs = $data -> InvTrxDs;
            $data['purchases'] = Purchase::find($purchaseId);

            foreach($invTrxDs as $invTrxD){
                $invTrxD['m_items'] = MItem::find($invTrxD['m_item_id']);
                $invTrxD['m_units'] = MUnit::find($invTrxD['m_unit_id']);
            }
        }
        
        return response()->json(['data'=>$datas]);
    }

    public function getByInvRequestId($inv_request_id)
    {
        $datas = InvTrx::where('inv_request_id', $inv_request_id)->get();
        foreach($datas as $data){
            $invTrxDs = $data -> InvTrxDs;
            foreach($invTrxDs as $invTrxD){
                $invTrxD['m_items'] = MItem::find($invTrxD['m_item_id']);
                $invTrxD['m_units'] = MUnit::find($invTrxD['m_unit_id']);
            }
        }
        
        return response()->json(['data'=>$datas]);
    }

    public function getStokAllSite(){
        $datas = DB::select("
                select 
                    (COALESCE(inv_in.site_id, inv_out.site_id)) as site_id, 
                    (COALESCE(inv_in.m_item_id, inv_out.m_item_id)) as m_item_id,
                    (COALESCE(inv_in.amount, 0)) as amount_in,
                    (COALESCE(inv_out.amount, 0)) as amount_out,
                    ((COALESCE(inv_in.amount, 0)) - (COALESCE(inv_out.amount, 0))) as stok,
                    inv_in.updated_at as last_update_in,
                    inv_out.updated_at as last_update_out
                from (select site_id, m_item_id, sum(amount) as amount, max(it.updated_at) as updated_at from inv_trxes it
                join inv_trx_ds itd on it.id = itd.inv_trx_id
                where is_entry = true
                group by site_id, m_item_id) inv_in
                full outer join (select site_id, m_item_id, sum(amount) as amount, max(it.updated_at) as updated_at from inv_trxes it
                join inv_trx_ds itd on it.id = itd.inv_trx_id
                where is_entry = false
                group by site_id, m_item_id) inv_out on inv_in.m_item_id = inv_out.m_item_id and inv_in.site_id = inv_out.site_id
                ");

        foreach($datas as $data) {
            $data->sites = Site::find($data->site_id);
            $data->m_items = DB::select('select * from m_items where id = ' . $data->m_item_id)[0];
            $data->m_units = MUnit::find($data->m_items->m_unit_id);
        }

        return response()->json(['data' => $datas]);
    }

    public function getStokSite($id){ //stok lawas sebelum warehouse
        try{
            $m_item_id = $_GET['m_item_id'];
        } catch(Exception $e) {
            $m_item_id = null;
        }

        if($m_item_id == null) {
            $datas = DB::select("
                select 
                    (COALESCE(inv_in.site_id, inv_out.site_id)) as site_id, 
                    (COALESCE(inv_in.m_item_id, inv_out.m_item_id)) as m_item_id,
                    (COALESCE(inv_in.m_warehouse_id, inv_out.m_warehouse_id)) as m_warehouse_id, 
                    (COALESCE(inv_in.amount, 0)) as amount_in,
                    (COALESCE(inv_out.amount, 0)) as amount_out,
                    (COALESCE(inv_out.amount_ret, 0)) as amount_ret,
                    ((COALESCE(inv_in.amount, 0)) - ((COALESCE(inv_out.amount, 0)) - (COALESCE(inv_out.amount_ret, 0)))) as stok,
                    inv_in.updated_at as last_update_in,
                    inv_out.updated_at as last_update_out
                from (select site_id, m_item_id, sum(amount) as amount, itd.m_warehouse_id as m_warehouse_id, max(it.updated_at) as updated_at from inv_trxes it
                join inv_trx_ds itd on it.id = itd.inv_trx_id
                where is_entry = true and site_id = ".$id." and trx_type != 'RET_ITEM'
                group by site_id, m_item_id, itd.m_warehouse_id) inv_in
                full outer join (select site_id, m_item_id, itd.m_warehouse_id, coalesce((SELECT sum(amount) as amount from inv_trxes it1
                join inv_trx_ds itd1 on it1.id = itd1.inv_trx_id
                where trx_type = 'RET_ITEM' and it1.site_id=it.site_id and itd1.m_item_id=itd.m_item_id and itd1.m_warehouse_id=itd.m_warehouse_id), 0) AS amount_ret, sum(amount) as amount, max(it.updated_at) as updated_at from inv_trxes it
                join inv_trx_ds itd on it.id = itd.inv_trx_id
                where is_entry = false and site_id = ".$id." 
                group by site_id, m_item_id, itd.m_warehouse_id) inv_out on inv_in.m_item_id = inv_out.m_item_id and inv_in.site_id = inv_out.site_id  and inv_in.m_warehouse_id = inv_out.m_warehouse_id
                ");
        } else {
            $datas = DB::select("
                select 
                    (COALESCE(inv_in.site_id, inv_out.site_id)) as site_id, 
                    (COALESCE(inv_in.m_item_id, inv_out.m_item_id)) as m_item_id,
                    (COALESCE(inv_in.m_warehouse_id, inv_out.m_warehouse_id)) as m_warehouse_id, 
                    (COALESCE(inv_in.amount, 0)) as amount_in,
                    (COALESCE(inv_out.amount, 0)) as amount_out,
                    (COALESCE(inv_out.amount_ret, 0)) as amount_ret,
                    ((COALESCE(inv_in.amount, 0)) - ((COALESCE(inv_out.amount, 0)) - (COALESCE(inv_out.amount_ret, 0)))) as stok,
                    inv_in.updated_at as last_update_in,
                    inv_out.updated_at as last_update_out
                from (select site_id, m_item_id, sum(amount) as amount, itd.m_warehouse_id as m_warehouse_id, max(it.updated_at) as updated_at from inv_trxes it
                join inv_trx_ds itd on it.id = itd.inv_trx_id
                where is_entry = true and site_id = ".$id." and m_item_id = ".$m_item_id." and trx_type != 'RET_ITEM'
                group by site_id, m_item_id, itd.m_warehouse_id) inv_in
                full outer join (select site_id, m_item_id, itd.m_warehouse_id as m_warehouse_id, coalesce((SELECT sum(amount) as amount from inv_trxes it1
                join inv_trx_ds itd1 on it1.id = itd1.inv_trx_id
                where trx_type = 'RET_ITEM' and it1.site_id=it.site_id and itd1.m_item_id=itd.m_item_id and itd1.m_warehouse_id=itd.m_warehouse_id), 0) AS amount_ret, sum(amount) as amount, max(it.updated_at) as updated_at from inv_trxes it
                join inv_trx_ds itd on it.id = itd.inv_trx_id
                where is_entry = false and site_id = ".$id." and m_item_id = ".$m_item_id."
                group by site_id, m_item_id, itd.m_warehouse_id) inv_out on inv_in.m_item_id = inv_out.m_item_id and inv_in.site_id = inv_out.site_id
                ");
        }

        foreach($datas as $data) {
            $data->sites = Site::find($data->site_id);
            $data->m_items = DB::select('select * from m_items where id = ' . $data->m_item_id)[0];
            $data->m_units = MUnit::find($data->m_items->m_unit_id);
            $data->m_warehouse = DB::table('m_warehouses')->where('id', $data->m_warehouse_id)->first();
            // harga average tiap item
            $get_save_price=DB::table('m_item_prices')->where(['m_item_id' => $data->m_item_id, 'site_id' => $data->site_id])->first();
            
            // harga satuan (dari tanggal terakhir)
            // $last_price = DB::select("select pd.base_price from inv_trxes it
            // join purchases p on it.purchase_id = p.id
            // join purchase_ds pd on p.id = pd.purchase_id
            // where it.is_entry = true and it.site_id = ? and pd.m_item_id = ?
            // order by it.created_at desc limit 1", [$data->site_id, $data->m_item_id]);
            $data->last_price = $get_save_price != null ? $get_save_price->price : 0;

            // nilai material
            $value = 0;
            $data_values = DB::select("select pd.amount, pd.base_price from inv_trxes it
            join purchases p on it.purchase_id = p.id
            join purchase_ds pd on p.id = pd.purchase_id
            where it.is_entry = true and it.site_id = ? and pd.m_item_id = ?", [$data->site_id, $data->m_item_id]);
            foreach($data_values as $data_value) {
                $amount = $data_value->amount != null ? $data_value->amount : 0;
                $price = $data_value->base_price != null ? $data_value->base_price : 0;
                $value +=  $amount * $price; 
            }
            $data->value = $value;
        }

        return response()->json(['data' => $datas]);
    }

    public function getStokRestSite($id){
        try{
            $m_item_id = $_GET['m_item_id'];
        } catch(Exception $e) {
            $m_item_id = null;
        }

        if($m_item_id == null) {
            $datas = DB::select("
                select 
                    (COALESCE(inv_in.site_id, inv_out.site_id)) as site_id, 
                    (COALESCE(inv_in.m_item_id, inv_out.m_item_id)) as m_item_id,
                    (COALESCE(inv_in.m_warehouse_id, inv_out.m_warehouse_id)) as m_warehouse_id, 
                    (COALESCE(inv_in.amount_rest, 0)) as amount_rest,
                    (COALESCE(inv_in.amount, 0)) as amount_in,
                    (COALESCE(inv_out.amount, 0)) as amount_out,
                    ((COALESCE(inv_in.amount, 0)) - (COALESCE(inv_out.amount, 0))) as stok,
                    inv_in.updated_at as last_update_in,
                    inv_out.updated_at as last_update_out
                from (select site_id, m_item_id, itd.m_warehouse_id, count(amount) as amount, max(it.updated_at) as updated_at, max(itd.amount) as amount_rest from inv_trxes it
                join inv_trx_rest_ds itd on it.id = itd.inv_trx_id
                where is_entry = true and trx_type = 'RET_ITEM' and site_id = ".$id."
                group by site_id, m_item_id, amount, itd.m_warehouse_id) inv_in
                full outer join (select site_id, m_item_id, itd.m_warehouse_id, count(amount) as amount, max(it.updated_at) as updated_at, max(itd.amount) as amount_rest from inv_trxes it
                join inv_trx_rest_ds itd on it.id = itd.inv_trx_id
                where is_entry = false and trx_type = 'REQ_ITEM' and site_id = ".$id." 
                group by site_id, m_item_id, amount, itd.m_warehouse_id) inv_out on inv_in.m_item_id = inv_out.m_item_id and inv_in.site_id = inv_out.site_id
                ");
        } else {
            $datas = DB::select("
                select 
                    (COALESCE(inv_in.site_id, inv_out.site_id)) as site_id, 
                    (COALESCE(inv_in.m_item_id, inv_out.m_item_id)) as m_item_id,
                    (COALESCE(inv_in.amount_rest, 0)) as amount_rest,
                    (COALESCE(inv_in.amount, 0)) as amount_in,
                    (COALESCE(inv_out.amount, 0)) as amount_out,
                    ((COALESCE(inv_in.amount, 0)) - (COALESCE(inv_out.amount, 0))) as stok,
                    inv_in.updated_at as last_update_in,
                    inv_out.updated_at as last_update_out
                from (select site_id, m_item_id, sum(amount) as amount, max(it.updated_at) as updated_at, max(itd.amount) as amount_rest from inv_trxes it
                join inv_trx_rest_ds itd on it.id = itd.inv_trx_id
                where is_entry = true and trx_type = 'RET_ITEM' and site_id = ".$id." and m_item_id = ".$m_item_id."
                group by site_id, m_item_id, amount) inv_in
                full outer join (select site_id, m_item_id, sum(amount) as amount, max(it.updated_at) as updated_at, max(itd.amount) as amount_rest from inv_trxes it
                join inv_trx_rest_ds itd on it.id = itd.inv_trx_id
                where is_entry = false and trx_type = 'RET_ITEM' and site_id = ".$id." and m_item_id = ".$m_item_id."
                group by site_id, m_item_id, amount) inv_out on inv_in.m_item_id = inv_out.m_item_id and inv_in.site_id = inv_out.site_id
                ");
        }

        foreach($datas as $data) {
            $data->sites = Site::find($data->site_id);
            $data->m_items = DB::select('select * from m_items where id = ' . $data->m_item_id)[0];
            $data->m_units = MUnit::find($data->m_items->m_unit_id);
            $data->m_warehouse = DB::table('m_warehouses')->where('id', $data->m_warehouse_id)->first();
            // harga average tiap item
            $get_save_price=DB::table('m_item_prices')->where(['m_item_id' => $data->m_item_id, 'site_id' => $data->site_id])->first();
            
            $data->last_price = $get_save_price != null ? $get_save_price->price : 0;

            // nilai material
            $value = 0;
            $data_values = DB::select("select pd.amount, pd.base_price from inv_trxes it
            join purchases p on it.purchase_id = p.id
            join purchase_ds pd on p.id = pd.purchase_id
            where it.is_entry = true and it.site_id = ? and pd.m_item_id = ?", [$data->site_id, $data->m_item_id]);
            foreach($data_values as $data_value) {
                $amount = $data_value->amount != null ? $data_value->amount : 0;
                $price = $data_value->base_price != null ? $data_value->base_price : 0;
                $value +=  $amount * $price; 
            }
            $data->value = $value;
        }

        return response()->json(['data' => $datas]);
    }

    // public function getStokSite($id){
    //     try{
    //         $m_item_id = $_GET['m_item_id'];
    //     } catch(Exception $e) {
    //         $m_item_id = null;
    //     }

    //     if($m_item_id == null) {
    //         $datas = DB::select("select (COALESCE(inv_in.purchase_d_id, 0)) as purchase_d_id, 
    //                 (COALESCE(inv_in.site_id, 0)) as site_id, 
    //                 (COALESCE(inv_in.no, null)) as no, 
    //                 (COALESCE(inv_in.m_item_id, 0)) as m_item_id,
    //                 (COALESCE(inv_in.amount, 0)) as amount_in,
    //                 (COALESCE(inv_in.stok_out, 0)) as amount_out,
    //                 ((COALESCE(inv_in.amount, 0)) - (COALESCE(inv_in.stok_out, 0))) as stok,
    //                 inv_in.updated_at as last_update_in,
    //                 inv_in.updated_at2 as last_update_out
    //             from (select site_id, max(it.no) as no, m_item_id, sum(amount) as amount, COALESCE((select sum(amount) as amount from inv_trxes it1
    //             join inv_trx_ds itd1 on it1.id = itd1.inv_trx_id
    //             where it1.is_entry = false and it1.site_id=it.site_id and itd1.m_item_id=itd.m_item_id and itd1.purchase_d_id=itd.purchase_d_id), 0) as stok_out, COALESCE((select max(itd1.updated_at) as updated_at from inv_trxes it1
    //             join inv_trx_ds itd1 on it1.id = itd1.inv_trx_id
    //             where it1.is_entry = false and it1.site_id=it.site_id and itd1.m_item_id=itd.m_item_id and itd1.purchase_d_id=itd.purchase_d_id), max(it.updated_at)) as updated_at2, max(it.updated_at) as updated_at, max(itd.purchase_d_id) as purchase_d_id from inv_trxes it
    //             join inv_trx_ds itd on it.id = itd.inv_trx_id
    //             where is_entry = true and site_id = ".$id."
    //             group by site_id, m_item_id, itd.purchase_d_id) inv_in
    //             ");
    //     } else {
    //         $datas = DB::select("select (COALESCE(inv_in.purchase_d_id, 0)) as purchase_d_id, 
    //                 (COALESCE(inv_in.site_id, 0)) as site_id, 
    //                 (COALESCE(inv_in.no, null)) as no, 
    //                 (COALESCE(inv_in.m_item_id, 0)) as m_item_id,
    //                 (COALESCE(inv_in.amount, 0)) as amount_in,
    //                 (COALESCE(inv_in.stok_out, 0)) as amount_out,
    //                 ((COALESCE(inv_in.amount, 0)) - (COALESCE(inv_in.stok_out, 0))) as stok,
    //                 inv_in.updated_at as last_update_in,
    //                 inv_in.updated_at2 as last_update_out
    //             from (select site_id, max(no) as no, m_item_id, sum(amount) as amount, COALESCE((select sum(amount) as amount from inv_trxes it1
    //             join inv_trx_ds itd1 on it1.id = itd1.inv_trx_id
    //             where it1.is_entry = false and it1.site_id=it.site_id and itd1.m_item_id=itd.m_item_id and itd1.purchase_d_id=itd.purchase_d_id), 0) as stok_out, COALESCE((select max(itd1.updated_at) as updated_at from inv_trxes it1
    //             join inv_trx_ds itd1 on it1.id = itd1.inv_trx_id
    //             where it1.is_entry = false and it1.site_id=it.site_id and itd1.m_item_id=itd.m_item_id and itd1.purchase_d_id=itd.purchase_d_id), max(it.updated_at)) as updated_at2, max(it.updated_at) as updated_at, max(itd.purchase_d_id) as purchase_d_id from inv_trxes it
    //             join inv_trx_ds itd on it.id = itd.inv_trx_id
    //             where is_entry = true and site_id = ".$id." and m_item_id = ".$m_item_id."
    //             group by site_id, m_item_id, itd.purchase_d_id) inv_in
    //             ");
    //     }

    //     foreach($datas as $data) {
    //         $data->sites = Site::find($data->site_id);
    //         $data->m_items = DB::select('select * from m_items where id = ' . $data->m_item_id)[0];
    //         $data->m_units = MUnit::find($data->m_items->m_unit_id);

    //         // harga satuan (dari tanggal terakhir)
    //         $last_price = DB::select("select base_price from purchase_ds where id = ?", [$data->purchase_d_id]);
    //         $data->last_price = $last_price != null ? $last_price[0]->base_price : 0;

    //         // nilai material
    //         $value = 0;
    //         $data_values = DB::select("select amount, base_price from purchase_ds where id = ?", [$data->purchase_d_id]);
    //         foreach($data_values as $data_value) {
    //             $amount = $data_value->amount != null ? $data_value->amount : 0;
    //             $price = $data_value->base_price != null ? $data_value->base_price : 0;
    //             $value +=  $amount * $price; 
    //         }
    //         $data->value = $value;
    //     }

    //     return response()->json(['data' => $datas]);
    // }

    public function getListRequestBarang(){
        $datas = DB::select("
            select ir.*, r.no as rab_no, mt.city as site_location, COALESCE(s.name, s2.name) as site_name, p.name as project_name, pr.name AS product_name, o.total AS total_order, ord.spk_number
                from inv_requests ir
                left join rabs r on ir.rab_id = r.id
                left join projects p on r.project_id = p.id
                left join orders ord on r.order_id = ord.id
                left join order_ds o on r.order_d_id = o.id
                left join products pr on o.product_id = pr.id
                left join sites s on p.site_id = s.id
                left join m_cities mt on s.m_city_id = mt.id
                left join sites s2 on ir.site_id = s2.id
                where ir.deleted_at is null
                and ir.req_type != 'RET_ITEM'
                and ir.req_type != 'REQ_ITEM_SP'
        ");

        return response()->json(['data' => $datas]);
    }
    public function getListRequestBarangAcc(){
        $datas = DB::select("
            select ir.*, r.no as rab_no, mt.city as site_location, COALESCE(s.name, s2.name) as site_name, p.name as project_name, pr.name AS product_name, o.total AS total_order, orders.spk_number
                from inv_requests ir
                left join rabs r on ir.rab_id = r.id
                left join projects p on r.project_id = p.id
                left join orders on r.order_id = orders.id
                left join order_ds o on r.order_d_id = o.id
                left join products pr on o.product_id = pr.id
                left join sites s on p.site_id = s.id
                left join m_cities mt on s.m_city_id = mt.id
                left join sites s2 on ir.site_id = s2.id
                where ir.deleted_at is null
                and ir.req_type != 'RET_ITEM'
                and ir.req_type != 'REQ_ITEM_SP'
                and ir.req_type != 'SPECIAL'
                or ir.id in (select id from inv_requests where req_type = 'SPECIAL' and user_auth is not null)
        ");

        return response()->json(['data' => $datas]);
    }
    public function getListRequestBarangAuth(){
        $datas = DB::select("
            select ir.*, r.no as rab_no, orders.spk_number, mt.city as site_location, COALESCE(s.name, s2.name) as site_name, p.name as project_name, pr.name AS product_name, o.total AS total_order
                from inv_requests ir
                left join rabs r on ir.rab_id = r.id
                left join orders on r.order_id = orders.id
                left join projects p on r.project_id = p.id
                left join order_ds o on r.order_d_id = o.id
                left join products pr on o.product_id = pr.id
                left join sites s on p.site_id = s.id
                left join m_cities mt on s.m_city_id = mt.id
                left join sites s2 on ir.site_id = s2.id
                where ir.deleted_at is null
                and ir.req_type = 'SPECIAL'
        ");

        return response()->json(['data' => $datas]);
    }

    public function getListReturnRequestBarang(){
        $datas = DB::select("
            select ir.*, r.no as rab_no, mt.city as site_location, COALESCE(s.name, s2.name) as site_name, p.name as project_name, pr.name AS product_name, o.total AS total_order
                from inv_requests ir
                left join rabs r on ir.rab_id = r.id
                left join projects p on r.project_id = p.id
                left join order_ds o on r.order_d_id = o.id
                left join products pr on o.product_id = pr.id
                left join sites s on p.site_id = s.id
                left join m_cities mt on s.m_city_id = mt.id
                left join sites s2 on ir.site_id = s2.id
                where ir.deleted_at is null
                and ir.req_type = 'RET_ITEM'
        ");

        return response()->json(['data' => $datas]);
    }

    public function getListRequestMaterialSP(){
        $datas = DB::select("
            select ir.*, r.no as rab_no, mt.city as site_location, COALESCE(s.name, s2.name) as site_name, p.name as project_name, pr.name AS product_name, o.total AS total_order
                from inv_requests ir
                left join rabs r on ir.rab_id = r.id
                left join projects p on r.project_id = p.id
                left join order_ds o on r.order_d_id = o.id
                left join products pr on o.product_id = pr.id
                left join sites s on p.site_id = s.id
                left join m_cities mt on s.m_city_id = mt.id
                left join sites s2 on ir.site_id = s2.id
                where ir.deleted_at is null
                and ir.req_type = 'REQ_ITEM_SP'
        ");

        return response()->json(['data' => $datas]);
    }

    public function getListRequestBarangDetail($id)
    {
        $datas = DB::select("
            select * from inv_request_ds
            where inv_request_id = ?
        ", [$id]);

        foreach($datas as $data){
            $data->m_items = MItem::withTrashed()->find($data->m_item_id);
            $data->m_units = MUnit::find($data->m_unit_id);
        }

        return response()->json(['data'=>$datas]);
    }

    public function getListPengeluaranBarang(){
        $datas = DB::select("
            select ir.*, r.no as rab_no, mt.city as site_location, COALESCE(s.name, s2.name) as site_name, p.name as project_name, it.id as inv_trx_id
            from inv_requests ir
            left join inv_trxes it on ir.id = it.inv_request_id
            left join rabs r on ir.rab_id = r.id
            left join projects p on r.project_id = p.id
            left join sites s on p.site_id = s.id
            left join m_cities mt on s.m_city_id = mt.id
            left join sites s2 on ir.site_id = s2.id
            where ir.req_type != 'RET_ITEM' and ir.req_type != 'REQ_ITEM_SP'
        ");
        // where it.id is null

        return response()->json(['data' => $datas]);
    }

    public function getListPengeluaranMaterialTrx(){
        $message = '';
        $status='';
        try{
            $datas = DB::select("
                select trx.id as inv_trx_id, trx.inv_trx_date, trx.no as no_trx, req.no as no_req, rab.no as no_rab, orders.spk_number, p.name as project_name, req.user_auth
                from inv_trxes trx
                join inv_requests req on req.id = trx.inv_request_id
                left join rabs rab on req.rab_id = rab.id
                left join orders on rab.order_id = orders.id
                left join projects p on rab.project_id = p.id
                where trx.trx_type = 'REQ_ITEM'
                order by inv_trx_date desc
            ");
            $message = 'sukses';
        }
        catch(\Exception $e){
            $status = 'fail';
            $message = $e->getMessage();
        }
        catch(\Illuminate\Database\QueryException $e){
            $status = 'fail';
            $message = $e->getMessage();
        }
        finally{
            // return $message;
            return response()->json(['data' => $datas]);
        }  
    }

    // Post Method
    public function getMutasiStok(Request $request) {
        $site_id = $request->get('site_id');
        $date_gte = $request->get('date_gte');
        $date_lte = $request->get('date_lte');
        $m_item_id = $request->get('m_item_id');
        $is_entry = $request->get('is_entry');
        $m_warehouse_id = $request->get('m_warehouse_id');
        $status = '';
        $message = '';
        try{
            
            // $query = DB::table('inv_trxes as it')->select('itd.m_item_id', 'itd.m_unit_id', 'itd.amount', 'it.is_entry', 'it.inv_trx_date', 'it.site_id, it.purchase_id', 'it.purchase_asset_id', 'it.transfer_stock_id', 'itd.value', 'itd.m_warehouse_id', 'it.ts_warehouse_id', 'itd.base_price', '*')->join('inv_trx_ds as itd', 'it.id', 'itd.inv_trx_id');
        
            $query = DB::table('inv_trxes as it')->select(
                    'itd.m_item_id',
                    'itd.m_unit_id',
                    'itd.amount',
                    'it.is_entry',
                    'it.inv_trx_date',
                    'it.site_id',
                    'it.purchase_id',
                    'it.purchase_asset_id',
                    'it.transfer_stock_id',
                    'itd.value',
                    'itd.m_warehouse_id',
                    'it.ts_warehouse_id',
                    'itd.base_price',
                    'it.inv_request_id',
                    'it.id as trx_id',
                    'it.no as no_trx'
                )->join('inv_trx_ds as itd', 'it.id', 'itd.inv_trx_id');

            if($site_id != null)
                $query->where("it.site_id", $site_id);
            if($date_gte != null && $date_lte != null)
                $query->whereBetween("it.inv_trx_date", [$date_gte, $date_lte]);
            if($date_lte != null)
                $query->where("it.inv_trx_date", '<=', "$date_lte");
            if($m_item_id != 'all')
                $query->where("itd.m_item_id", $m_item_id);
            if($is_entry != 'all')
                $query->where("it.is_entry", $is_entry);
            if($m_warehouse_id != '')
                $query->where("itd.m_warehouse_id", $m_warehouse_id);

            $datas = $query->get();

            foreach($datas as $data){
                $data->m_items = MItem::withTrashed()->find($data->m_item_id);
                if($data->inv_request_id != null){
                    $id = $data->inv_request_id;
                }
                $value = 0;
                $data->m_units = MUnit::find($data->m_unit_id);
                $data->m_warehouse = MWarehouse::find($data->m_warehouse_id);
                $data->ts_warehouse = TsWarehouse::find($data->ts_warehouse_id);
                $data->transfer_stok = TransferStock::find($data->transfer_stock_id);
                if($data->transfer_stok != null) {
                    $data->transfer_stok->sites_to = Site::find($data->transfer_stok->site_to);
                    $data->transfer_stok->sites_from = Site::find($data->transfer_stok->site_from);
                }
                $data->purchase = Purchase::where('id', $data->purchase_id)->select('no', 'm_supplier_id')->first();
                if($data->purchase != null){
                    $data->purchase->m_suppliers = MSupplier::find($data->purchase->m_supplier_id);
                    // $value = DB::select('
                    //             SELECT base_price FROM purchase_ds WHERE purchase_id = ? AND m_item_id = ?
                    //         ', [$data->purchase_id, $data->m_item_id])[0]->base_price;
                }
                $data->purchase_asset = PurchaseAsset::where('id', $data->purchase_asset_id)->select('no', 'm_supplier_id')->first();
                if($data->purchase_asset != null){
                    $data->purchase_asset->m_suppliers = MSupplier::find($data->purchase_asset->m_supplier_id);
                    // $value = DB::select('
                    //             SELECT base_price FROM purchase_ds WHERE purchase_id = ? AND m_item_id = ?
                    //         ', [$data->purchase_id, $data->m_item_id])[0]->base_price;
                }
                if($data->inv_request_id != null){
                    $data->inv_request = InvRequest::find($data->inv_request_id);
                    if($data->inv_request != null && $data->inv_request->rab_id != null){
                        $rabs = Rab::find($data->inv_request->rab_id);
                        if($rabs != null && $rabs->project_id != null) {
                            $data->inv_request->project = Project::find($rabs->project_id);
                        }
                    }
                }
                
                if($data->inv_request_id != null){
                    $data->ts_warehouse = TsWarehouse::find($data->ts_warehouse_id);
                }
                if(isset($data->inv_sale)){
                    $data->inv_sale = InvSale::find($data->inv_sale_id);
                }
                // if($data->is_entry == false)
                $data->value = $data->base_price;
                // else 
                //     $data->value = $value;
                // if ($data->m_items->category == 'SPARE PART' && $data->m_items->m_group_item_id == null) {
                //     unset($datas[$key]);
                // }
                
            }
            $status = 'sukses';
            $message = 'sukses';
        }
        catch(\Exception $e){
            $status = 'fail';
            $message = $e->getMessage();
        }
        catch(\Illuminate\Database\QueryException $e){
            $status = 'fail';
            $message = $e->getMessage();
        }
        finally{
            $respon = [
                'status' => $status,
                'message' => $message,
                'data' => $datas
            ];
            return response()->json($respon);
        }
    }

    public function getValueOut(Request $request) {
        $m_item_id = $request->m_item_id;
        $qty = $request->qty;

        $query = "select itd.id as inv_trx_d_id, 
            pd.amount, pd.base_price, COALESCE(itd.use_amount, 0) as use_amount from inv_trxes it
        join purchases p on it.purchase_id = p.id
        join purchase_ds pd on p.id = pd.purchase_id
        join inv_trx_ds itd on it.id = itd.inv_trx_id and itd.m_item_id = pd.m_item_id
        where it.is_entry = true and pd.m_item_id = ?
        order by pd.created_at asc";
        $datas = DB::select($query, [$m_item_id]);

        $value = 0;
        // dd($datas);
        foreach($datas as $data) {
            if ($qty > $data->amount) {
                $value += ($data->amount - $data->use_amount) * $data->base_price;
                $qty -= $data->amount;

                DB::table('inv_trx_ds')
                ->where('id', $data->inv_trx_d_id)
                ->update(['use_amount' => ($data->use_amount + $data->amount)]);

            } else {
                $value += $qty * $data->base_price;
                DB::table('inv_trx_ds')
                ->where('id', $data->inv_trx_d_id)
                ->update(['use_amount' => ($data->use_amount + $qty)]);
                break;
            }
        }

        return response()->json(['data'=> array('value' => $value)]);
    }

    public function getPenjualanKeluarList() {
        $site_id = null;
        if(isset($_GET['site_id']))
            $site_id = $_GET['site_id'];

        if ($site_id != null) {
            $query = 'select 
                    (SELECT SUM(amount*base_price) FROM inv_sale_ds WHERE inv_sale_id = inv_sales.id) AS total_amount,
                    inv_sales.*, customers.coorporate_name 
                from inv_sales left join customers on customers.id=inv_sales.customer_id WHERE inv_sales.site_id = ?';
            $datas = DB::select($query, [$site_id]);
        } else {
            $query = 'select 
                    (SELECT SUM(amount*base_price) FROM inv_sale_ds WHERE inv_sale_id = inv_sales.id) AS total_amount,
                    inv_sales.*, customers.coorporate_name 
                from inv_sales left join customers on customers.id=inv_sales.customer_id';
            $datas = DB::select($query);
        } 

        foreach($datas as $data){
            $data->sites = Site::find($data->site_id);
        }

        return response()->json(['data'=>$datas]);
    }

    public function getListPenjualanKeluarDetail($id)
    {
        $datas = DB::select("
            select * from inv_sale_ds
            where inv_sale_id = ?
        ", [$id]);

        foreach($datas as $data){
            $data->m_items = MItem::find($data->m_item_id);
            $data->m_units = MUnit::find($data->m_items->m_unit_id);

        }

        return response()->json(['data'=>$datas]);
    }

    public function getListReturnMaterial()
    {
        
        $data = InvReturn::all();
        foreach ($data as $key => $value) {
            $data[$key]['rab'] = Rab::where('id', $value->rab_id)->get(['no', 'base_price', 'project_id']);
            $data[$key]['projects']= Project::where('id', $data[$key]['rab'][0]->project_id)->get(['site_id', 'name', 'area']);
            $data[$key]['site'] = Site::where('id', $data[$key]['projects'][0]->site_id)->get(['name']);
        }
     
        return response()->json(['data'=>$data]);
    }
    public function getListReturnMaterialDetail($id)
    {
        $data = InvReturnD::where('inv_return_id', $id)->get();
        foreach ($data as $key => $value) {
            $data[$key]['m_items'] = MItem::find($value->m_item_id);
            $data[$key]['m_units']= MUnit::find($value->m_unit_id);
        }
     
        return response()->json(['data'=>$data]);
    }
    public function getInvTrxD($inv_trx_id){
        $datas = InvTrxD::where('inv_trx_id', $inv_trx_id)
                ->get();

        foreach($datas as $data){
            $data['m_items'] = DB::select('select * from m_items where id = ' . $data['m_item_id'])[0];
            $data['m_units'] = MUnit::find($data['m_unit_id']);
        }
        
        return response()->json(['data'=>$datas]);
    }

    public function getMaterialRequestSuggestion() {
        $site_id = null;
        if(isset($_GET['site_id']))
            $site_id = $_GET['site_id'];

        if($site_id != null) {
            $datas = DB::select("
                select 
                    ird.id as inv_request_d_id,
                    ird.m_item_id,
                    mi.name as m_item_name,
                    mi.no as m_item_no,
                    ird.amount as volume,
                    mi.m_unit_id,
                    mu.name as m_unit_name
                from inv_requests ir
                join inv_request_ds ird on ir.id = ird.inv_request_id
                join m_items mi on ird.m_item_id = mi.id
                join m_units mu on mi.m_unit_id = mu.id
                where ird.buy_date is null
                    and ir.site_id = ?
            ", [$site_id]);
        } else {
            $datas = DB::select("
                select 
                    ird.id as inv_request_d_id,
                    ird.m_item_id,
                    mi.name as m_item_name,
                    mi.no as m_item_no,
                    ird.amount as volume,
                    mi.m_unit_id,
                    mu.name as m_unit_name
                from inv_requests ir
                join inv_request_ds ird on ir.id = ird.inv_request_id
                join m_items mi on ird.m_item_id = mi.id
                join m_units mu on mi.m_unit_id = mu.id
                where ird.buy_date is null
            ");
        }

        foreach($datas as $data){
            $data->m_items = MItem::find($data->m_item_id);
            $data->m_units = MUnit::find($data->m_unit_id);
        }

        return response()->json(['data'=>$datas]);
    }

    public function calculateAllMaterialByProjectReqDevelopmentId($projectReqDevelopmentId) {
        $data_project_req_developments = DB::table('project_req_developments')
                                        ->join('rabs', 'project_req_developments.rab_id', 'rabs.id')
                                        ->join('orders', 'orders.id', 'rabs.order_id')
                                        ->join('order_ds', 'order_ds.order_id', 'orders.id')
                                        ->join('products', 'order_ds.product_id', 'products.id')
                                        ->join('kavlings', 'kavlings.id', 'products.kavling_id')
                                        ->where('project_req_developments.id', $projectReqDevelopmentId)
                                        ->select('project_req_developments.rab_id', 'project_req_developments.total', 'products.amount_set', 'kavlings.amount')
                                        ->first();
        
        $rab_id = $data_project_req_developments->rab_id;
        $qty_permintaan =  $data_project_req_developments->total;
        $amount_set =  $data_project_req_developments->amount_set;
        $total_kavling =  $data_project_req_developments->amount;

        $query_group_m_items = "SELECT pwd.m_item_id, SUM(pwd.amount) as amount  FROM project_worksub_ds pwd
                                JOIN project_worksubs pws ON pwd.project_worksub_id = pws.id
                                JOIN project_works pw ON pws.project_work_id = pw.id
                                JOIN m_items mi ON pwd.m_item_id = mi.id
                                WHERE pw.rab_id = " . $rab_id . "
                                GROUP BY pwd.m_item_id";

        $data_m_item_id = DB::select($query_group_m_items);


        foreach ($data_m_item_id as $key => $m_items) {
            // $query_hitung = "SELECT pwd.m_item_id, pwd.amount_unit_child, SUM(pwd.qty_item) as qty_item, MIN(mi.amount_unit_child) as ref_amount_unit_child FROM project_worksub_ds pwd
            //             JOIN project_worksubs pws ON pwd.project_worksub_id = pws.id
            //             JOIN project_works pw ON pws.project_work_id = pw.id
            //             JOIN m_items mi ON pwd.m_item_id = mi.id
            //             WHERE pw.rab_id = " . $rab_id . "
            //             AND pwd.m_item_id = " . $m_items->m_item_id ."
            //             GROUP BY pwd.m_item_id, pwd.amount_unit_child
            //             ORDER BY pwd.amount_unit_child DESC";
        
            // $data_hitung = DB::select($query_hitung);
            // // dd($data_hitung);

            // $data_hitung2 = array();
            // for ($i=0; $i < count($data_hitung) ; $i++) { 
            //     for ($j=0; $j < ($data_hitung[$i]->qty_item * $qty_permintaan); $j++) { 
            //         array_push($data_hitung2, array(
            //             "m_item_id" => $data_hitung[$i]->m_item_id,
            //             "amount_unit_child" => $data_hitung[$i]->amount_unit_child,
            //             "is_hitung" => false
            //         ));
            //     }
            // }

            // $amount_unit_child = $m_items->amount_unit_child;
            // $count_bahan = 0;
            // $amount_sisa = 0;
            // $test = 0;
            // $jumlah = 0;
            // while($test < count($data_hitung2)) {
            //     $sisa = $amount_unit_child;
            //     $skip = false;
            //     $last_index_check = 0;
            //     for ($i=0; $i < count($data_hitung2) ; $i++) { 
            //         if ($data_hitung2[$i]['is_hitung'] == false && $skip == false) {
            //             $temp_sisa = $sisa;
            //             $sisa = $sisa - $data_hitung2[$i]['amount_unit_child'];
            //             if ($sisa == 0) {
            //                 $count_bahan++;
            //                 $data_hitung2[$i]['is_hitung'] = true;
            //                 $skip = true;
            //             } 
            //             else if ($sisa > 0) {
            //                 $data_hitung2[$i]['is_hitung'] = true; 
            //             } 
            //             else if ($sisa < 0) {
            //                 $sisa = $temp_sisa;
            //             } 
            //         }
            //     }
            //     if ($sisa > 0 && $sisa != $amount_unit_child) {
            //         $count_bahan++;
            //         $amount_sisa += $sisa;
            //     }
            //     $test++;
            // }
            $total_amount = (int)($m_items->amount * ($qty_permintaan/$total_kavling));

            // insert to project_req_development_ds
            $project_req_development_ds = DB::table('project_req_development_ds')
                                        ->where('project_req_development_id', $projectReqDevelopmentId)
                                        ->where('m_item_id', $m_items->m_item_id)
                                        ->first();
            
            if ($project_req_development_ds == null) {
                DB::table('project_req_development_ds')->insert([
                    [
                        'project_req_development_id' => $projectReqDevelopmentId,
                        'm_item_id' => $m_items->m_item_id,
                        'amount' => $total_amount
                    ]
                ]);
            } else {
                DB::table('project_req_development_ds')
                            ->where('project_req_development_id', $projectReqDevelopmentId)
                            ->where('m_item_id', $m_items->m_item_id)
                            ->update(['amount' => $total_amount]);
            }
        }

        return response()->json(['message'=>'Success calculate material']);
    }
    public function getByPurchaseAssetId($purchaseId)
    {
        $datas = InvTrx::where('purchase_asset_id', $purchaseId)->get();
        foreach($datas as $data){
            $invTrxDs = $data -> InvTrxDs;
            $data['purchase_assets'] = PurchaseAsset::find($purchaseId);

            foreach($invTrxDs as $invTrxD){
                $invTrxD['m_items'] = MItem::find($invTrxD['m_item_id']);
                $invTrxD['m_units'] = MUnit::find($invTrxD['m_unit_id']);
            }
        }
        
        return response()->json(['data'=>$datas]);
    }
    public function getListInvRequestBarangDetail($id)
    {
        $datas = DB::select("
            select inv_trx_ds.* from inv_trx_ds join inv_trxes on inv_trx_ds.inv_trx_id = inv_trxes.id
            where inv_request_id = ?
        ", [$id]);

        foreach($datas as $data){
            $data->m_items = MItem::find($data->m_item_id);
            $data->m_units = MUnit::find($data->m_unit_id);
        }

        return response()->json(['data'=>$datas]);
    }
}
