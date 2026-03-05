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
class Horde_Url
{
    /**
     * Modern URL instance (does the real work).
     *
     * @var \Horde\Url\Url
     */
    protected $_modern;

    /**
     * Constructor.
     *
     * @param string $url   The basic URL, with or without query parameters.
     * @param bool|null $raw  Whether to output the URL in the raw URL format or HTML-encoded.
     */
    public function __construct($url = '', $raw = null)
    {
        $this->_modern = new \Horde\Url\Url($url, $raw);
    }

    /**
     * Magic getter for public properties.
     *
     * @param string $name Property name
     * @return mixed Property value
     */
    public function __get($name)
    {
        return $this->_modern->$name;
    }

    /**
     * Magic setter for public properties.
     *
     * @param string $name Property name
     * @param mixed $value Property value
     */
    public function __set($name, $value)
    {
        $this->_modern->$name = $value;
    }

    /**
     * Magic isset for public properties.
     *
     * @param string $name Property name
     * @return bool
     */
    public function __isset($name)
    {
        return isset($this->_modern->$name);
    }

    /**
     * Returns a clone of this object. Useful for chaining.
     *
     * @return self  A clone of this object.
     */
    public function copy()
    {
        $copy = new self();
        $copy->_modern = clone $this->_modern;
        return $copy;
    }

    /**
     * Adds one or more query parameters.
     *
     * @param mixed $parameters  Either the name value or an array of name/value pairs.
     * @param mixed $value       If specified, the value part.
     *
     * @return self  This (modified) object, to allow chaining.
     */
    public function add($parameters, $value = null)
    {
        $this->_modern->add($parameters, $value);
        return $this;
    }

    /**
     * Removes one or more parameters.
     *
     * @param string|array $parameters  Parameter(s) to remove.
     *
     * @return self  This (modified) object, to allow chaining.
     */
    public function remove($parameters)
    {
        $this->_modern->remove($parameters);
        return $this;
    }

    /**
     * Sets the URL anchor.
     *
     * @param string $anchor  An anchor to add.
     *
     * @return self  This (modified) object, to allow chaining.
     */
    public function setAnchor($anchor)
    {
        $this->_modern->setAnchor($anchor);
        return $this;
    }

    /**
     * Sets the $raw value.
     *
     * @param bool $raw  Whether to output raw or HTML-encoded.
     *
     * @return self  This object, to allow chaining.
     */
    public function setRaw($raw)
    {
        $this->_modern->setRaw($raw);
        return $this;
    }

    /**
     * Sets the URL scheme.
     *
     * @param string $scheme   The URL scheme.
     * @param bool $replace    Force using $scheme?
     *
     * @return self  This object, to allow chaining.
     */
    public function setScheme($scheme = 'http', $replace = false)
    {
        $this->_modern->setScheme($scheme, $replace);
        return $this;
    }

    /**
     * Creates the full URL string.
     *
     * Maintains loose typing for backward compatibility.
     *
     * @param mixed $raw   Whether to output raw or HTML-encoded.
     * @param mixed $full  Output the full URL?
     *
     * @return string  The string representation of this object.
     */
    public function toString($raw = false, $full = true)
    {
        return $this->_modern->toString((bool) $raw, (bool) $full);
    }

    /**
     * Creates the full URL string.
     *
     * @return string  The string representation of this object.
     */
    public function __toString()
    {
        return (string) $this->_modern;
    }

    /**
     * Generates a HTML link tag out of this URL.
     *
     * @param array $attributes  Additional attributes.
     *
     * @return string  An <a> tag.
     */
    public function link($attributes = [])
    {
        return $this->_modern->link($attributes);
    }

    /**
     * Add a unique parameter to the URL.
     *
     * @return self  This (modified) object, to allow chaining.
     */
    public function unique()
    {
        $this->_modern->unique();
        return $this;
    }

    /**
     * Sends a redirect request to the browser.
     *
     * @throws Horde_Url_Exception
     */
    public function redirect()
    {
        try {
            $this->_modern->redirect();
        } catch (\Horde\Url\UrlException $e) {
            throw new Horde_Url_Exception($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * URL-safe base64 encoding.
     *
     * @param string $string  String to encode.
     *
     * @return string  Encoded data.
     */
    public static function uriB64Encode($string)
    {
        return \Horde\Url\Url::uriB64Encode($string);
    }

    /**
     * Decode URL-safe base64 data.
     *
     * @param string $string  Encoded data.
     *
     * @return string  Decoded data.
     */
    public static function uriB64Decode($string)
    {
        return \Horde\Url\Url::uriB64Decode($string);
    }
}
