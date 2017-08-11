<?php

namespace Spatie\MixedContentScanner\Test;

use PHPUnit\Framework\TestCase;
use Spatie\Crawler\Url;
use Spatie\MixedContentScanner\MixedContent;

class MixedContentTest extends TestCase
{
    /** @test */
    public function it_can_convert_itself_to_an_array()
    {
        $elementName = 'a';
        $mixedContentUrl = Url::create('https://example.com');
        $foundOnUrl = Url::create('https://spatie.be');

        $mixedContent = new MixedContent($elementName, $mixedContentUrl, $foundOnUrl);

        $this->assertEquals([
            'elementName' => 'a',
            'mixedContentUrl' => 'https://example.com/',
            'foundOnUrl' => 'https://spatie.be/',
        ], $mixedContent->toArray());


    }
}