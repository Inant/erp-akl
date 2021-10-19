<?php
namespace App\Http\Controllers\INV;

use App\Models\Project;
use App\Models\Site;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;

class SiteController extends Controller
{
    public function getByTownId($townId)
    {
        $data = Site::where('m_city_id', $townId)
                ->orderBy('name','asc')
                ->get();
        return response()->json(['data'=>$data]);
    }
    public function getSite()
    {
        $data = Site::all();
        return response()->json(['data'=>$data]);
    }
}
