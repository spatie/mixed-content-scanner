<?php

namespace Spatie\MixedContentScanner;

use Psr\Http\Message\UriInterface;
use Spatie\Crawler\EmptyCrawlObserver;

class MixedContentObserver extends EmptyCrawlObserver
{
    public function hasBeenCrawled(UriInterface $crawledUrl, $response, ?UriInterface $foundOnUrl = null)
    {
        if (! $response) {
            $this->didNotRespond($crawledUrl, $response);

            return;
        }

        $mixedContent = MixedContentExtractor::extract((string) $response->getBody(), $crawledUrl);

        if (! count($mixedContent)) {
            $this->noMixedContentFound($crawledUrl);

            return;
        }

        foreach ($mixedContent as $mixedContentItem) {
            $this->mixedContentFound($mixedContentItem);
        }
    }

    /**
     * Will be called when the host did not give a response for the given url.
     *
     * @param \Psr\Http\Message\UriInterface
     */
    public function didNotRespond(UriInterface $crawledUrl)
    {
    }

    /**
     * Will be called when mixed content was found.
     *
     * @param \Spatie\MixedContentScanner\MixedContent $mixedContent
     */
    public function mixedContentFound(MixedContent $mixedContent)
    {
    }

    /**
     * Will be called when no mixed content was found on the given url.
     *
     * @param \Psr\Http\Message\UriInterface
     */
    public function noMixedContentFound(UriInterface $crawledUrl)
    {
    }
}
