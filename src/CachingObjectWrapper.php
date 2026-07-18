<?php

namespace RapidWeb\CachingObjectWrapper;

use Psr\Cache\CacheItemPoolInterface;

class CachingObjectWrapper
{
    private $wrappedObject;
    private $cache;
    private $expiryInSeconds;
    private $cacheNamespace;

    public function __construct($objectToWrap, CacheItemPoolInterface $cacheItemPool, $expiryInSeconds, $cacheNamespace = null)
    {
        $this->wrappedObject = $objectToWrap;
        $this->cache = $cacheItemPool;
        $this->expiryInSeconds = $expiryInSeconds;
        $this->cacheNamespace = $cacheNamespace === null ? get_class($objectToWrap) : (string) $cacheNamespace;
    }

    public function __call($name, $arguments)
    {
        $cacheKey = $this->cacheKeyFor($name, $arguments);

        $cacheItem = $this->cache->getItem($cacheKey);

        if ($cacheItem->isHit()) {
            return $cacheItem->get();
        }

        $response = call_user_func_array([$this->wrappedObject, $name], $arguments);

        $cacheItem->set($response);
        $cacheItem->expiresAfter($this->expiryInSeconds);

        $this->cache->save($cacheItem);

        return $response;
    }

    /**
     * Return the cache key used for a method call.
     */
    public function cacheKeyFor($name, array $arguments = [])
    {
        return sha1($this->cacheNamespace.$name.serialize($arguments));
    }

    /**
     * Remove one cached method call.
     */
    public function forgetCachedCall($name, array $arguments = [])
    {
        return $this->cache->deleteItem($this->cacheKeyFor($name, $arguments));
    }

    /**
     * Remove and immediately recompute one cached method call.
     */
    public function refreshCachedCall($name, array $arguments = [])
    {
        $this->forgetCachedCall($name, $arguments);

        return $this->__call($name, $arguments);
    }

    /**
     * Return the object being wrapped.
     */
    public function getWrappedObject()
    {
        return $this->wrappedObject;
    }
}
