<?php
/**
 * @author     Dmitry Petrov <dpetrov67@gmail.com>
 * @license    http://www.horde.org/licenses/lgpl21 LGPL 2.1
 * @category   Horde
 * @package    Url
 * @subpackage UnitTests
 */

namespace Horde\Url;
use \PHPUnit\Framework\TestCase;
use \Horde_Url;

class AddHordeUrlTest extends TestCase
{
    public function testAddHordeUrlToExistingUrl()
    {
        $url = new Horde_Url('test?foo=1&bar=2');
        $url->add('url', new Horde_Url('https://example.com/test?_t=123456&_h=Abcd123'));
        $this->assertEquals('test?foo=1&bar=2&url=https%3A%2F%2Fexample.com%2Ftest%3F_t%3D123456%26_h%3DAbcd123', (string)$url);
    }

}
