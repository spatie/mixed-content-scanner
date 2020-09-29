<?php

namespace Spatie\MixedContentScanner\Test;

use PHPUnit\Framework\TestCase;
use Spatie\MixedContentScanner\Exceptions\InvalidUrl;
use Spatie\MixedContentScanner\MixedContentScanner;

class InvalidUrlTest extends TestCase
{
    /** @test */
    public function it_will_throw_an_exception_when_scanning_an_empty_url()
    {
        $this->expectException(InvalidUrl::class);

        $this->scan('');
    }

    /** @test */
    public function it_will_throw_an_exception_when_scanning_an_url_with_an_invalid_protocol()
    {
        $this->expectException(InvalidUrl::class);

        $this->scan('htxp://spatie.be');
    }

    protected function scan(string $url)
    {
        $logger = new MixedContentLogger();

        $scanner = new MixedContentScanner($logger);

        $scanner->scan($url);
    }
}
