<?php
namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Site;
use App\Models\Rab;
use App\Models\ProjectWork;
use App\Models\ProjectWorksubD;
use App\Models\ProjectWorksub;
use App\Models\MUnit;
use App\Models\MItem;
use App\Models\MBestPrice;
use App\Models\GeneralSetting;
use Illuminate\Http\Request;
use DB;
use Carbon\Carbon;

class RabController extends Controller
{
    public function getKavlingListAll()
    {
        $projects = Project::all();
        foreach ($projects as $project)
        {
            $site = $project->Site;
            $site->MCity;
        }
        return response()->json(['data'=>$projects]);
    }

    public function getKavlingList()
    {
        $projects = Project::whereNotNull('base_price')->where('sale_status','Available')->get();
        foreach ($projects as $project)
        {
            $site = $project->Site;
            $site->MCity;
            $project->name = $site->name . ' - ' . $project->name;
        }
        return response()->json(['data'=>$projects]);
    }
    
    public function getRabList()
    {
        $site_id = isset($_GET['site_id']) ?  $_GET['site_id'] : null;
        $rabs = Rab::join('projects as p', 'p.id', '=', 'rabs.project_id')
                ->join('sites as s', 's.id','=','p.site_id')
                ->join('kavlings as k', 'k.id','=','rabs.kavling_id')
                // ->join('products as pr', 'pr.id','=','o.product_id')
                ->join('m_cities as t', 't.id','=','s.m_city_id')
                ->select(
                    [
                        'rabs.id AS rab_id',
                        'rabs.no AS rab_no', 
                        'rabs.estimate_end', 
                        'rabs.is_final_production', 
                        'rabs.date_final_production', 
                        's.name AS site_name', 
                        't.city AS site_location', 
                        'p.name AS project_name', 
                        'rabs.is_final AS is_final', 
                        'rabs.stats_code AS status',
                        'rabs.base_price AS rab_value',
                        'p.base_price AS project_value',
                        'k.name as kavling_name'
                        // 'pr.name AS product_name',
                        // 'o.total AS total_order'
                    ]
                    );

        if ($site_id != null)
            $rabs->where('p.site_id', $site_id);

        return response()->json(['data'=>$rabs->get()]);
    }

    public function getRabListByProjectId($projectId)
    {
        $rabs = Rab::join('projects as p', 'p.id', '=', 'rabs.project_id')
                ->join('sites as s', 's.id','=','p.site_id')
                ->join('m_cities as t', 't.id','=','s.m_city_id')
                ->join('order_ds as o', 'o.id','=','rabs.order_d_id')
                ->join('products as pr', 'pr.id','=','o.product_id')
                ->select(['rabs.id AS rab_id','rabs.no AS rab_no', 's.name AS site_name', 't.city AS site_location', 'p.name AS project_name', 'rabs.is_final AS is_final', 'rabs.stats_code AS status','pr.name AS product_name', 'o.total AS total_order'])
                ->where('rabs.project_id', $projectId)
                ->get();
        return response()->json(['data'=>$rabs]);
    }

    public function getRabById($id)
    {
        $rabs = Rab::join('projects as p', 'p.id', '=', 'rabs.project_id')
                ->join('sites as s', 's.id','=','p.site_id')
                ->join('m_cities as t', 't.id','=','s.m_city_id')
                ->join('orders as o', 'o.id','=','rabs.order_id')
                ->join('kavlings as k', 'k.id','=','rabs.kavling_id')
                ->select(['rabs.id AS rab_id', 'rabs.is_final', 'rabs.estimate_end', 'rabs.no AS rab_no', 's.name AS site_name', 't.city AS site_location', 'p.name AS project_name', 'p.id AS project_id', 'o.id as order_id', 'rabs.kavling_id', 'k.name as type_kavling'])
                ->where('rabs.id', $id)
                ->get();
        return response()->json(['data'=>$rabs]);
    }
    public function getDetail()
    {
        $sites = Site::all();
        foreach ($sites as $site)
        {
            $site -> MTown;
            $projects = $site -> Projects;
            foreach ($projects as $project)
            {
                $projectWorks = $project -> ProjectWorks;
                foreach ($projectWorks as $projectWork)
                {
                    $projectWorksubs = $projectWork -> ProjectWorksubs;
                    foreach($projectWorksubs as $projectWorksub)
                    {
                        $projectWorksub -> ProjectWorksubDs;
                    }
                }
            }
        }
        return response()->json(['data'=> $sites]); 
    }

