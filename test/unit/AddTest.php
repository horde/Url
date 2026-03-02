<?php

declare(strict_types=1);

namespace Horde\Url\Test\Unit;

use Horde\Url\Url;
use PHPUnit\Framework\TestCase;

/**
 * Tests for Horde\Url\Url::add() method.
 *
 * @author   Jan Schneider <jan@horde.org>
 * @license  http://www.horde.org/licenses/lgpl21 LGPL 2.1
 * @package  Url
 */
class AddTest extends TestCase
{
    public function testAddSimple(): void
    {
        $url = new Url('test');
        $url->add('foo', 1);
        $this->assertEquals('test?foo=1', (string)$url);
        $url->add('bar', 2);
        $this->assertEquals('test?foo=1&amp;bar=2', (string)$url);
        $url->add('baz', 3);
        $this->assertEquals('test?foo=1&amp;bar=2&amp;baz=3', (string)$url);
        $url->add('fez', null);
        $this->assertEquals('test?foo=1&amp;bar=2&amp;baz=3&amp;fez', (string)$url);

        $url->setAnchor('boo');
        $this->assertEquals('test?foo=1&amp;bar=2&amp;baz=3&amp;fez#boo', (string)$url);
        $url->setAnchor('bee');
        $this->assertEquals('test?foo=1&amp;bar=2&amp;baz=3&amp;fez#bee', (string)$url);
    }

    public function testAddArray(): void
    {
        $url = new Url('test');
        $url->add(['foo' => 1, 'bar' => 2]);
        $this->assertEquals('test?foo=1&amp;bar=2', (string)$url);

        $url = new Url('test?foo=1');
        $url->add(['bar' => 2, 'baz' => 3]);
        $this->assertEquals('test?foo=1&amp;bar=2&amp;baz=3', (string)$url);
    }

    public function testAddToExistingUrl(): void
    {
        $url = new Url('test?foo=1&bar=2');
        $url->add(['baz' => 3]);
        // URL parsed with & stays raw
        $this->assertEquals('test?foo=1&bar=2&baz=3', (string)$url);

        $url = new Url('test?foo=1&bar=2');
        $url->add('baz', 3);
        $this->assertEquals('test?foo=1&bar=2&baz=3', (string)$url);
    }

    public function testAddToExistingHtmlEncodedUrl(): void
    {
        $url = new Url('test?foo=1&amp;bar=2');
        $url->add(['baz' => 3]);
        $this->assertEquals('test?foo=1&amp;bar=2&amp;baz=3', (string)$url);

        $url = new Url('test?foo=1&amp;bar=2');
        $url->add('baz', 3);
        $this->assertEquals('test?foo=1&amp;bar=2&amp;baz=3', (string)$url);
    }

    public function testAddOverride(): void
    {
        $url = new Url('test?foo=1');
        $url->add('foo', 2);
        $this->assertEquals('test?foo=2', (string)$url);
    }

    public function testAddArrays(): void
    {
        $url = new Url('test');
        $url->add('foo', [1, 2, 3]);
        // Indexed arrays output as foo[]=value
        $this->assertEquals('test?foo[]=1&amp;foo[]=2&amp;foo[]=3', (string)$url);

        $url = new Url('test');
        $url->add('foo', ['bar' => 1, 'baz' => 2]);
        // Associative arrays output as foo[key]=value (brackets not URL-encoded)
        $this->assertEquals('test?foo[bar]=1&amp;foo[baz]=2', (string)$url);

        $url = new Url('test');
        $url->add('foo[]', 1);
        $url->add('foo[]', 2);
        // Using foo[] adds to array, output uses foo[] notation
        $this->assertEquals('test?foo[]=1&amp;foo[]=2', (string)$url);
    }

    public function testAddNull(): void
    {
        $url = new Url('test');
        $url->add('foo', null);
        $this->assertEquals('test?foo', (string)$url);
    }

    public function testAddZero(): void
    {
        $url = new Url('test');
        $url->add('foo', 0);
        $this->assertEquals('test?foo=0', (string)$url);
    }

    public function testAddZeroString(): void
    {
        $url = new Url('test');
        $url->add('foo', '0');
        $this->assertEquals('test?foo=0', (string)$url);
    }
}
