<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Spatie\Permission\Traits\HasRoles;

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
        Gate::define('admin-financeiro', function ($user) {
            return $user->hasAnyRole(['admin', 'financeiro']);
        });

        // para mostrar a badge de quem tem a role 'financeiro'
        Gate::define('financeiro', function ($user) {
            return $user->hasAnyRole(['financeiro']);
        });

        // para mostrar a badge de quem tem a permission 'revistas.empresa', mas que não é 'financeiro' nem 'admin'
        Gate::define('empresa', function ($user) {
            return $user->hasAnyPermission(['revistas.empresa']) && $user->roles->isEmpty();;
        });

        // para mostrar a badge de quem tem a permission 'revistas.revista', mas que não é 'financeiro' nem 'admin'
        Gate::define('revista', function ($user) {
            return $user->hasAnyPermission(['revistas.revista']) && $user->roles->isEmpty();;
        });
    }
}