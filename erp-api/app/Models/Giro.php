<?php

namespace App\Models;

class Giro extends _BaseModel
{
    public function Site()
    {
        return $this->belongsTo(Site::class);
    }
    public function CustomerBill()
    {
        return $this->belongsTo(CustomerBill::class);
    }
    public function CustomerBillD()
    {
        return $this->belongsTo(CustomerBillD::class);
    }
    public function Order()
    {
        return $this->belongsTo(Order::class);
    }
    public function GiroD(){
        return $this->belongsTo(GiroD::class);
    }
}