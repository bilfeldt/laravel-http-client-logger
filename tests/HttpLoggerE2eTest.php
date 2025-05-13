<?php

namespace Bilfeldt\LaravelHttpClientLogger\Tests;

use Bilfeldt\LaravelHttpClientLogger\HttpLogger;
use Bilfeldt\LaravelHttpClientLogger\MessageAccessor;
use Bilfeldt\LaravelHttpClientLogger\PsrMessageToStringConverter;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Psr\Http\Message\MessageInterface;
use TiMacDonald\Log\LogEntry;
use TiMacDonald\Log\LogFake;

class HttpLoggerE2eTest extends TestCase
{
    protected HttpLogger $logger;

    public function setUp(): void
    {
        parent::setUp();

        Http::preventStrayRequests();
        Http::fake([
            'https://api.example.com/login?username=SECRET_USER&pass=SECRET_PASSWORD' => Http::response(['authentication_token' => 'SECRET_TOKEN'], 200),
            'https://api.example.com/documents' => function ($request) {
                $authorizationHeader = $request->header('Authorization');

                if (($authorizationHeader[0] ?? '') === 'Bearer SECRET_TOKEN') {
                    return Http::response([
                        ['id' => '1', 'title' => 'Document Title 1', 'author' => 'Author Name 1'],
                        ['id' => '2', 'title' => 'Document Title 2', 'author' => 'Author Name 2']
                    ], 200);
                } else {
                    return Http::response(['error' => 'Unauthorized'], 401);
                }
            },
            '*' => Http::response(['error' => 'Not Found'], 404),
        ]);
    }

    public function test_accessor_adhoc_config()
    {
        LogFake::bind();

        $pendingRequest = Http::log(
            [],
            [ 'replace_json' => [ 'authentication_token'],
              'replace_headers' => ['Authorization'],
              'replace_query' => ['username', 'pass']
            ]
        );

        $responses = [
            $pendingRequest->get('https://api.example.com/login?username=SECRET_USER&pass=SECRET_PASSWORD'),
            $pendingRequest->withToken("SECRET_TOKEN")->get('https://api.example.com/documents')
        ];

        Log::assertLogged(fn (LogEntry $log) => !Str::contains($log->message, 'SECRET_'));

    }

    public function test_accessor_custom_class()
    {
        LogFake::bind();

        Http::log([], [
            'message_accessor_class' => MockMessageAccessor::class,
        ])->get('https://api.example.com/login?username=SECRET_USER&pass=SECRET_PASSWORD');

        Log::assertLogged(fn (LogEntry $log) => Str::contains($log->message, 'TOP SECRET'));
    }
}

class MockMessageAccessor extends MessageAccessor
{
    public function getContent(MessageInterface $message) : string
    {
        return "TOP SECRET";
    }
}