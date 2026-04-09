<?php

namespace Bilfeldt\LaravelHttpClientLogger\Providers;

use Bilfeldt\LaravelHttpClientLogger\Listeners\LogRequestSending;
use Bilfeldt\LaravelHttpClientLogger\Listeners\LogResponseReceived;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as BaseEventServiceProvider;
use Illuminate\Http\Client\Events\RequestSending;
use Illuminate\Http\Client\Events\ResponseReceived;

class EventServiceProvider extends BaseEventServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        RequestSending::class => [
            LogRequestSending::class,
        ],
        ResponseReceived::class => [
            LogResponseReceived::class,
        ],
    ];

    /**
     * Get the events and handlers.
     *
     * @return array
     */
    public function listens()
    {
        return config('http-client-logger.global') ? $this->listen : [];
    }
}