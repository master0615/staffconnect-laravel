<?php
namespace App;

use Hyn\Tenancy\Traits\UsesTenantConnection;
use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * @SWG\Definition()
 */
class ProfilePhotoCatLink extends Pivot
{
    use UsesTenantConnection;

    /**
     * @SWG\Property(
     * property="id",
     * type="integer",
     * description="Primary key id"
     * )
     * @SWG\Property(
     * property="profile_photo_id",
     * type="integer",
     * description="Document id"
     * )
     * @SWG\Property(
     * property="profile_photo_cat_id",
     * type="integer",
     * description="Category id"
     * )
     */
    public $timestamps = false;

    public function ProfilePhoto()
    {
        return $this->belongsTo('App\ProfilePhoto');
    }

    public function ProfilePhotoCategory()
    {
        return $this->belongsTo('App\ProfilePhotoCategory');
    }
}
