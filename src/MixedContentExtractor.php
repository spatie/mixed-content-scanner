<?php

namespace Spatie\MixedContentScanner;

use Exception;
use GuzzleHttp\Psr7\Uri;
use Illuminate\Support\Collection;
use Psr\Http\Message\UriInterface;
use Symfony\Component\DomCrawler\Crawler as DomCrawler;

class MixedContentExtractor
{
    public static function extract(string $html, string $currentUri): array
    {
        $linkedCss = [];
        $mixed = static::getSearchNodes()
            ->mapSpread(function ($tagName, $attribute) use ($html, $currentUri, &$linkedCss) {
                return (new DomCrawler($html, (string) $currentUri))
                    ->filterXPath("//{$tagName}[@{$attribute}]")
                    ->reduce(function (DomCrawler $node) {
                        return ! self::isShortLink($node);
                    })
                    ->each(function (DomCrawler $node) use ($tagName, $attribute, &$linkedCss) {
                        try {
                            $url = new Uri($node->attr($attribute));

                            if ($tagName === 'link' && $attribute === 'href') {
                                if ($node->attr('rel') !== 'stylesheet') {
                                    return;
                                }

                                if (self::hrefIsCss($url)) {
                                    $linkedCss[] = $url;
                                }
                            }

                            return $url->getScheme() === 'http' ? [$tagName, $url] : null;
                        } catch (Exception $e) {
                            // ignore invalid links
                        }
                    });
            })
            ->flatten(1)
            ->filter()
            ->mapSpread(function ($tagName, $mixedContentUrl) use ($currentUri) {
                return new MixedContent($tagName, $mixedContentUrl, new Uri($currentUri));
            })
            ->toArray();

        return [$mixed, $linkedCss];
    }

    protected static function getSearchNodes(): Collection
    {
        return collect([
            ['audio', 'src'],
            ['embed', 'src'],
            ['form', 'action'],
            ['link', 'href'],
            ['iframe', 'src'],
            ['img', 'src'],
            ['img', 'srcset'],
            ['object', 'data'],
            ['param', 'value'],
            ['script', 'src'],
            ['source', 'src'],
            ['source', 'srcset'],
            ['video', 'src'],
        ]);
    }

    protected static function isShortLink(DomCrawler $node): bool
    {
        $relAttribute = $node->getNode(0)->attributes->getNamedItem('rel');

        if (is_null($relAttribute)) {
            return false;
        }

        return strtolower($relAttribute->nodeValue) === 'shortlink';
    }

    protected static function hrefIsCss(UriInterface $uri): bool
    {
        return strpos((string) $uri, '.css') !== false;
    }
}
