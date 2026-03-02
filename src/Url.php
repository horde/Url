<?php

declare(strict_types=1);

namespace Horde\Url;

use Stringable;

/**
 * The Horde\Url class represents a single URL and provides methods for
 * manipulating URLs.
 *
 * Copyright 2009-2026 Horde LLC (http://www.horde.org/)
 *
 * See the enclosed file LICENSE for license information (LGPL). If you
 * did not receive this file, see http://www.horde.org/licenses/lgpl21.
 *
 * @author   Jan Schneider <jan@horde.org>
 * @author   Michael Slusarz <slusarz@horde.org>
 * @license  http://www.horde.org/licenses/lgpl21 LGPL 2.1
 * @package  Url
 */
class Url implements Stringable
{
    /**
     * The anchor string (a/k/a fragment).
     */
    public string $anchor = '';

    /**
     * Any PATH_INFO to be added to the URL.
     */
    public string $pathInfo = '';

    /**
     * The query parameters.
     *
     * The keys are parameter names, the values parameter values. Array values
     * will be added to the URL using name[]=value notation.
     */
    public array $parameters = [];

    /**
     * Whether to output the URL in the raw URL format or HTML-encoded.
     */
    public ?bool $raw = null;

    /**
     * A callback function to use when converting to a string.
     *
     * @var callable|null
     */
    public mixed $toStringCallback = null;

    /**
     * The basic URL, without query parameters.
     */
    public string $url = '';

    /**
     * Constructor.
     *
     * @param string|self|Stringable $url  The basic URL, with or without query parameters.
     * @param bool|null $raw               Whether to output the URL in the raw URL format or HTML-encoded.
     */
    public function __construct(string|self|Stringable $url = '', ?bool $raw = null)
    {
        if ($url instanceof self) {
            foreach (get_object_vars($url) as $k => $v) {
                $this->$k = $v;
            }
            if ($raw !== null) {
                $this->raw = $raw;
            }
            return;
        }

        $url = (string) $url;

        if (($pos = strrpos($url, '#')) !== false) {
            $this->anchor = urldecode(substr($url, $pos + 1));
            $url = substr($url, 0, $pos);
        }

        if (($pos = strrpos($url, '?')) !== false) {
            $query = substr($url, $pos + 1);
            $url = substr($url, 0, $pos);

            /* Check if the argument separator has been already
             * htmlentities-ized in the URL. */
            if (strpos($query, '&amp;') !== false) {
                $query = html_entity_decode($query);
                if ($raw === null) {
                    $raw = false;
                }
            } elseif (strpos($query, '&') !== false) {
                if ($raw === null) {
                    $raw = true;
                }
            }
            $pairs = explode('&', $query);
            foreach ($pairs as $pair) {
                $result = explode('=', urldecode($pair), 2);
                $this->add($result[0], $result[1] ?? null);
            }
        }

        $this->url = $url;
        $this->raw = $raw;
    }

    /**
     * Returns a clone of this object. Useful for chaining.
     *
     * @return self  A clone of this object.
     */
    public function copy(): self
    {
        return clone $this;
    }

    /**
     * Adds one or more query parameters.
     *
     * @param mixed $parameters  Either the name value or an array of name/value pairs.
     * @param mixed $value       If specified, the value part ($parameters is then assumed to just be the parameter name).
     *
     * @return self  This (modified) object, to allow chaining.
     */
    public function add(mixed $parameters, mixed $value = null): self
    {
        if (!is_array($parameters)) {
            $parameters = [$parameters => $value];
        }

        foreach ($parameters as $parameter => $value) {
            if (substr((string) $parameter, -2) === '[]') {
                $parameter = substr((string) $parameter, 0, -2);
                if (!isset($this->parameters[$parameter])) {
                    $this->parameters[$parameter] = [];
                }
                $this->parameters[$parameter][] = $value;
            } else {
                $this->parameters[$parameter] = $value;
            }
        }

        return $this;
    }

    /**
     * Removes one or more parameters.
     *
     * @param string|string[] $parameters  Either a single parameter to remove or an array of parameters to remove.
     *
     * @return self  This (modified) object, to allow chaining.
     */
    public function remove(string|array $parameters): self
    {
        if (!is_array($parameters)) {
            $parameters = [$parameters];
        }

        foreach ($parameters as $parameter) {
            unset($this->parameters[$parameter]);
        }

        return $this;
    }

    /**
     * Sets the URL anchor.
     *
     * @param string $anchor  An anchor to add.
     *
     * @return self  This (modified) object, to allow chaining.
     */
    public function setAnchor(string $anchor): self
    {
        $this->anchor = $anchor;
        return $this;
    }

    /**
     * Sets the $raw value. This call can be chained.
     *
     * @param bool $raw  Whether to output the URL in the raw URL format or HTML-encoded.
     *
     * @return self  This object, to allow chaining.
     */
    public function setRaw(bool $raw): self
    {
        $this->raw = $raw;
        return $this;
    }

