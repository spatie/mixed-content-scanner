**WORK IN PROGRESS, DO NOT USE YET**

# Scan your site for mixed content

[![Latest Version on Packagist](https://img.shields.io/packagist/v/spatie/mixed-content-scanner.svg?style=flat-square)](https://packagist.org/packages/spatie/mixed-content-scanner)
[![Build Status](https://img.shields.io/travis/spatie/mixed-content-scanner/master.svg?style=flat-square)](https://travis-ci.org/spatie/mixed-content-scanner)
[![SensioLabsInsight](https://img.shields.io/sensiolabs/i/7a85bc21-0d7b-4b0d-875d-da5c5dcb853e.svg?style=flat-square)](https://insight.sensiolabs.com/projects/7a85bc21-0d7b-4b0d-875d-da5c5dcb853e)
[![Quality Score](https://img.shields.io/scrutinizer/g/spatie/mixed-content-scanner.svg?style=flat-square)](https://scrutinizer-ci.com/g/spatie/mixed-content-scanner)
[![StyleCI](https://styleci.io/repos/28050386/shield?branch=master)](https://styleci.io/repos/28050386)
[![Total Downloads](https://img.shields.io/packagist/dt/spatie/mixed-content-scanner.svg?style=flat-square)](https://packagist.org/packages/spatie/mixed-content-scanner)

This package contains a class that can scan your site for [mixed content](https://developer.mozilla.org/en-US/docs/Web/Security/Mixed_content).

Here's an example of how you can use it:

```php
use Spatie\MixedContentScanner\MixedContentScanner

$logger = new MixedContentLogger();

$scanner = new MixedContentScanner($logger);

$scanner->scan('https://example.com');
```

`MixedContentLogger` is a class that contains methods that get called when mixed content is (not) found. 

If you don't need a custom implementation but simply want to look for mixed content using a command line tool, take a look at [our mixed-content-scanner-cli package](https://github.com/spatie/mixed-content-scanner-cli).

## Postcardware

You're free to use this package (it's [MIT-licensed](LICENSE.md)), but if it makes it to your production environment we highly appreciate you sending us a postcard from your hometown, mentioning which of our package(s) you are using.

Our address is: Spatie, Samberstraat 69D, 2060 Antwerp, Belgium.

We publish all received postcards [on our company website](https://spatie.be/en/opensource/postcards).

## Installation

You can install the package via composer:

```bash
composer require spatie/mixed-content-scanner
```

## How it work under the hood

When scanning a site, the scanner will crawl everypage. On the html of these package, these elements and attributes will be checked:

- `audio`: `src`
- `embed`: `src`
- `form`: `action`
- `link`: `href`
- `iframe`: `src`
- `img`: `src`, `srcset`
- `object`: `data`
- `param`: `value`
- `script`: `src`
- `source`: `src`, `srcset`
- `video`: `src`

If any of those attributes start with `http://` the element will be regarded as mixed content.

The package does not scan linked `.css` or `.js` files. Inline `<script>` or `<style>` are not taken into consideration.

## Usage

```php
use Spatie\MixedContentScanner\MixedContentScanner

$logger = new MixedContentLogger();

$scanner = new MixedContentScanner($logger);

$scanner->scan('https://example.com');
```

That `MixedContentScanner` accepts an instance of a class that extends `\Spatie\MixedContentScannerMixedContentObserver`. You should create such a class yourself. Let's take a look at an example implementation.

```php
use Spatie\Crawler\Url;
use Spatie\MixedContentScanner\MixedContent;
use Spatie\MixedContentScanner\MixedContentObserver;

class MyMixedContentLogger extends MixedContentObserver
{
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
```

Of course you should supply a function body to these methods yourself. If you don't need a function just leave it off.

The `$mixedContent` variable the `mixedContentFound` class accept is an instance of `\Spatie\MixedContentScanner\MixedContent` which has these three properties:

- `$elementName`: the name of the element that is regarded as mixed content
- `$mixedContentUrl`: the url of the element that is regarded as mixed content. For an image this can be the value of `src` or `srcset` for a `form` this can be the value of `action`, ...
- `$foundOnUrl`: the url where the mixed content was found

### Customizing the requests

The scanner is powered by [our homegrown Crawler](https://github.com/spatie/crawler) which on it's turn leverages [Guzzle](http://docs.guzzlephp.org/en/stable/) to perform webrequests.
You can pass an array of options to the second argument of `MixedContentScanner`. These options will be passed to the Guzzle Client. 

Here's an example where ssl verification is being turned off.

```php
$scanner = new MixedContentScanner($logger, ['verify' => 'false']);
```

### Filtering the crawled urls

By default the mixed content scanner will crawl all urls of the hostname given. If you want to filter the urls to be crawled, you can pass the scanner an implementation of `Spatie\Crawler\CrawlProfile`.

Here's the interface:

```php
namespace Spatie\Crawler;

interface CrawlProfile
{
    /**
     * Determine if the given url should be crawled.
     *
     * @param \Spatie\Crawler\Url $url
     *
     * @return bool
     */
    public function shouldCrawl(Url $url): bool;
}
```

And here's how you can let the scanner use your profile:

```php
use Spatie\MixedContentScanner\MixedContentScanner;

$logger = new MixedContentLogger();

$scanner = new MixedContentScanner($logger);

$scanner->useCrawlProfile(new MyCrawlProfile);
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Testing

``` bash
composer test
```

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security

If you discover any security related issues, please email freek@spatie.be instead of using the issue tracker.

## Credits

- [Freek Van der Herten](https://github.com/freekmurze)
- [All Contributors](../../contributors)

The scanner is inspired by [mixed-content-scan](https://github.com/bramus/mixed-content-scan) by [Bram Van Damme](https://github.com/bramus). Parts of his readme and code were used.

## About Spatie

Spatie is a webdesign agency based in Antwerp, Belgium. You'll find an overview of all our open source projects [on our website](https://spatie.be/opensource).

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
