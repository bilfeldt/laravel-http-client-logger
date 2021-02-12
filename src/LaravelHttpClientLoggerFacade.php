<?php

namespace Bilfeldt\LaravelHttpClientLogger;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Bilfeldt\LaravelHttpClientLogger\LaravelHttpClientLogger
 */
class LaravelHttpClientLoggerFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'laravel-http-client-logger';
    }
}
