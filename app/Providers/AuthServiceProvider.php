<?php

namespace App\Providers;

use App\Models\VacancyMagang;
use App\Policies\VacancyMagangPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
  protected $policies = [
    VacancyMagang::class => VacancyMagangPolicy::class,
  ];

  public function boot(): void
  {
    $this->registerPolicies();
  }
}
