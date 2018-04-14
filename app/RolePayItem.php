<?php
namespace App;

use Hyn\Tenancy\Traits\UsesTenantConnection;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * @SWG\Definition()
 */
class RolePayItem extends Model
{
    use UsesTenantConnection, LogsActivity;

    /**
     * @SWG\Property(
     * property="id",
     * type="integer",
     * description="Primary key id"
     * )
     * @SWG\Property(
     * property="shift_role_id",
     * type="integer",
     * description="Role the pay item belongs to"
     * )
     * @SWG\Property(
     * property="unit_rate",
     * type="decimal",
     * minimum="0.00",
     * maximum="99999999.99",
     * description="Unit rate"
     * )
     * @SWG\Property(
     * property="unit_rate_type",
     * type="enum",
     * default="pu",
     * enum={"pu","flat"},
     * description="Rate type"
     * )
     * @SWG\Property(
     * property="units",
     * type="decimal",
     * minimum="0.00",
     * maximum="99999999.99",
     * description="Units / quantity"
     * )
     * @SWG\Property(
     * property="item_name",
     * type="string",
     * minimum="1",
     * maximum="30",
     * description="Item name eg. Travel allowance"
     * )
     * @SWG\Property(
     * property="item_type",
     * type="enum",
     * default="other",
     * enum={"bonus","expense","travel","other"},
     * description="Item type"
     * )
     */
    public $timestamps = false;

    protected static $logAttributes = [
        'item_name',
        'item_type',
        'units',
        'unit_rate',
        'unit_rate_type'
    ];

    protected static $logOnlyDirty = true;

    public function getLogNameToUse(string $eventName = ''): string
    {
        return 'shift';
    }

    public function shiftRole()
    {
        return $this->belongsTo('App\ShiftRole');
    }

    public function staffPayItems()
    {
        return $this->hasMany('App\StaffPayItem');
    }
}
