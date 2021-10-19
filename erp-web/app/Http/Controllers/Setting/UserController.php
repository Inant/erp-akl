<?php

namespace App\Http\Controllers\Setting;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
use Illuminate\Support\Facades\Hash;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Exception;
use File;

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
            ->orderBy('users.name', 'asc')->get();

        $data = array(
            'users' => $users
        );

        return view('pages.setting.user.user_list', $data);
    }
    public function add()
    {
        $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . 'inv/base/Site']);
        $response = $client->request('GET', '', ['headers' => $headers]); 
        $body = $response->getBody();
        $content = $body->getContents();
        $response_array = json_decode($content, TRUE);

        $salespersons = $response_array['data'];

        $roles = DB::table('roles')
            ->orderBy('role_name', 'asc')->get();

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
        $token = str_random(60);
        if (isset($request->sales)) {
            $sales = $request->sales;
        } else {
            $sales = NULL;
        }
        $update = DB::table('users')->insert(
            [
                'name'      => $request->input('nama'),
                'email'     => $request->input('email'),
                'password'  => $password,
                'remember_token'  => $token,
                'is_active' => $request->input('status'),
                'role_id'   => $request->input('jabatan'),
                'site_id' => $request->input('site'),
                'm_warehouse_id' => $request->input('m_warehouse_id'),
                'm_employee_id' => $sales
            ]
        );
        if ($update) {
            return redirect('user');
        }
    }
    public function edit($id)
    {
        $users = DB::table('users')
            ->where('id', $id)->get();

        $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . 'inv/base/Site']);
        $response = $client->request('GET', '', ['headers' => $headers]); 
        $body = $response->getBody();
        $content = $body->getContents();
        $response_array = json_decode($content, TRUE);

        $salespersons = $response_array['data'];

        $roles = DB::table('roles')
            ->orderBy('role_name', 'asc')->get();

        $sales = DB::select("SELECT m.id, m.name from m_employees m left join users u on m.id = u.m_employee_id where u.m_employee_id IS NULL");
        // return $users;
        // exit;
        $warehouse = DB::table('m_warehouses')->where('site_id', $users[0]->site_id)->get();
        $data = array(
            'roles' => $roles,
            'site'  => $salespersons,
            'sales' => $sales,
            'value' => $users,
            'id' => $id,
            'warehouse' => $warehouse
        );
        return view('pages.setting.user.edit_user', $data);
    }
    public function edituser(Request $request)
    {
        if (isset($request->sales)) {
            $sales = $request->sales;
        } else {
            $sales = NULL;
        }
        $user = DB::table('users')->where(['id' => $request->input('id')])->first();
        // menangkap file 
        $nama_file = null;
        $file = $request->file('signature');
        // if ($file) {
        //     $nama_file = $request->input('id').'_'.time().$file->getClientOriginalName();
        //     $file->move('signature_user',$nama_file);
        //     File::delete('signature_user/'.$user->signature);
        // }
        if ($file) {
            try {
                $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . 'upload']);
                $reqBody = [
                    'headers' => $headers,
                    'multipart' => [
                        [
                            'name'     => 'user_id',
                            'contents' => $request->input('id')
                        ],
                        [
                            'Content-type' => 'multipart/form-data',
                            'name'     => 'file',
                            'contents' => fopen($file, 'r'),
                            'filename' => 'signature_user_' . $request->input('id') . '.png'
                        ]
                    ]
                ];
                $response = $client->request('POST', '', $reqBody);
                $body = $response->getBody();
                $content = $body->getContents();
                $response_array = json_decode($content, TRUE);
                $upload_data = $response_array;
                // echo json_encode($purchase);
            } catch (RequestException $exception) {
            }
            $nama_file = $upload_data['data']['path'];
        }

        $update = DB::table('users')->where(['id' => $request->input('id')])->update(
            [
                'name'      => $request->input('nama'),
                'email'     => $request->input('email'),
                'role_id'   => $request->input('jabatan'),
                'site_id' => $request->input('site'),
                'm_warehouse_id' => $request->input('m_warehouse_id'),
                'm_employee_id' => $sales,
                'code_signature'    => $request->code_signature,
                'signature'     => $nama_file != null ? $nama_file : $user->signature
            ]
        );
        if ($update) {
            return redirect('user');
        }
    }
    public function edit_password(Request $request)
    {
        $password = Hash::make($request->input('password'));
        //print_r($password);
        $update = DB::table('users')->where('id', $request->input('id'))->update(
            [
                'password' => $password,
            ]
        );
        if ($update) {
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
    public function cekCode(Request $request)
    {
        $code = $request->code;
        $id = $request->id;
        if ($id != null) {
            $cek = DB::table('users')->where('code_signature', $code)->whereNot('id', $id)->count();
        } else {
            $cek = DB::table('users')->where('code_signature', $code)->count();
        }
        $is_there = 0;
        if ($cek > 1) {
            $is_there = 1;
        }
        return $is_there;
    }
    public function profile($id)
    {
        $users = DB::table('users')
            ->where('id', $id)->get();

        $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . 'inv/base/Site']);
        $response = $client->request('GET', '', ['headers' => $headers]); 
        $body = $response->getBody();
        $content = $body->getContents();
        $response_array = json_decode($content, TRUE);

        $salespersons = $response_array['data'];

        $roles = DB::table('roles')
            ->orderBy('role_name', 'asc')->get();

        $sales = DB::select("SELECT m.id, m.name from m_employees m left join users u on m.id = u.m_employee_id where u.m_employee_id IS NULL");
        // return $users;
        // exit;
        $warehouse = DB::table('m_warehouses')->where('site_id', $users[0]->site_id)->get();
        $data = array(
            'roles' => $roles,
            'site'  => $salespersons,
            'sales' => $sales,
            'value' => $users,
            'id' => $id,
            'warehouse' => $warehouse
        );
        return view('pages.setting.user.profile', $data);
    }
    public function editprofile(Request $request)
    {
        $user = DB::table('users')->where(['id' => $request->input('id')])->first();
        // menangkap file 
        $nama_file = null;
        $file = $request->file('signature');
        if ($file) {
            try {
                $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . 'upload']);
                $reqBody = [
                    'headers' => $headers,
                    'multipart' => [
                        [
                            'name'     => 'user_id',
                            'contents' => $request->input('id')
                        ],
                        [
                            'Content-type' => 'multipart/form-data',
                            'name'     => 'file',
                            'contents' => fopen($file, 'r'),
                            'filename' => 'signature_user_' . $request->input('id') . '.png'
                        ]
                    ]
                ];
                $response = $client->request('POST', '', $reqBody);
                $body = $response->getBody();
                $content = $body->getContents();
                $response_array = json_decode($content, TRUE);
                $upload_data = $response_array;
                // echo json_encode($purchase);
            } catch (RequestException $exception) {
            }
            $nama_file = $upload_data['data']['path'];
        }

        $update = DB::table('users')->where(['id' => $request->input('id')])->update(
            [
                'name'      => $request->input('nama'),
                'email'     => $request->input('email'),
                'code_signature'    => $request->code_signature,
                'signature'     => $nama_file != null ? $nama_file : $user->signature
            ]
        );
        if ($update) {
            return redirect('user/profile/' . $request->input('id'));
        }
    }
    public function edit_profil_password(Request $request)
    {
        $password = Hash::make($request->input('password'));
        $update = DB::table('users')->where('id', $request->input('id'))->update(
            [
                'password' => $password,
            ]
        );
        if ($update) {
            return redirect('user/profile/' . $request->input('id'));
        }
    }
}
