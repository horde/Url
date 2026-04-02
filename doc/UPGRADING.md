# Upgrading to Horde_Url 3.0

This guide helps you migrate from Horde_Url 2.x to 3.0.

## Overview

Version 3.0 is a major modernization release with breaking changes:

- **PHP 8.1+ required** (was PHP 7.4+)
- **PSR-4 namespace** available alongside legacy PSR-0
- **Strict types** throughout
- **PSR-7 interoperability** via new Psr7Bridge
- **Removed BC layer** for ancient Horde_Url API

## Migration Strategies

### Preliminary Option: Keep Using PSR-0 (Minimal Changes)

Continue using `Horde_Url` - the PSR-0 wrapper remains and works exactly as before.

**No code changes needed** for basic usage:
```php
// This still works in 3.0
$url = new Horde_Url('http://example.com');
$url->add('foo', 'bar');
```

- Zero migration effort
- Drop-in upgrade
- Full backward compatibility

### Future Safe: Migrate to PSR-4 (Recommended)

Adopt the modern `Horde\Url\Url` namespace for new features and future-proofing.

## Breaking Changes

### 1. Namespace Change (PSR-4 only)

**Before (PSR-0):**
```php
$url = new Horde_Url('http://example.com');
```

**After (PSR-4):**
```php
use Horde\Url\Url;

$url = new Url('http://example.com');
```

### 2. Exception Namespace

**Before:**
```php
try {
    $url->redirect();
} catch (Horde_Url_Exception $e) {
    // Handle
}
```

**After (PSR-4):**
```php
use Horde\Url\UrlException;

try {
    $url->redirect();
} catch (UrlException $e) {
    // Handle
}
```

### 3. Type Declarations

All methods now have strict types. Code relying on loose type coercion may break.

**Before:**
```php
$url->add('foo', 123);      // Worked (int converted to string)
$url->setAnchor(null);      // Worked (null converted to empty string)
```

**After:**
```php
$url->add('foo', 123);      // Still works (mixed type)
$url->setAnchor('');        // Must pass string, not null
```

### 4. Constructor Changes

The PSR-4 `Url` constructor uses union types instead of mixed.

**Before (PSR-0):**
```php
new Horde_Url($url);        // Any type accepted
```

**After (PSR-4):**
```php
new Url($url);              // string|Url|Stringable only
```

## New Features in 3.0

### PSR-7 Bridge

Convert between Horde URLs and PSR-7 URIs:

```php
use Horde\Url\Psr7Bridge;
use Horde\Url\Url;

// Horde → PSR-7
$hordeUrl = new Url('https://example.com/api');
$psr7Uri = Psr7Bridge::toPsr7($hordeUrl);

// PSR-7 → Horde
$hordeUrl = Psr7Bridge::fromPsr7($psr7Uri);
```

**Use cases:**
- Working with PSR-7 middleware
- Integrating with modern HTTP clients
- Building PSR-compliant APIs

### Improved Type Safety

All public methods have complete type declarations:

```php
public function add(mixed $parameters, mixed $value = null): self
public function setAnchor(string $anchor): self
public function toString(bool $raw = false, bool $full = true): string
```

### Copy Method

Create independent copies for safe modifications:

```php
$original = new Url('http://example.com');
$copy = $original->copy();
$copy->add('foo', 'bar');

// Original unchanged
echo $original; // http://example.com
echo $copy;     // http://example.com?foo=bar
```

## Migration Guide

### Step 1: Update Composer

```bash
composer require horde/url:^3.0
```

### Step 2: Choose Your Path

**For minimal changes (PSR-0):**
- No code changes needed
- Continue using `Horde_Url`
- Plan to migrate to PSR-4 before 4.0

**For PSR-4 migration:**
- Continue to Step 3

### Step 3: Update Imports (PSR-4)

**Find and replace:**
```php
// Old imports
use Horde_Url;
use Horde_Url_Exception;

// New imports
use Horde\Url\Url;
use Horde\Url\UrlException;
```

**Class instantiation:**
```php
// Old
$url = new Horde_Url($string);

// New
$url = new Url($string);
```

### Step 4: Fix Type Issues (PSR-4)

**Check these patterns:**

```php
// Null anchor - change to empty string
$url->setAnchor(null);      // Before
$url->setAnchor('');        // After

// Non-string schemes
$url->setScheme(443);       // Before
$url->setScheme('https');   // After
```

### Step 5: Test Thoroughly

```bash
# Run your test suite
vendor/bin/phpunit

# Check for type errors
vendor/bin/phpstan analyze src/
```

## Common Migration Issues

