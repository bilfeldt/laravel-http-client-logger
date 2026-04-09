<?php

namespace Bilfeldt\LaravelHttpClientLogger\Listeners;

use GuzzleHttp\MessageFormatter;
use Illuminate\Http\Client\Events\ResponseReceived;
use Illuminate\Support\Facades\Log;
use Bilfeldt\LaravelHttpClientLogger\Middleware\LoggingMiddleware;

class LogResponseReceived
{
    /**
     * Handle the event.
     *
     * @param ResponseReceived $event
     * @return void
     */
    public function handle(ResponseReceived $event)
    {
        $event->response->withMiddleware((new LoggingMiddleware(
            $logger ?? app(HttpLoggerInterface::class),
            $filter ?? app(HttpLoggingFilterInterface::class)
        ))->__invoke($context = [], $config = []));    
    }
}