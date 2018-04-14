<?php
namespace App;

use Hyn\Tenancy\Traits\UsesTenantConnection;
use Illuminate\Database\Eloquent\Model;

/**
 * @SWG\Definition()
 */
class WorkAreaCategory extends Model
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
     */
    public $timestamps = false;

    protected $guarded = [];

    public function workAreas()
    {
        return $this->hasMany('App\WorkArea', 'work_area_cat_id');
    }
}
