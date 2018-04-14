<?php

namespace App\Jobs;

use App\User;
use Hyn\Tenancy\Queue\TenantAwareJob;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;

class GeocodeUserAddress implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, TenantAwareJob;

    protected $user;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if ($this->user->geocode_status == 'ready') {
            if (strlen($this->user->address) > 5 && strlen($this->user->city) > 1) {

                $addr = $this->user->address . ',' . $this->user->city;
                if (strlen($this->user->state) > 1) {
                    $addr .= ',' . $this->user->state;
                }
                if (strlen($this->user->postcode) > 3) {
                    $addr .= ',' . $this->user->postcode;
                }

                if ($res = \App\Helpers\Utilities::geocodeAddress($addr)) {
                    $this->user->lat = $res['lat'];
                    $this->user->lon = $res['lon'];
                    $this->user->geocode_status = 'success';
                } else {
                    $this->user->geocode_status == 'failed';
                }

            } else {
                $this->user->geocode_status == 'not_ready';
            }

            $this->user->save();
        }
    }
}
