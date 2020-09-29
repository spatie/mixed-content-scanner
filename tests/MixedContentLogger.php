<?php

namespace Spatie\MixedContentScanner\Test;

use PHPUnit\Framework\Assert;
use Psr\Http\Message\UriInterface;
use Spatie\MixedContentScanner\MixedContent;
use Spatie\MixedContentScanner\MixedContentObserver;

class MixedContentLogger extends MixedContentObserver
{
    protected $log = [];

    public function mixedContentFound(MixedContent $mixedContent): void
    {
        $this->log[] = $mixedContent;
    }

    public function noMixedContentFound(UriInterface $crawledUrl): void
    {
        $this->log[] = $crawledUrl;
    }

    public function assertPageHasMixedContent(string $pageUrl): void
    {
        $foundLogItems = collect($this->log)
            ->filter(function ($logItem) {
                return $logItem instanceof MixedContent;
            })
            ->filter(function (MixedContent $mixedContent) use ($pageUrl) {
                return $mixedContent->foundOnUrl->getPath() === $pageUrl;
            });

        Assert::assertTrue(count($foundLogItems) > 0, "Failed asserting that `{$pageUrl}` contains mixed content");
    }

    public function assertPageHasNoMixedContent(string $pageUrl): void
    {
        $foundLogItems = collect($this->log)
            ->filter(function ($logItem) {
                return $logItem instanceof UriInterface;
            })
            ->filter(function (UriInterface $url) use ($pageUrl) {
                return $url->getPath() === $pageUrl;
            });

        Assert::assertTrue(count($foundLogItems) > 0, "Failed asserting that `{$pageUrl}` contains no mixed content. Or maybe that url might not have been crawled");
    }

    public function assertCrawlCount(int $count): void
    {
        $actualCount = count($this->log);

        Assert::assertEquals($count, $actualCount, "Crawled {$count} urls instead of the expected {$actualCount}");
    }
}
