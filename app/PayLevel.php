<?php
namespace App;

use Hyn\Tenancy\Traits\UsesTenantConnection;
use Illuminate\Database\Eloquent\Model;

/**
 * @SWG\Definition()
 */
class PayLevel extends Model
{
    use UsesTenantConnection;

    /**
     * @SWG\Property(
     * property="id",
     * type="integer",
     * description="Primary key id"
     * )
     * @SWG\Property(
     * property="pay_cat_id",
     * type="integer",
     * description="Pay category id"
     * )
     * @SWG\Property(
     * property="pname",
     * type="string",
     * minimum="1",
     * maximum="20",
     * description="Pay level name"
     * )
     * @SWG\Property(
     * property="pay_rate",
     * type="number",
     * minimum="0.00",
     * maximum="99999999.99",
     * description="Rate"
     * )
     * @SWG\Property(
     * property="pay_rate_type",
     * type="enum",
     * enum={"phr","flat"},
     * default="phr"
     * )
     */
    public $timestamps = false;

    protected $guarded = [];

    public function payCategory()
    {
        return $this->belongsTo('App\PayCategory');
    }

    public function payLevelUsers()
    {
        return $this->hasMany('App\PayLevelUser');
    }

    public function users()
    {
        return $this->belongsToMany('App\User')->using('App\PayLevelUser');
    }
}
