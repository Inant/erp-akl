<?php

namespace App\Models;

class CustomerFamily extends _BaseModel
{
    public function Customers()
    {
        return $this->hasMany(Customer::class);
    }
    public function CustomerFamilyMembers()
    {
        return $this->hasMany(CustomerFamilyMember::class);
    }
}