<?php
namespace App;

use Hyn\Tenancy\Traits\UsesTenantConnection;
use Illuminate\Database\Eloquent\Model;

/**
 * @SWG\Definition()
 */
class ShiftGroup extends Model
{
    use UsesTenantConnection;

    /**
     * @SWG\Property(
     * property="id",
     * type="integer",
     * description="Primary key id"
     * )
     * @SWG\Property(
     * property="gname",
     * type="string",
     * minimum="1",
     * maximum="70",
     * description="Group name"
     * )
     * @SWG\Property(
     * property="apply_all_or_nothing",
     * type="boolean",
     * default="0",
     * description="When set forces all staff to apply for all matching named roles in group or none at all"
     * )
     */
    public $timestamps = false;

    public function shift()
    {
        return $this->hasMany('App\Shift');
    }
}
