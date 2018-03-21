# Caching Object Wrapper

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