<?php

namespace App\Http\Controllers\INV;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Carbon\Carbon;
use DB;

class PembelianAssetController extends Controller
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
        
        return view('pages.inv.pembelian_asset.pembelian_asset_create', $data);
    }

    public function createPost(Request $request){

        $project_worksub_d_id = $request->post('selected_project_worksub_d_id'); //order dari rab
        $purchase_d_id = $request->post('selected_purchase_d_id'); // order dari po canceled
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

            try
            {
                $headers = [
                    'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                    'Accept'        => 'application/json',
                ];
                $client = new Client(['base_uri' => $this->base_api_url . 'inv/base/PurchaseAsset']);
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
                        'is_without_ppn'   => $request->input('without_ppn') ? 1 : 0,
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
                // echo json_encode($purchase);
            } catch(RequestException $exception) {
            }
            // if ($wopPo[$i] == 'cash' && empty($request->input('with_ao'))) {
            //     $bill_no = $this->generateTransactionNo('PAID_SPPL', $period_year, $period_month, $this->site_id );
            //     try
            //     {
            //         $client = new Client(['base_uri' => $this->base_api_url . 'inv/base/PaymentSupplier']);
            //         $reqBody = [
            //             'headers' => $headers,
                // 'json' => [
            //                 'purchase_id' => 0,
            //                 'purchase_asset_id' => $purchase['id'],
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
                        $client = new Client(['base_uri' => $this->base_api_url . 'inv/base/PurchaseAssetD']);
                        $reqBody = [
                            'headers' => $headers,
                            'json' => [
                                'purchase_asset_id' => $purchase['id'],
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
                }
            }
        }
        
        if (empty($request->input('with_ao'))) {
        //update base price
        for($j = 0; $j < count($m_item_id); $j++){
            $query=DB::table('purchase_asset_ds')
                        ->join('purchase_assets', 'purchase_assets.id', '=', 'purchase_asset_ds.purchase_asset_id')
                        ->where('m_item_id', $m_item_id[$j])
                        ->select('purchase_assets.m_supplier_id', 'purchase_asset_ds.base_price', 'purchase_assets.id')
                        ->limit(3)
                        ->orderBy('purchase_asset_ds.id', 'DESC')
                        ->get();
            $stdClass = json_decode(json_encode($query));
            $numbers = array_column($stdClass, 'base_price');
            $min = array_keys($numbers, min($numbers));

            $purchases=DB::table('purchase_assets')->where('id', $stdClass[$min[0]]->id)->first();
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

        return redirect('pembelian_asset')->with($notification);
    }
    public function generateTransactionNo($trasaction_code, $period_year, $period_month, $site_id){
        try
        {
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
            ];
            $client = new Client(['base_uri' => $this->base_api_url . '/master/m_sequence/generate_trx_no']);
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
}
