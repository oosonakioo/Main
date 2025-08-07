<?php

namespace Doctrine\Tests\Common\Cache;

use Doctrine\Common\Cache\ArrayCache;
use Doctrine\Common\Cache\ChainCache;

class ChainCacheTest extends CacheTest
{
    protected function _getCacheDriver()
    {
        return new ChainCache([new ArrayCache]);
    }

    public function test_lifetime()
    {
        $this->markTestSkipped('The ChainCache test uses ArrayCache which does not implement TTL currently.');
    }

    public function test_get_stats()
    {
        $cache = $this->_getCacheDriver();
        $stats = $cache->getStats();

        $this->assertInternalType('array', $stats);
    }

    public function test_only_fetch_first_one()
    {
        $cache1 = new ArrayCache;
        $cache2 = $this->getMockForAbstractClass('Doctrine\Common\Cache\CacheProvider');

        $cache2->expects($this->never())->method('doFetch');

        $chainCache = new ChainCache([$cache1, $cache2]);
        $chainCache->save('id', 'bar');

        $this->assertEquals('bar', $chainCache->fetch('id'));
    }

    public function test_fetch_propagate_to_fastest_cache()
    {
        $cache1 = new ArrayCache;
        $cache2 = new ArrayCache;

        $cache2->save('bar', 'value');

        $chainCache = new ChainCache([$cache1, $cache2]);

        $this->assertFalse($cache1->contains('bar'));

        $result = $chainCache->fetch('bar');

        $this->assertEquals('value', $result);
        $this->assertTrue($cache2->contains('bar'));
    }

    public function test_namespace_is_propagated_to_all_providers()
    {
        $cache1 = new ArrayCache;
        $cache2 = new ArrayCache;

        $chainCache = new ChainCache([$cache1, $cache2]);
        $chainCache->setNamespace('bar');

        $this->assertEquals('bar', $cache1->getNamespace());
        $this->assertEquals('bar', $cache2->getNamespace());
    }

    public function test_delete_to_all_providers()
    {
        $cache1 = $this->getMockForAbstractClass('Doctrine\Common\Cache\CacheProvider');
        $cache2 = $this->getMockForAbstractClass('Doctrine\Common\Cache\CacheProvider');

        $cache1->expects($this->once())->method('doDelete');
        $cache2->expects($this->once())->method('doDelete');

        $chainCache = new ChainCache([$cache1, $cache2]);
        $chainCache->delete('bar');
    }

    public function test_flush_to_all_providers()
    {
        $cache1 = $this->getMockForAbstractClass('Doctrine\Common\Cache\CacheProvider');
        $cache2 = $this->getMockForAbstractClass('Doctrine\Common\Cache\CacheProvider');

        $cache1->expects($this->once())->method('doFlush');
        $cache2->expects($this->once())->method('doFlush');

        $chainCache = new ChainCache([$cache1, $cache2]);
        $chainCache->flushAll();
    }

    protected function isSharedStorage()
    {
        return false;
    }
}
