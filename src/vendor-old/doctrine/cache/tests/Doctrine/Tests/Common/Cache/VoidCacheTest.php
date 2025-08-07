<?php

namespace Doctrine\Tests\Common\Cache;

use Doctrine\Common\Cache\VoidCache;

/**
 * @covers \Doctrine\Common\Cache\VoidCache
 */
class VoidCacheTest extends \PHPUnit_Framework_TestCase
{
    public function test_should_always_return_false_on_contains()
    {
        $cache = new VoidCache;

        $this->assertFalse($cache->contains('foo'));
        $this->assertFalse($cache->contains('bar'));
    }

    public function test_should_always_return_false_on_fetch()
    {
        $cache = new VoidCache;

        $this->assertFalse($cache->fetch('foo'));
        $this->assertFalse($cache->fetch('bar'));
    }

    public function test_should_always_return_true_on_save_but_not_store_anything()
    {
        $cache = new VoidCache;

        $this->assertTrue($cache->save('foo', 'fooVal'));

        $this->assertFalse($cache->contains('foo'));
        $this->assertFalse($cache->fetch('foo'));
    }

    public function test_should_always_return_true_on_delete()
    {
        $cache = new VoidCache;

        $this->assertTrue($cache->delete('foo'));
    }

    public function test_should_always_return_null_on_get_status()
    {
        $cache = new VoidCache;

        $this->assertNull($cache->getStats());
    }

    public function test_should_always_return_true_on_flush()
    {
        $cache = new VoidCache;

        $this->assertTrue($cache->flushAll());
    }
}
