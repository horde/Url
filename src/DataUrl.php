<?php

declare(strict_types=1);

namespace Horde\Url;

use Stringable;

/**
 * An object to handle Data URLs (RFC 2397).
 *
 * Copyright 2013-2026 Horde LLC (http://www.horde.org/)
 *
 * See the enclosed file LICENSE for license information (LGPL). If you
 * did not receive this file, see http://www.horde.org/licenses/lgpl21.
 *
 * @author    Michael Slusarz <slusarz@horde.org>
 * @license   http://www.horde.org/licenses/lgpl21 LGPL 2.1
 * @package   Url
 */
class DataUrl implements Stringable
{
    /**
     * Should data be base64 encoded?
     */
    public bool $base64 = true;

    /**
     * Binary data.
     */
    public string $data = '';

    /**
     * The MIME type.
     */
    public string $type = 'application/octet-stream';

    /**
     * Create a new object from existing data.
     *
     * @param string|null $type    The MIME type of the data.
     * @param string|null $data    The data.
     * @param bool $base64         Should data be base64 encoded?
     *
     * @return self  The created DataUrl object.
     */
    public static function create(?string $type = null, ?string $data = null, bool $base64 = true): self
    {
        $ob = new self();

        if ($type !== null) {
            $ob->type = $type;
        }

        if ($data !== null) {
            $ob->data = $data;
        }

        $ob->base64 = $base64;

        return $ob;
    }

    /**
     * Check input to see if it contains RFC 2397 data.
     *
     * @param mixed $input  Input.
     *
     * @return bool  True if the input contains RFC 2397 compliant data.
     */
    public static function isData(mixed $input): bool
    {
        if (is_object($input)) {
            return ($input instanceof self);
        }

        return (is_string($input) && (strpos($input, 'data:') === 0));
    }

    /**
     * Constructor.
     *
     * @param string|null $data  An RFC 2397 compliant data string.
     */
    public function __construct(?string $data = null)
    {
        if ($data !== null
            && self::isData($data)
            && ($fp = @fopen($data, 'r'))) {
            $this->data = stream_get_contents($fp);
            $meta = stream_get_meta_data($fp);
            $this->type = $meta['mediatype'];
            fclose($fp);
        }
    }

    /**
     * Output RFC 2397 compliant data string.
     *
     * @return string  The RFC 2397 data URL.
     */
    public function __toString(): string
    {
        return 'data:' . htmlspecialchars($this->type)
            . ($this->base64
                ? ';base64,' . base64_encode($this->data)
                : ',' . rawurlencode($this->data));
    }
}
