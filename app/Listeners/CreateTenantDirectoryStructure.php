<?php
namespace App\Listeners;

class CreateTenantDirectoryStructure
{

    private $dirs;

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        // directories to be created for each tenant
        $this->dirs = [
            'check_photos',
            'check_photos/thumbs',
            'profile_documents',
            'profile_documents/thumbs',
            'profile_photos',
            'profile_photos/thumbs',
            'profile_photos/tthumbs',
            'profile_videos',
            'profile_videos/thumbs',
        ];
    }

    /**
     * Handle the event.
     *
     * @param  object $event
     * @return void
     */
    public function handle(\Hyn\Tenancy\Events\Websites\Created $event)
    {
        $w = $event->website;
        $path = storage_path('app/tenancy/tenants/' . $w->uuid . '/');

        foreach ($this->dirs as $dir) {
            if (!file_exists($path . $dir)) {
                mkdir($path . $dir, 0755, true);
            }
        }
    }
}
