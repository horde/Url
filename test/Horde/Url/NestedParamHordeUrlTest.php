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

class NestedParamHordeUrlTest extends TestCase
{
	public function testNestedArrayWithHordeUrlIsCurrentlyNotConvertedRecursively()
	{
		$url = new Horde_Url('test');
		$url->add('outer', [
			'inner' => new Horde_Url('https://example.com/test?_t=1&_h=2')
		]);
		// Current behavior: Only top-level values are normalized by PR #2.
		// Nested arrays containing Horde_Url will still be expanded by http_build_query.
		// This test documents current behavior and should be adapted once recursion is implemented.
		$this->assertStringContainsString('outer%5Binner%5D%5Bparameters%5D', (string)$url);
		$this->markTestIncomplete('Recursive normalization of nested Horde_Url parameters is not implemented yet.');
	}
}


