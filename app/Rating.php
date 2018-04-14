<?php
namespace App;

use Hyn\Tenancy\Traits\UsesTenantConnection;
use Illuminate\Database\Eloquent\Model;

/**
 * @SWG\Definition()
 */
class Rating extends Model
{
    use UsesTenantConnection;

    /**
     * @SWG\Property(
     * property="id",
     * type="integer",
     * description="Primary key id"
     * )
     * @SWG\Property(
     * property="rname",
     * type="string",
     * minimum="1",
     * maximum="30",
     * description="Rating name"
     * )
     */
    public $timestamps = false;

    protected $guarded = [];

    public function ratingUsers()
    {
        return $this->hasMany('App\RatingUser');
    }

    public function users()
    {
        return $this->belongsToMany('App\User')->withPivot('score')->using('App\RatingUser');
    }
}
