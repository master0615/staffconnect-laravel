<?php
namespace App;

use Hyn\Tenancy\Traits\UsesTenantConnection;
use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * @SWG\Definition()
 */
class TermUser extends Pivot
{
    use UsesTenantConnection;

    /**
     * @SWG\Property(
     * property="id",
     * type="integer",
     * description="Primary key id"
     * )
     * @SWG\Property(
     * property="term_id",
     * type="integer",
     * description="Term id"
     * )
     * @SWG\Property(
     * property="user_id",
     * type="integer",
     * description="User id"
     * )
     * @SWG\Property(
     * property="created_at",
     * type="datetime",
     * description="Record creation timestamp"
     * )
     */
    public $timestamps = false;

    // check if this works for pivots?
    public static function boot()
    {
        parent::boot();
        
        static::creating(function ($model) {
            $model->created_at = $model->freshTimestamp();
        });
    }

    public function term()
    {
        return $this->belongsTo('App\Term');
    }

    public function user()
    {
        return $this->belongsTo('App\User');
    }
}
