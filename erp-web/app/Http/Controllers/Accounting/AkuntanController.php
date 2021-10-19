<?php

namespace App\Http\Controllers\Accounting;

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
use App\Exports\InvoicesExport;
use App\Exports\NeracaExport;
use App\Exports\NeracaSaldoExport;
use App\Exports\GeneralLedgerExport;
use App\Exports\LabaRugiAllExport;
use App\Exports\AccountExport;
use App\Http\Controllers\RAB\RabController;
use App\Imports\ExcelDataImport;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Shared\Date;

use DB;

class AkuntanController extends Controller
{
    private $base_api_url;
    public $site_id = null;
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
        $raw=DB::table('tbl_akun')->get();
        $data1=array();
        foreach ($raw as $key => $value) {
            $main=DB::table('tbl_akun')->where('id_akun', $value->id_main_akun)->first();
            $row=array();
            $row['id_akun']= $value->id_akun;
            $row['no_akun']= $value->no_akun;
            $row['nama_akun']= $value->nama_akun;
            $row['level']= $value->level;
            $row['main']= ($main != null ? $main->nama_akun : '');
            $data1[]=$row;
        }
        
        $data=array(
            'list'  => $data1
        );
        return view('pages.akuntansi.list_akun', $data);
    }
    public function createAkun(){
        // $data['parent_option']=array();
        $data['parent_option']= DB::table('tbl_akun')->where('level', 0)->get();
        $parent_option_js=array();
        foreach (DB::table('tbl_akun')->where('level', 0)->get() as $key => $value) {
            // $data['parent_option'][$value->id_akun]=$value->nama_akun;
            $parent_option_js[]=array(
                'label'     => $value->id_akun,
                'value'     => $value->no_akun,
            );
        }
        $data['parent_option_js']=json_encode($parent_option_js);

        return view('pages.akuntansi.create_account', $data);
    }
    public function storeAkun(Request $request)
    {
        
        $row=DB::table('tbl_akun')->where('id_akun', $request->input('id_parent'))->first();
        $id_main_akun=0;
        $level=$request->input('level');
        $nama_akun=$request->input('nama_akun');
        $no_akun=$request->input('no_akun');
        $id_parent=$turunan1=$turunan2=$turunan3=$turunan4=0;
        $id_parent=($request->input('id_parent') ? $request->input('id_parent') : 0);
        $turunan1=($request->input('level2') ? $request->input('level2') : 0);
        $turunan2=($request->input('level3') ? $request->input('level3') : 0);
        $turunan3=($request->input('level4') ? $request->input('level4') : 0);

        if ($level == 1) {
            $id_main_akun=$request->input('id_parent');
        }else if ($level == 2) {
            $id_main_akun=$request->input('level2');
        }else if ($level == 3) {
            $id_main_akun=$request->input('level3');
        }else if ($level == 4) {
            $id_main_akun=$request->input('level4');
        }
        $data=array(
            'no_akun'         => $no_akun,
            'nama_akun'       => $nama_akun,
            'level'           => $level,
            'id_main_akun'    => $id_main_akun,
            'sifat_debit'     => $row->sifat_debit,
            'sifat_kredit'    => $row->sifat_kredit,
        );
        DB::table('tbl_akun')->insert($data);
        $row=DB::table('tbl_akun')->max('id_akun');
        $data_d=array(
            'id_akun'           => $row,
            'id_parent'         => $id_parent,
            'turunan1'          => $turunan1,
            'turunan2'          => $turunan2,
            'turunan3'          => $turunan3,
            'turunan4'          => $turunan4,
        );
        DB::table('tbl_akun_detail')->insert($data_d);
        return redirect('akuntansi');
    }
    public function getNoAkun($id){
        header('Content-Type: application/json');
        $data=DB::table('tbl_akun')
                ->select(DB::raw('MAX(id_main_akun) as id_main_akun'), DB::raw('MAX(no_akun) as no_akun'), DB::raw('COUNT(id_akun) as total_akun'))
                ->where('id_main_akun', $id)
                ->first();
        $data2=DB::table('tbl_akun')->where('id_akun', $id)->first();
        $akun= array('no_akun'=>$data->no_akun,'id_main_akun'=>$data->id_main_akun, 'no_akun_main'=>$data2->no_akun, 'total'=>$data->total_akun);
        echo json_encode($akun);
    }
    public function getLevel($id) {
        header('Content-Type: application/json');
        $data=DB::table('tbl_akun')
                ->where('id_main_akun', $id)
                ->get();
        echo json_encode($data);
    }
    public function jurnal(Request $request)
    {

        $user = DB::table('users')->where('id', $this->user_id)->first();

        $date=date('Y-m-d');
        $date2=date('Y-m-d');
        if ($request->input('date')) {
            $date=$request->input('date');
            $date2=$request->input('date2');
        }
        $list_trx=DB::table('tbl_trx_akuntansi')
                    ->where('tanggal', '>=', $date)
                    ->where('tanggal','<=', $date2)
                    ->orderBy('dtm_crt', 'DESC');
        if ($user->site_id != null) {
            $list_trx->where('location_id', $user->site_id);
        }
        $results=$list_trx->get();
        $account_payment=DB::table('tbl_akun')->where('id_main_akun', 13)->orWhere('id_akun', 96)->where('id_akun', '!=', 29)->pluck('id_akun');
        $data=array();

        foreach ($results as $key => $value) {
            $data[$key]=array(
                'id_trx_akun' => $value->id_trx_akun,
                'deskripsi' => $value->deskripsi,
                'tanggal'   => $value->tanggal
            );
            $data[$key]['detail']=$this->getDetailKas($value->id_trx_akun);
        }
        
        return view('pages.akuntansi.journal_list')->with(compact('data', 'date', 'date2', 'account_payment'));
    }
    private function getDetailKas($id)
    {
        $data=DB::table('tbl_trx_akuntansi')
                ->join('tbl_trx_akuntansi_detail', 'tbl_trx_akuntansi.id_trx_akun','=','tbl_trx_akuntansi_detail.id_trx_akun')
                ->join('tbl_akun', 'tbl_akun.id_akun','=','tbl_trx_akuntansi_detail.id_akun')
                ->where('tbl_trx_akuntansi.id_trx_akun', $id)
                ->orderBy('tbl_trx_akuntansi_detail.keterangan')
                ->get();
        foreach ($data as $key => $value) {
            $suppliers=DB::table('m_suppliers')->where('id', $value->m_supplier_id)->first();
            $value->supplier=$suppliers != null ? $suppliers->name : '';
            $value->inv_trxes=DB::table('inv_trxes')->select('no')->where('inv_trxes.id', $value->inv_trx_id)->first();
            $value->inv_trx_services=DB::table('inv_trx_services')->select('no')->where('inv_trx_services.id', $value->inv_trx_service_id)->first();
            $value->purchases=DB::table('purchases')->select('no')->where('id', $value->purchase_id)->first();
            $value->purchase_assets=DB::table('purchase_assets')->select('no')->where('id', $value->purchase_asset_id)->first();
            $value->orders=DB::table('orders')->select('order_no')->where('id', $value->order_id)->first();
            $value->install_orders=DB::table('install_orders')->select('no')->where('id', $value->install_order_id)->first();
            $value->giros=DB::table('giros')->select('no')->where('id', $value->giro_id)->first();
            $value->debts=DB::table('debts')->select('no')->where('id', $value->debt_id)->first();
            $value->ts_warehouses=DB::table('ts_warehouses')->select('no')->where('id', $value->ts_warehouse_id)->first();
            $value->paid_customers=DB::table('paid_customers')->select('no')->where('id', $value->paid_customer_id)->first();
            $value->paid_suppliers=DB::table('paid_suppliers')->select('no')->where('id', $value->paid_supplier_id)->first();
            $value->paid_debts=DB::table('paid_debts')->select('no')->where('id', $value->paid_debt_id)->first();
            $value->bill_vendors=DB::table('bill_vendors')->select('no')->where('id', $value->bill_vendor_id)->first();
            $value->payment_suppliers=DB::table('payment_suppliers')->select('no')->where('id', $value->payment_supplier_id)->first();
            $customer=DB::table('customers')->where('id', $value->customer_id)->first();
            $value->customer=$customer != null ? $customer->coorporate_name : '';
            $value->code_item='';
            if($value->m_item_id != null){
                $item=DB::table('m_items')->where('id', $value->m_item_id)->first();
                $value->code_item=$item->no;
            }
        }
        return $data;
    }
    public function getSaldo($id_parent=null, $date, $location_id, $jurnalClose){
        $query=DB::table('tbl_akun_detail')->join('tbl_akun', 'tbl_akun.id_akun', 'tbl_akun_detail.id_akun')
                        ->where('turunan1', 0)
                        ->orderBy('tbl_akun.no_akun', 'ASC');
        if ($id_parent != null) {
            $query->where('id_parent', $id_parent);
        }
        $turunan1=$query->get();
        
        $data=array();
        $j=0;
        foreach ($turunan1 as $key => $value) {
            $turunan2=DB::table('tbl_akun_detail')->join('tbl_akun', 'tbl_akun.id_akun', 'tbl_akun_detail.id_akun')->where('turunan1', $value->id_akun)->where('turunan2', 0)->get();
            $main_id=DB::table('tbl_akun_detail')
                    ->where('id_akun', $value->id_akun)
                    // ->orWhere('turunan1', $value->id_akun)
                    // ->orWhere('turunan2', $value->id_akun)
                    ->orWhere('turunan3', $value->id_akun)
                    ->pluck('id_akun');
            $saldo_before=DB::table('tbl_saldo_months')
                    ->where('bulan', $date)
                    ->whereIn('id_akun', $main_id)
                    ->where('location_id', $location_id)
                    ->select(DB::raw('SUM(total) as jumlah_saldo'))
                    ->first();
            if (count($turunan2) == 0) {
                $saldo=DB::table('tbl_saldo_akun')
                        ->where('id_akun', $value->id_akun)
                        ->where('tanggal', $date);
                if ($location_id != null) {
                    $saldo->where('location_id', $location_id);
                }
                $results=$saldo->first();
                // $n=0;
                $data[$j]['nama']=$value->nama_akun;
                $data[$j]['id_parent']=$value->id_parent;
                $data[$j]['data'][0]['detail']=$this->countSaldo($value->id_akun, $date, $location_id, $jurnalClose);
                $data[$j]['data'][0]['saldo']=$saldo_before;
                $j++;
            }else{
                $data[$j]['nama']=$value->nama_akun;
                $data[$j]['id_parent']=$value->id_parent;
                $n=0;
                foreach ($turunan2 as $k => $v) {
                    $saldo=DB::table('tbl_saldo_akun')
                            ->where('id_akun', $v->id_akun)
                            ->where('tanggal', $date);
                    if ($location_id != null) {
                        $saldo->where('location_id', $location_id);
                    }
                    $results=$saldo->first();

                    $data[$j]['data'][$n]['detail']=$this->countSaldo($v->id_akun, $date, $location_id, $jurnalClose);
                    $data[$j]['data'][$n]['saldo']=$saldo_before;
                    $n++;
                }
                $j++;
            }
        }
        return $data;
    }
    public function countSaldo($id, $date, $location_id, $jurnalClose){
        $results = DB::select( DB::raw("SELECT COALESCE((SELECT SUM(trd.jumlah) as jumlah FROM tbl_trx_akuntansi_detail 
                        trd JOIN tbl_trx_akuntansi tra ON trd.id_trx_akun=tra.id_trx_akun JOIN tbl_akun_detail tad ON 
                        tad.id_akun=trd.id_akun WHERE tra.tanggal::text LIKE '".$date."%' ".($location_id != null ? 'AND tra.location_id='.$location_id : '')." ".($jurnalClose == null ? 'AND tra.notes IS NULL ' : '')." AND tad.id_akun=tbl_akun_detail.id_akun 
                        AND trd.tipe='KREDIT'), 0) + COALESCE((SELECT SUM(trd.jumlah) as jumlah FROM tbl_trx_akuntansi_detail 
                        trd JOIN tbl_trx_akuntansi tra ON trd.id_trx_akun=tra.id_trx_akun JOIN tbl_akun_detail tad ON 
                        tad.id_akun=trd.id_akun WHERE tra.tanggal::text LIKE '".$date."%' ".($location_id != null ? 'AND tra.location_id='.$location_id : '')." ".($jurnalClose == null ? 'AND tra.notes IS NULL ' : '')." AND tad.turunan1=tbl_akun_detail.id_akun 
                        AND trd.tipe='KREDIT'), 0) + COALESCE((SELECT SUM(trd.jumlah) as jumlah FROM tbl_trx_akuntansi_detail 
                        trd JOIN tbl_trx_akuntansi tra ON trd.id_trx_akun=tra.id_trx_akun JOIN tbl_akun_detail tad ON 
                        tad.id_akun=trd.id_akun WHERE tra.tanggal::text LIKE '".$date."%' ".($location_id != null ? 'AND tra.location_id='.$location_id : '')." ".($jurnalClose == null ? 'AND tra.notes IS NULL ' : '')." AND tad.turunan2=tbl_akun_detail.id_akun 
                        AND trd.tipe='KREDIT'), 0) AS jumlah_kredit, COALESCE((SELECT SUM(trd.jumlah) as jumlah FROM tbl_trx_akuntansi_detail 
                        trd JOIN tbl_trx_akuntansi tra ON trd.id_trx_akun=tra.id_trx_akun JOIN tbl_akun_detail tad ON 
                        tad.id_akun=trd.id_akun WHERE tra.tanggal::text LIKE '".$date."%' ".($location_id != null ? 'AND tra.location_id='.$location_id : '')." ".($jurnalClose == null ? 'AND tra.notes IS NULL ' : '')." AND tad.id_akun=tbl_akun_detail.id_akun 
                        AND trd.tipe='DEBIT'), 0) + COALESCE((SELECT SUM(trd.jumlah) as jumlah FROM tbl_trx_akuntansi_detail 
                        trd JOIN tbl_trx_akuntansi tra ON trd.id_trx_akun=tra.id_trx_akun JOIN tbl_akun_detail tad ON 
                        tad.id_akun=trd.id_akun WHERE tra.tanggal::text LIKE '".$date."%' ".($location_id != null ? 'AND tra.location_id='.$location_id : '')." ".($jurnalClose == null ? 'AND tra.notes IS NULL ' : '')." AND tad.turunan1=tbl_akun_detail.id_akun 
                        AND trd.tipe='DEBIT'), 0) + COALESCE((SELECT SUM(trd.jumlah) as jumlah FROM tbl_trx_akuntansi_detail 
                        trd JOIN tbl_trx_akuntansi tra ON trd.id_trx_akun=tra.id_trx_akun JOIN tbl_akun_detail tad ON 
                        tad.id_akun=trd.id_akun WHERE tra.tanggal::text LIKE '".$date."%' ".($location_id != null ? 'AND tra.location_id='.$location_id : '')." ".($jurnalClose == null ? 'AND tra.notes IS NULL ' : '')." AND tad.turunan2=tbl_akun_detail.id_akun 
                        AND trd.tipe='DEBIT'), 0) AS jumlah_debit, tbl_akun_detail.*, tbl_akun.nama_akun, tbl_akun.no_akun , tbl_akun.sifat_debit, tbl_akun.sifat_kredit FROM tbl_akun_detail JOIN tbl_akun ON tbl_akun_detail.id_akun=tbl_akun.id_akun WHERE tbl_akun_detail.id_akun=".$id.""));
        return $results;
    }
    public function neraca(Request $request)
    {
       
        $user_id = request()->session()->get('user.id');
        $user = DB::table('users')->where('id', $this->user_id)->first();

        $date=0;
        $location_id=0;
        
        if ($request->input('bulan')) {
            $date=$request->input('tahun').'-'.$request->input('bulan');
            if ($request->input('location_id')) {
                $location_id=$request->input('location_id');
            }else{
                $location_id=$this->site_id;
            }
        }else{
            $date=date('Y-m');
            $location_id=$this->site_id;
        }
        $bulan=json_encode(explode('-', $date));
        $asset=$this->getSaldo(null, $date, $location_id, null);
        // $asset=$this->getSaldoAccount(null, $date1, $date2, $location_id, null);
        $parent=DB::table('tbl_akun')->where('level', 0)->get();
        foreach ($parent as $key => $value) {
            $i=0;
            foreach ($asset as $v) {
                if ($value->id_akun == $v['id_parent']) {
                    $value->detail[$i]=$v;
                    $i++;
                }
            }
        }
        $exp_bulan=explode('-', $date);
        $jumlah_hari=cal_days_in_month(CAL_GREGORIAN, $exp_bulan[1], $exp_bulan[0]);
        $data=array(
            'date'      => $date,
            'bulan'     => json_encode(explode('-', $date)),
            'saldo'     => $asset,
            'parent'    => $parent
        );
        return  view('pages.akuntansi.neraca_list')->with(compact('data', 'bulan', 'user', 'location_id', 'jumlah_hari'));
    }
    public function labaRugi(Request $request){
       
        $user_id = request()->session()->get('user.id');
        $user = DB::table('users')->where('id', $this->user_id)->first();

        $date=0;
        if ($request->input('bulan')) {
            $date=$request->input('tahun').'-'.$request->input('bulan');
        }else{
            $date=date('Y-m');
        }
        $data['bulan']=json_encode(explode('-', $date));
        // $data['pendapatan']=$this->getLabaRugi(4, $date, $user->site_id);
        // $data['beban']=$this->getLabaRugi(5, $date, $user->site_id);
        $data['profit']=$this->getSaldo(4, $date, $user->site_id, null);
        $data['biaya_produksi']=$this->getSaldo(5, $date, $user->site_id, null);
        $data['biaya_operasional']=$this->getSaldo(25, $date, $user->site_id, null);
        $data['biaya_adm']=$this->getSaldo(26, $date, $user->site_id, null);
        $data['biaya_lain']=$this->getSaldo(27, $date, $user->site_id, null);
        // return $data;
        return  view('pages.akuntansi.laba_rugi_list')->with(compact('data'));
    }
    private function cekAllJurnal($date, $location_id){
        $results = DB::select( DB::raw("SELECT COALESCE((SELECT SUM(trd.jumlah) as jumlah FROM tbl_trx_akuntansi_detail 
            trd JOIN tbl_trx_akuntansi tra ON trd.id_trx_akun=tra.id_trx_akun JOIN tbl_akun_detail tad ON 
            tad.id_akun=trd.id_akun WHERE CAST(tra.tanggal AS TEXT) LIKE '".$date."%' ".($location_id != null ? 'AND tra.location_id='.$location_id : '')." AND tad.id_akun=tbl_akun_detail.id_akun 
            AND trd.tipe='KREDIT'), 0) + COALESCE((SELECT SUM(trd.jumlah) as jumlah FROM tbl_trx_akuntansi_detail trd 
            JOIN tbl_trx_akuntansi tra ON trd.id_trx_akun=tra.id_trx_akun JOIN tbl_akun_detail tad ON 
            tad.id_akun=trd.id_akun WHERE CAST(tra.tanggal AS TEXT) LIKE '".$date."%' ".($location_id != null ? 'AND tra.location_id='.$location_id : '')." AND tad.turunan1=tbl_akun_detail.id_akun AND 
            trd.tipe='KREDIT'), 0) AS jumlah_kredit, COALESCE((SELECT SUM(trd.jumlah) as jumlah 
            FROM tbl_trx_akuntansi_detail trd JOIN tbl_trx_akuntansi tra ON trd.id_trx_akun=tra.id_trx_akun JOIN 
            tbl_akun_detail tad ON tad.id_akun=trd.id_akun WHERE CAST(tra.tanggal AS TEXT) LIKE '".$date."%' ".($location_id != null ? 'AND tra.location_id='.$location_id : '')." AND 
            tad.id_akun=tbl_akun_detail.id_akun AND trd.tipe='DEBIT'), 0) + COALESCE((SELECT SUM(trd.jumlah) as jumlah 
            FROM tbl_trx_akuntansi_detail trd JOIN tbl_trx_akuntansi tra ON trd.id_trx_akun=tra.id_trx_akun JOIN 
            tbl_akun_detail tad ON tad.id_akun=trd.id_akun WHERE CAST(tra.tanggal AS TEXT) LIKE '".$date."%' ".($location_id != null ? 'AND tra.location_id='.$location_id : '')." AND 
            tad.turunan1=tbl_akun_detail.id_akun AND trd.tipe='DEBIT'), 0) AS jumlah_debit, MAX(tbl_akun_detail.id_akun) AS 
            id_akun, MAX(tbl_akun.no_akun) AS no_akun, MAX(tbl_akun.nama_akun) AS nama_akun, MAX(tbl_akun_detail.turunan1) AS 
            turunan1, MAX(tbl_akun_detail.id_parent) AS id_parent FROM tbl_akun_detail JOIN tbl_akun ON 
            tbl_akun_detail.id_akun=tbl_akun.id_akun WHERE turunan1=0 GROUP BY tbl_akun_detail.id_akun ORDER BY no_akun"));
        return $results;
    }

    private function getLabaRugi($id, $date, $location_id){
        // $results = DB::select( DB::raw('SELECT COALESCE((SELECT SUM(trd.jumlah) as jumlah FROM tbl_trx_akuntansi_detail 
        //     trd JOIN tbl_trx_akuntansi tra ON trd.id_trx_akun=tra.id_trx_akun JOIN tbl_akun_detail tad ON 
        //     tad.id_akun=trd.id_akun WHERE CAST(tra.tanggal AS TEXT) LIKE "'.$date.'%" '.($location_id != null ? "AND tra.location_id=".$location_id : "").' AND tad.id_akun=tbl_akun_detail.id_akun 
        //     AND trd.tipe="KREDIT"), 0) + COALESCE((SELECT SUM(trd.jumlah) as jumlah FROM tbl_trx_akuntansi_detail trd 
        //     JOIN tbl_trx_akuntansi tra ON trd.id_trx_akun=tra.id_trx_akun JOIN tbl_akun_detail tad ON 
        //     tad.id_akun=trd.id_akun WHERE CAST(tra.tanggal AS TEXT) LIKE "'.$date.'%" '.($location_id != null ? "AND tra.location_id=".$location_id : "").' AND tad.turunan1=tbl_akun_detail.id_akun AND 
        //     trd.tipe="KREDIT"), 0) AS jumlah_kredit, COALESCE((SELECT SUM(trd.jumlah) as jumlah 
        //     FROM tbl_trx_akuntansi_detail trd JOIN tbl_trx_akuntansi tra ON trd.id_trx_akun=tra.id_trx_akun JOIN 
        //     tbl_akun_detail tad ON tad.id_akun=trd.id_akun WHERE CAST(tra.tanggal AS TEXT) LIKE "'.$date.'%" '.($location_id != null ? "AND tra.location_id=".$location_id : "").' AND 
        //     tad.id_akun=tbl_akun_detail.id_akun AND trd.tipe="DEBIT"), 0) + COALESCE((SELECT SUM(trd.jumlah) as jumlah 
        //     FROM tbl_trx_akuntansi_detail trd JOIN tbl_trx_akuntansi tra ON trd.id_trx_akun=tra.id_trx_akun JOIN 
        //     tbl_akun_detail tad ON tad.id_akun=trd.id_akun WHERE CAST(tra.tanggal AS TEXT) LIKE "'.$date.'%" '.($location_id != null ? "AND tra.location_id=".$location_id : "").' AND 
        //     tad.turunan1=tbl_akun_detail.id_akun AND trd.tipe="DEBIT"), 0) AS jumlah_debit, MAX(tbl_akun_detail.id_akun) AS 
        //     id_akun, MAX(tbl_akun.no_akun) AS no_akun, MAX(tbl_akun.nama_akun) AS nama_akun, MAX(tbl_akun_detail.turunan1) AS 
        //     turunan1, MAX(tbl_akun_detail.id_parent) AS id_parent FROM tbl_akun_detail JOIN tbl_akun ON 
        //     tbl_akun_detail.id_akun=tbl_akun.id_akun WHERE turunan1=0 AND id_parent='.$id.' GROUP BY tbl_akun_detail.id_akun ORDER BY tbl_akun.no_akun'));
        $results = DB::select( DB::raw("SELECT COALESCE((SELECT SUM(trd.jumlah) as jumlah FROM tbl_trx_akuntansi_detail 
            trd JOIN tbl_trx_akuntansi tra ON trd.id_trx_akun=tra.id_trx_akun JOIN tbl_akun_detail tad ON 
            tad.id_akun=trd.id_akun WHERE CAST(tra.tanggal AS TEXT) LIKE '".$date."%' ".($location_id != null ? 'AND tra.location_id='.$location_id : '')." AND tad.id_akun=tbl_akun_detail.id_akun 
            AND trd.tipe='KREDIT'), 0) + COALESCE((SELECT SUM(trd.jumlah) as jumlah FROM tbl_trx_akuntansi_detail trd 
            JOIN tbl_trx_akuntansi tra ON trd.id_trx_akun=tra.id_trx_akun JOIN tbl_akun_detail tad ON 
            tad.id_akun=trd.id_akun WHERE CAST(tra.tanggal AS TEXT) LIKE '".$date."%' ".($location_id != null ? 'AND tra.location_id='.$location_id : '')."  AND tad.turunan1=tbl_akun_detail.id_akun AND 
            trd.tipe='KREDIT'), 0) AS jumlah_kredit, COALESCE((SELECT SUM(trd.jumlah) as jumlah 
            FROM tbl_trx_akuntansi_detail trd JOIN tbl_trx_akuntansi tra ON trd.id_trx_akun=tra.id_trx_akun JOIN 
            tbl_akun_detail tad ON tad.id_akun=trd.id_akun WHERE CAST(tra.tanggal AS TEXT) LIKE '".$date."%' ".($location_id != null ? 'AND tra.location_id='.$location_id : '')."  AND 
            tad.id_akun=tbl_akun_detail.id_akun AND trd.tipe='DEBIT'), 0) + COALESCE((SELECT SUM(trd.jumlah) as jumlah 
            FROM tbl_trx_akuntansi_detail trd JOIN tbl_trx_akuntansi tra ON trd.id_trx_akun=tra.id_trx_akun JOIN 
            tbl_akun_detail tad ON tad.id_akun=trd.id_akun WHERE CAST(tra.tanggal AS TEXT) LIKE '".$date."%' ".($location_id != null ? 'AND tra.location_id='.$location_id : '')."  AND 
            tad.turunan1=tbl_akun_detail.id_akun AND trd.tipe='DEBIT'), 0) AS jumlah_debit, MAX(tbl_akun_detail.id_akun) AS 
            id_akun, MAX(tbl_akun.no_akun) AS no_akun, MAX(tbl_akun.nama_akun) AS nama_akun, MAX(tbl_akun_detail.turunan1) AS 
            turunan1, MAX(tbl_akun_detail.id_parent) AS id_parent FROM tbl_akun_detail JOIN tbl_akun ON 
            tbl_akun_detail.id_akun=tbl_akun.id_akun WHERE turunan1=0 AND id_parent='".$id."' GROUP BY tbl_akun_detail.id_akun ORDER BY no_akun"));
        return $results;
    }
    /**
     * Show the form for creating a new resource.
     * @return Response
     */

    public function createJournal()
    {

        $user = DB::table('users')->where('id', $this->user_id)->first();
        $location_id = $user->site_id;

        $business_locations = DB::table('sites')->get();
        $data['akun_option']=array();
        $data['akun_option'][''] = 'Pilih Akun / No Akun';
        $akun_option_js=array();
        $count=$c=0;
        $dataLevel0=DB::table('tbl_akun')->where('level', 0)->get();
        foreach ($dataLevel0 as $key => $value) {
            $id_akun=$no_akun=$nama_akun=0;
            $dataLevel1=DB::table('tbl_akun')->where('level', 1)->where('id_main_akun', $value->id_akun)->get();
            foreach ($dataLevel1 as $k => $v) {
                $id_akun=$v->id_akun;
                $no_akun=$v->no_akun;
                $nama_akun=$v->nama_akun;
                $dataLevel2=DB::table('tbl_akun')->where('level', 2)->where('id_main_akun', $v->id_akun)->get();
                foreach ($dataLevel2 as $k2 => $v2) {
                    $id_akun=$v2->id_akun;
                    $no_akun=$v2->no_akun;
                    $nama_akun=$v2->nama_akun;
                    $dataLevel3=DB::table('tbl_akun')->where('level', 3)->where('id_main_akun', $v2->id_akun)->get();
                    foreach ($dataLevel3 as $k3 => $v3) {
                        $c++;
                        $id_akun=$v3->id_akun;
                        $no_akun=$v3->no_akun;
                        $nama_akun=$v3->nama_akun;
                        $dataLevel4=DB::table('tbl_akun')->where('level', 4)->where('id_main_akun', $v3->id_akun)->get();
                        foreach ($dataLevel4 as $k4 => $v4) {
                            $c++;
                            $id_akun=$v4->id_akun;
                            $no_akun=$v4->no_akun;
                            $nama_akun=$v4->nama_akun;
                            $data['akun_option'][$id_akun]=$no_akun. ' | '.$nama_akun;
                            $akun_option_js[]=array(
                                'label'     => $id_akun,
                                'value'     => $no_akun. ' | '.$nama_akun
                            );
                        }
                        $data['akun_option'][$id_akun]=$no_akun. ' | '.$nama_akun;
                        $akun_option_js[]=array(
                            'label'     => $id_akun,
                            'value'     => $no_akun. ' | '.$nama_akun
                        );
                    }
                    $data['akun_option'][$id_akun]=$no_akun. ' | '.$nama_akun;
                    $akun_option_js[]=array(
                        'label'     => $id_akun,
                        'value'     => $no_akun. ' | '.$nama_akun
                    );
                }
                $data['akun_option'][$id_akun]=$no_akun. ' | '.$nama_akun;
                $akun_option_js[]=array(
                    'label'     => $id_akun,
                    'value'     => $no_akun. ' | '.$nama_akun
                );
            }
        }
        $data['akun_option_js']=json_encode($akun_option_js);
        $site_id=$this->site_id;
        return  view('pages.akuntansi.create_journal')
                ->with(compact('data', 'business_locations', 'location_id', 'user', 'site_id'));
    }

    public function storeJurnal(Request $request)
    {
        $deskripsi=$request->input('deskripsi');
        $location_id=$request->input('location_id');
        $tgl=$request->input('tanggal');
        $id_akun=$request->input('akun');
        $jumlah=$request->input('jumlah');
        $tipe_akun=$request->input('tipe_akun');
        $sifat_akun=$request->input('sifat_akun');
        $no_source=$request->input('no');
        $get_id=0;
        $type='0';
        // foreach ($id_akun as $key => $value) {
        //     $cek=DB::table('tbl_akun')->where('id_akun', $value)->where('id_main_akun', 13)->count();
        //     if ($cek != 0) {
        //         $get_id=$value;
        //         $type=($tipe_akun[$key] == 0 ? "KREDIT" : "DEBIT");
        //     }
        // }
        // $no=$this->createNo($get_id, $type);
        
        $data_trx=array(
                        'deskripsi'     => $deskripsi,
                        'location_id'     => $location_id,
                        'tanggal'       => $tgl,
                        // 'no'            => $no
                    );
        $insert=DB::table('tbl_trx_akuntansi')->insert($data_trx);

        if ($insert) {
            $id_last=DB::table('tbl_trx_akuntansi')->max('id_trx_akun');
            for ($i=0; $i < count($id_akun); $i++) { 
                $cek=DB::table('tbl_akun')->where('id_akun', $id_akun[$i])->where('id_main_akun', 13)->count();
                // $no='';
                // if ($cek != 0) {
                //     $get_id=$id_akun[$i];
                //     $type=($tipe_akun[$i] == 0 ? "KREDIT" : "DEBIT");
                //     $no=$this->createNo($get_id, $type);
                // }
                if ($id_akun[$i] != null) {
                    $data=array(
                        'id_trx_akun'   => $id_last,
                        'id_akun'       => $id_akun[$i],
                        'jumlah'        => $this->currency($jumlah[$i]),
                        'tipe'          => ($tipe_akun[$i] == 0 ? "KREDIT" : "DEBIT"),
                        'keterangan'    => $sifat_akun[$i],
                        'no'            => $no_source[$i]
                    );
                    // $this->updateSaldo($id_lawan[$i], $jumlah_lawan[$i], $tipe_akun_lawan[$i]);
                    DB::table('tbl_trx_akuntansi_detail')->insert($data);
                    if($id_akun[$i] == 24 || $id_akun[$i] == 101){
                        $warehouse_id=$id_akun[$i] == 24 ? 2 : 3;
                        $type=($tipe_akun[$i] == 0 ? "out" : "in");
                        $this->countSaldoCash($this->site_id, $warehouse_id, $type, $this->currency($jumlah[$i]));
                    }
                }
            }
        }
        return redirect('akuntansi/jurnal');
    }
    private function closeBookJournal($date, $location_id, $jumlah_hari){
        $pendapatan=$this->getSaldo(4, $date, $location_id, null);
        $beban=$this->getSaldo(5, $date, $location_id, null);
        $beban1=$this->getSaldo(25, $date, $location_id, null);
        $beban2=$this->getSaldo(26, $date, $location_id, null);
        $beban3=$this->getSaldo(27, $date, $location_id, null);
        foreach ($pendapatan as $key => $value) {
            foreach ($value['data'] as $v) {
                if ($v['detail'][0]->sifat_debit == 0) {
                    $total=$v['detail'][0]->jumlah_kredit - $v['detail'][0]->jumlah_debit;
                }else{
                    $total=$v['detail'][0]->jumlah_debit - $v['detail'][0]->jumlah_kredit;
                }
                $data=array(
                    'id_akun'   => 168,
                    'id_lawan'   => $v['detail'][0]->id_akun,
                    'total'   => ($total < 0 ? abs($total) : $total),
                    // 'total'   => $total,
                    'tipe_akun'   => ($total < 0 ? 1 : 0),
                    'tipe_akun_lawan'   => ($total < 0 ? 0 : 1),
                    'location_id'   => $location_id,
                    'tgl'   => $date.'-'.$jumlah_hari,
                    'name'   => $v['detail'][0]->nama_akun,
                );
                if ($total != 0) {
                    $this->jurnalTutupBuku($data);
                }
            }
        }
        foreach ($beban as $key => $value) {
            foreach ($value['data'] as $v) {
                if ($v['detail'][0]->sifat_debit == 0) {
                    $total=$v['detail'][0]->jumlah_kredit - $v['detail'][0]->jumlah_debit;
                }else{
                    $total=$v['detail'][0]->jumlah_debit - $v['detail'][0]->jumlah_kredit;
                }
                $data=array(
                    'id_akun'   => 168,
                    'id_lawan'   => $v['detail'][0]->id_akun,
                    'total'   => ($total < 0 ? abs($total) : $total),
                    // 'total'   => $total,
                    'tipe_akun'   => ($total < 0 ? 0 : 1),
                    'tipe_akun_lawan'   => ($total < 0 ? 1 : 0),
                    'location_id'   => $location_id,
                    'tgl'   => $date.'-'.$jumlah_hari,
                    'name'   => $v['detail'][0]->nama_akun,
                );
                if ($total != 0) {
                    $this->jurnalTutupBuku($data);
                }
            }
        }
        foreach ($beban1 as $key => $value) {
            foreach ($value['data'] as $v) {
                if ($v['detail'][0]->sifat_debit == 0) {
                    $total=$v['detail'][0]->jumlah_kredit - $v['detail'][0]->jumlah_debit;
                }else{
                    $total=$v['detail'][0]->jumlah_debit - $v['detail'][0]->jumlah_kredit;
                }
                $data=array(
                    'id_akun'   => 168,
                    'id_lawan'   => $v['detail'][0]->id_akun,
                    'total'   => ($total < 0 ? abs($total) : $total),
                    // 'total'   => $total,
                    'tipe_akun'   => ($total < 0 ? 0 : 1),
                    'tipe_akun_lawan'   => ($total < 0 ? 1 : 0),
                    'location_id'   => $location_id,
                    'tgl'   => $date.'-'.$jumlah_hari,
                    'name'   => $v['detail'][0]->nama_akun,
                );
                if ($total != 0) {
                    $this->jurnalTutupBuku($data);
                }
            }
        }
        foreach ($beban2 as $key => $value) {
            foreach ($value['data'] as $v) {
                if ($v['detail'][0]->sifat_debit == 0) {
                    $total=$v['detail'][0]->jumlah_kredit - $v['detail'][0]->jumlah_debit;
                }else{
                    $total=$v['detail'][0]->jumlah_debit - $v['detail'][0]->jumlah_kredit;
                }
                $data=array(
                    'id_akun'   => 168,
                    'id_lawan'   => $v['detail'][0]->id_akun,
                    'total'   => ($total < 0 ? abs($total) : $total),
                    // 'total'   => $total,
                    'tipe_akun'   => ($total < 0 ? 0 : 1),
                    'tipe_akun_lawan'   => ($total < 0 ? 1 : 0),
                    'location_id'   => $location_id,
                    'tgl'   => $date.'-'.$jumlah_hari,
                    'name'   => $v['detail'][0]->nama_akun,
                );
                if ($total != 0) {
                    $this->jurnalTutupBuku($data);
                }
            }
        }
        foreach ($beban3 as $key => $value) {
            foreach ($value['data'] as $v) {
                if ($v['detail'][0]->sifat_debit == 0) {
                    $total=$v['detail'][0]->jumlah_kredit - $v['detail'][0]->jumlah_debit;
                }else{
                    $total=$v['detail'][0]->jumlah_debit - $v['detail'][0]->jumlah_kredit;
                }
                $data=array(
                    'id_akun'   => 168,
                    'id_lawan'   => $v['detail'][0]->id_akun,
                    'total'   => ($total < 0 ? abs($total) : $total),
                    // 'total'   => $total,
                    'tipe_akun'   => ($total < 0 ? 0 : 1),
                    'tipe_akun_lawan'   => ($total < 0 ? 1 : 0),
                    'location_id'   => $location_id,
                    'tgl'   => $date.'-'.$jumlah_hari,
                    'name'   => $v['detail'][0]->nama_akun,
                );
                if ($total != 0) {
                    $this->jurnalTutupBuku($data);
                }
            }
        }
    }
    private function jurnalTutupBuku($data){
        $data_trx=array(
                        'deskripsi'     => 'Jurnal Tutup Buku '.$data['tgl'],
                        'location_id'     => $data['location_id'],
                        'tanggal'       => $data['tgl'],
                        'notes'       => 'jurnal tutup buku',
                    );
        $insert=DB::table('tbl_trx_akuntansi')->insert($data_trx);
        if ($insert) {
            $id_last=DB::table('tbl_trx_akuntansi')->max('id_trx_akun');
            $data1=array(
                        'id_trx_akun'   => $id_last,
                        'id_akun'       => $data['id_akun'],
                        'jumlah'        => $data['total'],
                        'tipe'          => ($data['tipe_akun'] == 0 ? "KREDIT" : "DEBIT"),
                        'keterangan'    => 'akun',
                    );
            DB::table('tbl_trx_akuntansi_detail')->insert($data1);
            $data1=array(
                'id_trx_akun'   => $id_last,
                'id_akun'       => $data['id_lawan'],
                'jumlah'        => $data['total'],
                'tipe'          => ($data['tipe_akun_lawan'] == 0 ? "KREDIT" : "DEBIT"),
                'keterangan'    => 'lawan',
            );
            DB::table('tbl_trx_akuntansi_detail')->insert($data1);
        }
    }
    public function closeBook(Request $request){
        
        $user_id = $this->user_id;
        $user = DB::table('users')->where('id', $user_id)->first();
        
        $location_id = $user->site_id;

        $date=0;
        if ($request->input('bulan')) {
            $date=$request->input('tahun').'-'.$request->input('bulan');
        }else{
            $date=date('Y-m');
        }

        $bulan=explode('-', $date);
        if ($request->input('submit')) {

            $location_id=$request->input('location_id') ? $request->input('location_id') : $user->site_id;
            $time = strtotime($date);
            $final = date('Y-m', strtotime("+1 month", $time));
            $cekJurnal=DB::table('tbl_saldo_akun')->where('tanggal', $final)->where('location_id', $location_id)->count();
            // $jurnal=$this->cekAllJurnal($date, $location_id);
            // $bl=BusinessLocation::where('id', $location_id)->first();

            $jumlah_hari=cal_days_in_month(CAL_GREGORIAN, $bulan[1], $bulan[0]);
            // $this->closeBookJournal($date, $location_id, $jumlah_hari);
            
            $asset=$this->getSaldo(null, $date, $location_id, 'jurnal tutup buku');
            
            $total_pendapatan=$total_beban=$total_kas=0;
            foreach ($asset as $key => $value) {
                foreach ($value['data'] as $v){
                    $saldo=$v['saldo'];
                    if ($v['detail'][0]->sifat_debit == 1) {
                        $jumlah_saldo=(($saldo != null ? $saldo->jumlah_saldo : 0) + $v['detail'][0]->jumlah_debit) - $v['detail'][0]->jumlah_kredit;
                        $data_saldo=array(
                            'id_akun'   => $v['detail'][0]->id_akun,
                            'jumlah_saldo'  => round($jumlah_saldo, 2),
                            'tanggal'   => $final,
                            'location_id'   => $location_id,
                            'is_updated'   => 0,
                        );
                        $ceksaldo=DB::table('tbl_saldo_akun')->where('tanggal', $final)->where('location_id', $location_id)->where('id_akun', $v['detail'][0]->id_akun)->first();
                        if ($ceksaldo == null) {
                            $data_saldo['dtm_crt']=date('Y-m-d H:i:s');
                            $data_saldo['dtm_upd']=date('Y-m-d H:i:s');
                            DB::table('tbl_saldo_akun')->insert($data_saldo);
                        }else{
                            $data_saldo['dtm_upd']=date('Y-m-d H:i:s');
                            DB::table('tbl_saldo_akun')->where('id_saldo', $ceksaldo->id_saldo)->update($data_saldo);
                        }
                    }else{
                        $jumlah_saldo=(($saldo != null ? $saldo->jumlah_saldo : 0) + $v['detail'][0]->jumlah_kredit) - $v['detail'][0]->jumlah_debit;
                        $data_saldo=array(
                            'id_akun'   => $v['detail'][0]->id_akun,
                            'jumlah_saldo'  => round($jumlah_saldo, 2),
                            'tanggal'   => $final,
                            'location_id'   => $location_id,
                            'is_updated'   => 0,
                        );
                        $ceksaldo=DB::table('tbl_saldo_akun')->where('tanggal', $final)->where('location_id', $location_id)->where('id_akun', $v['detail'][0]->id_akun)->first();
                        if ($ceksaldo == null) {
                            $data_saldo['dtm_crt']=date('Y-m-d H:i:s');
                            $data_saldo['dtm_upd']=date('Y-m-d H:i:s');
                            DB::table('tbl_saldo_akun')->insert($data_saldo);
                        }else{
                            $data_saldo['dtm_upd']=date('Y-m-d H:i:s');
                            DB::table('tbl_saldo_akun')->where('id_saldo', $ceksaldo->id_saldo)->update($data_saldo);
                        }
                    }
                }
            }

            $data_account=$this->countSaldoAccount($date);
            
            // dd($data['detail_month']);
            foreach ($data_account['detail_month'] as $v){
                if ($v->sifat_debit == 1) {
                    $jumlah_saldo=($v->saldo_awal_bulan + $v->total_debit) - $v->total_kredit;
                    $data_saldo=array(
                        'id_akun'   => $v->id_akun,
                        'total'  => round($jumlah_saldo, 2),
                        'bulan'   => $final,
                        'location_id'   => $location_id,
                        'total_debit'   => $v->total_debit,
                        'total_kredit'   => $v->total_kredit,
                    );
                    $ceksaldo=DB::table('tbl_saldo_months')->where('bulan', $final)->where('location_id', $location_id)->where('id_akun', $v->id_akun)->first();
                    if ($ceksaldo == null) {
                        $data_saldo['dtm_crt']=date('Y-m-d H:i:s');
                        $data_saldo['dtm_upd']=date('Y-m-d H:i:s');
                        DB::table('tbl_saldo_months')->insert($data_saldo);
                    }else{
                        $data_saldo['dtm_upd']=date('Y-m-d H:i:s');
                        DB::table('tbl_saldo_months')->where('id', $ceksaldo->id)->update($data_saldo);
                    }
                }else{
                    $jumlah_saldo=($v->saldo_awal_bulan + $v->total_kredit) - $v->total_debit;
                    $data_saldo=array(
                        'id_akun'   => $v->id_akun,
                        'total'  => round($jumlah_saldo, 2),
                        'bulan'   => $final,
                        'location_id'   => $location_id,
                        'total_debit'   => $v->total_debit,
                        'total_kredit'   => $v->total_kredit,
                    );
                    $ceksaldo=DB::table('tbl_saldo_months')->where('bulan', $final)->where('location_id', $location_id)->where('id_akun', $v->id_akun)->first();
                    if ($ceksaldo == null) {
                        $data_saldo['dtm_crt']=date('Y-m-d H:i:s');
                        $data_saldo['dtm_upd']=date('Y-m-d H:i:s');
                        DB::table('tbl_saldo_months')->insert($data_saldo);
                    }else{
                        $data_saldo['dtm_upd']=date('Y-m-d H:i:s');
                        DB::table('tbl_saldo_months')->where('id', $ceksaldo->id)->update($data_saldo);
                    }
                }
            }
            dd($data);
            $output = ['success' => 1,
                            'msg' => 'success'
                        ];
            return redirect('akuntansi/close-book')->with('status', $output);
        }
        // if ($request->input('submit')) {
        //     // $business_id=($request->session()->get('user.business_id'));
        //     // $business=DB::table('business')->where('id', $business_id)->first();
            
        //     $time = strtotime($date);
        //     $final = date('Y-m', strtotime("+1 month", $time));
        //     $cekJurnal=DB::table('tbl_saldo_akun')->where('tanggal', $final)->where('location_id', $location_id)->count();
        //     // if ($cekJurnal < 1) {
        //     $jurnal=$this->cekAllJurnal($date, null);
        //     $total_pendapatan=$total_beban=$total_kas=0;
        //     foreach ($jurnal as $key => $value) {
        //         $saldo=DB::table('tbl_saldo_akun')->where('id_akun', $value->id_akun)->where('location_id', $location_id)->where('tanggal', $date)->first();
        //         if ($value->id_parent == 3 || $value->id_parent == 7) {
        //             $jumlah_saldo=(($saldo != null ? $saldo->jumlah_saldo : 0) + $value->jumlah_debit) - $value->jumlah_kredit;
        //             if ($value->id_parent == 7) {
        //                 $total_beban+=($value->jumlah_debit - $value->jumlah_kredit);
        //             }
        //             if ($value->id_akun == 20) {
        //                 $total_kas=$jumlah_saldo;
        //             }
        //             $data_saldo=array(
        //                 'id_akun'   => $value->id_akun,
        //                 'jumlah_saldo'  => $jumlah_saldo,
        //                 'tanggal'   => $final,
        //                 'location_id'   => $location_id,
        //                 'is_updated'   => 0,
        //             );
        //             $ceksaldo=DB::table('tbl_saldo_akun')->where('tanggal', $final)->where('location_id', $location_id)->where('id_akun', $value->id_akun)->first();
        //             if ($ceksaldo == null) {
        //                 DB::table('tbl_saldo_akun')->insert($data_saldo);
        //             }else{
        //                 DB::table('tbl_saldo_akun')->where('id_saldo', $ceksaldo->id_saldo)->update($data_saldo);
        //             }
        //         }else{
        //             if ($value->id_akun != 56) {
        //                 $jumlah_saldo=(($saldo != null ? $saldo->jumlah_saldo : 0) + $value->jumlah_kredit) - $value->jumlah_debit;
        //                 if ($value->id_parent == 6) {
        //                     $total_pendapatan+=($value->jumlah_kredit - $value->jumlah_debit);
        //                 }
        //                 $data_saldo=array(
        //                     'id_akun'   => $value->id_akun,
        //                     'jumlah_saldo'  => $jumlah_saldo,
        //                     'tanggal'   => $final,
        //                     'location_id'   => $location_id,
        //                     'is_updated'   => 0,
        //                 );
        //                 $ceksaldo=DB::table('tbl_saldo_akun')->where('tanggal', $final)->where('location_id', $location_id)->where('id_akun', $value->id_akun)->first();
        //                 if ($ceksaldo == null) {
        //                     DB::table('tbl_saldo_akun')->insert($data_saldo);
        //                 }else{
        //                     DB::table('tbl_saldo_akun')->where('id_saldo', $ceksaldo->id_saldo)->update($data_saldo);
        //                 }
        //             }
        //         }
        //     }
        //     $output = ['success' => 1,
        //                     'msg' => 'success'
        //                 ];
        //     return redirect('akuntansi/close-book')->with('status', $output);
        // }
        return  view('pages.akuntansi.close_book')
                ->with(compact('bulan'));
    }
    public function rekapPc(Request $request){
        
        $user_id = request()->session()->get('user.id');
        $user = User::where('id', $user_id)->first();
        $location_id = $user->location_id;

        $date=0;
        if ($request->input('bulan')) {
            $date=$request->input('tahun').'-'.$request->input('bulan');
            $jumlah_hari=cal_days_in_month(CAL_GREGORIAN, $request->input('bulan'), $request->input('tahun'));
        }else{
            $date=date('Y-m');
            $jumlah_hari=cal_days_in_month(CAL_GREGORIAN, date('m'), date('Y'));
        }
        $detail=array();
        $bulan=json_encode(explode('-', $date));
        for ($i=1; $i <= $jumlah_hari; $i++) { 
            $tanggal=$date.'-'.(strlen($i) < 2 ? '0'.$i : $i);
            $detail[$i]['date']=$tanggal;
            $list_pc=$this->listPettyCashByDate($tanggal, $location_id);
            foreach ($list_pc as $key => $value) {
                $detail[$i]['data'][$key]=$this->getTrxPetty($value->id_trx_akun, $location_id);
            }
        }
        return  view('pages.akuntansi.rekap_pc')
                ->with(compact('detail', 'bulan'));
    }
    public function rekapTransaksi(Request $request){
        
        $user_id = request()->session()->get('user.id');
        $user = User::where('id', $user_id)->first();
        $location_id = $user->location_id;

        $date=0;
        if ($request->input('bulan')) {
            $date=$request->input('tahun').'-'.$request->input('bulan');
            $jumlah_hari=cal_days_in_month(CAL_GREGORIAN, $request->input('bulan'), $request->input('tahun'));
        }else{
            $date=date('Y-m');
            $jumlah_hari=cal_days_in_month(CAL_GREGORIAN, date('m'), date('Y'));
        }
        $detail=array();
        $bulan=json_encode(explode('-', $date));
        for ($i=1; $i <= $jumlah_hari; $i++) { 
            $tanggal=$date.'-'.(strlen($i) < 2 ? '0'.$i : $i);
            $detail[$i]['date']=$tanggal;
            $list_pc=$this->listTrx($tanggal, $location_id);
            foreach ($list_pc as $key => $value) {
                $detail[$i]['data'][$key]=$this->getTrx($value->id_trx_akun, $location_id);
            }
        }
        
        return  view('pages.akuntansi.rekap_trx')
                ->with(compact('detail', 'bulan'));
    }
    private function getTrxPetty($id, $location_id){
        $query = DB::table('tbl_trx_akuntansi_detail')
                            ->join('tbl_trx_akuntansi', 'tbl_trx_akuntansi.id_trx_akun', '=', 'tbl_trx_akuntansi_detail.id_trx_akun')
                            ->join('tbl_akun', 'tbl_akun.id_akun', '=', 'tbl_trx_akuntansi_detail.id_akun')
                            ->select('tbl_trx_akuntansi_detail.*','tbl_akun.*', 'tbl_trx_akuntansi.deskripsi')
                            ->where('tbl_trx_akuntansi_detail.id_trx_akun', $id)
                            ->where('tipe', 'DEBIT');

        if ($location_id != null) {
            $query->where('tbl_trx_akuntansi.location_id', $location_id);
        }
        $results=$query->get();
        return $results;
    }
    private function getTrx($id, $location_id){
        $query = DB::table('tbl_trx_akuntansi_detail')
                            ->join('tbl_trx_akuntansi', 'tbl_trx_akuntansi.id_trx_akun', '=', 'tbl_trx_akuntansi_detail.id_trx_akun')
                            ->join('tbl_akun', 'tbl_akun.id_akun', '=', 'tbl_trx_akuntansi_detail.id_akun')
                            ->where('tbl_trx_akuntansi_detail.id_trx_akun', $id)
                            // ->whereIn('tbl_trx_akuntansi_detail.id_akun', [91, 39])
                            ->where('tipe', 'KREDIT')
                            ->select('tbl_trx_akuntansi_detail.*','tbl_akun.*', 'tbl_trx_akuntansi.deskripsi');
        if ($location_id != null) {
            $query->where('tbl_trx_akuntansi.location_id', $location_id);
        }
        $results=$query->get();
        return $results;
    }
    private function listPettyCashByDate($date, $location_id){
        $query=DB::table('tbl_trx_akuntansi')
                            ->join('tbl_trx_akuntansi_detail', 'tbl_trx_akuntansi.id_trx_akun', '=', 'tbl_trx_akuntansi_detail.id_trx_akun')
                            ->select('tbl_trx_akuntansi.id_trx_akun')
                            ->where('tanggal', $date)
                            ->where('id_akun', 35);
                            
        if ($location_id != null) {
            $query->where('tbl_trx_akuntansi.location_id', $location_id);
        }
        $results=$query->get();
        return $results;
    }
    private function listTrx($date, $location_id){
        $query= DB::table('tbl_trx_akuntansi')
                            ->join('tbl_trx_akuntansi_detail', 'tbl_trx_akuntansi.id_trx_akun', '=', 'tbl_trx_akuntansi_detail.id_trx_akun')
                            ->where('tanggal', $date)
                            ->whereIn('id_akun', [91, 39])
                            // ->orWhere('id_akun','=', 39)
                            ->select('tbl_trx_akuntansi.id_trx_akun')
                            ->groupBy('tbl_trx_akuntansi.id_trx_akun');
        if ($location_id != null) {
            $query->where('tbl_trx_akuntansi.location_id', $location_id);
        }
        $results=$query->get();
        return $results;
    }
    public function generalLedger(Request $request)
    {
        $date1=date('Y-m-d');
        $date2=date('Y-m-d');
        if ($request->input('date')) {
            $date1=$request->input('date');
            $date2=$request->input('date2');
        }
        $user_id = request()->session()->get('user.id');
        $user = DB::table('users')->where('id', $this->user_id)->first();
        $location_id = $user->site_id;

        $account_selected=array();
        $data=array();
        if ($request->input('account')) {
            $account_selected=$request->account;
            $startTime = strtotime( $date1 );
            $endTime = strtotime( $date2 );
            $date=date('Y-m', $startTime);

            $first_date_month=$date.'-01';
            $saldo_before_start_date=0;
            foreach ($account_selected as $key => $value) {
                $akun=DB::table('tbl_akun')->where('id_akun', $value)->first();
                $query=DB::table('tbl_akun_detail')
                    ->where('id_akun', $value)
                    ->orWhere('turunan1', $value)
                    ->orWhere('turunan2', $value)
                    ->orWhere('turunan3', $value)
                    ->orWhere('turunan4', $value)
                    ->pluck('id_akun');
                if ($startTime > strtotime($first_date_month)) {
                    $min=$startTime - 86400;//kurangi sehari
                    $perubahan_saldo=DB::table('tbl_trx_akuntansi_detail as trd')
                                        ->select(DB::raw("SUM(CASE WHEN tipe = 'DEBIT' THEN jumlah ELSE 0 END) AS total_debit"), DB::raw("SUM(CASE WHEN tipe = 'KREDIT' THEN jumlah ELSE 0 END) AS total_kredit"))
                                        ->join('tbl_trx_akuntansi as tra', 'trd.id_trx_akun', 'tra.id_trx_akun')
                                        ->whereIn('trd.id_akun', $query)
                                        ->where('tra.location_id', $user->site_id)
                                        ->where('tanggal', '>=', $first_date_month)
                                        ->where('tanggal', '<=', date('Y-m-d', $min))
                                        ->whereNull('notes')
                                        ->first();
                    if ($akun->sifat_debit == 1) {
                        $saldo_before_start_date = $perubahan_saldo->total_debit - $perubahan_saldo->total_kredit;
                    }else{
                        $saldo_before_start_date = $perubahan_saldo->total_kredit - $perubahan_saldo->total_debit;
                    }
                }
                
                $bulan=explode('-', $date);
                $detail=array();
                for ( $i = $startTime; $i <= $endTime; $i = $i + 86400 ) {
                    $thisDate = date( 'Y-m-d', $i ); // 2010-05-01, 2010-05-02, etc
                    $dtSaldo=DB::table('tbl_trx_akuntansi_detail as trd')
                                        ->select('trd.*', 'trd.no as note_no', 'tra.*', 'ta.nama_akun', 'ta.no_akun')
                                        ->join('tbl_trx_akuntansi as tra', 'trd.id_trx_akun', 'tra.id_trx_akun')
                                        ->join('tbl_akun as ta', 'trd.id_akun', 'ta.id_akun')
                                        ->whereIn('trd.id_akun', $query)
                                        ->where('tra.location_id', $user->site_id)
                                        ->where('tanggal', $thisDate)
                                        ->whereNull('notes')
                                        ->get();
                    foreach ($dtSaldo as $key => $value) {
                        $suppliers=DB::table('m_suppliers')->where('id', $value->m_supplier_id)->first();
                        $value->supplier=$suppliers != null ? $suppliers->name : '';
                        $value->purchases=DB::table('purchases')->select('no')->where('id', $value->purchase_id)->first();
                        $value->purchase_assets=DB::table('purchase_assets')->select('no')->where('id', $value->purchase_asset_id)->first();
                        $value->orders=DB::table('orders')->select('order_no')->where('id', $value->order_id)->first();
                        $value->install_orders=DB::table('install_orders')->select('no')->where('id', $value->install_order_id)->first();
                        $value->giros=DB::table('giros')->select('no')->where('id', $value->giro_id)->first();
                        $value->debts=DB::table('debts')->select('no')->where('id', $value->debt_id)->first();
                        $value->ts_warehouses=DB::table('ts_warehouses')->select('no')->where('id', $value->ts_warehouse_id)->first();
                        $value->paid_customers=DB::table('paid_customers')->select('no')->where('id', $value->paid_customer_id)->first();
                        $value->paid_suppliers=DB::table('paid_suppliers')->select('no')->where('id', $value->paid_supplier_id)->first();
                        $customer=DB::table('customers')->where('id', $value->customer_id)->first();
                        $value->customer=$customer != null ? $customer->coorporate_name : '';
                        $value->code_item='';
                        if($value->m_item_id != null){
                            $item=DB::table('m_items')->where('id', $value->m_item_id)->first();
                            $value->code_item=$item->no;
                        }
                    }
                    if (count($dtSaldo) > 0) {
                        $detail[$i]['date']=$thisDate;
                        $detail[$i]['dt']=$dtSaldo;
                    }
                }
                $saldo_before=DB::table('tbl_saldo_months')
                                    ->where('bulan', $date)
                                    ->whereIn('id_akun', $query)
                                    ->where('location_id', $user->site_id)
                                    ->select(DB::raw('SUM(total) as jumlah_saldo'))
                                    ->first();
                $data[]=array(
                    'data'  => $detail,
                    'saldo_awal'    => $saldo_before,
                    'saldo_before_start_date'   => $saldo_before_start_date,
                    'akun'  => $akun,
                );
        
            }
        }
        // dd($data);
        // if (request()->ajax()) {

        //     $data=DB::table('tbl_akun')->where('level', '!=', 0)->get();
        //     $data1=array();
        //     foreach ($data as $key => $value) {
        //         $main=DB::table('tbl_akun')->where('id_akun', $value->id_main_akun)->first();
        //         $row=array();
        //         $row['id_akun']= $value->id_akun;
        //         $row['no_akun']= $value->no_akun;
        //         $row['nama_akun']= $value->nama_akun;
        //         $row['level']= $value->level;
        //         $row['debit']= $value->sifat_debit;
        //         $row['kredit']= $value->sifat_kredit;
        //         $row['main']= ($main != null ? $main->nama_akun : '');
        //         $data1[]=$row;
        //     }
        //     return DataTables::of($data1)
        //                         ->addColumn(
        //                             'action',
        //                             '<button data-href="" data-container=".account_model" class="btn btn-xs btn-primary btn-modal"><i class="glyphicon glyphicon-edit"></i> @lang("messages.edit")</button>'
        //                         )
        //                         ->make(true);
        // }
        $account=DB::table('tbl_akun')->where('level', '!=', 0)->orderBy('no_akun')->get();
        
        return view('pages.akuntansi.general_ledger')->with(compact('account', 'date1', 'date2', 'data', 'account_selected'));
    }
    public function detailGL(Request $request, $id)
    {
        $query=DB::table('tbl_akun_detail')
                    ->where('id_akun', $id)
                    ->orWhere('turunan1', $id)
                    ->orWhere('turunan2', $id)
                    ->orWhere('turunan3', $id)
                    ->orWhere('turunan4', $id)
                    ->pluck('id_akun');
        
        $user_id = request()->session()->get('user.id');
        $user = DB::table('users')->where('id', $this->user_id)->first();
        $location_id = $user->site_id;

        // $date=0;
        // if ($request->input('bulan')) {
        //     $date=$request->input('tahun').'-'.$request->input('bulan');
        // }else{
        //     $date=date('Y-m');
        // }

        $date1=date('Y-m-d');
        $date2=date('Y-m-d');
        if ($request->input('date')) {
            $date1=$request->input('date');
            $date2=$request->input('date2');
        }
        $akun=DB::table('tbl_akun')->where('id_akun', $id)->first();
        $startTime = strtotime( $date1 );
        $endTime = strtotime( $date2 );
        $date=date('Y-m', $startTime);

        $first_date_month=$date.'-01';
        $saldo_before_start_date=0;
        if ($startTime > strtotime($first_date_month)) {
            $min=$startTime - 86400;//kurangi sehari
            $perubahan_saldo=DB::table('tbl_trx_akuntansi_detail as trd')
                                ->select(DB::raw("SUM(CASE WHEN tipe = 'DEBIT' THEN jumlah ELSE 0 END) AS total_debit"), DB::raw("SUM(CASE WHEN tipe = 'KREDIT' THEN jumlah ELSE 0 END) AS total_kredit"))
                                ->join('tbl_trx_akuntansi as tra', 'trd.id_trx_akun', 'tra.id_trx_akun')
                                ->whereIn('trd.id_akun', $query)
                                ->where('tra.location_id', $user->site_id)
                                ->where('tanggal', '>=', $first_date_month)
                                ->where('tanggal', '<=', date('Y-m-d', $min))
                                ->whereNull('notes')
                                ->first();
            if ($akun->sifat_debit == 1) {
                $saldo_before_start_date = $perubahan_saldo->total_debit - $perubahan_saldo->total_kredit;
            }else{
                $saldo_before_start_date = $perubahan_saldo->total_kredit - $perubahan_saldo->total_debit;
            }
        }
        
        $bulan=explode('-', $date);
        $detail=array();
        for ( $i = $startTime; $i <= $endTime; $i = $i + 86400 ) {
            $thisDate = date( 'Y-m-d', $i ); // 2010-05-01, 2010-05-02, etc
            $dtSaldo=DB::table('tbl_trx_akuntansi_detail as trd')
                                ->select('trd.*', 'trd.no as note_no', 'tra.*', 'ta.nama_akun', 'ta.no_akun')
                                ->join('tbl_trx_akuntansi as tra', 'trd.id_trx_akun', 'tra.id_trx_akun')
                                ->join('tbl_akun as ta', 'trd.id_akun', 'ta.id_akun')
                                ->whereIn('trd.id_akun', $query)
                                ->where('tra.location_id', $user->site_id)
                                ->where('tanggal', $thisDate)
                                ->whereNull('notes')
                                ->get();
            foreach ($dtSaldo as $key => $value) {
                $suppliers=DB::table('m_suppliers')->where('id', $value->m_supplier_id)->first();
                $value->supplier=$suppliers != null ? $suppliers->name : '';
                $value->inv_trxes=DB::table('inv_trxes')->select('no')->where('inv_trxes.id', $value->inv_trx_id)->first();
                $value->inv_trx_services=DB::table('inv_trx_services')->select('no')->where('inv_trx_services.id', $value->inv_trx_service_id)->first();
                $value->purchases=DB::table('purchases')->select('no')->where('id', $value->purchase_id)->first();
                $value->purchase_assets=DB::table('purchase_assets')->select('no')->where('id', $value->purchase_asset_id)->first();
                $value->orders=DB::table('orders')->select('order_no')->where('id', $value->order_id)->first();
                $value->install_orders=DB::table('install_orders')->select('no')->where('id', $value->install_order_id)->first();
                $value->giros=DB::table('giros')->select('no')->where('id', $value->giro_id)->first();
                $value->debts=DB::table('debts')->select('no')->where('id', $value->debt_id)->first();
                $value->ts_warehouses=DB::table('ts_warehouses')->select('no')->where('id', $value->ts_warehouse_id)->first();
                $value->paid_customers=DB::table('paid_customers')->select('no')->where('id', $value->paid_customer_id)->first();
                $value->paid_suppliers=DB::table('paid_suppliers')->select('no')->where('id', $value->paid_supplier_id)->first();
                $customer=DB::table('customers')->where('id', $value->customer_id)->first();
                $value->customer=$customer != null ? $customer->coorporate_name : '';
                $value->code_item='';
                if($value->m_item_id != null){
                    $item=DB::table('m_items')->where('id', $value->m_item_id)->first();
                    $value->code_item=$item->no;
                }
            }
            if (count($dtSaldo) > 0) {
                $detail[$i]['date']=$thisDate;
                $detail[$i]['dt']=$dtSaldo;
            }
        }
        $saldo_before=DB::table('tbl_saldo_months')
                            ->where('bulan', $date)
                            ->whereIn('id_akun', $query)
                            ->where('location_id', $user->site_id)
                            ->select(DB::raw('SUM(total) as jumlah_saldo'))
                            ->first();
        // dd($saldo_before);
        // $date_before=date('Y-m', strtotime("- 1 months",  strtotime($date)));
        // // $saldo_before=DB::table('tbl_saldo_akun')
        // //                     ->where('tanggal', $date)
        // //                     ->whereIn('id_akun', $query)
        // //                     ->where('location_id', $user->site_id)
        // //                     ->select(DB::raw('SUM(jumlah_saldo) as jumlah_saldo'))
        // //                     ->first();
        // $saldo_before=DB::table('tbl_saldo_months')
        //                     ->where('bulan', $date)
        //                     ->whereIn('id_akun', $query)
        //                     ->where('location_id', $user->site_id)
        //                     ->select(DB::raw('SUM(total) as jumlah_saldo'))
        //                     ->first();
        
        // $bulan=explode('-', $date);
        // $jumlah_hari=cal_days_in_month(CAL_GREGORIAN, $bulan[1], $bulan[0]);
        // $detail=array();
        // for ($i=1; $i <= $jumlah_hari; $i++) { 
        //     $tanggal=$date.'-'.(strlen($i) < 2 ? '0'.$i : $i);
        //     $dtSaldo=DB::table('tbl_trx_akuntansi_detail as trd')
        //                         // ->select(DB::raw('SUM(jumlah) as jumlah'))
        //                         ->select('trd.*', 'trd.no as note_no', 'tra.*', 'ta.nama_akun', 'ta.no_akun')
        //                         ->join('tbl_trx_akuntansi as tra', 'trd.id_trx_akun', 'tra.id_trx_akun')
        //                         ->join('tbl_akun as ta', 'trd.id_akun', 'ta.id_akun')
        //                         ->whereIn('trd.id_akun', $query)
        //                         ->where('tra.location_id', $user->site_id)
        //                         ->where('tanggal', $tanggal)
        //                         ->whereNull('notes')
        //                         // ->groupBy('trd.tipe')
        //                         ->get();
        //     if (count($dtSaldo) > 0) {
        //         $detail[$i]['date']=$tanggal;
        //         // $detail[$i]['detail']=$countSaldo;
        //         $detail[$i]['dt']=$dtSaldo;
        //     }
        // }
        

        $data=array(
            'data'  => $detail,
            'saldo_awal'    => $saldo_before,
            'saldo_before_start_date'   => $saldo_before_start_date,
            'akun'  => $akun,
            'user'  => $user,
            'location_id'   => $location_id,
            'id'    => $id,
            'date1'  => $date1,
            'date2'  => $date2,
            'selected'  => ($request->id_trx_akun_detail ? $request->id_trx_akun_detail : 0)
        );

        // return ($data);
        // exit();
        return view('pages.akuntansi.detail_gl', $data);
    }
    public function accountPayment(){
        $data['akun_option']=array();
        $data['akun_option'][''] = 'Pilih Akun / No Akun';
        $akun_option_js=array();
        $query=DB::table('tbl_akun')->where('id_main_akun', 13)->get();
        foreach ($query as $key => $v) {
            $id_akun=$v->id_akun;
            $no_akun=$v->no_akun;
            $nama_akun=$v->nama_akun;
            
            $data['akun_option'][$id_akun]=$no_akun. ' | '.$nama_akun;
            $akun_option_js[]=array(
                'label'     => $id_akun,
                'value'     => $no_akun. ' | '.$nama_akun
            );
        }
        return $akun_option_js;
    }
    public function accountAdm(){
        $data['akun_option']=array();
        $data['akun_option'][''] = 'Pilih Akun / No Akun';
        $akun_option_js=array();
        $dataLevel0=DB::table('tbl_akun')->where('id_akun', 26)->get();
        foreach ($dataLevel0 as $key => $value) {
            $id_akun=$no_akun=$nama_akun=0;
            $dataLevel1=DB::table('tbl_akun')->where('level', 1)->where('id_main_akun', $value->id_akun)->get();
            foreach ($dataLevel1 as $k => $v) {
                $id_akun=$v->id_akun;
                $no_akun=$v->no_akun;
                $nama_akun=$v->nama_akun;
                $dataLevel2=DB::table('tbl_akun')->where('level', 2)->where('id_main_akun', $v->id_akun)->get();
                foreach ($dataLevel2 as $k2 => $v2) {
                    $id_akun=$v2->id_akun;
                    $no_akun=$v2->no_akun;
                    $nama_akun=$v2->nama_akun;
                    $dataLevel3=DB::table('tbl_akun')->where('level', 3)->where('id_main_akun', $v2->id_akun)->get();
                    foreach ($dataLevel3 as $k3 => $v3) {
                        // $c++;
                        $id_akun=$v3->id_akun;
                        $no_akun=$v3->no_akun;
                        $nama_akun=$v3->nama_akun;
                        $dataLevel4=DB::table('tbl_akun')->where('level', 4)->where('id_main_akun', $v3->id_akun)->get();
                        foreach ($dataLevel4 as $k4 => $v4) {
                            $c++;
                            $id_akun=$v4->id_akun;
                            $no_akun=$v4->no_akun;
                            $nama_akun=$v4->nama_akun;
                            $data['akun_option'][$id_akun]=$no_akun. ' | '.$nama_akun;
                            $akun_option_js[]=array(
                                'label'     => $id_akun,
                                'value'     => $no_akun. ' | '.$nama_akun
                            );
                        }
                        $data['akun_option'][$id_akun]=$no_akun. ' | '.$nama_akun;
                        $akun_option_js[]=array(
                            'label'     => $id_akun,
                            'value'     => $no_akun. ' | '.$nama_akun
                        );
                    }
                    $data['akun_option'][$id_akun]=$no_akun. ' | '.$nama_akun;
                    $akun_option_js[]=array(
                        'label'     => $id_akun,
                        'value'     => $no_akun. ' | '.$nama_akun
                    );
                }
                $data['akun_option'][$id_akun]=$no_akun. ' | '.$nama_akun;
                $akun_option_js[]=array(
                    'label'     => $id_akun,
                    'value'     => $no_akun. ' | '.$nama_akun
                );
            }
        }

        return $akun_option_js;
    }
    public function accountOpersional(){
        $data['akun_option']=array();
        $data['akun_option'][''] = 'Pilih Akun / No Akun';
        $akun_option_js=array();
        $dataLevel0=DB::table('tbl_akun')->where('id_akun', 25)->get();
        foreach ($dataLevel0 as $key => $value) {
            $id_akun=$no_akun=$nama_akun=0;
            $dataLevel1=DB::table('tbl_akun')->where('level', 1)->where('id_main_akun', $value->id_akun)->get();
            foreach ($dataLevel1 as $k => $v) {
                $id_akun=$v->id_akun;
                $no_akun=$v->no_akun;
                $nama_akun=$v->nama_akun;
                $dataLevel2=DB::table('tbl_akun')->where('level', 2)->where('id_main_akun', $v->id_akun)->get();
                foreach ($dataLevel2 as $k2 => $v2) {
                    $id_akun=$v2->id_akun;
                    $no_akun=$v2->no_akun;
                    $nama_akun=$v2->nama_akun;
                    $dataLevel3=DB::table('tbl_akun')->where('level', 3)->where('id_main_akun', $v2->id_akun)->get();
                    foreach ($dataLevel3 as $k3 => $v3) {
                        $c++;
                        $id_akun=$v3->id_akun;
                        $no_akun=$v3->no_akun;
                        $nama_akun=$v3->nama_akun;
                        $dataLevel4=DB::table('tbl_akun')->where('level', 4)->where('id_main_akun', $v3->id_akun)->get();
                        foreach ($dataLevel4 as $k4 => $v4) {
                            $c++;
                            $id_akun=$v4->id_akun;
                            $no_akun=$v4->no_akun;
                            $nama_akun=$v4->nama_akun;
                            $data['akun_option'][$id_akun]=$no_akun. ' | '.$nama_akun;
                            $akun_option_js[]=array(
                                'label'     => $id_akun,
                                'value'     => $no_akun. ' | '.$nama_akun
                            );
                        }
                        $data['akun_option'][$id_akun]=$no_akun. ' | '.$nama_akun;
                        $akun_option_js[]=array(
                            'label'     => $id_akun,
                            'value'     => $no_akun. ' | '.$nama_akun
                        );
                    }
                    $data['akun_option'][$id_akun]=$no_akun. ' | '.$nama_akun;
                    $akun_option_js[]=array(
                        'label'     => $id_akun,
                        'value'     => $no_akun. ' | '.$nama_akun
                    );
                }
                $data['akun_option'][$id_akun]=$no_akun. ' | '.$nama_akun;
                $akun_option_js[]=array(
                    'label'     => $id_akun,
                    'value'     => $no_akun. ' | '.$nama_akun
                );
            }
        }

        return $akun_option_js;
    }
    public function detailJournal($id){
        $trx_akun['data']=DB::table('tbl_trx_akuntansi')
                        ->where('tbl_trx_akuntansi_detail.id_trx_akun', $id)
                        ->join('tbl_trx_akuntansi_detail', 'tbl_trx_akuntansi.id_trx_akun', 'tbl_trx_akuntansi_detail.id_trx_akun')
                        ->join('tbl_akun', 'tbl_akun.id_akun', 'tbl_trx_akuntansi_detail.id_akun')
                        ->get();
        return $trx_akun;
    }
    public function detailInv($id){
        $inv=DB::table('inv_trxes')
                        ->where('inv_trxes.id', $id)
                        ->join('inv_trx_ds', 'inv_trxes.id', 'inv_trx_ds.inv_trx_id')
                        ->get();
        foreach($inv as $value){
            $value->m_items=DB::table('m_items')->where('id', $value->m_item_id)->first();
            $value->m_units=DB::table('m_units')->where('id', $value->m_unit_id)->first();
            $get_save_price=DB::table('m_item_prices')->where(['m_item_id' => $value->m_item_id, 'm_unit_id' => $value->m_unit_id, 'site_id'   => $this->site_id])->first();
            $value->best_price=$get_save_price->price;
        }
        $data=array(
            'data'  => $inv
        );
        return $data;
    }
    public function detailReqDev($id){
        $trx_akuntansi=DB::table('tbl_trx_akuntansi')
                        ->where('tbl_trx_akuntansi.project_req_development_id', $id)
                        ->orderBy('tanggal')
                        ->get();
        foreach ($trx_akuntansi as $key => $value) {
            $value->detail=DB::table('tbl_trx_akuntansi_detail')
                                ->where('id_trx_akun', $value->id_trx_akun)
                                ->join('tbl_akun', 'tbl_akun.id_akun', 'tbl_trx_akuntansi_detail.id_akun')
                                ->get();
        }
        $data=array(
            'data'  => $trx_akuntansi
        );
        return $data;
    }
    public function labaRugiProyek(){
        $site_id = auth()->user()['site_id'];
        try
        {
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            if ($site_id == null) {
                $client1 = new Client(['base_uri' => $this->base_api_url . 'order/option']); 
            }else{
                $client1 = new Client(['base_uri' => $this->base_api_url . 'order/option?site_id='.$site_id]); 
            } 
            $response1 = $client1->request('GET', '', ['headers' => $headers]); 
            $body1 = $response1->getBody();
            $content1 = $body1->getContents();
            $response_array1 = json_decode($content1,TRUE);

            $response1 = $content1;  
            $order_list = $response_array1['data'];
        } catch(RequestException $exception) {
            
        }

        $data=array(
            'order_list'     => $order_list
        );
        return view('pages.akuntansi.laba_rugi_proyek', $data);
    }
    public function getReportLabaRugiProyek($id){
        $get_project_dev=DB::table('project_req_developments')->where('order_id', $id)->get();
        $akun_pendapatan=DB::table('tbl_akun_detail')->whereIn('id_parent', [4])->pluck('id_akun');
        $akun_biaya=DB::table('tbl_akun_detail')->whereIn('id_parent', [5, 25, 26, 27])->pluck('id_akun');
        foreach ($get_project_dev as $key => $value) {
            $trx_akuntan=DB::table('tbl_trx_akuntansi as tra')
                                ->join('tbl_trx_akuntansi_detail as trd', 'tra.id_trx_akun', 'trd.id_trx_akun')
                                ->join('tbl_akun as ta', 'trd.id_akun', 'ta.id_akun')
                                ->select(DB::raw("SUM(CASE WHEN tipe = 'DEBIT' THEN jumlah ELSE 0 END) AS total_debit"), DB::raw("SUM(CASE WHEN tipe = 'KREDIT' THEN jumlah ELSE 0 END) AS total_kredit"), 'trd.id_akun', DB::raw('MAX(sifat_debit) as sifat_debit'), DB::raw('MAX(sifat_kredit) as sifat_kredit'), DB::raw('MAX(nama_akun) as nama_akun'))
                                // ->where('tra.project_req_development_id', $value->id)
                                ->where('tra.order_id', $id)
                                // ->whereNotIn('trd.id_akun', [149])
                                ->whereIn('trd.id_akun', [84])
                                ->groupBy('trd.id_akun')
                                ->get();
            $value->prd_detail=$trx_akuntan;
            // $trx_akuntansi=DB::table('tbl_trx_akuntansi')
            //                 ->where('tbl_trx_akuntansi.project_req_development_id', $value->id)
            //                 ->orderBy('tanggal')
            //                 ->get();
            // $value->prd=$trx_akuntansi;
            // foreach ($trx_akuntansi as $k => $v) {
            //     $v->detail=DB::table('tbl_trx_akuntansi_detail')
            //                         ->where('id_trx_akun', $v->id_trx_akun)
            //                         ->join('tbl_akun', 'tbl_akun.id_akun', 'tbl_trx_akuntansi_detail.id_akun')
            //                         ->get();
            // }
        }
        $pendapatan=DB::table('tbl_trx_akuntansi as tra')
                                ->join('tbl_trx_akuntansi_detail as trd', 'tra.id_trx_akun', 'trd.id_trx_akun')
                                ->join('tbl_akun as ta', 'trd.id_akun', 'ta.id_akun')
                                ->select(DB::raw("SUM(CASE WHEN tipe = 'DEBIT' THEN jumlah ELSE 0 END) AS total_debit"), DB::raw("SUM(CASE WHEN tipe = 'KREDIT' THEN jumlah ELSE 0 END) AS total_kredit"), 'trd.id_akun', DB::raw('MAX(sifat_debit) as sifat_debit'), DB::raw('MAX(sifat_kredit) as sifat_kredit'), DB::raw('MAX(nama_akun) as nama_akun'))
                                ->whereNull('tra.project_req_development_id')
                                ->where('tra.order_id', $id)
                                // ->whereNotIn('trd.id_akun', [149])
                                ->whereIn('trd.id_akun', $akun_pendapatan)
                                ->groupBy('trd.id_akun')
                                ->get();
        
        $data=array(
            'data'        => $get_project_dev,
            'pendapatan'        => $pendapatan,
            'html_content'  => view('pages.akuntansi.view_pl_proyek')->with(compact('get_project_dev', 'pendapatan'))->render()
        );
        return $data;
    }
    public function hppProyek(Request $request){
        $site_id = auth()->user()['site_id'];
        try
        {
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            // if ($site_id == null) {
            //     $client1 = new Client(['base_uri' => $this->base_api_url . 'order/option']); 
            // }else{
            //     $client1 = new Client(['base_uri' => $this->base_api_url . 'order/option?site_id='.$site_id]); 
            // } 
            // $response1 = $client1->request('GET', '', ['headers' => $headers]); 
            // $body1 = $response1->getBody();
            // $content1 = $body1->getContents();
            // $response_array1 = json_decode($content1,TRUE);

            // $response1 = $content1;  
            // $order_list = $response_array1['data'];
            if ($this->site_id == null) {
                $client1 = new Client(['base_uri' => $this->base_api_url . 'order/spk_option']); 
            }else{
                $client1 = new Client(['base_uri' => $this->base_api_url . 'order/spk_option?site_id='.$this->site_id]); 
            }
            $response1 = $client1->request('GET', '', ['headers' => $headers]); 
            $body1 = $response1->getBody();
            $content1 = $body1->getContents();
            $response_array1 = json_decode($content1,TRUE);

            $response1 = $content1;  
            $spk_list = $response_array1['data'];
        } catch(RequestException $exception) {
            
        }
        $order_id=$request->id;
        $data=array(
            'spk_list'     => $spk_list,
            'id'             => $order_id
        );
        return view('pages.akuntansi.hpp_proyek', $data);
    }
    public function getReportHppProyek(Request $request){
        $customer_project_id=$request->customer_project_id;
        $order_id=$request->order_id;
        $cust_project=DB::table('orders')->where('customer_project_id', $customer_project_id)->get();
        foreach ($cust_project as $a => $k) {
            if ($order_id != null) {
                if ($order_id == $k->id) {
                    $get_project_dev=DB::table('project_req_developments')->where('order_id', $k->id)->get();
                    $akun_pendapatan=DB::table('tbl_akun_detail')->whereIn('id_parent', [4])->pluck('id_akun');
                    $account_project=DB::table('account_projects')->where('order_id', $k->id)->first();
                    // $akun_biaya=DB::table('tbl_akun_detail')->whereIn('id_parent', [5, 25, 26, 27])->pluck('id_akun');
                    $akun_biaya=DB::table('tbl_akun_detail')->whereIn('id_parent', [1])->pluck('id_akun');
                    foreach ($get_project_dev as $key => $value) {
                        $trx_akuntan=DB::table('tbl_trx_akuntansi as tra')
                                            ->join('tbl_trx_akuntansi_detail as trd', 'tra.id_trx_akun', 'trd.id_trx_akun')
                                            ->join('tbl_akun as ta', 'trd.id_akun', 'ta.id_akun')
                                            ->select(DB::raw("SUM(CASE WHEN tipe = 'DEBIT' THEN jumlah ELSE 0 END) AS total_debit"), DB::raw("SUM(CASE WHEN tipe = 'KREDIT' THEN jumlah ELSE 0 END) AS total_kredit"), 'trd.id_akun', DB::raw('MAX(sifat_debit) as sifat_debit'), DB::raw('MAX(sifat_kredit) as sifat_kredit'), DB::raw('MAX(nama_akun) as nama_akun'))
                                            ->where('tra.project_req_development_id', $value->id)
                                            // ->whereNotIn('trd.id_akun', [$account_project->cost_service_id])
                                            ->whereIn('trd.id_akun', [$account_project->cost_service_id])
                                            ->groupBy('trd.id_akun')
                                            ->get();
                        $value->prd_detail=$trx_akuntan;
                        $inv_request=DB::table('inv_requests as ir')
                                            ->where('ir.project_req_development_id', $value->id)
                                            ->join('inv_trxes as it', 'ir.id', 'it.inv_request_id')
                                            ->select('ir.*', 'it.id as inv_trx_id')
                                            ->get();
                        $value->inv_request=$inv_request;
                        foreach ($value->inv_request as $v) {
                            $inv_trx_ds=DB::table('inv_trx_ds as itd')
                                            ->where('itd.inv_trx_id', $v->inv_trx_id)
                                            ->join('m_items as mi', 'itd.m_item_id', 'mi.id')
                                            ->join('m_units as mu', 'itd.m_unit_id', 'mu.id')
                                            ->select('itd.*', 'mi.name as item_name', 'mu.name as unit_name')
                                            ->get();
                            $v->detail=$inv_trx_ds;
                        }
                    }
                    $k->order_d=$get_project_dev;
                }else{
                    unset($cust_project[$a]);
                }
            }else{
                $get_project_dev=DB::table('project_req_developments')->where('order_id', $k->id)->get();
                $akun_pendapatan=DB::table('tbl_akun_detail')->whereIn('id_parent', [4])->pluck('id_akun');
                $account_project=DB::table('account_projects')->where('order_id', $k->id)->first();
                // $akun_biaya=DB::table('tbl_akun_detail')->whereIn('id_parent', [5, 25, 26, 27])->pluck('id_akun');
                $akun_biaya=DB::table('tbl_akun_detail')->whereIn('id_parent', [1])->pluck('id_akun');
                foreach ($get_project_dev as $key => $value) {
                    $trx_akuntan=DB::table('tbl_trx_akuntansi as tra')
                                        ->join('tbl_trx_akuntansi_detail as trd', 'tra.id_trx_akun', 'trd.id_trx_akun')
                                        ->join('tbl_akun as ta', 'trd.id_akun', 'ta.id_akun')
                                        ->select(DB::raw("SUM(CASE WHEN tipe = 'DEBIT' THEN jumlah ELSE 0 END) AS total_debit"), DB::raw("SUM(CASE WHEN tipe = 'KREDIT' THEN jumlah ELSE 0 END) AS total_kredit"), 'trd.id_akun', DB::raw('MAX(sifat_debit) as sifat_debit'), DB::raw('MAX(sifat_kredit) as sifat_kredit'), DB::raw('MAX(nama_akun) as nama_akun'))
                                        ->where('tra.project_req_development_id', $value->id)
                                        // ->whereNotIn('trd.id_akun', [$account_project->cost_service_id])
                                        ->whereIn('trd.id_akun', [$account_project->cost_service_id])
                                        ->groupBy('trd.id_akun')
                                        ->get();
                    $value->prd_detail=$trx_akuntan;
                    $inv_request=DB::table('inv_requests as ir')
                                        ->where('ir.project_req_development_id', $value->id)
                                        ->join('inv_trxes as it', 'ir.id', 'it.inv_request_id')
                                        ->select('ir.*', 'it.id as inv_trx_id')
                                        ->get();
                    $value->inv_request=$inv_request;
                    foreach ($value->inv_request as $v) {
                        $inv_trx_ds=DB::table('inv_trx_ds as itd')
                                        ->where('itd.inv_trx_id', $v->inv_trx_id)
                                        ->join('m_items as mi', 'itd.m_item_id', 'mi.id')
                                        ->join('m_units as mu', 'itd.m_unit_id', 'mu.id')
                                        ->select('itd.*', 'mi.name as item_name', 'mu.name as unit_name')
                                        ->get();
                        $v->detail=$inv_trx_ds;
                    }
                }
                $k->order_d=$get_project_dev;
            }
        }
        
        $data=array(
            'data'        => $cust_project,
            'html_content'  => view('pages.akuntansi.view_hpp_proyek')->with(compact('cust_project'))->render()
        );
        return $data;
    }
    private function currency($val){
        $data=explode('.', $val);
        $new=implode('', $data);
        return $new;
    }
    public function exportJournal(){
        return Excel::download(new InvoicesExport, 'journal.xlsx');
    }
    public function printCashIn($id){
        $account_payment=DB::table('tbl_akun')->where('id_main_akun', 13)->whereNotIn('id_akun', [24, 29])->pluck('id_akun')->toArray();
        $trx_akun=DB::table('tbl_trx_akuntansi_detail')->where('id_trx_akun_detail', $id)->first();
        $td=DB::table('tbl_trx_akuntansi_detail as trd')
                ->select('tra.*', 'trd.*', 'trd.no as no_label', 'ta.*')
                ->join('tbl_trx_akuntansi as tra', 'tra.id_trx_akun', 'trd.id_trx_akun')
                ->join('tbl_akun as ta', 'trd.id_akun', 'ta.id_akun')
                ->where('trd.id_trx_akun', $trx_akun->id_trx_akun)
                ->where('trd.id_trx_akun_detail', '!=', $id)
                // ->where('trd.id_trx_akun_detail', $id)
                // ->whereIn('trd.id_akun', $account_payment)
                ->where('trd.tipe', 'KREDIT')
                ->get();
        $no_label='';
        if (in_array($trx_akun->id_akun, $account_payment)) {
            $no_label='BBM';
        }else{
            $no_label='BKM';
        }
        
        $data=array(
            'tipe_label'  => $no_label,
            'no_label'  => $trx_akun->no,
            'data'  => $td,
            'cash'  => 'in'
        );
        return view('pages.akuntansi.print_bukti_kas', $data);
    }
    public function printCashOut($id){
        $account_payment=DB::table('tbl_akun')->where('id_main_akun', 13)->whereNotIn('id_akun', [24, 29])->pluck('id_akun')->toArray();
        $trx_akun=DB::table('tbl_trx_akuntansi_detail')->where('id_trx_akun_detail', $id)->first();
        $td=DB::table('tbl_trx_akuntansi_detail as trd')
                ->select('tra.*', 'trd.*', 'trd.no as no_label', 'ta.*')
                ->join('tbl_trx_akuntansi as tra', 'tra.id_trx_akun', 'trd.id_trx_akun')
                ->join('tbl_akun as ta', 'trd.id_akun', 'ta.id_akun')
                ->where('trd.id_trx_akun', $trx_akun->id_trx_akun)
                ->where('trd.id_trx_akun_detail', '!=', $id)
                // ->whereIn('trd.id_akun', $account_payment)
                ->where('trd.tipe', 'DEBIT')
                ->get();
        $no_label='';
        if (in_array($trx_akun->id_akun, $account_payment)) {
            $no_label='BBK';
        }else{
            $no_label='BKK';
        }
        
        $data=array(
            'tipe_label'  => $no_label,
            'no_label'  => $trx_akun->no,
            'data'  => $td,
            'cash'  => 'out'
        );
        return view('pages.akuntansi.print_bukti_kas', $data);
    }
    public function labaRugiAll(Request $request){
        $date1=date('Y-m-d');
        $date2=date('Y-m-d');
        if ($request->input('date')) {
            $date1=$request->input('date');
            $date2=$request->input('date2');
        }
        $site_id = auth()->user()['site_id'];
        $profit=$this->getSaldoAccount(4, $date1, $date2, $site_id, null);
        foreach ($profit as $key => $value) {
            if ($value['id_akun'] == 22) {
                foreach ($value['data'] as $k => $v) {
                    $account_project=DB::table('account_projects')->where('profit_id', $v['detail'][0]->id_akun)->first();
                    $hpp=DB::table('tbl_trx_akuntansi as tra')
                    ->join('tbl_trx_akuntansi_detail as trd', 'trd.id_trx_akun', 'tra.id_trx_akun')
                    ->select(DB::raw("COALESCE(SUM(CASE WHEN trd.tipe='DEBIT' THEN trd.jumlah ELSE 0 END), 0) as total_debit"), DB::raw("COALESCE(SUM(CASE WHEN trd.tipe='KREDIT' THEN trd.jumlah ELSE 0 END), 0) as total_kredit"))
                    ->where('trd.id_akun', 84)
                    ->where('tanggal', '>=', $date1)
                    ->where('tanggal', '<=', $date2)
                    ->where('trd.id_akun', 84)
                    ->where('location_id', $site_id)
                    ->first();
                    $profit[$key]['data'][$k]['hpp']=$hpp;
                }
            }else{
                unset($profit[$key]);
            }
        }
        
        $orders=DB::table('orders')->select('orders.*', 'customers.coorporate_name')->join('customers', 'customers.id', 'orders.customer_id')->where('orders.site_id', $site_id)->get();
        foreach ($orders as $key => $value) {
            $account_project=DB::table('account_projects')->where('order_id', $value->id)->first();
            $get_project_dev=DB::table('project_req_developments')->where('order_id', $value->id)->pluck('id');
            $pendapatan=DB::table('tbl_trx_akuntansi as tra')
                                ->join('tbl_trx_akuntansi_detail as trd', 'tra.id_trx_akun', 'trd.id_trx_akun')
                                ->join('tbl_akun as ta', 'trd.id_akun', 'ta.id_akun')
                                ->select(DB::raw("SUM(CASE WHEN tipe = 'DEBIT' THEN jumlah ELSE 0 END) AS total_debit"), DB::raw("SUM(CASE WHEN tipe = 'KREDIT' THEN jumlah ELSE 0 END) AS total_kredit"), 'trd.id_akun', DB::raw('MAX(sifat_debit) as sifat_debit'), DB::raw('MAX(sifat_kredit) as sifat_kredit'), DB::raw('MAX(nama_akun) as nama_akun'))
                                // ->whereIn('tra.project_req_development_id', $get_project_dev)
                                // ->orWhere('tra.order_id', $value->id)
                                ->whereIn('trd.id_akun', [$account_project->profit_id])
                                ->groupBy('trd.id_akun')
                                ->get();
            $value->pendapatan=$pendapatan;
            $hpp=DB::table('tbl_trx_akuntansi as tra')
                                ->join('tbl_trx_akuntansi_detail as trd', 'tra.id_trx_akun', 'trd.id_trx_akun')
                                ->join('tbl_akun as ta', 'trd.id_akun', 'ta.id_akun')
                                ->select(DB::raw("SUM(CASE WHEN tipe = 'DEBIT' THEN jumlah ELSE 0 END) AS total_debit"), DB::raw("SUM(CASE WHEN tipe = 'KREDIT' THEN jumlah ELSE 0 END) AS total_kredit"), 'trd.id_akun', DB::raw('MAX(sifat_debit) as sifat_debit'), DB::raw('MAX(sifat_kredit) as sifat_kredit'), DB::raw('MAX(nama_akun) as nama_akun'))
                                ->where('trd.id_akun', 84)
                                // ->whereIn('tra.project_req_development_id', $get_project_dev)
                                ->where('tra.order_id', $value->id)
                                ->groupBy('trd.id_akun')
                                ->get();
            $value->hpp=$hpp;
        }
        $date=date('Y-m');
        $saldo_ppn=DB::table('tbl_saldo_akun')->where('id_akun', 67)->where('tanggal', $date)->first();
        $hutang_ppn=DB::table('tbl_trx_akuntansi as tra')
                        ->join('tbl_trx_akuntansi_detail as trd', 'tra.id_trx_akun', 'trd.id_trx_akun')
                        ->join('tbl_akun as ta', 'trd.id_akun', 'ta.id_akun')
                        ->select(DB::raw("SUM(CASE WHEN tipe = 'DEBIT' THEN jumlah ELSE 0 END) AS total_debit"), DB::raw("SUM(CASE WHEN tipe = 'KREDIT' THEN jumlah ELSE 0 END) AS total_kredit"), 'trd.id_akun', DB::raw('MAX(sifat_debit) as sifat_debit'), DB::raw('MAX(sifat_kredit) as sifat_kredit'), DB::raw('MAX(nama_akun) as nama_akun'))
                        ->where('trd.id_akun', 67)
                        ->where('tra.tanggal', 'ilike', '%'.$date.'%')
                        ->groupBy('trd.id_akun')
                        ->get();
        
        $data=array(
            'date1'     => $date1,
            'date2'     => $date2,
            'data'  => $orders,
            'pendapatan' => $profit,
            'biaya_operasional' => $this->getSaldoAccount(25, $date1, $date2, $site_id, null),
            'biaya_adm' => $this->getSaldoAccount(26, $date1, $date2, $site_id, null),
            'biaya_lain' => $this->getSaldoAccount(27, $date1, $date2, $site_id, null),
            'hutang' => $hutang_ppn,
            'saldo_ppn' => $saldo_ppn
        );
        
        return view('pages.akuntansi.laba_rugi_all', $data);
    }
    public function importJurnal(Request $request) 
    {
        // validasi
        // $this->validate($request, [
        //     'file' => 'required|mimes:csv,xls,xlsx'
        // ]);
        
        // menangkap file excel
        $file = $request->file('importFile');
        
        // membuat nama file unik
        $nama_file = rand().$file->getClientOriginalName();
        // $file = $request->file('products_csv');
        // upload ke folder file_siswa di dalam folder public
        $file->move('import_excel',$nama_file);
 
        // import data
        // Excel::import(new SiswaImport, public_path('/file_siswa/'.$nama_file));
        $array = Excel::toArray(new ExcelDataImport, public_path('/import_excel/'.$nama_file));
        // $results =Excel::import(new ExcelDataImport, public_path('/import_excel/'.$nama_file));
        $temp=array();
        foreach ($array[0] as $key => $value) {
            if ($key > 0) {
                $akun=DB::table('tbl_akun')->where('no_akun', $value[2])->first();
                $isi=count($temp);
                $is_there=0;
                $index=0;
                foreach ($temp as $k => $v) {
                    if ($v['description'] == $value[5]) {
                        $is_there=1;
                        $index=$k;
                    }
                }
                
                if ($is_there == 0) {
                    $temp[$isi]['description']=$value[5];
                    $temp[$isi]['tanggal']=$value[0];
                    $temp[$isi]['detail'][0]['id_akun']=($akun != null ? $akun->id_akun : 0);
                    $temp[$isi]['detail'][0]['tipe']=($value[3] != null ? 'DEBIT' : 'KREDIT');
                    $temp[$isi]['detail'][0]['total']=($value[3] != null ? $value[3] : $value[4]);
                }else{
                    $fill=count($temp[$index]['detail']);
                    $temp[$index]['detail'][$fill]['id_akun']=($akun != null ? $akun->id_akun : 0);
                    $temp[$index]['detail'][$fill]['tipe']=($value[3] != null ? 'DEBIT' : 'KREDIT');
                    $temp[$index]['detail'][$fill]['total']=($value[3] != null ? $value[3] : $value[4]);
                }
            }
        }
        foreach ($temp as $key => $value) {
            $trx_akun=array(
                'deskripsi'     => $value['description'],
                'tanggal'     => date('Y-m-d', strtotime($value['tanggal'])),
                'location_id'     => $this->site_id,
                'user_id'     => $this->user_id,
            );
            $insert=DB::table('tbl_trx_akuntansi')->insert($trx_akun);

            if ($insert) {   
                $id_last=DB::table('tbl_trx_akuntansi')->max('id_trx_akun');
                foreach ($value['detail'] as $k => $v) {
                    $data=array(
                        'id_trx_akun'   => $id_last,
                        'id_akun'       => $v['id_akun'],
                        'jumlah'        => $v['total'],
                        'tipe'          => $v['tipe'],
                        'keterangan'    => null,
                    );
                    DB::table('tbl_trx_akuntansi_detail')->insert($data);
                }
            }
        }
        unlink(public_path('/import_excel/'.$nama_file));
        return redirect('akuntansi/jurnal');
    }
    public function accountAsset(){
        $data['akun_option']=array();
        $data['akun_option'][''] = 'Pilih Akun / No Akun';
        $akun_option_js=array();
        $query=DB::table('tbl_akun')->where('id_main_akun', 17)->get();
        foreach ($query as $key => $v) {
            $id_akun=$v->id_akun;
            $no_akun=$v->no_akun;
            $nama_akun=$v->nama_akun;
            $amort=DB::table('tbl_akun')->where('nama_akun', 'like', '%Akumulasi Penyusutan '.$nama_akun)->first();
            if ($amort != null) {
                $akun_option_js[]=array(
                    'id'     => $id_akun,
                    'no'     => $no_akun,
                    'nama'     => $nama_akun,
                    'amort_id'     => $amort->id_akun,
                    'amort_no'     => $amort->no_akun,
                    'amort_nama'     => $amort->nama_akun,
                );
            }
        }
        return $akun_option_js;
    }
    public function journalReturn($id){
        $trx_akun=DB::table('tbl_trx_akuntansi')->where('id_trx_akun', $id)->first();
        $trx_akun_detail=DB::table('tbl_trx_akuntansi_detail')->where('id_trx_akun', $id)->get();
        $data_trx=array(
            'deskripsi'     => 'Jurnal Balik - '.$trx_akun->deskripsi,
            'location_id'     => $trx_akun->location_id,
            'tanggal'       => $trx_akun->tanggal,
            'user_id'       => $trx_akun->user_id,
            'debt_id'       => $trx_akun->debt_id,
            'order_id'   => $trx_akun->order_id,
            'inv_trx_id'   => $trx_akun->inv_trx_id,
            'inv_request_id'   => $trx_akun->inv_request_id,
            'purchase_asset_id' => $trx_akun->purchase_asset_id,
            'purchase_id'   => $trx_akun->purchase_id,
            'ts_warehouse_id'   => $trx_akun->ts_warehouse_id,
            'install_order_id'   => $trx_akun->install_order_id,
            'project_req_development_id'   => $trx_akun->project_req_development_id
        );
        // print_r($data_trx);
        foreach ($trx_akun_detail as $key => $value) {
            $data=array(
                'id_trx_akun'   => 0,
                'id_akun'       => $value->id_akun,
                'jumlah'        => $value->jumlah,
                'tipe'          => ($value->tipe == 'DEBIT' ? 'KREDIT' : 'DEBIT'),
                'keterangan'    => $value->keterangan,
                'dtm_crt'       => date('Y-m-d H:i:s'),
                'dtm_upd'       => date('Y-m-d H:i:s'),
            );
            print_r($data);
        }
        // $insert=DB::table('tbl_trx_akuntansi')->insert($data_trx);

        // if ($insert) {
        //     $id_last=DB::table('tbl_trx_akuntansi')->max('id_trx_akun');
        //     for ($i=0; $i < count($id_akun); $i++) { 
        //         if ($id_akun[$i] != null) {
        //             $data=array(
        //                 'id_trx_akun'   => $id_last,
        //                 'id_akun'       => $id_akun[$i],
        //                 'jumlah'        => $this->currency($jumlah[$i]),
        //                 'tipe'          => ($tipe_akun[$i] == 0 ? "KREDIT" : "DEBIT"),
        //                 'keterangan'    => $sifat_akun[$i],
        //             );
        //             // $this->updateSaldo($id_lawan[$i], $jumlah_lawan[$i], $tipe_akun_lawan[$i]);
        //             DB::table('tbl_trx_akuntansi_detail')->insert($data);
        //         }
        //     }
        // }
    }
    public function fixPenerimaanJournal(){
        $this_month=date('Y-m');
        $date_before=date('Y-m', strtotime("- 1 months",  strtotime($this_month)));
        $id=$this->site_id;
        $datas = DB::select("
                select 
                    (COALESCE(inv_in.site_id, inv_out.site_id)) as site_id, 
                    (COALESCE(inv_in.m_item_id, inv_out.m_item_id)) as m_item_id,
                    (COALESCE(inv_in.m_unit_id, inv_out.m_unit_id)) as m_unit_id,
                    (COALESCE(inv_in.m_warehouse_id, inv_out.m_warehouse_id)) as m_warehouse_id, 
                    (COALESCE(inv_in.amount, 0)) as amount_in,
                    (COALESCE(inv_out.amount, 0) - COALESCE(inv_out.amount_ret, 0)) as amount_out,
                    (COALESCE(inv_out.amount_ret, 0)) as amount_ret,
                    ((COALESCE(inv_in.amount, 0) + COALESCE(inv_out.amount_ret, 0)) - (COALESCE(inv_out.amount, 0))) as stok,
                    inv_in.updated_at as last_update_in,
                    inv_out.updated_at as last_update_out
                from (select site_id, m_item_id, max(m_unit_id) as m_unit_id, sum(amount) as amount, itd.m_warehouse_id as m_warehouse_id, max(it.updated_at) as updated_at from inv_trxes it
                join inv_trx_ds itd on it.id = itd.inv_trx_id
                where is_entry = true and site_id = ".$id." and trx_type != 'RET_ITEM' and trx_type != 'TRF_STK' and itd.condition = 1 and itd.type_material != 'TRF_STK'
                group by site_id, m_item_id, itd.m_warehouse_id) inv_in
                full outer join (select site_id, m_item_id, max(m_unit_id) as m_unit_id, itd.m_warehouse_id, coalesce((SELECT sum(amount) as amount from inv_trxes it1
                join inv_trx_ds itd1 on it1.id = itd1.inv_trx_id
                where trx_type = 'RET_ITEM' and it1.site_id=it.site_id and itd1.m_item_id=itd.m_item_id and itd1.m_warehouse_id=itd.m_warehouse_id and itd1.condition = 1 and itd1.type_material != 'TRF_STK'), 0) AS amount_ret, sum(amount) as amount, max(it.updated_at) as updated_at from inv_trxes it
                join inv_trx_ds itd on it.id = itd.inv_trx_id
                where is_entry = false and site_id = ".$id." and trx_type != 'TRF_STK' and itd.condition = 1 and itd.type_material != 'TRF_STK'
                group by site_id, m_item_id, itd.m_warehouse_id) inv_out on inv_in.m_item_id = inv_out.m_item_id and inv_in.site_id = inv_out.site_id  and inv_in.m_warehouse_id = inv_out.m_warehouse_id
                ");
        return $datas;
    }
    public function exportNeraca(Request $request){
        return Excel::download(new NeracaExport, 'neraca.xlsx');
    }
    public function createNo($id, $type){
        $site_id=auth()->user()['site_id'];
        $akun=DB::table('tbl_akun')->where('id_akun', $id)->first();
        $cek=DB::table('tbl_akun')->where('id_akun', $id)->where('id_main_akun', 13)->count();
        $period_year = Carbon::now()->year;
        $period_month = Carbon::now()->month;
        $no=null;
        if ($cek != 0 || $id == 96) {
            if (strpos($akun->nama_akun, 'Kas') !== false || strpos($akun->nama_akun, 'Petty Cash') !== false || strpos($akun->nama_akun, 'Biaya Admin Bank') !== false) {
                $akun='KAS';
                $code=($type == 'DEBIT' ? 'BKM' : 'BKK');
            }else{
                if (strpos($akun->nama_akun, 'Danamon') !== false) {
                    $akun='DNM';
                }else if (strpos($akun->nama_akun, 'BCA') !== false) {
                    $akun='BCA';
                }
                $code=($type == 'DEBIT' ? 'BBM' : 'BBK');
            }
            // echo $akun;
            $rabcon = new RabController();
            $acc_no = $rabcon->generateTransactionNo($code, $period_year, $period_month, $site_id );
            $explode=explode('-', $acc_no);
            $no=$explode[0].'-'.$explode[1].'-'.$explode[2].'-'.$explode[3].'-'.$akun.'-'.$explode[4];
        }else{
            // $rabcon = new RabController();
            // $acc_no = $rabcon->generateTransactionNo('ACC', $period_year, $period_month, $site_id);
            $no='';
        }
        return $no;
    }
    public function updateNoTrx(){
        $cek=DB::table('tbl_akun')->where('id_main_akun', 13)->pluck('id_akun');
        $query=DB::table('tbl_trx_akuntansi_detail')->whereIn('id_akun', $cek)->orderBy('id_trx_akun_detail')->get();
        foreach ($query as $key => $value) {
            if ($value->no != null) {
                echo 'string';
            }
            // $no=$this->createNo($value->id_akun, $value->tipe);
            // $update=DB::table('tbl_trx_akuntansi_detail')->where('id_trx_akun_detail', $value->id_trx_akun_detail)->update(array('no' => $no));
        }
        // return $query;
    }
    public function kasReport(Request $request)
    {

        $user = DB::table('users')->where('id', $this->user_id)->first();
        $warehouse_id=auth()->user()['m_warehouse_id'];

        $date=date('Y-m-d');
        $date2=date('Y-m-d');
        if ($request->input('date')) {
            $date=$request->input('date');
            $date2=$request->input('date2');
        }
        $account_payment=DB::table('tbl_akun')->where('id_main_akun', 13)->pluck('id_akun');
        // $list_trx=DB::table('tbl_trx_akuntansi')
        //             ->join('tbl_trx_akuntansi_detail', 'tbl_trx_akuntansi.id_trx_akun','=','tbl_trx_akuntansi_detail.id_trx_akun')
        //             ->where('tanggal', '>=', $date)
        //             ->where('tanggal','<=', $date2)
        //             ->whereIn('tbl_trx_akuntansi_detail.id_akun', $account_payment)
        //             ->orderBy('tbl_trx_akuntansi.dtm_crt', 'DESC');
        // if ($user->site_id != null) {
        //     $list_trx->where('location_id', $user->site_id);
        // }
        // $results=$list_trx->get();
        
        $data=array();

        // foreach ($results as $key => $value) {
        //     $data[$key]=array(
        //         'id_trx_akun' => $value->id_trx_akun,
        //         'deskripsi' => $value->deskripsi,
        //         'tanggal'   => $value->tanggal
        //     );
        //     $data[$key]['detail']=$this->getReportKas($value->id_trx_akun);
        // }
        $data['akun_option']=array();
        $data['akun_option'][''] = 'Pilih Akun / No Akun';
        $akun_option_js=array();
        $dataLevel0=DB::table('tbl_akun')->where('id_akun', 26)->get();
        foreach ($dataLevel0 as $key => $value) {
            $id_akun=$no_akun=$nama_akun=0;
            $dataLevel1=DB::table('tbl_akun')->where('level', 1)->where('id_main_akun', $value->id_akun)->get();
            foreach ($dataLevel1 as $k => $v) {
                $id_akun=$v->id_akun;
                $no_akun=$v->no_akun;
                $nama_akun=$v->nama_akun;
                $dataLevel2=DB::table('tbl_akun')->where('level', 2)->where('id_main_akun', $v->id_akun)->get();
                foreach ($dataLevel2 as $k2 => $v2) {
                    $id_akun=$v2->id_akun;
                    $no_akun=$v2->no_akun;
                    $nama_akun=$v2->nama_akun;
                    $dataLevel3=DB::table('tbl_akun')->where('level', 3)->where('id_main_akun', $v2->id_akun)->get();
                    foreach ($dataLevel3 as $k3 => $v3) {
                        // $c++;
                        $id_akun=$v3->id_akun;
                        $no_akun=$v3->no_akun;
                        $nama_akun=$v3->nama_akun;
                        $dataLevel4=DB::table('tbl_akun')->where('level', 4)->where('id_main_akun', $v3->id_akun)->get();
                        foreach ($dataLevel4 as $k4 => $v4) {
                            $c++;
                            $id_akun=$v4->id_akun;
                            $no_akun=$v4->no_akun;
                            $nama_akun=$v4->nama_akun;
                            $data['akun_option'][$id_akun]=$no_akun. ' | '.$nama_akun;
                            $akun_option_js[]=array(
                                'label'     => $id_akun,
                                'value'     => $no_akun. ' | '.$nama_akun
                            );
                        }
                        $data['akun_option'][$id_akun]=$no_akun. ' | '.$nama_akun;
                        $akun_option_js[]=array(
                            'label'     => $id_akun,
                            'value'     => $no_akun. ' | '.$nama_akun
                        );
                    }
                    $data['akun_option'][$id_akun]=$no_akun. ' | '.$nama_akun;
                    $akun_option_js[]=array(
                        'label'     => $id_akun,
                        'value'     => $no_akun. ' | '.$nama_akun
                    );
                }
                $data['akun_option'][$id_akun]=$no_akun. ' | '.$nama_akun;
                $akun_option_js[]=array(
                    'label'     => $id_akun,
                    'value'     => $no_akun. ' | '.$nama_akun
                );
            }
        }
        $warehouse=DB::table('m_warehouses')->where('site_id', $this->site_id)->get();
        $cash=DB::table('cashes')->where('site_id', $this->site_id)->first();
        return view('pages.akuntansi.kas_report')->with(compact('data', 'date', 'date2', 'account_payment', 'cash', 'warehouse', 'warehouse_id'));
    }
    public function cashIn(Request $request)
    {
        $account_payment=$request->input('account_payment');
        $akun=DB::table('tbl_akun')->where('id_akun', $account_payment)->first();
        $jumlah=$request->input('total');
        // $cash=DB::table('cashes')->where('site_id', $this->site_id)->where('m_warehouse_id', $request->m_warehouse_id)->first();
        // if ($cash == null) {
        //     try
        //     {
        //         $headers = [
        //             'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
        //             'Accept'        => 'application/json',
        //         ];
        //         $client = new Client(['base_uri' => $this->base_api_url . 'inv/base/Cash']);
        //         $reqBody = [
        //             'headers' => $headers,
        //             'json' => [
        //                 'amount' => $this->currency($jumlah),
        //                 'amount_in' => $this->currency($jumlah),
        //                 'amount_out' => 0,
        //                 'm_warehouse_id'    => $request->m_warehouse_id,
        //                 'site_id'   => $this->site_id
        //             ]
        //         ]; 
        //         $response = $client->request('POST', '', $reqBody); 
        //         $body = $response->getBody();
        //         $content = $body->getContents();
        //         $response_array = json_decode($content,TRUE);
        //     } catch(RequestException $exception) {
        //     }
        // }else{
        //     try
        //     {
        //         $headers = [
        //             'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
        //             'Accept'        => 'application/json',
        //         ];
        //         $client = new Client(['base_uri' => $this->base_api_url . 'inv/base/Cash/'.$cash->id]);
        //         $reqBody = [
        //             'headers' => $headers,
        //             'json' => [
        //                 'amount' => $cash->amount + $this->currency($jumlah),
        //                 'amount_in' => $cash->amount_in + $this->currency($jumlah),
        //             ]
        //         ]; 
        //         $response = $client->request('PUT', '', $reqBody); 
        //         $body = $response->getBody();
        //         $content = $body->getContents();
        //         $response_array = json_decode($content,TRUE);
        //     } catch(RequestException $exception) {
        //     }
        // }
        //bank keluar
        if($account_payment != 29){
            $data_trx=array(
                'deskripsi'     => 'Pengeluaran Bank '.$akun->nama_akun.' untuk Kas',
                'location_id'     => $this->site_id,
                'tanggal'       => $request->date,
                'in_cash_journal'   => true,
                'm_warehouse_id'    => $request->m_warehouse_id
            );
            $insert=DB::table('tbl_trx_akuntansi')->insert($data_trx);

            if ($insert) {
                $id_last=DB::table('tbl_trx_akuntansi')->max('id_trx_akun');
                $data=array(
                    'id_trx_akun'   => $id_last,
                    'id_akun'       => 29,
                    'jumlah'        => $this->currency($jumlah),
                    'tipe'          => "DEBIT",
                    'keterangan'    => 'akun',
                );
                DB::table('tbl_trx_akuntansi_detail')->insert($data);
                // $no=$this->createNo($account_payment, "KREDIT");
                $data=array(
                    'id_trx_akun'   => $id_last,
                    'id_akun'       => $account_payment,
                    'jumlah'        => $this->currency($jumlah),
                    'tipe'          => "KREDIT",
                    'keterangan'    => 'lawan',
                    'no'            => $request->bbk
                );
                DB::table('tbl_trx_akuntansi_detail')->insert($data);
            }
        }
        
        //kas masuk
        $data_trx=array(
            'deskripsi'     => 'Pengisian Kas',
            'location_id'     => $this->site_id,
            'tanggal'       => $request->date,
            'in_cash_journal'   => true,
            'm_warehouse_id'    => $request->m_warehouse_id
        );
        $insert=DB::table('tbl_trx_akuntansi')->insert($data_trx);

        if ($insert) {
            $id_last=DB::table('tbl_trx_akuntansi')->max('id_trx_akun');
            // $no=$this->createNo(24, "DEBIT");
            $data=array(
                'id_trx_akun'   => $id_last,
                'id_akun'       => ($request->m_warehouse_id == 2 ? 24 : 101),
                'jumlah'        => $this->currency($jumlah),
                'tipe'          => "DEBIT",
                'keterangan'    => 'akun',
                'no'            => $request->bkm
            );
            DB::table('tbl_trx_akuntansi_detail')->insert($data);
            $no=$this->createNo($account_payment, "KREDIT");
            $data=array(
                'id_trx_akun'   => $id_last,
                'id_akun'       => 29,
                'jumlah'        => $this->currency($jumlah),
                'tipe'          => "KREDIT",
                'keterangan'    => 'lawan',
                // 'no'            => $no
            );
            DB::table('tbl_trx_akuntansi_detail')->insert($data);
        }
        $this->countSaldoCash($this->site_id, $request->m_warehouse_id, 'in', $this->currency($jumlah));
        return redirect('akuntansi/kas_report');
    }
    public function cashOut(Request $request)
    {
        $account_adm=$request->input('account_adm');
        $deskripsi=$request->input('deskripsi');
        $jumlah=$request->input('total');
        
        $data_trx=array(
                        'deskripsi'     => $deskripsi,
                        'location_id'     => $this->site_id,
                        'tanggal'       => date('Y-m-d'),
                        'in_cash_journal'   => true,
                        'm_warehouse_id'    => $request->m_warehouse_id
                    );
        $insert=DB::table('tbl_trx_akuntansi')->insert($data_trx);

        if ($insert) {
            $id_last=DB::table('tbl_trx_akuntansi')->max('id_trx_akun');
            // $no=$this->createNo(29, "DEBIT");
            $data=array(
                'id_trx_akun'   => $id_last,
                'id_akun'       => $account_adm,
                'jumlah'        => $this->currency($jumlah),
                'tipe'          => "DEBIT",
                'keterangan'    => 'akun',
            );
            DB::table('tbl_trx_akuntansi_detail')->insert($data);
            // $no=$this->createNo(24, "KREDIT");
            $data=array(
                'id_trx_akun'   => $id_last,
                'id_akun'       => ($request->m_warehouse_id == 2 ? 24 : 101),
                'jumlah'        => $this->currency($jumlah),
                'tipe'          => "KREDIT",
                'keterangan'    => 'lawan',
                'no'            => $request->bkk
            );
            DB::table('tbl_trx_akuntansi_detail')->insert($data);
        }
        $this->countSaldoCash($this->site_id, $request->m_warehouse_id, 'out', $this->currency($jumlah));

        return redirect('akuntansi/kas_report');
    }
    public function cashJson(Request $request)
    {
        $warehouse_id=$request->warehouse_id;
        $akun_id=$request->warehouse_id == 2 ? 24 : 101;
        if($warehouse_id != null){
            $query=DB::table('tbl_trx_akuntansi as tra')
                        ->select('tra.*', 'trd.tipe', 'trd.id_akun', 'trd.jumlah')
                        ->join('tbl_trx_akuntansi_detail as trd', 'tra.id_trx_akun', 'trd.id_trx_akun')
                        // ->where('in_cash_journal', true)
                        ->where('location_id', $this->site_id)
                        ->where('id_akun', $akun_id)
                        ->orderBy('tanggal', 'desc')
                        ->get();
        }else{
            $query=array();
        }
        
        return DataTables::of($query)
                    ->make(true);
    }
    private function getReportKas($id)
    {
        $account_payment=DB::table('tbl_akun')->where('id_main_akun', 13)->pluck('id_akun');
        $data=DB::table('tbl_trx_akuntansi')
                ->join('tbl_trx_akuntansi_detail', 'tbl_trx_akuntansi.id_trx_akun','=','tbl_trx_akuntansi_detail.id_trx_akun')
                ->join('tbl_akun', 'tbl_akun.id_akun','=','tbl_trx_akuntansi_detail.id_akun')
                ->where('tbl_trx_akuntansi.id_trx_akun', $id)
                ->whereIn('tbl_trx_akuntansi_detail.id_akun', $account_payment)
                ->orderBy('tbl_trx_akuntansi_detail.keterangan')
                ->get();
        return $data;
    }
    public function fixTagihan(){
        $order=DB::table('orders')->get();
        foreach ($order as $key => $value) {
            $query=DB::select("select tbl_trx_akuntansi_detail.*, tbl_trx_akuntansi.deskripsi from tbl_trx_akuntansi join tbl_trx_akuntansi_detail on tbl_trx_akuntansi.id_trx_akun=tbl_trx_akuntansi_detail.id_trx_akun where tbl_trx_akuntansi.deskripsi like '%Pembuatan Tagihan%' and order_id =".$value->id."");
            print_r($query);
            echo "<br>";
        }
    }
    public function showJsonGLDetail(Request $request){
        $id=$request->id;
        $date1=$request->date1;
        $date2=$request->date2;
        $tipe=$request->tipe;
        $query=DB::table('tbl_akun_detail')
                    ->where('id_akun', $id)
                    ->orWhere('turunan1', $id)
                    ->orWhere('turunan2', $id)
                    ->orWhere('turunan3', $id)
                    ->orWhere('turunan4', $id)
                    ->pluck('id_akun');
        
        $user_id = request()->session()->get('user.id');
        $user = DB::table('users')->where('id', $this->user_id)->first();
        $location_id = $user->site_id;

        $akun=DB::table('tbl_akun')->where('id_akun', $id)->first();
        $startTime = strtotime( $date1 );
        $endTime = strtotime( $date2 );
        $date=date('Y-m', $startTime);

        $first_date_month=$date.'-01';
        $saldo_before_start_date=0;
        if ($startTime > strtotime($first_date_month)) {
            $min=$startTime - 86400;//kurangi sehari
            $perubahan_saldo=DB::table('tbl_trx_akuntansi_detail as trd')
                                ->select(DB::raw("SUM(CASE WHEN tipe = 'DEBIT' THEN jumlah ELSE 0 END) AS total_debit"), DB::raw("SUM(CASE WHEN tipe = 'KREDIT' THEN jumlah ELSE 0 END) AS total_kredit"))
                                ->join('tbl_trx_akuntansi as tra', 'trd.id_trx_akun', 'tra.id_trx_akun')
                                ->whereIn('trd.id_akun', $query)
                                ->where('tra.location_id', $user->site_id)
                                ->where('tanggal', '>=', $first_date_month)
                                ->where('tanggal', '<=', date('Y-m-d', $min))
                                ->whereNull('notes')
                                ->first();
            if ($akun->sifat_debit == 1) {
                $saldo_before_start_date = $perubahan_saldo->total_debit - $perubahan_saldo->total_kredit;
            }else{
                $saldo_before_start_date = $perubahan_saldo->total_kredit - $perubahan_saldo->total_debit;
            }
        }
        
        $bulan=explode('-', $date);
        $detail=array();
        for ( $i = $startTime; $i <= $endTime; $i = $i + 86400 ) {
            $thisDate = date( 'Y-m-d', $i ); // 2010-05-01, 2010-05-02, etc
            $dtSaldo=DB::table('tbl_trx_akuntansi_detail as trd')
                                ->select('trd.*', 'trd.no as note_no', 'tra.*', 'ta.nama_akun', 'ta.no_akun')
                                ->join('tbl_trx_akuntansi as tra', 'trd.id_trx_akun', 'tra.id_trx_akun')
                                ->join('tbl_akun as ta', 'trd.id_akun', 'ta.id_akun')
                                ->whereIn('trd.id_akun', $query)
                                ->where('tra.location_id', $user->site_id)
                                ->where('tanggal', $thisDate)
                                ->where('tipe', $tipe)
                                ->whereNull('notes')
                                ->get();
            foreach ($dtSaldo as $key => $value) {
                $suppliers=DB::table('m_suppliers')->where('id', $value->m_supplier_id)->first();
                $value->supplier=$suppliers != null ? $suppliers->name : '';
                $value->inv_trxes=DB::table('inv_trxes')->select('no')->where('inv_trxes.id', $value->inv_trx_id)->first();
                $value->inv_trx_services=DB::table('inv_trx_services')->select('no')->where('inv_trx_services.id', $value->inv_trx_service_id)->first();
                $value->purchases=DB::table('purchases')->select('no')->where('id', $value->purchase_id)->first();
                $value->purchase_assets=DB::table('purchase_assets')->select('no')->where('id', $value->purchase_asset_id)->first();
                $value->orders=DB::table('orders')->select('order_no')->where('id', $value->order_id)->first();
                $value->install_orders=DB::table('install_orders')->select('no')->where('id', $value->install_order_id)->first();
                $value->giros=DB::table('giros')->select('no')->where('id', $value->giro_id)->first();
                $value->debts=DB::table('debts')->select('no')->where('id', $value->debt_id)->first();
                $value->ts_warehouses=DB::table('ts_warehouses')->select('no')->where('id', $value->ts_warehouse_id)->first();
                $value->paid_customers=DB::table('paid_customers')->select('no')->where('id', $value->paid_customer_id)->first();
                $value->paid_suppliers=DB::table('paid_suppliers')->select('no')->where('id', $value->paid_supplier_id)->first();
                $customer=DB::table('customers')->where('id', $value->customer_id)->first();
                $value->customer=$customer != null ? $customer->coorporate_name : '';
                $value->code_item='';
                if($value->m_item_id != null){
                    $item=DB::table('m_items')->where('id', $value->m_item_id)->first();
                    $value->code_item=$item->no;
                }
            }
            if (count($dtSaldo) > 0) {
                $detail[$i]['date']=$thisDate;
                $detail[$i]['dt']=$dtSaldo;
            }
        }
        $saldo_before=DB::table('tbl_saldo_months')
                            ->where('bulan', $date)
                            ->whereIn('id_akun', $query)
                            ->where('location_id', $user->site_id)
                            ->select(DB::raw('SUM(total) as jumlah_saldo'))
                            ->first();

        $data1=array(
            'data'  => $detail,
            'saldo_awal'    => $saldo_before,
            'saldo_before_start_date'   => $saldo_before_start_date,
            'akun'  => $akun,
            'user'  => $user,
            'location_id'   => $location_id,
            'id'    => $id,
            'date1'  => $date1,
            'date2'  => $date2,
        );

        $data=array(
            // 'data'        => $cust_project,
            'html_content'  => view('pages.akuntansi.view_gl_detail', $data1)->render()
        );
        return $data;
    }
    public function neracaSaldo(Request $request)
    {
        $user_id = request()->session()->get('user.id');
        $user = DB::table('users')->where('id', $this->user_id)->first();

        $date=0;
        $date1=date('Y-m-d');
        $date2=date('Y-m-d');
        if ($request->input('date')) {
            $date1=$request->input('date');
            $date2=$request->input('date2');
        }
        
        $location_id=0;
        if ($request->input('bulan')) {
            // $date=$request->input('tahun').'-'.$request->input('bulan');
            if ($request->input('location_id')) {
                $location_id=$request->input('location_id');
            }else{
                $location_id=$this->site_id;
            }
        }else{
            // $date=date('Y-m');
            $location_id=$this->site_id;
        }
        $bulan=json_encode(explode('-', $date));
        $except=[152, 153, 154, 43, 44, 45, 46, 47, 48];
        $data_akun=DB::table('tbl_akun')->join('tbl_akun_detail', 'tbl_akun_detail.id_akun', 'tbl_akun.id_akun')->where('level', 1)->orderBy('no_akun')->select('tbl_akun.id_akun', 'no_akun', 'nama_akun', 'sifat_debit', 'sifat_kredit', 'turunan1', 'turunan2', 'turunan3', 'turunan4')->get();
        foreach ($data_akun as $k => $v) {
            $v->detail=$this->countSaldoAccountByParent($v->id_akun, $date1, $date2, $location_id);
            $v->child=DB::table('tbl_akun')->where('level', 2)->join('tbl_akun_detail', 'tbl_akun_detail.id_akun', 'tbl_akun.id_akun')->where('id_main_akun', $v->id_akun)->select('tbl_akun.id_akun', 'no_akun', 'nama_akun', 'sifat_debit', 'sifat_kredit', 'turunan1', 'turunan2', 'turunan3', 'turunan4')->get();            
            foreach ($v->child as $k2 => $v2) {
                $child=DB::table('tbl_akun')->where('level', 3)->join('tbl_akun_detail', 'tbl_akun_detail.id_akun', 'tbl_akun.id_akun')->where('id_main_akun', $v2->id_akun)->select('tbl_akun.id_akun', 'no_akun', 'nama_akun', 'sifat_debit', 'sifat_kredit', 'turunan1', 'turunan2', 'turunan3', 'turunan4')->get();
                if(count($child) < 1 || in_array($v2->id_akun, [152, 153, 169, 154, 43, 44, 45, 46, 47, 48])){
                    $v2->detail=$this->countSaldoAccountByParent($v2->id_akun, $date1, $date2, $location_id);
                }else{
                    $v2->detail=array();
                }
                if(!in_array($v2->turunan2, $except)){
                    $v2->child=$child;
                    foreach ($v2->child as $k3 => $v3) {
                        $child=DB::table('tbl_akun')->where('level', 4)->join('tbl_akun_detail', 'tbl_akun_detail.id_akun', 'tbl_akun.id_akun')->where('id_main_akun', $v3->id_akun)->select('tbl_akun.id_akun', 'no_akun', 'nama_akun', 'sifat_debit', 'sifat_kredit', 'turunan1', 'turunan2', 'turunan3', 'turunan4')->get();
                        if(count($child) < 1 || in_array($v3->id_akun, [50, 51, 52, 53, 54, 179])){
                            $v3->detail=$this->countSaldoAccountByParent($v3->id_akun, $date1, $date2, $location_id);
                        }else{
                            $v3->detail=array();
                        }
                        
                        if($v3->turunan2 != 152 && $v3->turunan2 != 49){
                            $v3->child=$child;
                            foreach ($v3->child as $k4 => $v4) {
                                $v4->detail=$this->countSaldoAccountByParent($v4->id_akun, $date1, $date2, $location_id);
                            }
                        }else{
                            $v3->child=array();
                        }
                    }
                }else{
                    $v2->child=array();
                }
            }
        }
        // return $data_akun;
        $data=array(
            'date1'     => $date1,
            'date2'     => $date2,
        );
        return  view('pages.akuntansi.neraca_saldo_new')->with(compact('data', 'data_akun', 'bulan', 'user', 'location_id'));
    }
    private function getGLAccount($id, $date){
        $query=DB::table('tbl_akun_detail')
                    ->where('id_akun', $id)
                    ->orWhere('turunan1', $id)
                    ->orWhere('turunan2', $id)
                    ->orWhere('turunan3', $id)
                    ->orWhere('turunan4', $id)
                    ->pluck('id_akun');
        
        $user_id = request()->session()->get('user.id');
        $user = DB::table('users')->where('id', $this->user_id)->first();
        $location_id = $user->site_id;

        $date_before=date('Y-m', strtotime("- 1 months",  strtotime($date)));
        $saldo_before=DB::table('tbl_saldo_akun')
                            ->where('tanggal', $date)
                            ->whereIn('id_akun', $query)
                            ->where('location_id', $user->site_id)
                            ->select(DB::raw('SUM(jumlah_saldo) as jumlah_saldo'))
                            ->first();
        
        $bulan=explode('-', $date);
        $jumlah_hari=cal_days_in_month(CAL_GREGORIAN, $bulan[1], $bulan[0]);
        $detail=array();
        for ($i=1; $i <= $jumlah_hari; $i++) { 
            $tanggal=$date.'-'.(strlen($i) < 2 ? '0'.$i : $i);
            $dtSaldo=DB::table('tbl_trx_akuntansi_detail as trd')
                                // ->select(DB::raw('SUM(jumlah) as jumlah'))
                                ->select('trd.*', 'trd.no as note_no', 'tra.*', 'ta.nama_akun', 'ta.no_akun')
                                ->join('tbl_trx_akuntansi as tra', 'trd.id_trx_akun', 'tra.id_trx_akun')
                                ->join('tbl_akun as ta', 'trd.id_akun', 'ta.id_akun')
                                ->whereIn('trd.id_akun', $query)
                                ->where('tra.location_id', $user->site_id)
                                ->where('tanggal', $tanggal)
                                ->whereNull('notes')
                                // ->groupBy('trd.tipe')
                                ->get();
            if (count($dtSaldo) > 0) {
                $detail[$i]['date']=$tanggal;
                $detail[$i]['dt']=$dtSaldo;
            }
        }
        $data=array(
            'saldo_before'  => $saldo_before,
            'detail'        => $detail,
        );
        return $data;
    }
    public function allGLDetail(Request $request)
    {
        $date=0;
        if ($request->input('bulan')) {
            $date=$request->input('tahun').'-'.$request->input('bulan');
        }else{
            $date=date('Y-m');
        }
        $data_akun=DB::table('tbl_akun')->where('level', 1)->orderBy('no_akun')->get();
        foreach ($data_akun as $k => $v) {
            $v->detail=$this->getGLAccount($v->id_akun, $date);
            $v->child=DB::table('tbl_akun')->where('level', 2)->where('id_main_akun', $v->id_akun)->get();            
            foreach ($v->child as $k2 => $v2) {
                $v2->detail=$this->getGLAccount($v->id_akun, $date);
                $v2->child=DB::table('tbl_akun')->where('level', 3)->where('id_main_akun', $v2->id_akun)->get();
                foreach ($v2->child as $k3 => $v3) {
                    $v3->detail=$this->getGLAccount($v3->id_akun, $date);
                    $v3->child=DB::table('tbl_akun')->where('level', 4)->where('id_main_akun', $v3->id_akun)->get();
                    foreach ($v3->child as $k4 => $v4) {
                        $v4->detail=$this->getGLAccount($v4->id_akun, $date);
                    }
                }
            }
        }
        $user_id = request()->session()->get('user.id');
        $user = DB::table('users')->where('id', $this->user_id)->first();
        $location_id = $user->site_id;

        $data=array(
            'data'    => $data_akun,
            'location_id'   => $location_id,
            'bulan' => json_encode(explode('-', $date)),
            'date'  => $date,
        );

        return view('pages.akuntansi.all_gl_detail', $data);
    }
    public function countSaldoAccount($date){
        $user_id = request()->session()->get('user.id');
        $user = DB::table('users')->where('id', $this->user_id)->first();
        $location_id = $user->site_id;
        
        $saldo_this_month=DB::table('tbl_akun as ta')
                    ->select(DB::raw("COALESCE((SELECT SUM(CASE WHEN trd.tipe='DEBIT' THEN trd.jumlah ELSE 0 END) FROM tbl_trx_akuntansi_detail trd join tbl_trx_akuntansi as tra on trd.id_trx_akun=tra.id_trx_akun where tra.tanggal::text like '".$date."%' and trd.id_akun=ta.id_akun and tra.location_id='".$location_id."'), 0) as total_debit"), DB::raw("COALESCE((SELECT SUM(CASE WHEN trd.tipe='KREDIT' THEN trd.jumlah ELSE 0 END) FROM tbl_trx_akuntansi_detail trd join tbl_trx_akuntansi as tra on trd.id_trx_akun=tra.id_trx_akun where tra.tanggal::text like '".$date."%' and trd.id_akun=ta.id_akun and tra.location_id='".$location_id."'), 0) as total_kredit"), 'ta.*')
                    ->where('level', '!=', 0)
                    ->get();
        foreach ($saldo_this_month as $key => $value) {
            $saldo_awal_bulan=DB::table('tbl_saldo_months')->where('id_akun', $value->id_akun)->where('bulan', $date)->where('location_id', $location_id)->first();
            $value->saldo_awal_bulan=$saldo_awal_bulan;
        }
        $data=array(
            'detail_month'  => $saldo_this_month
        );
        return $data;
    }
    public function countSaldoAccountByParent($id, $date1, $date2, $location_id){
        // $user_id = request()->session()->get('user.id');
        // $user = DB::table('users')->where('id', $this->user_id)->first();
        // $location_id = $user->site_id;
        $startTime = strtotime( $date1 );
        $endTime = strtotime( $date2 );
        $date=date('Y-m', $startTime);

        $first_date_month=$date.'-01';
        $saldo_before_start_date=0;
        if ($startTime > strtotime($first_date_month)) {
            $min=$startTime - 86400;
        }
        
        $query=DB::table('tbl_akun_detail')
                    ->where('id_akun', $id)
                    ->orWhere('turunan1', $id)
                    ->orWhere('turunan2', $id)
                    ->orWhere('turunan3', $id)
                    ->orWhere('turunan4', $id)
                    ->pluck('id_akun');
        $perubahan_before=array();
        if ($startTime > strtotime($first_date_month)) {
            $perubahan_before=DB::table('tbl_trx_akuntansi as tra')
                        ->join('tbl_trx_akuntansi_detail as trd', 'trd.id_trx_akun', 'tra.id_trx_akun')
                        ->select(DB::raw("COALESCE(SUM(CASE WHEN trd.tipe='DEBIT' THEN trd.jumlah ELSE 0 END), 0) as total_debit"), DB::raw("COALESCE(SUM(CASE WHEN trd.tipe='KREDIT' THEN trd.jumlah ELSE 0 END), 0) as total_kredit"))
                        ->where('tanggal', '>=', $first_date_month)
                        ->where('tanggal', '<=', date('Y-m-d', $min))
                        ->whereIn('trd.id_akun', $query)
                        ->where('location_id', $location_id)
                        ->first();
        }else{
            $perubahan_before=(object)array('total_debit' => 0, 'total_kredit' => 0, 'jumlah_saldo' => 0);
        }
        $saldo_this_month=DB::table('tbl_trx_akuntansi as tra')
                    ->join('tbl_trx_akuntansi_detail as trd', 'trd.id_trx_akun', 'tra.id_trx_akun')
                    ->select(DB::raw("COALESCE(SUM(CASE WHEN trd.tipe='DEBIT' THEN trd.jumlah ELSE 0 END), 0) as total_debit"), DB::raw("COALESCE(SUM(CASE WHEN trd.tipe='KREDIT' THEN trd.jumlah ELSE 0 END), 0) as total_kredit"))
                    // ->where('tanggal', 'like', $date.'%')
                    ->where('tanggal', '>=', $date1)
                    ->where('tanggal', '<=', $date2)
                    ->whereIn('trd.id_akun', $query)
                    ->where('location_id', $location_id)
                    ->first();
        $saldo_awal_bulan=DB::table('tbl_saldo_months')
                                ->where('bulan', $date)
                                ->whereIn('id_akun', $query)
                                ->where('location_id', $location_id)
                                ->select(DB::raw('SUM(total) as jumlah_saldo'), DB::raw('SUM(total_debit) as total_debit'), DB::raw('SUM(total_kredit) as total_kredit'))
                                ->first();
        $data=array(
            'detail_month'  => $saldo_this_month,
            'saldo_month'   => $saldo_awal_bulan,
            'perubahan_before'   => $perubahan_before,
            // 'id'    => $query
        );
        return $data;
    }
    public function cutSaldo(Request $request){
        $user_id = $this->user_id;
        $user = DB::table('users')->where('id', $user_id)->first();
        
        $location_id = $user->site_id;

        $date=0;
        if ($request->input('bulan')) {
            $date=$request->input('tahun').'-'.$request->input('bulan');
        }else{
            $date=date('Y-m');
        }

        $bulan=explode('-', $date);
        if ($request->input('submit')) {

            $location_id=$request->input('location_id') ? $request->input('location_id') : $user->site_id;
            $time = strtotime($date);
            $final = date('Y-m', strtotime("+1 month", $time));

            $jumlah_hari=cal_days_in_month(CAL_GREGORIAN, $bulan[1], $bulan[0]);

            $data_account=$this->countSaldoAccount($date);
            
            // dd($data['detail_month']);
            foreach ($data_account['detail_month'] as $v){
                if ($v->sifat_debit == 1) {
                    $jumlah_saldo=(($v->saldo_awal_bulan != null ? $v->saldo_awal_bulan->total_debit : 0) + $v->total_debit) - (($v->saldo_awal_bulan != null ? $v->saldo_awal_bulan->total_kredit : 0) + $v->total_kredit);
                    $data_saldo=array(
                        'id_akun'   => $v->id_akun,
                        'total'  => round($jumlah_saldo, 2),
                        'bulan'   => $final,
                        'location_id'   => $location_id,
                        'total_debit'   => (($v->saldo_awal_bulan != null ? $v->saldo_awal_bulan->total_debit : 0) + $v->total_debit),
                        'total_kredit'   => (($v->saldo_awal_bulan != null ? $v->saldo_awal_bulan->total_kredit : 0) + $v->total_kredit),
                    );
                    $ceksaldo=DB::table('tbl_saldo_months')->where('bulan', $final)->where('location_id', $location_id)->where('id_akun', $v->id_akun)->first();
                    if ($ceksaldo == null) {
                        $data_saldo['dtm_crt']=date('Y-m-d H:i:s');
                        $data_saldo['dtm_upd']=date('Y-m-d H:i:s');
                        DB::table('tbl_saldo_months')->insert($data_saldo);
                    }else{
                        $data_saldo['dtm_upd']=date('Y-m-d H:i:s');
                        DB::table('tbl_saldo_months')->where('id', $ceksaldo->id)->update($data_saldo);
                    }
                }else{
                    $jumlah_saldo=(($v->saldo_awal_bulan != null ? $v->saldo_awal_bulan->total_kredit : 0) + $v->total_kredit) - (($v->saldo_awal_bulan != null ? $v->saldo_awal_bulan->total_debit : 0) + $v->total_debit);
                    $data_saldo=array(
                        'id_akun'   => $v->id_akun,
                        'total'  => round($jumlah_saldo, 2),
                        'bulan'   => $final,
                        'location_id'   => $location_id,
                        'total_debit'   => (($v->saldo_awal_bulan != null ? $v->saldo_awal_bulan->total_debit : 0) + $v->total_debit),
                        'total_kredit'   => (($v->saldo_awal_bulan != null ? $v->saldo_awal_bulan->total_kredit : 0) + $v->total_kredit),
                    );
                    $ceksaldo=DB::table('tbl_saldo_months')->where('bulan', $final)->where('location_id', $location_id)->where('id_akun', $v->id_akun)->first();
                    if ($ceksaldo == null) {
                        $data_saldo['dtm_crt']=date('Y-m-d H:i:s');
                        $data_saldo['dtm_upd']=date('Y-m-d H:i:s');
                        DB::table('tbl_saldo_months')->insert($data_saldo);
                    }else{
                        $data_saldo['dtm_upd']=date('Y-m-d H:i:s');
                        DB::table('tbl_saldo_months')->where('id', $ceksaldo->id)->update($data_saldo);
                    }
                }
            }
            $output = ['success' => 1,
                            'msg' => 'success'
                        ];
            return redirect('akuntansi/cut-saldo')->with('status', $output);
        }
        
        return  view('pages.akuntansi.cut-saldo')
                ->with(compact('bulan'));
    }
    // public function exportNeracaSaldo(){
    //     return Excel::download(new NeracaSaldoExport, 'neraca_saldo.xlsx');
    // }
    public function exportNeracaSaldo(Request $request){
        $user_id = request()->session()->get('user.id');
        $user = DB::table('users')->where('id', $this->user_id)->first();

        $date=0;
        $date1=date('Y-m-d');
        $date2=date('Y-m-d');
        if ($request->input('date')) {
            $date1=$request->get('date');
            $date2=$request->get('date2');
        }
        
        $location_id=0;
        if ($request->input('bulan')) {
            // $date=$request->input('tahun').'-'.$request->input('bulan');
            if ($request->input('location_id')) {
                $location_id=$request->input('location_id');
            }else{
                $location_id=$this->site_id;
            }
        }else{
            // $date=date('Y-m');
            $location_id=$this->site_id;
        }
        $bulan=json_encode(explode('-', $date));
        $except=[152, 153, 154, 43, 44, 45, 46, 47, 48];
        $data_akun=DB::table('tbl_akun')->join('tbl_akun_detail', 'tbl_akun_detail.id_akun', 'tbl_akun.id_akun')->where('level', 1)->orderBy('no_akun')->select('tbl_akun.id_akun', 'no_akun', 'nama_akun', 'sifat_debit', 'sifat_kredit', 'turunan1', 'turunan2', 'turunan3', 'turunan4')->get();
        foreach ($data_akun as $k => $v) {
            $v->detail=$this->countSaldoAccountByParent($v->id_akun, $date1, $date2, $location_id);
            $v->child=DB::table('tbl_akun')->where('level', 2)->join('tbl_akun_detail', 'tbl_akun_detail.id_akun', 'tbl_akun.id_akun')->where('id_main_akun', $v->id_akun)->select('tbl_akun.id_akun', 'no_akun', 'nama_akun', 'sifat_debit', 'sifat_kredit', 'turunan1', 'turunan2', 'turunan3', 'turunan4')->get();            
            foreach ($v->child as $k2 => $v2) {
                $child=DB::table('tbl_akun')->where('level', 3)->join('tbl_akun_detail', 'tbl_akun_detail.id_akun', 'tbl_akun.id_akun')->where('id_main_akun', $v2->id_akun)->select('tbl_akun.id_akun', 'no_akun', 'nama_akun', 'sifat_debit', 'sifat_kredit', 'turunan1', 'turunan2', 'turunan3', 'turunan4')->get();
                if(count($child) < 1 || in_array($v2->id_akun, [152, 153, 169, 154, 43, 44, 45, 46, 47, 48])){
                    $v2->detail=$this->countSaldoAccountByParent($v2->id_akun, $date1, $date2, $location_id);
                }else{
                    $v2->detail=array();
                }
                if(!in_array($v2->turunan2, $except)){
                    $v2->child=$child;
                    foreach ($v2->child as $k3 => $v3) {
                        $child=DB::table('tbl_akun')->where('level', 4)->join('tbl_akun_detail', 'tbl_akun_detail.id_akun', 'tbl_akun.id_akun')->where('id_main_akun', $v3->id_akun)->select('tbl_akun.id_akun', 'no_akun', 'nama_akun', 'sifat_debit', 'sifat_kredit', 'turunan1', 'turunan2', 'turunan3', 'turunan4')->get();
                        if(count($child) < 1 || in_array($v3->id_akun, [50, 51, 52, 53, 54, 179])){
                            $v3->detail=$this->countSaldoAccountByParent($v3->id_akun, $date1, $date2, $location_id);
                        }else{
                            $v3->detail=array();
                        }
                        
                        if($v3->turunan2 != 152 && $v3->turunan2 != 49){
                            $v3->child=$child;
                            foreach ($v3->child as $k4 => $v4) {
                                $v4->detail=$this->countSaldoAccountByParent($v4->id_akun, $date1, $date2, $location_id);
                            }
                        }else{
                            $v3->child=array();
                        }
                    }
                }else{
                    $v2->child=array();
                }
            }
        }
        
        $data=array(
            'date1'     => $date1,
            'date2'     => $date2,
            'data' => $data_akun,
            'bulan' => $bulan,
            'user' => $user,
            'location_id' => $location_id,
        );
        return view('exports.export_neraca_saldo', [
            'data' => $data,
        ]);
    }
    public function balanceSheet(Request $request)
    {
       
        $user_id = request()->session()->get('user.id');
        $user = DB::table('users')->where('id', $this->user_id)->first();

        $date=0;
        $location_id=0;
        $date1=date('Y-m-d');
        $date2=date('Y-m-d');
        if ($request->input('date')) {
            $date1=$request->input('date');
            $date2=$request->input('date2');
        }
        
        $location_id=$this->site_id;
        
        $asset=$this->getSaldoAccount(null, $date1, $date2, $location_id, null);
        $parent=DB::table('tbl_akun')->where('level', 0)->get();
        foreach ($parent as $key => $value) {
            $i=0;
            foreach ($asset as $v) {
                if ($value->id_akun == $v['id_parent']) {
                    $value->detail[$i]=$v;
                    $i++;
                }
            }
        }
        $hppProduksi = 0;
        if($date1 >= '2021-04-20' || $date2 >= '2021-04-20'){
            $getHppProduksi=$this->countSaldoAccountByParent(84, '2021-04-20', '2021-04-20', $location_id);
            $hppProduksi = $getHppProduksi['detail_month']->total_debit;
        }
        
        $data=array(
            'date1'      => $date1,
            'date2'      => $date2,
            'saldo'     => $asset,
            'parent'    => $parent,
            'hppProduksi' => $hppProduksi,
        );
        // print_r(number_format($getData['detail_month']->total_debit, 0, '', ''));
        return  view('pages.akuntansi.balance_sheet_new')->with(compact('data', 'user', 'location_id'));
    }
    public function getSaldoAccount($id_parent=null, $date1, $date2, $location_id, $jurnalClose){
        $query=DB::table('tbl_akun_detail')->join('tbl_akun', 'tbl_akun.id_akun', 'tbl_akun_detail.id_akun')
                        ->where('turunan1', 0)
                        ->orderBy('tbl_akun.no_akun', 'ASC');
        if ($id_parent != null) {
            $query->where('id_parent', $id_parent);
        }
        $turunan1=$query->get();
        
        $data=array();
        $j=0;
        foreach ($turunan1 as $key => $value) {
            $turunan2=DB::table('tbl_akun_detail')->join('tbl_akun', 'tbl_akun.id_akun', 'tbl_akun_detail.id_akun')->where('turunan1', $value->id_akun)->where('turunan2', 0)->get();
            $main_id=DB::table('tbl_akun_detail')
                    ->where('id_akun', $value->id_akun)
                    // ->orWhere('turunan1', $value->id_akun)
                    // ->orWhere('turunan2', $value->id_akun)
                    ->orWhere('turunan3', $value->id_akun)
                    ->pluck('id_akun');
            if (count($turunan2) == 0) {
                $getData=$this->countSaldoAccountByParent($value->id_akun, $date1, $date2, $location_id);
                $akun=DB::table('tbl_akun')->join('tbl_akun_detail', 'tbl_akun.id_akun', 'tbl_akun_detail.id_akun')->where('tbl_akun.id_akun', $value->id_akun)->first();
                
                $akun->jumlah_debit=$getData['perubahan_before']->total_debit + $getData['detail_month']->total_debit;
                $akun->jumlah_kredit=$getData['perubahan_before']->total_kredit + $getData['detail_month']->total_kredit;
                if($akun->sifat_debit == 1){
                    $jumlah_saldo=($getData['saldo_month'] != null ? $getData['saldo_month']->total_debit - $getData['saldo_month']->total_kredit : 0);
                }else{
                    $jumlah_saldo=($getData['saldo_month'] != null ? $getData['saldo_month']->total_kredit - $getData['saldo_month']->total_debit : 0);
                }
                
                $data[$j]['nama']=$value->nama_akun;
                $data[$j]['id_akun']=$value->id_akun;
                $data[$j]['id_parent']=$value->id_parent;
                $data[$j]['data'][0]['detail'][]=$akun;
                $data[$j]['data'][0]['saldo']=(object)array('jumlah_saldo' => $jumlah_saldo);
                $data[$j]['data'][0]['saldo_month']=$getData['saldo_month'];
                $j++;
            }else{
                $data[$j]['nama']=$value->nama_akun;
                $data[$j]['id_akun']=$value->id_akun;
                $data[$j]['id_parent']=$value->id_parent;
                $n=0;
                foreach ($turunan2 as $k => $v) {
                    $getData=$this->countSaldoAccountByParent($v->id_akun, $date1, $date2, $location_id);
                    $akun=DB::table('tbl_akun')->join('tbl_akun_detail', 'tbl_akun.id_akun', 'tbl_akun_detail.id_akun')->where('tbl_akun.id_akun', $v->id_akun)->first();
                
                    $akun->jumlah_debit=$getData['perubahan_before']->total_debit + $getData['detail_month']->total_debit;
                    $akun->jumlah_kredit=$getData['perubahan_before']->total_kredit + $getData['detail_month']->total_kredit;
                    if($akun->sifat_debit == 1){
                        $jumlah_saldo=($getData['saldo_month'] != null ? $getData['saldo_month']->total_debit - $getData['saldo_month']->total_kredit : 0);
                    }else{
                        $jumlah_saldo=($getData['saldo_month'] != null ? $getData['saldo_month']->total_kredit - $getData['saldo_month']->total_debit : 0);
                    }
                    $data[$j]['data'][$n]['detail'][]=$akun;
                    $data[$j]['data'][$n]['saldo']=(object)array('jumlah_saldo' => $jumlah_saldo);
                    $data[$j]['data'][$n]['saldo_month']=$getData['saldo_month'];
                    $n++;
                }
                $j++;
            }
        }
        return $data;
    }
    public function exportGeneralLedger(Request $request) {
        $date1=date('Y-m-d');
        $date2=date('Y-m-d');
        if ($request->input('date')) {
            $date1=$request->input('date');
            $date2=$request->input('date2');
        }
        $user_id = request()->session()->get('user.id');
        $user = DB::table('users')->where('id', $this->user_id)->first();
        $location_id = $user->site_id;

        $account_selected=array();
        $data=array();
        
        $account_selected=$request->account;
        $startTime = strtotime( $date1 );
        $endTime = strtotime( $date2 );
        $date=date('Y-m', $startTime);

        $first_date_month=$date.'-01';
        $saldo_before_start_date=0;
        foreach ($account_selected as $key => $value) {
            $akun=DB::table('tbl_akun')->where('id_akun', $value)->first();
            $query=DB::table('tbl_akun_detail')
                ->where('id_akun', $value)
                ->orWhere('turunan1', $value)
                ->orWhere('turunan2', $value)
                ->orWhere('turunan3', $value)
                ->orWhere('turunan4', $value)
                ->pluck('id_akun');
            if ($startTime > strtotime($first_date_month)) {
                $min=$startTime - 86400;//kurangi sehari
                $perubahan_saldo=DB::table('tbl_trx_akuntansi_detail as trd')
                                    ->select(DB::raw("SUM(CASE WHEN tipe = 'DEBIT' THEN jumlah ELSE 0 END) AS total_debit"), DB::raw("SUM(CASE WHEN tipe = 'KREDIT' THEN jumlah ELSE 0 END) AS total_kredit"))
                                    ->join('tbl_trx_akuntansi as tra', 'trd.id_trx_akun', 'tra.id_trx_akun')
                                    ->whereIn('trd.id_akun', $query)
                                    ->where('tra.location_id', $user->site_id)
                                    ->where('tanggal', '>=', $first_date_month)
                                    ->where('tanggal', '<=', date('Y-m-d', $min))
                                    ->whereNull('notes')
                                    ->first();
                if ($akun->sifat_debit == 1) {
                    $saldo_before_start_date = $perubahan_saldo->total_debit - $perubahan_saldo->total_kredit;
                }else{
                    $saldo_before_start_date = $perubahan_saldo->total_kredit - $perubahan_saldo->total_debit;
                }
            }
            
            $bulan=explode('-', $date);
            $detail=array();
            for ( $i = $startTime; $i <= $endTime; $i = $i + 86400 ) {
                $thisDate = date( 'Y-m-d', $i ); // 2010-05-01, 2010-05-02, etc
                $dtSaldo=DB::table('tbl_trx_akuntansi_detail as trd')
                                    ->select('trd.*', 'trd.no as note_no', 'tra.*', 'ta.nama_akun', 'ta.no_akun')
                                    ->join('tbl_trx_akuntansi as tra', 'trd.id_trx_akun', 'tra.id_trx_akun')
                                    ->join('tbl_akun as ta', 'trd.id_akun', 'ta.id_akun')
                                    ->whereIn('trd.id_akun', $query)
                                    ->where('tra.location_id', $user->site_id)
                                    ->where('tanggal', $thisDate)
                                    ->whereNull('notes')
                                    ->get();
                foreach ($dtSaldo as $key => $value) {
                    $suppliers=DB::table('m_suppliers')->where('id', $value->m_supplier_id)->first();
                    $value->supplier=$suppliers != null ? $suppliers->name : '';
                    $value->purchases=DB::table('purchases')->select('no')->where('id', $value->purchase_id)->first();
                    $value->purchase_assets=DB::table('purchase_assets')->select('no')->where('id', $value->purchase_asset_id)->first();
                    $value->orders=DB::table('orders')->select('order_no')->where('id', $value->order_id)->first();
                    $value->install_orders=DB::table('install_orders')->select('no')->where('id', $value->install_order_id)->first();
                    $value->giros=DB::table('giros')->select('no')->where('id', $value->giro_id)->first();
                    $value->debts=DB::table('debts')->select('no')->where('id', $value->debt_id)->first();
                    $value->ts_warehouses=DB::table('ts_warehouses')->select('no')->where('id', $value->ts_warehouse_id)->first();
                    $value->paid_customers=DB::table('paid_customers')->select('no')->where('id', $value->paid_customer_id)->first();
                    $value->paid_suppliers=DB::table('paid_suppliers')->select('no')->where('id', $value->paid_supplier_id)->first();
                    $customer=DB::table('customers')->where('id', $value->customer_id)->first();
                    $value->customer=$customer != null ? $customer->coorporate_name : '';
                    $value->code_item='';
                    if($value->m_item_id != null){
                        $item=DB::table('m_items')->where('id', $value->m_item_id)->first();
                        $value->code_item=$item->no;
                    }
                }
                if (count($dtSaldo) > 0) {
                    $detail[$i]['date']=$thisDate;
                    $detail[$i]['dt']=$dtSaldo;
                }
            }
            $saldo_before=DB::table('tbl_saldo_months')
                                ->where('bulan', $date)
                                ->whereIn('id_akun', $query)
                                ->where('location_id', $user->site_id)
                                ->select(DB::raw('SUM(total) as jumlah_saldo'))
                                ->first();
            $data[]=array(
                'data'  => $detail,
                'saldo_awal'    => $saldo_before,
                'saldo_before_start_date'   => $saldo_before_start_date,
                'akun'  => $akun,
                'date1'  => $date1,
                'date2'  => $date2,
            );
    
        }
        // dd($data);
        return Excel::download(new GeneralLedgerExport($data), 'general_ledger.xlsx');
    }
    public function editSourceNo(Request $request){
        $id=$request->id_trx_akun_detail;
        $no=$request->spk_no;
        DB::table('tbl_trx_akuntansi_detail')->where('id_trx_akun_detail', $id)->update(array('no' => $no));
        
        return redirect()->back();
    }
    public function deleteJurnal($id){
        $jurnal=DB::table('tbl_trx_akuntansi')->where('id_trx_akun', $id)->first();
        
        if ($jurnal->paid_supplier_id != null) {
            $payment_supplier_id=DB::table('paid_supplier_ds')->where('paid_supplier_id', $jurnal->paid_supplier_id)->pluck('payment_supplier_id');
            $payment_supplier_d_id=DB::table('paid_supplier_ds')->where('paid_supplier_id', $jurnal->paid_supplier_id)->pluck('payment_supplier_d_id');
            //delete detail payment
            DB::table('payment_supplier_ds')->whereIn('id', $payment_supplier_d_id)->delete();
            //update status paid
            DB::table('payment_suppliers')->whereIn('id', $payment_supplier_id)->update(array('is_paid' => false));

            //delete paid supplier
            DB::table('paid_supplier_ds')->where('paid_supplier_id', $jurnal->paid_supplier_id)->delete();
            DB::table('paid_suppliers')->where('id', $jurnal->paid_supplier_id)->delete();
            
            //delete jurnal
            DB::table('tbl_trx_akuntansi')->where('id_trx_akun', $id)->delete();
        }elseif ($jurnal->paid_sell_item_id != null) {
            $paid_sell_item=DB::table('paid_sell_items')->where('id', $jurnal->paid_sell_item_id)->first();
            
            $inv_sale_id=DB::table('paid_sell_item_ds')->where('paid_sell_item_id', $jurnal->paid_sell_item_id)->pluck('inv_sale_id');
            
            //update status paid
            DB::table('inv_sales')->whereIn('id', $inv_sale_id)->update(array('is_paid' => false));

            //delete paid supplier
            DB::table('paid_sell_item_ds')->where('paid_sell_item_id', $jurnal->paid_sell_item_id)->delete();
            DB::table('paid_sell_items')->where('id', $jurnal->paid_sell_item_id)->delete();
            
            //delete jurnal
            DB::table('tbl_trx_akuntansi')->where('id_trx_akun', $id)->delete();
        }elseif ($jurnal->paid_customer_id != null) {
            // $paid_customer=DB::table('paid_customers')->where('id', $jurnal->paid_customer_id)->first();
            
            $customer_bill_id=DB::table('paid_customer_ds')->where('paid_customer_id', $jurnal->paid_customer_id)->pluck('customer_bill_id');
            $customer_bill_d_id=DB::table('paid_customer_ds')->where('paid_customer_id', $jurnal->paid_customer_id)->pluck('customer_bill_d_id');
            
            //delete detail payment
            DB::table('customer_bill_ds')->whereIn('id', $customer_bill_d_id)->delete();
            //update status paid
            DB::table('customer_bills')->whereIn('id', $customer_bill_id)->update(array('is_paid' => false));

            //delete paid supplier
            DB::table('paid_customer_ds')->where('paid_customer_id', $jurnal->paid_customer_id)->delete();
            DB::table('paid_customers')->where('id', $jurnal->paid_customer_id)->delete();
            
            //delete jurnal
            DB::table('tbl_trx_akuntansi')->where('id_trx_akun', $id)->delete();
        }elseif ($jurnal->paid_debt_id != null) {
            $paid_debt=DB::table('paid_debts')->where('id', $jurnal->paid_debt_id)->first();
            
            $debt_id=DB::table('paid_debt_ds')->where('paid_debt_id', $jurnal->paid_debt_id)->pluck('debt_id');
            $debt_d_id=DB::table('paid_debt_ds')->where('paid_debt_id', $jurnal->paid_debt_id)->pluck('debt_d_id');
            
            //delete detail payment
            DB::table('debt_ds')->whereIn('id', $debt_d_id)->delete();
            //update status paid
            DB::table('debts')->whereIn('id', $debt_id)->update(array('is_paid' => false));

            //delete paid supplier
            DB::table('paid_debt_ds')->where('paid_debt_id', $jurnal->paid_debt_id)->delete();
            DB::table('paid_debts')->where('id', $jurnal->paid_debt_id)->delete();
            
            //delete jurnal
            DB::table('tbl_trx_akuntansi')->where('id_trx_akun', $id)->delete();
        }elseif ($jurnal->bill_vendor_id != null) {
            //update status paid
            DB::table('bill_vendors')->where('id', $jurnal->bill_vendor_id)->delete();

            //delete jurnal
            DB::table('tbl_trx_akuntansi')->where('id_trx_akun', $id)->delete();
        }elseif ($jurnal->paid_bill_vendor_id != null) {
            $paid_bill_vendor=DB::table('paid_bill_vendors')->where('id', $jurnal->paid_bill_vendor_id)->first();
            
            $bill_vendor_id=DB::table('paid_bill_vendor_ds')->where('paid_bill_vendor_id', $jurnal->paid_bill_vendor_id)->pluck('bill_vendor_id');
            
            //update status paid
            DB::table('bill_vendors')->whereIn('id', $bill_vendor_id)->update(array('is_paid' => false));

            //delete paid supplier
            DB::table('paid_bill_vendor_ds')->where('paid_bill_vendor_id', $jurnal->paid_bill_vendor_id)->delete();
            DB::table('paid_bill_vendors')->where('id', $jurnal->paid_bill_vendor_id)->delete();
            
            //delete jurnal
            DB::table('tbl_trx_akuntansi')->where('id_trx_akun', $id)->delete();
        }elseif ($jurnal->customer_bill_id != null) {
            //delete customer bill
            DB::table('customer_bills')->where('id', $jurnal->customer_bill_id)->delete();
            
            DB::table('tbl_trx_akuntansi')->where('id_trx_akun', $id)->delete();
        }elseif($jurnal->debt_id != null){
            DB::table('debts')->where('id', $jurnal->debt_id)->delete();
            
            DB::table('tbl_trx_akuntansi')->where('id_trx_akun', $id)->delete();
        }elseif($jurnal->payment_id != null){
            DB::table('payments')->where('id', $jurnal->payment_id)->delete();
            
            DB::table('tbl_trx_akuntansi')->where('id_trx_akun', $id)->delete();
        }elseif($jurnal->payment_cost_other_id != null){
            DB::table('payment_cost_others')->where('id', $jurnal->payment_cost_other_id)->delete();
            
            DB::table('tbl_trx_akuntansi')->where('id_trx_akun', $id)->delete();
        }elseif($jurnal->payment_per_week_d_id != null){
            DB::table('payment_per_week_ds')->where('id', $jurnal->payment_per_week_d_id)->delete();
            
            DB::table('tbl_trx_akuntansi')->where('id_trx_akun', $id)->delete();
        }elseif($jurnal->payment_order_install_id != null){
            DB::table('payment_order_installs')->where('id', $jurnal->payment_order_install_id)->delete();
            
            DB::table('tbl_trx_akuntansi')->where('id_trx_akun', $id)->delete();
        }elseif($jurnal->payment_per_week_id != null){
            $payment_per_week_d=DB::table('payment_per_week_ds')->where('payment_per_week_id', $jurnal->payment_per_week_id)->pluck('id');
            DB::table('tbl_trx_akuntansi')->whereIn('payment_per_week_d_id', $payment_per_week_d)->delete();
            DB::table('payment_per_week_ds')->whereIn('id', $payment_per_week_d)->delete();

            DB::table('payment_per_weeks')->where('id', $jurnal->payment_per_week_id)->delete();
            DB::table('tbl_trx_akuntansi')->where('id_trx_akun', $id)->delete();
        }elseif($jurnal->giro_id != null){
            DB::table('giro_ds')->where('giro_id', $jurnal->giro_id)->delete();
            
            DB::table('tbl_trx_akuntansi')->where('id_trx_akun', $id)->delete();
        }elseif($jurnal->in_cash_journal == true){
            DB::table('tbl_trx_akuntansi')->where('id_trx_akun', $id)->delete();
        }elseif($jurnal->inv_trx_id != null && $jurnal->purchase_id != null){
            $this->deleteInv($jurnal->inv_trx_id);
            DB::table('purchases')->where('id', $jurnal->purchase_id)->update(array('is_closed' => 0));
            DB::table('tbl_trx_akuntansi')->where('id_trx_akun', $id)->delete();
        }else{
            if($jurnal->inv_trx_id == null){
                DB::table('tbl_trx_akuntansi')->where('id_trx_akun', $id)->delete();
            }
        }
        $this->calculateCashSaldo(null);
        return redirect()->back();
    }
    public function deleteInv($id)
    {
        $inv=DB::table('inv_trx_ds')->where('inv_trx_id', $id)->get();
        foreach ($inv as $key => $value) {
            if ($value->condition == 1) {
                //update stock
                $cek_stok=DB::table('stocks')
                            ->where('m_warehouse_id', $value->m_warehouse_id)
                            ->where('site_id', $this->site_id)
                            ->where('m_item_id', $value->m_item_id)
                            ->where('m_unit_id', $value->m_unit_id)
                            ->where('type', 'STK_NORMAL')
                            ->first();
                $update_data=array(
                    'amount' => $cek_stok->amount - $value->amount,
                    'amount_in' => $cek_stok->amount_in - $value->amount,
                );
                DB::table('stocks')->where('id', $cek_stok->id)->update($update_data);
            }
        }
        DB::table('inv_trx_ds')->where('inv_trx_id', $id)->delete();
        DB::table('inv_trxes')->where('id', $id)->delete();
    }
    public function ProfitLossTemp(Request $request)
    {
        $site_id = auth()->user()['site_id'];
        try
        {
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $getCustomerProjectList = DB::table('customer_projects')->select('id', 'name');
            if ($site_id == null) {
                $client1 = new Client(['base_uri' => $this->base_api_url . 'order/option']); 
            }else{
                $client1 = new Client(['base_uri' => $this->base_api_url . 'order/option?site_id='.$site_id]);
                $getCustomerProjectList->where('site_id', $site_id);
            } 
            $getCustomerProjectList->orderBy('id', 'DESC');
            $customerProjectList = $getCustomerProjectList->get();
            $response1 = $client1->request('GET', '', ['headers' => $headers]); 
            $body1 = $response1->getBody();
            $content1 = $body1->getContents();
            $response_array1 = json_decode($content1,TRUE);

            $response1 = $content1;  
            $order_list = $response_array1['data'];
        } catch(RequestException $exception) {
            
        }
        $data_temp=array();
        $orderByProject = array();
        if($request->customer_project_id){
            $getOrder = DB::table('orders')->where('customer_project_id', $request->customer_project_id)->orderBy('id', 'ASC')->get();
            foreach($getOrder as $key => $value){
                $account_project=DB::table('account_projects')->where('order_id', $value->id)->first();
                $install_order=DB::table('install_orders')->where('order_id', $value->id)->pluck('id');
                $dp=DB::table('tbl_trx_akuntansi_detail')->where('id_akun', $account_project->dp_id)->get();
                // dd($account_project);
                $bill=DB::table('customer_bills')
                            ->where('order_id', $value->id)
                            ->orWhereIn('install_order_id', $install_order)
                            ->pluck('id');
                $paid_amount=DB::table('paid_customer_ds')->whereIn('customer_bill_id', $bill)->select(DB::raw('SUM(amount) as total'))->first();
                $paid_detail_amount=DB::table('paid_customer_ds')
                                        ->join('customer_bills as cb', 'cb.id', 'paid_customer_ds.customer_bill_id')
                                        ->join('paid_customers as pc', 'pc.id', 'paid_customer_ds.paid_customer_id')
                                        ->leftJoin('tbl_trx_akuntansi as tra', 'tra.paid_customer_id', 'pc.id')
                                        ->whereIn('paid_customer_ds.customer_bill_id', $bill)
                                        ->select(DB::raw('SUM(paid_customer_ds.amount) as total'), DB::raw('COALESCE((select SUM(jumlah) from tbl_trx_akuntansi tra join tbl_trx_akuntansi_detail trd on tra.id_trx_akun=trd.id_trx_akun where tra.customer_bill_id=paid_customer_ds.customer_bill_id and id_akun in (133, 135)), 0) AS total_tax'), DB::raw("MAX(cb.bill_no) as bill_no"), DB::raw("MAX(pc.paid_date) as paid_date"), DB::raw("MAX(cb.notes) as notes"), DB::raw('MAX(cb.no) as no'), DB::raw('MAX(tra.deskripsi) as deskripsi'))
                                        ->groupBy('paid_customer_ds.customer_bill_id', 'pc.paid_date')
                                        ->orderBy('pc.paid_date')
                                        ->get();
                
                $paid=DB::table('paid_customer_ds')->whereIn('customer_bill_id', $bill)->pluck('customer_bill_id');
                $tax_amount=DB::table('tbl_trx_akuntansi_detail as trd')
                                ->join('tbl_trx_akuntansi as tra', 'tra.id_trx_akun', 'trd.id_trx_akun')
                                ->whereIn('customer_bill_id', $paid)
                                ->whereIn('id_akun', [133, 135])
                                ->select(DB::raw('COALESCE(SUM(case when id_akun = 133 then jumlah else 0 end), 0) as total_ppn'), DB::raw('COALESCE(SUM(case when id_akun = 135 then jumlah else 0 end), 0) as total_pph'))
                                ->first();
    
                $biaya=DB::table('tbl_trx_akuntansi_detail as trd')
                                ->join('tbl_trx_akuntansi as tra', 'tra.id_trx_akun', 'trd.id_trx_akun')
                                ->whereIn('id_akun', [$account_project->cost_material_id, $account_project->cost_spare_part_id, $account_project->cost_service_id])
                                ->where('tipe', 'DEBIT')
                                ->select(DB::raw('COALESCE(SUM(jumlah), 0) as total'))
                                ->first();
                $biaya_detail=DB::table('tbl_trx_akuntansi_detail as trd')
                                ->select(DB::raw('SUM(trd.jumlah) as total'), DB::raw('MAX(ta.no_akun) as no_akun'), DB::raw('MAX(ta.nama_akun) as nama_akun'), DB::raw('MAX(tra.tanggal) as tanggal'), DB::raw('MAX(tra.inv_trx_id) as inv_trx_id'), 'tra.id_trx_akun')
                                ->join('tbl_trx_akuntansi as tra', 'tra.id_trx_akun', 'trd.id_trx_akun')
                                ->join('tbl_akun as ta', 'ta.id_akun', 'trd.id_akun')
                                ->whereIn('trd.id_akun', [$account_project->cost_material_id, $account_project->cost_spare_part_id, $account_project->cost_service_id])
                                ->where('trd.tipe', 'DEBIT')
                                ->groupBy('tra.id_trx_akun')
                                ->get();
                $orderByProject[$key]=array(
                    'paid'  => $paid_amount,
                    'paid_detail_amount'  => $paid_detail_amount,
                    'tax'   => $tax_amount,
                    'biaya' => $biaya,
                    'biaya_detail' => $biaya_detail,
                    'dp' => $dp,
                    'order_id' => $value->id,
                    'spk_number' => $value->spk_number,
                    'customer' => DB::table('customers')->select('coorporate_name')->where('id', $value->customer_id)->first(),
                );
            }
            // return $data_temp;
        }
        $data=array(
            'customerProjectList' => $customerProjectList,
            'order_list'     => $order_list,
            'order_id'      => $request->order_id,
            'customerProjectId' => $request->customer_project_id,
            'data'          => $orderByProject
        );
        return view('pages.akuntansi.laba_rugi_temp', $data);
    }
    
    public function exportProfitLossTemp(Request $request)
    {
        $site_id = auth()->user()['site_id'];
        $id = $request->get('proyek_id');
        $orderByProject = array();
        if($id){
            $getOrder = DB::table('orders')->where('customer_project_id', $id)->orderBy('id', 'ASC')->get();
            foreach($getOrder as $key => $value){
                $account_project=DB::table('account_projects')->where('order_id', $value->id)->first();
                $install_order=DB::table('install_orders')->where('order_id', $value->id)->pluck('id');
                $dp=DB::table('tbl_trx_akuntansi_detail')->where('id_akun', $account_project->dp_id)->get();
                // dd($account_project);
                $bill=DB::table('customer_bills')
                            ->where('order_id', $value->id)
                            ->orWhereIn('install_order_id', $install_order)
                            ->pluck('id');
                $paid_amount=DB::table('paid_customer_ds')->whereIn('customer_bill_id', $bill)->select(DB::raw('SUM(amount) as total'))->first();
                $paid_detail_amount=DB::table('paid_customer_ds')
                                        ->join('customer_bills as cb', 'cb.id', 'paid_customer_ds.customer_bill_id')
                                        ->join('paid_customers as pc', 'pc.id', 'paid_customer_ds.paid_customer_id')
                                        ->leftJoin('tbl_trx_akuntansi as tra', 'tra.paid_customer_id', 'pc.id')
                                        ->whereIn('paid_customer_ds.customer_bill_id', $bill)
                                        ->select(DB::raw('SUM(paid_customer_ds.amount) as total'), DB::raw('COALESCE((select SUM(jumlah) from tbl_trx_akuntansi tra join tbl_trx_akuntansi_detail trd on tra.id_trx_akun=trd.id_trx_akun where tra.customer_bill_id=paid_customer_ds.customer_bill_id and id_akun in (133, 135)), 0) AS total_tax'), DB::raw("MAX(cb.bill_no) as bill_no"), DB::raw("MAX(pc.paid_date) as paid_date"), DB::raw("MAX(cb.notes) as notes"), DB::raw('MAX(cb.no) as no'), DB::raw('MAX(tra.deskripsi) as deskripsi'))
                                        ->groupBy('paid_customer_ds.customer_bill_id', 'pc.paid_date')
                                        ->orderBy('pc.paid_date')
                                        ->get();
                
                $paid=DB::table('paid_customer_ds')->whereIn('customer_bill_id', $bill)->pluck('customer_bill_id');
                $tax_amount=DB::table('tbl_trx_akuntansi_detail as trd')
                                ->join('tbl_trx_akuntansi as tra', 'tra.id_trx_akun', 'trd.id_trx_akun')
                                ->whereIn('customer_bill_id', $paid)
                                ->whereIn('id_akun', [133, 135])
                                ->select(DB::raw('COALESCE(SUM(case when id_akun = 133 then jumlah else 0 end), 0) as total_ppn'), DB::raw('COALESCE(SUM(case when id_akun = 135 then jumlah else 0 end), 0) as total_pph'))
                                ->first();
    
                $biaya=DB::table('tbl_trx_akuntansi_detail as trd')
                                ->join('tbl_trx_akuntansi as tra', 'tra.id_trx_akun', 'trd.id_trx_akun')
                                ->whereIn('id_akun', [$account_project->cost_material_id, $account_project->cost_spare_part_id, $account_project->cost_service_id])
                                ->where('tipe', 'DEBIT')
                                ->select(DB::raw('COALESCE(SUM(jumlah), 0) as total'))
                                ->first();
                $biaya_detail=DB::table('tbl_trx_akuntansi_detail as trd')
                                ->select(DB::raw('SUM(trd.jumlah) as total'), DB::raw('MAX(ta.no_akun) as no_akun'), DB::raw('MAX(ta.nama_akun) as nama_akun'), DB::raw('MAX(tra.tanggal) as tanggal'), DB::raw('MAX(tra.inv_trx_id) as inv_trx_id'), 'tra.id_trx_akun')
                                ->join('tbl_trx_akuntansi as tra', 'tra.id_trx_akun', 'trd.id_trx_akun')
                                ->join('tbl_akun as ta', 'ta.id_akun', 'trd.id_akun')
                                ->whereIn('trd.id_akun', [$account_project->cost_material_id, $account_project->cost_spare_part_id, $account_project->cost_service_id])
                                ->where('trd.tipe', 'DEBIT')
                                ->groupBy('tra.id_trx_akun')
                                ->get();
                $query=DB::table('orders')->join('order_ds', 'orders.id', 'order_ds.order_id')  ->join('products', 'products.id', 'order_ds.product_id')->where('order_ds.order_id', $value->id)
                ->select('order_ds.*', 'products.amount_set')->orderBy('order_ds.updated_at','desc')->get();
                $total_kontrak=0;
                foreach ($query as $k => $v) {
                    $total=(($v->amount_set * $v->total) * $v->price);
                    $total_kontrak+=$total;
                }
                $orderByProject[$key]=array(
                    'paid'  => $paid_amount,
                    'paid_detail_amount'  => $paid_detail_amount,
                    'tax'   => $tax_amount,
                    'biaya' => $biaya,
                    'biaya_detail' => $biaya_detail,
                    'dp' => $dp,
                    'order_id' => $value->id,
                    'spk_number' => $value->spk_number,
                    'customer' => DB::table('customers')->select('coorporate_name')->where('id', $value->customer_id)->first(),
                    'nilaiKontrak' => $total_kontrak + ($total_kontrak * 10 /100),
                );
            }
        }
        $data=array(
            'data' => $orderByProject,
            'namaProyek' => DB::table('customer_projects')->select('name')->first(), 
        );
        return view('exports.export_profit_loss_temp_project', $data);
        
    }
    
    public function profitLossTempKontrak(Request $request)
    {
        $site_id = auth()->user()['site_id'];
        try
        {
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            if ($site_id == null) {
                $client1 = new Client(['base_uri' => $this->base_api_url . 'order/option']); 
            }else{
                $client1 = new Client(['base_uri' => $this->base_api_url . 'order/option?site_id='.$site_id]); 
            } 
            $response1 = $client1->request('GET', '', ['headers' => $headers]); 
            $body1 = $response1->getBody();
            $content1 = $body1->getContents();
            $response_array1 = json_decode($content1,TRUE);

            $response1 = $content1;  
            $order_list = $response_array1['data'];
        } catch(RequestException $exception) {
            
        }
        $data_temp=array();
        if($request->order_id){
            $account_project=DB::table('account_projects')->where('order_id', $request->input('order_id'))->first();
            $install_order=DB::table('install_orders')->where('order_id', $request->input('order_id'))->pluck('id');
            $dp=DB::table('tbl_trx_akuntansi_detail')->join('tbl_trx_akuntansi', 'tbl_trx_akuntansi_detail.id_trx_akun', 'tbl_trx_akuntansi.id_trx_akun')->where('id_akun', $account_project->dp_id)->get();
            $bill=DB::table('customer_bills')
                        ->where('order_id', $request->input('order_id'))
                        ->orWhereIn('install_order_id', $install_order)
                        ->pluck('id');
            $paid_amount=DB::table('paid_customer_ds')->whereIn('customer_bill_id', $bill)->select(DB::raw('SUM(amount) as total'))->first();
            $paid_detail_amount=DB::table('paid_customer_ds')
                                    ->join('customer_bills as cb', 'cb.id', 'paid_customer_ds.customer_bill_id')
                                    ->join('paid_customers as pc', 'pc.id', 'paid_customer_ds.paid_customer_id')
                                    ->leftJoin('tbl_trx_akuntansi as tra', 'tra.paid_customer_id', 'pc.id')
                                    ->whereIn('paid_customer_ds.customer_bill_id', $bill)
                                    ->select(DB::raw('SUM(paid_customer_ds.amount) as total'), DB::raw('COALESCE((select SUM(jumlah) from tbl_trx_akuntansi tra join tbl_trx_akuntansi_detail trd on tra.id_trx_akun=trd.id_trx_akun where tra.customer_bill_id=paid_customer_ds.customer_bill_id and id_akun in (133, 135)), 0) AS total_tax'), DB::raw("MAX(cb.bill_no) as bill_no"), DB::raw("MAX(pc.paid_date) as paid_date"), DB::raw("MAX(cb.notes) as notes"), DB::raw('MAX(cb.no) as no'), DB::raw('MAX(tra.deskripsi) as deskripsi'))
                                    ->groupBy('paid_customer_ds.customer_bill_id', 'pc.paid_date')
                                    ->orderBy('pc.paid_date')
                                    ->get();
            
            $paid=DB::table('paid_customer_ds')->whereIn('customer_bill_id', $bill)->pluck('customer_bill_id');
            $tax_amount=DB::table('tbl_trx_akuntansi_detail as trd')
                            ->join('tbl_trx_akuntansi as tra', 'tra.id_trx_akun', 'trd.id_trx_akun')
                            ->whereIn('customer_bill_id', $paid)
                            ->whereIn('id_akun', [133, 135])
                            ->select(DB::raw('COALESCE(SUM(case when id_akun = 133 then jumlah else 0 end), 0) as total_ppn'), DB::raw('COALESCE(SUM(case when id_akun = 135 then jumlah else 0 end), 0) as total_pph'))
                            ->first();

            $biaya=DB::table('tbl_trx_akuntansi_detail as trd')
                            ->join('tbl_trx_akuntansi as tra', 'tra.id_trx_akun', 'trd.id_trx_akun')
                            ->whereIn('id_akun', [$account_project->cost_material_id, $account_project->cost_spare_part_id, $account_project->cost_service_id])
                            ->where('tipe', 'DEBIT')
                            ->select(DB::raw('COALESCE(SUM(jumlah), 0) as total'))
                            ->first();
            $biaya_detail=DB::table('tbl_trx_akuntansi_detail as trd')
                            ->select(DB::raw('SUM(trd.jumlah) as total'), DB::raw('MAX(ta.no_akun) as no_akun'), DB::raw('MAX(ta.nama_akun) as nama_akun'), DB::raw('MAX(tra.tanggal) as tanggal'), DB::raw('MAX(tra.inv_trx_id) as inv_trx_id'), 'tra.id_trx_akun')
                            ->join('tbl_trx_akuntansi as tra', 'tra.id_trx_akun', 'trd.id_trx_akun')
                            ->join('tbl_akun as ta', 'ta.id_akun', 'trd.id_akun')
                            ->whereIn('trd.id_akun', [$account_project->cost_material_id, $account_project->cost_spare_part_id, $account_project->cost_service_id])
                            ->where('trd.tipe', 'DEBIT')
                            ->groupBy('tra.id_trx_akun')
                            ->get();
            $data_temp=array(
                'paid'  => $paid_amount,
                'paid_detail_amount'  => $paid_detail_amount,
                'tax'   => $tax_amount,
                'biaya' => $biaya,
                'biaya_detail' => $biaya_detail,
                'dp' => $dp
            );
            // return $data_temp;
        }
        $query=DB::table('orders')->join('order_ds', 'orders.id', 'order_ds.order_id')->join('products', 'products.id', 'order_ds.product_id')->where('order_ds.order_id', $request->order_id)
            ->select('order_ds.*', 'products.amount_set')->orderBy('order_ds.updated_at','desc')->get();
        $total_kontrak=0;
        foreach ($query as $k => $v) {
            $total=(($v->amount_set * $v->total) * $v->price);
            $total_kontrak+=$total;
        }
        
        $data=array(
            'order_list'     => $order_list,
            'order_id'      => $request->order_id,
            'data'          => $data_temp,
            'rab' => DB::table('rabs')->select('base_price')->where('order_id', $request->order_id)->first(),
            'nilaiKontrak' => $total_kontrak + ($total_kontrak * 10 /100)
        );
        return view('pages.akuntansi.laba_rugi_temp_kontrak', $data);
    }
    
    public function cashFlow(Request $request)
    {
        $site_id = auth()->user()['site_id'];
        try
        {
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            if ($site_id == null) {
                $client1 = new Client(['base_uri' => $this->base_api_url . 'order/option']); 
            }else{
                $client1 = new Client(['base_uri' => $this->base_api_url . 'order/option?site_id='.$site_id]); 
            } 
            $response1 = $client1->request('GET', '', ['headers' => $headers]); 
            $body1 = $response1->getBody();
            $content1 = $body1->getContents();
            $response_array1 = json_decode($content1,TRUE);

            $response1 = $content1;  
            $order_list = $response_array1['data'];
        } catch(RequestException $exception) {
            
        }
        $data_temp=array();
        if($request->order_id){
            $account_project=DB::table('account_projects')->where('order_id', $request->input('order_id'))->first();
            $install_order=DB::table('install_orders')->where('order_id', $request->input('order_id'))->pluck('id');
            $dp=DB::table('tbl_trx_akuntansi_detail')->where('id_akun', $account_project->dp_id)->get();
            // dd($account_project);
            $bill=DB::table('customer_bills')
                        ->where('order_id', $request->input('order_id'))
                        ->orWhereIn('install_order_id', $install_order)
                        ->pluck('id');
            $paid_amount=DB::table('paid_customer_ds')->whereIn('customer_bill_id', $bill)->select(DB::raw('SUM(amount) as total'))->first();
            $paid_detail_amount=DB::table('paid_customer_ds')
                                    ->join('customer_bills as cb', 'cb.id', 'paid_customer_ds.customer_bill_id')
                                    ->join('paid_customers as pc', 'pc.id', 'paid_customer_ds.paid_customer_id')
                                    ->leftJoin('tbl_trx_akuntansi as tra', 'tra.paid_customer_id', 'pc.id')
                                    ->whereIn('paid_customer_ds.customer_bill_id', $bill)
                                    ->select(DB::raw('SUM(paid_customer_ds.amount) as total'), DB::raw('COALESCE((select SUM(jumlah) from tbl_trx_akuntansi tra join tbl_trx_akuntansi_detail trd on tra.id_trx_akun=trd.id_trx_akun where tra.customer_bill_id=paid_customer_ds.customer_bill_id and id_akun in (133, 135)), 0) AS total_tax'), DB::raw("MAX(cb.bill_no) as bill_no"), DB::raw("MAX(pc.paid_date) as paid_date"), DB::raw("MAX(cb.notes) as notes"), DB::raw('MAX(cb.no) as no'), DB::raw('MAX(tra.deskripsi) as deskripsi'))
                                    ->groupBy('paid_customer_ds.customer_bill_id', 'pc.paid_date')
                                    ->orderBy('pc.paid_date')
                                    ->get();
            
            $paid=DB::table('paid_customer_ds')->whereIn('customer_bill_id', $bill)->pluck('customer_bill_id');
            $tax_amount=DB::table('tbl_trx_akuntansi_detail as trd')
                            ->join('tbl_trx_akuntansi as tra', 'tra.id_trx_akun', 'trd.id_trx_akun')
                            ->whereIn('customer_bill_id', $paid)
                            ->whereIn('id_akun', [133, 135])
                            ->select(DB::raw('COALESCE(SUM(case when id_akun = 133 then jumlah else 0 end), 0) as total_ppn'), DB::raw('COALESCE(SUM(case when id_akun = 135 then jumlah else 0 end), 0) as total_pph'))
                            ->first();

            $biaya=DB::table('tbl_trx_akuntansi_detail as trd')
                            ->join('tbl_trx_akuntansi as tra', 'tra.id_trx_akun', 'trd.id_trx_akun')
                            ->whereIn('id_akun', [$account_project->cost_material_id, $account_project->cost_spare_part_id, $account_project->cost_service_id])
                            ->where('tipe', 'DEBIT')
                            ->select(DB::raw('COALESCE(SUM(jumlah), 0) as total'))
                            ->first();
            $biaya_detail=DB::table('tbl_trx_akuntansi_detail as trd')
                            ->select(DB::raw('SUM(trd.jumlah) as total'), DB::raw('MAX(ta.no_akun) as no_akun'), DB::raw('MAX(ta.nama_akun) as nama_akun'), DB::raw('MAX(tra.tanggal) as tanggal'), DB::raw('MAX(tra.inv_trx_id) as inv_trx_id'), 'tra.id_trx_akun')
                            ->join('tbl_trx_akuntansi as tra', 'tra.id_trx_akun', 'trd.id_trx_akun')
                            ->join('tbl_akun as ta', 'ta.id_akun', 'trd.id_akun')
                            ->whereIn('trd.id_akun', [$account_project->cost_material_id, $account_project->cost_spare_part_id, $account_project->cost_service_id])
                            ->where('trd.tipe', 'DEBIT')
                            ->groupBy('tra.id_trx_akun')
                            ->get();
            $data_temp=array(
                'paid'  => $paid_amount,
                'paid_detail_amount'  => $paid_detail_amount,
                'tax'   => $tax_amount,
                'biaya' => $biaya,
                'biaya_detail' => $biaya_detail,
            );
            // return $data_temp;
        }
        $data=array(
            'order_list'     => $order_list,
            'order_id'      => $request->order_id,
            'data'          => $data_temp
        );
        return view('pages.akuntansi.cash_flow', $data);
    }
    
    public function recaptKasBank(Request $request)
    {

        $user = DB::table('users')->where('id', $this->user_id)->first();

        $date=date('Y-m-d');
        $date2=date('Y-m-d');
        if ($request->input('date')) {
            $date=$request->input('date');
            $date2=$request->input('date2');
        }
        $account_payment=DB::table('tbl_akun')->where('id_main_akun', 13)->where('id_akun', '!=', 29)->pluck('id_akun');
        $list_trx=DB::table('tbl_trx_akuntansi as tra')
                    ->where('tanggal', '>=', $date)
                    ->where('tanggal','<=', $date2)
                    ->whereIn('trd.id_akun', $account_payment)
                    ->join('tbl_trx_akuntansi_detail as trd', 'trd.id_trx_akun', 'tra.id_trx_akun')
                    ->join('tbl_akun as ta', 'trd.id_akun', 'ta.id_akun')
                    ->orderBy('tra.dtm_crt', 'DESC');
        if ($user->site_id != null) {
            $list_trx->where('location_id', $user->site_id);
        }
        $data=$list_trx->get();
        // return $data;
        
        return view('pages.akuntansi.recapt_kas')->with(compact('data', 'date', 'date2'));
    }
    public function detailInvBySuratJalan(Request $request){
        $no=$request->no;
        $date=$request->date;
        $inv=DB::table('inv_trxes')
                        ->where('inv_trxes.no_surat_jalan', $no)
                        ->where('inv_trxes.inv_trx_date', $date)
                        ->join('inv_trx_ds', 'inv_trxes.id', 'inv_trx_ds.inv_trx_id')
                        ->get();
        foreach($inv as $value){
            $value->m_items=DB::table('m_items')->where('id', $value->m_item_id)->first();
            $value->m_units=DB::table('m_units')->where('id', $value->m_unit_id)->first();
            $get_save_price=DB::table('m_item_prices')->where(['m_item_id' => $value->m_item_id, 'm_unit_id' => $value->m_unit_id, 'site_id'   => $this->site_id])->first();
            $value->best_price=$get_save_price->price;
        }
        $data=array(
            'data'  => $inv
        );
        return $data;
    }
    public function cutSaldoYear(Request $request){
        $date1=$request->bulan;
        $date2=$request->bulan2;
        if($date1 < $date2){
            $year=$request->tahun;
            for($i=$date1; $i <= $date2; $i++){
                $user_id = $this->user_id;
                $user = DB::table('users')->where('id', $user_id)->first();
                
                $location_id = $user->site_id;
                $date=date('Y-m', strtotime($year.'-'.$i));
                $bulan=explode('-', $date);
                $location_id=$user->site_id;
                $time = strtotime($date);
                $final = date('Y-m', strtotime("+1 month", $time));

                $data_account=$this->countSaldoAccount($date);
                
                // dd($data['detail_month']);
                foreach ($data_account['detail_month'] as $v){
                    if ($v->sifat_debit == 1) {
                        $jumlah_saldo=(($v->saldo_awal_bulan != null ? $v->saldo_awal_bulan->total_debit : 0) + $v->total_debit) - (($v->saldo_awal_bulan != null ? $v->saldo_awal_bulan->total_kredit : 0) + $v->total_kredit);
                        $data_saldo=array(
                            'id_akun'   => $v->id_akun,
                            'total'  => round($jumlah_saldo, 2),
                            'bulan'   => $final,
                            'location_id'   => $location_id,
                            'total_debit'   => (($v->saldo_awal_bulan != null ? $v->saldo_awal_bulan->total_debit : 0) + $v->total_debit),
                            'total_kredit'   => (($v->saldo_awal_bulan != null ? $v->saldo_awal_bulan->total_kredit : 0) + $v->total_kredit),
                        );
                        $ceksaldo=DB::table('tbl_saldo_months')->where('bulan', $final)->where('location_id', $location_id)->where('id_akun', $v->id_akun)->first();
                        if ($ceksaldo == null) {
                            $data_saldo['dtm_crt']=date('Y-m-d H:i:s');
                            $data_saldo['dtm_upd']=date('Y-m-d H:i:s');
                            DB::table('tbl_saldo_months')->insert($data_saldo);
                        }else{
                            $data_saldo['dtm_upd']=date('Y-m-d H:i:s');
                            DB::table('tbl_saldo_months')->where('id', $ceksaldo->id)->update($data_saldo);
                        }
                    }else{
                        $jumlah_saldo=(($v->saldo_awal_bulan != null ? $v->saldo_awal_bulan->total_kredit : 0) + $v->total_kredit) - (($v->saldo_awal_bulan != null ? $v->saldo_awal_bulan->total_debit : 0) + $v->total_debit);
                        $data_saldo=array(
                            'id_akun'   => $v->id_akun,
                            'total'  => round($jumlah_saldo, 2),
                            'bulan'   => $final,
                            'location_id'   => $location_id,
                            'total_debit'   => (($v->saldo_awal_bulan != null ? $v->saldo_awal_bulan->total_debit : 0) + $v->total_debit),
                            'total_kredit'   => (($v->saldo_awal_bulan != null ? $v->saldo_awal_bulan->total_kredit : 0) + $v->total_kredit),
                        );
                        $ceksaldo=DB::table('tbl_saldo_months')->where('bulan', $final)->where('location_id', $location_id)->where('id_akun', $v->id_akun)->first();
                        if ($ceksaldo == null) {
                            $data_saldo['dtm_crt']=date('Y-m-d H:i:s');
                            $data_saldo['dtm_upd']=date('Y-m-d H:i:s');
                            DB::table('tbl_saldo_months')->insert($data_saldo);
                        }else{
                            $data_saldo['dtm_upd']=date('Y-m-d H:i:s');
                            DB::table('tbl_saldo_months')->where('id', $ceksaldo->id)->update($data_saldo);
                        }
                    }
                }
            }
            $output = ['success' => 1,
                            'msg' => 'success'
                        ];
        }else{
            $output = ['success' => 0,
                            'msg' => 'success'
                        ];
        }
        return redirect('akuntansi/cut-saldo')->with('status', $output);
    }
    public function calculateCashSaldo($warehouse_id){
        $warehouse=DB::table('m_warehouses')->whereNotNull('site_id');
        if($warehouse_id != null){
            $warehouse->where('id', $warehouse_id);
        }
        $warehouse=$warehouse->get();
        foreach($warehouse as $row){
            $akun_id=$row->id == 2 ? 24 : 101;
            $dt=DB::table('tbl_trx_akuntansi as tra')
                ->join('tbl_trx_akuntansi_detail as trd', 'tra.id_trx_akun', 'trd.id_trx_akun')
                ->where('id_akun', $akun_id)
                ->select(DB::raw("COALESCE(SUM(CASE WHEN tipe ='DEBIT' THEN jumlah ELSE 0 END), 0) as debit"), DB::raw("COALESCE(SUM(CASE WHEN tipe ='KREDIT' THEN jumlah ELSE 0 END), 0) as kredit"))
                ->first();
            $cash=DB::table('cashes')->where('site_id', $row->site_id)->where('m_warehouse_id', $row->id)->first();
            if($cash == null){
                try
                {
                    $headers = [
                        'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                        'Accept'        => 'application/json',
                    ];
                    $client = new Client(['base_uri' => $this->base_api_url . 'inv/base/Cash']);
                    $reqBody = [
                        'headers' => $headers,
                        'json' => [
                            'amount' => $dt->debit - $dt->kredit,
                            'amount_in' => $dt->debit,
                            'amount_out' => $dt->kredit,
                            'm_warehouse_id' => $row->id,
                            'site_id'   => $this->site_id
                        ]
                    ]; 
                    $response = $client->request('POST', '', $reqBody); 
                    $body = $response->getBody();
                    $content = $body->getContents();
                    $response_array = json_decode($content,TRUE);
                } catch(RequestException $exception) {
                }
            }else{
                try
                {
                    $headers = [
                        'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                        'Accept'        => 'application/json',
                    ];
                    $client = new Client(['base_uri' => $this->base_api_url . 'inv/base/Cash/'.$cash->id]);
                    $reqBody = [
                        'headers' => $headers,
                        'json' => [
                            'amount' => $dt->debit - $dt->kredit,
                            'amount_in' => $dt->debit,
                            'amount_out' => $dt->kredit,
                        ]
                    ]; 
                    $response = $client->request('PUT', '', $reqBody); 
                    // $body = $response->getBody();
                    // $content = $body->getContents();
                    // $response_array = json_decode($content,TRUE);
                } catch(RequestException $exception) {
                }
            }
        }
    }
    public function exportLabaRugiAll(Request $request){
        $date1=date('Y-m-d');
        $date2=date('Y-m-d');
        if ($request->input('date')) {
            $date1=$request->input('date');
            $date2=$request->input('date2');
        }
        $site_id = auth()->user()['site_id'];
        $profit=$this->getSaldoAccount(4, $date1, $date2, $site_id, null);
        foreach ($profit as $key => $value) {
            if ($value['id_akun'] == 22) {
                foreach ($value['data'] as $k => $v) {
                    $account_project=DB::table('account_projects')->where('profit_id', $v['detail'][0]->id_akun)->first();
                    $hpp=DB::table('tbl_trx_akuntansi as tra')
                    ->join('tbl_trx_akuntansi_detail as trd', 'trd.id_trx_akun', 'tra.id_trx_akun')
                    ->select(DB::raw("COALESCE(SUM(CASE WHEN trd.tipe='DEBIT' THEN trd.jumlah ELSE 0 END), 0) as total_debit"), DB::raw("COALESCE(SUM(CASE WHEN trd.tipe='KREDIT' THEN trd.jumlah ELSE 0 END), 0) as total_kredit"))
                    ->where('trd.id_akun', 84)
                    ->where('tanggal', '>=', $date1)
                    ->where('tanggal', '<=', $date2)
                    ->where('trd.id_akun', 84)
                    ->where('location_id', $site_id)
                    ->first();
                    $profit[$key]['data'][$k]['hpp']=$hpp;
                }
            }else{
                unset($profit[$key]);
            }
        }
        
        $orders=DB::table('orders')->select('orders.*', 'customers.coorporate_name')->join('customers', 'customers.id', 'orders.customer_id')->where('orders.site_id', $site_id)->get();
        foreach ($orders as $key => $value) {
            $account_project=DB::table('account_projects')->where('order_id', $value->id)->first();
            $get_project_dev=DB::table('project_req_developments')->where('order_id', $value->id)->pluck('id');
            $pendapatan=DB::table('tbl_trx_akuntansi as tra')
                                ->join('tbl_trx_akuntansi_detail as trd', 'tra.id_trx_akun', 'trd.id_trx_akun')
                                ->join('tbl_akun as ta', 'trd.id_akun', 'ta.id_akun')
                                ->select(DB::raw("SUM(CASE WHEN tipe = 'DEBIT' THEN jumlah ELSE 0 END) AS total_debit"), DB::raw("SUM(CASE WHEN tipe = 'KREDIT' THEN jumlah ELSE 0 END) AS total_kredit"), 'trd.id_akun', DB::raw('MAX(sifat_debit) as sifat_debit'), DB::raw('MAX(sifat_kredit) as sifat_kredit'), DB::raw('MAX(nama_akun) as nama_akun'))
                                // ->whereIn('tra.project_req_development_id', $get_project_dev)
                                // ->orWhere('tra.order_id', $value->id)
                                ->whereIn('trd.id_akun', [$account_project->profit_id])
                                ->groupBy('trd.id_akun')
                                ->get();
            $value->pendapatan=$pendapatan;
            $hpp=DB::table('tbl_trx_akuntansi as tra')
                                ->join('tbl_trx_akuntansi_detail as trd', 'tra.id_trx_akun', 'trd.id_trx_akun')
                                ->join('tbl_akun as ta', 'trd.id_akun', 'ta.id_akun')
                                ->select(DB::raw("SUM(CASE WHEN tipe = 'DEBIT' THEN jumlah ELSE 0 END) AS total_debit"), DB::raw("SUM(CASE WHEN tipe = 'KREDIT' THEN jumlah ELSE 0 END) AS total_kredit"), 'trd.id_akun', DB::raw('MAX(sifat_debit) as sifat_debit'), DB::raw('MAX(sifat_kredit) as sifat_kredit'), DB::raw('MAX(nama_akun) as nama_akun'))
                                ->where('trd.id_akun', 84)
                                // ->whereIn('tra.project_req_development_id', $get_project_dev)
                                ->where('tra.order_id', $value->id)
                                ->groupBy('trd.id_akun')
                                ->get();
            $value->hpp=$hpp;
        }
        $date=date('Y-m');
        $saldo_ppn=DB::table('tbl_saldo_akun')->where('id_akun', 67)->where('tanggal', $date)->first();
        $hutang_ppn=DB::table('tbl_trx_akuntansi as tra')
                        ->join('tbl_trx_akuntansi_detail as trd', 'tra.id_trx_akun', 'trd.id_trx_akun')
                        ->join('tbl_akun as ta', 'trd.id_akun', 'ta.id_akun')
                        ->select(DB::raw("SUM(CASE WHEN tipe = 'DEBIT' THEN jumlah ELSE 0 END) AS total_debit"), DB::raw("SUM(CASE WHEN tipe = 'KREDIT' THEN jumlah ELSE 0 END) AS total_kredit"), 'trd.id_akun', DB::raw('MAX(sifat_debit) as sifat_debit'), DB::raw('MAX(sifat_kredit) as sifat_kredit'), DB::raw('MAX(nama_akun) as nama_akun'))
                        ->where('trd.id_akun', 67)
                        ->where('tra.tanggal', 'ilike', '%'.$date.'%')
                        ->groupBy('trd.id_akun')
                        ->get();
        
        $data=array(
            'date1'     => $date1,
            'date2'     => $date2,
            'data'  => $orders,
            'pendapatan' => $profit,
            'biaya_operasional' => $this->getSaldoAccount(25, $date1, $date2, $site_id, null),
            'biaya_adm' => $this->getSaldoAccount(26, $date1, $date2, $site_id, null),
            'biaya_lain' => $this->getSaldoAccount(27, $date1, $date2, $site_id, null),
            'hutang' => $hutang_ppn,
            'saldo_ppn' => $saldo_ppn
        );
        return Excel::download(new LabaRugiAllExport($data), 'laba_rugi_all.xlsx');
    }
    public function reportHppProyek($id){
        // $customer_project_id=$request->customer_project_id;
        $order_id=$id;
        $cust_project=DB::table('orders')->where('id', $id)->get();
        foreach ($cust_project as $a => $k) {
            if ($order_id != null) {
                if ($order_id == $k->id) {
                    $get_project_dev=DB::table('project_req_developments')->where('order_id', $k->id)->get();
                    $akun_pendapatan=DB::table('tbl_akun_detail')->whereIn('id_parent', [4])->pluck('id_akun');
                    $account_project=DB::table('account_projects')->where('order_id', $k->id)->first();
                    // $akun_biaya=DB::table('tbl_akun_detail')->whereIn('id_parent', [5, 25, 26, 27])->pluck('id_akun');
                    $akun_biaya=DB::table('tbl_akun_detail')->whereIn('id_parent', [1])->pluck('id_akun');
                    foreach ($get_project_dev as $key => $value) {
                        $trx_akuntan=DB::table('tbl_trx_akuntansi as tra')
                                            ->join('tbl_trx_akuntansi_detail as trd', 'tra.id_trx_akun', 'trd.id_trx_akun')
                                            ->join('tbl_akun as ta', 'trd.id_akun', 'ta.id_akun')
                                            ->select(DB::raw("SUM(CASE WHEN tipe = 'DEBIT' THEN jumlah ELSE 0 END) AS total_debit"), DB::raw("SUM(CASE WHEN tipe = 'KREDIT' THEN jumlah ELSE 0 END) AS total_kredit"), 'trd.id_akun', DB::raw('MAX(sifat_debit) as sifat_debit'), DB::raw('MAX(sifat_kredit) as sifat_kredit'), DB::raw('MAX(nama_akun) as nama_akun'))
                                            ->where('tra.project_req_development_id', $value->id)
                                            // ->whereNotIn('trd.id_akun', [$account_project->cost_service_id])
                                            ->whereIn('trd.id_akun', [$account_project->cost_service_id])
                                            ->groupBy('trd.id_akun')
                                            ->get();
                        $value->prd_detail=$trx_akuntan;
                        $inv_request=DB::table('inv_requests as ir')
                                            ->where('ir.project_req_development_id', $value->id)
                                            ->join('inv_trxes as it', 'ir.id', 'it.inv_request_id')
                                            ->select('ir.*', 'it.id as inv_trx_id')
                                            ->get();
                        $value->inv_request=$inv_request;
                        foreach ($value->inv_request as $v) {
                            $inv_trx_ds=DB::table('inv_trx_ds as itd')
                                            ->where('itd.inv_trx_id', $v->inv_trx_id)
                                            ->join('m_items as mi', 'itd.m_item_id', 'mi.id')
                                            ->join('m_units as mu', 'itd.m_unit_id', 'mu.id')
                                            ->select('itd.*', 'mi.name as item_name', 'mu.name as unit_name')
                                            ->get();
                            $v->detail=$inv_trx_ds;
                        }
                    }
                    $k->order_d=$get_project_dev;
                }else{
                    unset($cust_project[$a]);
                }
            }else{
                $get_project_dev=DB::table('project_req_developments')->where('order_id', $k->id)->get();
                $akun_pendapatan=DB::table('tbl_akun_detail')->whereIn('id_parent', [4])->pluck('id_akun');
                $account_project=DB::table('account_projects')->where('order_id', $k->id)->first();
                // $akun_biaya=DB::table('tbl_akun_detail')->whereIn('id_parent', [5, 25, 26, 27])->pluck('id_akun');
                $akun_biaya=DB::table('tbl_akun_detail')->whereIn('id_parent', [1])->pluck('id_akun');
                foreach ($get_project_dev as $key => $value) {
                    $trx_akuntan=DB::table('tbl_trx_akuntansi as tra')
                                        ->join('tbl_trx_akuntansi_detail as trd', 'tra.id_trx_akun', 'trd.id_trx_akun')
                                        ->join('tbl_akun as ta', 'trd.id_akun', 'ta.id_akun')
                                        ->select(DB::raw("SUM(CASE WHEN tipe = 'DEBIT' THEN jumlah ELSE 0 END) AS total_debit"), DB::raw("SUM(CASE WHEN tipe = 'KREDIT' THEN jumlah ELSE 0 END) AS total_kredit"), 'trd.id_akun', DB::raw('MAX(sifat_debit) as sifat_debit'), DB::raw('MAX(sifat_kredit) as sifat_kredit'), DB::raw('MAX(nama_akun) as nama_akun'))
                                        ->where('tra.project_req_development_id', $value->id)
                                        // ->whereNotIn('trd.id_akun', [$account_project->cost_service_id])
                                        ->whereIn('trd.id_akun', [$account_project->cost_service_id])
                                        ->groupBy('trd.id_akun')
                                        ->get();
                    $value->prd_detail=$trx_akuntan;
                    $inv_request=DB::table('inv_requests as ir')
                                        ->where('ir.project_req_development_id', $value->id)
                                        ->join('inv_trxes as it', 'ir.id', 'it.inv_request_id')
                                        ->select('ir.*', 'it.id as inv_trx_id')
                                        ->get();
                    $value->inv_request=$inv_request;
                    foreach ($value->inv_request as $v) {
                        $inv_trx_ds=DB::table('inv_trx_ds as itd')
                                        ->where('itd.inv_trx_id', $v->inv_trx_id)
                                        ->join('m_items as mi', 'itd.m_item_id', 'mi.id')
                                        ->join('m_units as mu', 'itd.m_unit_id', 'mu.id')
                                        ->select('itd.*', 'mi.name as item_name', 'mu.name as unit_name')
                                        ->get();
                        $v->detail=$inv_trx_ds;
                    }
                }
                $k->order_d=$get_project_dev;
            }
        }
        
        $data=array(
            'data'        => $cust_project,
            'html_content'  => view('pages.akuntansi.view_hpp_proyek')->with(compact('cust_project'))->render()
        );
        return $data;
    }
    public function exportAccount(){
        $data=DB::table('tbl_akun as ta')->orderBy('no_akun')->get();
        foreach($data as $row){
            $main=DB::table('tbl_akun')->where('id_akun', $row->id_main_akun)->first();
            $row->main_akun=$main != null ? $main->nama_akun : '';
        }
        return Excel::download(new AccountExport($data), 'account.xlsx');
    }
    public function cekCashWarehouse($id)
    {
        $dt=DB::table('cashes')->where('site_id', $this->site_id)->where('m_warehouse_id', $id)->first();
        $data=array(
            'data'  => $dt
        );
        return $data;
    }
    public function countSaldoCash($site_id, $warehouse_id, $type, $total){
        $cash=DB::table('cashes')->where('site_id', $site_id)->where('m_warehouse_id', $warehouse_id)->first();
        if($type == 'in'){
            $amount=($cash != null ? $cash->amount : 0) + $total;
            $amount_in=($cash != null ? $cash->amount_in : 0) + $total;
            $amount_out=($cash != null ? $cash->amount_out : 0);
        }else{
            $amount=($cash != null ? $cash->amount : 0) - $total;
            $amount_in=($cash != null ? $cash->amount_in : 0);
            $amount_out=($cash != null ? $cash->amount_out : 0) + $total;
        }
        if ($cash == null) {
            try
            {
                $headers = [
                    'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                    'Accept'        => 'application/json',
                ];
                $client = new Client(['base_uri' => $this->base_api_url . 'inv/base/Cash']);
                $reqBody = [
                    'headers' => $headers,
                    'json' => [
                        'amount' => $amount,
                        'amount_in' => $amount_in,
                        'amount_out' => $amount_out,
                        'm_warehouse_id'    => $warehouse_id,
                        'site_id'   => $site_id
                    ]
                ]; 
                $response = $client->request('POST', '', $reqBody); 
                $body = $response->getBody();
                $content = $body->getContents();
                $response_array = json_decode($content,TRUE);
            } catch(RequestException $exception) {
            }
        }else{
            try
            {
                $headers = [
                    'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                    'Accept'        => 'application/json',
                ];
                $client = new Client(['base_uri' => $this->base_api_url . 'inv/base/Cash/'.$cash->id]);
                $reqBody = [
                    'headers' => $headers,
                    'json' => [
                        'amount' => $amount,
                        'amount_in' => $amount_in,
                        'amount_out' => $amount_out,
                    ]
                ]; 
                $response = $client->request('PUT', '', $reqBody); 
            } catch(RequestException $exception) {
            }
        }
    }
}
