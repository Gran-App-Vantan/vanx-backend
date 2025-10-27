<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\User;


class AuthServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //î
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->registerPolicies();
        Gate::define('is-dealer', function (User $user) {
            return $user->user_job == 'dealer' || $user->user_job == 'admin';
        });
        Gate::define('is-admin', function (User $user) {
            return $user->user_job == 'admin';
        });
    }
}
