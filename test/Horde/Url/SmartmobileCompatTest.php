<?php

/**
 * Tests that Horde_Url shim works with subclasses like Horde_Core_Smartmobile_Url.
 *
 * @license  http://www.horde.org/licenses/lgpl21 LGPL 2.1
 * @package  Url
 */

use PHPUnit\Framework\TestCase;

/**
 * @coversNothing
 */
class Horde_Url_SmartmobileCompatTest extends TestCase
{
    /**
     * Simulates Horde_Core_Smartmobile_Url extending Horde_Url.
     *
     * Tests that:
     * - Subclass can access parent properties via magic methods
     * - Subclass can override toString with loose signature
     * - Subclass can store its own properties
     */
    public function testSmartmobileUrlPattern(): void
    {
        // Simulate Horde_Core_Smartmobile_Url behavior
        $smartUrl = new class (new Horde_Url('http://example.com/app')) extends Horde_Url {
            protected $_baseUrl;

            public function __construct($url = null, $raw = null)
            {
                if (!($url instanceof Horde_Url)) {
                    throw new InvalidArgumentException('Must pass Horde_Url');
                }

                // Extract anchor if present
                if (strlen($url->anchor)) {
                    $anchor = parse_url($url->anchor);
                    if (isset($anchor['query'])) {
                        $this->anchor = $anchor['path'];
                        $query = '?' . $anchor['query'];
                    } else {
                        $this->anchor = $url->anchor;
                        $query = '';
                    }
                    $url->anchor = '';
                } else {
                    $query = '';
                }

                $this->_baseUrl = $url;
                parent::__construct($query, $raw);
            }

            // Override with loose typing (no type hints)
            public function toString($raw = false, $full = true)
            {
                if (!strlen($this->anchor)) {
                    $baseUrl = $this->_baseUrl->copy();
                    $baseUrl->parameters = array_merge(
                        $baseUrl->parameters,
                        $this->parameters
                    );
                    return $baseUrl->toString($raw, $full);
                }

                $url = $this->_baseUrl->toString($raw, $full);

                if ($this->anchor) {
                    $url .= '#' . ($raw ? $this->anchor : rawurlencode($this->anchor));
                }

                if ($params = $this->parameters) {
                    $url .= '?' . http_build_query($params, '', $raw ? '&' : '&amp;');
                }

                return strval($url);
            }
        };

        // Test basic functionality
        $this->assertStringContainsString('example.com', $smartUrl->toString());

        // Test property access
        $smartUrl->add('foo', 'bar');
        $this->assertStringContainsString('foo=bar', $smartUrl->toString());

        // Test anchor handling
        $smartUrl->anchor = 'section';
        $this->assertStringContainsString('#section', $smartUrl->toString());
    }

    public function testSmartmobileUrlWithAnchorInConstructor(): void
    {
        $baseUrl = new Horde_Url('http://example.com/app');
        $baseUrl->anchor = 'page?param=value';

        $smartUrl = new class ($baseUrl) extends Horde_Url {
            protected $_baseUrl;

            public function __construct($url = null, $raw = null)
            {
                if (!($url instanceof Horde_Url)) {
                    throw new InvalidArgumentException('Must pass Horde_Url');
                }

                $query = '';
                if (strlen($url->anchor)) {
                    $anchor = parse_url($url->anchor);
                    if (isset($anchor['query'])) {
                        $this->anchor = $anchor['path'];
                        $query = '?' . $anchor['query'];
                    } else {
                        $this->anchor = $url->anchor;
                    }
                    $url->anchor = '';
                }

                $this->_baseUrl = $url;
                parent::__construct($query, $raw);
            }

            public function toString($raw = false, $full = true)
            {
                $url = $this->_baseUrl->toString($raw, $full);
                if ($this->anchor) {
                    $url .= '#' . $this->anchor;
                }
                if ($params = $this->parameters) {
                    $url .= '?' . http_build_query($params, '', $raw ? '&' : '&amp;');
                }
                return $url;
            }
        };

        $result = $smartUrl->toString();
        $this->assertStringContainsString('example.com', $result);
        $this->assertStringContainsString('#page', $result);
    }

    public function testSmartmobileUrlLinkGeneration(): void
    {
        $baseUrl = new Horde_Url('http://example.com/app');

        $smartUrl = new class ($baseUrl) extends Horde_Url {
            protected $_baseUrl;

            public function __construct($url = null, $raw = null)
            {
                if (!($url instanceof Horde_Url)) {
                    throw new InvalidArgumentException('Must pass Horde_Url');
                }
                $this->_baseUrl = $url;
                parent::__construct('', $raw);
            }

            public function toString($raw = false, $full = true)
            {
                return $this->_baseUrl->toString($raw, $full);
            }

            // If toString() is overridden, link() should also be overridden
            // to use the custom toString()
            public function link($attributes = [])
            {
                $url = $this->toString(false);  // Use overridden toString()
                $link = '<a';
                if (!empty($url)) {
                    $link .= ' href="' . $url . '"';
                }
                foreach ($attributes as $name => $value) {
                    $link .= ' ' . htmlspecialchars($name) . '="' . htmlspecialchars((string) $value) . '"';
                }
                return $link . '>';
            }
        };

        // Test link() method works with custom implementation
        $link = $smartUrl->link(['data-ajax' => 'false']);
        $this->assertStringContainsString('<a', $link);
        $this->assertStringContainsString('href="http://example.com/app"', $link);
        $this->assertStringContainsString('data-ajax="false"', $link);
    }
}
