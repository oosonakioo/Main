<?php

use League\Flysystem\Cached\Storage\Psr6Cache;

class Psr6CacheTests extends PHPUnit_Framework_TestCase
{
    public function test_load_fail()
    {
        $pool = Mockery::mock('Psr\Cache\CacheItemPoolInterface');
        $item = Mockery::mock('Psr\Cache\CacheItemInterface');
        $item->shouldReceive('isHit')->once()->andReturn(false);
        $pool->shouldReceive('getItem')->once()->andReturn($item);
        $cache = new Psr6Cache($pool);
        $cache->load();
        $this->assertFalse($cache->isComplete('', false));
    }

    public function test_load_success()
    {
        $response = json_encode([[], ['' => true]]);
        $pool = Mockery::mock('Psr\Cache\CacheItemPoolInterface');
        $item = Mockery::mock('Psr\Cache\CacheItemInterface');
        $item->shouldReceive('get')->once()->andReturn($response);
        $item->shouldReceive('isHit')->once()->andReturn(true);
        $pool->shouldReceive('getItem')->once()->andReturn($item);
        $cache = new Psr6Cache($pool);
        $cache->load();
        $this->assertTrue($cache->isComplete('', false));
    }

    public function test_save()
    {
        $response = json_encode([[], []]);
        $ttl = 4711;
        $pool = Mockery::mock('Psr\Cache\CacheItemPoolInterface');
        $item = Mockery::mock('Psr\Cache\CacheItemInterface');
        $item->shouldReceive('expiresAfter')->once()->with($ttl);
        $item->shouldReceive('set')->once()->andReturn($response);
        $pool->shouldReceive('getItem')->once()->andReturn($item);
        $pool->shouldReceive('save')->once()->with($item);
        $cache = new Psr6Cache($pool, 'foo', $ttl);
        $cache->save();
    }
}
