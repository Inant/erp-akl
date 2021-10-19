<?php

namespace App\Http\Controllers\Info;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use DB;

class InformationController extends Controller
{
    private $base_api_url;
    private $site_id = null;
    private $user_name = null;
    public function __construct()
    {
        //Authenticate page menu
        $this->middleware(function ($request, $next) {
            Controller::isLogin(auth()->user()['role_id']);       
            return $next($request);
        });

        $this->base_api_url = env('API_URL');
    }
    public function program()
    {
    	$data['data'] = DB::table('programs')->join('users', 'programs.user_id', '=', 'users.id')->select('programs.*', 'users.name as username')->where(['deleted_at'=>NULL, 'status'=>'Active'])->get();
        
        return view('pages.info.program', $data);
    }
    public function program_add()
    {
        $data['id']=auth()->user()['id'];
        return view('pages.info.program_add', $data);
    }
    public function programList()
    {
        $data['data'] = DB::table('programs')->join('users', 'programs.user_id', '=', 'users.id')->select('programs.*', 'users.name as username')->where('deleted_at', NULL)->get();
        
        return view('pages.info.programList', $data);
    }
    public function program_add_post(Request $request)
    {
        $nama=$request->input('nama');
        $user_id=$request->input('user_id');
        $status=$request->input('status');
        // $insert=DB::table('programs')->insert(
        //     [
        //         'name' => $nama,
        //         'user_id' => $user_id,
        //         'status' => $status,
        //         'created_at' => NOW(),
        //     ]
        // );
        // if ($insert) {
        //     return redirect('menu/program/');
        // }else{
        //     echo "gagal";
        // }
        try
        {
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . 'inv/base/Program']);
            $reqBody = [
                'headers' => $headers,
                'json' => [
                            'name' => $nama,
                            'user_id' => $user_id,
                            'status' => $status,
                            // 'created_at' => NOW(),
                ]
            ]; 
            $response = $client->request('POST', '', $reqBody); 
        } catch(RequestException $exception) {
        }
     
        $notification = array(
            'message' => 'Success add program',
            'alert-type' => 'success'
        );

        return redirect('dashboard/programList')->with($notification);
    }
    public function program_edit($id)
    {
        $programs = DB::table('programs')->where('id', $id)->get();
        $data = array(
            'data' => $programs,
        );
        return view('pages.info.program_edit', $data);
    }
    public function program_edit_post(Request $request)
    {
        $nama=$request->input('nama');
        $id=$request->input('id');
        $status=$request->input('status');
        // $update=DB::table('programs')->where('id', $id)->update(
        //     [
        //         'name' => $nama,
        //         'status' => $status,
        //         'updated_at' => NOW(),
        //     ]
        // );
        // if ($update) {
        //     return redirect('menu/program/');
        // }else{
        //     echo "gagal";
        // }
        try
        {
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . 'inv/base/Program/' . $id]);  
            $reqBody = [
                'headers' => $headers,
                'json' => [
                            'name' => $nama,
                            'status' => $status,
                ]
            ]; 
            $response = $client->request('PUT', '', $reqBody);
        } catch(RequestException $exception) {    
        }  
        
        $notification = array(
            'message' => 'Success edit program',
            'alert-type' => 'success'
        );

        return redirect('dashboard/programList')->with($notification);
    }
    public function program_delete($id)
    {
        try
        {
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . 'inv/base/Program/' . $id]);  
            $response = $client->request('DELETE', '', ['headers' => $headers]); 
        } catch(RequestException $exception) {    
        }

        $notification = array(
            'message' => 'Success delete program',
            'alert-type' => 'success'
        );

        return redirect('dashboard/programList')->with($notification);
    }
}
