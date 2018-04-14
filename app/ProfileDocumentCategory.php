<?php
namespace App;

use Hyn\Tenancy\Traits\UsesTenantConnection;
use Illuminate\Database\Eloquent\Model;

/**
 * @SWG\Definition()
 */
class ProfileDocumentCategory extends Model
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

    public function ProfileDocuments()
    {
        return $this->belongsToMany('App\ProfileDocument', 'profile_document_cat_link', 'profile_document_cat_id', 'profile_document_id')->using('App\ProfilePhotoCatLink');
    }

    public function ProfileDocumentCatLink()
    {
        return $this->hasMany('App\ProfileDocumentCatLink');
    }
}