    public function getDetailbyRabId($id)
    {
        $site = Site::find($id);
        $site -> MTown;
        $projects = $site -> Projects;
        foreach ($projects as $project)
        {
            $projectWorks = $project -> ProjectWorks;
            foreach ($projectWorks as $projectWork)
            {
                $projectWorksubs = $projectWork -> ProjectWorksubs;
                foreach($projectWorksubs as $projectWorksub)
                {
                    $projectWorksub -> ProjectWorksubDs;
                }
            }
        }
        return response()->json(['data'=> $site]); 
    }

    public function getProjectWorkByRabId($rabId)
    {
        // $data = ProjectWork::where('rab_id', $rabId)
        //         ->get();
        $data = Rab::find($rabId);
        $projectWorks = $data -> ProjectWorks;
        foreach ($projectWorks as $projectWork)
        {
            $projectWork->product=DB::table('products')->where('id', $projectWork->product_id)->first();
            $projectWorksubs = $projectWork -> ProjectWorksubs;
            foreach($projectWorksubs as $projectWorksub)
            {
                $projectWorksubDs = $projectWorksub -> ProjectWorksubDs;
                $projectWorksub['m_units'] = MUnit::find($projectWorksub['m_unit_id']);

                $total_material = 0;
                foreach($projectWorksubDs as $projectWorksubD){
                    $projectWorksubD['m_items'] = MItem::find($projectWorksubD['m_item_id']);
                    $projectWorksubD['m_units'] = MUnit::find($projectWorksubD['m_unit_id']);
                    $best_prices = MBestPrice::where('m_item_id', $projectWorksubD['m_item_id'])
                        ->first();
                    $projectWorksubD['best_price'] = $best_prices['best_price'];
                    $projectWorksubD['unit_child'] = MUnit::find($projectWorksubD['m_items']['m_unit_child']);
                    $total_material += $best_prices['best_price'] *  $projectWorksubD['amount'];
                }
                $projectWorksub['total_material'] = $total_material;
            }
        }

        return response()->json(['data'=>$data]);
    }

