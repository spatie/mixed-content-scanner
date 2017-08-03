<?php

namespace Spatie\MixedContentScanner;

use Spatie\Crawler\EmptyCrawlObserver;
use Spatie\Crawler\Url;

class MixedContentObserver extends EmptyCrawlObserver
{
    public function hasBeenCrawled(Url $crawledUrl, $response, Url $foundOnUrl = null)
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
     * @param \Spatie\Crawler\Url $crawledUrl
     */
    public function didNotRespond(Url $crawledUrl)
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
     * @param \Spatie\Crawler\Url $crawledUrl
     */
    public function noMixedContentFound(Url $crawledUrl)
    {
    }

    /**
     * Will be called when the scanner has finished crawling.
     */
    public function finishedCrawling()
    {
    }
}