    /**
     * Sets the URL scheme.
     *
     * @param string $scheme   The URL scheme.
     * @param bool $replace    Force using $scheme, even if it already exists?
     *
     * @return self  This object, to allow chaining.
     */
    public function setScheme(string $scheme = 'http', bool $replace = false): self
    {
        $pos = stripos($this->url, '://');
        if ($pos === false) {
            $this->url = $scheme . '://' . $this->url;
        } elseif ($replace) {
            $this->url = substr_replace($this->url, $scheme . '://', 0, $pos);
        }
        return $this;
    }

    /**
     * Creates the full URL string.
     *
     * @param bool $raw   Whether to output the URL in the raw URL format or HTML-encoded.
     * @param bool $full  Output the full URL?
     *
     * @return string  The string representation of this object.
     */
    public function toString(bool $raw = false, bool $full = true): string
    {
        if ($this->toStringCallback) {
            $callback = $this->toStringCallback;
            $this->toStringCallback = null;
            $ret = call_user_func($callback, $this);
            $this->toStringCallback = $callback;
            return $ret;
        }

        $url = $full
            ? $this->url
            : parse_url($this->url, PHP_URL_PATH);

        if (strlen($this->pathInfo)) {
            $url = rtrim($url, '/') . '/';
            if ($raw) {
                $url .= $this->pathInfo;
            } else {
                $url .= implode('/', array_map('rawurlencode', explode('/', $this->pathInfo)));
            }
        }

        if ($source = $this->parameters) {
            $params = [];
            self::encodeParameters($source, '', $params);
            $url .= '?' . implode($raw ? '&' : '&amp;', $params);
        }

        if ($this->anchor) {
            $url .= '#' . ($raw ? $this->anchor : rawurlencode($this->anchor));
        }

        return $url;
    }

    /**
     * Encode URL parameters recursively.
     *
     * @param array $source   The parameter array to encode.
     * @param string $prefix  The prefix for nested parameters.
     * @param array $params   The output array (passed by reference).
     */
    protected static function encodeParameters(array $source, string $prefix, array &$params): void
    {
        $index = 0;

        foreach ($source as $p => $v) {
            if (strlen($prefix)) {
                if ($index >= 0 && $p !== $index) {
                    $index = -1;
                }
                if ($index >= 0) {
                    $p = '';
                    ++$index;
                } else {
                    $p = rawurlencode((string) $p);
                }
                $p = $prefix . '[' . $p . ']';
            } else {
                $p = rawurlencode((string) $p);
            }

            if (is_array($v)) {
                self::encodeParameters($v, $p, $params);
            } else {
                $v = (string) $v;
                if (strlen($v)) {
                    $p .= '=' . rawurlencode($v);
                }
                $params[] = $p;
            }
        }
    }

    /**
     * Creates the full URL string.
     *
     * @return string  The string representation of this object.
     */
    public function __toString(): string
    {
        return $this->toString((bool) $this->raw);
    }

    /**
     * Generates a HTML link tag out of this URL.
     *
     * @param array $attributes  A hash with any additional attributes to be added to the link.
     *                           If the attribute name is suffixed with ".raw", the attribute value
     *                           won't be HTML-encoded.
     *
     * @return string  An <a> tag representing this URL.
     */
    public function link(array $attributes = []): string
    {
        $url = strval($this->setRaw(false));
        $link = '<a';
        if (!empty($url)) {
            $link .= " href=\"$url\"";
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
     * Add a unique parameter to the URL to aid in cache-busting.
     *
     * @return self  This (modified) object, to allow chaining.
     */
    public function unique(): self
    {
        return $this->add('u', uniqid((string) mt_rand()));
    }

    /**
     * Sends a redirect request to the browser to the URL in this object.
     *
     * @throws UrlException
     */
    public function redirect(): never
    {
        $url = strval($this->setRaw(true));
        if (!strlen($url)) {
            throw new UrlException('Redirect failed: URL is empty.');
        }

        header('Location: ' . $url);
        exit;
    }

    /**
     * URL-safe base64 encoding, with trimmed '='.
     *
     * @param string $string  String to encode.
     *
     * @return string  URL-safe, base64 encoded data.
     */
    public static function uriB64Encode(string $string): string
    {
        return str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($string));
    }

    /**
     * Decode URL-safe base64 data, dealing with missing '='.
     *
     * @param string $string  Encoded data.
     *
     * @return string  Decoded data.
     */
    public static function uriB64Decode(string $string): string
    {
        $data = str_replace(['-', '_'], ['+', '/'], $string);
        $mod4 = strlen($data) % 4;
        if ($mod4) {
            $data .= substr('====', $mod4);
        }
        return base64_decode($data);
    }
}
