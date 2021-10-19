<?php

namespace App\Models;

class Purchase extends _BaseModel
{
    public function PurchaseDs()
    {
        return $this->hasMany(PurchaseD::class);
    }
}