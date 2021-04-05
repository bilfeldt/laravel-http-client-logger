<?php

namespace Bilfeldt\LaravelHttpClientLogger;

use Illuminate\Support\Arr;
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
        $this->config = array_replace_recursive(config('http-client-logger'), $config); // Note this does not work optimally!

        if (Arr::get($this->config, 'log_to_channel.enabled')) {
            $this->logToChannel(Arr::get($this->config, 'log_to_channel.channel') ?? config('logging.default'));
        }

        if (Arr::get($this->config, 'log_to_disk.enabled')) {
            $this->logToDisk(Arr::get($this->config, 'log_to_disk.disk') ?? config('filesystems.default'));
        }
    }

    protected function getReplace(): array
    {
        return Arr::get($this->config, 'replace', []);
    }

    protected function getFileName(): string
    {
        return (Arr::get($this->config, 'log_to_disk.timestamp') ? now()->format(Arr::get($this->config, 'log_to_disk.timestamp')) : '')
            .Arr::get($this->config, 'log_to_disk.filename');
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
        if (Arr::get($this->config, 'log_to_disk.separate')) {
            Storage::disk($disk)->put(
                $this->getFileName().'-request'.Str::start($this->fileExt, '.'),
                $this->psrMessageStringConverter->toString($this->request, $this->getReplace())
            );
            Storage::disk($disk)->put(
                $this->getFileName().'-response'.Str::start($this->fileExt, '.'),
                $this->psrMessageStringConverter->toString($this->response, $this->getReplace())
            );
        } else {
            Storage::disk($disk)->put(
                $this->getFileName().Str::start($this->fileExt, '.'),
                $this->getMessage()
            );
        }
    }
}
