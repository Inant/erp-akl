<?php

namespace App\Models;

class SaleTrxKprBankPayment extends _BaseModel
{
    public function SaleTrx()
    {
        return $this->belongsTo(SaleTrx::class);
    }
}