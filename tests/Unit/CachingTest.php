<?php

use PHPUnit\Framework\TestCase;
use RapidWeb\CachingObjectWrapper\CachingObjectWrapper;
use rapidweb\RWFileCachePSR6\CacheItemPool;

final class CachingTest extends TestCase
{
    public function testObjectResponsesAreCached()
    {
        require_once __DIR__.'/includes/RandomNumberGenerator.php';

        $cache = $this->newCache();

        $randomNumberGenerator = new CachingObjectWrapper(new RandomNumberGenerator(), $cache, 60 * 60);

        $expected = $randomNumberGenerator->generate();

        for ($i = 0; $i < 100; $i++) {
            $this->assertEquals($expected, $randomNumberGenerator->generate());
        }
    }

    public function testCachedCallsCanBeForgottenAndRefreshed()
    {
        require_once __DIR__.'/includes/CountingValueGenerator.php';

        $cache = $this->newCache();
        $generator = new CountingValueGenerator();
        $wrapper = new CachingObjectWrapper($generator, $cache, 3600);

        $this->assertSame('first-1', $wrapper->generate('first'));
        $this->assertSame('first-1', $wrapper->generate('first'));
        $this->assertSame('second-2', $wrapper->generate('second'));
        $this->assertTrue($wrapper->forgetCachedCall('generate', ['first']));
        $this->assertSame('first-3', $wrapper->generate('first'));
        $this->assertSame('first-4', $wrapper->refreshCachedCall('generate', ['first']));
        $this->assertSame($generator, $wrapper->getWrappedObject());
    }

    public function testCustomNamespacesPreventCrossInstanceCollisions()
    {
        require_once __DIR__.'/includes/CountingValueGenerator.php';

        $cache = $this->newCache();
        $first = new CachingObjectWrapper(new CountingValueGenerator(), $cache, 3600, 'first');
        $second = new CachingObjectWrapper(new CountingValueGenerator(), $cache, 3600, 'second');

        $this->assertNotSame($first->cacheKeyFor('generate'), $second->cacheKeyFor('generate'));
        $this->assertSame('value-1', $first->generate('value'));
        $this->assertSame('value-1', $second->generate('value'));
    }

    private function newCache()
    {
        $cacheDirectory = sys_get_temp_dir().'/cow-tests-'.getmypid().'/';
        if (!is_dir($cacheDirectory)) {
            mkdir($cacheDirectory, 0777, true);
        }

        $cache = new CacheItemPool();
        $cache->changeConfig(
            [
                'cacheDirectory'  => $cacheDirectory,
                'gzipCompression' => true,
            ]
        );
        $cache->clear();

        return $cache;
    }
}
