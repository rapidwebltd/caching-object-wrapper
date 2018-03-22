<?php

use PHPUnit\Framework\TestCase;
use RapidWeb\CachingObjectWrapper\CachingObjectWrapper;
use rapidweb\RWFileCachePSR6\CacheItemPool;

final class CachingTest extends TestCase
{
    public function testObjectResponsesAreCached()
    {
        require_once __DIR__.'/includes/RandomNumberGenerator.php';

        $cache = new CacheItemPool();
        $cache->changeConfig(
            [
                'cacheDirectory'  => '/tmp/cow-tests/',
                'gzipCompression' => true,
                ]
            );
        
        $randomNumberGenerator = new CachingObjectWrapper(new RandomNumberGenerator(), $cache, 60*60);

        $expected = $randomNumberGenerator->generate();

        for ($i=0; $i < 100; $i++) { 
            $this->assertEquals($expected, $randomNumberGenerator->generate());
        }

    }
}
