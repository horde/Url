<?php
/**
 * @author     Torben Dannhauer / GPT-5
 * @license    http://www.horde.org/licenses/lgpl21 LGPL 2.1
 * @category   Horde
 * @package    Url
 * @subpackage UnitTests
 */

namespace Horde\Url;
use \PHPUnit\Framework\TestCase;
use \Horde_Url;

class HordeUrlParamRawModeTest extends TestCase
{
	public function testHordeUrlParamEscapedAndRaw()
	{
		$url = new Horde_Url('test');
		$url->add('url', new Horde_Url('https://example.com/test?_t=123456&_h=Abcd123'));
		$this->assertEquals(
			'test?url=https%3A%2F%2Fexample.com%2Ftest%3F_t%3D123456%26_h%3DAbcd123',
			(string)$url
		);

		// Raw output should not HTML-escape the ampersands
		$url->setRaw(true);
		$this->assertEquals(
			'test?url=https%3A%2F%2Fexample.com%2Ftest%3F_t%3D123456%26_h%3DAbcd123',
			(string)$url
		);
	}
}


