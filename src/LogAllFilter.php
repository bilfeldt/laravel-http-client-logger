<?php

namespace Bilfeldt\LaravelHttpClientLogger;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class LogAllFilter implements HttpLoggingFilterInterface
{
    public function shouldLog(
        RequestInterface $request,
        ResponseInterface $response,
        float $sec,
        array $context = [],
        array $config = []
    ): bool
    {
        return true;
    }
}
