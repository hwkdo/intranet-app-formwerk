<?php

namespace Hwkdo\IntranetAppFormwerk;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Livewire\Livewire;
use Livewire\Volt\Volt;

class IntranetAppFormwerkServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('intranet-app-formwerk')
            ->hasConfigFile()
            ->hasViews()
            ->discoversMigrations();
    }

    public function boot(): void
    {
        parent::boot();

        $this->app->booted(function () {
            Volt::mount(__DIR__.'/../resources/views/livewire');
            Livewire::addNamespace('intranet-app-formwerk', __DIR__.'/../resources/views/livewire');
        });

        $this->loadRoutesFrom(__DIR__.'/../routes/web.php');
        $this->loadRoutesFrom(__DIR__.'/../routes/api.php');
    }
}
