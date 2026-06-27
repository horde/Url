<?php

/**
 * Test case reproducing the horde/Core SmartmobileUrl failure
 *
 * This test ensures that when extending Horde_Url (as SmartmobileUrl does),
 * the base URL is properly preserved when using clone vs copy().
 *
 * @author     Ralf Lang <ralf.lang@ralf-lang.de>
 * @license    http://www.horde.org/licenses/lgpl21 LGPL 2.1
 * @category   Horde
 * @package    Url
 * @subpackage UnitTests
 */

namespace Horde\Url;

use PHPUnit\Framework\TestCase;
use Horde_Url;
use InvalidArgumentException;

/**
 * Test extending Horde_Url (similar to SmartmobileUrl in Core)
 * @coversNothing
 */
class ExtendedUrlTest extends TestCase
{
    public function testBaseUrlWithParametersUsingClone()
    {
        // Reproduce the SmartmobileUrl pattern using clone (the fix)
        $base = new Horde_Url('test');
        $base->add('foo', 0);

        $extended = new ExtendedUrlMockWithClone($base);
        $extended->add(['foo' => 1, 'bar' => 2]);

        // Should preserve base URL 'test' with clone
        $this->assertEquals('test?foo=1&amp;bar=2', (string) $extended);
    }

    public function testBaseUrlPreservationWithClone()
    {
        $base = new Horde_Url('test');
        $extended = new ExtendedUrlMockWithClone($base);
        $extended->add('foo', 'bar');

        // Base URL should be preserved with clone
        $this->assertStringStartsWith('test?', (string) $extended);
    }

    public function testBaseUrlWithAnchor()
    {
        // Test that anchor handling preserves base URL
        $base = new Horde_Url('test');
        $base->add('foo', 0);

        $extended = new ExtendedUrlMockWithClone($base);
        $extended->add(['foo' => 1, 'bar' => 2]);
        $extended->setAnchor('anchor');

        // Should have base URL + anchor + params
        $this->assertStringContainsString('test', (string) $extended);
        $this->assertStringContainsString('#anchor', (string) $extended);
    }
}

/**
 * Mock class that extends Horde_Url similar to SmartmobileUrl
 * Uses clone (the fix) instead of copy()
 */
class ExtendedUrlMockWithClone extends Horde_Url
{
    protected $_baseUrl;
    protected $_anchor = '';

    public function __construct($url = null, $raw = null)
    {
        if (is_null($url)) {
            $url = new Horde_Url();
        }
        if (!($url instanceof Horde_Url)) {
            throw new InvalidArgumentException('Must be Horde_Url');
        }

        $this->_baseUrl = $url;
        parent::__construct('', $raw);
    }

    public function setAnchor($anchor)
    {
        $this->_anchor = $anchor;
        return $this;
    }

    public function toString($raw = false, $full = true)
    {
        if (!strlen($this->_anchor)) {
            // Use clone (the fix) - this should preserve base URL
            $baseUrl = clone $this->_baseUrl;
            $baseUrl->parameters = array_merge(
                $baseUrl->parameters,
                $this->parameters
            );
            return $baseUrl->toString($raw, $full);
        }

        // With anchor: build custom URL like SmartmobileUrl does
        $url = $this->_baseUrl->toString($raw, $full);
        if ($this->_anchor) {
            $url .= '#' . ($raw ? $this->_anchor : rawurlencode($this->_anchor));
        }
        if ($this->parameters) {
            $url .= '?' . http_build_query($this->parameters, '', $raw ? '&' : '&amp;');
        }
        return strval($url);
    }
}
