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
            ->map(function (array $nodeProperties) use ($html, $currentUri) {
                [$elementName, $attribute] = $nodeProperties;

                return (new DomCrawler($html, $currentUri))
                    ->filterXPath("//{$elementName}[@{$attribute}]")
                    ->each(function (DomCrawler $node) use ($elementName, $attribute) {
                        $url = Url::create($node->attr($attribute));

                        return [$elementName, $url];
                    });
            })
            ->flatten(1)
            ->filter()
            ->filter(function (array $nodeProperties) {
                [$elementName, $url] = $nodeProperties;

                return $url->scheme === 'http';
            })
            ->map(function (array $nodeProperties) use ($currentUri) {
                [$elementName, $mixedContentUrl] = $nodeProperties;

                return new MixedContent($elementName, $mixedContentUrl, Url::create($currentUri));
            })
            ->toArray();
    }

    protected static function getSearchNodes(): Collection
    {
        return collect([
            'audio' => ['src'],
            'embed' => ['src'],
            'form' => ['action'],
            'link' => ['href'],
            'iframe' => ['src'],
            'img' => ['src', 'srcset'],
            'object' => ['data'],
            'param' => ['value'],
            'script' => ['src'],
            'source' => ['src', 'srcset'],
            'video' => ['src'],
        ])
            ->flatMap(function (array $attributes, string $element) {
                return collect($attributes)
                    ->map(function (string $attribute) use ($element) {
                        return [$element, $attribute];
                    });
            });
    }
}
