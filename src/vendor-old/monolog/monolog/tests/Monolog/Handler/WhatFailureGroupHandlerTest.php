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

use Monolog\Logger;
use Monolog\TestCase;

class WhatFailureGroupHandlerTest extends TestCase
{
    /**
     * @covers Monolog\Handler\WhatFailureGroupHandler::__construct
     *
     * @expectedException InvalidArgumentException
     */
    public function test_constructor_only_takes_handler()
    {
        new WhatFailureGroupHandler([new TestHandler, 'foo']);
    }

    /**
     * @covers Monolog\Handler\WhatFailureGroupHandler::__construct
     * @covers Monolog\Handler\WhatFailureGroupHandler::handle
     */
    public function test_handle()
    {
        $testHandlers = [new TestHandler, new TestHandler];
        $handler = new WhatFailureGroupHandler($testHandlers);
        $handler->handle($this->getRecord(Logger::DEBUG));
        $handler->handle($this->getRecord(Logger::INFO));
        foreach ($testHandlers as $test) {
            $this->assertTrue($test->hasDebugRecords());
            $this->assertTrue($test->hasInfoRecords());
            $this->assertTrue(count($test->getRecords()) === 2);
        }
    }

    /**
     * @covers Monolog\Handler\WhatFailureGroupHandler::handleBatch
     */
    public function test_handle_batch()
    {
        $testHandlers = [new TestHandler, new TestHandler];
        $handler = new WhatFailureGroupHandler($testHandlers);
        $handler->handleBatch([$this->getRecord(Logger::DEBUG), $this->getRecord(Logger::INFO)]);
        foreach ($testHandlers as $test) {
            $this->assertTrue($test->hasDebugRecords());
            $this->assertTrue($test->hasInfoRecords());
            $this->assertTrue(count($test->getRecords()) === 2);
        }
    }

    /**
     * @covers Monolog\Handler\WhatFailureGroupHandler::isHandling
     */
    public function test_is_handling()
    {
        $testHandlers = [new TestHandler(Logger::ERROR), new TestHandler(Logger::WARNING)];
        $handler = new WhatFailureGroupHandler($testHandlers);
        $this->assertTrue($handler->isHandling($this->getRecord(Logger::ERROR)));
        $this->assertTrue($handler->isHandling($this->getRecord(Logger::WARNING)));
        $this->assertFalse($handler->isHandling($this->getRecord(Logger::DEBUG)));
    }

    /**
     * @covers Monolog\Handler\WhatFailureGroupHandler::handle
     */
    public function test_handle_uses_processors()
    {
        $test = new TestHandler;
        $handler = new WhatFailureGroupHandler([$test]);
        $handler->pushProcessor(function ($record) {
            $record['extra']['foo'] = true;

            return $record;
        });
        $handler->handle($this->getRecord(Logger::WARNING));
        $this->assertTrue($test->hasWarningRecords());
        $records = $test->getRecords();
        $this->assertTrue($records[0]['extra']['foo']);
    }

    /**
     * @covers Monolog\Handler\WhatFailureGroupHandler::handle
     */
    public function test_handle_exception()
    {
        $test = new TestHandler;
        $exception = new ExceptionTestHandler;
        $handler = new WhatFailureGroupHandler([$exception, $test, $exception]);
        $handler->pushProcessor(function ($record) {
            $record['extra']['foo'] = true;

            return $record;
        });
        $handler->handle($this->getRecord(Logger::WARNING));
        $this->assertTrue($test->hasWarningRecords());
        $records = $test->getRecords();
        $this->assertTrue($records[0]['extra']['foo']);
    }
}

class ExceptionTestHandler extends TestHandler
{
    /**
     * {@inheritdoc}
     */
    public function handle(array $record)
    {
        parent::handle($record);

        throw new \Exception('ExceptionTestHandler::handle');
    }
}
