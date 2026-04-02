<?php

declare(strict_types=1);

namespace Horde\Url\Test\Unit;

use Horde\Url\Url;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

/**
 * Tests for Horde\Url\Url::copy() method.
 *
 * @license  http://www.horde.org/licenses/lgpl21 LGPL 2.1
 * @package  Url
 */
#[CoversClass(Url::class)]
class CopyTest extends TestCase
{
    public function testCopyCreatesIndependentInstance(): void
    {
        $original = new Url('http://example.com/path');
        $original->add('foo', 'bar');

        $copy = $original->copy();

        $this->assertNotSame($original, $copy);
        $this->assertEquals((string) $original, (string) $copy);
    }

    public function testModificationsToCopyDontAffectOriginal(): void
    {
        $original = new Url('http://example.com/path');
        $original->add('foo', 'bar');

        $copy = $original->copy();
        $copy->add('baz', 'qux');
        $copy->setAnchor('section');

        // Original should be unchanged
        $this->assertEquals('http://example.com/path?foo=bar', (string) $original);
        $this->assertEquals('', $original->anchor);

        // Copy should have new changes
        $this->assertEquals('http://example.com/path?foo=bar&amp;baz=qux#section', (string) $copy);
        $this->assertEquals('section', $copy->anchor);
    }

    public function testCopyCopiesAllProperties(): void
    {
        $original = new Url('http://example.com/path');
        $original->add(['foo' => 'bar', 'arr' => [1, 2, 3]]);
        $original->setAnchor('top');
        $original->setRaw(true);
        $original->pathInfo = '/extra/path';

        $copy = $original->copy();

        $this->assertEquals($original->url, $copy->url);
        $this->assertEquals($original->parameters, $copy->parameters);
        $this->assertEquals($original->anchor, $copy->anchor);
        $this->assertEquals($original->raw, $copy->raw);
        $this->assertEquals($original->pathInfo, $copy->pathInfo);
    }

    public function testCopySupportsChaining(): void
    {
        $original = new Url('http://example.com');

        $result = $original->copy()->add('foo', 'bar')->setAnchor('section');

        $this->assertInstanceOf(Url::class, $result);
        $this->assertEquals('http://example.com?foo=bar#section', (string) $result);

        // Original unchanged
        $this->assertEquals('http://example.com', (string) $original);
    }

    public function testCopyWithCallback(): void
    {
        $original = new Url('http://example.com');
        $original->toStringCallback = fn($url) => 'custom:' . $url->url;

        $copy = $original->copy();

        $this->assertNotNull($copy->toStringCallback);
        $this->assertEquals('custom:http://example.com', (string) $copy);
    }
}
