<?php
namespace App;

use Hyn\Tenancy\Traits\UsesTenantConnection;
use Illuminate\Database\Eloquent\Model;

/**
 * @SWG\Definition()
 */
class WorkArea extends Model
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
     * description="Work area name eg. Bar 1, Perth"
     * )
     * @SWG\Property(
     * property="work_area_cat_id",
     * type="integer",
     * default=null,
     * description="Work area category id"
     * )
     * @SWG\Property(
     * property="php_tz",
     * type="string",
     * default=null,
     * maximum="30",
     * description="Php timezone eg. Australia/Perth"
     * )
     * @SWG\Property(
     * property="lat",
     * type="double",
     * description="Latitude"
     * )
     * @SWG\Property(
     * property="lon",
     * type="double",
     * description="Longitude"
     * )
     */
    public $timestamps = false;

    protected $guarded = [];

    public function shifts()
    {
        return $this->belongsToMany('App\Shift')->using('App\ShiftWorkArea');
    }

    public function shiftWorkArea()
    {
        return $this->hasMany('App\ShiftWorkArea');
    }

    public function users()
    {
        return $this->belongsToMany('App\User')->using('App\UserWorkArea');
    }

    public function userWorkArea()
    {
        return $this->hasMany('App\UserWorkArea');
    }

    public function workAreaCategory()
    {
        return $this->belongsTo('App\WorkAreaCategory');
    }
}
