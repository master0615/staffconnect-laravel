<?php
namespace App;

use Hyn\Tenancy\Traits\UsesTenantConnection;
use Illuminate\Database\Eloquent\Model;

/**
 * @SWG\Definition()
 */
class OutsourceCompany extends Model
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
     * description="Outsource company name"
     * )
     */
    public $timestamps = false;

    public function outsourceCompanyUser()
    {
        return $this->hasMany('App\OutsourceCompanyUser');
    }

    public function roleStaff()
    {
        return $this->hasMany('App\RoleStaff');
    }

    public function shiftRole()
    {
        return $this->hasMany('App\ShiftRole');
    }

    public function users()
    {
        return $this->belongsToMany('App\User')->using('App\OutsourceCompanyUser');
    }
}
