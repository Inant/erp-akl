<?php

namespace App\Http\Controllers\INV;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Carbon\Carbon;
use DB;
use App\Imports\ExcelDataImport;
use Illuminate\Database\QueryException;
use Maatwebsite\Excel\Facades\Excel; 
use PhpOffice\PhpSpreadsheet\Shared\Date;

class PembelianRutinController extends Controller
{
    private $base_api_url;
    private $site_id = null;
    private $user_id = null;
    public function __construct()
    {
        //Authenticate page menu
        $this->middleware(function ($request, $next) {
            Controller::isLogin(auth()->user()['role_id']); 
            $this->site_id = auth()->user()['site_id'];
            $this->user_id = auth()->user()['id'];
            return $next($request);
        });

        $this->base_api_url = env('API_URL');
    }
    
    public function index()
    {
        $rab = null;
        $is_error = false;
        $error_message = '';

        $period_year = date('Y');
        $period_month = date('m');
        $no_po=DB::table('m_sequences')->where('seq_code', 'PO')->where('period_year', $period_year)->where('period_month', $period_month)->select(DB::raw('MAX(seq_no) as seq_no'))->first();
        $index=$no_po != null ? $no_po->seq_no + 1 : 1;
        $data = array(
            'error' => array(
                'is_error' => $is_error,
                'error_message' => $error_message
            ),
            'index' => $index
        );
        
        return view('pages.inv.pembelian_rutin.pembelian_rutin_create', $data);
    }

