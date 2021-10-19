<?php

namespace App\Models;

class FollowupHistory extends _BaseModel
{
    public function SaleTrxes()
    {
        return $this->hasMany(SaleTrx::class,'follow_history_id');
    }
}