<?php

/*
 * This file is part of the Monolog package.
 *
 * (c) Jordi Boggiano <j.boggiano@seld.be>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Monolog\Handler;

use Monolog\Formatter\LineFormatter;
use Monolog\Logger;
use Monolog\TestCase;

class RedisHandlerTest extends TestCase
{
    /**
     * @expectedException InvalidArgumentException
     */
    public function test_constructor_should_throw_exception_for_invalid_redis()
    {
        new RedisHandler(new \stdClass, 'key');
    }

    public function test_constructor_should_work_with_predis()
    {
        $redis = $this->getMock('Predis\Client');
        $this->assertInstanceof('Monolog\Handler\RedisHandler', new RedisHandler($redis, 'key'));
    }

    public function test_constructor_should_work_with_redis()
    {
        $redis = $this->getMock('Redis');
        $this->assertInstanceof('Monolog\Handler\RedisHandler', new RedisHandler($redis, 'key'));
    }

    public function test_predis_handle()
    {
        $redis = $this->getMock('Predis\Client', ['rpush']);

        // Predis\Client uses rpush
        $redis->expects($this->once())
            ->method('rpush')
            ->with('key', 'test');

        $record = $this->getRecord(Logger::WARNING, 'test', ['data' => new \stdClass, 'foo' => 34]);

        $handler = new RedisHandler($redis, 'key');
        $handler->setFormatter(new LineFormatter('%message%'));
        $handler->handle($record);
    }

    public function test_redis_handle()
    {
        $redis = $this->getMock('Redis', ['rpush']);

        // Redis uses rPush
        $redis->expects($this->once())
            ->method('rPush')
            ->with('key', 'test');

        $record = $this->getRecord(Logger::WARNING, 'test', ['data' => new \stdClass, 'foo' => 34]);

        $handler = new RedisHandler($redis, 'key');
        $handler->setFormatter(new LineFormatter('%message%'));
        $handler->handle($record);
    }

    public function test_redis_handle_capped()
    {
        $redis = $this->getMock('Redis', ['multi', 'rpush', 'ltrim', 'exec']);

        // Redis uses multi
        $redis->expects($this->once())
            ->method('multi')
            ->will($this->returnSelf());

        $redis->expects($this->once())
            ->method('rpush')
            ->will($this->returnSelf());

        $redis->expects($this->once())
            ->method('ltrim')
            ->will($this->returnSelf());

        $redis->expects($this->once())
            ->method('exec')
            ->will($this->returnSelf());

        $record = $this->getRecord(Logger::WARNING, 'test', ['data' => new \stdClass, 'foo' => 34]);

        $handler = new RedisHandler($redis, 'key', Logger::DEBUG, true, 10);
        $handler->setFormatter(new LineFormatter('%message%'));
        $handler->handle($record);
    }

    public function test_predis_handle_capped()
    {
        $redis = $this->getMock('Predis\Client', ['transaction']);

        $redisTransaction = $this->getMock('Predis\Client', ['rpush', 'ltrim']);

        $redisTransaction->expects($this->once())
            ->method('rpush')
            ->will($this->returnSelf());

        $redisTransaction->expects($this->once())
            ->method('ltrim')
            ->will($this->returnSelf());

        // Redis uses multi
        $redis->expects($this->once())
            ->method('transaction')
            ->will($this->returnCallback(function ($cb) use ($redisTransaction) {
                $cb($redisTransaction);
            }));

        $record = $this->getRecord(Logger::WARNING, 'test', ['data' => new \stdClass, 'foo' => 34]);

        $handler = new RedisHandler($redis, 'key', Logger::DEBUG, true, 10);
        $handler->setFormatter(new LineFormatter('%message%'));
        $handler->handle($record);
    }
}
