<?php

namespace App;

use Hyn\Tenancy\Traits\UsesTenantConnection;
use Illuminate\Database\Eloquent\Model;

class ApplicationReason extends Model
{
    use UsesTenantConnection;

    public $timestamps = false;

    protected $guarded = [];

    public function roleStaff()
    {
        return $this->belongsTo('App\RoleStaff');
    }
}
