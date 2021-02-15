<?php

namespace Bilfeldt\LaravelHttpClientLogger;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

interface HttpLoggerInterface
{
    public function log(
        RequestInterface $request,
        ResponseInterface $response,
        float $sec,
        array $context = []): void;
}
