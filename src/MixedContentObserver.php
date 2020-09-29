<?php

namespace Spatie\MixedContentScanner;

use GuzzleHttp\Exception\RequestException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\UriInterface;
use Spatie\Crawler\CrawlObservers\CrawlObserver;

class MixedContentObserver extends CrawlObserver
{
    public function crawled(UriInterface $url, ResponseInterface $response, ?UriInterface $foundOnUrl = null): void
    {
        $mixedContent = MixedContentExtractor::extract((string) $response->getBody(), $url);

        if (!count($mixedContent)) {
            $this->noMixedContentFound($url);

            return;
        }

        foreach ($mixedContent as $mixedContentItem) {
            $this->mixedContentFound($mixedContentItem);
        }
    }

    public function crawlFailed(
        UriInterface $url,
        RequestException $requestException,
        ?UriInterface $foundOnUrl = null
    ): void {
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
    public function noMixedContentFound(UriInterface $crawledUrl): void
    {
    }
}
