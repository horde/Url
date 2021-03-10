<?php
/**
 * @author     Michael Slusarz <slusarz@horde.org>
 * @license    http://www.horde.org/licenses/lgpl21 LGPL 2.1
 * @category   Horde
 * @package    Url
 * @subpackage UnitTests
 */
namespace Horde\Url;
use \PHPUnit\Framework\TestCase;
use \Horde_Url;

class CallbackTest extends TestCase
{
    public function testRemoveRaw()
    {
        $url = new Horde_Url('test?bar=2');
        $url->toStringCallback = array($this, 'callbackToString');
        $this->assertEquals('FOOtest?bar=2BAR', (string)$url);
    }

    public function callbackToString($url)
    {
        return 'FOO' . (string)$url . 'BAR';
    }

}
