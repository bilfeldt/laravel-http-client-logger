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

        if (config('http-client-logger.filtering.2xx') && $response->getStatusCode() >= 200 && $response->getStatusCode() < 300) {
            return true;
        }

        if (config('http-client-logger.filtering.3xx') && $response->getStatusCode() >= 300 && $response->getStatusCode() < 400) {
            return true;
        }

        if (config('http-client-logger.filtering.4xx') && $response->getStatusCode() >= 400 && $response->getStatusCode() < 500) {
            return true;
        }

        if (config('http-client-logger.filtering.5xx') && $response->getStatusCode() >= 500 && $response->getStatusCode() < 600) {
            return true;
        }

        if (config('http-client-logger.filtering.slow') < $sec) {
            return true;
        }

        return false;
    }
}
