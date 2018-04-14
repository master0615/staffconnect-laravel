<?php
namespace App;

use App\Events\RoleStaffSaved;
use App\Helpers\Utilities;
use Hyn\Tenancy\Traits\UsesTenantConnection;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class RoleStaff extends Model
{
    use UsesTenantConnection, LogsActivity;

    protected $table = 'role_staff';

    protected static $logAttributes = [
        'staff_status_id',
        'staff_start',
        'staff_end',
        'bill_rate',
        'bill_rate_type',
        'pay_rate',
        'pay_rate_type',
        'unpaid_break',
        'paid_break',
        'expense_limit',
        'outsource_company_id',
        'team_leader',
        'pay_released',
        'times_locked',
    ];

    protected static $ignoreChangedAttributes = [
        'created_at',
        'updated_at',
    ];

    protected $dispatchesEvents = [
        'saved' => RoleStaffSaved::class,
    ];

    protected static $logOnlyDirty = true;

    public function getLogNameToUse(string $eventName = ''): string
    {
        return 'shift';
    }

    public static function boot()
    {
        parent::boot();

        self::saving(function ($rs) {

            // set correct date on staff start and end to account for overnight shifts
            if (!empty($rs->staff_start)) {
                $rs->loadMissing('shiftRole.shift');
                $start = $rs->shiftRole->role_start ?: $rs->shiftRole->shift->shift_start;
                if (strtotime($rs->staff_start) < (strtotime($start) - 43200)) {
                    //add a day if more than 12 hours before start
                    $rs->staff_start = date('Y-m-d H:i:00', strtotime($rs->staff_start . ' + 1 day'));
                }
                if ($rs->staff_start == $start) {
                    $rs->staff_start = null;
                }
            }
            if (!empty($rs->staff_end)) {
                $rs->loadMissing('shiftRole.shift');
                $start = $rs->staff_start ?? $rs->shiftRole->role_start ?? $rs->shiftRole->shift->shift_start;
                if (strtotime($rs->staff_end) <= strtotime($start)) {
                    $rs->staff_end = date('Y-m-d H:i:00', strtotime($rs->staff_end . ' + 1 day'));
                }
                $end = $rs->shiftRole->role_end ?: $rs->shiftRole->shift->shift_end;
                if ($rs->staff_end == $end) {
                    $rs->staff_end = null;
                }
            }
        });
    }

    public function checkInOpen()
    {
        $minsBefore = \App\Setting::find(SETTING_STAFF_CHECKIN_START)->value;

        $this->loadMissing('shiftRole.shift');
        $open = 0;
        $start = $this->staff_start ?? $this->shiftRole->role_start ?? $this->shiftRole->shift->shift_start;
        $start = Utilities::applyTimezone($start, $this->ShiftRole->shift->tz);

        if (strtotime($start) <= (date('U') + $minsBefore * 60)) {
            $open = 1;
        }
        return $open;
    }

    public function actions($userId)
    {
        $actions = [];

        if ($this->past()) {

            switch ($this->staff_status_id) {

                case STAFF_STATUS_SELECTED:
                case STAFF_STATUS_CONFIRMED:
                    $actions = ['complete'];
                    break;

                case STAFF_STATUS_CHECK_IN_ATTEMPTED:
                case STAFF_STATUS_CHECKED_IN:
                    $actions = ['check_out'];
                    break;

                case STAFF_STATUS_CHECKED_OUT:
                    $actions = ['complete'];
                    break;

                case STAFF_STATUS_COMPLETED: //TODO check can staff invoice?
                    $actions = ['expenses', 'invoice'];
                    break;

                case STAFF_STATUS_INVOICED:
                    $actions = ['view_invoice'];
                    break;

                case STAFF_STATUS_PAID:
                    $actions = ['view_pay'];
                    break;
            }

        } else {

            switch ($this->staff_status_id) {

                case STAFF_STATUS_APPLIED:
                case STAFF_STATUS_HIDDEN_REJECTED:
                    $actions = ['cancel_application'];
                    break;

                case STAFF_STATUS_SELECTED:
                    if ($this->checkInOpen()) {
                        $actions = ['check_in', 'confirm', 'replace'];
                    } else {
                        $actions = ['confirm', 'replace'];
                    }
                    break;

                case STAFF_STATUS_CONFIRMED:
                    $actions = ['replace'];
                case STAFF_STATUS_CHECK_IN_ATTEMPTED:
                    if ($this->checkInOpen()) {
                        $actions = ['check_in'];
                    }
                    break;

                case STAFF_STATUS_STANDBY:
                    $actions = ['replace'];
                    break;

                case STAFF_STATUS_REPLACEMENT_REQUESTED:
                    $actions = ['cancel_replace'];
                    break;

                case STAFF_STATUS_STANDBY_REPLACEMENT_REQUESTED:
                    $actions = ['cancel_replace_stdby'];
                    break;

                case STAFF_STATUS_CHECKED_IN:
                    $actions = ['check_out'];
                    break;

                case STAFF_STATUS_CHECKED_OUT:
                    $actions = ['complete'];
                    break;

                case STAFF_STATUS_COMPLETED:
                    $actions = ['expenses', 'invoice'];
                    break;

                case STAFF_STATUS_INVOICED:
                    $actions = ['view_invoice'];
                    break;

                case STAFF_STATUS_PAID:
                    $actions = ['view_pay'];
                    break;

                case STAFF_STATUS_NOT_AVAILABLE:
                    if ($this->shiftRole()->first()->userEligible($userId) == '') {
                        $actions = ['apply'];
                    }
                    break;
            }
        }
        return $actions;
    }

    //has a staffs' end time past?
    public function past()
    {
        $this->loadMissing('shiftRole.shift');
        $past = 0;
        if ($this->staff_end) {
            $this->staff_end = Utilities::applyTimezone($this->staff_end, $this->ShiftRole->shift->tz);
            if (strtotime($this->staff_end) < date('U')) {
                $past = 1;
            }
        } else {
            $past = $this->ShiftRole->past();
        }
        return $past;
    }

    public function applicationReason()
    {
        return $this->hasOne('App\ApplicationReason');
    }

    public function outsourceCompany()
    {
        return $this->belongsTo('App\OutsourceCompany');
    }

    public function replacementReason()
    {
        return $this->hasOne('App\ReplacementReason');
    }

    public function shiftRole()
    {
        return $this->belongsTo('App\ShiftRole');
    }

    public function staffChecks()
    {
        return $this->hasMany('App\StaffCheck');
    }

    public function staffPayItems()
    {
        return $this->hasMany('App\StaffPayItem');
    }

    public function staffStatus()
    {
        return $this->belongsTo('App\StaffStatus');
    }

    public function user()
    {
        return $this->belongsTo('App\User');
    }
}
