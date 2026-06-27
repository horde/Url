<?php

/**
 * PSR-0 backward compatibility wrapper for Horde\Url\Url.
 *
 * This class provides backward compatibility for legacy code that:
 * - Uses loose type hints (no type declarations)
 * - Extends Horde_Url (like Horde_Core_Smartmobile_Url)
 * - Directly accesses public properties (anchor, pathInfo, parameters, etc.)
 * - Sets properties before calling parent::__construct()
 *
 * Implementation uses composition (wrapping) instead of inheritance to avoid
 * method signature conflicts with PHP 8.2+ strict typing requirements.
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
     * @var Horde\Url\Url|null
     */
    protected $_modern;

    /**
     * Buffer for properties set before _modern is initialized.
     *
     * @var array
     */
    private $_propertyBuffer = [];

    /**
     * Constructor.
     *
     * @param string|Horde_Url|Horde\Url\Url $url  The basic URL.
     * @param mixed $raw  Whether to output URL in raw format or HTML-encoded.
     */
    public function __construct($url = '', $raw = null)
    {
        // Handle Horde_Url or \Horde\Url\Url being passed in
        if ($url instanceof Horde\Url\Url) {
            $this->_modern = clone $url;
            if ($raw !== null) {
                $this->_modern->raw = (bool) $raw;
            }
        } elseif ($url instanceof self) {
            $this->_modern = clone $url->_modern;
            if ($raw !== null) {
                $this->_modern->raw = (bool) $raw;
            }
        } else {
            $this->_modern = new Horde\Url\Url((string) $url, $raw !== null ? (bool) $raw : null);
        }

        // Apply any buffered property sets
        foreach ($this->_propertyBuffer as $name => $value) {
            $this->_modern->$name = $value;
        }
        $this->_propertyBuffer = [];
    }

    /**
     * Magic getter for public properties.
     *
     * Delegates to the wrapped modern instance.
     *
     * @param string $name Property name
     * @return mixed Property value
     */
    public function __get($name)
    {
        if ($this->_modern === null) {
            return $this->_propertyBuffer[$name] ?? null;
        }
        return $this->_modern->$name;
    }

    /**
     * Magic setter for public properties.
     *
     * Delegates to the wrapped modern instance.
     *
     * @param string $name Property name
     * @param mixed $value Property value
     */
    public function __set($name, $value)
    {
        if ($this->_modern === null) {
            // Buffer property sets that happen before __construct() is called
            $this->_propertyBuffer[$name] = $value;
            return;
        }
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
        if ($this->_modern === null) {
            return isset($this->_propertyBuffer[$name]);
        }
        return isset($this->_modern->$name);
    }

    /**
     * Magic clone method to ensure deep cloning of the wrapped modern instance.
     *
     * @return void
     */
    public function __clone()
    {
        if ($this->_modern !== null) {
            $this->_modern = clone $this->_modern;
        }
    }

    /**
     * Returns a clone of this object. Useful for chaining.
     *
     * @return self  A clone of this object.
     */
    public function copy()
    {
        $copy = new self();
        $copy->_modern = $this->_modern->copy();
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
        $this->_modern->setRaw((bool) $raw);
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
        $this->_modern->setScheme($scheme, (bool) $replace);
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
        // Use $this->toString() to respect overridden toString() in subclasses
        $url = $this->toString(false);
        $link = '<a';
        if (!empty($url)) {
            $link .= ' href="' . $url . '"';
        }
        foreach ($attributes as $name => $value) {
            if (!strlen((string) $value)) {
                continue;
            }
            if (substr($name, -4) === '.raw') {
                $link .= ' ' . htmlspecialchars(substr($name, 0, -4))
                    . '="' . $value . '"';
            } else {
                $link .= ' ' . htmlspecialchars($name)
                    . '="' . htmlspecialchars((string) $value) . '"';
            }
        }
        return $link . '>';
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
        // Use $this->toString() to respect overridden toString() in subclasses
        $url = $this->toString(true);  // raw = true for redirect
        if (!strlen($url)) {
            throw new Horde_Url_Exception('Redirect failed: URL is empty.');
        }

        header('Location: ' . $url);
        exit;
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
        return Horde\Url\Url::uriB64Encode($string);
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
        return Horde\Url\Url::uriB64Decode($string);
    }
}
