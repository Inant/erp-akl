<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $data['data'] = DB::table('programs')->join('users', 'programs.user_id', '=', 'users.id')->select('programs.*', 'users.name as username')->where(['deleted_at'=>NULL, 'status'=>'Active'])->get();
        return view('pages.info.program', $data);
    }
}
