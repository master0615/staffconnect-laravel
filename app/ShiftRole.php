<?php
namespace App;

use App\Helpers\Utilities;
use Hyn\Tenancy\Traits\UsesTenantConnection;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class ShiftRole extends Model
{
    use UsesTenantConnection, LogsActivity;

    protected static $logAttributes = [
        'rname',
        'num_required',
        'sex',
        'role_start',
        'role_end',
        'notes',
        'completion_notes',
        'bill_rate',
        'bill_rate_type',
        'pay_category_id',
        'pay_rate',
        'pay_rate_type',
        'unpaid_break',
        'paid_break',
        'expense_limit',
        'application_deadline',
        'outsource_company_id',
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

        self::saving(function ($role) {

            // set correct date on role start and end to account for overnight shifts
            if (!empty($role->role_start)) {
                if (strtotime($role->role_start) < strtotime($role->shift->shift_start)) {
                    $role->role_start = date('Y-m-d H:i:00', strtotime($role->role_start . ' + 1 day'));
                }
                if ($role->role_start == $role->shift->shift_start) {
                    unset($role->attributes['role_start']);
                }
            }
            if (!empty($role->role_end)) {
                $start = ($role->role_start) ?: ($role->shift->shift_start);
                if (strtotime($role->role_end) < strtotime($start)) {
                    $role->role_end = date('Y-m-d H:i:00', strtotime($role->role_end . ' + 1 day'));
                }
                if ($role->role_end == $role->shift->shift_end) {
                    unset($role->attributes['role_end']);
                }
            }

            // remove application deadline if it's after the shift end
            if (!empty($role->application_deadline)) {
                if (strtotime($role->application_deadline) > strtotime($role->shift->shift_end)) {
                    unset($role->attributes['application_deadline']);
                }
            }
        });
    }

    public function applicationDeadlinePast()
    {
        $past = 0;
        if ($this->application_deadline) {
            $this->loadMissing('shift');

            $this->application_deadline = Utilities::applyTimezone($this->application_deadline, $this->shift->tz);
            if (strtotime($this->application_deadline) < date('U')) {
                $past = 1;
            }
        }
        return $past;
    }

    public function checkInOpen()
    {
        $minsBefore = \App\Setting::find(SETTING_STAFF_CHECKIN_START)->value;
        $this->loadMissing('shift');
        $open = 0;
        $start = $this->role_start ?? $this->shift->shift_start;

        $start = Utilities::applyTimezone($start, $this->shift->tz);
        if (strtotime($start) <= (date('U') + $minsBefore * 60)) {
            $open = 1;
        }
        return $open;
    }

    public function getStatus()
    {
        $this->loadMissing('roleStaff');

        if ($this->past()) {
            //TODO check reports completed? or do that seperate if icon on shift?

            $uss = $this->roleStaff()
                ->whereIn('staff_status_id', [
                    STAFF_STATUS_SELECTED,
                    STAFF_STATUS_CONFIRMED,
                    STAFF_STATUS_CHECK_IN_ATTEMPTED,
                    STAFF_STATUS_CHECKED_IN,
                    STAFF_STATUS_NO_SHOW,
                    STAFF_STATUS_CHECKED_OUT,
                    STAFF_STATUS_REPLACEMENT_REQUESTED,
                    STAFF_STATUS_STANDBY_REPLACEMENT_REQUESTED,
                    STAFF_STATUS_COMPLETED,
                ])
                ->pluck('staff_status_id')->all();

            $num = count($uss);
            if ($num) {
                if (in_array(STAFF_STATUS_NO_SHOW, $uss)) {
                    return SHIFT_STATUS_NO_SHOW;
                }
                if (in_array([
                    STAFF_STATUS_SELECTED,
                    STAFF_STATUS_CONFIRMED,
                    STAFF_STATUS_CHECK_IN_ATTEMPTED,
                    STAFF_STATUS_CHECKED_IN,
                    STAFF_STATUS_CHECKED_OUT,
                    STAFF_STATUS_REPLACEMENT_REQUESTED,
                    STAFF_STATUS_STANDBY_REPLACEMENT_REQUESTED,
                ], $uss)) {
                    return SHIFT_STATUS_PAST;
                }
                if (in_array(STAFF_STATUS_COMPLETED, $uss)) {
                    return SHIFT_STATUS_STAFF_COMPLETED;
                }
            }
            return SHIFT_STATUS_PAST;

        } else {
            $uss = $this->roleStaff()
                ->whereIn('staff_status_id', [
                    STAFF_STATUS_APPLIED,
                    STAFF_STATUS_SELECTED,
                    STAFF_STATUS_CONFIRMED,
                    STAFF_STATUS_CHECK_IN_ATTEMPTED,
                    STAFF_STATUS_CHECKED_IN,
                    STAFF_STATUS_NO_SHOW,
                    STAFF_STATUS_CHECKED_OUT,
                    STAFF_STATUS_REPLACEMENT_REQUESTED,
                    STAFF_STATUS_STANDBY_REPLACEMENT_REQUESTED,
                    STAFF_STATUS_COMPLETED,
                ])
                ->pluck('staff_status_id')->all();

            $num = count($uss);
            if ($num) {
                if (in_array(STAFF_STATUS_NO_SHOW, $uss)) {
                    return SHIFT_STATUS_NO_SHOW;
                }
                if (in_array([
                    STAFF_STATUS_REPLACEMENT_REQUESTED,
                    STAFF_STATUS_STANDBY_REPLACEMENT_REQUESTED,
                ], $uss)) {
                    return SHIFT_STATUS_REPLACEMENT_REQUESTED;
                }
                if ($num < $this->num_required) {
                    return SHIFT_STATUS_UNFILLED;
                }
                if (in_array(STAFF_STATUS_SELECTED, $uss)) {
                    return SHIFT_STATUS_FILLED;
                }
                if (in_array(STAFF_STATUS_CONFIRMED, $uss)) {
                    return SHIFT_STATUS_CONFIRMED;
                }
                if (in_array(STAFF_STATUS_CHECK_IN_ATTEMPTED, $uss)) {
                    return SHIFT_STATUS_CONFIRMED; //if check in attempted then leave at confirmed
                }
                if (in_array(STAFF_STATUS_CHECKED_IN, $uss)) {
                    return SHIFT_STATUS_CHECKED_IN;
                }
                if (in_array(STAFF_STATUS_CHECKED_OUT, $uss)) {
                    return SHIFT_STATUS_CHECKED_OUT;
                }
                if (in_array(STAFF_STATUS_COMPLETED, $uss)) {
                    return SHIFT_STATUS_STAFF_COMPLETED;
                }
                return SHIFT_STATUS_ENOUGH_APPLICANTS;
            }
            return SHIFT_STATUS_UNFILLED;
        }
    }

    public function numApplicants()
    {
        return $this->roleStaff()->where('staff_status_id', STAFF_STATUS_APPLIED)->get()->count();
    }

    public function numNa()
    {
        return $this->roleStaff()->whereIn('staff_status_id', STAFF_STATUSES_NA)->get()->count();
    }

    public function numSelected()
    {
        return $this->roleStaff()->whereIn('staff_status_id', STAFF_STATUSES_SELECTED)->get()->count();
    }

    public function numStandby()
    {
        return $this->roleStaff()->whereIn('staff_status_id', STAFF_STATUSES_STANDBY)->get()->count();
    }

    //has a role's end time past?
    public function past()
    {
        $this->loadMissing('shift');
        $past = 0;
        if ($this->role_end) {
            $this->role_end = Utilities::applyTimezone($this->role_end, $this->shift->tz);
            if (strtotime($this->role_end) < date('U')) {
                $past = 1;
            }
        } else {
            $past = $this->shift->past();
        }
        return $past;
    }

    public function userEligible($userId)
    {
        $u = \App\User::select('active', 'works_here', 'sex', 'dob')->where('id', $userId)->firstOrFail();

        //user works here and active?
        if (!$u->works_here || $u->active != 'active') {
            return 'Outsource or inactive';
        }

        // role outsourced?
        if ($this->outsource_company_id) {
            return 'Outsourced role';
        }

        //sex requirement
        if ($this->sex && $this->sex != $u->sex) {
            return 'Sex requirement not met';
        }

        //other requirements
        $reqs = \App\RoleRequirement::where('shift_role_id', $this->id)->get();
        foreach ($reqs as $req) {
            switch ($req->requirement) {

                case 'age':
                    if (!is_numeric($u->age())) {
                        return "Date of birth  not entered on profile.";
                    }
                    if ($req->operator == '>') {
                        if ($u->age() <= $req->value) {
                            return "Age requirement not met";
                        }
                    } elseif ($req->operator == '<') {
                        if ($u->age() >= $req->value) {
                            return "Age requirement not met";
                        }
                    }
                    break;

                case 'custom_rating':
                    $r = $u->ratings()->where('rating_id', $req->other_id)->first();
                    if ($r) {
                        if ($req->operator == '=') {
                            if ($r->score != $req->value) {
                                return "Rating $req->other_id requirement not met";
                            }
                        } elseif ($req->operator == '>') {
                            if ($r->score <= $req->value) {
                                return "Rating $req->other_id requirement not met";
                            }
                        } elseif ($req->operator == '<') {
                            if ($r->score >= $req->value) {
                                return "Rating $req->other_id requirement not met";
                            }
                        }
                    } else {
                        return "Rating requirement not met";
                    }
                    break;

                case 'performance_rating':
                    //average performance rating TODO
                    break;

                case 'attribute':
                    $a = $u->attributes()->where('attribute_id', $req->other_id)->first();
                    if ($req->operator == '=' && !$a) {
                        return "Attribute $req->other_id requirement not met";
                    } elseif ($req->operator == '!=' && $a) {
                        return "Attribute $req->other_id requirement not met";
                    }
                    break;

                case 'profile_element':
                    $e = $u->profileDate()->where('profile_element_id', $req->other_id)->first();
                    if ($req->operator == '=' && (!$e || strcasecmp($e->data, $req->value) != 0)) {
                        return "Profile element $req->other_id requirement not met";
                    } elseif ($req->operator == '!=' && $e && strcasecmp($e->data, $req->value) == 0) {
                        return "Profile element $req->other_id requirement not met";
                    } elseif ($req->operator == '>' && $e->data <= $e->value) {
                        return "Profile element $req->other_id requirement not met";
                    } elseif ($req->operator == '<' && $e->data >= $e->value) {
                        return "Profile element $req->other_id requirement not met";
                    }
                    break;
            }
        }

        return '';
    }

    public function payCategory()
    {
        return $this->belongsTo('App\PayCategory');
    }

    public function rolePayItems()
    {
        return $this->hasMany('App\RolePayItem');
    }

    public function roleRequirements()
    {
        return $this->hasMany('App\RoleRequirement');
    }

    public function roleStaff()
    {
        return $this->hasMany('App\RoleStaff');
    }

    public function shift()
    {
        return $this->belongsTo('App\Shift');
    }
}
