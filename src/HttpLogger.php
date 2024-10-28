<?php

namespace Bilfeldt\LaravelHttpClientLogger;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class HttpLogger implements HttpLoggerInterface
{
    protected PsrMessageToStringConverter $psrMessageStringConverter;

    protected RequestInterface $request;
    protected ResponseInterface $response;
    protected float $sec;
    protected array $context;
    protected array $config;
    protected string $fileExt = '.txt';

    public function __construct(PsrMessageToStringConverter $psrMessageStringConverter)
    {
        $this->psrMessageStringConverter = $psrMessageStringConverter;
    }

    public function log(
        RequestInterface $request,
        ResponseInterface $response,
        float $sec,
        array $context = [],
        array $config = []
    ): void {
        $this->request = $request;
        $this->response = $response;
        $this->sec = $sec;
        $this->context = $context;

        // ad-hoc config is not supported for message accessor settings: replace_json, replace_query, replace_headers, replace_values.
        if (Arr::hasAny($config, ['replace_json', 'replace_query', 'replace_headers', 'replace_values'])) {
            throw new \InvalidArgumentException('Ad-hoc config does not support replace_json, replace_query, replace_headers, replace_values.');
        }

        $this->config = array_merge(config('http-client-logger'), $config);

        if (Arr::get($this->config, 'channel')) {
            $this->logToChannel(($channel = Arr::get($this->config, 'channel')) == 'default' ? config('logging.default') : $channel);
        }

        if (Arr::get($this->config, 'disk')) {
            $this->logToDisk(($disk = Arr::get($this->config, 'disk')) == 'default' ? config('filesystems.default') : $disk);
        }
    }

    protected function getReplace(): array
    {
        return Arr::get($this->config, 'replace', []);
    }

    protected function getFileName(): string
    {
        return Arr::get($this->config, 'prefix')
            .(Arr::get($this->config, 'prefix_timestamp') ? Date::now()->format(Arr::get($this->config, 'prefix_timestamp')) : '')
            .Arr::get($this->config, 'filename');
    }

    protected function getMessage(): string
    {
        return "Time {$this->sec}sec\r\n"
            ."Request\r\n"
            .$this->psrMessageStringConverter->toString($this->request, $this->getReplace())."\r\n"
            ."Response\r\n"
            .$this->psrMessageStringConverter->toString($this->response, $this->getReplace());
    }

    protected function logToChannel(string $channel): void
    {
        if ($this->response->getStatusCode() >= 200 && $this->response->getStatusCode() < 300) {
            Log::channel($channel)->debug($this->getMessage(), $this->context);
        } elseif ($this->response->getStatusCode() >= 300 && $this->response->getStatusCode() < 400) {
            Log::channel($channel)->info($this->getMessage(), $this->context);
        } elseif ($this->response->getStatusCode() >= 400 && $this->response->getStatusCode() < 500) {
            Log::channel($channel)->error($this->getMessage(), $this->context);
        } else {
            Log::channel($channel)->critical($this->getMessage(), $this->context);
        }
    }

    protected function logToDisk(string $disk): void
    {
        if (Arr::get($this->config, 'disk_separate_files')) {
            Storage::disk($disk)->append(
                $this->getFileName().'-request'.Str::start($this->fileExt, '.'),
                $this->psrMessageStringConverter->toString($this->request, $this->getReplace())
            );
            Storage::disk($disk)->append(
                $this->getFileName().'-response'.Str::start($this->fileExt, '.'),
                $this->psrMessageStringConverter->toString($this->response, $this->getReplace())
            );
        } else {
            Storage::disk($disk)->append(
                $this->getFileName().Str::start($this->fileExt, '.'),
                $this->getMessage()
            );
        }
    }
}
