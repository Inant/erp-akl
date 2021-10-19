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

class EmployeeController extends Controller
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
            $query=DB::table('m_employees')
                        ->join('m_positions', 'm_positions.id', '=', 'm_employees.position_id')
                        ->leftjoin('sites', 'sites.id', '=', 'm_employees.site_id')
                        ->select('m_employees.*', 'm_positions.name as position_name', 'sites.name as site_name')
                        ->get();
            return DataTables::of($query)
                                    ->addColumn('action', function ($row) {

                                        return '<a href="/employee/edit/'.$row->id.'" class="btn btn-success btn-sm"><i class="mdi mdi-pencil"></i></a>&nbsp;
                                        <a href="/employee/delete/'.$row->id.'" class="btn btn-danger btn-sm" onclick="javasciprt: return confirm(\'Are You Sure ?\')"><i class="mdi mdi-delete"></i></a>';
                                    })
                                    ->make(true);
        }
        return  view('pages.hrms.employee.list_employee');
    }
    public function create(){
        $data['jabatan']=DB::table('m_positions')->select('id', 'name')->get();
        $data['sites']=DB::table('sites')->select('id', 'name')->get();
        return  view('pages.hrms.employee.employee_add', $data);
    }
    public function store(Request $request){
        $input=array(
            'name' => $request->input('nama'),
            'email' => $request->input('email'),
            'telp' => $request->input('telp'),
            'address' => $request->input('alamat'),
            'site_id' => $request->input('site_id'),
            'position_id' => $request->input('position_id'),
        );
        DB::table('m_employees')->insert($input);
        return redirect('employee');
    }
    public function edit($id){
        $data['data']=DB::table('m_employees')->where('id', $id)->first();
        $data['jabatan']=DB::table('m_positions')->select('id', 'name')->get();
        $data['sites']=DB::table('sites')->select('id', 'name')->get();
        return  view('pages.hrms.employee.employee_edit', $data);
    }
    public function update(Request $request){
        $input=array(
            'name' => $request->input('nama'),
            'email' => $request->input('email'),
            'telp' => $request->input('telp'),
            'address' => $request->input('alamat'),
            'site_id' => $request->input('site_id'),
            'position_id' => $request->input('position_id'),
        );
        DB::table('m_employees')
                    ->where('id', $request->input('id'))
                    ->update($input);
        return redirect('employee');
    }
    public function delete($id){
        $raw=DB::table('m_employees')
                    ->where('id', $id)
                    ->first();
        if ($raw != null) {
            DB::table('m_employees')
                    ->where('id', $id)
                    ->delete();
        }
        return redirect('employee');
    }
}
