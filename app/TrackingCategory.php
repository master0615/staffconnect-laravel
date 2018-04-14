<?php
namespace App;

use Hyn\Tenancy\Traits\UsesTenantConnection;
use Illuminate\Database\Eloquent\Model;

/**
 * @SWG\Definition()
 */
class TrackingCategory extends Model
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
     * maximum="30",
     * description="Category name eg. Job Number"
     * )
     * @SWG\Property(
     * property="staff_visibility",
     * type="enum",
     * default="hidden",
     * enum={"hidden","visible","visible_after_selection"},
     * description="Controls visibility to staff"
     * )
     * @SWG\Property(
     * property="client_visibility",
     * type="enum",
     * default="hidden",
     * enum={"hidden","visible"},
     * description="Controls visibility to clients"
     * )
     * @SWG\Property(
     * property="required",
     * type="boolean",
     * default="0",
     * description="Controls if required at shift creation"
     * )
     */
    public $timestamps = false;

    protected $guarded = [];

    public function trackingOptions()
    {
        return $this->hasMany('App\TrackingOption', 'tracking_cat_id');
    }
}
