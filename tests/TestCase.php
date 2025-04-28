<?php

namespace Bilfeldt\LaravelHttpClientLogger\Tests;

use Bilfeldt\LaravelHttpClientLogger\LaravelHttpClientLoggerServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

abstract class TestCase extends Orchestra
{
    public function setUp(): void
    {
        parent::setUp();

        config()->set('http-client-logger.filter_2xx', true);
        config()->set('http-client-logger.filter_3xx', true);
        config()->set('http-client-logger.filter_4xx', true);
        config()->set('http-client-logger.filter_5xx', true);
        config()->set('http-client-logger.filter_slow', true);
    }

    protected function getPackageProviders($app)
    {
        return [
            LaravelHttpClientLoggerServiceProvider::class,
        ];
    }
}
