<?php
namespace App\Http\Controllers\CRM;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
use Carbon\Carbon;

class KprSimulation extends Controller
{
    private $dateNow;
    private $now;
    public function __construct()
    {
        $this->dateNow = Carbon::now()->toDateString();
        $this->now = Carbon::now();
    }

    public function getBank()
    {
        $bank = DB::select("SELECT * from list_bank");

        return response()->json(['data'=>$bank]);
    }
    public function getKpr()
    {
        $kpr = DB::select("SELECT * from kpr_simulation k inner join list_bank l on k.bank_id = l.id_bank");

        return response()->json(['data'=>$kpr]);
    }
    public function getKprById($id)
    {
        $kpr = DB::select("SELECT * from kpr_simulation k inner join list_bank l on k.bank_id = l.id_bank where k.id = $id");

        return response()->json(['data'=>$kpr]);
    }
    public function saveKpr(Request $request)
    {
        DB::table('kpr_simulation')->insert([
            'bank_id'       => $request->bank_id,
            'link_url'      => $request->link_url,
            'created_at'    => $this->now
        ]);

        return response()->json(
            [
                'responseMessage' => 'success'
            ], 201);
    }

    public function deleteKpr($id)
    {
        DB::table('kpr_simulation')->where('id', $id)->delete();

        return response()->json(
            [
                'responseMessage' => 'success hapus '.$id
            ], 200);
    }

    public function updateKpr($id, Request $request)
    {
        // update data category
        DB::table('kpr_simulation')->where('id', $id)->update([
            'bank_id'       => $request->bank_id,
            'link_url'   => $request->link_url,
            'updated_at' => $this->now

        ]);

        return response()->json(
            [
                'responseMessage' => 'success update'
            ], 200);
    }


}
