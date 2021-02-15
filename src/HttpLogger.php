<?php

namespace Bilfeldt\LaravelHttpClientLogger;

use GuzzleHttp\Psr7\Message;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Psr\Http\Message\MessageInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class HttpLogger implements HttpLoggerInterface
{
    public function log(
        RequestInterface $request,
        ResponseInterface $response,
        float $sec,
        array $context = []
    ): void {
        $message = "Time {$sec}sec\r\n"
            ."Request\r\n"
            .$this->messageToString($request, Arr::get($context, 'replace', []))."\r\n"
            ."Response\r\n"
            .$this->messageToString($response, Arr::get($context, 'replace', []));

        $context = Arr::except($context, 'replace');

        $channel = config('http-client-logger.log_channel') ?? config('logging.default');

        if ($response->getStatusCode() >= 200 && $response->getStatusCode() < 300) {
            Log::channel($channel)->debug($message, $context);
        } elseif ($response->getStatusCode() >= 300 && $response->getStatusCode() < 400) {
            Log::channel($channel)->info($message, $context);
        } elseif ($response->getStatusCode() >= 400 && $response->getStatusCode() < 500) {
            Log::channel($channel)->error($message, $context);
        } else {
            Log::channel($channel)->critical($message, $context);
        }
    }
    protected function messageToString(MessageInterface $message, array $placeholders): string
    {
        return strtr(Message::toString($message), $placeholders);
    }
}
