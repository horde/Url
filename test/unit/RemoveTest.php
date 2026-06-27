<?php

declare(strict_types=1);

namespace Horde\Url\Test\Unit;

use Horde\Url\Url;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

/**
 * Tests for Horde\Url\Url::remove() method.
 *
 * @license  http://www.horde.org/licenses/lgpl21 LGPL 2.1
 * @package  Url
 */
#[CoversClass(Url::class)]
class RemoveTest extends TestCase
{
    public function testRemoveSimple(): void
    {
        $url = new Url('test?foo=1&bar=2&baz=3');
        $url->remove('foo');
        $this->assertEquals('test?bar=2&baz=3', (string) $url);
    }

    public function testRemoveArray(): void
    {
        $url = new Url('test?foo=1&bar=2&baz=3');
        $url->remove(['foo', 'bar']);
        $this->assertEquals('test?baz=3', (string) $url);
    }

    public function testRemoveAll(): void
    {
        $url = new Url('test?foo=1&bar=2&baz=3');
        $url->remove(['foo', 'bar', 'baz']);
        $this->assertEquals('test', (string) $url);
    }

    public function testRemoveNonExistent(): void
    {
        $url = new Url('test?foo=1');
        $url->remove('bar');
        $this->assertEquals('test?foo=1', (string) $url);
    }

    public function testRemoveChaining(): void
    {
        $url = new Url('test?foo=1&bar=2&baz=3');
        $url->remove('foo')->remove('bar');
        $this->assertEquals('test?baz=3', (string) $url);
    }
}
