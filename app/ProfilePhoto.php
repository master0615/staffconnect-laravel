<?php
namespace App;

use Hyn\Tenancy\Traits\UsesTenantConnection;
use Illuminate\Database\Eloquent\Model;

/**
 * @SWG\Definition()
 */
class ProfilePhoto extends Model
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
     * property="ext",
     * type="string",
     * minimum="1",
     * maximum="4",
     * description="File extension"
     * )
     * @SWG\Property(
     * property="display_order",
     * type="integer",
     * default=null,
     * description="Display order on user profile"
     * )
     * @SWG\Property(
     * property="admin_only",
     * type="boolean",
     * default=false,
     * description="Controls admin only visibility"
     * )
     * @SWG\Property(
     * property="locked",
     * type="boolean",
     * default=false,
     * description="Controls staff deletability"
     * )
     * @SWG\Property(
     * property="main",
     * type="boolean",
     * default=false,
     * description="Indicates main profile photo"
     * )
     * @SWG\Property(
     * property="created_at",
     * type="datetime",
     * description="Record creation timestamp"
     * )
     * @SWG\Property(
     * property="updated_at",
     * type="datetime",
     * description="Record updated timestamp"
     * )
     */
    public function ProfilePhotoCategories()
    {
        return $this->belongsToMany('App\ProfilePhotoCategory', 'profile_photo_cat_link', 'profile_photo_id', 'profile_photo_cat_id')->using('App\ProfilePhotoCatLink');
    }

    public function ProfilePhotoCatLink()
    {
        return $this->hasMany('App\ProfilePhotoCatLink');
    }

    public function path()
    {
        return action('Api\StorageController@getFile', ['profile_photo', $this->id, $this->ext]);
    }

    public function thumbnail()
    {
        return action('Api\StorageController@getFile', ['profile_photo', $this->id, $this->ext, 1]);
    }

    public function user()
    {
        return $this->belongsTo('App\User');
    }
}
