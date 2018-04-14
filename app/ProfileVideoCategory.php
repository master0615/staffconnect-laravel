<?php
namespace App;

use Hyn\Tenancy\Traits\UsesTenantConnection;
use Illuminate\Database\Eloquent\Model;

/**
 * @SWG\Definition()
 */
class ProfileVideoCategory extends Model
{
    use UsesTenantConnection;

    /**
     * @SWG\Property(
     * property="id",
     * type="integer",
     * description="Primary key id"
     * )
     * @SWG\Property(
     * property="cname",
     * type="string",
     * minimum="1",
     * maximum="40",
     * description="Category name"
     * )
     */
    public $timestamps = false;

    protected $guarded = [];

    public function ProfileVideos()
    {
        return $this->belongsToMany('App\ProfileVideo', 'profile_video_cat_link', 'profile_video_cat_id', 'profile_video_id')->using('App\ProfileVideoCatLink');
    }

    public function ProfileVideoCatLink()
    {
        return $this->hasMany('App\ProfileVideoCatLink');
    }
}
