<?php

namespace Spatie\MixedContentScanner;

use Spatie\Crawler\Crawler;
use Spatie\Crawler\CrawlProfile;
use Spatie\Crawler\CrawlInternalUrls;

class MixedContentScanner
{
    /** @var \Spatie\MixedContentScanner\MixedContentObserver */
    public $mixedContentObserver;

    /** @var null|\Spatie\Crawler\CrawlProfile */
    public $crawlProfile;

    public function __construct(MixedContentObserver $mixedContentObserver)
    {
        $this->mixedContentObserver = $mixedContentObserver;
    }

    public function scan(string $url, array $clientOptions = [])
    {
        Crawler::create($clientOptions)
            ->setCrawlProfile($this->crawlProfile ?? new CrawlInternalUrls($url))
            ->setCrawlObserver($this->mixedContentObserver)
            ->startCrawling($url);
    }

    public function useCrawlProfile(CrawlProfile $crawlProfile)
    {
        $this->crawlProfile = $crawlProfile;
    }
}
