<?php
namespace App;

use Hyn\Tenancy\Traits\UsesTenantConnection;
use Illuminate\Database\Eloquent\Model;

/**
 * @SWG\Definition()
 */
class ProfileElement extends Model
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
     * property="ename",
     * type="string",
     * minimum="1",
     * maximum="60",
     * description="Element name eg. Address"
     * )
     * @SWG\Property(
     * property="etype",
     * type="enum",
     * enum={"short","medium","long","list","date","number","listm"},
     * default="short",
     * description="Element type"
     * )
     * @SWG\Property(
     * property="editable",
     * type="boolean",
     * default="1",
     * description="Controls if element is editable"
     * )
     * @SWG\Property(
     * property="deletable",
     * type="boolean",
     * default="1",
     * description="Controls if element is deletable"
     * )
     * @SWG\Property(
     * property="visibility",
     * type="enum",
     * enum={"short","optional","required","hidden","pay"},
     * default="optional",
     * description="Controls element visibilty"
     * )
     * @SWG\Property(
     * property="display_order",
     * type="integer",
     * default=null,
     * description="Display order on user profile"
     * )
     * @SWG\Property(
     * property="sex",
     * type="enum",
     * enum={"male","female"},
     * default="null",
     * description="Controls element visibilty to males, females or both"
     * )
     * @SWG\Property(
     * property="filter",
     * type="enum",
     * enum={"equals","range"},
     * default="equals",
     * description="Controls filter type"
     * )
     */
    public $timestamps = false;

    protected $fillable = [
        'ename',
        'editable',
        'deletable',
        'visibility',
        'display_order',
        'sex',
        'filter',
        'profile_cat_id',
    ];

    //
    public function profileCategory()
    {
        return $this->belongsTo('App\ProfileCategory', 'id', 'profile_cat_id');
    }

    public function profileListOptions()
    {
        return $this->hasMany('App\ProfileListOption');
    }
}
