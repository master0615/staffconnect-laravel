<?php
namespace App;

use Hyn\Tenancy\Traits\UsesTenantConnection;
use Illuminate\Database\Eloquent\Model;

/**
 * @SWG\Definition()
 */
class TermApplyLvl extends Model
{
    use UsesTenantConnection;

    /**
     * @SWG\Property(
     * property="id",
     * type="integer",
     * description="Primary key id"
     * )
     * @SWG\Property(
     * property="term_id",
     * type="integer",
     * description="Term id"
     * )
     * @SWG\Property(
     * property="user_lvl",
     * type="enum",
     * default="staff",
     * enum={"owner","admin","staff","client","ext","registrant","api"},
     * description="User level the term applies to eg. staff"
     * )
     */
    public $timestamps = false;

    public function term()
    {
        return $this->belongsTo('App\Term');
    }
}
