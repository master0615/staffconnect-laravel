<?php
namespace App;

use Hyn\Tenancy\Traits\UsesTenantConnection;
use Illuminate\Database\Eloquent\Model;

class ProfileVideo extends Model
{
    use UsesTenantConnection;

    public function ProfileVideoCategory()
    {
        return $this->belongsToMany('App\ProfileVideoCategory', 'profile_video_cat_link', 'profile_video_id', 'profile_video_cat_id')->using('App\ProfileVideoCatLink');
    }

    public function ProfileVideoCatLink()
    {
        return $this->hasMany('App\ProfileVideoCatLink');
    }

    public function path()
    {
        return action('Api\StorageController@getFile', ['profile_video', $this->id, $this->ext]);
    }

    public function thumbnail()
    {
        return action('Api\StorageController@getFile', ['profile_video', $this->id, $this->ext, 1]);
    }

    public function user()
    {
        return $this->belongsTo('App\User');
    }
}
