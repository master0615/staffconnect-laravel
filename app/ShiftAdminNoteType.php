<?php

namespace App;

use Hyn\Tenancy\Traits\UsesTenantConnection;
use Illuminate\Database\Eloquent\Model;

class ShiftAdminNoteType extends Model
{
    use UsesTenantConnection;

    public $timestamps = false;

    protected $guarded = [];

    public function notes()
    {
        return $this->hasMany('App\ShiftAdminNote');
    }
}
