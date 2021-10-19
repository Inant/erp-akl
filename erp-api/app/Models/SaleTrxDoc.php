<?php

namespace App\Models;

class SaleTrxDoc extends _BaseModel
{
    public function SaleTrx()
    {
        return $this->belongsTo(SaleTrx::class);
    }
}