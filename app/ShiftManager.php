<?php
namespace App;

use Hyn\Tenancy\Traits\UsesTenantConnection;
use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * @SWG\Definition()
 */
class ShiftManager extends Pivot
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
     * property="user_id",
     * type="integer",
     * description="User id"
     * )
     */
    public $timestamps = false;

    protected $guarded = [];

    public function shift()
    {
        return $this->belongsTo('App\Shift');
    }

    public function user()
    {
        return $this->belongsTo('App\User');
    }
}
