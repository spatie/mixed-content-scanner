<?php

use GuzzleHttp\Psr7\Uri;
use Spatie\MixedContentScanner\MixedContent;

it('can convert itself to an array', function () {
    $elementName = 'a';
    $mixedContentUrl = new Uri('https://example.com');
    $foundOnUrl = new Uri('https://spatie.be');

    $mixedContent = new MixedContent($elementName, $mixedContentUrl, $foundOnUrl);

    expect($mixedContent->toArray())->toBe([
        'elementName' => 'a',
        'mixedContentUrl' => 'https://example.com/',
        'foundOnUrl' => 'https://spatie.be/',
    ]);
});
