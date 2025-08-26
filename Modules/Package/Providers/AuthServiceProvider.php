<?php

namespace Modules\Package\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Modules\Package\Models\Package;
use Modules\Package\Policies\PackagePolicy;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        Package::class => PackagePolicy::class,
    ];
    public function boot(): void
    {
        $this->registerPolicies();
    }
}
