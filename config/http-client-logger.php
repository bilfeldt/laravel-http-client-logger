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
    'filter_always' => env('HTTP_CLIENT_LOGGER_FILTER_ALL', false),

    'filter_2xx' => env('HTTP_CLIENT_LOGGER_FILTER_2XX', true),

    'filter_3xx' => env('HTTP_CLIENT_LOGGER_FILTER_3XX', true),

    'filter_4xx' => env('HTTP_CLIENT_LOGGER_FILTER_4XX', true),

    'filter_5xx' => env('HTTP_CLIENT_LOGGER_FILTER_5XX', true),

    'filter_slow' => env('HTTP_CLIENT_LOGGER_FILTER_SLOW', 1.5), // Log requests that took longer than the setting (in sec)

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
    | Log to channel
    |--------------------------------------------------------------------------
    |
    | These settings determine how to log request/responses to the Laravel log.
    | Note that these settings are only used by the default logger.
    | Set to false to disable the channel logging.
    |
    */
    'channel' => env('HTTP_CLIENT_LOGGER_CHANNEL', 'default'),

    /*
    |--------------------------------------------------------------------------
    | Log to disk
    |--------------------------------------------------------------------------
    |
    | These settings determine how to log request/responses to a flysystem disk.
    | Note that these settings are only used by the default logger.
    |
    */
    'disk' => env('HTTP_CLIENT_LOGGER_DISK', false),
    'disk_separate_files' => true,
    'prefix_timestamp' => 'Y-m-d-Hisu', // Leaving empty will remove the timestamp
    'filename' => '',
];
