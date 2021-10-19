<?php

namespace App\Models;

class SaleTrxD extends _BaseModel
{
    public function SaleTrx()
    {
        return $this->belongsTo(SaleTrx::class);
    }
}