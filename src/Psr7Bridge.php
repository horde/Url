<?php

declare(strict_types=1);

namespace Horde\Url;

use Psr\Http\Message\UriInterface;
use RuntimeException;

/**
 * PSR-7 bridge for Horde\Url.
 *
 * Provides conversion between Horde\Url\Url and PSR-7 UriInterface.
 *
 * Copyright 2026 Horde LLC (http://www.horde.org/)
 *
 * See the enclosed file LICENSE for license information (LGPL). If you
 * did not receive this file, see http://www.horde.org/licenses/lgpl21.
 *
 * @author   Michael Slusarz <slusarz@horde.org>
 * @license  http://www.horde.org/licenses/lgpl21 LGPL 2.1
 * @package  Url
 */
class Psr7Bridge
{
    /**
     * Convert a Horde\Url\Url to PSR-7 UriInterface.
     *
     * Requires a PSR-7 implementation (e.g., horde/http, nyholm/psr7, guzzlehttp/psr7).
     *
     * @param Url $url              The Horde URL to convert.
     * @param string|null $factoryClass  The PSR-7 URI factory class to use. If null, auto-detect.
     *
     * @return UriInterface  PSR-7 URI object.
     * @throws RuntimeException if no PSR-7 implementation is available.
     */
    public static function toPsr7(Url $url, ?string $factoryClass = null): UriInterface
    {
        // Auto-detect PSR-7 implementation if not specified
        if ($factoryClass === null) {
            $implementations = [
                '\\Horde\\Http\\Uri',            // horde/http
                '\\Nyholm\\Psr7\\Uri',          // nyholm/psr7
                '\\GuzzleHttp\\Psr7\\Uri',      // guzzlehttp/psr7
                '\\Laminas\\Diactoros\\Uri',    // laminas/laminas-diactoros
            ];

            foreach ($implementations as $class) {
                if (class_exists($class)) {
                    $factoryClass = $class;
                    break;
                }
            }

            if ($factoryClass === null) {
                throw new RuntimeException(
                    "No PSR-7 implementation found. "
                    . "Install horde/http, nyholm/psr7, guzzlehttp/psr7, or laminas/laminas-diactoros."
                );
            }
        }

        if (!class_exists($factoryClass)) {
            throw new RuntimeException(
                "PSR-7 implementation not found: $factoryClass"
            );
        }

        return new $factoryClass($url->toString(raw: true));
    }

    /**
     * Create a Horde\Url\Url from PSR-7 UriInterface.
     *
     * @param UriInterface $uri  PSR-7 URI object.
     * @param bool|null $raw     Whether to output the URL in raw or HTML-encoded format.
     *
     * @return Url  Horde URL object.
     */
    public static function fromPsr7(UriInterface $uri, ?bool $raw = null): Url
    {
        return new Url((string) $uri, $raw);
    }
}
