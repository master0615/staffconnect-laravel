<?php
namespace App;

use Hyn\Tenancy\Traits\UsesTenantConnection;
use Illuminate\Database\Eloquent\Model;

/**
 * @SWG\Definition()
 */
class ExcelScheduleColumnmap extends Model
{
    use UsesTenantConnection;

    /**
     * @SWG\Property(
     * property="id",
     * type="integer",
     * description="Primary key id"
     * )
     * @SWG\Property(
     * property="col_name_1",
     * type="string",
     * maximum="20",
     * description="Name in StaffConnect"
     * )
     * @SWG\Property(
     * property="col_name_2",
     * type="string",
     * maximum="20",
     * description="Column in spreadsheet"
     * )
     * @SWG\Property(
     * property="description",
     * type="string",
     * maximum="50",
     * minimum="1",
     * description="Descriptive text"
     * )    
     */
    public $timestamps = false;
}
