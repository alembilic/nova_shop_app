<?php

namespace App\Providers;

use App\Models\Store;
use App\Models\User;
use App\Models\UserStoresPivot;
use App\Policies\StorePolicy;
use App\Policies\UserPolicy;
use App\Policies\UserStorePolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
        User::class => UserPolicy::class,
        Store::class => StorePolicy::class,
        UserStoresPivot::class => UserStorePolicy::class
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        //
    }
}
