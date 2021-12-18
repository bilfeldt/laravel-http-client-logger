<?php

namespace Bilfeldt\LaravelHttpClientLogger;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class NullLogger implements HttpLoggerInterface
{
    public function log(
        RequestInterface $request,
        ResponseInterface $response,
        float $sec,
        array $context = [],
        array $config = []
    ): void {
        // Intentionally doing exactly nothing
    }
}
