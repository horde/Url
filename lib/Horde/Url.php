<?php

/**
 * PSR-0 backward compatibility wrapper for Horde\Url\Url.
 *
 * This wrapper maintains the legacy mutable API and loose typing.
 * All operations are forwarded to the strict PSR-4 implementation
 * with explicit type casting.
 *
 * Copyright 2009-2026 Horde LLC (http://www.horde.org/)
 *
 * See the enclosed file LICENSE for license information (LGPL). If you
 * did not receive this file, see http://www.horde.org/licenses/lgpl21.
 *
 * @author   Jan Schneider <jan@horde.org>
 * @author   Michael Slusarz <slusarz@horde.org>
 * @license  http://www.horde.org/licenses/lgpl21 LGPL 2.1
 * @deprecated Use Horde\Url\Url instead. Will be removed in Horde 7.
 * @package  Url
 */
class Horde_Url extends \Horde\Url\Url
{
    /**
     * Sends a redirect request to the browser to the URL in this object.
     *
     * Wraps PSR-4 exception with PSR-0 exception for backward compatibility.
     *
     * @throws Horde_Url_Exception
     */
    public function redirect(): never
    {
        try {
            parent::redirect();
        } catch (\Horde\Url\UrlException $e) {
            throw new Horde_Url_Exception($e->getMessage(), $e->getCode(), $e);
        }
    }
}
