<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class _BaseModel extends Model
{
    use SoftDeletes;
    protected $dates = ['deleted_at'];
    protected $guarded = ['created_at','updated_at','deleted_at' ];
    // protected $dateFormat = 'Y-m-d H:i:s.u';

    // public function getDateFormat()
    // {
    //     return 'Y-m-d H:i:s.u';
    // }
    // protected $dateFormat = 'Y-m-d\TH:i:s.u';

    protected function asDateTime($value)
    {
        try {
            return parent::asDateTime($value);
        } catch (\InvalidArgumentException $e) {
            return parent::asDateTime(new \DateTimeImmutable($value));
        }
    }

}