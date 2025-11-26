<?php

use Spatie\MixedContentScanner\Exceptions\InvalidUrl;
use Spatie\MixedContentScanner\MixedContentScanner;
use Spatie\MixedContentScanner\Test\MixedContentLogger;

it('will throw an exception when scanning an empty url', function () {
    scan('');
})->throws(InvalidUrl::class);

it('will throw an exception when scanning an url with an invalid protocol', function () {
    scan('htxp://spatie.be');
})->throws(InvalidUrl::class);

function scan(string $url): void
{
    $logger = new MixedContentLogger;

    $scanner = new MixedContentScanner($logger);

    $scanner->scan($url);
}
