<?php

namespace Kiwilan\Notifier;

use Kiwilan\Notifier\Commands\NotifierCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class NotifierServiceProvider extends PackageServiceProvider
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
