<?php

namespace App\Models;

class Rab extends _BaseModel
{
    public function ProjectWorks()
    {
        return $this->hasMany(ProjectWork::class);
    }
    public function Project()
    {
        return $this->belongsTo(Project::class);
    }
}