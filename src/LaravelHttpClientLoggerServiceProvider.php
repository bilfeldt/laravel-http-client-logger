<?php

namespace Bilfeldt\LaravelHttpClientLogger;

use Bilfeldt\LaravelHttpClientLogger\Middleware\LoggingMiddleware;
use Illuminate\Http\Client\PendingRequest;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class LaravelHttpClientLoggerServiceProvider extends PackageServiceProvider
{
    /**
     * This method is used by spatie/laravel-package-tools to setup the package.
     *
     * More info: https://github.com/spatie/laravel-package-tools
     *
     * @param  Package  $package
     */
    public function configurePackage(Package $package): void
    {
        $package
            ->name('laravel-http-client-logger')
            ->hasConfigFile();
    }

    /**
     * Will be called at the end of the boot method by spatie/laravel-package-tools.
     *
     * More info: https://github.com/spatie/laravel-package-tools
     */
    public function packageBooted()
    {
        PendingRequest::macro('log', function (
            $context = [],
            ?HttpLoggerInterface $logger = null,
            ?HttpLoggingFilterInterface $filter = null
        ) {
            return $this->withMiddleware((new LoggingMiddleware(
                $logger ?? resolve(HttpLoggerInterface::class),
                $filter ?? resolve(HttpLoggingFilterInterface::class)
            ))->__invoke($context));
        });

        PendingRequest::macro('logWhen', function (
            $condition,
            $context = [],
            ?HttpLoggerInterface $logger = null,
            ?HttpLoggingFilterInterface $filter = null
        ) {
            if ($condition) {
                return $this->log($context, $logger, $filter);
            } else {
                return $this;
            }
        });
    }

    /**
     * Will be called at the end of the register method by spatie/laravel-package-tools.
     *
     * More info: https://github.com/spatie/laravel-package-tools
     */
    public function packageRegistered()
    {
        $this->app->bind(HttpLoggerInterface::class, function ($app) {
            return $app->make(config('http-client-logger.logger'));
        });

        $this->app->bind(HttpLoggingFilterInterface::class, function ($app) {
            return $app->make(config('http-client-logger.filter'));
        });
    }
}
