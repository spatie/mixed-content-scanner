<?php

namespace Spatie\MixedContentScanner;

use Closure;
use GuzzleHttp\Psr7\Uri;
use Spatie\Crawler\Crawler;
use Spatie\Crawler\CrawlProfiles\CrawlInternalUrls;
use Spatie\Crawler\CrawlProfiles\CrawlProfile;
use Spatie\MixedContentScanner\Exceptions\InvalidUrl;

class MixedContentScanner
{
    public MixedContentObserver $mixedContentObserver;

    public ?CrawlProfile $crawlProfile = null;

    protected ?int $maximumCrawlCount = null;

    protected Closure $configureCrawler;

    public function __construct(MixedContentObserver $mixedContentObserver)
    {
        $this->mixedContentObserver = $mixedContentObserver;

        $this->configureCrawler = function (Crawler $crawler) {
        };
    }

    public function scan(string $url, array $clientOptions = []): void
    {
        $this->guardAgainstInvalidUrl($url);

        $url = new Uri($url);

        $crawler = Crawler::create($clientOptions);

        ($this->configureCrawler)($crawler);

        if ($this->maximumCrawlCount) {
            $crawler->setMaximumCrawlCount($this->maximumCrawlCount);
        }

        $crawler->setCrawlProfile($this->crawlProfile ?? new CrawlInternalUrls($url))
            ->setCrawlObserver($this->mixedContentObserver)
            ->startCrawling($url);
    }

    public function configureCrawler(callable $callable): self
    {
        $this->configureCrawler = $callable;

        return $this;
    }

    public function setCrawlProfile(CrawlProfile $crawlProfile): self
    {
        $this->crawlProfile = $crawlProfile;

        return $this;
    }

    protected function guardAgainstInvalidUrl(string $url): void
    {
        if ($url === '') {
            throw InvalidUrl::urlIsEmpty();
        }

        if (!$this->startsWith($url, ['http://', 'https://'])) {
            throw InvalidUrl::invalidScheme($url);
        }
    }

    protected function startsWith(string $haystack, array $needles): bool
    {
        foreach ((array) $needles as $needle) {
            if ($needle != '' && substr($haystack, 0, strlen($needle)) === (string) $needle) {
                return true;
            }
        }

        return false;
    }
}
