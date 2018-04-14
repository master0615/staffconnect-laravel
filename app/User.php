<?php
namespace App;

use Hyn\Tenancy\Traits\UsesTenantConnection;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Auth;
use Laravel\Passport\HasApiTokens;
// use Spatie\Activitylog\Traits\CausesActivity; can't be a causer and a logger?
use Spatie\Activitylog\Traits\LogsActivity;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable, UsesTenantConnection, LogsActivity;

    protected $dates = [
        'created_at',
        'updated_at',
        'dob',
    ];

    protected $fillable = [
        'fname',
        'lname',
        'email',
        'password',
        'lvl',
        'fav',
        'ppic_a',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public static function boot()
    {
        parent::boot();

        self::saving(function ($u) {

            // set geocode flag
            if (0 && $u->isDirty('address')) {
                if (strlen($u->address) > 5) {
                    $u->geocode_status = 'ready';

                    \App\Jobs\GeocodeUserAddress::dispatch($u)
                        ->delay(now()->addMinutes(2));

                } else {
                    $u->geocode_status = 'not_ready';
                }
            }
        });
    }

    public function age()
    {
        if ($this->dob) {
            return $this->dob->diffInYears(\Carbon\Carbon::now());
        } else {
            return '?';
        }
    }

    public function attributes()
    {
        return $this->belongsToMany('App\Attribute')->using('App\AttributeUser');
    }

    public function attributeUsers()
    {
        return $this->hasMany('App\AttributeUsers');
    }

    public function clientUsers()
    {
        return $this->hasMany('App\ClientUser');
    }

    public function canEditProfile($user_id)
    {
        if (Auth::user()->loggedInAsId() == $user_id) {
            return 1;
        } elseif (Auth::user()->loggedInAs()->hasRole('owner|admin')) {
            // should admin be able to access other admins or owners?
            return 1;
        } else {}
        return 0;
    }

    public function canViewProfile($user_id)
    {
        if (Auth::user()->loggedInAsId() == $user_id) {
            return 1;
        } elseif (Auth::user()->loggedInAs()->hasRole('owner|admin')) {
            // should admin be able to access other admins or owners?
            return 1;
        } else {}
        return 0;
    }

    public function hasRole($role)
    {
        $roles = explode('|', $role);
        return in_array($this->lvl, $roles);
    }

    public function loggedInAs()
    {
        $loggedInAsId = $this->loggedInAsId();
        if (Auth::id() != $loggedInAsId) {
            return User::findOrFail($loggedInAsId);
        }
        return $this;
    }

    public function loggedInAsId()
    {
        $oat = $this->oauthAccessTokens()->where([['revoked', '0'], ['client_id', '2']])->first();
        if ($oat && $oat->logged_in_as_id) {
            return $oat->logged_in_as_id;
        }
        return $this->id;
    }

    public function managedShifts()
    {
        return $this->belongsToMany('App\Shift', 'shift_managers', 'user_id', 'shift_id')->using('App\ShiftManager');
    }

    public function name()
    {
        return $this->fname . ' ' . $this->lname;
    }

    public function oauthAccessTokens()
    {
        return $this->hasMany('\App\OauthAccessToken');
    }

    public function outsourceCompanies()
    {
        return $this->belongsToMany('App\OutsourceCompany')->using('App\OutsourceCompanyUser');
    }

    public function payLevels()
    {
        return $this->belongsToMany('App\PayLevel')->using('App\PayLevelUser');
    }

    public function profileAdminNotes()
    {
        return $this->hasMany('App\ProfileAdminNote');
    }

    public function profileData()
    {
        return $this->hasMany('App\ProfileData');
    }

    public function profileDocuments()
    {
        return $this->hasMany('App\ProfileDocument');
    }

    public function profilePhotos()
    {
        return $this->hasMany('App\ProfilePhoto');
    }

    public function profileVideos()
    {
        return $this->hasMany('App\ProfileVideo');
    }

    public function ratings()
    {
        return $this->belongsToMany('App\Rating')->withPivot('score')->using('App\RatingUser');
    }

    public function ratingUsers()
    {
        return $this->hasMany('App\RatingUser');
    }

    public function roleStaff()
    {
        return $this->hasMany('App\RoleStaff');
    }

    public function threads()
    {
        return $this->belongsToMany('App\Thread')->withTimestamps();
    }

    public function tthumb()
    {
        if ($this->ppic_a) {
            return action('Api\StorageController@getFile', ['profile_photo', $this->id, $this->ppic_a, 2]);
        } elseif ($this->sex) {
            return 'https://staffconnect.net/images/nopic_thumb_' . $this->sex . '.jpg';
        } else {
            return 'https://staffconnect.net/images/nopic_thumb_either.jpg';
        }
    }

    public function unavailabilities()
    {
        return $this->hasMany('App\Unavailability');
    }

    public function userAttributes()
    {
        return $this->hasMany('App\UserAttribute');
    }

    public function userPayLevels()
    {
        return $this->hasMany('App\PayLevelUser');
    }

    public function userTerms()
    {
        return $this->hasMany('App\TermUser');
    }

    public function userWorkAreas()
    {
        return $this->hasMany('App\UserWorkArea');
    }

    public function workAreas()
    {
        return $this->belongsToMany('App\WorkArea')->using('App\UserWorkArea');
    }
}
