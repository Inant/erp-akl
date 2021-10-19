<?php
namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use App\Http\Controllers\Accounting\AkuntanController;
use DB;

class NeracaSaldoExport implements FromView
{
    public function view(): View
    {
        $location_id = auth()->user()['site_id'];
        $date1=request('date');
        $date2=request('date2');
        $acccon = new AkuntanController();
        $data_akun=DB::table('tbl_akun')->where('level', 1)->orderBy('no_akun')->get();
        foreach ($data_akun as $k => $v) {
            $v->detail=$acccon->countSaldoAccountByParent($v->id_akun, $date1, $date2, $location_id);
            $v->child=DB::table('tbl_akun')->where('level', 2)->where('id_main_akun', $v->id_akun)->get();            
            foreach ($v->child as $k2 => $v2) {
                $v2->detail=$acccon->countSaldoAccountByParent($v2->id_akun, $date1, $date2, $location_id);
                $v2->child=DB::table('tbl_akun')->where('level', 3)->where('id_main_akun', $v2->id_akun)->get();
                foreach ($v2->child as $k3 => $v3) {
                    $v3->detail=$acccon->countSaldoAccountByParent($v3->id_akun, $date1, $date2, $location_id);
                    $v3->child=DB::table('tbl_akun')->where('level', 4)->where('id_main_akun', $v3->id_akun)->get();
                    foreach ($v3->child as $k4 => $v4) {
                        $v4->detail=$acccon->countSaldoAccountByParent($v4->id_akun, $date1, $date2, $location_id);
                    }
                }
            }
        }
        
        $data=array(
            'date1'     => $date1,
            'date2'     => $date2,
            'data'     => $data_akun
        );
        return view('exports.export_neraca_saldo', [
            'data' => $data,
        ]);
    }
}