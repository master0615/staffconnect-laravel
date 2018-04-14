<?php
namespace App;

use Hyn\Tenancy\Traits\UsesTenantConnection;
use Illuminate\Database\Eloquent\Model;

/**
 * @SWG\Definition()
 */
class Device extends Model
{
    use UsesTenantConnection;

    public function user()
    {
        return $this->belongsTo('App\User');
    }
}
