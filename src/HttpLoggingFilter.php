<?php

namespace Bilfeldt\LaravelHttpClientLogger;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class HttpLoggingFilter implements HttpLoggingFilterInterface
{
    public function shouldLog(
        RequestInterface $request,
        ResponseInterface $response,
        float $sec,
        array $context = [],
        array $config = []
    ): bool {
        if (! config('http-client-logger.enabled')) {
            return false;
        }

        if (config('http-client-logger.filter_all')) {
            return true;
        }

        if (config('http-client-logger.filter_2xx') && $response->getStatusCode() >= 200 && $response->getStatusCode() < 300) {
            return true;
        }

        if (config('http-client-logger.filter_3xx') && $response->getStatusCode() >= 300 && $response->getStatusCode() < 400) {
            return true;
        }

        if (config('http-client-logger.filter_4xx') && $response->getStatusCode() >= 400 && $response->getStatusCode() < 500) {
            return true;
        }

        if (config('http-client-logger.filter_5xx') && $response->getStatusCode() >= 500 && $response->getStatusCode() < 600) {
            return true;
        }

        if (config('http-client-logger.filter_slow') < $sec) {
            return true;
        }

        return false;
    }
}
