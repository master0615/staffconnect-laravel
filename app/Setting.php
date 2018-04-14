<?php
namespace App;

use Hyn\Tenancy\Traits\UsesTenantConnection;
use Illuminate\Database\Eloquent\Model;

/**
 * @SWG\Definition()
 */
class Setting extends Model
{
    use UsesTenantConnection;

    public $timestamps = false;

}
