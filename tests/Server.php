<?php

namespace Spatie\MixedContentScanner\Test;

use GuzzleHttp\Client;

class Server
{
    /** @var \GuzzleHttp\Client */
    protected $client;

    public function __construct(Client $client = null)
    {
        static::boot();

        $this->client = $client ?? new Client();
    }

    public static function boot()
    {
        if (! file_exists(__DIR__.'/server/vendor')) {
            exec('cd "'.__DIR__.'/server"; composer install');
        }

        if (static::serverHasBooted()) {
            return;
        }

        $startServerCommand = 'php -S '.static::getServerUrl().' -t ./tests/server/public > /dev/null 2>&1 & echo $!';

        $pid = exec($startServerCommand);

        while (! static::serverHasBooted()) {

            usleep(1000);
        }

        register_shutdown_function(function () use ($pid) {
            exec("kill {$pid}");
        });
    }

    public static function getServerUrl(string $endPoint = ''): string
    {
        return 'localhost:'.getenv('TEST_SERVER_PORT').'/'.$endPoint;
    }

    public static function serverHasBooted(): bool
    {
        return @file_get_contents('http://'.self::getServerUrl('booted')) != false;
    }
}
