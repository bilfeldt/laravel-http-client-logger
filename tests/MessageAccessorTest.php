<?php

namespace Bilfeldt\LaravelHttpClientLogger\Tests;

use Bilfeldt\LaravelHttpClientLogger\MessageAccessor;
use GuzzleHttp\Psr7\Message;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class MessageAccessorTest extends TestCase
{
    public MessageAccessor $messageAccessor;
    public RequestInterface $request;
    public ResponseInterface $response;

    public function setUp(): void
    {
        parent::setUp();

        $this->messageAccessor = new MessageAccessor(
            ['secret'],
            ['search', 'filter.field2'],
            ['Authorization'],
            ['data.baz.*.password']
        );

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
                            'field_1'  => 'value1',
                            'field_2'  => 'value2',
                            'password' => '123456',
                            'secret'   => 'this is not for everyone',
                        ],
                    ],
                ],
            ])
        );
    }

    public function test_get_uri()
    {
        $uri = $this->messageAccessor->getUri($this->request);

        $this->assertEquals('https', $uri->getScheme());
        $this->assertEquals('user%3A********@********.example.com:9000', $uri->getAuthority());
        $this->assertEquals('user%3A********', $uri->getUserInfo());
        $this->assertEquals('********.example.com', $uri->getHost());
        $this->assertEquals('9000', $uri->getPort());
        $this->assertEquals('/some-path/********/should-not-be-removed', $uri->getPath());
        $this->assertEquals('test=true&search=********&filter[field1]=A&filter[field2]=********', urldecode($uri->getQuery()));
        $this->assertEquals('anchor', $uri->getFragment());
    }

    public function test_get_base()
    {
        $this->assertEquals(
            'https://user:********@********.example.com:9000',
            urldecode($this->messageAccessor->getBase($this->request))
        );
    }

    public function test_get_query()
    {
        $query = $this->messageAccessor->getQuery($this->request);

        $this->assertIsArray($query);
        $this->assertEquals([
            'test'   => 'true',
            'search' => '********',
            'filter' => [
                'field1' => 'A',
                'field2' => '********',
            ],
        ], $query);
    }

    public function test_get_headers()
    {
        $headers = $this->messageAccessor->getHeaders($this->request);

        $this->assertIsArray($headers);
        $this->assertEquals([
            'Accept'        => ['application/json'],
            'Content-Type'  => ['application/json'],
            'Authorization' => ['********'],
            'Host'          => ['********.example.com:9000'],
        ], $headers);
    }

    public function test_is_json()
    {
        $this->assertTrue($this->messageAccessor->isJson($this->request));
        $this->assertFalse($this->messageAccessor->isJson(new Response(200, ['Content-Type' => 'text/html'], '<html></html>')));
    }

    public function test_get_json()
    {
        $json = $this->messageAccessor->getJson($this->request);

        $this->assertIsArray($json);
        $this->assertEquals([
            'data' => [
                'foo' => 'bar',
                'baz' => [
                    [
                        'field_1'  => 'value1',
                        'field_2'  => 'value2',
                        'password' => '********',
                        'secret'   => 'this is not for everyone', // Note that keys are NOT filtered
                    ],
                ],
            ],
        ], $json);
    }

    public function test_get_content()
    {
        $content = $this->messageAccessor->getContent($this->request);

        $this->assertEquals(json_encode([
            'data' => [
                'foo' => 'bar',
                'baz' => [
                    [
                        'field_1'  => 'value1',
                        'field_2'  => 'value2',
                        'password' => '********',
                        'secret'   => 'this is not for everyone', // Note that keys are NOT filtered
                    ],
                ],
            ],
        ]), $content);
    }

    public function test_filter()
    {
        $request = $this->messageAccessor->filter($this->request);

        // Note that this is require to use double quotes for the Carriage Return (\r) to work
        $output = "POST /some-path/secret/should-not-be-removed?test=true&search=foo&filter%5Bfield1%5D=A&filter%5Bfield2%5D=B HTTP/1.1\r
Host: ********.example.com:9000\r
Accept: application/json\r
Content-Type: application/json\r
Authorization: ********\r
\r
{\"data\":{\"foo\":\"bar\",\"baz\":[{\"field_1\":\"value1\",\"field_2\":\"value2\",\"password\":\"********\",\"secret\":\"this is not for everyone\"}]}}";

        $this->assertEquals($output, Message::toString($request));
    }
}
