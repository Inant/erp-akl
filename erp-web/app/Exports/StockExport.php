<?php
namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use DB;

class StockExport implements FromView
{
    protected $data;

    function __construct($data) {
            $this->data = $data;
    }
    public function view(): View
    {
        return view('exports.export_stock', [
            'data' => $this->data,
        ]);
    }
}