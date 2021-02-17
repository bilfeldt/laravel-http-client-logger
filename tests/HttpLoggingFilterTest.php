<?php

namespace Bilfeldt\LaravelHttpClientLogger\Tests;

use Bilfeldt\LaravelHttpClientLogger\HttpLoggingFilter;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;

class HttpLoggingFilterTest extends TestCase
{
    protected HttpLoggingFilter $filter;

    public function setUp(): void
    {
        parent::setUp();

        $this->filter = new HttpLoggingFilter();
    }

    public function test_filter_log_2xx()
    {
        config(['http-client-logger.filtering.2xx' => true]);

        $this->assertTrue($this->filter->shouldLog(
            new Request('GET', '/'),
            new Response(200),
            0
        ));

        config(['http-client-logger.filtering.2xx' => false]);

        $this->assertFalse($this->filter->shouldLog(
            new Request('GET', '/'),
            new Response(200),
            0
        ));
    }

    public function test_filter_log_3xx()
    {
        config(['http-client-logger.filtering.3xx' => true]);

        $this->assertTrue($this->filter->shouldLog(
            new Request('GET', '/'),
            new Response(300),
            0
        ));

        config(['http-client-logger.filtering.3xx' => false]);

        $this->assertFalse($this->filter->shouldLog(
            new Request('GET', '/'),
            new Response(300),
            0
        ));
    }

    public function test_filter_log_4xx()
    {
        config(['http-client-logger.filtering.4xx' => true]);

        $this->assertTrue($this->filter->shouldLog(
            new Request('GET', '/'),
            new Response(400),
            0
        ));

        config(['http-client-logger.filtering.4xx' => false]);

        $this->assertFalse($this->filter->shouldLog(
            new Request('GET', '/'),
            new Response(400),
            0
        ));
    }

    public function test_filter_log_5xx()
    {
        config(['http-client-logger.filtering.5xx' => true]);

        $this->assertTrue($this->filter->shouldLog(
            new Request('GET', '/'),
            new Response(500),
            0
        ));

        config(['http-client-logger.filtering.5xx' => false]);

        $this->assertFalse($this->filter->shouldLog(
            new Request('GET', '/'),
            new Response(500),
            0
        ));
    }

    public function test_filter_log_slow()
    {
        config(['http-client-logger.filtering.2xx' => false]);
        config(['http-client-logger.filtering.slow' => 1.5]);

        $this->assertFalse($this->filter->shouldLog(
            new Request('GET', '/'),
            new Response(200),
            1.0
        ));

        $this->assertTrue($this->filter->shouldLog(
            new Request('GET', '/'),
            new Response(200),
            2.0
        ));
    }
}
