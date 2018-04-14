<?php
namespace App;

use Hyn\Tenancy\Traits\UsesTenantConnection;
use Illuminate\Database\Eloquent\Model;

/**
 * @SWG\Definition()
 */
class ProfileListOption extends Model
{
    use UsesTenantConnection;

    /**
     * @SWG\Property(
     * property="id",
     * type="integer",
     * description="Primary key id"
     * )
     * @SWG\Property(
     * property="profile_element_id",
     * type="integer",
     * description="Profie element id"
     * )
     * @SWG\Property(
     * property="option",
     * type="string",
     * minimum="1",
     * maximum="40",
     * description="Option to choose from"
     * )
     * @SWG\Property(
     * property="display_order",
     * type="integer",
     * default=null,
     * description="Display order in drop down select"
     * )
     */
    public $timestamps = false;

    public function profileElement()
    {
        return $this->belongsTo('App\ProfileElement');
    }
}
