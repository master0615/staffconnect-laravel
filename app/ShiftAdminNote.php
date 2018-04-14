<?php

namespace App;

use Hyn\Tenancy\Traits\UsesTenantConnection;
use Illuminate\Database\Eloquent\Model;

class ShiftAdminNote extends Model
{
    use UsesTenantConnection;

    public function creator()
    {
        return $this->belongsTo('App\User', 'creator_id');
    }

    public function shift()
    {
        return $this->belongsTo('App\Shift');
    }

    public function type()
    {
        return $this->belongsTo('App\ShiftAdminNoteType', 'type_id');
    }
}
