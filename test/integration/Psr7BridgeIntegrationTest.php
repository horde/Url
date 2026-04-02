<?php

declare(strict_types=1);

namespace Horde\Url\Test\Integration;

use Horde\Url\Psr7Bridge;
use Horde\Url\Url;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

/**
 * Integration tests for Horde\Url\Psr7Bridge with Horde\Http.
 *
 * These tests require horde/http to be installed.
 * Guarded by class_exists check - will be skipped if not available.
 *
 * @license  http://www.horde.org/licenses/lgpl21 LGPL 2.1
 * @package  Url
 */
#[CoversClass(Psr7Bridge::class)]
class Psr7BridgeIntegrationTest extends TestCase
{
    protected function setUp(): void
    {
        if (!class_exists('\\Horde\\Http\\Uri')) {
            $this->markTestSkipped('horde/http not available - integration tests skipped');
        }
    }

    public function testToPsr7WithHordeHttp(): void
    {
        $url = new Url('http://example.com/path?foo=bar#section');

        $psr7Uri = Psr7Bridge::toPsr7($url);

        $this->assertInstanceOf('\\Psr\\Http\\Message\\UriInterface', $psr7Uri);
        $this->assertEquals('http://example.com/path?foo=bar#section', (string) $psr7Uri);
    }

    public function testToPsr7AutoDetectsHordeHttp(): void
    {
        $url = new Url('https://example.com:8080/api/v1/users');

        // Don't specify factory class - should auto-detect
        $psr7Uri = Psr7Bridge::toPsr7($url);

        $this->assertInstanceOf('\\Horde\\Http\\Uri', $psr7Uri);
        $this->assertEquals('https', $psr7Uri->getScheme());
        $this->assertEquals('example.com', $psr7Uri->getHost());
        $this->assertEquals(8080, $psr7Uri->getPort());
        $this->assertEquals('/api/v1/users', $psr7Uri->getPath());
    }

    public function testToPsr7ExplicitHordeHttp(): void
    {
        $url = new Url('http://example.com/test');

        $psr7Uri = Psr7Bridge::toPsr7($url, '\\Horde\\Http\\Uri');

        $this->assertInstanceOf('\\Horde\\Http\\Uri', $psr7Uri);
        $this->assertEquals('http://example.com/test', (string) $psr7Uri);
    }

    public function testRoundTripConversion(): void
    {
        $original = new Url('https://user:pass@example.com:443/path?foo=bar&baz=qux#anchor');

        $psr7Uri = Psr7Bridge::toPsr7($original);
        $converted = Psr7Bridge::fromPsr7($psr7Uri);

        // Should preserve all components
        $this->assertStringContainsString('example.com', (string) $converted);
        $this->assertStringContainsString('foo=bar', (string) $converted);
        $this->assertStringContainsString('baz=qux', (string) $converted);
        $this->assertEquals('anchor', $converted->anchor);
    }

    public function testToPsr7PreservesQueryParameters(): void
    {
        $url = new Url('http://example.com/search');
        $url->add(['q' => 'test query', 'page' => 2, 'filters' => ['a', 'b']]);

        $psr7Uri = Psr7Bridge::toPsr7($url);

        $query = $psr7Uri->getQuery();
        $this->assertStringContainsString('q=', $query);
        $this->assertStringContainsString('page=', $query);
        $this->assertStringContainsString('filters', $query);
    }

    public function testToPsr7PreservesFragment(): void
    {
        $url = new Url('http://example.com/page');
        $url->setAnchor('section-2');

        $psr7Uri = Psr7Bridge::toPsr7($url);

        $this->assertEquals('section-2', $psr7Uri->getFragment());
    }

    public function testFromPsr7WithHordeHttpUri(): void
    {
        $psr7Uri = new \Horde\Http\Uri('http://example.com/api/endpoint?key=value');

        $url = Psr7Bridge::fromPsr7($psr7Uri);

        $this->assertInstanceOf(Url::class, $url);
        $this->assertStringContainsString('example.com', (string) $url);
        $this->assertStringContainsString('key=value', (string) $url);
    }

    public function testComplexUrlRoundTrip(): void
    {
        $url = new Url('https://api.example.com:8443/v2/resources');
        $url->add([
            'filter' => 'active',
            'sort' => 'name',
            'fields' => ['id', 'name', 'created']
        ]);
        $url->setAnchor('results');

        $psr7Uri = Psr7Bridge::toPsr7($url);
        $restored = Psr7Bridge::fromPsr7($psr7Uri, true);

        // Verify key components preserved
        $this->assertStringContainsString('api.example.com:8443', (string) $restored);
        $this->assertStringContainsString('filter=active', (string) $restored);
        $this->assertStringContainsString('sort=name', (string) $restored);
        $this->assertEquals('results', $restored->anchor);
        $this->assertTrue($restored->raw);
    }
}
