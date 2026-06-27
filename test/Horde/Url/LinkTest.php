<?php

/**
 * @author     Jan Schneider <jan@horde.org>
 * @license    http://www.horde.org/licenses/lgpl21 LGPL 2.1
 * @category   Horde
 * @package    Url
 * @subpackage UnitTests
 */

namespace Horde\Url;

use PHPUnit\Framework\TestCase;
use Horde_Url;

/**
 * @coversNothing
 */
class LinkTest extends TestCase
{
    public function testLink()
    {
        $url = new Horde_Url('test', true);
        $url->add(['foo' => 1, 'bar' => 2]);
        $this->assertEquals('test?foo=1&bar=2', (string) $url);
        $this->assertEquals('<a href="test?foo=1&amp;bar=2">', $url->link());
        $this->assertEquals('<a href="test?foo=1&amp;bar=2" title="foo&amp;bar">', $url->link(['title' => 'foo&bar']));
        $this->assertEquals('<a href="test?foo=1&amp;bar=2" title="foo&bar">', $url->link(['title.raw' => 'foo&bar']));
    }
}
