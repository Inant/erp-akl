<?php

namespace App\Models;

class SaleTrx extends _BaseModel
{
    public function ProjectWorks()
    {
        return $this->hasMany(ProjectWork::class);
    }
    public function SaleTrxDs()
    {
        return $this->hasMany(SaleTrxD::class);
    }
    public function SaleTrxKprBankPayments()
    {
        return $this->hasMany(SaleTrxKprBankPayment::class);
    }

    public function MEmployee()
    {
        return $this->belongsTo(MEmployee::class);
    }
    public function Customer()
    {
        return $this->belongsTo(Customer::class);
    }
    public function Project()
    {
        return $this->belongsTo(Project::class);
    }
}