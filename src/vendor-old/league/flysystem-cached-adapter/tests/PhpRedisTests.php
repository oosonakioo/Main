<?php

use League\Flysystem\Cached\Storage\PhpRedis;

class PhpRedisTests extends PHPUnit_Framework_TestCase
{
    public function test_load_fail()
    {
        $client = Mockery::mock('Redis');
        $client->shouldReceive('get')->with('flysystem')->once()->andReturn(false);
        $cache = new PhpRedis($client);
        $cache->load();
        $this->assertFalse($cache->isComplete('', false));
    }

    public function test_load_success()
    {
        $response = json_encode([[], ['' => true]]);
        $client = Mockery::mock('Redis');
        $client->shouldReceive('get')->with('flysystem')->once()->andReturn($response);
        $cache = new PhpRedis($client);
        $cache->load();
        $this->assertTrue($cache->isComplete('', false));
    }

    public function test_save()
    {
        $data = json_encode([[], []]);
        $client = Mockery::mock('Redis');
        $client->shouldReceive('set')->with('flysystem', $data)->once();
        $cache = new PhpRedis($client);
        $cache->save();
    }

    public function test_save_with_expire()
    {
        $data = json_encode([[], []]);
        $client = Mockery::mock('Redis');
        $client->shouldReceive('set')->with('flysystem', $data)->once();
        $client->shouldReceive('expire')->with('flysystem', 20)->once();
        $cache = new PhpRedis($client, 'flysystem', 20);
        $cache->save();
    }
}
