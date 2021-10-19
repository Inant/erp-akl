<?php
namespace App\Http\Controllers\INV;

use App\Models\Project;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;

class ProjectController extends Controller
{
    public function getBySiteId($siteId)
    {
        $data = Project::where(['site_id'=> $siteId, 'sale_status' => 'Available'])
                ->orderBy('name','asc')
                ->get();
        return response()->json(['data'=>$data]);
    }
}
