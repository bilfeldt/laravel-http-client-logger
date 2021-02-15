# A logger for the Laravel HTTP Client

[![Latest Version on Packagist](https://img.shields.io/packagist/v/bilfeldt/laravel-http-client-logger.svg?style=flat-square)](https://packagist.org/packages/bilfeldt/laravel-http-client-logger)
[![GitHub Tests Action Status](https://img.shields.io/github/workflow/status/bilfeldt/laravel-http-client-logger/run-tests?label=tests)](https://github.com/bilfeldt/laravel-http-client-logger/actions?query=workflow%3ATests+branch%3Amaster)
[![GitHub Code Style Action Status](https://img.shields.io/github/workflow/status/bilfeldt/laravel-http-client-logger/Check%20&%20fix%20styling?label=code%20style)](https://github.com/bilfeldt/laravel-http-client-logger/actions?query=workflow%3A"Check+%26+fix+styling"+branch%3Amaster)
[![Total Downloads](https://img.shields.io/packagist/dt/bilfeldt/laravel-http-client-logger.svg?style=flat-square)](https://packagist.org/packages/bilfeldt/laravel-http-client-logger)


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
Using the logger will log both the request, the response and the response time of request made with the [Larvel HTTP Client](https://laravel.com/docs/http-client).

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

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Credits

- [Anders Bilfeldt](https://github.com/bilfeldt)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
