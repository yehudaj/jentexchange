<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\Entertainer;
use App\Models\Customer;
use App\Policies\EntertainerPolicy;
use App\Policies\CustomerPolicy;

class AuthServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Gate::policy(Entertainer::class, EntertainerPolicy::class);
        Gate::policy(Customer::class, CustomerPolicy::class);
    }
}
