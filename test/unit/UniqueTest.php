<?php

declare(strict_types=1);

namespace Horde\Url\Test\Unit;

use Horde\Url\Url;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

/**
 * Tests for Horde\Url\Url::unique() method.
 *
 * @license  http://www.horde.org/licenses/lgpl21 LGPL 2.1
 * @package  Url
 */
#[CoversClass(Url::class)]
class UniqueTest extends TestCase
{
    public function testUniqueAddsParameter(): void
    {
        $url = new Url('http://example.com/style.css');
        $url->unique();

        $result = (string) $url;

        $this->assertStringContainsString('?u=', $result);
        $this->assertStringStartsWith('http://example.com/style.css?u=', $result);
    }

    public function testUniqueAddsToExistingParameters(): void
    {
        $url = new Url('http://example.com/api?action=list');
        $url->unique();

        $result = (string) $url;

        $this->assertStringContainsString('action=list', $result);
        $this->assertStringContainsString('&amp;u=', $result);
    }

    public function testUniqueValuesAreDifferent(): void
    {
        $url1 = new Url('http://example.com/file.js');
        $url1->unique();

        $url2 = new Url('http://example.com/file.js');
        $url2->unique();

        $result1 = (string) $url1;
        $result2 = (string) $url2;

        $this->assertNotEquals($result1, $result2, 'unique() should generate different values');
    }

    public function testUniqueSupportsChaining(): void
    {
        $url = new Url('http://example.com/page');

        $result = $url->unique()->setAnchor('section');

        $this->assertInstanceOf(Url::class, $result);
        $this->assertStringContainsString('?u=', (string) $url);
        $this->assertStringContainsString('#section', (string) $url);
    }

    public function testUniqueWithRawMode(): void
    {
        $url = new Url('http://example.com/file?foo=1', true);
        $url->unique();

        $result = (string) $url;

        // In raw mode, should use & not &amp;
        $this->assertStringContainsString('&u=', $result);
        $this->assertStringNotContainsString('&amp;', $result);
    }
}
