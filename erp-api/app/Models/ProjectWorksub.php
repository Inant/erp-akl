<?php

namespace App\Models;

class ProjectWorksub extends _BaseModel
{
    public function ProjectWorksubDs()
    {
        return $this->hasMany(ProjectWorksubD::class);
    }
}