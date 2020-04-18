<?php

namespace Spatie\MixedContentScanner;

use GuzzleHttp\Exception\RequestException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\UriInterface;
use Spatie\Crawler\CrawlObserver;

class MixedContentObserver extends CrawlObserver
{
    private $crawledCss = [];

    private $shouldExtractLinkedCss = false;

    public function crawled(UriInterface $url, ResponseInterface $response, ?UriInterface $foundOnUrl = null)
    {
        [$mixedContent, $linkedCss] = MixedContentExtractor::extract((string) $response->getBody(), $url);

        if ($this->shouldExtractLinkedCss) {
            foreach ($linkedCss as $css) {
                $mixedContent = array_merge($mixedContent, $this->getMixedContentFromLinkedCss($css, $url));
            }
        }

        if (! count($mixedContent)) {
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
    ) {
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

    public function getMixedContentFromLinkedCss(UriInterface $css, UriInterface $linkedByUrl)
    {
        if (! in_array($css, $this->crawledCss)) {
            $this->crawledCss[] = $css;
            try {
                return MixedContentLinkedCssExtractor::extract(Body::get($css), $css, $linkedByUrl);
            } catch (RequestException $e) {
                $this->crawlFailed($css, $e, $linkedByUrl);
            }
        }

        return [];
    }

    public function withLinkedCss()
    {
        $this->shouldExtractLinkedCss = true;

        return $this;
    }
}
