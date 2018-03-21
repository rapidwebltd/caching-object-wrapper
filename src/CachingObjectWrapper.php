<?php

namespace RapidWeb\CachingObjectWrapper;

use Psr\Cache\CacheItemPoolInterface;

class CachingObjectWrapper
{
    private $wrappedObject;
    private $cache;
    private $expiryInSeconds;

    public function __construct($objectToWrap, CacheItemPoolInterface $cacheItemPool, int $expiryInSeconds)
    {
        $this->wrappedObject = $objectToWrap;
        $this->cache = $cacheItemPool;
        $this->expiryInSeconds = $expiryInSeconds;
    }

    public function __call(string $name, array $arguments)
    {
        $cacheKey = sha1(get_class($this->wrappedObject).$name.serialize($arguments));

        $cacheItem = $this->cache->get($cacheKey);

        if ($cacheItem->isHit()) {
            return $cacheItem->get();
        }

        $response = call_user_func_array($this->wrappedObject->$name, $arguments);

        $cacheItem->set($response);
        $cacheItem->expiresAfter($this->expiryInSeconds);

        $this->cache->save($cacheItem);

        return $response;
    }

}