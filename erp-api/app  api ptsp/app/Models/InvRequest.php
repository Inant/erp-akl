<?php

namespace App\Models;

class InvRequest extends _BaseModel
{
    public function InvRequestDs()
    {
        return $this->hasMany(InvRequestD::class);
    }
}