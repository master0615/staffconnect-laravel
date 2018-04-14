<?php
namespace App\Listeners;

use Laravel\Passport\Events\AccessTokenCreated;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use DB;

class UpdateUserLastLogin
{

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param UserLoggedIn $event
     * @return void
     */
    public function handle(AccessTokenCreated $event)
    {
        // use db over model so that updated_at isn't modified
        DB::connection('tenant')->table('users')
            ->where('id', $event->userId)
            ->update([
            'last_login' => date('Y-m-d H:i:s')
        ]);
    }
}
