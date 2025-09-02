<?php

namespace App\Providers;

use App\Models\Absent;
use App\Models\Cuti;
use App\Policies\AbsentPolicy;
use App\Policies\CutiPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Absent::class => AbsentPolicy::class,
        Cuti::class => CutiPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        //
    }
}