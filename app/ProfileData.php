<?php
namespace App;

use Hyn\Tenancy\Traits\UsesTenantConnection;
use Illuminate\Database\Eloquent\Model;

// change to pivot??

/**
 * @SWG\Definition()
 */
class ProfileData extends Model
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
     * property="profile_element_id",
     * type="integer",
     * description="Profile element id"
     * )
     */
    protected $table = 'profile_data';

    public $timestamps = false;

    protected $guarded = [
        'id'
    ];

    public function profileElement()
    {
        return $this->belongsTo('App\ProfileElement');
    }

    public function user()
    {
        return $this->belongsTo('App\User');
    }
}
