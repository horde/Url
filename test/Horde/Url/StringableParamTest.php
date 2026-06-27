<?php

/**
 * @author     Torben Dannhauer / GPT-5
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
class StringableParamTest extends TestCase
{
    private function getStringableObject($value)
    {
        return new class ($value) {
            private $value;
            public function __construct($value)
            {
                $this->value = $value;
            }
            public function __toString()
            {
                return (string) $this->value;
            }
        };
    }

    public function testStringableObjectAsParamValue()
    {
        $this->markTestIncomplete('Generalized normalization for all stringable objects may be implemented later.');
        $url = new Horde_Url('test');
        $url->add('s', $this->getStringableObject('a&b'));
        // Current implementation only normalizes Horde_Url instances; generic stringables are not cast explicitly.
        // Keep test incomplete until generalized handling is agreed upon.
    }
}
