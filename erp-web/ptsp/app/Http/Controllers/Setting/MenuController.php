<?php

namespace App\Http\Controllers\Setting;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Carbon\Carbon;
use DB;

class MenuController extends Controller
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
        $menus = DB::table('menus')->where('is_deleted', false)->orderBy('title','asc')->get();
        foreach($menus as $menu){
            $main_menu = null;
            if($menu->is_main_menu != 0)
                $main_menu = DB::table('menus')->where('id', $menu->is_main_menu)->first()->title;
            else
                $main_menu = '-';

            $menu->is_main_menu = $main_menu;
        }

        $data = array(
            'menus' => $menus
        );

        return view('pages.setting.menu.menu_list', $data);
    }

    public function add()
    {
        $menus = DB::table('menus')->where('is_active', '1')->orderBy('title','asc')->get();
        $data = array(
            'menus' => $menus,
            'menu_by_id' => null
        );
        return view('pages.setting.menu.menu_form', $data);
    }

    public function addPost(Request $request)
    {
        DB::table('menus')->insert(
            [
                'title' => $request->input('title'),
                'url' => $request->input('url'),
                'icon' => $request->input('icon'),
                'seq_no' => $request->input('seq_no'),
                'is_main_menu' => $request->input('is_main_menu'),
                'is_active' => $request->input('is_active')
            ]
        );

        $notification = array(
              'message' => 'Success insert menu data',
              'alert-type' => 'success'
        );

        return redirect('menu')->with($notification);
    }

    public function edit($id)
    {
        $menus = DB::table('menus')->where('is_active', '1')->orderBy('title','asc')->get();
        $menu_by_id = DB::table('menus')->where('id', $id)->first();
        $data = array(
            'menus' => $menus,
            'menu_by_id' => $menu_by_id
        );
        return view('pages.setting.menu.menu_form', $data);
    }

    public function editPost(Request $request, $id)
    {
        DB::table('menus')->where('id', $id)->update(
            [
                'title' => $request->input('title'),
                'url' => $request->input('url'),
                'icon' => $request->input('icon'),
                'seq_no' => $request->input('seq_no'),
                'is_main_menu' => $request->input('is_main_menu'),
                'is_active' => $request->input('is_active'),
                'updated_at' => 'now()'
            ]
        );

        $notification = array(
              'message' => 'Success update menu data',
              'alert-type' => 'success'
        );

        return redirect('menu')->with($notification);
    }

    public function delete($id)
    {
        DB::table('menus')->where('id', $id)->update(
            [
                'is_active' => '0',
                'is_deleted' => true
            ]
        );

        $notification = array(
            'message' => 'Success delete menu data',
            'alert-type' => 'success'
        );

        return redirect('menu')->with($notification);
    }
    public function payment_id()
    {
        $menus = DB::table('m_kpr_bank_payments')->get();
        $data=array(
                    "data"=>$menus
                    );
        return view('pages.setting.menu.payment', $data);
    }
    public function payment_add()
    {
        $sql=DB::table('list_bank')
                    ->get();
        $data=array("bank" => $sql);
        return view('pages.setting.menu.payment_add', $data);
    }
    public function payment_add_post(Request $request)
    {
        $kode=$request->input('bank_name');
        $nama_bank=DB::table('list_bank')->where('bank_code', $kode)->first();
        $bank=$nama_bank->bank_name;
        $total=$request->input('sum');
        $update=DB::table('list_bank')->where('bank_code', $kode)->update(['status' => 'sudah terpakai']);
        for($i=0; $i < $total; $i++){
            $proses="proses".($i+1);
            $persen=$request->input($i+1);
            if($request->input($i+1) != NULL){
                $insert=DB::table('m_kpr_bank_payments')->insert(
                    [
                        'bank_name' => $bank,
                        'progress_category' => $request->input($proses),
                        'payment_percent' => $persen,
                        'bank_code' => $kode,
                    ]
                );
            }
        }
        return redirect('menu/payment/');
    }
    public function payment_edit($id)
    {
        $menus = DB::table('m_kpr_bank_payments')->where('bank_code', $id)->get();
        // print_r($menus);
        // exit();
        $total= count($menus);
        // $persen="";
        // for($i=0; $i < $total; $i++){
        //     $temp=round($menus[$i]->payment_percent, 2);
        //     $persen .=$temp;
        //     if($i != $total-1){
        //         $persen .=",";
        //     }
        // }


        $sql=DB::table('list_bank')->get();
        $data       = array(
            'id'    => $id,
            'data'  => $menus,
            'total' => $total,
            "bank"  => $sql,
        );
        return view('pages.setting.menu.payment_edit', $data);
    }
    public function payment_edit_post(Request $request)
    {
        $kode=$request->input('kode');
        $menus = DB::table('m_kpr_bank_payments')->where('bank_code', $kode)->get();
        $bank=$menus[0]->bank_name;
        // exit();
        for($i=0; $i<7; $i++){
            if(!empty($menus[$i]->id)){
                $id=$menus[$i]->id;
                echo $id;
                echo "<br>";
                echo $menus[$i]->bank_name;
                echo "<br>";
                echo $request->input($i);
                $proses='proses'.$i;
                echo "<br>";
                echo $request->input($proses);
                echo "<br>";
                if($request->input($i) == NULL){
                    $delete=DB::table('m_kpr_bank_payments')->where('id', $menus[$i]->id)->delete();
                }
                $update=DB::table('m_kpr_bank_payments')->where('id', $id)->update(
                    [
                        'bank_name' => $bank,
                        'progress_category' => $request->input($proses),
                        'payment_percent' => $request->input($i),
                        'bank_code' => $kode,
                    ]
                );
            }else{
                if($request->input($i) != NULL){
                    echo $bank;
                    echo "<br>";
                    echo $request->input($i);
                    $proses='proses'.$i;
                    echo "<br>";
                    echo $request->input($proses);
                    echo "<br>";
                    $proses='proses'.$i;
                    $insert=DB::table('m_kpr_bank_payments')->insert(
                        [
                            'bank_name' => $bank,
                            'progress_category' => $request->input($proses),
                            'payment_percent' => $request->input($i),
                            'bank_code' => $kode,
                        ]
                    );
                }
            }

        }
        return redirect('menu/payment/');
    }
    public function payment_delete($id)
    {
        $update=DB::table('list_bank')->where('bank_code', $id)->update(['status' => 'belum terpakai']);
        $delete=DB::table('m_kpr_bank_payments')->where('bank_code', $id)->delete();
        if($delete){
          return redirect('menu/payment/');
        }else{
            echo "gagal";
        }
    }public function give_feed(){
        return view('pages.setting.menu.feed');
    }

    public function get_feed(Request $request)
    {
        // echo $request->input('set');
        // exit;
        $rab=DB::table('rabs')->where('no', $request->input('set'))->get();
        $project_id=$rab[0]->project_id;
        $id=$rab[0]->id;

        echo $request->input('get');
        $rabget=DB::table('rabs')->where('no', $request->input('get'))->get();
        $project_id_get=$rabget[0]->project_id;
        $id_get=$rabget[0]->id;

        // echo $project_id_get.$id_get;
        // exit();

        $project=DB::table('project_works')->where(['project_id'=>$project_id_get, 'rab_id'=>$id_get])->get();
        foreach ($project as $key=>$val){
            print_r($val);
            echo "aksdjhkasjdhf<br>";
            $insertpw=DB::table('project_works')->insert(
                    [
                        'rab_id'                     => $id,
                        'project_id'                 => $project_id,
                        'name'                       => $val->name,
                        'base_price'                 => $val->base_price,
                        'created_at'                 => $val->created_at,
                        'updated_at'                 => $val->updated_at,
                        'deleted_at'                 => $val->deleted_at,
                    ]
                );
            $projects_worksubs=DB::table('project_worksubs')->where('project_work_id', $val->id)->get();
            foreach($projects_worksubs as $k=>$v){
                print_r($v);
                echo "<br>";
                $pw_id=DB::table('project_works')->max('id');
                $insertpws=DB::table('project_worksubs')->insert(
                            [
                                'project_work_id'    => $pw_id,
                                'name'               => $v->name,
                                'base_price'         => $v->base_price,
                                'amount'             => $v->amount,
                                'm_unit_id'          => $v->m_unit_id,
                                'work_start'         => $v->work_start,
                                'work_end'           => $v->work_end,
                                'created_at'         => $v->created_at,
                                'updated_at'         => $v->updated_at,
                                'deleted_at'         => $v->deleted_at,
                            ]
                        );
                $projects_worksub_ds=DB::table('project_worksub_ds')->where('project_worksub_id', $v->id)->get();
                foreach($projects_worksub_ds as $p){
                    print_r($p);
                    echo "<br>";
                    $pwd_id=DB::table('project_worksubs')->max('id');
                    $insertpws=DB::table('project_worksub_ds')->insert(
                            [
                                'project_worksub_id' => $pwd_id,
                                'm_item_id'          => $p->m_item_id,
                                'amount'             => $p->amount,
                                'm_unit_id'          => $p->m_unit_id,
                                'base_price'         => $p->base_price,
                                'buy_date'           => $p->buy_date,
                                'created_at'         => $p->created_at,
                                'updated_at'         => $p->updated_at,
                                'deleted_at'         => $p->deleted_at,
                            ]
                        );
                }
            }
        }
        // echo count($project);
        return redirect('menu/');
    }
    public function price()
    {
        return view('pages.setting.menu.price');
    }
    //sales
    public function sales()
    {
        return view('pages.crm.sales.index');
    }
    public function salesAdd()
    {
        return view('pages.crm.sales.add');
    }
    public function getSalesJson()
    {
        $response = null;
        try
        {
            $client = new Client(['base_uri' => $this->base_api_url . '/crm/sales']);
            $response = $client->request('GET', '');
            $body = $response->getBody();
            $content = $body->getContents();
            $response_array = json_decode($content,TRUE);

            $response = $content;
        } catch(RequestException $exception) {

        }

        return $response;
    }
    public function salesSave(Request $request)
    {
        $name = $request->name;
        $division = 'Marketing';
        $role = $request->role;
        $position = $request->position;
        $id = auth()->user()['id'];
        try
        {
            $client            = new Client(['base_uri' => $this->base_api_url . 'crm/sales']);
            $reqBody           = [
                'json'         => [
                    'name'     => $name,
                    'division' => $division,
                    'role'     => $role,
                    'position' => $position,
                    'id_user'  =>$id
                ]
            ];
            $response          = $client->request('POST', '', $reqBody);
            // print_r($response);
            // exit();
            return redirect('menu/sales');
        } catch(RequestException $exception) {
            print_r($exception);
        }
    }
    public function getSalesByIdJson($id)
    {
        $response = null;
        try
        {
            $client = new Client(['base_uri' => $this->base_api_url . '/crm/sales/'.$id]);
            $response = $client->request('GET', '');
            $body = $response->getBody();
            $content = $body->getContents();
            $response_array = json_decode($content,TRUE);

            $response = $content;
        } catch(RequestException $exception) {

        }

        return $response;
    }
    public function salesEdit($id)
    {
        $sales = DB::select("SELECT * from m_employees where id = $id");
        return view('pages.crm.sales.edit' , ['data' => $sales]);
    }
    public function salesDelete($id)
    {
        $id = $id;
        try
        {
            $client = new Client(['base_uri' => $this->base_api_url . 'crm/sales/'.$id]);
            $reqBody = [
                'json' => [
                    'id' => $id,
                ]
            ];
            $response = $client->request('DELETE', '', $reqBody);
            return redirect('menu/sales');
        } catch(RequestException $exception) {

        }

    }
    public function salesUpdate(Request $request)
    {
        $name = $request->name;
        $division = 'Marketing';
        $role = $request->role;
        $position = $request->position;
        $id = $request->id;

        try
        {
            $client            = new Client(['base_uri' => $this->base_api_url . 'crm/sales/'.$id]);
            $reqBody           = [
                'json'         => [
                    'id'       => $id,
                    'name'     => $name,
                    'division' => $division,
                    'role'     => $role,
                    'position' => $position
                ]
            ];
            $response          = $client->request('PUT', '', $reqBody);
            return redirect('menu/sales');
        } catch(RequestException $exception) {
            return "Gak bisa nangkep";
        }
    }

    //simulasi kpr
    public function kpr()
    {
        $id = auth()->user()['role_id'];
        return view('pages.crm.kpr.index',['id' => $id]);
    }
    public function kprAdd()
    {
        $bank =  DB::select("SELECT * from list_bank");
        $id = auth()->user()['role_id'];
        return view('pages.crm.kpr.add',['id' => $id, 'bank' => $bank]);
    }
    public function kprEdit($id)
    {
        $kpr = DB::select("SELECT * from kpr_simulation k inner join list_bank l on k.bank_id = l.id_bank where k.id = $id");
        $id = auth()->user()['role_id'];
        return view('pages.crm.kpr.edit',['id' => $id, 'data' => $kpr]);
    }
    public function getKprJson()
    {
        $response = null;
        try
        {
            $client = new Client(['base_uri' => $this->base_api_url . '/crm/kpr']);
            $response = $client->request('GET', '');
            $body = $response->getBody();
            $content = $body->getContents();
            $response_array = json_decode($content,TRUE);

            $response = $content;
        } catch(RequestException $exception) {

        }

        return $response;
    }
    public function kprSave(Request $request)
    {
        $bank_id = $request->bank_id;
        $link_url = $request->link_url;
        try
        {
            $client = new Client(['base_uri' => $this->base_api_url . 'crm/kpr']);
            $reqBody = [
                'json'         => [
                    'bank_id'     => $bank_id,
                    'link_url'    => $link_url,
                ]
            ];
            $response          = $client->request('POST', '', $reqBody);
            return redirect('menu/simulasi_kpr');
        } catch(RequestException $exception) {

        }
    }
    public function kprUpdate(Request $request)
    {
        $link_url = $request->link_url;
        $bank_id = $request->bank_id;
        $id = $request->id;

        try
        {
            $client = new Client(['base_uri' => $this->base_api_url . 'crm/kpr/'.$id]);
            $reqBody = [
                'json' => [
                    'id' => $id,
                    'bank_id' => $bank_id,
                    'link_url' => $link_url
                ]
            ];
            $response = $client->request('PUT', '', $reqBody);
            return redirect('menu/simulasi_kpr');
        } catch(RequestException $exception) {
            return "Gak bisa nangkep";
        }
    }
    public function kprDelete($id)
    {
        $id = $id;
        try
        {
            $client = new Client(['base_uri' => $this->base_api_url . 'crm/kpr/'.$id]);
            $reqBody = [
                'json' => [
                    'id' => $id,
                ]
            ];
            $response = $client->request('DELETE', '', $reqBody);
            return redirect('menu/simulasi_kpr');
        } catch(RequestException $exception) {

        }

    }

    //Gallery
    public function gambar()
    {
        $id = auth()->user()['role_id'];
        return view('pages.setting.gallery.index',['id' => $id]);
    }
    public function gambarAdd()
    {
        $id = auth()->user()['role_id'];
        return view('pages.setting.gallery.add',['id' => $id]);
    }
    public function gambarSave(Request $request)
    {
        $id = auth()->user()['id'];


        if ($request->hasFile('photo')){
            $photo = $request->file('photo');
            $request->photo->move(public_path('/upload/photo/'), $photo->getClientOriginalName());
            $name = $photo->getClientOriginalName();

            $client  = new Client(['base_uri' => $this->base_api_url . 'gallery']);
            $reqBody = [
                'json'             => [
                    'filename'     => $name,
                    'creator'      => $id,
                ]
            ];
            $response              = $client->request('POST', '', $reqBody);
        }

        return redirect('menu/gambar');
    }
    public function getGambarJson()
    {
        $response = null;
        try
        {
            $client = new Client(['base_uri' => $this->base_api_url . '/gallery']);
            $response = $client->request('GET', '');
            $body = $response->getBody();
            $content = $body->getContents();
            $response_array = json_decode($content,TRUE);

            $response = $content;
        } catch(RequestException $exception) {

        }

        return $response;
    }
    public function gambarDelete($id)
    {
        try
        {
            $client = new Client(['base_uri' => $this->base_api_url . 'gallery/'.$id]);
            $reqBody = [
                'json' => [
                    'id' => $id,
                ]
            ];
            $response = $client->request('DELETE', '', $reqBody);
            return redirect('menu/gambar');
        } catch(RequestException $exception) {

        }

    }
    public function gallery()
    {
        $data = DB::select("SELECT * from gallery_photos");

        return view('pages.setting.gallery.gallery',['data' => $data]);
    }
}
