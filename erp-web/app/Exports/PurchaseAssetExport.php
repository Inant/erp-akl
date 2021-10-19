<?php
namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use DB;

class PurchaseAssetExport implements FromView
{
    protected $data;

    function __construct($data) {
            $this->data = $data;
    }
    public function view(): View
    {
        return view('exports.export_purchase_asset', [
            'data' => $this->data,
        ]);
    }
}