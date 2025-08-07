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
use Monolog\Processor\WebProcessor;
use Monolog\TestCase;

class AbstractHandlerTest extends TestCase
{
    /**
     * @covers Monolog\Handler\AbstractHandler::__construct
     * @covers Monolog\Handler\AbstractHandler::getLevel
     * @covers Monolog\Handler\AbstractHandler::setLevel
     * @covers Monolog\Handler\AbstractHandler::getBubble
     * @covers Monolog\Handler\AbstractHandler::setBubble
     * @covers Monolog\Handler\AbstractHandler::getFormatter
     * @covers Monolog\Handler\AbstractHandler::setFormatter
     */
    public function test_construct_and_get_set()
    {
        $handler = $this->getMockForAbstractClass('Monolog\Handler\AbstractHandler', [Logger::WARNING, false]);
        $this->assertEquals(Logger::WARNING, $handler->getLevel());
        $this->assertEquals(false, $handler->getBubble());

        $handler->setLevel(Logger::ERROR);
        $handler->setBubble(true);
        $handler->setFormatter($formatter = new LineFormatter);
        $this->assertEquals(Logger::ERROR, $handler->getLevel());
        $this->assertEquals(true, $handler->getBubble());
        $this->assertSame($formatter, $handler->getFormatter());
    }

    /**
     * @covers Monolog\Handler\AbstractHandler::handleBatch
     */
    public function test_handle_batch()
    {
        $handler = $this->getMockForAbstractClass('Monolog\Handler\AbstractHandler');
        $handler->expects($this->exactly(2))
            ->method('handle');
        $handler->handleBatch([$this->getRecord(), $this->getRecord()]);
    }

    /**
     * @covers Monolog\Handler\AbstractHandler::isHandling
     */
    public function test_is_handling()
    {
        $handler = $this->getMockForAbstractClass('Monolog\Handler\AbstractHandler', [Logger::WARNING, false]);
        $this->assertTrue($handler->isHandling($this->getRecord()));
        $this->assertFalse($handler->isHandling($this->getRecord(Logger::DEBUG)));
    }

    /**
     * @covers Monolog\Handler\AbstractHandler::__construct
     */
    public function test_handles_psr_style_levels()
    {
        $handler = $this->getMockForAbstractClass('Monolog\Handler\AbstractHandler', ['warning', false]);
        $this->assertFalse($handler->isHandling($this->getRecord(Logger::DEBUG)));
        $handler->setLevel('debug');
        $this->assertTrue($handler->isHandling($this->getRecord(Logger::DEBUG)));
    }

    /**
     * @covers Monolog\Handler\AbstractHandler::getFormatter
     * @covers Monolog\Handler\AbstractHandler::getDefaultFormatter
     */
    public function test_get_formatter_initializes_default()
    {
        $handler = $this->getMockForAbstractClass('Monolog\Handler\AbstractHandler');
        $this->assertInstanceOf('Monolog\Formatter\LineFormatter', $handler->getFormatter());
    }

    /**
     * @covers Monolog\Handler\AbstractHandler::pushProcessor
     * @covers Monolog\Handler\AbstractHandler::popProcessor
     *
     * @expectedException LogicException
     */
    public function test_push_pop_processor()
    {
        $logger = $this->getMockForAbstractClass('Monolog\Handler\AbstractHandler');
        $processor1 = new WebProcessor;
        $processor2 = new WebProcessor;

        $logger->pushProcessor($processor1);
        $logger->pushProcessor($processor2);

        $this->assertEquals($processor2, $logger->popProcessor());
        $this->assertEquals($processor1, $logger->popProcessor());
        $logger->popProcessor();
    }

    /**
     * @covers Monolog\Handler\AbstractHandler::pushProcessor
     *
     * @expectedException InvalidArgumentException
     */
    public function test_push_processor_with_non_callable()
    {
        $handler = $this->getMockForAbstractClass('Monolog\Handler\AbstractHandler');

        $handler->pushProcessor(new \stdClass);
    }
}
