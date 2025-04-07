<?php

namespace App\Providers;

use App\Contracts\FileServiceInterface;
use App\Contracts\TelegramServiceInterface;
use App\Services\S3FileService;
use App\Services\TelegramService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
        $this->app->bind(FileServiceInterface::class, S3FileService::class);
        $this->app->bind(TelegramServiceInterface::class, TelegramService::class);
    }
}
