<?php

namespace App\Models;

class Order extends _BaseModel
{
    public function OrderDs()
    {
        return $this->hasMany(OrderD::class);
    }
}