<?php

namespace App\Models;

class MBestPrice extends _BaseModel
{
    public function MSupplier()
    {
        return $this->belongsTo(MSupplier::class);
    }
    public function MItem()
    {
        return $this->belongsTo(MItem::class);
    }
}