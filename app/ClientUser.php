<?php
namespace App;

use Hyn\Tenancy\Traits\UsesTenantConnection;
use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * @SWG\Definition()
 */
class ClientUser extends Pivot
{
    use UsesTenantConnection;

    /**
     * @SWG\Property(
     * property="id",
     * type="integer",
     * description="Primary key id"
     * )
     * @SWG\Property(
     * property="user_id",
     * type="integer",
     * description="User id"
     * )
     * @SWG\Property(
     * property="client_id",
     * type="integer",
     * description="Client id"
     * )
     */
    public $timestamps = false;

    public function client()
    {
        return $this->belongsTo('App\Client');
    }

    public function user()
    {
        return $this->belongsTo('App\User');
    }
}
