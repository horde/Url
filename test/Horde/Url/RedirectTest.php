<?php
/**
 * @author     Michael Slusarz <slusarz@horde.org>
 * @license    http://www.horde.org/licenses/lgpl21 LGPL 2.1
 * @category   Horde
 * @package    Url
 * @subpackage UnitTests
 */

class Horde_Url_RedirectTest extends Horde_Test_Case
{
    public function testEmptyRedirect()
    {
        $this->expectNotToPerformAssertions();
        $url = new Horde_Url('');

        try {
            $url->redirect();
            $this->fail();
        } catch (Horde_Url_Exception $e) {}
    }

}
