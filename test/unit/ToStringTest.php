<?php

declare(strict_types=1);

namespace Horde\Url\Test\Unit;

use Horde\Url\Url;
use PHPUnit\Framework\TestCase;

/**
 * Tests for Horde\Url\Url::toString() method.
 *
 * @license  http://www.horde.org/licenses/lgpl21 LGPL 2.1
 * @package  Url
 */
class ToStringTest extends TestCase
{
    public function testToStringSimple(): void
    {
        $url = new Url('test');
        $this->assertEquals('test', $url->toString());
    }

    public function testToStringWithParameters(): void
    {
        $url = new Url('test?foo=1&bar=2');
        $this->assertEquals('test?foo=1&amp;bar=2', $url->toString());
    }

    public function testToStringRaw(): void
    {
        $url = new Url('test?foo=1&bar=2');
        $this->assertEquals('test?foo=1&bar=2', $url->toString(raw: true));
    }

    public function testToStringPartial(): void
    {
        $url = new Url('http://example.com/path');
        $this->assertEquals('/path', $url->toString(full: false));
    }

    public function testToStringWithAnchor(): void
    {
        $url = new Url('test#anchor');
        $this->assertEquals('test#anchor', $url->toString());
    }

    public function testStringable(): void
    {
        $url = new Url('test?foo=1');
        $this->assertEquals('test?foo=1&amp;bar=2', $url->add('bar', 2)->toString());
    }
}
