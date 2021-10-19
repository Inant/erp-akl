<?php

namespace App\Models;

class MEmployee extends _BaseModel
{
    public function SaleTrxs()
    {
        return $this->hasMany(SaleTrx::class);
    }
}