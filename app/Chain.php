<?php
namespace App;

use Hyn\Tenancy\Traits\UsesTenantConnection;
use Illuminate\Database\Eloquent\Model;

/**
 * @SWG\Definition()
 */
class Chain extends Model
{
    use UsesTenantConnection;

    /**
     * @SWG\Property(
     * property="id",
     * type="integer",
     * description="The primary key id"
     * )
     * @SWG\Property(
     * property="cname",
     * type="string",
     * minimum="1",
     * maximum="40",
     * description="The chain name"
     * )
     * @SWG\Property(
     * property="created_at",
     * type="datetime",
     * description="Record creation timestamp"
     * )
     */
    public function location()
    {
        return $this->hasMany('App\Location');
    }
}