    public function createPost(Request $request){

        $project_worksub_d_id = $request->post('selected_project_worksub_d_id'); //order dari rab
        $purchase_d_id = $request->post('selected_purchase_d_id'); // order dari po canceled
        $inv_request_d_id = $request->post('selected_inv_request_d_id'); // order dari material request
        $suppl_single = $request->post('suppl_single');
        $cara_bayar_single = 'credit';
        $signature_request = $request->post('signature_request');

        $m_item_id = $request->post('m_item_id');
        $volume = $request->post('volume');
        $m_unit_id = $request->post('m_unit_id');
        $perkiraan_harga_suppl = $request->post('perkiraan_harga_suppl');
        $harga_diskon = $request->post('harga_diskon');
        $diskon = $request->post('diskon');
        $discount_type = $request->post('discount_type');
        $delivery_date = $request->post('delivery_date');
        $delivery_fee = $request->post('delivery_fee');
        $suppl = array();
        $cara_bayar = array();
        $purchase = array();
        // //update base price
        // for($j = 0; $j < count($m_item_id); $j++){
        //     $query=DB::table('purchase_ds')
        //                 ->join('purchases', 'purchases.id', '=', 'purchase_ds.purchase_id')
        //                 ->where('m_item_id', $m_item_id[$j])
        //                 ->select('purchases.m_supplier_id', 'purchase_ds.base_price')
        //                 ->limit(3)
        //                 ->orderBy('purchase_ds.id', 'DESC')
        //                 ->get();
        //     $stdClass = json_decode(json_encode($query));
        //     $numbers = array_column($stdClass, 'base_price');
        //     $min = array_keys($numbers, min($numbers));
        //     print_r($min[0]);
            
        //     // try
        //     // {
        //     //     $client = new Client(['base_uri' => $this->base_api_url . 'inv/best_price']);
        //     //     $reqBody = [
        //     //         'json' => [
        //     //             'm_supplier_id' => $stdClass[$min]->m_supplier_id,
        //     //             'm_item_id' => $m_item_id[$j],
        //     //             'best_price' => $stdClass[$min]->base_price
        //     //         ]
        //     //     ]; 
        //     //     $response = $client->request('POST', '', $reqBody); 
        //     //     $body = $response->getBody();
        //     //     $content = $body->getContents();
        //     //     $response_array = json_decode($content,TRUE);
        //     //     $data = $response_array['data'];
        //     // } catch(RequestException $exception) {
        //     // }
        // }
        // exit();
        // update project_worksub_d
        if (!empty($project_worksub_d_id) && count($project_worksub_d_id)) {
            for($i = 0; $i < count($project_worksub_d_id); $i++){
                if ($project_worksub_d_id[$i] != 'undefined') {
                    try
                    {
                        $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . 'rab/base/ProjectWorksubD/'.$project_worksub_d_id[$i]]);
                        $reqBody = [
                            'headers' => $headers,
                'json' => [
                                'buy_date' => date('Y-m-d')
                            ]
                        ]; 
                        $response = $client->request('PUT', '', $reqBody); 
                        $body = $response->getBody();
                        $content = $body->getContents();
                        $response_array = json_decode($content,TRUE);
                    } catch(RequestException $exception) {
                        // $is_error = true;
                        // $error_message .= $exception->getMessage();
                    }
                }
            }
        }
        // update purchase_ds
        if (!empty($purchase_d_id) && count($purchase_d_id)) {
            for($i = 0; $i < count($purchase_d_id); $i++){
                if ($purchase_d_id[$i] != 'undefined') {
                    try
                    {
                        $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . 'rab/base/PurchaseD/'.$purchase_d_id[$i]]);
                        $reqBody = [
                            'headers' => $headers,
                'json' => [
                                'buy_date' => date('Y-m-d')
                            ]
                        ]; 
                        $response = $client->request('PUT', '', $reqBody); 
                        $body = $response->getBody();
                        $content = $body->getContents();
                        $response_array = json_decode($content,TRUE);
                    } catch(RequestException $exception) {
                        // $is_error = true;
                        // $error_message .= $exception->getMessage();
                    }
                }
            }
        }
        // update inv_request_d
        if (!empty($inv_request_d_id) && count($inv_request_d_id)) {
            for($i = 0; $i < count($inv_request_d_id); $i++){
                if ($inv_request_d_id[$i] != 'undefined') {
                    try
                    {
                        $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . 'inv/base/InvRequestD/'.$inv_request_d_id[$i]]);
                        $reqBody = [
                            'headers' => $headers,
                'json' => [
                                'buy_date' => date('Y-m-d')
                            ]
                        ]; 
                        $response = $client->request('PUT', '', $reqBody); 
                        $body = $response->getBody();
                        $content = $body->getContents();
                        $response_array = json_decode($content,TRUE);
                    } catch(RequestException $exception) {
                        // $is_error = true;
                        // $error_message .= $exception->getMessage();
                    }
                }
            }
        }
        // set suppl
        for($i = 0; $i < count($m_item_id); $i++){
            $suppl[$i] = $suppl_single;
            $cara_bayar[$i] = $cara_bayar_single;
        }

        // PO timbul berdasarkan supplier
        // hitung jumlah supplier
        $supplPo = array();
        $wopPo = array();
        for($i = 0; $i < count($suppl); $i++){
            if(!in_array($suppl[$i], $supplPo)){
                array_push($supplPo, $suppl[$i]);
                array_push($wopPo, $cara_bayar[$i]);
            }
        }
        
        $period_year = date('Y');
        $period_month = date('m');
        for($i = 0; $i < count($supplPo); $i++){
            $po_no = $this->generateTransactionNo('PO', $period_year, $period_month, $this->site_id );
            $sum_perkiraan_harga_suppl = 0;
            for($j = 0; $j < count($perkiraan_harga_suppl); $j++){
                if($suppl[$j] == $supplPo[$i])
                    $sum_perkiraan_harga_suppl += ($harga_diskon[$j]*$volume[$j]);
            }
            $message = '';
            try
            {
                $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . 'inv/base/Purchase']);
                $reqBody = [
                    'headers' => $headers,
                    'json' => [
                        'no' => $po_no,
                        'spk_number' => $request->spk_number,
                        'base_price' => $sum_perkiraan_harga_suppl,
                        'm_supplier_id' => $supplPo[$i],
                        'wop' => $wopPo[$i],
                        'purchase_date' => Carbon::now()->toDateString(),
                        'is_closed' => false,
                        'is_special' => false,
                        'site_id' => $this->site_id,
                        'discount'  => $diskon,
                        'discount_type' => $discount_type,
                        'with_ao'   => $request->input('with_ao') ? 1 : 0,
                        'acc_ao'   => $request->input('with_ao') ? 0 : 1,
                        'with_ppn'   => $request->input('with_ppn') ? 1 : 0,
                        'signature_request'   => $signature_request,
                        'notes'   => $request->catatan,
                        'user_id'   => $this->user_id,
                        'delivery_date'   => $delivery_date,
                        'delivery_fee'   => $delivery_fee,
                        'status_payment'   => false,
                        'credit_age'    => $request->credit_age
                    ]
                ]; 
                $response = $client->request('POST', '', $reqBody); 
                $body = $response->getBody();
                $content = $body->getContents();
                $response_array = json_decode($content,TRUE);
                $purchase = $response_array['data'];
                $message = 'sukses';
            } catch(RequestException $exception) {
                $message = 'gagal ' . $exception->getMessage();
            }
            catch(QueryException $e){
                $message = 'gagal ' . $e->getMessage();
            }
            // return $message;
            // if ($wopPo[$i] == 'cash' && empty($request->input('with_ao'))) {
            //     $bill_no = $this->generateTransactionNo('PAID_SPPL', $period_year, $period_month, $this->site_id );
            //     try
            //     {
            //         $client = new Client(['base_uri' => $this->base_api_url . 'inv/base/PaymentSupplier']);
            //         $reqBody = [
            //             'json' => [
            //                 'purchase_id' => $purchase['id'],
            //                 'purchase_asset_id' => 0,
            //                 // 'inv_id' => 0,
            //                 'amount' => $sum_perkiraan_harga_suppl,
            //                 'due_date' => date('Y-m-d'),
            //                 'no'  => $bill_no,
            //                 'is_paid'   => 0,
            //                 'user_id'   => auth()->user()['id'],
            //                 'm_supplier_id' => $supplPo[$i],
            //                 'payment_po'   => $wopPo[$i],
            //                 'site_id'   => $this->site_id
            //             ]
            //         ]; 
                    
            //         $response = $client->request('POST', '', $reqBody); 
            //         $body = $response->getBody();
            //         $content = $body->getContents();
            //         $response_array = json_decode($content,TRUE);
            //         $payment_supplier=$response_array['data'];
            //     } catch(RequestException $exception) {
            //     }
            // }
            for($j = 0; $j < count($m_item_id); $j++){
                if($suppl[$j] == $supplPo[$i]){
                    try
                    {
                        $headers = [
                            'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                            'Accept'        => 'application/json',
                        ];
                        $client = new Client(['base_uri' => $this->base_api_url . 'inv/base/PurchaseD']);
                        $reqBody = [
                            'headers' => $headers,
                            'json' => [
                                'purchase_id' => $purchase['id'],
                                'm_item_id' => $m_item_id[$j],
                                'amount' => $volume[$j],
                                'm_unit_id' => $m_unit_id[$j],
                                'price_before_discount' => $perkiraan_harga_suppl[$j],
                                'base_price' => $harga_diskon[$j]
                            ]
                        ]; 
                        $response = $client->request('POST', '', $reqBody); 
                        $body = $response->getBody();
                        $content = $body->getContents();
                        $response_array = json_decode($content,TRUE);
                        $data = $response_array['data'];
                    } catch(RequestException $exception) {
                    }

                    // try
                    // {
                    //     $client = new Client(['base_uri' => $this->base_api_url . 'inv/best_price']);
                    //     $reqBody = [
                    //         'json' => [
                    //             'm_supplier_id' => $supplPo[$i],
                    //             'm_item_id' => $m_item_id[$j],
                    //             'best_price' => $perkiraan_harga_suppl[$j]
                    //         ]
                    //     ]; 
                    //     $response = $client->request('POST', '', $reqBody); 
                    //     $body = $response->getBody();
                    //     $content = $body->getContents();
                    //     $response_array = json_decode($content,TRUE);
                    //     $data = $response_array['data'];
                    // } catch(RequestException $exception) {
                    // }

                }
            }
        }
        
        if (empty($request->input('with_ao'))) {
        //update base price
        for($j = 0; $j < count($m_item_id); $j++){
            $query=DB::table('purchase_ds')
                        ->join('purchases', 'purchases.id', '=', 'purchase_ds.purchase_id')
                        ->where('m_item_id', $m_item_id[$j])
                        ->select('purchases.m_supplier_id', 'purchase_ds.base_price', 'purchases.id')
                        ->limit(3)
                        ->orderBy('purchase_ds.id', 'DESC')
                        ->get();
            $stdClass = json_decode(json_encode($query));
            $numbers = array_column($stdClass, 'base_price');
            $min = array_keys($numbers, min($numbers));
            
            $purchases=DB::table('purchases')->where('id', $stdClass[$min[0]]->id)->first();
            try
            {
                $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . 'inv/best_price']);
                $reqBody = [
                    'headers' => $headers,
                    'json' => [
                        'm_supplier_id' => $stdClass[$min[0]]->m_supplier_id,
                        'm_item_id' => $m_item_id[$j],
                        'best_price' => ($purchases->with_ppn == true ? ($stdClass[$min[0]]->base_price / 1.1) : $stdClass[$min[0]]->base_price)
                    ]
                ]; 
                $response = $client->request('POST', '', $reqBody); 
                $body = $response->getBody();
                $content = $body->getContents();
                $response_array = json_decode($content,TRUE);
                $data = $response_array['data'];
            } catch(RequestException $exception) {
            }
        }
        }
        $notification = array(
            'message' => 'Success purchase material',
            'alert-type' => 'success'
        );

        return redirect('pembelian')->with($notification);
    }

    public function getMaterialPembelianRutin(Request $request){
        // dd($request->rab_no);
        $response = null;
        try
        {
            if (!isset($request->rab_no)){
                $headers = [
                    'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                    'Accept'        => 'application/json',
                ];
                $client = new Client(['base_uri' => $this->base_api_url . 'rab/get_material_pembelian_rutin?site_id='.$this->site_id]);
            }else{
                    $headers = [
                    'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                    'Accept'        => 'application/json',
                ];
                $client = new Client(['base_uri' => $this->base_api_url . 'rab/get_material_pembelian_rutin?site_id='.$this->site_id.'&rab_no=' . $request->rab_no]);
            }
            $response = $client->request('GET', '', ['headers' => $headers]);  
            $body = $response->getBody();
            $content = $body->getContents();
            $response_array = json_decode($content,TRUE);

            $response = $content;         
        } catch(RequestException $exception) {
            
        }    

        return $response;
    }

    public function getAllSupplier(){
        $response = null;
        try
        {
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . 'inv/base/MSupplier']);  
            $response = $client->request('GET', '', ['headers' => $headers]);  
            $body = $response->getBody();
            $content = $body->getContents();
            $response_array = json_decode($content,TRUE);

            $response = $content;         
        } catch(RequestException $exception) {
            
        }    

        return $response;
    }

    public function generateTransactionNo($trasaction_code, $period_year, $period_month, $site_id){
        try
        {
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . 'master/m_sequence/generate_trx_no']);
            $reqBody = [
                'headers' => $headers,
                'json' => [
                    'transaction_code' => $trasaction_code,
                    'period_year' => $period_year,
                    'period_month' => $period_month,
                    'site_id' => $site_id
                   ]
               ]; 
            $response = $client->request('POST', '', $reqBody); 
            $body = $response->getBody();
            $content = $body->getContents();
            $response_array = json_decode($content,TRUE);
            $data = $response_array['data'];
        } catch(RequestException $exception) {
            // $is_error = true;
            // $error_message .= $exception->getMessage();
        } 

        return $data['transaction_number'];
    }

    public function getPoCanceled(){
        $response = null;
        try
        {
            if (!isset($request->po_no)){
                    $headers = [
                    'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                    'Accept'        => 'application/json',
                ];
                $client = new Client(['base_uri' => $this->base_api_url . 'inv/po_canceled?site_id='.$this->site_id]);  
            }else{
                    $headers = [
                    'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                    'Accept'        => 'application/json',
                ];
                $client = new Client(['base_uri' => $this->base_api_url . 'inv/po_canceled?site_id='.$this->site_id.'&po_no=' . $request->po_no]);  
            }
            $response = $client->request('GET', '', ['headers' => $headers]);  
            $body = $response->getBody();
            $content = $body->getContents();
            $response_array = json_decode($content,TRUE);

            $response = $content;         
        } catch(RequestException $exception) {
            
        }    

        return $response;
    }
    public function getBestPrices($id){
        $query['data']=DB::table('m_best_prices')
                            ->where('m_item_id', $id)
                            ->join('m_suppliers', 'm_suppliers.id', '=', 'm_best_prices.m_supplier_id')
                            ->first();
        return $query;
    }
    public function getMaterialRequestSuggestion(){
        $response = null;
        try
        {
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . 'inv/get_material_request_suggestion?site_id='.$this->site_id]);  
            $response = $client->request('GET', '', ['headers' => $headers]);  
            $body = $response->getBody();
            $content = $body->getContents();
            $response_array = json_decode($content,TRUE);

            $response = $content;         
        } catch(RequestException $exception) {
            
        }    

        return $response;
    }

    public function importMaterialPost(Request $request) 
    {
        // dd($request);

        // validasi
        $this->validate($request, [
            'file' => 'required|mimes:csv,xls,xlsx'
        ]);
 
        // menangkap file excel
        $file = $request->file;
        // membuat nama file unik
        $nama_file = rand().$file->getClientOriginalName();
        
        // upload ke folder file_siswa di dalam folder public
        $file->move('import_excel', $nama_file);
 
        // import data
        $array = Excel::toArray(new ExcelDataImport, public_path('/import_excel/'.$nama_file));
        
        $data = array();

        foreach ($array[0] as $key => $value) {
            if ($key > 0) {
                $m_item_no = $value[0];

                $m_items = DB::table('m_items')
                ->join('m_units', 'm_items.m_unit_id', 'm_units.id')
                ->where('m_items.no', $m_item_no)
                ->select('m_items.*', 'm_units.id as m_unit_id', 'm_units.name as m_unit_name')
                ->first();

                if ($m_items != null) {
                    array_push($data, [
                        'm_item_id' => $m_items->id,
                        'm_item_no' => $m_items->no,
                        'm_item_name' => $m_items->name,
                        'm_unit_id' => $m_items->m_unit_id,
                        'm_unit_name' => $m_items->m_unit_name,
                        'volume' => $value[1],
                        'harga_supplier' => $value[2],
                        'note' => $value[3],
                    ]);
                }

            }
        }
        unlink(public_path('/import_excel/'.$nama_file));

        return response()->json(['data'=>$data]);
    }
    public function formSignaturePO(Request $request, $id){
        // dd($request->file);
        try
        {
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
                        'contents' => $this->user_id
                    ],
                    [
                        'Content-type' => 'multipart/form-data',
                        'name'     => 'file',
                        'contents' => fopen($request->file, 'r'),
                        'filename' => 'PO_REQUEST_signature_'.$id.'.png'
                    ]
                ]
            ]; 
            $response = $client->request('POST', '', $reqBody);
            $body = $response->getBody();
            $content = $body->getContents();
            $response_array = json_decode($content,TRUE);
            $upload_data = $response_array;
            // echo json_encode($purchase);
        } catch(RequestException $exception) {
        }
        return $upload_data;
    }
}
