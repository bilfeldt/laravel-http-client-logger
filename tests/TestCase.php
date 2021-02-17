<?php

namespace Bilfeldt\LaravelHttpClientLogger\Tests;

use Bilfeldt\LaravelHttpClientLogger\LaravelHttpClientLoggerServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

abstract class TestCase extends Orchestra
{
    public function setUp(): void
    {
        ray()->newScreen("Test {$this->getName()}");

        parent::setUp();

        config()->set('http-client-logger.filtering.2xx', true);
        config()->set('http-client-logger.filtering.3xx', true);
        config()->set('http-client-logger.filtering.4xx', true);
        config()->set('http-client-logger.filtering.5xx', true);
        config()->set('http-client-logger.filtering.slow', true);
    }

    protected function getPackageProviders($app)
    {
        return [
            LaravelHttpClientLoggerServiceProvider::class,
        ];
    }
}
