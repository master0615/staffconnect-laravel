<?php
namespace App;

use Hyn\Tenancy\Traits\UsesTenantConnection;
use Illuminate\Database\Eloquent\Model;

// terms that are presented to user upon login for first time, must agree to continue otherwise profile set inactive
/**
 * @SWG\Definition()
 */
class Term extends Model
{
    use UsesTenantConnection;

    /**
     * @SWG\Property(
     * property="id",
     * type="integer",
     * description="Primary key id"
     * )
     * @SWG\Property(
     * property="tname",
     * type="string",
     * minimum="1",
     * maximum="30",
     * description="Term name"
     * )
     * @SWG\Property(
     * property="terms",
     * type="mediumtext",
     * description="Terms content"
     * )
     * @SWG\Property(
     * property="active",
     * type="boolean",
     * default="1",
     * description="Controls if terms are active"
     * )
     */
    public $timestamps = false;

    public function termAgreement()
    {
        return $this->hasMany('App\TermAgreement');
    }

    public function termApplyLvl()
    {
        return $this->hasMany('App\TermApplyLvl');
    }
}
