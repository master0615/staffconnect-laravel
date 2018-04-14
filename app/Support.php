<?php
namespace App;

use Hyn\Tenancy\Traits\UsesSystemConnection;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Auth;

class Support extends Authenticatable
{
    use Notifiable, UsesSystemConnection;

    protected $fillable = [
        'name',
        'email',
        'password'
    ];

    protected $hidden = [
        'password',
        'remember_token'
    ];
}
