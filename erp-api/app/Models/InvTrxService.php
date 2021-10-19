<?php

namespace App\Models;

class InvTrxService extends _BaseModel
{
    public function InvTrxServiceDs()
    {
        return $this->hasMany(InvTrxServiceD::class);
    }
}