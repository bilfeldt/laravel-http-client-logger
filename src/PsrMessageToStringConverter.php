<?php

namespace Bilfeldt\LaravelHttpClientLogger;

use GuzzleHttp\Psr7\Message;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\MessageInterface;

class PsrMessageToStringConverter
{
    public function toString(MessageInterface $message, array $replace): string
    {
        return strtr(Message::toString($message), $replace);
    }

    public function toRequest(string $message): Request
    {
        return Message::parseRequest($message);
    }

    public function toResponse(string $message): Response
    {
        return Message::parseResponse($message);
    }
}
