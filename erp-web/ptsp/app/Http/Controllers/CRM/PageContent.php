<?php

namespace App\Http\Controllers\CRM;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Exception;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Redirect;
use Carbon\Carbon;

class PageContent extends Controller
{
    private $base_api_url;
    public function __construct()
    {
        //Authenticate page menu
        // $this->middleware(function ($request, $next) {
        //     Controller::isLogin(auth()->user()['role_id']); 
        //     $this->site_id = auth()->user()['site_id'];
        //     $this->user_name = auth()->user()['name'];
            
        //     return $next($request);
        // });

        $this->base_api_url = env('API_URL');
    }
    public function getPageContent($category, $id=null)
    {
        $object = null;
        try
        {
            //spu fee
            if ($category == 'cust')
            {
                $url_destination = 'crm/customerdata/'.$id;
            }
            else if ($category == 'spufee')
            {
                $url_destination = 'base/gs/code/spufee';
            }
            else if ($category =='pbhtbpercent')
            {
                $url_destination = 'base/gs/code/pbhtbpercent';
            }
            else if ($category =='pphpercent')
            {
                $url_destination = 'base/gs/code/pphpercent';
            }
            else if ($category =='notaryfee')
            {
                $url_destination = 'base/gs/code/notaryfee';
            }
            else if ($category =='fasumfee')
            {
                $url_destination = 'base/gs/code/fasumfee';
            }
            else if ($category == 'projectbyid')
            {
                $url_destination = 'rab/base/Project/'.$id;
            }
            else if ($category == 'followuphistories')
            {
                $url_destination = 'crm/followuphistories/last/'.$id;
            }
            else if ($category == 'spucust')
            {
                $url_destination = 'crm/ppjb/spu/cust/'.$id;
            }
            else if ($category == 'nupcust')
            {
                $url_destination = 'crm/spu/nup/cust/'.$id;
            }
            else if ($category == 'bokcust')
            {
                $url_destination = 'crm/spu/bok/cust/'.$id;
            }
            else if ($category == 'spudata')
            {
                $url_destination = 'crm/ppjb/data/spu/'.$id;
            } 
            else if ($category == 'nupdata')
            {
                $url_destination = 'crm/request/data/nup/'.$id;
            }

            else if ($category == 'kprbank')
            {
                $url_destination = 'crm/kprbank/scheme/bankname/'.$id;
            }
            else if ($category == 'specup')
            {
                $url_destination = 'crm/request/specup/project/'.$id;
            }
            $client = new Client(['base_uri' => $this->base_api_url . $url_destination]);  
            $response = $client->request('GET', ''); 
            $body = $response->getBody();
            $content =$body->getContents();
            // $response_array = json_decode($content,TRUE);
            // $object = $response_array['data'][0];
            $object = $content;

        } catch(RequestException $exception) {
            $this->is_error = true;
            $this->error_message = $exception->getMessage();
            throw new Exception($this->error_message);
        }  
        

        return $object;
    }
}
