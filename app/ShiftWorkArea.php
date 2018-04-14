<?php
namespace App;

use Hyn\Tenancy\Traits\UsesTenantConnection;
use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * @SWG\Definition()
 */
class ShiftWorkArea extends Pivot
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
     * property="work_area_id",
     * type="integer",
     * description="Work area id"
     * )
     */
    public $timestamps = false;

    protected $guarded = [];

    public function shift()
    {
        return $this->belongsTo('App\Shift');
    }

    public function workArea()
    {
        return $this->belongsTo('App\WorkArea');
    }
}
