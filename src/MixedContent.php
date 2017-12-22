<?php

namespace Spatie\MixedContentScanner;

use Psr\Http\Message\UriInterface;

class MixedContent
{
    /** string */
    public $elementName = '';

    /** @var\Spatie\Crawler\Url */
    public $mixedContentUrl = null;

    /** @var\Spatie\Crawler\Url */
    public $foundOnUrl = null;

    public function __construct(string $elementName, UriInterface $mixedContentUrl, UriInterface $foundOnUrl)
    {
        $this->elementName = $elementName;

        $this->mixedContentUrl = $mixedContentUrl;

        $this->foundOnUrl = $foundOnUrl;
    }

    public function toArray(): array
    {
        return [
            'elementName' => $this->elementName,
            'mixedContentUrl' => (string) $this->mixedContentUrl,
            'foundOnUrl' => (string) $this->foundOnUrl,
        ];
    }
}
