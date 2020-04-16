<?php

namespace Spatie\MixedContentScanner;

use GuzzleHttp\Client;
use Psr\Http\Message\UriInterface;

class Body
{
    public static function get(UriInterface $uri): string
    {
        return (new Client())->request('GET', $uri)->getBody();
    }
}
