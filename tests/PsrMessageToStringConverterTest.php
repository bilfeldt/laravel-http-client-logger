<?php

namespace Bilfeldt\LaravelHttpClientLogger\Tests;

use Bilfeldt\LaravelHttpClientLogger\MessageAccessor;
use Bilfeldt\LaravelHttpClientLogger\PsrMessageToStringConverter;
use GuzzleHttp\Psr7\Request;
use Psr\Http\Message\RequestInterface;

class PsrMessageToStringConverterTest extends TestCase
{
    public PsrMessageToStringConverter $converter;

    public RequestInterface $request;

    public function setUp(): void
    {
        parent::setUp();

        $messageAccessor = new MessageAccessor(
            ['data.baz.*.password'],
            ['search', 'filter.field2'],
            ['Authorization'],
            ['secret'],
        );

        $this->converter = new PsrMessageToStringConverter($messageAccessor);

        $this->request = new Request(
            'POST',
            'https://user:secret@secret.example.com:9000/some-path/secret/should-not-be-removed?test=true&search=foo&filter[field1]=A&filter[field2]=B#anchor',
            [
                'Accept'        => 'application/json',
                'Content-Type'  => 'application/json',
                'Authorization' => 'Bearer 1234567890',
            ],
            json_encode([
                'data' => [
                    'foo' => 'bar',
                    'baz' => [
                        [
                            'field_1'          => 'value1',
                            'field_2'          => 'value2',
                            'password'         => '123456',
                            'secret'           => 'this is not for everyone',
                            'legacy_replace'   => 'replace array is also still used',
                        ],
                    ],
                ],
            ])
        );
    }

    public function test_to_string_replaces_sensitive_data()
    {
        $string = $this->converter->toString($this->request, ['legacy_replace' => '********']);

        $this->assertStringContainsString(
            'POST /some-path/********/should-not-be-removed?test=true&search=%2A%2A%2A%2A%2A%2A%2A%2A',
            $string,
            'sensitive data not replaced in URI'
        );

        $this->assertStringNotContainsString(
            '123456',
            $string,
            'sensitive data not replaced in json'
        );

        $this->assertStringContainsString(
            'Host: ********.example.com:9000',
            $string,
            'sensitive data not replaced in Host header'
        );

        $this->assertStringContainsString(
            'Authorization: ********',
            $string,
            'sensitive header not masked'
        );

        $this->assertStringNotContainsString(
            'legacy_replace',
            $string,
            'replace array not used'
        );
    }
}
