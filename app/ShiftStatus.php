<?php

namespace App;

use Hyn\Tenancy\Traits\UsesTenantConnection;
use Illuminate\Database\Eloquent\Model;

class ShiftStatus extends Model
{
    use UsesTenantConnection;

    public $timestamps = false;

    public function shift()
    {
        return $this->hasMany('App\Shift');
    }
}
