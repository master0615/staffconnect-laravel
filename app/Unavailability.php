<?php
namespace App;

use Hyn\Tenancy\Traits\UsesTenantConnection;
use Illuminate\Database\Eloquent\Model;

/**
 * @SWG\Definition()
 */
class Unavailability extends Model
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
     * description="Id of user the unavailability belongs to"
     * )
     * @SWG\Property(
     * property="title",
     * type="string",
     * minimum="1",
     * maximum="30",
     * description="Title eg. Holiday"
     * )
     * @SWG\Property(
     * property="weekday",
     * type="integer",
     * maximum="7",
     * description="null for irregular unavailability otherwise indicates day of week for recurring unavailability"
     * )
     * @SWG\Property(
     * property="ua_start",
     * type="datetime",
     * description="Start datetime"
     * )
     * @SWG\Property(
     * property="ua_end",
     * type="datetime",
     * description="End datetime"
     * )
     */
    public $timestamps = false;

    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo('App\User');
    }
}
