# 🎁 Caching Object Wrapper

[![Tests](https://github.com/rapidwebltd/caching-object-wrapper/actions/workflows/tests.yml/badge.svg)](https://github.com/rapidwebltd/caching-object-wrapper/actions/workflows/tests.yml)

Wraps up any PHP object so all its methods are cached.

## Installation

You can install this package easily via Composer. Just run the following command from the root of your project.

```
composer require rapidwebltd/caching-object-wrapper
```

## Usage

Here is a very simple usage example, showing how we can easily cache a simple object.

```php
use RapidWeb\CachingObjectWrapper\CachingObjectWrapper;
use rapidweb\RWFileCachePSR6\CacheItemPool;

// This is an example class that we are going to use. It just generates random numbers.
class RandomNumberGenerator
{
    public function generate()
    {
        return rand();
    }
}

// First, we need to install and setup a PSR6 cache item pool. 
// As an example, we'll use the PSR-6 adapter for RW File Cache.

// $ composer require rapidwebltd/rw-file-cache-psr-6

$cache = new CacheItemPool();
$cache->changeConfig(
    [
        'cacheDirectory'  => '/tmp/cow-tests/',
        'gzipCompression' => true,
        ]
    );

// Next, we can wrap up a new RandomNumberGenerator.
// We also pass in the $cache object we just created, and the desired cache expiry time in seconds.

$randomNumberGenerator = new CachingObjectWrapper(new RandomNumberGenerator(), $cache, 60 * 60);

// That's it!
// To test, we can tell the wrapped object to generate us two random numbers.

$randomNumber1 = $randomNumberGenerator->generate();
$randomNumber2 = $randomNumberGenerator->generate();

// Due to our caching, $randomNumber1 and $randomNumber2 should be identical.
```

## Invalidating and refreshing calls

You can remove a single cached call without clearing the entire cache pool. Pass the method name and the same argument list used for the call.

```php
$randomNumberGenerator->forgetCachedCall('generate');
$freshNumber = $randomNumberGenerator->refreshCachedCall('generate');
```

`cacheKeyFor($method, $arguments)` returns the underlying PSR-6 key when you need to inspect or manage it directly.

By default, wrappers around the same class share cached calls. Give the constructor an optional fourth argument to isolate instances that have different internal state.

```php
$wrapped = new CachingObjectWrapper($service, $cache, 3600, 'customer-123');
```

The package supports PSR Cache 1, 2, and 3 while retaining its PHP 5.6 minimum.
