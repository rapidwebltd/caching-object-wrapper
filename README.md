# 🎁 Caching Object Wrapper

[![Build Status](https://travis-ci.org/rapidwebltd/caching-object-wrapper.svg?branch=master)](https://travis-ci.org/rapidwebltd/caching-object-wrapper)
[![Coverage Status](https://coveralls.io/repos/github/rapidwebltd/caching-object-wrapper/badge.svg?branch=master)](https://coveralls.io/github/rapidwebltd/caching-object-wrapper?branch=master)
[![StyleCI](https://styleci.io/repos/126181707/shield?branch=master)](https://styleci.io/repos/126181707)

Automatically cache the method responses of a PHP object.

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