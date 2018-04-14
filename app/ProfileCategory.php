<?php
namespace App;

use Hyn\Tenancy\Traits\UsesTenantConnection;
use Illuminate\Database\Eloquent\Model;

/**
 * @SWG\Definition()
 */
class ProfileCategory extends Model
{
    use UsesTenantConnection;

    /**
     * @SWG\Property(
     * property="id",
     * type="integer",
     * description="Primary key id"
     * )
     * @SWG\Property(
     * property="profile_cat_id",
     * type="integer",
     * default="null",
     * description="Profile category id"
     * )
     * @SWG\Property(
     * property="cname",
     * type="string",
     * minimum="1",
     * maximum="40",
     * description="Category name eg. Personal Information"
     * )
     * @SWG\Property(
     * property="deletable",
     * type="boolean",
     * default="1",
     * description="Controls if category is deletable"
     * )
     * @SWG\Property(
     * property="display_order",
     * type="integer",
     * default=null,
     * description="Display order on user profile"
     * )
     */
    public $timestamps = false;

    public function profileElements()
    {
        return $this->hasMany('App\ProfileElement', 'profile_cat_id');
    }
}
