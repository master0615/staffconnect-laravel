<?php
namespace App;

use Hyn\Tenancy\Traits\UsesTenantConnection;
use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * @SWG\Definition()
 */
class PayLevelUser extends Pivot
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
     * property="pay_level_id",
     * type="integer",
     * description="Pay level id"
     * )
     */
    public $timestamps = false;

    protected $guarded = [];

    public function payLevel()
    {
        return $this->belongsTo('App\PayLevel');
    }

    public function user()
    {
        return $this->belongsTo('App\User');
    }
}
