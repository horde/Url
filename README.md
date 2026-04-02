# Horde_Url

URL manipulation library for PHP 8.1+
Includes bridge from/to PSR-7 native URI objects and legacy Horde internal URL handling.

## Installation

```bash
composer require horde/url
```

## Quick Start

```php
use Horde\Url\Url;

// Create URL
$url = new Url('https://example.com/page');

// Add parameters
$url->add(['foo' => 'bar', 'id' => 42]);
// Result: https://example.com/page?foo=bar&amp;id=42

// Set anchor
$url->setAnchor('section');
// Result: https://example.com/page?foo=bar&amp;id=42#section

// Generate HTML link
echo $url->link(['class' => 'button']);
// Output: <a href="https://example.com/page?foo=bar&amp;id=42#section" class="button">
```

## Features

- **Fluent API** - Chainable methods for clean code
- **PSR-7 Bridge** - Convert between Horde\Url and PSR-7 UriInterface
- **Array Parameters** - Automatic handling of nested arrays
- **Raw/HTML Modes** - Output for URLs (`&`) or HTML (`&amp;`)
- **Data URLs** - Create and manipulate RFC 2397 data URLs
- **Immutable Copy** - Clone URLs for safe modifications

## PSR-7 Interoperability

```php
use Horde\Url\Psr7Bridge;

// Horde Url → PSR-7 Uri
$hordeUrl = new Url('https://api.example.com/users?active=1');
$psr7Uri = Psr7Bridge::toPsr7($hordeUrl);

// PSR-7 Uri → Horde Url
$hordeUrl = Psr7Bridge::fromPsr7($psr7Uri);
```

## Documentation

- **Upgrading**: See [doc/UPGRADING.md](doc/UPGRADING.md) for migration guide
- **Homepage**: https://www.horde.org/libraries/Horde_Url
- **License**: LGPL-2.1-only

## Requirements

- PHP 8.1 or later
- horde/exception ^3

## Optional Dependencies

- `psr/http-message` - For PSR-7 bridge support
- `horde/http` - Provides PSR-7 Uri implementation

## Development

```bash
# Run tests
composer config minimum-stability dev
composer install
phpunit

# Run with coverage
phpunit --coverage-html coverage/
```

## Version 3.0 Changes

Version 3.0 is a major modernization with breaking changes in the src/ path.
See [doc/UPGRADING.md](doc/UPGRADING.md) for details.
The lib/ path is a legacy compatibility layer and will be removed in Horde 7.

- Modern PSR-4 namespace: `Horde\Url\Url`
- PHP 8.1+ strict types
- PSR-7 interoperability via `Psr7Bridge`
- Enhanced test coverage

## Contributing

Bug reports and pull requests are welcome at https://github.com/horde/Url

## Support

- **Documentation**: https://www.horde.org/libraries/Horde_Url
- **Mailing List**: https://www.horde.org/community/mail
- **Issues**: https://github.com/horde/Url/issues
