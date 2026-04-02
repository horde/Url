<?php

declare(strict_types=1);

namespace Horde\Url\Test\Unit;

use Horde\Url\Url;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

/**
 * Tests for Horde\Url\Url::setScheme() method.
 *
 * @license  http://www.horde.org/licenses/lgpl21 LGPL 2.1
 * @package  Url
 */
#[CoversClass(Url::class)]
class SetSchemeTest extends TestCase
{
    public function testAddSchemeToSchemelessUrl(): void
    {
        $url = new Url('example.com/path');
        $url->setScheme('https');

        $this->assertEquals('https://example.com/path', (string) $url);
    }

    public function testAddDefaultHttpScheme(): void
    {
        $url = new Url('example.com/path');
        $url->setScheme();

        $this->assertEquals('http://example.com/path', (string) $url);
    }

    public function testDontReplaceExistingSchemeByDefault(): void
    {
        $url = new Url('http://example.com/path');
        $url->setScheme('https');

        // Should still be http (replace=false by default)
        $this->assertEquals('http://example.com/path', (string) $url);
    }

    public function testReplaceExistingSchemeWhenRequested(): void
    {
        $url = new Url('http://example.com/path');
        $url->setScheme('https', true);

        $this->assertEquals('https://example.com/path', (string) $url);
    }

    public function testReplaceSchemePreservesPath(): void
    {
        $url = new Url('ftp://example.com/files/document.pdf');
        $url->setScheme('https', true);

        $this->assertEquals('https://example.com/files/document.pdf', (string) $url);
    }

    public function testReplaceSchemePreservesQueryParameters(): void
    {
        $url = new Url('http://example.com/search?q=test&page=2');
        $url->setScheme('https', true);

        $this->assertEquals('https://example.com/search?q=test&page=2', (string) $url);
    }

    public function testSetSchemeSupportsChaining(): void
    {
        $url = new Url('example.com/path');

        $result = $url->setScheme('https')->add('foo', 'bar');

        $this->assertInstanceOf(Url::class, $result);
        $this->assertEquals('https://example.com/path?foo=bar', (string) $url);
    }

    public function testSetSchemeWithPort(): void
    {
        $url = new Url('example.com:8080/path');
        $url->setScheme('https');

        $this->assertEquals('https://example.com:8080/path', (string) $url);
    }

    public function testReplaceCustomScheme(): void
    {
        $url = new Url('ftp://example.com/file.txt');
        $url->setScheme('sftp', true);

        $this->assertEquals('sftp://example.com/file.txt', (string) $url);
    }

    public function testSetSchemeWithMixedCaseExisting(): void
    {
        $url = new Url('HTTP://example.com/path');
        $url->setScheme('https', true);

        // setScheme uses stripos, so it should find and replace
        $this->assertEquals('https://example.com/path', (string) $url);
    }
}
