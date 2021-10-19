<?php

namespace App\Models;

class Project extends _BaseModel
{
    public function ProjectWorks()
    {
        return $this->hasMany(ProjectWork::class);
    }
    public function Rabs()
    {
        return $this->hasMany(Rab::class);
    }
    public function Site()
    {
        return $this->belongsTo(Site::class);
    }
}