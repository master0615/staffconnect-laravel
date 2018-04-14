<?php
namespace App;

use Hyn\Tenancy\Traits\UsesTenantConnection;
use Illuminate\Database\Eloquent\Model;

/**
 * @SWG\Definition()
 */
class ProfilePhotoCategory extends Model
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

    public function ProfilePhotos()
    {
        return $this->belongsToMany('App\ProfilePhoto', 'profile_photo_cat_link', 'profile_photo_cat_id', 'profile_photo_id')->using('App\ProfilePhotoCatLink');
    }

    public function ProfilePhotoCatLink()
    {
        return $this->hasMany('App\ProfilePhotoCatLink');
    }
}
