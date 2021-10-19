<?php
namespace App\Http\Controllers\CRM;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\Site;
use App\Models\RabRequest;
use App\Models\RabRequestD;
use App\Models\DiscountRequest;
use DB;

class UnitUpdateRequest extends Controller
{

    public function getDiscountRequestById($id)
    {
        $object = DiscountRequest::find($id);
        if ($object['project_id']!= null) $object['project_name'] = Project::select('name','site_id') -> where('id',$object['project_id']) ->get(1);
        else $object['project_name'] = null;            
        return response()->json(['data'=>$object]);
    }

    public function getSpecUpRequestById($id)
    {
        $object = RabRequest::find($id);
        if ($object['project_id']!= null) $object['project_name'] = Project::select('name','site_id') -> where('id',$object['project_id']) ->get(1);
        else $object['project_name'] = null;            
        $object -> RabRequestDs;
        return response()->json(['data'=>$object]);
    }
    public function getSpecUpRequestByProjectId($id)
    {
        $obejct = null;
        $object = RabRequest::select('id','no','amount') -> where('project_id',$id) -> where('is_approved',true) ->get(1);
        
        // if($object!=null)
        // {
        //     if($object[0]['amount']==null) $object[0]['amount'] == 0;
        // }
        return response()->json(['data'=>$object]);
    }

    public function saveDiscountRequest(Request $request)
    {
        DB::beginTransaction();
        if($request['id']==null){
            $discRequest =  new DiscountRequest();
            $discRequest['project_id'] = $request['project_id'];
            $discRequest['no'] = $request['no'];
            $discRequest['sale_trx_id'] = $request['sale_trx_id'];
            $discRequest['amount_requested'] = $request['amount_requested'];
            $discRequest['amount'] = 0;
        }else{
            $discRequest =  DiscountRequest::find($request['id']);
            $discRequest['amount'] = $request['amount'];
            $discRequest['is_approved'] = $request['is_approved'];
        }
        // dd($rabRequest);
        $discRequest->save();

        DB::commit();
        return response()->json(
        [
            'data'=> $discRequest,
            'responseMessage' => 'success'
        ], 201);
    }

    public function saveSpecUpRequest(Request $request)
    {
        DB::beginTransaction();
        //selalu edit karena saat add new cust sudah insert
        if($request['id']==null){
            $rabRequest =  new RabRequest();
            $rabRequest['project_id'] = $request['project_id'];
            $rabRequest['no'] = $request['no'];
            $rabRequest['additional_work'] = $request['additional_work'];
            $rabRequest['sale_trx_id'] = $request['sale_trx_id'];
            $rabRequest['amount_requested'] = $request['amount_requested'];
            $rabRequest['customer_id'] = $request['customer_id'];
            $rabRequest['amount'] = 0;
        }else{
            $rabRequest =  RabRequest::find($request['id']);
            $rabRequest['amount'] = $request['amount'];
            $rabRequest['is_approved'] = $request['is_approved'];
        }
        
        // dd($rabRequest);
        $rabRequest->save();
        
        if($request['details']!=null)
        {           
            foreach($request['details'] as $reqRabRequestD)
            {
                if($reqRabRequestD['id']==null){
                    $rabRequestD =  new RabRequestD();
                }else{
                    $rabRequestD =  RabRequestD::find($reqRabRequestD['id']);
                }
                $rabRequestD['rab_request_id'] = $rabRequest['id'];
                $rabRequestD['additional_work'] = $reqRabRequestD['additional_work'];
                
                $rabRequestD->save();
            }
        }

        DB::commit();
        return response()->json(
        [
            'data'=> $rabRequest,
            'responseMessage' => 'success'
        ], 201);
    }

    public function getDiscountRequestList()
    {
        $objects = DiscountRequest::All();
        foreach($objects as $object)
        {
            if ($object['project_id']!= null) $object['project_name'] = Project::select('name','site_id') -> where('id',$object['project_id']) ->get(1);
            else $object['project_name'] = null;
            if ($object['project_name'][0]['site_id']!= null) $object['site_name'] = Site::select('name') -> where('id', $object['project_name'][0]['site_id']) ->get(1);
            else $object['site_name'] = null;
        }
        
        return response()->json(['data'=>$objects]);
    }

    public function getSpecUpRequestList()
    {
        $objects = RabRequest::All();
        foreach($objects as $object)
        {
            if ($object['project_id']!= null) $object['project_name'] = Project::select('name','site_id') -> where('id',$object['project_id']) ->get(1);
            else $object['project_name'] = null;
            if ($object['project_name'][0]['site_id']!= null) $object['site_name'] = Site::select('name') -> where('id', $object['project_name'][0]['site_id']) ->get(1);
            else $object['site_name'] = null;
        }
        
        return response()->json(['data'=>$objects]);
    }
}
