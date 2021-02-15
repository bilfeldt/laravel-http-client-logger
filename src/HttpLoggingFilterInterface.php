<?php


namespace Bilfeldt\LaravelHttpClientLogger;


use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

interface HttpLoggingFilterInterface
{
    public function shouldLog(
        RequestInterface $request,
        ResponseInterface $response,
        float $sec,
        array $context = []
    ): bool;
}
