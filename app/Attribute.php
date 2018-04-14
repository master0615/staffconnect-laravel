<?php
namespace App;

use Hyn\Tenancy\Traits\UsesTenantConnection;
use Illuminate\Database\Eloquent\Model;

/**
 * @SWG\Definition()
 */
class Attribute extends Model
{
    use UsesTenantConnection;

    /**
     * @SWG\Property(
     * property="id",
     * type="integer",
     * description="Primary key id"
     * )
     * @SWG\Property(
     * property="aname",
     * type="string",
     * minimum="1",
     * maximum="50",
     * description="Attribute name"
     * )
     * @SWG\Property(
     * property="attribute_cat_id",
     * type="integer",
     * default="1",
     * description="Attribute category id"
     * )
     * @SWG\Property(
     * property="visibility",
     * type="enum",
     * enum={"staff","admin"},
     * default="staff",
     * description="Attribute visibility"
     * )
     * * @SWG\Property(
     * property="role_default",
     * type="enum",
     * enum={"any","yes","no"},
     * default="any",
     * description="Auto-population as requirement during role creation"
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

    public function attributeCategory()
    {
        return $this->belongsTo('App\AttributeCategory');
    }

    public function attributeUser()
    {
        return $this->hasMany('App\AttributeUser');
    }

    public function users()
    {
        return $this->belongsToMany('App\User')->using('App\AttributeUser');
    }
}
