<?php

namespace App\Http\Controllers\INV;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Carbon\Carbon;
use DB;

class PembelianKhususController extends Controller
{
    private $base_api_url;
    public function __construct()
    {
        //Authenticate page menu
        $this->middleware(function ($request, $next) {
            Controller::isLogin(auth()->user()['role_id']); 
            return $next($request);
        });

        $this->base_api_url = env('API_URL');
    }
    
    public function index()
    {
        $rab = null;
        $is_error = false;
        $error_message = '';

        $data = array(
            'error' => array(
                'is_error' => $is_error,
                'error_message' => $error_message
            )
        );
        
        return view('pages.inv.pembelian_khusus.pembelian_khusus_list', $data);
    }

    public function pembelianKhusus($id)
    {
        $is_error = false;
        $error_message = '';  

        $purchase = $this->getPurchaseById($id);
        $purchase = json_decode($purchase, TRUE);
        $purchase = $purchase['data'];

        $data = array(
            'error' => array(
                'is_error' => $is_error,
                'error_message' => $error_message
            ),
            'purchase' => $purchase
        );

        // echo json_encode($purchase);
        
        return view('pages.inv.pembelian_khusus.pembelian_khusus_create', $data);
    }

    public function pembelianKhususPost(Request $request)
    {
        $purchase_id = $request->post('purchase_id');
        $suppl = $request->post('suppl');
        $wop = $request->post('cara_bayar');

        //array
        $purchase_d_id = $request->post('purchase_d_id');
        $volume = $request->post('amount');
        $perkiraan_harga_suppl = $request->post('perkiraan_harga_suppl');

        DB::beginTransaction();
        try
        {
            $sum_perkiraan_harga_suppl = 0;
            for($j = 0; $j < count($perkiraan_harga_suppl); $j++){
                $sum_perkiraan_harga_suppl += ($perkiraan_harga_suppl[$j]*$volume[$j]);
            }

            try
            {
                $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
 ];
$client = new Client(['base_uri' => $this->base_api_url . '/inv/base/Purchase/'.$purchase_id]);
                $reqBody = [
                    'headers' => $headers,
                'json' => [
                        'm_supplier_id' => $suppl,
                        'wop' => $wop,
                        'base_price' => $sum_perkiraan_harga_suppl,
                        'purchase_date' => Carbon::now()->toDateString()
                    ]
                ]; 
                $response = $client->request('PUT', '', $reqBody); 
                $body = $response->getBody();
                $content = $body->getContents();
                $response_array = json_decode($content,TRUE);

            } catch(RequestException $exception) {
            }

            for($i = 0; $i < count($purchase_d_id); $i++){
                try
                {
                    $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
 ];
$client = new Client(['base_uri' => $this->base_api_url . '/inv/base/PurchaseD/'.$purchase_d_id[$i]]);
                    $reqBody = [
                        'headers' => $headers,
                'json' => [
                            'base_price' => $perkiraan_harga_suppl[$i]
                        ]
                    ]; 
                    $response = $client->request('PUT', '', $reqBody); 
                    $body = $response->getBody();
                    $content = $body->getContents();
                    $response_array = json_decode($content,TRUE);

                } catch(RequestException $exception) {
                }
            }
        }
        catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }

        $notification = array(
            'message' => 'Success purchase material',
            'alert-type' => 'success'
        );

        return redirect('pembelian_khusus')->with($notification);
    }

    public function getPurchaseById($id){
        $response = null;
        try
        {
            $headers = [
                'Authorization' => 'Bearer ' . auth()->user()['remember_token'],        
                'Accept'        => 'application/json',
 ];
$client = new Client(['base_uri' => $this->base_api_url . '/inv/purchase/'.$id]);  
            $response = $client->request('GET', '', ['headers' => $headers]); 
            $body = $response->getBody();
            $content = $body->getContents();
            $response_array = json_decode($content,TRUE);

            $response = $content;         
        } catch(RequestException $exception) {
            
        }    

        return $response;
    }
}
