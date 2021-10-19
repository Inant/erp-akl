<?php

namespace App\Models;

class RabRequest extends _BaseModel
{
    public function RabRequestDs()
    {
        return $this->hasMany(RabRequestD::class);
    }

    public function Project()
    {
        return $this->belongsTo(Project::class);
    }
}