<?php

namespace Bilfeldt\LaravelHttpClientLogger\Listeners;

use GuzzleHttp\MessageFormatter;
use Illuminate\Http\Client\Events\RequestSending;
use Illuminate\Support\Facades\Log;
use Psr\Http\Message\RequestInterface;

class LogRequestSending
{
    /**
     * Handle the event.
     *
     * @param RequestSending $event
     * @return void
     */
    public function handle(RequestSending $event)
    {
        return $event->request->withMiddleware((new LoggingMiddleware(
            $logger ?? app(HttpLoggerInterface::class),
            $filter ?? app(HttpLoggingFilterInterface::class)
        ))->__invoke($context = [], $config = []));   
    }

   
}