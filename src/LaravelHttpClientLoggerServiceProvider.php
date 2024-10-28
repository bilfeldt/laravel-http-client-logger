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
     * @param Package $package
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
            $config = [],
            ?HttpLoggerInterface $logger = null,
            ?HttpLoggingFilterInterface $filter = null
        ): PendingRequest {
            /** @var \Illuminate\Http\Client\PendingRequest $this */
            return $this->withMiddleware((new LoggingMiddleware(
                $logger ?? app(HttpLoggerInterface::class),
                $filter ?? app(HttpLoggingFilterInterface::class)
            ))->__invoke($context, $config));
        });

        PendingRequest::macro('logWhen', function (
            $condition,
            $context = [],
            $config = [],
            ?HttpLoggerInterface $logger = null,
            ?HttpLoggingFilterInterface $filter = null
        ): PendingRequest {
            if (value($condition)) {
                /** @var \Illuminate\Http\Client\PendingRequest $this */
                return $this->log($context, $config, $logger, $filter);
            } else {
                /** @var \Illuminate\Http\Client\PendingRequest $this */
                return $this;
            }
        });

        PendingRequest::macro('logWith', function (HttpLoggerInterface $logger = null): PendingRequest {
            /** @var \Illuminate\Http\Client\PendingRequest $this */
            return $this->withMiddleware((new LoggingMiddleware($logger, new LogAllFilter()))->__invoke());
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

        $this->app->singleton(MessageAccessor::class, function ($app) {
            return new MessageAccessor(
                config('http-client-logger.replace_json', []),
                config('http-client-logger.replace_query', []),
                config('http-client-logger.replace_headers', []),
                config('http-client-logger.replace_values', []),
            );
        });
    }
}
