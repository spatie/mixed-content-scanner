<?php

use Spatie\Crawler\Crawler;
use Spatie\MixedContentScanner\Exceptions\InvalidUrl;
use Spatie\MixedContentScanner\MixedContentScanner;
use Spatie\MixedContentScanner\Test\MixedContentLogger;
use Spatie\MixedContentScanner\Test\Server;

it('can find mixed content', function () {
    $logger = new MixedContentLogger;

    $scanner = new MixedContentScanner($logger);

    $scanner->scan('http://'.Server::getServerUrl());

    $logger->assertPageHasMixedContent('/mixedContent');

    $logger->assertPageHasNoMixedContent('/noMixedContent');
});

it('will only mark stylesheet rel as mixed content', function () {
    $logger = new MixedContentLogger;

    $scanner = new MixedContentScanner($logger);

    $scanner->scan('http://'.Server::getServerUrl());

    $logger->assertPageHasMixedContent('/linkRelStyleSheet');

    $logger->assertPageHasNoMixedContent('/linkRelProfile');
});

it('will throw an exception when given an url with an invalid protocol', function () {
    $logger = new MixedContentLogger;

    $scanner = new MixedContentScanner($logger);

    $scanner->scan('blabla');
})->throws(InvalidUrl::class);

it('can limit the amount of crawled urls', function () {
    foreach (range(1, 3) as $crawlCount) {
        $logger = new MixedContentLogger;

        $scanner = (new MixedContentScanner($logger))->configureCrawler(function (Crawler $crawler) use ($crawlCount) {
            $crawler->setTotalCrawlLimit($crawlCount);
        });

        $scanner->scan('http://'.Server::getServerUrl());

        $logger->assertCrawlCount($crawlCount);
    }
});

it('can scan pages not ending in slash', function () {
    $logger = new MixedContentLogger;

    $scanner = new MixedContentScanner($logger);

    $scanner->scan('http://'.Server::getRootServerUrl());

    $logger->assertPageHasMixedContent('/mixedContent');

    $logger->assertPageHasNoMixedContent('/noMixedContent');
});
