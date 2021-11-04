<?php

namespace App\Http\Controllers\CRM\Customer;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Exception;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Redirect;
use Carbon\Carbon;
use DB;
use Yajra\DataTables\Facades\DataTables;

class CustomerController extends Controller
{
    private $base_api_url;
    private $site_id = null;
    public $user_name = null;
    public function __construct()
    {
        //Authenticate page menu
        $this->middleware(function ($request, $next) {
            Controller::isLogin(auth()->user()['role_id']); 
            $this->site_id = auth()->user()['site_id'];
            $this->user_name = auth()->user()['name'];
            
            return $next($request);
        });

        $this->base_api_url = env('API_URL');
    }

    public function index() {
        $data = array();
        return view('pages.crm.customer.customer_list', $data);
    }

    public function indexDetail($id) {
        $customer = null;
        try
        {
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . '/crm/customerdata/' . $id]);  
            $response = $client->request('GET', '', ['headers' => $headers]);  
            $body = $response->getBody();
            $content = $body->getContents();
            $response_array = json_decode($content,TRUE);
            $customer = $response_array['data'];
        } catch(RequestException $exception) {      
        }   
        $followup = null;
        try
        {
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . '/crm/followuphistories/cust/'. $id . '/1/']);  
            $response = $client->request('GET', '', ['headers' => $headers]);  
            $body = $response->getBody();
            $content = $body->getContents();
            $response_array = json_decode($content,TRUE);
            $followup = $response_array['data'];
        } catch(RequestException $exception) {      
        }
        $sales = DB::select("SELECT m_employee_id from customers where id = $id");
        foreach($sales as $s){
            $idSales = $s->m_employee_id;
        }
        $getSales = DB::select("SELECT name from m_employees where id = $idSales");
        foreach($getSales as $s){
            $salesName = $s->name;
        }
        $data = array(
            'customer' => $customer,
            'followup' => $followup,
            'sales'    => $salesName
        );
        return view('pages.crm.customer.customer_detail', $data);
    }

    public function getCustomerList(){
        $response = null;
        try
        {
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . '/crm/customerdatamain']);  
            $response = $client->request('GET', '', ['headers' => $headers]);  
            $body = $response->getBody();
            $content = $body->getContents();
            $response_array = json_decode($content,TRUE);
                    
            $response = $content;    
            $data=DataTables::of($response_array['data'])
                                    ->make(true);     
        } catch(RequestException $exception) {
                    
        }    
                
        return $data;
    }
    public function dashboard(){        
        $id_user=$this->user_name = auth()->user()['id'];
        $low=$medium=$hot=$spu=$ppjb=0;
        $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . '/crm/customerdatamain']);  
        $response = $client->request('GET', '', ['headers' => $headers]);  
        $body = $response->getBody();
        $content = $body->getContents();
        $response_array = json_decode($content,TRUE);
        $response=$response_array['data'];
        $sql_user=DB::select("select id from customers where m_employee_id in (select id from m_employees where id_user=".$id_user.") and family_role='main'");
                
        for ($i=0; $i < count($sql_user); $i++) { 
            $id=$sql_user[$i]->id;
            $sql=DB::select("select * from followup_histories where id IN (select max(id) from followup_histories where customer_id=".$id.")");
            $sql_spu=DB::select("select * from sale_trxes where id IN (select max(id) from sale_trxes where customer_id=".$id.") and trx_type IN (select trx_type from sale_trxes where trx_type='SPU' OR trx_type='PPJB')");
            $type=$temp_deal=0;
            $id_employee=0;
            $id_employee_deal=0;
             if (count($sql_spu) != 0) {
                $id_employee_deal=$sql_spu[0]->m_employee_id;
                $type=$sql_spu[0]->trx_type;
                if ($type == 'SPU') {
                    $spu+=1;
                }else{
                    $ppjb+=1;
                }
            }else if (count($sql) != 0) {
                $id_employee=$sql[0]->m_employee_id;
                if($sql[0]->prospect_result == NULL){
                    $type='LOW';
                    $low+=1;
                }else if($sql[0]->prospect_result == 'MEDIUM'){
                    $type=$sql[0]->prospect_result;
                    $medium+=1;
                }else{
                    $type=$sql[0]->prospect_result;
                    $hot+=1;
                }
            }else {
            }
            if ($id_employee != 0) {
                $data['data'][$i]=array('prospect' =>$type, 'id'=>$id, 'id_employee'=>$id_employee);
            }else{
                $data['data'][$i]=array('prospect' =>$type, 'id'=>$id, 'id_employee'=>$id_employee_deal);
            }
        }
        

        $date=Date('Y-m');
        // $sql=DB::table('customers')->where('created_at', 'like', '%'.$date.'%')->where('family_role', 'main')->get();
        $sql=DB::select("select count(id) from customers where created_at::text like '%".$date."%' and m_employee_id in (select id from m_employees where id_user=$id_user) and family_role='main'");
        $sql=$sql[0]->count;
        $dateToday=Date('Y-m-d');
        $sql_today=DB::select("select count(id) from customers where created_at::text like '%".$dateToday."%' and m_employee_id in (select id from m_employees where id_user=$id_user) and family_role='main'");
        $sql_today=$sql_today[0]->count;
        // exit();
        // $sql_today=DB::table('customers')->where('created_at', 'like', '%'.$dateToday.'%')->where('family_role', 'main')->get();
        
        $employee=DB::table('m_employees')->select('m_employees.id', 'm_employees.name')->where('id_user', $id_user)->get();
        $data['employee']=[];
        foreach ($employee as $key => $value) {
            $dataLow=$dataMed=$dataHot=$dataSpu=$dataPpjb=0;
            $dataLowId=$dataMedId=$dataHotId=$dataSpuId=$dataPpjbId=null;
            for ($i=0; $i < count($data['data']); $i++) { 
                if ($value->id == $data['data'][$i]['id_employee']) {
                    if ($data['data'][$i]['prospect'] == 'LOW') {
                        $dataLow+=1;
                        $dataLowId.=$data['data'][$i]['id']." ";
                    }else if ($data['data'][$i]['prospect'] == 'MEDIUM') {
                        $dataMed+=1;
                        $dataMedId.=$data['data'][$i]['id']." ";
                    }else if ($data['data'][$i]['prospect'] == 'HOT') {
                        $dataHot+=1;
                        $dataHotId.=$data['data'][$i]['id']." ";
                    }else if ($data['data'][$i]['prospect'] == 'SPU') {
                        $dataSpu+=1;
                        $dataSpuId.=$data['data'][$i]['id']." ";
                    }else if ($data['data'][$i]['prospect'] == 'PPJB') {
                        $dataPpjb+=1;
                        $dataPpjbId.=$data['data'][$i]['id']." ";
                    }
                }
            }

            $data['employee'][$key]=array('id'    => $value->id,
                                    'nama'  => $value->name,
                                    'low'  => $dataLow,
                                    'idlow'  => $dataLowId,
                                    'medium'  => $dataMed,
                                    'idmedium'  => $dataMedId,
                                    'hot'  => $dataHot,
                                    'idhot'  => $dataHotId,
                                    'spu'  => $dataSpu,
                                    'idspu'  => $dataSpuId,
                                    'ppjb'  => $dataPpjb,
                                    'idppjb'  => $dataPpjbId,
            );

        }
        // header('Content-Type: Application/json');
        // echo json_encode($data['employee']);
        // exit();
        $data['count']=[];
        $cntLow=$cntMedium=$cntLow=$cntHot=$cntSpu=$cntPpjb='';
        for ($i=0; $i < count($data['employee']); $i++) { 
            $cntLow.=$data['employee'][$i]['idlow'];
            $cntMedium.=$data['employee'][$i]['idmedium'];
            $cntHot.=$data['employee'][$i]['idhot'];
            $cntSpu.=$data['employee'][$i]['idspu'];
            $cntPpjb.=$data['employee'][$i]['idppjb'];
        }
        $data['count']['low']=$cntLow;
        $data['count']['medium']=$cntMedium;
        $data['count']['hot']=$cntHot;
        $data['count']['spu']=$cntSpu;
        $data['count']['ppjb']=$cntPpjb;
        
        $data=array(
            'low' => $low,
            'medium'=>$medium,
            'hot' => $hot,
            'spu' => $spu,
            'ppjb' => $ppjb,
            'employee' => $data['employee'],
            'count' => $data['count'],
            'cust_month' => $sql,
            'cust_today' => $sql_today
        );
        
        try
        {
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . '/crm/followuphistories/list/cust/1']);
            $response = $client->request('GET', '', ['headers' => $headers]); 
            $body = $response->getBody();
            $content = $body->getContents();
            $response_array = json_decode($content,TRUE);
            $followup = $response_array['data'];
        } catch(RequestException $exception) {

        }
        $data['followup'] = $followup;
        return view('pages.crm.customer.dashboard_customer', $data);
    }
    public function getCustomerToday(){
        $id_user=$this->user_name = auth()->user()['id'];
        $response = null;
        try
        {
            $date=Date('Y-m-d');
            $client=DB::select("select customers.id, customers.name, customers.address, customers.phone_no, customers.city, m_employees.name as nama_sales from customers join m_employees on 
                customers.m_employee_id=m_employees.id where customers.created_at::text like '%".$date."%' and 
                customers.m_employee_id in (select id from m_employees where id_user=$id_user) and customers.family_role='main'");
            
            $response=response()->json(['data'=>$client]);

        } catch(RequestException $exception) {
                    
        }    
                
        return $response;
    }
    public function getCustomerMonth(){
        $id_user=$this->user_name = auth()->user()['id'];
        $response = null;
        try
        {
            $date=Date('Y-m');
            $client=DB::select("select customers.id, customers.name, customers.address, customers.phone_no, customers.city, m_employees.name as nama_sales from customers join m_employees on 
                customers.m_employee_id=m_employees.id where customers.created_at::text like '%".$date."%' and 
                customers.m_employee_id in (select id from m_employees where id_user=$id_user) and customers.family_role='main'");
            
            $response=response()->json(['data'=>$client]);
                    
        } catch(RequestException $exception) {
                    
        }    
                
        return $response;
    }

    public function getCountFollowUp($id, $nama){
        if ($nama == null or $nama == '') {
            
        }else{
            $nama=explode(' ', $nama);
            $dataCust=[];
            for ($i=0; $i < count($nama); $i++) { 
                if ($nama[$i] != NULL) {
                    $client=DB::table('customers')
                                                    ->select('customers.id', 'customers.name','customers.address','customers.phone_no', 'customers.city')
                                                    ->where('id', $nama[$i])->get();
                    $data=$client->toArray();
                    $dataCust['data'][$i]=array(
                                    'id'=>$data[0]->id,
                                    'name'=>$data[0]->name,
                                    'address'=>$data[0]->address,
                                    'phone_no'=>$data[0]->phone_no,
                                    'city'=>$data[0]->city,
                    );
                }
            }
            return response()->json($dataCust);
        }
    }
    public function getCountCust($nama){
        if ($nama == null or $nama == '') {
            
        }else{
            $nama=explode(' ', $nama);
            $dataCust=[];
            for ($i=0; $i < count($nama); $i++) { 
                if ($nama[$i] != NULL) {
                    $client=DB::table('customers')
                                                    ->select('m_employees.name as nama_sales', 'customers.id', 'customers.name','customers.address','customers.phone_no', 'customers.city')
                                                    ->join('m_employees', 'customers.m_employee_id', '=', 'm_employees.id')
                                                    ->where('customers.id', $nama[$i])->get();
                    $data=$client->toArray();
                    $dataCust['data'][$i]=array(
                                    'id'=>$data[0]->id,
                                    'name'=>$data[0]->name,
                                    'address'=>$data[0]->address,
                                    'phone_no'=>$data[0]->phone_no,
                                    'city'=>$data[0]->city,
                                    'nama_sales'=>$data[0]->nama_sales,
                    );
                }
            }
            return response()->json($dataCust);
        }
    }
    
    public function addCustomer()
    {
        $data['sales'] = DB::select("SELECT * from m_employees");
        return view('pages.crm.customer.customer_add', $data);
    }
    public function saveCustomer(Request $request)
    {
        // //ktp
        // $file = $request->file('foto_ktp');
		// $nama_ktp = "KTP_".time()."_".$file->getClientOriginalName();
		// $tujuan_upload_ktp = public_path('upload/ktp');
        // $file->move($tujuan_upload_ktp,$nama_ktp);

        // //foto profil
        // $profil = $request->file('profil');
		// $nama_profil = "FOTO_".time()."_".$profil->getClientOriginalName();
		// $tujuan_upload_profil = public_path('upload/profil');
		// $profil->move($tujuan_upload_profil,$nama_profil);

        DB::table('customers')->insert([
            'coorporate_name'       => $request->coorporate_name,
            'name'                  => $request->name,
            // 'birth_place'           => $request->tempat,
            // 'birth_date'            => $request->tanggal,
            'address'               => $request->alamat,
            // 'religion'              => $request->agama,
            // 'marital_status'        => $request->status,
            // 'rt'                    => $request->rt,
            // 'rw'                    => $request->rw,
            // 'kelurahan'             => $request->desa,
            // 'kecamatan'             => $request->kecamatan,
            // 'city'                  => $request->kabupaten,
            // 'profile_picture'       => $nama_profil,
            // 'id_picture'            => $nama_ktp,
            'family_role'           => "main",
            // 'id_no'                 => $request->nik,
            'phone_no'              => $request->hp,
            'phone_no2'              => $request->hp2,
            'email'              => $request->email,
            'jabatan'               => $request->jabatan,
            'npwp'               => $request->npwp,
            'npwp_address'               => $request->alamat_npwp,
            'flag'               => $request->flag,
            // 'm_employee_id'         => $request->sales
        ]);
        return redirect('customer');
    }
    public function editCustomer($id)
    {
        try
        {
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . '/inv/base/Customer/'.$id]);  
            $response = $client->request('GET', '', ['headers' => $headers]);  
            $body = $response->getBody();
            $content = $body->getContents();
            $response_array = json_decode($content,TRUE);
            $customer=$response_array['data'];
        } catch(RequestException $exception) {
                    
        }
        $data=array(
            'customer'  => $customer
        );
        return view('pages.crm.customer.customer_edit', $data);
    }
    public function updateCustomer(Request $request, $id)
    {
        try
        {
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . '/inv/base/Customer/'.$id]);  
            $reqBody = [
                'headers' => $headers,
                'json' => [
                    'coorporate_name'       => $request->coorporate_name,
                    'name'                  => $request->name,
                    'address'               => $request->alamat,
                    'family_role'           => "main",
                    'phone_no'              => $request->hp,
                    'phone_no2'              => $request->hp2,
                    'email'              => $request->email,
                    'jabatan'               => $request->jabatan,
                    'npwp'               => $request->npwp,
                    'npwp_address'               => $request->alamat_npwp,
                    'flag'               => $request->flag
                ]
            ]; 
            $response = $client->request('PUT', '', $reqBody); 
        } catch(RequestException $exception) {
                    
        }
        
        return redirect('customer');
    }
    public function getCustomerJson(){
        $response = null;
        try
        {
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . '/crm/customerdatamain']);  
            $response = $client->request('GET', '', ['headers' => $headers]);  
            $body = $response->getBody();
            $content = $body->getContents();
            $response_array = json_decode($content,TRUE);
                    
        } catch(RequestException $exception) {
                    
        }    
                
        return $response_array;
    }
}