<?php
namespace App\Http\Controllers\INV;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\MItem;
use App\Models\MUnit;
use DB;

class TransferStokController extends Controller
{
    public function getTransferStock()
    {
        $datas = DB::select("
            select ts.*, s1.name as site_from_name, s2.name as site_to_name from transfer_stocks ts
            join sites s1 on ts.site_from = s1.id
            join sites s2 on ts.site_to = s2.id
        ");

        return response()->json(['data'=>$datas]);
    }

    public function getTransferStockDByTransferStockId($transferStockId)
    {
        $datas = DB::select("
            select * from transfer_stock_ds
            where transfer_stock_id = ?
        ", [$transferStockId]);

        foreach($datas as $data){
            $data->m_items = MItem::find($data->m_item_id);
            $data->m_units = MUnit::find($data->m_unit_id);
        }

        return response()->json(['data'=>$datas]);
    }

    public function getStockOpname()
    {
        $datas = DB::select("
            select ts.*, s.name as site_name from stock_opnames ts
            join sites s on ts.site_id = s.id
        ");

        return response()->json(['data'=>$datas]);
    }

    public function getStockOpnameDByStockOpnameId($id)
    {
        $datas = DB::select("
            select * from stock_opname_ds
            where stock_opname_id = ?
        ", [$id]);

        foreach($datas as $data){
            $data->m_items = MItem::find($data->m_item_id);
            $data->m_units = MUnit::find($data->m_items->m_unit_id);
        }

        return response()->json(['data'=>$datas]);
    }
}
