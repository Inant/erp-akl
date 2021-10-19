<?php

namespace App\Models;

class Site extends _BaseModel
{
    public function Projects()
    {
        return $this->hasMany(Project::class);
    }
    public function MCity()
    {
        return $this->belongsTo(MCity::class);
    }
}