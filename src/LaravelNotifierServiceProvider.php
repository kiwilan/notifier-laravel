<?php

namespace Kiwilan\LaravelNotifier;

use Kiwilan\LaravelNotifier\Commands\NotifierCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class LaravelNotifierServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('notifier')
            ->hasConfigFile()
            ->hasCommand(NotifierCommand::class);
    }
}
