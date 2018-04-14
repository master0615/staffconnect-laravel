<?php
namespace App;

use Hyn\Tenancy\Traits\UsesTenantConnection;
use Illuminate\Database\Eloquent\Model;

/**
 * @SWG\Definition()
 */
class Client extends Model
{
    use UsesTenantConnection;

    /**
     * @SWG\Property(
     * property="id",
     * type="integer",
     * description="Primary key id"
     * )
     * @SWG\Property(
     * property="cname",
     * type="string",
     * minimum="1",
     * maximum="50",
     * description="Client name"
     * )
     */
    public $timestamps = false;

    public function clientUser()
    {
        return $this->hasMany('App\ClientUser');
    }

    public function shift()
    {
        return $this->hasMany('App\Shift');
    }

    public function users()
    {
        return $this->belongsToMany('App\User')->using('App\ClientUser');
    }
}
