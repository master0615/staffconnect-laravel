<?php
namespace App;

use Hyn\Tenancy\Traits\UsesTenantConnection;
use Illuminate\Database\Eloquent\Model;

/**
 * @SWG\Definition()
 */
class TrackingOption extends Model
{
    use UsesTenantConnection;

    /**
     * @SWG\Property(
     * property="id",
     * type="integer",
     * description="Primary key id"
     * )
     * @SWG\Property(
     * property="tracking_cat_id",
     * type="integer",
     * description="Id of tracking category the option belongs to"
     * )
     * @SWG\Property(
     * property="oname",
     * type="string",
     * minimum="1",
     * maximum="60",
     * description="Option name eg. ABC123"
     * )
     * @SWG\Property(
     * property="staff_visibility",
     * type="enum",
     * default="all",
     * enum={"all","team"},
     * description="Sets visibility to all staff or team only"
     * )
     * @SWG\Property(
     * property="active",
     * type="boolean",
     * default="1",
     * description="Sets visibility in search and filters"
     * )
     */
    public $timestamps = false;

    protected $guarded = [];

    public function shiftTrackigOption()
    {
        return $this->hasMany('App\ShiftTrackingOption');
    }

    public function trackingCategory()
    {
        return $this->belongsTo('App\TrackingCategory');
    }
}
