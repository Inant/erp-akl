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
        $rabs = Rab::join('projects as p', 'p.id', '=', 'rabs.project_id')
                ->join('sites as s', 's.id','=','p.site_id')
                ->join('m_cities as t', 't.id','=','s.m_city_id')
                ->select(
                    [
                        'rabs.id AS rab_id',
                        'rabs.no AS rab_no', 
                        's.name AS site_name', 
                        't.city AS site_location', 
                        'p.name AS project_name', 
                        'rabs.is_final AS is_final', 
                        'rabs.stats_code AS status',
                        'rabs.base_price AS rab_value',
                        'p.base_price AS project_value'
                    ]
                )
                ->get();
        return response()->json(['data'=>$rabs]);
    }

    public function getRabListByProjectId($projectId)
    {
        $rabs = Rab::join('projects as p', 'p.id', '=', 'rabs.project_id')
                ->join('sites as s', 's.id','=','p.site_id')
                ->join('m_cities as t', 't.id','=','s.m_city_id')
                ->select(['rabs.id AS rab_id','rabs.no AS rab_no', 's.name AS site_name', 't.city AS site_location', 'p.name AS project_name', 'rabs.is_final AS is_final', 'rabs.stats_code AS status'])
                ->where('rabs.project_id', $projectId)
                ->get();
        return response()->json(['data'=>$rabs]);
    }

    public function getRabById($id)
    {
        $rabs = Rab::join('projects as p', 'p.id', '=', 'rabs.project_id')
                ->join('sites as s', 's.id','=','p.site_id')
                ->join('m_cities as t', 't.id','=','s.m_city_id')
                ->select(['rabs.id AS rab_id','rabs.no AS rab_no', 's.name AS site_name', 't.city AS site_location', 'p.name AS project_name', 'p.id AS project_id'])
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
            $projectWorksubs = $projectWork -> ProjectWorksubs;
            foreach($projectWorksubs as $projectWorksub)
            {
                $projectWorksubDs = $projectWorksub -> ProjectWorksubDs;
                $projectWorksub['m_units'] = MUnit::find($projectWorksub['m_unit_id']);

                foreach($projectWorksubDs as $projectWorksubD){
                    $projectWorksubD['m_items'] = MItem::find($projectWorksubD['m_item_id']);
                    $projectWorksubD['m_units'] = MUnit::find($projectWorksubD['m_unit_id']);
                }
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
                        pwsd.amount as volume,
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
                        mi.no as m_item_no
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
                ", [$date_now, $date_buy, $site_id]);
        } else {
            $datas = DB::select("
                        select 
                        rabs.id as rab_id,
                        rabs.no as rab_no, 
                        pwsd.id as project_worksub_d_id,
                        pwsd.m_item_id as m_item_id,
                        mi.name as m_item_name,
                        pwsd.amount as volume,
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
                        mi.no as m_item_no
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
                    and (pws.work_start + interval '-1' day * mi.late_time::integer) <= ?
                ", [$date_now, $date_buy]);
        }

        return response()->json(['hari_pembelian_rutin' => $hari_pembelian_rutin, 
            'date_now' => $date_now,
            'date_buy' => $date_buy, 'date_prev_buy' => $date_prev_buy, 'data'=>$datas]);
    }

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
                    and rabs.id = ?
                    group by rabs.id, pwsd.m_item_id
                ", [$rabId]);

        foreach($datas as $data) {
            $used_datas = DB::select("select 
                            ir.rab_id as rab_id, 
                            itd.m_item_id as m_item_id,
                            sum(itd.amount) as amount
                        from inv_requests ir
                        join inv_trxes it on ir.id = it.inv_request_id
                        join inv_trx_ds itd on it.id = itd.inv_trx_id
                        where ir.rab_id = ? and itd.m_item_id = ?
                        group by ir.rab_id, itd.m_item_id", [$rabId, $data->m_item_id]);
            
            $data->used_amount = count($used_datas) > 0 ? $used_datas[0]->amount : 0;
        }

        return response()->json(['data' => $datas]);
    }

    public function getAllMaterial(){
        $datas = DB::select("
                    select 
                        mi.*,
                        mu.name AS m_unit_name
                    from m_items mi
                    join m_units mu ON mi.m_unit_id = mu.id
                    order by mi.name asc
                    ");

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

}
