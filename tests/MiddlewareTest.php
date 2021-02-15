<?php

namespace Bilfeldt\LaravelHttpClientLogger\Tests;

use Bilfeldt\LaravelHttpClientLogger\HttpLoggerInterface;
use Bilfeldt\LaravelHttpClientLogger\HttpLoggingFilterInterface;
use Bilfeldt\LaravelHttpClientLogger\Middleware\LoggingMiddleware;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Mockery\MockInterface;

class MiddlewareTest extends TestCase
{
    public function test_logs_when_filter_returns_true()
    {
        $logger = $this->mock(HttpLoggerInterface::class, function (MockInterface $mock) {
            $mock->shouldReceive('log')->once();
        });

        $filter = $this->mock(HttpLoggingFilterInterface::class, function (MockInterface $mock) {
            $mock->shouldReceive('shouldLog')->once()->andReturnTrue();
        });

        $middleware = new LoggingMiddleware($logger, $filter);

        $mockHandler = new MockHandler([
            new Response(200, ['X-Foo' => 'Bar'], 'Hello, World'),
        ]);

        $handlerStack = HandlerStack::create($mockHandler);
        $handlerStack->push($middleware());
        $client = new Client(['handler' => $handlerStack]);

        $client->request('GET', '/');
    }

    public function test_does_not_log_when_filter_returns_false()
    {
        $logger = $this->mock(HttpLoggerInterface::class, function (MockInterface $mock) {
            $mock->shouldNotReceive('log');
        });

        $filter = $this->mock(HttpLoggingFilterInterface::class, function (MockInterface $mock) {
            $mock->shouldReceive('shouldLog')->once()->andReturnFalse();
        });

        $middleware = new LoggingMiddleware($logger, $filter);

        $mockHandler = new MockHandler([
            new Response(200, ['X-Foo' => 'Bar'], 'Hello, World'),
        ]);

        $handlerStack = HandlerStack::create($mockHandler);
        $handlerStack->push($middleware());
        $client = new Client(['handler' => $handlerStack]);

        $client->request('GET', '/');
    }
}
