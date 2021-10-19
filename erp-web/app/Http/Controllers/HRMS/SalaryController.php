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

class SalaryController extends Controller
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
                        ->join('tbl_setting_gaji', 'm_employees.id', '=', 'tbl_setting_gaji.m_employee_id')
                        ->join('m_positions', 'm_positions.id', '=', 'm_employees.position_id')
                        ->join('sites', 'sites.id', '=', 'm_employees.site_id')
                        ->select('m_employees.*', 'tbl_setting_gaji.*', 'm_positions.name as position_name', 'sites.name as site_name')
                        ->get();
            return DataTables::of($query)
                                    ->addColumn('action', function ($row) {

                                        return '<a href="'.url('salary/slip/'.$row->id).'" class="btn btn-primary btn-sm" title="Slip Gaji"><i class="mdi mdi-email-open"></i></a>&nbsp;
                                        <a href="'.url('salary/edit/'.$row->id_setting_gaji).'" class="btn btn-success btn-sm"><i class="mdi mdi-pencil"></i></a>&nbsp;
                                        <a href="'.url('salary/delete/'.$row->id_setting_gaji).'" class="btn btn-danger btn-sm" onclick="javasciprt: return confirm(\'Are You Sure ?\')"><i class="mdi mdi-delete"></i></a>';
                                    })
                                    ->make(true);
        }
        return view('pages.hrms.salary.list_salary');
    }
    public function create(){
        $data['employee']=DB::select('select id, name from m_employees where id not in (select m_employee_id from tbl_setting_gaji)');
        return  view('pages.hrms.salary.salary_add', $data);
    }
    public function store(Request $request){
        $input=array(
            'm_employee_id' => $request->input('id_pegawai'),
            'gaji_pokok' => $this->currency($request->input('gaji_pokok')),
            'denda' => $this->currency($request->input('denda')),
            'denda_telat' => $this->currency($request->input('denda_telat')),
            'uang_makan' => $this->currency($request->input('uang_makan')),
            'uang_transport' => $this->currency($request->input('uang_transport')),
        );
        DB::table('tbl_setting_gaji')->insert($input);
        return redirect('salary');
    }
    public function edit($id){
        $data['detail']=DB::table('tbl_setting_gaji')->where('id_setting_gaji', $id)->first();
        $data['employee']=DB::table('m_employees')->select('id', 'name')->get();
        return view('pages.hrms.salary.salary_edit', $data);
    }
    public function update(Request $request){
        $input=array(
            // 'm_employee_id' => $request->input('id_pegawai'),
            'gaji_pokok' => $this->currency($request->input('gaji_pokok')),
            'denda' => $this->currency($request->input('denda')),
            'denda_telat' => $this->currency($request->input('denda_telat')),
            'uang_makan' => $this->currency($request->input('uang_makan')),
            'uang_transport' => $this->currency($request->input('uang_transport')),
            'dtm_upd' => date('Y-m-d H:i:s'),
        );
        DB::table('tbl_setting_gaji')
                    ->where('id_setting_gaji', $request->input('id'))
                    ->update($input);
        return redirect('salary');
    }
    public function delete($id){
        $raw=DB::table('tbl_setting_gaji')
                    ->where('id_setting_gaji', $id)
                    ->first();
        if ($raw != null) {
            DB::table('tbl_setting_gaji')
                    ->where('id_setting_gaji', $id)
                    ->delete();
        }
        return redirect('salary');
    }
    private function currency($val){
        $data=explode('.', $val);
        $new=implode('', $data);
        return $new;
    }
    public function slip(Request $request, $id){
        $bulan=0;
        if ($request->input('bulan')) {
            $bulan=$request->input('tahun').'-'.$request->input('bulan');
        }else{
            $bulan=date('Y-m');
        }

        $daftar_hari = array(
         'Sunday' => 'Minggu',
         'Monday' => 'Senin',
         'Tuesday' => 'Selasa',
         'Wednesday' => 'Rabu',
         'Thursday' => 'Kamis',
         'Friday' => 'Jumat',
         'Saturday' => 'Sabtu'
        );
        $newDate=explode('-', $bulan);
        $jumlah_hari = cal_days_in_month(CAL_GREGORIAN, $newDate[1], $newDate[0]);
        $jumlah_kehadiran=$jumlah_telat=$jumlah_denda=$jumlah_absen=0;
        $gaji=DB::table('m_employees')
                        ->join('tbl_setting_gaji', 'm_employees.id', '=', 'tbl_setting_gaji.m_employee_id')
                        ->join('m_positions', 'm_positions.id', '=', 'm_employees.position_id')
                        ->join('sites', 'sites.id', '=', 'm_employees.site_id')
                        ->select('m_employees.*', 'tbl_setting_gaji.*', 'm_positions.name as position_name', 'sites.name as site_name')
                        ->where('m_employees.id', $id)
                        ->first();

        $detail=array();
        $total_uang_makan=$jumlah_uang_makan=$total_uang_transport=$jumlah_uang_transport=$total_hari_kerja=0;
        $temp_telat=$temp_absen=$bonus_disiplin=$total_bonus_disiplin=0;
        for ($i=1; $i <= $jumlah_hari ; $i++) { 
            $hari=(strlen($i) < 2 ? '0'.$i : $i);
            $date=$bulan.'-'.$hari;
            $absensi=$this->getAbsensiByDay($id, $date);
            $denda=$telat=0;
            $tgl_cuti=$alasan_denda="";
            if ($absensi != null) {
                $jam_masuk = strtotime('08:00:00');
                $jam_masuk_real   = strtotime($absensi->jam_datang);
                $diff  = $jam_masuk_real - $jam_masuk;
                $hours = floor($diff / (60 * 60));
                $minutes = $diff - $hours * (60 * 60);
                // $seconds = $diff % (60);
                // echo $value->jam_datang." ".floor($seconds/1);
                // echo 'Selisih Waktu: ' . $hours .  ' Jam, ' . floor( $minutes / 60 ) . ' Menit <br>';
                $selisih_datang=($hours * 60) + floor( $minutes / 60 );
                if ($selisih_datang > 1) {
                    $jumlah_telat++;
                    $temp_telat++;
                    // if ($jumlah_telat > 2) {
                    //     // $denda=floor($selisih_datang/10)*10000;
                    //     $denda=(floor($selisih_datang/10)+1)*10000;
                    // }
                    // $denda=(floor($selisih_datang/10)+1)*10000;
                    $denda=$selisih_datang*$gaji->denda_telat;
                    $telat=$selisih_datang;
                    $alasan_denda="telat ".$telat." menit";
                }
                $total_hari_kerja++;
                $jumlah_kehadiran++;
            }else{
                $nama_jabatan=strtolower($gaji->position_name);
                $cek_cuti=DB::table('tbl_cuti')->where('m_employee_id', $id)->where('tanggal', $date)->first();
                $cek_ket=DB::table('tbl_absensi')
                            ->rightJoin('m_employees', 'm_employees.id', '=', 'tbl_absensi.m_employee_id')
                            ->select('tbl_absensi.*', 'm_employees.name')
                            ->where('tbl_absensi.m_employee_id', $id)
                            ->where('tbl_absensi.tanggal', $date)
                            ->first();
                $keterangan=($cek_ket == null ? 'bolos' : ($cek_ket->keterangan == 'sakit' ? 'sakit' : 'bolos'));
                if ($cek_cuti == null) {
                    $namahari = date('l', strtotime($date));
                    if ($namahari == 'Sunday') {
                        $denda=0;
                        $alasan_denda='Hari Libur';
                    }else{
                        $denda=($keterangan == 'sakit' ? 0 : $gaji->denda);
                        // $denda=$nama_jabatan == 'owner' ? $gaji->denda : ($keterangan == 'sakit' ? 75000 : 200000);
                        $alasan_denda=($keterangan == 'sakit' ? 'Ijin Sakit' : 'Bolos')." hari ".$daftar_hari[$namahari];
                        $total_hari_kerja++;
                        $jumlah_absen++;
                        $temp_absen++;
                    }
                }else{
                    $tgl_cuti=$date;
                    $namahari = date('l', strtotime($date));
                    $alasan_denda="Ijin Cuti";
                    $total_hari_kerja++;
                    $jumlah_absen++;
                    $temp_absen++;
                }
            }
            if ($i % 7 == 0) {
                if ($temp_telat == 0 && $temp_absen == 0) {
                    $bonus_disiplin+=($gaji->gaji_pokok * (2.5/100));
                    $total_bonus_disiplin++;
                }
                $temp_telat=$temp_absen=0;
            }
            $jumlah_denda+=$denda;
            $detail[]=array('tanggal_cuti' => $tgl_cuti, 'alasan_denda' => $alasan_denda, 'menit_telat' => $telat, 'denda' => $denda,'tanggal' => $date);
        }
        
        $sum_komisi=$total_potong=0;
        $durasi_lembur=$durasi_telat=0;
        $total_tepat_waktu=0;
        $data=array(
            'absensi'       => DB::table('tbl_absensi')
                                ->leftJoin('m_employees', 'm_employees.id', '=', 'tbl_absensi.m_employee_id')
                                ->select('tbl_absensi.*', 'm_employees.name')
                                ->where('tbl_absensi.m_employee_id', $id)
                                ->where('tbl_absensi.jam_datang', '!=', '00:00:00')
                                ->where('tbl_absensi.tanggal', 'like', $date.'%')
                                ->get(),
            'gaji'          => $gaji,
            'bulan'         => $bulan,
            'detail'        => $detail,
            'jumlah_denda'  => $jumlah_denda,
            'jumlah_kehadiran'=> $jumlah_kehadiran,
            'total_bonus_disiplin'  => $total_bonus_disiplin,
            'bonus_disiplin'  => $bonus_disiplin,
            'jumlah_telat'  => $jumlah_telat,
            'total_hari_kerja'  => $total_hari_kerja,
            'jumlah_absen'  => $jumlah_absen,
            'id'            =>$id,
        );
        // dd($data);
        return view('pages.hrms.salary.salary_slip', $data);
    }
    public function cetak(Request $request){
        $bulan=0;
        if ($request->input('bulan')) {
            $bulan=$request->input('tahun').'-'.$request->input('bulan');
        }else{
            $bulan=date('Y-m');
        }
        $id=$request->id_pegawai;
        $daftar_hari = array(
        'Sunday' => 'Minggu',
        'Monday' => 'Senin',
        'Tuesday' => 'Selasa',
        'Wednesday' => 'Rabu',
        'Thursday' => 'Kamis',
        'Friday' => 'Jumat',
        'Saturday' => 'Sabtu'
        );
        $newDate=explode('-', $bulan);
        $jumlah_hari = cal_days_in_month(CAL_GREGORIAN, $newDate[1], $newDate[0]);
        $jumlah_kehadiran=$jumlah_telat=$jumlah_denda=$jumlah_absen=0;
        $gaji=DB::table('m_employees')
                        ->join('tbl_setting_gaji', 'm_employees.id', '=', 'tbl_setting_gaji.m_employee_id')
                        ->join('m_positions', 'm_positions.id', '=', 'm_employees.position_id')
                        ->join('sites', 'sites.id', '=', 'm_employees.site_id')
                        ->select('m_employees.*', 'tbl_setting_gaji.*', 'm_positions.name as position_name', 'sites.name as site_name')
                        ->where('m_employees.id', $id)
                        ->first();

        $detail=array();
        $total_uang_makan=$jumlah_uang_makan=$total_uang_transport=$jumlah_uang_transport=$total_hari_kerja=0;
        $temp_telat=$temp_absen=$bonus_disiplin=$total_bonus_disiplin=0;
        for ($i=1; $i <= $jumlah_hari ; $i++) { 
            $hari=(strlen($i) < 2 ? '0'.$i : $i);
            $date=$bulan.'-'.$hari;
            $absensi=$this->getAbsensiByDay($id, $date);
            $denda=$telat=0;
            $tgl_cuti=$alasan_denda="";
            if ($absensi != null) {
                $jam_masuk = strtotime('08:00:00');
                $jam_masuk_real   = strtotime($absensi->jam_datang);
                $diff  = $jam_masuk_real - $jam_masuk;
                $hours = floor($diff / (60 * 60));
                $minutes = $diff - $hours * (60 * 60);
                // $seconds = $diff % (60);
                // echo $value->jam_datang." ".floor($seconds/1);
                // echo 'Selisih Waktu: ' . $hours .  ' Jam, ' . floor( $minutes / 60 ) . ' Menit <br>';
                $selisih_datang=($hours * 60) + floor( $minutes / 60 );
                if ($selisih_datang > 1) {
                    $jumlah_telat++;
                    $temp_telat++;
                    // if ($jumlah_telat > 2) {
                    //     // $denda=floor($selisih_datang/10)*10000;
                    //     $denda=(floor($selisih_datang/10)+1)*10000;
                    // }
                    // $denda=(floor($selisih_datang/10)+1)*10000;
                    $denda=$selisih_datang*$gaji->denda_telat;
                    $telat=$selisih_datang;
                    $alasan_denda="telat ".$telat." menit";
                }
                $total_hari_kerja++;
                $jumlah_kehadiran++;
            }else{
                $nama_jabatan=strtolower($gaji->position_name);
                $cek_cuti=DB::table('tbl_cuti')->where('m_employee_id', $id)->where('tanggal', $date)->first();
                $cek_ket=DB::table('tbl_absensi')
                            ->rightJoin('m_employees', 'm_employees.id', '=', 'tbl_absensi.m_employee_id')
                            ->select('tbl_absensi.*', 'm_employees.name')
                            ->where('tbl_absensi.m_employee_id', $id)
                            ->where('tbl_absensi.tanggal', $date)
                            ->first();
                $keterangan=($cek_ket == null ? 'bolos' : ($cek_ket->keterangan == 'sakit' ? 'sakit' : 'bolos'));
                if ($cek_cuti == null) {
                    $namahari = date('l', strtotime($date));
                    if ($namahari == 'Sunday') {
                        $denda=0;
                        $alasan_denda='Hari Libur';
                    }else{
                        $denda=($keterangan == 'sakit' ? 0 : $gaji->denda);
                        // $denda=$nama_jabatan == 'owner' ? $gaji->denda : ($keterangan == 'sakit' ? 75000 : 200000);
                        $alasan_denda=($keterangan == 'sakit' ? 'Ijin Sakit' : 'Bolos')." hari ".$daftar_hari[$namahari];
                        $total_hari_kerja++;
                        $jumlah_absen++;
                        $temp_absen++;
                    }
                }else{
                    $tgl_cuti=$date;
                    $namahari = date('l', strtotime($date));
                    $alasan_denda="Ijin Cuti";
                    $total_hari_kerja++;
                    $jumlah_absen++;
                    $temp_absen++;
                }
            }
            if ($i % 7 == 0) {
                if ($temp_telat == 0 && $temp_absen == 0) {
                    $bonus_disiplin+=($gaji->gaji_pokok * (2.5/100));
                    $total_bonus_disiplin++;
                }
                $temp_telat=$temp_absen=0;
            }
            $jumlah_denda+=$denda;
            $detail[]=array('tanggal_cuti' => $tgl_cuti, 'alasan_denda' => $alasan_denda, 'menit_telat' => $telat, 'denda' => $denda,'tanggal' => $date);
        }
        
        $sum_komisi=$total_potong=0;
        $durasi_lembur=$durasi_telat=0;
        $total_tepat_waktu=0;
        $data=array(
            'absensi'       => DB::table('tbl_absensi')
                                ->leftJoin('m_employees', 'm_employees.id', '=', 'tbl_absensi.m_employee_id')
                                ->select('tbl_absensi.*', 'm_employees.name')
                                ->where('tbl_absensi.m_employee_id', $id)
                                ->where('tbl_absensi.jam_datang', '!=', '00:00:00')
                                ->where('tbl_absensi.tanggal', 'like', $date.'%')
                                ->get(),
            'gaji'          => $gaji,
            'bulan'         => $bulan,
            'detail'        => $detail,
            'jumlah_denda'  => $jumlah_denda,
            'jumlah_kehadiran'=> $jumlah_kehadiran,
            'total_bonus_disiplin'  => $total_bonus_disiplin,
            'bonus_disiplin'  => $bonus_disiplin,
            'jumlah_telat'  => $jumlah_telat,
            'total_hari_kerja'  => $total_hari_kerja,
            'jumlah_absen'  => $jumlah_absen,
            'id'            =>$id,
        );
        
        return view('pages.hrms.salary.cetak_slip_gaji', $data);
    }
    private function getAbsensiByDay($id, $date){
        $query=DB::table('tbl_absensi')
                ->rightJoin('m_employees', 'm_employees.id', '=', 'tbl_absensi.m_employee_id')
                ->select('tbl_absensi.*', 'm_employees.name')
                ->where('tbl_absensi.m_employee_id', $id)
                ->where('tbl_absensi.jam_datang', '!=', '00:00:00')
                ->where('tbl_absensi.jam_datang', '!=', NULL)
                ->where('tbl_absensi.tanggal', $date)
                ->first();
        return $query;
    }
}
