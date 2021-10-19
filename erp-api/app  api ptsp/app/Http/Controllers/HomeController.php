<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Carbon\Carbon;

class HomeController extends Controller
{
    private $dateNow;
    private $now;
    public function __construct()
    {
        $this->dateNow = Carbon::now()->toDateString();
        $this->now = Carbon::now();
    }

    public function getProgramByUserId($userId) {
        $program = DB::table('programs')
            ->join('users', 'programs.user_id', '=', 'users.id')
            ->select('programs.*', 'users.name as username')
            ->where(['deleted_at'=>NULL, 'status'=>'Active', 'programs.user_id'=>$userId])
            ->get();
        return response()->json(['data'=>$program]);
    }

}

