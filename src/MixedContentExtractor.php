<?php

namespace Spatie\MixedContentScanner;

use Spatie\Crawler\Url;
use Illuminate\Support\Collection;
use Symfony\Component\DomCrawler\Link;
use Symfony\Component\DomCrawler\Crawler as DomCrawler;

class MixedContentExtractor
{
    public static function extract(string $html, string $currentUri): array
    {
        return static::getSearchNodes()
            ->mapSpread(function ($tagName, $attribute) use ($html, $currentUri) {
                return (new DomCrawler($html, $currentUri))
                    ->filterXPath("//{$tagName}[@{$attribute}]")
                    ->reduce(function (DomCrawler $node) {
                        return ! self::isShortLink($node);
                    })
                    ->each(function (DomCrawler $node) use ($tagName, $attribute) {
                        $url = Url::create($node->attr($attribute));

                        if ($tagName === 'link' && $attribute === 'href') {
                            if ($node->attr('rel') !== 'stylesheet') {
                                return null;
                            }
                        }

                        return $url->scheme === 'http' ? [$tagName, $url] : null;
                    });
            })
            ->flatten(1)
            ->filter()
            ->mapSpread(function ($tagName, $mixedContentUrl) use ($currentUri) {
                return new MixedContent($tagName, $mixedContentUrl, Url::create($currentUri));
            })
            ->toArray();
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

    protected static function isShortLink(DomCrawler $node)
    {
        $relAttribute = $node->getNode(0)->attributes->getNamedItem('rel');

        if (is_null($relAttribute)) {
            return false;
        }

        return strtolower($relAttribute->nodeValue) === 'shortlink';
    }
}
