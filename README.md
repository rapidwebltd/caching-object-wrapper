# Caching Object Wrapper

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

```php
$object = new CachingObjectWrapper($objectToCacheMethodResponsesOf, $psr6CacheItemPool, $expiryTimeInSeconds); 
```