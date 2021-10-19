<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use App\Transaction;
use App\Product;
class ApiController extends Controller
{
    public function suggestPW(Request $request){
        if($request->has('q')){
            $key=$request->q;
            $data=DB::table('project_works')
                        ->select('project_works.*', 'projects.name AS project_name', 'rabs.no AS rab_no', 'inv_requests.no AS inv_no', 'inv_requests.id AS inv_id')
                        ->join('projects', 'projects.id', '=', 'project_works.project_id')
                        ->join('inv_requests', 'inv_requests.project_work_id', '=', 'project_works.id')
                        // ->join('dev_projects', 'dev_projects.inv_request_id', '=', 'inv_requests.id')
                        ->join('rabs', 'rabs.id', '=', 'project_works.rab_id')
                        ->where('project_works.name', 'like', '%'.$key.'%')
                        ->orWhere('projects.name', 'like', '%'.$key.'%')
                        ->orWhere('rabs.no', 'like', '%'.$key.'%')
                        ->orWhere('inv_requests.no', 'like', '%'.$key.'%')
                        ->limit(15)
                        ->get();

            foreach ($data as $key => $value) {
                $getDevProject=DB::table('dev_projects')->where('inv_request_id', $value->inv_id)->first();
                
                if ($getDevProject != null) {
                    if ($getDevProject->is_done == true) {
                        unset($data[$key]);
                    }
                }
            }
            // $data1=array();
            // foreach ($data as $key => $value) {
            //     $data1[]=array('id' => $value->inv_id, 'text' => $value->name);
            // }
            // echo json_encode($data1);
            return $data;
        }
    }
}
