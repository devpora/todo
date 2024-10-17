<?php

namespace App\Providers;

use App\Models\Todo;
use App\Policies\ToDoPolicy;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;


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
