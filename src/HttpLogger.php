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
    protected array $replace;
    protected string $filename;
    protected string $fileExt = '.txt';

    public function __construct(PsrMessageToStringConverter $psrMessageStringConverter)
    {
        $this->psrMessageStringConverter = $psrMessageStringConverter;
    }

    public function log(
        RequestInterface $request,
        ResponseInterface $response,
        float $sec,
        array $context = []
    ): void
    {
        $this->request = $request;
        $this->response = $response;
        $this->sec = $sec;
        $this->replace = $this->getReplace($context);
        $this->filename = $this->getFileName($context);
        $this->context = $this->getContextCleaned($context); // must be called after the two above since the $context array is modified

        if (config('http-client-logger.log_to_channel.enabled')) {
            $this->logToChannel(config('http-client-logger.log_to_channel.channel') ?? config('logging.default'));
        }

        if (config('http-client-logger.log_to_disk.enabled')) {
            $this->logToDisk(config('http-client-logger.log_to_disk.disk') ?? config('filesystems.default'));
        }
    }

    protected function getContextCleaned(array $context): array
    {
        return Arr::except($context, ['replace', 'filename']);
    }

    protected function getReplace(array $context): array
    {
        return Arr::get($context, 'replace', []);
    }

    protected function getFileName(array $context): string
    {
        return Arr::get($context, 'filename', now()->format('Y-m-d-Hisu'));
    }

    protected function getMessage(): string
    {
        return "Time {$this->sec}sec\r\n"
            ."Request\r\n"
            .$this->psrMessageStringConverter->toString($this->request, $this->replace)."\r\n"
            ."Response\r\n"
            .$this->psrMessageStringConverter->toString($this->response, $this->replace);
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
        if (config('http-client-logger.log_to_disk.separate')) {
            Storage::disk($disk)->put(
                $this->filename.'-request'.Str::start($this->fileExt, '.'),
                $this->psrMessageStringConverter->toString($this->request, $this->replace)
            );
            Storage::disk($disk)->put(
                $this->filename.'-response'.Str::start($this->fileExt, '.'),
                $this->psrMessageStringConverter->toString($this->response, $this->replace)
            );
        } else {
            Storage::disk($disk)->put(
                $this->filename.Str::start($this->fileExt, '.'),
                $this->getMessage()
            );
        }
    }
}
