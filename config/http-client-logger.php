<?php

return [
    'logger' => \Bilfeldt\LaravelHttpClientLogger\HttpLogger::class,

    'log_channel' => null,

    'filter' => \Bilfeldt\LaravelHttpClientLogger\HttpLoggingFilter::class,

    'filter_2xx' => true,

    'filter_3xx' => true,

    'filter_4xx' => true,

    'filter_5xx' => true,

    'filter_slow' => 1.5,
];
