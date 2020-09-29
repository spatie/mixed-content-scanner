<?php

namespace Spatie\MixedContentScanner;

use Psr\Http\Message\UriInterface;

class MixedContent
{
    public string $elementName = '';

    public ?UriInterface $mixedContentUrl = null;

    public ?UriInterface $foundOnUrl = null;

    public function __construct(string $elementName, UriInterface $mixedContentUrl, UriInterface $foundOnUrl)
    {
        $this->elementName = $elementName;

        if ($mixedContentUrl->getPath() === '') {
            $mixedContentUrl = $mixedContentUrl->withPath('/');
        }

        $this->mixedContentUrl = $mixedContentUrl;

        if ($foundOnUrl->getPath() === '') {
            $foundOnUrl = $foundOnUrl->withPath('/');
        }

        $this->foundOnUrl = $foundOnUrl;
    }

    public function toArray(): array
    {
        return [
            'elementName'     => $this->elementName,
            'mixedContentUrl' => (string) $this->mixedContentUrl,
            'foundOnUrl'      => (string) $this->foundOnUrl,
        ];
    }
}
