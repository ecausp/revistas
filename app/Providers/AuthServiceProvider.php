<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        // para garantir que o menu do laravel-usp-theme seja visto por ambas as roles admin e revista;
        Gate::define('admin-revista', function ($user) {
            return $user->hasAnyRole(['admin', 'revista']);
        });

        Gate::define('revista', function ($user) {
            return $user->hasAnyRole(['revista']);
        });
    }
}