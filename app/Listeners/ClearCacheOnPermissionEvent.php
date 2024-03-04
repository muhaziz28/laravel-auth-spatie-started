<?php

namespace App\Listeners;

use App\Events\PermissionEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

class ClearCacheOnPermissionEvent
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(PermissionEvent $event): void
    {
        Artisan::call('optimize:clear');
    }
}
