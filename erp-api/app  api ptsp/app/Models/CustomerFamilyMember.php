<?php

namespace App\Models;

class CustomerFamilyMember extends _BaseModel
{
    public function Customer()
    {
        return $this->hasOne(Customer::class,'id','customer_id');
    }
}