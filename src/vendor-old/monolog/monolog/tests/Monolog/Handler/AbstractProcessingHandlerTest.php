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
use Monolog\Processor\WebProcessor;
use Monolog\TestCase;

class AbstractProcessingHandlerTest extends TestCase
{
    /**
     * @covers Monolog\Handler\AbstractProcessingHandler::handle
     */
    public function test_handle_lower_level_message()
    {
        $handler = $this->getMockForAbstractClass('Monolog\Handler\AbstractProcessingHandler', [Logger::WARNING, true]);
        $this->assertFalse($handler->handle($this->getRecord(Logger::DEBUG)));
    }

    /**
     * @covers Monolog\Handler\AbstractProcessingHandler::handle
     */
    public function test_handle_bubbling()
    {
        $handler = $this->getMockForAbstractClass('Monolog\Handler\AbstractProcessingHandler', [Logger::DEBUG, true]);
        $this->assertFalse($handler->handle($this->getRecord()));
    }

    /**
     * @covers Monolog\Handler\AbstractProcessingHandler::handle
     */
    public function test_handle_not_bubbling()
    {
        $handler = $this->getMockForAbstractClass('Monolog\Handler\AbstractProcessingHandler', [Logger::DEBUG, false]);
        $this->assertTrue($handler->handle($this->getRecord()));
    }

    /**
     * @covers Monolog\Handler\AbstractProcessingHandler::handle
     */
    public function test_handle_is_false_when_not_handled()
    {
        $handler = $this->getMockForAbstractClass('Monolog\Handler\AbstractProcessingHandler', [Logger::WARNING, false]);
        $this->assertTrue($handler->handle($this->getRecord()));
        $this->assertFalse($handler->handle($this->getRecord(Logger::DEBUG)));
    }

    /**
     * @covers Monolog\Handler\AbstractProcessingHandler::processRecord
     */
    public function test_process_record()
    {
        $handler = $this->getMockForAbstractClass('Monolog\Handler\AbstractProcessingHandler');
        $handler->pushProcessor(new WebProcessor([
            'REQUEST_URI' => '',
            'REQUEST_METHOD' => '',
            'REMOTE_ADDR' => '',
            'SERVER_NAME' => '',
            'UNIQUE_ID' => '',
        ]));
        $handledRecord = null;
        $handler->expects($this->once())
            ->method('write')
            ->will($this->returnCallback(function ($record) use (&$handledRecord) {
                $handledRecord = $record;
            }));
        $handler->handle($this->getRecord());
        $this->assertEquals(6, count($handledRecord['extra']));
    }
}
