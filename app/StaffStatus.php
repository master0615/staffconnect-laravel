<?php

namespace App;

use Hyn\Tenancy\Traits\UsesTenantConnection;
use Illuminate\Database\Eloquent\Model;

class StaffStatus extends Model
{
    use UsesTenantConnection;

    public $timestamps = false;

    public function roleStaff()
    {
        return $this->hasMany('App\RoleStaff');
    }
}
