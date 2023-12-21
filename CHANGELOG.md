# Changelog

All notable changes to `laravel-http-client-logger` will be documented in this file.

## Upgrade guides

### 1.* => 2.

No breaking changes. The only changes are to the development dependencies used for testing and then the minimum Laravel and PHP requirements.

### 0.3.0 => 1.0.0

This release flattens the configuration variables. It is suggested to republish the configuration after upgrading.

- `filtering.always` is renamed to `filter_all`
- `filtering.2xx` is renamed to `filter_2xx`
- `filtering.3xx` is renamed to `filter_3xx`
- `filtering.4xx` is renamed to `filter_4xx`
- `filtering.5xx` is renamed to `filter_5xx`
- `filtering.slow` is renamed to `filter_slow`
- `log_to_channel.enabled` has been removed, instead logging to channel is enabled when a channel is provided
- `log_to_channel.channel` is renamed to `channel`
- `log_to_disk.enabled` has been removed, instead logging to disk is enabled when a disk is provided
- `log_to_disk.disk` is renamed to `disk`
- `log_to_disk.separate` is renamed to `disk_separate_files`
- `log_to_disk.timestamp` is renamed to `prefix_timestamp`
- `log_to_disk.filename` is renamed to `filename`

The following environment variables have been renamed:
- `HTTP_CLIENT_LOGGER_FILTERING_ALWAYS` is renamed to `HTTP_CLIENT_LOGGER_FILTER_ALL`
- `HTTP_CLIENT_LOGGER_FILTERING_2XX` is renamed to `HTTP_CLIENT_LOGGER_FILTER_2XX`
- `HTTP_CLIENT_LOGGER_FILTERING_3XX` is renamed to `HTTP_CLIENT_LOGGER_FILTER_3XX`
- `HTTP_CLIENT_LOGGER_FILTERING_4XX` is renamed to `HTTP_CLIENT_LOGGER_FILTER_4XX`
- `HTTP_CLIENT_LOGGER_FILTERING_5XX` is renamed to `HTTP_CLIENT_LOGGER_FILTER_5XX`
- `HTTP_CLIENT_LOGGER_FILTERING_SLOW` is renamed to `HTTP_CLIENT_LOGGER_FILTER_SLOW`
- `HTTP_CLIENT_LOGGER_CHANNEL_LOG_ENABLED` removed in favor of `HTTP_CLIENT_LOGGER_CHANNEL`
- `HTTP_CLIENT_LOGGER_DISK_LOG_ENABLED` removed in favor of `HTTP_CLIENT_LOGGER_DISK`

### 0.2.0 => 0.3.0

This release includes breaking changes:

- `HttpLoggerInterface`: Signature changed with the addition of optional array parameter `$config`
- `HttpLoggingFilterInterface`: Signature changed with the addition of optional array parameter `$config`
- Macro `log()`: Signature changed with the addition of optional array parameter `$config`
- Macro `logWhen()`: Signature changed with the addition of optional array parameter `$config`

The following changes are required when updating:

- If the parameter `$context['replace']` is provided to any of the methods above this must instead be provided in the newly added `$config['replace']`
- The same change must be made if the parameter `$context['filename']` has been provided
- Any calls to the `log` or `logWhen` macro where logger or filter is provided must add an empty array before the logger parameter due to the new method signature
- Any custom implementation of `HttpLoggerInterface` and `HttpLoggingFilterInterface` must be refactored to fit the new method signature
- Optional: Republish configuration file

## Changes

### 2.1.0

- Add PHP 8.2 support

### 2.0.0

- Minimum PHP requirement 8.1
- Add support for PHP 8.2
- Minimum Laravel requirement 9.0
- Add support for Laravel 10.*

### 1.3.0

- Added return types for better IDE completion by @shahruslan in #24

### 1.2.1

- Append instead of override when writign to a log file by @afiqiqmal in #21
- Add Prefix Config by @afiqiqmal in #20

### 1.2.0

- Add Laravel 9 support

### 1.1.0

- Implement new MessageAccessor class by @bilfeldt in #13
- Implement a new logWith-method + a LogAllFilter class and a NullLogger by @bilfeldt in #15
- Apply fixes from StyleCI by @bilfeldt in #11
- Apply fixes from StyleCI by @bilfeldt in #14

### 1.0.1

- Add unofficial support for Lumen

### 1.0.0

- Release first stable release
- Flatten configuration (breaking change)

### 0.3.0

- Add on-demand configuration array (breaking change)
- Fix evaluation of closures in `logWhen` macro
- Add environmental variables for filtering options
- Add new filtering option for enabling all logging

### 0.2.0

- Refactor configuration (breaking change)
- Add logging to a flysystem disk
- Bugfix: `$context` not being passed down when using request macros

### 0.1.0

- initial release
