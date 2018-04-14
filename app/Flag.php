<?php
namespace App;

use Hyn\Tenancy\Traits\UsesTenantConnection;
use Illuminate\Database\Eloquent\Model;

/**
 * @SWG\Definition()
 */
class Flag extends Model
{
    use UsesTenantConnection;

    /**
     * @SWG\Property(
     * property="id",
     * type="integer",
     * description="Primary key id"
     * )
     * @SWG\Property(
     * property="fname",
     * type="string",
     * minimum="1",
     * maximum="20",
     * description="Flag name"
     * )
     */
    public $timestamps = false;

    protected $guarded = [];

    public function shift()
    {
        return $this->belongsToMany('App\Shift');
    }
}
