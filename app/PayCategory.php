<?php
namespace App;

use Hyn\Tenancy\Traits\UsesTenantConnection;
use Illuminate\Database\Eloquent\Model;

/**
 * @SWG\Definition()
 */
class PayCategory extends Model
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
     * maximum="20",
     * description="Category name"
     * )
     */
    public $timestamps = false;

    protected $guarded = [];

    public function payLevels()
    {
        return $this->hasMany('App\PayLevel');
    }

    public function shiftRole()
    {
        return $this->hasMany('App\ShiftRole');
    }
}
