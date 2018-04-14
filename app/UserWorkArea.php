<?php
namespace App;

use Hyn\Tenancy\Traits\UsesTenantConnection;
use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * @SWG\Definition()
 */
class UserWorkArea extends Pivot
{
    use UsesTenantConnection;

    /**
     * @SWG\Property(
     * property="id",
     * type="integer",
     * description="Primary key id"
     * )
     * @SWG\Property(
     * property="work_area_id",
     * type="integer",
     * description="Work area id"
     * )
     * @SWG\Property(
     * property="user_id",
     * type="integer",
     * description="User id"
     * )
     */
    public $timestamps = false;

    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function workArea()
    {
        return $this->belongsTo('App\WorkArea');
    }
}
