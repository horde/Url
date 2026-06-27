<?php

declare(strict_types=1);

namespace Horde\Url\Test\Unit;

use Horde\Url\Psr7Bridge;
use Horde\Url\Url;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\UriInterface;
use RuntimeException;

/**
 * Unit tests for Horde\Url\Psr7Bridge using mocks.
 *
 * @license  http://www.horde.org/licenses/lgpl21 LGPL 2.1
 * @package  Url
 */
#[CoversClass(Psr7Bridge::class)]
class Psr7BridgeTest extends TestCase
{
    protected function setUp(): void
    {
        if (!interface_exists('\\Psr\\Http\\Message\\UriInterface')) {
            $this->markTestSkipped('psr/http-message not available - unit tests require PSR-7 interfaces');
        }
    }

    public function testFromPsr7CreatesUrl(): void
    {
        $psr7Uri = $this->createStub(UriInterface::class);
        $psr7Uri->method('__toString')->willReturn('http://example.com/path?foo=bar#section');

        $url = Psr7Bridge::fromPsr7($psr7Uri);

        $this->assertInstanceOf(Url::class, $url);
        $this->assertEquals('http://example.com/path?foo=bar#section', (string) $url);
    }

    public function testFromPsr7WithRawMode(): void
    {
        $psr7Uri = $this->createStub(UriInterface::class);
        $psr7Uri->method('__toString')->willReturn('http://example.com/path');

        $url = Psr7Bridge::fromPsr7($psr7Uri, true);

        $this->assertTrue($url->raw);
    }

    public function testFromPsr7WithHtmlMode(): void
    {
        $psr7Uri = $this->createStub(UriInterface::class);
        $psr7Uri->method('__toString')->willReturn('http://example.com/path');

        $url = Psr7Bridge::fromPsr7($psr7Uri, false);

        $this->assertFalse($url->raw);
    }

    public function testFromPsr7WithDefaultMode(): void
    {
        $psr7Uri = $this->createStub(UriInterface::class);
        $psr7Uri->method('__toString')->willReturn('http://example.com/path');

        $url = Psr7Bridge::fromPsr7($psr7Uri);

        $this->assertNull($url->raw);
    }

    public function testFromPsr7PreservesComplexUrl(): void
    {
        $complexUrl = 'https://user:pass@example.com:8080/path/to/resource?foo=bar&baz=qux#section';
        $psr7Uri = $this->createStub(UriInterface::class);
        $psr7Uri->method('__toString')->willReturn($complexUrl);

        $url = Psr7Bridge::fromPsr7($psr7Uri);

        $this->assertStringContainsString('example.com:8080', (string) $url);
        $this->assertStringContainsString('foo=bar', (string) $url);
        $this->assertEquals('section', $url->anchor);
    }

    public function testToPsr7ThrowsWhenNoPsr7Available(): void
    {
        $url = new Url('http://example.com');

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('PSR-7 implementation not found');

        // Pass a non-existent class to force failure
        Psr7Bridge::toPsr7($url, 'NonExistent\\Psr7\\Uri');
    }

    public function testToPsr7ThrowsWhenSpecifiedClassNotFound(): void
    {
        $url = new Url('http://example.com');

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('PSR-7 implementation not found: Some\\Fake\\Class');

        Psr7Bridge::toPsr7($url, 'Some\\Fake\\Class');
    }
}
