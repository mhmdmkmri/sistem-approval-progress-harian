<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        //
    ];

    public function boot()
    {
        $this->registerPolicies();

        Gate::define('isAdmin', fn($user) => $user->role === 'admin');
        Gate::define('isOfficer', fn($user) => $user->role === 'officer');
        Gate::define('isPM', fn($user) => $user->role === 'pm');
        Gate::define('isVP', fn($user) => strtolower($user->role) === 'vpqhse');

        // Gate baru untuk approve (gabungan isPM atau isVP)
        Gate::define('canApprove', function ($user) {
            return in_array(strtolower($user->role), ['pm', 'vpqhse']);
        });
    }
}
