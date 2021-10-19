<?php
namespace App\Http\Controllers\INV;

use App\Models\InvTrx;
use App\Models\InvTrxD;
use App\Models\Purchase;
use App\Models\MItem;
use App\Models\MUnit;
use App\Models\Site;
use App\Models\TransferStock;
use App\Models\MSupplier;
use App\Models\Rab;
use App\Models\Project;
use App\Models\InvRequest;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
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

    public function getStokSite($id){
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
                    (COALESCE(inv_in.amount, 0)) as amount_in,
                    (COALESCE(inv_out.amount, 0)) as amount_out,
                    ((COALESCE(inv_in.amount, 0)) - (COALESCE(inv_out.amount, 0))) as stok,
                    inv_in.updated_at as last_update_in,
                    inv_out.updated_at as last_update_out
                from (select site_id, m_item_id, sum(amount) as amount, max(it.updated_at) as updated_at from inv_trxes it
                join inv_trx_ds itd on it.id = itd.inv_trx_id
                where is_entry = true and site_id = ".$id."
                group by site_id, m_item_id) inv_in
                full outer join (select site_id, m_item_id, sum(amount) as amount, max(it.updated_at) as updated_at from inv_trxes it
                join inv_trx_ds itd on it.id = itd.inv_trx_id
                where is_entry = false and site_id = ".$id."
                group by site_id, m_item_id) inv_out on inv_in.m_item_id = inv_out.m_item_id and inv_in.site_id = inv_out.site_id
                ");
        } else {
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
                where is_entry = true and site_id = ".$id." and m_item_id = ".$m_item_id."
                group by site_id, m_item_id) inv_in
                full outer join (select site_id, m_item_id, sum(amount) as amount, max(it.updated_at) as updated_at from inv_trxes it
                join inv_trx_ds itd on it.id = itd.inv_trx_id
                where is_entry = false and site_id = ".$id." and m_item_id = ".$m_item_id."
                group by site_id, m_item_id) inv_out on inv_in.m_item_id = inv_out.m_item_id and inv_in.site_id = inv_out.site_id
                ");
        }

        foreach($datas as $data) {
            $data->sites = Site::find($data->site_id);
            $data->m_items = DB::select('select * from m_items where id = ' . $data->m_item_id)[0];
            $data->m_units = MUnit::find($data->m_items->m_unit_id);

            // harga satuan (dari tanggal terakhir)
            $last_price = DB::select("select pd.base_price from inv_trxes it
            join purchases p on it.purchase_id = p.id
            join purchase_ds pd on p.id = pd.purchase_id
            where it.is_entry = true and it.site_id = ? and pd.m_item_id = ?
            order by it.created_at desc limit 1", [$data->site_id, $data->m_item_id]);
            $data->last_price = $last_price != null ? $last_price[0]->base_price : 0;

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

    public function getListRequestBarang(){
        $datas = DB::select("
            select ir.*, r.no as rab_no, mt.city as site_location, COALESCE(s.name, s2.name) as site_name, p.name as project_name 
                from inv_requests ir
                left join rabs r on ir.rab_id = r.id
                left join projects p on r.project_id = p.id
                left join sites s on p.site_id = s.id
                left join m_cities mt on s.m_city_id = mt.id
                left join sites s2 on ir.site_id = s2.id
                where ir.deleted_at is null
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
            $data->m_items = MItem::find($data->m_item_id);
            $data->m_units = MUnit::find($data->m_unit_id);
        }

        return response()->json(['data'=>$datas]);
    }

    public function getListPengeluaranBarang(){
        $datas = DB::select("
            select ir.*, r.no as rab_no, mt.city as site_location, COALESCE(s.name, s2.name) as site_name, p.name as project_name 
            from inv_requests ir
            left join inv_trxes it on ir.id = it.inv_request_id
            left join rabs r on ir.rab_id = r.id
            left join projects p on r.project_id = p.id
            left join sites s on p.site_id = s.id
            left join m_cities mt on s.m_city_id = mt.id
            left join sites s2 on ir.site_id = s2.id
            where it.id is null
        ");

        return response()->json(['data' => $datas]);
    }

    // Post Method
    public function getMutasiStok(Request $request) {
        $site_id = $request->site_id;
        $date_gte = $request->date_gte;
        $date_lte = $request->date_lte;
        $m_item_id = $request->m_item_id;
        $is_entry = $request->is_entry;

        $query = "";
        $query .= "select itd.m_item_id, itd.m_unit_id, itd.amount, it.is_entry, it.inv_trx_date, it.site_id, it.purchase_id, it.transfer_stock_id, itd.value, * from inv_trxes it
                    join inv_trx_ds itd on it.id = itd.inv_trx_id
                    where true ";
        if($site_id != null)
            $query .= " and it.site_id = " . $site_id;
        if($date_gte != null)
            $query .= " and it.inv_trx_date >= '" . $date_gte . "'";
        if($date_lte != null)
            $query .= " and it.inv_trx_date <= '" . $date_lte . "'";
        if($m_item_id != 'all')
            $query .= " and itd.m_item_id = " . $m_item_id;
        if($is_entry != 'all')
            $query .= " and it.is_entry = " . $is_entry;

        $datas = DB::select($query, []);

        foreach($datas as $data){
            $id = $data->inv_request_id;
            $value = 0;
            $data->m_items = MItem::find($data->m_item_id);
            $data->m_units = MUnit::find($data->m_unit_id);
            $data->transfer_stok = TransferStock::find($data->transfer_stock_id);
            if($data->transfer_stok != null) {
                $data->transfer_stok->sites_to = Site::find($data->transfer_stok->site_to);
                $data->transfer_stok->sites_from = Site::find($data->transfer_stok->site_from);
            }
            $data->purchase = Purchase::find($data->purchase_id);
            if($data->purchase != null){
                $data->purchase->m_suppliers = MSupplier::find($data->purchase->m_supplier_id);
                $value = DB::select('
                            SELECT base_price FROM purchase_ds WHERE purchase_id = ? AND m_item_id = ?
                        ', [$data->purchase_id, $data->m_item_id])[0]->base_price;
            }
            $data->inv_request = InvRequest::find($data->inv_request_id);
            if($data->inv_request != null && $data->inv_request->rab_id != null){
                $rabs = Rab::find($data->inv_request->rab_id);
                $data->inv_request->project = Project::find($rabs->project_id);
            }

            if($data->is_entry == false)
                $data->value = $data->value;
            else 
                $data->value = $value;
        }
        

        // dd($request->);

        return response()->json(['data'=>$datas]);
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
                    * 
                from inv_sales WHERE inv_sales.site_id = ?';
            $datas = DB::select($query, [$site_id]);
        } else {
            $query = 'select 
                    (SELECT SUM(amount*base_price) FROM inv_sale_ds WHERE inv_sale_id = inv_sales.id) AS total_amount,
                    * 
                from inv_sales';
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

}