    public function getMaterialPembelianRutin(){
        $date_now = Carbon::now()->toDateString();
        $day_of_week = Carbon::now()->dayOfWeek;
        $hari_pembelian_rutin = GeneralSetting::where('gs_code', 'HARI_PEMBELIAN_RUTIN')->first();
        $hari_pembelian_rutin = $hari_pembelian_rutin != null ? $hari_pembelian_rutin->gs_value : 0;
        if($day_of_week == $hari_pembelian_rutin){
            $date_buy = Carbon::now()->toDateString();
            $date_prev_buy = Carbon::now()->addDays(-30)->toDateString();
        }
        else{
            $date_buy = Carbon::now()->addDays(30 - ($day_of_week - $hari_pembelian_rutin))->toDateString();
            $date_prev_buy = Carbon::now()->addDays(-($day_of_week - $hari_pembelian_rutin))->toDateString();
        }

        $site_id = null;
        if(isset($_GET['site_id']))
            $site_id = $_GET['site_id'];

        if($site_id != null) {
            $datas = DB::select("
                        select 
                        rabs.id as rab_id,
                        rabs.no as rab_no, 
                        pwsd.id as project_worksub_d_id,
                        pwsd.m_item_id as m_item_id,
                        mi.name as m_item_name,
                        mi.category as category,
                        (pwsd.amount) as volume,
                        pwsd.m_unit_id as m_unit_id,
                        mu.name as m_unit_name,
                        pws.work_start as use_date,
                        mi.late_time as late_time,
                        mbp.best_price as best_price,
                        ms.name as supplier_name,
                        date(pws.work_start + interval '-1' day * mi.late_time::integer) as due_date,
                        (case 
                            when (date(pws.work_start + interval '-1' day * mi.late_time::integer) < ?)
                                then 'Late'
                            else
                                'Time to Buy'
                        end) as late_stat,
                        mi.no as m_item_no,
                        mi.m_group_item_id,
                        mi.amount_in_set
                    from rabs
                    join project_works pw ON rabs.id = pw.rab_id
                    join project_worksubs pws ON pw.id = pws.project_work_id
                    join project_worksub_ds pwsd ON pws.id = pwsd.project_worksub_id
                    join m_items mi ON pwsd.m_item_id = mi.id
                    join m_units mu ON pwsd.m_unit_id = mu.id
                    join projects p ON rabs.project_id = p.id
                    left join m_best_prices mbp 
                        join m_suppliers ms ON  mbp.m_supplier_id = ms.id
                    ON mi.id = mbp.m_item_id
                    where buy_date is null
                    and rabs.is_final = true
                    and (pws.work_start + interval '-1' day * mi.late_time::integer) <= ?
                    and p.site_id = ?
                    ". (isset($_GET['rab_no']) ? "and rabs.no = '".$_GET['rab_no']."' " : "") ."
                ", [$date_now, $date_buy, $site_id]);
        } else {
            $datas = DB::select("
                        select 
                        rabs.id as rab_id,
                        rabs.no as rab_no, 
                        pwsd.id as project_worksub_d_id,
                        pwsd.m_item_id as m_item_id,
                        mi.name as m_item_name,
                        (pwsd.amount) as volume,
                        pwsd.m_unit_id as m_unit_id,
                        mu.name as m_unit_name,
                        pws.work_start as use_date,
                        mi.late_time as late_time,
                        mbp.best_price as best_price,
                        ms.name as supplier_name,
                        date(pws.work_start + interval '-1' day * mi.late_time::integer) as due_date,
                        (case 
                            when (date(pws.work_start + interval '-1' day * mi.late_time::integer) < ?)
                                then 'Late'
                            else
                                'Time to Buy'
                        end) as late_stat,
                        mi.no as m_item_no,
                        mi.m_group_item_id,
                        mi.amount_in_set
                    from rabs
                    join project_works pw ON rabs.id = pw.rab_id
                    join project_worksubs pws ON pw.id = pws.project_work_id
                    join project_worksub_ds pwsd ON pws.id = pwsd.project_worksub_id
                    join m_items mi ON pwsd.m_item_id = mi.id
                    join m_units mu ON pwsd.m_unit_id = mu.id
                    left join m_best_prices mbp 
                        join m_suppliers ms ON  mbp.m_supplier_id = ms.id
                    ON mi.id = mbp.m_item_id
                    where buy_date is null
                    and rabs.is_final = true
                    ". (isset($_GET['rab_no']) ? "and rabs.no = '".$_GET['rab_no']."' " : "") ."
                    and (pws.work_start + interval '-1' day * mi.late_time::integer) <= ?
                ", [$date_now, $date_buy]);
        }

        return response()->json(['hari_pembelian_rutin' => $hari_pembelian_rutin, 
            'date_now' => $date_now,
            'date_buy' => $date_buy, 'date_prev_buy' => $date_prev_buy, 'data'=>$datas]);
    }
    // bug join di material pembelian rutin
    // JOIN orders ord ON rabs.order_id = ord.id
    // JOIN order_ds od ON ord.id = od.order_id
    // JOIN products prd ON od.product_id = prd.id
    public function getAllMaterialByRabId($rabId){
        $datas = DB::select("
                    select 
                        rabs.id as rab_id,
                        pwsd.m_item_id,
                        MIN(mi.name) as m_item_name,
                        SUM(pwsd.amount) as amount
                    from rabs
                    join project_works pw ON rabs.id = pw.rab_id
                    join project_worksubs pws ON pw.id = pws.project_work_id
                    join project_worksub_ds pwsd ON pws.id = pwsd.project_worksub_id
                    join m_items mi ON pwsd.m_item_id = mi.id
                    join m_units mu ON pwsd.m_unit_id = mu.id
                    where rabs.is_final = true
                    and mi.deleted_at is null
                    and rabs.id = ?
                    group by rabs.id, pwsd.m_item_id
                ", [$rabId]);

        foreach($datas as $data) {
            // $used_datas = DB::select("select 
            //                 ir.rab_id as rab_id, 
            //                 itd.m_item_id as m_item_id,
            //                 sum(itd.amount) as amount
            //             from inv_requests ir
            //             join inv_trxes it on ir.id = it.inv_request_id
            //             join inv_trx_ds itd on it.id = itd.inv_trx_id
            //             where ir.rab_id = ? and itd.m_item_id = ?
            //             group by ir.rab_id, itd.m_item_id", [$rabId, $data->m_item_id]);
            $used_datas = DB::select("select 
                            ir.rab_id as rab_id, 
                            itd.m_item_id as m_item_id,
                            sum(itd.amount) -
                            coalesce((select sum(itd.amount) as amount
                        from inv_requests ir
                        join inv_trxes it on ir.id = it.inv_request_id
                        join inv_trx_ds itd on it.id = itd.inv_trx_id
                        where ir.rab_id = ".$rabId." and itd.m_item_id = ".$data->m_item_id." and req_type = 'RET_ITEM'), 0) as amount
                        from inv_requests ir
                        join inv_trxes it on ir.id = it.inv_request_id
                        join inv_trx_ds itd on it.id = itd.inv_trx_id
                        where ir.rab_id = ? and itd.m_item_id = ? and req_type != 'RET_ITEM'
                        group by ir.rab_id, itd.m_item_id", [$rabId, $data->m_item_id]);
            
            $data->used_amount = count($used_datas) > 0 ? $used_datas[0]->amount : 0;
        }

        return response()->json(['data' => $datas]);
    }

    public function getAllMaterialByProjectWork($projectWorkId){
        $datas = DB::select("
                    select 
                        pw.id as id,
                        pw.rab_id as rab_id,
                        pwsd.m_item_id,
                        MIN(mi.name) as m_item_name,
                        SUM(pwsd.amount) as amount
                    from rabs
                    join project_works pw ON rabs.id = pw.rab_id
                    join project_worksubs pws ON pw.id = pws.project_work_id
                    join project_worksub_ds pwsd ON pws.id = pwsd.project_worksub_id
                    join m_items mi ON pwsd.m_item_id = mi.id
                    join m_units mu ON pwsd.m_unit_id = mu.id
                    where rabs.is_final = true
                    and mi.deleted_at is null
                    and pw.id = ?
                    group by pw.id, pwsd.m_item_id
                ", [$projectWorkId]);
        // print_r($datas);
        // exit();
        foreach($datas as $data) {
            // $used_datas = DB::select("select 
            //                 ir.rab_id as rab_id, 
            //                 itd.m_item_id as m_item_id,
            //                 sum(itd.amount) as amount
            //             from inv_requests ir
            //             join inv_trxes it on ir.id = it.inv_request_id
            //             join inv_trx_ds itd on it.id = itd.inv_trx_id
            //             where ir.rab_id = ? and itd.m_item_id = ?
            //             group by ir.rab_id, itd.m_item_id", [$rabId, $data->m_item_id]);
            $used_datas = DB::select("select 
                            ir.rab_id as rab_id, 
                            itd.m_item_id as m_item_id,
                            sum(itd.amount) -
                            coalesce((select sum(itd.amount) as amount
                        from inv_requests ir
                        join inv_trxes it on ir.id = it.inv_request_id
                        join inv_trx_ds itd on it.id = itd.inv_trx_id
                        where ir.rab_id = ".$data->rab_id." and ir.project_work_id = ".$data->id." and itd.m_item_id = ".$data->m_item_id." and req_type = 'RET_ITEM'), 0) as amount
                        from inv_requests ir
                        join inv_trxes it on ir.id = it.inv_request_id
                        join inv_trx_ds itd on it.id = itd.inv_trx_id
                        where ir.rab_id = ? and ir.project_work_id = ? and itd.m_item_id = ? and req_type != 'RET_ITEM'
                        group by ir.rab_id, ir.project_work_id, itd.m_item_id", [$data->rab_id, $data->id, $data->m_item_id]);
            
            $data->used_amount = count($used_datas) > 0 ? $used_datas[0]->amount : 0;
        }
        
        return response()->json(['data' => $datas]);
    }
    public function getAllMaterialByRequestDev($req_dev_id){
        $datas = DB::select("select 
                        prdd.id as id,
                        prd.rab_id as rab_id,
                        prdd.m_item_id,
                        mi.name as m_item_name,
                        mi.no as m_item_no,
                        (select sum(pwsd.amount) as amount from rabs
                        join project_works pw ON pw.rab_id = rabs.id
                        join project_worksubs pws ON pws.project_work_id = pw.id
                        join project_worksub_ds pwsd ON pwsd.project_worksub_id = pws.id
                        where rabs.id = prd.rab_id and pwsd.m_item_id = prdd.m_item_id
                        group by pwsd.m_item_id) as amount,
                        prd.total as total_request
                from project_req_development_ds prdd
                join project_req_developments prd ON prdd.project_req_development_id = prd.id
                join m_items mi ON prdd.m_item_id = mi.id
                where prdd.project_req_development_id = ?
                and mi.deleted_at is null
                ", [$req_dev_id]);

        foreach($datas as $data) {
            // $used_datas = DB::select("select 
            //                 ir.rab_id as rab_id, 
            //                 itd.m_item_id as m_item_id,
            //                 sum(itd.amount) -
            //                 coalesce((select sum(itd.amount) as amount
            //             from inv_requests ir
            //             join inv_trxes it on ir.id = it.inv_request_id
            //             join inv_trx_ds itd on it.id = itd.inv_trx_id
            //             where itd.m_item_id = ".$data->m_item_id." and req_type = 'RET_ITEM'), 0) as amount
            //             from inv_requests ir
            //             join inv_trxes it on ir.id = it.inv_request_id
            //             join inv_trx_ds itd on it.id = itd.inv_trx_id
            //             where itd.m_item_id = ".$data->m_item_id." and req_type != 'RET_ITEM'
            //             group by ir.rab_id, ir.project_work_id, itd.m_item_id");
            $used_datas = DB::select("SELECT ir.rab_id as rab_id, 
                                ird.m_item_id as m_item_id,
                                sum(ird.amount) as amount
                        FROM inv_requests ir 
                        join inv_request_ds ird ON ir.id = ird.inv_request_id
                        where ird.m_item_id = ".$data->m_item_id." and ir.rab_id = ".$data->rab_id." and ir.req_type != 'RET_ITEM'
                        group by ir.rab_id, ird.m_item_id
                        ");
            
            $data->used_amount = count($used_datas) > 0 ? $used_datas[0]->amount : 0;
        }

        // $datas = DB::select("
        //             select 
        //                 pw.id as id,
        //                 pw.rab_id as rab_id,
        //                 pwsd.m_item_id,
        //                 MIN(mi.name) as m_item_name,
        //                 SUM(pwsd.amount) as amount
        //             from rabs
        //             join project_works pw ON rabs.id = pw.rab_id
        //             join project_worksubs pws ON pw.id = pws.project_work_id
        //             join project_worksub_ds pwsd ON pws.id = pwsd.project_worksub_id
        //             join m_items mi ON pwsd.m_item_id = mi.id
        //             join m_units mu ON pwsd.m_unit_id = mu.id
        //             where rabs.is_final = true
        //             and mi.deleted_at is null
        //             and pw.id = ?
        //             group by pw.id, pwsd.m_item_id
        //         ", [$projectWorkId]);
        // // print_r($datas);
        // // exit();
        // foreach($datas as $data) {
        //     // $used_datas = DB::select("select 
        //     //                 ir.rab_id as rab_id, 
        //     //                 itd.m_item_id as m_item_id,
        //     //                 sum(itd.amount) as amount
        //     //             from inv_requests ir
        //     //             join inv_trxes it on ir.id = it.inv_request_id
        //     //             join inv_trx_ds itd on it.id = itd.inv_trx_id
        //     //             where ir.rab_id = ? and itd.m_item_id = ?
        //     //             group by ir.rab_id, itd.m_item_id", [$rabId, $data->m_item_id]);
        //     $used_datas = DB::select("select 
        //                     ir.rab_id as rab_id, 
        //                     itd.m_item_id as m_item_id,
        //                     sum(itd.amount) -
        //                     coalesce((select sum(itd.amount) as amount
        //                 from inv_requests ir
        //                 join inv_trxes it on ir.id = it.inv_request_id
        //                 join inv_trx_ds itd on it.id = itd.inv_trx_id
        //                 where ir.rab_id = ".$data->rab_id." and ir.project_work_id = ".$data->id." and ir.project_req_development_id = ".$req_dev_id."
        //                 and itd.m_item_id = ".$data->m_item_id." and req_type = 'RET_ITEM'), 0) as amount
        //                 from inv_requests ir
        //                 join inv_trxes it on ir.id = it.inv_request_id
        //                 join inv_trx_ds itd on it.id = itd.inv_trx_id
        //                 where ir.rab_id = ? and ir.project_work_id = ? and itd.m_item_id = ? and ir.project_req_development_id = ".$req_dev_id." and req_type != 'RET_ITEM'
        //                 group by ir.rab_id, ir.project_work_id, itd.m_item_id", [$data->rab_id, $data->id, $data->m_item_id]);
            
        //     $data->used_amount = count($used_datas) > 0 ? $used_datas[0]->amount : 0;
        // }
        
        return response()->json(['data' => $datas]);
    }

    public function getAllMaterial(){
        $datas = DB::select("
                    select 
                        mi.*,
                        mu.name AS m_unit_name,
                        mip.price as item_prices
                    from m_items mi
                    join m_units mu ON mi.m_unit_id = mu.id
                    left join m_item_prices mip ON mi.id = mip.m_item_id
                    where mi.deleted_at is null
                    order by mi.name asc
                    ");
        foreach ($datas as $key => $value) {
            $value->best_prices=DB::table('m_best_prices')
                                ->join('m_suppliers', 'm_suppliers.id', '=', 'm_best_prices.m_supplier_id')
                                ->where('m_item_id', $value->id)
                                ->select('m_best_prices.best_price', 'm_suppliers.name')
                                ->first();
        }
        return response()->json(['data' => $datas]);
    }

    public function getAllMaterialWithoutATK(){
        $datas = DB::select("
                    select 
                        mi.*,
                        mu.name AS m_unit_name,
                        mip.price as item_prices
                    from m_items mi
                    join m_units mu ON mi.m_unit_id = mu.id
                    left join m_item_prices mip ON mi.id = mip.m_item_id
                    where mi.category != 'ALAT KERJA' and mi.category != 'ATK' and mi.deleted_at is null
                    order by mi.name asc
                    ");
        foreach ($datas as $key => $value) {
            $value->best_prices=DB::table('m_best_prices')
                                ->join('m_suppliers', 'm_suppliers.id', '=', 'm_best_prices.m_supplier_id')
                                ->where('m_item_id', $value->id)
                                ->select('m_best_prices.best_price', 'm_suppliers.name')
                                ->first();
        }
        return response()->json(['data' => $datas]);
    }

    public function getAllMaterialATK(){
        $datas = DB::select("
                    select 
                        mi.*,
                        mu.name AS m_unit_name,
                        mip.price as item_prices
                    from m_items mi
                    join m_units mu ON mi.m_unit_id = mu.id
                    left join m_item_prices mip ON mi.id = mip.m_item_id
                    where mi.category != 'MATERIAL' and mi.category != 'SPARE PART' and mi.category != 'KACA' and mi.deleted_at is null
                    order by mi.name asc
                    ");
        foreach ($datas as $key => $value) {
            $value->best_prices=DB::table('m_best_prices')
                                ->join('m_suppliers', 'm_suppliers.id', '=', 'm_best_prices.m_supplier_id')
                                ->where('m_item_id', $value->id)
                                ->select('m_best_prices.best_price', 'm_suppliers.name')
                                ->first();
        }
        return response()->json(['data' => $datas]);
    }
    
    public function getMaterialCategoryByType($type) {
        $datas = DB::select(
            "select category from m_items where type = " . $type . " group by category order by category asc"
        );
        return response()->json(['data' => $datas]);
    }

    public function getMaterialByCategory(Request $request) {
        $category = $request['category'];

        $datas = DB::select(
            "select m_items.*, m_units.name as m_unit_name FROM m_items
            join m_units ON m_items.m_unit_id = m_units.id 
            WHERE category = '".$category."'"
        );
        return response()->json(['data' => $datas]);
    }

    public function getMaterialByNo(Request $request) {
        $no = $request['no'];

        $datas = DB::select(
            "select m_items.*, m_units.name as m_unit_name FROM m_items
            join m_units ON m_items.m_unit_id = m_units.id 
            WHERE no = '".$no."'"
        );
        return response()->json(['data' => $datas]);
    }
    public function getProjectWorkSubD($id)
    {
        $data = ProjectWorksubD::find($id);
        $data['m_items'] = MItem::find($data['m_item_id']);
        $data['m_units'] = MUnit::where('id', $data['m_unit_id'])->get(['name', 'id', 'code']);
        $data['pws'] = ProjectWorksub::find($data['project_worksub_id']);
     
        return response()->json(['data'=>$data]);
    }

    public function calculateAllMaterialByRabId($rabId) {
        $query_group_m_items = "SELECT pwd.m_item_id, MIN(mi.amount_unit_child) as amount_unit_child  FROM project_worksub_ds pwd
                                JOIN project_worksubs pws ON pwd.project_worksub_id = pws.id
                                JOIN project_works pw ON pws.project_work_id = pw.id
                                JOIN m_items mi ON pwd.m_item_id = mi.id
                                WHERE pw.rab_id = " . $rabId . "
                                AND pwd.tipe_material is null
                                GROUP BY pwd.m_item_id";

        $data_m_item_id = DB::select($query_group_m_items);

        foreach ($data_m_item_id as $key => $m_items) {
            $product = DB::select("SELECT products.amount_set, kavlings.amount as total_kavling FROM rabs
                                JOIN project_works pw ON rabs.id = pw.rab_id
                                JOIN orders ON rabs.order_id = orders.id
                                JOIN order_ds ON orders.id = order_ds.order_id
                                JOIN products ON order_ds.product_id = products.id
                                JOIN kavlings ON products.kavling_id = kavlings.id
                                WHERE rabs.id = " . $rabId);
            // $pd=DB::table('products')->where('id', $m_items->product_id)->first();
            // $amount_set =  $pd != null ? $pd->amount_set : 0;
            $total_kavling =  $product[0] != null ? $product[0]->total_kavling : 0;

            $query_hitung = "SELECT pwd.m_item_id, pw.product_id, pwd.amount_unit_child, SUM(pwd.qty_item) as qty_item, MIN(mi.amount_unit_child) as ref_amount_unit_child FROM project_worksub_ds pwd
                        JOIN project_worksubs pws ON pwd.project_worksub_id = pws.id
                        JOIN project_works pw ON pws.project_work_id = pw.id
                        JOIN m_items mi ON pwd.m_item_id = mi.id
                        WHERE pw.rab_id = " . $rabId . "
                        AND pwd.m_item_id = " . $m_items->m_item_id ."
                        AND pwd.tipe_material is null
                        GROUP BY pwd.m_item_id, pwd.amount_unit_child, pw.product_id
                        ORDER BY pwd.amount_unit_child DESC";
        
            $data_hitung = DB::select($query_hitung);

            $data_hitung2 = array();
            for ($i=0; $i < count($data_hitung) ; $i++) { 
                $pd=DB::table('products')->where('id', $data_hitung[$i]->product_id)->first();
                $amount_set =  $pd != null ? $pd->amount_set : 0;
                for ($j=0; $j < ($data_hitung[$i]->qty_item * $amount_set * $total_kavling) ; $j++) { 
                    array_push($data_hitung2, array(
                        "m_item_id" => $data_hitung[$i]->m_item_id,
                        "amount_unit_child" => $data_hitung[$i]->amount_unit_child,
                        "is_hitung" => false
                    ));
                }
            }

            $amount_unit_child = $m_items->amount_unit_child;
            $count_bahan = 0;
            $amount_sisa = 0;
            $test = 0;
            $jumlah = 0;
            while($test < count($data_hitung2)) {
                $sisa = $amount_unit_child;
                $skip = false;
                $last_index_check = 0;
                for ($i=0; $i < count($data_hitung2) ; $i++) { 
                    if ($data_hitung2[$i]['is_hitung'] == false && $skip == false) {
                        $temp_sisa = $sisa;
                        $sisa = $sisa - $data_hitung2[$i]['amount_unit_child'];
                        if ($sisa == 0) {
                            $count_bahan++;
                            $data_hitung2[$i]['is_hitung'] = true;
                            $skip = true;
                        } 
                        else if ($sisa > 0) {
                            $data_hitung2[$i]['is_hitung'] = true; 
                        } 
                        else if ($sisa < 0) {
                            $sisa = $temp_sisa;
                        } 
                    }
                }
                if ($sisa > 0 && $sisa != $amount_unit_child) {
                    $count_bahan++;
                    $amount_sisa += $sisa;
                }
                $test++;
            }

            $query_material = "SELECT pwd.* FROM project_worksub_ds pwd
                            JOIN project_worksubs pws ON pwd.project_worksub_id = pws.id
                            JOIN project_works pw ON pws.project_work_id = pw.id
                            JOIN m_items mi ON pwd.m_item_id = mi.id
                            WHERE pw.rab_id = " . $rabId . " AND pwd.m_item_id = " . $m_items->m_item_id . "
                            AND pwd.tipe_material is null
                            ORDER BY pwd.amount_unit_child * qty_item DESC";

            $data_material = DB::select($query_material);
            $count_material = count($data_material);
            $pembagian = floor($count_bahan/$count_material);
            $sisa_pembagian = $count_bahan % $count_material;
            $sisa_dari_sisa_pembagian = $sisa_pembagian;
            for ($i=0; $i < $count_material; $i++) { 
                if ($sisa_dari_sisa_pembagian > 0) {
                    $data_material[$i]->amount = $pembagian + 1;
                    $sisa_dari_sisa_pembagian--;
                } else {
                    $data_material[$i]->amount = $pembagian;
                }
                $affected = DB::table('project_worksub_ds')
                            ->where('id', $data_material[$i]->id)
                            ->update(['amount' => $data_material[$i]->amount]);
            }

        }
        $query_hitung_kk = DB::select("SELECT pwd.* FROM project_worksub_ds pwd
                        JOIN project_worksubs pws ON pwd.project_worksub_id = pws.id
                        JOIN project_works pw ON pws.project_work_id = pw.id
                        JOIN m_items mi ON pwd.m_item_id = mi.id
                        WHERE pw.rab_id = " . $rabId . "
                        AND pwd.tipe_material is not null");
        foreach ($query_hitung_kk as $key => $value) {
            DB::table('project_worksub_ds')->where('id', $value->id)->update(array('amount' => $value->qty_item));
        }
        $get_material_all = DB::select("SELECT pwd.* FROM project_worksub_ds pwd
                        JOIN project_worksubs pws ON pwd.project_worksub_id = pws.id
                        JOIN project_works pw ON pws.project_work_id = pw.id
                        JOIN m_items mi ON pwd.m_item_id = mi.id
                        WHERE pw.rab_id = " . $rabId . "");
        foreach ($get_material_all as $key => $value) {
            $best_prices=MBestPrice::where('m_item_id', $value->m_item_id)->first();
            $best_price=$best_prices != null ? $best_prices->best_price : 0;
            DB::table('project_worksub_ds')->where('id', $value->id)->update(array('base_price' => $best_price));
        }
        return response()->json(['message'=>'Success calculate material']);
    }

    public function showAllMaterialGroupByMaterial($rabId) {
        /*$query = "SELECT mi.name as m_item_name, mi.no as m_item_no, mu.name as m_unit_name, * FROM (SELECT pwd.m_item_id, SUM(pwd.amount) as amount, SUM(CASE WHEN pwd.tipe_material='kanan' THEN pwd.amount ELSE 0 END) as kanan, SUM(CASE WHEN pwd.tipe_material='kiri' THEN pwd.amount ELSE 0 END) as kiri, MAX(pwd.base_price) AS base_price FROM project_worksub_ds pwd
        JOIN project_worksubs pws ON pwd.project_worksub_id = pws.id
        JOIN project_works pw ON pws.project_work_id = pw.id
        WHERE pw.rab_id = " . $rabId . "
        GROUP BY pwd.m_item_id) AS A
        JOIN m_items mi ON A.m_item_id = mi.id
        JOIN m_units mu ON mi.m_unit_id = mu.id
        LEFT JOIN m_best_prices mbp ON mi.id = mbp.m_item_id";*/
        /*sebelah sini */
        
        $query = "SELECT mbp.price as best_price, mi.name as m_item_name, mi.no as m_item_no, mu.name as m_unit_name, * FROM (SELECT pwd.m_item_id, SUM(pwd.amount) as amount, SUM(CASE WHEN pwd.tipe_material='kanan' THEN pwd.amount ELSE 0 END) as kanan, SUM(CASE WHEN pwd.tipe_material='kiri' THEN pwd.amount ELSE 0 END) as kiri, MAX(pwd.base_price) AS base_price FROM project_worksub_ds pwd
        JOIN project_worksubs pws ON pwd.project_worksub_id = pws.id
        JOIN project_works pw ON pws.project_work_id = pw.id
        WHERE pw.rab_id = " . $rabId . "
        GROUP BY pwd.m_item_id) AS A
        JOIN m_items mi ON A.m_item_id = mi.id
        JOIN m_units mu ON mi.m_unit_id = mu.id
        LEFT JOIN m_best_prices mbp ON mi.id = mbp.m_item_id
        LEFT JOIN m_item_prices mip ON mi.id = mip.m_item_id";

        $data = DB::select($query);

        return response()->json(['data'=>$data]);
    }

}
