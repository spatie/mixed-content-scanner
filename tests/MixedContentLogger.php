<?php

namespace Spatie\MixedContentScanner\Test;

use PHPUnit\Framework\Assert;
use Psr\Http\Message\UriInterface;
use Spatie\MixedContentScanner\MixedContent;
use Spatie\MixedContentScanner\MixedContentObserver;

class MixedContentLogger extends MixedContentObserver
{
    protected $log = [];

    public function mixedContentFound(MixedContent $mixedContent)
    {
        $this->log[] = $mixedContent;
    }

    public function noMixedContentFound(UriInterface $crawledUrl)
    {
        $this->log[] = $crawledUrl;
    }

    public function assertPageHasMixedContent(string $pageUrl)
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

    public function assertPageHasNoMixedContent(string $pageUrl)
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

    public function assertCrawlCount(int $count)
    {
        $actualCount = count($this->log);

        Assert::assertEquals($count, $actualCount, "Crawled {$count} urls instead of the expected {$actualCount}");
    }

    public function assertPageHasLinkedCssWithMixedContent(string $pageUrl)
    {
        $log = collect($this->log)->filter(function ($logItem) {
            return $logItem instanceof MixedContent;
        })->map(function ($item) {
            return (string) $item->mixedContentUrl;
        })->toArray();
        Assert::assertEquals($log, [
            'http://localhost:9000/css/linked.css',
            'http://example.com/image.jpg',
        ]);
    }

    public function assertPageHasLinkedCssThatWasNotScanned(string $pageUrl)
    {
        $log = collect($this->log)->filter(function ($logItem) {
            return $logItem instanceof MixedContent;
        })->map(function ($item) {
            return (string) $item->mixedContentUrl;
        })->toArray();
        Assert::assertEquals($log, [
            'http://localhost:9000/css/linked.css',
        ]);
    }
}
