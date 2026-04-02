<?php

declare(strict_types=1);

namespace Horde\Url\Test\Unit;

use Horde\Url\Url;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

/**
 * Tests for Horde\Url\Url::link() method.
 *
 * @license  http://www.horde.org/licenses/lgpl21 LGPL 2.1
 * @package  Url
 */
#[CoversClass(Url::class)]
class LinkTest extends TestCase
{
    public function testLinkSimple(): void
    {
        $url = new Url('test');
        $this->assertEquals('<a href="test">', $url->link());
    }

    public function testLinkWithAttributes(): void
    {
        $url = new Url('test');
        $this->assertEquals('<a href="test" class="foo">', $url->link(['class' => 'foo']));
    }

    public function testLinkWithRawAttribute(): void
    {
        $url = new Url('test');
        $result = $url->link(['onclick.raw' => 'alert("test")']);
        $this->assertEquals('<a href="test" onclick="alert("test")">', $result);
    }

    public function testLinkEmpty(): void
    {
        $url = new Url('');
        $this->assertEquals('<a>', $url->link());
    }
}
