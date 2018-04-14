<?php
namespace App;

use Hyn\Tenancy\Traits\UsesTenantConnection;
use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * @SWG\Definition()
 */
class OutsourceCompanyUser extends Pivot
{
    use UsesTenantConnection;

    /**
     * @SWG\Property(
     * property="id",
     * type="integer",
     * description="Primary key id"
     * )
     * @SWG\Property(
     * property="user_id",
     * type="integer",
     * description="User id"
     * )
     * @SWG\Property(
     * property="outsource_company_id",
     * type="integer",
     * description="Outsource company id"
     * )
     */
    public $timestamps = false;

    protected $guarded = [];

    public function outsourceCompany()
    {
        return $this->belongsTo('App\OutsourceCompany');
    }

    public function user()
    {
        return $this->belongsTo('App\User');
    }
}
