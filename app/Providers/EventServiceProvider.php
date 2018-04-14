<?php
namespace App\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{

    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        'Laravel\Passport\Events\AccessTokenCreated' => [
            'App\Listeners\RevokeOldTokens',
            'App\Listeners\UpdateUserLastLogin',
        ],

        'Laravel\Passport\Events\RefreshTokenCreated' => [
            'App\Listeners\PruneOldTokens',
        ],

        \Illuminate\Auth\Events\Login::class => [
            \App\Listeners\UpdateSupportLastLogin::class,
        ],

        'Hyn\Tenancy\Events\Websites\Created' => [
            'App\Listeners\CreateTenantDirectoryStructure',
        ],

        'App\Events\RoleStaffSaved' => [
            'App\Listeners\UpdateRoleStaffShiftStatus',
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        //
    }
}
