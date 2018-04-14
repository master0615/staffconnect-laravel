<?php
namespace App;

use App\Helpers\Utilities;
use Hyn\Tenancy\Traits\UsesTenantConnection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * @SWG\Definition()
 */
class Shift extends Model
{
    use UsesTenantConnection, SoftDeletes, LogsActivity;

    protected $dates = [
        'deleted_at',
    ];

    protected $guarded = [];

    protected static $logAttributes = [
        'title',
        'location',
        'shift_start',
        'shift_end',
    ];

    protected static $ignoreChangedAttributes = [
        'created_at',
        'updated_at',
    ];

    protected static $logOnlyDirty = true;

    public function getLogNameToUse(string $eventName = ''): string
    {
        return 'shift';
    }

    public static function boot()
    {
        parent::boot();

        self::saving(function ($shift) {

            // set correct date on start and end to account for overnight shifts
            if (!empty($shift->shift_start)) {
                if (strtotime($shift->shift_start) > strtotime($shift->shift_end)) {
                    $shift->shift_end = date('Y-m-d H:i:00', strtotime($shift->shift_end . ' + 1 day'));
                }
            }
            if (!empty($shift->shift_end)) {
                if (strtotime($shift->shift_start) > strtotime($shift->shift_end)) {
                    $shift->shift_end = date('Y-m-d H:i:00', strtotime($shift->shift_end . ' + 1 day'));
                }
            }
            //TODO check dates on role and role staff if shift updated
        });
    }

    public function client()
    {
        return $this->belongsTo('App\Client');
    }

    public function flags()
    {
        return $this->belongsToMany('App\Flag');
    }

    public function location()
    {
        return $this->belongsTo('App\Location');
    }

    public function locker()
    {
        // inverse rel not defined in user... is it necessary?
        return $this->hasOne('App\User', 'id', 'locked');
    }

    //has a shift's end time past?
    public function past()
    {
        $past = 0;
        $this->shift_end = Utilities::applyTimezone($this->shift_end, $this->timezone);
        if (strtotime($this->shift_end) < date('U')) {
            $past = 1;
        }
        return $past;
    }

    public function shiftGroup()
    {
        return $this->belongsTo('App\ShiftGroup');
    }

    public function shiftStatus()
    {
        return $this->belongsTo('App\ShiftStatus');
    }

    public function managers()
    {
        return $this->belongsToMany('App\User', 'shift_manager', 'shift_id', 'user_id')->using('App\ShiftManager');
    }

    public function shiftAdminNotes()
    {
        return $this->hasMany('App\ShiftAdminNote');
    }

    public function shiftRoles()
    {
        return $this->hasMany('App\ShiftRole');
    }

    public function shiftTrackingOptions()
    {
        return $this->hasMany('App\ShiftTrackingOption');
    }

    public function shiftWorkAreas()
    {
        return $this->hasMany('App\ShiftWorkArea');
    }

    public function trackingOptions()
    {
        return $this->belongsToMany('App\TrackingOption')->using('App\ShiftTrackingOption');
    }

    public function userEligible($userId)
    {
        foreach ($this->shiftRoles()->get() as $sr) {
            if ($sr->userEligible($userId) == '') {
                return 1;
            }
        }
        return 0;
    }

    //returns array of colors for user shift
    public function userShiftColor($userId)
    {
        $colors = \Illuminate\Support\Facades\DB::connection('tenant')
            ->table('role_staff')
            ->select('bg_color', 'border_color', 'font_color')
            ->join('shift_roles', 'role_staff.shift_role_id', '=', 'shift_roles.id')
            ->join('staff_statuses', 'role_staff.staff_status_id', '=', 'staff_statuses.id')
            ->where([['user_id', $userId], ['shift_id', $this->id]])
            ->orderBy('priority', 'desc')
            ->first();

        return $colors;
    }

    //returns array keyed shift_role_id val array role_staff_id and selected
    public function userIn($userId)
    {
        $rs = \Illuminate\Support\Facades\DB::connection('tenant')
            ->table('role_staff')
            ->select('role_staff.id', 'shift_role_id', 'selected')
            ->join('shift_roles', 'role_staff.shift_role_id', '=', 'shift_roles.id')
            ->join('staff_statuses', 'role_staff.staff_status_id', '=', 'staff_statuses.id')
            ->where([['user_id', $userId], ['shift_id', $this->id]])
            ->get()
            ->keyBy('shift_role_id')
            ->all();

        return $rs;
    }

    public function updateStatus()
    {
        $this->loadMissing('shiftRoles.roleStaff');

        $statuses = [];
        foreach ($this->shiftRoles as $sr) {
            $statuses[] = $sr->getStatus();
        }

        $ss = \App\ShiftStatus::select('id')
            ->whereIn('id', $statuses)
            ->orderBy('priority', 'desc')
            ->first();

        $this->shift_status_id = $ss->id;
        $this->save();

        return $this->shift_status_id;
    }

    public function workAreas()
    {
        return $this->belongsToMany('App\WorkArea')->using('App\ShiftWorkArea');
    }
}
