<?php

namespace App\Models;

class InvReturn extends _BaseModel
{
    public function InvRequestDs()
    {
        return $this->hasMany(InvRequestD::class);
    }
}