<?php
namespace App;

use Hyn\Tenancy\Traits\UsesTenantConnection;
use Illuminate\Database\Eloquent\Relations\Pivot;

class FlagShift extends Pivot
{
    use UsesTenantConnection;

    public $timestamps = false;

    public function flag()
    {
        return $this->belongsTo('App\Flag');
    }

    public function shift()
    {
        return $this->belongsTo('App\Shift');
    }
}
