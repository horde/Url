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

class RedirectTest extends TestCase
{
    public function testEmptyRedirect()
    {
        $this->expectException('Horde_Url_Exception');
        $url = new Horde_Url('');
        $url->redirect();
    }
}
