<?php

namespace Bilfeldt\LaravelHttpClientLogger\Tests;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class LogAllFilterTest extends TestCase
{
    public function test_log_all_filter_returns_true()
    {
        $logAllFilter = new \Bilfeldt\LaravelHttpClientLogger\LogAllFilter();

        $this->assertTrue($logAllFilter->shouldLog(
            $this->mock(RequestInterface::class),
            $this->mock(ResponseInterface::class),
            123
        ));
    }
}
