<?php

namespace Spatie\MixedContentScanner\Test;

use GuzzleHttp\Psr7\Uri;
use PHPUnit\Framework\TestCase;
use Spatie\MixedContentScanner\MixedContent;

class MixedContentTest extends TestCase
{
    /** @test */
    public function it_can_convert_itself_to_an_array()
    {
        $elementName = 'a';
        $mixedContentUrl = new Uri('https://example.com');
        $foundOnUrl = new Uri('https://spatie.be');

        $mixedContent = new MixedContent($elementName, $mixedContentUrl, $foundOnUrl);

        $this->assertEquals([
            'elementName' => 'a',
            'mixedContentUrl' => 'https://example.com/',
            'foundOnUrl' => 'https://spatie.be/',
        ], $mixedContent->toArray());
    }
}
