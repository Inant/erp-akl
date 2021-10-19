<?php

namespace App\Models;

class CustomerBill extends _BaseModel
{
    public function Customer()
    {
        return $this->belongsTo(Customer::class);
    }
}