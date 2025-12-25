<?php

namespace App\Providers;

use App\Services\Instance\InstanceFileManager;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(InstanceFileManager::class, function ($app) {
            return new InstanceFileManager(
                Storage::disk(config('converting.storage'))
            );
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
