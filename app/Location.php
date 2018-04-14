<?php
namespace App;

use Hyn\Tenancy\Traits\UsesTenantConnection;
use Illuminate\Database\Eloquent\Model;

/**
 * @SWG\Definition()
 */
class Location extends Model
{
    use UsesTenantConnection;

    /**
     * @SWG\Property(
     * property="id",
     * type="integer",
     * description="Primary key id"
     * )
     * @SWG\Property(
     * property="chain_id",
     * type="integer",
     * default=null,
     * description="Chain id"
     * )
     * @SWG\Property(
     * property="lname",
     * type="string",
     * minimum="1",
     * maximum="60",
     * description="Location name"
     * )
     * @SWG\Property(
     * property="generic_name",
     * type="string",
     * maximum="60",
     * description="Generic location name, used for hiding actual location name"
     * )
     * @SWG\Property(
     * property="address",
     * type="string",
     * maximum="100",
     * description="Location address"
     * )
     * @SWG\Property(
     * property="lat",
     * type="double",
     * description="Latitude"
     * )
     * @SWG\Property(
     * property="lon",
     * type="double",
     * description="Longitude"
     * )
     * @SWG\Property(
     * property="location_number",
     * type="string",
     * maximum="10",
     * description="Custom location number"
     * )
     * @SWG\Property(
     * property="notes",
     * type="mediumtext",
     * description="General notes"
     * )
     * @SWG\Property(
     * property="created_at",
     * type="datetime",
     * description="Record creation timestamp"
     * )
     * @SWG\Property(
     * property="updated_at",
     * type="datetime",
     * description="Record update timestamp"
     * )
     */
    public function chain()
    {
        return $this->belongsTo('App\Chain');
    }

    public function shift()
    {
        return $this->hasMany('App\Shift');
    }
}
