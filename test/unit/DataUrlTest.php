<?php

declare(strict_types=1);

namespace Horde\Url\Test\Unit;

use Horde\Url\DataUrl;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

/**
 * Tests for Horde\Url\DataUrl.
 *
 * @license  http://www.horde.org/licenses/lgpl21 LGPL 2.1
 * @package  Url
 */
#[CoversClass(DataUrl::class)]
class DataUrlTest extends TestCase
{
    public function testCreateSimple(): void
    {
        $data = DataUrl::create('text/plain', 'test');
        $this->assertEquals('text/plain', $data->type);
        $this->assertEquals('test', $data->data);
        $this->assertTrue($data->base64);
    }

    public function testCreateNoBase64(): void
    {
        $data = DataUrl::create('text/plain', 'test', false);
        $this->assertFalse($data->base64);
    }

    public function testToStringBase64(): void
    {
        $data = DataUrl::create('text/plain', 'test', true);
        $this->assertEquals('data:text/plain;base64,' . base64_encode('test'), (string)$data);
    }

    public function testToStringRaw(): void
    {
        $data = DataUrl::create('text/plain', 'test', false);
        $this->assertEquals('data:text/plain,test', (string)$data);
    }

    public function testIsDataString(): void
    {
        $this->assertTrue(DataUrl::isData('data:text/plain,test'));
        $this->assertFalse(DataUrl::isData('http://example.com'));
    }

    public function testIsDataObject(): void
    {
        $data = DataUrl::create('text/plain', 'test');
        $this->assertTrue(DataUrl::isData($data));
    }

    public function testConstructorParsing(): void
    {
        // This test would require actual stream wrapper support
        $data = new DataUrl();
        $this->assertEquals('application/octet-stream', $data->type);
        $this->assertEquals('', $data->data);
    }
}
