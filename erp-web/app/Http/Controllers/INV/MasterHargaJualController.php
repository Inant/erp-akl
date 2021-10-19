<?php

namespace App\Http\Controllers\INV;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Yajra\DataTables\Facades\DataTables;
use DB;
use Illuminate\Support\Facades\DB as FacadesDB;

class MasterHargaJualController extends Controller
{
    private $base_api_url;
    private $site_id = null;
    public function __construct()
    {
        //Authenticate page menu
        $this->middleware(function ($request, $next) {
            Controller::isLogin(auth()->user()['role_id']);
            $this->site_id = auth()->user()['site_id'];
            return $next($request);
        });

        $this->base_api_url = env('API_URL');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('pages.inv.master_harga_jual.master_harga_jual_list');
    }

    public function GetItemJson()
    {
        $query = DB::table('m_harga_jual')->select('m_items.*', 'm_harga_jual.*')
                ->join('m_items', 'm_items.id', 'm_harga_jual.m_item_id')
                ->whereNull('deleted_at')
                ->get();
        foreach ($query as $key => $value) {
            $value->m_units = DB::table('m_units')->select('id', 'name')->where('id', $value->m_unit_id)->first();
            $value->m_unit_childs = DB::table('m_units')->select('id', 'name')->where('id', $value->m_unit_child)->first();
            $value->item_set = DB::table('m_items')->select('id', 'name')->where('id', $value->m_group_item_id)->first();
        }
        $data = DataTables::of($query)
            ->make(true);
        return $data;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $mItem = DB::table('m_items')->select('id', 'no')->get();

        return view('pages.inv.master_harga_jual.master_harga_jual_create', compact('mItem', $mItem));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        DB::table('m_harga_jual')->insert([
            'm_item_id' => $request->material,
            'retail' => $request->retail,
            'grosir' => $request->grosir,
            'distributor' => $request->distributor,
        ]);

        $notification = array(
            'message' => 'Success make harga jual',
            'alert-type' => 'success'
        );

        return redirect('master_harga_jual')->with($notification);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $mItem = DB::table('m_items')->select('id', 'no')->get();
        
        $data = DB::table('m_harga_jual')->where('id', $id)->first();

        return view('pages.inv.master_harga_jual.master_harga_jual_edit', compact([
            'data', $data,
            'mItem', $mItem,
        ]));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        DB::table('m_harga_jual')->where('id', $id)->update([
            'm_item_id' => $request->material,
            'retail' => $request->retail,
            'grosir' => $request->grosir,
            'distributor' => $request->distributor,
        ]);

        $notification = array(
            'message' => 'Success menyimpan data.',
            'alert-type' => 'success'
        );

        return redirect('master_harga_jual')->with($notification);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        DB::table('m_harga_jual')->where('id', $id)->delete();

        $notification = array(
            'message' => 'Success menghapus data.',
            'alert-type' => 'success'
        );

        return redirect('master_harga_jual')->with($notification);
    }
}
