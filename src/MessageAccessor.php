<?php

namespace Bilfeldt\LaravelHttpClientLogger;

use GuzzleHttp\Psr7\Utils;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Psr\Http\Message\MessageInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\UriInterface;

class MessageAccessor
{
    private array $values;
    private array $queryFilters;
    private array $headersFilters;
    private array $jsonFilters;
    private string $replace;

    public function __construct(
        array $jsonFilers = [],
        array $queryFilters = [],
        array $headersFilters = [],
        array $values = [],
        string $replace = '********'
    ) {
        $this->values = $values;
        $this->queryFilters = $queryFilters;
        $this->headersFilters = $headersFilters;
        $this->jsonFilters = $jsonFilers;
        $this->replace = $replace;
    }

    public function getUri(RequestInterface $request): UriInterface
    {
        $uri = $request->getUri();
        parse_str($uri->getQuery(), $query);

        return $uri
            ->withUserInfo($this->replace($this->values, $this->replace, $uri->getUserInfo()))
            ->withHost($this->replace($this->values, $this->replace, $uri->getHost()))
            ->withPath($this->replace($this->values, $this->replace, $uri->getPath()))
            ->withQuery(Arr::query($this->replaceParameters($query, $this->queryFilters, $this->values, $this->replace)));
    }

    public function getBase(RequestInterface $request): string
    {
        $uri = $this->getUri($request);

        $base = '';
        if ($uri->getScheme()) {
            $base .= $uri->getScheme().'://';
        }
        if ($uri->getUserInfo()) {
            $base .= $uri->getUserInfo().'@';
        }
        if ($uri->getHost()) {
            $base .= $uri->getHost();
        }
        if ($uri->getPort()) {
            $base .= ':'.$uri->getPort();
        }

        return $base;
    }

    public function getQuery(RequestInterface $request): array
    {
        parse_str($this->getUri($request)->getQuery(), $query);

        return $query;
    }

    public function getHeaders(MessageInterface $message): array
    {
        foreach ($this->headersFilters as $headersFilter) {
            if ($message->hasHeader($headersFilter)) {
                $message = $message->withHeader($headersFilter, $this->replace);
            }
        }

        // Header filter applied above as this is an array with two layers
        return $this->replaceParameters($message->getHeaders(), [], $this->values, $this->replace, false);
    }

    /**
     * Determine if the request is JSON.
     *
     * @see vendor/laravel/framework/src/Illuminate/Http/Client/Request.php
     *
     * @param MessageInterface $message
     *
     * @return bool
     */
    public function isJson(MessageInterface $message): bool
    {
        return $message->hasHeader('Content-Type') &&
            Str::contains($message->getHeaderLine('Content-Type'), 'json');
    }

    public function getJson(MessageInterface $message): ?array
    {
        return $this->replaceParameters(
            json_decode($message->getBody()->__toString(), true),
            $this->jsonFilters,
            $this->values,
            $this->replace
        );
    }

    public function getContent(MessageInterface $message): string
    {
        if ($this->isJson($message)) {
            $body = json_encode($this->getJson($message));
        } else {
            $body = $message->getBody()->__toString();
            foreach ($this->values as $value) {
                $body = str_replace($value, $this->replace, $body);
            }
        }

        return $body;
    }

    public function filterMessage(MessageInterface $message): MessageInterface
    {
        $body = $this->getContent($message);

        foreach ($this->getHeaders($message) as $header => $values) {
            $message = $message->withHeader($header, $values);
        }

        return $message
            ->withBody(Utils::streamFor($body));
    }

    public function filterRequest(RequestInterface $request): RequestInterface
    {
        /** @var RequestInterface $filtered */
        $filtered = $this->filterMessage($request);
        return $filtered->withUri($this->getUri($request));
    }

    protected function replaceParameters(array $array, array $parameters, array $values, string $replace, $strict = true): array
    {
        foreach ($parameters as $parameter) {
            if (data_get($array, $parameter, null)) {
                data_set($array, $parameter, $replace);
            }
        }

        array_walk_recursive($array, function (&$item) use ($values, $replace, $strict) {
            foreach ($values as $value) {
                if (!$strict && str_contains($item, $value)) {
                    $item = str_replace($value, $replace, $item);
                } elseif ($strict && $value === $item) {
                    $item = $replace;
                }
            }

            return $item;
        });

        return $array;
    }

    protected function replace($search, $replace, ?string $subject): ?string
    {
        if (is_null($subject)) {
            return null;
        }

        return str_replace($search, $replace, $subject);
    }
}
