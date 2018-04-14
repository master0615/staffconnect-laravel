<?php
namespace App\Listeners;

use DB;
use Illuminate\Auth\Events\Login;

class UpdateSupportLastLogin
{

    /**
     * Handle the event.
     *
     * @param \Illuminate\Auth\Events\Login $event
     * @return void
     */
    public function handle(Login $event)
    {
        // Update user last login date/time
        DB::table('supports')->where('id', $event->user->id)->update([
            'last_login' => date('Y-m-d H:i:s')
        ]);
    }
}