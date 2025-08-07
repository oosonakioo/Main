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

use Monolog\TestCase;

class DynamoDbHandlerTest extends TestCase
{
    private $client;

    protected function setUp()
    {
        if (! class_exists('Aws\DynamoDb\DynamoDbClient')) {
            $this->markTestSkipped('aws/aws-sdk-php not installed');
        }

        $this->client = $this->getMockBuilder('Aws\DynamoDb\DynamoDbClient')
            ->setMethods(['formatAttributes', '__call'])
            ->disableOriginalConstructor()->getMock();
    }

    public function test_construct()
    {
        $this->assertInstanceOf('Monolog\Handler\DynamoDbHandler', new DynamoDbHandler($this->client, 'foo'));
    }

    public function test_interface()
    {
        $this->assertInstanceOf('Monolog\Handler\HandlerInterface', new DynamoDbHandler($this->client, 'foo'));
    }

    public function test_get_formatter()
    {
        $handler = new DynamoDbHandler($this->client, 'foo');
        $this->assertInstanceOf('Monolog\Formatter\ScalarFormatter', $handler->getFormatter());
    }

    public function test_handle()
    {
        $record = $this->getRecord();
        $formatter = $this->getMock('Monolog\Formatter\FormatterInterface');
        $formatted = ['foo' => 1, 'bar' => 2];
        $handler = new DynamoDbHandler($this->client, 'foo');
        $handler->setFormatter($formatter);

        $formatter
            ->expects($this->once())
            ->method('format')
            ->with($record)
            ->will($this->returnValue($formatted));
        $this->client
            ->expects($this->once())
            ->method('formatAttributes')
            ->with($this->isType('array'))
            ->will($this->returnValue($formatted));
        $this->client
            ->expects($this->once())
            ->method('__call')
            ->with('putItem', [[
                'TableName' => 'foo',
                'Item' => $formatted,
            ]]);

        $handler->handle($record);
    }
}
