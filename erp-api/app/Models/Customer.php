<?php

namespace App\Models;

class Customer extends _BaseModel
{
    public function CustomerFinancials()
    {
        return $this->hasMany(CustomerFinancial::class);
    }

    public function CustomerFamily()
    {
        return $this->belongsTo(CustomerFamily::class);
    }

    public function FollowupHistories()
    {
        return $this->hasMany(FollowupHistory::class);
    }
}