<?php

declare(strict_types=1);

namespace Horde\Url\Test\Unit;

use Horde\Url\UrlException;
use Horde\Url\Url;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

/**
 * Tests for Horde\Url\Url::redirect() method.
 *
 * @license  http://www.horde.org/licenses/lgpl21 LGPL 2.1
 * @package  Url
 */
#[CoversClass(Url::class)]
#[CoversClass(UrlException::class)]
class RedirectTest extends TestCase
{
    public function testEmptyRedirect(): void
    {
        $this->expectException(UrlException::class);
        $url = new Url('');
        $url->redirect();
    }
}
