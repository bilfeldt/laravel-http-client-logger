<?php

namespace Bilfeldt\LaravelHttpClientLogger;

use GuzzleHttp\Psr7\Message;
use Psr\Http\Message\MessageInterface;

class PsrMessageToStringConverter
{
    public function toString(MessageInterface $message, array $placeholders): string
    {
        return strtr(Message::toString($message), $placeholders);
    }
}
