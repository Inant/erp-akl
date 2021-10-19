<?php

namespace App\Models;

class ProjectWork extends _BaseModel
{
    public function ProjectWorksubs()
    {
        return $this->hasMany(ProjectWorksub::class);
    }
}