### Issue 1: Null Values

**Symptom:**
```
TypeError: Horde\Url\Url::setAnchor(): Argument #1 must be of type string, null given
```

**Fix:**
```php
// Before
$url->setAnchor($maybeNull);

// After
$url->setAnchor($maybeNull ?? '');
```

### Issue 2: Type Juggling

**Symptom:**
```
TypeError: Argument must be string|Url|Stringable, int given
```

**Fix:**
```php
// Before
$url = new Url(123);

// After
$url = new Url((string) 123);
```

### Issue 3: Exception Catching

**Symptom:**
```
Exception Horde\Url\UrlException not caught
```

**Fix:**
```php
// Before
catch (Horde_Url_Exception $e)

// After
use Horde\Url\UrlException;
catch (UrlException $e)
```

## PSR-7 Integration Examples

### Example 1: PSR-7 Middleware

```php
use Horde\Url\Psr7Bridge;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

function redirectMiddleware(
    ServerRequestInterface $request,
    ResponseInterface $response
): ResponseInterface {
    $uri = $request->getUri();
    
    // Convert PSR-7 to Horde for manipulation
    $url = Psr7Bridge::fromPsr7($uri);
    $url->add('utm_source', 'redirect');
    $url->setScheme('https', replace: true);
    
    // Convert back to PSR-7
    $newUri = Psr7Bridge::toPsr7($url);
    
    return $response
        ->withStatus(301)
        ->withHeader('Location', (string) $newUri);
}
```

### Example 2: HTTP Client Integration

```php
use Horde\Url\Url;
use Horde\Url\Psr7Bridge;

// Build URL with Horde's API
$url = new Url('https://api.example.com/v1/users');
$url->add([
    'page' => 2,
    'limit' => 50,
    'filter' => ['active' => true]
]);

// Use with PSR-7 HTTP client
$psr7Uri = Psr7Bridge::toPsr7($url);
$request = new Request('GET', $psr7Uri);
$response = $httpClient->sendRequest($request);
```

## Deprecation Notices

### What's Deprecated

Nothing is deprecated in 3.0, but be aware:

- **PSR-0 wrapper** will be deprecated in 4.0
- **Migrate to PSR-4** now to avoid future breaking changes

## Testing Your Migration

### Unit Tests

```php
use PHPUnit\Framework\TestCase;
use Horde\Url\Url;

class UrlMigrationTest extends TestCase
{
    public function testBasicUsage(): void
    {
        $url = new Url('http://example.com');
        $url->add('foo', 'bar');

        $this->assertEquals(
            'http://example.com?foo=bar',
            (string) $url
        );
    }

    public function testPsr7Bridge(): void
    {
        $url = new Url('https://api.example.com/users');
        $psr7 = Psr7Bridge::toPsr7($url);

        $this->assertInstanceOf(
            \Psr\Http\Message\UriInterface::class,
            $psr7
        );
    }
}
```

### Static Analysis

```bash
# Install PHPStan
composer require --dev phpstan/phpstan

# Check for type errors
vendor/bin/phpstan analyze src/ --level=8
```

## Getting Help

### Resources

- **Documentation**: https://www.horde.org/libraries/Horde_Url
- **API Docs**: https://dev.horde.org/api/master/lib/Url/
- **Issues**: https://github.com/horde/Url/issues
- **Mailing List**: https://www.horde.org/community/mail

### Common Questions

**Q: Should I migrate to PSR-4 now?**

A: Yes, if you can. PSR-0 wrapper will be deprecated in 4.0. Migrating now gives you:
- Access to PSR-7 bridge
- Better type safety
- Future-proof code

**Q: Can I use both PSR-0 and PSR-4 during migration?**

A: Yes! Mix and match as needed:
```php
$old = new Horde_Url('http://example.com');  // PSR-0
$new = new Url('http://example.com');        // PSR-4
```

**Q: Do I need PSR-7 dependencies?**

A: No. PSR-7 bridge is optional. Install only if you need it:
```bash
composer require psr/http-message horde/http
```

**Q: What about performance?**

A: PSR-4 is equally fast. The strict types may even improve performance in PHP 8.1+.

## Version Support

| Version | PHP | Support Status |
|---------|-----|----------------|
| 2.x | 7.4+ | Security fixes only |
| 3.0 | 8.1+ | Active development |
| 4.0 | 8.2+ | Future (TBD) |

## Changelog

See [doc/changelog.yml](changelog.yml) for detailed changes.

## Credits

Version 3.0 modernization by the Horde development team.

Original Horde_Url by Michael Slusarz and Jan Schneider.
