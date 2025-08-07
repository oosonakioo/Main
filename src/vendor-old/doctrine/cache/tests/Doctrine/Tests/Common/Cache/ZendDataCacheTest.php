<?php

namespace Doctrine\Tests\Common\Cache;

use Doctrine\Common\Cache\ZendDataCache;

/**
 * @requires function zend_shm_cache_fetch
 */
class ZendDataCacheTest extends CacheTest
{
    protected function setUp()
    {
        if (php_sapi_name() !== 'apache2handler') {
            $this->markTestSkipped('Zend Data Cache only works in apache2handler SAPI.');
        }
    }

    public function test_get_stats()
    {
        $cache = $this->_getCacheDriver();
        $stats = $cache->getStats();

        $this->assertNull($stats);
    }

    protected function _getCacheDriver()
    {
        return new ZendDataCache;
    }
}
