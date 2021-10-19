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
use App\Imports\ExcelDataImport;
use Maatwebsite\Excel\Facades\Excel; 
use PhpOffice\PhpSpreadsheet\Shared\Date;


class HrmsController extends Controller
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
        $query=DB::table('m_employees')
                    ->join('m_positions', 'm_positions.id', '=', 'm_employees.position_id')
                    ->leftjoin('sites', 'sites.id', '=', 'm_employees.site_id')
                    ->select('m_employees.*', 'm_positions.name as position_name', 'sites.name as site_name')
                    ->get();
        
        return view('pages.hrms.absensi.absensi_list');
    }
    public function json() {
        $date=date('Y-m-d');
        if (isset($_POST['date'])) {
            $date=$_POST['date'];
        }
        $data1=array();
        foreach (DB::table('m_employees')->get() as $pegawai){
            $absensi_pegawai=DB::table('tbl_absensi')->where('tanggal', $date)->where('m_employee_id', $pegawai->id)->first();
            $row=array();
            $row['id']= $pegawai->id;
            $row['name']= $pegawai->name;
            $row['tanggal']= $date;
            if ($absensi_pegawai != null) {
                $row['jam_datang']= ($absensi_pegawai->jam_datang != null ? $absensi_pegawai->jam_datang : '');
                $row['jam_pulang']= ($absensi_pegawai->jam_pulang != null ? $absensi_pegawai->jam_pulang : '');   
            }else{
                $row['jam_datang']= '';
                $row['jam_pulang']= '';   
            }
            $row['action']       = '<a href="'.url('absensi/edit/'.$pegawai->id).'/'.$date.'" class="btn btn-success btn-sm"><i class="mdi mdi-pencil"></i></a>';
            $data1[]=$row;
        }
        $output = array(
                        "draw" => 0,
                        "recordsTotal" => count($data1),
                        "recordsFiltered" => count($data1),
                        "data" => $data1,
                );
        return $output;
    }
    public function edit($id, $date) 
    {
        $cek=$this->getAbsensiByIdPegawai($id, $date);
        if ($cek == null) {
            $data_insert=array('m_employee_id'=>$id, 'tanggal'=>$date);
            if ($date != '00:00:00') {
                # code...
                DB::table('tbl_absensi')->insert($data_insert);
            }
        }
        $row = $this->getAbsensiByIdPegawai($id, $date);
        $data = array(
            // 'id_absensi' => set_value('id_absensi', $row->id_absensi),
            // 'id_pegawai' => set_value('id_pegawai', $row->id_pegawai),
            // 'name' => set_value('name', $row->name),
            // 'tanggal' => set_value('tanggal', $row->tanggal),
            // 'jam_datang' => set_value('jam_datang', ($row->jam_datang == null ? '00:00:00' : $row->jam_datang)),
            // 'jam_pulang' => set_value('jam_pulang', ($row->jam_datang == null ? '00:00:00' : $row->jam_pulang)),
            // 'ket' => set_value('ket', $row->ket),
            'detail'    => $row,
        );
        return view('pages.hrms.absensi.absensi_update', $data);
    }
    public function update(Request $request) 
    {
        $data = array(
            'm_employee_id' => $request->input('id_pegawai'),
            'tanggal' => $request->input('tanggal'),
            'jam_datang' => $request->input('jam_datang'),
            'jam_pulang' => $request->input('jam_pulang'),
            'keterangan' => $request->input('ket'),
            'dtm_upd' => date("Y-m-d H:i:s",  time())
        );
        $where=array('id_absensi'=>$request->input('id_absensi'));
        $update=DB::table('tbl_absensi')
                ->where('id_absensi', $request->input('id_absensi'))
                ->update($data);
        return redirect('absensi');
    }
    private function getAbsensiByIdPegawai($id, $date){
        $query = DB::table('tbl_absensi')
                ->select('tbl_absensi.*', 'm_employees.name')
                ->join('m_employees', 'm_employees.id','=','tbl_absensi.m_employee_id')
                ->where('m_employees.id', $id)
                ->where('tbl_absensi.tanggal', $date)
                ->first();
        return $query;
    }
    public function month(Request $request)
    {
        $date=date('Y-m');
        $id_pegawai=null;
        $tahun = date('Y'); //Mengambil tahun saat ini
        $bulan = date('m'); //Mengambil bulan saat ini
        if (isset($_POST['id_pegawai'])) {
            $id_pegawai=$request->input('id_pegawai');
            $tahun=$request->input('tahun');
            $bulan=$request->input('bulan');
            $jumlah_hari = cal_days_in_month(CAL_GREGORIAN, $bulan, $tahun);
        }else{
            $jumlah_hari = cal_days_in_month(CAL_GREGORIAN, $bulan, $tahun);
        }
        $data['bulan'] = $bulan;
        $data['tahun'] = $tahun;

        $data['pegawai_option'] = array();
        $data['pegawai_option'][''] = 'Pilih Pegawai';
        $pegawai=DB::table('m_employees')->select('id', 'name')->where('id', $id_pegawai)->first();
        $data1=array();
        $absensi=array();
        $month='';
        if ($id_pegawai != 0) {
            $data1=array();
            for ($i=1; $i <= $jumlah_hari; $i++) { 
                $month=$tahun.'-'.$bulan.'-'.$i;
                // echo $month;
                $absensi_pegawai=$this->getAbsensiByDay($id_pegawai, $month);
                // print_r($absensi_pegawai);
                $row=array();
                $day=(strlen($i) == 1 ? '0'.$i : $i);
                $newMonth=(strlen($bulan) == 1 ? '0'.$bulan : $bulan);
                $row['tanggal']= $day.'-'.$newMonth.'-'.$tahun;
                $row['date']= $month;
                if ($absensi_pegawai != null) {
                    $row['id_pegawai']= $absensi_pegawai->m_employee_id;
                    $row['name']= $absensi_pegawai->name;
                    $row['jam_datang']= ($absensi_pegawai->jam_datang != null ? $absensi_pegawai->jam_datang : '');
                    $row['jam_pulang']= ($absensi_pegawai->jam_pulang != null ? $absensi_pegawai->jam_pulang : '');   
                }else{
                    $row['jam_datang']= '';
                    $row['jam_pulang']= '';   
                }
                $row['action']  = '<a href="/absensi/edit/'.$pegawai->id.'/'.$month.'" class="btn btn-success btn-sm"><i class="mdi mdi-pencil"></i></a>';
                $data1[]=$row;
            }
            $absensi=array('name'=>($pegawai != null ? $pegawai->name : ''), 'data'=>$data1);
        }
        $data['absensi']=$absensi;
        $data['jumlah_hari']=$jumlah_hari;
        $data['id_pegawai']=$id_pegawai;
        $data['tanggal']=$date;
        $data['pegawai_option']=DB::table('m_employees')->select('id', 'name')->get();
        
        return view('pages.hrms.absensi.absensi_list_month', $data);
    } 
    private function getAbsensiByDay($id, $date){
        $query=DB::table('tbl_absensi')
                ->select('tbl_absensi.*', 'm_employees.name')
                ->join('m_employees', 'm_employees.id','=','tbl_absensi.m_employee_id')
                ->where('m_employees.id', $id)
                ->where('tbl_absensi.jam_datang', '!=', '00:00:00')
                ->where('tbl_absensi.jam_datang','!=', NULL)
                ->where('tbl_absensi.tanggal', $date)
                ->first();
        return $query;
    }
    public function import(){
        return view('pages.hrms.absensi.import_absensi');
    }
    public function importPost(Request $request) 
    {
        // validasi
        $this->validate($request, [
            'file' => 'required|mimes:csv,xls,xlsx'
        ]);
 
        // menangkap file excel
        $file = $request->file('file');
        // membuat nama file unik
        $nama_file = rand().$file->getClientOriginalName();
        // $file = $request->file('products_csv');
        // upload ke folder file_siswa di dalam folder public
        $file->move('import_excel',$nama_file);
 
        // import data
        // Excel::import(new SiswaImport, public_path('/file_siswa/'.$nama_file));
        $array = Excel::toArray(new ExcelDataImport, public_path('/import_excel/'.$nama_file));
        // $results =Excel::import(new ExcelDataImport, public_path('/import_excel/'.$nama_file));
        foreach ($array[0] as $key => $value) {
            // if ($key > 1) {
            //     if ($value[0] != null) {
            //         $pegawai=DB::table('m_employees')->where('name', $value[0])->first();
            //         if ($pegawai != null) {
            //             if ($value[1] != null) {
            //                 $tanggal=\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value[1]);
            //                 $jam_datang=\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value[2]);
            //                 $jam_pulang=\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value[3]);
            //                 $data=array(
            //                     'm_employee_id'     => $pegawai->id,
            //                     'tanggal'           => date_format($tanggal, 'Y-m-d'),
            //                     'jam_datang'        => date_format($jam_datang, 'H:i:s'),
            //                     'jam_pulang'        => date_format($jam_pulang, 'H:i:s'),
            //                 );
            //                 // print_r($data);
            //                 $absensi=DB::table('tbl_absensi')->where('m_employee_id', $pegawai->id)->where('tanggal', date_format($tanggal, 'Y-m-d'))->first();
            //                 if ($absensi == null) {
            //                     DB::table('tbl_absensi')->insert($data);
            //                 }else{
            //                     DB::table('tbl_absensi')->where('id_absensi', $absensi->id_absensi)->update($data);
            //                 }
            //             }
            //         }
            //     }
            // }
            try {
                $tanggal=\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value[1]);
                $jam_datang=($value[2] != '' ? \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value[2]) : null);
                $jam_pulang=($value[3] == null ? '' : \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value[3]));
                $data=array(
                    'tanggal'           => date_format($tanggal, 'Y-m-d'),
                    'jam_datang'        => $jam_datang != null ? date_format($jam_datang, 'H:i:s') : '-',
                    'jam_pulang'        => $jam_pulang != '' ? date_format($jam_pulang, 'H:i:s') : '-',
                );
                print_r($data);
                echo '<br>';
            } catch (\Throwable $th) {
                //throw $th;
            }
        }
        unlink(public_path('/import_excel/'.$nama_file));
        // return redirect('/absensi');
    }
    public function cuti_list(Request $request)
    {
        $data['title']='Cuti';
        $tahun = date('Y'); //Mengambil tahun saat ini
        $bulan = date('m'); //Mengambil bulan saat ini
        $month=$tahun.'-'.$bulan;
        if (isset($_POST['bulan'])) {
            $id_pegawai=$request->input('id_pegawai');
            $tahun=$request->input('tahun');
            $bulan=$request->input('bulan');
            $jumlah_hari = cal_days_in_month(CAL_GREGORIAN, $bulan, $tahun);
            $month=$tahun.'-'.$bulan;
        }else{
            $jumlah_hari = cal_days_in_month(CAL_GREGORIAN, $bulan, $tahun);
        }
        if (request()->ajax()) {
            $query=DB::table('m_employees')
                    ->get();
            $data1 = array();
            foreach ($query as $val) {
                $row = array();
                $row['id']= $val->id;
                $row['name']= $val->name;
                $cuti=DB::table('tbl_cuti')
                        ->where('m_employee_id', $val->id)
                        ->where('tanggal', 'like', $month.'%')
                        ->orderBy('tanggal', 'ASC')
                        ->get();
                $list_cuti='';
                foreach ($cuti as $key => $value) {
                    $tanggal=strtotime($value->tanggal);
                    $list_cuti.=date('d-m-Y',$tanggal).", ";
                }
                $row['tanggal_cuti']= rtrim($list_cuti, ", ");
                $row['bulan']= $month;
                $row['action']='<a href="/cuti/form/'.$val->id.'/'.$month.'" class="btn btn-success btn-sm"><i class="mdi mdi-pencil"></i></a>';
                $data1[] = $row;
            }
            return DataTables::of($data1)
                                    ->addColumn('action', function ($row) {

                                        return '<a href="/cuti/form/'.$row['id'].'/'.$row['bulan'].'" class="btn btn-success btn-sm"><i class="mdi mdi-pencil"></i></a>';
                                    })
                                    ->make(true);
        }
        $data['bulan'] = $bulan;
        $data['tahun'] = $tahun;
        $data['month'] = $month;        
        
        $query=DB::table('m_employees')
                    ->get();
        $data1 = array();
        foreach ($query as $val) {
            $row = array();
            $row['id']= $val->id;
            $row['name']= $val->name;
            $cuti=DB::table('tbl_cuti')
                    ->where('m_employee_id', $val->id)
                    ->where('tanggal', 'like', $month.'%')
                    ->orderBy('tanggal', 'ASC')
                    ->get();
            $list_cuti='';
            foreach ($cuti as $key => $value) {
                $tanggal=strtotime($value->tanggal);
                $list_cuti.=date('d-m-Y',$tanggal).", ";
            }
            $row['tanggal_cuti']= rtrim($list_cuti, ", ");
            $data1[] = $row;
        }

        $data['detail']=$data1;
        return view('pages.hrms.cuti.list_cuti', $data);
    } 
    public function cutiJson(Request $request){

        $tahun = date('Y'); //Mengambil tahun saat ini
        $bulan = date('m'); //Mengambil bulan saat ini
        $month=$tahun.'-'.$bulan;
        if (isset($_POST['bulan'])) {
            $id_pegawai=$request->input('id_pegawai');
            $tahun=$request->input('tahun');
            $bulan=$request->input('bulan');
            $jumlah_hari = cal_days_in_month(CAL_GREGORIAN, $bulan, $tahun);
            $month=$tahun.'-'.$bulan;
        }else{
            $jumlah_hari = cal_days_in_month(CAL_GREGORIAN, $bulan, $tahun);
        }
        $query=DB::table('m_employees')
                    ->get();
        $data1 = array();
        foreach ($query as $val) {
            $row = array();
            $row['id']= $val->id;
            $row['name']= $val->name;
            $cuti=DB::table('tbl_cuti')
                    ->where('m_employee_id', $val->id)
                    ->where('tanggal', 'like', $month.'%')
                    ->orderBy('tanggal', 'ASC')
                    ->get();
            $list_cuti='';
            foreach ($cuti as $key => $value) {
                $tanggal=strtotime($value->tanggal);
                $list_cuti.=date('d-m-Y',$tanggal).", ";
            }
            $row['tanggal_cuti']= rtrim($list_cuti, ", ");
            $row['bulan']= $month;
            $row['action']='<a href="/cuti/form/'.$val->id.'/'.$month.'" class="btn btn-success btn-sm"><i class="mdi mdi-pencil"></i></a>';
            $data1[] = $row;
        }
        return DataTables::of($data1)
                                ->addColumn('action', function ($row) {

                                    return '<a href="/cuti/form/'.$row['id'].'/'.$row['bulan'].'" class="btn btn-success btn-sm"><i class="mdi mdi-pencil"></i></a>';
                                })
                                ->make(true);
    }
    public function cutiForm(Request $request, $id, $date){
        $id_pegawai=1;   
        if (isset($_POST['bulan'])) {
            $id_pegawai=$request->input('id_pegawai');
            $tahun=$request->input('tahun');
            $bulan=$request->input('bulan');
            $date=$tahun.'-'.$bulan;
            $jumlah_hari = cal_days_in_month(CAL_GREGORIAN, $bulan, $tahun);
            if ($request->input('tanggal')) {
                $tgl=strtotime($request->input('tanggal'));
                $tanggal=date('Y-m-d', $tgl);
                $cek=$this->getCutiByDate($id, $date);
                if (count($cek) < 3) {
                    // $cek_date=$this->Hrms_model->cek_cuti_by_date($tanggal);
                    $cek_date=DB::table('tbl_cuti')
                            ->where('m_employee_id', $id)
                            ->where('tanggal', $request->input('tanggal'))
                            ->first();
                    if ($cek_date == null) {
                        $data_cuti=array(
                            'm_employee_id'    => $request->input('id_pegawai'),
                            'tanggal'       => $request->input('tanggal')
                        );
                        DB::table('tbl_cuti')->insert($data_cuti);
                        // $this->session->set_flashdata('message_type', 'success');
                        // $this->session->set_flashdata('message', 'Create Record Success');
                    }else{
                        // $this->session->set_flashdata('message_type', 'error');
                        // $this->session->set_flashdata('message', 'Tanggal yang anda input sudah ada');
                    }
                }else{
                    // $this->session->set_flashdata('message_type', 'error');
                    // $this->session->set_flashdata('message', 'Cuti hanya bisa diinput 3 hari');
                }
            }
        }else{
            $newDate=explode('-', $date);
            $jumlah_hari = cal_days_in_month(CAL_GREGORIAN, $newDate[1], $newDate[0]);
        }

        $newDate=explode('-', $date);
        $data=array(
            'jumlah_hari'   => $jumlah_hari,  
            'list_cuti'     => $this->getCutiByDate($id, $date),
            'date'          => $date,
            'bulan'         => $newDate[1],
            'tahun'         => $newDate[0],
            'id_pegawai'    => $id,
            'pegawai'       => DB::table('m_employees')->where('id',$id)->first()
        );

        return view('pages.hrms.cuti.cuti_form', $data);
    }
    private function getCutiByDate($id, $date){
        $query=DB::table('tbl_cuti')
                    ->where('m_employee_id', $id)
                    ->where('tanggal', 'like', $date.'%')
                    ->get();
        return $query;
    }
}
