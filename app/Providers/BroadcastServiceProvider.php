<?php

namespace App\Providers;

use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\ServiceProvider;

class BroadcastServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Broadcast::routes(['middleware' => ['web']]);

        Broadcast::channel('users', function () {
            return true;
        });

        Broadcast::channel('telegram-messages', function () {
            return true;
        });
    }
}
