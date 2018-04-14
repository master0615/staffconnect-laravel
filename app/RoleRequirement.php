<?php

namespace App;

use Hyn\Tenancy\Traits\UsesTenantConnection;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class RoleRequirement extends Model
{
    use UsesTenantConnection, LogsActivity;
    public $timestamps = false;
    protected $guarded = ['id'];

    protected static $logAttributes = [
        'requirement',
        'operator',
        'value',
    ];

    protected static $logOnlyDirty = true;

    public function getLogNameToUse(string $eventName = ''): string
    {
        return 'shift';
    }

    public function shiftRole()
    {
        return $this->belongsTo('App\ShiftRole');
    }
}
