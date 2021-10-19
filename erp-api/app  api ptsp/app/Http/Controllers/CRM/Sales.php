<?php
namespace App\Http\Controllers\CRM;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\CustomerFinancial;
use App\Models\FollowupHistory;
use App\Models\SaleTrx;
use App\Models\MEmployee;
use DB;
use Carbon\Carbon;

class Sales extends Controller
{
    private $dateNow;
    private $now;
    public function __construct()
    {
        $this->dateNow = Carbon::now()->toDateString();
        $this->now = Carbon::now();
    }

    public function getSales()
    {
        $sales = DB::select("SELECT e.*, u.name as atasan from m_employees e left join users u on e.id_user = u.id");

        return response()->json(['data'=>$sales]);
    }
    public function getSalesById($id)
    {
        $sales = DB::select("SELECT * from m_employees where id = $id");

        return response()->json(['data'=>$sales]);
    }
    public function saveSales(Request $request)
    {
        DB::table('m_employees')->insert([
            'name'       => $request->name,
            'division'   => $request->division,
            'role'       => $request->role,
            'position'   => $request->position,
            'id_user'   => $request->id_user,
            'created_at' => $this->now
        ]);

        return response()->json(
            [
                'responseMessage' => 'success'
            ], 201);
    }

    public function deleteSales($id)
    {
        DB::table('m_employees')->where('id', $id)->delete();

        return response()->json(
            [
                'responseMessage' => 'success hapus '.$id
            ], 200);
    }

    public function updateSales($id, Request $request)
    {
        // update data category
        DB::table('m_employees')->where('id', $id)->update([
            'name'       => $request->name,
            'division'   => $request->division,
            'role'       => $request->role,
            'position'   => $request->position,
            'updated_at' => $this->now

        ]);

        return response()->json(
            [
                'responseMessage' => 'success update'
            ], 200);
    }


}
