<?php

namespace App;

use Hyn\Tenancy\Traits\UsesTenantConnection;
use Illuminate\Database\Eloquent\Model;

class StaffCheck extends Model
{
    use UsesTenantConnection;

    public function roleStaff()
    {
        return $this->belongsTo('App\RoleStaff');
    }

    public function checker()
    {
        return $this->belongsTo('App\User', 'checker_id', 'id', 'users');
    }
}
