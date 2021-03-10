<?php
/**
 * @author     Michael Slusarz <slusarz@horde.org>
 * @category   Horde
 * @license    http://www.horde.org/licenses/lgpl21 LGPL 2.1
 * @package    Url
 * @subpackage UnitTests
 */
namespace Horde\Url;
use \PHPUnit\Framework\TestCase;
use \Horde_Url;

class ConstructorTest extends TestCase
{
    public function testCopyAllParamsFromOriginal()
    {
        $orig = new Horde_Url('http://example.com/foo');
        $orig->toStringCallback = array($this, 'stringCallback');

        $copy = new Horde_Url($orig);

        $this->assertEquals(
            'bar',
            strval($copy)
        );
    }

    public function stringCallback()
    {
        return 'bar';
    }

}
