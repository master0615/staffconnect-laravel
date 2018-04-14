<?php

namespace App\Listeners;

use App\Events\RoleStaffSaved;

class UpdateRoleStaffShiftStatus
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
     * @param  RoleStaffSaved $event
     * @return void
     */
    public function handle(RoleStaffSaved $event)
    {
        $rs = $event->rs;

        if (isset($rs->staff_status_id) && $rs->isDirty('staff_status_id')) {
            //calc shift status
            $rs->loadMissing('shiftRole');
            $s = $rs->shiftRole->shift->updateStatus();
        }
    }
}
