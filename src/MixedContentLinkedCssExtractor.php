<?php

namespace Spatie\MixedContentScanner;

use GuzzleHttp\Psr7\Uri;
use Psr\Http\Message\UriInterface;

class MixedContentLinkedCssExtractor
{
    public static function extract(string $css, UriInterface $cssUri, UriInterface $linkedByUri): array
    {
        $pattern = '/url\((http:\/\/.*?)\)/m';
        $matches = [];
        preg_match_all($pattern, $css, $matches);

        return collect($matches[1])->map(function ($item, $i) use ($cssUri, $linkedByUri) {
            return new MixedContent($cssUri, new Uri($item), $linkedByUri);
        })->toArray();
    }
}
