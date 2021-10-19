<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use DB;
use Carbon\Carbon;

class UserController extends Controller
{
    public function getLogin(Request $request) {
        $email = $request->post('email');
        $password = $request->post('password');
        $users = DB::select('select * from users
        JOIN m_employees ON users.m_employee_id = m_employees.id 
        where users.email = ?', [$email]);

        $error = false;
        if(count($users) > 0) {
            $users = $users[0];
            $hashed_password = $users->password;

            unset($users->password);
            if (!Hash::check($password, $hashed_password)) {
                $users = array();
                $error = true;
            }
        } else {
            $error = true;
        }

        return response()->json(['error' => $error, 'data'=>$users]);
    }

}
