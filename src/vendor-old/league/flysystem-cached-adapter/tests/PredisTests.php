<?php

use League\Flysystem\Cached\Storage\Predis;

class PredisTests extends PHPUnit_Framework_TestCase
{
    public function test_load_fail()
    {
        $client = Mockery::mock('Predis\Client');
        $command = Mockery::mock('Predis\Command\CommandInterface');
        $client->shouldReceive('createCommand')->with('get', ['flysystem'])->once()->andReturn($command);
        $client->shouldReceive('executeCommand')->with($command)->andReturn(null);
        $cache = new Predis($client);
        $cache->load();
        $this->assertFalse($cache->isComplete('', false));
    }

    public function test_load_success()
    {
        $response = json_encode([[], ['' => true]]);
        $client = Mockery::mock('Predis\Client');
        $command = Mockery::mock('Predis\Command\CommandInterface');
        $client->shouldReceive('createCommand')->with('get', ['flysystem'])->once()->andReturn($command);
        $client->shouldReceive('executeCommand')->with($command)->andReturn($response);
        $cache = new Predis($client);
        $cache->load();
        $this->assertTrue($cache->isComplete('', false));
    }

    public function test_save()
    {
        $data = json_encode([[], []]);
        $client = Mockery::mock('Predis\Client');
        $command = Mockery::mock('Predis\Command\CommandInterface');
        $client->shouldReceive('createCommand')->with('set', ['flysystem', $data])->once()->andReturn($command);
        $client->shouldReceive('executeCommand')->with($command)->once();
        $cache = new Predis($client);
        $cache->save();
    }

    public function test_save_with_expire()
    {
        $data = json_encode([[], []]);
        $client = Mockery::mock('Predis\Client');
        $command = Mockery::mock('Predis\Command\CommandInterface');
        $client->shouldReceive('createCommand')->with('set', ['flysystem', $data])->once()->andReturn($command);
        $client->shouldReceive('executeCommand')->with($command)->once();
        $expireCommand = Mockery::mock('Predis\Command\CommandInterface');
        $client->shouldReceive('createCommand')->with('expire', ['flysystem', 20])->once()->andReturn($expireCommand);
        $client->shouldReceive('executeCommand')->with($expireCommand)->once();
        $cache = new Predis($client, 'flysystem', 20);
        $cache->save();
    }
}
