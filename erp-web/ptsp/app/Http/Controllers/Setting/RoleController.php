<?php

namespace App\Http\Controllers\Setting;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;

class RoleController extends Controller
{
    private $base_api_url;
    // protected $user;
    public function __construct()
    {
        //Authenticate page menu
        $this->middleware(function ($request, $next) {
            Controller::isLogin(auth()->user()['role_id']); 
            return $next($request);
        });

        // Controller::isLogin();
        $this->base_api_url = env('API_URL');

    }
    
    public function index()
    {
        $roles = DB::table('roles')
                ->orderBy('role_name','asc')->get();
        
        $data = array(
            'roles' => $roles            
        );

        return view('pages.setting.role.role_list', $data);
    }

    public function permission($id)
    {
        $role_by_id = DB::table('roles')
                    ->where('id', $id)
                    ->first();

        $modules = DB::table('menus AS m')
                    ->where('m.is_deleted', false)
                    ->orderBy('m.title','asc')
                    ->get();
                    
        $data = array(
            'role_by_id' => $role_by_id,
            'modules' => $modules
        );
        return view('pages.setting.role.role_user_permission', $data);
    }

    public function giveAccessAjax()
    {
        $menu_id = $_GET['menu_id'];
        $role_id = $_GET['role_id'];

        $access = DB::table('user_permission')
                    ->where('menu_id', $menu_id)
                    ->where('role_id', $role_id)
                    ->get();

        if(count($access) < 1){
            DB::table('user_permission')->insert(
                [
                    'menu_id' => $menu_id,
                    'role_id' => $role_id
                ]
            );
        } else {
            DB::table('user_permission')
                ->where('menu_id', $menu_id)
                ->where('role_id', $role_id)
                ->delete();
        }
    }
}
