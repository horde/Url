<?php

declare(strict_types=1);

namespace Horde\Url\Test\Unit;

use Horde\Url\Url;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

/**
 * Tests for Horde\Url\Url raw mode.
 *
 * @license  http://www.horde.org/licenses/lgpl21 LGPL 2.1
 * @package  Url
 */
#[CoversClass(Url::class)]
class RawTest extends TestCase
{
    public function testRawDefault(): void
    {
        $url = new Url('test?foo=1&bar=2');
        $this->assertEquals('test?foo=1&bar=2', $url->toString(raw: true));
        $this->assertEquals('test?foo=1&amp;bar=2', $url->toString(raw: false));
    }

    public function testRawConstructor(): void
    {
        $url = new Url('test?foo=1&bar=2', true);
        $this->assertEquals('test?foo=1&bar=2', (string)$url);
    }

    public function testRawConstructorFalse(): void
    {
        $url = new Url('test?foo=1&bar=2', false);
        $this->assertEquals('test?foo=1&amp;bar=2', (string)$url);
    }

    public function testRawSetRaw(): void
    {
        $url = new Url('test?foo=1&bar=2');
        $url->setRaw(true);
        $this->assertEquals('test?foo=1&bar=2', (string)$url);
    }

    public function testRawAnchor(): void
    {
        $url = new Url('test');
        $url->setAnchor('foo bar');
        $this->assertEquals('test#foo%20bar', $url->toString(raw: false));
        $this->assertEquals('test#foo bar', $url->toString(raw: true));
    }

    public function testRawPathInfo(): void
    {
        $url = new Url('test');
        $url->pathInfo = 'foo/bar baz';
        $this->assertEquals('test/foo/bar%20baz', $url->toString(raw: false));
        $this->assertEquals('test/foo/bar baz', $url->toString(raw: true));
    }
}
