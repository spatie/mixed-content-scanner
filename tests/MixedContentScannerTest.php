<?php

namespace Spatie\MixedContentScanner\Test;

use PHPUnit\Framework\TestCase;
use Spatie\MixedContentScanner\MixedContentScanner;

class MixedContentScannerTest extends TestCase
{
    public function setUp()
    {
        Server::boot();
    }

    /** @test */
    public function true_is_true()
    {
        $logger = new MixedContentLogger();

        $scanner = new MixedContentScanner($logger);

        $scanner->scan('http://' . Server::getServerUrl());

        $logger->assertPageHasMixedContent('/mixedContent');

        $logger->assertPageHasNoMixedContent('/noMixedContent');
    }


}
