<?php

/**
 * Tests for Horde_Url backward compatibility shim.
 *
 * @license  http://www.horde.org/licenses/lgpl21 LGPL 2.1
 * @package  Url
 */

use PHPUnit\Framework\TestCase;

class Horde_Url_ShimTest extends TestCase
{
    public function testConstructorWithString(): void
    {
        $url = new Horde_Url('http://example.com/path');
        $this->assertEquals('http://example.com/path', $url->toString());
    }

    public function testConstructorWithRawParameter(): void
    {
        $url = new Horde_Url('test?foo=1&bar=2', true);
        $this->assertEquals('test?foo=1&bar=2', (string) $url);
    }

    public function testPropertyAccessAnchor(): void
    {
        $url = new Horde_Url('test');
        $url->anchor = 'section';
        $this->assertEquals('section', $url->anchor);
        $this->assertEquals('test#section', $url->toString());
    }

    public function testPropertyAccessPathInfo(): void
    {
        $url = new Horde_Url('test');
        $url->pathInfo = 'extra/path';
        $this->assertEquals('extra/path', $url->pathInfo);
        $this->assertStringContainsString('extra/path', $url->toString());
    }

    public function testPropertyAccessParameters(): void
    {
        $url = new Horde_Url('test');
        $url->parameters = ['foo' => 'bar', 'baz' => 'qux'];
        $this->assertEquals(['foo' => 'bar', 'baz' => 'qux'], $url->parameters);
        $this->assertStringContainsString('foo=bar', $url->toString());
    }

    public function testPropertyAccessRaw(): void
    {
        $url = new Horde_Url('test?foo=1&bar=2');
        $url->raw = true;
        $this->assertTrue($url->raw);
        $this->assertEquals('test?foo=1&bar=2', (string) $url);
    }

    public function testPropertyAccessUrl(): void
    {
        $url = new Horde_Url();
        $url->url = 'http://example.com';
        $this->assertEquals('http://example.com', $url->url);
    }

    public function testAddMethod(): void
    {
        $url = new Horde_Url('test');
        $result = $url->add('foo', 'bar');
        $this->assertInstanceOf(Horde_Url::class, $result);
        $this->assertStringContainsString('foo=bar', $url->toString());
    }

    public function testAddMethodChaining(): void
    {
        $url = new Horde_Url('test');
        $url->add('foo', 'bar')->add('baz', 'qux');
        $output = $url->toString();
        $this->assertStringContainsString('foo=bar', $output);
        $this->assertStringContainsString('baz=qux', $output);
    }

    public function testRemoveMethod(): void
    {
        $url = new Horde_Url('test?foo=1&bar=2');
        $url->remove('foo');
        $output = $url->toString();
        $this->assertStringNotContainsString('foo=1', $output);
        $this->assertStringContainsString('bar=2', $output);
    }

    public function testSetAnchorMethod(): void
    {
        $url = new Horde_Url('test');
        $result = $url->setAnchor('section');
        $this->assertInstanceOf(Horde_Url::class, $result);
        $this->assertEquals('test#section', $url->toString());
    }

    public function testSetRawMethod(): void
    {
        $url = new Horde_Url('test?foo=1&bar=2');
        $url->setRaw(true);
        $this->assertEquals('test?foo=1&bar=2', (string) $url);
    }

    public function testToStringLooseTypes(): void
    {
        $url = new Horde_Url('test?foo=1');
        // Pass non-boolean values (loose typing)
        $this->assertEquals('test?foo=1', $url->toString(1, 1)); // 1 = raw
        $this->assertEquals('test?foo=1&amp;bar=2', $url->add('bar', 2)->toString(0, 1)); // 0 = HTML-encoded
    }

    public function testCopyMethod(): void
    {
        $url = new Horde_Url('test?foo=1');
        $copy = $url->copy();
        $this->assertInstanceOf(Horde_Url::class, $copy);
        $this->assertNotSame($url, $copy);
        $this->assertEquals($url->toString(), $copy->toString());

        // Verify it's a true copy
        $copy->add('bar', '2');
        $this->assertStringContainsString('bar=2', $copy->toString());
        $this->assertStringNotContainsString('bar=2', $url->toString());
    }

    public function testLinkMethod(): void
    {
        $url = new Horde_Url('http://example.com');
        $link = $url->link(['class' => 'test']);
        $this->assertStringContainsString('<a', $link);
        $this->assertStringContainsString('href=', $link);
        $this->assertStringContainsString('example.com', $link);
        $this->assertStringContainsString('class="test"', $link);
    }

    public function testUniqueMethod(): void
    {
        $url = new Horde_Url('test');
        $before = $url->toString();
        $url->unique();
        $after = $url->toString();
        $this->assertNotEquals($before, $after);
        $this->assertStringContainsString('u=', $after);
    }

    public function testStaticUriB64Encode(): void
    {
        $encoded = Horde_Url::uriB64Encode('test string');
        $this->assertIsString($encoded);
        $this->assertStringNotContainsString('+', $encoded);
        $this->assertStringNotContainsString('/', $encoded);
        $this->assertStringNotContainsString('=', $encoded);
    }

    public function testStaticUriB64Decode(): void
    {
        $encoded = Horde_Url::uriB64Encode('test string');
        $decoded = Horde_Url::uriB64Decode($encoded);
        $this->assertEquals('test string', $decoded);
    }

    public function testMagicIsset(): void
    {
        $url = new Horde_Url('test');
        $this->assertTrue(isset($url->url));
        $this->assertTrue(isset($url->anchor));
        $this->assertTrue(isset($url->parameters));
    }

    public function testExtendabilityWithLooseSignature(): void
    {
        // Test that subclasses can override toString with loose typing
        $subclass = new class('test') extends Horde_Url {
            public function toString($raw = false, $full = true)
            {
                return 'custom:' . parent::toString($raw, $full);
            }
        };

        $this->assertStringStartsWith('custom:', $subclass->toString());
    }

    public function testConstructorAcceptsHordeUrl(): void
    {
        $url1 = new Horde_Url('test?foo=1');
        $url2 = new Horde_Url($url1);
        $this->assertEquals($url1->toString(), $url2->toString());

        // Verify it's a copy, not a reference
        $url2->add('bar', '2');
        $this->assertStringContainsString('bar=2', $url2->toString());
        $this->assertStringNotContainsString('bar=2', $url1->toString());
    }

    public function testConstructorAcceptsModernUrl(): void
    {
        $modern = new \Horde\Url\Url('test?foo=1');
        $legacy = new Horde_Url($modern);
        $this->assertStringContainsString('foo=1', $legacy->toString());
    }
}
