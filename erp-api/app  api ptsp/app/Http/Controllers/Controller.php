<?php

namespace App\Http\Controllers;

use DB;
use Exception;
use Illuminate\Database\QueryException;
use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\Request;

class Controller extends BaseController
{
    public function _GetAllModels()
    {
        $tables = DB::select("SELECT tablename FROM pg_catalog.pg_tables where schemaname = 'public'");
        
        $tables = array_map('current',$tables);
        // $builder = DB::getSchemaBuilder();
        // // $tables = json_decode($tables,true);
        // foreach ($tables as $table)
        // {
        //     $columns = $builder->getColumnListing($table);
        //     $columnsWithType = collect($columns)->mapWithKeys(function ($item, $key) use ($builder,$table) 
        //     {
        //         $key = $builder->getColumnType($table, $item);
        //         return [$item => $key];
        //     });
        // }
        // return $columnsWithType->toArray();
        return $tables;
    }

    public function _GetGSByCode($code)
    {
        $objects = DB::select("SELECT gs_value FROM general_settings where gs_code = '".$code."'");
        
        return response()->json(['data'=>$objects]);;
    }

    public function _GetColumnType($model)
    {
        // $modelClass = "App\\Models\\".$model;
        $builder = DB::getSchemaBuilder();
        $columns = $builder->getColumnListing($model.'s');
        $columnsWithType = collect($columns)->mapWithKeys(function ($item, $key) use ($builder,$model) 
        {
            $key = $builder->getColumnType($model.'s', $item);
            return [$item => $key];
        });
        return $columnsWithType->toArray();
    }

    public function _Get($model)
    {
        $modelClass = "App\\Models\\".$model;
        $object = $modelClass::orderBy('id','asc')->get();
        return response()->json(['data'=> $object]); 
    }
    
    public function _GetById($model, $id)
    {
        $modelClass = "App\\Models\\".$model;
        $object = $modelClass::find($id);
        return response()->json(['data'=> $object]); 
    }

    public function _Create($model, Request $request)
    {
        $modelClass = "App\\Models\\".$model;
        $message = '';
        try {
            $message ='sukses';
            $object = $modelClass::create($request->all());
        } catch (QueryException $e) {
            $message = 'gagal ' . $e->getMessage();
        }
        catch (Exception $e) {
            $message = 'gagal ' . $e->getMessage();
        }
        return response()->json(
            [
                'data'=> $object,
                'responseMessage' => 'success',
                'message' => $message,
            ], 201);
    }

    public function _Update($model, $id, Request $request)
    {
        $modelClass = "App\\Models\\".$model;
        // $id = $request->id;
        $object = $modelClass::findOrFail($id);
        $object->update($request->all());
        return response()->json(array('success' => true, 'created_id' => $object->id), 200);
    }

    public function _Delete($model, $id)
    {
        $modelClass = "App\\Models\\".$model;
        $object = $modelClass::find($id);
        $object->delete();
        return response('Deleted Successfully', 200);
    }
}
