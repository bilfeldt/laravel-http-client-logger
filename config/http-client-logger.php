<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Filter class
    |--------------------------------------------------------------------------
    |
    | This is the filter used (unless specified at runtime) to determine if a
    | request should be logged or not. Any custom filter can be inserted here
    | but remember that it must implement the HttpLoggingFilterInterface.
    |
    */
    'filter' => \Bilfeldt\LaravelHttpClientLogger\HttpLoggingFilter::class,

    /*
    |--------------------------------------------------------------------------
    | Enable logging
    |--------------------------------------------------------------------------
    |
    | Whether or not logging should be enabled/disabled when using the filter
    | specified above. Specifying another filter at runtime will override this
    | setting.
    |
    */
    'enabled' => env('HTTP_CLIENT_LOGGER_ENABLED', true),

    /*
    |--------------------------------------------------------------------------
    | Filtering options
    |--------------------------------------------------------------------------
    |
    | These settings determine what request/responses should be logged. Note
    | that these settings are only used by the default filter.
    |
    */
    'filtering' => [
        '2xx' => true,

        '3xx' => true,

        '4xx' => true,

        '5xx' => true,

        'slow' => 1.5, // Log requests that took longer than the setting (in sec)
    ],

    /*
    |--------------------------------------------------------------------------
    | Logger class
    |--------------------------------------------------------------------------
    |
    | This is the logger used (unless specified at runtime) to actually log
    | the request and response. Any custom logger can be inserted here
    | but remember that it must implement the HttpLoggerInterface.
    |
    */
    'logger' => \Bilfeldt\LaravelHttpClientLogger\HttpLogger::class,

    /*
    |--------------------------------------------------------------------------
    | Logger to channel
    |--------------------------------------------------------------------------
    |
    | These settings determine how to log request/responses to the Laravel log.
    | Note that these settings are only used by the default logger.
    |
    */
    'log_to_channel' => [
        'enabled' => env('HTTP_CLIENT_LOGGER_CHANNEL_LOG_ENABLED', true),
        'channel' => env('HTTP_CLIENT_LOGGER_CHANNEL'), // Uses the default log channel unless specified
    ],

    /*
    |--------------------------------------------------------------------------
    | Logger to disk
    |--------------------------------------------------------------------------
    |
    | These settings determine how to log request/responses to a flysystem disk.
    | Note that these settings are only used by the default logger.
    |
    */
    'log_to_disk' => [
        'enabled' => env('HTTP_CLIENT_LOGGER_DISK_LOG_ENABLED', true),
        'disk' => env('HTTP_CLIENT_LOGGER_DISK'), // uses the default filesystem disk unless specified
        'separate' => true,
    ],
];
