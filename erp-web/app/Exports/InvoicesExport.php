<?php
namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use DB;

class InvoicesExport implements FromView
{
    public function view(): View
    {
        $site_id = auth()->user()['site_id'];
        $date = request('date');
        $date2 = request('date2');
        $list_trx=DB::table('tbl_trx_akuntansi')
                    ->where('tanggal', '>=', $date)
                    ->where('tanggal','<=', $date2)
                    ->orderBy('dtm_crt', 'DESC');
        if ($site_id != null) {
            $list_trx->where('location_id', $site_id);
        }
        $results=$list_trx->get();
        
        $data=array();
        foreach ($results as $key => $value) {
            $data[$key]=array(
                'id_trx_akun' => $value->id_trx_akun,
                'deskripsi' => $value->deskripsi,
                'tanggal'   => $value->tanggal
            );
            $data[$key]['detail']=$this->getDetailKas($value->id_trx_akun);
        }
        return view('exports.export_journal', [
            'data' => $data,
            'date'  => $date,
            'date2' => $date2
        ]);
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
}