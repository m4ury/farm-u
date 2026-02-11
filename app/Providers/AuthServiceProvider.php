<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        //
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();  

        Gate::define('areas', function ($user) {
            if ($user->type == 'admin') {   
                return true;
            }
            return false;
        });

        Gate::define('farmacos', function ($user) {
            if ($user->type == 'admin' || $user->type == 'farmacia') {
                return true;
            }
            return false;
        });

        Gate::define('salidas', function ($user) {
            if ($user->type == 'admin' || $user->type == 'farmacia') {
                return true;
            }
            return false;
        });

        Gate::define('admin', function ($user) {
            if ($user->type == 'admin') {
                return true;
            }
            return false;
        });

        Gate::define('farmacia', function ($user) {
            if ($user->type == 'farmacia' || $user->type == 'admin') {
                return true;
            }
            return false;
        });
        
        Gate::define('pedidos', function ($user) {
            $allowedTypes = ['admin', 'farmacia', 'area'];
            if (in_array($user->type, $allowedTypes)) {
                return true;
            }
            return false;
        });
    }
}
