<?php

namespace App\Providers;

use App\Models\Todo;
use App\Policies\ToDoPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        Gate::policy(Todo::class, ToDoPolicy::class);
    }
}
