<?php
namespace App;

use Hyn\Tenancy\Traits\UsesTenantConnection;
use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * @SWG\Definition()
 */
class RatingUser extends Pivot
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
     * property="rating_id",
     * type="integer",
     * description="Rating id"
     * )
     * @SWG\Property(
     * property="score",
     * type="integer",
     * minimum="1",
     * maximum="10",
     * description="Score"
     * )
     */
    public $timestamps = false;

    protected $guarded = [];

    public function rating()
    {
        return $this->belongsTo('App\Rating');
    }

    public function user()
    {
        return $this->belongsTo('App\User');
    }
}
