<?php

namespace App\Jobs;

use App\Http\NotificationService;
use Hyn\Tenancy\Queue\TenantAwareJob;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;

class SendNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, TenantAwareJob;

    protected $token;
    protected $payload;
    protected $sender;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($token, $sender, $payload)
    {
        $this->token = $token;
        $this->sender = $sender;
        $this->payload = $payload;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        NotificationService::pushFirebase($this->token, $this->sender, $this->payload);
    }
}
