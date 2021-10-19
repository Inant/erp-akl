<?php

namespace App\Http\Controllers\HRMS;

use Illuminate\Http\Response;

use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\View;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Exception;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Redirect;
use Carbon\Carbon;

use DB;

class PositionController extends Controller
{
    private $base_api_url;
    private $site_id = null;
    private $user_name = null;
    private $user_id = null;
    public function __construct()
    {
        //Authenticate page menu
        $this->middleware(function ($request, $next) {
            Controller::isLogin(auth()->user()['role_id']); 
            $this->user_id = auth()->user()['id'];
            $this->site_id = auth()->user()['site_id'];
            $this->user_name = auth()->user()['name'];
            
            return $next($request);
        });

        $this->base_api_url = env('API_URL');
    }

    // *
    //  * Constructor
    //  *
    //  * @param Util $commonUtil
    //  * @return void
     
    // public function __construct(Util $commonUtil)
    // {
    //     $this->commonUtil = $commonUtil;
    // }

    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        if (request()->ajax()) {
            $query=DB::table('m_positions')->get();
            return DataTables::of($query)
                                    ->addColumn('action', function ($row) {

                                        return '<a href="/position/edit/'.$row->id.'" class="btn btn-success btn-sm"><i class="mdi mdi-pencil"></i></a>&nbsp;
                                        <a href="/position/delete/'.$row->id.'" class="btn btn-danger btn-sm"><i class="mdi mdi-delete"></i></a>';
                                    })
                                    ->make(true);
        }
        return  view('pages.hrms.position.list_position');
    }
    public function create(){
        return  view('pages.hrms.position.position_add');
    }
    public function store(Request $request){
        DB::table('m_positions')->insert(['name' => $request->input('name')]);
        return redirect('position');
    }
    public function edit($id){
        $data['data']=DB::table('m_positions')->where('id', $id)->first();
        return  view('pages.hrms.position.position_edit', $data);
    }
    public function update(Request $request){
        DB::table('m_positions')
                    ->where('id', $request->input('id'))
                    ->update(['name' => $request->input('name')]);
        return redirect('position');
    }
    public function delete($id){
        $raw=DB::table('m_positions')
                    ->where('id', $id)
                    ->first();
        if ($raw != null) {
            DB::table('m_positions')
                    ->where('id', $id)
                    ->delete();
        }
        return redirect('position');
    }
}
