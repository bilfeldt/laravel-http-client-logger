<?php

namespace Bilfeldt\LaravelHttpClientLogger\Commands;

use Illuminate\Console\Command;

class LaravelHttpClientLoggerCommand extends Command
{
    public $signature = 'laravel-http-client-logger';

    public $description = 'My command';

    public function handle()
    {
        $this->comment('All done');
    }
}
