<?php
namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use App\Http\Controllers\Accounting\AkuntanController;
use DB;

class NeracaExport implements FromView
{
    public function view(): View
    {
        $site_id = auth()->user()['site_id'];
        $date1 = request('date');
        $date2 = request('date2');
        $acccon = new AkuntanController();
        $asset=$acccon->getSaldoAccount(null, $date1, $date2, $site_id, null);
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
            $getHppProduksi=$acccon->countSaldoAccountByParent(84, '2021-04-20', '2021-04-20', $site_id);
            $hppProduksi = $getHppProduksi['detail_month']->total_debit;
        }
        // $exp_bulan=explode('-', $date);
        // $jumlah_hari=cal_days_in_month(CAL_GREGORIAN, $exp_bulan[1], $exp_bulan[0]);
        $data=array(
            'date1'      => $date1,
            'date2'      => $date2,
            'saldo'     => $asset,
            // 'jumlah_hari'   => $jumlah_hari,
            'parent'    => $parent,
            'hppProduksi' => $hppProduksi,
        );
        return view('exports.export_neraca', [
            'data' => $data,
        ]);
    }
}