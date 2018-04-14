<?php
namespace App;

use Hyn\Tenancy\Traits\UsesTenantConnection;
use Illuminate\Database\Eloquent\Model;

/**
 * @SWG\Definition()
 */
class AttributeCategory extends Model
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
     * maximum="50",
     * description="Category name"
     * )
     * @SWG\Property(
     * property="display_order",
     * type="integer",
     * default=null,
     * description="Display order on user profile"
     * )
     */
    public $timestamps = false;

    protected $guarded = [];

    public function attributes()
    {
        return $this->hasMany('App\Attribute', 'attribute_cat_id');
    }
}
