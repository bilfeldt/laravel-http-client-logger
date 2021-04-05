<?php

namespace Bilfeldt\LaravelHttpClientLogger\Tests;

use Bilfeldt\LaravelHttpClientLogger\HttpLogger;
use Bilfeldt\LaravelHttpClientLogger\PsrMessageToStringConverter;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use TiMacDonald\Log\LogFake;

class HttpLoggerTest extends TestCase
{
    protected HttpLogger $logger;

    protected Request $request;

    public function setUp(): void
    {
        parent::setUp();

        $this->logger = new HttpLogger(new PsrMessageToStringConverter);
        $this->request = new Request('GET', 'https://example.com/path?query=ABCDEF', ['header1' => 'HIJKL'], 'TestRequestBody');
    }

    public function test_response_code_200_logs_debug_level()
    {
        Log::swap(new LogFake);

        $this->logger->log($this->request, new Response(200), 0.2);

        Log::assertLogged('debug');
    }

    public function test_response_code_300_logs_info_level()
    {
        Log::swap(new LogFake);

        $this->logger->log($this->request, new Response(300), 0.2);

        Log::assertLogged('info');
    }

    public function test_response_code_400_logs_error_level()
    {
        Log::swap(new LogFake);

        $this->logger->log($this->request, new Response(400), 0.2);

        Log::assertLogged('error');
    }

    public function test_response_code_400_logs_critical_level()
    {
        Log::swap(new LogFake);

        $this->logger->log($this->request, new Response(500), 0.2);

        Log::assertLogged('critical');
    }

    public function test_log_contains_request_header()
    {
        Log::swap(new LogFake);

        $this->logger->log($this->request, new Response(200), 0.2);

        Log::assertLogged('debug', function ($message, $context) {
            return Str::contains($message, 'header1: HIJKL');
        });
    }

    public function test_log_contains_request_body()
    {
        Log::swap(new LogFake);

        $this->logger->log($this->request, new Response(200), 0.2);

        Log::assertLogged('debug', function ($message, $context) {
            return Str::contains($message, 'TestRequestBody');
        });
    }

    public function test_log_contains_response_header()
    {
        Log::swap(new LogFake);

        $this->logger->log($this->request, new Response(200, ['header2' => 'XYZ']), 0.2);

        Log::assertLogged('debug', function ($message, $context) {
            return Str::contains($message, 'header2: XYZ');
        });
    }

    public function test_log_contains_response_body()
    {
        Log::swap(new LogFake);

        $this->logger->log($this->request, new Response(200, [], 'TestResponseBody'), 0.2);

        Log::assertLogged('debug', function ($message, $context) {
            return Str::contains($message, 'TestResponseBody');
        });
    }

    public function test_logs_context()
    {
        Log::swap(new LogFake);

        $this->logger->log($this->request, new Response(200), 0.2, ['context']);

        Log::assertLogged('debug', function ($message, $context) {
            return $context == ['context'];
        });
    }

    public function test_replaces_placeholders_from_request()
    {
        Log::swap(new LogFake);

        $this->logger->log($this->request, new Response(200), 0.2, ['test123'], ['replace' => ['example.com' => 'mock.org']]);

        Log::assertLogged('debug', function ($message, $context) {
            return Str::contains($message, 'mock.org')
                && ! Str::contains($message, 'example.com')
                && $context == ['test123'];
        });
    }

    public function test_replaces_placeholders_from_response()
    {
        Log::swap(new LogFake);

        $this->logger->log($this->request, new Response(200, [], 'My name is John Doe'), 0.2, ['test123'], ['replace' => ['Doe' => 'Smith']]);

        Log::assertLogged('debug', function ($message, $context) {
            return Str::contains($message, 'Smith')
                && ! Str::contains($message, 'Doe')
                && $context == ['test123'];
        });
    }
}
