<?php

namespace App\Events;

use App\RoleStaff;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class RoleStaffSaved
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $rs;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(RoleStaff $rs)
    {
        $this->rs = $rs;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
}
