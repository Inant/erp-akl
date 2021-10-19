<?php

namespace App\Models;

class MTown extends _BaseModel
{
    public function Projects()
    {
        return $this->hasMany(Site::class);
    }
}