<?php
namespace App\Http\Controllers\Master;

use App\Models\Product;
use App\Models\MUnit;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;

class MasterController extends Controller
{
    public function productList(){
        $data = Product::all();
        foreach($data as $value){
            $unit=MUnit::find($value->m_unit_id);
            $value->m_unit_name=$unit->name;
        }
        return response()->json(['data'=>$data]);
    }
}
