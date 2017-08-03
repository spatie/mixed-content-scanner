<?php

namespace Spatie\MixedContentScanner\Test;

use PHPUnit\Framework\TestCase;
use Spatie\MixedContentScanner\Exceptions\InvalidUrl;
use Spatie\MixedContentScanner\MixedContentScanner;

class MixedContentScannerTest extends TestCase
{
    public function setUp()
    {
        Server::boot();
    }

    /** @test */
    public function if_can_find_mixed_content()
    {
        $logger = new MixedContentLogger();

        $scanner = new MixedContentScanner($logger);

        $scanner->scan('http://'.Server::getServerUrl());

        $logger->assertPageHasMixedContent('/mixedContent');

        $logger->assertPageHasNoMixedContent('/noMixedContent');
    }

    /** @test */
    public function it_will_throw_an_exception_when_given_an_url_with_an_invalid_protocol()
    {
        $logger = new MixedContentLogger();

        $scanner = new MixedContentScanner($logger);

        $this->expectException(InvalidUrl::class);

        $scanner->scan('blabla');
    }
}
