<?php
namespace App;

use Hyn\Tenancy\Traits\UsesTenantConnection;
use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * @SWG\Definition()
 */
class AttributeUser extends Pivot
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
     * property="attribute_id",
     * type="integer",
     * description="Attribute id"
     * )
     * @SWG\Property(
     * property="setter_id",
     * type="integer",
     * description="User id of the user who created this record"
     * )
     * @SWG\Property(
     * property="created_at",
     * type="datetime",
     * description="Record creation timestamp"
     * )
     */
    public $timestamps = false;

    protected $guarded = [];

    public function attribute()
    {
        return $this->belongsTo('App\Attribute');
    }

    public function user()
    {
        return $this->belongsTo('App\User');
    }
}
