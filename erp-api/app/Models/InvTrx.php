<?php

namespace App\Models;

class InvTrx extends _BaseModel
{
    public function InvTrxDs()
    {
        return $this->hasMany(InvTrxD::class);
    }
}