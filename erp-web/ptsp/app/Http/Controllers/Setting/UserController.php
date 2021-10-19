<?php

namespace App\Http\Controllers\Setting;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
use Illuminate\Support\Facades\Hash;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Exception;

class UserController extends Controller
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
        $users = DB::table('users')
                ->join('roles', 'users.role_id', '=', 'roles.id')
                ->select('users.*', 'roles.role_name')
                // ->where('users.is_deleted', false)
                ->orderBy('users.name','asc')->get();
        
        $data = array(
            'users' => $users            
        );

        return view('pages.setting.user.user_list', $data);
    }
    public function add()
    {
        $client = new Client(['base_uri' => $this->base_api_url . 'inv/base/Site']);
        $response = $client->request('GET', ''); 
        $body = $response->getBody();
        $content =$body->getContents();
        $response_array = json_decode($content,TRUE);

        $salespersons = $response_array['data'];
        
        $roles = DB::table('roles')
                ->orderBy('role_name','asc')->get();
        
        $sales = DB::select("SELECT m.id, m.name from m_employees m left join users u on m.id = u.m_employee_id where u.m_employee_id IS NULL");

        $data       = array(
            'roles' => $roles,
            'site'  => $salespersons,
            'sales' => $sales
        );
        return view('pages.setting.user.add_user', $data);
    }
    public function adduser(Request $request)
    {
        $password = Hash::make($request->input('password'));
        if(isset($request->sales)){
            $sales = $request->sales;
        } else {
            $sales = NULL;
        }
        $update=DB::table('users')->insert(
            [
                'name'      => $request->input('nama'),
                'email'     => $request->input('email'),
                'password'  => $password,
                'is_active' => $request->input('status'),
                'role_id'   => $request->input('jabatan'),
                'site_id' => $request->input('site'),
                'm_employee_id' => $sales
            ]
        );
        if($update){
            return redirect('user');
        }
    }
    public function edit($id)
    {
        $users = DB::table('users')
                ->where('id', $id)->get();

        $client = new Client(['base_uri' => $this->base_api_url . 'inv/base/Site']);
        $response = $client->request('GET', ''); 
        $body = $response->getBody();
        $content =$body->getContents();
        $response_array = json_decode($content,TRUE);

        $salespersons = $response_array['data'];
        
        $roles = DB::table('roles')
                ->orderBy('role_name','asc')->get();
        
        $sales = DB::select("SELECT m.id, m.name from m_employees m left join users u on m.id = u.m_employee_id where u.m_employee_id IS NULL");

        $data = array(
            'roles' => $roles,
            'site'  => $salespersons,
            'sales' => $sales,
            'value' => $users,
            'id'=>$id
        );
        return view('pages.setting.user.edit_user', $data);
    }
    public function edituser(Request $request){
        if(isset($request->sales)){
            $sales = $request->sales;
        } else {
            $sales = NULL;
        }
        $update=DB::table('users')->where(['id' => $request->input('id')])->update(
            [
                'name'      => $request->input('nama'),
                'email'     => $request->input('email'),
                'role_id'   => $request->input('jabatan'),
                'site_id' => $request->input('site'),
                'm_employee_id' => $sales
            ]
        );
        if($update){
            return redirect('user');
        }
    }
    public function edit_password(Request $request){
        $password = Hash::make($request->input('password'));
        //print_r($password);
        $update=DB::table('users')->where('id', $request->input('id'))->update(
            [
                'password' => $password,
            ]
        );
        if($update){
            return redirect('user');
        }
    }
    public function delete($id)
    {
        DB::table('users')->where('id', $id)->update(
            [
                'is_active' => '0',
                'is_deleted' => true
            ]
        );

        $notification = array(
            'message' => 'Success delete user data',
            'alert-type' => 'success'
        );

        return redirect('user')->with($notification);
    }
}
