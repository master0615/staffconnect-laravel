<?php
namespace App;

use Hyn\Tenancy\Traits\UsesTenantConnection;
use Illuminate\Database\Eloquent\Model;

class ProfileDocument extends Model
{
    use UsesTenantConnection;

    public function ProfileDocumentCategories()
    {
        return $this->belongsToMany('App\ProfileDocumentCategory', 'profile_document_cat_link', 'profile_document_id', 'profile_document_cat_id')->using('App\ProfileDocumentCatLink');
    }

    public function ProfileDocumentCatLink()
    {
        return $this->hasMany('App\ProfileDocumentCatLink');
    }

    public function path()
    {
        return action('Api\StorageController@getFile', ['profile_document', $this->id, $this->ext]);
    }

    public function thumbnail()
    {
        return action('Api\StorageController@getFile', ['profile_document', $this->id, $this->ext, 1]);
    }

    public function user()
    {
        return $this->belongsTo('App\User');
    }
}
