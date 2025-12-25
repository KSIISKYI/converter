<?php

namespace App\Providers;

use App\Models\Instance;
use App\Repos\Instance\EloquentInstance;
use App\Repos\Instance\InstanceRepoInterface;
use Illuminate\Support\ServiceProvider;

class RepoServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(InstanceRepoInterface::class, function ($app) {
            return new EloquentInstance(
                new Instance()
            );
        });
    }
}
