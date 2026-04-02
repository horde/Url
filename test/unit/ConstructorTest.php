<?php

declare(strict_types=1);

namespace Horde\Url\Test\Unit;

use Horde\Url\Url;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

/**
 * Tests for Horde\Url\Url constructor.
 *
 * @license  http://www.horde.org/licenses/lgpl21 LGPL 2.1
 * @package  Url
 */
#[CoversClass(Url::class)]
class ConstructorTest extends TestCase
{
    public function testConstructEmpty(): void
    {
        $url = new Url();
        $this->assertEquals('', (string)$url);
    }

    public function testConstructString(): void
    {
        $url = new Url('test');
        $this->assertEquals('test', (string)$url);
    }

    public function testConstructUrl(): void
    {
        $url1 = new Url('test');
        $url2 = new Url($url1);
        $this->assertEquals('test', (string)$url2);
    }

    public function testConstructUrlWithRaw(): void
    {
        $url1 = new Url('test');
        $url2 = new Url($url1, true);
        $this->assertTrue($url2->raw);
    }
}
