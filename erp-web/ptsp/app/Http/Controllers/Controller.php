<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Request;
use DB;
// use Illuminate\Support\Facades\Auth;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected function isLogin($roleId)
    {
        $module = Request::segment(1);
        $menus = DB::table('menus')
                ->where('url', $module)->get();
        $menuId = $menus[0]->id;
        $userPermission = DB::table('user_permission')
                        ->where('menu_id', $menuId)
                        ->where('role_id', $roleId);

        // if($userPermission->count() < 1)
        //     redirect('unauthorized')->send();
    }
}
