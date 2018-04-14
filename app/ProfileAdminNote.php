<?php

namespace App;

use Hyn\Tenancy\Traits\UsesTenantConnection;
use Illuminate\Database\Eloquent\Model;

class ProfileAdminNote extends Model
{
    use UsesTenantConnection;

    public function creator()
    {
        return $this->belongsTo('App\User', 'creator_id');
    }

    public function user()
    {
        return $this->belongsTo('App\User');
    }
}
