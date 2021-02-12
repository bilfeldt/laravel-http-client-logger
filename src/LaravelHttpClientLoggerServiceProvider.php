<?php

namespace Bilfeldt\LaravelHttpClientLogger;

use Bilfeldt\LaravelHttpClientLogger\Commands\LaravelHttpClientLoggerCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class LaravelHttpClientLoggerServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('laravel-http-client-logger')
            ->hasConfigFile()
            ->hasViews()
            ->hasMigration('create_laravel_http_client_logger_table')
            ->hasCommand(LaravelHttpClientLoggerCommand::class);
    }
}
