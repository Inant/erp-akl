<?php

namespace App\Models;

class MCity extends _BaseModel
{
    public function Sites()
    {
        return $this->hasMany(Site::class);
    }
}