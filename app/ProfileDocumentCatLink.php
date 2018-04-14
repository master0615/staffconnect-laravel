<?php
namespace App;

use Hyn\Tenancy\Traits\UsesTenantConnection;
use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * @SWG\Definition()
 */
class ProfileDocumentCatLink extends Pivot
{
    use UsesTenantConnection;

    /**
     * @SWG\Property(
     * property="id",
     * type="integer",
     * description="Primary key id"
     * )
     * @SWG\Property(
     * property="profile_document_id",
     * type="integer",
     * description="Document id"
     * )
     * @SWG\Property(
     * property="profile_document_cat_id",
     * type="integer",
     * description="Category id"
     * )
     */
    public $timestamps = false;

    public function ProfileDocument()
    {
        return $this->belongsTo('App\ProfileDocument');
    }

    public function ProfileDocumentCategory()
    {
        return $this->belongsTo('App\ProfileDocumentCategory');
    }
}
