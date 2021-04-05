# Changelog

All notable changes to `laravel-http-client-logger` will be documented in this file.

## Upgrade guides

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

### 0.3.0

- Add on-demand configuration array (breaking change)

### 0.2.0

- Refactor configuration (breaking change)
- Add logging to a flysystem disk
- Bugfix: `$context` not being passed down when using request macros

### 0.1.0

- initial release
