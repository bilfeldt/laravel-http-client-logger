# :open_file_folder: A logger for the Laravel HTTP Client

![bilfeldt/laravel-http-client-logger](cover.jpg)

[![Latest Version on Packagist](https://img.shields.io/packagist/v/bilfeldt/laravel-http-client-logger)](https://packagist.org/packages/bilfeldt/laravel-http-client-logger)
[![GitHub Tests Action Status](https://img.shields.io/github/workflow/status/bilfeldt/laravel-http-client-logger/run-tests?label=tests)](https://github.com/bilfeldt/laravel-http-client-logger/actions?query=workflow%3ATests+branch%3Amaster)
[![GitHub Code Style Action Status](https://img.shields.io/github/workflow/status/bilfeldt/laravel-http-client-logger/Check%20&%20fix%20styling?label=code%20style)](https://github.com/bilfeldt/laravel-http-client-logger/actions?query=workflow%3A"Check+%26+fix+styling"+branch%3Amaster)
[![Total Downloads](https://img.shields.io/packagist/dt/bilfeldt/laravel-http-client-logger)](https://packagist.org/packages/bilfeldt/laravel-http-client-logger)


An easy yet very flexible logger for the Laravel HTTP Client.

## Installation

You can install the package via composer:

```bash
composer require bilfeldt/laravel-http-client-logger
```

Optionally publish the config file with:
```bash
php artisan vendor:publish --provider="Bilfeldt\LaravelHttpClientLogger\LaravelHttpClientLoggerServiceProvider" --tag="laravel-http-client-logger-config"
```

## Usage
Using the logger will log both the request, the response and the response time of an external HTTP request made with the [Laravel HTTP Client](https://laravel.com/docs/http-client).

### Basic logging
```php
Http::log()->get('https://example.com'); // uses the configured logger and filter
```

### Conditional logging
This will log the request/response when the `$condition` evaluates to `true`.
```php
Http::logWhen($condition)->get('https://example.com'); // uses the configured logger and filter
```

### Logging context
It is possible to supply context for the logger using:
```php
Http::log(['note' => 'Something to log'])->get('https://example.com');
// or
Http::logWhen($condition, ['note' => 'Something to log'])->get('https://example.com');
```

### Specifying a logger
The default logger and filter are specified in the package configuration `logger` and `filter` respectively but can be changed at runtime using:
```php
Http::log($context, $logger, $filter)->get('https://example.com');
// or
Http::logWhen($condition, $context, $logger, $filter)->get('https://example.com');
```
Note that the logger must implement `HttpLoggerInterface` while the filter must implement `HttpLoggingFilterInterface`.

### Logging example
The default logger converts the request and response to a [PSR-7 HTTP message](https://www.php-fig.org/psr/psr-7/) which is then logged as strings.

Log entry example when using default logger:

```php
Http::log()->get('https://repo.packagist.org/p2/bilfeldt/laravel-http-client-logger.json');

//[2021-03-08 06:58:49] local.DEBUG: Time 0.12105202674866sec
//Request
//GET /p2/bilfeldt/laravel-http-client-logger.json HTTP/1.1
//User-Agent: GuzzleHttp/7
//Host: repo.packagist.org
//
//
//Response
//HTTP/1.1 200 OK
//Server: nginx
//Date: Mon, 08 Mar 2021 06:58:49 GMT
//Content-Type: application/json
//Last-Modified: Wed, 17 Feb 2021 14:31:03 GMT
//Transfer-Encoding: chunked
//Connection: keep-alive
//Vary: Accept-Encoding
//
//{"packages":{"bilfeldt/laravel-http-client-logger":[{"name":"bilfeldt/laravel-http-client-logger","description":"A logger for the Laravel HTTP Client","keywords":["bilfeldt","laravel-http-client-logger"],"homepage":"https://github.com/bilfeldt/laravel-http-client-logger","version":"v0.2.0","version_normalized":"0.2.0.0","license":["MIT"],"authors":[{"name":"Anders Bilfeldt","email":"abilfeldt@gmail.com","role":"Developer"}],"source":{"type":"git","url":"https://github.com/bilfeldt/laravel-http-client-logger.git","reference":"67ea252a3d3d0c9c0e1c7daa11a3683db818ad5e"},"dist":{"type":"zip","url":"https://api.github.com/repos/bilfeldt/laravel-http-client-logger/zipball/67ea252a3d3d0c9c0e1c7daa11a3683db818ad5e","reference":"67ea252a3d3d0c9c0e1c7daa11a3683db818ad5e","shasum":""},"type":"library","time":"2021-02-17T14:28:45+00:00","autoload":{"psr-4":{"Bilfeldt\\LaravelHttpClientLogger\\":"src"}},"extra":{"laravel":{"providers":["Bilfeldt\\LaravelHttpClientLogger\\LaravelHttpClientLoggerServiceProvider"]}},"require":{"php":"^7.4|^8.0","guzzlehttp/guzzle":"^7.2","illuminate/http":"^8.0","illuminate/support":"^8.0","spatie/laravel-package-tools":"^1.1"},"require-dev":{"orchestra/testbench":"^6.0","phpunit/phpunit":"^9.3","spatie/laravel-ray":"^1.12","timacdonald/log-fake":"^1.9","vimeo/psalm":"^4.4"},"support":{"issues":"https://github.com/bilfeldt/laravel-http-client-logger/issues","source":"https://github.com/bilfeldt/laravel-http-client-logger/tree/v0.2.0"}},{"version":"0.1.0","version_normalized":"0.1.0.0","source":{"type":"git","url":"https://github.com/bilfeldt/laravel-http-client-logger.git","reference":"6bb8c8ada3959643103a75aa4e639c8dddddf2df"},"dist":{"type":"zip","url":"https://api.github.com/repos/bilfeldt/laravel-http-client-logger/zipball/6bb8c8ada3959643103a75aa4e639c8dddddf2df","reference":"6bb8c8ada3959643103a75aa4e639c8dddddf2df","shasum":""},"time":"2021-02-15T22:39:05+00:00","support":{"issues":"https://github.com/bilfeldt/laravel-http-client-logger/issues","source":"https://github.com/bilfeldt/laravel-http-client-logger/tree/0.1.0"}}]},"minified":"composer/2.0"}  

```

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Credits

- [Anders Bilfeldt](https://github.com/bilfeldt): Main package developer
- [Henry Be](https://unsplash.com/photos/lc7xcWebECc): Cover image

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
