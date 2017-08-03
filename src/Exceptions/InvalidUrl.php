<?php

namespace Spatie\MixedContentScanner\Exceptions;

use Exception;

class InvalidUrl extends Exception
{
    public static function invalidScheme(string $url)
    {
        return new static("`{$url}` did not start with a valid scheme. It should start with either `http://` or `https`");
    }

    public static function urlIsEmpty()
    {
        return new static('You must pass a valid url. Empty value given.');
    }
}
