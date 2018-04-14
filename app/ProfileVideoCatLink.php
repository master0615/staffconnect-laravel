<?php
namespace App;

use Hyn\Tenancy\Traits\UsesTenantConnection;
use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * @SWG\Definition()
 */
class ProfileVideoCatLink extends Pivot
{
    use UsesTenantConnection;

    /**
     * @SWG\Property(
     * property="id",
     * type="integer",
     * description="Primary key id"
     * )
     * @SWG\Property(
     * property="profile_video_id",
     * type="integer",
     * description="Document id"
     * )
     * @SWG\Property(
     * property="profile_video_cat_id",
     * type="integer",
     * description="Category id"
     * )
     */
    public $timestamps = false;

    public function ProfileVideo()
    {
        return $this->belongsTo('App\ProfileVideo');
    }

    public function ProfileVideoCategory()
    {
        return $this->belongsTo('App\ProfileVideoCategory');
    }
}
