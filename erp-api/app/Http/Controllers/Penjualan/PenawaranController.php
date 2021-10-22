<?php
namespace App\Http\Controllers\Penjualan;

use App\Models\Penawaran;
use App\Models\PenawaranDetail;
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

class PenawaranController extends Controller
{
  public function getPenjualanKeluarList() {
    $site_id = null;
    if(isset($_GET['site_id']))
        $site_id = $_GET['site_id'];

    if ($site_id != null) {
        $query = 'select 
                (SELECT SUM(amount*subtotal) FROM penawaran_detail WHERE penawaran_id = penawaran.id) AS total_amount,
                penawaran.*, customers.coorporate_name 
            from penawaran left join customers on customers.id=penawaran.customer_id WHERE penawaran.site_id = ?';
        $datas = DB::select($query, [$site_id]);
    } else {
        $query = 'select 
                (SELECT SUM(amount*subtotal) FROM penawaran_detail WHERE penawaran_id = penawaran.id) AS total_amount,
                penawaran.*, customers.coorporate_name 
            from penawaran left join customers on customers.id=penawaran.customer_id';
        $datas = DB::select($query);
    } 

    foreach($datas as $data){
        $data->sites = Site::find($data->site_id);
    }

    return response()->json(['data'=>$datas]);
}
}
