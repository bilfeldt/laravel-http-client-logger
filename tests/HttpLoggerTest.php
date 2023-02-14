<?php

namespace Bilfeldt\LaravelHttpClientLogger\Tests;

use Bilfeldt\LaravelHttpClientLogger\HttpLogger;
use Bilfeldt\LaravelHttpClientLogger\PsrMessageToStringConverter;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use TiMacDonald\Log\LogEntry;
use TiMacDonald\Log\LogFake;

class HttpLoggerTest extends TestCase
{
    protected HttpLogger $logger;

    protected Request $request;

    public function setUp(): void
    {
        parent::setUp();

        $this->logger = new HttpLogger(new PsrMessageToStringConverter());
        $this->request = new Request('GET', 'https://example.com/path?query=ABCDEF', ['header1' => 'HIJKL'], 'TestRequestBody');
    }

    public function test_response_code_200_logs_debug_level()
    {
        LogFake::bind();

        $this->logger->log($this->request, new Response(200), 0.2);

        Log::assertLogged(fn (LogEntry $log) => $log->level === 'debug');
    }

    public function test_response_code_300_logs_info_level()
    {
        LogFake::bind();

        $this->logger->log($this->request, new Response(300), 0.2);

        Log::assertLogged(fn (LogEntry $log) => $log->level === 'info');
    }

    public function test_response_code_400_logs_error_level()
    {
        LogFake::bind();

        $this->logger->log($this->request, new Response(400), 0.2);

        Log::assertLogged(fn (LogEntry $log) => $log->level === 'error');
    }

    public function test_response_code_400_logs_critical_level()
    {
        LogFake::bind();

        $this->logger->log($this->request, new Response(500), 0.2);

        Log::assertLogged(fn (LogEntry $log) => $log->level === 'critical');
    }

    public function test_log_contains_request_header()
    {
        LogFake::bind();

        $this->logger->log($this->request, new Response(200), 0.2);

        Log::assertLogged(function (LogEntry $log): bool {
            return $log->level === 'debug'
                && Str::contains($log->message, 'header1: HIJKL');
        });
    }

    public function test_log_contains_request_body()
    {
        LogFake::bind();

        $this->logger->log($this->request, new Response(200), 0.2);

        Log::assertLogged(function (LogEntry $log): bool {
            return $log->level === 'debug'
                && Str::contains($log->message, 'TestRequestBody');
        });
    }

    public function test_log_contains_response_header()
    {
        LogFake::bind();

        $this->logger->log($this->request, new Response(200, ['header2' => 'XYZ']), 0.2);

        Log::assertLogged(function (LogEntry $log): bool {
            return $log->level === 'debug'
                && Str::contains($log->message, 'header2: XYZ');
        });
    }

    public function test_log_contains_response_body()
    {
        LogFake::bind();

        $this->logger->log($this->request, new Response(200, [], 'TestResponseBody'), 0.2);

        Log::assertLogged(function (LogEntry $log): bool {
            return $log->level === 'debug'
                && Str::contains($log->message, 'TestResponseBody');
        });
    }

    public function test_logs_context()
    {
        LogFake::bind();

        $this->logger->log($this->request, new Response(200), 0.2, ['context']);

        Log::assertLogged(function (LogEntry $log): bool {
            return $log->level === 'debug'
                && $log->context == ['context'];
        });
    }

    public function test_replaces_placeholders_from_request()
    {
        LogFake::bind();

        $this->logger->log($this->request, new Response(200), 0.2, ['test123'], ['replace' => ['example.com' => 'mock.org']]);

        Log::assertLogged(function (LogEntry $log): bool {
            return $log->level === 'debug'
                && Str::contains($log->message, 'mock.org')
                && !Str::contains($log->message, 'example.com')
                && $log->context == ['test123'];
        });
    }

    public function test_replaces_placeholders_from_response()
    {
        LogFake::bind();

        $this->logger->log($this->request, new Response(200, [], 'My name is John Doe'), 0.2, ['test123'], ['replace' => ['Doe' => 'Smith']]);

        Log::assertLogged(function (LogEntry $log): bool {
            return $log->level === 'debug'
                && Str::contains($log->message, 'Smith')
                && !Str::contains($log->message, 'Doe')
                && $log->context == ['test123'];
        });
    }
}
