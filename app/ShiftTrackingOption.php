<?php
namespace App;

use Hyn\Tenancy\Traits\UsesTenantConnection;
use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * @SWG\Definition()
 */
class ShiftTrackingOption extends Pivot
{
    use UsesTenantConnection;

    /**
     * @SWG\Property(
     * property="id",
     * type="integer",
     * description="Primary key id"
     * )
     * @SWG\Property(
     * property="shift_id",
     * type="integer",
     * description="Shift id"
     * )
     * @SWG\Property(
     * property="tracking_option_id",
     * type="integer",
     * description="Tracking option id"
     * )
     */
    public $timestamps = false;

    protected $guarded = [];

    public function shift()
    {
        return $this->belongsTo('App\Shift');
    }

    public function trackingOption()
    {
        return $this->belongsTo('App\TrackingOption');
    }
}
