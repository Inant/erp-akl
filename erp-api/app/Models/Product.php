<?php

namespace App\Models;

class Product extends _BaseModel
{
    public function ProductSubs()
    {
        return $this->hasMany(ProductSub::class);
    }
}